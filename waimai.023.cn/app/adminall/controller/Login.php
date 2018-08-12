<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16
 * Time: 11:23
 */

namespace app\adminall\controller;


use think\Controller;
use think\captcha\Captcha;
use think\Request;
use think\Session;
use think\Loader;

class Login extends Controller
{
    protected $model_name = 'common/AuthAdmin';
    protected $login_name = 'common/Login';

    public function index()
    {
        return $this->fetch('sign_in');
    }

    public function login (Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            $vali = Loader::validate($this->model_name);
            //验证username  password  code
            if (!$vali->scene('login')->check($data)){
                return $this->result('',config('code.no'),$vali->getError());
            }
            //验证 验证码是否正确
            if (!captcha_check($data['code'])){
                return $this->result('',config('code.no'),'验证码不正确');
            }
            //验证用户是否存在
            $user = Loader::validate($this->login_name);
            if (!$user->scene('login')->check($data)){
                return $this->result('',config('code.no'),$user->getError());
            }
            $password = encrmd($data['password']);
            //验证吗密码
            $info = Loader::model('AuthAdmin')->tabFind('username,password',[['eq',$data['username']],['eq',$password]]);
            //储存到session中
            Session::set('id',$info['id']);
            Session::set('auth_type_id',1);
            Session::set('name',$info['username']);
            Session::set('password',$info['password']);
            return $this->result(url('Index/index'),config('code.yes'),'登录成功');
        }else{
            $this->error('非法请求');
        }
    }

    /**
     * 验证码输出
     */
    public function verify()
    {
        $config =    [
            //高度
            'imageH'      => 50,
            //宽度
            'imageW'      => 200,
            // 验证码字体大小
            'fontSize'    =>    24,
            // 验证码位数
            'length'      =>    5,
            // 关闭验证码杂点
            'useNoise'    =>    true,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串
    function check_verify($code, $id = '')
    {
        $verify = new Captcha();
        return $verify->check($code, $id);
    }

}