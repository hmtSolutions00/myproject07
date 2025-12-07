<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPartai;

class MasterPartaiSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_partai' => 'Tidak ada / default', 'kode_umap' => 0],
            ['nama_partai' => 'Nonpartai', 'kode_umap' => 1],
            ['nama_partai' => 'Partai Gerakan Indonesia Raya (Gerindra)', 'kode_umap' => 2],
            ['nama_partai' => 'Partai Bulan Bintang (PBB)', 'kode_umap' => 3],
            ['nama_partai' => 'Partai Golongan Karya (Golkar)', 'kode_umap' => 4],
            ['nama_partai' => 'Partai Demokrat', 'kode_umap' => 5],
            ['nama_partai' => 'Partai Kebangkitan Bangsa (PKB)', 'kode_umap' => 6],
            ['nama_partai' => 'Partai Amanat Nasional (PAN)', 'kode_umap' => 7],
            ['nama_partai' => 'Partai Solidaritas Indonesia (PSI)', 'kode_umap' => 8],
            ['nama_partai' => 'Partai Rakyat Adil Makmur (PRIMA)', 'kode_umap' => 9],
            ['nama_partai' => 'Partai Gelombang Rakyat Indonesia (Gelora)', 'kode_umap' => 10],
            ['nama_partai' => 'Partai Keadilan dan Persatuan (PKP)', 'kode_umap' => 11],
            ['nama_partai' => 'Partai Persatuan Indonesia (Perindo)', 'kode_umap' => 12],
        ];

        foreach ($rows as $r) {
            MasterPartai::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
