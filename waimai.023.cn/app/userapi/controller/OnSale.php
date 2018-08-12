<?php
/**
 * 我的 卡券
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21
 * Time: 14:16
 */

namespace app\userapi\controller;


use think\Db;
use think\Loader;
use think\Request;

class OnSale extends Api
{
    /**
     * 优惠券
     * @param Request $request
     * @return mixed
     */
    public function indexListPage(Request $request)
    {
        $model = Loader::model('Coupon');
        if ($request->isGet()){
            $id = $request->get('id');
            $name = $request->get('name');
            try{
                if (!empty($name)){
                    $shopid = Db::name('shop')->where('shop_name',$name)->value('id');
                    $list = $model->where('uid',$id)->where('shop_id',$shopid)->select();
                }else{
                    $list = $model->where('uid',$id)->select();
                }
                if (empty($list)){
                    $tree = '';
                }else{
                    foreach ($list as $value){
                        $tree[] = $value->toArray();
                    }
                }
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($tree)){
                return message('','获取失败',3);
            }else{
                return message('','获取成功',2);
            }
        }
    }


    /**
     * 红包
     * @param Request $request
     * @return mixed
     */
    public function indexRedList(Request $request)
    {
        $model = Loader::model('RedPacket');
        if ($request->isGet()){
            $id = $request->get('id');
            $name = $request->get('name');
            try{
                if (!empty($name)){
                    $shopid = Db::name('shop')->where('shop_name',$name)->value('id');
                    $list = $model->where('uid',$id)->where('shop_id',$shopid)->select();
                }else{
                    $list = $model->where('uid',$id)->select();
                }
                if (empty($list)){
                    $tree = '';
                }else{
                    foreach ($list as $value){
                        $tree[] = $value->toArray();
                    }
                }
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($tree)){
                return message('','获取失败',3);
            }else{
                return message('','获取成功',2);
            }
        }
    }
}