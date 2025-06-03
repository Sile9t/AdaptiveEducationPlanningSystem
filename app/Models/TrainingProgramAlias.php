<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingProgramAlias extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alias',
        'comment',
        'program_id'
    ];

    /**
     * Get the user role.
    */
    public function trainingProgram():BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'program_id');
    }
}
