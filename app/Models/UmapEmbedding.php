<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UmapEmbedding extends Model
{
    protected $table = 'umap_embeddings';

    protected $fillable = [
        'menteri_id',
        'umap_x',
        'umap_y',
        'color_code',   // ✅ tambahkan ini
        'batch_tag',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'umap_x' => 'float',
        'umap_y' => 'float',
    ];

    public function menteri(): BelongsTo
    {
        return $this->belongsTo(Menteri::class, 'menteri_id');
    }
}
