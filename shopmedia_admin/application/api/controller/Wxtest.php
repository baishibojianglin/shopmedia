<?php

namespace app\api\controller;
use think\Controller;
use think\Request;
use think\Db;


class Wxtest extends Controller
{

    public  function index()
    {
        // 获取随机字符串
        $echostr = $_GET['echostr'];
        if(checkSignature() && $echostr){ // 第一次接入微信API接口时
            echo $echostr; // 这样写才能验证成功
            exit;
        }else {
            responseMsg();
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


    

 
}
