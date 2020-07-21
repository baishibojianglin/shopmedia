<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/7/20
 * Time: 16:25
 */

namespace app\api\controller;

use think\Controller;

/**
 * 微信公众号
 * Class WeChant
 * @package app\api\controller
 */
class WeChant extends Controller
{
    /**
     * 检验signature
     *
     * 加密/校验流程如下：
     * 1）将token、timestamp、nonce三个参数进行字典序排序
     * 2）将三个参数字符串拼接成一个字符串进行sha1加密
     * 3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
     *
     * @return bool
     */
    public function checkSignature()
    {
        // 1）将token、timestamp、nonce三个参数进行字典序排序
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = '459e201a4cbfe4245b6078e65b51a03f';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);

        // 2）将三个参数字符串拼接成一个字符串进行sha1加密
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        // 3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
        if( $tmpStr == $signature ){
            //return true;
            echo $_GET['echostr']; // 这样写才能验证成功
            exit;
        }else {
            return false;
        }
    }

    /**
     * curl请求
     * @param string $url 接口url
     * @param string $type 请求方式
     * @param string $res 返回数据类型
     * @param string $arr post请求参数
     * @return mixed
     */
    public function http_curl($url, $type = 'get', $res = 'json', $arr = '')
    {
        // 1.初始化curl
        $ch = curl_init();

        // 2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }

        // 3.采集
        $output = curl_exec($ch);

        // 4.关闭
        curl_close($ch);

        if ($res == 'json') {
            return json_decode($output, true);
        }
        var_dump($output);
    }

    public function definedItem()
    {
        // 创建微信菜单
        // 目前微信接口的调用方式都是通过curl post/get
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token;
        $postArr = array(

        );
        $postJson = json_encode($postArr);
        $this->http_curl($url, 'get', $res = 'json', $arr = '');
    }
}