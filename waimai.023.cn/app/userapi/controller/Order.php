<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/2
 * Time: 11:35
 */

namespace app\userapi\controller;


use app\userapi\model\ShopCart;
use app\userapi\model\ShopDistribution;
use app\userapi\validate\OrderInfo;
use think\Db;
use think\Loader;
use think\Request;

class Order extends Api
{
    /**
     * 确认订单
     */
    public function createOrderList(Request $request)
    {
        if ($request->isPost()){
            $vali = new OrderInfo();
            $Shop = new \app\userapi\model\Shop();
            $dis_model = new ShopDistribution();
            $data['uid'] = $request->post('uid'); //用户id
            $data['sid'] = $request->post('sid'); //商家id
            $data['tid'] = $request->post('tid/a',[]); //购物车id集合
            $data['lt'] = $request->post('lt'); //用户经度
            $data['wt'] = $request->post('wt'); //用户维度
            if (!$vali->scene('create_order')->check($data)){
                return message('',$vali->getError(),1);
            }
            try{
                $oney = $dis_model->indexList($data['sid']); //电子围栏
                if (empty($oney[$data['sid']])){
                    return message('','非法商家',3);
                }
                $true = isArea($data['lt'],$data['wt'],json_decode($oney[$data['sid']]['point_set'],true));
                if (!$true){
                    return message('','您不在商家的配送范围',3);
                }
                if (empty($data['tid'])){
                    $sc_where = ['uid'=>$data['uid'],'shop_id'=>$data['sid']];
                }else{
                    $sc_where = ['uid'=>$data['uid'],'shop_id'=>$data['sid'],'id'=>['IN',$data['tid']]];
                }

                //获取购物车中的信息
                $cart_info = ShopCart::all($sc_where); //购物车信息
                if (empty($cart_info)){
                    return message('','没有相关购物车信息',3);
                }
//                halt($cart_info);
                $shop_info = $Shop::get($data['sid']);  //商家信息

                $user_address = Db::name('address')->where('user_id',$data['uid']) //默认地址
                    ->order('isdefault desc')->find();

                $price_num = $sum = $price_sum =  0; //初始化

                foreach ($cart_info as $value){
                    $value['goods_name'] = $value->getGoodsInfo->goods_name;
                    $value['goods_img'] = $value->getGoodsInfo->goods_img;
                    $price_num = $price_num+$value->getGoodsInfo->lunch_box_price;
                    $sum = $sum + $value['money'];
                    $price_sum = bcadd($price_sum,(bcmul($value['num'],$value['price'],2)),2);
                    unset($value['getGoodsInfo']);
                    $cart_info_list [] = $value;
                }
                //生成预定订单
                $order['address'] = $user_address;
                $order['shop_name'] = $shop_info['shop_name'];         //店铺名字
                $order['shop_img'] = $shop_info['logo'];               //店铺头像
                $order['discount'] = bcsub($sum,$price_sum,2);    //优惠金额
                $order['lunch_box_fee'] = $price_num;                  //餐盒费
                $order['distribution_fee'] = $oney[$data['sid']]['dis_money']; //基础配送费
                $order['total_fee'] = $sum;                            //总金额
                $order['goods_list'] = $cart_info_list;

                if (empty($order['discount'])){
                    $order['shop_activity'] = $Shop->getOnSaleList($data['sid']); //活动信息
                }else{
                    $order['shop_activity'] = $Shop->getOnSaleList($data['sid']); //活动信息
                }
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }

            return message($order,'获取成功',2);
        }
    }


    /**
     * 订单列表
     * @param Request $request
     * @return mixed
     */
    public function orderListAll(Request $request)
    {
        $model = new \app\userapi\model\Order();
        if ($request->isGet()){
            $data['id'] = $request->get('id'); //用户id
            $data['tp'] = $request->get('tp',0); //
            try{
                $list_info = $model::all(function ($query)use($data){
                    switch ($data['tp']){
                        case 1:  //待评价
                            $query->where('status',6);
                            break;
                        case 2:  //退款
                            $query->where('status','IN',[8,9]);
                            break;
                        default:
                            $query->where('user_id',$data['id']);
                            break;
                    }
                    $query->order(['id'=>'desc']);
                });
                foreach ($list_info as $value){
                    $goods_info = $model->getGoodsInfo()->where('order_id',$value['id'])->select();
                    $value['goods_info'] = $goods_info;
                    $value['goods_num'] = $model->getGoodsInfo()->where('order_id',$value['id'])->sum('num');
                    $value['goods_name'] = Db::name('goods')
                        ->where('id',$goods_info[0]['goods_id'])->value('goods_name');
                    $list[] = $value->toArray();
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

    /**
     * 退款进度
     * @param Request $request
     * @return mixed
     */
    public function getOutOrderContent(Request $request)
    {
        $model = Loader::model('ApplyRefund');
        if ($request->isGet()){
            $id = $request->get('id');
            $oid = $request->get('oid');
            try{
                $info = $model::get(['order_id'=>$oid]);
                $info->orderInfo->shop_id;
                $info['shop_name'] = Db::name('shop')->where('id',$info->orderInfo->shop_id)
                    ->value('shop_name');
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

}