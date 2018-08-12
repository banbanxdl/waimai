<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;
use OSS\Core\OssException;
use Qsnh\think\Upload\Upload;
use think\config;
class Rider extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        header('Access-Control-Allow-Origin:*');
    }

    function gmt_iso8601($time) {
//        $dtStr = date($time);
//        print_r($dtStr);
//        echo '<br>';
//        $mydatetime = new \DateTime($dtStr);
        $expiration = date("Y-m-d\TH:i:sO",$time);
 //       print_r($expiration);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }
public function upLoadtest(Request $request){
print_r(CONF_PATH);
//    header('content-type:application:json;charset=utf8');
//    header('Access-Control-Allow-Origin:*');
//    header('Access-Control-Allow-Methods:POST');
//    header('Access-Control-Allow-Headers:x-requested-with,content-type');
    $c =Config('upload');
    print_r($c);
    $conf = $c['aliyun'];
    $id=$conf['access_key_id'];//'LTAI5pa2ecubxXht';
    $key=$conf['access_key_secret'];// 'WxynMtiEnJUKsCFGELFUlgWOkAmGr7';
    $host = 'http://'.$conf['remote_url'];//'http://heziwaimai.oss-cn-shanghai.aliyuncs.com';
    $now = time();
    $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
    $end = $now + $expire;
    $expiration = $this->gmt_iso8601($end);
print_r($expiration);die;
    $dir = 'waimai_img/'.date('Y-m-d').'/';

    //最大文件大小.用户可以自己设置
    $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
    $conditions[] = $condition;

    //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
    $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
    $conditions[] = $start;


    $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
   // echo json_encode($arr);die;
    //return;
    $policy = json_encode($arr);
    $base64_policy = base64_encode($policy);
    $string_to_sign = $base64_policy;
    $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

    $response = array();
    $response['accessid'] = $id;
    $response['host'] = $host;
    $response['policy'] = $base64_policy;
    $response['signature'] = $signature;
    $response['expire'] = $end;
    //这个参数是设置用户上传指定的前缀
    $response['dir'] = $dir;
    echo json_encode($response);
}
    public function upLoad(Request $request){

        $upload = new Upload(config('upload'));

// $result = $upload->upload('avatar', '123.jpg');
        $result = $upload->upload('file'); // first parameter is folder path; second parameter is custom filename(default: null)

        if (!$result) {
            $this->error($upload->getError());
        }

        halt($result);

    }
    /**
     * 获取骑手信息
     * @param Request $request
     * @return \think\response\Json
     */
    public function getRider(Request $request)
    {
        header('Access-Control-Allow-Origin:*');
        try {
            $status = $request->param('status');
            $pow_takeaway = $request->param('pow_takeaway');
            $pow_legs = $request->param('pow_legs');
            $grade_num = $request->param('grade_num');
            $where = '';
            if ($status != '') {
                $where = ($where == '') ? ' r.status=' . $status : ' and r.status=' . $status;
            }
            if ($pow_takeaway != '') {
                $where = ($where == '') ? ' r.pow_takeaway=' . $pow_takeaway : $where . ' and r.pow_takeaway=' . $pow_takeaway;
            }
            if ($pow_legs != '') {
                $where = ($where == '') ? ' r.pow_legs=' . $pow_legs : $where . ' and r.pow_legs = ' . $pow_legs;
            }
            if ($grade_num != '') {
                $where = ($where == '') ? ' r.grade_num=' . $grade_num : $where . ' and r.grade_num = ' . $grade_num;
            }
            $where = ($where == '') ? '' : ' where ' . $where;
            
            $data = Db::query("select r.id,r.img,re.region_name,r.phone,r.status,i.sex,i.age,g.describe,r.money,date_format(from_unixtime(r.add_time),'%Y-%m-%d %H:%i:%s') as add_time,r.pow_takeaway,r.pow_legs from db_rider as r LEFT JOIN db_rider_grade as g on r.grade_num=g.id LEFT JOIN db_rider_info as i on r.id=i.rider_id LEFT JOIN db_region re on i.shi=re.id " . $where);

            $return = [
                'data' => $data,
                "status" => [
                    "code" => 200,
                    "message" => "获取信息成功"]
            ];
            return json($return);
        } catch (\Exception $e) {
            $err_code=$e->getCode();
            $err_msg=$e->getMessage();
            $r=[
                'err_code'=>$err_code,
                'err_msg'=>$err_msg
            ];
            return json($r);
        }
    }

    /**
     * 设置骑手参数
     * @param Request $request
     * $param
     * status 骑手状态 1 接单 2 未接单
     * pow_takeaway 外卖权限  1有 0无
     * pow_legs 跑腿权限  1有 0无
     * @return array
     */
    public function setRider(Request $request){
        header('Access-Control-Allow-Origin:*');
            $param = $request->param('param');
            $rider_id = $request->param('rider_id');
            $param_num =  $request->param('param_num');
            if(isset($param) && isset($rider_id) && isset($param_num)){
                Db::name('rider')->where(['id'=>$rider_id])->update([$param=>$param_num]);
            }else{
                $r=[
                    'err_code'=>'0',
                    'err_msg'=>'参数不全！'
                ];
                return json($r);
            }
            $r=[
                'err_code'=>'200',
                'err_msg'=>'提交成功！'
            ];
            return json($r);

    }

    public function delRider(Request $request){
        header('Access-Control-Allow-Origin:*');
        $rider_id = $request->param('rider_id');
        if(isset($rider_id)){
            Db::name('rider')->where(['id'=>$rider_id])->delete();
        }else{
            $r=[
                'err_code'=>'0',
                'err_msg'=>'参数不全！'
            ];
            return json($r);
        }
        $r=[
            'err_code'=>'200',
            'err_msg'=>'提交成功！'
        ];
        return json($r);

    }
    public function redistest()
    {
        //连接本地的 Redis 服务
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        echo "Connection to server sucessfully";
        //设置 redis 字符串数据
        $redis->set("tutorial-name", "Redis tutorial");
        // 获取存储的数据并输出
        echo "Stored string in redis:: " . $redis->get("tutorial-name");
    }
}