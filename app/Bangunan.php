<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use App\HelperModel;
class Bangunan extends Model
{
    use Uuid;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'bangunan';
	protected $primaryKey = 'bangunan_id';
    protected $guarded = [];
    public function tanah(){
        return $this->belongsTo('App\Tanah', 'tanah_id', 'tanah_id');
    }
    public function kepemilikan()
    {
        return $this->belongsTo(Status_kepemilikan_sarpras::class, 'kepemilikan_sarpras_id', 'kepemilikan_sarpras_id');
    }
    public function foto(){
        return $this->hasMany('App\Foto', 'bangunan_id', 'bangunan_id');
    }
    public function kondisi_bangunan(){
        return $this->hasOne('App\Kondisi_bangunan', 'bangunan_id', 'bangunan_id')->where('tahun_pendataan_id', HelperModel::tahun_pendataan());
    }
    public function all_kondisi(){
        return $this->hasOne('App\Kondisi_bangunan', 'bangunan_id', 'bangunan_id');
    }
    public function ruang()
    {
        return $this->hasMany('App\Ruang', 'bangunan_id', 'bangunan_id');
    }
}
