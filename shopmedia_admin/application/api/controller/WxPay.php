<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/7/1
 * Time: 19:54
 */

namespace app\api\controller;

//use pay\Wxpay;
use think\Controller;
use think\Db;
use think\Loader;

/**
 * 微信支付控制器类
 * Class WxPay
 * @package app\api\controller
 */
class WxPay extends AuthBase
{
    /**
     * 微信支付
     * @return \think\response\Json
     */
    public function index()
    {
        $pay_sn = date('YmdHis').rand(1000,9999);
        $total_fee = '0.01';
        $body   = "商品描述";
        $spbill_create_ip = '192.168.0.1';
        $notify_url = "你的回调地址";  #根据不同类型回调地址不同
        $trade_type = 'JSAPI';
        #JSAPI--JSAPI支付（或小程序支付）、
        #NATIVE--Native支付、
        #APP--app支付，
        #MWEB--H5支付，
        #不同trade_type决定了调起支付的方式，请根据支付产品正确上传


        #新的需要参数为六个
        # out_trade_no 商户订单号
        # total_fee    订单总额
        # body         商品描述
        # spbill_create_ip   终端IP
        # notify_url   回调通知地址
        # trade_type   交易类型

        Loader::import("payment.wxpay.WxPay", EXTEND_PATH);
        $wxpay  = new \WxPay();
        $date = [
            'out_trade_no'  => $pay_sn,
            'total_fee'     => $total_fee,
            'body'          => $body,
            'spbill_create_ip' => request()->ip(),
            'notify_url' => $notify_url,
            'trade_type' => $trade_type
        ];

        #根据 trade_type  类型不同，是否传递openid
        #APP 类型支付调用
        if ($trade_type == 'APP') {
            $res = $wxpay->index($date);
        }
        #JSAPI 类型支付调用
        if ($trade_type == 'JSAPI') {
            $userOauth = Db::name('user_oauth')->where(['user_id' => $this->user['user_id'], 'oauth' => 'wx'])->find();
            $openid = $userOauth['openid']; // TODO：此处从数据库获取openid，网页授权获取openid参考https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_4
            $res = $wxpay->index($date,$openid);
        }

        #获得后将对应内容返回前端即可
        //return $res;
        if (isset($res) && $res) {
            file_put_contents('./wxpay.txt', json_encode($res));
            return show(config('code.success'), '支付成功', $res);
        } else {
            file_put_contents('./wxpay.txt', '支付失败');
        }
    }

    /**
     * 微信支付回调
     */
    public function notify()
    {
        $wxpay  = new Wxpay();
        $result = $wxpay->notify();

        #根据拿到的数据 来进行自己的数据逻辑
        if($result)
        {
            $out_trade_no = $result['out_trade_no'];
            file_put_contents('./wxpay_notify.txt', json_encode($out_trade_no));
            echo "success";exit;
        }
        echo  "error";
    }
}