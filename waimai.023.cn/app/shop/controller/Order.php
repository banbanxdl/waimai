<?php
namespace app\shop\controller;
use app\common\controller\AdminCommon;
use think\Model;
use think\Request;
use think\Loader;
use think\Db;
use think\Session;
use think\Config;
class Order extends Index
{


    //显示店铺进行中的订单
	public function showJingOrder(Request $request){
		//店铺id
		$shopid=$request->param("shopid");

		//联表查询订单数据
		$data=db::view('order','id,ordernum,user_id,address_id,shop_id,total_fee,status,add_time,pre_delivery_time,pre_meal_time,odd_numbers,is_pro_order,dispatch_type,invite_time')//订单表
            ->view('user','id','order.user_id=user.id')//用户表
            ->view('rider','nickname,img,phone riderphone','order.rider_id=rider.id')//骑手表
            ->view('address','name,sex,phone,sheng,shi,qu,address,building_card,jd,wd','address.id=order.address_id')//用户地址表
	        ->view('shop_address','shopid,longitude,dimension','shop_address.shopid=order.shop_id')
	        ->where('order.shop_id='.$shopid)
	        ->where("(order.status=3 || order.status=4 || order.status=5) and date_format(date_sub(now(), interval 0 day),'%Y-%m-%d') <= date_format(from_unixtime(order.add_time),'%Y-%m-%d')")
	        //->order("id desc")
	        ->select();
        
        
        $countArr=count($data);
        for($i=0;$i<$countArr;$i++){
          //格式化时间
          $data[$i]["add_time"]=date("m-d H:i",$data[$i]["add_time"]);
          $data[$i]["pre_delivery_time"]=date("H:i",$data[$i]["pre_delivery_time"]);
          $data[$i]["pre_meal_time"]=date("H:i",$data[$i]["pre_meal_time"]);
            $data[$i]["invite_time"]=date("H:i",$data[$i]["invite_time"]);
          //查询商家到送餐地点的距离
          //$data[$i]["distance"]=Loader::model('Distance')->get_distance([$data[$i]["longitude"],$data[$i]["dimension"]],[$data[$i]['jd'],$data[$i]['wd']]);
          //查询订单里的商品
          $data[$i]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
          					->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
          					->where('order_goods.order_id='.$data[$i]["id"])
          					->select();
          //平台服务费
          $data[$i]["service_price"]=4;
          //预计收入
          $data[$i]["expected_income"]=$data[$i]["total_fee"]-$data[$i]["service_price"];
          					
        }

		//if($data){
			return $this->message($data,"成功",2);
		//}
	}
	//显示店铺已完成的订单(目前查询订单的已完成6、催单7)
	public function showFinishOrder(Request $request){
		//店铺id
		$shopid=$request->param("shopid");
		//订单状态
        $order_type=$request->param("order_type");
		//联表查询订单数据
		$data=db::view('order','id oid,ordernum,user_id,address_id,shop_id,total_fee,status,add_time,pre_delivery_time,pre_meal_time,delivery_time,odd_numbers,is_pro_order,dispatch_type,invite_time')//订单表
            ->view('user','id','order.user_id=user.id')//用户表
            ->view('rider','id rid,nickname,img,phone riderphone','order.rider_id=rider.id')//骑手表
            ->view('address','name,sex,phone,sheng,shi,qu,address,building_card,jd,wd','address.id=order.address_id')//用户地址表
	        ->view('shop_address','shopid,longitude,dimension','shop_address.shopid=order.shop_id')
	        ->where('order.shop_id='.$shopid)
	        ->where('order.status='.$order_type)
	        //->order("id desc")
	        ->select();
        
        
        $countArr=count($data);
        for($i=0;$i<$countArr;$i++){
          //格式化时间
          $data[$i]["add_time"]=date("m-d H:i",$data[$i]["add_time"]);
          $data[$i]["pre_delivery_time"]=date("H:i",$data[$i]["pre_delivery_time"]);
          $data[$i]["pre_meal_time"]=date("H:i",$data[$i]["pre_meal_time"]);
            $data[$i]["delivery_time"]=date("m-d H:i",$data[$i]["delivery_time"]);
            $data[$i]["invite_time"]=date("H:i",$data[$i]["invite_time"]);
          //查询商家到送餐地点的距离
          //$data[$i]["distance"]=Loader::model('Distance')->get_distance([$data[$i]["longitude"],$data[$i]["dimension"]],[$data[$i]['jd'],$data[$i]['wd']]);
          //查询订单里的商品
          $data[$i]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
          					->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
          					->where('order_goods.order_id='.$data[$i]["id"])
          					->select();
          //平台服务费
          $data[$i]["service_price"]=4;
          					
        }

		//if($data){
			return $this->message($data,"成功",2);
		//}
	}



