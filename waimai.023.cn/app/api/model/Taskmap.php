<?php

namespace app\api\model;

use think\Model;
use think\Db;

class Taskmap extends Model
{
  /**
  * @param 骑手任务地图
  */
  public function getTaskMap($rider_id)
  {
    //查询外卖订单以及跑腿订单和专送订单 取字段 商家名称 商家地址 经度纬度
    //用户地址 经度纬度
    $order=Db::table('db_order')->alias('o')->join('address a','a.id=o.address_id')->where('o.rider_id',$rider_id)->select();//外卖订单

    foreach ($order as $k => $v) {
      $order_shop_info[]=Db::table('db_shop')->alias('s')->join('shop_address sa','s.id=sa.shopid')->where('s.id',$v['shop_id'])->find();
    } 

    $run_order=Db::table('db_run_order')->alias('ro')->join('address a','ro.address_id=a.id')->where('ro.rider_id',$rider_id)->select();//跑腿订单
    

    $take_order=Db::name('take_order')->where('rider_id',$rider_id)->select();//专送订单

    foreach ($take_order as $k => $v) {
      $talk_address[]=DB::name('address')->where('id',$v['take_address_id'])->find();//专送订单的取货地址

      $give_address[]=Db::name('address')->where('id',$v['give_address_id'])->find();//专送订单的收货地址
    }
    
    $order_take_list=array();

    $order_give_list=array();

    //收集数据
    foreach ($order as $k => $v) {
      $order_take_list[]=array(
        'take_name'   =>$order_shop_info[$k]['shop_name'],//店铺名称
        'take_address'=>$order_shop_info[$k]['shop_address'],//店铺地址
        'take_jd'   =>$order_shop_info[$k]['longitude'],//店铺经度
        'take_wd'   =>$order_shop_info[$k]['dimension'],//店铺纬度
        'address_type'=>1,//地址类型 取
        'receipt_time'=>$v['receipt_time'],//骑手接单时间

      );

      $order_give_list[]=array(
        'give_address'=>$v['address']+$v['building_card'],
        'give_jd'     =>$v['jd'],
        'give_wd'     =>$v['wd'],
        'address_type'=>2,//地址类型 送
        'receipt_time'=>$v['receipt_time'],//骑手接单时间
      );      
    }

    $run_order_take_list=array();

    $run_order_give_list=array();

    foreach($run_order as $K=>$v){
      $run_order_take_list[]=array(
        'take_name'   =>0,//店铺名称
        'take_address'=>$v['appoint_address'],//店铺地址
        'take_jd'     =>$v['appoint_address_jd'],//店铺经度
        'take_wd'   =>$v['appoint_address_wd'],//店铺纬度
        'address_type'=>1,//地址类型 取
        'receipt_time'=>$v['single_time'],//骑手接单时间
      );

      $run_order_give_list[]=array(
        'give_address'=>$v['address']+$v['building_card'],
        'give_jd'     =>$v['jd'],
        'give_wd'     =>$v['wd'],
        'address_type'=>2,//地址类型 送
        'receipt_time'=>$v['single_time'],//骑手接单时间      
      );
    }

    $take_order_take_list=array();

    $take_order_give_list=array();

    foreach ($take_order as $k => $v) {
      $take_order_take_list[]=array(
        'take_name'   =>0,//店铺名称
        'take_address'=>$talk_address[$k]['address']+$talk_address[$k]['building_card'],//店铺地址
        'take_jd'     =>$talk_address[$k]['jd'],//店铺经度
        'take_wd'   =>$talk_address[$k]['wd'],//店铺纬度
        'address_type'=>1,//地址类型 取
        'receipt_time'=>$v['single_time'],//骑手接单时间        
      );
      $take_order_give_list[]=array(
        'give_address'=>$give_address[$k]['address']+$give_address[$k]['building_card'],//店铺地址
        'give_jd'     =>$give_address[$k]['jd'],//店铺经度
        'give_wd'   =>$give_address[$k]['wd'],//店铺纬度
        'address_type'=>2,//地址类型 取
        'receipt_time'=>$v['single_time'],//骑手接单时间        
      );
    }


    $model=array();
    $model=array_merge($order_take_list,$order_give_list,$run_order_take_list,$run_order_give_list,$take_order_take_list,$take_order_give_list);

    multi_array_sort($model,'receipt_time',SORT_ASC);//数组排序

    return $model;
  }

