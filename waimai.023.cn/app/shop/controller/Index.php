<?php
namespace app\shop\controller;
use think\Controller;
use think\Request;
use think\Loader;
use think\Db;
class Index extends Controller
{
    public function index()
    {
        return 'kukguk';
    }

     /**
     * @param $info            数据列表
     * @param $content         返回的状态信息
     * @param $status          状态码
     * @return mixed
     */
    protected function message($info, $content, $status){
        $data = array();
        $sta = [];
        if (is_object($info)){
            $data['data'] = $info;
        }
        if (is_array($info) && !empty($info)){
            foreach ($info as $k => $val){
                $key[] = $k;
            }
            //检查数组中 是否有data键值
            if (in_array('data',$key,true)){
                $data = $info;
            }else{
                $data['data'] = $info;
            }
        }
        if (is_string($info)){
            $data['data'] = $info;
        }
        if (is_int($info)){
            $data['data'] = $info;
        }
        if (empty($info)){
            $data['data'] = $info;
        }
        if ($status == 1){
            $sta['code'] = 100;       //参数不对
            $sta['message'] = $content;
        }elseif ($status == 2){
            $sta['code'] = 200;       //成功
            $sta['message'] = $content;
        }elseif ($status == 3){
            $sta['code'] = 300;       //信息不存在
            $sta['message'] = $content;
        }elseif ($status == 4){
            $sta['code'] = 404;       //找不到
            $sta['message'] = $content;
        }elseif ($status == 5){
            $sta['code'] = 500;       //服务器错误
            $sta['message'] = $content;
        }
        $data['status'] = $sta; //组装状态
        return json($data)->send();
    }
}
