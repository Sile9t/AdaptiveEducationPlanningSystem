<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultBranches = new Sequence(
            [
                'name' => 'Администрация',
            ],
            [
                'name' => 'Алданское ЛПУМГ',
            ],
            [
                'name' => 'Александровское ЛПУМГ',
            ],
            [
                'name' => 'Алтайское ЛПУМГ',
            ],
            [
                'name' => 'Амурское ЛПУМГ',
            ],
            [
                'name' => 'Барабинское ЛПУМГ',
            ],
            [
                'name' => 'Инженерно-технический центр',
            ],
            [
                'name' => 'Иркутское ЛПУМГ',
            ],
            [
                'name' => 'Камчатское ЛПУМГ',
            ],
            [
                'name' => 'Кемеровское ЛПУМГ',
            ],
            [
                'name' => 'Корпоративный институт',
            ],
            [
                'name' => 'Ленское ЛПУМГ',
            ],
            [
                'name' => 'Магистральное ЛПУМГ',
            ],
            [
                'name' => 'Нерюнгринское ЛПУМГ',
            ],
            [
                'name' => 'Новокузнецкое ЛПУМГ',
            ],
            [
                'name' => 'Новосибирское ЛПУМГ',
            ],
            [
                'name' => 'Омское ЛПУМГ',
            ],
            [
                'name' => 'Приморское ЛПУМГ',
            ],
            [
                'name' => 'Сахалинское ЛПУМТ',
            ],
            [
                'name' => 'Свободненское ЛПУМГ',
            ],
            [
                'name' => 'Сковородинское ЛПУМГ',
            ],
            [
                'name' => 'Томское ЛПУМГ',
            ],
            [
                'name' => 'Управление аварийно-восстановительных работ',
            ],
            [
                'name' => 'Управление аварийно-восстановительных работ №2',
            ],
            [
                'name' => 'Управление материально-технического снабжения и комплектации',
            ],
            [
                'name' => 'Управление технологического транспорта и специальной техники',
            ],
            [
                'name' => 'Хабаровское ЛПУМГ',
            ],
            [
                'name' => 'Юргинское ЛПУМГ',
            ],
        );

        $ids = Branch::all(['id']);
        if ($ids->count() == 0) {
            //Seeding default branches
            Branch::factory()->count(28)->state($defaultBranches)->create();
        }
    }
}
