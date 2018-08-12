<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27
 * Time: 19:10
 */

namespace app\shopadmin\controller;
use think\Db;
use think\Request;
use think\Session;

class Shopset extends Admin
{
    /**
     * 显示门店设置
     */
    public function showShopData(Request $request)
    {

        $bid=session::get("bid");

        $shopid=session::get("shopid");
        if(!isset($bid)){
            $bid=1;
        }
        if(!isset($shopid)){
            //若shopid不存在
            $shop=Db::name("shop")->where("bid=".$bid)->find();
            $shopid=$shop["id"];
        }
        //查询店铺基本信息
        $shopDetails=Db::name("shop")->where("id=".$shopid)->find();
        //查询店铺地址
        $address=Db::name("shop_address")->where("shopid=".$shopid)->find();
        //查询店铺公告
        $notice=Db::name("shop_notice")->where("shopid=".$shopid)->find();
        //处理营业时间
        $week=explode(",",$shopDetails["business_week"]);
        //查询店铺营业执照证件
        $cardOne=Db::name("shop_card")->where("card_type=1 and shop_id=".$shopid)->find();
        //查询店铺特许证证件
        $cardTwo=Db::name("shop_card")->where("card_type=4 and shop_id=".$shopid)->find();
        //查询店铺许可证证件
        $cardThree=Db::name("shop_card")->where("card_type=3 and shop_id=".$shopid)->find();
        //查询店铺个人证件
        $cardFour=Db::name("shop_card")->where("card_type=2 and shop_id=".$shopid)->find();
        //处理证件照
        $IDPhoto=explode(",",$cardFour["certificates_img"]);
        //查询店铺餐饮等级
        $cardFive=Db::name("shop_card")->where("card_type=5 and shop_id=".$shopid)->find();
        $this->assign("shopDetails",$shopDetails);
        $this->assign("address",$address);
        $this->assign("notice",$notice);
        $this->assign("week",$week);
        $this->assign("cardOne",$cardOne);
        $this->assign("cardTwo",$cardTwo);
        $this->assign("cardThree",$cardThree);
        $this->assign("cardFour",$cardFour);
        $this->assign("IDPhoto",$IDPhoto);
        $this->assign("cardFive",$cardFive);
        return $this->fetch('Shop_manage');
    }

    /**
     * 修改营业状态
    **/
    public function updShopstatus(Request $request)
    {
        $bid=$request->param("bid");
        $shopid=$request->param("shopid");
        //查询店铺状态
        $status=db("shop")->where("id=".$shopid." and bid=".$bid)->select();

        //状态为0改为1
        if($status[0]["shop_status"]==0){
            $arr=['shop_status'=>1];
            $result=db("shop")->where("id=".$shopid." and bid=".$bid)->update($arr);
            //if($result){
                //return "<script>alert('成功');history.back(-1);</script>";
            //}else{
                return "<script>history.back(-1);</script>";
            //}
        }
        //状态为1改为0
        if($status[0]["shop_status"]==1){
            $arr=['shop_status'=>0];
            $result=db("shop")->where("id=".$shopid." and bid=".$bid)->update($arr);
            //if($result){
                //return "<script>alert('成功');history.back(-1);</script>";
            //}else{
                return "<script>history.back(-1);</script>";
            //}
        }
    }

    /**
     * 修改门店信息
     */
    public function updShopName(Request $request)
    {
        $shopid=$request->param("shopid");
        if($request->isPost()){
            $arr=[
                "shop_name"=>$_POST["shop_name"],
                "shop_info"=>$_POST["shop_info"]
            ];
            $shop=Db::name("shop")->where("id=".$shopid)->update($arr);
            if($shop){
                return "<script>history.back(-1);</script>";
            }else{
                return "<script>alert('修改门店信息失败');history.back(-1);</script>";
            }
        }

    }

    /**
     * 修该门店基本信息
     */
    public function updShopInformation(Request $request)
    {
        $shopid=$request->param("shopid");
        if($request->isPost()){
            //修该店铺表的店铺电话
            $shop=Db::name("shop")->where("id=".$shopid)->update(["shop_phone"=>$_POST["shop_phone"]]);
            //修该公告表店铺公告
            $notice=Db::name("shop_notice")->where("shopid=".$shopid)->update(["notice"=>$_POST["notice"]]);
            //修该地址表的店铺地址
            //$address=Db::name("shop_address")->where("shopid=".$shopid)->update([""]);
            if($notice){
                return "<script>history.back(-1);</script>";
            }else{
                return "<script>alert('修改门店信息失败');history.back(-1);</script>";
            }
            if($shop){
                return "<script>history.back(-1);</script>";
            }else{
                return "<script>alert('修改门店信息失败');history.back(-1);</script>";
            }
        }
    }

    /**
     * 修改营业执照信息
     */
    public function updBusinessicense(Request $request)
    {
        $shopid=$request->param("shopid");
        /*if(){

        }*/
        $arr=[
            "certificates_name"=>$_POST["certificates_name"],
        ];
        $sicense=Db::name("shop_card")->where("card_type=1 and shop_id=".$shopid)->update($arr);

        return $this->view;
    }
}