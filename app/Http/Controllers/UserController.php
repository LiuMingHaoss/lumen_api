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
        var_dump($data);
   }
}
