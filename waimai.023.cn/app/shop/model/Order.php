<?php


namespace app\shop\model;

use think\Model;

class Order extends Model{


    /**
     * @param $val 字段的值
     * @param $data 这条数据的值
     */
    public function getDeliveryTimeAttr($val,$data)
    {
        date('%Y.%m.%d %H:%I',$val);
    }
}