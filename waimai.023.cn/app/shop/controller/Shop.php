<?php
namespace app\shop\controller;
use think\Request;
use think\Loader;
use think\Db;
class Shop extends Index
{
	//显示店铺类型
    public function showShoptype(Request $request)
    {
        $shoptype=db('shop_type')->select();
        return $this->message($shoptype,"成功",2);
    }

    //显示经营类型
    public function show_businesstype(Request $request)
    {
    	$shop_typeid=$request->param("shop_typeid");//店铺类型
        $businesstype=db('shop_businesstype')
            ->where("shop_type=".$shop_typeid." and pid=0")
            ->select();
        for($i=0;$i<count($businesstype);$i++){
            $businesstype[$i]["Sanji"]=db("shop_businesstype")
                ->where("pid=".$businesstype[$i]["id"])
                ->select();
        }
        return $this->message($businesstype,"成功",2);
    }

    //添加店铺的店铺类型
   /** public function add_shoptype(Request $request){

    	$bid=$request->param("bid");//商家id
    	$shop_type=$request->param("shop_type");//店铺类型
    	$arr= [
    		'bid' =>$bid, 
    		'shop_type'=>$shop_type,
    	];
    	//添加某商家店铺类型到数据库
    	$result=Db('shop')->insert($arr);
    	if($result){
    		return $this->message("","成功",2);
    	}else{
    		return $this->message("","失败",3);
    	}
    }
    //修改店铺的店铺类型
    public function upd_shoptype(Request $request){

    	$bid=$request->param("bid");//商家id
    	$shop_type=$request->param("shop_type");//店铺类型
    	$arr= [
    		//'bid' =>$bid, 
    		'shop_type'=>$shop_type,
    	];
    	//添加某商家店铺类型到数据库
    	$result=Db('shop')->where("bid=".$bid)->update($arr);
    	if($result){
    		return $this->message("","成功",2);
    	}else{
    		return $this->message("","失败",3);
    	}
    }**/

    //添加店铺的店铺名称和类型
    public function addShop(Request $request){

    	$bid=$request->param("bid");//商家id
    	$shop_type=$request->param("shop_type");//店铺类型
    	$shop_name=$request->param("shop_name");//店铺名称
    	$arr= [
    		'bid' =>$bid, 
    		'shop_type'=>$shop_type,
    		'shop_name'=>$shop_name,
    	];
    	//添加某商家店铺数据到数据库
    	$result=Db('shop')->insertGetId($arr);
    	if($result){
    		return $this->message($result,"成功",2);
    	}else{
    		return $this->message($result,"失败",3);
    	}
    }

    //显示店铺的店铺名称和类型
    public function showShopname(Request $request)
    {
        $shop_id=$request->param("shop_id");//店铺id
        $shop=db::view("shop","id,shop_name","shop.shop_type=shop_businesstype.id")
            ->view("shop_businesstype","id tid,typename")
            ->where("shop.id=".$shop_id)->select();
        return $this->message($shop,"成功",2);
    }

    //修改店铺的店铺名称和类型
    public function updShop(Request $request){

    	$shop_id=$request->param("shop_id");//店铺id
    	$shop_type=$request->param("shop_type");//店铺类型
    	$shop_name=$request->param("shop_name");//店铺名称
    	$arr= [
    		'shop_type'=>$shop_type,
    		'shop_name'=>$shop_name,
    	];
    	//添加某商家店铺数据到数据库
    	$result=Db('shop')->where("id=".$shop_id)->update($arr);
    	if($result){
    		return $this->message("","成功",2);
    	}else{
    		return $this->message("","失败",3);
    	}
    }

    //添加店铺的基本信息
    public function addShopbasic(Request $request){

    	$data=$request->param();
    	//halt($data);
    	$shoparr= [
    		'id'=>$data["id"],//店铺id
    		'contacts_phone' =>$data["contacts_phone"], //联系人电话
    		'contacts'=>$data["contacts"], //联系人姓名
    		'shop_phone'=>$data["shop_phone"], //店铺电话
    		'business_hours'=>$data["business_hours"], //营业时间
            'upd_time'=>time(), //编辑时间
    		//'shop_address'=>$data["shop_address"], //店铺地址
    	];
    	$addressarr=[
    		'shopid'=>$data["id"], //店铺id
            'sheng'=>$data["sheng"],//省
            'shi'=>$data["shi"],//市
            'qu'=>$data["qu"],//区
    		'shop_address'=>$data["shop_address"], //店铺地址
    		'longitude'=>$data["longitude"], //店铺经度
    		'dimension'=>$data["dimension"], //店铺纬度
    	];
    	//添加某商家店铺数据到数据库
    	$result=Db('shop')->where("id=".$data["id"])->update($shoparr);
    	$seaddress=db("shop_address")->where("shopid=".$data["id"])->select();
    	
    	$address=Db('shop_address')->insert($addressarr);
    	if($result){
    		if($address){
    			return $this->message("","成功",2);
    		}
    	}else{
    		return $this->message("","失败",3);
    	}
    }



