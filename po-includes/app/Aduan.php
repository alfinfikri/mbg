<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\Traits\LogsActivity;

class Aduan extends Model
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
    protected $table = 'aduans';

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
            'wilayah_id',
            'sppg_id',
            'nama',
            'no_hp',
            'alamat',
            'judul_aduan',
            'isi_aduan',
            'foto',
            'kode_tiket',
            'tgl_aduan',
            'tgl_disposisi',
            'tgl_proses',
            'tgl_selesai',
            'user_id',
            'respon_proses',
            'respon_selesai',
            'status',
            'created_by',
            'updated_by'
        ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function sppg()
    {
        return $this->belongsTo(Sppg::class, 'sppg_id');
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
