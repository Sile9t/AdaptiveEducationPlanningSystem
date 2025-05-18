<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_id',
        'category_id',
        'periodicity_years',
    ];

    /**
     * Get the program wich permit belongs to.
     *
     * @var <TrainingProgram>
     */
    public function program():BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'program_id');
    }

    /**
     * Get the category wich permit belongs to.
     *
     * @var <EmployeeCategory>
     */
    public function category():BelongsTo
    {
        return $this->belongsTo(EmployeeCategory::class, 'category_id');
    }
}
