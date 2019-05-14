<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class UserController extends BaseController
{
    //
   public function reg(Request $request){
       header('Access-Control-Allow-Origin:*');
       $data=$request->input();
       $email=DB::table('api_user')->where('email',$data['email'])->first();
       if($email){
           $response=[
               'errno'=>50001,
               'msg'=>'邮箱已注册',
           ];
           die(json_encode($response,JSON_UNESCAPED_UNICODE));
       }
       $info=[
           'username'=>$data['username'],
           'email'=>$data['email'],
           'pwd'=>$data['pwd']
       ];
       $res=DB::table('api_user')->insert($info);
       if($res){
           $response=[
               'errno'=>0,
               'msg'=>'注册成功',
           ];
           die(json_encode($response,JSON_UNESCAPED_UNICODE));

       }else{
           $response=[
               'errno'=>40001,
               'msg'=>'注册失败',
           ];
           die(json_encode($response,JSON_UNESCAPED_UNICODE));

       }
   }

   public function login(Request $request){
       header('Access-Control-Allow-Origin:*');
       $data=$request->input();
       $arr=DB::table('api_user')->where('email',$data['email'])->first();
       if($arr){
           if($arr->pwd===$data['pwd']){

               $key='token:uid:'.$arr->id;
               $token=Redis::get($key);
               if(!$token){
                   $token=Str::random(8);
                   Redis::set($key,$token);
                   Redis::expire($key,604800);
               }
               $response=[
                   'errno'=>0,
                   'msg'=>'登录成功',
                   'token'=>$token,
                    'uid'=>$arr->id
               ];
               die(json_encode($response,JSON_UNESCAPED_UNICODE));
           }else{
               $response=[
                   'errno'=>50003,
                   'msg'=>'密码错误',
               ];
               die(json_encode($response,JSON_UNESCAPED_UNICODE));
           }
       }else{
           $response=[
               'errno'=>50002,
               'msg'=>'邮箱不存在',
           ];
           die(json_encode($response,JSON_UNESCAPED_UNICODE));
       }
   }
}
