<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
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
     * Get users for the role.
     * 
     * @var array<int, User>
    */
    public function users():HasMany
    {
        return $this->hasMany(User::class);
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
