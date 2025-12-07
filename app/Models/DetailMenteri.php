<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailMenteri extends Model
{
    protected $table = 'detail_menteris';

    protected $fillable = [
        'menteri_id',
        'tempat_lahir',
        'tanggal_lahir',
        'umur_tahun',
        'almamater_sma',
        'almamater_s1',
        'almamater_s2',
        'almamater_s3',
        'kekayaan_rp',
        'status_hukum',
        'catatan',
    ];

    public function menteri(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Menteri::class, 'menteri_id');
    }
}
