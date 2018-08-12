<?php
/**
 * 账号设置
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:19
 */

namespace app\shopadmin\controller;
use think\Controller;
use think\Db;
use think\Request;

class Accountset extends Admin
{
    /**
     * 显示账号设置里面的数据
     */
    public function showBusiness(Request $request)
    {
        $bid=$request->param("bid",'1');
        $business=Db::name("business")->where("id=".$bid)->find();
        $this->assign("business",$business);
        $tb=Tpldemo::temp()->_table_button;
        $this->assign('_table_button',$tb);
        $this->assign('_from_button',Tpldemo::temp()->_from_button);
//        return Tpldemo::temp()->template('Account_set')->templateView();
        return $this->fetch('Account_set');
    }

    /**
     * 修改密码
     */
    public function updPassword(Request $request)
    {
        $bid=$request->param("bid");
        if($request->isPost()){
            $business=Db::name("business")->where("id=".$bid)->find();

            $old=encrmd($_POST["old"]);
            $new=encrmd($_POST["second_new"]);

            if($business["pwd"]!=$old){
                return "<script>alert('原密码不正确');history.back(-1);</script>";
                //return $this->result('',config('code.no'),'原密码不正确');
            }else{

                $result=Db::name("business")->where("id=".$bid)->update(["pwd"=>$new]);

                if($result){
                    return "<script>alert('修改成功,请用新密码重新登录');history.back(-1);</script>";
                    //return $this->result('',config('code.yes'),'修改成功');
                }else{
                    return "<script>alert('修改失败');history.back(-1);</script>";
                    //return $this->result('',config('code.no'),'修改失败');
                }
            }
        }
    }
    /**
     * 修改绑定的手机号
     */
    public function updBusinessPhone(Request $request)
    {
        if($request->isPost()){

        }
        return $this->view;
    }

    /**
     * 系统设置
     */
    public function showSystemSetup()
    {
        return $this->fetch('System_set');
    }
}