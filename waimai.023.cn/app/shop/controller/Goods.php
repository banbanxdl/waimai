<?php
namespace app\shop\controller;
use think\Request;
use think\Loader;
use think\Db;
use think\Image;
class Goods extends Index
{
	//添加菜单
	public function addMenu(Request $request)
	{
		$data=$request->param();
		$arr=[
			'shop_id'=>$data["shopid"],
			'menu_name'=>$data["menu_name"],
            //'second_level'=>$data["second_level"],
			'describe'=>$data["describe"],
			//'is_top'=>$data["is_top"]
		];
		$menu=db("goods_menu")->where(['shop_id'=>$data['shopid'],'menu_name'=>$data['menu_name']])->select();
		if ($menu) {
			return $this->message("","该菜单已存在",3);
		}
		$result=db("goods_menu")->insert($arr);
		if($result){
			return $this->message("","菜单添加成功",2);
		}
	}

    //显示菜单列表
	public function showMenu(Request $request)
	{
		$shopid=$request->param("shopid");
		$menu=Db::name("goods_menu")
            ->field("id,shop_id,menu_name,describe")
            ->where("shop_id=".$shopid." and pid=0")->select();
        foreach ($menu as $k => $v) {
            $goods=Db::name("goods")->field("id,menu_id")->where("menu_id=".$v["id"])->column("id");
            $menu[$k]["goods_count"]=count($goods);
        }

		/*for($i=0;$i<count($menu);$i++){
		    $menus=db("goods_menu")->field("id")->where("pid=".$menu[$i]["id"])->select();
		    //判断是否有下级
		    if(count($menu)==0){

            }
		    for ($a=0;$a<count($menus);$a++){
                $goods=db("goods")->where("menu_id=".$menus[$a]["id"])->column("id");
                $a=count($goods)-1;
                //dump($a);
            }
            $menu[$i]["goodscount"]=$a;
        }*/
		if($menu){
			return $this->message($menu,"成功",2);
		}
	}
	//修改菜单
	public function updMenu(Request $request)
	{
		$data=$request->param();
		$arr=[
			'menu_name'=>$data['menu_name'],
			'describe'=>$data['describe'],
			'is_top'=>0
		];
		$menu=db("goods_menu")->where(['shop_id'=>$data['shopid'],'menu_name'=>$data['menu_name']])->select();
		if ($menu) {
			return $this->message("","该菜单已存在",3);
		}
		$result=db("goods_menu")->where("id=".$data["id"])->update($arr);
		if($result){
			return $this->message("","菜单修改成功",2);
		}
	}
	//删除菜单
	public function delMenu(Request $request)
	{
		$id=$request->param("id");
		$result=db("goods_menu")->where("id=".$id)->delete();
		return $this->message("","删除成功",2);
	}

	//在一级菜单里显示二级菜单
    public function showTwoLevelMenu(Request $request)
    {
        $data=$request->param();
        $menu=db("goods_menu")->where("shop_id=".$data["shopid"]." and pid=".$data["pid"])->select();
        return $this->message($menu,"成功",2);

    }

    //在一级菜单里面新增二级菜单
    public function addTwoLevelMenu(Request $request)
    {
        $data=$request->param();
        $arr=[
            'pid'=>$data["pid"],
            'shop_id'=>$data["shopid"],
            'menu_name'=>$data["menu_name"],
            'describe'=>$data["describe"]
        ];
        $result=db("goods_menu")->insert($arr);
        if($result){
            return $this->message($result,"添加成功",2);
        }else{
            return $this->message($result,"添加失败",2);
        }
    }

    //修改二级菜单
    public function updTwoLevelMenu(Request $request)
    {
        $data=$request->param();
        $arr=[
            'menu_name'=>$data["menu_name"],
            'describe'=>$data["describe"]
        ];
        $result=db("goods_menu")->where("id=".$data["mid"])->update($arr);
        if($result){
            return $this->message($result,"修改成功",2);
        }else{
            return $this->message($result,"修改失败",2);
        }
    }

