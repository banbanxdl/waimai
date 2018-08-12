<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/30
 * Time: 15:31
 */

namespace app\userapi\controller;


use app\userapi\model\ShopDistribution;
use think\Controller;
use think\Db;
use think\Request;

class Test extends Controller
{


    public function demo()
    {
        $handler = opendir('public/upload/goods_img');//当前目录中的文件夹下的文件夹
        while( ($filename = readdir($handler)) !== false ) {
            if($filename != "." && $filename != ".."){
                $img[] = $filename;
            }
        }
        closedir($handler);


        for ($d=0;$d<rand(50,200);$d++){
            $arr['menu_id'] = rand(28,111);
            $s= Db::name('goods_menu')->where('id',$arr['menu_id'])->value('shop_id');
            $arr['shop_id'] = $s;
            $arr['goods_img'] = config('system.site_url').'/public/upload/goods_img/'.$img[rand(0,(count($img)+1))];
            $arr['goods_name'] = md5(rand(100,999));
            $arr['goods_price'] = rand(1,80);
            $arr['lunch_box_price'] = rand(10,30).'.'.rand(1,10);
            $arr['describe'] = '';
            $arr['stock'] = rand(100,1000);
            $arr['add_time'] = time();
            $arr['sort'] = rand(1,100);
            $data[] = $arr;
        }
        $n = Db::name('goods')->insertAll($data);
        dump($n);
    }

}