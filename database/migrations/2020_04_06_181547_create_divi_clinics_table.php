<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiviClinicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divi_clinics', function (Blueprint $table) {
            $table->id();
            $table->string('divi_id');
            $table->string('ik_number');
            $table->string('description', 400);
            $table->string('street');
            $table->string('street_number');
            $table->string('postcode');
            $table->string('city');
            $table->string('state');
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamp('last_submit_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('divi_clinics');
    }
}
