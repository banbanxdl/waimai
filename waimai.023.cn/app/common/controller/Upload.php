<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 18:00
 */

namespace app\common\controller;


use think\Request;

class Upload
{
    /**
     * 单张图片上传
     * @param string $file_name
     * @return string
     */
    public function upload($file_name = 'images')
    {
        $file = Request::instance()->file($file_name);
        if (count($file) == 1){
            $files[] = $file;
        }else{
            $files = $file;
        }
        //获取当前控制器的名称
        $module = Request::instance()->module();
        foreach ($files as $value) {
            $info = $value->validate([
                'size' => 4194304, 'ext' => 'jpeg,jpg,png,gif'
            ])->move(ROOT_PATH . 'public' . DS . 'upload/' . $module . '/');
            if ($info) {
                // 成功上传后 获取上传信息
                $string = $info->getSaveName();
                $list[] = config('system.site_url') . '/public' . DS . 'upload/' . $module . '/' . $string;
            } else {
                // 上传失败获取错误信息
                return $value->getError();
            }
        }

        return $list;

    }

    public function upload_web($img)
    {
        $config=config('aliyunoss.aliyun_oss');

        // 获取表单上传文件
        $files = request()->file($img);

        if(empty($files)){
            return 405;//没有传参
        }

        $array=array();

        $arrays=array();

        $ext=['pdf','word','excel','txt','doc','mp4','png','jpg','jpeg'];

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $files->validate(['size'=>'10485760','ext'=>$ext])->move(ROOT_PATH . 'public' . DS . 'uploads/api');

        if(empty($info)){
            // 上传失败获取错误信息
            //return $file->getError();
            return 403;
        }else{
            $array[]=$info->getSaveName();

            $arrays[]=$info->getPathname();
        }

        $bucket = $config['Bucket'];//存储空间名称

        for($i=0;$i<count($array);$i++){

            $fileName[] = 'api/'.$array[$i];//文件名称

            $path[]     =$arrays[$i];//本地文件路径

            $enen[]=uploadFile($bucket, $fileName[$i], $path[$i]);//上传文件到服务器
        }

        return $array;
    }

}