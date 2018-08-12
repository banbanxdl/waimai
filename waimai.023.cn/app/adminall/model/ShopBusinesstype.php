<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/26
 * Time: 10:40
 */

namespace app\adminall\model;


use think\Db;

class ShopBusinesstype extends Admin
{
    public $search_field = 'id|typename';

    public $_table_title = [
        'id'=>'ID',
        'pid'=>'上级',
        'typename'=>'分类名称',
        'shop_type'=>'分类',
        'action'=>'操作',
    ];

    /**
     * 下拉菜单
     * @param $n
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function fromTypeSelectValues($n,$data)
    {
        $typelist = Db::name('shop_type')->field('id,typename title')->select();
        $list['shop_type'] = $n==1?$typelist:$data->get('shop_type');
        return $list;
    }

    public function getPidAttr($val,$data)
    {
        $name = $this->where('id',$val)->value('typename');
        if (empty($name)){
            return '顶级分类';
        }
        return $this->where('id',$val)->value('typename');
    }

}