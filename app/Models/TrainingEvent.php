<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'program_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'passed_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * Get the program wich event belongs to.
     *
     * @var <TrainingProgram>
     */
    public function trainingProgram():BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class);
    }    
    
    /**
     * Get the employee wich event wired with.
     *
     * @var <Employee>
     */
    public function employee():BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
