<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/7
 * Time: 20:46
 */

namespace app\shopadmin\controller;
use think\Request;
use think\Session;
use think\Db;

class Activity extends Admin
{
    /**
     * 活动显示页面
     */
    public function getActivity()
    {
        return $this->fetch('Marketing');
    }

    /**
     * 查询活动
     */
    public function seActivity()
    {
        $activity=Db::name("shop_activity_type")->select();
        halt($activity);
    }

    /**
     * 满减活动显示页面
     */
    public function FullSubtractActivity()
    {
        return $this->fetch('Full_minus');
    }
    /**
     * 减配送费活动显示页面
     */
    public function DistributActivity()
    {
        return $this->fetch('Minus_dispatch');
    }

}