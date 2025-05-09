<?php

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
            $table->string('email', 190)->change();
            $table->renameColumn('name', 'first_name');
            $table->string('last_name');
            $table->string('patronymic')->nullable();
            $table->foreignIdFor(Role::class, 'role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->change();
            $table->renameColumn('first_name', 'name');
            $table->dropColumn('last_name');
            $table->dropColumn('patronymic');
            $table->dropForeignIdFor(Role::class, 'role_id');
        });
    }
};
