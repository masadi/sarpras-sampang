<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sekolah;
use App\Bangunan;
use App\Ruang;
use App\Alat;
use App\Angkutan;
use App\Buku;
class DashboardController extends Controller
{
    public function index(Request $request){
        $all_data = [
            'jml_sekolah' => Sekolah::count(),
            'jml_bangunan' => Bangunan::count(),
            'jml_ruang' => Ruang::count(),
            'jml_alat_dll' => Alat::count() + Angkutan::count() + Buku::count(),
        ];
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
}
