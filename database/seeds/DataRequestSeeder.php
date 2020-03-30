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
    }
}
