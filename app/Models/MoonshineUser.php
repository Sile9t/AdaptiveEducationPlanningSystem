<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use MoonShine\Laravel\Models\MoonshineUser as ModelsMoonshineUser;

class MoonshineUser extends ModelsMoonshineUser
{
    use HasApiTokens;
}
