<?php

namespace App\Http\Controllers;

use App\Clinic;
use App\ClinicStatus;
use App\DataRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class DiviDataController extends Controller
{
    public function showAll()
    {
        $clinics = Clinic::with('statuses')
            ->orderBy('address', 'asc')
            ->get()
            ->map(function (Clinic $clinic) {
                return $this->mapClinic($clinic);
            })->all();

        return view('data.clinics', [
            'clinics' => $clinics,
        ]);
    }

    public function show(string $id)
    {
        /** @var Clinic $clinic */
        $clinic = Clinic::with([
            'statuses' => function ($query) {
                /** @var Builder $query */
                $query->orderBy('submitted_at', 'desc');
            }
        ])
            ->find($id);
        abort_if(! $clinic, 404, 'Clinic not found');

        $clinic = $this->mapClinic($clinic);

        return view('data.clinic', [
            'clinic' => $clinic,
        ]);
    }

    public function export(string $type)
    {
        if ($type === 'json') {
            return $this->exportJson();
        } else if ($type === 'csv') {
            return $this->exportCsv();
        }

        abort(400, 'Unknown export format');
    }

    private function exportJson()
    {
        $clinics = Clinic::with('statuses')
            ->orderBy('address', 'asc')
            ->get()
            ->map(function (Clinic $clinic) {
                return $this->mapClinic($clinic, true);
            })->all();

        DataRequest::incrementKey('json_request');

        return response()->json($clinics);
    }

    /**
     * @throws CannotInsertRecord
     * @throws OverflowException
     */
    private function exportCsv()
    {
        $clinics = Clinic::with('statuses')
            ->orderBy('address', 'asc')
            ->get()
            ->map(function (Clinic $clinic) {
                return $this->mapClinic($clinic);
            });

        $headerClinics = ['id', 'name', 'address', 'city', 'state', 'num_statuses', 'last_submit_at'];
        $recordsClinics = $clinics->map(function (array $clinic) {
            return [
                $clinic['id'],
                $clinic['name'],
                $clinic['address'],
                $clinic['city'],
                $clinic['state'],
                $clinic['statuses']->count(),
                $clinic['last_submit_at']->toISOString(),
            ];
        })->all();

        $csvClinics = Writer::createFromString('');
        $csvClinics->insertOne($headerClinics);
        $csvClinics->insertAll($recordsClinics);

        $headerStatuses = ['id', 'clinic_id', 'icu_low_care', 'icu_high_care', 'ecmo', 'submitted_at'];
        $recordsStatuses = [];
        foreach ($clinics as $clinic) {
            foreach ($clinic['statuses'] as $status) {
                $recordsStatuses[] = [
                    $status->id,
                    $status->clinic_id,
                    $status->icu_low_care,
                    $status->icu_high_care,
                    $status->ecmo,
                    $status->submitted_at->toISOString(),
                ];
            }
        }

        $csvStatuses = Writer::createFromString('');
        $csvStatuses->insertOne($headerStatuses);
        $csvStatuses->insertAll($recordsStatuses);

        $options = new Archive();
        $options->setSendHttpHeaders(true);

        $now = Carbon::now();
        $zip = new ZipStream('divi_data_csv_export-' . $now->format('Ymd-His') . '.zip', $options);
        $zip->addFile('clinics.csv', $csvClinics->getContent());
        $zip->addFile('clinics_statuses.csv', $csvStatuses->getContent());

        DataRequest::incrementKey('csv_request');

        $zip->finish();
    }

    private function mapClinic(Clinic $clinic, bool $mapStatuses = false): array
    {
        $addressArray = explode(PHP_EOL, $clinic->address);
        $addressArrayLength = count($addressArray);
        $address = $addressArray[$addressArrayLength - 2];
        $city = $addressArray[$addressArrayLength - 1];

        unset($addressArray[$addressArrayLength - 2]);
        unset($addressArray[$addressArrayLength - 1]);

        $name = implode(' ', $addressArray);
        $name = str_replace('&amp;', '&', $name);

        $statuses = $clinic->statuses;
        if ($mapStatuses) {
            $statuses = $this->mapStatuses($statuses);
        }

        return [
            'id' => $clinic->id,
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'state' => $clinic->state,
            'last_submit_at' => $clinic->last_submit_at,
            'statuses' => $statuses,
        ];
    }

    private function mapStatuses(Collection $statuses): array
    {
        return $statuses->map(function (ClinicStatus $status) {
            return [
                'icu_low_care' => $status->icu_low_care,
                'icu_high_care' => $status->icu_high_care,
                'ecmo' => $status->ecmo,
                'submitted_at' => $status->submitted_at->format('Y-m-d H:i'),
            ];
        })
            ->all();
    }
}
