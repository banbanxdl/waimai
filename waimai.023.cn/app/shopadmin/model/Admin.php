<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 17:13
 */

namespace app\shopadmin\model;


use app\common\model\AdminMenu;
use app\common\model\Base;
use app\common\controller\ApiCommon;
use think\Db;
use think\Loader;
use think\Session;

class Admin extends Base
{
    /**
     * 后台菜单
     * @return array menu列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function _menu()
    {
        $type = Session::get('auth_type_id');
        $id = Session::get('id');
//        $AdminMenu = new AdminMenu();
        if ($type == 1){
            $group_id = Db::name('AuthGroupAccess')->where('uid',$id)->value('group_id');
            $rules = Db::name('auth_group')->where('id',$group_id)->value('rules');
            $admin_menu_list=Db::name('auth_rule')->where('id','in',$rules)
                ->where('menu','in','1,3')->select();
//            $list = Loader::model('AuthMenu')->where('menu','in','')->where('user_id','eq',$id)->value('menu');
        }else{
            $rules = Loader::model('AuthGroup')->where('auth_type_id',$type)->value('rules');
            $admin_menu_list=Db::name('auth_rule')->where('id','in',$rules)
                ->where('menu','in','1,3')->select();
//            $list = Loader::model('AuthMenu')->where('type_id','eq',$type)->where('user_id','eq',0)->value('menu');
        }
//        if (empty($admin_menu_list))
        $list = ApiCommon::temp()->arrayTree($admin_menu_list);
        return $list;
    }

    /**
     * 获取顶部菜单
     * @param $title
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function _menuTop($title)
    {
        $tree = [];
        $adminMenu = new AdminMenu();
        $info = Db::name('auth_rule')->where('name',$title)->find();
        if ($info['menu'] == 3){
            $pid = $info['pid'];
            $isc = 1;
        }elseif($info['menu'] == 2){
            $pid = Db::name('auth_rule')->where('id',$info['pid'])->value('pid');
        }else{
            $pid = $info['id'];
            $isc = 1;
        }
        $list = $adminMenu->where('pid',$pid)->where('menu',3)->select();
        foreach ($list as $val){
            if (isset($isc)){
                if ($val['id'] == $info['id'])
                    //iss 标记 菜单选中
                    $val['iss'] = 1;
                elseif($val['name'] == $info['name'])
                    $val['iss'] = 1;
            }
            $tree[] = $val->toArray();
        }
        return $tree;
    }

    /**
     * 获取按钮
     * @param int $site
     * @param $pid
     * @return array
     */
    public function authButton($site=1,$pid)
    {
        $admin_menu = Loader::model('common/AdminMenu');
        $type = Session::get('auth_type_id');
        $id = Session::get('id');
        if ($type == 1){
            $group_id = Db::name('AuthGroupAccess')->where('uid',$id)->value('group_id');
            $rules = Db::name('auth_group')->where('id',$group_id)->value('rules');
            $admin_menu_list=$admin_menu->where('id','in',$rules)->where('pid',$pid)
                ->where('menu',2)->where('but_site',$site)->select();
        }else{
            $rules = Loader::model('AuthGroup')->where('auth_type_id',$type)->value('rules');
            $admin_menu_list = $admin_menu->where('id','IN',$rules)->where('pid',$pid)
                ->where('menu',2)->where('but_site',$site)->select();
        }
        $arr = $list = [];
        foreach ($admin_menu_list as $value){
            $arr['but_type']=$value['but_type'];
            $arr['but_site']=$value['but_site'];
            if (strpos($value['but_field'],',') && !empty($value['but_field'])){
                list($value['but_field'],$value['but_stat']) = explode(',',$value['but_field']);
            }
            if(!empty($value['but_field'])){
                $arr['but_field']=$value['but_field'];
            }
            $value['urlval'] = http_build_query($arr);
            $list[] = $value->toArray();
        }
        return $list;
    }

}