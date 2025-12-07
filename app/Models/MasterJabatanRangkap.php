<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterJabatanRangkap extends Model
{
    protected $table = 'master_jabatan_rangkaps';

    protected $fillable = ['label', 'kode_umap'];

    public function menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'jabatan_rangkap_id');
    }
}
