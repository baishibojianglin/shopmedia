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

/**
 * 生成唯一随机数
 * @param $min
 * @param $max
 * @param array|int $number 用于比较的值
 * @return int
 */
function uniqueRand($min, $max, $number){
    // 生成随机数
    $rand = mt_rand($min, $max);

    // 判断生成的随机数是否唯一，重复时重新生成
    if (is_array($number)) {
        $flag = in_array($rand, $number) ? false : true;
    } else {
        $flag = $rand == $number ? false : true;
    }
    if ($flag == false) {
        uniqueRand($min, $max, $number);
    } else {
        return $rand;
    }
}