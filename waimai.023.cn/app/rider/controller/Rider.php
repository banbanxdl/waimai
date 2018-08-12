<?php
namespace app\rider\controller;

//use app\userapi\model\User;
use think\Controller;
use think\Request;
use think\Loader;
use think\Db;
use think\Validate;
//use app\rider\model\Rider as mrider;
use app\api\model\Geograph AS M_geograph;
class Rider extends Admin
{
    private $but_url;
    public function __construct(Request $request)
    {
        parent::__construct($request);
      //  header('Access-Control-Allow-Origin:*'); //允许跨域访问

        //$but_url = Tpldemo::temp()->_table_button[0]['name'];
    }

    public function index()
    {
        return $this->fetch();
    }

    public function welcome()
    {
        return $this->fetch();
    }

    /**
     * 获取骑手信息
     * @param Request $request
     * status 骑手状态 1 接单 2 未接单
     * pow_takeaway 外卖权限  1有 0无
     * pow_legs 跑腿权限  1有 0无
     * grade_num 等级
     * @return \think\response\Json
     */
    public function getRider(Request $request)
    {
      //  header('Access-Control-Allow-Origin:*');
        $a_url = $this->_form_button;//按钮的授权地址
        $del_url=$a_url[0]['url'];//删除按钮
        $status_url=$a_url[1]['url'];//工作状态按钮
        $addrider_url=$a_url[2]['url'];//工作状态按钮
        try {
            $status = $request->param('status','');
            $pow_takeaway = $request->param('pow_takeaway','');
            $pow_legs = $request->param('pow_legs','');
            $grade_num = $request->param('grade_num','');

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
            $data = Db::table('db_rider')
                ->alias('r')
                ->field(' r.id,r.img,re.region_name,r.phone,r.status,g.describe,i.name,i.sex,i.age,g.describe,r.money')
                ->field('date_format(from_unixtime(r.add_time),\'%Y-%m-%d %H:%i:%s\') as add_time,r.pow_takeaway,r.pow_legs')
                ->join('db_rider_grade g','r.grade_num = g.id','LEFT')
                ->join('db_rider_info i','r.id = i.rider_id','LEFT')
                ->join('db_region re','i.shi = re.id','LEFT')
                ->where($where)
                ->order('r.id desc')
                ->paginate(10);
            //取省名称
            $p_data = json_decode(action('api/geograph/getprovince'),true);
            $province = $p_data['data'];
            $c_data=json_decode(action('api/geograph/getcity',[$province[0]['id']]),true);
            $city=$c_data['data']['city_list'];
            $a_data=json_decode(action('api/geograph/getArea',[$city[0]['id']]),true);
            $area = $a_data['data']['area_list'];
            unset($area[0]);
            $this->assign('rlist',$data);
            $this->assign('status',$status);
            $this->assign('pow_takeaway',$pow_takeaway);
            $this->assign('pow_legs',$pow_legs);
            $this->assign('grade_num',$grade_num);
            $this->assign('province',$province);
            $this->assign('city',$city);
            $this->assign('area',$area);
            $this->assign('delurl',$del_url);//授权的地址
            $this->assign('statusurl',$status_url);//授权的地址
            $this->assign('addriderurl',$addrider_url);//授权的地址
            return Tpldemo::temp()->template('getrider')->templateView();
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
     * 设置骑手
     * @param Request $request
     * $param
     * status 骑手状态 1 接单 2 未接单
     * pow_takeaway 外卖权限  1有 0无
     * pow_legs 跑腿权限  1有 0无
     * @return array
     */
    public function setRider(Request $request){
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

    /**
     * 删除骑手
     * @param Request $request rider_id  ；骑手id
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function delRider(Request $request){
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


    /**后台添加骑手
     *  rider_id:	number	骑手id
        name:	number	真实姓名
        sex:	number	性别
        id_card:	string	身份证号码
        age:	number	年龄
        city_id:	number	工作城市id
        province:	int	工作城市省
        city:	int	工作城市 市
        area:	int	工作城市 区
        hold_justimg:	string	手持身份证正面
        hold_backimg:	string	手持身份证反面
        driver_license_img:	string	驾驶证
        vice_page_img:	string	驾驶证副页
        health_img:	string	健康证
     * @param Request $request
     * @return mixed
     */
    public function insertRider(Request $request){
        //接受参数
        $name = $request->param('name');
        $sex = $request->param('sex');
        $id_card = $request->param('id_card');
        $age = $request->param('age');
        $city_id = $request->param('province');
        $sheng = $request->param('province');
        $shi = $request->param('city');
        $qu = $request->param('area');
        $hold_justimg = $request->param('hupImg1');
        $hold_backimg = $request->param('hupImg2');
        $driver_license_img = $request->param('hupImg3');
        $vice_page_img = $request->param('hupImg4');
        $health_img = $request->param('hupImg5');
        $phone  = $request->param('phone');

        $data = [
            'name'         => $name,
            'sex'          => $sex,
            'id_card'      => $id_card,
            'phone'        => $phone,
            'age'          => $age,
            'city_id'      =>$city_id,
            'sheng'        =>$sheng,
            'shi'          =>$shi,
            'qu'           =>$qu,
            'hold_justimg' =>$hold_justimg,
            'driver_license_img'=>$driver_license_img,
            'vice_page_img'=>$vice_page_img,
            'hold_backimg'=>$hold_backimg,
            'health_img'=>$health_img,
        ];
        $validate = new Validate([
            'phone'  => 'require|length:11',
        ]);
        $rider = [
            'phone'=>$phone,
            'nickname'=>$name,
            'add_time'=>time()
        ];
        if (!$validate->check($rider)) {
            return $validate->getError();
        }

        //验证
        $validate = Loader::validate('Rider');
        $result   = $validate->check($data);
        if(!$result){
            return $validate->getError();
        }
        $r_infoid = Loader::model('rider')->insertrider($data,$rider);
        if($r_infoid){
            $this->success('新增成功', 'rider/getrider');
        }

    }

//    public function redistest()
//    {
//        //连接本地的 Redis 服务
//        $redis = new \Redis();
//        $redis->connect('127.0.0.1', 6379);
//        echo "Connection to server sucessfully";
//        //设置 redis 字符串数据
//        $redis->set("tutorial-name", "Redis tutorial");
//        // 获取存储的数据并输出
//        echo "Stored string in redis:: " . $redis->get("tutorial-name");
//    }
}