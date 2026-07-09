<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Lead Developer',
            'Project Manager',
            'Full Stack Developer',
            'Backend Developer',
            'Frontend Developer',
            'WordPress Developer',
            'Mobile Developer',
            'UI/UX Designer',
            'Quality Assurance',
            'System Analyst',
            'Cyber Security',
        ];

        foreach ($positions as $position) {
            DB::table('positions')->insert([
                'name'       => $position,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}