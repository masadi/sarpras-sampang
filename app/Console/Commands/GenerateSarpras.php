<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Sekolah;
use App\Tanah;
use App\Bangunan;
use App\Ruang;
use App\Kondisi_ruang;
use App\Kondisi_bangunan;
use App\HelperModel;

class GenerateSarpras extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:sarpras';

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
        $sheets = (new FastExcel)->importSheets('public/data-sarpras.xlsx');
        $rusak_ringan = $sheets[0];
        foreach($rusak_ringan as $ringan){
            $sekolah = Sekolah::where('npsn', $ringan['NPSN'])->first();
            if($sekolah){
                $tanah = Tanah::updateOrCreate([
                    'sekolah_id' => $sekolah->sekolah_id,
                    'nama' => 'Tanah '.$sekolah->nama,
                    'no_sertifikat_tanah' => 123,
                    'panjang' => 0,
                    'lebar' => 0,
                    'luas' => 0,
                    'luas_lahan_tersedia' => 0,
                    'kepemilikan_sarpras_id' => 1,
                    'keterangan' => '',
                ]);
                $bangunan = Bangunan::updateOrCreate([
                    'tanah_id' => $tanah->tanah_id,
                    'nama' => $ringan['GEDUNG'],
                    'imb' => 123,
                    'panjang' => 0,
                    'lebar' => 0,
                    'luas' => 0,
                    'lantai' => 1,
                    'kepemilikan_sarpras_id' => 1,
                    'tahun_bangun' => date('Y'),
                    'keterangan' => '',
                ]);
                $kondisi_bangunan = Kondisi_bangunan::updateOrcreate(
                    [
                        'bangunan_id' => $bangunan->bangunan_id,
                        'tahun_pendataan_id' => HelperModel::tahun_pendataan(),
                    ],
                    [
                        'rusak_pondasi' => '20.00',
                        'ket_pondasi' => 'Penurunan merata pada seluruh struktur bangunan',
                        'rusak_sloop_kolom_balok' => '0.00',
                        'ket_sloop_kolom_balok' => '-',
                        'rusak_kudakuda_atap' => '0.00',
                        'ket_kudakuda_atap' => '-',
                        'rusak_plester_struktur' => '0.00',
                        'ket_plester_struktur' => '-',
                        'rusak_tutup_atap' => '0.00',
                        'ket_tutup_atap' => '-',
                        'nilai_saat_ini' => '20.00',
                    ]
                );
                $ruang = Ruang::updateOrCreate([
                    'jenis_prasarana_id' => 1,
                    'bangunan_id' => $bangunan->bangunan_id,
                    'kode' => $ringan['RUANG'],
                    'nama' => $ringan['RUANG'],
                    'tahun_bangun' => date('Y'),
                    'tahun_renovasi' => NULL,
                    'lantai_ke' => 1,
                    'panjang' => 0,
                    'lebar' => 0,
                    'luas' => 0,
                    'luas_plester' => 0,
                    'luas_plafon' => 0,
                    'luas_dinding' => 0,
                    'luas_daun_jendela' => 0,
                    'luas_daun_pintu' => 0,
                    'luas_kusen' => 0,
                    'luas_tutup_lantai' => 0,
                    'jumlah_instalasi_listrik' => 0,
                    'panjang_instalasi_air' => 0,
                    'jumlah_instalasi_air' => 0,
                    'panjang_drainase' => 0,
                    'luas_finish_struktur' => 0,
                    'luas_finish_plafon' => 0,
                    'luas_finish_dinding' => 0,
                    'luas_finish_kpj' => 0,
                    'keterangan' => '',
                ]);
                $kondisi_ruang = Kondisi_ruang::updateOrcreate(
                    [
                        'ruang_id' => $ruang->ruang_id,
                        'tahun_pendataan_id' => HelperModel::tahun_pendataan(),
                    ],
                    [
                        'rusak_bata_dinding' => '10.00',
                        'ket_bata_dinding' => '-',
                        'rusak_daun_jendela' => '10.00',
                        'ket_daun_jendela' => '-',
                        'rusak_daun_pintu' => '10.00',
                        'ket_daun_pintu' => '-',
                        'rusak_kusen' => '10.00',
                        'ket_kusen' => '-',
                        'rusak_tutup_plafon' => '10.00',
                        'ket_tutup_plafon' => '-',
                        'rusak_tutup_lantai' => '10.00',
                        'ket_penutup_lantai' => '-',
                        'ket_inst_listrik' => '-',
                        'rusak_inst_listrik' => '0.00',
                        'ket_inst_air' => '-',
                        'rusak_inst_air' => '0.00',
                        'rusak_drainase' =>'0.00',
                        'ket_drainase' => '-',
                        'rusak_finish_plafon' => '0.00',
                        'ket_finish_plafon' => '-',
                        'rusak_finish_dinding' => '0.00',
                        'ket_finish_dinding' => '-',
                        'rusak_finish_kpj' => '0.00',
                        'ket_finish_kpj' => '-',
                        'nilai_saat_ini' => '10.00',
                    ]
                );
            }
        }
    }
}
