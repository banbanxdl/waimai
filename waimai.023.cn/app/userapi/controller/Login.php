<?php
/**
 * 登录
 * Created by PhpStorm.
 * User: Calos
 * Date: 2018/6/19
 * Time: 15:54
 */

namespace app\userapi\controller;


use think\Loader;
use think\Request;

class Login extends Api
{
    public function login(Request $request)
    {
        $vali = Loader::validate('User');
        $model = Loader::model('User');
        if ($request->isGet()){
            $data['phone'] = $request->get('phone');
            $data['code'] = $request->get('code');
            if (!$vali->scene('LoginPhone')->check($data)){
                return message('',$vali->getError(),1);
            }
            return message('','登录成功',2);
        }else{
            $data['phone'] = $request->post('phone');
            $data['password'] = $request->post('password');
            if (!$vali->scene('LoginPwd')->check($data)){
                return message('',$vali->getError(),1);
            }
            return message('','登录成功',2);
        }
    }

}