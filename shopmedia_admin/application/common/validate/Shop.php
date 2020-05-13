<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/5/13
 * Time: 10:35
 */

namespace app\common\validate;

use think\Validate;

class Shop extends Validate
{
    protected $rule = [
        'shop_name|店铺名称' => 'require',
        'shop_name|店铺' => 'unique:shop,shop_name^shopkeeper_id', // 其中unique为验证联合唯一性
        'user_id|店家所属用户' => 'require',
        'shopkeeper_id|店家' => 'require',
        'cate|店铺类别' => 'require',
        'address' => 'require',
        'longitude' => 'require|notIn:0',
        'latitude' => 'require|notIn:0',
    ];

    protected $message = [
        //'shop_name.unique' => '店铺已存在', // 店铺名唯一性
        'address.require' => '获取位置信息失败',
        'longitude.require' => '获取经纬度位置失败',
        'longitude.notIn' => '获取经纬度位置失败',
        'latitude.require' => '获取经纬度位置失败',
        'latitude.notIn' => '获取经纬度位置失败',
    ];

    protected $scene = [

    ];
}