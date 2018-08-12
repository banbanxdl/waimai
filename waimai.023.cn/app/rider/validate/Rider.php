<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 16:48
 */

namespace app\rider\validate;


use think\Validate;

class Rider extends Validate
{
    protected         $rule =   [
        'name|姓名'          => 'require|max:25',
        'age|年龄'           => 'number|between:18,60',
        'id_card|身份证号码'       =>'require|length:18',
        'city_id|城市ID'       => 'require|number',
        'sheng|省份ID'         => 'require|number',
        'shi|城市ID'           => 'require|number',
        'qu|所属区县ID'            => 'require|number',
        'hold_justimg|手持身份证正面照' =>'require',
        'driver_license_img|手持身份证背面照' =>'require',
        'vice_page_img|驾驶证'  =>'require',
        'hold_backimg|驾驶证副页' =>'require',
        'health_img|健康证'  =>'require',
        'phone|手机号'           =>'require|max:11',
    ];

    protected $message  =   [
        'name.require' => '姓名不能为空',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在18-60之间',
        'id_card.length'=> '身份证号码长度必须在18位',

    ];

}