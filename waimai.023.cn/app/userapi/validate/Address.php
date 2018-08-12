<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 9:48
 */

namespace app\userapi\validate;


use think\Validate;

class Address extends Validate
{
    protected $rule = [
        'name|名字' => 'require',
        'sex|性别' => 'require|number',
        'phone|手机号' => 'require|number',
        'address|地址' => 'require',
        'building_card|楼牌号' => 'require',
        'label|标签' => 'require|number',
        'user_id|用户id' => 'require|number',
        'jd|经度' => 'require',
        'wd|纬度' => 'require',
        'sheng|省' => 'require',
        'shi|市' => 'require',
        'qu|区/县' => 'require',
    ];

    protected $scene = [
        'AddList'=>['name','sex','phone','address','building_card','label','user_id','jd','wd','sheng','shi','qu'],
    ];

}