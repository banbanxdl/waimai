<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 20:25
 */

namespace app\adminall\model;


use think\Db;

class TakeMoneyAll extends Admin
{
    protected $name = 'put_forward';

    protected $createTime = 'add_time';

    protected $updateTime = false;

    public $_table_title = [
        'id' => '提现编号',
        'uid' => '提现人id【名称】',
        'identity' => '提现人id身份',
        'bank_account' => '提现账户',
        'money' => '提现金额',
        'add_time' => '审核时间',
        'status' => '审核状态	',
        'action' => '操作',
    ];

    public function getUidAttr($val,$data)
    {
        return $val.'['.($data['name']?:'').']';
    }

    public function getBankAccountAttr($val,$data)
    {
        $f = Db::name('bank_card')->where('id',$data['bank_id'])
            ->where('identity',$data['identity'])->find();
        $s = $f?'开户行：'.$f['opening_bank'].'/ 账户：'.$f['card'].'/开户人：'.$f['name']:'该用户没有银行卡';
        return $s;
    }

    public function getStatusAttr($val,$data)
    {
        $list = [1=>'审核中',2=>'审核成功',3=>'审核失败',4=>'转账成功',5=>'转账失败'];
        return isset($list[$val])?$list[$val]:'无效申请';
    }

    public function getIdentityAttr($val,$data)
    {
        $list = [1=>'用户',2=>'骑手',3=>'商家'];
        return isset($list[$val])?$list[$val]:'无效身份';
    }



}