<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/1
 * Time: 14:44
 */

namespace app\common\validate;


use think\Validate;

class AuthRule extends Validate
{
    protected $rule = [
        'name|标识符'         => 'require|max:80|unique:auth_rule',
        'title|规则名称'        => 'require|max:20',
        'condition|条件'    => 'max:100',
    ];
    protected $scene = [
        'add'      => ['name','title','condition'],
        'edit'     => ['name','title','condition'],
    ];

}