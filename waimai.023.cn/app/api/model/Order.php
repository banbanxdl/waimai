<?php

namespace app\api\model;

use think\Model;
use think\Db;


class Order extends Model
{
    /**
     * @param 骑手订单详情
     */
    public function OrderDetails($order_id, $order_type)
    {
        if ($order_type == 1) {//这是外卖订单详情
            $order_info = Db::name('order')->where('id', $order_id)->find();//订单信息 下单的用户信息 商家信息

            //$shop_info=Db::table('db_shop')->alias('s')->join('db_shop_detailed_info sd','s.id=sd.shop_id')->where('s.id',$order_info['shop_id'])->field('s.*,sd.shop_name,sd.shop_address,sd.shop_jd,sd.shop_wd')->find();//商家信息

            $shop_info = Db::table('db_shop')->alias('s')->join('db_shop_address sa', 's.id=sa.shopid')->where('s.id', $order_info['shop_id'])->find();

            $user_info = Db::table('db_user')->alias('s')->join('db_address a', 's.id=a.user_id')->where('s.id', $order_info['user_id'])->field('s.*,a.name,a.phone as address_phone,a.address,a.building_card,a.jd,a.wd')->find();//用户信息


            //位置 用户信息 商家信息
            $list = array(
                'shop_name' => $shop_info['shop_name'],//店铺名称
                'address' => $shop_info['shop_address'],//店铺地址
                'shop_jd' => $shop_info['longitude'],//店铺经度
                'shop_wd' => $shop_info['dimension'],//店铺纬度
                'shop_phone' => $shop_info['shop_phone'],//店铺电话
                'user_name' => $user_info['name'],//用户姓名
                'user_phone' => $user_info['address_phone'],//用户电话
                'user_address' => $user_info['address'] + $user_info['building_card'],//用户地址
                'user_jd' => $user_info['jd'],//用户地址经度
                'user_wd' => $user_info['wd'],//用户地址纬度
                'order_status' => $order_info['status'],//订单状态
                'time' => $order_info['shop_time'],//派单时间
                'receipt_time' => $order_info['receipt_time'],//骑手接单时间
                'purchase_time' => $order_info['purchase_time'],//骑手取货时间
                'delivery_time' => $order_info['delivery_time'],//骑手送达时间
                'pre_meal_time' => $order_info['pre_meal_time'],//预出餐时间

            );

            //订单菜品详情
            $order_goods = Db::table('db_order_goods')->alias('og')->join('db_goods g', 'og.goods_id=g.id')->where('og.order_id', $order_id)->field('og.id,g.goods_name,og.num,og.total')->select();//购买的商品

            $peisong_fee = array(array('goods_name' => '配送费', 'num' => 0, 'total' => $order_info['distribution_fee']));

            $canhe_fee = array(array('goods_name' => '餐盒费', 'num' => 0, 'total' => $order_info['lunch_box_fee']));

            $goods_info = array();
            $goods_info = array_merge($order_goods, $peisong_fee, $canhe_fee);//订单商品信息

            //订单信息
            $list_order = array(
                'ordernum' => $order_info['ordernum'],//订单编号
                'pre_delivery_time' => $order_info['pre_delivery_time'],//期望送达时间
                'remarks' => $order_info['remarks'],//备注信息
                'invoice' => $order_info['invoice'],//发票抬头
            );


            //骑手收入详情
            $list_rider = array(
                'platform_fee' => $order_info['platform_fee'],//平台奖励
                'basics_fee' => $order_info['rider_distribution_fee'],//基础费用 配送费
                'total_fee' => $order_info['total_fee'],//总费用
            );

            if ($order_info) {
                $model = array(
                    'list' => $list,
                    'order_goods' => $goods_info,
                    'list_order' => $list_order,
                    'list_rider' => $list_rider

                );
            } else {
                $model = array();
            }


        } elseif ($order_type == 2) {//这是帮买订单详情

            $order_info = Db::name('run_order')->where('id', $order_id)->find();//订单信息

            $user_info = Db::name('user')->where('id', $order_info['user_id'])->find();//用户信息

            $user_address = Db::name('address')->where('id', $order_info['address_id'])->find();//查询用户的收货地址


            //基本信息
            $list = array(
                'goods_name' => $order_info['goods_name'],//购买的商品名称
                'purchase_method' => $order_info['purchase_method'],//购买方式 1指定地址购买 2 骑手就近购买
                'appoint_address' => $order_info['appoint_address'],//指定地址
                'appoint_address_jd' => $order_info['appoint_address_jd'],//指定地址经度
                'appoint_address_wd' => $order_info['appoint_address_wd'],//指定地址纬度
                'user_name' => $user_address['name'],//收货人姓名
                'user_phone' => $user_address['phone'],//收货人电话
                'user_sheng' => $user_address['sheng'],//地址所在省
                'user_shi' => $user_address['shi'],//地址所在市
                'user_qu' => $user_address['qu'],//地址所在区
                'user_address' => $user_address['address'] + $user_address['building_card'],//收货人详细地址
                'user_jd' => $user_address['jd'],//位置经度
                'user_wd' => $user_address['wd'],//位置纬度
                'order_status' => $order_info['status'],//订单状态
                'time' => $order_info['add_time'],//派单时间
                'receipt_time' => $order_info['single_time'],//骑手接单时间
                'purchase_time' => $order_info['purchase_time'],//骑手取货时间
                'delivery_time' => $order_info['delivery_time'],//骑手送达时间


            );

            //订单详情
            $list_goods = array(
                'goods_name' => $order_info['goods_name'],//购买的商品名称
                'goods_price' => $order_info['price'],//评估的价格
            );

            //订单信息
            $list_order = array(
                'ordernum' => $order_info['ordernum'],//订单编号
                'pre_delivery_time' => $order_info['pre_delivery_time'],//预送达时间
            );

            //骑手收入详情
            $list_rider = array(
                'tip' => $order_info['tip'],//小费
                'platform_fee' => $order_info['platform_fee'],//平台奖励
                'basics_fee' => $order_info['rider_distribution_fee'],//基础配送费
                'total_fee' => $order_info['total_fee'],//总收入
            );

            if ($order_info) {
                $model = array(
                    'list' => $list,
                    'order_goods' => $list_goods,
                    'list_order' => $list_order,
                    'list_rider' => $list_rider

                );
            } else {
                $model = array();
            }


        } elseif ($order_type == 3) {//这是专送订单详情
            $order_info = Db::name('take_order')->where('id', $order_id)->find();//订单信息

            $user_info = Db::name('user')->where('id', $order_info['user_id'])->find();//用户信息

            $user_address = Db::name('address')->where('id', $order_info['take_address_id'])->find();//取件地址id

            $give_user_address = Db::name('address')->where('id', $order_info['give_address_id'])->find();//收件地址id

            //基本信息
            $list = array(
                'talk_name' => $user_address['name'],//取件人姓名
                'talk_address' => $user_address['address'],//取件人地址
                'talk_building_card' => $user_address['building_card'],//取件人门牌号
                'talk_jd' => $user_address['jd'],
                'talk_wd' => $user_address['wd'],
                'talk_sheng' => $user_address['sheng'],
                'talk_shi' => $user_address['shi'],
                'talk_qu' => $user_address['qu'],
                'talk_phone' => $user_address['phone'],//取件人电话
                'give_name' => $give_user_address['name'],//收件人姓名
                'give_phone' => $give_user_address['phone'],//收件人电话
                'give_address' => $give_user_address['address'] + $give_user_address['building_card'],//收件地址
                'give_jd' => $give_user_address['jd'],//收件经度
                'give_wd' => $give_user_address['wd'],//收件纬度
                'give_sheng' => $give_user_address['sheng'],//收件省
                'give_shi' => $give_user_address['sheng'],//收件市
                'give_qu' => $give_user_address['qu'],//收件区
                'order_status' => $order_info['status'],//订单状态
                'time' => $order_info['pre_take_time'],//派单时间
                'receipt_time' => $order_info['single_time'],//骑手接单时间
                'purchase_time' => $order_info['purchase_time'],//骑手取货时间
                'delivery_time' => $order_info['delivery_time'],//骑手送达时间


            );

            //订单详情
            $list_goods = array(
                'goods_type' => $order_info['goods_type'],//物品类型
                'weight' => $order_info['weight'],//物品重量
                'insurance' => $order_info['insurance'],//货损险 为0时就是没有
            );

            //订单信息
            $list_order = array(
                'ordernum' => $order_info['ordernum'],
            );

            //骑手收入详情
            $list_rider = array(
                'tip' => $order_info['tip'],//小费
                'platform_fee' => $order_info['platform_fee'],//平台奖励
                'basics_fee' => $order_info['rider_distribution_fee'],//基础配送费
                'total_fee' => $order_info['total_fee'],//总费用
            );

            if ($order_info) {
                $model = array(
                    'list' => $list,
                    'order_goods' => $list_goods,
                    'list_order' => $list_order,
                    'list_rider' => $list_rider

                );
            } else {
                $model = array();
            }


        }

        return $model;
    }

