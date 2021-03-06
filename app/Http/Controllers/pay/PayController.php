<?php

namespace App\Http\Controllers\Pay;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PayController extends BaseController
{
    public $app_id;
    public $gate_way;
    public $notify_url;
    public $return_url;
    public $rsaPrivateKeyFilePath;
    public $aliPubKey;


    public function __construct()
    {
        $this->app_id = env('ALIPAY_APPID');
        $this->gate_way = 'https://openapi.alipaydev.com/gateway.do';
        $this->notify_url = 'http://lmhapi.chenyys.com/alipay/notify';
        $this->return_url = 'http://lmhapi.chenyys.com/alipay/return';
        $this->rsaPrivateKeyFilePath = storage_path('app/openssl/private_key.pem');    //应用私钥
        $this->aliPubKey = storage_path('app/openssl/ali_pub.key'); //支付宝公钥
    }

    /**
     * 订单支付
     * @param $oid
     */
    public function pay()
    {

        $oid=$_GET['oid'];
        //验证订单状态 是否已支付 是否是有效订单
        $order_info = DB::table('api_order')->where(['id'=>$oid])->first();
        $json=json_encode($order_info);
        $order_info=json_decode($json,true);
        echo '<pre>';print_r($order_info);echo '</pre>';echo '<hr>';

        //判断订单是否已被支付
        if($order_info['pay_time']>0){
            die("订单已支付，请勿重复支付");
        }
        //判断订单是否已被删除
        if($order_info['is_del']==1){
            die("订单已被删除，无法支付");
        }

        //业务参数
        $bizcont = [
            'subject'           => 'Lening-Order: ' .$oid,
            'out_trade_no'      => $order_info['order_no'],
            'total_amount'      => $order_info['order_amount'] ,
            'product_code'      => 'QUICK_WAP_WAY',
        ];

        //公共参数
        $data = [
            'app_id'   => $this->app_id,
            'method'   => 'alipay.trade.wap.pay',
            'format'   => 'JSON',
            'charset'   => 'utf-8',
            'sign_type'   => 'RSA2',
            'timestamp'   => date('Y-m-d H:i:s'),
            'version'   => '1.0',
            'notify_url'   => $this->notify_url,        //异步通知地址
            'return_url'   => $this->return_url,        // 同步通知地址
            'biz_content'   => json_encode($bizcont),
        ];

        //签名
        $sign = $this->rsaSign($data);
        $data['sign'] = $sign;
        $param_str = '?';
        foreach($data as $k=>$v){
            $param_str .= $k.'='.urlencode($v) . '&';
        }

        $url = rtrim($param_str,'&');
        $url = $this->gate_way . $url;
        header("Location:".$url);       // 重定向到支付宝支付页面
    }


    public function rsaSign($params) {
        return $this->sign($this->getSignContent($params));
    }

    protected function sign($data) {

        $priKey = file_get_contents($this->rsaPrivateKeyFilePath);
        $res = openssl_get_privatekey($priKey);

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);

        if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }


    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, 'UTF-8');
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }


    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {

        if (!empty($data)) {
            $fileType = 'UTF-8';
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }


    /**
     * 支付宝异步通知
     */
    public function notify()
    {
        $p = json_encode($_POST);
        $data=json_decode($p,true);
        $log_str = "\n>>>>>> " .date('Y-m-d H:i:s') . ' '.$p . " \n";
        file_put_contents('logs/alipay_notify',$log_str,FILE_APPEND);
        echo 'success';
        //TODO 验签 更新订单状态
        $pay_time = strtotime($data['gmt_payment']);
        DB::table('api_order')->where('order_no',$data['out_trade_no'])->update(['pay_time'=>$pay_time]);
    }

    /**
     * 支付宝同步通知
     */
    public function aliReturn()
    {
        echo '<pre>';print_r($_GET);echo '</pre>';
    }

}
