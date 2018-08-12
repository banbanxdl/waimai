<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/21
 * Time: 9:49
 */

namespace app\rider\model;
use think\image\Exception;
use think\Model;
use think\Db;

class RiderList extends  Admin
{
    protected  $name = 'rider_info';
    protected $search_field = 'name|sex|age';
    protected $autoWriteTimestamp = true;

    /**组合身份证
     * shop_id ShopId
     * @param $val
     * @param $data
     */
    public function getIdcardAttr($data,$val)
    {
        $l_str=substr($data,0,5);
        $r_str=substr($data,-4,4);
        $str = $l_str.'*********'.$r_str;
        return $str;
    }
    public function getStatusAttr($data,$val)
    {
       if($data == 2)
           $str='审核通过';
        else{
            $str='<span style="color:#f00">审核失败</span>';
        }
        return $str;
    }
}