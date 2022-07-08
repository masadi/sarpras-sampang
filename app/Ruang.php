<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use App\HelperModel;
class Ruang extends Model
{
    use Uuid;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ruang';
	protected $primaryKey = 'ruang_id';
    protected $guarded = [];
    public function bangunan(){
        return $this->belongsTo('App\Bangunan', 'bangunan_id', 'bangunan_id');
    }
    public function jenis_prasarana()
    {
        return $this->belongsTo(Jenis_prasarana::class, 'jenis_prasarana_id', 'id');
    }
    public function kondisi_ruang(){
        return $this->hasOne('App\Kondisi_ruang', 'ruang_id', 'ruang_id')->where('tahun_pendataan_id', HelperModel::tahun_pendataan());
    }
    public function all_kondisi_ruang(){
        return $this->hasOne('App\Kondisi_ruang', 'ruang_id', 'ruang_id');
    }
    public function baik(){
        return $this->hasOne('App\Kondisi_ruang', 'ruang_id', 'ruang_id')->where('nilai_saat_ini', 0);
    }
    public function ringan(){
        return $this->hasOne('App\Kondisi_ruang', 'ruang_id', 'ruang_id')->where('nilai_saat_ini', '>', 0)->where('nilai_saat_ini', '<=', 30);
    }
    public function sedang(){
        return $this->hasOne('App\Kondisi_ruang', 'ruang_id', 'ruang_id')->where('nilai_saat_ini', '>', 30)->where('nilai_saat_ini', '<=', 45);
    }
    public function berat(){
        return $this->hasOne('App\Kondisi_ruang', 'ruang_id', 'ruang_id')->where('nilai_saat_ini', '>', 45);//->where('nilai_saat_ini', '<=', 65);
    }
    public function foto(){
        return $this->hasMany('App\Foto', 'ruang_id', 'ruang_id');
    }
}
