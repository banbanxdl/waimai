<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/21
 * Time: 11:32
 */
namespace app\rider\controller;
use think\Controller;
use think\Request;
use think\Loader;
use think\Db;


class RiderList extends Admin
{
    public function index(Request $request)
    {
        $model = Loader::model($this->model_name);
        //分页
        $where = $model->whereCopy('status',[['GT',1]])->laypageWhere($request);
        $data = $model->whereCopy('status',[['GT',1]])->selectList($where->index_where['field'],$where->index_where['value'],[$where->index_curr,$where->index_size]);

        //  halt($model->getlastSql());
        //
        foreach ($data as $val){
            $val['name'] = $val->name;
            $val['sex'] = $val->sex;
            $val['id_card'] = $val->id_card;
            $val['age'] = $val->age;
            $_from_data[] = $val->toArray();
        }
        $_table_title = [
            ['name'=>'姓名'],
            ['phone'=>'手机号'],
            ['id_card'=>'身份证'],
            ['create_time'=>'提交时间'],
            ['status'=>'审核结果'],
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
//        $this->assign('search_true',true);
//        $this->assign('search_field_true',true);
//        $this->assign('search_url',$where->search_url);
//        $this->assign('search_name',$where->search_name);
//        //时间搜索
//        $this->assign('search_time',true);
//        $this->assign('start_time',$where->search_start_time);
//        $this->assign('end_time',$where->search_end_time);

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
//        halt(Tpldemo::temp()->tableData($_from_data));
        return Tpldemo::temp()->tableData($_from_data)
            ->tableTitle($_table_title)->template('riderlist')->templateView();
    }

}