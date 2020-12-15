<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/7/1
 * Time: 19:54
 */

namespace app\api\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Loader;

require_once __DIR__ . '/../../../extend/payment/wxpay/php_sdk_v3.0.10/lib/WxPay.Api.php';
require_once __DIR__ . '/../../../extend/payment/wxpay/php_sdk_v3.0.10/example/WxPay.JsApiPay.php';
require_once __DIR__ . '/../../../extend/payment/wxpay/php_sdk_v3.0.10/example/WxPay.Config.php';
//require_once __DIR__ . '/../../../extend/payment/wxpay/php_sdk_v3.0.10/lib/WxPay.Data.php'; // lib/WxPay.Api.php文件中已引入

/**
 * 微信支付控制器类
 * Class WxPay
 * @package app\api\controller
 */
class WxPay extends AuthBase
{
    /**
     * 微信JSAPI调起支付
     * @return \think\response\Json
     * @throws ApiException
     */
    public function index()
    {
        try{
            // ①、获取用户openid
            //Loader::import("payment.wxpay.JsApiPay", EXTEND_PATH);
            $tools = new \JsApiPay();
            //$openId = $tools->GetOpenid();
            $userOauth = Db::name('user_oauth')->where(['user_id' => $this->user['user_id'], 'oauth' => 'wx'])->find();
            $openId = $userOauth['openid']; // TODO：此处从数据库获取openid，网页授权获取openid参考https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_4

            // ②、统一下单
            //Loader::import("payment.wxpay.lib.WxPayUnifiedOrder", EXTEND_PATH);
            $input = new \WxPayUnifiedOrder();
            $input->SetBody("test");
            $input->SetAttach("test");
            $input->SetOut_trade_no("sdkphp".date("YmdHis"));
            $input->SetTotal_fee("1");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url("https://media.sustock.net/index.php/api/wxPayNotify");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $config = new \WxPayConfig();
            $order = \WxPayApi::unifiedOrder($config, $input);
            //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
            //printf_info($order);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            //file_put_contents('./wxpay.txt', json_encode($jsApiParameters));

            //获取共享收货地址js函数参数
            $editAddress = $tools->GetEditAddressParameters();
            //file_put_contents('./wxpay.txt', '\\n' . json_encode($editAddress), FILE_APPEND);

            $data = ['jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress];
            return show(config('code.success'), 'OK', $data);
        }catch (\Exception $e){
            file_put_contents('./wxpayException.txt', json_encode($e->getMessage()));
            throw new ApiException($e->getMessage(), 500);
        }
    }
}