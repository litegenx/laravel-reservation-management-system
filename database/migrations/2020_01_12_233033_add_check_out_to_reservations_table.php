<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckOutToReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->time('check_out')->comment(__('database.reservations.check_out'))->nullable(false)->default(collect(json_decode(file_get_contents(resource_path('config/settings.json')), true))->first(function ($item) {
                return 'check_out' === $item[0];
            })[1]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('check_out');
        });
    }
}
