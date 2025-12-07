<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPendidikan;

class MasterPendidikanSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label_jurusan' => 'Tidak ada S2/S3', 'kode_umap' => 0, 'jenjang_default' => 's2s3'],
            ['label_jurusan' => 'Kepolisian', 'kode_umap' => 1, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Pertahanan', 'kode_umap' => 2, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Militer', 'kode_umap' => 3, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Hukum', 'kode_umap' => 4, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Ekonomi', 'kode_umap' => 5, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Politik', 'kode_umap' => 6, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Kehutanan', 'kode_umap' => 7, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Teknik', 'kode_umap' => 8, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Sastra', 'kode_umap' => 9, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Perikanan', 'kode_umap' => 10, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Pemerintahan', 'kode_umap' => 11, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Peternakan', 'kode_umap' => 12, 'jenjang_default' => 'umum'],

            ['label_jurusan' => 'Sosial', 'kode_umap' => 13, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Pertanian', 'kode_umap' => 14, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Manajemen', 'kode_umap' => 15, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Kesehatan', 'kode_umap' => 16, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Komunikasi', 'kode_umap' => 17, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Ilmu Lingkungan', 'kode_umap' => 18, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Administrasi', 'kode_umap' => 19, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Agama', 'kode_umap' => 20, 'jenjang_default' => 'umum'],

            // duplikat label Sosial di excel → dipaksa unik
            ['label_jurusan' => 'Sosial (kode 21)', 'kode_umap' => 21, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Kedokteran', 'kode_umap' => 22, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Matematika', 'kode_umap' => 23, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Sosial (kode 24)', 'kode_umap' => 24, 'jenjang_default' => 'umum'],

            ['label_jurusan' => 'Administrasi (kode 25)', 'kode_umap' => 25, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Strategis', 'kode_umap' => 26, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Pendidikan', 'kode_umap' => 27, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Nanoteknologi', 'kode_umap' => 28, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Sejarah', 'kode_umap' => 29, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Sains', 'kode_umap' => 30, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Seni', 'kode_umap' => 31, 'jenjang_default' => 'umum'],
            ['label_jurusan' => 'Biologi', 'kode_umap' => 32, 'jenjang_default' => 'umum'],
        ];

        foreach ($rows as $r) {
            MasterPendidikan::updateOrCreate(
                ['kode_umap' => $r['kode_umap'], 'jenjang_default' => $r['jenjang_default']],
                $r
            );
        }
    }
}
