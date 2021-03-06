<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLuasDaunPintuToRuangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ruang', function (Blueprint $table) {
            $table->integer('luas_daun_pintu')->unsigned()->nullable()->default(0)->after('luas_daun_jendela');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ruang', function (Blueprint $table) {
            $table->dropColumn('luas_daun_pintu');
        });
    }
}