    //显示店铺基本信息
    public function showShopbasic(Request $request)
    {
    	$shopid=$request->param("shopid");
    	$shopdata=db("shop")->where("id=".$shopid)->select();
    	$addressdate=db("shop_address")->where("shopid=".$shopid)->select();
    	$arr=[
    	    'id'=>$shopdata[0]["id"],//店铺id
    		'shop_phone'=>$shopdata[0]["shop_phone"],//外卖电话
    		'contacts'=>$shopdata[0]["contacts"],//联系人姓名
    		'contacts_phone'=>$shopdata[0]["contacts_phone"],//联系人电话
    		'business_hours'=>$shopdata[0]["business_hours"],//营业时间
            'sheng'=>$addressdate[0]["sheng"],//省
            'shi'=>$addressdate[0]["shi"],//市
            'qu'=>$addressdate[0]["qu"],//区
    		'shop_address'=>$addressdate[0]["shop_address"],//门店地址
    		'longitude'=>$addressdate[0]["longitude"],//经度
    		'dimension'=>$addressdate[0]["dimension"],//维度
    		'out_img'=>$shopdata[0]["out_img"],//门脸照
    		'in_img'=>$shopdata[0]["in_img"],//店内照
    		'logo'=>$shopdata[0]["logo"],//门店logo
    	];
    	if ($arr) {
    		return $this->message($arr,"成功",2);
    	}else{
    		return $this->message("","失败",3);
    	}
    }

    //修改店铺的基本信息
    public function updShopbasic(Request $request){

    	$data=$request->param();

    	$shoparr= [
    		'id'=>$data["id"],//店铺id
    		'contacts_phone' =>$data["contacts_phone"], //联系人电话
    		'contacts'=>$data["contacts"], //联系人姓名
    		'shop_phone'=>$data["shop_phone"], //店铺电话
    		'business_hours'=>$data["business_hours"], //营业时间
            'upd_time'=>time(), //编辑时间
    		//'shop_address'=>$data["shop_address"], //店铺地址
    	];
    	$addressarr=[
    		'shopid'=>$data["id"], //店铺id
            'sheng'=>$data["sheng"],//省
            'shi'=>$data["shi"],//市
            'qu'=>$data["qu"],//区
    		'shop_address'=>$data["shop_address"], //店铺地址
    		'longitude'=>$data["longitude"], //店铺经度
    		'dimension'=>$data["dimension"], //店铺纬度
    	];
    	//添加某商家店铺数据到数据库
    	$result=Db('shop')->where("id=".$data["id"])->update($shoparr);
    	//修改店铺地址
    	$address=Db('shop_address')->where("shopid=".$data["id"])->update($addressarr);

    	if($result || $address){
    		
    		return $this->message("","修改成功",2);

    	}else{
    		return $this->message("","修改失败",3);
    	}
    }
    //添加/修改收款信息
    public function addBank(Request $request){

    	$data=$request->param();
    	$arr=[
    		'uid'=>$data["shop_id"],
            'identity'=>3,
    		'account_type'=>$data["account_type"],
    		'name'=>$data["name"],
    		'card'=>$data["card"],
            'city'=>$data["city"],
    		'opening_bank'=>$data["opening_bank"],
            'add_time'=>time(),
    	];
    	$seresult=db("bank_card")->where("uid=".$data["shop_id"])->select();

    	if ($seresult) {
    		//若该店铺已添加收款信息则变为修改信息
    		$result=db("bank_card")->where("uid=".$data["shop_id"])->update($arr);
    		//halt($result);
    		return $this->message($result,"修改成功",2);

    	}
    	$result=db("bank_card")->insert($arr);
    	if($result){
    		return $this->message("","添加成功",2);
    	}else{
    		return $this->message("","添加失败",3);
    	}

    }

