<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 11:33
 */

namespace app\common\model;


class AuthGroup extends Base
{

    public function getRulesAttr($val)
    {
        $value = explode(',',$val);
        return $value;
    }

    public function setRulesAttr($val)
    {
        if (is_array($val)){
            $value = implode(',',$val);
        }else{
            $value = $val;
        }
        return $value;
    }

}