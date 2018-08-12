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


class Review extends Admin
{
    public function index(Request $request)
    {
        $a_url = $this->_form_button;//按钮的授权地址
        $url = $a_url[0]['url'];
        $_from_data = [];
        $model = Loader::model($this->model_name);
        //分页
        $where = $model->whereCopy('status',[['eq',1]])->laypageWhere($request);
        $data = $model->whereCopy('status',[['eq',1]])->selectList($where->index_where['field'],$where->index_where['value'],[$where->index_curr,$where->index_size]);

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
            ['id'=>'ID'],
            ['name'=>'姓名'],
            ['sex'=>'性别'],
            ['id_card'=>'身份证'],
            ['age'=>'年龄'],
            ['hold_justimg'=>'手持身份证正面照'],
            ['hold_backimg'=>'手持身份证反面照'],
            ['driver_license_img'=>'驾驶证'],
            ['vice_page_img'=>'驾驶证副页'],
            ['health_img'=>'健康证'],
            ['status'=>'操作']
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

        //审核地址
        $this->assign('reviewurl',$url);
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

    /**获取审核列表
     * @param  $status 骑手审核状态 1 提交审核 2审核通过 3审核不通过
     * @return string|\think\response\Json
     */
    public function rList(Request $request){
        $status = $request->param('status');
        if(is_numeric($status)) {
            $map = ['status' => $status];
          // $list = Loader::model('Review')->getRider($map);
           $list =  model('Review')->getRider(array(),$map);
            if($list) {
                $return = $list;
            }else{
                $return =  '查无数据';
            }
        }else{
            $return = '条件错误';
        }

        $this->assign('rlist',$return);
        return $this->fetch();
    }

    public function Reviewed(Request $request){


        $post = $request->post();
        $id = $post['data']['id'];
        $status = $post['data']['status'];
        if($id && $status){
          $r = Loader::model('Review')->setstatus($id,$status);
            if($r == 1){
                $r=[
                    'code'=>200,
                    'msg'=>'审核通过'
                ];
            }else{
                $r=[
                    'code'=>-1,
                    'msg'=>'审核失败'
                ];
            }
            return $r;
        }
    }

    /**
     * @param $rider_id:id号
     * @param  $imgname: 图片字段名 hold_justimg 手持身份证正面照 hold_backimg 手持身份证反面照 driver_license_img 驾驶证 vice_page_img 驾驶证副页 health_img 健康证
     * @return string|\think\response\Json
     */
    public function rImg(Request $request){
        $rider_id = $request->param('rider_id');
        $imgname = $request->param('imgname');
        if(is_numeric($rider_id) && $imgname!=''){
            $map = ['rider_id'=>$rider_id];
            $img = Loader::model('Review')->getRider($imgname,$map);
           return json($img);
        }else{
            return '没有图片';
        }
    }
}