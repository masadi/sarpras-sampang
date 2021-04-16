<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKondisiSaatIniToKondisiRuangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kondisi_ruang', function (Blueprint $table) {
            $table->decimal('nilai_saat_ini', 20,2)->default(0)->after('berfungsi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kondisi_ruang', function (Blueprint $table) {
            $table->dropColumn('nilai_saat_ini');
        });
    }
}
