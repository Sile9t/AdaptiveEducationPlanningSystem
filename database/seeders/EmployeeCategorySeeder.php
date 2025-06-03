<?php

namespace Database\Seeders;

use App\Models\EmployeeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class EmployeeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = new Sequence(
            [
                'name' => 'Руководитель',
                'description' => 'Руководящий состав, принимающий управленческие решения',
            ],
            [
                'name' => 'Специалист',
                'description' => 'Сотрудники, обладающие профильной квалификацией',
            ],
            [
                'name' => 'Рабочий',
                'description' => 'Производственный персонал и технические исполнители',
            ],
        );

        $ids = EmployeeCategory::all();
        if ($ids->count() == 0){
            // Seeding default employee categories
            EmployeeCategory::factory()->count(3)->state($defaultCategories)->create();
        }
    }
}
