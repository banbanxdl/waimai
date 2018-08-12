<?php
/**
 * 行政区域划分获取省市区(县)
 */
namespace app\api\model;
use think\Model;
use think\Db;


class Geograph extends Model
{
    //***************************
    //  获取省份数据接口
    //***************************
    public function get_Province(){
        //所有省份
        $list = db::name("region")->where('parent_id=1')->field('id,region_name as name')->select();
        return $list;
    }

    //***************************
    //  获取城市数据接口
    //***************************
    public function get_City($province){
        if (!$province){
            echo json_encode(array('status'=>0,'err'=>'请选择省份.'.__LINE__));
            exit();
        }
        $id=db::name('region')->where('parent_id='.intval($province))->field('id')->find();
        $city = db::name('region')->where('parent_id='.$province .' or '. 'id='.($id['id']+1))->field('id,region_name as name')->select();

        return array('status'=>1,'city_list'=>$city,'city'=>intval($province));
    }

    //***************************
    //  获取区域数据接口
    //***************************
    public function get_Area($city){
        if (!$city){
            echo json_encode(array('status'=>0,'err'=>'请选择城市.'.__LINE__));
            exit();
        }

        //所有省份
        $area = db::name('region')->where('parent_id='.intval($city))->field('id,region_name as name')->select();
        return array('status'=>1,'area_list'=>$area,'city'=>intval($city));
    }
}