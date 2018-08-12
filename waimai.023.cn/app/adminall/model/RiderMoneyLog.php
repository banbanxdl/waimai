<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 15:58
 */

namespace app\adminall\model;


class RiderMoneyLog extends Admin
{
    protected $name = 'rider_detailed_log';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'add_time';

    protected $updateTime = false;

    protected $dateFormat = 'Ymd';

    public $_table_title = [
        'add_time'=>'账单日期',
        'rider_id'=>'配送员ID[名字]',
        'money'=>'配送费',
        'sys_reward'=>'平台奖励',
        'sys_cut'=>'平台抽成	',
        'rider_get'=>'配送员应得',
        'action'=>'操作',
    ];

    public function getRiderIdAttr($val,$data)
    {
        return $val.'['.$data['rider_name'].']';
    }

}