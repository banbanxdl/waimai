<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/26
 * Time: 14:17
 */

namespace app\userapi\controller;

use app\common\controller\ApiCommon;
use app\userapi\model\Goods;
use app\userapi\model\GoodsMenu;
use app\userapi\model\OrderComment;
use app\userapi\model\ShopDistribution;
use app\userapi\validate\ShopCart;
use think\Db;
use think\Loader;
use think\Request;

class Shop extends Api
{
    /**
     * 获取商家商品菜单列表
     * @param Request $request
     * @return mixed
     */
    public function getShopGoodsMenuList(Request $request)
    {
        $model = Loader::model('GoodsMenu');
        $tree = [];
        if ($request->isGet()){
            $shop_id = $request->get('id');
            try{
                $list = collection($model->where('shop_id',$shop_id)->order(['is_top'=>'desc','id'])->select())->toArray();
                if (!empty($list)) {
                    $tree = ApiCommon::temp()->arrayTreeList($list);
                }
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($tree)){
                return message('','获取失败',3);
            }else{
                return message($tree,'获取成功',2);
            }
        }
    }


    /**
     * 根据店铺id 菜单id 获取商品列表
     * @param Request $request
     * @return mixed
     */
    public function getShopGoodsList(Request $request)
    {
        $model = Loader::model('Goods');
        $tree = [];
        if ($request->isGet()){
            $id = $request->get('id');
            $goods_menu_id = $request->get('mg');
            try{
                $goods_list = $model->where('shop_id',$id)->where('menu_id',$goods_menu_id)->select();
                foreach ($goods_list as $value){
                    $value['is_on_sale'] = $value->on_sale; //是否是打折商品
                    $value['on_sale_price'] = $value->on_sale_price; //打折后的价格  没有打折就显示原价格
                    $value['on_sale_num'] = $value->on_sale_num;
                    $tree[] = $value->toArray();
                }
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($tree)){
                return message('','获取失败',3);
            }else{
                return message($tree,'获取成功',2);
            }

        }
    }


    /**
     * 购物车 操作 清空  添加  修改
     * @param Request $request
     * @return mixed
     */
    public function shopCart(Request $request)
    {
        if ($request->isPost()){
            $data['uid'] = $request->post('uid');
            $data['shop_id'] = $request->post('shop_id');
            $data['goods_id'] = $request->post('goods_id');
            $id = $request->post('id/a');
            $is_null = $request->post('null',0);
            try{
                $number = Db::name('shop_cart')->where('uid', $data['uid'])
                    ->where('shop_id', $data['shop_id'])
                    ->where('goods_id', $data['goods_id'])->find();
                if (!empty($number['num']) && $is_null == 1){
                    $is_null = 2;
                }

                $goods_price = Db::name('goods')->where('id',$data['goods_id'])->value('goods_price');
                $goods_box_price = Db::name('goods')->where('id',$data['goods_id'])->value('lunch_box_price');
                if (empty($goods_price)){
                    halt('This is not goods!');
                }

                $find = Db::name('shop_activity')
                    ->where('type',2)->where('shop_id',$data['shop_id'])
                    ->where('goods_id',$data['goods_id'])->find();
                if (empty($find)){
                    $data['money'] = $goods_price; //原价
                }else{
                    if (!empty($number['num']) && $number['num'] != $find['num']){
                        $data['money'] = bcmul($find['give_money'],$find['money'],2); //打折价
                    }else{
                        $data['money'] = $find['money']; //查询不到 用原价
                    }
                    $data['avt_id'] = $find['id'];
                }

                $shop_cart = Db::name('shop_cart')->where('uid', $data['uid'])
                    ->where('shop_id', $data['shop_id'])
                    ->where('goods_id', $data['goods_id']);

                switch ($is_null){
                    case 1: //添加
                        $data['num'] = 1;
                        $data['price'] = $goods_price;
                        $data['box_money'] = $goods_box_price;
                        $num = Db::name('shop_cart')->insert($data);
                        break;
                    case 2:  //加
                        $n = $shop_cart->setInc('num',1);
                        $num = Db::name('shop_cart')->where('uid', $data['uid'])
                            ->where('shop_id', $data['shop_id'])
                            ->where('goods_id', $data['goods_id'])->setInc('money',$data['money']);
                        break;
                    case 3:  //减
                        if ($number['num'] == 1){
                            $num = $shop_cart->delete();
                        }else{
                            $n = $shop_cart->setDec('num');
                            $num = Db::name('shop_cart')->where('uid', $data['uid'])
                                ->where('shop_id', $data['shop_id'])
                                ->where('goods_id', $data['goods_id'])->setDec('money',$data['money']);
                        }
                        break;
                    default: // 清空
                        if (is_array($id) && !empty($id)){
                            $num = Db::name('shop_cart')->delete($id);
                        }else{
                            $num = Db::name('shop_cart')->where('uid',$data['uid'])
                                ->where('shop_id',$data['shop_id'])->delete(true);
                        }
                        break;
                }
                $sum = Db::name('shop_cart')->where('uid', $data['uid'])
                    ->where('shop_id', $data['shop_id'])->sum('money');
                $dis_model = new ShopDistribution();
                $dion = $dis_model->indexList($data['shop_id']);
                $dis_money = isset($dion[$data['shop_id']])?$dion[$data['shop_id']]['dis_money']:0;
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($num)){
                return message('','操作失败',3);
            }else{
                return message([$sum,$dis_money],'操作成功',2);
            }
        }else{
            return message('','非法请求',4);
        }
    }

