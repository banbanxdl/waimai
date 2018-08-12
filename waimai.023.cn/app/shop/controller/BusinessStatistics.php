<?php
/**
 * 营业统计
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31
 * Time: 16:39
 */

namespace app\shop\controller;
use think\Request;
use think\Db;

class Businessstatistics extends Index
{

    /**
     *今日营业额
     */
    public function TodayTurnover($shopid)
    {
        $order=db("order")
            ->field("total_fee,rider_distribution_fee,delivery_time")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["delivery_time"]=date("H",$order[$i]["delivery_time"]);
            $order[$i]["total_fee"]=$order[$i]["total_fee"]-$order[$i]["rider_distribution_fee"];
        }
        //今日营业额
        $todayBS=number_format(array_sum(array_column($order,"total_fee")),2);

        $TodayBS=[
            'todayBS'=>$todayBS,
            'todayOrder'=>$order
        ];
        return $TodayBS;
    }

    /**
     * 预计订单收入
     */
    public function EstimateOrder($shopid)
    {
        $order=db("order")
            ->field("total_fee,rider_distribution_fee")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["total_fee"]=$order[$i]["total_fee"]-$order[$i]["rider_distribution_fee"];
            //$total=$order[$i]["total_fee"]+$order[$i+1]["total_fee"];
        }
        //预计订单收入
        $estimat=number_format(array_sum(array_column($order,"total_fee")),2);
        return $estimat;
    }
    /**
     * 查询有无效订单
     */
    public function EffectiveOrInvalid($shopid,$status)
    {
        $order=db("order")
            ->field("id,status,add_time")
            ->where("shop_id=".$shopid." and status=".$status." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["add_time"]=date("H",$order[$i]["add_time"]);
        }
        return $order;
    }

    /**
     * 有效订单
     */
    public function Effective($shopid)
    {
        $effective=$this->EffectiveOrInvalid($shopid,6);

        return count($effective);
    }
    /**
     * 无效订单
     */
    public function Invalid($shopid)
    {
        $order=db("order")
            ->field("total_fee,rider_distribution_fee")
            ->where("shop_id=".$shopid." and (status=1 or status=8 or status=9) and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')")
            ->select();
        return count($order);
    }

    /**
     * 查询在某一时间营业额之和
     */
    public function TotalTodaydata($key,$date)
    {
        $total=0;
        if(count($key)>0){
            foreach ($key as $item) {
                if ($item['delivery_time'] == $date) {
                    $total=number_format(array_sum(array_column($key,"total_fee")),2);
                }
            }
        }
        return $total;
    }

    /**
     * 营业额趋势
     */
    public function TodayDataTrend($shopid)
    {
        //今日订单
        $TodayData=$this->TodayTurnover($shopid);
        //调用另一个控制器的查询时间的方法
        $Goodsanalysis=new Goodsanalysis();
        $date=$Goodsanalysis->YesTime();

        //营业额趋势
        $todayDT0=$this->TotalTodaydata($TodayData["todayOrder"],$date["date0"]);
        $todayDT2=$this->TotalTodaydata($TodayData["todayOrder"],($date["date0"]+2));
        $todayDT4=$this->TotalTodaydata($TodayData["todayOrder"],$date["date4"]);
        $todayDT6=$this->TotalTodaydata($TodayData["todayOrder"],($date["date4"]+2));
        $todayDT8=$this->TotalTodaydata($TodayData["todayOrder"],$date["date8"]);
        $todayDT10=$this->TotalTodaydata($TodayData["todayOrder"],($date["date8"]+2));
        $todayDT12=$this->TotalTodaydata($TodayData["todayOrder"],$date["date12"]);
        $todayDT14=$this->TotalTodaydata($TodayData["todayOrder"],($date["date12"]+2));
        $todayDT16=$this->TotalTodaydata($TodayData["todayOrder"],$date["date16"]);
        $todayDT18=$this->TotalTodaydata($TodayData["todayOrder"],($date["date16"]+2));
        $todayDT20=$this->TotalTodaydata($TodayData["todayOrder"],$date["date20"]);
        $todayDT22=$this->TotalTodaydata($TodayData["todayOrder"],($date["date12"]+2));
        $todayDT24=$this->TotalTodaydata($TodayData["todayOrder"],$date["date24"]);

        $TodayDT=[
            'todayDT0'=>$todayDT0,
            'todayDT2'=>$todayDT2,
            'todayDT4'=>$todayDT4,
            'todayDT6'=>$todayDT6,
            'todayDT8'=>$todayDT8,
            'todayDT10'=>$todayDT10,
            'todayDT12'=>$todayDT12,
            'todayDT14'=>$todayDT14,
            'todayDT16'=>$todayDT16,
            'todayDT18'=>$todayDT18,
            'todayDT20'=>$todayDT20,
            'todayDT22'=>$todayDT22,
            'todayDT24'=>$todayDT24,
        ];
        return $TodayDT;
    }
    /**
     * 查询有效订单在某一时间出现的次数
     */
    public function EffectiveCount($key,$date)
    {
        $count=0;
        if(count($key)>0){
            foreach ($key as $item) {
                if ($item['add_time'] == $date) {
                    $count=count(array_column($key,"id"));
                }
            }
        }
        return $count;
    }

    /**
     * 有效订单趋势
     */
    public function EffectiveTrend($shopid)
    {
        $EffectiveOrInvalid=$this->EffectiveOrInvalid($shopid,6);
        //调用另一个控制器的查询时间的方法
        $Goodsanalysis=new Goodsanalysis();
        $date=$Goodsanalysis->YesTime();
        //有效订单趋势
        $todayET0=$this->EffectiveCount($EffectiveOrInvalid,$date["date0"]);
        $todayET2=$this->EffectiveCount($EffectiveOrInvalid,($date["date0"]+2));
        $todayET4=$this->EffectiveCount($EffectiveOrInvalid,$date["date4"]);
        $todayET6=$this->EffectiveCount($EffectiveOrInvalid,($date["date4"]+2));
        $todayET8=$this->EffectiveCount($EffectiveOrInvalid,$date["date8"]);
        $todayET10=$this->EffectiveCount($EffectiveOrInvalid,($date["date8"]+2));
        $todayET12=$this->EffectiveCount($EffectiveOrInvalid,$date["date12"]);
        $todayET14=$this->EffectiveCount($EffectiveOrInvalid,($date["date12"]+2));
        $todayET16=$this->EffectiveCount($EffectiveOrInvalid,$date["date16"]);
        $todayET18=$this->EffectiveCount($EffectiveOrInvalid,($date["date16"]+2));
        $todayET20=$this->EffectiveCount($EffectiveOrInvalid,$date["date20"]);
        $todayET22=$this->EffectiveCount($EffectiveOrInvalid,($date["date20"]+2));
        $todayET24=$this->EffectiveCount($EffectiveOrInvalid,$date["date24"]);

        $todayET=[
            'todayET0'=>$todayET0,
            'todayET2'=>$todayET2,
            'todayET4'=>$todayET4,
            'todayET6'=>$todayET6,
            'todayET8'=>$todayET8,
            'todayET10'=>$todayET10,
            'todayET12'=>$todayET12,
            'todayET14'=>$todayET14,
            'todayET16'=>$todayET16,
            'todayET18'=>$todayET18,
            'todayET20'=>$todayET20,
            'todayET22'=>$todayET22,
            'todayET24'=>$todayET24,
        ];
        return $todayET;
    }

    /**
     * 今日实时数据
     */
    public function TodayTimeData(Request $request)
    {
        $shopid=$request->param("shopid");
        //今日营业额
        $TodayTurnover=$this->TodayTurnover($shopid);
        //预计订单收入
        $EstimateOrder=$this->EstimateOrder($shopid);
        //有效订单
        $Effective=$this->Effective($shopid);
        //无效订单
        $Invalid=$this->Invalid($shopid);
        //营业额趋势
        $TodayDataTrend=$this->TodayDataTrend($shopid);
        //有效订单趋势
        $EffectiveTrend=$this->EffectiveTrend($shopid);
        $data=[
            'TodayTurnover'=>$TodayTurnover["todayBS"],
            'EstimateOrder'=>$EstimateOrder,
            'Effective'=>$Effective,
            'Invalid'=>$Invalid,
            'TodayDataTrend'=>$TodayDataTrend,
            'EffectiveTrend'=>$EffectiveTrend
        ];
        return $this->message($data,"成功",2);
    }

    /**
     * 昨日无效订单金额
     */
    public function YesInvalidTotal($shopid)
    {
        $order=db("order")
            ->field("id,total_fee,rider_distribution_fee")
            ->where("shop_id=".$shopid." and (status=1 or status=8 or status=9) and date_format(date_sub(now(), interval 1 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["total_fee"]=$order[$i]["total_fee"]-$order[$i]["rider_distribution_fee"];
        }
        $Invalidtotal=number_format(array_sum(array_column($order,"total_fee")),2);
        return $Invalidtotal;
    }
    /**
     * 昨日顾客退款金额
     */
    public function YesUesrRM($shopid)
    {
        $order=db("order")
            ->field("id,total_fee,rider_distribution_fee")
            ->where("shop_id=".$shopid." and status=8 and date_format(date_sub(now(), interval 1 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["total_fee"]=$order[$i]["total_fee"]-$order[$i]["rider_distribution_fee"];
        }
        $UeserRefundtotal=number_format(array_sum(array_column($order,"total_fee")),2);
        $UserRefundcount=count($order);
        $YesUserRefund=[
            'UserRefundtotal'=>$UeserRefundtotal,
            'UserRefundcount'=>$UserRefundcount
        ];
        return $YesUserRefund;
    }

    /**
     * 昨日商家退款金额
     */
    public function YesBuinessRM($shopid)
    {
        $order=db("order")
            ->field("id,total_fee,rider_distribution_fee")
            ->where("shop_id=".$shopid." and status=9 and date_format(date_sub(now(), interval 1 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["total_fee"]=$order[$i]["total_fee"]-$order[$i]["rider_distribution_fee"];
        }
        $BuRefundtotal=number_format(array_sum(array_column($order,"total_fee")),2);
        $BuRefundcount=count($order);
        $YesBuRefund=[
            'BuRefundtotal'=>$BuRefundtotal,
            'BuRefundcount'=>$BuRefundcount
        ];
        return $YesBuRefund;
    }
    /**
     *  昨日具体时间退款订单
     */
    public function YesRefundOrderSe($shopid,$status)
    {
        $order=db("order")
            ->field("id,status,add_time")
            ->where("shop_id=".$shopid." and status=".$status." and date_format(date_sub(now(), interval 1 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["add_time"]=date("H",$order[$i]["add_time"]);
        }
        //调用另一个控制器的查询时间的方法
        $Goodsanalysis=new Goodsanalysis();
        $date=$Goodsanalysis->YesTime();
        $RefundCount0=$this->EffectiveCount($order,$date["date0"]);
        $RefundCount4=$this->EffectiveCount($order,$date["date4"]);
        $RefundCount8=$this->EffectiveCount($order,$date["date8"]);
        $RefundCount12=$this->EffectiveCount($order,$date["date12"]);
        $RefundCount16=$this->EffectiveCount($order,$date["date16"]);
        $RefundCount20=$this->EffectiveCount($order,$date["date20"]);
        $RefundCount24=$this->EffectiveCount($order,$date["date24"]);

        $YesRC=[
            'RefundCount0'=>$RefundCount0,
            'RefundCount4'=>$RefundCount4,
            'RefundCount8'=>$RefundCount8,
            'RefundCount12'=>$RefundCount12,
            'RefundCount16'=>$RefundCount16,
            'RefundCount20'=>$RefundCount20,
            'RefundCount24'=>$RefundCount24,
        ];
        return $YesRC;
    }

    /**
     * 昨日具体时间顾客和商家退款订单
     */
    public function YesRefundOrder($shopid)
    {
        //顾客退款
        $YesGuRC=$this->YesRefundOrderSe($shopid,8);
        //商家退款
        $YesBuRC=$this->YesRefundOrderSe($shopid,9);

        $YesRC=[

            'YesGuRC'=>$YesGuRC,
            'YesBuRC'=>$YesBuRC,
        ];

        return $YesRC;
    }

    /**
     *  近七天具体时间退款订单
     */
    public function SevenRefundOrderSe($shopid,$status)
    {
        $order=db("order")
            ->field("id,status,add_time")
            ->where("shop_id=".$shopid." and status=".$status." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["add_time"]=date("Y-m-d",$order[$i]["add_time"]);
        }

        //调用另一个控制器的查询时间的方法
        $Goodsanalysis=new Goodsanalysis();
        $date=$Goodsanalysis->SevenTime();
        $RefundCount1=seCount($order,$date["date1"],"add_time",$RefundCount1=0);
        $RefundCount2=seCount($order,$date["date2"],"add_time",$RefundCount2=0);
        $RefundCount3=seCount($order,$date["date3"],"add_time",$RefundCount3=0);
        $RefundCount4=seCount($order,$date["date4"],"add_time",$RefundCount4=0);
        $RefundCount5=seCount($order,$date["date5"],"add_time",$RefundCount5=0);
        $RefundCount6=seCount($order,$date["date6"],"add_time",$RefundCount6=0);
        $RefundCount7=seCount($order,$date["date7"],"add_time",$RefundCount7=0);

        $SevenRC=[
            'RefundCount1'=>$RefundCount1,
            'RefundCount2'=>$RefundCount2,
            'RefundCount3'=>$RefundCount3,
            'RefundCount4'=>$RefundCount4,
            'RefundCount5'=>$RefundCount5,
            'RefundCount6'=>$RefundCount6,
            'RefundCount7'=>$RefundCount7,
        ];
        return $SevenRC ;
    }

    /**
     * 近七天具体时间顾客和商家退款订单
     */
    public function SevenRefundOrder($shopid)
    {
        //顾客退款
        $SevenGuRC=$this->SevenRefundOrderSe($shopid,8);
        //商家退款
        $SevenBuRC=$this->SevenRefundOrderSe($shopid,9);
        //调用另一个控制器的查询时间的方法
        $Goodsanalysis=new Goodsanalysis();
        $date=$Goodsanalysis->SevenTime();
        $SevenRC=[
            'date1'=>$date["date1"],
            'date2'=>$date["date2"],
            'date3'=>$date["date3"],
            'date4'=>$date["date4"],
            'date5'=>$date["date5"],
            'date6'=>$date["date6"],
            'date7'=>$date["date7"],
            'SevenGuRC'=>$SevenGuRC,
            'SevenBuRC'=>$SevenBuRC,
        ];

        return $SevenRC;
    }



    /**
     *  近三十天具体时间顾客和商家退款订单
     */
    public function ThirtyRefundOrderSe($shopid,$status)
    {
        $order=db("order")
            ->field("id,status,add_time")
            ->where("shop_id=".$shopid." and status=".$status." and date_format(date_sub(now(), interval 30 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($order);$i++){
            $order[$i]["add_time"]=date("Y-m-d",$order[$i]["add_time"]);
        }

        //调用另一个控制器的查询时间的方法
        $Goodsanalysis=new Goodsanalysis();
        $date=$Goodsanalysis->ThirtyTime();
        $RefundCount1=seCount($order,$date["date1"],"add_time",$RefundCount1=0);
        $RefundCount5=seCount($order,$date["date5"],"add_time",$RefundCount5=0);
        $RefundCount10=seCount($order,$date["date10"],"add_time",$RefundCount10=0);
        $RefundCount15=seCount($order,$date["date15"],"add_time",$RefundCount15=0);
        $RefundCount20=seCount($order,$date["date20"],"add_time",$RefundCount20=0);
        $RefundCount25=seCount($order,$date["date25"],"add_time",$RefundCount25=0);
        $RefundCount30=seCount($order,$date["date30"],"add_time",$RefundCount30=0);

        $SevenRC=[
            'RefundCount1'=>$RefundCount1,
            'RefundCount5'=>$RefundCount5,
            'RefundCount10'=>$RefundCount10,
            'RefundCount15'=>$RefundCount15,
            'RefundCount20'=>$RefundCount20,
            'RefundCount25'=>$RefundCount25,
            'RefundCount30'=>$RefundCount30,
        ];
        return $SevenRC;
    }

    /**
     * 近三十天具体时间顾客和商家退款订单
     */
    public function ThirtyRefundOrder($shopid)
    {
        //顾客退款
        $ThirtyGuRC=$this->ThirtyRefundOrderSe($shopid,8);
        //商家退款
        $ThirtyBuRC=$this->ThirtyRefundOrderSe($shopid,9);
        //调用另一个控制器的查询时间的方法
        $Goodsanalysis=new Goodsanalysis();
        $date=$Goodsanalysis->ThirtyTime();
        $ThirtyRC=[
            'date1'=>$date["date1"],
            'date5'=>$date["date5"],
            'date10'=>$date["date10"],
            'date15'=>$date["date15"],
            'date20'=>$date["date20"],
            'date25'=>$date["date25"],
            'date30'=>$date["date30"],
            'ThirtyGuRC'=>$ThirtyGuRC,
            'ThirtyBuRC'=>$ThirtyBuRC,
        ];

        return $ThirtyRC;
    }

    /**
     * 查询所有的无效订单
     */
    public function SeAllInvalid($shopid)
    {
        $InvalidOrder=db("order")
            ->field("add_time,status")
            ->where("shop_id=".$shopid." and (status=8 or status=9)")
            ->select();
        for($i=0;$i<count($InvalidOrder);$i++){
            $InvalidOrder[$i]["add_time"]=date("Y-m-d H:m:s",$InvalidOrder[$i]["add_time"]);
        }
        return $InvalidOrder;
    }

    /**
     * 昨日无效订单数据整合
     */
    public function YesInvalidData(Request $request)
    {
        $shopid=$request->param("shopid");
        //昨日无效订单总金额
        $YesInvalidTotal=$this->YesInvalidTotal($shopid);
        //昨日顾客退款金额/次数
        $YesUesrRM=$this->YesUesrRM($shopid);
        //昨日商家退款金额/次数
        $YesBuinessRM=$this->YesBuinessRM($shopid);
        //昨日无效订单顾客与商家趋势图
        $YesRefundOrder=$this->YesRefundOrder($shopid);
        //近七日无效订单顾客与商家趋势图
        $SevenRefundOrder=$this->SevenRefundOrder($shopid);
        //近三十日无效订单顾客与商家趋势图
        $ThirtyRefundOrder=$this->ThirtyRefundOrder($shopid);
        //历史无效订单
        $SeAllInvalid=$this->SeAllInvalid($shopid);

        $YesInvalidData=[
            'YesInvalidTotal'=>$YesInvalidTotal,
            'YesUesrRM'=>$YesUesrRM,
            'YesBuinessRM'=>$YesBuinessRM,
            'YesRefundOrder'=>$YesRefundOrder,
            'SevenRefundOrder'=>$SevenRefundOrder,
            'ThirtyRefundOrder'=>$ThirtyRefundOrder,
            'SeAllInvalid'=>$SeAllInvalid
        ];
        return $this->message($YesInvalidData,"成功",2);
    }


}















