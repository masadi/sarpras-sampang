<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataSekolahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_sekolah', function (Blueprint $table) {
            $table->uuid('data_sekolah_id');
            $table->uuid('sekolah_id');
            $table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')->onDelete('cascade');
            $table->decimal('tahun_pendataan_id', 4, 0);
            $table->foreign('tahun_pendataan_id')->references('tahun_pendataan_id')->on('tahun_pendataan')->onDelete('cascade');
            $table->integer('jumlah_rombel')->unsigned()->nullable();
            $table->integer('jumlah_pd')->unsigned()->nullable();
            $table->timestamps();
            $table->primary('data_sekolah_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_sekolah');
    }
}
