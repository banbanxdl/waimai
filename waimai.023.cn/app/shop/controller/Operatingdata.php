<?php
/**
 * 经营数据
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 10:47
 */

namespace app\shop\controller;
use think\Request;
use think\Db;
class Operatingdata extends Index{

    public function yesterdayOperadata(Request $request)
    {
        $shopid=$request->param("shopid");
        //查询昨天的访客
        $yesvisit=db("shop_visitor")
            ->where("shopid=".$shopid ." and date_format(from_unixtime(time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->select();
        //查询前天的访客
        $beYesterdayvisit=db("shop_visitor")
            ->where("shopid=".$shopid ." and date_format(from_unixtime(time),'%Y-%m-%d') = date_format(date_sub(now(), interval 2 day),'%Y-%m-%d')")
            ->select();
        //昨天跟前天对比访客(上升/下降/持平)
        $contrastVisit=count($yesvisit)-count($beYesterdayvisit);

        //查询昨日下单顾客
        $yesterdayOrder=db::view("order","user_id","order.user_id=shop_visitor.uid")
            ->view("shop_visitor","uid")
            ->distinct(true)
            ->where("order.shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d') ")
            ->where("shop_visitor.shopid=".$shopid." and date_format(from_unixtime(time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')" )
            ->select();
        //查询前天下单顾客
        $beyesterdayOrder=db::view("order","id,user_id","order.user_id=shop_visitor.uid")
            ->view("shop_visitor","uid")
            ->distinct(true)
            ->where("order.shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 2 day),'%Y-%m-%d') ")
            ->where("shop_visitor.shopid=".$shopid." and date_format(from_unixtime(time),'%Y-%m-%d') = date_format(date_sub(now(), interval 2 day),'%Y-%m-%d')" )
            ->select();
        //昨天跟前天对比下单顾客(上升/下降/持平)
        $contrastOrder=count($yesterdayOrder)-count($beyesterdayOrder);

        //计算昨日下单转换率
        if(count($yesvisit)>0){
            $conversionRate=number_format(count($yesterdayOrder)/count($yesvisit)*100,2);
        }else{
            $conversionRate=0;
        }



        //昨日顾客
        $yesGuest=db("order")
                    ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
                    ->column('user_id');
        //昨日之前的顾客
        $AllbeyesGuest=db("order")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') < date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->column('user_id');
        //查找昨日顾客不存在于昨日之前顾客(新客)
        $yesNewGuest=array_diff($yesGuest,$AllbeyesGuest);
        //昨日新顾客数量
        $yesNewGuestnum=count(array_unique($yesNewGuest));

        //前日顾客
        $qianGuest=db("order")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 2 day),'%Y-%m-%d')")
            ->column('user_id');
        //前日之前的顾客
        $AllbeqianGuest=db("order")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') < date_format(date_sub(now(), interval 2 day),'%Y-%m-%d')")
            ->column('user_id');
        //查找前日顾客不存在于前日之前顾客(新客)
        $qianNewGuest=array_diff($qianGuest,$AllbeqianGuest);
        //前日新顾客数量
        $qianNewGuestnum=count(array_unique($qianNewGuest));

        //比较新客上升人数(上升/下降/持平)
        $contrastNewGuest=$yesNewGuestnum-$qianNewGuestnum;

        //查找昨日顾客存在于昨日之前顾客(老客)
        $yesOldGuest=array_intersect($yesGuest,$AllbeyesGuest);
        //昨日老顾客数量
        $yesOldGuestnum=count(array_unique($yesOldGuest));

        //查找前日顾客存在于前日之前顾客(老客)
        $qianOldGuest=array_intersect($qianGuest,$AllbeqianGuest);
        //前日老顾客数量
        $qianOldGuestnum=count(array_unique($qianOldGuest));

        //比较旧客上升人数(上升/下降/持平)
        $contrastOldGuest=$yesOldGuestnum-$qianOldGuestnum;


        //昨日访问趋势
        for($i=0;$i<count($yesvisit);$i++){
            $yesvisit[$i]["time"]=date("H",$yesvisit[$i]["time"]);
        }
        //昨日下单趋势
        $downwardTrend=db("order")
            ->field("id,user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(from_unixtime(add_time),'%Y-%m-%d') = date_format(date_sub(now(), interval 1 day),'%Y-%m-%d')")
            ->select();
        for($a=0;$a<count($downwardTrend);$a++){
            $downwardTrend[$a]["add_time"]=date("H",$downwardTrend[$a]["add_time"]);
        }

