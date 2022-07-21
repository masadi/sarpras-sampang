<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Sekolah;
use App\Wilayah;

class GenerateTk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:tk';

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
        $sync_sekolah = Http::get('http://103.40.55.242/erapor_server/sync/get_sekolah/69906132');
        $sekolah =$sync_sekolah->object();
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
            [
                'npsn' => $sekolah->npsn,
            ],
            [
                'sekolah_id' => strtolower($sekolah->sekolah_id),    
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
                'bentuk_pendidikan_id' => $sekolah->bentuk_pendidikan_id,
            ]
        );
        $this->info($sekolah->nama. ' berhasil disimpan');
        $key = 'tk';
        $role = \App\Role::firstOrCreate([
            'name' => $key,
            'display_name' => ucwords(str_replace('_', ' ', $key)),
            'description' => ucwords(str_replace('_', ' ', $key))
        ]);
        $permissions = [];

        $this->info('Creating Role '. strtoupper($key));
        $modules = [
            'news' => 'c,r,u,d',
        ];
        $mapPermission = collect(config('laratrust_seeder.permissions_map'));
        // Reading role permission modules
        foreach ($modules as $module => $value) {

            foreach (explode(',', $value) as $p => $perm) {

                $permissionValue = $mapPermission->get($perm);

                $permissions[] = \App\Permission::firstOrCreate([
                    'name' => $module . '-' . $permissionValue,
                    'display_name' => ucfirst($permissionValue) . ' ' . ucfirst($module),
                    'description' => ucfirst($permissionValue) . ' ' . ucfirst($module),
                ])->id;

                $this->info('Creating Permission to '.$permissionValue.' for '. $module);
            }
        }
        // Attach all permissions to the role
        $role->permissions()->sync($permissions);
        $user = \App\User::firstOrCreate([
            'email' => $key.'@disdik.sampangkab.go.id',
            ],
            [
                'name' => ucfirst($key),
                'username' => $key,
                'password' => bcrypt('12345678')
            ]
        );
        if(!$user->hasRole($role)){
            $user->attachRole($role);
        }
    }
}
