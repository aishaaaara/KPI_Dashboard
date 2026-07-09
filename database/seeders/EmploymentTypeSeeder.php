<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employmentTypes = [
            'Full-Time',
            'Intern',
        ];

        foreach ($employmentTypes as $type) {
            DB::table('employment_types')->insert([
                'name'       => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}