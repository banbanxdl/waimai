<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/10
 * Time: 19:27
 */

namespace app\userapi\validate;


use think\Validate;

class Bank extends Validate
{
    protected $rule = [
        'uid|会员id' => 'require|number',
        'account_type|账号类型' => 'require|number',
        'name|持卡人名字' => 'require|chs',
        'card|卡号' => 'require|number',
        'city|开户地址' => 'require|max:200',
        'opening_bank|银行编码' => 'require|number',
    ];

    protected $scene = [
        'add' => ['uid','account_type','name','card','city','opening_bank'],
    ];

}