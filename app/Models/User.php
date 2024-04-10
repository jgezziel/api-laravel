<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user';

    protected $fillable = ['name', 'firstName', 'lastName', 'age', 'email', 'password'];

    protected $hidden = ['password', 'created_at', 'updated_at', 'deleted_at'];

}
