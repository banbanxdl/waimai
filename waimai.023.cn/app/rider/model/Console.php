<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 9:08
 */

namespace app\rider\model;

use think\Model;
use think\Db;

class Console extends Admin
{
    public function index()
    {
//        $subQuery = Db::table('db_order')
//            ->field('rider_id')
//            ->where('status', 'GT', 6)//当前未送达且有分配骑手的订单
//            ->where('receipt_time>UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE))')//今日送货
//            ->buildSql();
        $rider = Db::table('db_rider')//获取在线骑手工作量
        ->alias('r')
            ->field('r.id,i.name,today_order_num as today_num,all_order_num as all_num')
            ->join('db_rider_info i', 'r.id = i.rider_id', 'LEFT')
            ->where('r.status = 1')
            ->select();
        $onlinenum = Db::table('db_rider')->where('status=1')->count();
        $outlinenum = Db::table('db_rider')->where('status=0')->count();
        $data = [
            'rider' => $rider,
            'onlinenum' => $onlinenum,
            'outlinenum' => $outlinenum
        ];
        return $data;
    }

    public function riderSelect($select)
    {
        $val = $select['val'];//是否在线
        $rider = $select['rider'];//姓名
        $num = $select['num'];//接单(总)量
        $curent = $select['curent'];
        if ($rider == 1) {
            $order = 'i.name asc';
        }else{
            $order = 'i.name desc';
        }
        if ($num == 1) {
            if($curent == 'rider') {
                $order = ($order == '') ? 'r.all_order_num asc' : $order . ',r.all_order_num asc';
            }else{
                $order = ($order == '') ? 'r.all_order_num asc' :  'r.all_order_num asc,'.$order;
            }
        }else{
            if($curent == 'rider') {
                $order = ($order == '') ? 'r.all_order_num desc' : $order . ',r.all_order_num desc';
            }else{
                $order = ($order == '') ? 'r.all_order_num desc' :  'r.all_order_num desc,'.$order;
            }
        }
            if ($val == 1) {
                $rider = Db::table('db_rider')//获取在线骑手工作量
                ->alias('r')
                    ->field('r.id,i.name,r.today_order_num as today_num,r.all_order_num as all_num')
                    ->join('db_rider_info i', 'r.id = i.rider_id', 'LEFT')
                    ->where('r.status = 1')
                    ->order($order)
                    ->select();
            }else{
                $rider = Db::table('db_rider')//获取在线骑手工作量
                ->alias('r')
                    ->field('r.id,i.name,r.today_order_num as today_num,r.all_order_num as all_num')
                    ->join('db_rider_info i', 'r.id = i.rider_id', 'LEFT')
                    ->where('r.status = 0')
                    ->order($order)
                    ->select();
            }
                return $rider;

    }


}