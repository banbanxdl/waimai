<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
if (!function_exists('message')) {
    /**
     * @param $info     数据列表
     * @param $content  返回的状态信息
     * @param $status   状态码
     * @return mixed
     */
    function message($info, $content, $status)
    {
        $data = [];
        $sta = [];
        if (is_object($info)) {
            $data['data'] = $info;
        }
        if (is_array($info) && !empty($info)) {
            foreach ($info as $k => $val) {
                $key[] = $k;
            }
            //检查数组中 是否有data键值
            if (in_array('data', $key, true)) {
                $data = $info;
            } else {
                $data['data'] = $info;
            }
        }
        if (is_string($info)) {
            $data['data'] = $info;
        }
        if (is_int($info)) {
            $data['data'] = $info;
        }
        if (is_float($info)){
            $data['data'] = $info;
        }
        if (empty($info)) {
            $data['data'] = $info;
        }
        if ($status == 1) {
            $sta['code'] = 100;       //参数不对
            $sta['message'] = $content;
        } elseif ($status == 2) {
            $sta['code'] = 200;       //成功
            $sta['message'] = $content;
        } elseif ($status == 3) {
            $sta['code'] = 300;       //信息不存在
            $sta['message'] = $content;
        } elseif ($status == 4) {
            $sta['code'] = 404;       //找不到
            $sta['message'] = $content;
        } elseif ($status == 5) {
            $sta['code'] = 500;       //服务器错误
            $sta['message'] = $content;
        }
        $data['status'] = $sta; //组装状态
        return json($data)->send();
    }
}

if (!function_exists('encrmd')) {
    /**
     * @param $string  需要加密的数据类型
     * @return bool|string    哈希算法
     */
    function encrmd($string)
    {
        if (!is_string($string)) {
            return false;
            exit();
        }
        $str = sha1(md5(md5($string) . sha1($string)));
        return $str;
    }
}

if (!function_exists('multi_array_sort')) {
    /*多维数组排序
    $multi_array:多维数组名称
    $sort_key:二维数组的键名
    $sort:排序常量 SORT_ASC || SORT_DESC
    */
    function multi_array_sort($multi_array, $sort_key, $sort = SORT_DESC)
    {
        if (is_array($multi_array)) {

            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    //把要排序的字段放入一个数组中，
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        //对多个数组或多维数组进行排序
        array_multisort($key_array, $sort, $multi_array);

        return $multi_array;
    }
}

if (!function_exists('OssUplodas')) {
    /**
     * 图片上传方法
     * @param $img
     * @return array|int
     */
    function OssUplodas($img)
    {

        //$config=config('aliyunoss.aliyun_oss');

        // 获取表单上传文件
        $files = request()->file($img);


        if (empty($files)) {
            return 405;//没有传参
        }

        $array = array();

        // $arrays=array();

        foreach ($files as $file) {

            $ext = ['pdf', 'word', 'excel', 'txt', 'doc', 'mp4', 'png', 'jpg', 'jpeg'];

            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['size' => '10485760', 'ext' => $ext])->move(ROOT_PATH . 'public' . DS . 'uploads/api');

            if (empty($info)) {
                // 上传失败获取错误信息
                //return $file->getError();
                return 403;
            } else {
                $array[] = $info->getSaveName();

                // $arrays[]=$info->getPathname();
            }
        }

        // $bucket = $config['Bucket'];//存储空间名称

        // for($i=0;$i<count($array);$i++){

        //   $fileName[] = 'api/'.$array[$i];//文件名称

        //   $path[]     =$arrays[$i];//本地文件路径

        //   $enen[]=uploadFile($bucket, $fileName[$i], $path[$i]);//上传文件到服务器
        // }

        return $array;
    }
}

