<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5
 * Time: 10:09
 */

namespace app\userapi\controller;


use think\Request;

class Upload extends Api
{

    /**
     * 上传一张或多张图片并返回图片地址
     * @param Request $request
     * @return mixed
     */
    public function addImages(Request $request)
    {
        if ($request->isPost()){
            $img_name = $request->post('name');
            $upload = new \app\common\controller\Upload();
            $list = $upload->upload($img_name);
            if (is_array($list)){
                return message($list,'上传成功',2);
            }elseif (is_string()){
                return message('',$list,3);
            }
        }
    }

}