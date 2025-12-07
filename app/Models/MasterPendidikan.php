<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterPendidikan extends Model
{
    protected $table = 'master_pendidikans';

    protected $fillable = ['label_jurusan', 'kode_umap', 'jenjang_default'];

    public function s1Menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'pendidikan_s1_id');
    }

    public function s2s3Menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'pendidikan_s2s3_id');
    }
}
