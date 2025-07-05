<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultRoles = new Sequence(
            [
                'name' => 'Начальник отдела обучения',
                'description' => 'Начальник отдела обучения: доступ ко всем модулям, аналитике и выгрузкам',
            ],
            [
                'name' => 'Главный менеджер по обучению',
                'description' => 'Главный менеджер по обучению: загрузка/просмотр всех филиалов',
            ],
            [
                'name' => 'Менеджер по обучению (филиал)',
                'description' => 'Менеджер по обучению (филиал): загрузка и просмотр только по своему филиалу   ',
            ],
        );

        $ids = Role::all(['id']);
        if ($ids->count() == 0) {
            // Seeding basic user roles
            Role::factory()->count(3)->state($defaultRoles)->create();
        }
    }
}
