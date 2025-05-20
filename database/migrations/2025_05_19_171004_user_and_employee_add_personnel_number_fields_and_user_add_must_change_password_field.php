<?php

use App\Models\Branch;
use App\Models\EmployeeCategory;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('personnel_number')->unique()->nullable();
            $table->boolean('must_change_password')->default(0);
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->string('personnel_number')->unique()->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('position');
            $table->foreignIdFor(EmployeeCategory::class, 'category_id');
            $table->foreignIdFor(Branch::class, 'branch_id');
            $table->timestamps();
        });

        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('second_name');
            $table->string('patronymic')->nullable();
            $table->string('email', 190)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignIdFor(Role::class, 'role_id');
            $table->foreignIdFor(Branch::class, 'branch_id');
            $table->timestamps();
        });
    }
};
