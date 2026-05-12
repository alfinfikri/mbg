<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penerima extends Model
{
	protected $table = 'penerimas';

	protected $fillable = [
		'sekolah_id', 'wilayah_id', 'kategori', 'jumlah', 'status_mbg'
	];

	public function sekolah()
	{
		return $this->belongsTo('App\Sekolah', 'sekolah_id');
	}

	public function wilayah()
	{
		return $this->belongsTo('App\Wilayah', 'wilayah_id');
	}
}
