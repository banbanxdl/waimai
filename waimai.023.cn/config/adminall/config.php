<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16
 * Time: 17:42
 */

use think\request;
use think\Session;
return [
    //调试模式
    'app_debug'    =>  true,

    // 显示错误信息
    'show_error_msg'         => false,

    //模板变量
    'view_replace_str'  =>array(
        '__WEB__'       =>  'http://' . $_SERVER['HTTP_HOST'],
        '__ACSS__'      =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/css',
        '__AJS__'       =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/js',
        '__AFONT__'     =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/font',
        '__AIMG__'      =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/img',
        '__LIB__'       =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/lib',
        '__STC__'       =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/static',
    ),

    //模板设置
    'template'  => [
        'layout_on' => true,
        'layout_name'  => 'layout/layout'
    ],


    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'adminall',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        //密码
        'password'       => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
        'httponly'       => true,
        'secure'         => false,
    ],

];