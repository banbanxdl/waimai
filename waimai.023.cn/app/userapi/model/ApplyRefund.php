<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5
 * Time: 15:42
 */

namespace app\userapi\model;


class ApplyRefund extends Admin
{
    public function orderInfo()
    {
        return $this->hasOne('Order','id','order_id');
    }

}