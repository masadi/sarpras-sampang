<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Sekolah;
use App\Exports\InstrumenExport;
use Maatwebsite\Excel\Facades\Excel;
use File;

class GenerateInstrumen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:instrumen';

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
        Sekolah::whereNull('bentuk_pendidikan_id')->update(['bentuk_pendidikan_id' => 5]);
        $sekolah = Sekolah::orderBy('bentuk_pendidikan_id')->get();
        foreach($sekolah as $s){
            $excel = 'Instrumen Sarpras-'.$s->nama.'-'.$s->npsn.'.xlsx';
            Excel::store(new InstrumenExport($s->sekolah_id), $excel);
            File::move(storage_path('app/'.$excel), public_path('downloads/'.$excel));
            $this->info($s->nama);
        }
    }
}
