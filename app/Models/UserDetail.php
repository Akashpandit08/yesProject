<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'profile_image',
        'name',
        'phone',
        'email',
        'street_address',
        'city',
        'country',
        'state',
    ];
}