    //店铺申请记录
    public function formalRecord($bid){
    	$shop=db("shop")
            ->field("id,logo,shop_name,upd_time")
            ->where("examine_status=0 and bid=".$bid)->select();
    	for($i=0;$i<count($shop);$i++){
    	    if(empty($shop[$i]["upd_time"])){
                $shop[$i]["upd_time"]=0;
            }
            $shop[$i]["upd_time"]=date("Y-m-d",$shop[$i]["upd_time"]);
        }
    	if ($shop) {
    		return $this->message($shop,"成功",2);
    	}
    }
    //店铺审核中记录
    public function auditRecord($bid){
    	$shop=db("shop")
            ->field("id,logo,shop_name,upd_time")
            ->where("examine_status=1 and bid=".$bid)->select();
        for($i=0;$i<count($shop);$i++){
            $shop[$i]["upd_time"]=date("Y-m-d",$shop[$i]["upd_time"]);
        }
    	if ($shop) {
    		return $this->message($shop,"成功",2);
    	}
    }

    //店铺已完成申请记录
    public function finishRecord($bid){
    	$shop=db("shop")
            ->field("id,logo,shop_name,upd_time")
            ->where("(examine_status=2 || examine_status=3) and bid=".$bid)->select();
        for($i=0;$i<count($shop);$i++){
            $shop[$i]["upd_time"]=date("Y-m-d",$shop[$i]["upd_time"]);
        }
    	//if ($shop) {
    		return $this->message($shop,"成功",2);
    	//}
    }

    //显示店铺基本信息
    /**public function basic(Request $request){

    	$shop_id=$request->param("shop_id");
    	$shop=db("shop")->where("id=".$shop_id)->select();
    	if($shop){
			return $this->message($shop,"成功",2);
    	}
    }**/

    //显示营业执照
    public function showLicense(Request $request){

    	$shopid=$request->param("shopid");
    	$shop=db("shop_card")->where("card_type=1 and shop_id=".$shopid)->select();
    	if($shop){
			return $this->message($shop,"成功",2);
    	}
    }

    //显示法人身份证照信息
    public function showIdentity(Request $request){

    	$shopid=$request->param("shopid");
    	$shop=db("shop_card")->where("card_type=2 and shop_id=".$shopid)->select();
    	if($shop){
			return $this->message($shop,"成功",2);
    	}
    }

    //显示许可证信息
    public function showLicence(Request $request){

    	$shopid=$request->param("shopid");
    	$shop=db("shop_card")->where("card_type=3 and shop_id=".$shopid)->select();
    	if($shop){
			return $this->message($shop,"成功",2);
    	}
    }

    //显示账户信息
    public function showBank(Request $request){

    	$shop_id=$request->param("shop_id");
    	$shop=db("bank_card")
            ->field("id,uid,account_type,name,card,city,opening_bank,add_time")
            ->where("uid=".$shop_id)->select();
    	for($i=0;$i<count($shop);$i++){
            $shop[$i]["add_time"]=date("Y-m-d H:m:s",$shop[$i]["add_time"]);
        }
		return $this->message($shop,"成功",2);
    }

    //上传营业执照信息
 	 public function addLicense(Request $request){
 	 	$data=$request->param();

 	 	$arr=[
 	 		'shop_id'=>$data["shop_id"],//店铺id
 	 		'card_type'=>1,//证件类型
 	 		'certificates_name'=>$data["certificates_name"],//证件名称
 	 		'legal_person'=>$data["legal_person"],//法人代表姓名
 	 		'register_number'=>$data["register_number"],//证件注册号
 	 		'certificates_place'=>$data["certificates_place"],//证件所在地
 	 		'validity_type'=>$data["validity_type"],//证件有效类型(长期有效/短期有效)
 	 		'validity_term'=>$data["validity_term"],//有效期限(时间)
 	 	];

 	 	$license=db("shop_card")->where("card_type=1 and shop_id=".$data["shop_id"])->select();
 	 	//若存在数据则变为修改
 	 	if($license){
 	 		$result=db("shop_card")->where("card_type=1 and shop_id=".$data["shop_id"])->update($arr);
 	 		if($result){
 	 			return $this->message("","修改成功",2);
 	 		}else{
 	 			return $this->message("","修改失败",3);
 	 		}
 	 	}
 	 	//若不存在数据则变为添加数据
 	 	$addresult=db("shop_card")->insert($arr);

 	 	if($addresult){
 	 		return $this->message("","添加成功",2);
 	 	}else{
          	return $this->message("","添加失败",3);
        }
 	 }
 	 


