<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\Traits\LogsActivity;

class MenuHarian extends Model
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
    protected $table = 'menu_harians';

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
        'tanggal',
        'nama',
        'sppg_id',
        'deskripsi',
        'foto',
        'kecil_energi',
        'kecil_lemak',
        'kecil_protein',
        'kecil_karbohidrat',
        'kecil_serat',
        'besar_energi',
        'besar_lemak',
        'besar_protein',
        'besar_karbohidrat',
        'besar_serat',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function sppg()
    {
        return $this->belongsTo(\App\Sppg::class, 'sppg_id');
    }

    public function distribusis()
    {
        return $this->hasMany(\App\Distribusi::class, 'menu_harian_id');
    }

    public function laporanSekolahs()
    {
        return $this->hasMany(\App\LaporanSekolah::class, 'menu_harian_id');
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
