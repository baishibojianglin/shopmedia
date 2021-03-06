<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/6/23
 * Time: 12:00
 */

// 广告、广告设备配置
return [
    /*广告配置 s*/
    // 广告类型
    'ad_type' => [1 => '图片', 2 => '视频'],

    // 广告所属行业类别
    'ad_cate' => [
        1 => '餐饮',
        2 => '景区/城乡宣传',
        3 => '服装',
        4 => '粮油/农(副)产品',
        5 => '生鲜',
        6 => '通信',
        7 => '美容/美发/文身',
        8 => '零食/休闲食品',
        9 => '商场/超市',
        10 => '钟表/眼镜零售',
        11 => '五金',
        12 => '家纺',
        13 => '艺术/文化传播',
        14 => '医疗保健',
        15 => '母婴用品',
        16 => '娱乐',
        17 => '鲜花零售业',
        18 => '建筑装饰装修',
        19 => '法拍房/房地产',
        20 => '茶楼',
        21 => '宠物',
        22 => '金银珠宝',
        23 => '宣传片', // TODO：待替换成其他
        24 => '教育',
        25 => '企业服务/财税',
        26 => '交通运输',
        27 => '机动车维护保养/机动车燃料零售',
    ],
    /*广告配置 s*/


    /*广告设备配置 s*/
    // 广告设备类别
    'ad_device_cate' => [1 => '广告屏', 2 => '广告框'],

    // 广告设备品牌
    'device_brand' => [
        0 => '其他',
        1 => '铂臣'
    ],

    // 广告屏设备型号
    'device_model' => [
        0 => [1 => '其他'],
        1 => [1 => 'h-1', 2 => 'h-2'],
        2 => [1 => 'c-1', 2 => 'c-2']
    ],

    // 广告设备尺寸
    'device_size' => [
        // 广告屏
        1 => 22,
        2 => 43,
        // 广告框
        3 => 24
    ],

    // 广告屏等级（用于计算广告单价）
    'device_level' => [
        1 => 1,
        2 => 0.8,
        3 => 0.6
    ],
    /*广告设备配置 e*/
];