<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/24
 * Time: 14:55
 */

namespace app\userapi\validate;


use think\Validate;

class ShopCart extends Validate
{
    protected $rule = [
        'uid|用户id' => 'require|number',
        'shop_id|店铺id' => 'number'
    ];

    protected $scene = [
        'list' => ['uid','shop_id'],
    ];

}