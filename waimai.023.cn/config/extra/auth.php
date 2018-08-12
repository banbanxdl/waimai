<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 17:53
 */
return [

    /**
     * 系统管理员 是指  登录总后台
     */
    'type_list'=>[
        ['id'=>1,'title'=>'系统管理员'],
        ['id'=>2,'title'=>'商家后台'],
        ['id'=>3,'title'=>'配送后台'],
        ['id'=>4,'title'=>'其他后台'],
        ['id'=>5,'title'=>'其他后台'],
    ],
    'type_name'=>[
        1 => '系统管理员',
        2 => '商家后台',
        3 => '配送后台',
        4 => '其他后台',
        5 => '其他后台',
    ],

    //菜单or按钮
    'menu_type_list'=>[
        ['id'=>1,'title'=>'左侧菜单'],
        ['id'=>2,'title'=>'按钮'],
        ['id'=>3,'title'=>'列表顶部菜单'],
    ],
    'menu_type_value'=>[
        1=>'左侧菜单',
        2=>'按钮',
        3=>'列表顶部菜单',
    ],

    //图标列表
    'ico_list'=>[
        ['id'=>'gotop','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-gotop"></i>'],
        ['id'=>'menu','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-menu"></i>'],
        ['id'=>'jiandao','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-jiandao"></i>'],
        ['id'=>'search2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-search2"></i>'],
        ['id'=>'save','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-save"></i>'],
        ['id'=>'chexiao','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-chexiao"></i>'],
        ['id'=>'zhongzuo','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-zhongzuo"></i>'],
        ['id'=>'down','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-down"></i>'],
        ['id'=>'slider-right','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-slider-right"></i>'],
        ['id'=>'slider-left','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-slider-left"></i>'],
        ['id'=>'fabu','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-fabu"></i>'],
        ['id'=>'add2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-add2"></i>'],
        ['id'=>'huanyipi','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-huanyipi"></i>'],
        ['id'=>'dengdai','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-dengdai"></i>'],
        ['id'=>'daochu','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-daochu"></i>'],
        ['id'=>'daoru','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-daoru"></i>'],
        ['id'=>'del','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-del"></i>'],
        ['id'=>'del2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-del2"></i>'],
        ['id'=>'del3','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-del3"></i>'],
        ['id'=>'shuru','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-shuru"></i>'],
        ['id'=>'add','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-add"></i>'],
        ['id'=>'jianhao','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-jianhao"></i>'],
        ['id'=>'edit2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-edit2"></i>'],
        ['id'=>'edit','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-edit"></i>'],
        ['id'=>'manage','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-manage"></i>'],
        ['id'=>'add3','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-add3"></i>'],
        ['id'=>'add4','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-add4"></i>'],
        ['id'=>'key','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-key"></i>'],
        ['id'=>'jiesuo','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-jiesuo"></i>'],
        ['id'=>'suoding','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-suoding"></i>'],
        ['id'=>'close','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-close"></i>'],
        ['id'=>'close2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-close2"></i>'],
        ['id'=>'xuanze','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-xuanze"></i>'],
        ['id'=>'weigouxuan2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-weigouxuan2"></i>'],
        ['id'=>'xuanzhong1','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-xuanzhong1"></i>'],
        ['id'=>'xuanzhong','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-xuanzhong"></i>'],
        ['id'=>'weixuanzhong','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-weixuanzhong"></i>'],
        ['id'=>'gouxuan2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-gouxuan2"></i>'],
        ['id'=>'chongqi','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-chongqi"></i>'],
        ['id'=>'selected','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-selected"></i>'],
        ['id'=>'shangjia','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-shangjia"></i>'],
        ['id'=>'xiajia','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-xiajia"></i>'],
        ['id'=>'upload','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-upload"></i>'],
        ['id'=>'yundown','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-yundown"></i>'],
        ['id'=>'caiqie','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-caiqie"></i>'],
        ['id'=>'xuanzhuan','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-xuanzhuan"></i>'],
        ['id'=>'gouxuan','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-gouxuan"></i>'],
        ['id'=>'weigouxuan','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-weigouxuan"></i>'],
        ['id'=>'luyin','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-luyin"></i>'],
        ['id'=>'yulan','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-yulan"></i>'],
        ['id'=>'shenhe-weitongguo','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-shenhe-weitongguo"></i>'],
        ['id'=>'shenhe-butongguo2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-shenhe-butongguo2"></i>'],
        ['id'=>'shenhe-tongguo','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-shenhe-tongguo"></i>'],
        ['id'=>'shenhe-tingyong','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-shenhe-tingyong"></i>'],
        //菜单相关
        ['id'=>'home','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-home"></i>'],
        ['id'=>'home2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-home2"></i>'],
        ['id'=>'news','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-news"></i>'],
        ['id'=>'system','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-system"></i>'],
        ['id'=>'fenlei','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-fenlei"></i>'],
        ['id'=>'hetong','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-hetong"></i>'],
        ['id'=>'quanbudingdan','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-quanbudingdan"></i>'],
        ['id'=>'renwu','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-renwu"></i>'],
        ['id'=>'feedback2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-feedback2"></i>'],
        ['id'=>'dangan','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-dangan"></i>'],
        ['id'=>'log','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-log"></i>'],
        ['id'=>'pages','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-pages"></i>'],
        ['id'=>'manage2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-manage2"></i>'],
        ['id'=>'order','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-order"></i>'],
        ['id'=>'picture1','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-picture1"></i>'],
        ['id'=>'tuwenxiangqing','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-tuwenxiangqing"></i>'],
        //用户相关
        ['id'=>'user','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-user"></i>'],
        ['id'=>'user2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-user2"></i>'],
        ['id'=>'user-group','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-user-group"></i>'],
        ['id'=>'root','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-root"></i>'],
        ['id'=>'usergroup2','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-usergroup2"></i>'],
        //统计相关
        ['id'=>'tongji-bing','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-tongji-bing"></i>'],
        ['id'=>'ad','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-ad"></i>'],
        ['id'=>'shujutongji','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-shujutongji"></i>'],
        ['id'=>'tongji','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-tongji"></i>'],
        ['id'=>'tongji-zhu','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-tongji-zhu"></i>'],
        ['id'=>'tongji-xian','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-tongji-xian"></i>'],
        ['id'=>'paixingbang','title'=>'<i style="font-size: 25px" class="Hui-iconfont Hui-iconfont-paixingbang"></i>'],
    ],
    //按钮打开方式
    'but_type_list'=>[
        ['id'=>1,'title'=>'跳转页面'],
        ['id'=>2,'title'=>'禁止、开启'],
        ['id'=>3,'title'=>'审核'],
        ['id'=>4,'title'=>'删除'],
        ['id'=>5,'title'=>'打开弹窗'],
//        ['id'=>6,'title'=>'批量删除'],
    ],
    'but_type_value'=>[
        1=>'跳转页面',
        2=>'禁止、开启',
        3=>'审核',
        4=>'删除',
        5=>'打开弹窗',
//        6=>'批量删除',
    ],
    //按钮位置
    'but_site_list'=>[
        ['id'=>1,'title'=>'顶部按钮'],
        ['id'=>2,'title'=>'列表中的按钮'],
        ['id'=>3,'title'=>'表单中的按钮'],
    ]
];