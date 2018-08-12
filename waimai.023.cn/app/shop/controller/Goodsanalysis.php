<?php
/**
 * 顾客分析
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/24
 * Time: 19:10
 */

namespace app\shop\controller;
use think\Request;
use think\Db;
class Goodsanalysis extends Index{

    //根据商品名查询总价
    public function seTotalPrice($goods_name)
    {
        $total=db::view("order_goods","goods_id,total","order_goods.goods_id=goods.id")
            ->view("goods","id,goods_name")
            ->where("goods.goods_name",$goods_name[0])
            ->sum("total");
        return number_format($total,2);
    }

    /**
     * 根据时间销售额排行
     */
    public function TimeSaleVolume($shopid,$typeid)
    {
        $order=db::view("order","id","order.id=order_goods.order_id")
            ->view("order_goods","order_id,goods_id,num,total")
            ->view("goods","goods_name","order_goods.goods_id=goods.id")
            ->where("order.shop_id=".$shopid." and date_format(date_sub(now(), interval ".$typeid." day),'%Y-%m-%d') <= date_format(from_unixtime(order.delivery_time),'%Y-%m-%d')")
            ->column("goods_name");
        $goodsNum=array_count_values($order);
        $sort=sort_with_keyName($goodsNum);
        $sortNum=array_slice($sort,0,3);
        //排行榜第一的销售额
        $goodsName1=array_keys(array_slice($sort,0,1));
        $total1=$this->seTotalPrice($goodsName1);
        //排行榜第二的销售额
        $goodsName2=array_keys(array_slice($sort,1,1));
        $total2=$this->seTotalPrice($goodsName2);
        //排行榜第三的销售额
        $goodsName3=array_keys(array_slice($sort,2,1));
        $total3=$this->seTotalPrice($goodsName3);

        //统计的销售额排行
        $SalesRankings=array();
        if(count($order)!=0){
            $SalesRankings=[
                'goodsName1'=>$goodsName1[0],
                'total1'=>$total1,
                'goodsName2'=>$goodsName2[0],
                'total2'=>$total2,
                'goodsName3'=>$goodsName3[0],
                'total3'=>$total3,
            ];
        }
        return $SalesRankings;
    }

    /**
     * 昨日销售额排行
     */
    public function YesSalesVolume($shopid)
    {
        $YesSalesRankings=$this->TimeSaleVolume($shopid,1);
        return $YesSalesRankings;
    }

    /**
     *近七日销售额排行
     */
    public function SevenSalesVolume($shopid)
    {
        $SevenSalesRankings=$this->TimeSaleVolume($shopid,7);
        return $SevenSalesRankings;
    }
    /**
     *近三十日销售额排行
     */
    public function ThirtySalesVolume($shopid)
    {
        $ThirtySalesRankings=$this->TimeSaleVolume($shopid,30);
        return $ThirtySalesRankings;
    }



    /**
     * 根据时间的销售排行(有待修改)
     */
    public function TimeSales($shopid,$typeid)
    {
        $order=db::view("order","id","order.id=order_goods.order_id")
            ->view("order_goods","order_id,goods_id,num,total")
            ->view("goods","goods_name","order_goods.goods_id=goods.id")
            ->where("order.shop_id=".$shopid." and date_format(date_sub(now(), interval ".$typeid." day),'%Y-%m-%d') <= date_format(from_unixtime(order.delivery_time),'%Y-%m-%d')")
            ->order("num desc")
            ->column("goods_name");
        $goodsNum=array_count_values($order);
        $sort=sort_with_keyName($goodsNum);
        $sortNum=array_slice($sort,0,3);

        /*$num=db::view("order","id","order.id=order_goods.order_id")
            ->view("order_goods","id,order_id,goods_id,num,total")
            ->view("goods","goods_name","order_goods.goods_id=goods.id")
            ->where("order.shop_id=".$shopid." and date_format(date_sub(now(), interval ".$typeid." day),'%Y-%m-%d') <= date_format(from_unixtime(order.delivery_time),'%Y-%m-%d')")
            ->order("num desc")
            ->limit(3)
            ->select();
        //订单排行榜第一
        $OrderRank1=array_values(array_slice($sort,0,1));
        //订单排行榜第二
        $OrderRank2=array_values(array_slice($sort,1,1));
        //数量排行第一与订单排行第一比较
        if($num[0]["num"]>$OrderRank1[0]){
            $GoodsName1=$num[0]["goods_name"];
            $OrderNum1=$num[0]["num"];
        }else if($num[0]["num"]<$OrderRank1[0] && $num[0]["num"]>$OrderRank2[0]) {
            $GoodsName1 = array_keys(array_slice($sort, 0, 1));
            $OrderNum1 = $OrderRank1;
        }else if($num[0]["num"]==$OrderRank1[0]){

        }

        //订单排行榜第三
        $OrderRank3=array_values(array_slice($sort,2,1));
        //数量排行第二与订单排行第二比较
        if($num[1]["num"]>$OrderRank2[0]){
            $GoodsName2=$num[1]["goods_name"];
            $OrderNum2=$num[1]["num"];
        }else if($num[1]["num"]<=$OrderRank2[0] && $num[1]["num"]>$OrderRank3[0]){
            $GoodsName2=array_keys(array_slice($sort,1,1))[0];
            $OrderNum2=$OrderRank2[0];
        }

        //数量排行第三与订单排行第三比较
        if($num[2]["num"]>$OrderRank3[0]){
            $GoodsName3=$num[2]["goods_name"];
            $OrderNum3=$num[2]["num"];
        }else if($num[2]["num"]<=$OrderRank3[0]){
            $GoodsName3=array_keys(array_slice($sort,2,1))[0];
            $OrderNum3=$OrderRank3[0];
        }*/

        //排行榜第一的销售
        $goodsName1=array_keys(array_slice($sort,0,1));
        $order1=array_values(array_slice($sort,0,1));

        //排行榜第二的销售
        $goodsName2=array_keys(array_slice($sort,1,1));
        $order2=array_values(array_slice($sort,1,1));
        //排行榜第三的销售
        $goodsName3=array_keys(array_slice($sort,2,1));
        $order3=array_values(array_slice($sort,2,1));

        //统计销量排行
        $SalesRankings=array();
        if(count($order)!=0){
            $SalesRankings=[
                'goodsName1'=>$goodsName1[0],
                'order1'=>$order1[0],
                'goodsName2'=>$goodsName2[0],
                'order2'=>$order2[0],
                'goodsName3'=>$goodsName3[0],
                'order3'=>$order3[0],
            ];
        }
        return $SalesRankings;
    }

