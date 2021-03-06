<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 15:52
 */

namespace app\admin\controller;

use app\common\model\AuthRule as AuthRuleModel;
use think\Db;
use think\Request;
use think\Loader;

class AuthRule extends Admin
{
    protected $model_name = 'AuthRule';

    public function index(Request $request)
    {
        $model = Loader::model($this->model_name);
        //分页
        $where = $model->whereCopy('menu',[['eq',0]])->laypageWhere($request);
        $index_url = url('index');
        $data = $model->whereCopy('menu',[['eq',0]])->selectList($where->index_where['field'],$where->index_where['value'],[$where->index_curr,$where->index_size]);
        //
        foreach ($data as $val){
            $_user_list[] = $val->toArray();
        }
        $_from_data = $_user_list;
        $_table_title = [
            ['id'=>'编号'],
            ['name'=>'规则表示符'],
            ['title'=>'规则中文名'],
            ['type'=>'类型'],
            ['status'=>'状态'],
            ['action'=>'操作'],
        ];
        $replenish = 'status,edit,del';
        $argument = [url('AuthRule/setStatus'),url('AuthRule/save'),url('AuthRule/setStatus')];
        $_from_action = url('AuthRule/save');
        $_from_button = [
            ['menu_title' => '添加权限', 'from_url' => $_from_action, 'ico' => '&#xe600;']
        ];
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
//        Tpldemo::temp()->_template_title = '权限管理';
        Tpldemo::temp()->_from_button = $_from_button;
        return Tpldemo::temp()->tableData($_from_data,'','')
            ->tableTitle($_table_title)->templateView();
    }

    public function save(Request $request)
    {
        $model = Loader::model($this->model_name);
        if ($request->isPost()){
            $id = $request->post('id');
            $data = $request->post();
            $vali = Loader::validate('common/AuthRule');
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
            $res = $model->tabAdd($data,$id);
            if (!empty($res)){
                return $this->result($res,config('code.yes'),$con.'成功');
            }else{
                return $this->result($res,config('code.no'),$con.'失败');
            }
        }else {
            $id = $request->get('id');
            if (!empty($id)){
                $info = $model::get($id);
                $list = $info->getData();
                $this->assign('data',$list);
            }
            Tpldemo::temp()->_from_action = url('AuthRule/save');
            Tpldemo::temp()->_template_title = '规则操作';
            Tpldemo::temp()
                ->tableData('规则唯一标识','name','规则标识必须是字母和数字，下划线_及破折号-',1)
                ->tableData('规则中文名称','title','规则名称必须是中文',1)
                ->tableData('规则表达式','condition','为空表示存在就验证，条件为正则表达式',0);
            return Tpldemo::temp()->templateFrom()->templateView();
        }
    }

    public function setStatus(Request $request, $model, $script = false)
    {
        $model = 'AuthRule';
        return parent::setStatus($request, $model, $script); // TODO: Change the autogenerated stub
    }

}