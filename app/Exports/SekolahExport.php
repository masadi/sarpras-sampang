<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Instrumen;
use App\Sekolah;
use App\Komponen;

class SekolahExport implements FromView
{
    private $rusak;

    public function __construct($rusak) 
    {
        $this->rusak = $rusak;
    }
    public function view(): View
    {
        $all_data = Sekolah::where(function($query){
            if($this->rusak){
                if($this->rusak == 'ringan'){
                    $query->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                        $query->where('nilai_saat_ini', '>', 0);
                        $query->where('nilai_saat_ini', '<=', 30);
                    });
                } elseif($this->rusak == 'sedang'){
                    $query->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                        $query->where('nilai_saat_ini', '>', 30);
                        $query->where('nilai_saat_ini', '<=', 45);
                    });
                } elseif($this->rusak == 'berat'){
                    $query->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                        $query->where('nilai_saat_ini', '>', 45);
                        $query->where('nilai_saat_ini', '<=', 65);
                    });
                } elseif($this->rusak == 'sangat-berat'){
                    $query->whereHas('tanah.bangunan.ruang.kondisi_ruang', function($query){
                        $query->where('nilai_saat_ini', '>', 65);
                    });
                }
            }
        })->get();
        return view('exports.sekolah', [
            'all_data' => $all_data,
        ]);
    }
}
