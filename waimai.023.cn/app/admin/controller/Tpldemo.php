<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 14:19
 */

namespace app\admin\controller;


use app\common\controller\Core;

class Tpldemo extends Core
{
    protected static $temp;
    protected  $_template_title;          //模板标题
    protected  $_template_nav;            //模板导航条
    protected  $_from_action;             //表单提交url
    protected  $_from_button;            //列表头部按钮
    protected  $_table_title = [];        //列表头部数据
    protected  $_table_data = [];         //数据列表
    protected  $_table_button = [];       //列表按钮
    protected  $_extra_html = '';         //额外HTML补充
    protected  $_temp_footer = 0;         //模板js加载判断
    protected  $_template = '';           //模板位置s
    private $temp_str = 'templatefromstemplatelistslayerfrom';

    /**
     * 单例模式
     * @return Tpldemo
     */
    public static function temp()
    {
        if (self::$temp){
            return self::$temp;
        }else{
            self::$temp = new Tpldemo();
            return self::$temp;
        }
    }

    /**
     * 创建
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $this->$name=$value;
        return $this;
        // TODO: Implement __set() method.
    }

    /**
     * 获取
     * @param $name
     * @return $this
     */
    public function __get($name)
    {
        $this->$name;
        return $this;
        // TODO: Implement __get() method.
    }

    /**
     * 自动完成方法
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $name = strtolower($name);
        //判断字符串是否包含
        if (strpos($this->temp_str,$name)!== false){
            $this->template($name, $arguments);
        }else{
            return $this->error('系统暂时没有找到与之匹配的方法名，请重新输入',url('index/index'));
        }
        return $this;
        // TODO: Implement __call() method.
    }

    private function template($name,$value)
    {
        if ($name == 'templatefrom' || $name == 'from'){
            $this->_temp_footer = 1;
            $this->_template = 'layout/from';
        }elseif ($name == 'templatelists' || $name == 'list'){
            $this->_temp_footer = 0;
            $this->_template = 'layout/list';
        }elseif ($name == 'layerfrom' || $name == 'layer'){
            $this->_temp_footer = 0;
            $this->_template = 'layout/layerfrom';
        }else{
            return $this->error('模板路径不存在',url('Admin/Login/index'));
        }
        return $this;
    }

    /**
     * @param $_table_title  列表头部数据
     * @return mixed
     */
    public function tableTitle($_table_title)
    {
        $arr_list = $list = [];
        if (!is_array($_table_title)){
            return $this->error('您传入的不是数组',url('index/index'));
        }
        foreach ($_table_title as $key => $val) {
            foreach ($val as $j => $ab) {
                $list['title'] = $ab;
                $list['fields'] = $j;
                $arr_list[] = $list;
            }
        }
        $this->_table_title = $arr_list;
        return $this;
    }

    /**
     * 添加按钮 和 属性
     * @param $type
     * @param $con
     * @return mixed
     */
    private function addButtonList($type, $con)
    {
        switch ($type){
            /**
             * 执行模板方法
             * from_ajax
             */
            case 'del':
                $con['title'] = '删除';
                $con['status'] = 'del';
                $con['ico'] = '&#xe6e2;';
                $con['color'] = '#f00';
                break;
            case 'opensta':
                $con['title'] = '启用';
                $con['ico'] = '&#xe615;';
                $con['color'] = '#00B83F';
                break;
            case 'closesta':
                $con['title'] = '禁止';
                $con['ico'] = '&#xe631;';
                $con['color'] = '#f00';
                break;
            /**
             * 执行模板方法
             * list_or
             */
            case 'audit':
                $con['title'] = '审核';
                $con['field'] = 'status';
                $con['ico'] = '&#xe606;';
                $con['color'] = '#f00';
                break;
            /**
             * 执行模板方法
             * save_open
             */
            case 'edit':
                $con['title'] = '编辑';
                $con['ico'] = '&#xe6df;';
                $con['color'] = '#0ff';
                break;
            case 'right':
                $con['title'] = '分配规则';
                $con['ico'] = '&#xe605;';
                $con['color'] = '#ff7800';
                break;
            case 'addrule'://用户组添加管理员
                $con['title'] = '分配管理员';
                $con['ico'] = '&#xe667;';
                $con['color'] = '#0070ff';
                break;
            case 'imgurl'://添加或修改头像
                $con['title'] = '修改头像';
                $con['ico'] = '&#xe60a;';
                $con['color'] = '#6400ff';
                break;
            case 'money': //充值
                $con['title'] = '充值';
                $con['ico'] = '&#xe63a;';
                $con['color'] = '#ffca00';
                break;
            case 'pwd':
                $con['title'] = '修改密码';
                $con['ico'] = '&#xe63f;';
                $con['color'] = '#caff00';
                break;
            case 'recommend':
                $con['title'] = '推荐到广告位';
                $con['ico'] = '&#xe6aa;';
                $con['color'] = '#095223';
                break;
            case 'show':
                $con['title'] = '展示';
                $con['ico'] = '&#xe60c;';
                $con['color'] = '#8d6cef';
                break;
            case 'button':
                $con['title'] = '添加按钮';
                $con['ico'] = '&#xe61f;';
                $con['color'] = '#299a38';
                break;
            case 'button_list':
                $con['title'] = '查看按钮列表';
                $con['ico'] = '&#xe6bf;';
                $con['color'] = '#299a38';
                break;
        }
        return $con;
    }


