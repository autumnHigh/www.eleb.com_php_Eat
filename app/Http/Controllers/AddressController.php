<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    //保存添加的数据到指定的地址表中
    public function saveadd(Request $request){
        //dump($request->all());

        //判断如何有默认为 is_default的为1的话，那么其他的就是0

        Address::create([
            'user_id'=>Auth::user()->id,
            'province'=>$request->provence,
            'city'=>$request->city,
            'county'=>$request->area,
            'address'=>$request->detail_address,
            'tel'=>$request->tel,
            'name'=>$request->name,
           /* 'is_default'=>1*/
        ]);
        return [
            'status'=>'true',
            'message'=>'添加成功'
        ];

    }

    //显示地址素有数据的列表
    public function list(){
        //dump(111);
        $address=Address::where('user_id','=',Auth::user()->id)->get();
        //定义一个空数组，进行二维数组推送到外部列表
        $addres=[];

        foreach($address as $addr){
            $adds=[
                "id"=>$addr->id,
                "provence"=>$addr->province,
                "city"=>$addr->city,
                "area"=>$addr->county,
                "detail_address"=>$addr->address,
                "name"=>$addr->name,
                "tel"=>$addr->tel
            ];
           $addres[]=$adds;
        }

        //运行到最后，返回数据到addresslist中
        return $addres;

    }

    //修改指定的地址信息
    public function change(Request $request){
        //dump($request->all());
        //dump(1121);
        //根据传入的id查询出当前的的数据，返回到页面上
        $address=Address::where('id','=',$request->id)->first();

        //保存指定的数据

        $data=[
            "id"=> $address->id,
             "provence"=> $address->province,
             "city"=> $address->city,
             "area"=> $address->county,
             "detail_address"=> $address->address,
             "name"=>$address->name,
             "tel"=> $address->tel
        ];

        //返回指定的数据到页面中
        return $data;
    }

    //保存修改的地址信息栏
    public function savechange(Request $request){
        //dump($request->all());
        //根据传入的id得到符合自身的数据，再去修改

        $addres=Address::where('id','=',$request->id)->first();
        //进行修改对指定的数据修改
        $addres->update([
            "provence"=>$request->province,
            "city"=>$request->city,
            "county"=>$request->area,
            "detail_address"=>$request->address,
            "name"=>$request->name,
            "tel"=>$request->tel
        ]);

        //返回提示信息到窗口上
        return [
            "status"=> "true",
            "message"=> "修改成功"
        ];

    }

}
