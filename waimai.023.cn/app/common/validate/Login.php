<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2
 * Time: 11:04
 */

namespace app\common\validate;


use think\Validate;
use think\Loader;

class Login extends Validate
{
    protected $rule = [
        'username|用户名'        => 'userunqie',
        'password|密码'          => 'pwdunqie',
    ];
    protected $scene = [
        'login'       => ['username','password'],
    ];

    public function userunqie($value,$rule,$data)
    {
        //验证用户名
        $username = $data['username'];
        $res = Loader::model('AuthAdmin')->tabFind('username',[$username]);
        if (empty($res)){
            return '管理员不存在';
        }else{
            return true;
        }
    }
    public function pwdunqie($value,$rule,$data)
    {
        $password = encrmd($data['password']);
        //验证吗密码
        $res = Loader::model('AuthAdmin')
            ->tabFind('username,password',[['eq',$data['username']],['eq',$password]]);
        if (empty($res)){
            return '密码错误';
        }else{
            return true;
        }
    }




}