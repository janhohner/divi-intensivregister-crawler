<?php

use Illuminate\Database\Seeder;
use App\DataRequest;

class DataRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonRequest = DataRequest::firstWhere('key', '=', 'json_request');
        if (! $jsonRequest) {
            $jsonRequest = new DataRequest();
            $jsonRequest->key = 'json_request';
            $jsonRequest->value = 0;
            $jsonRequest->save();
        }

        $csvRequest = DataRequest::firstWhere('key', '=', 'csv_request');
        if (! $csvRequest) {
            $csvRequest = new DataRequest();
            $csvRequest->key = 'csv_request';
            $csvRequest->value = 0;
            $csvRequest->save();
        }

        $casesJsonRequest = DataRequest::firstWhere('key', '=', 'cases_json_request');
        if (! $casesJsonRequest) {
            $casesJsonRequest = new DataRequest();
            $casesJsonRequest->key = 'cases_json_request';
            $casesJsonRequest->value = 0;
            $casesJsonRequest->save();
        }

        $casesCsvRequest = DataRequest::firstWhere('key', '=', 'cases_csv_request');
        if (! $casesCsvRequest) {
            $casesCsvRequest = new DataRequest();
            $casesCsvRequest->key = 'cases_csv_request';
            $casesCsvRequest->value = 0;
            $casesCsvRequest->save();
        }

        $diviJsonRequest = DataRequest::firstWhere('key', '=', 'divi_json_request');
        if (! $diviJsonRequest) {
            $diviJsonRequest = new DataRequest();
            $diviJsonRequest->key = 'divi_json_request';
            $diviJsonRequest->value = 0;
            $diviJsonRequest->save();
        }
    }
}
