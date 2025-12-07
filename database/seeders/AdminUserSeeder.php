<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@umap.local'],
            [
                'name' => 'Admin UMAP',
                'password' => Hash::make('admin12345'), // ganti nanti
                'is_admin' => true,
            ]
        );
    }
}
