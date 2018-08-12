<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 18:47
 */

namespace app\adminall\model;


class ShopIncomeLog extends Admin
{
    protected $autoWriteTimestamp = true;

    protected $createTime = 'add_time';

    protected $updateTime = false;

    public $_table_title = [
        'id' => '日志ID',
        'shop_id' => '商家ID',
        'shop_phone' => '商家电话',
        'money' => '余额变动',
        'type' => '变动缘由	',
        'add_time' => '创建时间',
        'action' => '操作',
    ];

    public function getShopIdAttr($val,$data)
    {
        return $val.'['.$data['shop_name'].']';
    }

    public function getTypeAttr($val,$data)
    {
        $list = [1=>'提现',2=>'平台打款'];
        return isset($list[$val])?$list[$val]:"无效记录";
    }
}