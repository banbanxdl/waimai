<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 11:01
 */

namespace app\userapi\model;


use think\Db;

class OrderComment extends Admin
{
    public function getShopNameAttr($val,$data)
    {
        $name = Db::name('shop')->where('id',$data['sid'])->value('shop_name');
        $adrs = Db::name('shop_address')->where('shopid',$data['sid'])->value('shop_address');
        if (empty($name)){
            return '';
        }else{
            return $name.'('.($adrs?:'').')';
        }
    }
    public function getShopImgAttr($val,$data)
    {
        $value = Db::name('shop')->where('id',$data['sid'])->value('logo');
        if (empty($value)){
            return '';
        }else{
            return $value;
        }
    }

    public function getGoodsImgAttr($val,$data)
    {
        $goods_id = Db::name('order_goods')->where('order_id',$data['order_id'])->column('goods_id');
        $goods_img = Db::name('goods')->where('id',$goods_id[0])->value('goods_img');
        return $goods_img;
    }
    public function getGoodsNameAttr($val,$data)
    {
        $goods_id = Db::name('order_goods')->where('order_id',$data['order_id'])->column('goods_id');
        $goods_num = Db::name('order_goods')->where('order_id',$data['order_id'])->column('num');
        $goods_name = Db::name('goods')->where('id','IN',$goods_id)->column('goods_name');
        $name = '';
        foreach ($goods_name as  $k=>$value){
            $name .= ' '.$value.'x'.$goods_num[$k];
        }
        return $name;
    }

    public function getTimeNumAttr($val,$data)
    {
        $info = Db::name('order')->where('id',$data['order_id'])->find();
        $time = $info['delivery_time'] - $info['add_time'];
        $minute = $time/60;
        return $minute;
    }

    public function getRevertAttr($val,$data)
    {
        return Db::name('reply')->where('reply_id',$data['id'])->value('reply_content');
    }

    public function userInfo()
    {
        return $this->hasOne('User','id','uid');
    }

    //店铺评论统计
    public function shopTalkCount($sid)
    {
        $data['uta'] = Db::name('order_comment')->where('sid',$sid)->count();
        $data['str'] = Db::name('order_comment')->where('sid',$sid)->avg('num');
        $data['rct'] = Db::name('rider_evaluate')->where('shop_id',$sid)->where('stars',"EGT",3)->count();
        $nums = Db::name('order')->field('(delivery_time-add_time) times')
            ->where('status',6)->where('shop_id',$sid)->select();
        $tms = 30;
        if (!empty($nums)) {
            foreach ($nums as $value) {
                $arr[] = $value['times'];
            }
            $tms = bcdiv((array_sum($arr) / count($arr)), 60);
        }
        $data['tms'] = $tms;
        return $data;
    }


}