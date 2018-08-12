<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 16:49
 */

namespace app\adminall\model;


use think\Db;

class UserAddress extends Admin
{
    protected $name = 'address';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'time';

    protected $updateTime = false;

    public $_table_title = [
        'id' => 'ID',
        'user_id' => 'UID[用户名]',
        'name' => '收货姓名',
        'phone' => '收货电话',
        'address' => '地址信息',
        'label' => '标签',
        'isdefault' => '是否为默认地址',
        'time' => '创建时间',
        'action' => '操作',
    ];

    public function getUserIdAttr($val,$data)
    {
        $n = Db::name('user')->where('id',$data['user_id'])->value('nickname');
        return $data['user_id'].'['.($n?:"").']';
    }

    public function getNameAttr($val,$data)
    {
        return $val.' '.$data['sex'];
    }

    public function getLabelAttr($val)
    {
        $list = [1=>'家',2=>'公司',3=>'学校'];
        return isset($list[$val])?$list[$val]:'';
    }

    public function getIsdefaultAttr($val)
    {
        $list = [0=>'否',1=>'是'];
        return isset($list[$val])?$list[$val]:'否';
    }
}