<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21
 * Time: 9:54
 */

namespace app\userapi\controller;


use think\Loader;
use think\Request;

class PayVip extends Api
{
    public function indexVipList(Request $request)
    {
        $model = Loader::model('Vip');
        if ($request->isGet()){
            return message($model::all(),'success',2);
        }
    }

    public function setPayVip(Request $request)
    {
        $model = Loader::model('VipUser');
        if ($request->isPost()){
            $id = $request->post('id');
            $vid = $request->post('vid');
            try{
                $true = $model->saveVipTime($id,$vid);
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if ($true){
                return message('','购买成功',2);
            }else{
                return message('','购买失败',3);
            }
        }
    }

}