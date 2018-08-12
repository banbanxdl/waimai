<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 14:19
 */

namespace app\adminall\controller;


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
    protected  $_template = '';           //模板位置
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
        return $this->$name;
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
            array_unshift($arguments,$name);
            return call_user_func_array([$this,'template'],$arguments);
        }else{
            return $this->error('系统暂时没有找到与之匹配的方法名，请重新输入',url('index/index'));
        }
        return $this;
        // TODO: Implement __call() method.
    }

    /**
     * 模板
     * @param $name
     * @param string $value
     * @return $this|void
     */
    private function template($name,$value='')
    {
        if (strpos($name,'from')!== false){
            $this->_temp_footer = 1;
            if (empty($value)){
                $value = 'from';
            }
            $this->_template = 'layout/'.$value;
        }elseif (strpos($name,'list')!== false){
            $this->_temp_footer = 0;
            if (empty($value)){
                $value = 'list';
            }
        }elseif (strpos($name,'template')!== false){
            $this->_temp_footer = 0;
            if (empty($value)){
                $value = 'list';
            }
        }else{
            return $this->error('模板不存在',url('index/index'));
        }
        $this->_template = 'layout/'.$value;
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
            if (is_array($val) && !empty($val)){
                foreach ($val as $j => $ab) {
                    $list['fields'] = $j;
                    $list['title'] = $ab;
                }
            }else{
                $list['fields'] = $key;
                $list['title'] = $val;
            }
            $arr_list[] = $list;
        }
        $this->_table_title = $arr_list;
        return $this;
    }

    /**
     * 添加按钮 和 属性
     * @param array $data
     * @param array $val
     * @return array|mixed
     */
    private function addButtonList($data=[],$val)
    {
        if (empty($data)){
            return [];
        }else {
            foreach ($data as $value) {
                switch ($value['but_type']) {
                    case 1:
                        $value;
                        break;
                    case 2:
                        //开启
                        if ($val[$value['but_field']] === config('status.open')) {
                            $value['but_status'] = 'closesta';
                            //禁止
                        } elseif ($val[$value['but_field']] === config('status.close')) {
                            $value['but_status'] = 'opensta';
                        }
                        break;
                    case 3:
                        if ($val[$value['but_field']] === config('status.wait')) {
                            $value['but_status'] = 'audit';
                        }
                        break;
                    case 4:
                        $value['but_status'] = 'del';
                        break;
                    default:
                        $value;
                        break;
                }
                $list[] = $value;
            }
            return $list;
        }
    }

    /**
     * @param $_table_data        数据列表
     * @param string $replenish   补充  显示按钮参数 add,edit,delete,status
     * @param string $argument    参数  需要操作的参数
     * @param string $op
     * @param string $type
     * @return $this
     */
    public function tableData($_table_data, $replenish = '', $argument = '', $op = '', $type = 'input')
    {
        if (is_array($_table_data)) {
            foreach ($_table_data as $key => $val) {
                //判断按钮是否存在
                $val['action'] = $this->addButtonList($this->_table_button,$val);
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
        if (is_string($_extra_html)){
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