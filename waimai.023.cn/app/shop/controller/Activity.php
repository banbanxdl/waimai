<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10
 * Time: 9:43
 */
namespace app\shop\controller;
use think\Request;
use think\Loader;
use think\Db;
class Activity extends Index{

    //显示全部活动类型
    public function showActivitytype(Request $request)
    {
        $data=db("shop_activity_type")->select();
        return $this->message($data,"成功",2);
    }
    //新建活动
    public function addActivity(Request $request)
    {
        $data=$request->param();
        $arr=[
            'type'=>$data['typeid'],
            'shop_id'=>$data['shopid'],
            'begin_time'=>$data['begin_time'],
            'end_time'=>$data['end_time'],
            'money'=>$data['money'],
            'give_money'=>$data['give_money'],
            ];
        $result=db("shop_activity")->insertGetId($arr);
        if($result){
            return $this->message($result,"添加活动成功",2);
        }
    }
    //显示未开始的活动
    public function showActivity(Request $request)
    {
        $shopid=$request->param("shopid");
        $data=db("shop_activity")->where("shop_id=".$shopid." and begin_time >".time())->select();
        for($i=0;$i<count($data);$i++){

            $data[$i]["begin_time"]=date("Y-m-d H:i:s",$data[$i]["begin_time"]);
            $data[$i]["end_time"]=date("Y-m-d H:i:s",$data[$i]["end_time"]);
            $data[$i]["add_time"]=date("Y-m-d H:i:s",$data[$i]["add_time"]);
        }
        return $this->message($data,"成功",2);
    }
    //显示进行中的活动
    public function JingActivity(Request $request)
    {
        $shopid=$request->param("shopid");
        $data=db("shop_activity")->where("shop_id=".$shopid." and begin_time <=".time()." and end_time >=".time())->select();

        for($i=0;$i<count($data);$i++){
            //查询活动类型
            $data[$i]["type"]=db("shop_activity_type")->field("activitytype")->where("id=".$data[$i]["type"])->select();
            //查询该活动的总订单量
            $data[$i]["zongorder"]=db::view("order_activity","orderid,activityid")
                ->view("order","id","order_activity.orderid=order.id")
                ->where("order.status=6")
                ->where("activityid like '%,".$data[$i]["id"]."' or activityid like '%,".$data[$i]["id"].",%' or activityid like '".$data[$i]["id"].",%' or activityid =".$data[$i]["id"])
                ->count();
            //查询该活动的昨日订单量(流水)
            $result=db::view("order_activity")
                ->view("order","total_fee,delivery_time","order_activity.orderid=order.id")
                ->where("order.status=6")
                ->where("activityid like '%,".$data[$i]["id"]."' or activityid like '%,".$data[$i]["id"].",%' or activityid like '".$data[$i]["id"].",%' or activityid =".$data[$i]["id"])
                ->select();
            //遍历循环订单送达时间
            //dump($data[$i]["zongorder"]);
            foreach ($result as $value){
                $time=$value['delivery_time'];

                $money=$value['total_fee'];
                //转化时间
                if(date("d",time()-86400)==date("d",$time)){

                    $vak[] = date("d",$time);
                    $shouru[] =$money ;

                }
            }
            //输出昨日订单量
            $data[$i]['yesdayorder'] = count($vak);
            //输出昨日流水
            $data[$i]['yesdayshou'] = array_sum($shouru);
            //查询该活动的总流水
            $data[$i]["zongshou"]=db::view("order_activity","orderid,activityid")
                ->view("order","id,total_fee","order_activity.orderid=order.id")
                ->where("order.status=6")
                ->where("activityid like '%,".$data[$i]["id"]."' or activityid like '%,".$data[$i]["id"].",%' or activityid like '".$data[$i]["id"].",%' or activityid =".$data[$i]["id"])
                ->sum("total_fee");


            $data[$i]["begin_time"]=date("Y-m-d H:i:s",$data[$i]["begin_time"]);
            $data[$i]["tian"]=intval(($data[$i]["end_time"]-time())/3600/24);//计算剩余天数
            $data[$i]["end_time"]=date("Y-m-d H:i:s",$data[$i]["end_time"]);
            $data[$i]["add_time"]=date("Y-m-d H:i:s",$data[$i]["add_time"]);


        }
        return $this->message($data,"成功",2);
    }
    //活动详情
    public function activitydetails(Request $request){

    }
    //显示已结束的活动
    public function FinishActivity(Request $request)
    {
        $shopid=$request->param("shopid");
        $data=db("shop_activity")->where("shop_id=".$shopid." and end_time <=".time())->select();
        for($i=0;$i<count($data);$i++){
            $data[$i]["begin_time"]=date("Y-m-d H:i:s",$data[$i]["begin_time"]);
            $data[$i]["end_time"]=date("Y-m-d H:i:s",$data[$i]["end_time"]);
            $data[$i]["add_time"]=date("Y-m-d H:i:s",$data[$i]["add_time"]);
        }
        return $this->message($data,"成功",2);
    }

    /*public function text()
    {
        if(date("Y-m-d ",$data[$i]["time"])==date("Y-m-d ",time())){

        }
    }*/
}