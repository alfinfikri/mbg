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
        'nama', 'wilayah_id', 'alamat', 'created_by', 'updated_by'
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
        
    public function menuharians()
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
	
	protected static $logAttributes = ['*'];
}
