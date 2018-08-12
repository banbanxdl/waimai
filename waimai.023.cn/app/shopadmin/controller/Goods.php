<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 10:39
 */

namespace app\shopadmin\controller;
use think\Session;
use think\Db;
use think\Request;

class Goods extends Admin
{
    //显示商品页面
    public function showGoods($menuid=0,$condition=10){
        $bid=session::get("bid");
        if(!isset($bid)){
            $bid=1;
        }
        $shopid=session::get("shopid");
        if(!isset($shopid)){
            //若shopid不存在
            $shop=Db::name("shop")->where("bid=".$bid)->find();
            $shopid=$shop["id"];
        }
        $menuList=$this->showMenu($shopid);
        //计算全部商品数量
        $goodsCount=Db::name("goods")->where("shop_id=".$shopid)->count();
        //计算售卖中的商品数量
        $no_lower=Db::name("goods")->where("shop_id=".$shopid." and is_lower=0")->count();
        //计算已下架的商品数量
        $lower=Db::name("goods")->where("shop_id=".$shopid." and is_lower=1")->count();
        //计算已售完的商品数量
        $finish=Db::name("goods")->where("shop_id=".$shopid." and stock=0")->count();
        //查询商品列表
        $goodsList=$this->showShopData($shopid,$menuid,$condition);
        $this->assign("menuList",$menuList);
        $this->assign("goodsCount",$goodsCount);
        $this->assign("no_lower",$no_lower);
        $this->assign("lower",$lower);
        $this->assign("finish",$finish);
        $this->assign("goodsList",$goodsList);
        return $this->fetch('Goods_managment');
    }

    //显示菜单列表
    public function showMenu($shopid)
    {
        $menu=Db::name("goods_menu")
            ->field("id,shop_id,menu_name,describe")
            ->where("shop_id=".$shopid." and pid=0")->select();
        foreach ($menu as $k => $v) {
            $goods=Db::name("goods")->field("id,menu_id")->where("menu_id=".$v["id"])->column("id");
            $menu[$k]["goods_count"]=count($goods);
        }
        return $menu;
    }


    /**
     * 保存分类数据
     */
    public function addMenu(Request $request)
    {
        $shopid=$request->param("shopid");
        if($request->isPost()){
            $arr=[
                'shop_id'=>$shopid,
                'menu_name'=>$_POST["menu_name"],
                'describe'=>$_POST["describe"],
            ];
            //$menu=db("goods_menu")->where(['shop_id'=>$shopid,'menu_name'=>$_POST['menu_name']])->find();
            if (isset($_POST["menu_id"])) {
                $result=db("goods_menu")->where("id=".$_POST["menu_id"])->update(["menu_name"=>$_POST["menu_name"],"describe"=>$_POST["describe"]]);
                if($result) {
                    return "<script>history.back(-1);</script>";
                }else{
                    return "<script>alert('菜单修改失败');history.back(-1);</script>";
                }
            }
            $result=db("goods_menu")->insert($arr);
            if($result){
                return "<script>history.back(-1);</script>";
            }else{
                return "<script>alert('菜单添加失败');history.back(-1);</script>";
            }
        }

    }

    /**
     * 通过分类条件查询
     */
    public function showShopData($shopid,$menuid,$condition)
    {
        /*$shopid=$request->param("shopid");
        $menuid=$request->param("menuid");
        $condition=$request->param("condition");*/
        //halt($menuid);
        //当menuid为0时表示查询全部商品
        if($menuid==0){
            //当condition为10时表示查询全部商品
            if($condition==10){
                $goods=Db::name("goods")->where("shop_id=".$shopid)->order("id desc")->select();

            }else if ($condition==0 || $condition==1){
                //当condition为0或1时表示上架或已下架的商品
                $goods=Db::name("goods")->where("shop_id=".$shopid." and is_lower=".$condition)->order("id desc")->select();
            }else if ($condition==2){
                //当condition为2时表示已售空的商品
                $goods=Db::name("goods")->where("shop_id=".$shopid." and stock=0")->order("id desc")->select();
            }
            //查询商品分类
            for ($i=0;$i<count($goods);$i++){
                $menu=Db::name("goods_menu")->field("menu_name")->where("id=".$goods[$i]["menu_id"])->find();
                $goods[$i]["menu"]=$menu["menu_name"];
                $goods[$i]["month_num"]=$this->Month_num($goods[$i]["id"]);
            }
        }else{
            if($condition==10){
                $goods=Db::name("goods")->where("shop_id=".$shopid." and menu_id=".$menuid)->order("id desc")->select();
            }else if ($condition==0 || $condition==1){
                $goods=Db::name("goods")->where("shop_id=".$shopid." and menu_id=".$menuid." and is_lower=".$condition)->order("id desc")->select();
            }else if ($condition==2){
                $goods=Db::name("goods")->where("shop_id=".$shopid." and menu_id=".$menuid." and stock=0")->order("id desc")->select();
            }
            //查询商品分类
            for ($i=0;$i<count($goods);$i++){
                $menu=Db::name("goods_menu")->field("menu_name")->where("id=".$goods[$i]["menu_id"])->find();
                $goods[$i]["menu"]=$menu["menu_name"];
                $goods[$i]["month_num"]=$this->Month_num($goods[$i]["id"]);
            }
        }
        return $goods;

    }

