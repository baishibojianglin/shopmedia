<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/5/16
 * Time: 19:38
 */

// 业务员提成配置
return [
    'partner_salesman_commission' => 0.1, // 广告屏合作商业务员提成：广告屏价格的10%
    'shopkeeper_salesman_commission' => 0, // 开拓店铺业务员提成金额（元）(线下结算，后台不审核)

    /*投放【广告】审核通过涉及到的提成与收益 s*/
    'ad_partner_commission' => 0.16, // 广告屏合作商：广告费的（按广告屏出资比例，且细分到每个广告屏对应不同的合作商）：≤5000为16%，＞5000为46%
    'ad_partner_salesman_commission' => 0, // 广告屏合作商业务员：广告费的0%
    'ad_partner_salesman_parent_commission' => 0, // 广告屏合作商业务员上级：广告费的0%
    'ad_shopkeeper_commission' => 0.02, // 店家：广告费的：≤5000为2%，＞5000为2%
    'ad_shop_salesman_commission' => 0, // 店铺业务员：广告费的0%
    'ad_advertiser_salesman_commission' => 0, // 广告主业务员：总广告费的0%(线下结算)
    /*投放【广告】审核通过涉及到的提成与收益 e*/
];