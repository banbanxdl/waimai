<?php
/**
 * 微信支出工具
 */

namespace app\common;
use think\Db;
class Wxpay {
    public $appid;
    public $secret;
    public $mch_id;
    public $key;
    public $notify_url;
    public $trade_type;//取值如下：JSAPI，NATIVE，APP
    public $openid;
    public function __construct($type) {
       $data= Db::name('sys_conf')->select();
        $conf = array();
        if($data){
            foreach($data as $k=>$v){
                $conf[$v['conf_name']] = $v['conf_value'];
            }
        }
        $this->appid      = $conf['wxpay_'.$type.'_appid'];
        $this->secret     = $conf['wxpay_'.$type.'_secret'];
        $this->mch_id     = $conf['wxpay_'.$type.'_mchid'];
        $this->key        = $conf['wxpay_'.$type.'_key'];
        $this->notify_url = $conf['wxpay_notifyurl'];
        $this->trade_type = $type;
    }

    /**
     * 统一下单
     *
     * @param $param
     *
     * @return mixed
     */
    public function unifiedPay($param ) {
        $post_data['appid']     = $this->appid;
        $post_data['mch_id']    = $this->mch_id;
        $post_data['nonce_str'] = $this->createNoncestr();
        if (  $param->param('type') == 'JSAPI' ) {
            if ( empty( $param->param('openid') ) ) {
                return 'openid不能为空';
            }
            $post_data['openid']     = $param->param('openid');
        }
        $post_data['body'] = $param->param('body');//'商品或支付单简要描述';
        if ( empty( $param->param('body') ) ) {
            return 'body不能为空';
        }
        $post_data['body'] = $param->param('body');//'商品或支付单简要描述';
        if ( ! empty( $param->param('detail') ) ) {
            $post_data['detail'] = $param->param('detail');// '商品名称明细列表';//可不要
        }
        if ( ! empty( $param->param('attach') ) ) {
            $post_data['attach'] = $param->param('attach');//  '附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据';//可不要
        }
        if ( empty( $param->param('out_trade_no') ) ) {
            return 'out_trade_no不能为空';
        }
        $post_data['out_trade_no'] = $param->param('out_trade_no');//商户系统内部的订单号
        if ( $param->param('total_fee') < 1 ) {
            return 'total_fee不能小余1';//订单总金额，单位为分
        }
        $post_data['total_fee'] = $param->param('total_fee');//订单总金额，单位为分
        if ( empty( $param->param('spbill_create_ip') ) ) {
            return 'spbill_create_ip不能为空';
        }
        $post_data['spbill_create_ip'] = $param->param('spbill_create_ip');//APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
        if ( ! empty( $param->param('goods_tag') ) ) {
            $post_data['goods_tag'] = '';//商品标记，代金券或立减优惠功能的参数 可以不要
        }
        $post_data['notify_url'] = $this->notify_url;
        $post_data['trade_type'] = $this->trade_type;//取值如下：JSAPI，NATIVE，APP
        if ( $this->trade_type == 'NATIVE' ) {
            if ( empty( $param->param('product_id') ) ) {
                return 'product_id不能为空';
            } else {
                $post_data['product_id'] = $param->param('product_id');//trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
            }
        }
        //$post_data['limit_pay'] =  '';//no_credit--指定不能使用信用卡支付
        if ( $this->trade_type == 'JSAPI' ) {
            if ( empty( $param->param('openid') ) ) {
                return 'openid不能为空';
            } else {
                $post_data['openid'] = $param->param('openid');//trade_type=JSAPI，此参数必传
            }
        }

        $sign              = $this->createSign( $post_data );//生成签名
        $post_data['sign'] = $sign;
        //dump($post_data);exit;
        $url      = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $xml_data = $this->arrayToXml( $post_data );//生成xml
        //echo $xml_data;exit;
        $return_xml = $this->httpPost( $url, $xml_data );//请求返回的数据
        //dump($return_xml);
        $return_arr = $this->xmlToArray( $return_xml );//转换为数组
       // dump($return_arr);die;
        if ( $return_arr['return_code'] == 'SUCCESS' and $return_arr['result_code'] == 'SUCCESS' ) {
            $return_data['status']    = 1;
            $return_data['prepay_id'] = $return_arr['prepay_id'];
           // $return_data['code_url']  = $return_arr['code_url'];
           // dump($return_data);die;
        } else {
            $return_data['status'] = 0;
            if ( $return_arr['return_code'] != 'SUCCESS' ) {
                $return_data['return_code'] = $return_arr['return_code'];
                $return_data['return_msg']  = $return_arr['return_msg'];
            } elseif ( ! empty( $return_arr['err_code'] ) ) {
                $return_data['return_code'] = $return_arr['err_code'];
                $return_data['return_msg']  = $return_arr['err_code_des'];
            }
        }

        return $return_data;
    }

