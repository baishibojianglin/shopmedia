<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/25
 * Time: 12:38
 */

namespace app\admin\validate;

use think\Validate;

class GoodsBrand extends Validate
{
    protected $rule = [
        'brand_name|商品品牌名称' => 'require|unique:goods_brand|max:20',
    ];

    protected $message = [
        'brand_name.unique' => '商品品牌已存在', // 唯一性
        'audit_status.require' => '请选择审核状态' // 审核状态
    ];

    protected $scene = [
        'update' => ['brand_name' => 'require|max:20', 'brand_name.unique' => 'unique:goods_brand, brand_name^brand_id'], // 忽略唯一(unique)类型字段brand_name对自身数据的唯一性验证
    ];
}