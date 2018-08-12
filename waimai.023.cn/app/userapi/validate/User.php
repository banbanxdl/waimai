<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/19
 * Time: 10:07
 */

namespace app\userapi\validate;


use think\Db;
use think\Session;
use think\Validate;

class User extends Validate
{

    protected $rule = [
        'id|用户id'      =>  'require|userIdUn',
        'code|验证码'    =>  'require|codeFun',
        'opwd|旧密码'    =>  'require',
        'pwd|密码'       =>  'require',
        'pwdx|确认密码'  =>  'require|confirm:pwd',
        'phone|手机号'   =>  'require|number|regix:^((13[0-9])|(15[^4])|(166)|(17[0-8])|(18[0-9])|(19[8-9])|(147,145))\\d{8}$',
        'password|密码'  =>  'require|pwdOnly',
        'pay_pwd|支付密码' => 'require|number|length:6',
        'wx_oppenid|微信openid' => 'require',
        'nickname|昵称' => 'require',
        'head_img|头像' => 'require',
    ];

    protected $scene = [
        'GetPwd'  => ['id','code','pwdx','pwd'],
        'PostPwd' => ['code','phone'=>'require|number|phoneUn','pwd'],
        'PostAdd' => ['phone'=>'require|number|unique:user','code','pwdx'],
        'LoginPhone' => ['phone'=>'require|number|phoneUn','code'],
        'LoginPwd' => ['phone','password'],
        'AuthPay' => ['id','pay_pwd'=>'require|number|length:6|authPay'],
        'oppenidInfo' => ['phone'=>'number|regix:^((13[0-9])|(15[^4])|(166)|(17[0-8])|(18[0-9])|(19[8-9])|(147,145))\\d{8}$',
            'wx_oppenid','nickname','head_img'],
    ];

    public function codeFun($value,$rule,$data)
    {
        if (Session::get('code','userapi') == $value){
            return true;
        }else{
            return '验证码错误,请重新获取';
        }
    }

    public function userIdUn($value,$rule,$data)
    {
        $name = Db::name('user')->where('id',$value)->find();
        if (empty($name)){
            return '用户id不存在';
        }else{
            return true;
        }
    }

    public function phoneUn($value,$rule,$data)
    {
        $info = Db::name('user')->where('phone',$value)->find();
        if (empty($info)){
            return '手机号不存在';
        }else{
            return true;
        }
    }

    public function pwdOnly($value,$rule,$data)
    {
        $pwd = Db::name('user')->where('phone',$data['phone'])->value('password');
        if (encrmd($value) == $pwd){
            return true;
        }else{
            return '密码错误，请重新登录';
        }
    }

    /**
     * 验证支付密码
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    public function authPay($value,$rule,$data)
    {
        if (!empty($data['id'])){
            $pwd = Db::name('user')->where('id',$data['id'])->value('pay_pwd');
            if ($pwd == encrmd($value)){
                return true;
            }else{
                return '支付密码错误';
            }
        }elseif (!empty($data['phone'])){
            $pwd = Db::name('user')->where('phone',$data['phone'])->value('pay_pwd');
            if ($pwd == encrmd($value)){
                return true;
            }else{
                return '支付密码错误';
            }
        }else{
            return '密码格式错误';
        }

    }

}