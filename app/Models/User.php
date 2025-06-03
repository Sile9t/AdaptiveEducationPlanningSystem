<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'patronymic',
        'email',
        'password',
        'role_id',
        'branch_id',
        'personnel_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password' => 'bool',
    ];

    /**
     * Get the user role.
    */
    public function role():BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user branch.
    */
    public function branch():BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function full_name():string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function full_name_with_patronymic():string
    {
        return $this->full_name() . ' ' . $this->patronymic;
    }
}
