<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/22
 * Time: 10:43
 */

namespace app\userapi\model;


class ShopAddress extends Admin
{

    /**
     *  获取 地区 一定范围内的商家信息
     * @param $sheng
     * @param $shi
     * @param $qu
     * @param $rounds
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDistenceRangeShop($sheng,$shi,$qu,$rounds,$tp)
    {
        $op = $tp===0?'neq':'eq';
        $list = $this->where('sheng',$sheng)->where('shi',$shi)->where('qu',$qu)
        ->where('longitude','between',[$rounds['minlt'],$rounds['maxlt']])->where('shop_type',$op,$tp)
            ->where('dimension','between',[$rounds['minwt'],$rounds['maxwt']])->order('id')->select();
        return $list;
    }



}