<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/30
 * Time: 15:54
 */

return [
    //域名
    'site_url' => 'http://'.$_SERVER['HTTP_HOST'],
    'user_img' => '/public/static/common/img/user_head_img_default.png',
    //首页 导航图标 列表
    'first_icon_list'=> [
        ['id'=>1,'title'=>'<img src="">'],
        ['id'=>2,'title'=>'<img src="">'],
        ['id'=>3,'title'=>'<img src="">'],
        ['id'=>4,'title'=>'<img src="">'],
        ['id'=>5,'title'=>'<img src="">'],
        ['id'=>6,'title'=>'<img src="">'],
        ['id'=>7,'title'=>'<img src="">'],
    ],

    //
    ''=> 10,

    //图标链接地址
    'first_icon_url' => [
        1=>'',
        2=>'',
        3=>'',
        4=>'',
        5=>'',
        6=>'',
        7=>'',
    ],

    'user_pay_log_type' => [
        //1充值 2提现 3余额支付 4微信支付 5支付宝支付
        1=>'充值',2=>'提现',3=>'余额支付',4=>'微信支付',5=>'支付宝支付',
    ]
];