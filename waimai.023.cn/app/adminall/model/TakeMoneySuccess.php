<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/7
 * Time: 10:52
 */

namespace app\adminall\model;


class TakeMoneySuccess extends TakeMoneyAll
{
    public $_table_title = [
        'id' => '提现编号',
        'uid' => '提现人id【名称】',
        'identity' => '提现人id身份',
        'bank_account' => '提现账户',
        'money' => '提现金额',
        'status' => '审核状态	',
    ];

    public function whereCopy($field = '', $value = [])
    {
        $field = 'status';
        $value = [4];
        return parent::whereCopy($field, $value); // TODO: Change the autogenerated stub
    }

}