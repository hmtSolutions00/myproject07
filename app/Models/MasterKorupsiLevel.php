<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKorupsiLevel extends Model
{
    protected $table = 'master_korupsi_levels';

    protected $fillable = ['label', 'kode_umap'];

    public function menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'korupsi_level_id');
    }
}
