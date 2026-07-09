<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            'Product & Data',
            'Software Engineers',
        ];

        foreach ($teams as $team) {
            DB::table('teams')->insert([
                'name'       => $team,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}