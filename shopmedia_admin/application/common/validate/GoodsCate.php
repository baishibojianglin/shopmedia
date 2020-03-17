<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/25
 * Time: 12:38
 */

namespace app\common\validate;

use think\Validate;

class GoodsCate extends Validate
{
    protected $rule = [
        'cate_name|商品类别名称' => 'require|unique:goods_cate|max:20',
    ];

    protected $message = [
        'cate_name.unique' => '商品类别已存在', // 唯一性
        'audit_status.require' => '请选择审核状态' // 审核状态
    ];

    protected $scene = [
        'update' => ['cate_name' => 'require|max:20', 'cate_name.unique' => 'unique:goods_cate, cate_name^cate_id'], // 忽略唯一(unique)类型字段cate_name对自身数据的唯一性验证
    ];
}