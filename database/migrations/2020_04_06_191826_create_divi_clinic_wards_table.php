<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiviClinicWardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divi_clinic_wards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('divi_clinics_id', false, true);
            $table->string('divi_id');
            $table->string('description', 400);
            $table->text('organisation_tag')->nullable();
            $table->boolean('ards_network_member');
            $table->timestamp('last_submit_at');
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
        Schema::dropIfExists('divi_clinic_wards');
    }
}
