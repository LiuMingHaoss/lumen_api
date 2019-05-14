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