if (!function_exists('page_array')) {
    /**
     * 数组分页函数 核心函数 array_slice
     * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
     * $count  每页多少条数据
     * $page  当前第几页
     * $array  查询出来的所有数组
     * order 0 - 不变   1- 反序
     */
    function page_array($count, $page, $array, $order)
    {
        global $countpage; #定全局变量
        $page = (empty($page)) ? '1' : $page; #判断当前页面是否为空 如果为空就表示为第一页面
        $start = ($page - 1) * $count; #计算每次分页的开始位置
        if ($order == 1) {
            $array = array_reverse($array);
        }
        $totals = count($array);
        $countpage = ceil($totals / $count); #计算总页面数
        $pagedata = array();
        $pagedata = array_slice($array, $start, $count);

        return $pagedata; #返回查询数据
    }
}

if (!function_exists('angle')) {
    /**
     * 计算三条边的夹角
     * @param $a 要计算的夹角对应的边长
     * @param $b 边长
     * @param $c 边长
     * @return float 角度
     */
    function angle($a, $b, $c)
    {
        if (is_numeric($a) && is_numeric($a) && is_numeric($c)) {
            $cosA = ($b * $b + $c * $c - $a * $a) / (2 * $b * $c);
            $acos = acos($cosA);
            return rad2deg($acos);
        } else {
            return '参数错误';
        }
    }
}

if (!function_exists('distance')) {
    /**
     *根据经纬度计算距离
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @param $unit
     * @return float
     * echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
     * echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
     * echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";
     */
    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}

if (!function_exists('log_w')) {
    /**
     * 写日志
     * @param $msg
     * @param string $fileName 默认info
     * 调用：log_w(__CLASS__."\t".__FUNCTION__."\t");
     */
    function log_w($msg, $fileName = 'info')
    {
        //设置时区
        date_default_timezone_set('Asia/Chongqing');
        $logFile = sprintf($_SERVER['DOCUMENT_ROOT'] . "/wxlog/waimai_%s/log/xz-%s.%s.log", 'app', $fileName, date('Y-m-d', strtotime("today")));
        // 判断日志有没有达到2g, 如果达到就用不前时间戳重命名
        $dir = sprintf($_SERVER['DOCUMENT_ROOT'] . "/wxlog/waimai_%s/log", 'app');
        directory($dir);
        $flag = is_out_size($logFile);
        if ($flag) {
            // 重命名文件
            $str = date('Y-m-d', strtotime("today")) . '-' . time();
            $newName = sprintf($_SERVER['DOCUMENT_ROOT'] . "/wxlog/waimai_%s/log/xz-%s.%s.log", 'app', $fileName, $str);
            rename($logFile, $newName);
        }
        $hostName = phpversion() < "5.3.0" ? $_SERVER['HOSTNAME'] : gethostname();
        $ip = get_real_ip();
        $fp = fopen($logFile, 'a');
        fwrite($fp, sprintf("%s\tip=%s\t%s\thostname=%s\n", date("H:i:s"), $ip, $msg, $hostName));
        fclose($fp);
    }
}