    /**
     * 购物车列表
     * @param Request $request
     * @return mixed
     */
    public function shopCartList(Request $request)
    {
        $vali = new ShopCart();
        $model = new \app\userapi\model\ShopCart();
        if ($request->isGet()){
            $data['uid'] = $request->get('uid');
            $data['shop_id'] = $request->get('shop_id',null);
            if (!$vali->scene('list')->check($data)){
                return message('',$vali->getError(),3);
            }
            try{
                $info = $model->shopList($data['uid'],$data['shop_id'],'shop_id');
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }

            if (empty($info)){
                return message('','没有商品',3);
            }else{
                return message($info,'获取成功',2);
            }
        }
    }


    /**
     * 店铺信息
     * @param Request $request
     * @return mixed
     */
    public function shopInfo(Request $request)
    {
        $model = new \app\userapi\model\Shop();
        if ($request->isGet()){
            $id = $request->get('id');
            try{
                $info = $model->shopContent($id);
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($info)){
                return message('','获取失败',3);
            }else{
                return message($info,'获取成功',2);
            }
        }
    }

    /**
     * 参数 经度 维度 省 市 区 搜索内容
     * @param Request $request
     * @return mixed
     */
    public function searchShopList(Request $request)
    {
        $Shop = new \app\userapi\model\Shop();
        $vali = new \app\userapi\validate\Shop();
        if ($request->isPost()){
            $data['sheng'] = $request->post('sheng');
            $data['shi'] = $request->post('shi');
            $data['qu'] = $request->post('qu');
            $data['search_con'] = $request->post('search_con');
            $data['id'] = $request->post('id');
            $data['pa'] = $request->post('pa',1);
            $data['ge'] = $request->post('ge',10);
            if (!$vali->scene('search')->check($data)){
                return message('',$vali->getError(),1);
            }
            try{
                $where_ad = ['sheng'=>$data['sheng'],'shi'=>$data['shi'],'qu'=>$data['qu']];
                $info_list = $Shop->searchShopList($where_ad,$data);
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }

            if (empty($info_list)){
                return message('','没有结果',3);
            }else{
                return message($info_list,'搜索成功',2);
            }

        }
    }

    /**
     * 首页 四最
     * @param Request $request
     * @return mixed
     */
    public function recommendShopList(Request $request)
    {
        $model = new \app\userapi\model\Order();
        $vali = new \app\userapi\validate\Shop();
        if ($request->isGet()){
            $data['sheng'] = $request->get('sheng');
            $data['shi'] = $request->get('shi');
            $data['qu'] = $request->get('qu');
            if (!$vali->scene('recomment')->check($data)){
                return message('',$vali->getError(),1);
            }
            try{
                $ex_sql = Db::name('order_comment')->field('id,sid,AVG(num) nums')
                    ->group('sid')->buildSql();
                $ct_sql = Db::name('order_comment')->field('id,sid,count(num) counts,num nums')
                    ->group('sid')->where('num',"EGT",3)->buildSql();
                $od_sql = Db::name('order o')->field('o.*,count(o.shop_id) shop_nums')
                    ->group('o.id')->where('o.status',6)->buildSql();
                $at_sql = Db::name('order o')->field('o.*,(o.purchase_time-o.shop_time) time_nums')
                    ->group('o.id')->where('o.status',6)->buildSql();
                //评分最高
                $list[0] = $model->getBestNumShopList($ex_sql,$data,'sid','nums desc');
                $list[0]['suibian'] = '评分最高';
                //好评最多
                $list[1] = $model->getBestNumShopList($ct_sql,$data,'sid',['counts'=>'desc','nums'=>'desc']);
                $list[1]['suibian'] = '好评最多';
                //销量最高
                $list[2] = $model->getBestNumShopList($od_sql,$data,'shop_id',['shop_nums'=>'desc']);
                $list[2]['suibian'] = '销量最高';
                //速度最快
                $list[3] = $model->getBestNumShopList($at_sql,$data,'shop_id','time_nums');
                $list[3]['suibian'] = '速度最快';
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($list)){
                return message('','获取失败',3);
            }else{
                return message($list,'获取成功',2);
            }
        }
    }

    /**
     * 店铺菜单列表
     * @param Request $request
     * @return mixed
     */
    public function shopTypeList(Request $request)
    {
        if ($request->isGet()){
            $Goods = new Goods();
            $list = [];
            $sid = $request->get('id/d');
            if (empty($sid)){
                return message('','店铺id不能为空',1);
            }
            try{
                $menu_list = Db::name('goods_menu')->where('shop_id',$sid)->order('is_top desc')->select();
                $goods_list = $Goods->shopGoodsList($sid,'menu_id');
                if (!empty($menu_list)) {
                    foreach ($menu_list as $value) {
                        $value['goods_list'] = isset($goods_list[$value['id']])?$goods_list[$value['id']]:[];
                        $list[] = $value;
                    }
                }
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($list)){
                return message('','获取失败',3);
            }else{
                return message($list,'获取成功',2);
            }
        }
    }

    //店铺评论
    public function shopTalkCount(Request $request)
    {
        $model = new OrderComment();
        if ($request->isPost()){
            $id = $request->post('id'); // 店铺id
            if (empty($id)){
                return message('','缺少参数',1);
            }
            try{
                $info = $model->shopTalkCount($id);
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($info)){
                return message('','失败',3);
            }else{
                return message($info,'成功',2);
            }
        }
    }
}