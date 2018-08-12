<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 16:32
 */

namespace app\admin\model;



use app\common\controller\ApiCommon;
use app\common\model\AdminMenu;
use app\common\model\Base;
use app\common\model\User;
use think\Db;
use think\Loader;
use think\Session;

class Admin extends Base
{
    /**
     * @var array 后台菜单list
     */
    public $menu = [];

    /**
     *  mixed平台资金数据
     */
    public function systemMoney()
    {
    }
    /**
     * @param $data URL地址中的PATH_INFO信息 不含后缀
     * @return bool 是否符合条件
     */
    public function pathLogin($data)
    {
        $list = explode('/',$data);
        $str = '';
        foreach ($list as $value){
            if ($value == 'admin') {
                $str .= '1';
            }elseif ($value == 'extro_login') {
                $str .= '1';
            }elseif ($value == 'agent' || $value == 'shop') {
                $str .= '1';
            }else{
                $str .= '0';
            }
        }
        if ($str === '111'){
            return true;
        }else{
            return false;
        }
    }

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
//            $list = Loader::model('AuthMenu')->where('type_id','eq',$type)->where('user_id','eq',0)->value('menu');
        }
//        if (empty($admin_menu_list))
        $list = ApiCommon::temp()->arrayTree($admin_menu_list);
        return $list;
    }


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
        }

        return $admin_menu_list;
    }


}