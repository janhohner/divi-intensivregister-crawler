<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiviClinicWardDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divi_clinic_ward_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('divi_clinic_wards_id', false, true);
            $table->integer('ecmo_cases_year', false, true);
            $table->integer('beds_planned_capacity', false, true);
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->foreign('divi_clinic_wards_id')->references('id')->on('divi_clinic_wards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('divi_clinic_ward_data');
    }
}
