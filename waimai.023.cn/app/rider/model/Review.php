<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/21
 * Time: 9:49
 */

namespace app\rider\model;
use think\image\Exception;
use think\Model;
use think\Db;

class Review extends  Admin
{
    protected  $name = 'rider_info';
    protected $search_field = 'name|sex|age';
    protected $autoWriteTimestamp = true;
    public function getRider($fields,$map=null){
        if($map) {
            $data = Db::name('rider_info')->field($fields)->where(
                $map
            )->select();
            return $data;
        }
    }

    public function setstatus($id,$status){
        try {
            $data = [
                'status' => $status
            ];
            $r = Db::name('rider_info')->where(['id' =>$id])->update($data);
            return $r;
        }catch(Exception $e){
            return [
                'error_code'=>$e->getCode(),
                'error_msg'=>$e->getMessage()
            ];
        }

    }

    /**组合身份证
     * shop_id ShopId
     * @param $val
     * @param $data
     */
    public function getIdcardAttr($data,$val)
    {
        $l_str=substr($data,0,5);
        $r_str=substr($data,-4,4);
        $str = $l_str.'*********'.$r_str;
        return $str;
    }

    public function getHoldJustimgAttr($data,$val){
        $rand = mt_rand();
        if($data!=''){
            $status = '已上传';
            $html_str=$status."
        <script>
        function showimg".$rand."(){
                    layer.open({
                     title: '照片',
                      type: 1,
                      skin: 'layui-layer-rim', //加上边框
                      area: ['600px', '400px'], //宽高
                      content: '<img style=\'max-width:600px;\' src=\'".$data."\'/>'
                    });
                    }
        </script><a href='javascript:void(0)' onclick='showimg".$rand."();'>（点击查看）</button>" ;
        }else{
            $status = '未上传';
            $html_str=$status;
        }
        return $html_str;
    }

    public function getHoldBackimgAttr($data,$val){
        $rand = mt_rand();
        $html_str="已上传
        <script>
        function showimg".$rand."(){
                    layer.open({
                     title: '照片',
                      type: 1,
                      skin: 'layui-layer-rim', //加上边框
                      area: ['600px', '400px'], //宽高
                      content: '<img style=\'max-width:600px;\' src=\'".$data."\'/>'
                    });
                    }
        </script><a href='javascript:void(0)' onclick='showimg".$rand."();'>（点击查看）</button>
                    "
        ;
        return $html_str;
    }

    public function getDriverLicenseImgAttr($data,$val){
        $rand = mt_rand();
        $html_str="已上传
        <script>
        function showimg".$rand."(){
                    layer.open({
                     title: '照片',
                      type: 1,
                      skin: 'layui-layer-rim', //加上边框
                      area: ['600px', '400px'], //宽高
                      content: '<img style=\'max-width:600px;\' src=\'".$data."\'/>'
                    });
                    }
        </script><a href='javascript:void(0)' onclick='showimg".$rand."();'>（点击查看）</button>
                    "
        ;
        return $html_str;
    }
    public function getVicePageImgAttr($data,$val){
        $rand = mt_rand();
        $html_str="已上传
        <script>
        function showimg".$rand."(){
                    layer.open({
                     title: '照片',
                      type: 1,
                      skin: 'layui-layer-rim', //加上边框
                      area: ['600px', '400px'], //宽高
                      content: '<img style=\'max-width:600px;\' src=\'".$data."\'/>'
                    });
                    }
        </script><a href='javascript:void(0)' onclick='showimg".$rand."();'>（点击查看）</button>
                    "
        ;
        return $html_str;
    }

    public function getHealthImgAttr($data,$val){
        $rand = mt_rand();
        $html_str="已上传
        <script>
        function showimg".$rand."(){
                    layer.open({
                     title: '照片',
                      type: 1,
                      skin: 'layui-layer-rim', //加上边框
                      area: ['600px', '400px'], //宽高
                      content: '<img style=\'max-width:600px;\' src=\'".$data."\'/>'
                    });
                    }
        </script><a href='javascript:void(0)' onclick='showimg".$rand."();'>（点击查看）</button>
                    "
        ;
        return $html_str;
    }

}