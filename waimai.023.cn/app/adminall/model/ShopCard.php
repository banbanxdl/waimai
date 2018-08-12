<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14
 * Time: 15:49
 */

namespace app\adminall\model;


class ShopCard extends Admin
{
    public function getCertificatesImgAttr($val)
    {
        if (strpos($val,',')){
            return explode($val,',');
        }else{
            return $val;
        }
    }

}