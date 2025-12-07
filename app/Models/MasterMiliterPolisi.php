<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterMiliterPolisi extends Model
{
    // harus sama persis dengan migration
    protected $table = 'master_militer_polisis';

    protected $fillable = ['label', 'kode_umap'];

    public function menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'militer_polisi_id');
    }
}
