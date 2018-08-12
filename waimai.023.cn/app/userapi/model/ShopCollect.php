<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 15:40
 */

namespace app\userapi\model;


use think\Db;

class ShopCollect extends Admin
{
    public function getModelList($id)
    {
        $Sdstr = new ShopDistribution();
        if (empty($id)){
            return [];
        }else{
            $shops_id = $this->where('uid',$id)->select();
            if (empty($shops_id)){
                return [];
            }else{
                foreach ($shops_id as $value){
                    $info = Shop::get($value['shop_id'])->toArray();
                    $adr = ShopAddress::get(['shopid'=>$value['shop_id']])->toArray();
                    $dist = $Sdstr->indexList($value['shop_id']);
                    $val['id'] = $value['id'];
                    $val['shop_id'] = $info['id'];
                    $val['shop_name'] = $info['shop_name'].'('.(isset($adr['shop_address'])?$adr['shop_address']:'').')';
                    $val['shop_logo'] = $info['logo'];
                    $val['shop_star'] = $info['num'];
                    $val['shop_price'] = isset($dist[$value['shop_id']]['start_price'])?$dist[$value['shop_id']]['start_price']:0;
                    $val['shop_disb'] = $dist[$value['shop_id']]['dis_money']; // todo 配送费
                    $list[] = $val;
                }
                return $list;
            }
        }
    }

}