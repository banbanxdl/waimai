<?php

namespace app\api\model;

use think\Model;
use think\Db;

class Login extends Model
{
  
  /**
  * @param 修改登录密码
  */
  public function setUpdPwd($id,$type,$pwd)
  {
  	switch ($type) {
		case '1':
			# code...
			break;
		case '2'://这是骑手在修改密码

			$result=db('rider')->where('id',$id)->update(['pwd'=>encrmd($pwd)]);

			break;
		case '3':

			break;
		default:
			# code...
			break;
	}

	return $result;
  }


  /**
  * @param 注册账号
  */
  public function setRegister($phone,$pwd,$type)
  {
  	switch ($type) {
  		case '1':
  			# code...
  			break;
  		case '2'://这是骑手注册
  			$new['phone']   =$phone;
  			$new['pwd']     =encrmd($pwd);
  			$new['add_time']=time();

  			//判断账号是否存在
  			$rider_info=Db::name('rider')->where('phone',$phone)->find();
  			if($rider_info){
  				return 2;
  			}

  			$result=Db::name('rider')->insert($new);
  			break;
  		case '3':
  			# code...
  			break;  			  		
  		default:
  			# code...
  			break;
  	}
  	return $result;
  }
  
}