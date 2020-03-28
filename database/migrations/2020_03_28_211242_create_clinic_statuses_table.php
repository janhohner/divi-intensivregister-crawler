<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('clinic_id', false, true);
            $table->string('icu_low_care')->nullable();
            $table->string('icu_high_care')->nullable();
            $table->string('ecmo')->nullable();
            $table->string('submitted_at')->nullable();
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
        Schema::dropIfExists('clinic_statuses');
    }
}
