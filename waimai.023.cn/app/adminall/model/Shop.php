<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 17:12
 */

namespace app\adminall\model;


use think\Db;

class Shop extends Admin
{
    protected $search_field = 'site|title|fturl|imgtitle|imgsite|imgurl';
    protected $search_time = 'register_time';

    public function getShopStatusAttr($val,$data)
    {
        $list = [0=>'false',1=>'true'];
        $value = [0=>'开始营业',1=>'停止营业'];

        return '<div class="huadong">
                    <div class="right" id="'.$list[$val].'" onclick="
                    from_ajax(this,'.$data['id'].',\''.$value[$val].'\',\''.url('shop/setStatus').'\','.$val.',\'shop_status\')">
                        <div isopen="'.$list[$val].'" class="btnn"></div>
                    </div>
                </div>';
    }

    public function getWebLinkAttr($val)
    {
        return '<span><a href="javascript:;">查看</a></span>';
    }

    public function getAdminLinkAttr($val)
    {
        return '<span><a href="javascript:;">查看</a></span>';
    }

    public function getShopTypeAttr($val)
    {
        return Db::name('ShopBusinesstype')->where('id',$val)->value('typename');
    }

    public function getShopNumberAttr($val,$data)
    {
        return Db::name('goods')->where('shop_id',$data['id'])->count();
    }

    public function address()
    {
        return $this->hasOne('ShopAddress','shopid','id' ,[],'LEFT JOIN');
    }

    public function getShopAddressAttr($val)
    {
        if (empty($val)){
            $val = '没有地址';
        }
        return $val;
    }


}