    /**
     * 昨天的销量排行
     */
    public function YesSales($shopid)
    {
        $YesSalesRankings=$this->TimeSales($shopid,1);
        return $YesSalesRankings;
    }
    /**
     * 七日的销量排行
     */
    public function SevenSales($shopid)
    {
        $SevenSalesRankings=$this->TimeSales($shopid,7);
        return $SevenSalesRankings;
    }
    /**
     * 三十日的销量排行
     */
    public function ThirtySales($shopid)
    {
        $ThirtySalesRankings=$this->TimeSales($shopid,30);
        return $ThirtySalesRankings;
    }

    /**
     * 昨日时间段
     */
    public function YesTime()
    {
        $date0 = date('H', strtotime(-date('H') ."hours"));
        $date4 = date('H', strtotime(-date('H')+4 ."hours"));
        $date8 = date('H', strtotime(-date('H')+8 ."hours"));
        $date12 = date('H', strtotime(-date('H')+12 ."hours"));
        $date16 = date('H', strtotime(-date('H')+16 ."hours"));
        $date20 = date('H', strtotime(-date('H')+20 ."hours"));
        $date24 = date('H', strtotime(-date('H')+24 ."hours"));
        $ArrDate=[
            'date0'=>$date0,
            'date4'=>$date4,
            'date8'=>$date8,
            'date12'=>$date12,
            'date16'=>$date16,
            'date20'=>$date20,
            'date24'=>$date24,
        ];
        return $ArrDate;
    }

    /**
     * 查询一个值在二维数组特定值中出现的
     * @param $key
     * @param $date
     * @return array
     */
    public function TimeCount($key,$date)
    {
        $SalesVolume=array();
        foreach ($key as $item){
            if ($item['delivery_time'] == $date){
                $SalesVolume[]=$item;
            }
        }
        return $SalesVolume;
    }
    /**
     * 通过名字查询时间以及总价
     */
    public function TimeandTotal($TimeSaleVolume)
    {
        $Volume=db::view("order_goods","goods_id,total","order_goods.goods_id=goods.id")
            ->view("order","id,delivery_time","order_goods.order_id=order.id")
            ->view("goods","id,goods_name")
            ->where("goods.goods_name",$TimeSaleVolume)
            ->select();
        for($i=0;$i<count($Volume);$i++){
            $Volume[$i]["delivery_time"]=date("H",$Volume[$i]["delivery_time"]);
        }
        return $Volume;
    }


