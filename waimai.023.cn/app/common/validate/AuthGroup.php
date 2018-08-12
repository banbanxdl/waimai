<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/1
 * Time: 14:44
 */

namespace app\common\validate;


use think\Db;
use think\Validate;

class AuthGroup extends Validate
{
    protected $rule = [
        'title|用户组中文名称'=>'require',
        'auth_type_id|用户组类型'=>'require|untype',
    ];
    protected $scene = [
        'add'  => ['title','auth_type_id'],
        'edit' => ['title','auth_type_id'],
        'rightadd' => ['rules'=>'require'],
        'rightedit' => ['rules'=>'require'],
    ];

    public function untype($value,$rule,$data)
    {
        if ($data['auth_type_id'] == 1) {
            return true;
        }else{
            $info = Db::name('auth_group')->where('auth_type_id',$value)->find();
            if (empty($info)){
                return true;
            }else{
                return false;
            }
        }
    }

}