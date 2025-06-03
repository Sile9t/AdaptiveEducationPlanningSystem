<?php

namespace Database\Seeders;

use App\Models\TrainingProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TrainingProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPrograms = new Sequence(
            [
                'title' => 'Охрана труда'
            ],
            [
                'title' => 'Пожарная безопасность'
            ],
            [
                'title' => 'Промышленная безопасность'
            ],
            [
                'title' => 'Электробезопасность'
            ],
            [
                'title' => 'Работы на высоте'
            ],
            [
                'title' => 'Оказание первой помощи пострадавшим'
            ],
            [
                'title' => 'Экологическая безопасность'
            ],
            [
                'title' => 'Пожарно-технический минимум'
            ],
            [
                'title' => 'Безопасность при работах с сосудами под давлением'
            ],
            [
                'title' => 'Безопасность работ на газораспределительных станциях'
            ],
            [
                'title' => 'Безопасность строительных работ'
            ],
            [
                'title' => 'Охрана окружающей среды'
            ],
        );

        $ids = TrainingProgram::all(['id']);
        if ($ids->count() == 0) {
            // Seeding default programs
            TrainingProgram::factory()->count(12)->state($defaultPrograms)->create();
        }
    }
}
