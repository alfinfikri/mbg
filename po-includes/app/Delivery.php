<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\Traits\LogsActivity;

class Delivery extends Model
{
	//use LogsActivity;
	
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    // public $timestamps = false;

    protected $table = 'deliverys';

    protected $fillable = [
        'sppg_id', 'menu_id', 'tanggal', 'foto', 'created_by', 'updated_by'
    ];

    public function sppg()
    {
        return $this->belongsTo(\App\Sppg::class, 'sppg_id');
    }

    public function menu()
    {
        return $this->belongsTo(\App\MenuHarian::class, 'menu_id');
    }

    public function sekolahs()
    {
        return $this->belongsToMany(
            \App\Sekolah::class,
            'delivery_sekolahs',
            'delivery_id',
            'sekolah_id'
        )->withPivot('jumlah_porsi');
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
