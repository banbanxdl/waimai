<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 17:09
 */

namespace app\adminall\controller;


use think\Lang;
use think\Request;
use think\Loader;

class Shop extends Admin
{
    public function index(Request $request)
    {
        $_from_data = [];
        $model = Loader::model($this->model_name);
        //分页
        $where = $model->laypageWhere($request);
        $data = $model->selectList($where->index_where['field'],$where->index_where['value'],[$where->index_curr,$where->index_size]);
        //
        foreach ($data as $val){
            $val['web_link'] = $val->web_link;
            $val['admin_link'] = $val->admin_link;
            $val['shop_number'] = $val->shop_number;
            $val['shop_address'] = $val->address?$val->address->shop_address:'';
            $_from_data[] = $val->toArray();
        }
        $_table_title = [
            'id'=>'ID',
            'shop_name'=>'店铺名称',
            'shop_type'=>'店铺分类',
            'contacts_phone'=>'联系电话',
            'shop_address'=>'店铺地址',
            'shop_status'=>'营业状态',
            'web_link'=>'店铺链接',
            'admin_link'=>'后台链接',
            'shop_number'=>'商品数量',
            'action'=>'操作',
        ];

        /**
         * 搜索参数
         */

        //下拉菜单参数
//        $select['ad'] = config('adv.sign');
//        $select_name['ad'] = $request->get('ad');
//        $select['city_id'] = $model->cityList($model->column('city_id'));
//        $select_name['city_id'] = $request->get('city_id');
//        $this->assign('select_name',$select_name);
//        $this->assign('type_select',$select);
        //搜索参数
        $this->assign('search_true',true);
        $this->assign('search_field_true',true);
        $this->assign('search_url',$where->search_url);
        $this->assign('search_name',$where->search_name);
        //时间搜索
        $this->assign('search_time',true);
        $this->assign('start_time',$where->search_start_time);
        $this->assign('end_time',$where->search_end_time);


        /**
         * 分页参数
         */
        //分页上传的地址
        $this->assign('index_url',$request->pathinfo());
        //分页的总数
        $this->assign('index_count',$where->index_count);
        //分页的页码
        $this->assign('index_curr',$where->index_curr);
        //分页的每页数量
        $this->assign('index_size',$where->index_size);
        /**
         * 分页参数
         */
        return Tpldemo::temp()->tableData($_from_data)
            ->tableTitle($_table_title)->templateLists()->templateView();
    }

    public function setStatus(Request $request, $model = '', $script = false)
    {
        if (empty($model)){
            $model=$request->controller();
        }
        return parent::setStatus($request, $model, $script); // TODO: Change the autogenerated stub
    }


}