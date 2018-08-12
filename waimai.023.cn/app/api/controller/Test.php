<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 下午 11:18
 */

namespace app\api\controller;
use app\common\controller\ApiCommon;
use think\Controller;
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Lib\Timer;
use PHPSocketIO\SocketIO;
use think\request;
class Test extends ApiCommon
{
    public  $http_worker;
    public function makeMd5(){
        return 'success!';
    }
   public function workerman(){
       require_once '/vendor/workerman/workerman/Autoloader.php';
// 创建一个Worker监听2345端口，使用http协议通讯
       $this->http_worker= new Worker("websocket://0.0.0.0:2345");
       $this->http_worker->name = 'MyWebsocketWorker';
// 启动4个进程对外提供服务
       $this->http_worker->count = 1;
       $this->http_worker->onConnect = function($connection){
           print_r($connection);
       };
       $this->http_worker->onWorkerStart = function(){
           //保存链接
           $this->http_worker->uidConnections = array();
           $inner_http_worker = new Worker('tcp://0.0.0.0:2121');
           // 当http客户端发来数据时触发
           $inner_http_worker->onMessage = function($connection, $buffer){
              // $data = json_decode($buffer,true);
               if(!empty($buffer)) {
                    try{
                       $b_ary = json_decode($buffer, true);
                       $uid = $b_ary['uid'];
                       $msg = (string)$b_ary['msg'];
                        if($buffer) {
                            //   echo $uid;
                            $content = $msg;
//               // 通过workerman，向uid的页面推送数据
                            $ret = $this->sendMessageByUid($uid, $content);
                        }else{
                            $ret=false;
                        }
                        // 返回推送结果
                        $connection->send($ret ? 'ok' : 'fail');
                    }catch(Excetion $e) {
                       return $e->getMessage();
                   }
               }
           };
           $inner_http_worker->listen();
       };
// 接收到浏览器发送的数据时回复hello world给浏览器
       $this->http_worker->onMessage = function($connection, $data)
       {
           // 判断当前客户端是否已经验证,既是否设置了uid
           if(!isset($connection->uid))
           {
               // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
               $connection->uid = $data;
               /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
                * 实现针对特定uid推送数据
                */
               $this->http_worker->uidConnections[$connection->uid] = $connection;
               return;
           }
       };
// 当有客户端连接断开时
       $this->http_worker->onClose = function($connection)
       {

           if(isset($connection->uid))
           {
               // 连接断开时删除映射
               unset($this->http_worker->uidConnections[$connection->uid]);
           }
       };

       Worker::runAll();
   }
// 针对uid推送数据
    public function sendMessageByUid($uid, $message)
    {
        if(isset($this->http_worker->uidConnections[$uid]))
        {
            $connection = $this->http_worker->uidConnections[$uid];
            $connection->send($message);
            return true;
        }
        return false;
    }

    public function pushMsg(Request $request){
        $msg = $request->param('msg');
        header("Content-Type: text/html; charset=UTF-8");
// 建立socket连接到内部推送端口
        $client = stream_socket_client('tcp://127.0.0.1:2121', $errno, $errmsg, 1);
// 推送的数据，包含uid字段，表示是给这个uid推送
        $data = array('uid'=>'123', 'msg'=>$msg);
// 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
        fwrite($client, json_encode($data)."\n");
// 读取推送结果
        echo fread($client, 8192);
    }
    public function worker(){
        require_once '/vendor/workerman/workerman/Autoloader.php';

// 全局数组保存uid在线数据
        $uidConnectionMap = array();
// 记录最后一次广播的在线用户数
        $last_online_count = 0;
// 记录最后一次广播的在线页面数
        $last_online_page_count = 0;


// PHPSocketIO服务
        $sender_io = new SocketIO(2120);
// 客户端发起连接事件时，设置连接socket的各种事件回调
        $sender_io->on('connection', function($socket){
            // 当客户端发来登录事件时触发
            $socket->on('login', function ($uid)use($socket){
                echo $uid;
                global $uidConnectionMap, $last_online_count, $last_online_page_count;
                // 已经登录过了
                if(isset($socket->uid)){
                    return;
                }
                // 更新对应uid的在线数据
                $uid = (string)$uid;
                if(!isset($uidConnectionMap[$uid]))
                {
                    $uidConnectionMap[$uid] = 0;
                }
                // 这个uid有++$uidConnectionMap[$uid]个socket连接
                ++$uidConnectionMap[$uid];
                // 将这个连接加入到uid分组，方便针对uid推送数据
                $socket->join($uid);
                $socket->uid = $uid;
                // 更新这个socket对应页面的在线数据
                $socket->emit('update_online_count', "当前<b>{$last_online_count}</b>人在线，共打开<b>{$last_online_page_count}</b>个页面");
            });

            // 当客户端断开连接是触发（一般是关闭网页或者跳转刷新导致）
            $socket->on('disconnect', function () use($socket) {
                if(!isset($socket->uid))
                {
                    return;
                }
                global $uidConnectionMap, $sender_io;
                // 将uid的在线socket数减一
                if(--$uidConnectionMap[$socket->uid] <= 0)
                {
                    unset($uidConnectionMap[$socket->uid]);
                }
            });
        });

// 当$sender_io启动后监听一个http端口，通过这个端口可以给任意uid或者所有uid推送数据
        $sender_io->on('workerStart', function(){
            // 监听一个http端口
            $inner_http_worker = new Worker('http://0.0.0.0:2121');
            // 当http客户端发来数据时触发

            $inner_http_worker->onMessage = function($http_connection, $data){
                global $uidConnectionMap;
                $_POST = $_POST ? $_POST : $_GET;
                // 推送数据的url格式 type=publish&to=uid&content=xxxx
                echo @$_POST['type'];
                echo @$_POST['to'];
                switch(@$_POST['type']){
                    case 'publish':
                        global $sender_io;
                        $to = @$_POST['to'];
                        $_POST['content'] = htmlspecialchars(@$_POST['content']);
                        // 有指定uid则向uid所在socket组发送数据
                        if($to){
                            $sender_io->to($to)->emit('new_msg', $_POST['content']);
                            // 否则向所有uid推送数据
                        }else{
                            $sender_io->emit('new_msg', @$_POST['content']);
                        }
                        // http接口返回，如果用户离线socket返回fail
                        if($to && !isset($uidConnectionMap[$to])){
                            return $http_connection->send('offline');
                        }else{
                            return $http_connection->send('ok');
                        }
                }
                return $http_connection->send('fail');
            };
            // 执行监听
            $inner_http_worker->listen();
        });

        if(!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }

    public function toUser(){
        $to_uid = "123";
        // 推送的url地址
        $push_api_url = "http://127.0.0.1:2121/";
        $post_data = array(
            "type" => "publish",
            "content" => "数据",
            "to" => $to_uid,
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        var_export($return);

    }
}