  /**
  *@param 骑手历史任务
  */
  public function getHistoricalTask($rider_id,$page)
  {
    //查看三种订单的历史任务
    $order=Db::name('order')->where(['rider_id'=>$rider_id,'status'=>6])->select();

      foreach ($order as $k => $v) {

        $shop_info[]=Db::table('db_shop')->where('id',$v['shop_id'])->find();//商家信息

        $shop_address[]=Db::name('shop_address')->where('shopid',$v['shop_id'])->find();

        $user_address_info[]=Db::name('address')->where('id',$v['address_id'])->find();//用户地址信息
      }
       
    $run_order=Db::table('db_run_order')->alias('ro')->join('address a','a.id=ro.address_id')->where(['ro.rider_id'=>$rider_id,'ro.status'=>5])->select();

    $take_order=Db::name('take_order')->where(['rider_id'=>$rider_id,'status'=>5])->select();

      foreach ($take_order as $k => $v) {
        $talk_address[]=DB::name('address')->where('id',$v['take_address_id'])->find();//专送订单的取货地址

        $give_address[]=Db::name('address')->where('id',$v['give_address_id'])->find();//专送订单的收货地址
      }   

    //数据整理
    $order_list=array();
    foreach ($order as $k => $v) {
      $order_list[]=array(
        'order_id'    =>$v['id'],//订单id
        'order_type'  =>1,//订单类型
        'ordernum'    =>$v['ordernum'],
        'take_name'   =>$shop_info[$k]['shop_name'],//店铺名称
        'take_address'=>$shop_address[$k]['shop_address'],//店铺地址
        'give_address'=>$user_address_info[$k]['address'].$user_address_info[$k]['building_card'],//配送地址
        'delivery_time'=>$v['delivery_time'],//订单送达时间
        'money'       =>$v['distribution_fee']+$v['platform_fee'],//结算费用
                
      );      
    }

    $run_order_list=array();
    foreach ($run_order as $k => $v) {
      $run_order_list[]=array(
        'order_id'    =>$v['id'],
        'order_type'  =>2,
        'ordernum'    =>$v['ordernum'],
        'take_name'   =>0,
        'take_address'=>$v['appoint_address'],
        'give_address'=>$v['address'].$v['building_card'],
        'delivery_time'=>$v['delivery_time'],
        'money'       =>$v['distribution_fee']+$v['tip']+$v['platform_fee'],
      );
    }
    $take_order_list=array();
    foreach ($take_order as $k => $v) {
      $take_order_list[]=array(
        'order_id'    =>$v['id'],
        'order_type'  =>3,
        'ordernum'    =>$v['ordernum'],
        'take_name'   =>0,
        'take_address'=>$talk_address[$k]['address'].$talk_address[$k]['building_card'],
        'give_address'=>$give_address[$k]['address'].$give_address[$k]['building_card'],
        'delivery_time'=>$v['delivery_time'],
        'money'       =>$v['distribution_fee']+$v['tip']+$v['platform_fee'],        
      );
    }

    $model=array_merge($order_list,$run_order_list,$take_order_list);

    if(!empty($model)){

      multi_array_sort($model,'delivery_time',SORT_DESC);//数组排序
    }

    page_array('10',$page,$model,0);

    return $model;
  }

