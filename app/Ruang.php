<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
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
        return $this->hasOne('App\Kondisi_ruang', 'ruang_id', 'ruang_id');
    }
    /**
     * Get all of the comments for the Ruang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function foto(){
        return $this->hasMany('App\Foto', 'ruang_id', 'ruang_id');
    }
}
