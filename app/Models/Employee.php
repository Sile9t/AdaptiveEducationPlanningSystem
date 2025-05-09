<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'position',
        'category_id',
        'branch_id'
    ];

    /**
     * Get the employee category wich employee belongs to.
     *
     * @var <EmployeeCategory>
     */
    public function category():BelongsTo
    {
        return $this->belongsTo(EmployeeCategory::class);
    }

    /**
     * Get the branch wich employee belongs to.
     *
     * @var <Branch>
     */
    public function branch():BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get employees for the role.
     * 
     * @var array<int, Employee>
    */
    public function employees():HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
