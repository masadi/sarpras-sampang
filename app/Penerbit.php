<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Penerbit extends Model
{
    use Uuid;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'penerbit';
	protected $primaryKey = 'penerbit_id';
    protected $guarded = [];
    public function buku(){
        return $this->hasMany('App\Buku', 'penerbit_id', 'penerbit_id');
    }
}
