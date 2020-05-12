<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/12/12
 * Time: 12:12
 */

namespace app\common\validate;

use think\Validate;

class PartnerOrder extends Validate
{
    protected $rule = [
        'order_sn|订单编号' => 'require|unique:partner_order',
        'user_id|用户' => 'require',
        'partner_id|广告屏合作商' => 'require',
        'device_id|广告屏' => 'require',
        'order_price|订单价格' => 'require', // 应付款金额
        'device_id' => 'unique:partner_order,partner_id^device_id', // 其中unique为验证联合唯一性
    ];

    protected $message = [
        'order_sn.unique' => '订单已存在', // 订单唯一性
        'device_id.unique' => '广告屏合作商已生成该广告屏订单' // 合作商广告屏唯一性
    ];

    protected $scene = [

    ];
}