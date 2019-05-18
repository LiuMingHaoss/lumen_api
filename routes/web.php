<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('/test','TestController@test');
$router->post('/decaesar','TestController@decaesar');

//验证签名
$router->post('/signTest','TestController@signTest');

//测试 注册
$router->post('/reg','TestController@reg');
$router->post('/login','TestController@login');

$router->get('/test/b','TestController@b');


//注册接口
$router->post('/user/reg','UserController@reg');
$router->post('/user/login','UserController@login');

$router->get('/user/my',[
    'as'=>'profile',
    'uses'=>'UserController@my',
    'middleware'=>'CheckLogin'
]);

//商品信息
$router->get('/goods/goodsList','GoodsController@goodsList');
$router->get('/goods/goodsDesc','GoodsController@goodsDesc');   //商品详情
$router->post('/goods/goodsCart','GoodsController@goodsCart');  //添加购物车
$router->get('/goods/cartList','GoodsController@cartList');     //购物车列表
$router->get('/goods/orderAdd','GoodsController@orderAdd');     //添加订单
$router->get('/goods/orderList','GoodsController@orderList');   //订单列表



$router->get('/alipay/pay','pay\PayController@pay');   //支付
$router->post('/alipay/notify','pay\PayController@notify');   //异步回调
$router->get('/alipay/return','pay\PayController@aliReturn');   //同步回调
$router->get('/alipay/test','pay\PayController@test');   //同步回调













