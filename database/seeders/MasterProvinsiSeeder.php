<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterProvinsi;

class MasterProvinsiSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama' => 'Luar Negeri / Tidak diketahui', 'kode_umap' => 0, 'region_order' => 0],
            ['nama' => 'Aceh', 'kode_umap' => 1, 'region_order' => 1],
            ['nama' => 'Sumatera Utara', 'kode_umap' => 2, 'region_order' => 2],
            ['nama' => 'Sumatera Barat', 'kode_umap' => 3, 'region_order' => 3],
            ['nama' => 'Riau', 'kode_umap' => 4, 'region_order' => 4],
            ['nama' => 'Kepulauan Riau', 'kode_umap' => 5, 'region_order' => 5],
            ['nama' => 'Jambi', 'kode_umap' => 6, 'region_order' => 6],
            ['nama' => 'Bengkulu', 'kode_umap' => 7, 'region_order' => 7],
            ['nama' => 'Sumatera Selatan', 'kode_umap' => 8, 'region_order' => 8],
            ['nama' => 'Kepulauan Bangka Belitung', 'kode_umap' => 9, 'region_order' => 9],
            ['nama' => 'Lampung', 'kode_umap' => 10, 'region_order' => 10],
            ['nama' => 'DKI Jakarta', 'kode_umap' => 11, 'region_order' => 11],
            ['nama' => 'Jawa Barat', 'kode_umap' => 12, 'region_order' => 12],
            ['nama' => 'Banten', 'kode_umap' => 13, 'region_order' => 13],
            ['nama' => 'Jawa Tengah', 'kode_umap' => 14, 'region_order' => 14],
            ['nama' => 'Daerah Istimewa Yogyakarta', 'kode_umap' => 15, 'region_order' => 15],
            ['nama' => 'Jawa Timur', 'kode_umap' => 16, 'region_order' => 16],
            ['nama' => 'Bali', 'kode_umap' => 17, 'region_order' => 17],
            ['nama' => 'Nusa Tenggara Barat', 'kode_umap' => 18, 'region_order' => 18],
            ['nama' => 'Nusa Tenggara Timur', 'kode_umap' => 19, 'region_order' => 19],
            ['nama' => 'Kalimantan Barat', 'kode_umap' => 20, 'region_order' => 20],
            ['nama' => 'Kalimantan Tengah', 'kode_umap' => 21, 'region_order' => 21],
            ['nama' => 'Kalimantan Selatan', 'kode_umap' => 22, 'region_order' => 22],
            ['nama' => 'Kalimantan Timur', 'kode_umap' => 23, 'region_order' => 23],
            ['nama' => 'Kalimantan Utara', 'kode_umap' => 24, 'region_order' => 24],
            ['nama' => 'Sulawesi Utara', 'kode_umap' => 25, 'region_order' => 25],
            ['nama' => 'Gorontalo', 'kode_umap' => 26, 'region_order' => 26],
            ['nama' => 'Sulawesi Tengah', 'kode_umap' => 27, 'region_order' => 27],
            ['nama' => 'Sulawesi Barat', 'kode_umap' => 28, 'region_order' => 28],
            ['nama' => 'Sulawesi Selatan', 'kode_umap' => 29, 'region_order' => 29],
            ['nama' => 'Sulawesi Tenggara', 'kode_umap' => 30, 'region_order' => 30],
            ['nama' => 'Maluku Utara', 'kode_umap' => 31, 'region_order' => 31],
            ['nama' => 'Maluku', 'kode_umap' => 32, 'region_order' => 32],
            ['nama' => 'Papua', 'kode_umap' => 33, 'region_order' => 33],
            ['nama' => 'Papua Barat', 'kode_umap' => 34, 'region_order' => 34],
            ['nama' => 'Papua Tengah', 'kode_umap' => 35, 'region_order' => 35],
            ['nama' => 'Papua Pegunungan', 'kode_umap' => 36, 'region_order' => 36],
            ['nama' => 'Papua Selatan', 'kode_umap' => 37, 'region_order' => 37],
            ['nama' => 'Papua Barat Daya', 'kode_umap' => 38, 'region_order' => 38],
            ['nama' => 'Luar Negeri', 'kode_umap' => 39, 'region_order' => 39],
        ];

        foreach ($rows as $r) {
            MasterProvinsi::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
