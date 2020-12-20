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
     * 微信JSAPI支付（测试）
     * @return \think\response\Json
     * @throws ApiException
     */
    public function index()
    {
        try {
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
        } catch (\Exception $e) {
            file_put_contents('./wxpayException.txt', json_encode($e->getMessage()));
            throw new ApiException($e->getMessage(), 500);
        }
    }

    /**
     * 微信（JSAPI）支付通用方法
     * @param array $param
     * @return array
     * @throws ApiException
     */
    public function wxPay($param = [])
    {
        try {
            // ①、获取用户openid
            //Loader::import("payment.wxpay.JsApiPay", EXTEND_PATH);
            $tools = new \JsApiPay();
            //$openId = $tools->GetOpenid();
            $userOauth = Db::name('user_oauth')->where(['user_id' => $this->user['user_id'], 'oauth' => 'wx'])->find();
            $openId = $userOauth['openid']; // TODO：此处从数据库获取openid，网页授权获取openid参考https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_4

            // ②、统一下单
            //Loader::import("payment.wxpay.lib.WxPayUnifiedOrder", EXTEND_PATH);
            $input = new \WxPayUnifiedOrder();
            $input->SetBody($param['body']); // 商品描述
            if (isset($param['attach'])) {
                $input->SetAttach($param['attach']); // 附加数据，非必填
            }
            $input->SetOut_trade_no($param['out_trade_no']); // 商户订单号
            $input->SetTotal_fee($param['total_fee']); // 订单总金额，单位为分
            $input->SetTime_start(date("YmdHis")); // 交易起始时间，非必填
            $input->SetTime_expire(date("YmdHis", time() + 600)); // 交易结束时间，非必填
            if (isset($param['goods_tag'])) {
                $input->SetGoods_tag($param['goods_tag']); // 订单优惠标记，非必填
            }
            $input->SetNotify_url($param['notify_url']); // 通知地址：异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数
            $input->SetTrade_type("JSAPI"); // 交易类型
            $input->SetOpenid($openId); // 用户标识：trade_type=JSAPI时（即JSAPI支付），此参数必传，此参数为微信用户在商户对应appid下的唯一标识。
            $config = new \WxPayConfig();
            $order = \WxPayApi::unifiedOrder($config, $input);
            //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
            //printf_info($order);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            //file_put_contents('./wxpay.txt', json_encode($jsApiParameters));

            //获取共享收货地址js函数参数
            $editAddress = $tools->GetEditAddressParameters();
            //file_put_contents('./wxpay.txt', PHP_EOL . json_encode($editAddress), FILE_APPEND);

            $data = ['jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress];
            return $data;
        } catch (\Exception $e) {
            file_put_contents('./wxpayException.txt', json_encode($e->getMessage()));
            throw new ApiException($e->getMessage(), 500);
        }
    }

    /**
     * 广告投放订单微信支付
     * @return \think\response\Json
     * @throws ApiException
     */
    public function adWxPay()
    {
        // 判断为POST请求
        if(!request()->isPost()){
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $data = input('post.');

        // 获取广告投放数据
        if (isset($data['ad_id']) && $data['ad_id']) {
            $ad = model('Ad')->find($data['ad_id']);
            if (!empty($ad)) {
                // 微信支付统一下单参数
                $param['body'] = '店通传媒-广告投放支付';
                $param['attach'] = '店通传媒';
                $param['out_trade_no'] = $ad['order_sn'];
                $param['total_fee'] = (int)($data['ad_price'] * 100);
                $param['notify_url'] = 'https://media.sustock.net/index.php/api/adWxPayNotify';
                // 微信支付
                $res = $this->wxPay($param);
                if ($res) {
                    return show(config('code.success'), 'OK', $res);
                }
            }
        }
    }
}