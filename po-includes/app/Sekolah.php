<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\Traits\LogsActivity;

class Sekolah extends Model
{
	//use LogsActivity;
	
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    // public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sekolahs';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'nama', 'jenis_id', 'status_layanan', 'wilayah_id', 'alamat', 'latitude', 'longitude', 'jumlah_total', 'created_by', 'updated_by'
	];

    public function sppgs()
    {
        return $this->belongsToMany(
            \App\Sppg::class,
            'sppg_sekolahs',
            'sekolah_id',
            'sppg_id'
        );
    }
    
    public function wilayah()
	{
		return $this->belongsTo('App\Wilayah', 'wilayah_id');
	}

	public function penerimas()
	{
		return $this->hasMany('App\Penerima', 'sekolah_id');
	}

	public function users()
	{
		return $this->hasMany('App\User', 'sekolah_id');
	}

	public function distribusis()
	{
		return $this->hasMany('App\Distribusi', 'sekolah_id');
	}

	public function laporanSekolahs()
	{
		return $this->hasMany('App\LaporanSekolah', 'sekolah_id');
	}
	
	public function createdBy()
	{
		return $this->belongsTo('App\User', 'created_by');
	}
	
	public function updatedBy()
	{
		return $this->belongsTo('App\User', 'updated_by');
	}
	
	protected static $logAttributes = ['*'];
}
