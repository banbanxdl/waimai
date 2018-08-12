<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/22
 * Time: 10:36
 */

namespace app\userapi\controller;


use app\userapi\model\Icon;
use think\Db;
use think\Loader;
use think\Request;

class Index extends Api
{
    /**
     * 商家列表
     * @param Request $request
     * @return mixed
     */
    public function indexList(Request $request)
    {
        $model = new \app\userapi\model\Shop();
        $vali = new \app\userapi\validate\Shop();
        if ($request->isGet()){
            $data['id'] = $request->get('id'); //用户id
            $data['lt'] = $request->get('lt'); //经度
            $data['wt'] = $request->get('wt'); //维度
            $data['sheng'] = $request->get('sheng');
            $data['shi'] = $request->get('shi');
            $data['qu'] = $request->get('qu');
            /**
             * 1 => 默认排序 综合排序 2 => 距离最短 3 => 销量最高 4 => 评价最高 5 => 配送费最低 6 => 起送价最低
             */
            $data['type'] = $request->get('type');
            $data['pa'] = $request->get('pa',1);
            $data['ge'] = $request->get('ge',10);
            $data['tp'] = $request->get('tp',0); //店铺分类id
            if (!$vali->scene('selist')->check($data)){
                return message('',$vali->getError(),1);
            }
            $order = [
                1=>['id','sales_volume'=>'desc','num'=>'desc'],
                2=>['id'],
                3=>['sales_volume'=>'desc'],
                4=>['num'=>'desc'],
                5=>['id'],
                6=>['id'],
            ];
            try{
                switch ($data['type']){
                    case 6:
                        $arr = $model->shopNumberList($data,$order[$data['type']],false);
                        $tree = $this->roundShopList($arr,$data['pa'],$data['ge'],'start_price');
                        break;
                    case 5:
                        $arr = $model->shopNumberList($data,$order[$data['type']],false);
                        $tree = $this->roundShopList($arr,$data['pa'],$data['ge'],'dis_money');
                        break;
                    case 4:
                        $tree = $model->shopNumberList($data,$order[$data['type']]);
                        break;
                    case 3:
                        $tree = $model->shopNumberList($data,$order[$data['type']]);
                        break;
                    case 2:
                        $arr = $model->shopNumberList($data,$order[$data['type']],false);
                        $tree = $this->roundShopList($arr,$data['pa'],$data['ge'],'distence');
                        break;
                    default:
                        $tree = $model->shopNumberList($data,$order[$data['type']]);
                        break;
                }
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($tree)){
                return message('','此地区，暂时没有商家',3);
            }else{
                return message($tree,'获取成功',2);
            }
        }
    }

    /**
     * 获取当前 经纬度的 店铺id集合
     * 用户信息 Redis(hashes) name => userInfo.$id
     * 用户店铺列表 Redis(Sorted sets) name => shopList.$id
     * @param $data
     * @return mixed
     */
    /**
     * 根据键值 按升序排序 并返回每页数量
     * @param $arr 数据
     * @param $pa 页码
     * @param $ge 每页数量
     * @param $field 排序字段
     * @return array
     */
    public function roundShopList($arr,$pa,$ge,$field)
    {
        $key = [];
        foreach ($arr as $item){
            $key[] = $item[$field];
        }
        array_multisort($key,SORT_ASC,SORT_NUMERIC,$arr); //多维数组排序
        $tree=array_slice($arr,(($pa*$ge)-$ge),$ge);
        return $tree;
    }

    /**
     * 首页导航栏目列表
     * @param Request $request
     * @return mixed
     */
    public function iconList(Request $request)
    {
        $model = new Icon();
        if ($request->isGet()){
            $sheng = $request->get('sheng');
            $shi = $request->get('shi');
            $qu = $request->get('qu');
            try{
                $list = $model->where('sheng',$sheng)->where('shi',$shi)->where('qu',$qu)
                    ->order(['sort'=>'desc','id'=>'desc'])->select();
                if (empty($list)){
                    $tree = [];
                }else {
                    foreach ($list as $value) {
                        $value['url'] = $value->icon_url;
                        $tree[] = $value->toArray();
                    }
                }
            }catch (\Exception $e){
                return message('',$e->getMessage(),5);
            }
            if (empty($tree)){
                return message('','获取失败',3);
            }else{
                return message('','获取成功',2);
            }
        }
    }

}

