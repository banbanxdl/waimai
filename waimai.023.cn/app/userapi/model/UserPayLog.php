<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30
 * Time: 14:24
 */

namespace app\userapi\model;


class UserPayLog extends Admin
{
    public function getTypeAttr($val)
    {
        $list = config('system.user_pay_log_type');
        return $list[$val];
    }

}