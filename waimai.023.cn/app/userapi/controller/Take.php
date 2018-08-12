<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5
 * Time: 9:15
 */

namespace app\userapi\controller;


use think\Db;
use think\Loader;
use think\Request;

class Take extends Api
{
    /**
     * 用户评价 店铺 骑手
     * @param Request $request
     * @return mixed
     */
    public function addUserTake(Request $request)
    {
        $OrderComment = Loader::model('OrderComment');
        $UserTalk = Loader::model('UserEvaluateRider');
        $vali = Loader::validate('Talk');
        if ($request->isPost()){
            $data = $request->post();
            if (!$vali->scene()->check($data)){
                return message('',$vali->getError(),1);
            }
            $take_shop['order_id'] = $data['order_id'];
            $take_shop['uid'] = $data['user_id'];
            $take_shop['sid'] = $data['shop_id'];
            $take_shop['num'] = $data['num'];
            $take_shop['content'] = $data['content'];
            $take_shop['imgurl'] = implode(',',$data['img_list']);
            $take_shop['add_time'] = time();
            $take_shop['is_anonymous'] = 0;
            $talk_rider['uid'] = $data['user_id'];
            $talk_rider['type'] = 1;
            $talk_rider['oid'] = $data['order_id'];
            $talk_rider['evaluate'] = $data['right'];
            $talk_rider['reason'] = implode(',',$data['why']);
            $talk_rider['add_time'] = time();
            try{
                $talk_rider['rid'] = Db::name('Order')->where('id',$data['order_id'])->value('rider_id');
                $n = $OrderComment->insert($take_shop);
                $n = $UserTalk->insert($talk_rider);
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($n)){
                return message('','评价失败',3);
            }else{
                return message('','评价成功',2);
            }
        }
    }

}