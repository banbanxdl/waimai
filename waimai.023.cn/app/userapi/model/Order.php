<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4
 * Time: 11:11
 */

namespace app\userapi\model;


use think\Db;

class Order extends Admin
{
    public $shop_min;
    public function getGoodsInfo()
    {
        return $this->hasMany('OrderGoods','order_id','id');
    }

    /**
     * 子查询
     * @param $sql
     * @param $data
     * @param $field
     * @param $order
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBestNumShopList($sql,$data,$field,$order)
    {
        $a = $this->alias_name;
        $order = $this->tabOrder($order);
        $list = Db::table($sql.' '.$a)->field('s.id id,s.logo logo,s.shop_name shop_name')
            ->join('__SHOP_ADDRESS__ sa','sa.shopid='.$a.'.'.$field)
            ->join('__SHOP__ s','s.id='.$a.'.'.$field)
            ->where('sa.qu',$data['qu'])
            ->where('sa.sheng',$data['sheng'])->where('sa.shi',$data['shi'])
            ->order($order)->find();
        return $list;
    }

    //计算单店铺 或多店铺 的时间
    public function shopMinute($data)
    {
        $info = Db::name('order')->field('*,(delivery_time-add_time) times')
            ->whereTime('add_time','week')
            ->where('status',6)
            ->select(function ($query)use ($data){
                if (is_numeric($data)){
                    $query->where('shop_id',$data);
                }elseif (is_array($data)){
                    $keys = implode('',array_keys($data));
                    if (is_numeric($keys)){
                        $query->whereIn('shop_id',$data);
                    }else{
                        $query;
                    }
                }
            });
        if (!empty($info)){
            foreach ($info as $value){
                $shop_min[$value['shop_id']][]   = $value['times'];
            }
            foreach ($shop_min as $k => $value){
                $shops[$k] = bcdiv((array_sum($value)/count($value)),60);
            }
        }else{
            $shops =  [];
        }

        return $shops;
    }
}