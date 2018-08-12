<?php
namespace app\shop\model;
//use app\common\model\Base;
use think\Loader;
use think\Image;
use think\Request;
use think\Db;

class Img 
{
    
    //图片上传接口
   public function picture($files)
   {
        //$files = request()->file($img);
        //定义一个数组存储图片路径
        //$img_data=array();
        //foreach($files as $file){
            //允许格式
            $ext=['pdf','word','excel','txt','doc','mp4','png','jpg','jpeg','gif'];

            //移动到框架应用根目录 /public/uploads/ 目录下
            $info = $files->validate(['size'=>'10485760','ext'=>$ext])->move(ROOT_PATH . 'public' . DS . 'uploads/shop');

            if($info){
                // 成功上传后 获取上传信息

                $img_data=$info->getSaveName();
                $imgs=
                'http://'. $_SERVER['HTTP_HOST'].'/public/uploads/shop/'.$img_data;
                return $imgs;

            }else{
                // 上传失败获取错误信息
                return $files->getError();
            }
       // }
       return $img_data;

    }


}