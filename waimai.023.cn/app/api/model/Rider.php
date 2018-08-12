<?php

namespace app\api\model;

use think\Model;
use think\Db;

class Rider extends Model
{
  /**
  *@param 骑手评价商家
  */
  public function getriderEvaluateShop($rider_id,$shop_id,$stars,$content,$img)
  {
    $new['rider_id']=$rider_id;
    $new['shop_id'] =$shop_id;
    $new['stars']   =$stars;
    $new['content'] =$content;
    $new['img']     =$img;
    $new['add_time']=time();

    $result=Db::name('rider_evaluate')->insert($new);

    return $result;
  }
  /**
  *@param 骑手添加银行卡
  */
  public function addBank($data)
  {

    //先查询
    $bank_info=Db::name('bank_card')->where(['uid'=>$data['uid'],'identity'=>2])->find();

    if($bank_info){
      $new['card']    =$data['card'];//持卡人卡号
      $new['name']    =$data['name'];
      $new['city']    =$data['city'];//开户所在地址
      $new['opening_bank']=$data['opening_bank'];//开户行
      $new['add_time']=time();//修改时间 

      $result=Db::name('bank_card')->where(['uid'=>$data['uid'],'identity'=>2])->update($new);     
    }else{
      $new['uid']     =$data['uid'];//用户id
      $new['identity']='2';//身份 骑手
      $new['name']    =$data['name'];//持卡人姓名
      $new['card']    =$data['card'];//持卡人卡号
      $new['city']    =$data['city'];//开户所在地址
      $new['opening_bank']=$data['opening_bank'];//开户行
      $new['add_time']=time();//修改时间

      $result=Db::name('bank_card')->insert($new);      
    }    

    return $result;
  }

  /**
   * 返回开户银行编码 名称
   * @return false|\PDOStatement|string|\think\Collection
   */
  public function getBank(){
    $banklist=DB::name('bank_code')->select();
    return $banklist;
  }
  /**
  *@param 骑手实名认证
  */
  public function getAuthentication($data)
  {
    $rider_id=$data['id'];//骑手id
    //先查询该骑手是否实名认证
    $rider_info=Db::name('rider_info')->where('rider_id',$rider_id)->find();

    $img=OssUplodas('img');

    $new['name']   =$data['name'];

    $new['sex']    =$data['sex'];

    $new['id_card']=$data['id_card'];

    $new['age']    =$data['age'];

    $new['city_id']=$data['city_id'];

    $new['sheng']  =$data['sheng'];

    $new['shi']    =$data['shi'];

    $new['qu']     =$data['qu'];

    $new['hold_justimg']=$img[0];//手持身份证正面

    $new['hold_backimg']=$img[1];//手持身份证反面

    $new['driver_license_img']=$img[2];//驾驶证正面

    $new['vice_page_img']=$img[3];//驾驶证副页

    $new['health_img']   =$img[4];//健康证

    $new['status']=1;//提交审核状态    

    if($rider_info){//已有数据，修改

      $result=Db::name('rider_info')->where('rider_id',$rider_id)->update($new);
    }else{//没有数据，新增

      $new['rider_id']=$rider_id;
      $result=Db::name('rider_info')->insert($new);
    }

    return $result;
  }

  /**
  *@param 获取实名认证信息
  */
  public function getRiderList($rider_id)
  {
    $result=Db::name('rider_info')->where('rider_id',$rider_id)->select();

    foreach ($result as $k => $v) {
      $result[$k]['hold_justimg']='http:://'.$_SERVER['HTTP_HOST'].'public/uploads/api/'.$v['hold_justimg'];
      $result[$k]['hold_backimg']='http:://'.$_SERVER['HTTP_HOST'].'public/uploads/api/'.$v['hold_backimg'];
      $result[$k]['driver_license_img']='http:://'.$_SERVER['HTTP_HOST'].'public/uploads/api/'.$v['driver_license_img'];
      $result[$k]['vice_page_img']='http:://'.$_SERVER['HTTP_HOST'].'public/uploads/api/'.$v['vice_page_img'];
      $result[$k]['health_img']='http:://'.$_SERVER['HTTP_HOST'].'public/uploads/api/'.$v['health_img'];
    }

    return $result;
  }
  
