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
            //return false;
            $this->responseMsg();
        }
    }

    /**
     * 接收事件推送并回复
     */
    public function responseMsg()
    {
        // 1.获取到微信推送过来的post数据（XML格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];

        // 2.设置消息类型，并设置回复类型和内容
        /* 推送XML数据包示例：
        <xml>
          <ToUserName><![CDATA[toUser]]></ToUserName>
          <FromUserName><![CDATA[FromUser]]></FromUserName>
          <CreateTime>123456789</CreateTime>
          <MsgType><![CDATA[event]]></MsgType>
          <Event><![CDATA[subscribe]]></Event>
        </xml>*/
        $postObj = simplexml_load_string($postArr); // XML格式转对象
        /*$postObj->ToUserName = ''; // 开发者微信号
        $postObj->FromUserName = ''; // 发送方帐号（一个OpenID）
        $postObj->CreateTime = ''; // 消息创建时间 （整型）
        $postObj->MsgType = ''; // 消息类型，event
        $postObj->Event = ''; // 事件类型，subscribe(订阅)、unsubscribe(取消订阅)*/
        // 判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            // 如果是关注 subscribe 事件
            if (strtolower($postObj->Event) == 'subscribe') {
                // 回复用户消息
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time = time();
                $msgType = 'text';
                $content = '欢迎关注。。。';
                $template = '<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>';
                $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
            }
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