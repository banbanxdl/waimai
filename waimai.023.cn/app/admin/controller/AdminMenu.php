<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 15:55
 */

namespace app\admin\controller;


use app\common\controller\ApiCommon;
use think\Request;
use think\Loader;
use think\Db;

class AdminMenu extends Admin
{
    public function index(Request $request)
    {
        $_model_list = [];
        $model = Loader::model($this->model_name);
        //分页
        $where = $model->laypageWhere($request);
        $index_url = url($request->action());
        $data = $model->whereCopy('menu',[['in',[1,2,3]]])
            ->selectList($where->index_where['field'],$where->index_where['value'],[$where->index_curr,$where->index_size]);
        //
        foreach ($data as $val){
            $_model_list[] = $val->toArray();
        }
        $_table_title = [
            ['id'=>'编号'],
            ['pid'=>'上级'],
            ['name'=>'地址标识'],
            ['title'=>'标题'],
            ['menu'=>'类型'],
            ['action'=>'操作']
        ];

        /**
         * 搜索参数
         */
        //搜索参数
        $this->assign('search_true',true);
        $this->assign('search_url',$where->search_url);
        $this->assign('search_name',$where->search_name);
        /**
         * 分页参数
         */
        //分页上传的地址
        $this->assign('index_url',$index_url);
        //分页的总数
        $this->assign('index_count',$where->index_count);
        //分页的页码
        $this->assign('index_curr',$where->index_curr);
        //分页的每页数量
        $this->assign('index_size',$where->index_size);
        /**
         * 分页参数
         */

        Tpldemo::temp()->templateLists();
        return Tpldemo::temp()->tableData($_model_list,'','')
            ->tableTitle($_table_title)->templateView();
    }

    public function save(Request $request)
    {
        $model = Loader::model($this->model_name);
        if ($request->isAjax()){
            $id = $request->post('id');
            $data = $request->post();
            $this->tabSave($id,$data);
        }else{
            $id = $request->param('id');
            if (empty($id)){
                $list['menu'] = 1;
            }else{
                $info = $model::get($id);
                $list = $info->toArray();
                $list['menu'] = $info->getData('menu');
            }
            $this->assign('data',$list);
            /**
             * 下拉菜单
             */
            $sid = $id?$id:0;
            $menu = Db::name('AuthRule')->field('id,pid,title')->where('menu','IN','1,3')
                ->where('id','NEQ',$sid)->select();
            $menu_list = $this->arrayTree($menu);
            $select['pid'] = $menu_list;
            $select['menu'] = config('auth.menu_type_list');
            $select['but_type'] = config('auth.but_type_list');
            $select['but_site'] = config('auth.but_site_list');
            $this->assign('select', $select);

            $radio['but_ico'] = config('auth.ico_list');
            $this->assign('radio',$radio);

            //模板
            Tpldemo::temp()->_from_action = $this->_from_action;
            Tpldemo::temp()
                ->tableData('菜单上级','pid','选择菜单上级',0,'select')
                ->tableData('标识说明','title','请填写标识说明，菜单列表上的显示',1)
                ->tableData('地址标识','name','标识唯一，链接地址必须是(模块/控制器/方法名),包括大小写必须一致',1)
                ->tableData('菜单or按钮','menu','请选择菜单or按钮',1,'select')
                ->tableData('按钮图标','but_ico','请选择按钮图标',0,'radio')
                ->tableData('按钮颜色','but_clo','请选择按钮颜色',0,'inputcolour')
                ->tableData('按钮打开方式','but_type','请选择按钮打开方式',0,'select')
                ->tableData('按钮位置','but_site','请选择按钮位置',0,'select')
                ->tableData('按钮字段','but_field','字段名称必须与数据库的字段一致',0)
                ->tableData('排序','sort','请填写大于0的数字',0);
            return Tpldemo::temp()->templateFrom()->templateView();
        }
    }

    public function button(Request $request)
    {
        $model = Loader::model($this->model_name);
        if ($request->isAjax()){
            $id = $request->post('id');
            $data = $request->post();
            $vali = Loader::validate($this->model_name);
            $info = Db::name('AdminMenu')->where('id',$data['pid'])->find();
            if ($info['pid'] === 0){
                return $this->result('',config('code.no'),'顶级菜单不符合添加按钮条件');
            }
            if($info['is_butt'] == 1){
                return $this->result('',config('code.no'),'按钮不能添加按钮');
            }
            if (empty($id)){//新增
                $con = '新增';
                if (!$vali->scene('add')->check($data)){
                    return $this->result('',config('code.no'),$vali->getError());
                }
            }else{//修改
                $con = '修改';
                if (!$vali->scene('edit')->check($data)){
                    return $this->result('',config('code.no'),$vali->getError());
                }
            }
            $data['is_butt'] = 1;
            $res = $model->tabAdd($data,$id);
            if (!empty($res)){
                return $this->result($res,config('code.yes'),$con.'成功');
            }else{
                return $this->result($res,config('code.no'),$con.'失败');
            }
        }else{
            $id = $request->get('id');
            $list['pid'] = $id;
            $this->assign('data',$list);

            //模板
            Tpldemo::temp()->_from_action = url('button');
            Tpldemo::temp()
                ->tableData('','pid','',0,'hidden')
                ->tableData('按钮名称','title','请填写按钮名称',1)
                ->tableData('按钮链接地址','name','请填写正确的按钮链接地址',1);
            return Tpldemo::temp()->templateFrom()->templateView();
        }
    }

    public function setStatus(Request $request, $model = '', $script = false)
    {
        if (empty($model)) {
            $model = 'auth_rule';
        }
        return parent::setStatus($request, $model, $script); // TODO: Change the autogenerated stub
    }

}