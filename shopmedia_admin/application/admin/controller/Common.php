<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Cache;
use think\Controller;

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
}
