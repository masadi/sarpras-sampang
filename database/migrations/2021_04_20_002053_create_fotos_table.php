<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foto', function (Blueprint $table) {
            $table->uuid('foto_id');
            $table->uuid('ruang_id')->nullable();
            $table->foreign('ruang_id')->references('ruang_id')->on('ruang')->onDelete('cascade');
            $table->uuid('bangunan_id')->nullable();
            $table->foreign('bangunan_id')->references('bangunan_id')->on('bangunan')->onDelete('cascade');
            $table->decimal('tahun_pendataan_id', 4, 0);
            $table->foreign('tahun_pendataan_id')->references('tahun_pendataan_id')->on('tahun_pendataan')->onDelete('cascade');
            $table->string('nama')->nullable();
            $table->timestamps();
            $table->primary('foto_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foto');
    }
}
