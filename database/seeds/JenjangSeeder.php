<?php

use Illuminate\Database\Seeder;
use App\Sekolah;

class JenjangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sekolah::whereNull('bentuk_pendidikan_id')->update(['bentuk_pendidikan_id' => 6]);
    }
}
