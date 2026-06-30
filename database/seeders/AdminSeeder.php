<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate supaya ga duplikat kalau di-seed ulang
        DB::table('users')->updateOrInsert(
            ['email' => 'adminkpi@gmail.com'], // kondisi pengecekan
            [
                'name'       => 'Admin',
                'email'      => 'adminkpi@gmail.com',
                'password'   => Hash::make('adminbaru'),
                'role'       => 'admin', 
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}