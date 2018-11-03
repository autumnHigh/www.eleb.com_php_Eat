<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Od;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //添加订单数据，同时添加数据到订单商品表中
    public function shopsave(Request $request){

/*
        if(!$request->address_id){
            return [
                "status"=> "true",
                "message"=> "添加失败",
                "order_id"=>'00'
            ];
        }*/
        //dump($request->all());

        //专门给返回值使用的order_id
        $od_id='';

        DB::beginTransaction();

        /*try{

            //DB::commit();
//dump(222);
        }catch(\Exception $e){
            //dump($e);
            DB::rollback();
        }*/
        try{

            //dump($request->all());
            //$address=Address::where('id','=',$request->address_id)->first();
            $address=Address::where('id','=',$request->address_id)->first();
            // dump($address);


            //还要查找menus表中的数据，Auth::user()->id
            $cart=Cart::where('user_id','=',Auth::user()->id)->orderBy('id','desc')->get();
            //通过排序得到的最后一条的数据，查询商品id
            // dump($cart[0]->goods_id);
            //通过商品id  ,$cart[0]->goods_id得到符合的商品的shop_id
            $menu=DB::table('menus')->where('id','=',$cart[0]->goods_id)->first();
            // dump($menu->shop_id);
            $shop_id=$menu->shop_id;
            // dump($shop_id);

            //得到carts中的订单列表，得到total最终价格 Auth::user()->id
            $carts=Cart::where('user_id','=',Auth::user()->id)->get();
            //dump($carts);
            //在外部定义一个空的存储价格的变量
            $totalCost=0;
            //定义一个存放查询数组的空数组

            foreach($carts as $cart){
                //dump($cart->goods_id);
                //根据goods_id得到商品表的数据
                $menu=Menu::where('id','=',$cart->goods_id)->get();
                //dump($menu);
                foreach($menu as $meu){
                    //dump($cart->goods_id,$meu->goods_price);
                    $totalCost+=$meu->goods_price;
                }

            }
//dump($totalCost);//打印总价格
            //dump($carts);

            // dump($totalCost);//订单的总价格
            //  dump($shop_id);
            // exit;
            //往order表中插入收集到的数据
            $order=Order::create([
                'user_id'=>Auth::user()->id,
                'shop_id'=>$shop_id,
                'sn'=>date('YmdHis',time()).rand('1111','9999'),
                'province'=>$address->province,
                'city'=>$address->city,
                'county'=>$address->county,
                'address'=>$address->address,
                'tel'=>$address->tel,
                'name'=>$address->name,
                'total'=>$totalCost,
                'status'=>0,
                'out_trade_no'=>str_random('10')
            ]);
            //dump($order->id);//打印先添加的订单的id


            //获得order表插入的id，再把总和数据放入到order_details表中
            $order_id=$order->id;//得到新增的订单表的id，插入到订单详情表中
            $od_id=$order->id;//给外部全局变量使用

            foreach($carts as $cart){
                // dump($cart->id);

                $menus=Menu::where('id','=',$cart->goods_id)->get();
//dump($menus);
                foreach($menus as $menu){
                    //dump($menu->goods_name,$menu->id);
                    // dump($menu->goods_img);//循环查看菜单的东西

                    //插入数据到order_details中
                    Od::create([
                        'order_id'=>$order_id,//添加的是前面添加的订单表的id
                        'goods_id'=>$cart->goods_id,
                        'amount'=>$cart->amount,
                        'goods_name'=>$menu->goods_name,
                        'goods_img'=>$menu->goods_img,
                        'goods_price'=>$menu->goods_price
                    ]);
                }

            }

//dump(111);
            //下了订单之后，就直接删除订单表中carts中的数据
            /*$caart=Cart::where('user_id','=',Auth::user()->id)->get();*/

            //执行循环命令删除
            /* foreach($caart as $car){
                 //执行删除功能
                 $car->delete();//上面收集数据的时候。就已经存在数据，不用再去查询了
             }*/
            DB::table('carts')->where('user_id','=',Auth::user()->id)->delete();

            DB::commit();

            //返回执行状态
            return [
                "status"=> "true",
                "message"=> "添加成功",
                "order_id"=>$od_id
            ];

        }catch(\Exception $e){

            DB::rollBack();

            //返回执行状态
            return [
                "status"=> "false",
                "message"=> "添加失败",
                "order_id"=>$od_id
            ];

        }



    }

    //显示订单数据到表单中
    public function list(){
        $order=Order::where('user_id','=',Auth::user()->id)->first();
        //dump($order->city);
        //根据$order->id得到order_details表中的符合的数据
        $ods=Od::where('order_id','=',$order->id)->get();
        //dump($ods);
        //根据shop_id得到商铺表中的shop_img地址
        $shop=Shop::where('id','=',$order->shop_id)->first();

        //订单详情表的所有数据形成的数组
        $odss=[];

        foreach($ods as $od){
            $ode=[
                "goods_id"=>$od->goods_id,
                "goods_name"=>$od->goods_name,
                "goods_img"=>$od->goods_img,
                "amount"=>$od->amount,
                "goods_price"=>$od->goods_price
            ];
            $odss[]=$ode;
        }


        $date=[
            'id'=>$order->id,
            'order_code'=>$order->sn,
            'order_birth_time'=>$order->created_at,
            'order_status'=>$order->status,//前端的订单页面详细按钮，改成 '代付款'
            'shop_id'=>$order->shop_id,
            'shop_img'=>$shop->shop_img,
            'goods_list'=>$odss,
            'order_price'=>$order->total,
            'order_address'=>$order->address
        ];

        //返回给调用列表
        return $date;

    }


    //所有订单列表
    public function orderlist(){
        $orders=Order::where('user_id','=',Auth::user()->id)->get();
        //dump($orders);

        //定义一个大数组，包裹晓得订单数组
        $datas=[];

        //订单详情表的所有数据形成的数组
        $odss=[];

        foreach($orders as $order){
            //dump($order->id);exit;
            //收集返回数据模块
            //根据$order->id得到order_details表中的符合的数据
            $ods=Od::where('order_id','=',$order->id)->get();
            //dump($ods);
            //根据shop_id得到商铺表中的shop_img地址
            $shop=Shop::where('id','=',$order->shop_id)->first();



            foreach($ods as $od){
                $ode=[
                    "goods_id"=>$od->goods_id,
                    "goods_name"=>$od->goods_name,
                    "goods_img"=>$od->goods_img,
                    "amount"=>$od->amount,
                    "goods_price"=>$od->goods_price
                ];
                $odss[]=$ode;
            }



           //整体返回数据模块

            $data=[
                'id'=>$order->id,
                'order_code'=>$order->sn,
                'order_birth_time'=>$order->created_at,
                'order_status'=>$order->status,
                'shop_id'=>$order->shop_id,
                'shop_img'=>$shop->shop_img,
                'goods_list'=>$odss,//详细订单数据
                'order_price'=>$order->total,
                'order_address'=>$order->address
            ];
            $datas[]=$data;
        }

        //返回给调用列表
        return $datas;

    }



}