    //添加商品(也可修改商品)
	public function addGoods(Request $request)
	{
		$data=$request->param();
		$arr=[
		    'id'=>$data["id"],//商品id
			'shop_id'=>$data["shopid"],//店铺id
			'goods_name'=>$data["goods_name"],//商品名称
			'goods_price'=>$data["goods_price"],//商品价格
			'menu_id'=>$data["menu_id"],//菜单id
			'begin_time'=>$data["begin_time"],//开始时间
            'end_time'=>$data["end_time"],//开始时间
			'label_one'=>$data["label_one"],//标签一
			'label_two'=>$data["label_two"],//标签二
			'label_three'=>$data["label_three"],//标签三
			//'lunch_box_num'=>$data["lunch_box_num"],//餐盒数
			'lunch_box_price'=>$data["lunch_box_price"],//餐盒价格
			'describe'=>$data["describe"],//描述信息
			'stock'=>$data["stock"],//库存
			'is_lower'=>0,//未下架
			'add_time'=>time(),//商品添加时间
            'norms_id'=>$data["norms_id"],//商品规格id
		];
		$goods=db("goods")->where('id',$data['id'])->select();
		if ($goods) {
			$result=db("goods")->where('id',$data['id'])->update($arr);
			return $this->message($result,"商品修改成功",2);
		}
		$result=db("goods")->insertGetId($arr);
		if($result){
			return $this->message($result,"商品添加成功",2);
		}
	}
	 // 商品图片上传
    public function addGoodsimg(Request $request)
    {
    	$files = request()->file('img');
    	$id=$request->param("id");
    	$shopid=$request->param("shopid");
        //return $this->message($files."+".$id."+".$shopid,"数据返回",2);
    	//调用上传图片的接口
        $info = Loader::model('Img')->picture($files);
    	$goods=db("goods")->where('id',$id)->select();
    	$arr=['goods_img'=>$info,'shop_id'=>$shopid];
    	if($goods){
    		$result=db("goods")->where('id',$id)->update($arr);
			return $this->message("","商品图片修改成功",2);
    	}
    	$result=db("goods")->insertGetId($arr);
		if($result){
			return $this->message($result,"商品图片上传成功",2);
		}
    }

    //在商品选择菜单里面显示所有二级菜单
    public function AllTwoLevelMenu()
    {
        $menu=db("goods_menu")->field("id,menu_name")->where("pid!=0")->select();
        return $this->message($menu,"成功",2);
    }


    //显示指定商品信息
    public function GoodsDetail(Request $request)
    {
        $id=$request->param("id");
        $goods=Db::name("goods")->where("id=".$id)->find();

        $menu=Db::name("goods_menu")->where("id=".$goods["menu_id"])->find();
        $goods["menu_name"]=$menu["menu_name"];
        if($goods){
            return $this->message($goods,"成功",2);
        }
    }

    //显示商品
	public function showGoods(Request $request)
	{
		$shopid=$request->param("shopid");
		$goods=Db::name("goods")->where("shop_id=".$shopid)->find();
		if($goods){
			return $this->message($goods,"成功",2);
		}
	}

	//商品上下架
	public function isLower(Request $request)
	{
		$id=$request->param("id");
        $type=$request->param("type");
        $result=Db::name("goods")->where("id=".$id)->find();
        if($type==0){
            if ($result["is_lower"]==0){
                return $this->message(0,"商品已上架",2);
            }else{
                $yeslower=db("goods")->where("id=".$id)->update(["is_lower"=>$type]);
                return $this->message(0,"商品上架成功",2);
            }
        }else if($type==1){
            if ($result["is_lower"]==1){
                return $this->message(1,"商品已下架",2);
            }else{
                $yeslower=db("goods")->where("id=".$id)->update(["is_lower"=>$type]);
                return $this->message(1,"商品下架成功",2);
            }
        }

	}

