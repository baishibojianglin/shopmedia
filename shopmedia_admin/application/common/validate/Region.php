<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/25
 * Time: 12:38
 */

namespace app\common\validate;

use think\Validate;

class Region extends Validate
{
    protected $rule = [
        'region_name|区域名称' => 'require|unique:region|max:50',
    ];

    protected $message = [
        'region_name.unique' => '区域已存在', // 唯一性
    ];

    protected $scene = [
        'update' => ['region_name' => 'require|max:20', 'region_name.unique' => 'unique:region, region_name^region_id'] // 忽略唯一(unique)类型字段region_name对自身数据的唯一性验证
    ];
}