<?php

namespace app\api\controller;
use think\Controller;
use think\Request;
use think\Db;


class Wxtest extends Controller
{


  public function index()
  {
      // 获取随机字符串
      $echostr = $_GET['echostr'];
      if($this->checkSignature() && $echostr){ // 第一次接入微信API接口时
         echo $echostr; // 这样写才能验证成功
         exit;
      }else {
         $this->responseMsg();
      }
  }
    
       
  public function checkSignature()
  {
          $signature = $_GET["signature"];
          $timestamp = $_GET["timestamp"];
          $nonce = $_GET["nonce"];
        
          $token = '459e201a4cbfe4245b6078e65b51a03f';
          $tmpArr = array($token, $timestamp, $nonce);
          sort($tmpArr, SORT_STRING);
          $tmpStr = implode( $tmpArr );
          $tmpStr = sha1( $tmpStr );
          
          if( $tmpStr == $signature ){
              return true;
          }else{
              return false;
          }

  }


  /**
   * 接收事件推送并回复
   */
  public function responseMsg()
  {
      // 1.获取到微信推送过来的post数据（XML格式）
      //$postArr = $GLOBALS['HTTP_RAW_POST_DATA']; // php7版本以上不支持
      $postArr = file_get_contents('php://input'); // php7+

      // 2.处理消息类型，并设置回复类型和内容
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
              $toUser   = $postObj->FromUserName;
              $fromUser = $postObj->ToUserName;
              $time     = time();
              $msgType  = 'text';
              $content  = '欢迎关注我们的测试微信公众号';
              $template = "<xml>
                          <ToUserName><![CDATA[%s]]></ToUserName>
                          <FromUserName><![CDATA[%s]]></FromUserName>
                          <CreateTime>%s</CreateTime>
                          <MsgType><![CDATA[%s]]></MsgType>
                          <Content><![CDATA[%s]]></Content>
                          </xml>";
              $info     = sprintf($template, $toUser, $fromUser, $time, $msgType,$content);
              echo $info;
          }else{
              echo '不是订阅事件';
          }
      }
  }







    

 
}
