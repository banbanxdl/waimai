<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/9
 * Time: 9:38
 */

namespace app\adminall\controller;


use think\Loader;
use think\Request;

class Error
{
    public function index(Request $request)
    {
        $admin = new Admin();
        return $admin->formList($request);
    }
}