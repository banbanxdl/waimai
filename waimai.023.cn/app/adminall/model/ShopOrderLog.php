<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 20:45
 */

namespace app\adminall\model;


class ShopOrderLog extends Admin
{
    protected $autoWriteTimestamp = true;

    protected $createTime = 'add_time';

    protected $dateFormat = 'Ymd';

    protected $updateTime = false;

    protected $name = 'order_money_log';

    public $_table_title = [
        'add_time' => '账单日期',
        'shop_id' => '商家ID[名字]',
        'sys_money' => '平台应得',
        'money' => '支付金额',
        'atv_money' => '活动折扣',
        'shop_money' => '商家应得',
        'dis_money' => '配送费',
        'action' => '操作',
    ];

    public function getShopIdAttr($val,$data)
    {
        return $val.'['.$data['shop_name'].']';
    }

}