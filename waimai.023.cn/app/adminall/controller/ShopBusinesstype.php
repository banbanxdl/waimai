<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/26
 * Time: 10:38
 */

namespace app\adminall\controller;


use think\Request;
use think\Loader;

class ShopBusinesstype extends Admin
{
//    public function index(Request $request)
//    {
//        $_from_data = [];
//        $model = Loader::model($this->model_name);
//        //分页
//        $where = $model->laypageWhere($request);
//        $data = $model->selectList($where->index_where['field'],$where->index_where['value'],[$where->index_curr,$where->index_size]);
//        //
//        foreach ($data as $val){
//            $_from_data[] = $val->toArray();
//        }
//        $_table_title = [
//            ['id'=>'ID'],
//            ['pid'=>'上级'],
//            ['typename'=>'分类名称'],
//            ['shop_type'=>'分类'],
//            ['action'=>'操作'],
//        ];
//        /**
//         * 分页参数
//         */
//        //分页上传的地址
//        $this->assign('index_url',$_SERVER['PATH_INFO']);
//        //分页的总数
//        $this->assign('index_count',$where->index_count);
//        //分页的页码
//        $this->assign('index_curr',$where->index_curr);
//        //分页的每页数量
//        $this->assign('index_size',$where->index_size);
//        /**
//         * 分页参数
//         */
//        return Tpldemo::temp()->tableData($_from_data)
//            ->tableTitle($_table_title)->templateLists()->templateView();
//    }

    public function setStatus(Request $request, $model = '', $script = false)
    {
        if (empty($model)){
            $model=$request->controller();
        }
        return parent::setStatus($request, $model, $script); // TODO: Change the autogenerated stub
    }


}