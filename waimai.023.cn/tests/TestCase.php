<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------


class TestCase extends \think\testing\TestCase
{
    protected $baseUrl = 'http://localhost';
    public function test(){
        function &abc($n){
            $arr=array('aaa','bbb','ccc');
            return $arr[$n];  //将数组的第二个元素返回
        }
        $tem=&abc(1); //这里只能得到数组的第二个元素的引用,因为只返回了第二个元素
        $tem='ddd';  //这里将数组的第二个元素的值改变成了'ddd'
    }
}