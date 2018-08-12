<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 11:33
 */

namespace app\admin\model;


class AuthAdmin extends Admin
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_at';
    protected $updateTime = false;

    public function setPasswordAttr($val)
    {
        if (!empty($this->id) && strlen($val) == 40) {
            return $val;
        }else{
            if (!is_string($val)) {
                return false;
            }
            $str = encrmd($val);
            return $str;
        }
    }

    public function adminList($field,$value,$page)
    {
        $where = getWhere($field,$value,'aa.');
        $list = AuthAdmin::alias('aa')->field('aa.*,aga.group_id')
            ->join('cls_auth_group_access aga','aga.uid = aa.id')->where($where)->select();
    }
}