    //显示店铺已取消的订单
	public function showCancelOrder(Request $request){
		//店铺id
		$shopid=$request->param("shopid");

		//联表查询订单数据
		$data=db::view('order','id oid,ordernum,user_id,address_id,shop_id,total_fee,status ostatus,add_time,pre_delivery_time,pre_meal_time,delivery_time,odd_numbers,is_pro_order,dispatch_type,invite_time')//订单表
            ->view('user','id','order.user_id=user.id')//用户表
            ->view('apply_refund','id rid,user_type,reason','order.id=apply_refund.order_id')
            ->view('rider','id qid,nickname,img,phone riderphone','order.rider_id=rider.id')//骑手表
            ->view('address','name,sex,phone,sheng,shi,qu,address,building_card,jd,wd','address.id=order.address_id')//用户地址表
	        ->view('shop_address','shopid,longitude,dimension','shop_address.shopid=order.shop_id')
	        ->where('order.shop_id='.$shopid)
	        ->where('order.status=8 or order.status=9')
	        //->order("id desc")
	        ->select();
        
        
        $countArr=count($data);
        for($i=0;$i<$countArr;$i++){
          //格式化时间
          $data[$i]["add_time"]=date("m-d H:i",$data[$i]["add_time"]);
            $data[$i]["pre_delivery_time"]=date("H:i",$data[$i]["pre_delivery_time"]);
            $data[$i]["pre_meal_time"]=date("H:i",$data[$i]["pre_meal_time"]);
            $data[$i]["invite_time"]=date("H:i",$data[$i]["invite_time"]);//自取时间
          //查询商家到送餐地点的距离
          //$data[$i]["distance"]=Loader::model('Distance')->get_distance([$data[$i]["longitude"],$data[$i]["dimension"]],[$data[$i]['jd'],$data[$i]['wd']]);
          //查询订单里的商品
          $data[$i]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
          					->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
          					->where('order_goods.order_id='.$data[$i]["id"])
          					->select();
          //平台服务费
          $data[$i]["service_price"]=4;
          					
        }

		return $this->message($data,"成功",2);
	}

