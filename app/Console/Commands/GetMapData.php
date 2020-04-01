<?php

namespace App\Console\Commands;

use App\MapClinic;
use App\MapClinicStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GetMapData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'divi:get-map-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from DIVI Map';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $response = Http::get('https://diviexchange.z6.web.core.windows.net/report.html');

        if ($response->status() !== 200) {
            throw new Exception("[R1] Expected 200 but received " . $response->status());
        }

        $html = $response->body();
        $html = str_replace("\r\n", PHP_EOL, $html);
        $htmlLines = explode(PHP_EOL, $html);

        $submittedAt = null;
        foreach ($htmlLines as $line) {
            $line = trim($line);

            if (Str::startsWith($line, 'vegaEmbed("#left')) {
                $needle = '["Stand: ';
                $submittedAt = substr($line, stripos($line, $needle) + strlen($needle));

                $example = '30.03.2020, 05:00';
                $submittedAt = substr($submittedAt, 0, strlen($example));
                $submittedAt = Carbon::createFromFormat('d.m.Y, H:i', $submittedAt);
                break;
            }
        }

        if (! $submittedAt) {
            throw new Exception("No submission date found!");
        }

        $isNewData = MapClinic::checkIfNewerThanExistingData($submittedAt);
        if (! $isNewData) {
            $this->output->success('No new data available');
            return;
        }

        $collectedData = [];

        foreach ($htmlLines as $line) {
            $line = trim($line);

            $dataStart = 'vegaEmbed("#bottom_';
            if (Str::startsWith($line, $dataStart)) {
                $state = substr($line, strpos($line, $dataStart) + strlen($dataStart));
                $state = substr($state, 0, strpos($state, '"'));

                $line = substr($line, stripos($line, '{'));
                $line = substr($line, 0, strripos($line, ', {'));

                $json = json_decode($line, true);
                if (! isset($json['datasets'])) {
                    throw new Exception("No datasets found!");
                }

                $data = [];
                foreach ($json['datasets'] as $dataset) {
                    if (isset($dataset[0]['lat']) && isset($dataset[0]['COVID-19 aktuell'])) {
                        $data = array_merge($data, $dataset);
                    }
                }

                foreach ($data as $entryKey => $entry) {
                    foreach ($entry as $k => $v) {
                        $entry[$k] = trim($v);
                    }

                    if (! isset($entry['Klinikname']) && strlen($entry['Klinikname']) === 0) {
                        throw new Exception('Empty clinic name! Trying again next cycle.');
                    }

                    $entry['Bundesland'] = $state;
                    $entry['id'] = sha1(strtolower($entry['Klinikname'] . $entry['Bundesland']));

                    $data[$entryKey] = $entry;
                }

                $data = collect($data)
                    ->groupBy('id')
                    ->map(function (Collection $collection) {
                        $first = $collection->first();
                        return [
                            'id' => $first['id'],
                            'Klinikname' => $first['Klinikname'],
                            'Bundesland' => $first['Bundesland'],
                            'lat' => $first['lat'],
                            'lon' => $first['lon'],
                            'COVID-19 aktuell' => $collection->sum('COVID-19 aktuell'),
                        ];
                    })->all();

                $collectedData = array_merge($collectedData, $data);
            }
        }

        $this->output->progressStart(count($collectedData));
        $this->saveData($collectedData, $submittedAt);

        return;
    }

    private function saveData(array $data, Carbon $submittedAt)
    {
        \DB::transaction(function () use ($data, $submittedAt) {
            foreach ($data as $element) {
                $clinic = MapClinic::firstWhere('clinic_identifier', '=', $element['id']);
                if (! $clinic) {
                    $clinic = new MapClinic();
                    $clinic->clinic_identifier = $element['id'];
                    $clinic->name = $element['Klinikname'];
                    $clinic->state = $element['Bundesland'];
                    $clinic->lat = $element['lat'];
                    $clinic->lon = $element['lon'];
                }

                $clinic->last_submit_at = $submittedAt;
                $clinic->save();

                $clinicStatus = new MapClinicStatus();
                $clinicStatus->map_clinic_id = $clinic->id;
                $clinicStatus->covid19_cases = $element['COVID-19 aktuell'];
                $clinicStatus->submitted_at = $submittedAt;
                $clinicStatus->save();

                $this->output->progressAdvance();
            }
        });
    }
}
