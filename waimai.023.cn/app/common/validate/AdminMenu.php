<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 16:16
 */

namespace app\common\validate;


use think\Db;
use think\Loader;
use think\Validate;

class AdminMenu extends Validate
{
    protected $rule = [
        'name|地址标识'   => 'requireWith:pid|regex:/[a-z]\/[a-z]\/[a-z]/|unname|unique:auth_rule,but_type^but_site',
        'title|标识说明' => 'require|max:20',
        'menu|菜单or按钮' => 'require',
        'but_ico|按钮图标' => 'requireIf:menu,2',
        'but_clo|按钮颜色' => 'requireIf:menu,2',
        'but_type|按钮打开方式' => 'requireIf:menu,2',
        'but_site|按钮位置' => 'requireIf:menu,2',
        'but_field|按钮字段' => 'requireIf:but_type,2|requireIf:but_type,3',
    ];
    protected $scene = [
        'add'  => ['title','name'=>'requireWith:pid|max:80|unname','menu',
            'but_ico','but_clo','but_type','but_site','but_field'],
        'edit' => ['title','name'=>'requireWith:pid|max:80|unname','menu',
            'but_ico','but_clo','but_type','but_site','but_field'],
    ];

    public function unname($value,$rule,$data)
    {
        if (empty($data['id'])){
            $name = Db::name('auth_rule')->where('name', $value)
                ->where('but_site', 'neq', $data['but_site'])
                ->where('but_type', 'neq', $data['but_type'])
                ->value('name');
            if (empty($name)) {
                return true;
            } else {
                return '标识不唯一';
            }
        }else {
            $name = Db::name('auth_rule')->where('name', $value)->where('but_site', 'neq', $data['but_site'])
                ->where('id', 'neq', $data['id'])->where('but_type', 'neq', $data['but_type'])->value('name');
            if (empty($name)) {
                return true;
            } else {
                return '标识不唯一';
            }
        }
    }

}