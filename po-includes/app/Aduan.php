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
            'sekolah_id',
            'nama',
            'no_hp',
            'email',
            'kategori',
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
            'disposisi_user_id',
            'disposisi_sppg_id',
            'disposisi_satgas_id',
            'disposisi_satgas_ids',
            'catatan_disposisi',
            'disposisi_at',
            'respon_proses',
            'respon_satgas',
            'respon_selesai',
            'tanggapan',
            'tanggapan_sppg',
            'foto_tindak_lanjut',
            'ditindaklanjuti_at',
            'closed_by',
            'closed_at',
            'status',
            'status_pengaduan',
            'created_by',
            'updated_by'
        ];

    protected $casts = [
        'disposisi_satgas_ids' => 'array',
        'respon_satgas' => 'array',
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

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function disposisiUser()
    {
        return $this->belongsTo(User::class, 'disposisi_user_id');
    }

    public function disposisiSppg()
    {
        return $this->belongsTo(Sppg::class, 'disposisi_sppg_id');
    }

    public function disposisiSatgas()
    {
        return $this->belongsTo(User::class, 'disposisi_satgas_id');
    }

    public function disposisiSatgasUsers()
    {
        $ids = $this->disposisi_satgas_ids ?: ($this->disposisi_satgas_id ? [$this->disposisi_satgas_id] : []);

        return User::whereIn('id', $ids)->orderBy('name')->get();
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
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