	/**
     * 商品管理显示的菜单和商品
     */
	public function MenuAndGoods(Request $request)
    {
        $shopid=$request->param("shopid");
        $menu=db("goods_menu")
            ->field("id,menu_name,describe")
            //->view("goods","goods_img,goods_name,goods_price,is_lower","goods.menu_id=goods_menu.id")
            ->where("shop_id=".$shopid." and pid!=0")
            ->select();
        for($i=0;$i<count($menu);$i++){
            $menu[$i]["goods"]=db::view("goods","id,goods_img,goods_name,goods_price,is_lower")
                ->view("order_goods","goods_id,num","goods.id=order_goods.goods_id")
                //->field("id,goods_img,goods_name,goods_price,is_lower")
                ->where("menu_id=".$menu[$i]["id"])
                ->select();
            /*for(){

            }*/
           /* $menu[$i]["goodsid"]=db::view("goods","id")
                //->view("order_goods","goods_id,num","goods.id=order_goods.goods_id")
                //->field("id")
                ->where("menu_id=".$menu[$i]["id"])
                ->select();*/
            //dump($id);
        }

        return $this->message($menu,"商品上架成功",2);

    }

    /**
     * 显示点击菜单下的商品信息
     */
    public function showGoodsData(Request $request)
    {
        $shopid=$request->param("shopid");
        $menuid=$request->param("menuid");
        $goods=db::view("goods","id goods_id,goods_img,goods_name,goods_price,is_lower,sort")
            //->view("order_goods","num goods_num","goods.id=order_goods.goods_id")
            //->field("id,goods_img,goods_name,goods_price,is_lower")
            //->order("sort asc")
            ->where("shop_id=".$shopid." and menu_id=".$menuid)
            ->select();
        foreach ($goods as $k => $v) {
            $order_goods=Db::name("order_goods")->field("goods_id,num goods_num")->where("goods_id=".$v["goods_id"])->select();
            //dump(count($order_goods));
            $goods[$k]["goods_num"]=count($order_goods);
        }
        return $this->message($goods,"商品上架成功",2);
    }

    /**
     * 添加商品规格
     */
    public function addNorms(Request $request)
    {
        $data=$request->param();
        $arr=[
            "goods_id"=>$data["goods_id"],
            "norms"=>$data["norms"],
            "price"=>$data["price"],
            "stock"=>$data["stock"],
            "lunch_box_price"=>$data["lunch_box_price"],
            "upc_code"=>$data["upc_code"],
            "sku_code"=>$data["sku_code"],
            "position_code"=>$data["position_code"],
        ];
        $result=Db::name("goods_norms")->insertGetId($arr);
        if($result){
            return $this->message($result,"添加成功",2);
        }else{
            return $this->message($result,"添加失败",3);
        }
    }

    /**
     * 查询商品规格
     */
    public function showNorms(Request $request)
    {
        //商品id
        $goods_id=$request->param("goods_id");
        $goods=Db::name("goods")->where("id=".$goods_id)->find();
        if(empty($goods["norms_id"])){
            return $this->message($goods["norms_id"],"成功",2);
        }
        $norms_id=explode(",",$goods["norms_id"]);
        foreach($norms_id as $k=>$v){
            $norms[]=Db::name("goods_norms")->field("id,norms,price,stock,lunch_box_price,upc_code,sku_code,position_code")->where("id=".$v)->find();
        }
        return $this->message($norms,"成功",2);
    }

    /**
     * 修改商品规格
     */
    public function updNorms(Request $request)
    {
        $data=$request->param();
        $arr=[
            //"goods_id"=>$data["goodsid"],
            "norms"=>$data["norms"],
            "price"=>$data["price"],
            "stock"=>$data["stock"],
            "lunch_box_price"=>$data["lunch_box_price"],
            "upc_code"=>$data["upc_code"],
            "sku_code"=>$data["sku_code"],
            "position_code"=>$data["position_code"],
        ];
        $result=Db::name("goods_norms")->where("id=".$data["id"])->update($arr);
        if($result){
            return $this->message($result,"修改成功",2);
        }else{
            return $this->message($result,"修改失败",3);
        }
    }

}