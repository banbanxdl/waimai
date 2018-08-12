<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;

class Message extends Controller
{
	/**
	*@param 骑手消息列表
	*/
	public function getRiderMessage()
	{
		$message=Db::name('message')->where('type',2)->select();

		return message($message,'获取成功',2);
	}

	/**
	*@param 获取骑手劳务协议
	*/
	public function getRiderlw(Request $request)
	{
		$title=$request->param('title');//平台协议名称

		$result=Db::name('agreement')->where(['title'=>$title,'type'=>2])->find();

		if(empty($result)){
			$result=array();
		}

		return message($result,'获取成功',2);
	}

	/**
	*@param 获取骑手端当前版本
	*/
	public function getRiderEdition(Request $request)
	{
		$phone_type=$request->param('phone_type');//手机类型

		$result=Db::name('app_edition')->where(['phone_type'=>$phone_type,'type'=>2])->field('edition')->find();

		return message($result,'获取成功',2);
	}

	
}
