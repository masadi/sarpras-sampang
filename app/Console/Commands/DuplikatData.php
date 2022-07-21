<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Sekolah;
use App\Tanah;
use App\Bangunan;
use App\Kondisi_bangunan;
use App\Ruang;
use App\Kondisi_ruang;
use App\HelperModel;
class DuplikatData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duplikat:data';

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
    public $tahun_pendataan_id;
    public function __construct()
    {
        parent::__construct();
        $this->tahun_pendataan_id = HelperModel::tahun_pendataan();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Sekolah::whereNull('bentuk_pendidikan_id')->update(['bentuk_pendidikan_id' => 5]);
        Sekolah::doesntHave('tanah')->where('bentuk_pendidikan_id', 5)->chunk(10, function ($data_sekolah) {
            foreach ($data_sekolah as $sekolah) {
                $tanah = Tanah::whereHas('bangunan', function($query){
                    $query->has('kondisi_bangunan');
                    $query->whereHas('ruang', function($query){
                        $query->has('kondisi_ruang');
                    });
                })->with(['bangunan' => function($query){
                    $query->with(['kondisi_bangunan', 'ruang' => function($query){
                        $query->with(['kondisi_ruang']);
                    }]);
                }])->first();
                $new_tanah = Tanah::updateOrCreate(
                    [
                        'sekolah_id' => $sekolah->sekolah_id,
                        'nama' => 'Tanah '.$sekolah->nama,
                    ],
                    [
                        'no_sertifikat_tanah' => $tanah->no_sertifikat_tanah,
                        'panjang' => $tanah->panjang,
                        'lebar' => $tanah->lebar,
                        'luas' => $tanah->luas,
                        'luas_lahan_tersedia' => $tanah->luas_lahan_tersedia,
                        'kepemilikan_sarpras_id' => $tanah->kepemilikan_sarpras_id,
                        'keterangan' => $tanah->keterangan,
                    ]
                );
                foreach($tanah->bangunan as $bangunan){
                    $new_bangunan = Bangunan::updateOrCreate(
                        [
                            'tanah_id' => $new_tanah->tanah_id,
                            'nama' => $bangunan->nama.' '.$sekolah->nama,
                        ],
                        [
                            'imb' => $bangunan->img,
                            'panjang' => $bangunan->panjang,
                            'lebar' => $bangunan->lebar,
                            'luas' => $bangunan->luas,
                            'lantai' => $bangunan->lantai,
                            'kepemilikan_sarpras_id' => $bangunan->kepemilikan_sarpras_id,
                            'tahun_bangun' => $bangunan->tahun_bangun,
                            'keterangan' => $bangunan->keterangan,
                            'total_kerusakan' => $bangunan->total_kerusakan,
                        ]
                    );
                    if($bangunan->kondisi_bangunan){
                        Kondisi_bangunan::updateOrCreate(
                            [
                                'bangunan_id' => $new_bangunan->bangunan_id,
                                'tahun_pendataan_id' => $this->tahun_pendataan_id,
                            ],
                            [
                                'rusak_pondasi' => $bangunan->kondisi_bangunan->rusak_pondasi,
                                'ket_pondasi' => $bangunan->kondisi_bangunan->ket_pondasi,
                                'rusak_sloop_kolom_balok' => $bangunan->kondisi_bangunan->rusak_sloop_kolom_balok,
                                'ket_sloop_kolom_balok' => $bangunan->kondisi_bangunan->ket_sloop_kolom_balok,
                                'rusak_plester_struktur' => $bangunan->kondisi_bangunan->rusak_plester_struktur,
                                'ket_plester_struktur' => $bangunan->kondisi_bangunan->ket_plester_struktur,
                                'rusak_kudakuda_atap' => $bangunan->kondisi_bangunan->rusak_kudakuda_atap,
                                'ket_kudakuda_atap' => $bangunan->kondisi_bangunan->ket_kudakuda_atap,
                                'rusak_kaso_atap' => $bangunan->kondisi_bangunan->rusak_kaso_atap,
                                'ket_kaso_atap' => $bangunan->kondisi_bangunan->ket_kaso_atap,
                                'rusak_reng_atap' => $bangunan->kondisi_bangunan->rusak_reng_atap,
                                'ket_reng_atap' => $bangunan->kondisi_bangunan->ket_reng_atap,
                                'rusak_tutup_atap' => $bangunan->kondisi_bangunan->rusak_tutup_atap,
                                'ket_tutup_atap' => $bangunan->kondisi_bangunan->ket_tutup_atap,
                                'nilai_saat_ini' => $bangunan->kondisi_bangunan->nilai_saat_ini,
                            ]
                        );
                    }
                    foreach($bangunan->ruang as $ruang){
                        $new_ruang = Ruang::updateOrCreate(
                            [
                                'bangunan_id' => $new_bangunan->bangunan_id,
                                'kode' => $ruang->kode,
                                'nama' => $ruang->nama,
                            ],
                            [
                                'jenis_prasarana_id' => $ruang->jenis_prasarana_id,
                                'tahun_bangun' => $ruang->tahun_bangun,
                                'tahun_renovasi' => $ruang->tahun_renovasi,
                                'lantai_ke' => $ruang->lantai_ke,
                                'panjang' => $ruang->panjang,
                                'lebar' => $ruang->lebar,
                                'luas' => $ruang->luas,
                                'luas_plester' => $ruang->luas_plester,
                                'luas_plafon' => $ruang->luas_plafon,
                                'luas_dinding' => $ruang->luas_dinding,
                                'luas_daun_jendela' => $ruang->luas_daun_jendela,
                                'luas_daun_pintu' => $ruang->luas_daun_pintu,
                                'luas_kusen' => $ruang->luas_kusen,
                                'luas_tutup_lantai' => $ruang->luas_tutup_lantai,
                                'jumlah_instalasi_listrik' => $ruang->jumlah_instalasi_listrik,
                                'panjang_instalasi_air' => $ruang->panjang_instalasi_air,
                                'jumlah_instalasi_air' => $ruang->jumlah_instalasi_air,
                                'panjang_drainase' => $ruang->panjang_drainase,
                                'luas_finish_struktur' => $ruang->luas_finish_struktur,
                                'luas_finish_plafon' => $ruang->luas_finish_plafon,
                                'luas_finish_dinding' => $ruang->luas_finish_dinding,
                                'luas_finish_kpj' => $ruang->luas_finish_kpj,
                                'keterangan' => $ruang->keterangan,
                            ]
                        );
                        if($ruang->kondisi_ruang){
                            Kondisi_ruang::updateOrCreate(
                                [
                                    'ruang_id' => $new_ruang->ruang_id,
                                    'tahun_pendataan_id' => $this->tahun_pendataan_id,
                                ],
                                [
                                    'rusak_lisplang_talang' => $ruang->kondisi_ruang->rusak_lisplang_talang,
                                    'ket_lisplang_talang' => $ruang->kondisi_ruang->ket_lisplang_talang,
                                    'rusak_rangka_plafon' => $ruang->kondisi_ruang->rusak_rangka_plafon,
                                    'ket_rangka_plafon' => $ruang->kondisi_ruang->ket_rangka_plafon,
                                    'rusak_tutup_plafon' => $ruang->kondisi_ruang->rusak_tutup_plafon,
                                    'ket_tutup_plafon' => $ruang->kondisi_ruang->ket_tutup_plafon,
                                    'rusak_bata_dinding' => $ruang->kondisi_ruang->rusak_bata_dinding,
                                    'ket_bata_dinding' => $ruang->kondisi_ruang->ket_bata_dinding,
                                    'rusak_plester_dinding' => $ruang->kondisi_ruang->rusak_plester_dinding,
                                    'ket_plester_dinding' => $ruang->kondisi_ruang->ket_plester_dinding,
                                    'rusak_daun_jendela' => $ruang->kondisi_ruang->rusak_daun_jendela,
                                    'ket_daun_jendela' => $ruang->kondisi_ruang->ket_daun_jendela,
                                    'rusak_daun_pintu' => $ruang->kondisi_ruang->rusak_daun_pintu,
                                    'ket_daun_pintu' => $ruang->kondisi_ruang->ket_daun_pintu,
                                    'rusak_kusen' => $ruang->kondisi_ruang->rusak_kusen,
                                    'ket_kusen' => $ruang->kondisi_ruang->ket_kusen,
                                    'rusak_tutup_lantai' => $ruang->kondisi_ruang->rusak_tutup_lantai,
                                    'ket_penutup_lantai' => $ruang->kondisi_ruang->ket_penutup_lantai,
                                    'rusak_inst_listrik' => $ruang->kondisi_ruang->rusak_inst_listrik,
                                    'ket_inst_listrik' => $ruang->kondisi_ruang->ket_inst_listrik,
                                    'rusak_inst_air' => $ruang->kondisi_ruang->rusak_inst_air,
                                    'ket_inst_air' => $ruang->kondisi_ruang->ket_inst_air,
                                    'rusak_drainase' => $ruang->kondisi_ruang->rusak_drainase,
                                    'ket_drainase' => $ruang->kondisi_ruang->ket_drainase,
                                    'rusak_finish_struktur' => $ruang->kondisi_ruang->rusak_finish_struktur,
                                    'ket_finish_struktur' => $ruang->kondisi_ruang->ket_finish_struktur,
                                    'rusak_finish_plafon' => $ruang->kondisi_ruang->rusak_finish_plafon,
                                    'ket_finish_plafon' => $ruang->kondisi_ruang->ket_finish_plafon,
                                    'rusak_finish_dinding' => $ruang->kondisi_ruang->rusak_finish_dinding,
                                    'ket_finish_dinding' => $ruang->kondisi_ruang->ket_finish_dinding,
                                    'rusak_finish_kpj' => $ruang->kondisi_ruang->rusak_finish_kpj,
                                    'ket_finish_kpj' => $ruang->kondisi_ruang->ket_finish_kpj,
                                    'berfungsi' => $ruang->kondisi_ruang->berfungsi,
                                    'nilai_saat_ini' => $ruang->kondisi_ruang->nilai_saat_ini,
                                ]
                            );
                        }
                    }
                }
                $this->info($sekolah->nama);
            }
        });
    }
}
