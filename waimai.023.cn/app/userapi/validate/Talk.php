<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5
 * Time: 14:05
 */

namespace app\userapi\validate;


use think\Validate;

class Talk extends Validate
{
    protected $rule = [
        'order_id|订单id' => 'require|number|>:0',
        'user_id|用户id'  => 'require|number|>:0',
        'shop_id|店铺id'  => 'require|number|>:0',
        'num|星星'        => 'require|number|between:1,5',
        'content|评价内容' => 'require|max:200',
        'img_list|图片'   => 'require|array',
        'right|是否满意'   => 'require|in:1,2',
        'why|标签'        => 'require|array',
    ];

    protected $scene = [
        'add'=>['order_id','user_id','shop_id','num','content','img_list','right','why'],
    ];

}