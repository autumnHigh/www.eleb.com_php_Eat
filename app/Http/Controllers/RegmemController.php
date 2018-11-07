<?php

namespace App\Http\Controllers;

use App\Models\Regmem;
use App\Models\SignatureHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class RegmemController extends Controller
{
    //保存表单数据到members会员表中
    public function tel(Request $request){
        dump($request->all());

        //在点击获取按钮的时候，就验证，手机号码是否存在，如果存在就允许获得==》发送验证码，如果没有 变更为添加用户，用户名默认为@电话号码

        //随机生成一个redis变量名
        //$code=str_random('6');
        Redis::setex('code'.$request->tel,300,mt_rand('100000','999999'));
        $code=Redis::get('code'.$request->tel);
        dump($code);

        $date=['tel'=>$request->tel,'code'=>$code];
        dump($date['tel'],$date['code']);

        $this->send($date);

        //dump('成功');

        return [
            'status'=>'true',
            'message'=>'获取短信验证成功'
        ];



       /* $sends=$this->send($date);
       // return '131';
        //如果发送成功，就返回成功信息
        if($sends->Message == 'OK'){
            return [
                'status'=>'true',
                'message'=>'获取短信验证成功'
            ];
        }*/


    }

    //发送短信验证的数据
    public function send($date){
        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        //$accessKeyId = "LTAIQTCsXfAc924O";
        $accessKeyId = "LTAIj3HeUkqPbIJr";
        //$accessKeySecret = "jjW8VTOJmWCOHLKlNTB0CM2VleIErQ";
        $accessKeySecret = "F6rD2vmIoUSWjByRgIaibhgahKWPu3";

        // fixme 必填: 短信接收号码
        //$params["PhoneNumbers"] = $date['tel'];
        $params["PhoneNumbers"] =$date['tel'];

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "苏木情深";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_149097544";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            //"code" => str_random('6'),
            "code" => $date['code'],
            //"product" => "阿里通信"
        );

   // dump($date['code']);
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

        dump($content);
    }



    //登陆之后修改密码模块
    public function changePassword(Request $request){
        //功能没做。。。。。
        //dump('1222');
        //根据登陆的id得到自身的数据，进行修改操作
        $regmem=Regmem::where('id','=',Auth::user()->id)->first();
//dump($request->all());

        if(Hash::check($request->oldPassword,Auth::user()->password)){

            $regmem->update([
                'password'=>bcrypt($request->newPassword)
            ]);

            return [
                "status"=>"true",
                "message"=>"修改成功"
            ];

        }else{
            return [
                "status"=>"false",
                "message"=>"旧密码错误"
            ];
        }

       /* $regmem->update([
            'password'=>$request->newPassword
        ]);

        return [
            "status"=>"true",
            "message"=>"修改成功"
        ];*/
    }



    //会员表members表注册信息
    public function register(Request $request){
        //dump($request->all());

        $code=Redis::get('code'.$request->tel);
        //dump($request->sms,$code);

        //判断如果手机验证码和发送的redis保存的验证码相同就允许注册
        if($request->sms == $code){
            Regmem::create([
                'username'=>$request->username,
                'password'=>bcrypt($request->password),
                'tel'=>$request->tel,
                'rememberToken'=>str_random('10')
            ]);
            return [
                'status'=>'true',
                'message'=>'注册成功'
            ];
        }else{
            return [
                'status'=>'true',
                'message'=>'注册失败'
            ];
        }

    }


    //修改会员表密码，根据手机号码？
    public function forgetPassword(Request $request){
        //dump($request->all());

        //根据传入的电话号码tel=$request->tel,得到老的数据,
       $old_msg=Regmem::where('tel','=',$request->tel)->first();
       if(!$old_msg){
           return [
               'status'=>'false',
               'message'=>'验证码错误'
           ];
       }

       if($request->sms == Redis::get('code'.$request->tel)){
           //验证传入的电话验证码sms 和redis保存的密码是否一致，如果一致就进行修改

           $old_msg->update([
               'password'=>bcrypt($request->password),
               'rememberToken'=>str_random('10')
           ]);

           return [
               'status'=>'true',
               'message'=>'修改成功'
           ];

       }else{
           return [
               'status'=>'true',
               'message'=>'验证码错误'
           ];
       }



    }


    //验证Members会员表登陆模块
    public function login(Request $request){
        //dump($request->all());
        if(Auth::attempt(['username'=>$request->name,'password'=>$request->password])){
            return [
                'status'=>'true',
                'message'=>'登陆成功',
                'user_id'=>Auth::user()->id,
                'username'=>Auth::user()->username,
            ];
        }else{
            return [
                'status'=>'false',
                'message'=>'用户名或密码错误',
                'user_id'=>'0',
                'username'=>'guest'
            ];
        }

    }



}