    /**
     *
     * 生成前端支付参数 aPP
     */
    public function createPayParam( $prepayid ) {
        $post_data['appid']     = $this->appid;
        $post_data['partnerid'] = $this->mch_id;
        $post_data['prepayid']  = $prepayid;
        $post_data['package']   = 'Sign=WXPay';
        $post_data['noncestr']  = $this->createNoncestr();
        $post_data['timestamp'] = time();
        $post_data['sign']      = $this->createSign( $post_data );//生成签名

        return $post_data;
    }

    /**
     * 微信网站
     *
     * @param $prepay_id
     *
     * @return mixed
     */
    public function jsJson( $prepay_id ) {
        $time                   = time();
        $nonce_str              = $this->createNoncestr();
        $sign_data['appId']     = "$this->appid";
        $sign_data['timeStamp'] = "$time";
        $sign_data['nonceStr']  = "$nonce_str";
        $sign_data['package']   = "prepay_id=$prepay_id";
        $sign_data['signType']  = "MD5";
        $sign                   = $this->createSign( $sign_data );
        $sign_data['paySign']   = "$sign";

        return $sign_data;
    }

    /**
     *
     * 验证回调
     */
    public function checkNotifyUrl() {

        $xml=isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents("php://input");

        $arr = $this->xmlToArray( $xml );
        if ( $arr['return_code'] == 'SUCCESS' and $arr['result_code'] == 'SUCCESS' ) {//支付成功
            $r = $this->checkSign( $arr );//验证参数的合法性
            if ( $r ) {//验证通过
                $message['return_code'] = 'SUCCESS';
                $messageXml             = $this->arrayToXml( $message );
                $r_data['status']       = 1;
                $_data['data']          = $arr;//返回的数据
            } else {
                $message['return_code'] = 'FAIL';
                $message['return_msg']  = '签名失败';
                $messageXml             = $this->arrayToXml( $message );
                $r_data['status']       = 0;
            }
        } else {
            $message['return_code'] = 'FAIL';
            $message['return_msg']  = '支付失败';
            $messageXml             = $this->arrayToXml( $message );
            $r_data['status']       = 0;
        }
        $r_data['msg'] = $messageXml;

        return $r_data;
    }

    /**
     *
     * 生成认证签名
     * create_time 2015-10-12
     * author wj
     */
    public function createSign( $data ) {
        $str = '';
        ksort( $data );//数组排序
        foreach ( $data as $k => $v ) {//拼接成相关规则的字符串
            if ( ! empty( $v ) ) {
                if ( $str == '' ) {
                    $str = $k . "=" . $v;
                } else {
                    $str .= '&' . $k . "=" . $v;
                }
            }
        }
        $str .= "&key=$this->key";
        //加密
        $str = MD5( $str );
        $res = strtoupper( $str );

        return $res;
    }

    /**
     * 验证参数的合法性
     */
    public function checkSign( $arr ) {
        $tmpData = $arr;
        unset( $tmpData['sign'] );
        $sign = $this->createSign( $tmpData );//本地签名
        if ( $arr['sign'] == $sign ) {
            return true;
        }

        return false;
    }

