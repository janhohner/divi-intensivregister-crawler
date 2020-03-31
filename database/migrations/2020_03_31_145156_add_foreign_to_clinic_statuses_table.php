<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignToClinicStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::getConnection()->getPdo()->query('ALTER TABLE clinic_statuses MODIFY clinic_id bigint unsigned NOT NULL');

        Schema::table('clinic_statuses', function (Blueprint $table) {
            $table->foreign('clinic_id')->references('id')->on('clinics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_statuses', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
        });
    }
}
