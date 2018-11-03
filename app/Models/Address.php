<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //定义要操作的表
    protected $table='addresses';
    //定义要操作的字段
    protected $fillable=['user_id','province','city','city','county','address','tel','name','is_default'];
}
