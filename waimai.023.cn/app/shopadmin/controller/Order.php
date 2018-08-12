<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 10:17
 */

namespace app\shopadmin\controller;

use think\Request;
use think\Session;
use think\Db;
use think\Config;

class Order extends Admin
{

    /**
     * 统计今日概况
     */
    public function TodaySurvey($shopid)
    {
        //已接订单数量
        $receipt=Db::name("order_log")
            ->where("uid=".$shopid." and type=2 and status=4 and
             date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')")
            ->count();
        //预订单数量
        $preorder=db::name("order_main")->where("order_type=1 and is_reserve_num=1 and 
        date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and shop_id=".$shopid)->count();
        //退款订单
        $ordermain=Db::name("order_main")->where("order_type=1 and 
        date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and shop_id=".$shopid)->select();
        $refund=[];$refundmoney=0;
        foreach ($ordermain as $key=>$value){
            $refund=Db::name("apply_refund")->where("status=3 and order_id=".$value["id"])->select();
            $refundmoney=Db::name("apply_refund")->field("order_id,money,status")->where("status=3 and order_id=".$value["id"])->sum("money");
        }
        //今日订单收入
        $income=Db::name("order_money_log")->field("shop_money,add_time")->where("date_format(from_unixtime(add_time),'%Y-%m-%d') 
        = date_format(now(),'%Y-%m-%d') and shop_id=".$shopid)->sum("shop_money");
        //今日营业额
        $turnover=$income-$refundmoney;
        $arr=[
            'receipt'=>$receipt,
            'preorder'=>$preorder,
            'refund'=>count($refund),
            'turnover'=>$turnover
        ];
        return $arr;
    }

    /**
     * 进行中的订单
     */
    public function HaveHandOrder(Request $request)
    {


        return $this->fetch('Have_hand');
    }

    /**
     * 已完成的订单
     */
    public function CompletionOrder(Request $request)
    {
        $bid=session::get("bid");
        if(!isset($bid)){
            $bid=1;
        }
        $shopid=session::get("shopid");
        if(!isset($shopid)){
            //若shopid不存在
            $shop=Db::name("shop")->where("bid=".$bid)->find();
            $shopid=$shop["id"];
        }
        //$shopid=$request->param("shopid");
        $type=$request->param("type");
        $order=$order=Db::name("order")->where("shop_id=".$shopid." and is_pro_order=0 and dispatch_type=1 and status=6")->select();
        if($type==1){
            //查询普通订单
            $order=Db::name("order")
                ->where("shop_id=".$shopid." and is_pro_order=0 and dispatch_type=1 and status=6")
                ->select();
        }else if ($type==2){
            //查询普通订单
            $order=Db::name("order")
                ->where("shop_id=".$shopid." and is_pro_order=1 and dispatch_type=1 and status=6")
                ->select();
        }else if ($type==3){
            //查询普通订单
            $order=Db::name("order")
                ->where("shop_id=".$shopid." and is_pro_order=0 and dispatch_type=2 and status=6")
                ->select();
        }
        foreach ($order as $key=>$value){
            //查询用户地址信息
            $address=Db::name("address")->where("id=".$value["address_id"])->find();
            $order[$key]["user_name"]=$address["name"];//用户名
            $order[$key]["phone"]=$address["phone"];//用户电话
            $order[$key]["address"]=$address["address"];//用户地址
            //查询骑手信息
            $rider=Db::name("rider")->where("id=".$value["rider_id"])->find();
            $riderinfo=Db::name("rider_info")->where("rider_id=".$value["rider_id"])->find();
            $order[$key]["rider_name"]=$riderinfo["name"];//骑手名字
            $order[$key]["rider_img"]=$rider["img"];//骑手头像
            $order[$key]["rider_phone"]=$riderinfo["phone"];//骑手电话
            //查询商品信息
            $order_goods=Db::view("order_goods","sale,num,total","order_goods.goods_id=goods.id")
                ->view("goods","goods_name")
                ->where("order_goods.order_id=".$value["id"])
                ->select();
            $order[$key]["goods"]=$order_goods;
            //查询订单相关金额
            $order_money=Db::name("order_money_log")->where("order_id=".$value["order_main_id"])->find();
            $order[$key]["service"]=$order_money["sys_money"];//平台所得
            $order[$key]["income"]=$order_money["shop_money"];//店铺所得
            $order_main=Db::name("order_main")->where("id=".$value["order_main_id"])->find();
            $order[$key]["order_num"]=$order_main["order_num"];//订单编号
            $order[$key]["distribution_info"]=$shop["distribution_info"];//店铺配送方式

        }
        //今日订单概况
        $todaySurvey=$this->TodaySurvey($shopid);
        $this->assign("order",$order);
        $this->assign("todaySurvey",$todaySurvey);
        return $this->fetch('Order_completion');
    }

    /**
     * 已取消的订单
     */
    public function CancelOrder(Request $request)
    {
        $bid=session::get("bid");
        if(!isset($bid)){
            $bid=1;
        }
        $shopid=session::get("shopid");
        if(!isset($shopid)){
            //若shopid不存在
            $shop=Db::name("shop")->where("bid=".$bid)->find();
            $shopid=$shop["id"];
        }
        //$shopid=$request->param("shopid");
        $type=$request->param("type");
        $order=$order=Db::name("order")->where("shop_id=".$shopid." and is_pro_order=0 and dispatch_type=1 and status=6")->select();
        if($type==1){
            //查询普通订单
            $order=Db::name("order")
                ->where("shop_id=".$shopid." and is_pro_order=0 and dispatch_type=1 and status=6")
                ->select();
        }else if ($type==2){
            //查询普通订单
            $order=Db::name("order")
                ->where("shop_id=".$shopid." and is_pro_order=1 and dispatch_type=1 and status=6")
                ->select();
        }else if ($type==3){
            //查询普通订单
            $order=Db::name("order")
                ->where("shop_id=".$shopid." and is_pro_order=0 and dispatch_type=2 and status=6")
                ->select();
        }
        foreach ($order as $key=>$value){
            //查询用户地址信息
            $address=Db::name("address")->where("id=".$value["address_id"])->find();
            $order[$key]["user_name"]=$address["name"];//用户名
            $order[$key]["phone"]=$address["phone"];//用户电话
            $order[$key]["address"]=$address["address"];//用户地址
            //查询骑手信息
            $rider=Db::name("rider")->where("id=".$value["rider_id"])->find();
            $riderinfo=Db::name("rider_info")->where("rider_id=".$value["rider_id"])->find();
            $order[$key]["rider_name"]=$riderinfo["name"];//骑手名字
            $order[$key]["rider_img"]=$rider["img"];//骑手头像
            $order[$key]["rider_phone"]=$riderinfo["phone"];//骑手电话
            //查询商品信息
            $order_goods=Db::view("order_goods","sale,num,total","order_goods.goods_id=goods.id")
                ->view("goods","goods_name")
                ->where("order_goods.order_id=".$value["id"])
                ->select();
            $order[$key]["goods"]=$order_goods;
            //查询订单相关金额
            $order_money=Db::name("order_money_log")->where("order_id=".$value["order_main_id"])->find();
            $order[$key]["service"]=$order_money["sys_money"];//平台所得
            $order[$key]["income"]=$order_money["shop_money"];//店铺所得
            $order_main=Db::name("order_main")->where("id=".$value["order_main_id"])->find();
            $order[$key]["order_num"]=$order_main["order_num"];//订单编号
            $order[$key]["distribution_info"]=$shop["distribution_info"];//店铺配送方式

        }
        //今日订单概况
        $todaySurvey=$this->TodaySurvey($shopid);
        $this->assign("order",$order);
        $this->assign("todaySurvey",$todaySurvey);
        return $this->fetch('Order_completion');
    }
}