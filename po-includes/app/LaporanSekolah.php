<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaporanSekolah extends Model
{
	protected $table = 'laporan_sekolahs';

	protected $fillable = [
		'tanggal',
		'sppg_id',
		'sekolah_id',
		'menu_harian_id',
		'distribusi_id',
		'foto_menu',
		'foto_siswa',
		'waktu_upload',
		'latitude',
		'longitude',
		'lokasi',
		'status_laporan',
		'rating',
		'catatan_verifikasi',
		'verified_by',
		'verified_at',
		'created_by',
		'updated_by'
	];

	protected $casts = [
		'tanggal' => 'date',
		'waktu_upload' => 'datetime',
		'verified_at' => 'datetime',
		'latitude' => 'decimal:7',
		'longitude' => 'decimal:7',
		'rating' => 'integer',
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

	public function distribusi()
	{
		return $this->belongsTo('App\Distribusi', 'distribusi_id');
	}

	public function verifiedBy()
	{
		return $this->belongsTo('App\User', 'verified_by');
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