        /*
        *访问趋势的具体时间段
         */
        $date0 = date('H', strtotime(-date('H') ."hours"));
        $date2 = date('H', strtotime(-date('H')+2 ."hours"));
        $date4 = date('H', strtotime(-date('H')+4 ."hours"));
        $date6 = date('H', strtotime(-date('H')+6 ."hours"));
        $date8 = date('H', strtotime(-date('H')+8 ."hours"));
        $date10 = date('H', strtotime(-date('H')+10 ."hours"));
        $date12 = date('H', strtotime(-date('H')+12 ."hours"));
        $date14 = date('H', strtotime(-date('H')+14 ."hours"));
        $date16 = date('H', strtotime(-date('H')+16 ."hours"));
        $date18 = date('H', strtotime(-date('H')+18 ."hours"));
        $date20 = date('H', strtotime(-date('H')+20 ."hours"));
        $date22 = date('H', strtotime(-date('H')+22 ."hours"));
        $date24 = date('H', strtotime(-date('H')+24 ."hours"));
        /**
         * 昨日具体访问趋势
         */
        //0-2点的访客量
        $yesVisit0=seTimeCount($yesvisit,$date0,$date2,'time',$visit0Num=0);
        //2-4点的访客量
        $yesVisit2=seTimeCount($yesvisit,$date2,$date4,'time',$visit2Num=0);
        //4-6点的访客量
        $yesVisit4=seTimeCount($yesvisit,$date4,$date6,'time',$visit4Num=0);
        //6-8点的访客量
        $yesVisit6=seTimeCount($yesvisit,$date6,$date8,'time',$visit6Num=0);
        //8-10点的访客量
        $yesVisit8=seTimeCount($yesvisit,$date8,$date10,'time',$visit8Num=0);
        //10-12点的访客量
        $yesVisit10=seTimeCount($yesvisit,$date10,$date12,'time',$visit10Num=0);
        //12-14点的访客量
        $yesVisit12=seTimeCount($yesvisit,$date12,$date14,'time',$visit12Num=0);
        //14-16点的访客量
        $yesVisit14=seTimeCount($yesvisit,$date14,$date16,'time',$visit14Num=0);
        //16-18点的访客量
        $yesVisit16=seTimeCount($yesvisit,$date16,$date18,'time',$visit16Num=0);
        //18-20点的访客量
        $yesVisit18=seTimeCount($yesvisit,$date18,$date20,'time',$visit18Num=0);
        //20-22点的访客量
        $yesVisit20=seTimeCount($yesvisit,$date20,$date22,'time',$visit20Num=0);
        //22-24点的访客量
        $yesVisit22=seTimeCount($yesvisit,$date22,$date24,'time',$visit24Num=0);
        /**
         * 昨日具体下单趋势
         */
        //0-2点的下单量
        $yesOrder0=seTimeCount($downwardTrend,$date0,$date2,'add_time',$order0Num=0);
        //2-4点的下单量
        $yesOrder2=seTimeCount($downwardTrend,$date2,$date4,'add_time',$order2Num=0);
        //4-6点的下单量
        $yesOrder4=seTimeCount($downwardTrend,$date4,$date6,'add_time',$order4Num=0);
        //6-8点的下单量
        $yesOrder6=seTimeCount($downwardTrend,$date6,$date8,'add_time',$order6Num=0);
        //8-10点的下单量
        $yesOrder8=seTimeCount($downwardTrend,$date8,$date10,'add_time',$order8Num=0);
        //10-12点的下单量
        $yesOrder10=seTimeCount($downwardTrend,$date10,$date12,'add_time',$order10Num=0);
        //12-14点的下单量
        $yesOrder12=seTimeCount($downwardTrend,$date12,$date14,'add_time',$order12Num=0);
        //14-16点的下单量
        $yesOrder14=seTimeCount($downwardTrend,$date14,$date16,'add_time',$order14Num=0);
        //16-18点的下单量
        $yesOrder16=seTimeCount($downwardTrend,$date16,$date18,'add_time',$order16Num=0);
        //18-20点的下单量
        $yesOrder18=seTimeCount($downwardTrend,$date18,$date20,'add_time',$order18Num=0);
        //20-22点的下单量
        $yesOrder20=seTimeCount($downwardTrend,$date20,$date22,'add_time',$order20Num=0);
        //22-24点的下单量
        $yesOrder22=seTimeCount($downwardTrend,$date22,$date24,'add_time',$order24Num=0);
        //时间
        $time = [
            $date0,
            $date2,
            $date4,
            $date6,
            $date8,
            $date10,
            $date12,
            $date14,
            $date16,
            $date18,
            $date20,
            $date22,
        ];
        //昨日顾客访问数
        $yesVisit = [
            $yesVisit0,
            $yesVisit2,
            $yesVisit4,
            $yesVisit6,
            $yesVisit8,
            $yesVisit10,
            $yesVisit12,
            $yesVisit14,
            $yesVisit16,
            $yesVisit18,
            $yesVisit20,
            $yesVisit22,
        ];
        //昨日顾客下单数
        $yesOrder = [
            $yesOrder0,
            $yesOrder2,
            $yesOrder4,
            $yesOrder6,
            $yesOrder8,
            $yesOrder10,
            $yesOrder12,
            $yesOrder14,
            $yesOrder16,
            $yesOrder18,
            $yesOrder20,
            $yesOrder22,
        ];

