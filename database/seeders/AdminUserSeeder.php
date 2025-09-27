<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@unfv.edu.pe'],
            [
                'name' => 'admin',
                'password' => 'admin',
                'role' => 'admin',
                'status' => 'active',
                'document_type' => 'DNI',
                'document_number' => '00000000',
                'school_id' => null,
            ]
        );
    }
}