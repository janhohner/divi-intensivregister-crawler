<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiviClinicDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divi_clinic_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('divi_clinics_id', false, true);
            $table->string('low_care')->nullable();
            $table->string('high_care')->nullable();
            $table->string('ecmo')->nullable();
            $table->integer('covid19_cases', false, true);
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->foreign('divi_clinics_id')->references('id')->on('divi_clinics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('divi_clinic_data');
    }
}
