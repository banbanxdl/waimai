<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/16
 * Time: 20:34
 */

namespace app\common\controller;


class Auth
{
    /**
     * 密码加密
     * 哈希算法
     * @param $string 需要加密的字符串
     * @return bool|string
     */
    public static function encrmd($string)
    {
        if (!is_string($string)) {
            return '不是字符';
        }
        $str = sha1(md5(md5($string).sha1($string)));
        return $str;
    }

}