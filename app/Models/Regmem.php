<?php

namespace App\Models;

use App\User;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

class Regmem extends \Illuminate\Foundation\Auth\User
{
    use Notifiable;

    //定义要操作的表
    protected $table='members';
    //定义要操作的字段
    protected $fillable=['username','password','tel','rememberToken'];
}
