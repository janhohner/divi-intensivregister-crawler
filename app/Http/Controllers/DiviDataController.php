<?php

namespace App\Http\Controllers;

use App\Clinic;
use App\ClinicStatus;
use App\DataRequest;
use App\MapClinic;
use App\MapClinicStatus;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class DiviDataController extends Controller
{
    /**
     * @return Factory|View
     */
    public function showAllLoad()
    {
        $clinics = Clinic::with('statuses')
            ->orderBy('address', 'asc')
            ->get()
            ->map(function (Clinic $clinic) {
                return $this->mapLoadClinic($clinic);
            })->all();

        $lastUpdate = Clinic::getLastUpdate();

        return view('data.load.clinics', [
            'clinics' => $clinics,
            'lastUpdate' => $lastUpdate,
        ]);
    }

    /**
     * @param string $id
     * @return Factory|View
     */
    public function showLoad(string $id)
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

        $clinic = $this->mapLoadClinic($clinic);

        return view('data.load.clinic', [
            'clinic' => $clinic,
        ]);
    }

    /**
     * @param string $type
     * @return JsonResponse|void
     * @throws CannotInsertRecord
     * @throws OverflowException
     */
    public function exportLoad(string $type)
    {
        if ($type === 'json') {
            return $this->exportLoadJson();
        } else if ($type === 'csv') {
            return $this->exportLoadCsv();
        }

        abort(400, 'Unknown export format');
    }

    /**
     * @return JsonResponse
     */
    private function exportLoadJson()
    {
        $clinics = Clinic::with('statuses')
            ->orderBy('address', 'asc')
            ->get()
            ->map(function (Clinic $clinic) {
                return $this->mapLoadClinic($clinic, true);
            })->all();

        DataRequest::incrementKey('json_request');

        return response()->json($clinics);
    }

    /**
     * @throws CannotInsertRecord
     * @throws OverflowException
     */
    private function exportLoadCsv()
    {
        $clinics = Clinic::with('statuses')
            ->orderBy('address', 'asc')
            ->get()
            ->map(function (Clinic $clinic) {
                return $this->mapLoadClinic($clinic);
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

    /**
     * @param Clinic $clinic
     * @param bool $mapStatuses
     * @return array
     */
    private function mapLoadClinic(Clinic $clinic, bool $mapStatuses = false): array
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
            $statuses = $this->mapLoadStatuses($statuses);
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

    /**
     * @param Collection $statuses
     * @return array
     */
    private function mapLoadStatuses(Collection $statuses): array
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

    /**
     * @return Factory|View
     */
    public function showAllCases()
    {
        $clinics = MapClinic::with('statuses')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function (MapClinic $clinic) {
                return $this->mapCasesClinic($clinic);
            })->all();

        $lastUpdate = MapClinic::getLastUpdate();

        return view('data.cases.clinics', [
            'clinics' => $clinics,
            'lastUpdate' => $lastUpdate,
        ]);
    }

    /**
     * @param string $id
     * @return Factory|View
     */
    public function showCases(string $id)
    {
        /** @var MapClinic $clinic */
        $clinic = MapClinic::with([
            'statuses' => function ($query) {
                /** @var Builder $query */
                $query->orderBy('submitted_at', 'desc');
            }
        ])
            ->find($id);
        abort_if(! $clinic, 404, 'Clinic not found');

        $clinic = $this->mapCasesClinic($clinic);

        return view('data.cases.clinic', [
            'clinic' => $clinic,
        ]);
    }

    /**
     * @param MapClinic $clinic
     * @param bool $mapStatuses
     * @return array
     */
    private function mapCasesClinic(MapClinic $clinic, bool $mapStatuses = false): array
    {
        $statuses = $clinic->statuses;
        if ($mapStatuses) {
            $statuses = $this->mapCasesStatuses($statuses);
        }

        return [
            'id' => $clinic->id,
            'name' => $clinic->name,
            'state' => $clinic->state,
            'lat' => $clinic->lat,
            'lon' => $clinic->lon,
            'last_submit_at' => $clinic->last_submit_at,
            'statuses' => $statuses,
        ];
    }

    /**
     * @param Collection $statuses
     * @return array
     */
    private function mapCasesStatuses(Collection $statuses): array
    {
        return $statuses->map(function (MapClinicStatus $status) {
            return [
                'covid19_cases' => $status->covid19_cases,
                'submitted_at' => $status->submitted_at->format('Y-m-d H:i'),
            ];
        })
            ->all();
    }

    /**
     * @param string $type
     * @return JsonResponse|void
     * @throws CannotInsertRecord
     * @throws OverflowException
     */
    public function exportCases(string $type)
    {
        if ($type === 'json') {
            return $this->exportCasesJson();
        } else if ($type === 'csv') {
            return $this->exportCasesCsv();
        }

        abort(400, 'Unknown export format');
    }

    /**
     * @return JsonResponse
     */
    private function exportCasesJson()
    {
        $clinics = MapClinic::with('statuses')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function (MapClinic $clinic) {
                return $this->mapCasesClinic($clinic, true);
            })->all();

        DataRequest::incrementKey('cases_json_request');

        return response()->json($clinics);
    }

    /**
     * @throws CannotInsertRecord
     * @throws OverflowException
     */
    private function exportCasesCsv()
    {
        $clinics = MapClinic::with('statuses')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function (MapClinic $clinic) {
                return $this->mapCasesClinic($clinic);
            });

        $headerClinics = ['id', 'name', 'state', 'lat', 'lon', 'num_statuses', 'last_submit_at'];
        $recordsClinics = $clinics->map(function (array $clinic) {
            return [
                $clinic['id'],
                $clinic['name'],
                $clinic['state'],
                $clinic['lat'],
                $clinic['lon'],
                $clinic['statuses']->count(),
                $clinic['last_submit_at']->toISOString(),
            ];
        })->all();

        $csvClinics = Writer::createFromString('');
        $csvClinics->insertOne($headerClinics);
        $csvClinics->insertAll($recordsClinics);

        $headerStatuses = ['id', 'map_clinic_id', 'covid19_cases', 'submitted_at'];
        $recordsStatuses = [];
        foreach ($clinics as $clinic) {
            foreach ($clinic['statuses'] as $status) {
                $recordsStatuses[] = [
                    $status->id,
                    $status->map_clinic_id,
                    $status->covid19_cases,
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
        $zip = new ZipStream('divi_map_data_csv_export-' . $now->format('Ymd-His') . '.zip', $options);
        $zip->addFile('map_clinics.csv', $csvClinics->getContent());
        $zip->addFile('map_clinics_statuses.csv', $csvStatuses->getContent());

        DataRequest::incrementKey('cases_csv_request');

        $zip->finish();
    }
}
