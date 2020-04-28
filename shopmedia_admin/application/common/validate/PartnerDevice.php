<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/12/12
 * Time: 12:12
 */

namespace app\common\validate;

use think\Validate;

class PartnerDevice extends Validate
{
    protected $rule = [
        'user_id|用户' => 'require',
        'partner_id|广告屏合作商' => 'require',
        'device_id|广告屏' => 'require',
        'device_id' => 'unique:partner_device,partner_id^device_id', // 其中unique为验证联合唯一性
    ];

    protected $message = [
        'device_id.unique' => '广告屏合作商已生成该广告屏订单' // 合作商广告屏唯一性
    ];

    protected $scene = [

    ];
}