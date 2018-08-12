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
        $controller = Loader::controller('admin');
        return $controller->formList($request,$request->controller());
    }
}