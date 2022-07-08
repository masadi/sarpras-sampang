<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;
use Carbon\Carbon;
use Excel;
use App\Exports\SekolahExport;
use App\Sekolah;
class UnduhanController extends Controller
{
    public function index(Request $request){
        $function = 'get_'.request()->route('query');
        $function = str_replace('-','_', $function);
        return $this->{$function}($request);
    }
    public function rekap(Request $request){
        $jenjang = ($request->route('jenjang') == 'sd') ? 5 : 6;
        $all_data = Sekolah::where(function($query) use ($jenjang){
            $query->where('bentuk_pendidikan_id', $jenjang);
        })->with(['tanah.bangunan' => function($query){
            $query->with([
                'baik' => function($query){
                    //$query->where('tahun_pendataan_id', HelperModel::tahun_pendataan());
                    $query->where('tahun_pendataan_id', 2021);
                },
                'ringan' => function($query){
                    //$query->where('tahun_pendataan_id', HelperModel::tahun_pendataan());
                    $query->where('tahun_pendataan_id', 2021);
                },
                'sedang' => function($query){
                    //$query->where('tahun_pendataan_id', HelperModel::tahun_pendataan());
                    $query->where('tahun_pendataan_id', 2021);
                },
                'berat' => function($query){
                    //$query->where('tahun_pendataan_id', HelperModel::tahun_pendataan());
                    $query->where('tahun_pendataan_id', 2021);
                },
            ]);
        }])->get();
        $output = [];
        foreach($all_data as $data){
            foreach($data->tanah as $tanah){
                foreach($tanah->bangunan as $bangunan){
                    if($bangunan->baik){
                        $result['baik']['NAMA SEKOLAH'] = $data->nama;
                        $result['baik']['NAMA GEDUNG'] = $bangunan->nama;
                        $result['baik']['TAHUN DIBANGUN'] = $bangunan->tahun_bangun;
                        $result['baik']['TAHUN DIRENOVASI'] = $bangunan->tahun_renovasi;
                        $result['baik']['KONDISI SEKARANG'] = 'BAIK';
                    }
                    if($bangunan->ringan){
                        $result['ringan']['NAMA SEKOLAH'] = $data->nama;
                        $result['ringan']['NAMA GEDUNG'] = $bangunan->nama;
                        $result['ringan']['TAHUN DIBANGUN'] = $bangunan->tahun_bangun;
                        $result['ringan']['TAHUN DIRENOVASI'] = $bangunan->tahun_renovasi;
                        $result['ringan']['KONDISI SEKARANG'] = 'RUSAK RINGAN';
                    }
                    if($bangunan->sedang){
                        $result['sedang']['NAMA SEKOLAH'] = $data->nama;
                        $result['sedang']['NAMA GEDUNG'] = $bangunan->nama;
                        $result['sedang']['TAHUN DIBANGUN'] = $bangunan->tahun_bangun;
                        $result['sedang']['TAHUN DIRENOVASI'] = $bangunan->tahun_renovasi;
                        $result['sedang']['KONDISI SEKARANG'] = 'RUSAK SEDANG';
                    }
                    if($bangunan->berat){
                        $result['berat']['NAMA SEKOLAH'] = $data->nama;
                        $result['berat']['NAMA GEDUNG'] = $bangunan->nama;
                        $result['berat']['TAHUN DIBANGUN'] = $bangunan->tahun_bangun;
                        $result['berat']['TAHUN DIRENOVASI'] = $bangunan->tahun_renovasi;
                        $result['berat']['KONDISI SEKARANG'] = 'RUSAK BERAT';
                    }
                }
            }
            if(isset($result['baik'])){
                $output['baik'][] = $result['baik'];
            }
            if(isset($result['ringan'])){
                $output['ringan'][] = $result['ringan'];
            }
            if(isset($result['sedang'])){
                $output['sedang'][] = $result['sedang'];
            }
            if(isset($result['berat'])){
                $output['berat'][] = $result['berat'];
            }
            //dd($data);
        }
        if(isset($output['baik'])){
            $baik = collect($output['baik'])->unique('NAMA SEKOLAH');
        } else {
            $baik = [];
        }
        if(isset($output['ringan'])){
            $ringan = collect($output['ringan'])->unique('NAMA SEKOLAH');
        } else {
            $ringan = [];
        }
        if(isset($output['sedang'])){
            $sedang = collect($output['sedang'])->unique('NAMA SEKOLAH');
        } else {
            $sedang = [];
        }
        if(isset($output['berat'])){
            $berat = collect($output['berat'])->unique('NAMA SEKOLAH');
        } else {
            $berat = [];
        }
        $sheets = new SheetCollection([
            'BAIK' => $baik,
            'RUSAK RINGAN' => $ringan,
            'RUSAK SEDANG' => $sedang,
            'RUSAK BERAT' => $berat
        ]);
        return (new FastExcel($sheets))->download('KONDISI GEDUNG JENJANG '.strtoupper($request->route('jenjang')).'.xlsx');
    }
    public function get_download_sekolah($request){
        return Excel::download(new SekolahExport($request->rusak), 'DATA SEKOLAH KONDISI RUSAK '.strtoupper($request->rusak).'.xlsx');
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
        return (new FastExcel($all_data))->download('file.xlsx');
        dd($request->all());
    }
    public function nilai_instrumen($limit){
        return Excel::download(new InstrumenExport($limit), 'NILAI INSTRUMEN.xlsx');
    }
    public function nilai_aspek($limit){
        return Excel::download(new AspekExport($limit), 'NILAI ASPEK.xlsx');
    }
    public function get_detil_laporan(){
        $all_pendampingan = Laporan::with(['sekolah.sekolah_sasaran.sektor', 'pendamping'])->where('jenis_laporan_id', 1)->get();
        $no=1;
        $pendampingan = [];
        $belum_pendampingan = [];
        $verifikasi = [];
        $belum_verifikasi = [];
        foreach($all_pendampingan as $data_pendampingan){
            $tanggal_pelaksanaan = Carbon::parse($data_pendampingan->tanggal_pelaksanaan)->locale('id');
            $created_at = Carbon::parse($data_pendampingan->created_at)->locale('id');
            $pendampingan[] = [
                'No' => $no,
                'Provinsi' => $data_pendampingan->sekolah->provinsi,
                'Kabupaten/Kota' => $data_pendampingan->sekolah->kabupaten,
                'Nama Sekolah' => $data_pendampingan->sekolah->nama,
                'NPSN' => $data_pendampingan->sekolah->npsn,
                'Sektor CoE' => ($data_pendampingan->sekolah->sekolah_sasaran->sektor) ? $data_pendampingan->sekolah->sekolah_sasaran->sektor->nama : '-',
                'Nama Pendamping' => ($data_pendampingan->pendamping) ? $data_pendampingan->pendamping->nama : '-',
                'Tanggal Pelaksanaan' => $tanggal_pelaksanaan->isoFormat('Do MMMM YYYY'),
                'Tanggal Pelaporan' => $created_at->isoFormat('Do MMMM YYYY'),
                'Jumlah dan nama IDUKA' => $data_pendampingan->jumlah_iduka,
                'Berapakah jumlah lulusan dari sekolah yang bapak/ibu dampingi dapat diserap oleh IDUKA?' => $data_pendampingan->lulusan,
                'Berapakah jumlah lulusan dari sekolah yang bapak/ibu dampingi sudah diserap oleh IDUKA?' => $data_pendampingan->lulusan_all,
                'Perkembangan SMK' => $data_pendampingan->perkembangan_smk,
                'Kesimpulan dan Saran' => $data_pendampingan->kesimpulan_saran,
            ];
            $no++;
        }
        $sekolah_sudah_monev = Sekolah::has('sekolah_sasaran')->has('smk_coe')->with(['laporan', 'sekolah_sasaran' => function($query){
            $query->with(['sektor', 'pendamping']);
        }])->whereHas('laporan', function (Builder $query) {
            $query->where('jenis_laporan_id', 5);
        })->get();
        /*$no=1;
        foreach($sekolah_sudah_monev as $data_sudah_monev){
            $tanggal_pelaksanaan_monev = Carbon::parse($data_sudah_monev->tanggal_pelaksanaan)->locale('id');
            $created_at_monev = Carbon::parse($data_sudah_monev->laporan->created_at)->locale('id');
            $sudah_monev[] = [
                'No' => $no,
                'Provinsi' => $data_sudah_monev->provinsi,
                'Kabupaten/Kota' => $data_sudah_monev->kabupaten,
                'Nama Sekolah' => $data_sudah_monev->nama,
                'NPSN' => $data_sudah_monev->npsn,
                'Sektor CoE' => ($data_sudah_monev->sekolah_sasaran->sektor) ? $data_sudah_monev->sekolah_sasaran->sektor->nama : '-',
                'Nama Pendamping' => ($data_sudah_monev->sekolah_sasaran->pendamping) ? $data_sudah_monev->sekolah_sasaran->pendamping->nama : '-',
                'Tanggal Pelaksanaan' => $tanggal_pelaksanaan->isoFormat('Do MMMM YYYY'),
                'Tanggal Pelaporan' => $created_at->isoFormat('Do MMMM YYYY'),
                'Keterangan' => '',
            ];
            $no++;
        }*/
        $sheets = new SheetCollection([
            'Laporan Pendampingan' => $pendampingan,
            //'Laporan Monev' => $sudah_monev,
        ]);
        return (new FastExcel($sheets))->download('Detil Laporan Pendampingan Penjaminan Mutu SMK CoE Tahun 2020.xlsx');
    }
    public function get_laporan($request){
        $all_pendampingan = Laporan::with(['sekolah.sekolah_sasaran.sektor', 'pendamping'])->where('jenis_laporan_id', 1)->get();
        $no=1;
        $pendampingan = [];
        $belum_pendampingan = [];
        $verifikasi = [];
        $belum_verifikasi = [];
        foreach($all_pendampingan as $data_pendampingan){
            $tanggal_pelaksanaan = Carbon::parse($data_pendampingan->tanggal_pelaksanaan)->locale('id');
            $created_at = Carbon::parse($data_pendampingan->created_at)->locale('id');
            $pendampingan[] = [
                'No' => $no,
                'Provinsi' => $data_pendampingan->sekolah->provinsi,
                'Kabupaten/Kota' => $data_pendampingan->sekolah->kabupaten,
                'Nama Sekolah' => $data_pendampingan->sekolah->nama,
                'NPSN' => $data_pendampingan->sekolah->npsn,
                'Sektor CoE' => ($data_pendampingan->sekolah->sekolah_sasaran->sektor) ? $data_pendampingan->sekolah->sekolah_sasaran->sektor->nama : '-',
                'Nama Pendamping' => ($data_pendampingan->pendamping) ? $data_pendampingan->pendamping->nama : '-',
                'Tanggal Pelaksanaan' => $tanggal_pelaksanaan->isoFormat('Do MMMM YYYY'),
                'Tanggal Pelaporan' => $created_at->isoFormat('Do MMMM YYYY'),
                'Keterangan' => '',
            ];
            $no++;
        }
        $all_verifikasi = Laporan::with(['sekolah.sekolah_sasaran.sektor', 'verifikator'])->where('jenis_laporan_id', 3)->get();
        $no=1;
        foreach($all_verifikasi as $data_verifikasi){
            $tanggal_pelaksanaan = Carbon::parse($data_verifikasi->tanggal_pelaksanaan)->locale('id');
            $created_at = Carbon::parse($data_verifikasi->created_at)->locale('id');
            $verifikasi[] = [
                'No' => $no,
                'Provinsi' => $data_verifikasi->sekolah->provinsi,
                'Kabupaten/Kota' => $data_verifikasi->sekolah->kabupaten,
                'Nama Sekolah' => $data_verifikasi->sekolah->nama,
                'NPSN' => $data_verifikasi->sekolah->npsn,
                'Sektor CoE' => ($data_verifikasi->sekolah->sekolah_sasaran->sektor) ? $data_verifikasi->sekolah->sekolah_sasaran->sektor->nama : '-',
                'Nama Verifikator' => ($data_verifikasi->verifikator) ? $data_verifikasi->verifikator->name : '-',
                'Tanggal Pelaksanaan' => $tanggal_pelaksanaan->isoFormat('Do MMMM YYYY'),
                'Tanggal Pelaporan' => $created_at->isoFormat('Do MMMM YYYY'),
                'Keterangan' => '',
            ];
            $no++;
        }
        $sekolah_belum_pendampingan = Sekolah::has('sekolah_sasaran')->has('smk_coe')->with(['sekolah_sasaran' => function($query){
            $query->with(['sektor', 'pendamping']);
        }])->whereDoesntHave('laporan', function (Builder $query) {
            $query->where('jenis_laporan_id', 1);
        })->get();
        $no=1;
        foreach($sekolah_belum_pendampingan as $data_belum_pendampingan){
            $belum_pendampingan[] = [
                'No' => $no,
                'Provinsi' => $data_belum_pendampingan->provinsi,
                'Kabupaten/Kota' => $data_belum_pendampingan->kabupaten,
                'Nama Sekolah' => $data_belum_pendampingan->nama,
                'NPSN' => $data_belum_pendampingan->npsn,
                'Sektor CoE' => ($data_belum_pendampingan->sekolah_sasaran->sektor) ? $data_belum_pendampingan->sekolah_sasaran->sektor->nama : '-',
                'Nama Pendamping' => ($data_belum_pendampingan->sekolah_sasaran->pendamping) ? $data_belum_pendampingan->sekolah_sasaran->pendamping->nama : '-',
                'Tanggal Pelaksanaan' => '-',
                'Tanggal Pelaporan' => '-',
                'Keterangan' => '',
            ];
            $no++;
        }
        $sekolah_belum_verifikasi = Sekolah::has('sekolah_sasaran')->has('smk_coe')->with(['sekolah_sasaran' => function($query){
            $query->with(['sektor', 'pendamping']);
        }])->whereDoesntHave('laporan', function (Builder $query) {
            $query->where('jenis_laporan_id', 3);
        })->get();
        $no=1;
        foreach($sekolah_belum_verifikasi as $data_belum_verifikasi){
            $nama_verifikator = '-';
            if($data_belum_verifikasi->sekolah_sasaran->verifikator){
                if($data_belum_verifikasi->sekolah_sasaran->verifikator_id != '84ff9f29-1bd0-462f-976f-4c512dc22cc2'){
                    $nama_verifikator = $data_belum_verifikasi->sekolah_sasaran->verifikator->name;
                }
            }
            $belum_verifikasi[] = [
                'No' => $no,
                'Provinsi' => $data_belum_verifikasi->provinsi,
                'Kabupaten/Kota' => $data_belum_verifikasi->kabupaten,
                'Nama Sekolah' => $data_belum_verifikasi->nama,
                'NPSN' => $data_belum_verifikasi->npsn,
                'Sektor CoE' => ($data_belum_verifikasi->sekolah_sasaran->sektor) ? $data_belum_verifikasi->sekolah_sasaran->sektor->nama : '-',
                'Nama Verifikator' => $nama_verifikator, 
                'Tanggal Pelaksanaan' => '-',
                'Tanggal Pelaporan' => '-',
                'Keterangan' => '',
            ];
            $no++;
        }
        /************************ */
        $sekolah_belum_monev = Sekolah::has('sekolah_sasaran')->has('smk_coe')->with(['sekolah_sasaran' => function($query){
            $query->with(['sektor', 'pendamping']);
        }])->whereDoesntHave('laporan', function (Builder $query) {
            $query->where('jenis_laporan_id', 5);
        })->get();
        $no=1;
        foreach($sekolah_belum_monev as $data_belum_monev){
            $belum_monev[] = [
                'No' => $no,
                'Provinsi' => $data_belum_monev->provinsi,
                'Kabupaten/Kota' => $data_belum_monev->kabupaten,
                'Nama Sekolah' => $data_belum_monev->nama,
                'NPSN' => $data_belum_monev->npsn,
                'Sektor CoE' => ($data_belum_monev->sekolah_sasaran->sektor) ? $data_belum_monev->sekolah_sasaran->sektor->nama : '-',
                'Nama Pendamping' => ($data_belum_monev->sekolah_sasaran->pendamping) ? $data_belum_monev->sekolah_sasaran->pendamping->nama : '-',
                'Tanggal Pelaksanaan' => '-',
                'Tanggal Pelaporan' => '-',
                'Keterangan' => '',
            ];
            $no++;
        }
        $sekolah_sudah_monev = Sekolah::has('sekolah_sasaran')->has('smk_coe')->with(['laporan', 'sekolah_sasaran' => function($query){
            $query->with(['sektor', 'pendamping']);
        }])->whereHas('laporan', function (Builder $query) {
            $query->where('jenis_laporan_id', 5);
        })->get();
        $no=1;
        foreach($sekolah_sudah_monev as $data_sudah_monev){
            $tanggal_pelaksanaan_monev = Carbon::parse($data_sudah_monev->tanggal_pelaksanaan)->locale('id');
            $created_at_monev = Carbon::parse($data_sudah_monev->laporan->created_at)->locale('id');
            $sudah_monev[] = [
                'No' => $no,
                'Provinsi' => $data_sudah_monev->provinsi,
                'Kabupaten/Kota' => $data_sudah_monev->kabupaten,
                'Nama Sekolah' => $data_sudah_monev->nama,
                'NPSN' => $data_sudah_monev->npsn,
                'Sektor CoE' => ($data_sudah_monev->sekolah_sasaran->sektor) ? $data_sudah_monev->sekolah_sasaran->sektor->nama : '-',
                'Nama Pendamping' => ($data_sudah_monev->sekolah_sasaran->pendamping) ? $data_sudah_monev->sekolah_sasaran->pendamping->nama : '-',
                'Tanggal Pelaksanaan' => $tanggal_pelaksanaan->isoFormat('Do MMMM YYYY'),
                'Tanggal Pelaporan' => $created_at->isoFormat('Do MMMM YYYY'),
                'Keterangan' => '',
            ];
            $no++;
        }
        /** */
        $sheets = new SheetCollection([
            'Sudah Pendampingan' => $pendampingan,
            'Belum Pendampingan' => $belum_pendampingan,
            'Sudah Verifikasi' => $verifikasi,
            'Belum Verifikasi' => $belum_verifikasi,
            'Sudah Monev' => $sudah_monev,
            'Belum Monev' => $belum_monev,
        ]);
        return (new FastExcel($sheets))->download('Rekapitulasi Laporan Rapor Mutu SMK CoE Tahun 2020.xlsx');
    }
    public function get_verifikasi(){
        $jenis = Jenis_rapor::where('jenis', 'verval')->first();
        $sekolah_verifikasi = Sekolah::has('smk_coe')->whereHas('rapor_mutu', function($query) use ($jenis){
            $query->where('jenis_rapor_id', $jenis->id);
        })->with(['nilai_akhir' => function($query){
            $query->whereNull('verifikator_id');
        }, 'nilai_akhir_verifikasi' => function($query){
            $query->whereNotNull('verifikator_id');
            $query->where('verifikator_id', '<>', '84ff9f29-1bd0-462f-976f-4c512dc22cc2');
        }])->get();
        $sekolah_belum_verifikasi = Sekolah::has('smk_coe')->whereDoesntHave('rapor_mutu', function($query) use ($jenis){
            $query->where('jenis_rapor_id', $jenis->id);
        })->get();
        $verifikasi = [];
        $belum_verifikasi = [];
        $no = 1;
        foreach($sekolah_verifikasi as $s_verifikasi){
            $nama_verifikator = '-';
            if($s_verifikasi->sekolah_sasaran->verifikator){
                if($s_verifikasi->sekolah_sasaran->verifikator_id != '84ff9f29-1bd0-462f-976f-4c512dc22cc2'){
                    $nama_verifikator = $s_verifikasi->sekolah_sasaran->verifikator->name;
                }
            }
            $verifikasi[] = [
                'No' => $no,
                'Provinsi' => $s_verifikasi->provinsi,
                'Kabupaten/Kota' => $s_verifikasi->kabupaten,
                'Nama Sekolah' => $s_verifikasi->nama,
                'NPSN' => $s_verifikasi->npsn,
                'Sektor CoE' => ($s_verifikasi->sekolah_sasaran->sektor) ? $s_verifikasi->sekolah_sasaran->sektor->nama : '-',
                'Nama Verifikator' => $nama_verifikator,
                'Nilai Sekolah' => $s_verifikasi->nilai_akhir->nilai,
                'Nilai Verifikasi' => $s_verifikasi->nilai_akhir_verifikasi->nilai,
            ];
            $no++;
        }
        $no=1;
        foreach($sekolah_belum_verifikasi as $s_belum_verifikasi){
            $nama_verifikator = '-';
            if($s_belum_verifikasi->sekolah_sasaran->verifikator){
                if($s_belum_verifikasi->sekolah_sasaran->verifikator_id != '84ff9f29-1bd0-462f-976f-4c512dc22cc2'){
                    $nama_verifikator = $s_belum_verifikasi->sekolah_sasaran->verifikator->name;
                }
            }
            $belum_verifikasi[] = [
                'No' => $no,
                'Provinsi' => $s_belum_verifikasi->provinsi,
                'Kabupaten/Kota' => $s_belum_verifikasi->kabupaten,
                'Nama Sekolah' => $s_belum_verifikasi->nama,
                'NPSN' => $s_belum_verifikasi->npsn,
                'Sektor CoE' => ($s_belum_verifikasi->sekolah_sasaran->sektor) ? $s_belum_verifikasi->sekolah_sasaran->sektor->nama : '-',
                'Nama Verifikator' => $nama_verifikator,
            ];
            $no++;
        }
        $sheets = new SheetCollection([
            'Sudah Verifikasi' => $verifikasi,
            'Belum Verifikasi' => $belum_verifikasi,
        ]);
        return (new FastExcel($sheets))->download('Rekapitulasi Laporan Verifikasi SMK CoE Tahun 2020.xlsx');
    }
    public function isian_instrumen($sekolah_id){
        $sekolah = Sekolah::find($sekolah_id);
        return Excel::download(new NilaiExport($sekolah_id), 'NILAI INSTRUMEN '.$sekolah->nama.'.xlsx');
    }
}
