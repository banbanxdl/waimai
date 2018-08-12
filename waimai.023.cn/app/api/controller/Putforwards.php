<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Loader;

class Putforwards extends Controller
{
   
	/**
	* @param 骑手申请提现
	*/
	public function setRiderPutForward(Request $request)
	{
		$rider_id=$request->param('id');//骑手id

		$identity=2;//提现身份

		$money=$request->param('money');//提现金额

		$bank_id=$request->param('bank_id');//银行卡id

		$bank_code=$request->param('bank_code');//银行卡开户行编号
		if(empty($rider_id) || empty($identity) || empty($money) || empty($bank_id) || empty($bank_code) ){
			return message('','参数为空',3);
		}

		//判断骑手提现金额是否足够

		$rider_info=Db::name('rider')->where('id',$rider_id)->find();

		if($rider_info['money']<$money){
			return message('','余额不足',3);
		}

		$result=Loader::model('Putforward')->getRiderPutForward($rider_id,$identity,$money,$bank_id,$bank_code);

		if($result){
			return message('','申请提交成功',2);
		}else{
			return message('','申请提交失败',3);
		}

	}

	/**
	*@param 骑手查看提现记录表
	*/
	public function getRiderPresentRecord(Request $request)
	{
		$rider_id=$request->param('id');//骑手id

		$page=$request->param('page');//当前页码

		$identity=2;//身份

		$result=Db::name('put_forward')->where(['uid'=>$rider_id,'identity'=>$identity])->order('add_time desc')->select();

		foreach ($result as $k => $v) {
			$result[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
		}
		//分页
		$model=page_array('10',$page,$result,'0');

		return message($model,'获取列表成功',2);

	}
}
