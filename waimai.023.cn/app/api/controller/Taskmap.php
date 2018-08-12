<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;
use app\api\model\Taskmap as M_Taskmap;

class Taskmap extends Controller
{
   
	/**
	*@param 骑手任务地图
	*/
	public function getTaskMap(Request $request)
	{
		$rider_id=$request->param('id');//骑手id

		$result=Loader::model('Taskmap')->getTaskMap($rider_id);

		return message($result,'获取成功',2);

	} 

	/**
	*@param 骑手常见问题
	*/
	public function getRiderProblem(Request $request)
	{
		$page=$request->param('page');
		$result=Db::name('questions')->where('type',2)->page($page,2)->select();

		return message($result,'获取成功',2);
	}

	/**
	*@param 骑手历史任务
	*/
	public function getHistoricalTask(Request $request)
	{
		$rider_id=$request->param('id');//骑手id

		$page    =$request->param('page');//分页


		$result=Loader::model('Taskmap')->getHistoricalTask($rider_id,$page);

		return message($result,'获取数据成功',2);
	}

	/**
	* @param 骑手业务统计
	*/
	public function  getRiderStatistics(Request $request)
	{
		$rider_id=$request->param('id');//骑手id

		$result=Loader::model('Taskmap')->RiderStatistics($rider_id);

		return message($result,'获取成功',2);
	}

	/**
	* @param 骑手意见反馈
	*/
	public function getRiderFeedback(Request $request)
	{
		$data=$request->param();
		$img=implode(',', OssUplodas('img') );//配图
	//	$result=Loader::model('Taskmap')->addRiderFeedback($data);
		$result=Loader::model('Taskmap')->addRiderFeedback($data,$img);
		if($result){
			return message('','提交成功',2);
		}else{
			return message('','提交失败',3);
		}
	}

	

}
