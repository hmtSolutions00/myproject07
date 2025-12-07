<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterUmurKategori extends Model
{
    protected $table = 'master_umur_kategoris';

    protected $fillable = ['label', 'kode_umap'];

    public function menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'umur_kategori_id');
    }
}
