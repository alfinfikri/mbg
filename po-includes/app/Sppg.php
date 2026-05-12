<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\Traits\LogsActivity;

class Sppg extends Model
{
	//use LogsActivity;
	
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    // public $timestamps = false;

    protected $table = 'sppgs';

    protected $fillable = [
        'nama',
        'wilayah_id',
        'alamat',
        'latitude',
        'longitude',
        'nama_penanggung_jawab',
        'no_hp',
        'email',
        'slhs_nomor',
        'slhs_tanggal',
        'slhs_tanggal_terbit',
        'slhs_berlaku_hingga',
        'slhs_file',
        'halal_nomor',
        'halal_tanggal_terbit',
        'halal_file',
        'foto_dapur',
        'nama_ahli_gizi',
        'keterangan_data_profil',
        'fasilitas_dapur',
        'kapasitas_produksi',
        'jumlah_petugas',
        'status_layanan',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    public function sekolahs()
    {
        return $this->belongsToMany(
            \App\Sekolah::class,
            'sppg_sekolahs',
            'sppg_id',
            'sekolah_id'
        )->withTimestamps();
    }
        
    public function menuHarians()
    {
        return $this->hasMany(\App\MenuHarian::class, 'sppg_id');
    }

    public function wilayah()
	{
		return $this->belongsTo('App\Wilayah', 'wilayah_id');
	}
	
	public function createdBy()
	{
		return $this->belongsTo('App\User', 'created_by');
	}
	
	public function updatedBy()
	{
		return $this->belongsTo('App\User', 'updated_by');
	}

    public function users()
    {
        return $this->hasMany(\App\User::class, 'sppg_id');
    }

    public function distribusis()
    {
        return $this->hasMany(\App\Distribusi::class, 'sppg_id');
    }

    public function laporanSekolahs()
    {
        return $this->hasMany(\App\LaporanSekolah::class, 'sppg_id');
    }
	
	protected static $logAttributes = ['*'];
}
