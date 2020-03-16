<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/22
 * Time: 17:52
 */

// 状态码配置
return [
    // API接口状态码
    'error' => 0,
    'success' => 1,

    // 账户状态
    'status' => [
        0 => '禁用',
        1 => '启用',
        2 => '待审核',
    ],

    'status_disable' => 0, // 关闭/停用
    'status_enable' => 1, // 开启/启用
    'status_pending' => 2, // 待审核

    // 是否删除
    'not_delete' => 0, // 未删除
    'is_delete' => 1, // 删除

    // 性别：0保密，1男，2女
    'gender' => [
        0 => '保密',
        1 => '男',
        2 => '女',
    ],

    // 审核状态：0待审核，1通过，2驳回
    'audit_status' => [
        0 => '待审核',
        1 => '正常',
        2 => '驳回',
    ],

    // 是否上架：0下架，1上架
    'is_on_sale' => [
        0 => '下架',
        1 => '上架'
    ]
];