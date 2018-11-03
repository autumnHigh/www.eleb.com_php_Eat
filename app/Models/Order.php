<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //定义要操作的数据表
    protected $table='orders';
    //定义要操作的字段属性
    protected $fillable=['out_trade_no','status','total','name','tel','address','county','city','province','sn','shop_id','user_id'];

    //得到menus商品表的详细数据
    public function menuInfo(){
        return $this->belongsTo(Cart::class,'goods_id','id');
    }


}
