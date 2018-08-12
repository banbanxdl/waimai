<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;

class Order extends Controller
{
   
   /**
   * @param 骑手订单池 列表 手动接单列表
   */
   public function getOrderList(Request $request)
   {	
   		//待接单 待取货 待送达  订单以商家一公里之内的骑手为准

   		$type=$request->param('type');//1手动接单待接单订单池 2 待取货订单池 3 待送达订单池 4系统派单待接单订单池

   		$rider_id=$request->param('rider_id');//骑手id

   		$rider_jd=$request->param('rider_jd');//骑手当前经度

   		$rider_wd=$request->param('rider_wd');//骑手当前纬度
   		
   		if(empty($type) || empty($rider_id)){
   			return message('','参数为空',3);
   		}

   		$model=Loader::model('Order')->getOrderList($type,$rider_id);
  		
  		//查询待取货和待送达的订单数量

   		$take=Loader::model('Order')->getOrderList(2,$rider_id);

   		$song=Loader::model('Order')->getOrderList(3,$rider_id);

   		//获取数组长度
   		$length_take=count($take,0);
   		$length_song=count($song,0);

   		$array=array(
   			'list'=>$model,
   			'qu_count'=>$length_take,
   			'song_count'=>$length_song
   		);
   		return message($array,'获取成功',2);
   		
   }
  
   /**
   * @param 骑手抢单
   */
	public function getRobbing(Request $request)
	{
		$rider_id=$request->param('rider_id');//骑手id

		$order_id=$request->param('order_id');//订单id

		$type    =$request->param('type');//订单类型 1 外卖 2 帮买 3专送

		if(empty($rider_id) || empty($order_id) || empty($type)){
			return message('','参数为空',3);
		}

		$order_info=Db::name('order')->where('id',$order_id)->find();

		if(empty($order_info['rider_id'])|| $order_info['rider_id']!=$rider_id){
			return message('','订单已被抢',3);
		}
		switch ($type) {
			case '1':
				$result=Db::name('order')->where('id',$order_id)->update(['rider_id'=>$rider_id,'status'=>4,'receipt_time'=>time()]);
				break;

			case '2':
				$result=Db::name('run_order')->where('id',$order_id)->update(['rider_id'=>$rider_id,'status'=>3,'single_time'=>time()]);
				break;

			case '3':
				$result=Db::name('take_order')->where('id',$order_id)->update(['rider_id'=>$rider_id,'status'=>3,'single_time'=>time()]);
				break;

			default:
				
				break;
		}
		

		if($result){
			return message('','抢单成功',2);
		}else{
			return message('','抢单失败',3 );
		}
	}

	/**
	* @param 骑手确认取货
	*/   
	public function getPickUpGoods(Request $request)
	{
		$order_id=$request->param('order_id');//订单id

		$type    =$request->param('type');//订单类型 1 外卖 2 帮买 3专送

		if(empty($order_id) || empty($type)){
			return message('','参数为空',3);
		}
		switch ($type) {
			case '1':
				$result=Db::name('order')->where('id',$order_id)->update(['status'=>5,'purchase_time'=>time()]);
				break;

			case '2':
				$result=Db::name('run_order')->where('id',$order_id)->update(['status'=>4,'purchase_time'=>time()]);
				break;

			case '3':
				$result=Db::name('take_order')->where('id',$order_id)->update(['status'=>4,'purchase_time'=>time()]);
				break;			
			default:
				# code...
				break;
		}

		if($result){
			return message('','取货成功',2);
		}else{
			return message('','取货失败',3);
		}
	}

	/**
	* @param 骑手确送达
	*/
	public function getService(Request $request)
	{
		$order_id=$request->param('order_id');//订单id

		$type    =$request->param('type');//订单类型 1 外卖 2 帮买 3专送

		if(empty($order_id) || empty($type)){
			return message('','参数为空',3);
		}

		switch ($type) {
			case '1':
				$result=Db::name('order')->where('id',$order_id)->update(['status'=>6,'delivery_time'=>time()]);
				break;

			case '2':
				$result=Db::name('run_order')->where('id',$order_id)->update(['status'=>5,'delivery_time'=>time()]);
				break;

			case '3':
				$result=Db::name('take_order')->where('id',$order_id)->update(['status'=>5,'delivery_time'=>time()]);
				break;	

			default:
				# code...
				break;
		}

		

		if($result){
			return message('','确认送达成功',2);
		}else{
			return message('','确认送达失败',3);
		}

	} 
	/**
	* @param 骑手订单详情
	*/
	public function getOrderDetails(Request $request)
	{
		
		$order_id=$request->param('id');//订单id

		$order_type=$request->param('type');//订单类型 1外卖订单 2 帮买订单 3专送订单

		//$jd=$request->param('jd');//骑手当前位置的经度

		//$wd=$request->param('wd');//骑手当前位置的纬度


		if(empty($order_id) || empty($order_type)){
			
			return message('','参数为空',3);
		}

		$result=Loader::model('Order')->OrderDetails($order_id,$order_type);

		return message($result,'获取数据成功',2);


	}

	/**
	* @param 骑手转接订单
	*/
	public function transferOrder(Request $request)
	{
		$oid=$request->param('oid');//订单id

		$rider_id=$request->param('id');//当前登录的骑手id

		//给所有在该区域的骑手发一个推送
	}


	/**
	* @param 骑手取消订单
	*/
	public function cancelOrder(Request $request)
	{

		$type_order    =$request->param('type_order');// 取消订单类型 1 外卖订单 2 跑腿订单 3专送订单

		$rider_id=$request->param('id');//当前登录的骑手id

		$reason  =$request->param('reason');//取消订单原因

		$oid     =$request->param('oid');//取消订单id

		$identity='2';//取消订单身份

		$result  =Loader::model('order')->getCancelOrder($type_order,$rider_id,$reason,$oid,$identity);

		if($result){
			//返回订单信息
			$model=Loader::model('order')->getCancelOrderInfo($type_order,$oid);
			
			return message($model,'取消订单成功',2);
		}else{
			return message('','取消订单失败',3);
		}
	}	


	//测试距离
    public function test()
    {

    	//查询所有商家的位置

    	$shop_distance=Db::name('shop_address')->select();

    	foreach ($shop_distance as $k => $v) {

    		$result[] = GetRange($v[''],20.031541,5000);
    	}

		$result = GetRange(110.325945,20.031541,5000);

		dump($result);
		// $where = " (`jingdu` between ".$result['minLat']." and ".$result['maxLat'].") and ( `weidu` between ".$result['minLon']." and ".$result['maxLon']." ) ";

		//$query = $db->query("select * from ".DB_PRE."hospital where $where order BY id DESC ");

		//商家距离的五千米内的
		$where['jd']=array('between',array($result['minLat'],$result['maxLat']));
		$where['wd']=array('between',array($result['minLon'],$result['maxLon']));

		$list=Db::name('order')->where($where)->select();

		// while ( $row = $db->fetch_array($query) ) {
 
		//     $list[] = $row['all_name'];
		// }
		print_r($list);    	
    }


}
