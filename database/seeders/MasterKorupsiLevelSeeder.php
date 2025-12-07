<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterKorupsiLevel;

class MasterKorupsiLevelSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Tidak ada / default', 'kode_umap' => 0],
            ['label' => 'Level 1', 'kode_umap' => 1],
            ['label' => 'Level 2', 'kode_umap' => 2],
            ['label' => 'Level 3', 'kode_umap' => 3],
            ['label' => 'Level 4', 'kode_umap' => 4],
            ['label' => 'Level 5', 'kode_umap' => 5],
        ];

        foreach ($rows as $r) {
            MasterKorupsiLevel::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
