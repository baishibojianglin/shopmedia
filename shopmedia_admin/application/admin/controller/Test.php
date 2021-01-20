<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * Class Test
 * @package app\admin\controller
 */
class Test extends Controller
{
    /**
     * 测试
     * @return \think\response\Json
     * @throws ApiException
     */
    public function index()
    {
        try {
            $data = model('Newsq')->select();
        } catch (\Exception $e) {
            return show(0, $e->getMessage(), []);
            //throw new ApiException($e->getMessage(), 500);
        }
        return show(1, 'OK', $data);
    }
}
