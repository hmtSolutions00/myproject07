<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterPartai extends Model
{
    protected $table = 'master_partais';

    protected $fillable = ['nama_partai', 'kode_umap'];

    public function menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'partai_id');
    }
}
