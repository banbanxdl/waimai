<?php
/**
 * 行政区域划分获取省市区(县)
 */
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;
use app\api\model\Geograph AS M_geograph;

class Geograph extends Controller
{

    //***************************
    //  获取省份数据接口
    //***************************
    public function getProvince(){
        //所有省份
        $Mgeograph=new M_geograph;
        $list=$Mgeograph->get_Province();
        $return=array('error_code'=>0,'data'=>$list);
        return message($return,'获取信息成功',2);
    }

    //***************************
    //  获取城市数据接口
    //***************************
    public function getCity(){
        $province=intval($_REQUEST['province']);
        if (!$province){
            echo json_encode(array('status'=>0,'err'=>'请选择省份.'.__LINE__));
            exit();
        }
        //所有省份
        $Mgeograph=new M_geograph;
        $list=$Mgeograph->get_City($province);
        $return=array('error_code'=>0,'data'=>$list);
        return message($return,'获取信息成功',2);
    }

    //***************************
    //  获取区域数据接口
    //***************************
    public function getArea(){
        $city=intval($_REQUEST['city']);
        if (!$city){
            echo json_encode(array('status'=>0,'err'=>'请选择城市.'.__LINE__));
            exit();
        }
        //所有省份
        $Mgeograph=new M_geograph;
        $list=$Mgeograph->get_Area($city);
        $return=array('error_code'=>0,'data'=>$list);
        return message($return,'获取信息成功',2);
    }
}