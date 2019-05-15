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
//       header('Access-Control-Allow-Origin:*');

       //接收登录数据
       $data=$request->input();
       $json_str=json_encode($data);
//       $url='http://passport.1809a.com/user/reg';
       $url='http://passport.chenyys.com/user/reg';

       $ch=curl_init();
       curl_setopt($ch,CURLOPT_URL,$url);
       curl_setopt($ch,CURLOPT_POST,1);
       curl_setopt($ch,CURLOPT_POSTFIELDS,$json_str);
       curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-type:text/plain']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //获取页面内容 不直接输出
       $a=curl_exec($ch);
       curl_errno($ch);
       echo $a;
       curl_close($ch);

   }

   public function login(Request $request){
       $data=$request->input();
       $json_str=json_encode($data);
//       $url='http://passport.1809a.com/user/login';
       $url='http://passport.chenyys.com/user/login';
       $ch=curl_init();
       curl_setopt($ch,CURLOPT_URL,$url);
       curl_setopt($ch,CURLOPT_POST,1);
       curl_setopt($ch,CURLOPT_POSTFIELDS,$json_str);
       curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-type:text/plain']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //获取页面内容 不直接输出
       $a=curl_exec($ch);
       curl_errno($ch);
       echo $a;
       curl_close($ch);
   }

   public function my(){
//       header('Access-Control-Allow-Origin:*');
       $uid=$_GET['uid'];
       $userInfo=DB::table('api_user')->where('id',$uid)->first();
       $json_arr=json_encode($userInfo);
       echo $json_arr;
   }
}