  /**
  *@param 骑手业务统计
  */
  public function RiderStatistics($rider_id)
  {
    //查询顾客满意率
    $satisfied_num=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'evaluate'=>1,'type'=>1])->count();//满意单数

    $total_num=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>1])->count();//总单数

    if($total_num==0){
      $user_rate=0;//满意率
    }else{
      $user_rate=($satisfied_num/$total_num);//用户满意率
    }
    

    //查询商家满意率
    $shop_satisfied_num=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'evaluate'=>1,'type'=>2])->count();//商家满意单数

    $shop_total_num=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>2])->count();

    if($shop_total_num==0){
      $shop_rate=0;//满意率
    }else{
      $shop_rate=($shop_satisfied_num/$shop_total_num);//商家满意率
    }


    //查询准时几率 平均配送时长 配送里程

    //三种订单的准时几率
    $ordernum=0;
    $where['rider_id']=$rider_id;
    $where['status']  =6;
    $order_num=Db::name('order')->where($where)->count();//该骑手完成的总外卖订单数量

    //准时达的外卖订单
    $order_on_time=0;
    $order=Db::name('order')->where($where)->select();//全部订单
    foreach ($order as $k => $v) {
      if($v['delivery_time']<=$v['pre_delivery_time']){
        $order_on_time++;
      }
    }

    $run_order_num=0;
    $run_where['rider_id']=$rider_id;
    $run_where['status']  =5;
    $run_order_num=Db::name('run_order')->where($run_where)->count();//该骑手完成的总跑腿订单数量

    //准时达的跑腿订单
    $run_order_on_time=0;
    $run_order=Db::name('run_order')->where($run_where)->select();
    foreach ($run_order as $k => $v) {
      if($v['delivery_time']<=$v['pre_delivery_time']){
        $run_order_on_time++;
      }
    }

    $take_order_num=0;
    $take_where['rider_id']=$rider_id;
    $take_where['status']  =5;
    $take_order_num=Db::name('take_order')->where($take_where)->count();//该骑手完成的总专送订单数量

    //准时达的专送订单
    $take_order_on_time=0;
    $take_order=Db::name('take_order')->where($take_where)->select();
    foreach ($take_order as $k => $v) {
      if($v['delivery_time']<=$v['pre_delivery_time']){
        $take_order_on_time++;
      }
    }
    $num=$order_on_time+$run_order_on_time+$take_order_on_time;
    $nums=$order_num+$run_order_num+$take_order_num;
    
    if($num==0&&$nums==0){
      $order_rate=0;
    }else{
      $order_rate=($order_on_time+$run_order_on_time+$take_order_on_time)/($order_num+$run_order_num+$take_order_num);//准时几率
    }
    
   
    //平均配送时长
    $order_avg_time=Db::name('order')->where($where)->avg('total_distribution_time');
    $run_avg_time=Db::name('run_order')->where($run_where)->avg('total_distribution_time');
    $take_avg_time=Db::name('take_order')->where($take_where)->avg('total_distribution_time');

    $avg_time=($order_avg_time+$run_avg_time+$take_avg_time)/3;//平均配送时长

    //配送里程
    $order_mileage=Db::name('order')->where($where)->sum('mileage');
    $run_mileage=Db::name('run_order')->where($run_where)->sum('mileage');
    $take_mileage=Db::name('take_order')->where($take_where)->sum('mileage');

    $mileage=$order_mileage+$run_mileage+$take_mileage;//配送里程
    
    //查询配送单量 早达 晚达
    $total_order=Db::name('order')->where($where)->count();

    $total_run=Db::name('run_order')->where($run_where)->count();

    $total_take=Db::name('take_order')->where($take_where)->count();

    $total_num=$total_order+$total_run+$total_take;//配送单量

    //早达
    $zao_reach_num=0;
    $wan_reach_num=0;
    foreach ($order as $k => $v) {
      if($v['delivery_time']<=$v['pre_delivery_time']){
        $zao_reach_num++;
      }

      if($v['delivery_time']>$v['pre_delivery_time']){
        $wan_reach_num++;
      }
    }

    $rzao_reach_num=0;
    $rwan_reach_num=0;
    foreach ($run_order as $k => $v) {
      if($v['delivery_time']<=$v['pre_delivery_time']){
        $rzao_reach_num++;
      }

      if($v['delivery_time']>$v['pre_delivery_time']){
        $rwan_reach_num++;
      }      
    }

    $tzao_reach_num=0;
    $twan_reach_num=0;
    foreach ($take_order as $k => $v) {
      if($v['delivery_time']<=$v['pre_delivery_time']){
        $tzao_reach_num++;
      }

      if($v['delivery_time']>$v['pre_delivery_time']){
        $twan_reach_num++;
      }      
    }

    $zao_total=$zao_reach_num+$rzao_reach_num+$tzao_reach_num;//早达总次数
    $wan_total=$wan_reach_num+$rwan_reach_num+$twan_reach_num;//晚达总次数

    $t_model=array();
    $t_model=array(
      'user_rate'=>$user_rate,//商家满意率
      'shop_rate'=>$shop_rate,//用户满意率
    );

    $tt_model=array();
    $tt_model=array(
      'order_rate'=>$order_rate,//订单准时几率
      'avg_time'  =>$avg_time,//平均配送时长
      'mileage'   =>$mileage,//配送里程
    );

    $ttt_model=array();
    $ttt_model=array(
      'total_num'=>$total_num,//配送单量
      'zao_total'=>$zao_total,//早达总次数
      'wan_total'=>$wan_total,//晚达总次数
    );

    $model=array(
      'satisfied'=>$t_model,
      'on_time'=>$tt_model,
      'amount'=>$ttt_model,
    );
    
    return $model;
  }

  /**
  *@param 骑手意见反馈
  */
  public function addRiderFeedback($data,$img)
  {
    $new['fid']=$data['id'];//当前登录的骑手id
    $new['identity']=2;
    $new['phone']=$data['phone'];
    $new['content']=$data['content'];
    $new['img']= $img;
    $result=Db::name('feedback')->insert($new);
    return $result;

  }
}