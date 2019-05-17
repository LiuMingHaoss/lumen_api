<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class GoodsController extends BaseController
{

    //商品列表
    public function goodsList(){
     $data=DB::table('api_goods')->get();
     $json=json_encode($data);
     $arr=json_decode($json,true);
     print_r($json);
 }

    //商品详情
    public function goodsDesc(){
        $goods_id=$_GET['goods_id'];
        $goodsInfo=DB::table('api_goods')->where('goods_id',$goods_id)->first();
        return json_encode($goodsInfo);
    }

    //购物车
    public function goodsCart(Request $request){
        $data=$request->input();
        $info=[
          'goods_id'=>$data['goods_id'],
          'goods_name'=>$data['goods_name'],
          'goods_price'=>$data['self_price'],
            'uid'=>$data['uid'],
        ];
        $res=DB::table('api_cart')->insertGetId($info);
        if($res){
            $response=[
              'errno'=>0,
              'msg'=>'添加购物车成功'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }else{
            $response=[
                'errno'=>70001,
                'msg'=>'添加购物车失败'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }
 }

    //购物车列表
    public function cartList(){
     $cartInfo=DB::table('api_cart')->get();
    return json_encode($cartInfo);
 }

    //生成订单
    public function orderAdd(){
        $uid=$_GET['uid'];

        $cartInfo=DB::table('api_cart')->where('uid',$uid)->get();
        $allprice=0;
        foreach($cartInfo as $k=>$v){
            $allprice+=$v->goods_price*$v->buy_number;
        }
        $order_no=Str::random(10);
        $orderInfo=[
          'order_no'=>$order_no,
          'order_amount'=>$allprice,
            'uid'=>$uid,
            'create_time'=>time()
        ];
        $oid=DB::table('api_order')->insertGetId($orderInfo);


        foreach($cartInfo as $key=>$val){
            $order_detail=[
                'oid'=>$oid,
                'goods_id'=>$val->goods_id,
                'goods_name'=>$val->goods_name,
                'goods_price'=>$val->goods_price,
                'uid'=>$uid
            ];
            $res=DB::table('api_order_detail')->insertGetId($order_detail);
        }
        if($res){
            $response=[
              'errno'=>0,
              'msg'=>'添加订单成功'
            ];
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

    }

    //订单列表
    public function orderList(){
        $orderInfo=DB::table('api_order')->get();
        return json_encode($orderInfo,JSON_UNESCAPED_UNICODE);
    }
}
