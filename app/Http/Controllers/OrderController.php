<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Od;
use App\Models\Order;
use App\Models\Shop;
use App\Models\SignatureHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    //添加订单数据，同时添加数据到订单商品表中
    public function shopsave(Request $request){

       //dump($request->all());
       /* $shop_id=32;
//dump($shop_id);
        $user=DB::table('users')->where('shop_id','=',$shop_id)->first();
//dump($user);
        //定义保存发怂邮件需要的数据
        $data=['name'=>$user->name,'email'=>$user->email];
        dump($data);
        //发送邮件信息
        $this->send($data);
        dump('邮件发送成功');
        exit;*/



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


            //发送邮件,根据上面查询的最后一个购物车的shop_id得到哪一个商家的 shop_id数据，再去查询符合的商户表的数据
            $user=DB::table('users')->where('shop_id','=',$shop_id)->first();
//dump($user);
            //定义保存发怂邮件需要的数据
            $data=['name'=>$user->name,'email'=>$user->email];
           // dump($data);
            //发送邮件信息
            $this->send($data);
            //订单成功下单之后，发送短信到用户手机上
            $this->message(Auth::user()->tel);



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


    //下单成功之后，发送短信给用户
    public function message($tel){



        //dump($request->all());
       // exit;
        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIInQ3Y57LPePO";
        $accessKeySecret = "LgdJ2WhnWSFMmTJu6xE2MDb4bpOdmY";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "庞克的个人case";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_150570073";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => "12345",
            //"product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            )),
            $security
        );

        $content;
    }

    //下订单发送商家邮件处理订单
    public function send($data){
        Mail::send('email', ['name'=>$data['name']], function($message) use($data)
        {
            $message->to($data['email'])->subject('新的订单需要处理！');
        });
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
            'order_birth_time'=>date('Y-m-d H-i-s',strtotime($order->created_at)),
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
                //'order_birth_time'=>date('Y-m-d H-i-s',strtotime($order->created_at)),
                'order_birth_time'=>$order->created_at->toDatetimeString(),//拿出来的是一个对象，需要转换为时间日期格式
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
