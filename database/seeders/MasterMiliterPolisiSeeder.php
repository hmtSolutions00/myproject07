<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterMiliterPolisi;

class MasterMiliterPolisiSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Tidak ada / default', 'kode_umap' => 0],
            ['label' => 'Militer', 'kode_umap' => 1],
            ['label' => 'Polisi', 'kode_umap' => 2],
        ];

        foreach ($rows as $r) {
            MasterMiliterPolisi::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
