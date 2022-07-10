<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Sekolah;
use App\Kondisi_bangunan;

class GenerateKondisi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:kondisi';

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
        $sekolah = Sekolah::whereHas('tanah', function($query){
            $query->whereHas('bangunan', function($query){
                $query->whereDoesntHave('kondisi_bangunan', function($query){
                    $query->where('tahun_pendataan_id', config('app.tahun_pendataan'));
                });
            });
        })->orderBy('bentuk_pendidikan_id')->get();
        $data_pondasi = collect([
            'Tidak ada kerusakan' =>  0,
            'Penurunan merata pada seluruh struktur bangunan' =>  20,
            'Penurunan <= 1/250L' =>  40,
            'Penurunan > 1/250L' =>  60,
            'Bangunan miring' =>  80,
            'Pondasi patah' =>  100,
        ]);
        for($i=1;$i<=20;$i++){
            $angka[] = $i;
        }
        $angka = collect($angka);
        foreach($sekolah as $s){
            foreach($s->tanah as $tanah){
                foreach($tanah->bangunan as $bangunan){
                    $kerusakan = [
                        $angka->random(),
                        $angka->random(),
                        $angka->random(),
                        $angka->random(),
                        $angka->random(),
                    ];
                    $nilai_saat_ini = collect($kerusakan)->sum();
                    Kondisi_bangunan::updateOrCreate(
                        [
                            'bangunan_id' => $bangunan->bangunan_id,
                            'tahun_pendataan_id' => config('app.tahun_pendataan')
                        ],
                        [
                            'rusak_pondasi' => $kerusakan[0],
                            'ket_pondasi' => '-',
                            'rusak_sloop_kolom_balok' => $kerusakan[1],
                            'ket_sloop_kolom_balok' => '-',
                            'rusak_kudakuda_atap' => $kerusakan[2],
                            'ket_kudakuda_atap' => '-',
                            'rusak_plester_struktur' => $kerusakan[3],
                            'ket_plester_struktur' => '-',
                            'rusak_tutup_atap' => $kerusakan[4],
                            'ket_tutup_atap' => '-',
                            'nilai_saat_ini' => $nilai_saat_ini,
                        ]
                    );
                    $this->info("Bangunan $bangunan->nama milik $s->nama berhasil di generate dengan total nilai $nilai_saat_ini");
                }
            }
        }
    }
}