 	 //上传法人身份证照信息
 	 public function addlegal(Request $request){
 	 	$data=$request->param();
 	 	//halt($data);
 	 	$arr=[
 	 		'shop_id'=>$data["shop_id"],//店铺id
 	 		'card_type'=>2,//证件类型
 	 		'certificates_name'=>"法人身份证照",//证件名称
 	 		'legal_person'=>$data["legal_person"],//法人代表姓名
 	 		'register_number'=>$data["register_number"],//证件注册号(法人身份证号)
 	 	];

 	 	$legal=db("shop_card")->where("card_type=2 and shop_id=".$data["shop_id"])->select();
 	 	//若存在数据则变为修改
 	 	if($legal){
 	 		$result=db("shop_card")->where("card_type=2 and shop_id=".$data["shop_id"])->update($arr);
 	 		if($result){
 	 			return $this->message("","修改成功",2);
 	 		}else{
 	 			return $this->message("","修改失败",3);
 	 		}
 	 	}
 	 	//添加数据
 	 	$addresult=db("shop_card")->insert($arr);
 	 	if($addresult){
 	 		return $this->message("","添加成功",2);
 	 	}else{
          	return $this->message("","添加失败",3);
        }
 	 }

 	 //上传许可证信息
 	 public function addLicence(Request $request){
 	 	$data=$request->param();
 	 	//halt($data);
 	 	$arr=[
 	 		'shop_id'=>$data["shop_id"],//店铺id
 	 		'card_type'=>3,//证件类型
 	 		'certificates_name'=>$data["certificates_name"],//证件名称
 	 		'legal_person'=>$data["legal_person"],//法人代表姓名
 	 		'register_number'=>$data["register_number"],//证件注册号
 	 		'certificates_place'=>$data["certificates_place"],//证件所在地
 	 		'validity_type'=>$data["validity_type"],//证件有效类型(长期有效/短期有效)
 	 		'validity_term'=>$data["validity_term"],//有效期限(时间)
 	 	];

 	 	$licence=db("shop_card")->where("card_type=3 and shop_id=".$data["shop_id"])->select();
 	 	//若存在数据则变为修改
 	 	if($licence){
 	 		$result=db("shop_card")->where("card_type=3 and shop_id=".$data["shop_id"])->update($arr);
 	 		if($result){
 	 			return $this->message("","修改成功",2);
 	 		}else{
 	 			return $this->message("","修改失败",3);
 	 		}
 	 	}
 	 	//添加数据
 	 	$result=db("shop_card")->insert($arr);
 	 	if($result){
 	 		return $this->message("","添加成功",2);
 	 	}else{
          	return $this->message("","添加失败",3);
        }
 	 }

 	 //上传特许证信息
 	 public function addConcession(Request $request){
 	 	$data=$request->param();
 	 	//halt($data);
 	 	$arr=[
 	 		'shop_id'=>$data["shop_id"],//店铺id
 	 		'card_type'=>4,//证件类型
 	 		'certificates_name'=>$data["certificates_name"],//证件名称
 	 		//'legal_person'=>$data["legal_person"],//法人代表姓名
 	 		'register_number'=>$data["register_number"],//证件注册号
 	 		'certificates_place'=>$data["certificates_place"],//证件所在地
 	 		'validity_type'=>$data["validity_type"],//证件有效类型(长期有效/短期有效)
 	 		'validity_term'=>$data["validity_term"],//有效期限(时间)
 	 	];

 	 	$licence=db("shop_card")->where("card_type=4 and shop_id=".$data["shop_id"])->select();
 	 	//若存在数据则变为修改
 	 	if($licence){
 	 		$result=db("shop_card")->where("card_type=4 and shop_id=".$data["shop_id"])->update($arr);
 	 		if($result){
 	 			return $this->message("","修改成功",2);
 	 		}else{
 	 			return $this->message("","修改失败",3);
 	 		}
 	 	}
 	 	//添加数据
 	 	$result=db("shop_card")->where("card_type=4 and shop_id=".$data["shop_id"])->insert($arr);
 	 	if($result){
 	 		return $this->message("","添加成功",2);
 	 	}else{
          	return $this->message("","添加失败",3);
        }
 	 }

}