	//显示未处理的新订单
	public function showUntreated(Request $request){
		//店铺id
		$shopid=$request->param("shopid");

		//联表查询订单数据
		$data=db::view('order','id oid,ordernum,user_id,address_id,shop_id,total_fee,distribution_fee,platform_fee,status,add_time,pre_delivery_time,pre_meal_time,odd_numbers,is_pro_order,dispatch_type,invite_time')//订单表
            ->view('user','id','order.user_id=user.id')//用户表
            ->view('address','name,sex,phone,sheng,shi,qu,address,building_card,jd,wd','address.id=order.address_id')//用户地址表
	        ->view('shop_address','shopid,longitude,dimension','shop_address.shopid=order.shop_id')
	        ->where('order.shop_id='.$shopid)
	        ->where("order.status=2 and date_format(date_sub(now(), interval 0 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
	        ->order("add_time desc")
	        ->select();
        
        
        $countArr=count($data);
        for($i=0;$i<$countArr;$i++){
          //格式化时间
          $data[$i]["add_time"]=date("m-d H:i",$data[$i]["add_time"]);
            $data[$i]["pre_delivery_time"]=date("H:i",$data[$i]["pre_delivery_time"]);
            $data[$i]["pre_meal_time"]=date("H:i",$data[$i]["pre_meal_time"]);
            $data[$i]["invite_time"]=date("H:i",$data[$i]["invite_time"]);//自取时间
          //查询商家到送餐地点的距离
          //$data[$i]["distance"]=Loader::model('Distance')->get_distance([$data[$i]["longitude"],$data[$i]["dimension"]],[$data[$i]['jd'],$data[$i]['wd']]);
          //查询订单里的商品
          $data[$i]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
          					->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
          					->where('order_goods.order_id='.$data[$i]["id"])
          					->select();
          //平台服务费
          $data[$i]["service_price"]=4;
          //预计收入
          $data[$i]["total_fee"]=$data[$i]["total_fee"]-$data[$i]["service_price"]+$data[$i]["platform_fee"];
          //订单序列号
          //$data[$i]["serial_number"]=$i+1;
          if($data[$i]["odd_numbers"]==0){
              //查询上一条数据
              $result=db("order")->field("id,odd_numbers")
                  ->where("date_format(date_sub(now(), interval 0 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")->select();
              for($a=0;$a<count($result);$a++){
                    if($a==0){
                        if($result[$a]["odd_numbers"]==0){
                            $result[$a]["odd_numbers"]=1;
                            $data[$i]["odd_numbers"]=1;
                            //将单号放入数据库
                            db("order")->where("id=".$result[$a]["id"])->update(['odd_numbers'=>$result[$a]["odd_numbers"]]);
                        }
                    }else{
                        if($result[$a]["odd_numbers"]==0){
                            $result[$a]["odd_numbers"]=$result[$a-1]["odd_numbers"]+1;
                            $data[$i]["odd_numbers"]=$result[$a]["odd_numbers"];
                            db("order")->where("id=".$result[$a]["id"])->update(['odd_numbers'=>$result[$a]["odd_numbers"]]);
                        }
                    }
              }

          }
        }

			return $this->message($data,"成功",2);
	}

    /**
     * 待发配送(2018-06-19)
     */
    public function waitDistribution(Request $request)
    {
        //店铺id
        $shopid=$request->param("shopid");

        //联表查询订单数据
        $data=db::view('order','id oid,ordernum,user_id,address_id,shop_id,total_fee,distribution_fee,platform_fee,status,add_time,pre_delivery_time,pre_meal_time,odd_numbers,is_pro_order,dispatch_type,invite_time')//订单表
        ->view('user','id','order.user_id=user.id')//用户表
        ->view('address','name,sex,phone,sheng,shi,qu,address,building_card,jd,wd','address.id=order.address_id')//用户地址表
        ->view('shop_address','shopid,longitude,dimension','shop_address.shopid=order.shop_id')
            ->where('order.shop_id='.$shopid)
            ->where("(order.status=3 or order.status=4) and date_format(date_sub(now(), interval 0 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->order("add_time desc")
            ->select();


        $countArr=count($data);
        for($i=0;$i<$countArr;$i++){
            //格式化时间
            $data[$i]["add_time"]=date("m-d H:i",$data[$i]["add_time"]);
            $data[$i]["pre_delivery_time"]=date("H:i",$data[$i]["pre_delivery_time"]);
            $data[$i]["pre_meal_time"]=date("H:i",$data[$i]["pre_meal_time"]);
            $data[$i]["invite_time"]=date("H:i",$data[$i]["invite_time"]);
            //查询商家到送餐地点的距离
            //$data[$i]["distance"]=Loader::model('Distance')->get_distance([$data[$i]["longitude"],$data[$i]["dimension"]],[$data[$i]['jd'],$data[$i]['wd']]);
            //查询订单里的商品
            $data[$i]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
                ->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
                ->where('order_goods.order_id='.$data[$i]["id"])
                ->select();
            //平台服务费
            $data[$i]["service_price"]=4;
            //预计收入
            $data[$i]["total_fee"]=$data[$i]["total_fee"]-$data[$i]["service_price"]+$data[$i]["platform_fee"];

        }

        return $this->message($data,"成功",2);
    }

    /**
     * 用户申请的退款订单
     */
    public function requestRefund(Request $request)
    {
        //店铺id
        $shopid=$request->param("shopid");

        //联表查询订单数据
        $data=db::view('order','id oid,ordernum,user_id,address_id,shop_id,total_fee,distribution_fee,platform_fee,status,add_time,pre_delivery_time,pre_meal_time,odd_numbers,is_pro_order,dispatch_type,invite_time')//订单表
        ->view('user','id','order.user_id=user.id')//用户表
        ->view('address','name,sex,phone,sheng,shi,qu,address,building_card,jd,wd','address.id=order.address_id')//用户地址表
        ->view('shop_address','shopid,longitude,dimension','shop_address.shopid=order.shop_id')
            ->where('order.shop_id='.$shopid)
            ->where("order.status=8 and date_format(date_sub(now(), interval 0 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
            ->order("add_time desc")
            ->select();


        $countArr=count($data);
        for($i=0;$i<$countArr;$i++){
            //格式化时间
            $data[$i]["add_time"]=date("m-d H:i",$data[$i]["add_time"]);
            $data[$i]["pre_delivery_time"]=date("H:i",$data[$i]["pre_delivery_time"]);
            $data[$i]["pre_meal_time"]=date("H:i",$data[$i]["pre_meal_time"]);
            $data[$i]["invite_time"]=date("H:i",$data[$i]["invite_time"]);
            //查询商家到送餐地点的距离
            //$data[$i]["distance"]=Loader::model('Distance')->get_distance([$data[$i]["longitude"],$data[$i]["dimension"]],[$data[$i]['jd'],$data[$i]['wd']]);
            //查询订单里的商品
            $data[$i]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
                ->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
                ->where('order_goods.order_id='.$data[$i]["id"])
                ->select();
            //平台服务费
            $data[$i]["service_price"]=4;
            //预计收入
            $data[$i]["total_fee"]=$data[$i]["total_fee"]-$data[$i]["service_price"]+$data[$i]["platform_fee"];
            //订单序列号
            $data[$i]["serial_number"]=$i+1;
        }

        return $this->message($data,"成功",2);
    }



    //用户催单
	public function businessReceipt(Request $request)
	{
		//店铺id
		$shopid=$request->param("shopid");

		//联表查询订单数据
		$data=db::view('order','id,ordernum,user_id,address_id,shop_id,total_fee,status,add_time')//订单表
            ->view('user','id','order.user_id=user.id')//用户表
            ->view('rider','nickname,img,phone riderphone','order.rider_id=rider.id')//骑手表
            ->view('address','name,sex,phone,sheng,shi,qu,address,building_card,jd,wd','address.id=order.address_id')//用户地址表
	        ->view('shop_address','shopid,longitude,dimension','shop_address.shopid=order.shop_id')
	        ->where('order.shop_id='.$shopid)
	        ->where("order.status=7 and date_format(date_sub(now(), interval 0 day),'%Y-%m-%d') <= date_format(from_unixtime(add_time),'%Y-%m-%d')")
	        //->order("id desc")
	        ->select();
        
        
        $countArr=count($data);
        for($i=0;$i<$countArr;$i++){
          //格式化时间
          $data[$i]["add_time"]=date("m-d H:i",$data[$i]["add_time"]);
          //查询商家到送餐地点的距离
          //$data[$i]["distance"]=Loader::model('Distance')->get_distance([$data[$i]["longitude"],$data[$i]["dimension"]],[$data[$i]['jd'],$data[$i]['wd']]);
          //查询订单里的商品
          $data[$i]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
          					->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
          					->where('order_goods.order_id='.$data[$i]["id"])
          					->select();
          //平台服务费
          $data[$i]["service_price"]=4;
          					
        }

		if($data){
			return $this->message($data,"成功",2);
		}
	}

    /**
     * 预订单设置
     */
    public function PerOrderSet(Request $request)
    {
        $data=$request->param();
        $arr=[
            'shopid'=>$data["shopid"],
            'is_accept_preorder'=>$data["is_accept_preorder"],
            'accept_predate'=>$data["accept_predate"]
        ];
        $perSet=db("shop_setpreorder")->where("shopid=".$data["shopid"])->select();
        if(count($perSet)>0){
            $result=db("shop_setpreorder")->where("shopid=".$data["shopid"])->update($arr);
            if($result){
                return $this->message($result,"修改设置成功",2);
            }else{
                return $this->message($result,"修改设置失败",3);
            }
        }else{
            $result=db("shop_setpreorder")->insert($arr);
            if($result){
                return $this->message($result,"设置成功",2);
            }else{
                return $this->message($result,"设置失败",3);
            }
        }
    }

    /**
     * 显示预订单设置
     */
    public function ShowPerOrderSet($shopid)
    {
        $perSet=db("shop_setpreorder")->where("shopid=".$shopid)->select();
        return $this->message($perSet,"成功",2);
    }
    /**
     * 是否设置手机自动接单
     */
    public function AutomaticReceipt($shopid)
    {
        $receipt=db("shop")->field("automatic_receipt")->where("id=".$shopid)->select();
        if($receipt[0]["automatic_receipt"]==0){
            $result=db("shop")->where("id=".$shopid)->update(["automatic_receipt"=>1]);
            if($result){
                return $this->message("1","设置自动接单成功",2);
            }else{
                return $this->message("0","设置自动接单失败",3);
            }
        }else if($receipt[0]["automatic_receipt"]==1){
            $result=db("shop")->where("id=".$shopid)->update(["automatic_receipt"=>0]);
            if($result){
                return $this->message("0","取消自动接单成功",2);
            }else{
                return $this->message("1","取消自动接单失败",3);
            }
        }
    }

    /**
     * 商家手动接单 2018.7.17
     */
    public function manualReceipt(Request $request)
    {
        //订单id
        $orderid=$request->param("orderid");
        $result=Db::name("order")->where("id=".$orderid)->update(["status"=>3,"shop_time"=>time()]);
        if($result){
            return$this->message($result,"手动接单成功",2);
        }else{
            return$this->message($result,"手动接单失败",3);
        }

    }

    /**
     * 商家送餐 2018.7.17
     */
    public function foodDelivery(Request $request)
    {
        //订单id
        $orderid=$request->param("orderid");
        $result=Db::name("order")->where("id=".$orderid)->update(["status"=>5,"purchase_time"=>time()]);
        if($result){
            return$this->message($result,"商家送餐正在送餐",2);
        }else{
            return$this->message($result,"商家送餐失败",3);
        }

    }

    //显示预订单
    public function showPreorder(Request $request)
    {
        //店铺id
        $shopid=$request->param("shopid");
        //联表查询订单数据
        $data=db::view('order','id oid,ordernum,user_id,address_id,shop_id,total_fee,status,add_time,odd_numbers,shop_time,is_pro_order,expected_time,delivery_time')//订单表
        ->view('user','id','order.user_id=user.id')//用户表
        ->view('rider','id rid,nickname,img,phone riderphone','order.rider_id=rider.id')//骑手表
        ->view('address','name,sex,phone,sheng,shi,qu,address,building_card,jd,wd','address.id=order.address_id')//用户地址表
        ->view('shop_address','shopid,longitude,dimension','shop_address.shopid=order.shop_id')
        ->where('order.shop_id='.$shopid)
        ->where('order.is_pro_order=1')
        ->select();

        $countArr=count($data);
        for($i=0;$i<$countArr;$i++){
            //格式化时间
            $data[$i]["add_time"]=date("m-d H:i",$data[$i]["add_time"]);
            $data[$i]["shop_time"]=date("H:m",$data[$i]["shop_time"]);
            $data[$i]["expected_time"]=date("H:i",$data[$i]["expected_time"]);
            $data[$i]["delivery_time"]=date("m-d H:i",$data[$i]["delivery_time"]);
            //查询商家到送餐地点的距离
            //$data[$i]["distance"]=Loader::model('Distance')->get_distance([$data[$i]["longitude"],$data[$i]["dimension"]],[$data[$i]['jd'],$data[$i]['wd']]);
            //查询订单里的商品
            $data[$i]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
                ->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
                ->where('order_goods.order_id='.$data[$i]["id"])
                ->select();
            //平台服务费
            $data[$i]["service_price"]=4;
        }
        if($data){
            return $this->message($data,"成功",2);
        }
    }

    //查询今日订单收入和今日订单数量
    public function todayOrder(Request $request)
    {
        $shopid=$request->param("shopid");

        $order=db("order")->where("shop_id=".$shopid." and status=6")->select();

        $arr=[];$money=0;$num=0;
        for($i=0;$i<count($order);$i++)
        {
            $order[$i]["delivery_time"]=date("Y-m-d",$order[$i]["delivery_time"]);

            //判断是否是今日订单
            if($order[$i]["delivery_time"]==date("Y-m-d",time())){
                $data[]=number_format($order[$i]["total_fee"],2)-number_format($order[$i]["distribution_fee"],2);
                $money=array_sum($data);
                $num=count($data);
                $arr=['money'=>$money,'num'=>$num];

            }
        }
        return $this->message($arr,"成功",2);

    }


    //财务对账,查询余额,今日订单详情,历史订单
    public function Finance(Request $request){

	    $shopid=$request->param("shopid");
	    //余额
	    $money=db("shop")->where("id=".$shopid)->select();

	    //今日订单外卖
        $takeout_ordernum=db("order")->where("shop_id=".$shopid." and status=6 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') ")->count();
        $takeout_order=db("order")->where("shop_id=".$shopid." and status=6 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') ")->sum("total_fee");
        $takeout_order=number_format($takeout_order,2);
        //今日订单退款
        $refund_ordernum=db("order")->where("shop_id=".$shopid." and status=9 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') ")->count();
        $refund_order=db("order")->where("shop_id=".$shopid." and status=9 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') ")->sum("total_fee");
        $refund_order=number_format($refund_order,2);
        //历史订单
        $all_order=db("order")->field("id,delivery_time,total_fee")->where("shop_id=".$shopid)->limit(10)->select();
        for ($i=0;$i<count($all_order);$i++){
            $all_order[$i]["delivery_time"]=date("Y.m.d H:m",$all_order[$i]["delivery_time"]);
        }

        $arr=[
            "money"=>$money[0]["money"],//余额
            "takeout_ordernum"=>$takeout_ordernum,//今日外卖订单数量
            "takeout_order"=>$takeout_order,//今日外卖订单的钱
            "refund_ordernum"=>$refund_ordernum,//今日退款订单数量
            "refund_order"=>$refund_order,//今日订单外卖的钱
            "all_order"=>$all_order,//历史订单

        ];

        return $this->message($arr,"成功",2);

    }

    //查询历史订单详情
    public function orderDetails(Request $request)
    {
        $orderid=$request->param("orderid");
        $details=db::view('order','id,ordernum,total_fee,add_time,mileage')//订单表
            ->view('user','id','order.user_id=user.id')//用户表
            ->view('rider','nickname,img,phone riderphone','order.rider_id=rider.id')//骑手表
            ->view('address','name,sex,phone,sheng,shi,qu,address,building_card','address.id=order.address_id')//用户地址表
            ->where('order.id='.$orderid)
            ->where('order.status=6')
            //->order("id desc")
            ->select();
        $details[0]["goods"]=db::view('order_goods','order_id,goods_id,num,total')
            ->view('goods','goods_name,goods_price','goods.id=order_goods.goods_id')
            ->where('order_goods.order_id='.$details[0]["id"])
            ->select();
        //格式化时间
        $details[0]["add_time"]=date("m-d H:i",$details[0]["add_time"]);

        return $this->message($details,"成功",2);
    }

    //查询余额流水
    public function balanceWater(Request $request)
    {
        $shopid=$request->param("shopid");
        $shop_pay=db("shop_pay")->field("buy_type,money,time")->where("shopid=".$shopid." and is_pay=1")->select();
        $put_forward=db("put_forward")->field("money,add_time")->where("uid=".$shopid." and identity=3")->select();
        for($i=0;$i<count($shop_pay);$i++){
            $shop_pay[$i]["time"]=date("Y-m-d H:m",$shop_pay[$i]["time"]);
        }
        for($i=0;$i<count($put_forward);$i++){
            $put_forward[$i]["add_time"]=date("Y-m-d H:m",$put_forward[$i]["add_time"]);
        }
        $arr=["shop_pay"=>$shop_pay,"put_forward"=>$put_forward];
        return $this->message($arr,"成功",2);
    }

    //根据日期查询余额流水
    public function dateBalanceWater(Request $request)
    {
        $shopid=$request->param("shopid");
        $date=$request->param("date");
        $shop_pay=db("shop_pay")->field("buy_type,money,time")->where("shopid=".$shopid." and is_pay=1")->select();
        $put_forward=db("put_forward")->field("money,add_time")->where("uid=".$shopid." and identity=3")->select();
        //格式化充值/支付的时间
        for($i=0;$i<count($shop_pay);$i++){
            if(date("Y-m-d",$shop_pay[$i]["time"])==$date){
                $shop_pay[$i]["time"]=date("Y-m-d H:m",$shop_pay[$i]["time"]);
            }
            else{
                $shop_pay=[];
            }

        }
        //格式化提现的时间
        for($i=0;$i<count($put_forward);$i++){
            if(date("Y-m-d",$put_forward[$i]["add_time"])==$date){
                $put_forward[$i]["add_time"]=date("Y-m-d H:m",$put_forward[$i]["add_time"]);
            }else{
                $put_forward=[];
            }
        }
        $arr=["shop_pay"=>$shop_pay,"put_forward"=>$put_forward];
        return $this->message($arr,"成功",2);
    }

    /**
     * 全部历史订单列表 2018.07.19
     */
    public function OrderHistoryList(Request $request)
    {
        $shopid=$request->param("shopid");
        //$model = new \app\shop\model\Order();
        //历史订单
        $all_order=Db::name("order")
            ->field("id,delivery_time,total_fee")
            ->where("shop_id=".$shopid." and status=6")
            ->order("delivery_time desc")
            ->select();
        foreach ($all_order as $value){
            $value['delivery_time'] = date('Y年m月d日',$value['delivery_time']);
            $list[] = $value;
        }
        //对月份筛选
        /*$mon = $this->arrayMonth($all_order,"delivery_time");
        $mon = array_values($mon);*/
        //对数组进行降维
        //$month=$this->arrayFall($mon);
        //商家所有月份的统计
        //$sum = $model->userLogSum($all_order);

        //把提出来时间当做条件查询历史订单
        /*for($i=0;$i<count($mon);$i++){
            $all_orderlist=Db::name("order")
                ->field("id,
                date_format(from_unixtime(delivery_time),'%Y-%m') delivery_time_str,(total_fee-distribution_fee) total_sum")
                ->where("shop_id=".$shopid." and status=6 and date_format(from_unixtime(delivery_time),'%Y-%m' )='".$mon[$i][0]."'")
                ->select();
            $arr_list[$i] = $all_orderlist;
            $arr_mon[$i] = $mon[$i][0];
        }*/


        /*$arr=[
            "mon"=>$arr_mon,
            "list"=>$arr_list
        ];*/

        return $this->message($list,"成功",2);
    }

    /**
     * 获取月份，合并重复数据
     * @param $data
     * @param $field
     * @return array
     */
    public function arrayMonth($data,$field)
    {

        //取出所有月份
        foreach ($data as $vao){
            //strtotime将时间格式转换为时间戳
            /*if (is_string($vao[$field])){
                $time_num = strtotime($vao[$field]);
            }*/
            $mo['0'] = date('Y-m',$vao[$field]);
            //$mo['1'] = date('m',$time_num);
            $month[] = $mo;
        }
        //去除重复月份
        $mon = $this->arrtUnique($month);
        return $mon;
    }
    /**
     * 合并二维数组的重复数据
     * @param $array2D
     * @return array
     */
    public static function arrtUnique($array2D)
    {
        foreach ($array2D as $v){
            $v=join(',',$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[]=$v;
        }
        $temp=array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
        foreach ($temp as $k => $v){
            $temp[$k]=explode(',',$v); //再将拆开的数组重新组装
        }
        return $temp;
    }


    /**
     * 今日订单明细列表
     */
    public function TodayOrderList(Request $request)
    {

        $shopid=$request->param("shopid");
        //今天的时间
        $todayTime=date("Y.m.d",time());
        //今日外卖订单
        $order=Db::name("order")
            //->view("order_goods","order_id,num,price","order.id=order_goods.order_id")
            ->field("id,discount,distribution_fee,lunch_box_fee,total_fee")
            ->where("status=6 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and shop_id=".$shopid)
            ->select();

        $out_price=0;$out_dis=0;$out_serprice=0;
        if(count($order)==0) {
            $list = [];
        }
        foreach ($order as $value=>$va){
            $va["goods_price"]=0;
            $goods=Db::name("order_goods")
                ->field("id,num,price,(price*num) goods_price")
                ->where("order_id=".$va["id"])->select();
//            $goods = OrderGoods::where("order_id=".$va["id"])->select();
            foreach ($goods as $val=>$v){
                $va['goods_price'] += $v["goods_price"];

                //$lis = $val;
            }

            $va['service_fee'] = 1;

            $out_price+=$va['total_fee'];//今日外卖订单的总金额
            $out_dis+=$va['distribution_fee'];//今日外卖订单的配送费
            $out_serprice+=$va['service_fee'];//今日外卖订单的平台服务费
            $list[] = $va;
        }
        //判断该商家配送方式
        $distribution=Db::name("shop_distribution")
            ->where("shop_id=".$shopid)
            ->find();
        if($distribution["dis_type"]==1){
            //平台配送
            $out_price=$out_price-$out_dis-$out_serprice;
        }else if($distribution["dis_type"]==2){
            //自送
            $out_price=$out_price-$out_serprice;
        }
        //今日退款订单
        /*$refund=Db::view("order")
            ->view("apply_refund","order_id,user_type,money,status","order.id=order_goods.order_id")
            ->field("id,discount,distribution_fee,lunch_box_fee,total_fee")
            ->where("status=6 and date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and shop_id=".$shopid)
            ->select();*/
        $re_order=Db::name("order")->where("date_format(from_unixtime(delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and shop_id=".$shopid)->select();
        $refund_price=0;
        if(count($re_order)!=0){
            $refund=Db::view("order","total_fee total")
                ->view("apply_refund","money","order.id=apply_refund.order_id")
                //->field("id,discount,distribution_fee,lunch_box_fee,total_fee")
                ->where("apply_refund.status=3 and date_format(from_unixtime(order.delivery_time),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and order.shop_id=".$shopid)
                ->select();

            foreach ($refund as $value=>$va){
                $refund_price+=$va['money'];//今日外卖订单的总金额
            }
        }else{
            $refund=[];
        }
        $arr=[
            "today_time"=>$todayTime,//今天的日期
            "out_order"=>$list,//今日的外卖订单
            "out_num"=>count($list),//今日外卖订单的数量
            "out_price"=>$out_price,//今日外卖订单的营业额
            "refund_order"=>$refund,//今日的退款订单
            "refund_num"=>count($refund),//今日退款的数量
            "refund_price"=>$refund_price,
            "all_money"=>$out_price+$refund_price,//今日总金额
            "out_num"=>count($list),



        ];
        return $this->message($arr,"成功",2);

    }

    /**
     * 今日订单明细列表详情
     */
    public function OrderListDe(Request $request)
    {
        $orderid=$request->param("order_id");
        $order=Db::name("order")
            //->view("order_goods","order_id,goods_id,num,total","order.id=order_goods.order_id")
            ->field("id,shop_id,ordernum,discount,lunch_box_fee,distribution_fee,total_fee,add_time,delivery_time,odd_numbers")
            ->where("id=".$orderid)
            ->find();
        $order_goods=Db::view("order_goods","num,sale")
            ->view("goods","goods_name","order_goods.goods_id=goods.id")
            ->where("order_goods.order_id=".$orderid)
            ->select();
        foreach ($order_goods as $val=>$v){
            $order["goods"][]=$v;
        }
        $order["add_time"]=date("Y-m-d H-m-s",$order["add_time"]);
        $order["delivery_time"]=date("Y-m-d H-m-s",$order["delivery_time"]);
        //计算总价,先查询该商家的配送方式
        $distribution=Db::name("shop_distribution")
            ->where("shop_id=".$order["shop_id"])
            ->find();
        //平台服务费
        $service_money=Config::get("servicemoney.service_money");
        if($distribution["dis_type"]==1){
            //平台配送
            $order["total_fee"]=$order["total_fee"]-$order["total_fee"]-$service_money;
            $order["distribution"]="平台配送";
            $order["service"]=$service_money;
        }else if($distribution["dis_type"]==2){
            //自送
            $order["total_fee"]=$order["total_fee"]-$order["total_fee"];
            $order["distribution"]="商家自配";
            $order["service"]=$service_money;
        }
        return $this->message($order,"成功",2);

    }


    //测试距离
	public function text()
	{
		$info = Loader::model('Distance')->get_distance([118.012951,36.810024],[118.012951,35.810024]);
		halt($info);
		return $this->message($info,"成功",2);
	}
}