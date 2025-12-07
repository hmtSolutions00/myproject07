<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKementerian extends Model
{
    protected $table = 'master_kementerians';

    protected $fillable = [
        'nama_kementerian',
        'kode_umap',
        'urutan',
    ];

    public function menteris(): HasMany
    {
        return $this->hasMany(Menteri::class, 'kementerian_id');
    }
}
