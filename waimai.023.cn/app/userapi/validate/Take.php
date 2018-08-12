<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/11
 * Time: 9:31
 */

namespace app\userapi\validate;


use think\Db;
use think\Validate;

class Take extends Validate
{
    protected $rule = [
        'uid|用户id' => 'require|number',
        'money|提现金额' => 'require|number|userUn',
        'bank_id|银行卡id' => 'require|number',
    ];

    protected $scene = [
        'add' => ['uid','money','bank_id']
    ];

    public function userUn($value,$rule,$data)
    {
        $money = Db::name('user')->where('id',$data['uid'])->value('balance');
        if (bccomp($value,$money,2) == 1){
            return true;
        }else{
            return '您的余额不足';
        }
    }

}