<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapClinicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_clinics', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_identifier');
            $table->string('name');
            $table->string('state');
            $table->string('lat');
            $table->string('lon');
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
        Schema::dropIfExists('map_clinics');
    }
}
