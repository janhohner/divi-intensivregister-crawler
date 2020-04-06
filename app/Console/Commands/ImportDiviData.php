<?php

namespace App\Console\Commands;

use App\DiviClinic;
use App\DiviClinicData;
use App\DiviClinicWard;
use App\DiviClinicWardData;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ImportDiviData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'divi:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data from DIVI Intensivregister';

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
        $pageSize = 10000;

        $response = $this->makeRegisterRequest($pageSize);

        $json = json_decode($response->body(), true);
        if ($json['rowCount'] < $pageSize) {
            $response = $this->makeRegisterRequest($json['rowCount']);
            $json = json_decode($response->body(), true);
        }

        $this->output->title('Importing register data');
        $this->output->progressStart($json['rowCount']);

        $data = $json['data'];

        $clinicsForWardProcessing = [];

        foreach ($data as $entry) {
            $newDataAvailable = false;
            $newEntry = false;

            $clinic = DiviClinic::findByDiviId($entry['id']);
            if (! $clinic) {
                $newEntry = true;

                $clinic = new DiviClinic();
                $clinic->divi_id = $entry['id'];
            }

            $lastSubmitAt = Carbon::parse($entry['meldezeitpunkt']);

            if (! $newEntry && $clinic->last_submit_at->lessThan($lastSubmitAt)) {
                $newDataAvailable = true;
            }

            if ($newEntry || $newDataAvailable) {
                $clinic->last_submit_at = $lastSubmitAt;
                $clinic->ik_number = $entry['krankenhausStandort']['ikNummer'];
                $clinic->description = $entry['krankenhausStandort']['bezeichnung'];
                $clinic->street = $entry['krankenhausStandort']['strasse'];
                $clinic->street_number = $entry['krankenhausStandort']['hausnummer'];
                $clinic->postcode = $entry['krankenhausStandort']['plz'];
                $clinic->city = $entry['krankenhausStandort']['ort'];
                $clinic->state = $entry['krankenhausStandort']['bundesland'];
                $clinic->latitude = $entry['krankenhausStandort']['position']['latitude'];
                $clinic->longitude = $entry['krankenhausStandort']['position']['longitude'];
                $clinic->save();

                $clinicData = new DiviClinicData();
                $clinicData->divi_clinics_id = $clinic->id;
                $clinicData->low_care = $this->mapDiviBedStatus($entry['bettenStatus']['statusLowCare']);
                $clinicData->high_care = $this->mapDiviBedStatus($entry['bettenStatus']['statusHighCare']);
                $clinicData->ecmo = $this->mapDiviBedStatus($entry['bettenStatus']['statusECMO']);
                $clinicData->covid19_cases = $entry['faelleCovidAktuell'] ? $entry['faelleCovidAktuell'] : 0;
                $clinicData->submitted_at = $lastSubmitAt;
                $clinicData->save();
            }

            $clinicsForWardProcessing[] = $clinic;

            $this->output->progressAdvance();
        }

        $this->output->writeln('');
        $this->output->writeln('');
        $this->output->title('Importing clinic data');
        $this->output->progressStart(count($clinicsForWardProcessing));

        foreach ($clinicsForWardProcessing as $clinic) {
            $this->getWardsForClinic($clinic);

            usleep(1000000);

            $this->output->progressAdvance();
        }

        return;
    }

    /**
     * @param DiviClinic $clinic
     * @throws Exception
     */
    private function getWardsForClinic(DiviClinic $clinic)
    {
        $response = $this->makeClinicRequest($clinic->divi_id);
        $data = json_decode($response->body(), true);

        foreach ($data as $entry) {
            $newDataAvailable = false;
            $newEntry = false;

            $ward = DiviClinicWard::findByDiviId($entry['id']);
            if (! $ward) {
                $newEntry = true;

                $ward = new DiviClinicWard();
                $ward->divi_clinics_id = $clinic->id;
                $ward->divi_id = $entry['id'];
            }

            $lastSubmitAt = Carbon::parse($entry['letzteMeldung']);

            if (! $newEntry && $ward->last_submit_at->lessThan($lastSubmitAt)) {
                $newDataAvailable = true;
            }

            if ($newEntry || $newDataAvailable) {
                $ward->description = $entry['bezeichnung'];
                $ward->organisation_tag = count($entry['tags']) > 0 ? $entry['tags'][0] : null;
                $ward->ards_network_member = ($entry['ardsNetzwerkMitglied'] === 'JA');
                $ward->last_submit_at = $lastSubmitAt;
                $ward->save();

                $wardData = new DiviClinicWardData();
                $wardData->divi_clinic_wards_id = $ward->id;
                $wardData->ecmo_cases_year = $entry['faelleEcmoJahr'];
                $wardData->beds_planned_capacity = $entry['bettenPlankapazitaet'];
                $wardData->submitted_at = $lastSubmitAt;
                $wardData->save();
            }
        }
    }

    /**
     * @param int $size
     * @return Response
     * @throws Exception
     */
    private function makeRegisterRequest(int $size): Response
    {
        $url = 'https://www.intensivregister.de/api/public/intensivregister?page=0&size=' . $size;
        return $this->makeRequest($url);
    }

    /**
     * @param string $diviId
     * @return Response
     * @throws Exception
     */
    private function makeClinicRequest(string $diviId): Response
    {
        $url = 'https://www.intensivregister.de/api/public/stammdaten/krankenhausstandort/' . $diviId
            . '/meldebereiche';
        return $this->makeRequest($url);
    }

    /**
     * @param string $url
     * @return Response
     * @throws Exception
     */
    private function makeRequest(string $url): Response
    {
        $tries = 0;
        $maxTries = 5;

        while ($tries < $maxTries) {
            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json'
                ])->get($url);

                if ($response->status() === 200) {
                    return $response;
                }
            } catch (Exception $exception) {
                $this->output->error($exception->getMessage());
            }

            sleep(5);
            $tries++;
        }

        throw new Exception('Request ' . $url . ' failed ' . $tries . ' times. Aborting.');
    }

    /**
     * @param string|null $status
     * @return string|null
     */
    private function mapDiviBedStatus(?string $status): ?string
    {
        if (! $status) {
            return null;
        }

        $status = strtoupper($status);

        switch ($status) {
            case 'VERFUEGBAR':
                return 'available';
            case 'BREGRENZT':
                return 'limited';
            case 'NICHT_VERFUEGBAR':
                return 'unavailable';
            default:
                return null;
        }
    }
}
