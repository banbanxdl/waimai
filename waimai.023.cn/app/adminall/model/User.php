<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 18:26
 */

namespace app\adminall\model;


use think\Db;

class User extends Admin
{
    public $_table_title = [
        'id' => 'UID',
        'nickname' => '用户名',
        'phone' => '手机号',
        'balance' => '余额',
        'is_vip' => '是否会员',
        'user_sum_num' => '总交易金额',
        'user_sum_order' => '成交订单数',
        'user_out_order' => '取消订单数',
        'action' => '操作',
    ];

    public function getUserSumNumAttr($val,$data)
    {
        $n = Db::name('order')->where('user_id',$data['id'])->where('status',6)
            ->sum('total_fee');
        return $n?:0;
    }

    public function getUserSumOrderAttr($val,$data)
    {
        $n = Db::name('order')->where('user_id',$data['id'])->where('status',6)->count();
        return $n?:0;
    }

    public function getUserOutOrderAttr($val,$data)
    {
        $n = Db::name('order')->where('user_id',$data['id'])->where('status',1)->count();
        return $n?:0;
    }

}