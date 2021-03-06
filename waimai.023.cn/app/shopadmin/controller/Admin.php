<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/18
 * Time: 15:05
 */

namespace app\shopadmin\controller;

use app\common\controller\Core;
use think\Db;
//use auth\Auth;
//use think\Config;
use think\Request;
//use think\Response;
//use think\Session;
use think\Loader;

class Admin extends Core
{
    public static $model; //admin  obj

    /**
     * @var 后台左侧菜单
     */
    protected $admin_menu;
    /**
     * @var 列表顶部菜单
     */
    protected $menu_top_list;
    protected $ruleList = [];
    /**
     * @var 模型名称
     */
    protected $model_name ;
    protected $model_save = 'save';
    protected $model_stat = 'setStatus';
    protected $_from_action;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $request = Request::instance();
        $model = $request->module();
        $controller = $request->controller();
        $action = $request->action();
        $where = $model.'/'.$controller.'/'.$action;
        $admin = Loader::model('AdminAuthAll');
        $admin_menu = Loader::model('common/AdminMenu');

        $name = $admin_menu->where('name',$where.'$menu=3')->find();
        if (empty($name) && $controller != 'Index'){
            $name = $admin_menu->where('name',$where)->find();
        }
        $pname = $admin_menu->where('id',$name['pid'])->value('title');
        $but_top = $admin->authButton(1,$name['id']);
        $but_list = $admin->authButton(2,$name['id']);

        if (!empty($but_top)) {
            foreach ($but_top as $value) {
                $value['url'] = url($value['name'], $value['urlval']);
                $_from_button[] = $value;
            }
            Tpldemo::temp()->_from_button = $_from_button;
        }

        if (!empty($but_list)){
            foreach ($but_list as $value){
                $value['url'] = url($value['name'],$value['urlval']);
                $_from_list_but[] = $value;
            }
            Tpldemo::temp()->_table_button = $_from_list_but;
        }

        Tpldemo::temp()->_template_title = $name['title'];
        Tpldemo::temp()->_template_nav = $pname;

        $this->_from_action = url($this->model_save);
        $this->model_name = $controller;
        $this->admin_menu = $admin->_menu();
//        halt($this->admin_menu);
        $this->menu_top_list = $admin->_menuTop($this->title);
        $this->assign('menu_top_list',$this->menu_top_list);
        $this->assign('auth_admin_menu',$this->admin_menu);
    }
}