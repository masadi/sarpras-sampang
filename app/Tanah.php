<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Tanah extends Model
{
    use Uuid;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'tanah';
	protected $primaryKey = 'tanah_id';
    protected $guarded = [];
    public function sekolah(){
        return $this->belongsTo('App\Sekolah', 'sekolah_id', 'sekolah_id');
    }
    public function kepemilikan()
    {
        return $this->belongsTo(Status_kepemilikan_sarpras::class, 'kepemilikan_sarpras_id', 'kepemilikan_sarpras_id');
    }
    public function bangunan()
    {
        return $this->hasMany('App\Bangunan', 'tanah_id', 'tanah_id');
    }
}