  /**
  *@param 骑手详情信息
  */
  public function getRiderDetailsInfo($rider_id)
  {
    $rider_info=Db::name('rider')->where('id',$rider_id)->field('id,grade_num,nickname,img')->find();//骑手信息

    $grade    =Db::name('rider_grade')->where('id',$rider_info['grade_num'])->find();//骑手等级权益

    //查看今日战绩和本月战绩的 完成单和配送里程
    $beginYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'));//今日开始时间
    $endYesterday  =mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;//今日结束时间

    //查询今日已完成外卖订单
    $where['add_time']=array('between',array($beginYesterday,$endYesterday));
    $where['status']  =6;
    $where['dispatch_type']=1;
    $where['rider_id']=$rider_id;

    //查询今日已完成的跑腿订单
    $where_run['add_time']=array('between',array($beginYesterday,$endYesterday));
    $where_run['status']  =5;
    $where_run['rider_id']=$rider_id;

    //查询今日已完成的专送订单
    $where_take['add_time']=array('between',array($beginYesterday,$endYesterday));
    $where_take['status']  =5;
    $where_take['rider_id']=$rider_id;
    
    $order=Db::name('order')->where($where)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();
    $run_order=Db::name('run_order')->where($where_run)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();
    $take_order=Db::name('take_order')->where($where_take)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();
    $rider_info['today_count']=$order+$run_order+$take_order;  //今日完成单子


    $order_mileage=Db::name('order')->where($where)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');
    $run_order_mileage=Db::name('run_order')->where($where_run)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');
    $take_order_mileage=Db::name('take_order')->where($where_take)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');
    $rider_info['today_mileage']=$order_mileage+$run_order_mileage+$take_order_mileage; //今日里程  

    
    $beginYesterdays=mktime(0,0,0,date('m'),1,date('Y'));//本月开始时间
    $endYesterdays  =mktime(23,59,59,date('m'),date('t'),date('Y'))-1;//本月结束时间

    //查询本月已完成外卖订单
    $wheres['add_time']=array('between',array($beginYesterday,$endYesterday));
    $wheres['status']  =6;
    $wheres['dispatch_type']=1;
    $wheres['rider_id']=$rider_id;

    //查询昨日已完成的跑腿订单
    $where_runs['add_time']=array('between',array($beginYesterday,$endYesterday));
    $where_runs['status']  =5;
    $where_runs['rider_id']=$rider_id;

    //查询昨日已完成的专送订单
    $where_takes['add_time']=array('between',array($beginYesterday,$endYesterday));
    $where_takes['status']  =5;
    $where_takes['rider_id']=$rider_id;

      
    $order_month=Db::name('order')->where($wheres)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();  
    $run_order_month=Db::name('run_order')->where($where_runs)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();
    $take_order_month=Db::name('take_order')->where($where_takes)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();
    $rider_info['month_count']=$order_month+$run_order_month+$take_order_month;//本月完成单子数量

    $order_month_mileage=Db::name('order')->where($wheres)->field('id,ordernum,rider_id,status,add_time,delivery_time')->sum('mileage');  
    $run_order_month_mileage=Db::name('run_order')->where($where_runs)->field('id,ordernum,rider_id,status,add_time,delivery_time')->sum('mileage');
    $take_order_month_mileage=Db::name('take_order')->where($where_takes)->field('id,ordernum,rider_id,status,add_time,delivery_time')->sum('mileage');
    $rider_info['month_mileage']=$order_month_mileage+$run_order_month_mileage+$take_order_month_mileage;//本月完成单子数量
    
    if(empty($grade)){
      $grade=array();
    }

    $model=array(
      'grade'=>$grade,
      'rider_info'=>$rider_info,
    );
    return $model;
  }

  /**
  *@param 骑手查看评价
  */
  public function getRiderEvaluateList($rider_id,$evaluate_type,$type,$page)
  {
    switch ($type) {
      case '1'://用户评价骑手
        if($evaluate_type==1){//这是查看全部评价

          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>1])->order('add_time desc')->select();

          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }

        }elseif($evaluate_type==2){//这是查看好评

          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>1,'evaluate'=>1])->order('add_time desc')->select();
          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          } 

        }elseif($evaluate_type==3){//这是查看差评
          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>1,'evaluate'=>2])->order('add_time desc')->select();

          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }
        }
        break;

      case '2'://商家评价骑手
        if($evaluate_type==1){//这是查看全部评价

          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>2])->order('add_time desc')->select();
          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }

        }elseif($evaluate_type==2){//这是查看好评

          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>2])->where('evaluate','GT','3')->order('add_time desc')->select();
          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }

        }elseif($evaluate_type==3){//这是查看差评
          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>2,'evaluate'=>2])->order('add_time desc')->select();
          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }
        }        
        break;

      default:
        break;
    }
    $array=page_array('10',$page,$evaluate,0);

    return $array;
  }

    /**
  *@param 骑手查看评价
  */
  public function getRiderEvaluateListNum($rider_id,$evaluate_type,$type)
  {
    switch ($type) {
      case '1'://用户评价骑手
        if($evaluate_type==1){//这是查看全部评价

          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>1])->order('add_time desc')->select();

          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }

        }elseif($evaluate_type==2){//这是查看好评

          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>1,'evaluate'=>1])->order('add_time desc')->select();

          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          } 

        }elseif($evaluate_type==3){//这是查看差评
          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>1,'evaluate'=>2])->order('add_time desc')->select();

          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }
        }
        break;

      case '2'://商家评价骑手
        if($evaluate_type==1){//这是查看全部评价

          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>2])->order('add_time desc')->select();

          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }

        }elseif($evaluate_type==2){//这是查看好评

          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>2])->where('evaluate','GT','3')->order('add_time desc')->select();

          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          } 

        }elseif($evaluate_type==3){//这是查看差评
          $evaluate=Db::name('user_evaluate_rider')->where(['rid'=>$rider_id,'type'=>2])->where('evaluate','LT','3')->order('add_time desc')->select();

          foreach ($evaluate as $k => $v) {
            $evaluate[$k]['reason']=explode(',', $v['reason']);
          }
        }        
        break;

      default:
        break;
    }

    return $evaluate;
  }

}