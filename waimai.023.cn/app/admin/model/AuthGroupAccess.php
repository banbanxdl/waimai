<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/7
 * Time: 11:49
 */

namespace app\admin\model;


use app\common\controller\AdminCommon;
use think\Db;

class AuthGroupAccess extends Admin
{

    public function infoData($id,$data)
    {
        $list = [];
        if (is_array($data)){
            foreach ($data as $value){
                $val['uid'] = (int)$id;
                $val['group_id'] = (int)$value;
                $list[] = $val;
            }
        }
        Db::name('AuthGroupAccess')->where('uid','eq',$id)->delete();
        return $list;
        //$true = serialize($info)==serialize($list) ? true : false;
    }

}