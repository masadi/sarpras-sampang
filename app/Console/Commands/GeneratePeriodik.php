<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Tahun_pendataan;

class GeneratePeriodik extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:periodik';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Tahun_pendataan::where('periode_aktif', 1)->update(['periode_aktif' => 0]);
        Tahun_pendataan::updateOrCreate(
            [
                'tahun_pendataan_id' => 2022,
            ],
            [
                'nama' => 'Tahun Anggaran 2022',
                'periode_aktif' => 1,
            ]
        );
    }
}