    /**
     *    作用：产生随机字符串，不长于32位
     */
    public function createNoncestr( $length = 32 ) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str   = "";
        for ( $i = 0; $i < $length; $i ++ ) {
            $str .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
        }

        return $str;
    }

    /**
     * POST 请求
     *
     * @param string $url
     * @param array $param
     *
     * @return string content
     */
    public function httpPost( $url, $param ) {
        $oCurl = curl_init();
        if ( stripos( $url, "https://" ) !== false ) {
            curl_setopt( $oCurl, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $oCurl, CURLOPT_SSL_VERIFYHOST, false );
        }
        if ( is_string( $param ) ) {
            $strPOST = $param;
            if ( substr( $param, 0, 1 ) == '@' ) {
                $strPOST = array( 'file' => $param );
            }
        } else {
            $aPOST = array();
            foreach ( $param as $key => $val ) {
                $aPOST[] = $key . "=" . urlencode( $val );
            }
            $strPOST = join( "&", $aPOST );
        }
        curl_setopt( $oCurl, CURLOPT_URL, $url );
        curl_setopt( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $oCurl, CURLOPT_POST, true );
        curl_setopt( $oCurl, CURLOPT_POSTFIELDS, $param );
        $sContent = curl_exec( $oCurl );
        $aStatus  = curl_getinfo( $oCurl );
        curl_close( $oCurl );
//        var_dump($aStatus);
//        var_dump($sContent);die;
        if ( intval( $aStatus["http_code"] ) == 200 ) {
            return $sContent;
        } else {
            return false;
        }
    }

    function curl_post_ssl($url, $vars, $second=30,$cert,$aHeader=array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        //第二种方式，两个文件合成一个.pem文件
        curl_setopt($ch,CURLOPT_SSLCERT,$cert['SSLCERT_PATH']);
        curl_setopt($ch,CURLOPT_SSLKEY,$cert['SSLKEY_PATH']);

        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            //echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }


    /**
     * GET 请求
     *
     * @param $url
     *
     * @return bool|mixed
     */
    public function httpGet( $url ) {
        $oCurl = curl_init();
        if ( stripos( $url, "https://" ) !== false ) {
            curl_setopt( $oCurl, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $oCurl, CURLOPT_SSL_VERIFYHOST, false );
        }
        curl_setopt( $oCurl, CURLOPT_URL, $url );
        curl_setopt( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec( $oCurl );
        $aStatus  = curl_getinfo( $oCurl );
        curl_close( $oCurl );
        if ( intval( $aStatus["http_code"] ) == 200 ) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     *    作用：将xml转为array
     */
    public function xmlToArray( $xml ) {
        //将XML转为array
        $array_data = json_decode( json_encode( simplexml_load_string( $xml, 'SimpleXMLElement', LIBXML_NOCDATA ) ), true );

        return $array_data;
    }

    /**
     *    作用：array转xml
     */
    function arrayToXml( $arr ) {
        $xml = "<xml>";
        foreach ( $arr as $key => $val ) {
            if ( 1 == 1 ) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";

        return $xml;
    }

    public function sendBonusMoney($param,$cert_path){


        $post_data['mch_appid'] =  $this->appid;
        $post_data['mchid'] =  $this->mch_id;
        $post_data['nonce_str'] =  $this->createNoncestr();
        $post_data['partner_trade_no'] =  $param['partner_trade_no'];
        $post_data['openid'] =  $param['openid'];
        $post_data['check_name'] =  $param['check_name'];
        $post_data['amount'] =  $param['amount'];
        $post_data['desc'] =  $param['desc'];
        $post_data['spbill_create_ip'] =  $param['spbill_create_ip'];
        $sign = $this->createSign($post_data);//生成签名
        $post_data['sign'] = $sign;

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $xml_data = $this->arrayToXml($post_data);//生成xml
        $data = $this->curl_post_ssl($url, $xml_data,30,$cert_path);
        $return_arr=$this->xmlToArray($data);//转换为数组

        return $return_arr;

    }

}