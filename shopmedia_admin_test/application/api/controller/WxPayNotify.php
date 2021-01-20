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
     * 微信支付回调通知（测试）
     */
    public function notify()
    {
        //获取通知的数据
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        file_put_contents(RUNTIME_PATH . 'wxpay_notify.txt', json_encode($xml) . PHP_EOL, FILE_APPEND);die;
        Loader::import("payment.wxpay.WxPay", EXTEND_PATH);
        $wxpay  = new \WxPay();
        $result = $wxpay->notify();
        file_put_contents(RUNTIME_PATH . 'wxpay_notify.txt', json_encode($result) . PHP_EOL, FILE_APPEND);

        #根据拿到的数据 来进行自己的数据逻辑
        if($result)
        {
            $out_trade_no = $result['out_trade_no'];
            file_put_contents(RUNTIME_PATH . 'wxpay_notify.txt', json_encode($out_trade_no) . PHP_EOL, FILE_APPEND);
            echo "success";exit;
        }
        echo  "error";
    }

    /**
     * 微信支付回调通知通用方法
     * TODO：通过调用查单接口确认是否真正支付成功，再更新订单状态
     * @param string $modelName
     */
    private function _wxPayNotify($modelName)
    {
        //获取通知的数据
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        //file_put_contents(RUNTIME_PATH . 'wxpay_notify.txt', json_encode($xml) . PHP_EOL, FILE_APPEND);
        $result = $this->xmlToArray($xml);
        //file_put_contents(RUNTIME_PATH . 'wxpay_notify.txt', json_encode($result) . PHP_EOL, FILE_APPEND);

        # 根据拿到的数据 来进行自己的数据逻辑
        if($result)
        {
            // 商户订单号
            $out_trade_no = $result['out_trade_no'];
            if ($out_trade_no) {
                // 更新（广告投放、广告套餐）订单状态
                $data = [
                    'pay_status' => 1,
                    'pay_time' => time(),
                ];
                $res = model($modelName)->save($data, ['order_sn' => $out_trade_no]);
            }
            //echo "success";exit;
        }
        //echo  "error";
    }

    /**
     * 将xml转为array
     * @param  string $xml xml字符串
     * @return array       转换得到的数组
     */
    public function xmlToArray($xml){
        # 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }

    /**
     * 广告投放订单微信支付回调通知
     */
    public function adWxPayNotify()
    {
        $this->_wxPayNotify('Ad');
    }

    /**
     * 广告套餐订单微信支付回调通知
     */
    public function adComboWxPayNotify()
    {
        $this->_wxPayNotify('AdComboOrder');
    }
}