<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/11
 * Time: 18:30
 */

namespace app\userapi\model;


use think\Db;

class ShopDistribution extends Admin
{
    /**
     * 获取单店铺  或 多店铺的 电子围栏
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function indexList($data)
    {
        $distion = Db::name('shop_distribution')->alias('sd')
            ->join('__SHOP_ADDRESS__ sa','sa.shopid=sd.shop_id')
            ->select(function ($query)use ($data){
                if (is_array($data)){
                    $keys = implode('',array_keys($data));
                    if (is_numeric($keys)){
                        $query->whereIn('sd.shop_id',$data);
                    }else {
                        $query->where('sa.sheng', $data['sheng'])->where('sa.shi', $data['shi'])
                            ->where('sa.qu', $data['qu']);
                    }
                }elseif (is_numeric($data)){
                    $query->where('sd.shop_id',$data);
                }
            });

        foreach ($distion as $val){
            if (strtotime($val['open']) < time() && time() < strtotime($val['close'])){
                $arrtion[$val['shop_id']] = $val;
            }else{
                if (empty($arrtion[$val['shop_id']])){
                    $arrtion[$val['shop_id']] = [];
                }
            }
        }
        return $arrtion;
    }

    public function address()
    {
        return $this->hasOne('ShopAddress','shopid','shop_id');
    }

}