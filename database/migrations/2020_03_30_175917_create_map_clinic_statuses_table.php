<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapClinicStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_clinic_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('map_clinic_id', false, true);
            $table->integer('covid19_cases', false, true);
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->foreign('map_clinic_id')->references('id')->on('map_clinics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('map_clinic_statuses');
    }
}
