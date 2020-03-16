<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/28
 * Time: 0:49
 */

namespace app\common\validate;

use think\Validate;

class Partner extends Validate
{
    protected $rule = [
        'partner_name' => 'require|max:20',
        'password' => 'require|max:20',
        'phone' => 'require|unique:partner',
        'email' => 'email',
    ];

    protected $message = [
        'partner_name.require' => '用户名称不能为空',
        'partner_name.max' => '用户名称长度不能超过20',
        'password.require' => '密码不能为空',
        'password.max' => '密码长度不能超过20',
        'phone.require' => '手机号码不能为空',
        'phone.unique' => '手机号码已存在', // 唯一性
        'email' => '邮箱格式错误',
    ];

    protected $scene = [
        'login' => ['phone' => 'require', 'phone.unique' => 'unique:partner, phone^partner_id'], // 忽略唯一(unique)类型字段phone对自身数据的唯一性验证
        'update' => ['partner_name', 'password', 'phone' => 'require', 'phone.unique' => 'unique:partner, phone^partner_id', 'email',],
    ];
}