<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterKementerianSeeder::class,
            MasterJenisKelaminSeeder::class,
            MasterProvinsiSeeder::class,
            MasterUmurKategoriSeeder::class,
            MasterPartaiSeeder::class,
            MasterJabatanRangkapSeeder::class,
            MasterDprMprSeeder::class,
            MasterMiliterPolisiSeeder::class,
            MasterPendidikanSeeder::class,
            MasterKorupsiLevelSeeder::class,
            MasterHartaLevelSeeder::class,
            MenteriSeeder::class, // nanti kita isi dari Excel
        ]);
    }
}
