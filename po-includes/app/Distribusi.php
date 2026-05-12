<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
	protected $table = 'distribusis';

	protected $fillable = [
		'tanggal',
		'sppg_id',
		'sekolah_id',
		'menu_harian_id',
		'jumlah_porsi',
		'status_distribusi',
		'keterangan',
		'created_by',
		'updated_by',
	];

	protected $casts = [
		'tanggal' => 'date',
	];

	public function sppg()
	{
		return $this->belongsTo('App\Sppg', 'sppg_id');
	}

	public function sekolah()
	{
		return $this->belongsTo('App\Sekolah', 'sekolah_id');
	}

	public function menuHarian()
	{
		return $this->belongsTo('App\MenuHarian', 'menu_harian_id');
	}

	public function laporanSekolahs()
	{
		return $this->hasMany('App\LaporanSekolah', 'distribusi_id');
	}

	public function laporanSekolah()
	{
		return $this->hasOne('App\LaporanSekolah', 'distribusi_id');
	}

	public function createdBy()
	{
		return $this->belongsTo('App\User', 'created_by');
	}

	public function updatedBy()
	{
		return $this->belongsTo('App\User', 'updated_by');
	}
}
