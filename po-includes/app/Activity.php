<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity_log';

    protected $fillable = [
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties'
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function aduan()
    {
        return $this->belongsTo(Aduan::class, 'subject_id');
    }

    public function getKeteranganAttribute()
    {
        return $this->properties['keterangan'] ?? null;
    }

    public function getTanggalAttribute()
    {
        return $this->created_at->format('d M Y, H:i');
    }
}