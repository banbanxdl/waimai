<?php

namespace app\api\model;

use think\Model;
use think\Db;

class Money extends Model
{
  /**
  *@param 获取骑手收支明细
  */
  public function riderIncomeAndExpenditure($rider_id)
  {
    $list=Db::name('rider_detailed_log')->where(['uid'=>$rider_id,'identity'=>2])->order('add_time desc')->select();

    foreach ($list as $k => $v) {
      switch ($v['type']) {
        case '1':
          $list[$k]['type_explain']='配送收入';
          break;
        case '2':
          $list[$k]['type_explain']='取消扣款';
          break;
        case '3':
          $list[$k]['type_explain']='违规扣款';
          break;                  
        default:
          # code...
          break;
      }
      $list[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
    }

    return $list;
  } 
  
}