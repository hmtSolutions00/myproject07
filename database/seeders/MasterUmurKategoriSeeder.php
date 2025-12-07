<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterUmurKategori;

class MasterUmurKategoriSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
             ['label' => 'Tidak ada / default', 'kode_umap' => 0],
            ['label' => '10-an', 'kode_umap' => 1],
            ['label' => '20-an', 'kode_umap' => 2],
            ['label' => '30-an', 'kode_umap' => 3],
            ['label' => '40-an', 'kode_umap' => 4],
            ['label' => '50-an', 'kode_umap' => 5],
            ['label' => '60-an', 'kode_umap' => 6],
            ['label' => '70-an', 'kode_umap' => 7],
        ];

        foreach ($rows as $r) {
            MasterUmurKategori::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
