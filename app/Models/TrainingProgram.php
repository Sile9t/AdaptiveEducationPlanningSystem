<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingProgram extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
    ];

    /**
     * Get all aliases for specified program.
     *
     * @var array<int, TrainingProgramAlias>
     */
    public function trainingProgramAliases():HasMany
    {
        return $this->hasMany(TrainingProgramAlias::class, 'program_id');
    }

    /**
     * Get all permits for specified program.
     *
     * @var array<int, Permit>
     */
    public function permits():HasMany
    {
        return $this->hasMany(Permit::class, 'program_id');
    }

    /**
     * Get all training events for specified program.
     *
     * @var array<int, TrainingEvent>
     */
    public function trainingEvents():HasMany
    {
        return $this->hasMany(TrainingEvent::class, 'program_id');
    }
}
