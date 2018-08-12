<?php
namespace app\shop\controller;
use think\Exception;
use think\Request;
use think\Loader;
use think\Db;
use think\Session;
class Personal extends Index
{

	//个人中心显示店铺信息
	public function showData(Request $request)
	{
		$data=$request->param();

		$business=db("business")->where("id=".$data["bid"])->select();

		$shop=db("shop")->where("id=".$data["shopid"])->select();

		$arr=[
			'bid'=>$business[0]['id'],//商家id
			'account_name'=>$business[0]['account_name'],//商家名称
			'shopid'=>$shop[0]['id'],//店铺id
			'shop_name'=>$shop[0]['shop_name'],//店铺名称
			'head_img'=>$shop[0]['head_img'],//店铺头像
			//'qrcode'=>$shop[0]['qrcode'],//店铺二维码
			'shop_status'=>$shop[0]['shop_status'],//店铺状态
            'distribution_info'=>$shop[0]['distribution_info'],//店铺配送信息
		];

		if($business || $shop){
			return $this->message($arr,"成功",2);
		}
	}

	//修改营业状态
	public function updShopstatus(Request $request)
	{
		$bid=$request->param("bid");
		$shopid=$request->param("shopid");
		//查询店铺状态
		$status=db("shop")->where("id=".$shopid." and bid=".$bid)->select();

		//状态为0改为1
		if($status[0]["shop_status"]==0){
			$arr=['shop_status'=>1];
			$result=db("shop")->where("id=".$shopid." and bid=".$bid)->update($arr);
			if($result){
				return $this->message("1","开启营业成功",2);
			}else{
				return $this->message("","开启营业失败",3);
			}
		}
		//状态为1改为0
		if($status[0]["shop_status"]==1){
			$arr=['shop_status'=>0];
			$result=db("shop")->where("id=".$shopid." and bid=".$bid)->update($arr);
			if($result){
				return $this->message("0","关闭营业成功",2);
			}else{
				return $this->message("","关闭营业失败",3);
			}
		}
	}

	//商家服务中心显示问题分类
	public function serviceType()
	{
		$data=db("business_service_type")
            ->select();
		for ($i=0;$i<count($data);$i++){
            $data[$i]["pro"]=db("business_service_problem")
                ->field("id,problem")
                ->where("typeid=".$data[$i]["id"])
                ->select();
        }
		return $this->message($data,"成功",2);
	}

	//商家服务中心显示问题
	public function serviceProblem(Request $request)
	{
		$id=$request->param("id");
		$data=db("business_service_problem")
            //->field("id,content")
            ->where("id=".$id)->select();
		return $this->message($data,"成功",2);
	}

	//商家提交意见反馈
	public function businessFeedback(Request $request)
	{
        $files = request()->file('img');
        $data=$request->param();
        //调用上传图片的接口
        //halt($files);
        if($files!=null){
            $info = Loader::model('Img')->picture($files);
        }else{
            $info="";
        }


		$arr=[
			'fid'=>$data['fid'],
            'identity'=>1,
			'phone'=>$data['phone'],
			'content'=>$data['content'],
			'img'=>$info,
		];
		$result=db("feedback")->insert($arr);
		if($result){
			return $this->message("","提交意见反馈成功",2);
		}else{
			return $this->message("","提交意见反馈失败",3);
		}

	}

	//显示商家所有门店
	public function allShop(Request $request)
	{
		$bid=$request->param("bid");
		$data=db("shop")->field("id,bid,shop_name")->where("bid=".$bid)->select();

		return $this->message($data,"成功",2);
	}

    /**
     * 显示我的账户信息
     */
    public function showMyAccount(Request $request)
    {

        $bid=$request->param("bid");
        $data=db("business")->where("id=".$bid)->select();
        $data[0]["login_time"]=date("Y-m-d H:m:s",$data[0]["login_time"]);
        return $this->message($data,"成功",2);
    }

    //修改商家密码
	public function updPwd(Request $request)
	{
		$data=$request->param();
		$business=db("business")->where("id=".$data["bid"])->select();
		//判断是否是初始密码
		if(mb_strlen($business[0]["pwd"],'utf8') == 8){
			//判断输入的旧密码是否正确
			if($data["oldpwd"]!=$business[0]["pwd"]){
				return $this->message("","密码不正确",3);
			}
			//若正确则设置新密码
			$arr=['pwd'=>encrmd($data["newpwd"])];
			$result=db("business")->where("id=".$data["bid"])->update($arr);
			if($result){
				return $this->message("","密码修改成功",2);
			}else{
				return $this->message("","密码修改失败",3);
			}
		}else{
			//判断输入的旧密码是否正确
			if(encrmd($data["oldpwd"])!=$business[0]["pwd"]){
				return $this->message("","密码不正确",3);
			}
			//若正确则设置新密码
			$arr=['pwd'=>encrmd($data["newpwd"])];
			$result=db("business")->where("id=".$data["bid"])->update($arr);
			if($result){
				return $this->message("","密码修改成功",2);
			}else{
				return $this->message("","密码修改失败",3);
			}
		}
	}

