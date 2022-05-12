<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Sekolah;
use App\User;
use App\Role;
use App\Wilayah;

class GenerateSd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:sd';

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
        $collection = (new FastExcel)->import('public/template_sd.xlsx');
        $i=1;
        foreach($collection as $data){
            $sync_sekolah = Http::get('http://103.40.55.242/erapor_server/sync/get_sekolah/'.$data['NPSN']);
            $sekolah = json_decode($sync_sekolah->body());
            if(isset($sekolah->data[0])){
                $sekolah = $sekolah->data[0];
                $wilayah = Wilayah::with('parrentRecursive')->withTrashed()->find($sekolah->kode_wilayah);
                $kecamatan_id = NULL;
                $kabupaten_id = NULL;
                $provinsi_id = NULL;
                $kecamatan = NULL;
                $kabupaten = NULL;
                $provinsi = NULL;
                if($wilayah->parrentRecursive){
                    $kecamatan_id = $wilayah->parrentRecursive->kode_wilayah;
                    $kecamatan = $wilayah->parrentRecursive->nama;
                    if($wilayah->parrentRecursive->parrentRecursive){
                        $kabupaten_id = $wilayah->parrentRecursive->parrentRecursive->kode_wilayah;
                        $kabupaten = $wilayah->parrentRecursive->parrentRecursive->nama;
                    }
                    if($wilayah->parrentRecursive->parrentRecursive->parrentRecursive){
                        $provinsi_id = $wilayah->parrentRecursive->parrentRecursive->parrentRecursive->kode_wilayah;
                        $provinsi = $wilayah->parrentRecursive->parrentRecursive->parrentRecursive->nama;
                    }
                }
                Sekolah::updateOrCreate(
                    ['sekolah_id' => strtolower($sekolah->sekolah_id)],
                    [
                        'npsn' => $sekolah->npsn,
                        'nama' => $sekolah->nama,
                        'nss' => $sekolah->nss,
                        'alamat' => $sekolah->alamat_jalan,
                        'desa_kelurahan' => $sekolah->desa_kelurahan,
                        'kecamatan' => $kecamatan,
                        'kode_wilayah' => $sekolah->kode_wilayah,
                        'kabupaten' => $kabupaten,
                        'provinsi' => $provinsi,
                        'kode_pos' => $sekolah->kode_pos,
                        'lintang' => $sekolah->lintang,
                        'bujur' => $sekolah->bujur,
                        'no_telp' => $sekolah->nomor_telepon,
                        'no_fax' => $sekolah->nomor_fax,
                        'email' => $sekolah->email,
                        'website' => $sekolah->website,
                        'status_sekolah' => $sekolah->status_sekolah,
                        'kecamatan_id' => $kecamatan_id,
                        'kabupaten_id' => $kabupaten_id,
                        'provinsi_id' => $provinsi_id,
                    ]
                );
                $this->info($i.'. '.$sekolah->nama. ' berhasil disimpan');
            } else {
                $this->error($i.'. '.$data['Nama']. ' gagal disimpan');
            }
            $i++;
        }
    }
}