    /**
     * 计算昨天的具体时间段营业额
     */
    public function YesSalesDatas($shopid)
    {

        $TimeSaleVolume=$this->TimeSaleVolume($shopid,1);//查询昨天的排行榜
        $date=$this->YesTime();//查询昨天的具体时间

        $FirstVolume=$this->TimeandTotal($TimeSaleVolume["goodsName1"]);//查询昨天的排行榜第一的商品

        $FSalesVolumeArr0=$this->TimeCount($FirstVolume,$date["date0"]);//查询昨天排行榜第一具体时间的数据
        $FSalesVolume0=number_format(array_sum(array_column($FSalesVolumeArr0,"total")),2);//具体时间数据总的营业额

        $FSalesVolumeArr4=$this->TimeCount($FirstVolume,$date["date4"]);
        $FSalesVolume4=number_format(array_sum(array_column($FSalesVolumeArr4,"total")),2);

        $FSalesVolumeArr8=$this->TimeCount($FirstVolume,$date["date8"]);
        $FSalesVolume8=number_format(array_sum(array_column($FSalesVolumeArr8,"total")),2);

        $FSalesVolumeArr12=$this->TimeCount($FirstVolume,$date["date12"]);
        $FSalesVolume12=number_format(array_sum(array_column($FSalesVolumeArr12,"total")),2);

        $FSalesVolumeArr16=$this->TimeCount($FirstVolume,$date["date16"]);
        $FSalesVolume16=number_format(array_sum(array_column($FSalesVolumeArr16,"total")),2);

        $FSalesVolumeArr20=$this->TimeCount($FirstVolume,$date["date20"]);
        $FSalesVolume20=number_format(array_sum(array_column($FSalesVolumeArr20,"total")),2);

        $FSalesVolumeArr24=$this->TimeCount($FirstVolume,$date["date24"]);
        $FSalesVolume24=number_format(array_sum(array_column($FSalesVolumeArr24,"total")),2);

        //统计排行第一的营业额
        $FirstRank=[
            'FSalesVolume0'=>$FSalesVolume0,
            'FSalesVolume4'=>$FSalesVolume4,
            'FSalesVolume8'=>$FSalesVolume8,
            'FSalesVolume12'=>$FSalesVolume12,
            'FSalesVolume16'=>$FSalesVolume16,
            'FSalesVolume20'=>$FSalesVolume20,
            'FSalesVolume24'=>$FSalesVolume24,
        ];
        //查询昨天的排行榜第二的商品
        $SecondVolume=$this->TimeandTotal($TimeSaleVolume["goodsName2"]);
        //查询昨天排行榜第二具体时间的数据
        $SSalesVolumeArr0=$this->TimeCount($SecondVolume,$date["date0"]);
        //具体时间数据总的营业额
        $SSalesVolume0=number_format(array_sum(array_column($SSalesVolumeArr0,"total")),2);

        $SSalesVolumeArr4=$this->TimeCount($SecondVolume,$date["date4"]);
        $SSalesVolume4=number_format(array_sum(array_column($SSalesVolumeArr4,"total")),2);

        $SSalesVolumeArr8=$this->TimeCount($SecondVolume,$date["date8"]);
        $SSalesVolume8=number_format(array_sum(array_column($SSalesVolumeArr8,"total")),2);

        $SSalesVolumeArr12=$this->TimeCount($SecondVolume,$date["date12"]);
        $SSalesVolume12=number_format(array_sum(array_column($SSalesVolumeArr12,"total")),2);

        $SSalesVolumeArr16=$this->TimeCount($SecondVolume,$date["date16"]);
        $SSalesVolume16=number_format(array_sum(array_column($SSalesVolumeArr16,"total")),2);

        $SSalesVolumeArr20=$this->TimeCount($SecondVolume,$date["date20"]);
        $SSalesVolume20=number_format(array_sum(array_column($SSalesVolumeArr20,"total")),2);

        $SSalesVolumeArr24=$this->TimeCount($SecondVolume,$date["date24"]);
        $SSalesVolume24=number_format(array_sum(array_column($SSalesVolumeArr24,"total")),2);

        //统计排行第一的营业额
        $SecondRank=[
            'SSalesVolume0'=>$SSalesVolume0,
            'SSalesVolume4'=>$SSalesVolume4,
            'SSalesVolume8'=>$SSalesVolume8,
            'SSalesVolume12'=>$SSalesVolume12,
            'SSalesVolume16'=>$SSalesVolume16,
            'SSalesVolume20'=>$SSalesVolume20,
            'SSalesVolume24'=>$SSalesVolume24,
        ];

        //查询昨天的排行榜第三的商品
        $ThirtyVolume=$this->TimeandTotal($TimeSaleVolume["goodsName3"]);
        //查询昨天排行榜第三具体时间的数据
        $TSalesVolumeArr0=$this->TimeCount($ThirtyVolume,$date["date0"]);
        //具体时间数据总的营业额
        $TSalesVolume0=number_format(array_sum(array_column($TSalesVolumeArr0,"total")),2);

        $TSalesVolumeArr4=$this->TimeCount($ThirtyVolume,$date["date4"]);
        $TSalesVolume4=number_format(array_sum(array_column($TSalesVolumeArr4,"total")),2);

        $TSalesVolumeArr8=$this->TimeCount($ThirtyVolume,$date["date8"]);
        $TSalesVolume8=number_format(array_sum(array_column($TSalesVolumeArr8,"total")),2);

        $TSalesVolumeArr12=$this->TimeCount($ThirtyVolume,$date["date12"]);
        $TSalesVolume12=number_format(array_sum(array_column($TSalesVolumeArr12,"total")),2);

        $TSalesVolumeArr16=$this->TimeCount($ThirtyVolume,$date["date16"]);
        $TSalesVolume16=number_format(array_sum(array_column($TSalesVolumeArr16,"total")),2);

        $TSalesVolumeArr20=$this->TimeCount($ThirtyVolume,$date["date20"]);
        $TSalesVolume20=number_format(array_sum(array_column($TSalesVolumeArr20,"total")),2);

        $TSalesVolumeArr24=$this->TimeCount($ThirtyVolume,$date["date24"]);
        $TSalesVolume24=number_format(array_sum(array_column($TSalesVolumeArr24,"total")),2);

        //统计排行第一的营业额
        $ThirtyRank=[
            'TSalesVolume0'=>$TSalesVolume0,
            'TSalesVolume4'=>$TSalesVolume4,
            'TSalesVolume8'=>$TSalesVolume8,
            'TSalesVolume12'=>$TSalesVolume12,
            'TSalesVolume16'=>$TSalesVolume16,
            'TSalesVolume20'=>$TSalesVolume20,
            'TSalesVolume24'=>$TSalesVolume24,
        ];

        return $this->message($ThirtyRank,"成功",2);
    }
    /**
     * 近七日的具体时间
     */
    public function SevenTime()
    {
        $date1 = date('Y-m-d', strtotime('-1 days'));
        $date2 = date('Y-m-d', strtotime('-2 days'));
        $date3 = date('Y-m-d', strtotime('-3 days'));
        $date4 = date('Y-m-d', strtotime('-4 days'));
        $date5 = date('Y-m-d', strtotime('-5 days'));
        $date6 = date('Y-m-d', strtotime('-6 days'));
        $date7 = date('Y-m-d', strtotime('-7 days'));

        $arrTime=[
            'date1'=>$date1,
            'date2'=>$date2,
            'date3'=>$date3,
            'date4'=>$date4,
            'date5'=>$date5,
            'date6'=>$date6,
            'date7'=>$date7,

        ];
        return $arrTime;
    }
    /**
     * 通过名字查询时间以及总价
     */
    public function TimeandTotal2($TimeSaleVolume)
    {
        $Volume=db::view("order_goods","goods_id,total","order_goods.goods_id=goods.id")
            ->view("order","id,delivery_time","order_goods.order_id=order.id")
            ->view("goods","id,goods_name")
            ->where("goods.goods_name",$TimeSaleVolume)
            ->select();
        for($i=0;$i<count($Volume);$i++){
            $Volume[$i]["delivery_time"]=date("Y-m-d",$Volume[$i]["delivery_time"]);
        }
        return $Volume;
    }
    /**
     * 计算近七日的具体时间段营业额
     */
    public function SevenSalesDatas($shopid)
    {

        $TimeSaleVolume=$this->TimeSaleVolume($shopid,7);//查询近七日的排行榜
        $date=$this->SevenTime();//查询近七日的具体时间

        $FirstVolume=$this->TimeandTotal2($TimeSaleVolume["goodsName1"]);//查询近七日的排行榜第一的商品
        $FSalesVolumeArr1=$this->TimeCount($FirstVolume,$date["date1"]);//查询近七日排行榜第一具体时间的数据
        $FSalesVolume1=number_format(array_sum(array_column($FSalesVolumeArr1,"total")),2);//具体时间数据总的营业额

        $FSalesVolumeArr2=$this->TimeCount($FirstVolume,$date["date2"]);
        $FSalesVolume2=number_format(array_sum(array_column($FSalesVolumeArr2,"total")),2);

        $FSalesVolumeArr3=$this->TimeCount($FirstVolume,$date["date3"]);
        $FSalesVolume3=number_format(array_sum(array_column($FSalesVolumeArr3,"total")),2);

        $FSalesVolumeArr4=$this->TimeCount($FirstVolume,$date["date4"]);
        $FSalesVolume4=number_format(array_sum(array_column($FSalesVolumeArr4,"total")),2);

        $FSalesVolumeArr5=$this->TimeCount($FirstVolume,$date["date5"]);
        $FSalesVolume5=number_format(array_sum(array_column($FSalesVolumeArr5,"total")),2);

        $FSalesVolumeArr6=$this->TimeCount($FirstVolume,$date["date6"]);
        $FSalesVolume6=number_format(array_sum(array_column($FSalesVolumeArr6,"total")),2);

        $FSalesVolumeArr7=$this->TimeCount($FirstVolume,$date["date7"]);
        $FSalesVolume7=number_format(array_sum(array_column($FSalesVolumeArr7,"total")),2);

        //统计排行第一的营业额
        $FirstRank=[
            'FSalesVolume1'=>$FSalesVolume1,
            'FSalesVolume2'=>$FSalesVolume2,
            'FSalesVolume3'=>$FSalesVolume3,
            'FSalesVolume4'=>$FSalesVolume4,
            'FSalesVolume5'=>$FSalesVolume5,
            'FSalesVolume6'=>$FSalesVolume6,
            'FSalesVolume7'=>$FSalesVolume7,
        ];

        //查询近七日的排行榜第二的商品
        $SecondVolume=$this->TimeandTotal2($TimeSaleVolume["goodsName2"]);
        //查询近七日排行榜第二具体时间的数据
        $SSalesVolumeArr1=$this->TimeCount($SecondVolume,$date["date1"]);
        //具体时间数据总的营业额
        $SSalesVolume1=number_format(array_sum(array_column($SSalesVolumeArr1,"total")),2);

        $SSalesVolumeArr2=$this->TimeCount($SecondVolume,$date["date2"]);
        $SSalesVolume2=number_format(array_sum(array_column($SSalesVolumeArr2,"total")),2);

        $SSalesVolumeArr3=$this->TimeCount($SecondVolume,$date["date3"]);
        $SSalesVolume3=number_format(array_sum(array_column($SSalesVolumeArr3,"total")),2);

        $SSalesVolumeArr4=$this->TimeCount($SecondVolume,$date["date4"]);
        $SSalesVolume4=number_format(array_sum(array_column($SSalesVolumeArr4,"total")),2);

        $SSalesVolumeArr5=$this->TimeCount($SecondVolume,$date["date5"]);
        $SSalesVolume5=number_format(array_sum(array_column($SSalesVolumeArr5,"total")),2);

        $SSalesVolumeArr6=$this->TimeCount($SecondVolume,$date["date6"]);
        $SSalesVolume6=number_format(array_sum(array_column($SSalesVolumeArr6,"total")),2);

        $SSalesVolumeArr7=$this->TimeCount($SecondVolume,$date["date7"]);
        $SSalesVolume7=number_format(array_sum(array_column($SSalesVolumeArr7,"total")),2);

        //统计排行第一的营业额
        $SecondRank=[
            'SSalesVolume1'=>$SSalesVolume1,
            'SSalesVolume2'=>$SSalesVolume2,
            'SSalesVolume3'=>$SSalesVolume3,
            'SSalesVolume4'=>$SSalesVolume4,
            'SSalesVolume5'=>$SSalesVolume5,
            'SSalesVolume6'=>$SSalesVolume6,
            'SSalesVolume7'=>$SSalesVolume7,
        ];

        //查询昨天的排行榜第三的商品
        $ThirtyVolume=$this->TimeandTotal2($TimeSaleVolume["goodsName3"]);
        //查询昨天排行榜第三具体时间的数据
        $TSalesVolumeArr1=$this->TimeCount($ThirtyVolume,$date["date1"]);
        //具体时间数据总的营业额
        $TSalesVolume1=number_format(array_sum(array_column($TSalesVolumeArr1,"total")),2);

        $TSalesVolumeArr2=$this->TimeCount($ThirtyVolume,$date["date2"]);
        $TSalesVolume2=number_format(array_sum(array_column($TSalesVolumeArr2,"total")),2);

        $TSalesVolumeArr3=$this->TimeCount($ThirtyVolume,$date["date3"]);
        $TSalesVolume3=number_format(array_sum(array_column($TSalesVolumeArr3,"total")),2);

        $TSalesVolumeArr4=$this->TimeCount($ThirtyVolume,$date["date4"]);
        $TSalesVolume4=number_format(array_sum(array_column($TSalesVolumeArr4,"total")),2);

        $TSalesVolumeArr5=$this->TimeCount($ThirtyVolume,$date["date5"]);
        $TSalesVolume5=number_format(array_sum(array_column($TSalesVolumeArr5,"total")),2);

        $TSalesVolumeArr6=$this->TimeCount($ThirtyVolume,$date["date6"]);
        $TSalesVolume6=number_format(array_sum(array_column($TSalesVolumeArr6,"total")),2);

        $TSalesVolumeArr7=$this->TimeCount($ThirtyVolume,$date["date7"]);
        $TSalesVolume7=number_format(array_sum(array_column($TSalesVolumeArr7,"total")),2);

        //统计排行第一的营业额
        $ThirtyRank=[
            'TSalesVolume1'=>$TSalesVolume1,
            'TSalesVolume2'=>$TSalesVolume2,
            'TSalesVolume3'=>$TSalesVolume3,
            'TSalesVolume4'=>$TSalesVolume4,
            'TSalesVolume5'=>$TSalesVolume5,
            'TSalesVolume6'=>$TSalesVolume6,
            'TSalesVolume7'=>$TSalesVolume7,
        ];

        return $this->message($ThirtyRank,"成功",2);
    }
    /**
     * 近三十日的具体时间
     */
    public function ThirtyTime()
    {
        $date1 = date('Y-m-d', strtotime('-1 days'));
        $date5 = date('Y-m-d', strtotime('-5 days'));
        $date10 = date('Y-m-d', strtotime('-10 days'));
        $date15 = date('Y-m-d', strtotime('-15 days'));
        $date20 = date('Y-m-d', strtotime('-20 days'));
        $date25 = date('Y-m-d', strtotime('-25 days'));
        $date30 = date('Y-m-d', strtotime('-30 days'));

        $arrTime=[
            'date1'=>$date1,
            'date5'=>$date5,
            'date10'=>$date10,
            'date15'=>$date15,
            'date20'=>$date20,
            'date25'=>$date25,
            'date30'=>$date30,

        ];
        return $arrTime;
    }
    /**
     * 计算近三十日的具体时间段营业额
     */
    public function ThirtySalesDatas($shopid)
    {

        $TimeSaleVolume=$this->TimeSaleVolume($shopid,30);//查询近三十日的排行榜
        $date=$this->SevenTime();//查询近三十日的具体时间

        $FirstVolume=$this->TimeandTotal2($TimeSaleVolume["goodsName1"]);//查询近三十日的排行榜第一的商品
        $FSalesVolumeArr1=$this->TimeCount($FirstVolume,$date["date1"]);//查询近三十日排行榜第一具体时间的数据
        $FSalesVolume1=number_format(array_sum(array_column($FSalesVolumeArr1,"total")),2);//具体时间数据总的营业额

        $FSalesVolumeArr2=$this->TimeCount($FirstVolume,$date["date2"]);
        $FSalesVolume2=number_format(array_sum(array_column($FSalesVolumeArr2,"total")),2);

        $FSalesVolumeArr3=$this->TimeCount($FirstVolume,$date["date3"]);
        $FSalesVolume3=number_format(array_sum(array_column($FSalesVolumeArr3,"total")),2);

        $FSalesVolumeArr4=$this->TimeCount($FirstVolume,$date["date4"]);
        $FSalesVolume4=number_format(array_sum(array_column($FSalesVolumeArr4,"total")),2);

        $FSalesVolumeArr5=$this->TimeCount($FirstVolume,$date["date5"]);
        $FSalesVolume5=number_format(array_sum(array_column($FSalesVolumeArr5,"total")),2);

        $FSalesVolumeArr6=$this->TimeCount($FirstVolume,$date["date6"]);
        $FSalesVolume6=number_format(array_sum(array_column($FSalesVolumeArr6,"total")),2);

        $FSalesVolumeArr7=$this->TimeCount($FirstVolume,$date["date7"]);
        $FSalesVolume7=number_format(array_sum(array_column($FSalesVolumeArr7,"total")),2);

        //统计排行第一的营业额
        $FirstRank=[
            'FSalesVolume1'=>$FSalesVolume1,
            'FSalesVolume2'=>$FSalesVolume2,
            'FSalesVolume3'=>$FSalesVolume3,
            'FSalesVolume4'=>$FSalesVolume4,
            'FSalesVolume5'=>$FSalesVolume5,
            'FSalesVolume6'=>$FSalesVolume6,
            'FSalesVolume7'=>$FSalesVolume7,
        ];

        //查询近七日的排行榜第二的商品
        $SecondVolume=$this->TimeandTotal2($TimeSaleVolume["goodsName2"]);
        //查询近七日排行榜第二具体时间的数据
        $SSalesVolumeArr1=$this->TimeCount($SecondVolume,$date["date1"]);
        //具体时间数据总的营业额
        $SSalesVolume1=number_format(array_sum(array_column($SSalesVolumeArr1,"total")),2);

        $SSalesVolumeArr2=$this->TimeCount($SecondVolume,$date["date2"]);
        $SSalesVolume2=number_format(array_sum(array_column($SSalesVolumeArr2,"total")),2);

        $SSalesVolumeArr3=$this->TimeCount($SecondVolume,$date["date3"]);
        $SSalesVolume3=number_format(array_sum(array_column($SSalesVolumeArr3,"total")),2);

        $SSalesVolumeArr4=$this->TimeCount($SecondVolume,$date["date4"]);
        $SSalesVolume4=number_format(array_sum(array_column($SSalesVolumeArr4,"total")),2);

        $SSalesVolumeArr5=$this->TimeCount($SecondVolume,$date["date5"]);
        $SSalesVolume5=number_format(array_sum(array_column($SSalesVolumeArr5,"total")),2);

        $SSalesVolumeArr6=$this->TimeCount($SecondVolume,$date["date6"]);
        $SSalesVolume6=number_format(array_sum(array_column($SSalesVolumeArr6,"total")),2);

        $SSalesVolumeArr7=$this->TimeCount($SecondVolume,$date["date7"]);
        $SSalesVolume7=number_format(array_sum(array_column($SSalesVolumeArr7,"total")),2);

        //统计排行第一的营业额
        $SecondRank=[
            'SSalesVolume1'=>$SSalesVolume1,
            'SSalesVolume2'=>$SSalesVolume2,
            'SSalesVolume3'=>$SSalesVolume3,
            'SSalesVolume4'=>$SSalesVolume4,
            'SSalesVolume5'=>$SSalesVolume5,
            'SSalesVolume6'=>$SSalesVolume6,
            'SSalesVolume7'=>$SSalesVolume7,
        ];

        //查询昨天的排行榜第三的商品
        $ThirtyVolume=$this->TimeandTotal2($TimeSaleVolume["goodsName3"]);
        //查询昨天排行榜第三具体时间的数据
        $TSalesVolumeArr1=$this->TimeCount($ThirtyVolume,$date["date1"]);
        //具体时间数据总的营业额
        $TSalesVolume1=number_format(array_sum(array_column($TSalesVolumeArr1,"total")),2);

        $TSalesVolumeArr2=$this->TimeCount($ThirtyVolume,$date["date2"]);
        $TSalesVolume2=number_format(array_sum(array_column($TSalesVolumeArr2,"total")),2);

        $TSalesVolumeArr3=$this->TimeCount($ThirtyVolume,$date["date3"]);
        $TSalesVolume3=number_format(array_sum(array_column($TSalesVolumeArr3,"total")),2);

        $TSalesVolumeArr4=$this->TimeCount($ThirtyVolume,$date["date4"]);
        $TSalesVolume4=number_format(array_sum(array_column($TSalesVolumeArr4,"total")),2);

        $TSalesVolumeArr5=$this->TimeCount($ThirtyVolume,$date["date5"]);
        $TSalesVolume5=number_format(array_sum(array_column($TSalesVolumeArr5,"total")),2);

        $TSalesVolumeArr6=$this->TimeCount($ThirtyVolume,$date["date6"]);
        $TSalesVolume6=number_format(array_sum(array_column($TSalesVolumeArr6,"total")),2);

        $TSalesVolumeArr7=$this->TimeCount($ThirtyVolume,$date["date7"]);
        $TSalesVolume7=number_format(array_sum(array_column($TSalesVolumeArr7,"total")),2);

        //统计排行第一的营业额
        $ThirtyRank=[
            'TSalesVolume1'=>$TSalesVolume1,
            'TSalesVolume2'=>$TSalesVolume2,
            'TSalesVolume3'=>$TSalesVolume3,
            'TSalesVolume4'=>$TSalesVolume4,
            'TSalesVolume5'=>$TSalesVolume5,
            'TSalesVolume6'=>$TSalesVolume6,
            'TSalesVolume7'=>$TSalesVolume7,
        ];

        return $this->message($ThirtyRank,"成功",2);
    }
    /**
     * 通过名字查询时间
     */
    public function SaleTime($TimeSaleVolume)
    {
        $Volume=db::view("order_goods","goods_id,num","order_goods.goods_id=goods.id")
            ->view("order","id,delivery_time","order_goods.order_id=order.id")
            ->view("goods","id,goods_name")
            ->where("goods.goods_name",$TimeSaleVolume)
            ->select();
        for($i=0;$i<count($Volume);$i++){
            $Volume[$i]["delivery_time"]=date("H",$Volume[$i]["delivery_time"]);
        }
        return $Volume;
    }
    /**
     * 通过名字查询时间
     */
    public function SaleTime2($TimeSaleVolume)
    {
        $Volume=db::view("order_goods","goods_id,num","order_goods.goods_id=goods.id")
            ->view("order","id,delivery_time","order_goods.order_id=order.id")
            ->view("goods","id,goods_name")
            ->where("goods.goods_name",$TimeSaleVolume)
            ->select();
        for($i=0;$i<count($Volume);$i++){
            $Volume[$i]["delivery_time"]=date("Y-m-d",$Volume[$i]["delivery_time"]);
        }
        return $Volume;
    }
    /**
     *查询昨天的销售量
     */
    public function YesSaleRanks($shopid)
    {
        //查询昨天总的销售
        $SaleRanks=$this->TimeSales($shopid,1);
        //查询昨天的具体时间
        $date=$this->YesTime();

        //查询昨天销量排行榜第一的商品
        $FirstSale=$this->SaleTime($SaleRanks["goodsName1"]);

        //查询昨天排行榜第一在某个时间段的销量
        $FSalesArr0=$this->TimeCount($FirstSale,$date["date0"]);
        $FSaleNum0=array_sum(array_column($FSalesArr0,"num"));

        $FSalesArr4=$this->TimeCount($FirstSale,$date["date4"]);
        $FSaleNum4=array_sum(array_column($FSalesArr4,"num"));

        $FSalesArr8=$this->TimeCount($FirstSale,$date["date8"]);
        $FSaleNum8=array_sum(array_column($FSalesArr8,"num"));

        $FSalesArr12=$this->TimeCount($FirstSale,$date["date12"]);
        $FSaleNum12=array_sum(array_column($FSalesArr12,"num"));

        $FSalesArr16=$this->TimeCount($FirstSale,$date["date16"]);
        $FSaleNum16=array_sum(array_column($FSalesArr16,"num"));

        $FSalesArr20=$this->TimeCount($FirstSale,$date["date20"]);
        $FSaleNum20=array_sum(array_column($FSalesArr20,"num"));

        $FSalesArr24=$this->TimeCount($FirstSale,$date["date24"]);
        $FSaleNum24=array_sum(array_column($FSalesArr24,"num"));

        //统计昨日排行第一的销售趋势
        $YesFSale=[
            'FSaleNum0'=>$FSaleNum0,
            'FSaleNum4'=>$FSaleNum4,
            'FSaleNum8'=>$FSaleNum8,
            'FSaleNum12'=>$FSaleNum12,
            'FSaleNum16'=>$FSaleNum16,
            'FSaleNum20'=>$FSaleNum20,
            'FSaleNum24'=>$FSaleNum24,
        ];

        //查询昨天销量排行榜第二的商品
        $SecondSale=$this->SaleTime($SaleRanks["goodsName2"]);

        //查询昨天排行榜第二在某个时间段的销量
        $SSalesArr0=$this->TimeCount($SecondSale,$date["date0"]);
        $SSaleNum0=array_sum(array_column($SSalesArr0,"num"));

        $SSalesArr4=$this->TimeCount($SecondSale,$date["date4"]);
        $SSaleNum4=array_sum(array_column($SSalesArr4,"num"));

        $SSalesArr8=$this->TimeCount($SecondSale,$date["date8"]);
        $SSaleNum8=array_sum(array_column($SSalesArr8,"num"));

        $SSalesArr12=$this->TimeCount($SecondSale,$date["date12"]);
        $SSaleNum12=array_sum(array_column($SSalesArr12,"num"));

        $SSalesArr16=$this->TimeCount($SecondSale,$date["date16"]);
        $SSaleNum16=array_sum(array_column($SSalesArr16,"num"));

        $SSalesArr20=$this->TimeCount($SecondSale,$date["date20"]);
        $SSaleNum20=array_sum(array_column($SSalesArr20,"num"));

        $SSalesArr24=$this->TimeCount($SecondSale,$date["date24"]);
        $SSaleNum24=array_sum(array_column($SSalesArr24,"num"));

        //统计昨日排行第一的销售趋势
        $YesSSale=[
            'SSaleNum0'=>$SSaleNum0,
            'SSaleNum4'=>$SSaleNum4,
            'SSaleNum8'=>$SSaleNum8,
            'SSaleNum12'=>$SSaleNum12,
            'SSaleNum16'=>$SSaleNum16,
            'SSaleNum20'=>$SSaleNum20,
            'SSaleNum24'=>$SSaleNum24,
        ];

        //查询昨天销量排行榜第三的商品
        $ThirtySale=$this->SaleTime($SaleRanks["goodsName3"]);

        //查询昨天排行榜第三在某个时间段的销量
        $TSalesArr0=$this->TimeCount($ThirtySale,$date["date0"]);
        $TSaleNum0=array_sum(array_column($TSalesArr0,"num"));

        $TSalesArr4=$this->TimeCount($ThirtySale,$date["date4"]);
        $TSaleNum4=array_sum(array_column($TSalesArr4,"num"));

        $TSalesArr8=$this->TimeCount($ThirtySale,$date["date8"]);
        $TSaleNum8=array_sum(array_column($TSalesArr8,"num"));

        $TSalesArr12=$this->TimeCount($ThirtySale,$date["date12"]);
        $TSaleNum12=array_sum(array_column($TSalesArr12,"num"));

        $TSalesArr16=$this->TimeCount($ThirtySale,$date["date16"]);
        $TSaleNum16=array_sum(array_column($TSalesArr16,"num"));

        $TSalesArr20=$this->TimeCount($ThirtySale,$date["date20"]);
        $TSaleNum20=array_sum(array_column($TSalesArr20,"num"));

        $TSalesArr24=$this->TimeCount($ThirtySale,$date["date24"]);
        $TSaleNum24=array_sum(array_column($TSalesArr24,"num"));

        //统计昨日排行第一的销售趋势
        $YesTSale=[
            'TSaleNum0'=>$TSaleNum0,
            'TaleNum4'=>$TSaleNum4,
            'TSaleNum8'=>$TSaleNum8,
            'TSaleNum12'=>$TSaleNum12,
            'TSaleNum16'=>$TSaleNum16,
            'TSaleNum20'=>$TSaleNum20,
            'TSaleNum24'=>$TSaleNum24,
        ];

        //统计昨日销量排行榜前三的趋势
        $YesRankTrend=[
            'YesFSale'=>$YesFSale,
            'YesSSale'=>$YesSSale,
            'YesTSale'=>$YesTSale,
        ];
        return  $YesRankTrend;

    }

