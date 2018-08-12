<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 17:40
 */

namespace app\common\model;


use app\admin\model\AuthAdmin;

class AuthMenu extends Base
{
    public function getTypeNameAttr($val,$data)
    {
        $list = config('auth.type_name');
        return $list[$data['type_id']];
    }

    public function getUserNameAttr($val,$data)
    {
        $value = AuthAdmin::where('id','eq',$data['user_id'])->value('username');
        $list = config('auth.type_name');
        return $list[$data['type_id']];
    }

    public function setUserIdAttr($val,$data)
    {
        $tree = [1 => $val, 2 => 0, 3 => 0, 4 => 0 , 5=>0];
        return $tree[$data['type_id']];
    }

    public function setMenuAttr($val,$data)
    {
        $str = implode(',',$val);
        return $str;
    }

    public function getMenuAttr($val)
    {
        $array = explode(',',$val);
        return $array;
    }

}