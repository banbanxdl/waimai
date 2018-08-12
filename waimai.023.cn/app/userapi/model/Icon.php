<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/24
 * Time: 9:27
 */

namespace app\userapi\model;


class Icon extends Admin
{
    public function getIconUrlAttr($val,$data)
    {
        $list = config('system.first_icon_url');
        return $list[$data['tid']];
    }

}