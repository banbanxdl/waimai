<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 18:10
 */

namespace app\common\controller;


use think\exception\HttpResponseException;
use think\Request;
use think\Response;
use traits\controller\Jump;

class AdminCommon
{
    use Jump;
    protected static $temp;

    public static function __callStatic($name,$arguments)
    {
        $name = strtolower($name);//将字母转换为小写
        if ($name == 'create'){
            if (self::$temp){
                return self::$temp;
            }else{
                self::$temp = new AdminCommon();
                return self::$temp;
            }
        }
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @param $data         要返回的数据
     * @param string $msg   返回的 code
     * @param int $code     提示信息
     * @param string $type  返回数据格式
     * @param array $header 发送的 Header 信息
     */
    public function ajaxResult($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        if ($code === 0){
            $true = false;
        }else{
            $true = true;
        }
        $result = [
            'code'    => $code,
            'data'    => $data,
            'success' => $true,
            'time'    => Request::instance()->server('REQUEST_TIME'),
            'msg'     => $msg
        ];
        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 多维数组降维
     * @param $array 多维数组
     * @return array
     */
    public function arrayFall($array)
    {
        if (!empty($array)) {
            foreach ($array as $v) {
                $val = json_encode($v);//
                $list[] = $val;
            }
            return $list;
        }else{
            return [];
        }
    }

    /**
     * 将一维数组展开生成多维数组
     * @param $array
     * @return array
     */
    public function arraySpread($array)
    {
        if (!empty($array)) {
            foreach ($array as $v) {
                $val = json_decode($v);
                $list[] = $val;
            }
            return $list;
        }else{
            return [];
        }
    }


    /**
     * 多维数组去重
     * @param $array
     * @return array
     */
    public function assocUnique($array)
    {
        $list = $this->arrayFall($array);
        $temp = array_unique($list);
        $tree = $this->arraySpread($temp);
        return $tree;
    }

    /**
     * 比较两个数组取出差集
     * @param $array1
     * @param $array2
     * @return array
     */
    public function arrayDiffAssoc($array1,$array2)
    {
        $arr1 = $this->arrayFall($array1);//降维
        $arr2 = $this->arrayFall($array2);//
        $list = array_diff_assoc($arr1,$arr2);
        $tree = $this->arraySpread($list);//展开
        return $tree;
    }

}