    /**
     *查询近七日的销售量
     */
    public function SevenSaleRanks($shopid)
    {
        //查询近七日总的销售
        $SaleRanks=$this->TimeSales($shopid,7);
        //查询近七日的具体时间
        $date=$this->SevenTime();

        //查询近七日销量排行榜第一的商品
        $FirstSale=$this->SaleTime2($SaleRanks["goodsName1"]);

        //查询近七日排行榜第一在某个时间段的销量
        $FSalesArr1=$this->TimeCount($FirstSale,$date["date1"]);
        $FSaleNum1=array_sum(array_column($FSalesArr1,"num"));

        $FSalesArr2=$this->TimeCount($FirstSale,$date["date2"]);
        $FSaleNum2=array_sum(array_column($FSalesArr2,"num"));

        $FSalesArr3=$this->TimeCount($FirstSale,$date["date3"]);
        $FSaleNum3=array_sum(array_column($FSalesArr3,"num"));

        $FSalesArr4=$this->TimeCount($FirstSale,$date["date4"]);
        $FSaleNum4=array_sum(array_column($FSalesArr4,"num"));

        $FSalesArr5=$this->TimeCount($FirstSale,$date["date5"]);
        $FSaleNum5=array_sum(array_column($FSalesArr5,"num"));

        $FSalesArr6=$this->TimeCount($FirstSale,$date["date6"]);
        $FSaleNum6=array_sum(array_column($FSalesArr6,"num"));

        $FSalesArr7=$this->TimeCount($FirstSale,$date["date7"]);
        $FSaleNum7=array_sum(array_column($FSalesArr7,"num"));

        //统计近七日排行第一的销售趋势
        $SevenFSale=[
            'FSaleNum1'=>$FSaleNum1,
            'FSaleNum2'=>$FSaleNum2,
            'FSaleNum3'=>$FSaleNum3,
            'FSaleNum4'=>$FSaleNum4,
            'FSaleNum5'=>$FSaleNum5,
            'FSaleNum6'=>$FSaleNum6,
            'FSaleNum7'=>$FSaleNum7,
        ];

        //查询近七日销量排行榜第二的商品
        $SecondSale=$this->SaleTime2($SaleRanks["goodsName2"]);

        //查询近七日排行榜第二在某个时间段的销量
        $SSalesArr1=$this->TimeCount($SecondSale,$date["date1"]);
        $SSaleNum1=array_sum(array_column($SSalesArr1,"num"));

        $SSalesArr2=$this->TimeCount($SecondSale,$date["date2"]);
        $SSaleNum2=array_sum(array_column($SSalesArr2,"num"));

        $SSalesArr3=$this->TimeCount($SecondSale,$date["date3"]);
        $SSaleNum3=array_sum(array_column($SSalesArr3,"num"));

        $SSalesArr4=$this->TimeCount($SecondSale,$date["date4"]);
        $SSaleNum4=array_sum(array_column($SSalesArr4,"num"));

        $SSalesArr5=$this->TimeCount($SecondSale,$date["date5"]);
        $SSaleNum5=array_sum(array_column($SSalesArr5,"num"));

        $SSalesArr6=$this->TimeCount($SecondSale,$date["date6"]);
        $SSaleNum6=array_sum(array_column($SSalesArr6,"num"));

        $SSalesArr7=$this->TimeCount($SecondSale,$date["date7"]);
        $SSaleNum7=array_sum(array_column($SSalesArr7,"num"));

        //统计近七日排行第一的销售趋势
        $SevenSSale=[
            'SSaleNum1'=>$SSaleNum1,
            'SSaleNum2'=>$SSaleNum2,
            'SSaleNum3'=>$SSaleNum3,
            'SSaleNum4'=>$SSaleNum4,
            'SSaleNum5'=>$SSaleNum5,
            'SSaleNum6'=>$SSaleNum6,
            'SSaleNum7'=>$SSaleNum7,
        ];

        //查询近七日销量排行榜第三的商品
        $ThirtySale=$this->SaleTime2($SaleRanks["goodsName3"]);

        //查询近七日排行榜第三在某个时间段的销量
        $TSalesArr1=$this->TimeCount($ThirtySale,$date["date1"]);
        $TSaleNum1=array_sum(array_column($TSalesArr1,"num"));

        $TSalesArr2=$this->TimeCount($ThirtySale,$date["date2"]);
        $TSaleNum2=array_sum(array_column($TSalesArr2,"num"));

        $TSalesArr3=$this->TimeCount($ThirtySale,$date["date3"]);
        $TSaleNum3=array_sum(array_column($TSalesArr3,"num"));

        $TSalesArr4=$this->TimeCount($ThirtySale,$date["date4"]);
        $TSaleNum4=array_sum(array_column($TSalesArr4,"num"));

        $TSalesArr5=$this->TimeCount($ThirtySale,$date["date5"]);
        $TSaleNum5=array_sum(array_column($TSalesArr5,"num"));

        $TSalesArr6=$this->TimeCount($ThirtySale,$date["date6"]);
        $TSaleNum6=array_sum(array_column($TSalesArr6,"num"));

        $TSalesArr7=$this->TimeCount($ThirtySale,$date["date7"]);
        $TSaleNum7=array_sum(array_column($TSalesArr7,"num"));

        //统计近七日排行第三的销售趋势
        $SevenTSale=[
            'TSaleNum1'=>$TSaleNum1,
            'TaleNum2'=>$TSaleNum2,
            'TSaleNum3'=>$TSaleNum3,
            'TSaleNum4'=>$TSaleNum4,
            'TSaleNum5'=>$TSaleNum5,
            'TSaleNum6'=>$TSaleNum6,
            'TSaleNum7'=>$TSaleNum7,
        ];

        //统计近七日销量排行榜前三的趋势
        $SevenRankTrend=[
            'date'=>[
                'date1'=>$date["date1"],
                'date2'=>$date["date2"],
                'date3'=>$date["date3"],
                'date4'=>$date["date4"],
                'date5'=>$date["date5"],
                'date6'=>$date["date6"],
                'date7'=>$date["date7"],
            ],
            'SevenFSale'=>$SevenFSale,
            'SevenSSale'=>$SevenSSale,
            'SevenTSale'=>$SevenTSale,
        ];
        return  $SevenRankTrend;
    }

