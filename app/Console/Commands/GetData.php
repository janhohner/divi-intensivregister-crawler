<?php

namespace App\Console\Commands;

use App\Clinic;
use App\ClinicStatus;
use Carbon\Carbon;
use DOMAttr;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class GetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'divi:get-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from DIVI Intensivregister';

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
        $response = Http::get('https://www.divi.de/register/intensivregister');

        if ($response->status() !== 200) {
            throw new Exception("[R1] Expected 200 but received " . $response->status());
        }

        $crawler = new Crawler($response->body());
        $crawler = $crawler->filter('#dataList tbody tr');

        $numRows = $crawler->count();

        if ($numRows === 20) {
            $response = Http::asForm()->post('https://www.divi.de/register/intensivregister?view=items', [
                'filter[search]' => '',
                'list[fullordering]' => 'a.title+ASC',
                'list[limit]' => '0',
                'filter[federalstate]' => '0',
                'filter[chronosort]' => '0',
                'filter[icu_highcare_state]' => '',
                'filter[ecmo_state]' => '',
                'filter[ards_network]' => '',
                'limitstart' => '0',
                'task' => '',
                'boxchecked' => '0',
            ]);

            if ($response->status() !== 200) {
                throw new Exception("[R2] Expected 200 but received " . $response->status());
            }

            $crawler = new Crawler($response->body());
            $crawler = $crawler->filter('#dataList tbody tr');

            $numRows = $crawler->count();

            $this->output->writeln('numRows: ' . $numRows);
        }

        $this->output->progressStart($numRows);

        $crawler->each(function ($row) {
            /** @var Crawler $row */
            $tds = $row->filter('td');

            $address = strip_tags($tds->first()->html());
            $address = explode("\r\n", trim($address));
            for ($i = 0; $i < count($address); $i++) {
                $address[$i] = trim($address[$i]);
            }

            $address = implode(PHP_EOL, $address);
            $clinicIdentifier = sha1($address);

            $state = trim($tds->getNode(2)->textContent);

            $icuLowCare = $this->getStatus($tds->getNode(3));
            $icuHighCare = $this->getStatus($tds->getNode(4));
            $ecmo = $this->getStatus($tds->getNode(5));

            $lastSubmitAt = trim(preg_replace('/\W{2,}/', ' ', $tds->getNode(6)->textContent));
            $lastSubmitAt = Carbon::createFromFormat('d.m.Y H:i', $lastSubmitAt);

            $isNew = false;
            $clinic = Clinic::getByClinicIdentifier($clinicIdentifier);
            if (! $clinic) {
                $isNew = true;

                $clinic = new Clinic();
                $clinic->clinic_identifier = $clinicIdentifier;
                $clinic->address = $address;
                $clinic->state = $state;
                $clinic->last_submit_at = $lastSubmitAt;
                $clinic->save();
            }

            if ($isNew || $clinic->last_submit_at->lessThan($lastSubmitAt)) {
                $clinicStatus = new ClinicStatus();
                $clinicStatus->clinic_id = $clinic->id;
                $clinicStatus->icu_low_care = $icuLowCare;
                $clinicStatus->icu_high_care = $icuHighCare;
                $clinicStatus->ecmo = $ecmo;
                $clinicStatus->submitted_at = $lastSubmitAt;
                $clinicStatus->save();
            }

            if ($clinic->last_submit_at->lessThan($lastSubmitAt)) {
                $clinic->last_submit_at = $lastSubmitAt;
            }
            $clinic->save();

            $this->output->progressAdvance();
        });
    }

    private function getStatus(\DOMNode $node): string
    {
        $types = [];
        foreach ($node->childNodes as $childNode) {
            /** @var \DOMNode $childNode */
            if ($childNode->nodeName === 'span') {
                foreach ($childNode->attributes as $attribute) {
                    /** @var DOMAttr $attribute */
                    if ($attribute->nodeName === 'class') {
                        $value = $attribute->value;
                        $value = explode('-', $value);
                        return $value[count($value) - 1];
                    }
                }
            }
        }

        return null;
    }
}
