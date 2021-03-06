<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalKerusakanToBangunanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bangunan', function (Blueprint $table) {
            $table->decimal('total_kerusakan', 6,2)->default(0)->after('keterangan');
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
            $table->dropColumn('total_kerusakan');
        });
    }
}
