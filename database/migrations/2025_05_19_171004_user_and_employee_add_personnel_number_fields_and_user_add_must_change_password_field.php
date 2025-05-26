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
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('personnel_number');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('personnel_number', 'must_change_password');
        });
    }
};