    /**
     *查询近三十日的销售量
     */
    public function ThirtySaleRanks($shopid)
    {
        //查询近三十日总的销售
        $SaleRanks=$this->TimeSales($shopid,30);
        //查询近三十日的具体时间
        $date=$this->ThirtyTime();

        //查询近三十日销量排行榜第一的商品
        $FirstSale=$this->SaleTime2($SaleRanks["goodsName1"]);

        //查询近三十日排行榜第一在某个时间段的销量
        $FSalesArr1=$this->TimeCount($FirstSale,$date["date1"]);
        $FSaleNum1=array_sum(array_column($FSalesArr1,"num"));

        $FSalesArr5=$this->TimeCount($FirstSale,$date["date5"]);
        $FSaleNum5=array_sum(array_column($FSalesArr5,"num"));

        $FSalesArr10=$this->TimeCount($FirstSale,$date["date10"]);
        $FSaleNum10=array_sum(array_column($FSalesArr10,"num"));

        $FSalesArr15=$this->TimeCount($FirstSale,$date["date15"]);
        $FSaleNum15=array_sum(array_column($FSalesArr15,"num"));

        $FSalesArr20=$this->TimeCount($FirstSale,$date["date20"]);
        $FSaleNum20=array_sum(array_column($FSalesArr20,"num"));

        $FSalesArr25=$this->TimeCount($FirstSale,$date["date25"]);
        $FSaleNum25=array_sum(array_column($FSalesArr25,"num"));

        $FSalesArr30=$this->TimeCount($FirstSale,$date["date30"]);
        $FSaleNum30=array_sum(array_column($FSalesArr30,"num"));

        //统计近三十日排行第一的销售趋势
        $ThirtyFSale=[
            'FSaleNum1'=>$FSaleNum1,
            'FSaleNum5'=>$FSaleNum5,
            'FSaleNum10'=>$FSaleNum10,
            'FSaleNum15'=>$FSaleNum15,
            'FSaleNum20'=>$FSaleNum20,
            'FSaleNum25'=>$FSaleNum25,
            'FSaleNum30'=>$FSaleNum30,
        ];

        //查询近三十日销量排行榜第二的商品
        $SecondSale=$this->SaleTime2($SaleRanks["goodsName2"]);

        //查询近三十日排行榜第二在某个时间段的销量
        $SSalesArr1=$this->TimeCount($SecondSale,$date["date1"]);
        $SSaleNum1=array_sum(array_column($SSalesArr1,"num"));

        $SSalesArr5=$this->TimeCount($SecondSale,$date["date5"]);
        $SSaleNum5=array_sum(array_column($SSalesArr5,"num"));

        $SSalesArr10=$this->TimeCount($SecondSale,$date["date10"]);
        $SSaleNum10=array_sum(array_column($SSalesArr10,"num"));

        $SSalesArr15=$this->TimeCount($SecondSale,$date["date15"]);
        $SSaleNum15=array_sum(array_column($SSalesArr15,"num"));

        $SSalesArr20=$this->TimeCount($SecondSale,$date["date20"]);
        $SSaleNum20=array_sum(array_column($SSalesArr20,"num"));

        $SSalesArr25=$this->TimeCount($SecondSale,$date["date25"]);
        $SSaleNum25=array_sum(array_column($SSalesArr25,"num"));

        $SSalesArr30=$this->TimeCount($SecondSale,$date["date30"]);
        $SSaleNum30=array_sum(array_column($SSalesArr30,"num"));

        //统计近三十日排行第一的销售趋势
        $ThirtySSale=[
            'SSaleNum1'=>$SSaleNum1,
            'SSaleNum5'=>$SSaleNum5,
            'SSaleNum10'=>$SSaleNum10,
            'SSaleNum15'=>$SSaleNum15,
            'SSaleNum20'=>$SSaleNum20,
            'SSaleNum25'=>$SSaleNum25,
            'SSaleNum30'=>$SSaleNum30,
        ];

        //查询近三十日销量排行榜第三的商品
        $ThirtySale=$this->SaleTime2($SaleRanks["goodsName3"]);

        //查询近三十日排行榜第三在某个时间段的销量
        $TSalesArr1=$this->TimeCount($ThirtySale,$date["date1"]);
        $TSaleNum1=array_sum(array_column($TSalesArr1,"num"));

        $TSalesArr5=$this->TimeCount($ThirtySale,$date["date5"]);
        $TSaleNum5=array_sum(array_column($TSalesArr5,"num"));

        $TSalesArr10=$this->TimeCount($ThirtySale,$date["date10"]);
        $TSaleNum10=array_sum(array_column($TSalesArr10,"num"));

        $TSalesArr15=$this->TimeCount($ThirtySale,$date["date15"]);
        $TSaleNum15=array_sum(array_column($TSalesArr15,"num"));

        $TSalesArr20=$this->TimeCount($ThirtySale,$date["date20"]);
        $TSaleNum20=array_sum(array_column($TSalesArr20,"num"));

        $TSalesArr25=$this->TimeCount($ThirtySale,$date["date25"]);
        $TSaleNum25=array_sum(array_column($TSalesArr25,"num"));

        $TSalesArr30=$this->TimeCount($ThirtySale,$date["date30"]);
        $TSaleNum30=array_sum(array_column($TSalesArr30,"num"));

        //统计近三十日排行第三的销售趋势
        $ThirtyTSale=[
            'TSaleNum1'=>$TSaleNum1,
            'TaleNum5'=>$TSaleNum5,
            'TSaleNum10'=>$TSaleNum10,
            'TSaleNum15'=>$TSaleNum15,
            'TSaleNum20'=>$TSaleNum20,
            'TSaleNum25'=>$TSaleNum25,
            'TSaleNum30'=>$TSaleNum30,
        ];

        //统计近三十日销量排行榜前三的趋势
        $ThirtyRankTrend=[
            'date'=>[
                'date1'=>$date["date1"],
                'date5'=>$date["date5"],
                'date10'=>$date["date10"],
                'date15'=>$date["date15"],
                'date20'=>$date["date20"],
                'date25'=>$date["date25"],
                'date30'=>$date["date30"],
            ],
            'ThirtyFSale'=>$ThirtyFSale,
            'ThirtySSale'=>$ThirtySSale,
            'ThirtyTSale'=>$ThirtyTSale,
        ];
        return  $ThirtyRankTrend;
    }