        //根据下标匹配数据
        foreach ($time as $k=>$value){
            $arr['time'] = $value;
            $arr['yesVisit'] = $yesVisit[$k];
            $arr['yesOrder'] = $yesOrder[$k];
            $tree[] = $arr;
        }

        //显示全部数据
        $arr=[
            'yesvisit'=>count($yesvisit),//昨日访问顾客数量
            'contrastVisit'=>$contrastVisit,//较前日访问状况(上升/下降/持平)
            'yesterdayOrder'=>count($yesterdayOrder),//昨日下单顾客数量
            'contrastOrder'=>$contrastOrder,//较前日下单状况(上升/下降/持平)
            'conversionRate'=>$conversionRate,//计算昨日下单转换率
            'yesNewGuestnum'=>$yesNewGuestnum,//昨日新顾客数量
            'contrastNewGuest'=>$contrastNewGuest,//比较新客上升人数(上升/下降/持平)
            'yesOldGuestnum'=>$yesNewGuestnum,//昨日老客数量
            'contrastOldGuest'=>$contrastNewGuest,//比较老客上升人数(上升/下降/持平)
            'tree'=>$tree
            //0-2点的数据
            /*'dateZeroArr'=>[
                'Time'=>[
                    $date0,
                    $date2,
                    $date4,
                    $date6,
                    $date8,
                    $date10,
                    $date12,
                    $date14,
                    $date16,
                    $date18,
                    $date20,
                    $date22,
                ],
                'yesVisit'=>[
                    $yesVisit0,
                    $yesVisit2,
                    $yesVisit4,
                    $yesVisit6,
                    $yesVisit8,
                    $yesVisit10,
                    $yesVisit12,
                    $yesVisit14,
                    $yesVisit16,
                    $yesVisit18,
                    $yesVisit20,
                    $yesVisit22,
                ],
                'yesOrder'=>[
                    $yesOrder0,
                    $yesOrder2,
                    $yesOrder4,
                    $yesOrder6,
                    $yesOrder8,
                    $yesOrder10,
                    $yesOrder12,
                    $yesOrder14,
                    $yesOrder16,
                    $yesOrder18,
                    $yesOrder20,
                    $yesOrder22,
                ],
                'dateTwo'=>$date2,
                'yesVisitZero'=>$yesVisit0,
                'yesOrderZero'=>$yesOrder0,
            //],
            //2-4点的数据
            'dateTwoArr'=>[
                //'date4'=>$date4,
                'yesVisitTwo'=>$yesVisit2,
                'yesOrderTwo'=>$yesOrder2,
           // ],
            //4-6点的数据
            //'dateFourArr'=>[
                //'date6'=>$date6,
                'yesVisitFour'=>$yesVisit4,
                'yesOrderFour'=>$yesOrder4,
           // ],
            //6-8点的数据
           // 'dateSixArr'=>[
                //'date8'=>$date8,
                'yesVisitSix'=>$yesVisit6,
                'yesOrderSix'=>$yesOrder6,
           // ],
            //8-10点的数据
           // 'dateEightArr'=>[
                //'date10'=>$date10,
                'yesVisitEight'=>$yesVisit8,
                'yesOrderEight'=>$yesOrder8,
            //],
            //10-12点的数据
           // 'dateTenArr'=>[
                //'date12'=>$date12,
                'yesVisitTen'=>$yesVisit10,
                'yesOrderTen'=>$yesOrder10,
            //],
            //12-14点的数据
            //'dateTwelveArr'=>[
                //'date14'=>$date14,
                'yesVisitTwelve'=>$yesVisit12,
                'yesOrderTwelve'=>$yesOrder12,
           // ],
            //14-16点的数据
            //'dateFourteenArr'=>[
                //'date16'=>$date16,
                'yesVisitFourteen'=>$yesVisit14,
                'yesOrderFourteen'=>$yesOrder14,
            //],
            //16-18点的数据
            //'dateSixteenArr'=>[
                //'date18'=>$date18,
                'yesVisitSixteen'=>$yesVisit16,
                'yesOrderSixteen'=>$yesOrder16,
            //],
            //18-20点的数据
            //'dateEighteenArr'=>[
                //'date20'=>$date20,
                'yesVisitEighteen'=>$yesVisit18,
                'yesOrderEighteen'=>$yesOrder18,
            //],
            //20-22点的数据
            //'dateTwentyArr'=>[
                //'date22'=>$date22,
                'yesVisitTwenty'=>$yesVisit20,
                'yesOrderTwenty'=>$yesOrder20,
            //],
            //22-24点的数据
            //'dateTwenty_twoArr'=>[
                //'date24'=>$date24,
                'yesVisitTwenty_two'=>$yesVisit22,
                'yesOrderTwenty_two'=>$yesOrder22,
            ],*/
            //'accessTrend'=>$yesvisit,//昨日访问趋势
            //'downwardTrend'=>$downwardTrend,//昨日下单趋势
        ];
        return $this->message($arr,"成功",2);
    }


    //七日经营报告
    public function sevendayOperadata(Request $request)
    {

        $shopid=$request->param("shopid");
        //查询七日的访客
        $yesvisit=db("shop_visitor")
            ->where("shopid=".$shopid ." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(time),'%Y-%m-%d')")
            ->select();
        for($i=0;$i<count($yesvisit);$i++){
            $yesvisit[$i]["time"]=date("Y-m-d",$yesvisit[$i]["time"]);
        }
        //查询七日的下单顾客
        $downwardTrend=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and date_format(date_sub(now(), interval 7 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->select();
        for($a=0;$a<count($downwardTrend);$a++){
            $downwardTrend[$a]["add_time"]=date("Y-m-d",$downwardTrend[$a]["add_time"]);
        }

        //七日之前的下单顾客
        $AllbeseGuest=db("order")
            ->field("user_id,add_time")
            ->where("shop_id=".$shopid." and TIMESTAMPDIFF(day,date_format(from_unixtime(add_time),'%Y-%m-%d'),date_format(now(),'%Y-%m-%d'))>7 ")
            ->select();
        for($b=0;$b<count($AllbeseGuest);$b++){
            $AllbeseGuest[$b]["add_time"]=date("Y-m-d",$AllbeseGuest[$b]["add_time"]);
        }
        $AllbeseGuest2 = array_column($AllbeseGuest, 'add_time','user_id');

        //查找七日下单顾客不存在于七日之前下单顾客(新客)
        $sevenNewGuest=array();
        $cmp=function($av, $bv){
            $r=strcmp($av['user_id'],$bv['user_id']);
            return $r===0 ? strcmp($av['user_id'],$bv['user_id']) : $r;
        };
        //查询俩个二维数组中的差集
        $sevenNewGuest=array_values(array_udiff($downwardTrend, $AllbeseGuest, $cmp));
        $d=array_udiff($AllbeseGuest, $downwardTrend, $cmp);
        foreach($d as &$dv) {
            $sevenNewGuest[]=$dv;
            unset($d);
        }

        //查找七日下单顾客存在于七日之前下单顾客(老客)
        $sevenOldGuest=array();
        //查询俩个二维数组中的交集
        foreach ($downwardTrend as $value){
            foreach ($AllbeseGuest as $val){
                if($value["user_id"]==$val["user_id"]){

                    $sevenOldGuest[]=$value;

                }
            }
        }
        /*
         * 七日访问顾客每日的数量
         */
        //时间：七日的第一天
        $date1 = date('Y-m-d', strtotime('-1 days'));
        //获取七日中的第一天的访客量
        //$visit1Num=0;
        /*foreach ($yesvisit as $item) {
            if ($item['time'] == $date1) {
                $visit1Num+=1;
            }
        }*/
        $visit1Num=seCount($yesvisit,$date1,'time',$visit1Num=0);

        //时间：七日的第二天
        $date2 = date('Y-m-d', strtotime('-2 days'));
        //获取七日中的第二天的访客量
        $visit2Num=seCount($yesvisit,$date2,'time',$visit2Num=0);

        //时间：七日的第三天
        $date3 = date('Y-m-d', strtotime('-3 days'));
        //获取七日中的第三天的访客量
        $visit3Num=seCount($yesvisit,$date3,'time',$visit3Num=0);

        //时间：七日的第四天
        $date4 = date('Y-m-d', strtotime('-4 days'));
        //获取七日中的第四天的访客量
        $visit4Num=seCount($yesvisit,$date4,'time',$visit4Num=0);

        //时间：七日的第五天
        $date5 = date('Y-m-d', strtotime('-5 days'));
        //获取七日中的第五天的访客量
        $visit5Num=seCount($yesvisit,$date5,'time',$visit5Num=0);

        //时间：七日的第六天
        $date6 = date('Y-m-d', strtotime('-6 days'));
        //获取七日中的第六天的访客量
        $visit6Num=seCount($yesvisit,$date6,'time',$visit6Num=0);

        //时间：七日的第七天
        $date7 = date('Y-m-d', strtotime('-7 days'));
        //获取七日中的第七天的访客量
        $visit7Num=seCount($yesvisit,$date7,'time',$visit7Num=0);


        /*
         * 七日下单顾客每日的数量
         */

        //获取七日中的第一天的顾客量
        $downorder1Num=seCount($downwardTrend,$date1,'add_time',$downorder1Num=0);

        //获取七日中的第二天的顾客量
        $downorder2Num=seCount($downwardTrend,$date2,'add_time',$downorder2Num=0);

        //获取七日中的第三天的顾客量
        $downorder3Num=seCount($downwardTrend,$date3,'add_time',$downorder3Num=0);

        //获取七日中的第四天的顾客量
        $downorder4Num=seCount($downwardTrend,$date4,'add_time',$downorder4Num=0);

        //获取七日中的第五天的顾客量
        $downorder5Num=seCount($downwardTrend,$date5,'add_time',$downorder5Num=0);

        //获取七日中的第六天的顾客量
        $downorder6Num=seCount($downwardTrend,$date6,'add_time',$downorder6Num=0);

        //获取七日中的第七天的顾客量
        $downorder7Num=seCount($downwardTrend,$date7,'add_time',$downorder7Num=0);

        /*
         * 七日下单新客每日的数量
         */
        //获取七日中的第一天
        $same1Num=seCount($sevenNewGuest,$date1,'add_time',$same1Num=0);

        //获取七日中的第二天
        $same2Num=seCount($sevenNewGuest,$date2,'add_time',$same2Num=0);

        //获取七日中的第三天
        $same3Num=seCount($sevenNewGuest,$date3,'add_time',$same3Num=0);

        //获取七日中的第四天
        $same4Num=seCount($sevenNewGuest,$date4,'add_time',$same4Num=0);

        //获取七日中的第五天
        $same5Num=seCount($sevenNewGuest,$date5,'add_time',$same5Num=0);

        //获取七日中的第六天
        $same6Num=seCount($sevenNewGuest,$date6,'add_time',$same6Num=0);

        //获取七日中的第七天
        $same7Num=seCount($sevenNewGuest,$date7,'add_time',$same7Num=0);

        /*
         * 七日下单老客每日的数量
         */
        //获取七日中的第一天
        $diff1Num=seCount($sevenOldGuest,$date1,'add_time',$diff1Num=0);

        //获取七日中的第二天
        $diff2Num=seCount($sevenOldGuest,$date2,'add_time',$diff2Num=0);

        //获取七日中的第三天
        $diff3Num=seCount($sevenOldGuest,$date3,'add_time',$diff3Num=0);

        //获取七日中的第四天
        $diff4Num=seCount($sevenOldGuest,$date4,'add_time',$diff4Num=0);

        //获取七日中的第五天
        $diff5Num=seCount($sevenOldGuest,$date5,'add_time',$diff5Num=0);

        //获取七日中的第六天
        $diff6Num=seCount($sevenOldGuest,$date6,'add_time',$diff6Num=0);

        //获取七日中的第七天
        $diff7Num=seCount($sevenOldGuest,$date7,'add_time',$diff7Num=0);

        $time = [
            $date1,$date2,$date3,$date4,$date5,$date6,$date7
        ];
        $visitNum=[
            $visit1Num,$visit2Num,$visit3Num,$visit4Num,$visit5Num,$visit6Num,$visit7Num
        ];
        $downorderNum=[
            $downorder1Num,$downorder2Num,$downorder3Num,$downorder4Num,$downorder5Num,$downorder6Num,$downorder7Num
        ];
        $newCustomer=[
            $same1Num,$same2Num,$same3Num,$same4Num,$same5Num,$same6Num,$same7Num
        ];
        $oldCustomer=[
            $diff1Num,$diff2Num,$diff3Num,$diff4Num,$diff5Num,$diff6Num,$diff7Num
        ];
        //根据下标匹配数据
        foreach ($time as $k=>$value){
            $arr['time'] = $value;
            $arr['visitNum'] = $visitNum[$k];
            $arr['downorderNum'] = $downorderNum[$k];
            $arr['newCustomer'] = $newCustomer[$k];
            $arr['oldCustomer'] = $oldCustomer[$k];
            $tree[] = $arr;
        }

        /*$arr=[

            //'dataOne'=>[
                'dateOne'=>$date1,//七天前的第一天的日期
                'visitOneNum'=>$visit1Num,//七天前的第一天的访客量
                'downOrderOneNum'=>$downorder1Num,//七天前的第一天的下单量
                'sameOneNum'=>$same1Num,//七天前的第一天的新客量
                'diffOneNum'=>$diff1Num,//七天前的第一天的老客量
            //],
            //'dataTwo'=>[
                'dateTwo'=>$date2,//七天前的第二天的日期
                'visitTwoNum'=>$visit2Num,//七天前的第二天的访客量
                'downOrderTwoNum'=>$downorder2Num,//七天前的第二天的下单量
                'sameTwoNum'=>$same2Num,//七天前的第二天的新客量
                'diffTwoNum'=>$diff2Num,//七天前的第二天的老客量
            //],
            //'dataThree'=>[
                'dateThree'=>$date3,//七天前的第三天的日期
                'visitThreeNum'=>$visit3Num,//七天前的第三天的访客量
                'downOrderThreeNum'=>$downorder3Num,//七天前的第三天的下单量
                'sameThreeNum'=>$same3Num,//七天前的第三天的新客量
                'diffThreeNum'=>$diff3Num,//七天前的第三天的老客量
            //],
            //'dataFour'=>[
                'dateFour'=>$date4,//七天前的第四天的日期
                'visitFourNum'=>$visit4Num,//七天前的第四天的访客量
                'downOrderFourNum'=>$downorder4Num,//七天前的第四天的下单量
                'sameFourNum'=>$same4Num,//七天前的第四天的新客量
                'diffFourNum'=>$diff4Num,//七天前的第四天的老客量
            //],
            //'dataFive'=>[
                'dateFive'=>$date5,//七天前的第五天的日期
                'visitFiveNum'=>$visit5Num,//七天前的第五天的访客量
                'downOrderFiveNum'=>$downorder5Num,//七天前的第五天的下单量
                'sameFiveNum'=>$same5Num,//七天前的第五天的新客量
                'diffFiveNum'=>$diff5Num,//七天前的第五天的老客量
            //],
            //'dataSix'=>[
                'dateSix'=>$date6,//七天前的第六天的日期
                'visitSixNum'=>$visit6Num,//七天前的第六天的访客量
                'downOrderSixNum'=>$downorder6Num,//七天前的第六天的下单量
                'sameSixNum'=>$same6Num,//七天前的第六天的新客量
                'diffSixNum'=>$diff6Num,//七天前的第六天的老客量
            //],
            //'dataSeven'=>[
                'dateSeven'=>$date7,//七天前的第七天的日期
                'visitSevenNum'=>$visit7Num,//七天前的第七天的访客量
                'downOrderSevenNum'=>$downorder7Num,//七天前的第七天的下单量
                'sameSevenNum'=>$same7Num,//七天前的第七天的新客量
                'diffSevenNum'=>$diff7Num,//七天前的第七天的老客量
           // ],

        ];*/
        return $this->message($tree,"成功",2);

    }
}