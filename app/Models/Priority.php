<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

enum PriorityStatus
{
    case Passed;
    case Expiring;
    case Control;
    case Active;
}

class Priority extends Model
{
    use HasFactory;

    protected $connection = 'redis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'category',
        'position',
        'branch',
        'permit', //equal to 'training program' name
    ];

        /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'passed_at' => 'datetime',
        'expired_at' => 'datetime',
        'status' => Priority::class,
    ];
}
