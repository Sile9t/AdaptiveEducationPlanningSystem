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
            $table->addColumn('string','last_name');
            $table->string('patronymic')->nullable();
            $table->string('email', 190)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_name', 'patronymic');
            $table->string('email')->change();
        });
    }
};
