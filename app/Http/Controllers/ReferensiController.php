<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Sekolah;
use App\User;
use App\HelperModel;
use App\Tahun_pendataan;
use Carbon\Carbon;
use File;
use Validator;
use PDF;
use Artisan;
use App\Tanah;
use App\Bangunan;
use App\Ruang;
use App\Alat;
use App\Angkutan;
use App\Buku;
use App\Jenis_prasarana;
use App\Jenis_sarana;
use App\Status_kepemilikan_sarpras;
use App\Mata_pelajaran;
use App\Penerbit;
use App\Wilayah;
use App\Data_sekolah;
use App\Foto;

class ReferensiController extends Controller
{
    public function index(Request $request, $query){
        $function = 'get_'.str_replace('-', '_', $query);
        return $this->{$function}($request);
    }
    public function get_tanah($request){
        $all_data = Tanah::where(function($query){
            if(request()->sekolah_id){
                $query->where('sekolah_id', request()->sekolah_id);
            }
        })->orderBy(request()->sortby, request()->sortbydesc)
            ->when(request()->q, function($all_data) {
                $all_data = $all_data->where('nama', 'ilike', '%' . request()->q . '%')
                ->orWhere('kepemilikan', 'ilike', '%' . request()->q . '%')
                ->orWhere('keterangan', 'ilike', '%' . request()->q . '%');
        })->with(['sekolah', 'kepemilikan'])->paginate(request()->per_page); //KEMUDIAN LOAD PAGINATIONNYA BERDASARKAN LOAD PER_PAGE YANG DIINGINKAN OLEH USER
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_bangunan($request){
        $all_data = Bangunan::where(function($query){
            if(request()->sekolah_id){
                $query->whereHas('tanah', function($query){
                    $query->where('sekolah_id', request()->sekolah_id);
                });
            }
        })->orderBy(request()->sortby, request()->sortbydesc)
            ->when(request()->q, function($all_data) {
                $all_data = $all_data->where('nama', 'ilike', '%' . request()->q . '%')
                ->orWhere('kepemilikan', 'ilike', '%' . request()->q . '%')
                ->orWhere('keterangan', 'ilike', '%' . request()->q . '%');
        })->with(['foto', 'tanah.sekolah', 'kepemilikan'])->paginate(request()->per_page); //KEMUDIAN LOAD PAGINATIONNYA BERDASARKAN LOAD PER_PAGE YANG DIINGINKAN OLEH USER
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_ruang($request){
        $all_data = Ruang::where(function($query){
            if(request()->sekolah_id){
                $query->whereHas('bangunan', function($query){
                    $query->whereHas('tanah', function($query){
                        $query->where('sekolah_id', request()->sekolah_id);
                    });
                });
            }
        })->orderBy(request()->sortby, request()->sortbydesc)
            ->when(request()->q, function($all_data) {
                $all_data = $all_data->where('nama', 'ilike', '%' . request()->q . '%')
                ->orWhere('kepemilikan', 'ilike', '%' . request()->q . '%')
                ->orWhere('keterangan', 'ilike', '%' . request()->q . '%');
        })->with(['foto', 'bangunan.tanah.sekolah', 'jenis_prasarana'])->paginate(request()->per_page); //KEMUDIAN LOAD PAGINATIONNYA BERDASARKAN LOAD PER_PAGE YANG DIINGINKAN OLEH USER
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_alat($request){
        $all_data = Alat::where(function($query){
            if(request()->sekolah_id){
                $query->whereHas('ruang', function($query){
                    $query->whereHas('bangunan', function($query){
                        $query->whereHas('tanah', function($query){
                            $query->where('sekolah_id', request()->sekolah_id);
                        });
                    });
                });
            }
        })->orderBy(request()->sortby, request()->sortbydesc)
            ->when(request()->q, function($all_data) {
                $all_data = $all_data->where('nama', 'ilike', '%' . request()->q . '%')
                ->orWhere('kepemilikan', 'ilike', '%' . request()->q . '%')
                ->orWhere('keterangan', 'ilike', '%' . request()->q . '%');
        })->with(['ruang.bangunan.tanah.sekolah', 'kepemilikan', 'jenis_sarana'])->paginate(request()->per_page); //KEMUDIAN LOAD PAGINATIONNYA BERDASARKAN LOAD PER_PAGE YANG DIINGINKAN OLEH USER
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_angkutan($request){
        $all_data = Angkutan::where(function($query){
            if(request()->sekolah_id){
                $query->where('sekolah_id', request()->sekolah_id);
            }
        })->orderBy(request()->sortby, request()->sortbydesc)
            ->when(request()->q, function($all_data) {
                $all_data = $all_data->where('nama', 'ilike', '%' . request()->q . '%')
                ->orWhere('kepemilikan', 'ilike', '%' . request()->q . '%')
                ->orWhere('keterangan', 'ilike', '%' . request()->q . '%');
        })->with(['sekolah', 'kepemilikan', 'jenis_sarana'])->paginate(request()->per_page); //KEMUDIAN LOAD PAGINATIONNYA BERDASARKAN LOAD PER_PAGE YANG DIINGINKAN OLEH USER
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_buku($request){
        $all_data = Buku::where(function($query){
            if(request()->sekolah_id){
                $query->where('sekolah_id', request()->sekolah_id);
            }
        })->orderBy(request()->sortby, request()->sortbydesc)
            ->when(request()->q, function($all_data) {
                $all_data = $all_data->where('judul', 'ilike', '%' . request()->q . '%')
                ->orWhere('kepemilikan', 'ilike', '%' . request()->q . '%')
                ->orWhere('keterangan', 'ilike', '%' . request()->q . '%');
        })->with(['sekolah', 'mata_pelajaran'])->paginate(request()->per_page); //KEMUDIAN LOAD PAGINATIONNYA BERDASARKAN LOAD PER_PAGE YANG DIINGINKAN OLEH USER
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_all_sekolah($request){
        $all_data = Sekolah::select('sekolah_id', 'nama')->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_kecamatan($request){
        $all_data = Wilayah::select('kode_wilayah', 'nama')->where('id_level_wilayah', 3)->where('mst_kode_wilayah', '052700 ')->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_desa($request){
        $all_data = Wilayah::select('kode_wilayah', 'nama')->where('id_level_wilayah', 4)->where('mst_kode_wilayah', $request->kecamatan_id)->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_all_mapel($request){
        $all_data = Mata_pelajaran::select('mata_pelajaran_id', 'nama')->whereHas('mapel', function($query) use ($request){
            $query->where('tingkat_pendidikan_id', $request->tingkat_pendidikan_id);
        })->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_all_tanah($request){
        $all_data = Tanah::select('tanah_id', 'nama')->where('sekolah_id', $request->sekolah_id)->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_all_bangunan($request){
        $all_data = Bangunan::select('bangunan_id', 'nama')->whereHas('tanah', function($query) use ($request){
            $query->where('sekolah_id', $request->sekolah_id);
        })->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_all_ruang($request){
        $all_data = Ruang::select('ruang_id', 'nama')->whereHas('bangunan.tanah', function($query) use ($request){
            //$query->whereHas('tanah', function($query) use ($request){
                $query->where('sekolah_id', $request->sekolah_id);
            //});
        })->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_all_jenis_prasarana($request){
        $all_data = Jenis_prasarana::select('id', 'nama')->where('a_ruang', 1)->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_all_jenis_sarana($request){
        $all_data = Jenis_sarana::select('id', 'nama')->where($request->data, 1)->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_all_penerbit($request){
        $all_data = Penerbit::select('penerbit_id', 'nama')->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_kepemilikan($request){
        $all_data = Status_kepemilikan_sarpras::select('kepemilikan_sarpras_id', 'nama')->get();//->pluck('nama', 'sekolah_id');
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function get_sekolah($request)
    {
        $sortBy = request()->sortby;
        $all_data = Sekolah::where(function($query){
            if(request()->q){
                $query->where(function($query){
                    $query->where('nama', 'ilike', '%' . request()->q . '%');
                    if(request()->sekolah_id){
                        $query->where('sekolah_id', request()->sekolah_id);
                    }
                });
                $query->orWhere(function($query){
                    $query->where('npsn', 'ilike', '%' . request()->q . '%');
                    if(request()->sekolah_id){
                        $query->where('sekolah_id', request()->sekolah_id);
                    }
                });
            } else {
                if(request()->sekolah_id){
                    $query->where('sekolah_id', request()->sekolah_id);
                }
            }
        })->with(['user', 'data_sekolah'])->orderBy($sortBy, request()->sortbydesc)
            /*->when(request()->q, function($all_data) {
                $all_data = $all_data->where('nama', 'ilike', '%' . request()->q . '%');
                $all_data->orWhere('npsn', 'ilike', '%' . request()->q . '%');
                $all_data->orWhere('kecamatan', 'ilike', '%' . request()->q . '%');
                $all_data->orWhere('kabupaten', 'ilike', '%' . request()->q . '%');
                $all_data->orWhere('provinsi', 'ilike', '%' . request()->q . '%');
        })*/->paginate(request()->per_page); //KEMUDIAN LOAD PAGINATIONNYA BERDASARKAN LOAD PER_PAGE YANG DIINGINKAN OLEH USER
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function cetak(Request $request){
        $data['all_komponen'] = Komponen::with(['aspek.instrumen' => function($query){
            $query->with(['jawaban' => function($query){
                $query->where('user_id', request()->user_id);
                $query->whereNull('verifikator_id');
            }]);
            $query->with(['subs']);
            $query->where('urut', 0);
        }])->get();
        $data['user'] = User::with(['sekolah'])->find(request()->user_id); 
        //return view('cetak.instrumen', $data);
        $pdf = PDF::loadView('cetak.instrumen', $data, [], [
            'format' => [220, 330],
        ]);
        //return $pdf->stream('instrumen.pdf');
		return $pdf->download('instrumen.pdf');
    }
    public function simpan_data(Request $request){
        if($request->route('query') == 'tanah'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'nama.required'	=> 'Nama tidak boleh kosong',
                'no_sertifikat_tanah.required'	=> 'Nomor Sertifikat Tanah tidak boleh kosong',
                'panjang.required'	=> 'Panjang (m) tidak boleh kosong',
                'panjang.numeric'	=> 'Panjang (m) harus berupa angka',
                'lebar.required'	=> 'Lebar (m) tidak boleh kosong',
                'lebar.numeric'	=> 'Lebar (m) harus berupa angka',
                'luas.required' => 'Luas (m<sup>2</sup>) tidak boleh kosong',
                'luas.numeric'	=> 'Luas (m) harus berupa angka',
                'kepemilikan_sarpras_id.required' => 'Kepemilikan tidak boleh kosong',
                'luas_lahan_tersedia.required' => 'Luas Lahan Tersedia (m<sup>2</sup>) tidak boleh kosong',
                'luas_lahan_tersedia.numeric'	=> 'Luas Lahan Tersedia (m<sup>2</sup>) harus berupa angka',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'nama' => 'required',
                'no_sertifikat_tanah' => 'required',
                'panjang' => 'required|numeric',
                'lebar' => 'required|numeric',
                'luas' => 'required|numeric',
                'kepemilikan_sarpras_id' => 'required',
                'luas_lahan_tersedia' => 'required|numeric',
            ],
            $messages
            )->validate();
            $insert_data = Tanah::create([
                'sekolah_id' => $request->sekolah_id['sekolah_id'],
                'nama' => $request->nama,
                'no_sertifikat_tanah' => $request->no_sertifikat_tanah,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'luas' => $request->luas,
                'luas_lahan_tersedia' => $request->luas_lahan_tersedia,
                'kepemilikan_sarpras_id' => $request->kepemilikan_sarpras_id['kepemilikan_sarpras_id'],
                'keterangan' => $request->keterangan,
            ]);
            return response()->json(['status' => 'success', 'data' => $insert_data]);
        } elseif($request->route('query') == 'bangunan'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'tanah_id.required'	=> 'Tanah tidak boleh kosong',
                'nama.required'	=> 'Nama tidak boleh kosong',
                'imb.required'	=> 'Nomor IMB tidak boleh kosong',
                'panjang.required'	=> 'Panjang (m) tidak boleh kosong',
                'panjang.numeric'	=> 'Panjang (m) harus berupa angka',
                'lebar.required'	=> 'Lebar (m) tidak boleh kosong',
                'lebar.numeric'	=> 'Lebar (m) harus berupa angka',
                'luas.required' => 'Luas (m<sup>2</sup>) tidak boleh kosong',
                'luas.numeric'	=> 'Luas (m) harus berupa angka',
                'lantai.required' => 'Jumlah Lantai tidak boleh kosong',
                'lantai.numeric'	=> 'Jumlah Lantai harus berupa angka',
                'kepemilikan_sarpras_id.required' => 'Kepemilikan tidak boleh kosong',
                'tahun_bangun.required' => 'Tahun Bangun tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'tanah_id' => 'required',
                'nama' => 'required',
                'imb' => 'required',
                'panjang' => 'required|numeric',
                'lebar' => 'required|numeric',
                'luas' => 'required|numeric',
                'kepemilikan_sarpras_id' => 'required',
                'lantai' => 'required|numeric',
                'tahun_bangun' => 'required',
            ],
            $messages
            )->validate();
            $insert_data = Bangunan::create([
                'tanah_id' => $request->tanah_id['tanah_id'],
                'nama' => $request->nama,
                'imb' => $request->imb,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'luas' => $request->luas,
                'lantai' => $request->lantai,
                'kepemilikan_sarpras_id' => $request->kepemilikan_sarpras_id['kepemilikan_sarpras_id'],
                'tahun_bangun' => date('Y', strtotime($request->tahun_bangun)),
                'keterangan' => $request->keterangan,
            ]);
            return response()->json(['status' => 'success', 'data' => $insert_data]);
        } elseif($request->route('query') == 'ruang'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'bangunan_id.required'	=> 'Bangunan tidak boleh kosong',
                'jenis_prasarana_id.required'	=> 'Jenis Ruang tidak boleh kosong',
                'kode.required'	=> 'Kode Ruang tidak boleh kosong',
                'nama.required'	=> 'Nama Ruang tidak boleh kosong',
                'tahun_bangun.required'	=> 'Tahun Bangun tidak boleh kosong',
                //'tahun_renovasi.required'	=> 'Tahun Terakhir direnovasi tidak boleh kosong',
                'lantai_ke.numeric' => 'Lantai Ke- harus berupa angka',
                'panjang.numeric' => 'Panjang (m) harus berupa angka',
                'lebar.numeric' => 'Lebar (m) harus berupa angka',
                'luas.numeric' => 'Luas (m) harus berupa angka',
                'luas_plester.numeric' => 'Luas Plester harus berupa angka',
                'luas_plafon.numeric' => 'Luas Plafon harus berupa angka',
                'luas_dinding.numeric' => 'Luas Dinding harus berupa angka',
                'luas_daun_jendela.numeric' => 'Luas Daun Jendela harus berupa angka',
                'luas_daun_pintu.numeric' => 'Luas Daun Pintu harus berupa angka',
                'luas_kusen.numeric' => 'Luas Kusen harus berupa angka',
                'luas_tutup_lantai.numeric' => 'Luas Tutup Lantai harus berupa angka',
                'jumlah_instalasi_listrik.numeric' => 'Jumlah Instalasi Listrik harus berupa angka',
                'panjang_instalasi_air.numeric' => 'Panjang Instalasi Air harus berupa angka',
                'jumlah_instalasi_air.numeric' => 'Jumlah Instalasi Air harus berupa angka',
                'panjang_drainase.numeric' => 'Panjang Drainase harus berupa angka',
                'luas_finish_struktur.numeric' => 'Luas Finish Struktur harus berupa angka',
                'luas_finish_plafon.numeric' => 'Luas Finish Plafon harus berupa angka',
                'luas_finish_dinding.numeric' => 'Luas Finish Dinding harus berupa angka',
                'luas_finish_kpj.numeric' => 'Luas Finish KPJ harus berupa angka',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'bangunan_id' => 'required',
                'jenis_prasarana_id' => 'required',
                'kode' => 'required',
                'nama' => 'required',
                'tahun_bangun' => 'required',
                //'tahun_renovasi' => 'required',
                'lantai_ke' => 'numeric',
                'panjang' => 'numeric',
                'lebar' => 'numeric',
                'luas' => 'numeric',
                'luas_plester' => 'numeric',
                'luas_plafon' => 'numeric',
                'luas_dinding' => 'numeric',
                'luas_daun_jendela' => 'numeric',
                'luas_daun_pintu' => 'numeric',
                'luas_kusen' => 'numeric',
                'luas_tutup_lantai' => 'numeric',
                'jumlah_instalasi_listrik' => 'numeric',
                'panjang_instalasi_air' => 'numeric',
                'jumlah_instalasi_air' => 'numeric',
                'panjang_drainase' => 'numeric',
                'luas_finish_struktur' => 'numeric',
                'luas_finish_plafon' => 'numeric',
                'luas_finish_dinding' => 'numeric',
                'luas_finish_kpj' => 'numeric',
            ],
            $messages
            )->validate();
            $insert_data = Ruang::create([
                'jenis_prasarana_id' => $request->jenis_prasarana_id['id'],
                'bangunan_id' => $request->bangunan_id['bangunan_id'],
                'kode' => $request->kode,
                'nama' => $request->nama,
                'tahun_bangun' => date('Y', strtotime($request->tahun_bangun)),
                'tahun_renovasi' => ($request->tahun_renovasi) ? date('Y', strtotime($request->tahun_renovasi)) : NULL,
                'lantai_ke' => $request->lantai_ke,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'luas' => $request->luas,
                'luas_plester' => $request->luas_plester,
                'luas_plafon' => $request->luas_plafon,
                'luas_dinding' => $request->luas_dinding,
                'luas_daun_jendela' => $request->luas_daun_jendela,
                'luas_daun_pintu' => $request->luas_daun_pintu,
                'luas_kusen' => $request->luas_kusen,
                'luas_tutup_lantai' => $request->luas_tutup_lantai,
                'jumlah_instalasi_listrik' => $request->jumlah_instalasi_listrik,
                'panjang_instalasi_air' => $request->panjang_instalasi_air,
                'jumlah_instalasi_air' => $request->jumlah_instalasi_air,
                'panjang_drainase' => $request->panjang_drainase,
                'luas_finish_struktur' => $request->luas_finish_struktur,
                'luas_finish_plafon' => $request->luas_finish_plafon,
                'luas_finish_dinding' => $request->luas_finish_dinding,
                'luas_finish_kpj' => $request->luas_finish_kpj,
                'keterangan' => $request->keterangan,
            ]);
            return response()->json(['status' => 'success', 'data' => $insert_data]);
        } elseif($request->route('query') == 'alat'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'tanah_id.required'	=> 'Tanah tidak boleh kosong',
                'bangunan_id.required'	=> 'Bangunan tidak boleh kosong',
                'ruang_id.required'	=> 'Ruang tidak boleh kosong',
                'jenis_sarana_id.required'	=> 'Jenis Sarana tidak boleh kosong',
                'nama.required'	=> 'Nama tidak boleh kosong',
                'kepemilikan_sarpras_id.required'	=> 'Kepemilikan tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'tanah_id' => 'required',
                'bangunan_id' => 'required',
                'ruang_id' => 'required',
                'jenis_sarana_id' => 'required',
                'nama' => 'required',
                'kepemilikan_sarpras_id' => 'required',
            ],
            $messages
            )->validate();
            $insert_data = Alat::create([
                'jenis_sarana_id' => $request->jenis_sarana_id['id'],
                'ruang_id' => $request->ruang_id['ruang_id'],
                'nama' => $request->nama,
                'spesifikasi' => $request->spesifikasi,
                'kepemilikan_sarpras_id' => $request->kepemilikan_sarpras_id['kepemilikan_sarpras_id'],
                'keterangan' => $request->keterangan,
            ]);
            return response()->json(['status' => 'success', 'data' => $insert_data]);
        } elseif($request->route('query') == 'angkutan'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'jenis_sarana_id.required'	=> 'Jenis Sarana tidak boleh kosong',
                'nama.required'	=> 'Nama tidak boleh kosong',
                'spesifikasi.required'	=> 'Spesifikasi tidak boleh kosong',
                'merk.required'	=> 'Merk tidak boleh kosong',
                'no_polisi.required'	=> 'Nomor Polisi tidak boleh kosong',
                'no_bpkb.required'	=> 'Nomor BPKB tidak boleh kosong',
                'kepemilikan_sarpras_id.required'	=> 'Kepemilikan tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'jenis_sarana_id' => 'required',
                'nama' => 'required',
                'spesifikasi' => 'required',
                'merk' => 'required',
                'no_polisi' => 'required',
                'no_bpkb' => 'required',
                'kepemilikan_sarpras_id' => 'required',
            ],
            $messages
            )->validate();
            $insert_data = Angkutan::create([
                'sekolah_id' => $request->sekolah_id['sekolah_id'],
                'jenis_sarana_id' => $request->jenis_sarana_id['id'],
                'nama' => $request->nama,
                'spesifikasi' => $request->spesifikasi,
                'merk' => $request->merk,
                'no_polisi' => $request->no_polisi,
                'no_bpkb' => $request->no_bpkb,
                'kepemilikan_sarpras_id' => $request->kepemilikan_sarpras_id['kepemilikan_sarpras_id'],
                'keterangan' => $request->keterangan,
            ]);
            return response()->json(['status' => 'success', 'data' => $insert_data]);
        } elseif($request->route('query') == 'sekolah'){
            $messages = [
                'npsn.required'	=> 'NPSN tidak boleh kosong',
                'nama.required'	=> 'Nama Sekolah tidak boleh kosong',
                'alamat.required'	=> 'Alamat tidak boleh kosong',
                'kecamatan_id.required'	=> 'Kecamatan tidak boleh kosong',
                'desa_id.required'	=> 'Desa/Kelurahab tidak boleh kosong',
                'status_sekolah.required'	=> 'Status Sekolah tidak boleh kosong',
                'nama_kepsek.required'	=> 'Nama Kepala Sekolah tidak boleh kosong',
                'no_telp.required'	=> 'Nomor HP Kepala Sekolah tidak boleh kosong',
                'nomor_ijop.required'	=> 'Nomor Ijin Operasional tidak boleh kosong',
                'tahun_ijop.required'	=> 'Tahun Ijin Operasional tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'npsn' => 'required',
                'nama' => 'required',
                'alamat' => 'required',
                'kecamatan_id' => 'required',
                'desa_id' => 'required',
                'status_sekolah' => 'required',
                'nama_kepsek' => 'required',
                'no_telp' => 'required',
                'nomor_ijop' => 'required',
                'tahun_ijop' => 'required',
            ],
            $messages
            )->validate();
            $wilayah = Wilayah::with('parrentRecursive')->find($request->desa_id['kode_wilayah']);
            $insert_data = Sekolah::create([
                'npsn' => $request->npsn,
                'nama' => $request->nama,
                'nss' => $request->nss,
                'alamat' => $request->alamat,
                'kode_pos' => $request->kode_pos,
                'email' => $request->email,
                'website' => $request->website,
                'status_sekolah' => $request->status_sekolah['key'],
                'nama_kepsek' => $request->nama_kepsek,
                'no_telp' => $request->no_telp,
                'kecamatan_id' => $request->kecamatan_id['kode_wilayah'],
                'kode_wilayah' => $request->desa_id['kode_wilayah'],
                'desa_kelurahan' => $wilayah->nama,
                'kecamatan' => $wilayah->parrentRecursive->nama,
                'kabupaten' => $wilayah->parrentRecursive->parrentRecursive->nama,
                'provinsi' => $wilayah->parrentRecursive->parrentRecursive->parrentRecursive->nama,
                'kabupaten_id' => $wilayah->parrentRecursive->parrentRecursive->kode_wilayah,
                'provinsi_id' => $wilayah->parrentRecursive->parrentRecursive->parrentRecursive->kode_wilayah,
                'nomor_ijop' => $request->nomor_ijop,
                'tahun_ijop' => date('Y', strtotime($request->tahun_ijop)),
            ]);
            Data_sekolah::updateOrCreate([
                'sekolah_id' => $insert_data->sekolah_id,
                'tahun_pendataan_id' => HelperModel::tahun_pendataan(),
                'jumlah_rombel' => $request->jumlah_rombel,
                'jumlah_pd' => $request->jumlah_pd,
            ]);
            return response()->json(['status' => 'success', 'data' => $insert_data]);
        } elseif($request->route('query') == 'file'){
            $messages = [
                'file.required'	=> 'File Upload tidak boleh kosong',
                'file.mimes'	=> 'File Upload harus berekstensi .XLSX',
            ];
            $validator = Validator::make(request()->all(), [
                'file' => 'required|image',
                //'file' => 'required',
            ],
            $messages
            )->validate();
            $file = $request->file('file');
            $image = Carbon::now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move('uploads', $image);
            Foto::create([
                'tahun_pendataan_id' => HelperModel::tahun_pendataan(),
                'ruang_id' => ($request->ruang_id) ? $request->ruang_id : NULL,
                'bangunan_id' => ($request->bangunan_id) ? $request->bangunan_id : NULL,
                'nama' => $image,
            ]);
        }
        return response()->json(['status' => 'failed', 'data' => NULL]);
    }
    public function update_data(Request $request)
    {
        if($request->route('query') == 'buku'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'kode.required'	=> 'Kode Buku tidak boleh kosong',
                'judul.required'	=> 'Judul tidak boleh kosong',
                'mata_pelajaran_id.required'	=> 'Mata Pelajaran tidak boleh kosong',
                'nama_penerbit.required'	=> 'Nama Penerbit tidak boleh kosong',
                'isbn_issn.required'	=> 'ISBN/ISSN tidak boleh kosong',
                'kelas.required'	=> 'Kelas tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'kode' => 'required',
                'judul' => 'required',
                'mata_pelajaran_id' => 'required',
                'nama_penerbit' => 'required',
                'isbn_issn' => 'required',
                'kelas' => 'required',
            ],
            $messages
            )->validate();
            $update_data = Buku::find($request->id);
            $update_data->sekolah_id = $request->sekolah_id['sekolah_id'];
            $update_data->kode = $request->kode;
            $update_data->judul = $request->judul;
            $update_data->mata_pelajaran_id = $request->mata_pelajaran_id['mata_pelajaran_id'];
            $update_data->nama_penerbit = (is_array($request->nama_penerbit)) ? $request->nama_penerbit['nama'] : $request->nama_penerbit;
            $update_data->penerbit_id = (is_array($request->nama_penerbit)) ? $request->nama_penerbit['penerbit_id'] : NULL;
            $update_data->isbn_issn = $request->isbn_issn;
            $update_data->kelas = $request->kelas;
            $update_data->keterangan = $request->keterangan;
            if($update_data->save()){
                return response()->json(['status' => 'success', 'message' => 'Data Buku berhasil diperbaharui']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Buku gagal diperbaharui']);
            }
        } elseif($request->route('query') == 'angkutan'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'jenis_sarana_id.required'	=> 'Jenis Sarana tidak boleh kosong',
                'nama.required'	=> 'Nama tidak boleh kosong',
                'spesifikasi.required'	=> 'Spesifikasi tidak boleh kosong',
                'merk.required'	=> 'Merk tidak boleh kosong',
                'no_polisi.required'	=> 'Nomor Polisi tidak boleh kosong',
                'no_bpkb.required'	=> 'Nomor BPKB tidak boleh kosong',
                'kepemilikan_sarpras_id.required'	=> 'Kepemilikan tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'jenis_sarana_id' => 'required',
                'nama' => 'required',
                'spesifikasi' => 'required',
                'merk' => 'required',
                'no_polisi' => 'required',
                'no_bpkb' => 'required',
                'kepemilikan_sarpras_id' => 'required',
            ],
            $messages
            )->validate();
            $update_data = Angkutan::find($request->id);
            $update_data->sekolah_id = $request->sekolah_id['sekolah_id'];
            $update_data->jenis_sarana_id = $request->jenis_sarana_id['id'];
            $update_data->nama = $request->nama;
            $update_data->spesifikasi = $request->spesifikasi;
            $update_data->merk = $request->merk;
            $update_data->no_polisi = $request->no_polisi;
            $update_data->no_bpkb = $request->no_bpkb;
            $update_data->kepemilikan_sarpras_id = $request->kepemilikan_sarpras_id['kepemilikan_sarpras_id'];
            $update_data->keterangan = $request->keterangan;
            if($update_data->save()){
                return response()->json(['status' => 'success', 'message' => 'Data Angkutan berhasil diperbaharui']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Angkutan gagal diperbaharui']);
            }
        } elseif($request->route('query') == 'alat'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'tanah_id.required'	=> 'Tanah tidak boleh kosong',
                'bangunan_id.required'	=> 'Bangunan tidak boleh kosong',
                'ruang_id.required'	=> 'Ruang tidak boleh kosong',
                'jenis_sarana_id.required'	=> 'Jenis Sarana tidak boleh kosong',
                'nama.required'	=> 'Nama tidak boleh kosong',
                'kepemilikan_sarpras_id.required'	=> 'Kepemilikan tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'tanah_id' => 'required',
                'bangunan_id' => 'required',
                'ruang_id' => 'required',
                'jenis_sarana_id' => 'required',
                'nama' => 'required',
                'kepemilikan_sarpras_id' => 'required',
            ],
            $messages
            )->validate();
            $update_data = Alat::find($request->id);
            $update_data->jenis_sarana_id = $request->jenis_sarana_id['id'];
            $update_data->ruang_id = $request->ruang_id['ruang_id'];
            $update_data->nama = $request->nama;
            $update_data->spesifikasi = $request->spesifikasi;
            $update_data->kepemilikan_sarpras_id = $request->kepemilikan_sarpras_id['kepemilikan_sarpras_id'];
            $update_data->keterangan = $request->keterangan;
            if($update_data->save()){
                return response()->json(['status' => 'success', 'message' => 'Data Angkutan berhasil diperbaharui']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Angkutan gagal diperbaharui']);
            }
        } elseif($request->route('query') == 'ruang'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'bangunan_id.required'	=> 'Bangunan tidak boleh kosong',
                'jenis_prasarana_id.required'	=> 'Jenis Ruang tidak boleh kosong',
                'kode.required'	=> 'Kode Ruang tidak boleh kosong',
                'nama.required'	=> 'Nama Ruang tidak boleh kosong',
                'tahun_bangun.required'	=> 'Tahun Bangun tidak boleh kosong',
                //'tahun_renovasi.required'	=> 'Tahun Terakhir direnovasi tidak boleh kosong',
                'lantai_ke.numeric' => 'Lantai Ke- harus berupa angka',
                'panjang.numeric' => 'Panjang harus berupa angka',
                'lebar.numeric' => 'Lebar harus berupa angka',
                'luas.numeric' => 'Luas harus berupa angka',
                'luas_plester.numeric' => 'Luas Plester harus berupa angka',
                'luas_plafon.numeric' => 'Luas Plafon harus berupa angka',
                'luas_dinding.numeric' => 'Luas Dinding harus berupa angka',
                'luas_daun_jendela.numeric' => 'Luas Daun Jendela harus berupa angka',
                'luas_daun_pintu.numeric' => 'Luas Daun Pintu harus berupa angka',
                'luas_kusen.numeric' => 'Luas Kusen harus berupa angka',
                'luas_tutup_lantai.numeric' => 'Luas Tutup Lantai harus berupa angka',
                'jumlah_instalasi_listrik.numeric' => 'Jumlah Instalasi Listrik harus berupa angka',
                'panjang_instalasi_air.numeric' => 'Panjang Instalasi Air harus berupa angka',
                'jumlah_instalasi_air.numeric' => 'Jumlah Instalasi Air harus berupa angka',
                'panjang_drainase.numeric' => 'Panjang Drainase harus berupa angka',
                'luas_finish_struktur.numeric' => 'Luas Finish Struktur harus berupa angka',
                'luas_finish_plafon.numeric' => 'Luas Finish Plafon harus berupa angka',
                'luas_finish_dinding.numeric' => 'Luas Finish Dinding harus berupa angka',
                'luas_finish_kpj.numeric' => 'Luas Finish KPJ harus berupa angka',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'bangunan_id' => 'required',
                'jenis_prasarana_id' => 'required',
                'kode' => 'required',
                'nama' => 'required',
                'tahun_bangun' => 'required',
                //'tahun_renovasi' => 'required',
                'lantai_ke' => 'numeric',
                'panjang' => 'numeric',
                'lebar' => 'numeric',
                'luas' => 'numeric',
                'luas_plester' => 'numeric',
                'luas_plafon' => 'numeric',
                'luas_dinding' => 'numeric',
                'luas_daun_jendela' => 'numeric',
                'luas_daun_pintu' => 'numeric',
                'luas_kusen' => 'numeric',
                'luas_tutup_lantai' => 'numeric',
                'jumlah_instalasi_listrik' => 'numeric',
                'panjang_instalasi_air' => 'numeric',
                'jumlah_instalasi_air' => 'numeric',
                'panjang_drainase' => 'numeric',
                'luas_finish_struktur' => 'numeric',
                'luas_finish_plafon' => 'numeric',
                'luas_finish_dinding' => 'numeric',
                'luas_finish_kpj' => 'numeric',
            ],
            $messages
            )->validate();
            $update_data = Ruang::find($request->id);
            $update_data->jenis_prasarana_id = $request->jenis_prasarana_id['id'];
            $update_data->bangunan_id = $request->bangunan_id['bangunan_id'];
            $update_data->kode = $request->kode;
            $update_data->nama = $request->nama;
            $update_data->tahun_bangun = date('Y', strtotime($request->tahun_bangun));
            $update_data->tahun_renovasi = ($request->tahun_renovasi) ? date('Y', strtotime($request->tahun_renovasi)) : NULL;
            $update_data->lantai_ke = $request->lantai_ke;
            $update_data->panjang = $request->panjang;
            $update_data->lebar = $request->lebar;
            $update_data->luas = $request->luas;
            $update_data->luas_plester = $request->luas_plester;
            $update_data->luas_plafon = $request->luas_plafon;
            $update_data->luas_dinding = $request->luas_dinding;
            $update_data->luas_daun_jendela = $request->luas_daun_jendela;
            $update_data->luas_daun_pintu = $request->luas_daun_pintu;
            $update_data->luas_kusen = $request->luas_kusen;
            $update_data->luas_tutup_lantai = $request->luas_tutup_lantai;
            $update_data->jumlah_instalasi_listrik = $request->jumlah_instalasi_listrik;
            $update_data->panjang_instalasi_air = $request->panjang_instalasi_air;
            $update_data->jumlah_instalasi_air = $request->jumlah_instalasi_air;
            $update_data->panjang_drainase = $request->panjang_drainase;
            $update_data->luas_finish_struktur = $request->luas_finish_struktur;
            $update_data->luas_finish_plafon = $request->luas_finish_plafon;
            $update_data->luas_finish_dinding = $request->luas_finish_dinding;
            $update_data->luas_finish_kpj = $request->luas_finish_kpj;
            $update_data->keterangan = $request->keterangan;
            if($update_data->save()){
                return response()->json(['status' => 'success', 'message' => 'Data Ruang berhasil diperbaharui']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Ruang gagal diperbaharui']);
            }
        } elseif($request->route('query') == 'bangunan'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'tanah_id.required'	=> 'Tanah tidak boleh kosong',
                'nama.required'	=> 'Nama tidak boleh kosong',
                'imb.required'	=> 'Nomor IMB tidak boleh kosong',
                'panjang.required'	=> 'Panjang (m) tidak boleh kosong',
                'panjang.numeric'	=> 'Panjang (m) harus berupa angka',
                'lebar.required'	=> 'Lebar (m) tidak boleh kosong',
                'lebar.numeric'	=> 'Lebar (m) harus berupa angka',
                'luas.required' => 'Luas (m<sup>2</sup>) tidak boleh kosong',
                'luas.numeric'	=> 'Luas (m) harus berupa angka',
                'lantai.required' => 'Jumlah Lantai tidak boleh kosong',
                'lantai.numeric'	=> 'Jumlah Lantai harus berupa angka',
                'kepemilikan_sarpras_id.required' => 'Kepemilikan tidak boleh kosong',
                'tahun_bangun.required' => 'Tahun Bangun tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'tanah_id' => 'required',
                'nama' => 'required',
                'imb' => 'required',
                'panjang' => 'required|numeric',
                'lebar' => 'required|numeric',
                'luas' => 'required|numeric',
                'kepemilikan_sarpras_id' => 'required',
                'lantai' => 'required|numeric',
                'tahun_bangun' => 'required',
            ],
            $messages
            )->validate();
            $update_data = Bangunan::find($request->id);
            $update_data->tanah_id = $request->tanah_id['tanah_id'];
            $update_data->nama = $request->nama;
            $update_data->imb = $request->imb;
            $update_data->panjang = $request->panjang;
            $update_data->lebar = $request->lebar;
            $update_data->luas = $request->luas;
            $update_data->lantai = $request->lantai;
            $update_data->kepemilikan_sarpras_id = $request->kepemilikan_sarpras_id['kepemilikan_sarpras_id'];
            $update_data->tahun_bangun = date('Y', strtotime($request->tahun_bangun));
            $update_data->keterangan = $request->keterangan;
            if($update_data->save()){
                return response()->json(['status' => 'success', 'message' => 'Data Bangunan berhasil diperbaharui']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Bangunan gagal diperbaharui']);
            }
        } elseif($request->route('query') == 'tanah'){
            $messages = [
                'sekolah_id.required'	=> 'Sekolah tidak boleh kosong',
                'nama.required'	=> 'Nama tidak boleh kosong',
                'no_sertifikat_tanah.required'	=> 'Nomor Sertifikat Tanah tidak boleh kosong',
                'panjang.required'	=> 'Panjang (m) tidak boleh kosong',
                'panjang.numeric'	=> 'Panjang (m) harus berupa angka',
                'lebar.required'	=> 'Lebar (m) tidak boleh kosong',
                'lebar.numeric'	=> 'Lebar (m) harus berupa angka',
                'luas.required' => 'Luas (m<sup>2</sup>) tidak boleh kosong',
                'luas.numeric'	=> 'Luas (m) harus berupa angka',
                'kepemilikan_sarpras_id.required' => 'Kepemilikan tidak boleh kosong',
                'luas_lahan_tersedia.required' => 'Luas Lahan Tersedia (m<sup>2</sup>) tidak boleh kosong',
                'luas_lahan_tersedia.numeric'	=> 'Luas Lahan Tersedia (m<sup>2</sup>) harus berupa angka',
            ];
            $validator = Validator::make(request()->all(), [
                'sekolah_id' => 'required',
                'nama' => 'required',
                'no_sertifikat_tanah' => 'required',
                'panjang' => 'required|numeric',
                'lebar' => 'required|numeric',
                'luas' => 'required|numeric',
                'kepemilikan_sarpras_id' => 'required',
                'luas_lahan_tersedia' => 'required|numeric',
            ],
            $messages
            )->validate();
            $update_data = Tanah::find($request->id);
            $update_data->sekolah_id = $request->sekolah_id['sekolah_id'];
            $update_data->nama = $request->nama;
            $update_data->no_sertifikat_tanah = $request->no_sertifikat_tanah;
            $update_data->panjang = $request->panjang;
            $update_data->lebar = $request->lebar;
            $update_data->luas = $request->luas;
            $update_data->luas_lahan_tersedia = $request->luas_lahan_tersedia;
            $update_data->kepemilikan_sarpras_id = $request->kepemilikan_sarpras_id['kepemilikan_sarpras_id'];
            $update_data->keterangan = $request->keterangan;
            if($update_data->save()){
                return response()->json(['status' => 'success', 'message' => 'Data Tanah berhasil diperbaharui']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Tanah gagal diperbaharui']);
            }
        } elseif($request->route('query') == 'sekolah'){
            $messages = [
                'npsn.required'	=> 'NPSN tidak boleh kosong',
                'nama.required'	=> 'Nama Sekolah tidak boleh kosong',
                'alamat.required'	=> 'Alamat tidak boleh kosong',
                'kecamatan_id.required'	=> 'Kecamatan tidak boleh kosong',
                'desa_id.required'	=> 'Desa/Kelurahab tidak boleh kosong',
                'status_sekolah.required'	=> 'Status Sekolah tidak boleh kosong',
                'nama_kepsek.required'	=> 'Nama Kepala Sekolah tidak boleh kosong',
                'no_telp.required'	=> 'Nomor HP Kepala Sekolah tidak boleh kosong',
                'nomor_ijop.required'	=> 'Nomor Ijin Operasional tidak boleh kosong',
                'tahun_ijop.required'	=> 'Tahun Ijin Operasional tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'npsn' => 'required',
                'nama' => 'required',
                'alamat' => 'required',
                'kecamatan_id' => 'required',
                'desa_id' => 'required',
                'status_sekolah' => 'required',
                'nama_kepsek' => 'required',
                'no_telp' => 'required',
                'nomor_ijop' => 'required',
                'tahun_ijop' => 'required',
            ],
            $messages
            )->validate();
            $wilayah = Wilayah::with('parrentRecursive')->find($request->desa_id['kode_wilayah']);
            $update_data = Sekolah::find($request->id);
            $update_data->npsn = $request->npsn;
            $update_data->nama = $request->nama;
            $update_data->nss = $request->nss;
            $update_data->alamat = $request->alamat;
            $update_data->kode_pos = $request->kode_pos;
            $update_data->email = $request->email;
            $update_data->website = $request->website;
            $update_data->status_sekolah = $request->status_sekolah['key'];
            $update_data->nama_kepsek = $request->nama_kepsek;
            $update_data->no_telp = $request->no_telp;
            $update_data->kecamatan_id = $request->kecamatan_id['kode_wilayah'];
            $update_data->kode_wilayah = $request->desa_id['kode_wilayah'];
            $update_data->desa_kelurahan = $wilayah->nama;
            $update_data->kecamatan = $wilayah->parrentRecursive->nama;
            $update_data->kabupaten = $wilayah->parrentRecursive->parrentRecursive->nama;
            $update_data->provinsi = $wilayah->parrentRecursive->parrentRecursive->parrentRecursive->nama;
            $update_data->kabupaten_id = $wilayah->parrentRecursive->parrentRecursive->kode_wilayah;
            $update_data->provinsi_id = $wilayah->parrentRecursive->parrentRecursive->parrentRecursive->kode_wilayah;
            $update_data->nomor_ijop = $request->nomor_ijop;
            $update_data->tahun_ijop = date('Y', strtotime($request->tahun_ijop));
            if($update_data->save()){
                Data_sekolah::updateOrCreate([
                    'sekolah_id' => $request->id,
                    'tahun_pendataan_id' => HelperModel::tahun_pendataan(),
                    'jumlah_rombel' => $request->jumlah_rombel,
                    'jumlah_pd' => $request->jumlah_pd,
                ]);
                return response()->json(['status' => 'success', 'message' => 'Data Sekolah berhasil diperbaharui']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Sekolah gagal diperbaharui']);
            }
        }
        return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Tidak ada data diperbaharui']);
    }
    public function delete_data(Request $request)
    {
        $id = $request->route('id');
        if($request->route('query') == 'buku'){
            $delete_data = Buku::find($id);
            if($delete_data->delete()){
                return response()->json(['title' => 'Berhasil', 'status' => 'success', 'message' => 'Data Buku berhasil dihapus']);
            } else {
                return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Data Buku gagal dihapus']);
            }
        } elseif($request->route('query') == 'angkutan'){
            $delete_data = Angkutan::find($id);
            if($delete_data->delete()){
                return response()->json(['title' => 'Berhasil', 'status' => 'success', 'message' => 'Data Angkutan berhasil dihapus']);
            } else {
                return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Data Buku Angkutan dihapus']);
            }
        } elseif($request->route('query') == 'alat'){
            $delete_data = Alat::find($id);
            if($delete_data->delete()){
                return response()->json(['title' => 'Berhasil', 'status' => 'success', 'message' => 'Data Alat berhasil dihapus']);
            } else {
                return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Data Buku Alat dihapus']);
            }
        } elseif($request->route('query') == 'ruang'){
            $delete_data = Ruang::find($id);
            if($delete_data->delete()){
                return response()->json(['title' => 'Berhasil', 'status' => 'success', 'message' => 'Data Ruang berhasil dihapus']);
            } else {
                return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Data Buku Ruang dihapus']);
            }
        } elseif($request->route('query') == 'bangunan'){
            $delete_data = Bangunan::find($id);
            if($delete_data->delete()){
                return response()->json(['title' => 'Berhasil', 'status' => 'success', 'message' => 'Data Bangunan berhasil dihapus']);
            } else {
                return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Data Buku Bangunan dihapus']);
            }
        } elseif($request->route('query') == 'tanah'){
            $delete_data = Tanah::find($id);
            if($delete_data->delete()){
                return response()->json(['title' => 'Berhasil', 'status' => 'success', 'message' => 'Data Tanah berhasil dihapus']);
            } else {
                return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Data Buku Tanah dihapus']);
            }
        } elseif($request->route('query') == 'foto'){
            $delete_data = Foto::find($id);
            File::delete('uploads/'.$delete_data->nama);
            if($delete_data->delete()){
                return response()->json(['title' => 'Berhasil', 'status' => 'success', 'message' => 'Foto berhasil dihapus']);
            } else {
                return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Foto Tanah dihapus']);
            }
        }
        return response()->json(['title' => 'Gagal', 'status' => 'error', 'message' => 'Tidak ada data terhapus']);
    }
}
