<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


//阿里云oss对象存储
if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

//对象存储
use OSS\OssClient;
use OSS\Core\OssException;

//短信
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;



/**
 * 阿里短信接口
 */

function sendmessage($phone){


    /*设置短信验证码*/
    $informationcode=session('informationcode');
    if(empty($informationcode)){
        $informationcode=mt_rand(1000,9999);
        session('informationcode',$informationcode);  
    }else{
        session('informationcode',null);
        $informationcode=mt_rand(1000,9999);
        session('informationcode',$informationcode);  
    }
    AlibabaCloud::accessKeyClient('LTAI4Fu2RQ1sZsL55xAgNZhs', 'srZezsN1ZQ1WkP8DFsH6gs09JBXL74')->regionId('cn-hangzhou')->asDefaultClient();
    try {
        $result = AlibabaCloud::rpc()
                              ->product('Dysmsapi')
                              // ->scheme('https') // https | http
                              ->version('2017-05-25')
                              ->action('SendSms')
                              ->method('POST')
                              ->host('dysmsapi.aliyuncs.com')
                              ->options([
                                            'query' => [
                                              'RegionId' => "cn-hangzhou",
                                              'PhoneNumbers' => $phone,
                                              'SignName' => "商市通",
                                              'TemplateCode' => "SMS_186870504",
                                              'TemplateParam' => "{\"code\":\"".$informationcode."\"}",
                                            ],
                                        ])
                              ->request();
        //print_r($result->toArray());
        //return json($informationcode);
    } catch (ClientException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    } catch (ServerException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    }

}





  /**
  *上传图片
  */

   function uploadimage($info)
    {

        // 阿里云RAM账号AccessKey
        $accessKeyId = "LTAI4FkCSGwQHirzGvdvWqiG";
        $accessKeySecret = "ACpMHxZXPkkl23ont4mQfzjCZKtL3L";
        // Endpoint以成都为例，其它Region请按实际情况填写。
        $endpoint = "http://oss-cn-chengdu.aliyuncs.com";
        // 存储空间名称
        $bucket = "goodshopimages";
        // 文件名称
        $object =md5(uniqid(mt_rand(),true)).$info['name'];
        // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt
        $filePath = $info['tmp_name'];

        try{
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $result=$ossClient->uploadFile($bucket, $object, $filePath);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }

        //返回图片地址

        $data['name']=$object;
        $data['url']=$result['info']['url'];
        return $data;

      }


  /**
  *删除图片
  */
 function deleteimage($data)
    {

        // 阿里云RAM账号AccessKey
        $accessKeyId = "LTAI4FkCSGwQHirzGvdvWqiG";
        $accessKeySecret = "ACpMHxZXPkkl23ont4mQfzjCZKtL3L";
        // Endpoint以成都为例，其它Region请按实际情况填写。
        $endpoint = "http://oss-cn-chengdu.aliyuncs.com";
        // 存储空间名称
        $bucket = "goodshopimages";
        // 文件名称
        $object =$data['name'];


        try{
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $result=$ossClient->deleteObject($bucket, $object);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        //print(__FUNCTION__ . ": OK" . "\n");


      }





/**
 * 通用化API接口数据输出
 * @param int $status 业务状态码
 * @param string $message 信息提示
 * @param array $data 数据
 * @param int $httpCode http状态码
 * @return \think\response\Json
 */
function show($status, $message, $data = [], $httpCode = 200){
    $data = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];
    return json($data, $httpCode);
}
