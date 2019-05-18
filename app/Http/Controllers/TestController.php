<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class TestController extends BaseController
{
    //
    public function test(){

        // 加密算法
        $encryptMethod = 'AES-256-CBC';
        // 明文数据
        $iv = 'abcddddddddddddo';
        $str=$_GET['str'];
        $encrypted=base64_decode($str);
        echo 'b64:'.$str;echo '<br>';
        $decrypted = openssl_decrypt($encrypted, $encryptMethod, 'secret', OPENSSL_RAW_DATA, $iv);
        echo '密文：'.$encrypted;echo '<br>';
        echo '解密：'.$decrypted;
    }

    //非对称解密
    public function decaesar(){
        $arr=file_get_contents('php://input');
        $ba64=base64_decode($arr);
        //解密
        $public_key=openssl_get_publickey('file://'.storage_path('app/openssl/public_key.pem'));
        openssl_public_decrypt($ba64,$de_str,$public_key);
        echo '解密：'.$de_str;
    }

    //验证签名
    public function signTest(){

        $ba64=$_GET['sign'];
        $json_str=file_get_contents('php://input');
        echo '数据：'.$json_str;echo '<hr>';
        $sign=base64_decode($ba64);
        $public_key=openssl_get_publickey('file://'.storage_path('app/openssl/public_key.pem'));
        $v=openssl_verify($json_str,$sign,$public_key);
        echo '验证签名：'.$v;
    }

    //注册
    public function reg(){
        $ba64=file_get_contents('php://input');
        $json_str=base64_decode($ba64);
        $public_key=openssl_get_publickey('file://'.storage_path('app/openssl/public_key.pem'));
        openssl_public_decrypt($json_str,$de_str,$public_key);
        $data=json_decode($de_str);

        $email=DB::table('test_api')->where('email',$data->email)->first();
        if($email){
            $response=[
                'errno'=>50001,
                'msg'=>'邮箱已注册',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $info=[
          'username'=>$data->username,
            'email'=>$data->email,
            'pwd'=>$data->pwd
        ];
        $res=DB::table('test_api')->insert($info);
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

    //登录
    public function login(){
        $ba64=file_get_contents('php://input');
        $json_str=base64_decode($ba64);
        $public_key=openssl_get_publickey('file://'.storage_path('app/openssl/public_key.pem'));
        openssl_public_decrypt($json_str,$de_str,$public_key);
        $data=json_decode($de_str);
        $arr=DB::table('test_api')->where('email',$data->email)->first();
        if($arr){
            if($arr->pwd===$data->pwd){

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

    public function b(){
//        header('Access-Control-Allow-Origin:http://client.1809a.com');
        echo 'alert("aaa")';
    }
}
