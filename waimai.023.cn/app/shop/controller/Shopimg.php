<?php
namespace app\shop\controller;
use think\Request;
use think\Loader;
use think\Db;
class Shopimg extends Index
{

	 //上传店铺门脸照
	public function upload_out(Request $request)
	{
	    $files = request()->file('img');
    	$shopid=$request->param("shopid");
    	//halt($files);
    	return $this->message($files,"",1);
    	//调用上传图片的接口
        $info = Loader::model('Img')->picture($files);
        $arr=['out_img'=>$info];
		$result=db("shop")->where('id='.$shopid)->update($arr);
		if($result){
            return $this->message($result,"店铺门脸照上传成功",2);
        }else{
            return $this->message($result,"店铺门脸照上传失败",3);
        }

	}
	 //上传店铺店内照
	public function upload_in(Request $request)
	{
	    $files = request()->file('img');
    	$shopid=$request->param("shopid");
    	//调用上传图片的接口
        $info = Loader::model('Img')->picture($files);
    	$arr=['in_img'=>$info];
		$result=db("shop")->where('id',$shopid)->update($arr);
        if($result){
            return $this->message($result,"店铺店内照上传成功",2);
        }else{
            return $this->message($result,"店铺店内照上传失败",3);
        }

	 }
	 //上传门店logo
	public function upload_logo(Request $request)
	{
	    $files = request()->file('img');
    	$shopid=$request->param("shopid");
    	//调用上传图片的接口
        $info = Loader::model('Img')->picture($files);
    	$arr=['logo'=>$info];
		$result=db("shop")->where('id',$shopid)->update($arr);
        if($result){
            return $this->message($result,"店铺logo上传成功",2);
        }else{
            return $this->message($result,"店铺logo上传失败",3);
        }
	 }

 	 //上传营业执照/许可证
 	public function upload_license(Request $request)
 	{
 	    $files = request()->file('img');
    	$shopid=$request->param("shopid");
        $card_type=$request->param("card_type");
    	//调用上传图片的接口
        $info = Loader::model('Img')->picture($files);

    	$goods=db("shop_card")
            ->where("shop_id=".$shopid." and card_type=".$card_type)
            ->select();
    	$arr=['certificates_img'=>$info,'shop_id'=>$shopid,'card_type'=>$card_type];
    	if($goods){
    		$result=db("shop_card")->where("shop_id=".$shopid." and card_type=".$card_type)->update($arr);
    		if($result){
                return $this->message($result,"营业执照修改成功",2);
            }else{
                return $this->message($result,"营业执照修改失败",3);
            }
    	}
    	$result=db("shop_card")->insertGetId($arr);
		if($result){
			return $this->message($result,"营业执照上传成功",2);
		}else{
            return $this->message($result,"营业执照上传失败",3);
        }

 	}
 	//上传许可证
 	public function upload_Licence(Request $request)
 	{
 	    $files = request()->file('img');
    	$shopid=$request->param("shopid");
    	//调用上传图片的接口
        $info = Loader::model('Img')->picture($files);

    	$goods=db("shop_card")->where('shop_id',$shopid)->select();
    	$arr=['certificates_img'=>$info,'shop_id'=>$shopid];
    	if($goods){
    		$result=db("shop_card")->where('shop_id',$shopid)->update($arr);
            if($result){
                return $this->message($result,"许可证图片修改成功",2);
            }else{
                return $this->message($result,"许可证图片修改失败",3);
            }
    	}
    	$result=db("shop_card")->insertGetId($arr);
		if($result){
			return $this->message($result,"许可证图片上传成功",2);
		}else{
            return $this->message($result,"许可证图片上传失败",3);
        }

 	}
 	//上传法人身份证图片
 	public function upload_Identity(Request $request)
 	{
 	    $files = request()->file('positive');
        $files2 = request()->file('opposite');
    	$shopid=$request->param("shopid");
        $legal_person=$request->param("legal_person");
        $register_number=$request->param("register_number");

        if (empty($files) || empty($files) || empty($files) || empty($files) || empty($files)){
            return $this->message("","参数不对",2);
        }
        //halt($request->param());
        /*$data=$request->param()."+".$files."+".$files2;
        return $this->message($data,"",2);*/
    	//调用上传图片的接口
        $info = Loader::model('Img')->picture($files);
        $info2 = Loader::model('Img')->picture($files2);
        $infoAll=$info.",".$info2;
    	$goods=db("shop_card")->where("shop_id=".$shopid." and card_type=2")->select();
    	$arr=[
            'shop_id'=>$shopid,
            'card_type'=>2,
            'certificates_name'=>"法人身份证照",//证件名称
            'legal_person'=>$legal_person,//法人代表姓名
            'register_number'=>$register_number,//证件注册号(法人身份证号)
    	    'certificates_img'=>$infoAll,

        ];
    	if($goods){
    		$result=db("shop_card")->where("shop_id=".$shopid." and card_type=2")->update($arr);
			if($result){
                return $this->message($result,"法人身份证信息修改成功",2);
            }else{
                return $this->message($result,"法人身份证图片信息失败",3);
            }
    	}
    	$result=db("shop_card")->insertGetId($arr);
		if($result){
			return $this->message($result,"法人身份证信息上传成功",2);
		}else{
            return $this->message($result,"法人身份证信息上传成功",2);
        }

 	}

    /**
     * 上传餐饮食品安全等级图片
     * */
    public function upload_FoodSafety(Request $request)
    {
        $files = request()->file('img');
        $shopid=$request->param("shopid");
        //$card_type=$request->param("card_type");
        //调用上传图片的接口
        $info = Loader::model('Img')->picture($files);

        $goods=db("shop_card")
            ->where("shop_id=".$shopid." and card_type=5")
            ->select();
        $arr=[
            'certificates_name'=>"餐饮食品安全等级",
            'certificates_img'=>$info,
            'shop_id'=>$shopid,
            'card_type'=>5];
        if($goods){
            $result=db("shop_card")->where("shop_id=".$shopid." and card_type=5")->update($arr);
            if($result){
                return $this->message($result,"餐饮食品安全等级修改成功",2);
            }else{
                return $this->message($result,"餐饮食品安全等级修改失败",3);
            }
        }
        $result=db("shop_card")->insertGetId($arr);
        if($result){
            return $this->message($result,"餐饮食品安全等级上传成功",2);
        }else{
            return $this->message($result,"餐饮食品安全等级上传失败",3);
        }

    }

    /**
     * 显示餐饮食品安全等级图片
     */
    public function showFoodSafety(Request $request)
    {
        $shopid=$request->param("shopid");
        $shop_card=Db::name("shop_card")->field("id,shop_id,card_type,certificates_img")->where("shop_id=".$shopid." and card_type=5")->find();
        return $this->message($shop_card,"成功",2);
    }
 	 

}
