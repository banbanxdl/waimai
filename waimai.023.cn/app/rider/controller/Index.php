<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16
 * Time: 11:20
 */

namespace app\rider\controller;

use think\Session;
use think\request;

class Index extends Admin
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $cent_id = Session::get('cent_id');
        if(!empty($cent_id)){

        }
    }

    public function index()
    {
        return $this->fetch();
    }

    public function welcome()
    {
        return $this->fetch();
    }

    public function logout ()
    {
        if (Session::has('name')) {
            $list = [
                1 => ['url' => 'index/index', 'value' => []],
                2 => ['url' => 'extro_index/index', 'value' => ['name' => 'agent']],
                3 => ['url' => 'extro_index/index', 'value' => ['name' => 'city']],
                4 => ['url' => 'extro_index/index', 'value' => ['name' => 'area']],
            ];
            $id = Session::get('auth_type_id');

            //清除session 并跳转登录
            Session::clear();
            $this->redirect($list[$id]['url'], $list[$id]['value']);
        }else {
            Session::clear();
            $this->redirect('Login/index');
        }
    }
}