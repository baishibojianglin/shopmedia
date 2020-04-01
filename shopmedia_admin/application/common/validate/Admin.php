<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/8/2
 * Time: 12:23
 */

namespace app\common\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        //'user_name' => 'require|unique:admin,user_name^company_id|length:2,20', // 其中unique为验证联合唯一性
        'account' => 'require|unique:admin|length:2,20',
        'phone|管理员电话号码' => 'require|unique:admin|max:20'
    ];

    protected $message = [
        /*'user_name.require' => '管理员名称不能为空',
        'user_name.unique' => '管理员名称已存在', // 唯一性
        'user_name.max' => '管理员名称长度为2~20',*/
        'account.require' => '管理员账号不能为空',
        'account.unique' => '管理员账号已存在', // 唯一性
        'account.max' => '管理员账号长度为2~20'
    ];

    protected $scene = [
        // 验证update场景
        'update' => [
            //'user_name' => 'require|length:2,20',
            //'user_name.unique' => 'unique:company_user,user_name^company_id', // 验证联合唯一性，TODO：此验证该场景无效
            'account' => 'require|length:2,20',
            'phone' => 'require|max:20'
        ],
    ];
}