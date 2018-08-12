<?php

namespace app\api\model;

use think\Model;
use think\Db;

class Putforward extends Model
{
  /**
  * @param 骑手申请提现
  */ 

  public function getRiderPutForward($rider_id,$identity,$money,$bank_id,$bank_code)
  {
    $new['uid']     =$rider_id;
    $new['identity']=$identity;
    $new['money']   =$money;
    $new['bank_id'] =$bank_id;
    $new['add_time']=time();
    $new['bank_code']=$bank_code;
    $new['status']  =1;

    $result=Db::name('put_forward')->insert($new);

    return $result;

  }
}