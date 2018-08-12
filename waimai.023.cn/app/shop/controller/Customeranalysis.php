<?php
/**
 * 顾客分析
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 15:08
 */

namespace app\shop\controller;
use think\Request;
use think\Db;
class Customeranalysis extends Index{

    //昨日总览模块
    public function YesterdayOverview($shopid)
    {
        //$shopid=$request->param("shopid");
        //昨日下单人数
        $yesOrderPerson=array_unique(db("order")->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")->column("user_id"));
        //前天下单人数
        $beyesOrderPerson=array_unique(db("order")->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 2 day),'%Y-%m-%d')")->column("user_id"));
        //昨天跟前天做比较
        $yesCompare=count($yesOrderPerson)-count($beyesOrderPerson);
        //昨日下单趋势
        $downwardTrend=db("order")
            ->field("id,user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->select();
        for($a=0;$a<count($downwardTrend);$a++){
            $downwardTrend[$a]["add_time"]=date("H",$downwardTrend[$a]["add_time"]);
        }
        //昨天0-24点的时间段
        $date0 = date('H', strtotime(-date('H') ."hours"));
        $date4 = date('H', strtotime(-date('H')+4 ."hours"));
        $date8 = date('H', strtotime(-date('H')+8 ."hours"));
        $date12 = date('H', strtotime(-date('H')+12 ."hours"));
        $date16 = date('H', strtotime(-date('H')+16 ."hours"));
        $date20 = date('H', strtotime(-date('H')+20 ."hours"));
        $date24 = date('H', strtotime(-date('H')+24 ."hours"));

        //0点的下单量
        $yesOrder0=seCount($downwardTrend,$date0,'add_time',$order0Num=0);
        //4点的下单量
        $yesOrder4=seCount($downwardTrend,$date4,'add_time',$order4Num=0);
        //8点的下单量
        $yesOrder8=seCount($downwardTrend,$date8,'add_time',$order8Num=0);
        //12点的下单量
        $yesOrder12=seCount($downwardTrend,$date12,'add_time',$order12Num=0);
        //16点的下单量
        $yesOrder16=seCount($downwardTrend,$date16,'add_time',$order16Num=0);
        //20点的下单量
        $yesOrder20=seCount($downwardTrend,$date20,'add_time',$order20Num=0);
        //24点的下单量
        $yesOrder24=seCount($downwardTrend,$date24,'add_time',$order24Num=0);

        //昨日之前的顾客
        $AllbeyesGuest=array_unique(db("order")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') < date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->column('user_id'));
        //查找昨日顾客不存在于昨日之前顾客(新客)
        $yesNewGuest=array_diff($yesOrderPerson,$AllbeyesGuest);
        //昨日新顾客数量
        $yesNewGuestnum=count(array_unique($yesNewGuest));
        //halt($yesNewGuestnum);
        //前日之前的顾客
        $AllbeqianGuest=array_unique(db("order")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') < date_format(date_sub(now(), interval 2 day),'%Y-%m-%d')")
            ->column('user_id'));
        //查找前日顾客不存在于前日之前顾客(新客)
        $qianNewGuest=array_diff($beyesOrderPerson,$AllbeqianGuest);
        //前日新顾客数量
        $qianNewGuestnum=count(array_unique($qianNewGuest));
        //比较新客上升人数(上升/下降/持平)
        $contrastNewGuest=$yesNewGuestnum-$qianNewGuestnum;
        //新客占总客的比例
        $AllOrder=array_unique(db("order")
            ->where("shop_id=".$shopid)
            ->column('user_id'));//总订单
        if($AllOrder>0){
            $NewPersonRatio=number_format($yesNewGuestnum/count($AllOrder)*100,2);
        }else{
            $NewPersonRatio=number_format(0,2);
        }

        //查找昨日顾客存在于昨日之前顾客(老客)
        $yesOldGuest=array_intersect($yesOrderPerson,$AllbeyesGuest);
        //昨日老顾客数量
        $yesOldGuestnum=count(array_unique($yesOldGuest));

        //查找前日顾客存在于前日之前顾客(老客)
        $qianOldGuest=array_intersect($beyesOrderPerson,$AllbeqianGuest);
        //前日老顾客数量
        $qianOldGuestnum=count(array_unique($qianOldGuest));
        //比较新客上升人数(上升/下降/持平)
        $contrastOldGuest=$yesOldGuestnum-$qianOldGuestnum;
        //计算老客占总客的比例
        if($AllOrder>0){
            $OldPersonRatio=number_format($yesOldGuestnum/count($AllOrder)*100,2);
        }else{
            $OldPersonRatio=number_format(0,2);
        }


        //昨天总览模块数据
        $yesDatas=[
            'yesOrderPerson'=>count($yesOrderPerson),//昨天下单人数
            'yesCompare'=>$yesCompare,//昨天跟前天做比较(正数表示上升,负数表示下降)
            /*
             * 下单人数趋势图
             */
            //昨天的0点
            'date0'=>$date0,
            'yesOrder0'=>$yesOrder0,
            //昨天的4点
            'date4'=>$date4,
            'yesOrder4'=>$yesOrder4,
            //昨天的8点
            'date8'=>$date8,
            'yesOrder8'=>$yesOrder8,
            //昨天的12点
            'date12'=>$date12,
            'yesOrder12'=>$yesOrder12,
            //昨天的16点
            'date16'=>$date16,
            'yesOrder16'=>$yesOrder16,
            //昨天的20点
            'date20'=>$date20,
            'yesOrder20'=>$yesOrder20,
            //昨天的24点
            'date24'=>$date24,
            'yesOrder24'=>$yesOrder24,


            //昨天的新客数量
            'yesNewGuestnum'=>count($yesNewGuest),
            //昨天与前天的新客比较
            'contrastNewGuest'=>$contrastNewGuest,
            //昨天的新客占总客的比例
            'NewPersonRatio'=>$NewPersonRatio."%",
            //昨天的老客数量
            'yesOldGuest'=>count($yesOldGuest),
            //昨天与前天的老客比较
            'contrastOldGuest'=>$contrastOldGuest,
            //昨天的老客占总客的比例
            'OldPersonRatio'=>$OldPersonRatio."%",


        ];
        return $yesDatas;

    }
    //近七日总览模块
    public function SevenOverview($shopid)
    {
        //$shopid=$request->param("shopid");
        //近七天下单人数
        $sevenOrderPerson=db("order")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->column("user_id");
        //前14天下单量
        $beFourteenOrderPerson=db("order")->field("user_id,add_time")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 14 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
        //前7天下单量
        $besevenOrderPerson=db("order")->field("user_id,add_time")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
        for ($i=0;$i<count($beFourteenOrderPerson);$i++){
            $beFourteenOrderPerson[$i]["add_time"]=date("Y-m-d",$beFourteenOrderPerson[$i]["add_time"]);
        }
        for ($i=0;$i<count($besevenOrderPerson);$i++){
            $besevenOrderPerson[$i]["add_time"]=date("Y-m-d",$besevenOrderPerson[$i]["add_time"]);
        }

        $beSevenOrder=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['add_time'],$bv['add_time']);
            return $r===0 ? strcmp($av['add_time'],$bv['add_time']) : $r;
        };
        //查询俩个二维数组中的差集
        $beSevenOrder=array_values(array_udiff($beFourteenOrderPerson, $besevenOrderPerson, $cmp));
        $d=array_udiff($besevenOrderPerson, $beFourteenOrderPerson, $cmp);
        foreach($d as &$dv) {
            $beSevenOrder[]=$dv;
            unset($d);
        }


        //近七天跟前七天做比较(正数表示上升,负数表示下降)
        $sevenCompare=count($sevenOrderPerson)-count($beSevenOrder);

        /*
         * 七日下单顾客每日的数量
         */

        //获取七日中的第一天的下单人数
        $date1 = date('Y-m-d', strtotime('-1 days'));
        $downorder1Num=seCount($besevenOrderPerson,$date1,'add_time',$downorder1Num=0);
        //获取七日中的第二天的下单人数
        $date2 = date('Y-m-d', strtotime('-2 days'));
        $downorder2Num=seCount($besevenOrderPerson,$date2,'add_time',$downorder2Num=0);
        //获取七日中的第三天的下单人数
        $date3 = date('Y-m-d', strtotime('-3 days'));
        $downorder3Num=seCount($besevenOrderPerson,$date3,'add_time',$downorder3Num=0);
        //获取七日中的第四天的下单人数
        $date4 = date('Y-m-d', strtotime('-4 days'));
        $downorder4Num=seCount($besevenOrderPerson,$date4,'add_time',$downorder4Num=0);
        //获取七日中的第五天的下单人数
        $date5 = date('Y-m-d', strtotime('-5 days'));
        $downorder5Num=seCount($besevenOrderPerson,$date5,'add_time',$downorder5Num=0);
        //获取七日中的第六天的下单人数
        $date6 = date('Y-m-d', strtotime('-6 days'));
        $downorder6Num=seCount($besevenOrderPerson,$date6,'add_time',$downorder6Num=0);
        //获取七日中的第七天的下单人数
        $date7 = date('Y-m-d', strtotime('-7 days'));
        $downorder7Num=seCount($besevenOrderPerson,$date7,'add_time',$downorder7Num=0);


        //七日之前所有的下单顾客
        $AllbeseGuest=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>7 ")
            ->select();
        for($b=0;$b<count($AllbeseGuest);$b++){
            $AllbeseGuest[$b]["add_time"]=date("Y-m-d",$AllbeseGuest[$b]["add_time"]);
        }
        //$AllbeseGuest2 = array_column($AllbeseGuest, 'add_time','user_id');

        //查找七日下单顾客不存在于七日之前下单顾客(新客)
        $sevenNewGuest=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['user_id'],$bv['user_id']);
            return $r===0 ? strcmp($av['user_id'],$bv['user_id']) : $r;
        };
        //查询俩个二维数组中的差集
        $sevenNewGuest=array_values(array_udiff($besevenOrderPerson, $AllbeseGuest, $cmp));
        $d=array_udiff($AllbeseGuest, $besevenOrderPerson, $cmp);
        foreach($d as &$dv) {
            $sevenNewGuest[]=$dv;
            unset($d);
        }
        if(count($sevenNewGuest)==count($besevenOrderPerson)+count($AllbeseGuest)){
            $sevenNewGuest=$besevenOrderPerson;
        }

        //查找七日下单顾客存在于七日之前下单顾客(老客)
        $sevenOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($besevenOrderPerson as $value){
            foreach ($AllbeseGuest as $val){
                if($value["user_id"]==$val["user_id"]){

                    $sevenOldGuest[]=$value;

                }
            }
        }

        //14日之前所有下单顾客
        $AllbefoGuest=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>14 ")
            ->select();
        for($b=0;$b<count($AllbefoGuest);$b++){
            $AllbefoGuest[$b]["add_time"]=date("Y-m-d",$AllbefoGuest[$b]["add_time"]);
        }
        //$AllbefoGuest2 = array_column($AllbefoGuest, 'add_time','user_id');

        //查找前前7日下单顾客不存在于前前七日之前下单顾客(新客)
        $FourteenNewGuest=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['user_id'],$bv['user_id']);
            return $r===0 ? strcmp($av['user_id'],$bv['user_id']) : $r;
        };
        //查询俩个二维数组中的差集
        $FourteenNewGuest=array_values(array_udiff($beSevenOrder, $AllbefoGuest, $cmp));
        $d=array_udiff($AllbefoGuest, $beSevenOrder, $cmp);
        foreach($d as &$dv) {
            $FourteenNewGuest[]=$dv;
            unset($d);
        }
        if(count($FourteenNewGuest)==count($beSevenOrder)+count($AllbefoGuest)){
            $FourteenNewGuest=$beSevenOrder;
        }
        //查找前前七日下单顾客存在于前前七日之前下单顾客(老客)
        $FourteenOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($beSevenOrder as $value){
            foreach ($AllbefoGuest as $val){
                if($value["user_id"]==$val["user_id"]){

                    $FourteenOldGuest[]=$value;

                }
            }
        }

        //近七天与前七天的新客比较
        $NewCompare=count($sevenNewGuest)-count($FourteenNewGuest);
        //近七天与前七天的老客比较
        $OldCompare=count($sevenOldGuest)-count($FourteenOldGuest);
        //查询所有总订单
        $AllOrder=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid)
            ->select();
        //近七日的新客占总客的比例
        if(count($AllOrder)>0){
            $sevenNewRatio=number_format(count($sevenNewGuest)/count($AllOrder)*100,2);
        }else{
            $sevenNewRatio=number_format(0,2);
        }
        //近七日的老客占总客的比例
        if(count($AllOrder)>0){
            $sevenOldRatio=number_format(count($sevenOldGuest)/count($AllOrder)*100,2);
        }else{
            $sevenOldRatio=number_format(0,2);
        }

        //近七天总览模块数据
        $sevenDatas=[
            'sevenOrderPersonNum'=>count($sevenOrderPerson),//近七天下单人数
            'sevenCompare'=>$sevenCompare,//近七天跟前七天做比较(正数表示上升,负数表示下降)
            /*
             * 下单人数趋势图
             */
            //近七天的第一天
            'date1'=>$date1,
            'downorder1Num'=>$downorder1Num,
            //近七天的第二天
            'date2'=>$date2,
            'downorder2Num'=>$downorder2Num,
            //近七天的第三天
            'date3'=>$date3,
            'downorder3Num'=>$downorder3Num,
            //近七天的第四天
            'date4'=>$date4,
            'downorder4Num'=>$downorder4Num,
            //近七天的第五天
            'date5'=>$date5,
            'downorder5Num'=>$downorder5Num,
            //近七天的第六天
            'date6'=>$date6,
            'downorder6Num'=>$downorder6Num,
            //近七天的第七天
            'date7'=>$date7,
            'downorder7Num'=>$downorder7Num,

            //近七天的新客数量
            'sevenNewGuest'=>count($sevenNewGuest),
            //近七天与前七天的新客比较
            'NewCompare'=>$NewCompare,
            //近七日的新客占总客的比例
            'sevenNewRatio'=>$sevenNewRatio."%",
            //近七天的老客数量
            'sevenOldGuest'=>count($sevenOldGuest),
            //近七天与前七天的老客比较
            'OldCompare'=>$OldCompare,
            //近七日的老客占总客的比例
            'sevenOldRatio'=>$sevenOldRatio."%",


        ];
        return $sevenDatas;
    }

    //近三十日总览模块
    public function ThirtyOverview($shopid)
    {
        //$shopid=$request->param("shopid");
        //近三十天下单人数
        $ThirtyOrderPerson=db("order")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 30 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->column("user_id");
        //前六十天下单量
        $beSixtyOrderPerson=db("order")->field("user_id,add_time")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 60 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
        //前三十天下单量
        $beThirtyOrderPerson=db("order")->field("user_id,add_time")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 30 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
        for ($i=0;$i<count($beSixtyOrderPerson);$i++){
            $beSixtyOrderPerson[$i]["add_time"]=date("Y-m-d",$beSixtyOrderPerson[$i]["add_time"]);
        }
        for ($i=0;$i<count($beThirtyOrderPerson);$i++){
            $beThirtyOrderPerson[$i]["add_time"]=date("Y-m-d",$beThirtyOrderPerson[$i]["add_time"]);
        }

        $cmp=function($av, $bv){
            $r=strcmp($av['add_time'],$bv['add_time']);
            return $r===0 ? strcmp($av['add_time'],$bv['add_time']) : $r;
        };
        //查询俩个二维数组中的差集
        $beThirtyOrder=array_values(array_udiff($beSixtyOrderPerson, $beThirtyOrderPerson, $cmp));
        $d=array_udiff($beThirtyOrderPerson, $beSixtyOrderPerson, $cmp);
        foreach($d as &$dv) {
            $beThirtyOrder[]=$dv;
            unset($d);
        }

        //近三十天跟前三十天做比较(正数表示上升,负数表示下降)
        $thirtyCompare=count($ThirtyOrderPerson)-count($beThirtyOrder);
        /*
         * 三十日下单顾客每五日的数量
         */

        //获取七日中的第一天的下单人数
        $date1 = date('Y-m-d', strtotime('-1 days'));
        $downorder1Num=seCount($beThirtyOrderPerson,$date1,'add_time',$downorder1Num=0);
        //获取七日中的第二天的下单人数
        $date5 = date('Y-m-d', strtotime('-5 days'));
        $downorder5Num=seCount($beThirtyOrderPerson,$date5,'add_time',$downorder5Num=0);
        //获取七日中的第三天的下单人数
        $date10 = date('Y-m-d', strtotime('-10 days'));
        $downorder10Num=seCount($beThirtyOrderPerson,$date10,'add_time',$downorder10Num=0);
        //获取七日中的第四天的下单人数
        $date15 = date('Y-m-d', strtotime('-15 days'));
        $downorder15Num=seCount($beThirtyOrderPerson,$date15,'add_time',$downorder15Num=0);
        //获取七日中的第五天的下单人数
        $date20 = date('Y-m-d', strtotime('-20 days'));
        $downorder20Num=seCount($beThirtyOrderPerson,$date20,'add_time',$downorder20Num=0);
        //获取七日中的第六天的下单人数
        $date25 = date('Y-m-d', strtotime('-25 days'));
        $downorder25Num=seCount($beThirtyOrderPerson,$date25,'add_time',$downorder25Num=0);
        //获取七日中的第七天的下单人数
        $date30 = date('Y-m-d', strtotime('-30 days'));
        $downorder30Num=seCount($beThirtyOrderPerson,$date30,'add_time',$downorder30Num=0);


        //三十日之前所有的下单顾客
        $AllbethGuest=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>30 ")
            ->select();
        for($b=0;$b<count($AllbethGuest);$b++){
            $AllbethGuest[$b]["add_time"]=date("Y-m-d",$AllbethGuest[$b]["add_time"]);
        }
        //$AllbeseGuest2 = array_column($AllbeseGuest, 'add_time','user_id');

        //查找三十日下单顾客不存在于三十日之前下单顾客(新客)
        $thirtyNewGuest=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['user_id'],$bv['user_id']);
            return $r===0 ? strcmp($av['user_id'],$bv['user_id']) : $r;
        };
        //查询俩个二维数组中的差集
        $thirtyNewGuest=array_values(array_udiff($beThirtyOrderPerson, $AllbethGuest, $cmp));
        $d=array_udiff($AllbethGuest, $beThirtyOrderPerson, $cmp);
        foreach($d as &$dv) {
            $thirtyNewGuest[]=$dv;
            unset($d);
        }
        if(count($thirtyNewGuest)==count($beThirtyOrderPerson)+count($AllbethGuest)){
            $thirtyNewGuest=$beThirtyOrderPerson;
        }

        //查找30日下单顾客存在于30日之前下单顾客(老客)
        $thirtyOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($beThirtyOrderPerson as $value){
            foreach ($AllbethGuest as $val){
                if($value["user_id"]==$val["user_id"]){

                    $thirtyOldGuest[]=$value;

                }
            }
        }

        //六十日之前所有下单顾客
        $AllbesixGuest=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>60 ")
            ->select();
        for($b=0;$b<count($AllbesixGuest);$b++){
            $AllbesixGuest[$b]["add_time"]=date("Y-m-d",$AllbesixGuest[$b]["add_time"]);
        }
        //$AllbefoGuest2 = array_column($AllbefoGuest, 'add_time','user_id');

        //查找前前30日下单顾客不存在于前前30日之前下单顾客(新客)
        $SixtyNewGuest=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['user_id'],$bv['user_id']);
            return $r===0 ? strcmp($av['user_id'],$bv['user_id']) : $r;
        };
        //查询俩个二维数组中的差集
        $SixtyNewGuest=array_values(array_udiff($beThirtyOrder, $AllbesixGuest, $cmp));
        $d=array_udiff($AllbesixGuest, $beThirtyOrder, $cmp);
        foreach($d as &$dv) {
            $SixtyNewGuest[]=$dv;
            unset($d);
        }
        if(count($SixtyNewGuest)==count($beThirtyOrder)+count($AllbesixGuest)){
            $SixtyNewGuest=$beThirtyOrder;
        }
        //查找前前三十日下单顾客存在于前前三十日之前下单顾客(老客)
        $SixtyOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($beThirtyOrder as $value){
            foreach ($AllbesixGuest as $val){
                if($value["user_id"]==$val["user_id"]){

                    $SixtyOldGuest[]=$value;

                }
            }
        }

        //近三十天与前三十天的新客比较
        $thirtyNewCompare=count($thirtyNewGuest)-count($SixtyNewGuest);
        //近三十天与前三十天的老客比较
        $thirtyOldCompare=count($thirtyOldGuest)-count($SixtyOldGuest);
        //查询所有总订单
        $AllOrder=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid)
            ->select();
        //近三十日的新客占总客的比例
        if(count($AllOrder)>0){
            $thirtyNewRatio=number_format(count($thirtyNewGuest)/count($AllOrder)*100,2);
        }else{
            $thirtyNewRatio=number_format(0,2);
        }
        //近三十日的老客占总客的比例
        if(count($AllOrder)>0){
            $thirtyOldRatio=number_format(count($thirtyOldGuest)/count($AllOrder)*100,2);
        }else{
            $thirtyOldRatio=number_format(0,2);
        }

        //近三十天总览模块数据
        $thirtyDatas=[
            'ThirtyOrderPerson'=>count($ThirtyOrderPerson),//近三十天下单人数
            'thirtyCompare'=>$thirtyCompare,//近三十天跟前三十天做比较(正数表示上升,负数表示下降)
            /*
             * 下单人数趋势图
             */
            //近三十天的第一天
            'date1'=>$date1,
            'downorder1Num'=>$downorder1Num,
            //近三十天的第五天
            'date5'=>$date5,
            'downorder5Num'=>$downorder5Num,
            //近三十天的第十天
            'date10'=>$date10,
            'downorder10Num'=>$downorder10Num,
            //近三十天的第十五天
            'date15'=>$date15,
            'downorder15Num'=>$downorder15Num,
            //近三十天的第二十天
            'date20'=>$date20,
            'downorder20Num'=>$downorder20Num,
            //近三十天的第二十五天
            'date25'=>$date25,
            'downorder25Num'=>$downorder25Num,
            //近三十天的第三十天
            'date30'=>$date30,
            'downorder30Num'=>$downorder30Num,

            //近三十天的新客数量
            'thirtyNewGuest'=>count($thirtyNewGuest),
            //近三十天与前七天的新客比较
            'thirtyNewCompare'=>$thirtyNewCompare,
            //近三十日的新客占总客的比例
            'thirtyNewRatio'=>$thirtyNewRatio,
            //近三十天的老客数量
            'thirtyOldGuest'=>count($thirtyOldGuest),
            //近三十天与前七天的老客比较
            'thirtyOldCompare'=>$thirtyOldCompare,
            //近三十日的老客占总客的比例
            'thirtyOldRatio'=>$thirtyOldRatio,

        ];
        return $thirtyDatas;
    }
    public function YesOrder($shopid,$typeid)
    {
        //根据时间查询订单
        $order=db::view("order","user_id")
            ->view("user","id,birthday","order.user_id=user.id")
            ->where("order.shop_id=".$shopid." and date_format(date_sub(now(), interval ".$typeid." day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();

        return $order;
    }

    //昨日顾客结构
    public function YesCustomerStructure($shopid)
    {
        //昨天的订单
        $order=$this->YesOrder($shopid,1);
        //计算下单顾客的时代
        $yesOrderPerson00=seTimeCount($order,'2000','2010','birthday',$yesOrderPerson00=0);//00后下单人数
        $yesOrderPerson90=seTimeCount($order,'1990','2000','birthday',$yesOrderPerson90=0);//90后下单人数
        $yesOrderPerson80=seTimeCount($order,'1980','1990','birthday',$yesOrderPerson80=0);//80后下单人数
        $yesOrderPersonbe80=0;
        foreach ($order as $item) {
            if ($item['birthday'] < '1980') {
                $yesOrderPersonbe80+=1;//80以前下单人数
            }
        }
        //计算个时代所占的比例
        if(count($order)>0){
            $yesOrderRatio00=number_format(($yesOrderPerson00/count($order))*100,2);//00后下单人数所占比例
            $yesOrderRatio90=number_format(($yesOrderPerson90/count($order))*100,2);//90后下单人数所占比例
            $yesOrderRatio80=number_format(($yesOrderPerson80/count($order))*100,2);//80后下单人数所占比例
            $yesOrderRatiobe80=number_format(($yesOrderPersonbe80/count($order))*100,2);//80以前下单人数所占比例
        }else{
            $yesOrderRatio00=0.00;
            $yesOrderRatio90=0.00;
            $yesOrderRatio80=0.00;
            $yesOrderRatiobe80=0.00;
        }
        $yesCS=[
            'yesOrderPerson00'=>$yesOrderPerson00,
            'yesOrderPerson90'=>$yesOrderPerson90,
            'yesOrderPerson80'=>$yesOrderPerson80,
            'yesOrderPersonbe80'=>$yesOrderPersonbe80,
            'yesOrderRatio00'=>$yesOrderRatio00."%",
            'yesOrderRatio90'=>$yesOrderRatio90."%",
            'yesOrderRatio80'=>$yesOrderRatio80."%",
            'yesOrderRatiobe80'=>$yesOrderRatiobe80."%",
        ];
        return $yesCS;
    }

    //七日顾客结构
    public function SevenCustomerStructure($shopid)
    {
        //七日的订单
        $order=$this->YesOrder($shopid,7);
        //计算下单顾客的时代
        $sevenOrderPerson00=seTimeCount($order,'2000','2010','birthday',$sevenOrderPerson00=0);//00后下单人数
        $sevenOrderPerson90=seTimeCount($order,'1990','2000','birthday',$sevenOrderPerson90=0);//90后下单人数
        $sevenOrderPerson80=seTimeCount($order,'1980','1990','birthday',$sevenOrderPerson80=0);//80后下单人数
        $sevenOrderPersonbe80=0;
        foreach ($order as $item) {
            if ($item['birthday'] < '1980') {
                $sevenOrderPersonbe80+=1;//80以前下单人数
            }
        }
        //计算个时代所占的比例
        if(count($order)>0){
            $sevenOrderRatio00=number_format(($sevenOrderPerson00/count($order))*100,2);//00后下单人数所占比例
            $sevenOrderRatio90=number_format(($sevenOrderPerson90/count($order))*100,2);//90后下单人数所占比例
            $sevenOrderRatio80=number_format(($sevenOrderPerson80/count($order))*100,2);//80后下单人数所占比例
            $sevenOrderRatiobe80=number_format(($sevenOrderPersonbe80/count($order))*100,2);//80以前下单人数所占比例
        }else{
            $sevenOrderRatio00=0.00;
            $sevenOrderRatio90=0.00;
            $sevenOrderRatio80=0.00;
            $sevenOrderRatiobe80=0.00;
        }
        $sevenCS=[
            'sevenOrderPerson00'=>$sevenOrderPerson00,
            'sevenOrderPerson90'=>$sevenOrderPerson90,
            'sevenOrderPerson80'=>$sevenOrderPerson80,
            'sevenOrderPersonbe80'=>$sevenOrderPersonbe80,
            'sevenOrderRatio00'=>$sevenOrderRatio00."%",
            'sevenOrderRatio90'=>$sevenOrderRatio90."%",
            'sevenOrderRatio80'=>$sevenOrderRatio80."%",
            'sevenOrderRatiobe80'=>$sevenOrderRatiobe80."%",
        ];
        return $sevenCS;
    }

    //三十日顾客结构
    public function ThirtyCustomerStructure($shopid)
    {
        //三十日的订单
        $order=$this->YesOrder($shopid,30);
        //计算下单顾客的时代
        $thirtyOrderPerson00=seTimeCount($order,'2000','2010','birthday',$thirtyOrderPerson00=0);//00后下单人数
        $thirtyOrderPerson90=seTimeCount($order,'1990','2000','birthday',$thirtyOrderPerson90=0);//90后下单人数
        $thirtyOrderPerson80=seTimeCount($order,'1980','1990','birthday',$thirtyOrderPerson80=0);//80后下单人数
        $thirtyOrderPersonbe80=0;
        foreach ($order as $item) {
            if ($item['birthday'] < '1980') {
                $thirtyOrderPersonbe80+=1;//80以前下单人数
            }
        }
        //计算个时代所占的比例
        if(count($order)>0){
            $thirtyOrderRatio00=number_format(($thirtyOrderPerson00/count($order))*100,2);//00后下单人数所占比例
            $thirtyOrderRatio90=number_format(($thirtyOrderPerson90/count($order))*100,2);//90后下单人数所占比例
            $thirtyOrderRatio80=number_format(($thirtyOrderPerson80/count($order))*100,2);//80后下单人数所占比例
            $thirtyOrderRatiobe80=number_format(($thirtyOrderPersonbe80/count($order))*100,2);//80以前下单人数所占比例
        }else{
            $thirtyOrderRatio00=0.00;
            $thirtyOrderRatio90=0.00;
            $thirtyOrderRatio80=0.00;
            $thirtyOrderRatiobe80=0.00;
        }
        $thirtyCS=[
            'thirtyOrderPerson00'=>$thirtyOrderPerson00,
            'thirtyOrderPerson90'=>$thirtyOrderPerson90,
            'thirtyOrderPerson80'=>$thirtyOrderPerson80,
            'thirtyOrderPersonbe80'=>$thirtyOrderPersonbe80,
            'thirtyOrderRatio00'=>$thirtyOrderRatio00."%",
            'thirtyOrderRatio90'=>$thirtyOrderRatio90."%",
            'thirtyOrderRatio80'=>$thirtyOrderRatio80."%",
            'thirtyOrderRatiobe80'=>$thirtyOrderRatiobe80."%",
        ];
        return $thirtyCS;
    }

    //昨日重复订单
    public function YesRepeatOrder($shopid){
        //昨日下单趋势
        $yesOrder=db("order")
            ->field("id,user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->column("user_id");
        //前日下单趋势
        $beyesOrder=db("order")
            ->field("id,user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 2 day),'%Y-%m-%d')")
            ->column("user_id");
        //查询重复数据出现的次数
        $yesRepeatNum=array_count_values($yesOrder);
        $beyesRepeatNum=array_count_values($beyesOrder);
        //昨日重复下单人数
        $yesRepeatPerson=0;
        foreach ($yesRepeatNum as $item) {
            if ($item >1) {
                $yesRepeatPerson+=1;
            }
        }
        //前日重复下单人数
        $beyesRepeatPerson=0;
        foreach ($beyesRepeatNum as $item) {
            if ($item >1) {
                $beyesRepeatPerson+=1;
            }
        }
        //比较重复下单人数
        $yesRepeatCompare=$yesRepeatPerson-$beyesRepeatPerson;

        //昨日重复下单率
        $yesRepetitionRate=number_format(0,2);
        $beyesRepetitionRate=number_format(0,2);

        if(count($yesOrder)>0){
            if($yesRepeatPerson>0){

                $yesRepetitionRate=number_format($yesRepeatPerson/count($yesOrder)*100,2);
            }
            if($beyesRepeatPerson>0){
                //前日重复下单率
                $beyesRepetitionRate=number_format($beyesRepeatPerson/count($beyesOrder)*100,2);
            }
        }

        //比较重复下单率
        $yesRRCompare=number_format($yesRepetitionRate-$beyesRepetitionRate,2);
        //统计昨日重复下单
        $yesDOrder=[

            'yesRepetitionRate'=>$yesRepetitionRate."%",
            'yesRRCompare'=>$yesRRCompare."%",
            'yesRepeatPerson'=>$yesRepeatPerson,
            'yesRepeatCompare'=>$yesRepeatCompare

        ];
        return $yesDOrder;
    }

    //七日重复下单
    public function SevenRepeatOrder($shopid)
    {
       //七日订单
        $sevenOrder=db("order")->field("user_id,add_time")
                    ->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
                    ->column("user_id");

        //前14天下单量
        $beFourteenOrderPerson=db("order")->field("user_id,add_time")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 14 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
        //前7天下单量
        $besevenOrderPerson=db("order")->field("user_id,add_time")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
        for ($i=0;$i<count($beFourteenOrderPerson);$i++){
            $beFourteenOrderPerson[$i]["add_time"]=date("Y-m-d",$beFourteenOrderPerson[$i]["add_time"]);
        }
        for ($i=0;$i<count($besevenOrderPerson);$i++){
            $besevenOrderPerson[$i]["add_time"]=date("Y-m-d",$besevenOrderPerson[$i]["add_time"]);
        }
        //前前7天下单量
        $beSevenOrder=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['add_time'],$bv['add_time']);
            return $r===0 ? strcmp($av['add_time'],$bv['add_time']) : $r;
        };
        //查询俩个二维数组中的差集
        $beSevenOrder=array_values(array_udiff($beFourteenOrderPerson, $besevenOrderPerson, $cmp));
        $d=array_udiff($besevenOrderPerson, $beFourteenOrderPerson, $cmp);
        foreach($d as &$dv) {
            $beSevenOrder[]=$dv;
            unset($d);
        }
        $beSevenOrder=array_column($beSevenOrder,"user_id");
        $sevenRepeatNum=array_count_values($sevenOrder);
        $besevenRepeatNum=array_count_values($beSevenOrder);
        //近七日重复下单人数
        $sevenRepeatPerson=0;
        foreach ($sevenRepeatNum as $item) {
            if ($item >1) {
                $sevenRepeatPerson+=1;
            }
        }
        //前七日重复下单人数
        $besevenRepeatPerson=0;
        foreach ($besevenRepeatNum as $item) {
            if ($item >1) {
                $besevenRepeatPerson+=1;
            }
        }
        //比较重复下单人数
        $sevenRepeatCompare=$sevenRepeatPerson-$besevenRepeatPerson;

        //近七日重复下单率
        $sevenRepetitionRate=number_format(0,2);
        $besevenRepetitionRate=number_format(0,2);
        if(count($sevenOrder)>0){
            if($sevenRepeatPerson>0){
                $sevenRepetitionRate=number_format($sevenRepeatPerson/count($sevenOrder)*100,2);
            }
            if($besevenRepeatPerson>0){
                //前七日重复下单率
                $besevenRepetitionRate=number_format($besevenRepeatPerson/count($beSevenOrder)*100,2);
            }

        }

        //比较重复下单率
        $sevenRRCompare=number_format($sevenRepetitionRate-$besevenRepetitionRate,2);

        //统计七日重复下单
        $sevenDOrder=[

            'sevenRepetitionRate'=>$sevenRepetitionRate."%",
            'sevenRRCompare'=>$sevenRRCompare."%",
            'sevenRepeatPerson'=>$sevenRepeatPerson,
            'sevenRepeatCompare'=>$sevenRepeatCompare

        ];
        return $sevenDOrder;
    }

    //三十日重复下单
    public function ThirtyRepeatOrder($shopid)
    {
        //三十日订单
        $thirtyOrder=db("order")->field("user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 30 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->column("user_id");

        //前六十天下单量
        $beSixtyOrderPerson=db("order")->field("user_id,add_time")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 60 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
        //前三十天下单量
        $bethirtyOrderPerson=db("order")->field("user_id,add_time")->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 30 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
        for ($i=0;$i<count($beSixtyOrderPerson);$i++){
            $beFourteenOrderPerson[$i]["add_time"]=date("Y-m-d",$beSixtyOrderPerson[$i]["add_time"]);
        }
        for ($i=0;$i<count($bethirtyOrderPerson);$i++){
            $bethirtyOrderPerson[$i]["add_time"]=date("Y-m-d",$bethirtyOrderPerson[$i]["add_time"]);
        }
        //前前30天下单量
        $bethirtyOrder=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['add_time'],$bv['add_time']);
            return $r===0 ? strcmp($av['add_time'],$bv['add_time']) : $r;
        };
        //查询俩个二维数组中的差集
        $bethirtyOrder=array_values(array_udiff($beSixtyOrderPerson, $bethirtyOrderPerson, $cmp));
        $d=array_udiff($bethirtyOrderPerson, $beSixtyOrderPerson, $cmp);
        foreach($d as &$dv) {
            $bethirtyOrder[]=$dv;
            unset($d);
        }
        $bethirtyOrder=array_column($bethirtyOrder,"user_id");
        $thirtyRepeatNum=array_count_values($thirtyOrder);
        $bethirtyRepeatNum=array_count_values($bethirtyOrder);
        //近三十日重复下单人数
        $thirtyRepeatPerson=0;
        foreach ($thirtyRepeatNum as $item) {
            if ($item >1) {
                $thirtyRepeatPerson+=1;
            }
        }
        //前三十日重复下单人数
        $bethirtyRepeatPerson=0;
        foreach ($bethirtyRepeatNum as $item) {
            if ($item >1) {
                $bethirtyRepeatPerson+=1;
            }
        }
        //比较重复下单人数
        $thirtyRepeatCompare=$thirtyRepeatPerson-$bethirtyRepeatPerson;

        //近三十日重复下单率
        $thirtyRepetitionRate=number_format(0,2);
        $bethirtyRepetitionRate=number_format(0,2);
        if(count($thirtyOrder)>0) {
            if ($thirtyRepeatPerson > 0) {
                $thirtyRepetitionRate = number_format($thirtyRepeatPerson / count($thirtyOrder) * 100, 2);
            }
            if ($bethirtyRepeatPerson > 0) {
                //前三十日重复下单率
                $bethirtyRepetitionRate = number_format($bethirtyRepeatPerson / count($bethirtyOrder) * 100, 2);

            }
        }
        //比较重复下单率
        $thirtyRRCompare=number_format($thirtyRepetitionRate-$bethirtyRepetitionRate,2);
        //统计三十日重复下单
        $thirtyDOrder=[

            'thirtyRepetitionRate'=>$thirtyRepetitionRate."%",
            'thirtyRRCompare'=>$thirtyRRCompare."%",
            'thirtyRepeatPerson'=>$thirtyRepeatPerson,
            'thirtyRepeatCompare'=>$thirtyRepeatCompare

        ];
        return $thirtyDOrder;
    }

    //昨日顾客下单趋势
    public function YesDownwardTrend($shopid)
    {
        $data=$this->YesOrderTime($shopid);
        //我的店铺昨日下单顾客
        $downwardTrend=$data["YesOrder"];

        //查询本店铺的经营类型
        //$shoptype=db("shop")->where("id=".$shopid)->select();
        //其他的店铺昨日下单
        $yesPeerOrder=db("order")
            ->field("shop_id,add_time")
            ->where("shop_id!=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->select();
        for ($i=0;$i<count($yesPeerOrder);$i++){
            $yesPeerOrder[$i]["add_time"]=date("H",$yesPeerOrder[$i]["add_time"]);
        }

        /*
        *访问趋势的具体时间段
         */
        $date0 = $data["date0"];
        //$date2 = $data["date2"];
        $date4 = $data["date4"];
        //$date6 = $data["date6"];
        $date8 = $data["date8"];
        //$date10 = $data["date10"];
        $date12 = $data["date12"];
        //$date14 = $data["date14"];
        $date16 = $data["date16"];
        //$date18 = $data["date18"];
        $date20 = $data["date20"];
        //$date22 = $data["date22"];
        $date24 = $data["date24"];
        /**
         * 我的店铺昨日具体下单趋势
         */
        //0点的下单量
        $yesMyOrder0=seCount($downwardTrend,$date0,'add_time',$order0Num=0);
        //4点的下单量
        //$yesMyOrder2=seTimeCount($downwardTrend,$date2,$date4,'add_time',$order2Num=0);
        //4点的下单量
        $yesMyOrder4=seCount($downwardTrend,$date4,'add_time',$order4Num=0);
        //8点的下单量
        //$yesMyOrder6=seTimeCount($downwardTrend,$date6,$date8,'add_time',$order6Num=0);
        //8点的下单量
        $yesMyOrder8=seCount($downwardTrend,$date8,'add_time',$order8Num=0);
        //12点的下单量
        //$yesMyOrder10=seTimeCount($downwardTrend,$date10,$date12,'add_time',$order10Num=0);
        //12点的下单量
        $yesMyOrder12=seCount($downwardTrend,$date12,'add_time',$order12Num=0);
        //16点的下单量
        //$yesMyOrder14=seTimeCount($downwardTrend,$date14,$date16,'add_time',$order14Num=0);
        //16点的下单量
        $yesMyOrder16=seCount($downwardTrend,$date16,'add_time',$order16Num=0);
        //20点的下单量
        //$yesMyOrder18=seTimeCount($downwardTrend,$date18,$date20,'add_time',$order18Num=0);
        //22点的下单量
        $yesMyOrder20=seCount($downwardTrend,$date20,'add_time',$order20Num=0);
        //24点的下单量
        $yesMyOrder24=seCount($downwardTrend,$date24,'add_time',$order24Num=0);

        /**
         * 同行昨日具体下单趋势
         */
        //0点的下单量
        $yesPeOrder0=seCount($yesPeerOrder,$date0,'add_time',$order0Num=0);
        $yesPeShop0=seShopCount($yesPeerOrder,$date0,'add_time',$order0Num=0);
        //0点的同行平均数
        $yesPeAverage0=$this->PeAverage($yesPeOrder0,$yesPeShop0);
        //4点的下单量
        $yesPeOrder4=seCount($yesPeerOrder,$date4,'add_time',$order4Num=0);
        $yesPeShop4=seShopCount($yesPeerOrder,$date4,'add_time',$order4Num=0);
        //4点的同行平均数
        $yesPeAverage4=$this->PeAverage($yesPeOrder4,$yesPeShop4);
        //8点的下单量
        $yesPeOrder8=seCount($yesPeerOrder,$date8,'add_time',$order8Num=0);
        $yesPeShop8=seShopCount($yesPeerOrder,$date8,'add_time',$order8Num=0);
        //8点的同行平均数
        $yesPeAverage8=$this->PeAverage($yesPeOrder8,$yesPeShop8);
        //12点的下单量和店铺数
        $yesPeOrder12=seCount($yesPeerOrder,$date12,'add_time',$order12Num=0);
        $yesPeShop12=seShopCount($yesPeerOrder,$date12,'add_time',$order12Num=0);
        //12点的同行平均数
        $yesPeAverage12=$this->PeAverage($yesPeOrder12,$yesPeShop12);
        //16点的下单量
        $yesPeOrder16=seCount($yesPeerOrder,$date16,'add_time',$order16Num=0);
        $yesPeShop16=seShopCount($yesPeerOrder,$date16,'add_time',$order16Num=0);
        //16点的同行平均数
        $yesPeAverage16=$this->PeAverage($yesPeOrder16,$yesPeShop16);
        //20点的下单量
        $yesPeOrder20=seCount($yesPeerOrder,$date20,'add_time',$order20Num=0);
        $yesPeShop20=seShopCount($yesPeerOrder,$date20,'add_time',$order20Num=0);
        //20点的同行平均数
        $yesPeAverage20=$this->PeAverage($yesPeOrder20,$yesPeShop20);
        //24点的下单量
        $yesPeOrder24=seCount($yesPeerOrder,$date24,'add_time',$order24Num=0);
        $yesPeShop24=seShopCount($yesPeerOrder,$date24,'add_time',$order24Num=0);
        //24点的同行平均数
        $yesPeAverage24=$this->PeAverage($yesPeOrder24,$yesPeShop24);

        $YesDT=[
            'yesMyOrder0'=>$yesMyOrder0,
            'yesPeAverage0'=>$yesPeAverage0,
            'yesMyOrder4'=>$yesMyOrder4,
            'yesPeAverage4'=>$yesPeAverage4,
            'yesMyOrder8'=>$yesMyOrder8,
            'yesPeAverage8'=>$yesPeAverage8,
            'yesMyOrder12'=>$yesMyOrder12,
            'yesPeAverage12'=>$yesPeAverage12,
            'yesMyOrder16'=>$yesMyOrder16,
            'yesPeAverage16'=>$yesPeAverage16,
            'yesMyOrder20'=>$yesMyOrder20,
            'yesPeAverage20'=>$yesPeAverage20,
            'yesMyOrder24'=>$yesMyOrder24,
            'yesPeAverage24'=>$yesPeAverage24,
        ];
        return $YesDT;
    }
    //求同行平均数
    public function PeAverage($OrderNum,$ShopNum)
    {
        if($ShopNum>1){
            $yesAverage=number_format($OrderNum/$ShopNum,0) ;
        }else{
            $yesAverage=$ShopNum;
        }
        return $yesAverage;
    }

    //七日下单趋势
    public function SevenDownwardTrend($shopid)
    {
        $data=$this->SevenOrderTime($shopid);
        //查询七日的下单顾客
        $downwardTrend=$data["SevenOrder"];
        //查询七日其他店铺的下单顾客
        $sevenPeerOrder=db("order")
            ->field("shop_id,add_time")
            ->where("shop_id!=".$shopid." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for ($i=0;$i<count($sevenPeerOrder);$i++){
            $sevenPeerOrder[$i]["add_time"]=date("Y-m-d",$sevenPeerOrder[$i]["add_time"]);
        }

        /*
         * 七日我的店铺下单顾客每日的数量
         */

        //获取七日中的第一天的顾客量
        $sevenMyOrder1=seCount($downwardTrend,$data["date1"],'add_time',$downorder1Num=0);

        //获取七日中的第二天的顾客量
        $sevenMyOrder2=seCount($downwardTrend,$data["date2"],'add_time',$downorder2Num=0);

        //获取七日中的第三天的顾客量
        $sevenMyOrder3=seCount($downwardTrend,$data["date3"],'add_time',$downorder3Num=0);

        //获取七日中的第四天的顾客量
        $sevenMyOrder4=seCount($downwardTrend,$data["date4"],'add_time',$downorder4Num=0);

        //获取七日中的第五天的顾客量
        $sevenMyOrder5=seCount($downwardTrend,$data["date5"],'add_time',$downorder5Num=0);

        //获取七日中的第六天的顾客量
        $sevenMyOrder6=seCount($downwardTrend,$data["date6"],'add_time',$downorder6Num=0);

        //获取七日中的第七天的顾客量
        $sevenMyOrder7=seCount($downwardTrend,$data["date7"],'add_time',$downorder7Num=0);
        /**
         * 七日同行店铺下单顾客每日的数量
         */
        //获取七日中的第一天的下单量
        $sevenPeOrder1=seCount($sevenPeerOrder,$data["date1"],'add_time',$order1Num=0);
        $sevenPeShop1=seShopCount($sevenPeerOrder,$data["date1"],'add_time',$order1Num=0);
        //获取七日中的第一天的同行平均数
        $sevenPeAverage1=$this->PeAverage(count($sevenPeerOrder),$sevenPeShop1);

        //获取七日中的第二天的下单量
        $sevenPeOrder2=seCount($sevenPeerOrder,$data["date2"],'add_time',$order2Num=0);
        $sevenPeShop2=seShopCount($sevenPeerOrder,$data["date2"],'add_time',$order2Num=0);
        //获取七日中的第二天的同行平均数
        $sevenPeAverage2=$this->PeAverage(count($sevenPeerOrder),$sevenPeShop2);

        //获取七日中的第三天的下单量
        $sevenPeOrder3=seCount($sevenPeerOrder,$data["date3"],'add_time',$order3Num=0);
        $sevenPeShop3=seShopCount($sevenPeerOrder,$data["date3"],'add_time',$order3Num=0);
        //获取七日中的第三天的同行平均数
        $sevenPeAverage3=$this->PeAverage(count($sevenPeerOrder),$sevenPeShop3);

        //获取七日中的第四天的下单量
        $sevenPeOrder4=seCount($sevenPeerOrder,$data["date4"],'add_time',$order4Num=0);
        $sevenPeShop4=seShopCount($sevenPeerOrder,$data["date4"],'add_time',$order4Num=0);
        //获取七日中的第四天的同行平均数
        $sevenPeAverage4=$this->PeAverage(count($sevenPeerOrder),$sevenPeShop4);

        //获取七日中的第五天的下单量
        $sevenPeOrder5=seCount($sevenPeerOrder,$data["date5"],'add_time',$order5Num=0);
        $sevenPeShop5=seShopCount($sevenPeerOrder,$data["date5"],'add_time',$order5Num=0);
        //获取七日中的第五天的同行平均数
        $sevenPeAverage5=$this->PeAverage(count($sevenPeerOrder),$sevenPeShop5);

        //获取七日中的第六天的下单量
        $sevenPeOrder6=seCount($sevenPeerOrder,$data["date6"],'add_time',$order6Num=0);
        $sevenPeShop6=seShopCount($sevenPeerOrder,$data["date6"],'add_time',$order6Num=0);
        //获取七日中的第六天的同行平均数
        $sevenPeAverage6=$this->PeAverage(count($sevenPeerOrder),$sevenPeShop6);

        //获取七日中的第七天的下单量
        $sevenPeOrder7=seCount($sevenPeerOrder,$data["date7"],'add_time',$order7Num=0);
        $sevenPeShop7=seShopCount($sevenPeerOrder,$data["date7"],'add_time',$order7Num=0);
        //获取七日中的第七天的同行平均数
        $sevenPeAverage7=$this->PeAverage(count($sevenPeerOrder),$sevenPeShop7);
        $SevenDT=[
            'date1'=>$data["date1"],
            'sevenMyOrder1'=>$sevenMyOrder1,
            'sevenPeAverage1'=>$sevenPeAverage1,
            'date2'=>$data["date2"],
            'sevenMyOrder2'=>$sevenMyOrder2,
            'sevenPeAverage2'=>$sevenPeAverage2,
            'date3'=>$data["date3"],
            'sevenMyOrder3'=>$sevenMyOrder3,
            'sevenPeAverage3'=>$sevenPeAverage3,
            'date4'=>$data["date4"],
            'sevenMyOrder4'=>$sevenMyOrder4,
            'sevenPeAverage4'=>$sevenPeAverage4,
            'date5'=>$data["date5"],
            'sevenMyOrder5'=>$sevenMyOrder5,
            'sevenPeAverage5'=>$sevenPeAverage5,
            'date6'=>$data["date6"],
            'sevenMyOrder6'=>$sevenMyOrder6,
            'sevenPeAverage6'=>$sevenPeAverage6,
            'date7'=>$data["date7"],
            'sevenMyOrder7'=>$sevenMyOrder7,
            'sevenPeAverage7'=>$sevenPeAverage7,
        ];
        return $SevenDT;
    }

    //三十日下单趋势
    public function ThirtyDownwardTrend($shopid)
    {
        $data=$this->ThirtyOrderTime($shopid);
        //查询三十日的下单顾客
        $downwardTrend=$data["thirtyOrder"];
        //查询三十日其他店铺的下单顾客
        $thirtyPeerOrder=db("order")
            ->field("shop_id,add_time")
            ->where("shop_id!=".$shopid." and date_format(date_sub(now(), interval 30 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for ($i=0;$i<count($thirtyPeerOrder);$i++){
            $thirtyPeerOrder[$i]["add_time"]=date("Y-m-d",$thirtyPeerOrder[$i]["add_time"]);
        }
        /*
         * 三十日下单顾客每日的数量
         */

        //获取三十日中的第一天的顾客量
        $thirtyMyOrder1=seCount($downwardTrend,$data["date1"],'add_time',$downorder1Num=0);

        //获取三十日中的第五天的顾客量
        $thirtyMyOrder5=seCount($downwardTrend,$data["date5"],'add_time',$downorder5Num=0);

        //获取三十日中的第十天的顾客量
        $thirtyMyOrder10=seCount($downwardTrend,$data["date10"],'add_time',$downorder10Num=0);

        //获取三十日中的第十五天的顾客量
        $thirtyMyOrder15=seCount($downwardTrend,$data["date15"],'add_time',$downorder15Num=0);

        //获取三十日中的第二十天的顾客量
        $thirtyMyOrder20=seCount($downwardTrend,$data["date20"],'add_time',$downorder20Num=0);

        //获取三十日中的第二十五天的顾客量
        $thirtyMyOrder25=seCount($downwardTrend,$data["date25"],'add_time',$downorder25Num=0);

        //获取三十日中的第三十天的顾客量
        $thirtyMyOrder30=seCount($downwardTrend,$data["date30"],'add_time',$downorder30Num=0);
        /**
         * 三十日同行店铺下单顾客每日的数量
         */
        //获取三十日中的第一天的下单量
        $thirtyPeOrder1=seCount($thirtyPeerOrder,$data["date1"],'add_time',$order1Num=0);
        $thirtyPeShop1=seShopCount($thirtyPeerOrder,$data["date1"],'add_time',$order1Num=0);
        //获取三十日中的第一天的同行平均数
        $thirtyPeAverage1=$this->PeAverage(count($thirtyPeerOrder),$thirtyPeShop1);

        //获取三十日中的第五天的下单量
        $thirtyPeOrder5=seCount($thirtyPeerOrder,$data["date5"],'add_time',$order5Num=0);
        $thirtyPeShop5=seShopCount($thirtyPeerOrder,$data["date5"],'add_time',$order5Num=0);
        //获取三十日中的第五天的同行平均数
        $thirtyPeAverage5=$this->PeAverage(count($thirtyPeerOrder),$thirtyPeShop5);

        //获取三十日中的第十天的下单量
        $thirtyPeOrder10=seCount($thirtyPeerOrder,$data["date10"],'add_time',$order10Num=0);
        $thirtyPeShop10=seShopCount($thirtyPeerOrder,$data["date10"],'add_time',$order10Num=0);
        //获取三十日中的第十天的同行平均数
        $thirtyPeAverage10=$this->PeAverage(count($thirtyPeerOrder),$thirtyPeShop10);

        //获取三十日中的第十五天的下单量
        $thirtyPeOrder15=seCount($thirtyPeerOrder,$data["date15"],'add_time',$order15Num=0);
        $thirtyPeShop15=seShopCount($thirtyPeerOrder,$data["date15"],'add_time',$order15Num=0);
        //获取三十日中的第十五天的同行平均数
        $thirtyPeAverage15=$this->PeAverage(count($thirtyPeerOrder),$thirtyPeShop15);

        //获取三十日中的第二十天的下单量
        $thirtyPeOrder20=seCount($thirtyPeerOrder,$data["date20"],'add_time',$order20Num=0);
        $thirtyPeShop20=seShopCount($thirtyPeerOrder,$data["date20"],'add_time',$order20Num=0);
        //获取三十日中的第二十天的同行平均数
        $thirtyPeAverage20=$this->PeAverage(count($thirtyPeerOrder),$thirtyPeShop20);

        //获取三十日中的第二十五天的下单量
        $thirtyPeOrder25=seCount($thirtyPeerOrder,$data["date25"],'add_time',$order25Num=0);
        $thirtyPeShop25=seShopCount($thirtyPeerOrder,$data["date25"],'add_time',$order25Num=0);
        //获取三十日中的第二十五天的同行平均数
        $thirtyPeAverage25=$this->PeAverage(count($thirtyPeerOrder),$thirtyPeShop25);

        //获取三十日中的第三十天的下单量
        $thirtyPeOrder30=seCount($thirtyPeerOrder,$data["date30"],'add_time',$order30Num=0);
        $thirtyPeShop30=seShopCount($thirtyPeerOrder,$data["date30"],'add_time',$order30Num=0);
        //获取三十日中的第三十天的同行平均数
        $thirtyPeAverage30=$this->PeAverage(count($thirtyPeerOrder),$thirtyPeShop30);
        $ThirtyDT=[
            'date1'=>$data["date1"],
            'thirtyMyOrder1'=>$thirtyMyOrder1,
            'thirtyPeAverage1'=>$thirtyPeAverage1,
            'date5'=>$data["date5"],
            'thirtyMyOrder5'=>$thirtyMyOrder5,
            'thirtyPeAverage5'=>$thirtyPeAverage5,
            'date10'=>$data["date10"],
            'thirtyMyOrder10'=>$thirtyMyOrder10,
            'thirtyPeAverage10'=>$thirtyPeAverage10,
            'date15'=>$data["date15"],
            'thirtyMyOrder15'=>$thirtyMyOrder15,
            'thirtyPeAverage15'=>$thirtyPeAverage15,
            'date20'=>$data["date20"],
            'thirtyMyOrder20'=>$thirtyMyOrder20,
            'thirtyPeAverage20'=>$thirtyPeAverage20,
            'date25'=>$data["date25"],
            'thirtyMyOrder25'=>$thirtyMyOrder25,
            'thirtyPeAverage25'=>$thirtyPeAverage25,
            'date30'=>$data["date30"],
            'thirtyMyOrder30'=>$thirtyMyOrder30,
            'thirtyPeAverage30'=>$thirtyPeAverage30,
        ];
        return $ThirtyDT;
    }
    //昨日订单和时间
    public function YesOrderTime($shopid)
    {
        //昨日订单
        $YesOrder=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->select();
        for($a=0;$a<count($YesOrder);$a++){
            $YesOrder[$a]["add_time"]=date("H",$YesOrder[$a]["add_time"]);
        }
        //昨日之前所有的订单
        $AllBeyesOrder=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') < date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->select();
        for($b=0;$b<count($AllBeyesOrder);$b++){
            $AllBeyesOrder[$b]["add_time"]=date("H",$AllBeyesOrder[$b]["add_time"]);
        }
        //昨日同行订单
        $YesPeOrder=db("order")
            ->field("user_id,shop_id,add_time")
            ->where("shop_id!=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->select();
        for($c=0;$c<count($YesPeOrder);$c++){
            $YesPeOrder[$c]["add_time"]=date("H",$YesPeOrder[$c]["add_time"]);
        }
        //昨日之前所有的同行订单
        $AllBeyesPeOrder=db("order")
            ->field("user_id,shop_id,add_time")
            ->where("shop_id!=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') < date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->select();
        for($d=0;$d<count($AllBeyesPeOrder);$d++){
            $AllBeyesPeOrder[$d]["add_time"]=date("H",$AllBeyesPeOrder[$d]["add_time"]);
        }
        $date0 = date('H', strtotime(-date('H') ."hours"));
        $date4 = date('H', strtotime(-date('H')+4 ."hours"));
        $date8 = date('H', strtotime(-date('H')+8 ."hours"));
        $date12 = date('H', strtotime(-date('H')+12 ."hours"));
        $date16 = date('H', strtotime(-date('H')+16 ."hours"));
        $date20 = date('H', strtotime(-date('H')+20 ."hours"));
        $date24 = date('H', strtotime(-date('H')+24 ."hours"));

        $arrTime=[
            'YesOrder'=>$YesOrder,
            'AllBeyesOrder'=>$AllBeyesOrder,
            'YesPeOrder'=>$YesPeOrder,
            'AllBeyesPeOrder'=>$AllBeyesPeOrder,
            'date0'=>$date0,
            'date4'=>$date4,
            'date8'=>$date8,
            'date12'=>$date12,
            'date16'=>$date16,
            'date20'=>$date20,
            'date24'=>$date24,

        ];
        return $arrTime;
    }
    //昨日新客
    public function YesNewGuest($shopid)
    {
        $data=$this->YesOrderTime($shopid);
        //昨日订单
        $YesOrder=$data["YesOrder"];
        //昨日之前所有的订单
        $AllBeyesOrder=$data["AllBeyesOrder"];
        //昨日同行订单
        $YesPeOrder=$data["YesPeOrder"];
        //昨日之前所有的同行订单
        $AllBeyesPeOrder=$data["AllBeyesPeOrder"];

        /**
         * 查询我的店铺新客
         */
        //查找昨日顾客不存在于昨日之前顾客(新客)
        $yesNewGuest=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['user_id'],$bv['user_id']);
            return $r===0 ? strcmp($av['user_id'],$bv['user_id']) : $r;
        };
        //查询俩个二维数组中的差集
        $yesNewGuest=array_values(array_udiff($YesOrder, $AllBeyesOrder, $cmp));
        $d=array_udiff($AllBeyesOrder, $YesOrder, $cmp);
        foreach($d as &$dv) {
            $yesNewGuest[]=$dv;
            unset($d);
        }
        if(count($yesNewGuest)==count($YesOrder)+count($AllBeyesOrder)){
            $yesNewGuest=$YesOrder;
        }
        /**
         * 查询同行店铺新客
         */
        //查找昨日顾客不存在于昨日之前顾客(新客)
        $yesPeNewGuest=array();

        //查询俩个二维数组中的差集
        $yesPeNewGuest=array_values(array_udiff($YesPeOrder, $AllBeyesPeOrder, $cmp));
        $Ped=array_udiff($AllBeyesPeOrder, $YesPeOrder, $cmp);
        foreach($Ped as &$dv) {
            $yesNewGuest[]=$dv;
            unset($Ped);
        }
        if(count($yesPeNewGuest)==count($YesPeOrder)+count($AllBeyesPeOrder)){
            $yesPeNewGuest=$YesPeOrder;
        }

        /*
         * 昨日下单新客每段时间的数量
         */
        //获取昨日中的0点
        $NewGuestNum0=seCount($yesNewGuest,$data["date0"],'add_time',$NewGuestNum0=0);
        //获取昨日中的4点
        $NewGuestNum4=seCount($yesNewGuest,$data["date4"],'add_time',$NewGuestNum4=0);
        //获取昨日中的8点
        $NewGuestNum8=seCount($yesNewGuest,$data["date8"],'add_time',$NewGuestNum8=0);
        //获取昨日中的12点
        $NewGuestNum12=seCount($yesNewGuest,$data["date12"],'add_time',$NewGuestNum12=0);
        //获取昨日中的16点
        $NewGuestNum16=seCount($yesNewGuest,$data["date16"],'add_time',$NewGuestNum16=0);
        //获取昨日中的20点
        $NewGuestNum20=seCount($yesNewGuest,$data["date20"],'add_time',$NewGuestNum20=0);
        //获取昨日中的24点
        $NewGuestNum24=seCount($yesNewGuest,$data["date24"],'add_time',$NewGuestNum24=0);
        /**
         * 同行昨日新客每段时间的数量
         */
        //0点的下单量
        $NewPeGuestNum0=seCount($yesPeNewGuest,$data["date0"],'add_time',$NewPeGuestNum0=0);
        $yesPeShop0=seShopCount($yesPeNewGuest,$data["date0"],'add_time',$yesPeShop0=0);
        //0点的同行平均数
        $yesPeAverage0=$this->PeAverage($NewPeGuestNum0,$yesPeShop0);

        //4点的下单量
        $NewPeGuestNum4=seCount($yesPeNewGuest,$data["date4"],'add_time',$NewPeGuestNum4=0);
        $yesPeShop4=seShopCount($yesPeNewGuest,$data["date4"],'add_time',$yesPeShop4=0);
        //4点的同行平均数
        $yesPeAverage4=$this->PeAverage($NewPeGuestNum4,$yesPeShop4);

        //8点的下单量
        $NewPeGuestNum8=seCount($yesPeNewGuest,$data["date8"],'add_time',$NewPeGuestNum8=0);
        $yesPeShop8=seShopCount($yesPeNewGuest,$data["date8"],'add_time',$yesPeShop8=0);
        //8点的同行平均数
        $yesPeAverage8=$this->PeAverage($NewPeGuestNum8,$yesPeShop8);

        //12点的下单量和店铺数
        $NewPeGuestNum12=seCount($yesPeNewGuest,$data["date12"],'add_time',$NewPeGuestNum12=0);
        $yesPeShop12=seShopCount($yesPeNewGuest,$data["date12"],'add_time',$yesPeShop12=0);
        //12点的同行平均数
        $yesPeAverage12=$this->PeAverage($NewPeGuestNum12,$yesPeShop12);

        //16点的下单量
        $NewPeGuestNum16=seCount($yesPeNewGuest,$data["date16"],'add_time',$NewPeGuestNum16=0);
        $yesPeShop16=seShopCount($yesPeNewGuest,$data["date16"],'add_time',$yesPeShop16=0);
        //16点的同行平均数
        $yesPeAverage16=$this->PeAverage($NewPeGuestNum16,$yesPeShop16);

        //20点的下单量
        $NewPeGuestNum20=seCount($yesPeNewGuest,$data["date20"],'add_time',$NewPeGuestNum20=0);
        $yesPeShop20=seShopCount($yesPeNewGuest,$data["date20"],'add_time',$yesPeShop20=0);
        //20点的同行平均数
        $yesPeAverage20=$this->PeAverage($NewPeGuestNum20,$yesPeShop20);

        //24点的下单量
        $NewPeGuestNum24=seCount($yesPeNewGuest,$data["date24"],'add_time',$NewPeGuestNum24=0);
        $yesPeShop24=seShopCount($yesPeNewGuest,$data["date24"],'add_time',$yesPeShop24=0);
        //24点的同行平均数
        $yesPeAverage24=$this->PeAverage($NewPeGuestNum24,$yesPeShop24);
        $NewGuestTrend=[
            'NewGuestNum0'=>$NewGuestNum0,
            'yesPeAverage0'=>$yesPeAverage0,
            'NewGuestNum4'=>$NewGuestNum4,
            'yesPeAverage4'=>$yesPeAverage4,
            'NewGuestNum8'=>$NewGuestNum8,
            'yesPeAverage8'=>$yesPeAverage8,
            'NewGuestNum12'=>$NewGuestNum12,
            'yesPeAverage12'=>$yesPeAverage12,
            'NewGuestNum16'=>$NewGuestNum16,
            'yesPeAverage16'=>$yesPeAverage16,
            'NewGuestNum20'=>$NewGuestNum20,
            'yesPeAverage20'=>$yesPeAverage20,
            'NewGuestNum24'=>$NewGuestNum24,
            'yesPeAverage24'=>$yesPeAverage24,
        ];

        return $this->message($NewGuestTrend,"成功",2);

    }
    //昨日老客
    public function YesOldGuest($shopid)
    {
        $data=$this->YesOrderTime($shopid);
        //昨日订单
        $YesOrder=$data["YesOrder"];
        //昨日之前所有的订单
        $AllBeyesOrder=$data["AllBeyesOrder"];
        //昨日同行订单
        $YesPeOrder=$data["YesPeOrder"];
        //昨日之前所有的同行订单
        $AllBeyesPeOrder=$data["AllBeyesPeOrder"];
        /**
         * 查询我的店铺新客
         */
        //查找昨日顾客存在于昨日之前顾客(老客)
        $yesOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($YesOrder as $value){
            foreach ($AllBeyesOrder as $val){
                if($value["user_id"]==$val["user_id"]){

                    $yesOldGuest[]=$value;

                }
            }
        }
        /**
         * 查询同行店铺老客
         */
        //查找昨日顾客存在于昨日之前顾客(老客)
        $yesPeOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($YesPeOrder as $value){
            foreach ($AllBeyesPeOrder as $val){
                if($value["user_id"]==$val["user_id"]){

                    $yesPeOldGuest[]=$value;

                }
            }
        }

        /*
         * 昨日下单老客每段时间的数量
         */
        //获取昨日中的0点
        $OldGuestNum0=seCount($yesOldGuest,$data["date0"],'add_time',$OldGuestNum0=0);
        //获取昨日中的4点
        $OldGuestNum4=seCount($yesOldGuest,$data["date4"],'add_time',$OldGuestNum4=0);
        //获取昨日中的8点
        $OldGuestNum8=seCount($yesOldGuest,$data["date8"],'add_time',$OldGuestNum8=0);
        //获取昨日中的12点
        $OldGuestNum12=seCount($yesOldGuest,$data["date12"],'add_time',$OldGuestNum12=0);
        //获取昨日中的16点
        $OldGuestNum16=seCount($yesOldGuest,$data["date16"],'add_time',$OldGuestNum16=0);
        //获取昨日中的20点
        $OldGuestNum20=seCount($yesOldGuest,$data["date20"],'add_time',$OldGuestNum20=0);
        //获取昨日中的24点
        $OldGuestNum24=seCount($yesOldGuest,$data["date24"],'add_time',$OldGuestNum24=0);
        /**
         * 同行昨日老客每段时间的数量
         */
        //0点的老客量
        $OldPeGuestNum0=seCount($yesPeOldGuest,$data["date0"],'add_time',$OldPeGuestNum0=0);
        $yesPeShop0=seShopCount($yesPeOldGuest,$data["date0"],'add_time',$yesPeShop0=0);
        //0点的同行平均数
        $yesPeAverage0=$this->PeAverage($OldPeGuestNum0,$yesPeShop0);

        //4点的老客量
        $OldPeGuestNum4=seCount($yesPeOldGuest,$data["date4"],'add_time',$OldPeGuestNum4=0);
        $yesPeShop4=seShopCount($yesPeOldGuest,$data["date4"],'add_time',$yesPeShop4=0);
        //4点的同行平均数
        $yesPeAverage4=$this->PeAverage($OldPeGuestNum4,$yesPeShop4);

        //8点的老客量
        $OldPeGuestNum8=seCount($yesPeOldGuest,$data["date8"],'add_time',$OldPeGuestNum8=0);
        $yesPeShop8=seShopCount($yesPeOldGuest,$data["date8"],'add_time',$yesPeShop8=0);
        //8点的同行平均数
        $yesPeAverage8=$this->PeAverage($OldPeGuestNum8,$yesPeShop8);

        //12点的老客量和店铺数
        $OldPeGuestNum12=seCount($yesPeOldGuest,$data["date12"],'add_time',$OldPeGuestNum12=0);
        $yesPeShop12=seShopCount($yesPeOldGuest,$data["date12"],'add_time',$yesPeShop12=0);
        //12点的同行平均数
        $yesPeAverage12=$this->PeAverage($OldPeGuestNum12,$yesPeShop12);

        //16点的老客量
        $OldPeGuestNum16=seCount($yesPeOldGuest,$data["date16"],'add_time',$OldPeGuestNum16=0);
        $yesPeShop16=seShopCount($yesPeOldGuest,$data["date16"],'add_time',$yesPeShop16=0);
        //16点的同行平均数
        $yesPeAverage16=$this->PeAverage($OldPeGuestNum16,$yesPeShop16);

        //20点的老客量
        $OldPeGuestNum20=seCount($yesPeOldGuest,$data["date20"],'add_time',$OldPeGuestNum20=0);
        $yesPeShop20=seShopCount($yesPeOldGuest,$data["date20"],'add_time',$yesPeShop20=0);
        //20点的同行平均数
        $yesPeAverage20=$this->PeAverage($OldPeGuestNum20,$yesPeShop20);

        //24点的老客量
        $OldPeGuestNum24=seCount($yesPeOldGuest,$data["date24"],'add_time',$OldPeGuestNum24=0);
        $yesPeShop24=seShopCount($yesPeOldGuest,$data["date24"],'add_time',$yesPeShop24=0);
        //24点的同行平均数
        $yesPeAverage24=$this->PeAverage($OldPeGuestNum24,$yesPeShop24);
        $OldGuestTrend=[
            'OldGuestNum0'=>$OldGuestNum0,
            'yesPeAverage0'=>$yesPeAverage0,
            'OldGuestNum4'=>$OldGuestNum4,
            'yesPeAverage4'=>$yesPeAverage4,
            'OldGuestNum8'=>$OldGuestNum8,
            'yesPeAverage8'=>$yesPeAverage8,
            'OldGuestNum12'=>$OldGuestNum12,
            'yesPeAverage12'=>$yesPeAverage12,
            'OldGuestNum16'=>$OldGuestNum16,
            'yesPeAverage16'=>$yesPeAverage16,
            'OldGuestNum20'=>$OldGuestNum20,
            'yesPeAverage20'=>$yesPeAverage20,
            'OldGuestNum24'=>$OldGuestNum24,
            'yesPeAverage24'=>$yesPeAverage24,
        ];
        return $this->message($OldGuestTrend,"成功",2);

    }


    //七日时间段
    public function SevenOrderTime($shopid)
    {
        //七日订单
        $SevenOrder=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for($a=0;$a<count($SevenOrder);$a++){
            $SevenOrder[$a]["add_time"]=date("Y-m-d",$SevenOrder[$a]["add_time"]);
        }
        //查询七日其他店铺的下单顾客
        $sevenPeerOrder=db("order")
            ->field("user_id,shop_id,add_time")
            ->where("shop_id!=".$shopid." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for ($i=0;$i<count($sevenPeerOrder);$i++){
            $sevenPeerOrder[$i]["add_time"]=date("Y-m-d",$sevenPeerOrder[$i]["add_time"]);
        }
        //七日之前所有的订单
        $AllBeseOrder=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>7 ")
            ->select();
        for($b=0;$b<count($AllBeseOrder);$b++){
            $AllBeseOrder[$b]["add_time"]=date("Y-m-d",$AllBeseOrder[$b]["add_time"]);
        }
        //七日之前其他店铺所有的订单
        $AllBesePeOrder=db("order")
            ->field("user_id,shop_id,add_time")
            ->where("shop_id!=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>7 ")
            ->select();
        for($b=0;$b<count($AllBesePeOrder);$b++){
            $AllBesePeOrder[$b]["add_time"]=date("Y-m-d",$AllBesePeOrder[$b]["add_time"]);
        }
        $date1 = date('Y-m-d', strtotime('-1 days'));
        $date2 = date('Y-m-d', strtotime('-2 days'));
        $date3 = date('Y-m-d', strtotime('-3 days'));
        $date4 = date('Y-m-d', strtotime('-4 days'));
        $date5 = date('Y-m-d', strtotime('-5 days'));
        $date6 = date('Y-m-d', strtotime('-6 days'));
        $date7 = date('Y-m-d', strtotime('-7 days'));

        $arrTime=[
            'SevenOrder'=>$SevenOrder,
            'AllBeseOrder'=>$AllBeseOrder,
            'sevenPeerOrder'=>$sevenPeerOrder,
            'AllBesePeOrder'=>$AllBesePeOrder,
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
    //七日新客
    public function SevenNewGuest($shopid)
    {
        //获取时间和订单
        $data=$this->SevenOrderTime($shopid);
        $SevenOrder=$data["SevenOrder"];//七日订单
        $AllBeseOrder=$data["AllBeseOrder"];//七日之前所有订单
        $sevenPeerOrder=$data["sevenPeerOrder"];//七日其他店铺订单
        $AllBesePeOrder=$data["AllBesePeOrder"];//七日之前其他店铺所有订单

        //查找七日顾客不存在于七日之前顾客(新客)
        $sevenNewGuest=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['user_id'],$bv['user_id']);
            return $r===0 ? strcmp($av['user_id'],$bv['user_id']) : $r;
        };
        //查询俩个二维数组中的差集
        $sevenNewGuest=array_values(array_udiff($SevenOrder, $AllBeseOrder, $cmp));
        $d=array_udiff($AllBeseOrder, $SevenOrder, $cmp);
        foreach($d as &$dv) {
            $sevenNewGuest[]=$dv;
            unset($d);
        }
        if(count($sevenNewGuest)==count($SevenOrder)+count($AllBeseOrder)){
            $sevenNewGuest=$SevenOrder;
        }

        //查找七日顾客不存在于七日之前顾客(其他店铺新客)
        //查询俩个二维数组中的差集
        $sevenPeNewGuest=array_values(array_udiff($sevenPeerOrder, $AllBesePeOrder, $cmp));
        $pd=array_udiff($AllBesePeOrder, $sevenPeerOrder, $cmp);
        foreach($pd as &$dv) {
            $sevenPeNewGuest[]=$dv;
            unset($pd);
        }
        if(count($sevenPeNewGuest)==count($sevenPeerOrder)+count($AllBesePeOrder)){
            $sevenPeNewGuest=$sevenPeerOrder;
        }

        /*
         * 七日下单新客每段时间的数量
         */
        //获取七日中的第一天
        $NewGuestNum1=seCount($sevenNewGuest,$data["date1"],'add_time',$NewGuestNum1=0);
        //获取七日中的第二天
        $NewGuestNum2=seCount($sevenNewGuest,$data["date2"],'add_time',$NewGuestNum2=0);
        //获取七日中的第三天
        $NewGuestNum3=seCount($sevenNewGuest,$data["date3"],'add_time',$NewGuestNum3=0);
        //获取七日中的第四天
        $NewGuestNum4=seCount($sevenNewGuest,$data["date4"],'add_time',$NewGuestNum4=0);
        //获取七日中的第五天
        $NewGuestNum5=seCount($sevenNewGuest,$data["date5"],'add_time',$NewGuestNum5=0);
        //获取七日中的第六天
        $NewGuestNum6=seCount($sevenNewGuest,$data["date6"],'add_time',$NewGuestNum6=0);
        //获取七日中的第七天
        $NewGuestNum7=seCount($sevenNewGuest,$data["date7"],'add_time',$NewGuestNum7=0);

        /**
         * 七日其他店铺下单新客每段时间的数量
         */
        //获取七日中的第一天的新客量
        $NewPeGuestNum1=seCount($sevenPeNewGuest,$data["date1"],'add_time',$NewPeGuestNum1=0);
        $sevenPeShop1=seShopCount($sevenPeNewGuest,$data["date1"],'add_time',$sevenPeShop1=0);
        //获取七日中的第一天的同行平均数
        $sevenPeAverage1=$this->PeAverage($NewPeGuestNum1,$sevenPeShop1);

        //获取七日中的第二天的新客量
        $NewPeGuestNum2=seCount($sevenPeNewGuest,$data["date2"],'add_time',$NewPeGuestNum2=0);
        $sevenPeShop2=seShopCount($sevenPeNewGuest,$data["date2"],'add_time',$sevenPeShop2=0);
        //获取七日中的第二天的同行平均数
        $sevenPeAverage2=$this->PeAverage($NewPeGuestNum2,$sevenPeShop2);

        //获取七日中的第三天的新客量
        $NewPeGuestNum3=seCount($sevenPeNewGuest,$data["date3"],'add_time',$NewPeGuestNum3=0);
        $sevenPeShop3=seShopCount($sevenPeNewGuest,$data["date3"],'add_time',$sevenPeShop3=0);
        //获取七日中的第三天的同行平均数
        $sevenPeAverage3=$this->PeAverage($NewPeGuestNum3,$sevenPeShop3);

        //获取七日中的第四天的新客量
        $NewPeGuestNum4=seCount($sevenPeNewGuest,$data["date4"],'add_time',$NewPeGuestNum4=0);
        $sevenPeShop4=seShopCount($sevenPeNewGuest,$data["date4"],'add_time',$sevenPeShop4=0);
        //获取七日中的第四天的同行平均数
        $sevenPeAverage4=$this->PeAverage($NewPeGuestNum4,$sevenPeShop4);

        //获取七日中的第五天的新客量
        $NewPeGuestNum5=seCount($sevenPeNewGuest,$data["date5"],'add_time',$NewPeGuestNum5=0);
        $sevenPeShop5=seShopCount($sevenPeNewGuest,$data["date5"],'add_time',$sevenPeShop5=0);
        //获取七日中的第五天的同行平均数
        $sevenPeAverage5=$this->PeAverage($NewPeGuestNum5,$sevenPeShop5);

        //获取七日中的第六天的新客量
        $NewPeGuestNum6=seCount($sevenPeNewGuest,$data["date6"],'add_time',$NewPeGuestNum6=0);
        $sevenPeShop6=seShopCount($sevenPeNewGuest,$data["date6"],'add_time',$sevenPeShop6=0);
        //获取七日中的第六天的同行平均数
        $sevenPeAverage6=$this->PeAverage($NewPeGuestNum6,$sevenPeShop6);

        //获取七日中的第七天的新客量
        $NewPeGuestNum7=seCount($sevenPeNewGuest,$data["date7"],'add_time',$NewPeGuestNum7=0);
        $sevenPeShop7=seShopCount($sevenPeNewGuest,$data["date7"],'add_time',$sevenPeShop7=0);
        //获取七日中的第七天的同行平均数
        $sevenPeAverage7=$this->PeAverage($NewPeGuestNum7,$sevenPeShop7);

        $NewGuestTrend=[
            'date1'=>$data["date1"],
            'NewGuestNum1'=>$NewGuestNum1,
            'sevenPeAverage1'=>$sevenPeAverage1,
            'date2'=>$data["date2"],
            'NewGuestNum2'=>$NewGuestNum2,
            'sevenPeAverage2'=>$sevenPeAverage2,
            'date3'=>$data["date3"],
            'NewGuestNum3'=>$NewGuestNum3,
            'sevenPeAverage3'=>$sevenPeAverage3,
            'date4'=>$data["date4"],
            'NewGuestNum4'=>$NewGuestNum4,
            'sevenPeAverage4'=>$sevenPeAverage4,
            'date5'=>$data["date5"],
            'NewGuestNum5'=>$NewGuestNum5,
            'sevenPeAverage5'=>$sevenPeAverage5,
            'date6'=>$data["date6"],
            'NewGuestNum6'=>$NewGuestNum6,
            'sevenPeAverage6'=>$sevenPeAverage6,
            'date7'=>$data["date7"],
            'NewGuestNum7'=>$NewGuestNum7,
            'sevenPeAverage7'=>$sevenPeAverage7,
        ];

        return $this->message($NewGuestTrend,"成功",2);

    }

    //七日老客
    public function SevenOldGuest($shopid)
    {
        //获取时间和订单
        $data=$this->SevenOrderTime($shopid);
        $SevenOrder=$data["SevenOrder"];//七日订单
        $AllBeseOrder=$data["AllBeseOrder"];//七日之前所有订单
        $sevenPeerOrder=$data["sevenPeerOrder"];//七日其他店铺订单
        $AllBesePeOrder=$data["AllBesePeOrder"];//七日之前其他店铺所有订单

        //查找七日顾客存在于七日之前顾客(老客)
        $sevenOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($SevenOrder as $value){
            foreach ($AllBeseOrder as $val){
                if($value["user_id"]==$val["user_id"]){

                    $sevenOldGuest[]=$value;

                }
            }
        }

        //查找七日顾客存在于七日之前顾客(其他店铺老客)
        $sevenPeOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($sevenPeerOrder as $value2){
            foreach ($AllBesePeOrder as $val2){
                if($value2["user_id"]==$val2["user_id"]){

                    $sevenPeOldGuest[]=$value2;

                }
            }
        }

        /*
         * 七日下单新客每段时间的数量
         */
        //获取七日中的第一天
        $OldGuestNum1=seCount($sevenOldGuest,$data["date1"],'add_time',$OldGuestNum1=0);
        //获取七日中的第二天
        $OldGuestNum2=seCount($sevenOldGuest,$data["date2"],'add_time',$OldGuestNum2=0);
        //获取七日中的第三天
        $OldGuestNum3=seCount($sevenOldGuest,$data["date3"],'add_time',$OldGuestNum3=0);
        //获取七日中的第四天
        $OldGuestNum4=seCount($sevenOldGuest,$data["date4"],'add_time',$OldGuestNum4=0);
        //获取七日中的第五天
        $OldGuestNum5=seCount($sevenOldGuest,$data["date5"],'add_time',$OldGuestNum5=0);
        //获取七日中的第六天
        $OldGuestNum6=seCount($sevenOldGuest,$data["date6"],'add_time',$OldGuestNum6=0);
        //获取七日中的第七天
        $OldGuestNum7=seCount($sevenOldGuest,$data["date7"],'add_time',$OldGuestNum7=0);

        /**
         * 七日其他店铺下单老客每段时间的数量
         */
        //获取七日中的第一天的老客量
        $OldPeGuestNum1=seCount($sevenPeOldGuest,$data["date1"],'add_time',$OldPeGuestNum1=0);
        $sevenPeShop1=seShopCount($sevenPeOldGuest,$data["date1"],'add_time',$sevenPeShop1=0);
        //获取七日中的第一天的同行平均数
        $sevenPeAverage1=$this->PeAverage($OldPeGuestNum1,$sevenPeShop1);

        //获取七日中的第二天的老客量
        $OldPeGuestNum2=seCount($sevenPeOldGuest,$data["date2"],'add_time',$OldPeGuestNum2=0);
        $sevenPeShop2=seShopCount($sevenPeOldGuest,$data["date2"],'add_time',$sevenPeShop2=0);
        //获取七日中的第二天的同行平均数
        $sevenPeAverage2=$this->PeAverage($OldPeGuestNum2,$sevenPeShop2);

        //获取七日中的第三天的老客量
        $OldPeGuestNum3=seCount($sevenPeOldGuest,$data["date3"],'add_time',$OldPeGuestNum3=0);
        $sevenPeShop3=seShopCount($sevenPeOldGuest,$data["date3"],'add_time',$sevenPeShop3=0);
        //获取七日中的第三天的同行平均数
        $sevenPeAverage3=$this->PeAverage($OldPeGuestNum3,$sevenPeShop3);

        //获取七日中的第四天的老客量
        $OldPeGuestNum4=seCount($sevenPeOldGuest,$data["date4"],'add_time',$OldPeGuestNum4=0);
        $sevenPeShop4=seShopCount($sevenPeOldGuest,$data["date4"],'add_time',$sevenPeShop4=0);
        //获取七日中的第四天的同行平均数
        $sevenPeAverage4=$this->PeAverage($OldPeGuestNum4,$sevenPeShop4);

        //获取七日中的第五天的老客量
        $OldPeGuestNum5=seCount($sevenPeOldGuest,$data["date5"],'add_time',$OldPeGuestNum5=0);
        $sevenPeShop5=seShopCount($sevenPeOldGuest,$data["date5"],'add_time',$sevenPeShop5=0);
        //获取七日中的第五天的同行平均数
        $sevenPeAverage5=$this->PeAverage($OldPeGuestNum5,$sevenPeShop5);

        //获取七日中的第六天的老客量
        $OldPeGuestNum6=seCount($sevenPeOldGuest,$data["date6"],'add_time',$OldPeGuestNum6=0);
        $sevenPeShop6=seShopCount($sevenPeOldGuest,$data["date6"],'add_time',$sevenPeShop6=0);
        //获取七日中的第六天的同行平均数
        $sevenPeAverage6=$this->PeAverage($OldPeGuestNum6,$sevenPeShop6);

        //获取七日中的第七天的老客量
        $OldPeGuestNum7=seCount($sevenPeOldGuest,$data["date7"],'add_time',$OldPeGuestNum7=0);
        $sevenPeShop7=seShopCount($sevenPeOldGuest,$data["date7"],'add_time',$sevenPeShop7=0);
        //获取七日中的第七天的同行平均数
        $sevenPeAverage7=$this->PeAverage($OldPeGuestNum7,$sevenPeShop7);

        $OldGuestTrend=[
            'date1'=>$data["date1"],
            'OldGuestNum1'=>$OldGuestNum1,
            'sevenPeAverage1'=>$sevenPeAverage1,
            'date2'=>$data["date2"],
            'OldGuestNum2'=>$OldGuestNum2,
            'sevenPeAverage2'=>$sevenPeAverage2,
            'date3'=>$data["date3"],
            'OldGuestNum3'=>$OldGuestNum3,
            'sevenPeAverage3'=>$sevenPeAverage3,
            'date4'=>$data["date4"],
            'OldGuestNum4'=>$OldGuestNum4,
            'sevenPeAverage4'=>$sevenPeAverage4,
            'date5'=>$data["date5"],
            'OldGuestNum5'=>$OldGuestNum5,
            'sevenPeAverage5'=>$sevenPeAverage5,
            'date6'=>$data["date6"],
            'OldGuestNum6'=>$OldGuestNum6,
            'sevenPeAverage6'=>$sevenPeAverage6,
            'date7'=>$data["date7"],
            'OldGuestNum7'=>$OldGuestNum7,
            'sevenPeAverage7'=>$sevenPeAverage7,
        ];

        return $this->message($OldGuestTrend,"成功",2);

    }

    //三十日订单和时间
    public function ThirtyOrderTime($shopid)
    {
        //前三十天下单量
        $thirtyOrder=db("order")->field("user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 30 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for ($i=0;$i<count($thirtyOrder);$i++){
            $thirtyOrder[$i]["add_time"]=date("Y-m-d",$thirtyOrder[$i]["add_time"]);
        }
        //三十日之前所有的下单顾客
        $AllbethOrder=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>30 ")
            ->select();
        for($b=0;$b<count($AllbethOrder);$b++){
            $AllbethOrder[$b]["add_time"]=date("Y-m-d",$AllbethOrder[$b]["add_time"]);
        }
        //前三十天其他店铺下单量
        $thirtyPeOrder=db("order")->field("user_id,shop_id,add_time")
            ->where("shop_id!=".$shopid." and date_format(date_sub(now(), interval 30 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for ($i=0;$i<count($thirtyPeOrder);$i++){
            $thirtyPeOrder[$i]["add_time"]=date("Y-m-d",$thirtyPeOrder[$i]["add_time"]);
        }
        //三十日之前其他店铺所有的下单顾客
        $AllbethPeOrder=db("order")
            ->field("user_id,shop_id,add_time")
            ->where("shop_id!=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>30 ")
            ->select();
        for($b=0;$b<count($AllbethPeOrder);$b++){
            $AllbethPeOrder[$b]["add_time"]=date("Y-m-d",$AllbethPeOrder[$b]["add_time"]);
        }
        $date1 = date('Y-m-d', strtotime('-1 days'));
        $date5 = date('Y-m-d', strtotime('-5 days'));
        $date10 = date('Y-m-d', strtotime('-10 days'));
        $date15 = date('Y-m-d', strtotime('-15 days'));
        $date20 = date('Y-m-d', strtotime('-20 days'));
        $date25 = date('Y-m-d', strtotime('-25 days'));
        $date30 = date('Y-m-d', strtotime('-30 days'));

        $arrTime=[
            'thirtyOrder'=>$thirtyOrder,
            'AllbethOrder'=>$AllbethOrder,
            'thirtyPeOrder'=>$thirtyPeOrder,
            'AllbethPeOrder'=>$AllbethPeOrder,
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

    //三十日新客
    public function ThirtyNewGuest($shopid)
    {
        //获取时间和订单
        $data=$this->ThirtyOrderTime($shopid);
        $ThirtyOrder=$data["thirtyOrder"];//三十日订单
        $AllbethOrder=$data["AllbethOrder"];//三十日之前所有订单
        $ThirtyPeOrder=$data["thirtyPeOrder"];//三十日其他店铺订单
        $AllbethPeOrder=$data["AllbethPeOrder"];//三十日之前其他店铺所有订单

        //查找三十日顾客不存在于三十日之前顾客(新客)
        $thirtyNewGuest=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['user_id'],$bv['user_id']);
            return $r===0 ? strcmp($av['user_id'],$bv['user_id']) : $r;
        };
        //查询俩个二维数组中的差集
        $thirtyNewGuest=array_values(array_udiff($ThirtyOrder, $AllbethOrder, $cmp));
        $d=array_udiff($AllbethOrder, $ThirtyOrder, $cmp);
        foreach($d as &$dv) {
            $thirtyNewGuest[]=$dv;
            unset($d);
        }
        if(count($thirtyNewGuest)==count($ThirtyOrder)+count($AllbethOrder)){
            $thirtyNewGuest=$ThirtyOrder;
        }

        //查找三十日顾客不存在于三十日之前顾客(其他店铺新客)
        //查询俩个二维数组中的差集
        $thirtyPeNewGuest=array_values(array_udiff($ThirtyPeOrder, $AllbethPeOrder, $cmp));
        $Pd=array_udiff($AllbethPeOrder, $ThirtyPeOrder, $cmp);
        foreach($Pd as &$dv) {
            $thirtyPeNewGuest[]=$dv;
            unset($Pd);
        }
        if(count($thirtyPeNewGuest)==count($ThirtyPeOrder)+count($AllbethPeOrder)){
            $thirtyPeNewGuest=$ThirtyPeOrder;
        }

        /*
         * 三十日下单新客每段时间的数量
         */
        //获取三十日中的第一天
        $NewGuestNum1=seCount($thirtyNewGuest,$data["date1"],'add_time',$NewGuestNum1=0);
        //获取三十日中的第五天
        $NewGuestNum5=seCount($thirtyNewGuest,$data["date5"],'add_time',$NewGuestNum5=0);
        //获取三十日中的第十天
        $NewGuestNum10=seCount($thirtyNewGuest,$data["date10"],'add_time',$NewGuestNum10=0);
        //获取三十日中的第十五天
        $NewGuestNum15=seCount($thirtyNewGuest,$data["date15"],'add_time',$NewGuestNum15=0);
        //获取三十日中的第二十天
        $NewGuestNum20=seCount($thirtyNewGuest,$data["date20"],'add_time',$NewGuestNum20=0);
        //获取三十日中的第二十五天
        $NewGuestNum25=seCount($thirtyNewGuest,$data["date25"],'add_time',$NewGuestNum25=0);
        //获取三十日中的第三十天
        $NewGuestNum30=seCount($thirtyNewGuest,$data["date30"],'add_time',$NewGuestNum30=0);

        /**
         * 三十日其他店铺下单新客每段时间的数量
         */
        //获取七日中的第一天的新客量
        $NewPeGuestNum1=seCount($thirtyPeNewGuest,$data["date1"],'add_time',$NewPeGuestNum1=0);
        $thirtyPeShop1=seShopCount($thirtyPeNewGuest,$data["date1"],'add_time',$thirtyPeShop1=0);
        //获取七日中的第一天的同行平均数
        $thirtyPeAverage1=$this->PeAverage($NewPeGuestNum1,$thirtyPeShop1);

        //获取七日中的第五天的新客量
        $NewPeGuestNum5=seCount($thirtyPeNewGuest,$data["date5"],'add_time',$NewPeGuestNum5=0);
        $thirtyPeShop5=seShopCount($thirtyPeNewGuest,$data["date5"],'add_time',$thirtyPeShop5=0);
        //获取七日中的第五天的同行平均数
        $thirtyPeAverage5=$this->PeAverage($NewPeGuestNum5,$thirtyPeShop5);

        //获取七日中的第十天的新客量
        $NewPeGuestNum10=seCount($thirtyPeNewGuest,$data["date10"],'add_time',$NewPeGuestNum10=0);
        $thirtyPeShop10=seShopCount($thirtyPeNewGuest,$data["date10"],'add_time',$thirtyPeShop10=0);
        //获取七日中的第十天的同行平均数
        $thirtyPeAverage10=$this->PeAverage($NewPeGuestNum10,$thirtyPeShop10);

        //获取七日中的第十五天的新客量
        $NewPeGuestNum15=seCount($thirtyPeNewGuest,$data["date15"],'add_time',$NewPeGuestNum15=0);
        $thirtyPeShop15=seShopCount($thirtyPeNewGuest,$data["date15"],'add_time',$thirtyPeShop15=0);
        //获取七日中的第十五天的同行平均数
        $thirtyPeAverage15=$this->PeAverage($NewPeGuestNum15,$thirtyPeShop15);

        //获取七日中的第二十天的新客量
        $NewPeGuestNum20=seCount($thirtyPeNewGuest,$data["date20"],'add_time',$NewPeGuestNum20=0);
        $thirtyPeShop20=seShopCount($thirtyPeNewGuest,$data["date20"],'add_time',$thirtyPeShop20=0);
        //获取七日中的第二十天的同行平均数
        $thirtyPeAverage20=$this->PeAverage($NewPeGuestNum20,$thirtyPeShop20);

        //获取七日中的第二十五天的新客量
        $NewPeGuestNum25=seCount($thirtyPeNewGuest,$data["date25"],'add_time',$NewPeGuestNum25=0);
        $thirtyPeShop25=seShopCount($thirtyPeNewGuest,$data["date25"],'add_time',$thirtyPeShop25=0);
        //获取七日中的第二十五天的同行平均数
        $thirtyPeAverage25=$this->PeAverage($NewPeGuestNum25,$thirtyPeShop25);

        //获取七日中的第三十天的新客量
        $NewPeGuestNum30=seCount($thirtyPeNewGuest,$data["date30"],'add_time',$NewPeGuestNum30=0);
        $thirtyPeShop30=seShopCount($thirtyPeNewGuest,$data["date30"],'add_time',$thirtyPeShop30=0);
        //获取七日中的第三十天的同行平均数
        $thirtyPeAverage30=$this->PeAverage($NewPeGuestNum30,$thirtyPeShop30);

        $NewGuestTrend=[
            'date1'=>$data["date1"],
            'NewGuestNum1'=>$NewGuestNum1,
            'thirtyPeAverage1'=>$thirtyPeAverage1,
            'date5'=>$data["date5"],
            'NewGuestNum5'=>$NewGuestNum5,
            'thirtyPeAverage5'=>$thirtyPeAverage5,
            'date10'=>$data["date10"],
            'NewGuestNu10'=>$NewGuestNum10,
            'thirtyPeAverage10'=>$thirtyPeAverage10,
            'date15'=>$data["date15"],
            'NewGuestNum15'=>$NewGuestNum15,
            'thirtyPeAverage15'=>$thirtyPeAverage15,
            'date20'=>$data["date20"],
            'NewGuestNum20'=>$NewGuestNum20,
            'thirtyPeAverage20'=>$thirtyPeAverage20,
            'date25'=>$data["date25"],
            'NewGuestNum25'=>$NewGuestNum25,
            'thirtyPeAverage25'=>$thirtyPeAverage25,
            'date30'=>$data["date30"],
            'NewGuestNum30'=>$NewGuestNum30,
            'thirtyPeAverage30'=>$thirtyPeAverage30,
        ];

        return $this->message($NewGuestTrend,"成功",2);

    }

    //三十日老客
    public function ThirtyOldGuest($shopid)
    {
        //获取时间和订单
        $data=$this->ThirtyOrderTime($shopid);
        $ThirtyOrder=$data["thirtyOrder"];//三十日订单
        $AllBethOrder=$data["AllbethOrder"];//三十日之前所有订单
        $ThirtyPeOrder=$data["thirtyPeOrder"];//三十日其他店铺订单
        $AllbethPeOrder=$data["AllbethPeOrder"];//三十日之前其他店铺所有订单

        //查找三十日顾客存在于三十日之前顾客(老客)
        $thirtyOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($ThirtyOrder as $value){
            foreach ($AllBethOrder as $val){
                if($value["user_id"]==$val["user_id"]){

                    $thirtyOldGuest[]=$value;

                }
            }
        }

        //查找三十日顾客存在于三十日之前顾客(其他店铺老客)
        $thirtyPeOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($ThirtyPeOrder as $value2){
            foreach ($AllbethPeOrder as $val2){
                if($value2["user_id"]==$val2["user_id"]){

                    $thirtyPeOldGuest[]=$value2;

                }
            }
        }

        /*
         * 三十日下单新客每段时间的数量
         */
        //获取三十日中的第一天
        $OldGuestNum1=seCount($thirtyOldGuest,$data["date1"],'add_time',$OldGuestNum1=0);
        //获取三十日中的第五天
        $OldGuestNum5=seCount($thirtyOldGuest,$data["date5"],'add_time',$OldGuestNum5=0);
        //获取三十日中的第十天
        $OldGuestNum10=seCount($thirtyOldGuest,$data["date10"],'add_time',$OldGuestNum10=0);
        //获取三十日中的第十五天
        $OldGuestNum15=seCount($thirtyOldGuest,$data["date15"],'add_time',$OldGuestNum15=0);
        //获取三十日中的第二十天
        $OldGuestNum20=seCount($thirtyOldGuest,$data["date20"],'add_time',$OldGuestNum20=0);
        //获取三十日中的第二十五天
        $OldGuestNum25=seCount($thirtyOldGuest,$data["date25"],'add_time',$OldGuestNum25=0);
        //获取三十日中的第三十天
        $OldGuestNum30=seCount($thirtyOldGuest,$data["date30"],'add_time',$OldGuestNum30=0);

        /**
         * 三十日其他店铺下单老客每段时间的数量
         */
        //获取七日中的第一天的老客量
        $OldPeGuestNum1=seCount($thirtyPeOldGuest,$data["date1"],'add_time',$OldPeGuestNum1=0);
        $thirtyPeShop1=seShopCount($thirtyPeOldGuest,$data["date1"],'add_time',$thirtyPeShop1=0);
        //获取七日中的第一天的同行平均数
        $thirtyPeAverage1=$this->PeAverage($OldPeGuestNum1,$thirtyPeShop1);

        //获取七日中的第五天的老客量
        $OldPeGuestNum5=seCount($thirtyPeOldGuest,$data["date5"],'add_time',$OldPeGuestNum5=0);
        $thirtyPeShop5=seShopCount($thirtyPeOldGuest,$data["date5"],'add_time',$thirtyPeShop5=0);
        //获取七日中的第五天的同行平均数
        $thirtyPeAverage5=$this->PeAverage($OldPeGuestNum5,$thirtyPeShop5);

        //获取七日中的第十天的老客量
        $OldPeGuestNum10=seCount($thirtyPeOldGuest,$data["date10"],'add_time',$OldPeGuestNum10=0);
        $thirtyPeShop10=seShopCount($thirtyPeOldGuest,$data["date10"],'add_time',$thirtyPeShop10=0);
        //获取七日中的第十天的同行平均数
        $thirtyPeAverage10=$this->PeAverage($OldPeGuestNum10,$thirtyPeShop10);

        //获取七日中的第十五天的老客量
        $OldPeGuestNum15=seCount($thirtyPeOldGuest,$data["date15"],'add_time',$OldPeGuestNum15=0);
        $thirtyPeShop15=seShopCount($thirtyPeOldGuest,$data["date15"],'add_time',$thirtyPeShop15=0);
        //获取七日中的第十五天的同行平均数
        $thirtyPeAverage15=$this->PeAverage($OldPeGuestNum15,$thirtyPeShop15);

        //获取七日中的第二十天的老客量
        $OldPeGuestNum20=seCount($thirtyPeOldGuest,$data["date20"],'add_time',$OldPeGuestNum20=0);
        $thirtyPeShop20=seShopCount($thirtyPeOldGuest,$data["date20"],'add_time',$thirtyPeShop20=0);
        //获取七日中的第二十天的同行平均数
        $thirtyPeAverage20=$this->PeAverage($OldPeGuestNum20,$thirtyPeShop20);

        //获取七日中的第二十五天的老客量
        $OldPeGuestNum25=seCount($thirtyPeOldGuest,$data["date25"],'add_time',$OldPeGuestNum25=0);
        $thirtyPeShop25=seShopCount($thirtyPeOldGuest,$data["date25"],'add_time',$thirtyPeShop25=0);
        //获取七日中的第二十五天的同行平均数
        $thirtyPeAverage25=$this->PeAverage($OldPeGuestNum25,$thirtyPeShop25);

        //获取七日中的第三十五天的老客量
        $OldPeGuestNum30=seCount($thirtyPeOldGuest,$data["date30"],'add_time',$OldPeGuestNum30=0);
        $thirtyPeShop30=seShopCount($thirtyPeOldGuest,$data["date30"],'add_time',$thirtyPeShop30=0);
        //获取七日中的第三十天的同行平均数
        $thirtyPeAverage30=$this->PeAverage($OldPeGuestNum30,$thirtyPeShop30);

        $OldGuestTrend=[
            'date1'=>$data["date1"],
            'OldGuestNum1'=>$OldGuestNum1,
            'thirtyPeAverage1'=>$thirtyPeAverage1,
            'date5'=>$data["date5"],
            'OldGuestNum5'=>$OldGuestNum5,
            'thirtyPeAverage5'=>$thirtyPeAverage5,
            'date10'=>$data["date10"],
            'OldGuestNum10'=>$OldGuestNum10,
            'thirtyPeAverage10'=>$thirtyPeAverage10,
            'date15'=>$data["date15"],
            'OldGuestNum15'=>$OldGuestNum15,
            'thirtyPeAverage15'=>$thirtyPeAverage15,
            'date20'=>$data["date20"],
            'OldGuestNum20'=>$OldGuestNum20,
            'thirtyPeAverage20'=>$thirtyPeAverage20,
            'date25'=>$data["date25"],
            'OldGuestNum25'=>$OldGuestNum25,
            'thirtyPeAverage25'=>$thirtyPeAverage25,
            'date30'=>$data["date30"],
            'OldGuestNum30'=>$OldGuestNum30,
            'thirtyPeAverage30'=>$thirtyPeAverage30,
        ];

        return $this->message($OldGuestTrend,"成功",2);

    }

    /**
     * @return 昨日顾客分析的总数据显示
     */
    public function ShowYesterdayAnalysis(Request $request)
    {
        $shopid=$request->param("shopid");
        //昨日总览模块的数据
        $YesOverview=$this->YesterdayOverview($shopid);
        //昨日顾客结构的数据
        $YesCS=$this->YesCustomerStructure($shopid);
        //昨日重复下单的数据
        $YesRO=$this->YesRepeatOrder($shopid);
        //昨日下单趋势
        $YesDT=$this->YesDownwardTrend($shopid);
        $time=[
            $YesOverview["date0"],$YesOverview["date4"],$YesOverview["date8"],$YesOverview["date12"],$YesOverview["date16"],$YesOverview["date20"],$YesOverview["date24"]
        ];
        $yesOrder=[
            $YesOverview["yesOrder0"],$YesOverview["yesOrder4"],$YesOverview["yesOrder8"],$YesOverview["yesOrder12"],$YesOverview["yesOrder16"],$YesOverview["yesOrder20"],$YesOverview["yesOrder24"]
        ];
        $yesMyOrder=[
            $YesDT["yesMyOrder0"],$YesDT["yesMyOrder4"],$YesDT["yesMyOrder8"],$YesDT["yesMyOrder12"],$YesDT["yesMyOrder16"],$YesDT["yesMyOrder20"],$YesDT["yesMyOrder24"]
        ];
        $yesPeAverage=[
            $YesDT["yesPeAverage0"],$YesDT["yesPeAverage4"],$YesDT["yesPeAverage8"],$YesDT["yesPeAverage12"],$YesDT["yesPeAverage16"],$YesDT["yesPeAverage20"],$YesDT["yesPeAverage24"]
        ];
        //根据下标匹配数据
        foreach ($time as $k=>$value){
            $arr['time'] = $value;
            $arr['yesOrder'] = $yesOrder[$k];
            $arr['yesMyOrder'] = $yesMyOrder[$k];
            $arr['yesPeAverage'] = $yesPeAverage[$k];
            $tree[] = $arr;
        }
        //将数据全部放入数组中
        $showYesArr=[
            'YesOverview'=>[
                "yesOrderPerson"=>$YesOverview["yesOrderPerson"],
                "yesCompare"=>$YesOverview["yesCompare"],
                "yesNewGuestnum"=>$YesOverview["yesNewGuestnum"],
                "contrastNewGuest"=>$YesOverview["contrastNewGuest"],
                "NewPersonRatio"=>$YesOverview["NewPersonRatio"],
                "yesOldGuest"=>$YesOverview["yesOldGuest"],
                "contrastOldGuest"=>$YesOverview["contrastOldGuest"],
                "OldPersonRatio"=>$YesOverview["OldPersonRatio"],
            ],
            'YesCS'=>$YesCS,
            'YesRO'=>$YesRO,
            'YesDT'=>$tree,
            //'YesDT'=>$YesDT,
        ];
        return $this->message($showYesArr,"成功",2);
    }

    /**
     * @return 七日顾客分析的总数据显示
     */
    public function ShowSevenAnalysis(Request $request)
    {
        $shopid=$request->param("shopid");
        //七日总览模块的数据
        $SevenOverview=$this->SevenOverview($shopid);
        //七日顾客结构的数据
        $SevenCS=$this->SevenCustomerStructure($shopid);
        //七日重复下单的数据
        $SevenRO=$this->SevenRepeatOrder($shopid);
        //七日下单趋势
        $SevenDT=$this->SevenDownwardTrend($shopid);

        //将数据全部放入数组中
        $showSevenArr=[
            'SevenOverview'=>$SevenOverview,
            'SevenCS'=>$SevenCS,
            'SevenRO'=>$SevenRO,
            'SevenDT'=>$SevenDT,
        ];
        return $this->message($showSevenArr,"成功",2);
    }
    /**
     * @return 七日顾客分析的总数据显示
     */
    public function ShowThirtyAnalysis(Request $request)
    {
        $shopid=$request->param("shopid");
        //七日总览模块的数据
        $ThirtyOverview=$this->ThirtyOverview($shopid);
        //七日顾客结构的数据
        $ThirtyCS=$this->ThirtyCustomerStructure($shopid);
        //七日重复下单的数据
        $ThirtyRO=$this->ThirtyRepeatOrder($shopid);
        //七日下单趋势
        $ThirtyDT=$this->ThirtyDownwardTrend($shopid);

        //将数据全部放入数组中
        $showThirtyArr=[
            'ThirtyOverview'=>$ThirtyOverview,
            'ThirtyCS'=>$ThirtyCS,
            'ThirtyRO'=>$ThirtyRO,
            'ThirtyDT'=>$ThirtyDT,
        ];
        return $this->message($showThirtyArr,"成功",2);
    }
}





















