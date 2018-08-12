<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 15:48
 */

namespace app\userapi\model;


use auth\redis\Redis;
use think\Db;
use think\Lang;
use think\Loader;

class Shop extends Admin
{
    public function inlist($ids,$true = false,$id='')
    {
        if (empty($ids)){
            return [];
        }else{
            $select = $this->where('id','IN',$ids)->select();
            $arrid = array_unique($ids);
            //获取
            $minutes = Loader::model('Order')->shopMinute($ids);
            $dion = Loader::model('ShopDistribution')->indexList($ids);
            foreach ($arrid as $k=>$value){
                foreach ($select as $val){
                    if ($value == $val['id']){
                        if ($true){
                            $val['on_sale'] = $this->getOnSaleList($value);
                        }
                        if (!empty($id)){
                            $val['distence'] = Redis::Create()->zScore('disort'.$id,$value);
                        }
                        if (empty($dion[$value['id']])){
                            $val['start_price'] = 0;
                            $val['dis_money'] = 0;
                        }else{
                            $val['start_price'] = $dion[$value['id']]['start_price'];
                            $val['dis_money'] = $dion[$value['id']]['dis_money'];
                        }
                        if (!empty($minutes[$value])){
                            $val['minute'] = $minutes[$value];
                        }else{
                            // 默认30分钟
                            $val['minute'] = 30;
                        }
                        $list[$k] = $val->toArray();
                    }
                }
            }
            return $list;
        }
    }

    /**
     * 根据商家id 获取  商家 优惠
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOnSaleList($id)
    {
        $list = Db::name('shop_activity')->where('shop_id',$id)->select();
        $type_list = Db::name('shop_activity_type')->select();
        $name = '';
        $tree = [];
        foreach ($type_list as $val){
            if (!empty($list)){
                foreach ($list as $value){
                    if ($val['id'] == $value['type']){
                        switch ($value['type']){
                            case 1:
                                $name['dis_class']  = 'discoun';
                                $name['dis_jian']   = '减';
                                if (empty($name['dis_con'])){
                                    $name['dis_con'] = '';
                                }
                                $name['dis_con']   .= ' 满'.$value['money'].'减'.$value['give_money'];
                                break;
                            case 2:
                                $name['dis_class']  = 'song';
                                $name['dis_jian']   = '送';
                                $name['dis_con']    = '折扣商品'.$value['give_money'].'折';
                                break;
                            default:
                                $name = '';
                                break;
                        }
                    }
                }
            }
            if (!empty($name)){
                $tree[] = $name;
            }
        }

        return $tree;
    }

    /**
     * 首页商家列表
     * @param $data
     * @param string $order
     * @param bool $true
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function shopNumberList($data,$order = '',$true = true)
    {
        $list = [];
        $op = $data['tp']?'eq':'neq';
        $order = $order?:'id';
        $address_where = ['sheng'=>$data['sheng'],'shi'=>$data['shi'],'qu'=>$data['qu']];
        $shop_list = $this->hasWhere('shopAddressInfo',$address_where)
            ->where('shop_type',$op,$data['tp'])->order($order)
            ->select(function ($query)use ($data,$true){
            if ($true){
                $query->page($data['pa'],$data['ge']);
            }else{
                $query;
            }
        });

        if (!empty($shop_list) && is_array($shop_list)) {
            $dion = Loader::model('ShopDistribution')->indexList($data);
            foreach ($shop_list as $value) {
                $value['on_sale']  = $this->getOnSaleList($value['id']);
                $value['distence'] = GetDistances($data['lt'], $data['wt'],
                        $value->shopAddressInfo->longitude,
                        $value->shopAddressInfo->dimension);
                if (empty($dion[$value['id']])){
                    $value['start_price'] = 0;
                    $value['dis_money']   = 0;
                }else{
                    $value['start_price'] = $dion[$value['id']]['start_price'];
                    $value['dis_money']   = $dion[$value['id']]['dis_money'];
                }
                $list[] = $value->toArray();
            }
        }
        return $list;
    }

    /**
     * 店铺信息
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function shopContent($id)
    {
        $shop_dis = new ShopDistribution();
        $info = $this->where('id',$id)->find();
        $dis_info = $shop_dis->indexList($id);
        $minutes = Loader::model('Order')->shopMinute($id);
        $info['shop_photo'] = Db::name('shop_photo')->where('shop_id',$id)->select()?:[];
        $info['address'] = Db::name('shop_address')->where('shopid',$id)->find();
        $info['sale'] = $this->getOnSaleList($id);
        $info['start_price'] = isset($dis_info[$id])?$dis_info[$id]['start_price']:0;
        $info['dis_money'] = isset($dis_info[$id])?$dis_info[$id]['dis_money']:0;
        $info['minute'] = isset($minutes[$id])?$minutes[$id]:30;
        $arr= Db::name('shop_distribution')->field("concat_ws('-',open,close) open_times")
            ->where('shop_id',$id)
            ->select();
        $str = '';
        if (!empty($arr)) {
            foreach ($arr as $value) {
                $arrs[] = $value['open_times'];
            }
            $str = implode(',',$arrs);
        }
        $info['open_time'] = $str;
        return $info;
    }

    /**
     * 店铺地址
     * @return \think\model\relation\HasOne
     */
    public function shopAddressInfo()
    {
        return $this->hasOne('ShopAddress','shopid','id');
    }

    public function shopDistribution()
    {
        return $this->hasMany('ShopDistribution','shop_id','id');
    }


    /**
     * 搜索商家 或 商品
     * @param $where_ad
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchShopList($where_ad,$data)
    {
        $info_list = [];
        $GOODS = new Goods();
        $shop_id_arr = Goods::hasWhere('shopAddress',$where_ad)
            ->whereLike('goods_name','%'.$data['search_con'].'%')
            ->column('shop_id');
        if (is_array($shop_id_arr) && !empty($shop_id_arr)){
            $shop_id_arr = array_values(array_unique($shop_id_arr));
        }
        $shop_info = Shop::hasWhere('shopAddressInfo',$where_ad)
            ->page($data['pa'],$data['ge'])
            ->select(function ($query)use ($shop_id_arr,$data){
                if (is_array($shop_id_arr) && !empty($shop_id_arr)){
                    $query->whereIn('Shop.id',$shop_id_arr)
                        ->whereLike('shop_name','%'.$data['search_con'].'%','OR');
                }else{
                    $query->whereLike('shop_name','%'.$data['search_con'].'%');
                }
            });
        if (!empty($shop_info)) {
            $_goods_list = $GOODS->whereCopy('goods_name',[['LIKE','%' . $data['search_con'] . '%']])
                ->shopGoodsList($shop_id_arr);
            foreach ($shop_info as $value) {
                $value['on_sale']  = $this->getOnSaleList($value['id']);
                $value['goods_list'] = isset($_goods_list[$value['id']])?$_goods_list[$value['id']]:[];
                $info_list[] = $value->toArray();
            }
        }else{
            $list = $this->userLikeList($data);
            halt($list);
        }
        return $info_list;
    }

    //猜你喜欢
    public function userLikeList($data,$type = 1)
    {
        $data['tp'] = 0;
        $list = $this->shopNumberList($data,['id','sales_volume'=>'desc','num'=>'desc']);
        return $list;
    }

}