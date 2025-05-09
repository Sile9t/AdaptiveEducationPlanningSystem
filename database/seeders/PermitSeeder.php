<?php

namespace Database\Seeders;

use App\Models\EmployeeCategory;
use App\Models\Permit;
use App\Models\TrainingProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periodicities = [ 3, 3, 5, 1, 1, 1, 3, 1, 1, 3, 5, 3 ];
        $programs = TrainingProgram::all(['id']);
        $categories = EmployeeCategory::all(['id']);

        $ids = Permit::all(['id']);
        if ($ids->count() == 0) {
            // Seeding permits into database
            for ($i = 0; $i < $programs->count(); $i++) {
                foreach ($categories as $category){
                    Permit::factory()->count(1)->state([
                        'program_id' => $programs[$i],
                        'category_id' => $category,
                        'periodicity_years' => $periodicities[$i],
                    ])->create();
                }
            }
        }
    }
}
