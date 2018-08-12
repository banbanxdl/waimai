<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 17:11
 */

namespace app\adminall\model;


class AdminMoneyLog extends Admin
{
    protected $name = 'system_make_money_log';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'add_time';

    protected $updateTime = false;

    public $_table_title = [
        'add_time'=> '账单日期',
        'user_name'=> '用户ID[名字]',
        'admin_name'=> '操作员',
        'type'=> '类型',
        'money'=> '金额',
    ];

    public function getUserNameAttr($val,$data)
    {
        return $data['user_id'].'['.$val.']';
    }

    public function getTypeAttr($val,$data)
    {
        $list = [1=>'充值',2=>'扣除'];
        return isset($list[$val])?$list[$val]:'无效类型';
    }

}