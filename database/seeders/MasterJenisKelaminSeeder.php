<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterJenisKelamin;

class MasterJenisKelaminSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Laki-laki', 'kode_umap' => 1],
            ['label' => 'Perempuan', 'kode_umap' => 2],
        ];

        foreach ($rows as $r) {
            MasterJenisKelamin::updateOrCreate(
                ['kode_umap' => $r['kode_umap']],
                $r
            );
        }
    }
}
