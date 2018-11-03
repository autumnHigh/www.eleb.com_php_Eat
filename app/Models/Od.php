<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Od extends Model
{
    //声明要操作的表
    protected $table='order_details';
    //声明要操作的字段舒心
    protected $fillable=['order_id','goods_id','amount','goods_name','goods_img','goods_price'];
}
