<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22
 * Time: 11:45
 */

namespace app\common\lib\exception;


use think\exception\Handle;
use think\Loader;
use think\Request;

class ApiHandleException extends Handle
{
    public function render(\Exception $e)
    {
        if (config('app_debug')){
            return parent::render($e);
        }else{
            return message('', $e->getMessage(), 5);
        }
    }

}