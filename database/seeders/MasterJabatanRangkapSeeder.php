<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterJabatanRangkap;

class MasterJabatanRangkapSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Tidak diketahui / default', 'kode_umap' => 0],
            ['label' => 'Independen', 'kode_umap' => 1],
            ['label' => 'Partai', 'kode_umap' => 2],
            ['label' => 'Pemerintah', 'kode_umap' => 3],
            ['label' => 'BUMN', 'kode_umap' => 4],
        ];

        foreach ($rows as $r) {
            MasterJabatanRangkap::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
