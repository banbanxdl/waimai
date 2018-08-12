<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;

class Money extends Controller
{
  /**
  * @param 骑手收支明细
  */
  public function getIncomeAndExpenditure(Request $request)
  {
    $rider_id=$request->param('id');//骑手id

    $resurt=Loader::model('Money')->riderIncomeAndExpenditure($rider_id);

    return message($resurt,'获取成功',2);
  }
}
