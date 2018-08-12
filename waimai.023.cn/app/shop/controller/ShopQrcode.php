<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/8
 * Time: 15:21
 */
namespace app\shop\controller;

use think\Hook;
use think\Loader;
use think\Request;
use think\Db;
use think\Controller;
//        require_once '../extend/phpqrcode/qrlib.php';

class ShopQrcode extends Index
{
    /**
     * 生成商家地址链接二维码
     */
    public function MakeQRcode($url)
    {
        //引入QRcode文件
        header('Content-Type: image/png');
        Request::instance()->header('Content-Type: image/png');
        Loader::import('qrlib',__DIR__."/../../../extend/phpqrcode",".php");
        // 1. 生成原始的二维码(生成图片文件)
        $value = $url;                  //二维码内容
        //$errorCorrectionLevel = 'L';    //容错级别
        $matrixPointSize = 10;           //生成图片大小
        //生成二维码图片
        $filename = 'uploads/qrcode/' . microtime() . '.png';
        \QRcode::png($value, $filename, QR_ECLEVEL_M, $matrixPointSize, 2);

        $QR = $filename;                //已经生成的原始二维码图片文件
        $QR = imagecreatefromstring(file_get_contents($QR));

        //输出图片
        imagepng($QR, 'qrcode.png');
        imagedestroy($QR);
        return'http://'. $_SERVER['HTTP_HOST'].'/public/'.$filename;

    }
}