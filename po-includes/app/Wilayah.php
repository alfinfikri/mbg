<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\Traits\LogsActivity;

class Wilayah extends Model
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
    protected $table = 'wilayahs';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

	  protected static $logAttributes = ['*'];

    public function parent()
    {
        return $this->belongsTo(Wilayah::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Wilayah::class, 'parent_id');
    }
    
    public function sekolahs()
    {
        return $this->hasMany('App\Sekolah', 'wilayah_id');
    }

    public function sppgs()
    {
        return $this->hasMany('App\Sppg', 'wilayah_id');
    }
}
