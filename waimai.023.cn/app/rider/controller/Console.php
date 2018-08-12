<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 20:39
 */

namespace app\rider\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Loader;

class Console extends Admin
{
    private $riders;//骑手组
    private $area;//区域

    public function __construct(Request $request)
    {
        parent::__construct($request);

    }

    public function index(Request $request)
    {
        $a_url = $this->_form_button;//按钮的授权地址
        $rchg_url = $a_url[0]['url'];
        $data = Loader::model('Console')->index();
        $rider = $data['rider'];
        $onlinenum = $data['onlinenum'];
        $outlinenum = $data['outlinenum'];
        $this->assign('rider', $rider);
        $this->assign('onlinenum', $onlinenum);
        $this->assign('outlinenum', $outlinenum);
        $this->assign('riderchg', $rchg_url);
        return Tpldemo::temp()->template('cent')->templateView();
    }

    public function newOrder()
    {
        $order = Db::table('db_order')->where('status=3')->select();
        $tack = Db::table('db_take_order')->where('status = 3')->select();
    }

    public function rideChg(Request $request)
    {
        $post = $request->post();
        $val = $post['data']['val'];
        $rider = $post['data']['rider'];
        $num = $post['data']['num'];
        $curent = $post['data']['curent'];
        $where=['val'=>$val,'rider'=>$rider,'num'=>$num,'curent'=>$curent];
        $rider = Loader::model('Console')->riderSelect($where);
        return $rider;

    }

    public function riderSort(Request $request)
    {
        $post = $request->post();
        $val = $post['data']['val'];
        $rider = $post['data']['rider'];
        $num = $post['data']['num'];
        $where=['val'=>$val,'rider'=>$rider,'num'=>$num];
        $rider = Loader::model('Console')->riderSelect($where);
        return $rider;
    }

    public function riderSelect($where){

    }
}