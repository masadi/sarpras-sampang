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

class DashboardController extends Controller
{
    public function index(Request $request){
        $all_data = [
            'rusak_ringan' => Sekolah::whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                $query->where('nilai_saat_ini', '>', 0);
                $query->where('nilai_saat_ini', '<=', 30);
            })->count(),
            'rusak_sedang' => Sekolah::whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                $query->where('nilai_saat_ini', '>', 30);
                $query->where('nilai_saat_ini', '<=', 45);
            })->count(),
            'rusak_berat' => Sekolah::whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                $query->where('nilai_saat_ini', '>', 45);
                $query->where('nilai_saat_ini', '<=', 65);
            })->count(),
            'rusak_sangat_berat' => Sekolah::whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                $query->where('nilai_saat_ini', '>', 65);
            })->count(),
            'kondisi_bangunan' => Kondisi_bangunan::where('tahun_pendataan_id', HelperModel::tahun_pendataan())->select(
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
            'kondisi_ruang' => Kondisi_ruang::where('tahun_pendataan_id', HelperModel::tahun_pendataan())->select(
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
