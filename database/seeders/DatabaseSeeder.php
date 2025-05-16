<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
        $this->call([
            RoleSeeder::class,
            BranchSeeder::class,
            TrainingProgramSeeder::class,
            EmployeeCategorySeeder::class,
            PermitSeeder::class,
        ]);

=======
>>>>>>> 15bfcefd7c654678f6c234e51a8eb84189995c34
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
