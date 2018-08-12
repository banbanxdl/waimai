<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27
 * Time: 11:37
 */

namespace app\shop\controller;

use think\Hook;
use think\Loader;
use think\Request;
use think\Db;
use think\Controller;
use think\Session;

//require "./extend/aliyun_code/sendSms.php";

class Register extends Index
{

    /*
    *注册
    */
    public  function register(Request $request)
    {
        $data=$request->param();
        //判断验证码是否正确
        $arr=[
        	"account_name"=>rand_account(),
        	"phone"=>$data["phone"],
        	"pwd"=>rand_pwd(),
            "register_time"=>time()
        ];
        if($data['code']!=session::get('code')){
            return $this->message('','验证码错误',3);
        }

        if(session::get('time')==time()){
            Session::delete('time');
            Session::delete('code');
            return $this->message('','验证码过期',3);
        }
        $is_exist=db("business")->where("phone=".$data["phone"])->select();
        if ($is_exist) {
        	return $this->message('','账号已存在',3);
        }
        $result=Db('business')->insert($arr);

        if($result){
        	return $this->message("","账号注册成功,您的初始密码为".$is_exist[0]["pwd"].",请尽快修改",2);
        }


    }

    /*
    *账号密码登录
    */
    public function login(Request $request)
    {
        $data=$request->param();

        //判断账号密码是否为空
        if(empty($data['phone']) || empty($data['pwd'])){
        	
             return $this->message('','账号密码不能为空',3);
             
        }

        $phone=Db::name('business')->where('phone',$data['phone'])->find();
        $pwd=encrmd($data['pwd']);
        //判断账号是否存在
        if(empty($phone)){
           return $this->message('','登录账号不存在',3);
           
        }else{
            $rel=Db::name('business')->where(['phone'=>$data['phone'],'pwd'=>$pwd])->select();
            /**foreach ($rel as $k => $v) {
                 $rel[$k]['imgurl']="http://youhuibao.023wx.cn/public/uploads/api/".$v['imgurl'];
                 //查询商户id
                 $shop_id=db('shops')->where('user_id',$v['id'])->find();

                 if($shop_id){
                    $rel[$k]['shop_id']=$shop_id['id'];
                 }else{
                    $rel[$k]['shop_id']=0;
                 }
            }**/

            if($rel){
                //添加最近登录时间和登录设备
                Db::name('business')
                    ->where("id=".$rel[0]["id"])
                    ->update(["login_time"=>time(),"equipment"=>$data["equipment"]]);
                return $this->message($rel,'登录成功',2);
                
            }else{
                return $this->message('','登录密码错误',3);
            }
        }


    }
    /*
    *手机验证码登录
    */
    public function codeLogin(Request $request)
    {
        $data=$request->param();
        //判断账号密码是否为空
        if(empty($data['phone']) || empty($data['code'])){
        	
             return $this->message('','账号验证码不能为空',3);
             
        }

        $phone=Db::name('business')->where('phone',$data['phone'])->find();
        
        //判断账号是否存在
        if(empty($phone)){
           return $this->message('','登录账号不存在',3);
           
        }else{
        	//验证码错误
        	if($data['code']!=session::get('code')){
            	return $this->message('','验证码错误',3);
	        }
	        //验证码过期
	        if(session::get('time')==time()){
	            Session::delete('time');
	            Session::delete('code');
	            return $this->message('','验证码过期',3);
	        }
            $rel=Db::name('business')->where('phone='.$data['phone'])->select();

            if($rel){
                //添加最近登录时间和登录设备
                Db::name('business')
                    ->where("id=".$rel[0]["id"])->
                    update(["login_time"=>time(),"equipment"=>$data["equipment"]]);
                return $this->message($rel,'登录成功',2);
                
            }else{
                return $this->message('','登录密码错误',3);
            }
        }


    }
    /*
    *找回密码
    */
    public function backPwd(Request $request)
    {

        $data=$request->param();

        //判断手机号码是否存在

        $phone=db('user')->where('phone',$data['phone'])->find();

        if($phone){

            //判断验证码是否正确

            
            if($data['code']!=session::get('code')){
                return $this->message('','验证码错误',3);
            }

             if(session::get('time')+60>time()){
                Session::delete('time');
                Session::delete('code');
                return $this->message('','验证码过期',3);
            }

            $rel=Loader::model('Login')->getbackPwd($data['phone'],$data['cipher']);

            if($rel){
                return $this->message('','修改成功',2);
            }else{
                return $this->message('','修改失败',3);
            }
            
        }else{
            return $this->message('','账号不存在',3);
        }
    }



    /*
    *发送验证码
    */
    public function getSendsms(Request $request)
    {
        $data=$request->param();

        $phone=$data['phone'];

        $type=$data['type'];//1 为注册验证 2 变更验证

        //验证码
        $code=rand(100000,999999);

        if($type=='1'){
            $type_name="注册验证";
             $rel=sendSms(trim($type_name),trim($phone),trim($code),'盒籽','SMS_66015043');
        }elseif($type=='2'){
            $type_name="变更验证";
            $rel=sendSms(trim($type_name),trim($phone),trim($code),'盒籽','SMS_66015041');
        }
        
        if($rel->Message=='OK' && $rel->Code=='OK'){
            session::set('code',$code);
            session::set('time',time()+300);
            return $this->message('','验证码发送成功',2);
        }else{
            return $this->message('','验证码发送失败',3);
        }


    }

    /*

    *微信 扣扣 登录 授权 注册

    */
    public function setThirdPartyRegister(Request $request)
    {
        $data=$request->param();

        $info=Loader::model('Login')->getThirdPartyRegister($data);

        //判断验证码是否正确

        if($data['code']!=session::get('code')){
            return $this->message('','验证码错误',3);
        }

        if(session::get('tiem')+60>time()){
            Session::delete('time');
            Session::delete('code');
            return $this->message('','验证码过期',3);
        }

        if($info==1){
            if($data['type']=='wx'){
                $user_info=db('user')->where('wx_oppenid',$data['oppenid'])->select();

            }elseif($data['type']=='qq'){
                $user_info=db('user')->where('qq_oppenid',$data['oppenid'])->select();
                
            }
            

            return $this->message($user_info,'信息绑定成功',2);
        }elseif($info==3){
            return $this->message('','推荐人不存在',3);
        }else{
            return $this->message($info,'信息绑定失败',3);
        }

    }


    //检查是否有oppenid

    public function checkOppenid(Request $request)
    {
        $data=$request->param();

        $info=Loader::model('Login')->getCheckOppenid($data);

        if($info==2){
            //查询用户信息
            if($data['type']=='qq'){
                $user_info=db('user')->where('qq_oppenid',$data['oppenid'])->select();

            }elseif($data['type']=='wx'){

                $user_info=db('user')->where('wx_oppenid',$data['oppenid'])->select();      
            }
            

            return $this->message($user_info,'登录成功',2);

        }elseif($info==0){
            return $this->message('','添加信息失败',3);
        }else{
            return $this->message($info,'请前去绑定账号信息',3);
        }
    }

}