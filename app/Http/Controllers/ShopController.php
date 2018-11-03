<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    //显示商户信息的接口
    public function show(Request $request){
        //return '12313';

        $showAll=Shop::where('shop_name','like',"%{$request->keyword}%")->get();

        $datas=[];

        foreach($showAll as $shop){
           // dump($shop);
            $data=[
                'id'=>$shop->id,
                'shop_name'=>$shop->shop_name,
                "shop_img"=>$shop->shop_img,
                "shop_rating"=>$shop->shop_rating,
                "brand"=>$shop->brand,
                "on_time"=>$shop->on_time,
                "fengniao"=>$shop->fengniao,
                "bao"=>$shop->bao,
                "piao"=>$shop->piao,
                "zhun"=>$shop->zhun,
                "start_send"=>$shop->start_zend,
                "send_cost"=>$shop->start_cost,
                "distance"=>$shop->discount,
                "estimate_time"=>30,
                "notice"=>$shop->notice,
                "discount"=>$shop->discount
            ];


            $datas[]=$data;
        }
       // dd($datas);
        return $datas;

    }


    //查看点击上架菜单之后的商家菜品分类，和菜品分类数据
    public function index()
    {
        $id = $_GET['id'] ?? '32';
        //dd($id);

        //商家店铺的信息
        $shops = DB::table('shops')->where('id', '=', $id)->first();
        //dd($shops);

        //查询商家菜品分类的数据
        $menucate = DB::table('menu_categories')->where('shop_id', '=', $id)->get();
        // dump($menucate);
        //商家菜单的信息
        $menus = DB::table('menus')->where('shop_id', '=', $id)->get();
        //dump($menus);

        //分类菜品所需的参数
        $cates = [];
        //分类下的详细菜品

        $goods_list = [];
        // $goods_lists=[];
        foreach ($menus as $menu) {
            //dump($menu->category_id);
            //if($menuc->id==$menu->category_id){
            $goodS = [
                "goods_id" => $menu->id,
                "goods_name" => $menu->goods_name,
                "rating" => $menu->rating,
                "goods_price" => $menu->goods_price,
                "description" => $menu->description,
                "month_sales" => $menu->month_sales,
                "rating_count" => $menu->rating_count,
                "tips" => $menu->tips,
                "satisfy_count" => $menu->satisfy_count,
                "satisfy_rate" => $menu->satisfy_rate,
                "goods_img" => $menu->goods_img
            ];
            $goods_list[$menu->category_id][]= $goodS;//将菜品表的菜品分类id category_id 当做二维数组的key，再把循环出来的菜品装进符合分类id的数组中，形成一个二维数组
        //}

            //dump($goodS);
        }

        //dump($goods_list);

        //$goods_lists=[
        // '4'=>[
        //  [],[],[]
        //  ],'5'=>[
        //  [],[],[]
        //  ]
        //]


        foreach($menucate as $menuc){
            //dump($menuc->id);
            //每一次循环的时候，把前一次的菜单的数据清空，避免堆叠菜品，
            //array_splice($goods_lists,0);
            //第二种直接数组清空；
            //$goods_lists=[];//在内部直接制空。。。

            
       // dd($goods_lists);

            $cate=[
                "description"=>$menuc->description,
                "is_selected"=>$menuc->is_selected,
                "name"=>$menuc->name,
                "type_accumulation"=>$menuc->type_accumulation,
                //菜品分类下面的菜品详细数据
                "goods_list"=>$goods_list[$menuc->id]
            ];

            $cates[]=$cate;

        }

//dd($cates);

//exit;

        $data=[
            'id'=>$shops->id,
            'shop_name'=>$shops->shop_name,
            "shop_img"=>$shops->shop_img,
            "shop_rating"=>$shops->shop_rating,
            "brand"=>$shops->brand,
            "on_time"=>$shops->on_time,
            "fengniao"=>$shops->fengniao,
            "bao"=>$shops->bao,
            "piao"=>$shops->piao,
            "zhun"=>$shops->zhun,
            "start_send"=>$shops->start_zend,
            "send_cost"=>$shops->start_cost,
            "distance"=>$shops->discount,
            "estimate_time"=>30,
            "notice"=>$shops->notice,
            "discount"=>$shops->discount,
            //店铺评价的参数
            "evaluate"=>[
                [ "user_id"=> 12344,
                "username"=> "w******k",
                "user_img"=> "/images/slider-pic4.jpeg",
                "time"=> "2017-2-22",
                "evaluate_code"=> 1,
                "send_time"=> 30,
                "evaluate_details"=> "不怎么好吃"]
            ],


            //菜品分类下面的菜品信息模块
            "commodity"=> $cates


        ];


        return $data;

    }


}