    /**
     * 统计昨天的商品分析数据
     */
    public function YesterdayCA($shopid)
    {
        $YesterdayCA=[
            //昨天销售额排行
            'YesSalesVolume'=>$this->YesSalesVolume($shopid),
            //昨天销售排行
            'YesSales'=>$this->YesSales($shopid),
            //昨日销量数据趋势
            'YesSaleRanks'=>$this->YesSaleRanks($shopid)
        ];
        return $this->message($YesterdayCA,"成功",2);
    }

    /**
     * 统计近七日的商品分析数据
     */
    public function SevenCA($shopid)
    {
        $SevenCA=[
            //近七日销售额排行
            'SevenSalesVolume'=>$this->SevenSalesVolume($shopid),
            //近七日销售排行
            'SevenSales'=>$this->SevenSales($shopid),
            //近七日销量数据趋势
            'SevenSaleRanks'=>$this->SevenSaleRanks($shopid)
        ];
        return $this->message($SevenCA,"成功",2);
    }

    /**
     * 统计近三十日的商品分析
     */
    public function ThirtyCA($shopid)
    {
        $ThirtyCA=[
            //近三十日销售额排行
            'ThirtySalesVolume'=>$this->ThirtySalesVolume($shopid),
            //近三十日销售排行
            'ThirtySales'=>$this->ThirtySales($shopid),
            //近三十日销量数据趋势
            'ThirtySaleRanks'=>$this->ThirtySaleRanks($shopid)
        ];
        return $this->message($ThirtyCA,"成功",2);
    }
}