<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 16:52
 */

namespace app\userapi\validate;


use think\Validate;

class OrderInfo extends Validate
{
    protected $rule = [
        'uid|用户id' => 'require|number',
        'sid|用户id' => 'require|number',
        'tid|购物车id' => 'array',
        'lt|经度' => 'require|float',
        'wt|维度' => 'require|float',
    ];

    protected $scene = [
        'create_order' => ['uid','sid','tid','lt','wt'],
    ];

}