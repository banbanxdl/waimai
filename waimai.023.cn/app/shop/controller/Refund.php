<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/7
 * Time: 17:47
 */
namespace app\shop\controller;
use think\Request;
use think\Loader;
use think\Db;
use think\Image;
class Refund extends Index
{
    /**
     * 开启或关闭极速退款设置
     * */
    public function OpenQuickRefund(Request $request)
    {
        $shopid=$request->param("shopid");
        $refund=db("shop")->field("quick_refund")->where("id=".$shopid)->select();
        if($refund[0]["quick_refund"]==0){
            $result=db("shop")->where("id=".$shopid)->update(["quick_refund"=>1]);
            if($result){
                return $this->message("1","开启成功",2);
            }else{
                return $this->message("0","开启失败",3);
            }
        }else if($refund[0]["quick_refund"]==1){
            $result=db("shop")->where("id=".$shopid)->update(["quick_refund"=>0]);
            if($result){
                return $this->message("0","关闭成功",2);
            }else{
                return $this->message("1","开启失败",3);
            }
        }
    }
    /**
     * 商家同意退款
     */
    public function approvalRefund(Request $request)
    {
        //订单id
        $orderid=$request->param("orderid");
        $result=db("apply_refund")->where("order_id=".$orderid)->update(["status"=>1]);
        if($result){
            return $this->message($result,"成功",2);
        }else{
            return $this->message($result,"失败",3);
        }

    }
    /**
     * 商家主动退款(全部退款)
     */
    public function activeRefund(Request $request)
    {
        //订单id
        $data=$request->param();
        $update=db("order")->where("id=".$data['orderid'])->update(["status"=>9]);
        $arr=[
            'order_id'=>$data['orderid'],
            'money'=>$data['money'],
            'reason'=>$data['reason'],
            'other_reason'=>$data['other_reason'],
            'add_time'=>time(),
            'update_time'=>time(),
            'status'=>1,
        ];
        $add=db("apply_refund")->where("order_id=".$data['orderid'])->insert($arr);
        if($add){
            return $this->message($add,"成功",2);
        }else{
            return $this->message($add,"失败",3);
        }
    }
}