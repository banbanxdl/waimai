<?php
/**
 * 支付接口
 *
 */
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\Wxpay as Wxpay;
use Pay\Pay as alipay;
use think\Config;
use think\exception;
class Payment  extends Controller{
    /**微信支付
     * @param Request $request 参数如下：
     * @param  $type : APP JSAPI
     * @param  $body    string    商品描述
     * @param  $detail    是    string    商品详情
     * @param $out_trade_no    是    string    订单编号
     * $total_fee    是    int    订单总金额(分)
     * $spbill_create_ip    是    string    设备IP
     * $type    是    string    APP(APP)/JSAPI(小程序)
     * @return string
     */
    public function wxPay(Request $request){
        try {
            $type = $request->param('type');
            if ($type) {
                $wxpay = new Wxpay($type);
                $r = $wxpay->unifiedPay($request);
                $prepay_id = $r['prepay_id'];
                $return = $wxpay->createPayParam($prepay_id);
                return json_encode($return);
            } else {
                return '{"msg":"参数错误！"}';
            }
        }catch(\Exception $e){
            $data = [
                'errorcode' => $e->getCode(),
                'errormsg'  => $e->getMessage()
            ];
            return json($data);
        }

 }

    /**
     * 微信支付给银行帐号
     * @param Request $request
     * @return string
     */
    public function wxBankPay(Request $request){
        $conf = Config::get('pay');
        $pay = new alipay($conf);
        $user_type = $request->param('user_type');
        $user_id =  $request->param('user_id');
        $money = $request->param('amount')/100;
        $cmms_amt=1;//手续费 需要后端设置规则
        $arry_type = ['1'=>'shop','2'=>'rider','3'=>'user'];
        if($user_type && $user_id && $money) {
           $user = Db::name($arry_type[$user_type])->where(['id'=>$user_id])->find();
        }else{
            $data = [
                'errorcode' => '-1',
                'errormsg'  => '非法请求！'
            ];
            return json($data);
        }
        if($user){
            if($user['money'] <  ($money+$cmms_amt)){
                $data = [
                    'errorcode' => '0',
                    'errormsg'  => '余额不足！'
                ];
                return json($data);
            }
        }else{
            $data = [
                'errorcode' => '-1',
                'errormsg'  => '非法请求！'
            ];
            return json($data);
        }
        $option = [
            'partner_trade_no'     => $request->param('partner_trade_no'), // 订单号
            'enc_bank_no'        => $request->param('enc_bank_no'), // 收款方银行卡号
            'enc_true_name'             => $request->param('enc_true_name'), // 收款方用户名
            'bank_code' => $request->param('bank_code'), // 银行卡所在开户行编号
            'amount'     => $request->param('amount'), // 付款金额：RMB分
            'desc'     => $request->param('desc'), // 付款说明
        ];
        $option['notify_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/Payment/aliNotifyUrl'; // 定义通知URL
        try {
            $options = $pay->driver('wechat')->gateway('bank')->apply($option);
            if($options){
                log_w(__CLASS__."\t".__FUNCTION__."\t".json_encode($options));
                return json_encode($options);
            }
        } catch (\Exception $e) {
            $data = [
                'errorcode' => $e->getCode(),
                'errormsg'  => $e->getMessage()
            ];
            log_w(__CLASS__."\t".__FUNCTION__."\t partner_trade_no".$request->param('partner_trade_no')."\t enc_bank_no: ".$request->param('enc_bank_no')."\t enc_true_name". $request->param('enc_true_name')."\t bank_code:". $request->param('bank_code')."\t amount:".$request->param('amount')."\t desc:".$request->param('desc')."\t exception:".json_encode($data));
            return json($data);
        }
    }

    /**
     * 微信支付回调
     * @return mixed
     */
 public function wxcheckNotifyUrl(){
     $wxpay = new Wxpay('APP');
     $return = $wxpay->checkNotifyUrl();
     return $return;
 }

    /**
     * @param Request $request
     *  out_trade_no：订单号
     * total_fee：订单金额，**单位：分**
     * body：订单描述
     * create_ip： 支付人的 IP
     * @return \think\response\Json
     */
    public function alipay(Request $request){
      //  include '../../../vendor/zoujingli/pay-php-sdk/init.php';
        $data=Db::name('sys_conf')->where('conf_name','like','ali_%')->select();
        $conf = array();
        if(isset($data)){
            foreach($data as $k=>$v){
                $name = $v['conf_name'];
                $name=str_replace('alipay_','',$name);
                $conf['alipay'][$name]=$v['conf_value'];
            }
        }
        $conf['alipay']['notity_url']='http://'.$_SERVER['HTTP_HOST'].$conf['alipay']['notity_url'];
        $pay = new alipay($conf);
        $option = [
            'out_trade_no'     => $request->param('out_trade_no'), // 订单号
            'total_fee'        => $request->param('total_fee'), // 订单金额，**单位：分**
            'body'             => $request->param('body'), // 订单描述
            'spbill_create_ip' => $request->param('create_ip'), // 支付人的 IP
        ];
        $option['notify_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/Payment/aliNotifyUrl'; // 定义通知URL
       try {
            $options = $pay->driver('alipay')->gateway('app')->apply($option);
           // log_w(__CLASS__."\t".__FUNCTION__."\t request_ali".$request->param('out_trade_no').$request->param('total_fee').$request->param('body').$request->param('create_ip'));
           // log_w(__CLASS__."\t".__FUNCTION__."\t ali_response".json($options));
           $data = [
               'code'=>'200',
               'data'=>$options
           ];
        return json($data);
       } catch (Exception $e) {
           $data = [
               'err_code'=>'-1',
               'err_message'=>$e->getMessage()
           ];
            return json($data);
        }
    }

    /**
     *支付回调url
     */
    public function aliNotifyUrl(){
        try {
            $conf = Config::get('pay');
            $pay = new \Pay\Pay($conf);
            if ($pay->driver('alipay')->gateway()->verify($_POST)) {//验证回调
                log_w("收到来自支付宝的异步通知\r\n" . "订单单号：{$_POST['out_trade_no']}\r\n" . "订单金额：{$_POST['total_amount']}\r\n\r\n");
                file_put_contents('notify.txt', "收到来自支付宝的异步通知\r\n", FILE_APPEND);
                file_put_contents('notify.txt', "订单单号：{$_POST['out_trade_no']}\r\n", FILE_APPEND);
                file_put_contents('notify.txt', "订单金额：{$_POST['total_amount']}\r\n\r\n", FILE_APPEND);
                /**
                 * 此处需要业务处理调用各个端的订单支付后处理任务
                 */
            } else {
                log_w(__CLASS__ . "\t" . __FUNCTION__ . "\t ali_response");
                file_put_contents('notify.txt', "收到异步通知\r\n", FILE_APPEND);
            }
        }catch(\Exception $e){
            $r = [
                'err_code'=>$e->getCode(),
                'err_message'=>'无效请求'
            ];
            log_w(__CLASS__ . "\t" . __FUNCTION__ . "\t ali_response");
            return json($r);
        }
    }
}