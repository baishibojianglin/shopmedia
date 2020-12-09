<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/7/1
 * Time: 19:54
 */

namespace app\api\controller;

use think\Controller;

// 支付宝支付
/*if (is_file(__DIR__ . '/../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
}*/
require __DIR__ . '/../../../vendor/autoload.php';

use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Config;

class Payment extends AuthBase
{
    public function __construct()
    {
        parent::__construct();
        //1. 设置参数（全局只需设置一次）
        Factory::setOptions($this->getAlipayOptions());
    }

    public function index()
    {
        //return json('okk');
//        try {
//            //return show(1, 'exception2123', []);
//            $aa = $this->getAlipayOptions();
//        } catch (\Exception $e) {
//            return show(1, 'exception' . $e->getMessage(), []);
//        }

        try {
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
            $result = Factory::payment()->common()->create("iPhone6 16G", "20200326235526001", "0.01", "2088002656718920");

            //3. 处理响应或异常
            if (!empty($result['code']) && $result['code'] == 10000) {
                //echo "调用成功". PHP_EOL;
                //return json("调用成功". PHP_EOL);
                return show(1, "调用成功", $result, 201);
            } else {
                //echo "调用失败，原因：". $result['msg']."，".$result['sub_msg'].PHP_EOL;
                //return json("调用失败，原因：". $result['msg']."，".$result['sub_msg'].PHP_EOL);
                return show(0, "调用失败，原因：". $result['msg']."，".$result['sub_msg'].PHP_EOL, [], 400);
            }
        } catch (\Exception $e) {
            //echo "调用失败，". $e->getMessage(). PHP_EOL;
            //return json("调用失败，". $e->getMessage(). PHP_EOL);
            return show(0, "调用失败，". $e->getMessage(). PHP_EOL, [], 501);
        }
    }

    /**
     * 获取Alipay参数
     * @return Config
     */
    private function getAlipayOptions()
    {
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType = 'RSA2';

        $options->appId = config('alipay.app_id'); // 请填写您的AppId

        // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
        $options->merchantPrivateKey = config('alipay.merchant_private_key') ;  // 请填写您的应用私钥

        // 证书模式
        //$options->alipayCertPath = '<-- 请填写您的支付宝公钥证书文件路径，例如：/foo/alipayCertPublicKey_RSA2.crt -->';
        //$options->alipayRootCertPath = '<-- 请填写您的支付宝根证书文件路径，例如：/foo/alipayRootCert.crt" -->';
        //$options->merchantCertPath = '<-- 请填写您的应用公钥证书文件路径，例如：/foo/appCertPublicKey_2019051064521003.crt -->';

        //注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
        $options->alipayPublicKey = config('alipay.alipay_public_key'); // 请填写您的支付宝公钥

        //可设置异步通知接收服务地址（可选）
        //$options->notifyUrl = "<-- 请填写您的支付类接口异步通知接收服务地址，例如：https://www.test.com/callback -->";

        //可设置AES密钥，调用AES加解密相关接口时需要（可选）
        //$options->encryptKey = "<-- 请填写您的AES密钥，例如：aa4BtZ4tspm2wnXLb1ThQA== -->";


        return $options;
    }
}