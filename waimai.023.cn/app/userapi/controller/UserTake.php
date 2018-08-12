<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/11
 * Time: 9:23
 */

namespace app\userapi\controller;


use think\Db;
use think\Loader;
use think\Request;

class UserTake extends Api
{
    /**
     * 用户提现
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request)
    {
        $vali = new \app\userapi\validate\Take();
        if ($request->isPost()){
            $data = $request->post();
            if (!$vali->scene('add')->check($data)){
                return message('',$vali->getError(),1);
            }
            try{
                $data['identity'] = 1;
                $data['status'] = 1;
                $data['add_time'] = time();
                $n = Db::name('put_forword')->insert($data);
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($n)){
                return message('','提现失败',3);
            }else{
                return message('','提现成功',2);
            }

        }
    }


}