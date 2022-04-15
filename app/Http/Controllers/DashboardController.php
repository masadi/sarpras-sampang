<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\HelperModel;
use App\Sekolah;
use App\Bangunan;
use App\Ruang;
use App\Alat;
use App\Angkutan;
use App\Buku;
use App\Kondisi_bangunan;
use App\Kondisi_ruang;
use App\User;

class DashboardController extends Controller
{
    public function index(Request $request){
        $user = User::find($request->user_id);
        //sd = 5
        //smp = 6
        $all_data = [
            'rusak_ringan' => Sekolah::where(function($query) use ($user){
                if($user->hasRole('sd')){
                    $query->where('bentuk_pendidikan_id', 5);
                } elseif($user->hasRole('smp')){
                    $query->where('bentuk_pendidikan_id', 6);
                }
            })->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                $query->where('nilai_saat_ini', '>', 0);
                $query->where('nilai_saat_ini', '<=', 30);
            })->count(),
            'rusak_sedang' => Sekolah::where(function($query) use ($user){
                if($user->hasRole('sd')){
                    $query->where('bentuk_pendidikan_id', 5);
                } elseif($user->hasRole('smp')){
                    $query->where('bentuk_pendidikan_id', 6);
                }
            })->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                $query->where('nilai_saat_ini', '>', 30);
                $query->where('nilai_saat_ini', '<=', 45);
            })->count(),
            'rusak_berat' => Sekolah::where(function($query) use ($user){
                if($user->hasRole('sd')){
                    $query->where('bentuk_pendidikan_id', 5);
                } elseif($user->hasRole('smp')){
                    $query->where('bentuk_pendidikan_id', 6);
                }
            })->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                $query->where('nilai_saat_ini', '>', 45);
                $query->where('nilai_saat_ini', '<=', 65);
            })->count(),
            'rusak_sangat_berat' => Sekolah::where(function($query) use ($user){
                if($user->hasRole('sd')){
                    $query->where('bentuk_pendidikan_id', 5);
                } elseif($user->hasRole('smp')){
                    $query->where('bentuk_pendidikan_id', 6);
                }
            })->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                $query->where('nilai_saat_ini', '>', 65);
            })->count(),
            'kondisi_bangunan' => Kondisi_bangunan::where(function($query) use ($user){
                if($user->hasRole('sd')){
                    $query->whereHas('bangunan.tanah.sekolah', function($query){
                        $query->where('bentuk_pendidikan_id', 5);
                    });
                } elseif($user->hasRole('smp')){
                    $query->whereHas('bangunan.tanah.sekolah', function($query){
                        $query->where('bentuk_pendidikan_id', 6);    
                    });
                }
            })->where('tahun_pendataan_id', HelperModel::tahun_pendataan())->select(
                'tahun_pendataan_id',
                    DB::raw('count(*) jml_bangunan'),
                    DB::raw('sum(nilai_saat_ini = 0.00) baik'),
                    DB::raw('sum(nilai_saat_ini > 0 AND nilai_saat_ini <= 30) ringan'),
                    DB::raw('sum(nilai_saat_ini > 30 AND nilai_saat_ini <= 45) sedang'),
                    DB::raw('sum(nilai_saat_ini > 45 AND nilai_saat_ini <= 65) berat'),
                    DB::raw('sum(nilai_saat_ini > 65) total')
                )
                ->groupBy('tahun_pendataan_id')
                ->first(),
            'kondisi_ruang' => Kondisi_ruang::where(function($query) use ($user){
                if($user->hasRole('sd')){
                    $query->whereHas('ruang.bangunan.tanah.sekolah', function($query){
                        $query->where('bentuk_pendidikan_id', 5);
                    });
                } elseif($user->hasRole('smp')){
                    $query->whereHas('ruang.bangunan.tanah.sekolah', function($query){
                        $query->where('bentuk_pendidikan_id', 6);    
                    });
                }
            })->where('tahun_pendataan_id', HelperModel::tahun_pendataan())->select(
                'tahun_pendataan_id',
                    DB::raw('count(*) jml_ruang'),
                    DB::raw('sum(nilai_saat_ini = 0.00) baik'),
                    DB::raw('sum(nilai_saat_ini > 0 AND nilai_saat_ini <= 30) ringan'),
                    DB::raw('sum(nilai_saat_ini > 30 AND nilai_saat_ini <= 45) sedang'),
                    DB::raw('sum(nilai_saat_ini > 45 AND nilai_saat_ini <= 65) berat'),
                    DB::raw('sum(nilai_saat_ini > 65) total')
                )
                ->groupBy('tahun_pendataan_id')
                ->first(),
        ];
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
}
