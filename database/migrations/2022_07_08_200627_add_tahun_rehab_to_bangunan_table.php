<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTahunRehabToBangunanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bangunan', function (Blueprint $table) {
            $table->integer('tahun_renovasi')->unsigned()->nullable()->after('tahun_bangun');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bangunan', function (Blueprint $table) {
            $table->dropColumn('tahun_renovasi');
        });
    }
}