	//修改绑定的手机号码
	public function updPhone(Request $request){
		$data=$request->param();
		$business=db("business")->where("phone=".$data["oldphone"])->select();
		if($business==0){
			return $this->message("","该旧手机号不存在",3);
		}
		//判断验证码
		if($data['code']!=session::get('code')){
            return $this->message('','验证码错误',3);
        }

        if(session::get('time')==time()){
            Session::delete('time');
            Session::delete('code');
            return $this->message('','验证码过期',3);
        }
        //判断新手机好是否被绑定过
        $business2=db("business")->where("phone=".$data["newphone"])->select();
        if($business2){
        	return $this->message("","新手机号已被绑定",3);
        }
        $arr=["phone"=>$data["newphone"]];
        $result=db("business")->where("id=".$data["bid"])->update($arr);
        if ($result) {
        	return $this->message("","修改绑定手机号码成功",2);
        }else{
        	return $this->message("","修改绑定手机号码失败",3);
        }

	}

    /**
     * 添加/修改商家预定日期2018-8-3
     */
    public function addShopPerDate(Request $request)
    {
        $shopid=$request->param("shopid");
        $accept_date=$request->param("accept_date");
        $predate=Db::name("shop_preorder")->where("shop_id=".$shopid)->find();
        if(count($predate)>0){
            try{
                $result=Db::name("shop_preorder")->where("shop_id=".$shopid)->update(["accept_date"=>$accept_date]);
            }catch (\Exception $e){
                return $this->message('',"修改失败",3);
            }
            if($result){
                return $this->message($result,"修改成功",2);
            }else{
                return $this->message($result,"数据保持不变",3);
            }
        }else{
            $result=Db::name("shop_preorder")->insert(["shop_id"=>$shopid,"accept_date"=>$accept_date]);
            if($result){
                return $this->message($result,"添加成功",2);
            }else{
                return $this->message($result,"添加失败",3);
            }
        }
    }

    /**
     * 添加/修改商家预定单提醒时间 2018-8-3
     */
    public function addShopReminTime(Request $request)
    {
        $shopid=$request->param("shopid");
        $remind_time=$request->param("remind_time");
        $predate=Db::name("shop_preorder")->where("shop_id=".$shopid)->find();
        if(count($predate)>0){
            try{
                $result=Db::name("shop_preorder")->where("shop_id=".$shopid)->update(["remind_time"=>$remind_time]);
            }catch (\Exception $e){
                return $this->message('',"修改失败",3);
            }
            if($result){
                return $this->message($result,"修改成功",2);
            }else{
                return $this->message($result,"数据保持不变",3);
            }
        }else{
            $result=Db::name("shop_preorder")->insert(["shop_id"=>$shopid,"remind_time"=>$remind_time]);
            if($result){
                return $this->message($result,"添加成功",2);
            }else{
                return $this->message($result,"添加失败",3);
            }
        }
    }

    /**
     * 修改商家是否接收预订单 2018-8-3
     */
    public function updatePreOrderSet(Request $request)
    {
        $shopid=$request->param("shopid");
        $preorder=Db::name("shop")->where("id=".$shopid)->find();
        if($preorder["open_preorder"]===1){
            //关闭接收预订单

            $resultOne=Db::name("shop")->where("id=".$shopid)->update(['open_preorder'=>2]);
            //halt($resultOne);
            if($resultOne){
                return $this->message(2,"停止成功",2);
            }else{
                return $this->message(1,"停止失败",3);
            }
        }else if($preorder["open_preorder"]===2){
            //开启接收预订单

            $result=Db::name("shop")->where("id=".$shopid)->update(['open_preorder'=>1]);
            //halt($result);
            if($result){

                return $this->message(1,"开启成功",2);
            }else{
                return $this->message(2,"开启失败",3);
            }
        }
    }

    /**
     * 查询商家预订单设置 2018-8-3
     */
    public function showPreOrderSet(Request $request)
    {
        $shopid=$request->param("shopid");
        $pre_order=Db::name("shop_preorder")->where("shop_id=".$shopid)->find();
        $shop=Db::name("shop")->field("open_preorder")->where("id=".$shopid)->find();
        $pre_order["open_preorder"]=$shop["open_preorder"];

        return $this->message($pre_order,"成功",2);
    }
}