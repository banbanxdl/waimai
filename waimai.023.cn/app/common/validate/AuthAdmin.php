<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/1
 * Time: 14:44
 */

namespace app\common\validate;


use think\Validate;

class AuthAdmin extends Validate
{
    protected $rule = [
        'username'          => 'require|alphaDash|max:50',
        'password'          => 'require|alphaDash|max:50',
        'text'              => 'max:100',
        'code'              => 'require|length:5'
    ];
    protected $message = [
        'username.require'  => '管理员账号不能为空',
        'username.alphaDash'=> '管理员账号必须是字母和数字，下划线_及破折号-',
        'username.max'      => '管理员账号长度不能超过50',
        'password.require'  => '管理员密码不能为空',
        'password.alphaDash'=> '管理员密码必须是字母和数字，下划线_及破折号-',
        'password.max'      => '管理员密码长度不能超过50',
        'text.max'          => '管理员简介内容长度不能超过100',
        'code.require'      => '验证码不能为空',
        'code.length'       => '验证码长度不对'
    ];
    protected $scene = [
        'add'               => ['username','password','text'],
        'edit'              => ['username','password','text'],
        'login'              => ['username','password','code'],
    ];

}