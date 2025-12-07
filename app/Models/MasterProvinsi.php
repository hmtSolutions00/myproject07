<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterProvinsi extends Model
{
    protected $table = 'master_provinsis';

    protected $fillable = ['nama', 'kode_umap', 'region_order'];

    public function lahirMenteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'provinsi_lahir_id');
    }

    public function smaMenteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'lokasi_sma_id');
    }

    public function s1Menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'lokasi_s1_id');
    }

    public function s2Menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'lokasi_s2_id');
    }

    public function s3Menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'lokasi_s3_id');
    }
}
