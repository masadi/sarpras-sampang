<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenerbitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penerbit', function (Blueprint $table) {
            $table->uuid('penerbit_id');
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('telepon_cp')->nullable();
            $table->string('npwp')->nullable();
            $table->string('siup')->nullable();
            $table->timestamps();
            $table->primary('penerbit_id');
        });
        Schema::table('buku', function (Blueprint $table) {
            $table->uuid('penerbit_id')->nullable()->after('mata_pelajaran_id');
            $table->foreign('penerbit_id')->references('penerbit_id')->on('penerbit')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropForeign(['penerbit_id']);
        });
        Schema::dropIfExists('penerbit');
    }
}
