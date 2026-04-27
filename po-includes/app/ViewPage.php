<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Shetabit\Visitor\Traits\Visitable;

class ViewPage extends Model
{
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
    protected $table = 'shetabit_visits';

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
		'id', 'method', 'request', 'url', 'referer', 'languages', 'useragent', 'headers', 'device', 'platform', 'browser', 'ip', 'created_at', 'updated_at'
	];

	protected static $logAttributes = ['*'];
}
