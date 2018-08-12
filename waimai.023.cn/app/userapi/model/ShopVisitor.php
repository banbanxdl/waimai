<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 16:16
 */

namespace app\userapi\model;


use think\Loader;

class ShopVisitor extends Admin
{

    public function shopListAll($id)
    {
        $list = [];
        $ids = $this->where('uid',$id)->column('shopid');
        if (!empty($ids)){
            $list = (new Shop)->inlist($ids,true);
        }
        return $list;
    }

}