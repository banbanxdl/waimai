<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;

class Rankinglist extends Controller
{
   
	/**
	*@param 骑手排行榜 昨日单量榜
	*/
	public function getRiderSingleList()
	{
		//查询所有骑手
		$rider=Db::name('rider')->field('id,nickname,img')->select();

		$beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));//昨日开始时间
		$endYesterday  =mktime(0,0,0,date('m'),date('d'),date('Y'))-1;//昨日结束时间

		//查询昨日已完成外卖订单
		$where['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where['status']  =6;
		$where['dispatch_type']=1;

		//查询昨日已完成的跑腿订单
		$where_run['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where_run['status']  =5;


		//查询昨日已完成的专送订单
		$where_take['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where_take['status']  =5;

		foreach ($rider as $k => $v) {
			$where['rider_id']=$v['id'];
			$order[]=Db::name('order')->where($where)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();

			$where_run['rider_id']=$v['id'];
		    $run_order[]=Db::name('run_order')->where($where_run)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();

		    $where_take['rider_id']=$v['id'];
		    $take_order[]=Db::name('take_order')->where($where_take)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();
			
			$rider[$k]['count']=$order[$k]+$run_order[$k]+$take_order[$k];
		}
		

		multi_array_sort($rider,'count',SORT_DESC);
		
		return message($rider,'获取成功',2);
	}

	/**
	*@param 骑手昨日历程榜
	*/
	public function getRiderMileageList()
	{
		//查询所有骑手
		$rider=Db::name('rider')->field('id,nickname,img')->select();

		$beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));//昨日开始时间
		$endYesterday  =mktime(0,0,0,date('m'),date('d'),date('Y'))-1;//昨日结束时间

		//查询昨日已完成外卖订单
		$where['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where['status']  =6;
		$where['dispatch_type']=1;

		//查询昨日已完成的跑腿订单
		$where_run['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where_run['status']  =5;


		//查询昨日已完成的专送订单
		$where_take['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where_take['status']  =5;

		foreach ($rider as $k => $v) {
			$where['rider_id']=$v['id'];
			$order[]=Db::name('order')->where($where)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');

			$where_run['rider_id']=$v['id'];
		    $run_order[]=Db::name('run_order')->where($where_run)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');

		    $where_take['rider_id']=$v['id'];
		    $take_order[]=Db::name('take_order')->where($where_take)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');
			
			$rider[$k]['mileage']=$order[$k]+$run_order[$k]+$take_order[$k];
		}
		

		multi_array_sort($rider,'mileage',SORT_DESC);
		
		return message($rider,'获取成功',2);
	}

	/**
	*@param 骑手月单量榜
	*/
	public function getRiderMonthSingle()
	{
		//查询所有骑手
		$rider=Db::name('rider')->field('id,nickname,img')->select();

		$beginYesterday=mktime(0,0,0,date('m'),1,date('Y'));//本月开始时间
		$endYesterday  =mktime(23,59,59,date('m'),date('t'),date('Y'))-1;//本月结束时间

		//查询本月已完成外卖订单
		$where['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where['status']  =6;
		$where['dispatch_type']=1;

		//查询昨日已完成的跑腿订单
		$where_run['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where_run['status']  =5;


		//查询昨日已完成的专送订单
		$where_take['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where_take['status']  =5;

		foreach ($rider as $k => $v) {
			$where['rider_id']=$v['id'];
			$order[]=Db::name('order')->where($where)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();

			$where_run['rider_id']=$v['id'];
		    $run_order[]=Db::name('run_order')->where($where_run)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();

		    $where_take['rider_id']=$v['id'];
		    $take_order[]=Db::name('take_order')->where($where_take)->field('id,ordernum,rider_id,status,add_time,delivery_time')->count();
			
			$rider[$k]['count']=$order[$k]+$run_order[$k]+$take_order[$k];
		}
		

		multi_array_sort($rider,'count',SORT_DESC);
		
		return message($rider,'获取成功',2);
	}

	/**
	*@param 骑手月历程榜
	*/
	public function getRiderMonthMileage()
	{
		//查询所有骑手
		$rider=Db::name('rider')->field('id,nickname,img')->select();

		$beginYesterday=mktime(0,0,0,date('m'),1,date('Y'));//本月开始时间
		$endYesterday  =mktime(23,59,59,date('m'),date('t'),date('Y'))-1;//本月结束时间

		//查询昨日已完成外卖订单
		$where['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where['status']  =6;
		$where['dispatch_type']=1;

		//查询昨日已完成的跑腿订单
		$where_run['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where_run['status']  =5;


		//查询昨日已完成的专送订单
		$where_take['add_time']=array('between',array($beginYesterday,$endYesterday));
		$where_take['status']  =5;

		foreach ($rider as $k => $v) {
			$where['rider_id']=$v['id'];
			$order[]=Db::name('order')->where($where)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');

			$where_run['rider_id']=$v['id'];
		    $run_order[]=Db::name('run_order')->where($where_run)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');

		    $where_take['rider_id']=$v['id'];
		    $take_order[]=Db::name('take_order')->where($where_take)->field('id,ordernum,rider_id,status,add_time,delivery_time,mileage')->sum('mileage');
			
			$rider[$k]['mileage']=$order[$k]+$run_order[$k]+$take_order[$k];
		}
		

		multi_array_sort($rider,'mileage',SORT_DESC);
		
		return message($rider,'获取成功',2);
	}

}
