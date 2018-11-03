<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    //保存新增的数据
    public function saveadd(Request $request){

        //dump($request->all());

        $goods_list=$request->goodsList;
       $goods_count=$request->goodsCount;

       $carts=Cart::where('user_id','=',Auth::user()->id)->get();
       // dump($carts);

        //$goods_list=[2,6,7];

       for($i=0;$i<count($goods_list);++$i){
           //dump($goods_list[$i]);

           $has=Cart::where([ ['user_id','=',Auth::user()->id],['goods_id','=',$goods_list[$i]] ])->first();
    //dump($has);
            if(!$has){
                Cart::create([
                'user_id'=>Auth::user()->id,
                'goods_id'=>$goods_list[$i],
                'amount'=>$goods_count[$i]
            ]);

            }else{
                foreach ($carts as $cart){
                    //dump($cart);
                    if($cart->goods_id==$goods_list[$i]){
                        $has->update([
                            'amount'=>$has->amount+$goods_count[$i]
                        ]);
                    }
                }
            }


       }


       return [
           'status'=>'true',
           'message'=>'添加成功'
       ];

    }


    //读取购物车数据到列表中
    public function list(Request $request){
        //dump($request->all());

            //$carts=Cart::where('user_id','=',1)->get();

        //计算总价格
        $totalPrice=0;
        //返回数组
        $datas='';


        //查询购物车carts表中的符合当前用户的数据

            $carts=Cart::where('user_id',Auth::user()->id)->get();
            //dump($carts[1]->goods_id);
            //使用查询出来的购物车的goods_id查询menus菜品表中的数据，一一对应，根据数量*价格=goods_price
            foreach($carts as $cart){
                //dump($cart->goods_id);
                //查询菜品中的符合购物车id的数据
               // $menu=DB::table('menus')->where('id','=',$cart->goods_id)->first();
               // dump($menu);

                     $goods_list= [

                         "goods_id"=> $cart->goods_id,
                         "goods_name"=> $cart->getMenu->goods_name,
                         "goods_img"=> $cart->getMenu->goods_img,
                         "amount"=> $cart->amount,
                         "goods_price"=> $cart->getMenu->goods_price

                     ];

                $datas[]=$goods_list;
                  $totalPrice+=$cart->getMenu->goods_price*$cart->amount;
            }
                $data=[
                    'goods_list'=>$datas,
                    'totalCost'=>$totalPrice
                ];

                return $data;
    }
}