    /**
     * 查询月售量
     */
    public function Month_num($goodsid)
    {
        $num = Db::name('order_goods')->where('goods_id='.$goodsid)
            ->whereTime('add_at','month')->sum('num');
        $month_num = $num?:0;
        return $month_num;
    }

    /**
     *  商品上下架
     */
    public function isLower(Request $request)
    {
        $goodsid=$request->param("goodsid");
        $result=Db::name("goods")->where("id=".$goodsid)->find();
            if ($result["is_lower"]==0){
                $yeslower=db("goods")->where("id=".$goodsid)->update(["is_lower"=>1]);
                if($yeslower){
                    return "<script>history.back(-1);</script>";
                }else{
                    return "<script>alert('商品下架成功');history.back(-1);</script>";
                }
            }else{
                $yeslower=db("goods")->where("id=".$goodsid)->update(["is_lower"=>0]);
                if($yeslower){
                    return "<script>history.back(-1);</script>";
                }else{
                    return "<script>alert('商品上架成功');history.back(-1);</script>";
                }
            }
    }

    /**
     * 添加商品
     */
    public function addGoods(Request $request)
    {
        $shopid=$request->param("shopid");
        if($request->isPost()){
            $goods=[
                'shop_id'=>$shopid,
                'menu_id'=>$_POST["menu_id"],
                'goods_name'=>$_POST["goods_name"],
                'describe'=>$_POST["describe"],
                'goods_price'=>$_POST["goods_price"],
                'lunch_box_price'=>$_POST["lunch_box_price"],
                'label_one'=>$_POST["label_one"],
                'label_two'=>$_POST["label_two"],
                'label_three'=>$_POST["label_three"],
                'stock'=>$_POST["stock"],
                'add_time'=>time(),
            ];
            /*$norms=[
                'norms'=>$_POST["norms"],
                'price'=>$_POST["price"],
                'stock'=>$_POST["stock"],
                'lunch_box_price'=>$_POST["lunch_box_price"],
                'upc_code'=>$_POST["upc_code"],
                'sku_code'=>$_POST["sku_code"],
                'position_code'=>$_POST["position_code"],
            ];*/
            //添加到商品表
            $resultG=Db::name("goods")->insert($goods);
            //$resultN=Db::name("goods_norms")->insert($norms);
            if($resultG){
                return "<script>alert('商品添加成功');history.back(-1);</script>";
            }else{
                return "<script>alert('商品添加失败');history.back(-1);</script>";
            }
        }else{
            //查询商品分类
            $menu=Db::name("goods_menu")->field("id,menu_name")->where("shop_id=".$shopid)->select();
            $this->assign("menu",$menu);
            $this->assign("shopid",$shopid);
        }

        return $this->fetch('Add_goods');
    }


    /**
     * 编辑商品
     */
    public function updGoods(Request $request)
    {
        $goodsid=$request->param("goodsid");
        if ($request->isPost()){
            $arrG=[
                'menu_id'=>$_POST["menu_id"],
                'goods_name'=>$_POST["goods_name"],
                'describe'=>$_POST["describe"],
                'goods_price'=>$_POST["goods_price"],
                'lunch_box_price'=>$_POST["lunch_box_price"],
                'stock'=>$_POST["stock"],
                'label_one'=>$_POST["label_one"],
                'label_two'=>$_POST["label_two"],
                'label_three'=>$_POST["label_three"],
            ];

            $resultG=Db::name("goods")->where("id=".$goodsid)->update($arrG);
            if($resultG){
                return "<script>alert('商品修改成功');history.back(-1);</script>";
            }else{
                return "<script>alert('商品修改失败');history.back(-1);</script>";
            }
        }else{
            //查询商品详情
            $goods=Db::name("goods")->where("id=".$goodsid)->find();
            //查询商品规则
            $norms=Db::name("goods_norms")->where("goods_id=".$goodsid)->select();
            //查询菜单
            $menu=Db::name("goods_menu")->field("id,menu_name")->where("shop_id=".$goods["shop_id"])->select();
            $this->assign("goods",$goods);
            $this->assign("norms",$norms);
            $this->assign("menu",$menu);

        }
        return $this->fetch('Upd_goods');
    }

    /**
     * 删除商品
     */
    public function delGoods(Request $request)
    {
        $goodsid=$request->param("goodsid");
        $result=Db::name("goods")->where("id=".$goodsid)->delete();
        if($result){
            return "<script>history.back(-1);</script>";
        }else{
            return "<script>alert('商品修改失败');history.back(-1);</script>";
        }

    }

}