<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 10:29
 */

namespace app\adminall\model;


use think\Db;

class OrderComment extends Admin
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';
    protected $updateTime = false;

    public $search_field = 'id|order_id';

    public function getShopNameAttr($val,$data)
    {
        $name = Db::name('shop')->where('id',$data['sid'])->value('shop_name');
        return $name.'['.$data['sid'].']';
    }

    public function getUserNameAttr($val,$data)
    {
        $name = Db::name('user')->where('id',$data['uid'])->value('nickname');
        return $name.'['.$data['uid'].']';
    }

    public function getNumAttr($val)
    {
        return '<span style="color: #0e90d2">'.$val.'星</span>';
    }

    public function riderInfo()
    {
        return $this->hasOne('UserEvaluateRider','oid','order_id','','LEFT')
            ->where('type',1);
    }

    public function getDistributionAttr($val)
    {
        $list = [1=>'满意',2=>'不满意'];
        $value = isset($list[$val])?$list[$val]:'';
        return '<span style="color: #0e90d2">'.$value.'</span>';
    }

}