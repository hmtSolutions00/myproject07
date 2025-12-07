<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterHartaLevel;

class MasterHartaLevelSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Tidak ada / default', 'kode_umap' => 0],
            ['label' => '< 5 Miliar', 'kode_umap' => 1],
            ['label' => '5 - 10 Miliar', 'kode_umap' => 2],
            ['label' => '10 - 15 Miliar', 'kode_umap' => 3],
            ['label' => '15 - 20 Miliar', 'kode_umap' => 4],
            ['label' => '20 - 30 Miliar', 'kode_umap' => 5],
            ['label' => '30 - 50 Miliar', 'kode_umap' => 6],
            ['label' => '50 - 100 Miliar', 'kode_umap' => 7],
            ['label' => '> 100 Miliar', 'kode_umap' => 8],
        ];

        foreach ($rows as $r) {
            MasterHartaLevel::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
