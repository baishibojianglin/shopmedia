<?php

namespace app\api\controller;

use think\Cache;
use think\Controller;

/**
 * api模块公共控制器类
 * Class Common
 * @package app\api\controller
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
        $headers = request()->header(); //halt($headers);

        /* TODO
        sign 加密：客户端工程师，解密：服务端工程师
        1.headers、body仿照sign形式做参数的加解密：如对 headers 的 version、apptype、did、model 加密放在一个参数 headers_params 里，对 headers_params 解密时则生成字符串 'version=1&apptype=android&did=12345dg&model=mix2s'
        2.IAuth()->setSign()的算法步骤需要客户端与服务端工程师约定，但最终算法是AES */

        $this->headers = $headers; // headers信息校验成功后，便以其他继承该类的子类使用headers数据
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
}