    /**
     * @param 骑手取消订单
     */
    public function getCancelOrder($type_order, $rider_id, $reason, $oid, $identity)
    {

        $new['identity'] = $identity;//取消订单身份 1 外卖订单 2 跑腿订单 3帮买订单

        $new['uid'] = $rider_id;//对应id

        $new['oid'] = $oid;//订单id

        $new['oid_type'] = $type_order;//取消订单的类型

        $new['reason'] = $reason;

        $new['money'] = 0;

        $new['cancel_time'] = time();

        $result = db('cancel_order')->insert($new);

        if ($result) {
            //改变订单骑手状态
            switch ($type_order) {
                case '1':
                    Db::name('order')->where('id', $oid)->update(['rider_id' => '', 'status' => 3, 'receipt_time' => '']);
                    break;
                case '2':
                    Db::name('run_order')->where('id', $oid)->update(['rider_id' => '', 'status' => 2, 'single_time' => '']);
                    break;
                case '3':
                    Db::name('take_order')->where('id', $oid)->update(['rider_id' => '', 'status' => 2, 'single_time' => '']);
                    break;

                default:
                    # code...
                    break;
            }
        }

        return $result;
    }

    /**
     * @param  骑手取消订单返回的数据
     */
    public function getCancelOrderInfo($type_order, $oid)
    {
        switch ($type_order) {
            case '1':
                $order_info = Db::name('order')->where('id', $oid)->find();//订单信息


                $cancel_order_info = Db::name('cancel_order')->where(['oid' => $oid, 'oid_type' => 1])->find();//取消订单信息


                $shop_info = Db::table('db_shop')->alias('s')->join('db_shop_address sa', 's.id=sa.shopid')->where('s.id', $order_info['shop_id'])->find();//商家信息

                $user_info = Db::table('db_user')->alias('s')->join('db_address a', 's.id=a.user_id')->where('s.id', $order_info['user_id'])->field('s.*,a.name,a.phone as address_phone,a.address,a.building_card,a.jd,a.wd')->find();//用户信息


                //基本信息
                $cancel_order = array(
                    'reason' => $cancel_order_info['reason'],//取消原因
                    'money' => $cancel_order_info['money'],//扣款金额
                    'rob_time' => $order_info['receipt_time'],//骑手接单时间
                    'cancel_time' => $cancel_order_info['cancel_time'],//取消时间
                );


                //商家用户信息
                $info = array(
                    'shop_name' => $shop_info['shop_name'],
                    'shop_address' => $shop_info['shop_address'],
                    'shop_phone' => $shop_info['shop_phone'],
                    'shop_jd' => $shop_info['longitude'],
                    'shop_wd' => $shop_info['dimension'],
                    'user_name' => $user_info['name'],
                    'user_phone' => $user_info['address_phone'],
                    'user_address' => $user_info['address'] + $user_info['building_card'],
                    'user_jd' => $user_info['jd'],
                    'user_wd' => $user_info['wd'],
                );


                //订单菜品详情//订单详情
                $order_goods = Db::table('db_order_goods')->alias('og')->join('db_goods g', 'og.goods_id=g.id')->where('og.order_id', $oid)->field('og.id,g.goods_name,og.num,og.total')->select();//购买的商品

                $peisong_fee = array(array('goods_name' => '配送费', 'num' => 0, 'total' => $order_info['distribution_fee']));

                $canhe_fee = array(array('goods_name' => '餐盒费', 'num' => 0, 'total' => $order_info['lunch_box_fee']));

                $goods_info = array();
                $goods_info = array_merge($order_goods, $peisong_fee, $canhe_fee);//订单商品信息


                $model = array(
                    'list' => $cancel_order,
                    'info' => $info,
                    'order_goods' => $goods_info,
                );
                break;
            case '2':
                $order_info = Db::name('run_order')->where('id', $oid)->find();

                $cancel_order_info = Db::name('cancel_order')->where(['oid' => $oid, 'oid_type' => 2])->find();

                //基本信息
                $cancel_order = array(
                    'reason' => $cancel_order_info['reason'],//取消原因
                    'money' => $cancel_order_info['money'],//扣款金额
                    'rob_time' => $order_info['single_time'],//骑手接单时间
                    'cancel_time' => $cancel_order_info['cancel_time'],//取消时间
                );

                $user_info = Db::table('db_user')->alias('s')->join('db_address a', 's.id=a.user_id')->where('s.id', $order_info['user_id'])->field('s.*,a.name,a.phone as address_phone,a.address,a.building_card,a.jd,a.wd')->find();//用户信息

                //商家用户信息
                $info = array(
                    'purchase_method' => $order_info['purchase_method'],//购买方式
                    'appoint_address' => $order_info['appoint_address'],//指定地址
                    'appoint_address_jd' => $order_info['appoint_address_jd'],//经度
                    'appoint_address_wd' => $order_info['appoint_address_wd'],//纬度
                    'user_name' => $user_info['name'], //收货人姓名
                    'user_phone' => $user_info['address_phone'],//收货人电话
                    'user_address' => $user_info['address'] + $user_info['building_card'],//详细地址
                    'user_jd' => $user_info['jd'],
                    'user_wd' => $user_info['wd'],
                );

                //订单详情
                $list_goods = array(
                    'goods_name' => $order_info['goods_name'],//购买的商品名称
                    'goods_price' => $order_info['price'],//评估的价格
                    'distribution_fee' => $order_info['rider_distribution_fee'],//配送费
                    'tip' => $order_info['tip'],//小费
                );

                $model = array(
                    'list' => $cancel_order,
                    'info' => $info,
                    'order_goods' => $list_goods,
                );

                break;
            case '3':
                $order_info = Db::name('take_order')->where('id', $oid)->find();

                $cancel_order_info = Db::name('cancel_order')->where(['oid' => $oid, 'oid_type' => 3])->find();

                //基本信息
                $cancel_order = array(
                    'reason' => $cancel_order_info['reason'],//取消原因
                    'money' => $cancel_order_info['money'],//扣款金额
                    'rob_time' => $order_info['single_time'],//骑手接单时间
                    'cancel_time' => $cancel_order_info['cancel_time'],//取消时间
                );

                $take_user_info = Db::table('db_take_order')->alias('s')->join('db_address a', 's.take_address_id=a.id')->where('s.id', $oid)->field('s.*,a.name,a.phone as address_phone,a.address,a.building_card,a.jd,a.wd')->find();//用户取件地址信息

                $give_user_info = Db::table('db_take_order')->alias('s')->join('db_address a', 's.give_address_id=a.id')->where('s.id', $oid)->field('s.*,a.name,a.phone as address_phone,a.address,a.building_card,a.jd,a.wd')->find();//用户收件地址信息

                //商家用户信息
                $info = array(
                    'take_name' => $take_user_info['name'],
                    'take_phone' => $take_user_info['address_phone'],
                    'take_address' => $take_user_info['address'] + $take_user_info['building_card'],//取件地址
                    'take_jd' => $take_user_info['jd'],//取件经度
                    'take_wd' => $take_user_info['wd'],//取件纬度
                    'give_name' => $give_user_info['name'],//收件人姓名
                    'give_phone' => $give_user_info['address_phone'],//收件人电话
                    'give_address' => $give_user_info['address'] + $give_user_info['building_card'],//收件地址
                    'give_jd' => $give_user_info['jd'],//经度
                    'give_wd' => $give_user_info['wd'],//纬度
                );

                //订单详情
                $list_goods = array(
                    'goods_type' => $order_info['goods_type'],//购买的商品类型
                    'goods_weight' => $order_info['weight'],//商品重量
                    'pre_take_time' => $order_info['pre_take_time'],//用户期望取件时间
                    'distribution_fee' => $order_info['rider_distribution_fee'],//配送费
                    'tip' => $order_info['tip'],//小费
                );

                $model = array(
                    'list' => $cancel_order,
                    'info' => $info,
                    'order_goods' => $list_goods,
                );

                break;

            default:
                break;
        }

        return $model;

    }

