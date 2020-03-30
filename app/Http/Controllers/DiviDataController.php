<?php

namespace App\Http\Controllers;

use App\Clinic;
use App\ClinicStatus;
use App\DataRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

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

    private function mapClinic(Clinic $clinic, bool $mapStatuses = false): array
    {
        $addressArray = explode(PHP_EOL, $clinic->address);
        $addressArrayLength = count($addressArray);
        $address = $addressArray[$addressArrayLength - 2];
        $city = $addressArray[$addressArrayLength - 1];

        unset($addressArray[$addressArrayLength - 2]);
        unset($addressArray[$addressArrayLength - 1]);

        $name = implode(' ', $addressArray);

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