if (!function_exists('is_out_size')) {
    /**
     *判断日志文件是否超过大小，超过2G返回true ,否则返回false,文件不存在return false
     */
    function is_out_size($logFile)
    {
        $config_size = config('log_size');
        if (!file_exists($logFile)) {
            return false;
        }
        $size = filesize($logFile);
        if ($size < $config_size) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('get_real_ip')) {
    /**
     * 获取当前IP
     * @return bool|string
     */
    function get_real_ip()
    {
        static $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi("^(10|172\.16|192\.168)\.", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        $remote_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
        $ip = $ip ? $ip : $remote_ip;
        return $ip;
    }
}

if (!function_exists('directory')) {
    /**
     * 递归创建目录
     * @param $dir
     * @return bool
     */
    function directory($dir)
    {
        return is_dir($dir) or directory(dirname($dir)) and mkdir($dir, 0775);

    }
}

if (!function_exists('node_merge')) {
    /**
     * 应用公共文件
     * @param $node 节点
     * @param null $access
     * @param int $pid
     * @return array
     */
    function node_merge($node, $access = null, $pid = 0)
    {
        $arr = array();
        foreach ($node as $v) {
            if (is_array($access)) {
                $v['access'] = in_array($v['id'], $access) ? 1 : 0;
            }
            if ($v['pid'] == $pid) {
                $v['child'] = node_merge($node, $access, $v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }
}

if (!function_exists('stringArr')) {
    /**
     * 在字符串后面加元素
     * @param $string
     * @param $str
     * @return string
     */
    function stringArr($string, $str)
    {
        $arr = explode(',', $string);
        array_push($arr, $str);
        $arr = array_unique($arr);
        $str = implode(',', $arr);
        return $str;
    }
}

if (!function_exists('foo')) {
    function foo(&$v, $k, $kname)
    {
        $v = array_combine($kname, array_slice($v, 0, 1));
    }
}

if (!function_exists('arrStr')) {
    /**
     * 二维数组转换为字符串
     * @param $arr   二维数组
     * @param $field  关键字段
     * @return string
     */
    function arrStr($arr, $field)
    {
        $uid = array();
        foreach ($arr as $k => $v) {
            $uid[$v[$field]] = 1;
        }
        $ddd = array_keys($uid);
        $str = implode(',', $ddd);
        return $str;
    }
}

if (!function_exists('randomkeys')) {
    function randomkeys($length)
    {
        $key = '';
        $patterm = '1234567890abcdefghijklmnopqrstuvwxyz';
        for ($i = 0; $i < $length; $i++) {
            $key .= $patterm{mt_rand(0, 35)};
        }
        return $key;
    }
}

if (!function_exists('msectime')) {
    /**
     * 上传文件的文件名命名
     * @return string
     */
    function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000) . randomkeys(3);
        return $msectime;
    }
}

if (!function_exists('get_Sum')) {
    /**
     * 二维数组的和
     * @param $arr   二维数组
     * @param $key   键值
     * @return int   整数
     */
    function get_Sum($arr, $key)
    {
        $sum = 0;
        foreach ($arr as $k => $v) {
            $sum += $v[$key];
        }
        return $sum;
    }
}

if (!function_exists('orderNum')) {
    /**
     * 生成订单号
     * @return string
     */
    function orderNum()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) .
            date('d') . substr(time(), -5) . substr(microtime(), 2, 5) .
            sprintf('%02d', rand(0, 99));
        return $orderSn;
    }
}

if (!function_exists('arraychlist')) {
    /**
     * 二数组 合并 输出重复值
     * @param $arr1
     * @param $arr2
     * @return array
     */
    function arraychlist($arr1, $arr2, $true = false)
    {
        $array = array_merge($arr1, $arr2);       //两数组合并
        $sum = array_unique($array);             //去重复的值
        $num = array_diff_assoc($array, $sum);    //获取重复值
        $info = $true ? $sum : $num;
        return $info;
    }
}

if (!function_exists('getmaxdim')) {
    /**
     * 判断数组为一维或多维
     * @param $vDim
     * @return int
     */
    function getmaxdim($vDim)
    {
        if (!is_array($vDim)) return 0;
        else {
            $max1 = 0;
            foreach ($vDim as $item1) {
                $t1 = getmaxdim($item1);
                if ($t1 > $max1) $max1 = $t1;
            }
            return $max1 + 1;
        }
    }
}

if (!function_exists('getWhere')) {
    /**
     * 生成where条件
     * @param $field
     * @param $value
     * @param string $eve
     * @return mixed
     */
    function getWhere($field, $value, $eve = '')
    {
        if (is_string($field)) {
            $fie = explode(',', $field);
        } elseif (is_array($field)) {
            $fie = $field;
        }
        foreach ($fie as $k => $val) {
            $where[$eve . $val] = $value[$k];
        }
        return $where;
    }
}

if (!function_exists('geTimeOuts')) {
    /**
     * last month time number and this month time number (where)
     * @param string $y
     * @param string $m
     * @return mixed
     */
    function geTimeOuts($y = '', $m = '')
    {
        if (empty($y)) {
            $y = date('Y');
        }
        if (empty($m)) {
            $m = date('m');
        }
        if (($m - 1) == 0) {
            $last_int = strtotime(($y - 1) . '-12-01 00:00:00');
        } else {
            $last_int = strtotime($y . '-' . ($m - 1) . '-01 00:00:00');
        }
        $start_int = strtotime($y . '-' . $m . '-01 00:00:00');
        if (empty($y) && empty($m)) {
            $this_int = time();
        } else {
            if ($m == 12) {
                $this_int = strtotime(($y + 1) . '-' . '01' . '-01 00:00:00');
            } else {
                $this_int = strtotime($y . '-' . ($m + 1) . '-01 00:00:00');
            }
        }
        $time['last_where'] = ['between', [$last_int, $start_int]];
        $time['this_where'] = ['between', [$start_int, $this_int]];
        return $time;
    }
}

if (!function_exists('maopao')) {
    /**
     * 冒泡排序
     * @param $arr
     * @param $field
     * @param bool $op
     * @return mixed
     */
    function maopao($arr, $field, $op = false)
    {
        // 进行第一层遍历
        for ($i = 0, $k = count($arr); $i < $k; $i++) {
            // 进行第二层遍历 将数组中每一个元素都与外层元素比较
            // 这里的i+1意思是外层遍历当前元素往后的
            for ($j = $i + 1; $j < $k; $j++) {
                if ($op) {
                    // 内外层两个数比较
                    if ($arr[$i][$field] < $arr[$j][$field]) {
                        // 先把其中一个数组赋值给临时变量
                        $temp = $arr[$j];
                        // 交换位置
                        $arr[$j] = $arr[$i];
                        // 再从临时变量中赋值回来
                        $arr[$i] = $temp;
                    }
                } else {
                    // 内外层两个数比较
                    if ($arr[$i][$field] > $arr[$j][$field]) {
                        // 先把其中一个数组赋值给临时变量
                        $temp = $arr[$j];
                        // 交换位置
                        $arr[$j] = $arr[$i];
                        // 再从临时变量中赋值回来
                        $arr[$i] = $temp;
                    }
                }
            }
        }
        // 返回排序后的数组
        return $arr;
    }
}

if (!function_exists('GetDistances')) {
    /**
     * 获取2点之间的距离
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float|int
     */
    function GetDistances($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371.393; //approximate radius of earth in meters(km)

        /*
        Convert these degrees to radians
        to work with the formula
        */

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        /*
        Using the
        Haversine formula
        http://en.wikipedia.org/wiki/Haversine_formula
        calculate the distance
        */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return number_format($calculatedDistance,2,'.','');  // (km)
    }
}

if (!function_exists('new_oss')) {
    /**
     * 实例化阿里云OSS
     * @return object 实例化得到的对象
     * @return 此步作为共用对象，可提供给多个模块统一调用
     */
    function new_oss()
    {
        //获取配置项，并赋值给对象$config
        $config = config('aliyun_oss');
        //实例化OSS
        $oss = new \OSS\OssClient($config['KeyId'], $config['KeySecret'], $config['Endpoint']);

        return $oss;
    }
}

if (!function_exists('uploadFile')) {
    /**
     * 上传指定的本地文件内容
     *
     * @param OssClient $ossClient OSSClient实例
     * @param string $bucket 存储空间名称
     * @param string $object 上传的文件名称
     * @param string $Path 本地文件路径
     * @return null
     */
    function uploadFile($bucket, $object, $Path)
    {
        //try 要执行的代码,如果代码执行过程中某一条语句发生异常,则程序直接跳转到CATCH块中,由$e收集错误信息和显示
        try {
            //没忘吧，new_oss()是我们上一步所写的自定义函数
            $ossClient = new_oss();
            //uploadFile的上传方法
            $ossClient->uploadFile($bucket, $object, $Path);
        } catch (OssException $e) {
            //如果出错这里返回报错信息
            return $e->getMessage();
        }
        //否则，完成上传操作
        return true;
    }
}

if (!function_exists('GetRange')) {
    /**
     * 查找一定范围内的经纬度值
     * 传入值：纬度  经度  查找半径(m)
     * 返回值：最小纬度、经度，最大纬度、经度
     * @param $lat
     * @param $lon
     * @param $raidus
     * @return mixed
     */
    function GetRange($lat, $lon, $raidus)
    {
        $PI = M_PI;                // 圆周率
        $EARTH_RADIUS = 6370856;      // 地球半径
        $RAD = M_PI / 180.0;        // 弧度

        $latitude = $lat;  // 维度
        $longitude = $lon; // 经度

        $degree = (24901 * 1609) / 360.0;
        $raidusMile = $raidus;

        $dpmLat = 1 / $degree;
        $radiusLat = $dpmLat * $raidusMile;
        $minLat = $latitude - $radiusLat;
        $maxLat = $latitude + $radiusLat;

        $mpdLng = $degree * cos($latitude * ($PI / 180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = $dpmLng * $raidusMile;
        $minLng = $longitude - $radiusLng;
        $maxLng = $longitude + $radiusLng;
        $result['minwt'] = $minLat;
        $result['minlt'] = $minLng;
        $result['maxwt'] = $maxLat;
        $result['maxlt'] = $maxLng;
        return $result;
    }
}

if (!function_exists('rand_account')) {
    /**
     * 随机生成账号
     * @return string
     */
    function rand_account($length=6)
    {
        $pattern = 'ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $num='0123456789';
        $key="";
        for($i=0;$i<$length;$i++)
        {
            $key .= chr(rand(65,90));    //生成php随机数
        }
        return $key;
    }
}
if (!function_exists('rand_pwd')) {
    /**
     * 随机生成密码
     * @return string
     */
    function rand_pwd($length=8)
    {
        $pattern = '1234567890abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key="";
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,35)};    //生成php随机数
        }
        return $key;
    }
}
if (!function_exists('seCount')) {
    /**
     * 查询一个值在二维数组出现的次数
     *
     */
    function seCount($key,$date,$time,$num){
        foreach ($key as $item) {
            if ($item[$time] == $date) {
                $num+=1;
            }
        }
        return $num;
    }
}
if (!function_exists('seTimeCount')) {
    /**
     * 查询一个值在一段时间内二维数组出现的次数
     *
     */
    function seTimeCount($key,$date0,$date2,$time,$num){
        foreach ($key as $item) {
            if ($item[$time] >= $date0 && $item[$time] < $date2) {
                $num+=1;
            }
        }
        return $num;
    }
}
if (!function_exists('seShopCount')) {
    /**
     * 查询一个值在二维数组出现的次数(求有多少店铺在某一时间段出现)
     *
     */
    function seShopCount($key,$date,$time,$num){
        foreach ($key as $item) {
            if ($item[$time] == $date) {
                $num=count(array_count_values(array_column($key,"shop_id")));
            }
        }
        return $num;
    }
}

if (!function_exists('isPointInPolygon')){

    /**
     * 电子围栏：根据经纬度判断一点是否在不规则多边形区域内
     * 不规则多边形：奇内偶外
     * @param $lon 经度 string
     * @param $lat  维度 string
     * @param $coords  不规则多边形的点 json字符串
     * @return int
     */
    function isArea( $lon, $lat, $coords ){
        $wn = 0;
        $shift = false;
        if( $coords[0]['lat'] > $lat ) {
            $shift = true;
        }

        for( $i = 1; $i<count( $coords ); $i++ ){
            $shiftp = $shift;
            $shift = $coords[$i]['lat'] > $lat;

            if( $shiftp != $shift ) {
                $n = ( $shiftp ? 1 : 0 ) - ( $shift ? 1 : 0 );
                if( $n * (
                        ( $coords[$i-1]['lng'] - $lon ) * ( $coords[$i]['lat'] - $lat ) -
                        ( $coords[$i-1]['lat'] - $lat ) * ( $coords[$i]['lng'] - $lon )
                    )
                    > 0
                ) {
                    $wn += $n;
                }
            }
        }
        $n = $wn%2;
        if ($n === 0){
            return true;
        }else{
            return false;
        }
    }
}


