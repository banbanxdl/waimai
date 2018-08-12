<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 16:16
 */

namespace app\common\validate;


use think\Validate;

class AuthMenu extends Validate
{
    protected $rule = [
        'type_id'   => 'require|unique:auth_menu',
        'user_id'   => 'requireIf:type_id,1|unique:auth_menu',
        'menu'      => 'require',
    ];
    protected $message = [
        'type_id.require'     => '类型必须选择',
        'type_id.unique'      => '该类型已经添加过',
        'user_id.requireIf'   => '系统管理必须选择管理员',
        'user_id.unique'      => '管理员必须唯一',
        'menu.require'        => '必须选择菜单',
    ];
    protected $scene = [
        'add'  => ['type_id','user_id','menu'],
        'edit' => ['user_id','menu'],
    ];

}