<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/21
 * Time: 9:49
 */

namespace app\rider\model;
use think\Db;
use think\Loader;

class Rider extends  Admin
{
    public function insertrider($data,$rider){

        //操作数据库
        $rid =  Db::name('rider')->insert($rider);
        $data['rider_id'] =  Db::name('rider')->getLastInsID();
        $r_info = Db::name('rider_info')->insert($data);
        return $r_info;
    }

}