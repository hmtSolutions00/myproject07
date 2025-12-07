<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterDprMpr;

class MasterDprMprSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Tidak Anggota', 'kode_umap' => 0],
            ['label' => 'Anggota', 'kode_umap' => 1],
            ['label' => 'Ketua', 'kode_umap' => 2],
        ];

        foreach ($rows as $r) {
            MasterDprMpr::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
