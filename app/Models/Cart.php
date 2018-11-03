<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //定义要操作的表
    protected $table='carts';
    //定义要操作的字段属性
    protected $fillable=['user_id','goods_id','amount'];

    public function getMenu(){
        return $this->belongsTo(Menu::class,'goods_id','id');
    }
}
