<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;
use Carbon\Carbon;
use App\Sekolah;

class UnduhanController extends Controller
{
    public function index(Request $request){
        $function = 'get_'.request()->route('query');
        $function = str_replace('-','_', $function);
        return $this->{$function}($request);
    }
    public function get_download_sekolah($request){
        $all_data = Sekolah::where(function($query){
            if(request()->rusak){
                if(request()->rusak == 'ringan'){
                    $query->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                        $query->where('nilai_saat_ini', '>', 0);
                        $query->where('nilai_saat_ini', '<=', 30);
                    });
                } elseif(request()->rusak == 'sedang'){
                    $query->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                        $query->where('nilai_saat_ini', '>', 30);
                        $query->where('nilai_saat_ini', '<=', 45);
                    });
                } elseif(request()->rusak == 'berat'){
                    $query->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                        $query->where('nilai_saat_ini', '>', 45);
                        $query->where('nilai_saat_ini', '<=', 65);
                    });
                } elseif(request()->rusak == 'sangat-berat'){
                    $query->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                        $query->where('nilai_saat_ini', '>', 65);
                    });
                }
            }
        })->get();
        dd($request->all());
        return (new FastExcel($all_data))->download('Data Sekolah Kondisi Rusak '.ucfirst($request->rusak).'.xlsx');
    }
}
