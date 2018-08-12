<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16
 * Time: 11:20
 */

namespace app\shopadmin\controller;

use think\Config;
use think\Session;
use think\Db;
class Index extends Admin
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 首页数据显示
     */
    public function welcome()
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
        //今日订单
        $TodayOrder=$this->showValidOrder($shopid);
        //店铺数据
        $ShopData=$this->showShopdata($shopid);
        $this->assign("TodayOrder",$TodayOrder);
        $this->assign("ShopData",$ShopData);
        return $this->fetch();
    }

    public function logout ()
    {
        if (Session::has('name')) {
            $list = [
                1 => ['url' => 'index/index', 'value' => []],
                2 => ['url' => 'extro_index/index', 'value' => ['name' => 'agent']],
                3 => ['url' => 'extro_index/index', 'value' => ['name' => 'city']],
                4 => ['url' => 'extro_index/index', 'value' => ['name' => 'area']],
            ];
            $id = Session::get('auth_type_id');

            //清除session 并跳转登录
            Session::clear();
            $this->redirect($list[$id]['url'], $list[$id]['value']);
        }else {
            Session::clear();
            $this->redirect('Login/index');
        }
    }

    /**
     * 首页显示的有效订单和订单收入
     */
    public function showValidOrder($shopid)
    {

        //查询今日有效订单数量
        $orderCunt=Db::name("order")
            ->where("shop_id=".$shopid." and status=6 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')")
            ->count();
        //查询今日订单收入
        $orderMoney=Db::name("order")
            ->where("shop_id=".$shopid." and status=6 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')")
            ->field("(total_fee-distribution_fee) total_fee")
            ->select();
        //扣除服务费
        $orderSum=0;
        $service_money=Config::get("servicemoney.service_money");
        foreach ($orderMoney as $k=>$v){
            $v["total_fee"]=$v["total_fee"]-$service_money;
            $orderSum+=$v["total_fee"];
        }

        //查询昨日有效订单数量
        $YesorderCunt=Db::name("order")
            ->where("shop_id=".$shopid." and status=6 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->count();
        //查询昨日订单收入
        $YesorderMoney=Db::name("order")
            ->where("shop_id=".$shopid." and status=6 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->field("(total_fee-distribution_fee) total_fee")
            ->select();
        //扣除服务费
        $YesorderSum=0;
        foreach ($YesorderMoney as $k=>$v){
            $v["total_fee"]=$v["total_fee"]-$service_money;
            $YesorderSum+=$v["total_fee"];
        }

        $TodayOrder=[
            "orderCunt"=>$orderCunt,
            "orderMoney"=>number_format($orderSum,2),
            "YesorderCunt"=>$YesorderCunt,
            "YesorderMoney"=>number_format($YesorderSum,2)
        ];

        return $TodayOrder;
    }

    /**
     * 首页显示店铺信息
     */
    public function showShopdata($shopid)
    {
        $shop=Db::name("shop")->field("logo,shop_name")->where("id=".$shopid)->find();
        $notice=Db::name("shop_notice")->field("notice")->where("shopid=".$shopid)->find();
        //查询到期预约单数量
        $preorder=Db::name("order")->where("status=6 and is_pro_order=1 and shop_id=".$shopid)->count();
        //查询待回复差评数量
        $comment=Db::name("order_comment")->where("num<=2 and sid=".$shopid)->select();
        $replyCount=0;
        foreach ($comment as $k=>$v){
            $reply=Db::name("reply")->where("reply_id =".$v["id"])->count();
            //已回复差评数量
            $replyCount+=$reply;
        }
        //查询全部差评数量
        $commentCount=Db::name("order_comment")->where("num<=2 and sid=".$shopid)->count();
        //待回复差评数量
        $waitReplyCount=$commentCount-$replyCount;

        $ShopData=[
            "ShopLogo"=>$shop["logo"],
            "ShopName"=>$shop["shop_name"],
            "ShopNotice"=>$notice["notice"],
            "preorder"=>$preorder,
            "waitReplyCount"=>$waitReplyCount,
        ];
        return $ShopData;
    }
}