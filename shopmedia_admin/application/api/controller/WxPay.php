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
require_once __DIR__ . '/../../../extend/payment/wxpay/php_sdk_v3.0.10/example/WxPay.NativePay.php';

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
            //file_put_contents(RUNTIME_PATH . 'wxpay.txt', json_encode($jsApiParameters) . PHP_EOL, FILE_APPEND);

            //获取共享收货地址js函数参数
            $editAddress = $tools->GetEditAddressParameters();
            //file_put_contents(RUNTIME_PATH . 'wxpay.txt', json_encode($editAddress) . PHP_EOL, FILE_APPEND);

            $data = ['jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress];
            return show(config('code.success'), 'OK', $data);
        } catch (\Exception $e) {
            file_put_contents(RUNTIME_PATH . 'wxpay_exception.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
            throw new ApiException($e->getMessage(), 500);
        }
    }

    /**
     * 微信JSAPI支付通用方法
     * @param array $param
     * @return array
     * @throws ApiException
     */
    public function wxJsApiPay($param = [])
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
            $input->SetBody($param['body']); // 商品描述，必填
            if (isset($param['attach'])) {
                $input->SetAttach($param['attach']); // 附加数据，非必填
            }
            $input->SetOut_trade_no($param['out_trade_no']); // 商户订单号，必填
            $input->SetTotal_fee($param['total_fee']); // 订单总金额，单位为分，必填
            $input->SetTime_start(date("YmdHis")); // 交易起始时间，非必填
            $input->SetTime_expire(date("YmdHis", time() + 600)); // 交易结束时间，非必填
            if (isset($param['goods_tag'])) {
                $input->SetGoods_tag($param['goods_tag']); // 订单优惠标记，非必填
            }
            $input->SetNotify_url($param['notify_url']); // 通知地址，必填：异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
            $input->SetTrade_type("JSAPI"); // 交易类型，必填
            $input->SetOpenid($openId); // 用户标识：trade_type=JSAPI时（即JSAPI支付），此参数必传，此参数为微信用户在商户对应appid下的唯一标识。
            $config = new \WxPayConfig();
            $order = \WxPayApi::unifiedOrder($config, $input);
            //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
            //printf_info($order);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            //file_put_contents(RUNTIME_PATH . 'wxpay.txt', json_encode($jsApiParameters) . PHP_EOL, FILE_APPEND);

            //获取共享收货地址js函数参数
            $editAddress = $tools->GetEditAddressParameters();
            //file_put_contents(RUNTIME_PATH . 'wxpay.txt', PHP_EOL . json_encode($editAddress) . PHP_EOL, FILE_APPEND);

            $data = ['jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress];
            return $data;
        } catch (\Exception $e) {
            file_put_contents(RUNTIME_PATH . 'wxpay_exception.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
            throw new ApiException($e->getMessage(), 500);
        }
    }

    /**
     * 获取微信Native支付（扫码支付）二维码链接通用方法
     * @param array $param
     * @return mixed
     * @throws ApiException
     *
     * 微信Native支付模式二流程：
     * 1、调用统一下单，取得code_url，生成二维码
     * 2、用户扫描二维码，进行支付
     * 3、支付完成之后，微信服务器会通知支付成功
     * 4、在支付成功通知中需要查单确认是否真正支付成功（见：/extend/payment/wxpay/php_sdk_v3.0.10/example/notify.php）
     */
    public function wxNativePayQRCodeUrl($param = [])
    {
        try {
            // 统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody($param['body']); // 商品描述，必填
            //$input->SetAttach(isset($param['attach']) ? $param['attach'] : ''); // 附加数据，非必填
            $input->SetOut_trade_no($param['out_trade_no']); // 商户订单号，必填
            $input->SetTotal_fee($param['total_fee']); // 订单总金额，单位为分，必填
            $input->SetTime_start(date("YmdHis")); // 交易起始时间，非必填
            $input->SetTime_expire(date("YmdHis", time() + 600)); // 交易结束时间，非必填
            //$input->SetGoods_tag(isset($param['goods_tag']) ? $param['goods_tag'] : ''); // 订单优惠标记，非必填
            $input->SetNotify_url($param['notify_url']); // 通知地址，必填
            $input->SetTrade_type("NATIVE"); // 交易类型，必填
            $input->SetProduct_id($param['product_id']); // 商品ID：trade_type=NATIVE时，此参数必传。

            // 获取code_url
            $notify = new \NativePay();
            $result = $notify->GetPayUrl($input);
            $url2 = $result["code_url"];
            //file_put_contents(RUNTIME_PATH . 'wxpay.txt', $url2 . PHP_EOL, FILE_APPEND);
            return $url2;
        } catch (\Exception $e) {
            file_put_contents(RUNTIME_PATH . 'wxpay_exception.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
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
                // 微信JSAPI支付
                $res = $this->wxJsApiPay($param);
                if ($res) {
                    return show(config('code.success'), 'OK', $res);
                }
            }
        }
    }

    /**
     * 获取广告套餐订单微信扫码支付二维码链接
     * @return \think\response\Json
     * @throws ApiException
     */
    public function adComboWxNativePayQRCodeUrl()
    {
        // 判断为POST请求
        if(!request()->isPost()){
            return show(config('code.error'), '请求不合法', '', 400);
        }

        // 传入的参数
        $data = input('post.');

        // 获取广告投放数据
        if (isset($data['order_id']) && $data['order_id']) {
            $adComboOrder = model('AdComboOrder')->find($data['order_id']);
            if (!empty($adComboOrder)) {
                // 微信支付统一下单参数
                $param['body'] = '店通传媒-广告套餐支付';
                //$param['attach'] = '店通传媒'; // 该参数会导致报错
                $param['out_trade_no'] = $adComboOrder['order_sn'];
                $param['total_fee'] = (int)($data['order_price'] * 100);
                //file_put_contents(RUNTIME_PATH . 'wxpay_exception.txt', $param['total_fee'] . "\n" . gettype($param['total_fee']) . PHP_EOL, FILE_APPEND);
                $param['notify_url'] = 'https://media.sustock.net/index.php/api/adComboWxPayNotify';
                $param['product_id'] = $adComboOrder['combo_id'];
                // 获取微信Native支付（扫码支付）二维码链接
                $res = $this->wxNativePayQRCodeUrl($param);
                if ($res) {
                    return show(config('code.success'), 'OK', ['code_url' => $res]);
                }
            }
        }
    }
}