    private function buttonAuthField($data,$val)
    {
        foreach ($data as $value){
            if ($value['but_type'] == 2){  //开启、禁止
                //开启
                if ($val[$value['but_field']] === config('status.open')) {
                    $status = 'closesta';
                    $value['title'] = '禁止';
                    $value['but_ico'] = 'shenhe-tingyong';
                    $value['status'] = config('status.open');
                    $con['ico'] = '&#xe631;';
                    $con['color'] = '#f00';

                    //禁止
                } elseif ($val[$value['but_field']] === config('status.close')) {
                    $status = 'opensta';
                    $value['title'] = '启用';
                    $value['but_ico'] = 'gouxuan';
                    $value['status'] = config('status.close');
                    $con['ico'] = '&#xe615;';
                    $con['color'] = '#00B83F';

                }
            }elseif ($value['but_type'] == 3){ //审核
                if ($val[$value['but_field']] === config('status.wait')){
                    $value['status'] = 'audit';
                    $con['title'] = '审核';
                    $con['field'] = 'status';
                    $con['ico'] = '&#xe606;';
                    $con['color'] = '#f00';
                }
            }
        }
    }

    /**
     * @param $_table_data        数据列表
     * @param string $replenish   补充  显示按钮参数 add,edit,delete,status
     * @param string $argument    参数  需要操作的参数
     * @param string $op
     * @param string $date
     * @return $this
     */
    public function tableData($_table_data, $replenish = '', $argument = '', $op = '', $type = 'input')
    {
        if (!in_array($op,[0,1],true)) {
            if (!empty($replenish) && !empty($argument)) {
                if (is_string($replenish)) {
                    $replenish = explode(',', $replenish);
                }
                if (is_string($argument)) {
                    $argument = explode(',', $argument);
                }
                foreach ($replenish as $key => $value) {
                    if (isset($argument[$key]['url'])){
                        $accident[$value] = $argument[$key];
                    }else{
                        $accident[$value] = ['url' => $argument[$key]];
                    }
                }
                foreach ($accident as $key => $value) {
                    if ($key != 'status') {
                        $value = $this->addButtonList($key, $value);
                    }
                    $action[$key] = $value;
                }
            }

            foreach ($_table_data as $key => $val) {
                //判断按钮是否存在
                if (!empty($replenish) && !empty($argument)) {
                    //判断字段和按钮是否存在
                    $status = '';
                    if (isset($val['status']) && isset($action['status'])) {
                        if (!isset($action['status']['field'])){
                            $action['status']['field'] = 'status';
                        }
                        //开启
                        if ($val[$action['status']['field']] === config('status.open')) {
                            $status = 'closesta';
                            //禁止
                        } elseif ($val[$action['status']['field']] === config('status.close')) {
                            $status = 'opensta';
                        }
                        $action['status'] = $this->addButtonList($status, $action['status']);
                    }
                    $val['action'] = $action;
                    //将不符合条件的数组删除
                    if (isset($val['status'])) {
                        if ($val['status'] != 2) {
                            unset($val['action']['audit']);
                        }
                    }
                }else{
                    $val['action'] = $this->_table_button;
                }
                $this->_table_data[] = $val;
            }
        }else{
            //标题
            $list['title'] = $_table_data;
            //字段
            $list['field'] = $replenish;
            //是否加*号
            $list['red'] = $op===1?$op:0;
            //表单选项的类型
            $list['name'] = $type?$type:'input';
            //说明
            $list['hint'] = $argument;
            $this->_table_data[] = $list;
        }
        return $this;
    }

    /**
     * 额外的HTML代码
     * @param $_extra_html
     * @return $this
     */
    public function extraHtml($_extra_html)
    {
        if (is_array($_extra_html)){
            $_extra_html = "{include file='formtpl/extra_html' content='$_extra_html' }";
        }
        $this->_extra_html = $_extra_html;
        return $this;
    }

    /**
     * 模板输出
     * @return mixed
     */
    public function templateView()
    {
        $this->assign('_template_title',$this->_template_title);
        $this->assign('_template_nav',$this->_template_nav);
        $this->assign('_from_action',$this->_from_action);
        $this->assign('_from_button',$this->_from_button);
        $this->assign('_table_title',$this->_table_title);
        $this->assign('_table_data',$this->_table_data);
        $this->assign('_extra_html',$this->_extra_html);
        $this->assign('_temp_footer',$this->_temp_footer);
        return $this->fetch($this->_template);
    }
}