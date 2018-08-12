<?php
namespace app\api\controller;

use Pay\Exceptions\Exception;
use think\Controller;
use think\Request;
use think\Loader;
use think\Db;
use OSS\Core\OssException;
use Qsnh\think\Upload\Upload;
use think\config;
use DefaultAcsClient;
use Curder\AliyunCore\Profile\DefaultProfile;
use Sts\Request\V20150401\AssumeRoleRequest;
use IAcsClient;

class Aliupload extends Controller
{

    function gmt_iso8601($time)
    {
        $expiration = date("Y-m-d\TH:i:sO", $time);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    /**
     * ali表单上传图片应用服务接口
     *
     */
    public function formUpload()
    {
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $c = Config('upload');
        $conf = $c['aliyun'];
        $id = $conf['access_key_id'];
        $key = $conf['access_key_secret'];
        $host = 'http://' . $conf['remote_url'];
        $now = time();
        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);
        $dir = 'waimai_img/' . date('Y-m-d') . '/';

        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;


        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
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

    public function upLoad(Request $request)
    {
//修改上传临时路径配置
        $conf = config('upload');
        $path=ROOT_PATH . 'public' . DS . 'uploads/osstemp/';
        $conf['path'] = $path;
        $upload = new Upload($conf);
        try {
            $r = $upload->upload('file'); // first parameter is folder path; second parameter is custom filename(default: null)
            $result=[
                'code'=>'200',
                'msg'=>'上传成功！',
                'data'=>[
                    'filename'=>$r
                ]
            ];
        }catch(Exception $e){
            $result=[
                'error_code'=>$e->getCode(),
                'error_msg'=>$e->getMessage()
            ];
        }

        return $result;

    }

    /**
     * app直传ALIOSS获取授权码
     * @return string
     */
    public function appSts()
    {
        $c = Config('upload');
        $conf = $c['aliyun'];
        $EndPoint = $conf['oss_server'];
        $Bucket  = $conf['remote_url'];

        //本地用以下2行调试
        include 'extend/php-aliyun-ams/aliyun-php-sdk-core/Config.php';
        $content = self::read_file('cert/sts.json');

        //线上用以下三行调试
//        Loader::import('extend/php-aliyun-ams/aliyun-php-sdk-core/IAcsClient.php');
//        include 'extend/php-aliyun-ams/aliyun-php-sdk-core/Config.php';
//        $content = self::read_file('/www/wwwroot/waimai.023wx.cn/cert/sts.json');
        //线上使用的权限配置路径
//sts.json  "PolicyFile": "/www/wwwroot/waimai.023wx.cn/policy/all_policy.txt"

        $myjsonarray = json_decode($content);
        $accessKeyID = $myjsonarray->AccessKeyID;
        $accessKeySecret = $myjsonarray->AccessKeySecret;
        $roleArn = $myjsonarray->RoleArn;
        $tokenExpire = $myjsonarray->TokenExpireTime;
        $policy = self::read_file($myjsonarray->PolicyFile);

        $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $accessKeyID, $accessKeySecret);
        $client = new DefaultAcsClient($iClientProfile);

        $request = new AssumeRoleRequest();
        $request->setRoleSessionName("client_name");
        $request->setRoleArn($roleArn);
        $request->setPolicy($policy);
        $request->setDurationSeconds($tokenExpire);
        $response = $client->doAction($request);

        $rows = array();
        $body = $response->getBody();
        $content = json_decode($body);
        if ($response->getStatus() == 200) {
            $rows['StatusCode'] = 200;
//            $rows['AccessKeyId'] = $content->Credentials->AccessKeyId;
//            $rows['AccessKeySecret'] = $content->Credentials->AccessKeySecret;
            $rows['Expiration'] = $content->Credentials->Expiration;
            $rows['SecurityToken'] = $content->Credentials->SecurityToken;
            $rows['EndPoint'] = $EndPoint;
            $rows['Bucket'] = $Bucket;
        } else {
            $rows['StatusCode'] = 500;
            $rows['ErrorCode'] = $content->Code;
            $rows['ErrorMessage'] = $content->Message;
        }
        return json_encode($rows);
    }

    protected function read_file($fname)
    {
        $content = '';
        if (!file_exists($fname)) {
            echo "The file $fname does not exist\n";
            exit (0);
        }
        $handle = fopen($fname, "rb");
        while (!feof($handle)) {
            $content .= fread($handle, 10000);
        }
        fclose($handle);
        return $content;
    }

}