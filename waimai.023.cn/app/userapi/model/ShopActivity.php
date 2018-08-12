<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 18:00
 */

namespace app\userapi\model;


use think\Db;

class ShopActivity extends Admin
{



    public function getActivityNameAttr($val,$data)
    {
        return Db::name('shop_activity_type')->where('id',$data['type'])->value('activitytype');
    }

}