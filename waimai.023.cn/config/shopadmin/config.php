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
    //'配置项'=>'配置值'
//    'user_auth_key' => Session::get('user_auth_key')?Session::get('user_auth_key'):0,
//    'exception_handle'       => '\think\exception\Handle',
    /**
     * 调试模式
     */
    'app_debug'    =>  true,

    // 显示错误信息
    'show_error_msg'         => true,

    //  auth 权限配置
    'auth_config'   => array(
        'auth_on'           => true,                      // 认证开关
        'auth_type'         => 1,                         // 认证方式，1为实时认证；2为登录认证。
        'auth_group'        => config('database.prefix').'auth_group',        // 用户组数据表名
        'auth_group_access' => config('database.prefix').'auth_group_access', // 用户-用户组关系表
        'auth_rule'         => config('database.prefix').'auth_rule',         // 权限规则表
        'auth_user'         => config('database.prefix').'auth_admin'             //后台管理员表
    ),

    /**
     * 模板变量
     */
    'view_replace_str'  =>array(
        '__WEB__'       =>  'http://' . $_SERVER['HTTP_HOST'],
        '__ACSS__'      =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/css',
        '__AJS__'       =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/js',
        '__AFONT__'     =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/font',
        '__AIMG__'      =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/img',
        '__LIB__'       =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/lib',
        '__STC__'       =>  '/public/static/' . request::instance()->root() . request::instance()->module() . '/static',
    ),

    /**
     * 模板设置
     */
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
        'prefix'         => 'shopadmin',
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