    /**
     * @param 骑手手动接单订单池
     */
    public function getOrderList($type, $rider_id)
    {
        switch ($type) {
            case '1'://订单池待接单订单 商家店铺名称 位置

                //查询所有商家的位置
                //$shop_distance=Db::name('shop_address')->select();

                //$distance = GetRange($rider_jd,$rider_wd,5000);

                $order_info = Db::name('order')->where(['status' => 3, 'dispatch_type' => 1])->field('id,ordernum,user_id,shop_id,rider_distribution_fee,address_id,add_time,status,pre_delivery_time,is_pro_order')->select();
                foreach ($order_info as $k => $v) {
                    //查询店铺名称 地址
                    $shop_info[] = Db::table('db_shop')->alias('s')->join('shop_address sa', 's.id=sa.shopid')->where('s.id', $v['shop_id'])->field('s.id,s.shop_name,sa.shop_address,sa.longitude,sa.dimension')->find();

                    $order_info[$k]['shop_name'] = $shop_info[$k]['shop_name'];
                    $order_info[$k]['shop_address'] = $shop_info[$k]['shop_address'];
                    $order_info[$k]['longitude'] = $shop_info[$k]['longitude'];
                    $order_info[$k]['dimension'] = $shop_info[$k]['dimension'];

                    //查询用户地址
                    $user_address = $this->getUserAddress($v['address_id']);
                    $order_info[$k]['user_address'] = $user_address['sheng'] . $user_address['shi'] . $user_address['qu'] . $user_address['address'];//详细地址
                    $order_info[$k]['building_card'] = $user_address['building_card'];//楼牌号
                    $order_info[$k]['add_time'] =date('Y-m-d H:i:s',$order_info[$k]['add_time']);
                    $order_info[$k]['pre_delivery_time'] =date('Y-m-d H:i:s',$order_info[$k]['pre_delivery_time']);
                    $order_info[$k]['user_jd'] = $user_address['jd'];

                    $order_info[$k]['user_wd'] = $user_address['wd'];
                    $order_info[$k]['order_type'] = $k+1;//订单类型 1 外卖订单 2 跑腿订单 3专送订单
                }


                $run_order_info = Db::name('run_order')->where('status', 2)->field('id,ordernum,user_id,0 as shop_id,rider_distribution_fee,address_id,add_time,status,pre_delivery_time,0 as is_pro_order,rider_type,"" as shop_name,appoint_address as shop_address,appoint_address_jd as longitude,appoint_address_wd as dimension,"" as shop_phone')->select();

                foreach ($run_order_info as $k => $v) {
                    //查询用户地址
                    $run_user_address = $this->getUserAddress($v['address_id']);

                    $run_order_info[$k]['user_address'] = $run_user_address['sheng'] . $run_user_address['shi'] . $run_user_address['qu'] . $run_user_address['address'];//详细地址

                    $run_order_info[$k]['building_card'] = $run_user_address['building_card'];

                    $run_order_info[$k]['user_jd'] = $run_user_address['jd'];

                    $run_order_info[$k]['user_wd'] = $run_user_address['wd'];

                    $run_order_info[$k]['pre_delivery_time'] =date('Y-m-d H:i:s',$run_order_info[$k]['pre_delivery_time']);

                    $run_order_info[$k]['order_type'] = 2;//订单类型 1 外卖订单 2 跑腿订单 3专送订单

                }


                $take_order_info = Db::name('take_order')->where('status', 2)->field('id,ordernum,user_id,0 as shop_id,rider_distribution_fee,take_address_id as address_id,add_time,status,pre_delivery_time,0 as is_pro_order,rider_type,"" as shop_name,"" as shop_address,"" as longitude,"" as dimension,"" as shop_phone,take_address_id,give_address_id')->select();

                foreach ($take_order_info as $k => $v) {
                    //查询取件地址
                    $take_address = $this->getUserAddress($v['address_id']);

                    $take_order_info[$k]['take_user_address'] = $take_address['sheng'] . $take_address['shi'] . $take_address['qu'] . $take_address['address'];//详细地址

                    $take_order_info[$k]['take_building_card'] = $take_address['building_card'];

                    $take_order_info[$k]['take_user_jd'] = $take_address['jd'];

                    $take_order_info[$k]['take_user_wd'] = $take_address['wd'];
                    $take_order_info[$k]['add_time'] = date('Y-m-d H:i:s',$take_order_info[$k]['add_time']);
                    $take_order_info[$k]['pre_delivery_time'] =date('Y-m-d H:i:s',$take_order_info[$k]['pre_delivery_time']);

                    //查询收件地址
                    $give_address = $this->getUserAddress($v['give_address_id']);

                    $take_order_info[$k]['give_user_address'] = $give_address['sheng'] . $give_address['shi'] . $give_address['qu'] . $give_address['address'];//详细地址

                    $take_order_info[$k]['give_building_card'] = $give_address['building_card'];

                    $take_order_info[$k]['give_user_jd'] = $give_address['jd'];

                    $take_order_info[$k]['give_user_wd'] = $give_address['wd'];

                    $take_order_info[$k]['order_type'] = 3;//订单类型 1 外卖订单 2 跑腿订单 3专送订单

                }
                break;

            case '2'://订单池待取货订单
                $order_info = Db::name('order')->where(['status' => 4, 'rider_id' => $rider_id])->field('id,ordernum,user_id,shop_id,rider_distribution_fee,address_id,add_time,status,pre_delivery_time,is_pro_order,rider_type')->select();
                foreach ($order_info as $k => $v) {
                    //查询店铺名称 地址
                    $shop_info[] = Db::table('db_shop')->alias('s')->join('shop_address sa', 's.id=sa.shopid')->where('s.id', $v['shop_id'])->field('s.id,s.shop_name,s.shop_phone,sa.shop_address,sa.longitude,sa.dimension')->find();

                    $order_info[$k]['shop_name'] = $shop_info[$k]['shop_name'];
                    $order_info[$k]['shop_address'] = $shop_info[$k]['shop_address'];
                    $order_info[$k]['longitude'] = $shop_info[$k]['longitude'];
                    $order_info[$k]['dimension'] = $shop_info[$k]['dimension'];
                    $order_info[$k]['shop_phone'] = $shop_info[$k]['shop_phone'];//餐厅电话

                    //查询用户地址
                    $user_address = $this->getUserAddress($v['address_id']);

                    $order_info[$k]['user_address'] = $user_address['sheng'] . $user_address['shi'] . $user_address['qu'] . $user_address['address'];//详细地址
                    $order_info[$k]['building_card'] = $user_address['building_card'];//楼牌号

                    $order_info[$k]['user_jd'] = $user_address['jd'];

                    $order_info[$k]['user_wd'] = $user_address['wd'];

                    $order_info[$k]['order_type'] = 2;//订单类型 1 外卖订单 2 跑腿订单 3专送订单
                }

                $run_order_info = Db::name('run_order')->where(['status' => 3, 'rider_id' => $rider_id])->field('id,ordernum,user_id,0 as shop_id,rider_distribution_fee,address_id,add_time,status,pre_delivery_time,0 as is_pro_order,rider_type,"" as shop_name,appoint_address as shop_address,appoint_address_jd as longitude,appoint_address_wd as dimension,"" as shop_phone')->select();

                foreach ($run_order_info as $k => $v) {
                    //查询用户地址
                    $run_user_address = $this->getUserAddress($v['address_id']);

                    $run_order_info[$k]['user_address'] = $run_user_address['sheng'] . $run_user_address['shi'] . $run_user_address['qu'] . $run_user_address['address'];//详细地址


                    $run_order_info[$k]['building_card'] = $run_user_address['building_card'];

                    $run_order_info[$k]['user_jd'] = $run_user_address['jd'];

                    $run_order_info[$k]['user_wd'] = $run_user_address['wd'];

                    $run_order_info[$k]['order_type'] = 2;//订单类型 1 外卖订单 2 跑腿订单 3专送订单

                }
                $take_order_info = Db::name('take_order')->where(['status' => 3, 'rider_id' => $rider_id])->field('id,ordernum,user_id,0 as shop_id,rider_distribution_fee,take_address_id as address_id,add_time,status,pre_delivery_time,0 as is_pro_order,rider_type,"" as shop_name,"" as shop_address,"" as longitude,"" as dimension,"" as shop_phone,take_address_id,give_address_id')->select();

                foreach ($take_order_info as $k => $v) {
                    //查询取件地址
                    $take_address = $this->getUserAddress($v['take_address_id']);

                    $take_order_info[$k]['take_user_address'] = $take_address['sheng'] . $take_address['shi'] . $take_address['qu'] . $take_address['address'];//详细地址

                    $take_order_info[$k]['take_building_card'] = $take_address['building_card'];

                    $take_order_info[$k]['take_user_jd'] = $take_address['jd'];

                    $take_order_info[$k]['take_user_wd'] = $take_address['wd'];

                    //查询收件地址
                    $give_address = $this->getUserAddress($v['give_address_id']);

                    $take_order_info[$k]['give_user_address'] = $give_address['sheng'] . $give_address['shi'] . $give_address['qu'] . $give_address['address'];//详细地址

                    $take_order_info[$k]['give_building_card'] = $give_address['building_card'];

                    $take_order_info[$k]['give_user_jd'] = $give_address['jd'];

                    $take_order_info[$k]['give_user_wd'] = $give_address['wd'];

                    $take_order_info[$k]['order_type'] = 3;//订单类型 1 外卖订单 2 跑腿订单 3专送订单

                }
                break;

            case '3'://订单池待送达订单
                $order_info = Db::name('order')->where(['status' => 5, 'rider_id' => $rider_id])->field('id,ordernum,user_id,shop_id,rider_distribution_fee,address_id,add_time,status,pre_delivery_time,is_pro_order,rider_type')->select();

                foreach ($order_info as $k => $v) {
                    //查询店铺名称 地址
                    $shop_info[] = Db::table('db_shop')->alias('s')->join('shop_address sa', 's.id=sa.shopid')->where('s.id', $v['shop_id'])->field('s.id,s.shop_name,s.shop_phone,sa.shop_address,sa.longitude,sa.dimension')->find();

                    $order_info[$k]['shop_name'] = $shop_info[$k]['shop_name'];
                    $order_info[$k]['shop_address'] = $shop_info[$k]['shop_address'];
                    $order_info[$k]['longitude'] = $shop_info[$k]['longitude'];
                    $order_info[$k]['dimension'] = $shop_info[$k]['dimension'];
                    $order_info[$k]['shop_phone'] = $shop_info[$k]['shop_phone'];//餐厅电话

                    //查询用户地址
                    $user_address = $this->getUserAddress($v['address_id']);

                    $order_info[$k]['user_address'] = $user_address['sheng'] . $user_address['shi'] . $user_address['qu'] . $user_address['address'];//详细地址
                    $order_info[$k]['building_card'] = $user_address['building_card'];//楼牌号

                    $order_info[$k]['user_jd'] = $user_address['jd'];

                    $order_info[$k]['user_wd'] = $user_address['wd'];

                    $order_info[$k]['order_type'] = 1;//订单类型 1 外卖订单 2 跑腿订单 3专送订单
                }

                $run_order_info = Db::name('run_order')->where(['status' => 4, 'rider_id' => $rider_id])->field('id,ordernum,user_id,0 as shop_id,rider_distribution_fee,address_id,add_time,status,pre_delivery_time,0 as is_pro_order,rider_type,"" as shop_name,appoint_address as shop_address,appoint_address_jd as longitude,appoint_address_wd as dimension,"" as shop_phone')->select();

                foreach ($run_order_info as $k => $v) {
                    //查询用户地址
                    $run_user_address = $this->getUserAddress($v['address_id']);

                    $run_order_info[$k]['user_address'] = $run_user_address['sheng'] . $run_user_address['shi'] . $run_user_address['qu'] . $run_user_address['address'];//详细地址

                    $run_order_info[$k]['building_card'] = $run_user_address['building_card'];

                    $run_order_info[$k]['user_jd'] = $run_user_address['jd'];

                    $run_order_info[$k]['user_wd'] = $run_user_address['wd'];

                    $run_order_info[$k]['order_type'] = 2;//订单类型 1 外卖订单 2 跑腿订单 3专送订单

                }

                $take_order_info = Db::name('take_order')->where(['status' => 4, 'rider_id' => $rider_id])->field('id,ordernum,user_id,0 as shop_id,rider_distribution_fee,take_address_id as address_id,add_time,status,pre_delivery_time,0 as is_pro_order,rider_type,"" as shop_name,"" as shop_address,"" as longitude,"" as dimension,"" as shop_phone,take_address_id,give_address_id')->select();
                foreach ($take_order_info as $k => $v) {
                    //查询取件地址
                    $take_address = $this->getUserAddress($v['take_address_id']);

                    $take_order_info[$k]['take_user_address'] = $take_address['sheng'] . $take_address['shi'] . $take_address['qu'] . $take_address['address'];//详细地址

                    $take_order_info[$k]['take_building_card'] = $take_address['building_card'];

                    $take_order_info[$k]['take_user_jd'] = $take_address['jd'];

                    $take_order_info[$k]['take_user_wd'] = $take_address['wd'];

                    //查询收件地址
                    $give_address = $this->getUserAddress($v['give_address_id']);

                    $take_order_info[$k]['user_address'] = $give_address['sheng'] . $give_address['shi'] . $give_address['qu'] . $give_address['address'];//详细地址

                    $take_order_info[$k]['building_card'] = $give_address['building_card'];

                    $take_order_info[$k]['user_jd'] = $give_address['jd'];

                    $take_order_info[$k]['user_wd'] = $give_address['wd'];

                    $take_order_info[$k]['order_type'] = 3;//订单类型 1 外卖订单 2 跑腿订单 3专送订单

                }

                break;
            case '4'://系统派单 待接单订单池

                $order_info = Db::name('order')->where(['status' => 10, 'rider_id' => $rider_id])->field('id,ordernum,user_id,shop_id,rider_distribution_fee,address_id,add_time,status,pre_delivery_time,is_pro_order,rider_type')->select();//系统派单 骑手待确认
                foreach ($order_info as $k => $v) {
                    //查询店铺名称 地址
                    $shop_info[] = Db::table('db_shop')->alias('s')->join('shop_address sa', 's.id=sa.shopid')->where('s.id', $v['shop_id'])->field('s.id,s.shop_name,s.shop_phone,sa.shop_address,sa.longitude,sa.dimension')->find();

                    $order_info[$k]['shop_name'] = $shop_info[$k]['shop_name'];
                    $order_info[$k]['shop_address'] = $shop_info[$k]['shop_address'];
                    $order_info[$k]['longitude'] = $shop_info[$k]['longitude'];
                    $order_info[$k]['dimension'] = $shop_info[$k]['dimension'];
                    $order_info[$k]['shop_phone'] = $shop_info[$k]['shop_phone'];//餐厅电话

                    //查询用户地址
                    $user_address = $this->getUserAddress($v['address_id']);

                    $order_info[$k]['user_address'] = $user_address['sheng'] . $user_address['shi'] . $user_address['qu'] . $user_address['address'];//详细地址
                    $order_info[$k]['building_card'] = $user_address['building_card'];//楼牌号

                    $order_info[$k]['user_jd'] = $user_address['jd'];

                    $order_info[$k]['user_wd'] = $user_address['wd'];

                    $order_info[$k]['order_type'] = 1;//订单类型 1 外卖订单 2 跑腿订单 3专送订单
                }

                $run_order_info = Db::name('run_order')->where(['status' => 6, 'rider_id' => $rider_id])->field('id,ordernum,user_id,0 as shop_id,rider_distribution_fee,address_id,add_time,status,pre_delivery_time,0 as is_pro_order,rider_type,"" as shop_name,appoint_address as shop_address,appoint_address_jd as longitude,appoint_address_wd as dimension,"" as shop_phone')->select();

                foreach ($run_order_info as $k => $v) {
                    //查询用户地址

                    $run_user_address = $this->getUserAddress($v['address_id']);

                    $run_order_info[$k]['user_address'] = $run_user_address['sheng'] . $run_user_address['shi'] . $run_user_address['qu'] . $run_user_address['address'];//详细地址

                    $run_order_info[$k]['building_card'] = $run_user_address['building_card'];

                    $run_order_info[$k]['user_jd'] = $run_user_address['jd'];

                    $run_order_info[$k]['user_wd'] = $run_user_address['wd'];

                    $run_order_info[$k]['order_type'] = 2;//订单类型 1 外卖订单 2 跑腿订单 3专送订单

                }

                $take_order_info = Db::name('take_order')->where(['status' => 6, 'rider_id' => $rider_id])->field('id,ordernum,user_id,0 as shop_id,rider_distribution_fee,take_address_id as address_id,add_time,status,pre_delivery_time,0 as is_pro_order,rider_type,"" as shop_name,"" as shop_address,"" as longitude,"" as dimension,"" as shop_phone,take_address_id,give_address_id')->select();

                foreach ($take_order_info as $k => $v) {
                    //查询取件地址
                    $take_address = $this->getUserAddress($v['take_address_id']);

                    $take_order_info[$k]['take_user_address'] = $take_address['sheng'] . $take_address['shi'] . $take_address['qu'] . $take_address['address'];//详细地址

                    $take_order_info[$k]['take_building_card'] = $take_address['building_card'];

                    $take_order_info[$k]['take_user_jd'] = $take_address['jd'];

                    $take_order_info[$k]['take_user_wd'] = $take_address['wd'];

                    //查询收件地址
                    $give_address = $this->getUserAddress($v['give_address_id']);

                    $take_order_info[$k]['give_user_address'] = $give_address['sheng'] . $give_address['shi'] . $give_address['qu'] . $give_address['address'];//详细地址

                    $take_order_info[$k]['give_building_card'] = $give_address['building_card'];

                    $take_order_info[$k]['give_user_jd'] = $give_address['jd'];

                    $take_order_info[$k]['give_user_wd'] = $give_address['wd'];

                    $take_order_info[$k]['order_type'] = 3;//订单类型 1 外卖订单 2 跑腿订单 3专送订单

                }
                break;
            default:
                # code...
                break;
        }

        $model = array();

        $model = array_merge($order_info , $run_order_info , $take_order_info);

        if ($model) {
            multi_array_sort($model, 'add_time', SORT_DESC);
        }
        return $model;
    }

    /**
     * @param 根据ID查询用户地址
     */
    public function getUserAddress($id)
    {
        $where['id'] = array('in', $id);

        $address = Db::name('address')->where($where)->select();

        return $address[0];
    }


}