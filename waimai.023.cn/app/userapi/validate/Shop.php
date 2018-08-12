<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/7
 * Time: 10:03
 */

namespace app\userapi\validate;


use think\Validate;

class Shop extends Validate
{
    protected $rule = [
        'id|用户id'=>'require|number',
        'lt|经度'=>'require|float',
        'wt|维度'=>'require|float',
        'sheng|省'=>'require|chs',
        'shi|市'=>'require|chs',
        'qu|区'=>'require|chs',
        'search_con|搜索内容' => 'require|max:100',
        'type|排序条件'=>'require|number|>:0',
        'pa|页码'=>'number|>=:1',
        'ge|每页数量'=>'number|>=:10',
        'tp|商铺分类id'=>'number|>=:0',
    ];

    protected $scene = [
        'selist' => ['id','lt','wt','sheng','shi','qu','type','pa','ge','tp'],
        'search' => ['id','sheng','shi','qu','search_con'],
        'recomment' => ['sheng','shi','qu'],
    ];

}