<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21
 * Time: 9:51
 */

namespace app\userapi\model;


use think\Db;

class VipUser extends Admin
{
    public function saveVipTime($uid,$vid)
    {
        $info = Db::name('user')->where('id',$uid)->find();
        $vinfo = Db::name('vip')->where('id',$vid)->find();
        //余额 是否足够
        if ($info['balance'] <= $vinfo['money']){
            return false;
        }else{
            Db::name('user')->where('id',$uid)->setDec('balance',$vinfo['money']);
            $vip_list = Db::name('vip_user')->where('uid',$uid)->find();
            if (empty($vip_list)){
                Db::name('vip_paylog')->insert([
                    'uid'=>$uid,
                    'vip_id'=>$vid,
                    'add_time'=>time(),
                    'money'=>$vinfo['money'],
                    'end_time'=>strtotime('+'.$vinfo['time'].' month'),
                    'pack_money'=>$vinfo['pack_num']*$vinfo['lucky_money'],
                ]);
                Db::name('vip_user')->insert([
                    'uid'=>$uid,
                    'vip_id'=>$vid,
                    'update_time'=>time(),
                    'endtime'=>strtotime('+'.$vinfo['time'].' month'),
                    'money'=>$vinfo['money'],
                    'pack_sum'=>$vinfo['pack_num']*$vinfo['lucky_money'],
                ]);
                return true;
            }else{
                if (time() > $vip_list['endtime'] ){
                    Db::name('vip_paylog')->insert([
                        'uid'=>$uid,
                        'vip_id'=>$vid,
                        'add_time'=>time(),
                        'money'=>$vinfo['money'],
                        'end_time'=>strtotime('+'.$vinfo['time'].' month'),
                        'pack_money'=>$vinfo['pack_num']*$vinfo['lucky_money'],
                    ]);
                    Db::name('vip_user')->insert([
                        'uid'=>$uid,
                        'vip_id'=>$vid,
                        'update_time'=>time(),
                        'endtime'=>strtotime('+'.$vinfo['time'].' month'),
                        'money'=>$vinfo['money'],
                        'pack_sum'=>$vinfo['pack_num']*$vinfo['lucky_money'],
                    ]);
                    return true;
                }else{
                    Db::name('vip_paylog')->insert([
                        'uid'=>$uid,
                        'vip_id'=>$vid,
                        'add_time'=>time(),
                        'money'=>$vinfo['money'],
                        'end_time'=>strtotime('+'.$vinfo['time'].' month',$vip_list['endtime']),
                        'pack_money'=>$vinfo['pack_num']*$vinfo['lucky_money'],
                    ]);
                    Db::name('vip_user')->insert([
                        'uid'=>$uid,
                        'vip_id'=>$vid,
                        'update_time'=>time(),
                        'endtime'=>strtotime('+'.$vinfo['time'].' month',$vip_list['endtime']),
                        'money'=>$vinfo['money'],
                        'pack_sum'=>$vinfo['pack_num']*$vinfo['lucky_money'],
                    ]);
                    return true;
                }
            }
        }
    }

}