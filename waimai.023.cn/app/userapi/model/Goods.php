<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/26
 * Time: 14:57
 */

namespace app\userapi\model;


use think\Db;

class Goods extends Admin
{
    public function getOnSaleAttr($val,$data)
    {
        $info = Db::name('shop_activity')->where('type',2)->where('shop_id',$data['shop_id'])
            ->where('goods_id',$data['id'])->find();
        if (empty($info)){
            return 1;
        }else{
            return 2;
        }
    }

    public function getOnSalePriceAttr($val,$data)
    {
        $info = Db::name('shop_activity')->where('type',2)->where('shop_id',$data['shop_id'])
            ->where('goods_id',$data['id'])->find();
        if (empty($info)){
            return $data['goods_price'];
        }else{
            return bcmul($info['give_money'],$data['goods_price'],2);
        }
    }

    public function getOnSaleNumAttr($val,$data)
    {
        $info = Db::name('shop_activity')->where('type',2)->where('shop_id',$data['shop_id'])
            ->where('goods_id',$data['id'])->find();
        if (empty($info)){
            return 0;
        }else{
            return $info['num'];
        }
    }

    public function getNumAttr($val,$data)
    {
        if ($val === null){
            $val = 0;
        }
        return $val;
    }

    public function shop()
    {
        return $this->belongsTo('Shop');
    }

    public function shopAddress()
    {
        return $this->hasOne('ShopAddress','shopid','shop_id');
    }

    /**
     * 获取商品列表
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function shopGoodsList($data, $op = 'shop_id')
    {
        $list = [];
        $orgods = new OrderGoods();
        $info = $this->alias('g')->field('g.*,sc.num')->order('g.sort','desc')
            ->where('g.is_lower',0)
            ->join('__SHOP_CART__ sc','sc.goods_id=g.id','LEFT')
            ->select(function ($query)use ($data){
            if (is_numeric($data)){
                $query->where('g.shop_id',$data);
            }elseif (is_array($data)){
                $keys = implode('',array_keys($data));
                if (is_numeric($keys)){
                    $query->whereIn('g.shop_id',$data);
                }elseif ($keys == 'shop_idcon') {
                    $query->whereIn('g.shop_id',$data['shop_id'])->whereLike('g.goods_name', '%' . $data['con'] . '%');
                }
            }
        });
        foreach ($info as $value){
            $value['is_on_sale'] = $value->on_sale; //是否是打折商品
            $value['on_sale_price'] = $value->on_sale_price; //打折后的价格  没有打折就显示原价格
            $value['on_sale_num'] = $value->on_sale_num;
            $num = Db::name('order_goods')->where('goods_id',$value['id'])
                ->whereTime('add_at','month')->sum('num');
            $value['month_num'] = $num?:0;
            $list[$value[$op]][] = $value->toArray();
        }
        return $list;
    }

}