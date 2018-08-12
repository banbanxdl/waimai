<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4
 * Time: 15:27
 */

namespace app\userapi\model;


use think\Db;

class ShopCart extends Admin
{
    public function getGoodsInfo()
    {
        return $this->hasOne('Goods','id','goods_id');
    }

    public function getGoodsNameAttr($val,$data)
    {
        $name=Db::name('goods')->where('id',$data['goods_id'])->value('goods_name');
        return $name;
    }


    /**
     * 获取商品列表
     * @param $uid 用户id
     * @param string $data 参数
     * @param string $op key值
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getShopCartList($uid,$data='',$op = 'shop_id')
    {
        $list = [];
        $info = $this->where('uid',$uid)
            ->select(function ($query)use ($data){
                if (is_numeric($data)){
                    $this->where('shop_id',$data);
                }elseif (is_array($data)){
                    $keys = implode('',array_keys($data));
                    if (is_numeric($keys)){
                        $query->whereIn('id',$data);
                    }
                }
            });
        foreach ($info as $value){
            $value['goods_name'] = $value->goods_name;
            $list[$value[$op]][] = $value->toArray();
        }
        return $list;
    }

    /**
     * @param $uid
     * @param $data
     * @param string $op
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function shopList($uid,$data,$op='shop_id')
    {
        $shop_dis = new ShopDistribution();
        $s = $this->where('uid',$uid)->column($op);
        if (!empty($data)){
            if (is_numeric($data)){
                $s = $data;
            }
        }
        $list = [];
        $info = Shop::all($s);
        $goods = $this->getShopCartList($uid,$data,$op);
        $dis_info = $shop_dis->indexList($s);
        foreach ($info as $val){
            $arr['shop_id'] = $val['id'];
            $arr['shop_name'] = $val['shop_name'];
            $arr['address'] = isset($val->shopAddressInfo)?$val->shopAddressInfo->shop_address:'';
            $arr['atv'] = '';
            $arr['dis_money'] = isset($dis_info[$val['id']]['dis_money'])?$dis_info[$val['id']]['dis_money']:0;
            $a=$this->where('uid',$uid)->where('shop_id',$val['id'])->sum('avt_id');
            if ($a === 0){
                $sum = $this->where('uid',$uid)->where('shop_id',$val['id'])->sum('money');
                $find = Db::name('shop_activity')->where('type',1)->where('shop_id',$val['id'])
                    ->where('money','ELT',$sum)
                    ->order('money desc')->find();
                if (!empty($find)){
                    $arr['atv'] = '满'.$find['money'].'减'.$find['give_money'];
                }
            }
            $arr['goods_list'] = isset($goods[$val['id']])?$goods[$val['id']]:[];
            if (isset($goods[$val['id']])){
                $arr['box_money'] = get_Sum($goods[$val['id']],'box_money');
                $arr['sum'] = get_Sum($goods[$val['id']],'money');
            }
            if (!empty($data)){
                if (is_numeric($data)){
                    if ($val['id'] == $data){
                        $list = $arr;
                    }
                }
            }else{
                $list[] = $arr;
            }
        }

        return $list;
    }

}