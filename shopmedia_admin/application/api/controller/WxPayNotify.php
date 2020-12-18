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
 * 微信支付回调控制器类
 * Class WxPay
 * @package app\api\controller
 */
class WxPayNotify extends Controller
{
    /**
     * 微信支付回调通知
     */
    public function notify()
    {
        //获取通知的数据
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        file_put_contents('./wxpay_notify.txt', json_encode($xml));die;
        Loader::import("payment.wxpay.WxPay", EXTEND_PATH);
        $wxpay  = new \WxPay();
        $result = $wxpay->notify();
        file_put_contents('./wxpay_notify.txt', json_encode($result));

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