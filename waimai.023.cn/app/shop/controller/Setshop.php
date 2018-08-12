<?php
namespace app\shop\controller;
use think\Request;
use think\Loader;
use think\Db;
class Setshop extends Index
{
	//显示店铺所有信息
	public function showShopdata(Request $request)
	{
		//$bid=$request->param("bid");
		$shopid=$request->param("shopid");
		$shop=db::view("shop","id,bid,head_img,shop_status,send_price,shop_phone,business_week,business_hours,distribution_info")
            ->view("shop_address","sheng,shi,qu,shop_address,longitude,dimension","shop_address.shopid=shop.id")
            //->field("id,bid,head_img,shop_status,send_price,shop_phone,business_week,business_hours,distribution_info")
            ->where("shop.id=".$shopid)
            ->select();
		//$address=db("shop_address")->field("shop_address")->where("shopid=".$shopid)->select();
		//halt($address);
		//$card=db("shop_card")->where("shop_id=".$shopid)->select();
		//$arr=[
			//'shop'=>$shop,
			//'address'=>$address,
			//'card'=>$card
		//];
		return $this->message($shop,"成功",2);
	}
	//显示证件
    public function showCertificates(Request $request)
    {
        $shopid=$request->param("shopid");
        $card=db("shop_card")->where("shop_id=".$shopid)->select();
        return $this->message($card,"成功",2);
    }

    //修改店铺头像(不行)
	public function updHeadimg($shopid,$img){

		 $files = request()->file($img);
		 //halt($files);
		 //定义一个数组存储图片路径
		 $img_data=array();

		 foreach($files as $file){
		     //允许格式
		     $ext=['gif','jpeg','png','jpg','bmp'];

		     //移动到框架应用根目录 /public/uploads/shop/out 目录下
		     $info = $file->validate(['size'=>'10485760','ext'=>$ext])->move(ROOT_PATH . 'public' . DS . 'uploads/shop/headimg');

		     if($info){
		         // 成功上传后 获取上传信息

		         $img_data[]=$info->getSaveName();
		         $arr=['head_img'=>$img_data];
		         //将图片存储路径添加到数据库
		         $result=db("shop")->where("id=".$shopid)->update($arr);
		         if ($result) {
		         	return $this->message("","成功",2);
		         }else{
		         	return $this->message("","失败",3);
		         }

		     }else{
		         // 上传失败获取错误信息
		         return $this->message($file->getError(),"失败",3);

		     }
		 }
		return $img_data;
	}

	//修改店铺起送价
	public function updSendprice(Request $request)
	{
		$shopid=$request->param("shopid");
		$price=$request->param("price");
		$arr=['send_price'=>$price];
		$result=db("shop")->where("id=".$shopid)->update($arr);
		return $this->message($result,"修改成功",2);

	}
	//修改餐厅电话
	public function updShopphone(Request $request)
	{
		$shopid=$request->param("shopid");
		$phone=$request->param("phone");
		$arr=['shop_phone'=>$phone];
		$result=db("shop")->where("id=".$shopid)->update($arr);
		return $this->message($result,"修改成功",2);

	}
	//修改餐厅地址
	public function updShopaddress(Request $request)
	{
		$data=$request->param();
		$arr=[
		    'sheng'=>$data["sheng"],
            'shi'=>$data["shi"],
            'qu'=>$data["qu"],
		    'shop_address'=>$data["address"],
            'longitude'=>$data["longitude"],
            'dimension'=>$data["dimension"]
        ];
		//先查询有无地址
        $addre=db("shop_address")->where("shopid=".$data["shopid"])->select();

		if(count($addre)==0){
            $result=db("shop_address")->where("shopid=".$data["shopid"])->insert($arr);
            return $this->message($result,"添加地址成功",2);
		}
		$result2=db("shop_address")->where("shopid=".$data["shopid"])->update($arr);
		return $this->message($result2,"修改地址成功",2);

	}
	//修改餐厅营业时间
	public function updBusinesstime(Request $request)
	{
		$data=$request->param();
		$arr=[
			'business_week'=>$data["week"],
			'business_hours'=>$data["hours"],
		];
		$result=db("shop")->where("id=".$data['shopid'])->update($arr);
		return $this->message($result,"修改成功",2);

	}
	//添加/修改店铺公告
	public function updBusinessNotice(Request $request)
	{
		$data=$request->param();
		$arr=[
			'shopid'=>$data['shopid'],
			'notice'=>$data["notice"],
		];
		$se=db("shop_notice")->where("shopid=".$data['shopid'])->select();
		if($se){
			$result=db("shop_notice")->where("shopid=".$data['shopid'])->update($arr);
			return $this->message($result,"修改成功",2);
		}
		$result=db("shop_notice")->insert($arr);
		return $this->message($result,"添加成功",2);

	}

	//修改店铺配送信息
    public function updDistribution(Request $request)
    {
        $shopid=$request->param("shopid");
        $shop=db("shop")->field("distribution_info")->where("id=".$shopid)->select();
        if($shop[0]["distribution_info"]==0){
            //将自己配送改为专送
            $arr=[
                'distribution_info'=>1
            ];
            $result=db("shop")->where("id=".$shopid)->update($arr);
            if($result){
                return $this->message($result,"修改成功",2);
            }else{
                return $this->message($result,"修改失败",3);
            }
        }else if($shop[0]["distribution_info"]==1){
            //将专送改为自己配送
            $arr=[
                'distribution_info'=>0
            ];
            $result=db("shop")->where("id=".$shopid)->update($arr);
            if($result){
                return $this->message($result,"修改成功",2);
            }else{
                return $this->message($result,"修改失败",3);
            }
        }
    }

    /**
     * 修改店铺简介(18.07.12)
     */
    public function updShopinfo(Request $request)
    {
        $data=$request->param();
        $arr=[
            'id'=>$data['shopid'],
            'shop_info'=>$data["shopinfo"],
        ];

        $result=Db::name("shop")->where("id=".$data['shopid'])->update($arr);
        if($result){
            return $this->message($result,"修改店铺简介成功",2);
        }else{
            return $this->message($result,"修改店铺简介失败",3);
        }
    }

    /**
     *显示店铺图片(18.07.14)
     */
    public function showShopPhoto(Request $request)
    {
        $shopid=$request->param("shopid");
        $photo=Db::name("shop_photo")->field("id,shop_id,imgurl")->where("shop_id=".$shopid)->select();
        return $this->message($photo,"成功",2);
    }

    /**
     *添加店铺图片(18.07.14)
     */
    public function addShopPhoto(Request $request)
    {
        $data=$request->param();
        $arr=[
            'shop_id'=>$data["shopid"],
            'imgurl'=>$data["imgurl"],
            'add_time'=>time()
        ];
        $photo=Db::name("shop_photo")->where("shop_id=".$data["shopid"])->insert($arr);
        if($photo){
            return $this->message($photo,"添加成功",2);
        }else{
            return $this->message($photo,"添加失败",3);
        }
        return $this->view;
    }
    /**
     *删除店铺图片(18.07.14)
     */
    public function delShopPhoto(Request $request)
    {
        $id=$request->param("id");
        $value = explode(',', $id);
        for ($i=0;$i<count($value);$i++) {
            $result=Db::name("shop_photo")->where("id=".$value[$i])->delete();
        }
        //halt($result);
        if($result){
            return $this->message($result,"删除成功",2);
        }else{
            return $this->message($result,"删除失败",3);
        }
    }


}