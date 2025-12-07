<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterHartaLevel extends Model
{
    protected $table = 'master_harta_levels';

    protected $fillable = ['label', 'kode_umap'];

    public function menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'harta_level_id');
    }
}
