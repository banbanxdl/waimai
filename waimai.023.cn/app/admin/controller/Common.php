<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 14:19
 */

namespace app\admin\controller;


use app\common\controller\Core;
use think\Loader;
use think\Request;
use app\common\model\Shops;
use auth\Qrcode;


/**
 * 公共方法
 * Class Common
 * @package app\admin\controller
 */
class Common extends Core
{

    protected static $temp;
    /**
     * 单例模式
     * @return Common
     */
    public static function create()
    {
        if (self::$temp){
            return self::$temp;
        }else{
            self::$temp = new Common();
            return self::$temp;
        }
    }

    /**
     * 二维码邮寄 生成商家 二维码
     * @param $id
     * @return string
     * @throws \think\exception\DbException
     */
    public function qrcodeimg($id)
    {
        header('Content-Type: image/png');
        Request::instance()->header('Content-Type: image/png');
        Loader::import('qrlib',EXTEND_PATH.'phpqrcode','.php');

        //获取商家信息
        $shop = Shops::get($id);
        //二维码内容
        $img = 'http://ht.yhbapp.com/api.php/Payment/identify/id/'.$id;
        //二维码图片名称
        $img_name = 'public/uploads/qrimg/'.md5($img).'.png';
        //生成二维码
        \QRcode::png($img, $img_name, QR_ECLEVEL_M, '10', '5');
        $logo = $shop['imgurl'];
        //将头像添加到二维码上
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($img_name));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
        }
        //输出图片
        $image = 'public/uploads/qrimg/'.md5($shop['imgurl']).'.png';
        imagepng($QR, $image);

        $BK = imagecreatefromstring(file_get_contents('442112.jpg'));
        $co = imagecreatefromstring(file_get_contents($image));
        $BK_width = imagesx($BK);//图片宽度
        $BK_height = imagesy($BK);//图片高度
        $co_width = imagesx($co);//二维码o图片宽度
        $co_height = imagesy($co);//二维码图片高度
        $co_qr_width = $BK_width / 2;
        $co_qr_height = $BK_height / 2;
        $scale_nu = $co_width/$co_qr_width;
        $co_qr_height = $co_height/$scale_nu;
        $from_width_nu = ($BK_width - $co_qr_width) / 2;
        $from_height_nu = ($BK_height - $co_qr_height) / 2;
        //重新组合图片并调整大小
        imagecopyresampled($BK, $co, $from_width_nu, $from_height_nu, 0, 0, $co_qr_width,
            $co_qr_height, $co_width, $co_height);

        $imgaes = 'public/uploads/qrimg/'.md5($shop['imgurl']).'.png';
        imagepng($BK, $imgaes);

        $nameimg = $this->createtext($shop['name']);

        $BKS = imagecreatefromstring(file_get_contents($imgaes));
        $name = imagecreatefromstring(file_get_contents($nameimg));
        $BKS_width = imagesx($BKS);//图片宽度
        $BKS_height = imagesy($BKS);//图片高度
        $name_width = imagesx($name);//二维码o图片宽度
        $name_height = imagesy($name);//二维码图片高度
        $name_qr_width = $BKS_width / 2;
        $name_qr_height = $BKS_height / 2;
        $scale_nu_bk = $name_width/$name_qr_width;
        $name_qr_height = $name_height/$scale_nu_bk;
        $from_width_name = ($BKS_width - $name_qr_width) / 2;
        $from_height_name = (($BKS_height - $name_qr_height) / 4 ) + 80;
        //重新组合图片并调整大小
        imagecopyresampled($BKS, $name, $from_width_name, $from_height_name, 0, 0, $name_qr_width,
            $name_qr_height, $name_width, $name_height);

        $imgaes = 'public/uploads/qrimg/'.md5($shop['imgurl'].rand(1,99999999)).'.png';
        imagepng($BKS, $imgaes);

        return $imgaes;
    }

    /**
     * 生成会员推广海报
     * @param $id
     * @param $phone
     * @param $city_id
     * @param $text
     * @param $down_img
     * @return string
     */
    public function userAdvPro($id,$phone,$city_id,$text,$down_img = '')
    {
        header('Content-Type: image/png');
        Request::instance()->header('Content-Type: image/png');
        Loader::import('qrlib',EXTEND_PATH.'phpqrcode','.php');

        //二维码内容
        $qr_img_con = 'http://ht.yhbapp.com/api.php/Login/web_register?pid='.$id.'&tel='.$phone.'&city_id='.$city_id;
        //二维码图片名称
        $qr_img_name = 'public/uploads/qrimg/'.md5($qr_img_con).'.png';
        //生成二维码
        \QRcode::png($qr_img_con, $qr_img_name, QR_ECLEVEL_M, '10', '5');
        //生成文字图片
        $text_img=$this->textImgTo($text,24);
        //获取底图
        if (empty($down_img)){
            $down_img='qrimg11111245.jpg';
        }

        $BK = imagecreatefromstring(file_get_contents($down_img));
        $QR = imagecreatefromstring(file_get_contents($qr_img_name));
        //获取底图的 宽和高
        $BK_width = imagesx($BK);
        $BK_height = imagesy($BK);
        //获取二维码的 宽和高
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);
        //计算二维码的x轴 和 y轴 坐标
        $QR_x = ($BK_width - 155)/2;
        $QR_y = 233;
        //将二维码合成到底图上
        imagecopyresampled($BK,$QR,$QR_x,$QR_y,0,0,155,155,$QR_width,$QR_height);

        $IMG = 'public/uploads/qrimg/'.md5($qr_img_con).'.png';
        imagepng($BK,$IMG);

        $BKS = imagecreatefromstring(file_get_contents($IMG));
        $TXT = imagecreatefromstring(file_get_contents($text_img));
        //获取底图的 宽和高
        $BKS_width = imagesx($BKS);
        $BKS_height = imagesy($BKS);
        //获取文字图片 宽和高
        $TXT_width = imagesx($TXT);
        $TXT_height = imagesy($TXT);
        //计算文字的x轴 和 y轴 坐标
        $TXT_x = 228;
        $TXT_y = 836;
        //将文字合成到底图上
        imagecopyresampled($BKS,$TXT,$TXT_x,$TXT_y,0,0,$TXT_width,$TXT_height,$TXT_width,$TXT_height);

        $IMG = 'public/uploads/qrimg/'.md5($qr_img_con.rand(1,99999)).'.png';
        imagepng($BKS,$IMG);
        return $IMG;

    }

    /**
     * 生成透明的文字图片
     * @param string $text
     * @param int $size
     * @param array $text_color
     * @return bool|string
     */
    public function textImgTo($text = '测试',$size = 40,$text_color = [255,255,255])
    {
        header("content-type:image/png");
        $font = '/public/static/admin/fonts/simhei.ttf';
        //获取文字的像素
        $text_box = imagettfbbox($size, 0, $font, $text);
        if( !$text_box )
            return false;
        $min_x = min( array($text_box[0], $text_box[2], $text_box[4], $text_box[6]) );
        $max_x = max( array($text_box[0], $text_box[2], $text_box[4], $text_box[6]) );
        $min_y = min( array($text_box[1], $text_box[3], $text_box[5], $text_box[7]) );
        $max_y = max( array($text_box[1], $text_box[3], $text_box[5], $text_box[7]) );
        $text_width  = ( $max_x - $min_x );
        $text_height = ( $max_y - $min_y );

        $block = imagecreatetruecolor($text_width,$text_height);//建立一个画板
        $bg = imagecolorallocatealpha($block , 0 , 0 , 0 , 127);//拾取一个完全透明的颜色，不要用imagecolorallocate拾色
        $color = imagecolorallocate($block,$text_color[0],$text_color[1],$text_color[2]); //字体拾色
        imagealphablending($block , false);//关闭混合模式，以便透明颜色能覆盖原画板
        imagefill($block , 0 , 0 , $bg);//填充
        imagefttext($block,$size,0,0,-$text_box[7],$color,$font,$text);
        imagesavealpha($block , true);//设置保存PNG时保留透明通道信息
        $img = 'public/uploads/qrtext/'.md5(rand(1,999999)).'.png';
        imagepng($block,$img);//生成图片
        return $img;
    }

    /**
     * 生成文字图片
     * @param string $text
     * @return bool|string
     */
    public function createtext($text = "优慧宝专卖店")
    {
        //字体大小
        $size = 40;
        //字体类型，本例为宋体
        $font ="/public/static/admin/fonts/simhei.ttf";
        //显示的文字
        $title = "收款商户：";
        $title .= $text;
        //创建一个长为500高为80的空白图片
        $img = imagecreate(690, 80);
        //给图片分配颜色
        imagecolorallocate($img, 255, 255, 255);
        //设置字体颜色
        $black = imagecolorallocate($img, 0, 0, 0);
        //获取文字的像素
        $box   = imagettfbbox($size, 0, $font, $title);
        if( !$box )
            return false;
        $min_x = min( array($box[0], $box[2], $box[4], $box[6]) );
        $max_x = max( array($box[0], $box[2], $box[4], $box[6]) );
        $min_y = min( array($box[1], $box[3], $box[5], $box[7]) );
        $max_y = max( array($box[1], $box[3], $box[5], $box[7]) );
        $width  = ( $max_x - $min_x );
        $height = ( $max_y - $min_y );
        if ($width>690){
            $width_img = 0;
        }else{
            $width_img = (690-$width)/2;
        }
        //将ttf文字写到图片中
        imagettftext($img, $size, 0, $width_img, 50, $black, $font, $title);
        //发送头信息
        header('Content-Type: image/gif');
        //输出图片
        $name = 'public/uploads/qrtext/'.md5('sdfsdf1sd12f2s').'.gif';
        imagegif($img,$name);
        return $name;
    }

    /**
     *
     */
    public function jpgraphtj(Request $request)
    {
        Loader::import('jpgraph',EXTEND_PATH.'jpgraph','.php');
        Loader::import('jpgraph_line',EXTEND_PATH.'jpgraph','.php');
        //创建画布
        $graph = new \Graph(600,400);
        //设置横纵坐标的样式
        /**
         * lin 直线
         * log 对数
         * text 文本
         * int 数字
         */
        $graph->SetScale('textint');
        // 设置标题内容
        $graph->title->Set('this is text');
        $array = [1=>45,2=>12,3=>26,4=>32,5=>32,6=>54,7=>15,8=>25,9=>66,10=>89,11=>18,12=>57];
        //生成统计图
        $linePlot = new \LinePlot($array);
        //添加到画布
        $graph->Add($linePlot);
        //设置图例
        $linePlot->SetLegend('tuli');

        $time = date('Ymd',time());
        $path_dir = 'public/uploads/count/'.$time;
        if (!file_exists($path_dir))
            mkdir($path_dir);
        $path = $path_dir.'/'.md5(rand(1000,9999)).'.png';

        $graph->Stroke($path);
    }

    /**
     * 生成统计图
     * @param string $tab 表名称
     * @param int $id 用户的id
     * @param array $x X轴坐标参数
     * @param string $field 计算和的字段
     * @return string
     */
    public function imgcleod($array1 = [-5,17,55,24],$x = array('A','B','C','D'),$title = 'Filled Y-grid',$leg = 'Line 1')
    {
        Loader::import('jpgraph',EXTEND_PATH.'jpgraph','.php');
        Loader::import('jpgraph_line',EXTEND_PATH.'jpgraph','.php');

        // Setup the graph
        $graph = new \Graph(800,600);
        $graph->SetScale("textlin");

        $theme_class=new \UniversalTheme;

        $graph->SetTheme($theme_class);
        $graph->img->SetAntiAliasing(false);
        $graph->title->Set($title);
        $graph->SetBox(false);

        $graph->img->SetAntiAliasing();

        $graph->yaxis->HideZeroLabel();
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);

        $graph->xgrid->Show();
        $graph->xgrid->SetLineStyle("solid");
        $graph->xaxis->SetTickLabels($x);
        $graph->xgrid->SetColor('#E3E3E3');

        $line_plot = new \LinePlot($array1);
        $graph->Add($line_plot);
        $line_plot->SetColor('#'.randColor());
        $line_plot->SetLegend($leg);

        $graph->legend->SetFrameWeight(1);

        $time = date('Ymd',time());
        $path_dir = 'public/uploads/count/'.$time;
        if (!file_exists($path_dir))
            mkdir($path_dir);
        $path = $path_dir.'/'.md5(rand(1000,9999)).'.png';
        // Output line
        $graph->Stroke($path);
        return $path;
    }

    /**
     * 根据地区id查询出该区域相应id
     */
    public function shopColnum($id,$tp = 1)
    {
        if ($tp == 1){
            $id=$id;
            $model = new Shops;
            $where['area_id'] = $id;
        }elseif ($tp == 2){ //获取代理商id
            $model = new \app\common\model\Agent;
            $where['area_id'] = $id;
        }
        return $model->where($where)->column('id');
    }

    /**
     * 支付宝转账
     * 转账给单个账户
     */
    public function alipayShift($data = [])
    {
        Loader::import('AopClient',EXTEND_PATH.'alipay/aop','.php');
        Loader::import('AlipayFundTransToaccountTransferRequest',EXTEND_PATH.'alipay/aop/request','.php');

        $aop = new \AopClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = config('aliyun.alipay_appid');
        $aop->rsaPrivateKey = config('aliyun.alipay_rsaPrivateKey');
        $aop->alipayrsaPublicKey = config('aliyun.alipay_alipayrsaPublicKey');
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayFundTransToaccountTransferRequest();
        $data["out_biz_no"] = orderNum();  // 订单号
        $data["payee_type"] = "ALIPAY_LOGONID";  // 	收款方账户类型
        $data["payee_account"] = "460444157@qq.com";  //收款方账户
        $data["amount"] = "0.1";  //转账金额
//        $data["payer_show_name"] = "福建易鹏电子商务有限公司";  //	付款方姓名
//        $data["payee_real_name"] = "万小明";  //收款方真实姓名
//        $data["remark"] = "";  //转账备注
        $data = json_encode($data);
        $request->setBizContent($data);
//        halt($aop);
        $result = $aop->execute ($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }

}