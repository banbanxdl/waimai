<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Session;

class Login extends Controller
{
   
   /**
   * @param 修改登录密码
   */
   public function updLoginPwd(Request $request)
   {	
   		$type =2;//修改登录密码的身份 1 用户 2 骑手 3 商家
   		$id   =$request->param('id');//身份对应的id 骑手id
   		$pwd  =$request->param('pwd');//需要修改的密码

   		if(empty($type)||empty($id)||empty($pwd)){
   			
   			return message('','参数为空',3);
   		}
  		
  		$result=Loader::model('login')->setUpdPwd($id,$type,$pwd);

  		if($result){
  			return message('','修改成功',2);
  		}else{
  			return message('','修改失败',3);
  		}

   }

   /**
   *@param 修改手机号
   */
   public function updRiderPhone(Request $request)
   {
   	$rider_id=$request->param('id');//当前登录的骑手id

   	$phone  =$request->param('pgone');//需要修改的手机号

   	$code   =$request->param('code');//验证码

    //判断验证码
    if($code!=session::get('code')){
      return message('','验证码错误',3);
    }

    //判断手机号是否存在
	$rider_info=Db::name('rider')->where(['phone'=>$phone])->find();

	if($rider_info){
		return message('','手机号码已存在',3);
	}else{
		$upd=Db::name('rider')->where(['id'=>$id])->update(['phone'=>$phone]);

		if($upd){
			return message('','修改成功',2);
		}else{
			return message('','修改失败',3);
		}
	}   	
   }


  /**
  * @param 注册
  */
  public function register(Request $request)
  {
    $phone=$request->param('phone');

    $pwd  =$request->param('pwd');

   //$code =$request->param('code');//验证码

    $type =$request->param('type');//注册的身份 1 用户 2 骑手 3 商家

    //empty($code)
    if(empty($phone) || empty($pwd)  || empty($type)){
      return message('','参数为空',3);
    }

    //判断验证码
    // if($code!=session::get('code')){
    //   return message('','验证码错误',3);
    // }

    $result=Loader::model('Login')->setRegister($phone,$pwd,$type);

    if($result==1){

      return message('','注册成功',2);
    }elseif($result==2){

      return message('','账号存在',3);
    }elseif($result==0){

      return message('','注册失败',3);
    }

  }

  /**
  * @param 登录
  */  
  public function riderLogin(Request $request)
  {
    $login_type =$request->param('login_type');//登录方式 1 密码登录  2 手机号码登录

    $phone=$request->param('phone');

    switch ($login_type) {
      case '1':
        $pwd  =$request->param('pwd');

        $rider_info=db('rider')->where(['phone'=>$phone])->find();

        if(empty($rider_info)){
          return message('','账号不存在',3);
        }else{
          $rider_pwd=db('rider')->where(['phone'=>$phone,'pwd'=>encrmd($pwd)])->find();

          if(empty($rider_pwd)){
            return message('','密码错误',3);
          }else{

            return message($rider_pwd,'登录成功',2);
          return message($rider_info,'登录成功',2);
          }
        }

        break;
      case '2':
        $code =$request->param('code');

        if($code!=session::get('code')){
          return message('','验证码错误',3);
        }

        $rider_phone=db('rider')->where('phone',$phone)->find();
        if($rider_phone){
          Session::delete('code');//清空验证码
          return message($rider_phone,'登录成功',2);
        }else{
          return message('','账号不存在',3);
        }

        break;      
      default:
        # code...
        break;
    }
   
  }




 
}
