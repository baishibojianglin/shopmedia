<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Cache;
use think\Controller;

//阿里云oss对象存储
if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
use OSS\OssClient;
use OSS\Core\OssException;


/**
 * admin模块公共控制器类
 * Class Common
 * @package app\admin\controller
 */
class Common extends Controller
{
    /**
     * headers信息
     * @var string
     */
    public $headers = '';

    /**
     * page 当前页
     * @var string
     */
    public $page = '';

    /**
     * size 每页条数
     * @var string
     */
    public $size = '';

    /**
     * 每页从第几条开始
     * @var int
     */
    public $from = 0;

    /**
     * 初始化
     */
    public function _initialize()
    {
        $this->checkRequestAuth(); // TODO：生产环境必须检查数据的合法性
    }

    /**
     * 检查每次app请求的数据是否合法
     */
    public function checkRequestAuth()
    {
        // 首先需要获取headers
        $headers = request()->header();

        // TODO：校验 headers 信息
        // 校验基础参数
        if (!$headers) {
            throw new ApiException('headers参数错误', 400);
        }
        /*if (empty($headers['sign'])) {
            throw new ApiException('sign不存在', 400);
        }*/

        // headers信息校验成功后，便以其他继承该类的子类使用headers数据
        $this->headers = $headers;
    }

    /**
     * 获取分页page、size、from
     * @param $params
     */
    public function getPageAndSize($params)
    {
        $this->page = !empty($params['page']) ? $params['page'] : 1;
        $this->size = !empty($params['size']) ? $params['size'] : config('paginate.list_rows');
        $this->from = ($this->page - 1) * $this->size; // 'limit from,size'
    }

  /**
  *上传图片
  */

    public function uploadimg()
    {
        //上传
        $data=input();
        $file = request()->file($data['name']);
        $info=$file->getInfo();
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
        return json($data);

      }



  /**
  *删除图片
  */
    public function deleteimg()
    {
        //上传
        $data=input();
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

        //返回图片地址
        //return json($result);

      }




}
