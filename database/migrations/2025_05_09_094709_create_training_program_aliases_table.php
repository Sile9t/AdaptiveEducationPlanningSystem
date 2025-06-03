<?php

use App\Models\TrainingProgram;
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
        Schema::create('training_program_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('alias');
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('program_id');
            $table->foreign('program_id')->references('id')->on('training_programs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_program_aliases');
    }
};
