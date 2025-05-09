<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get permits for specified category.
     *
     * @var array<int, Permit>
     */
    public function permits():HasMany
    {
        return $this->hasMany(Permit::class);
    }

    /**
     * Get employees for specified category.
     *
     * @var array<int, Employee>
     */
    public function employees():HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
