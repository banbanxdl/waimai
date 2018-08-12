<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;
use app\api\model\Rider as Mrider;
class Rider extends Controller
{
   
   /**
   * @param 骑手个人信息
   */
   public function getRiderInfo(Request $request)
   {	
   		$id=$request->param('id');//骑手id

      if(empty($id)){
        return message('','参数为空',1);
      }

   		$info=db('rider')->where('id',$id)->select();

      $info[0]['img']="http://".$_SERVER['HTTP_HOST'].'/public/uploads/api/'.$info[0]['img'];

   		return message($info,'获取信息成功',2);
   }
  	
  	/**
  	* @param 骑手实名认证
  	*/
  	public function riderAuthentication(Request $request)
  	{
      $data=$request->param();

      $result=Loader::model('Rider')->getAuthentication($data);

      if($result){
        return message('','提交审核成功',2);
      }else{
        return message('','提交审核失败',3);
      }

  	}

    /**
    *@param 获取骑手认证信息
    */
    public function getRiderAuthentication(Request $request){
      $rider_id=$request->param('id');//骑手id

      $result=Loader::model('Rider')->getRiderList($rider_id);

      return message($result,'获取成功',2);
    }

  	/**
  	* @param 骑手设置常驻点
  	*/
  	public function setRiderPermanent(Request $request)
  	{
  		$rider_id=$request->param('rider_id');

  		$address=$request->param('address');//常驻位置

  		$jd=$request->param('jd');//常驻位置经度

  		$wd=$request->param('wd');//常驻位置纬度

  		if(empty($rider_id) || empty($address) || empty($jd) || empty($wd)){
  			return message('','参数为空',3);
  		}

  		$result=Db::name('rider')->where('id',$rider)->update(['permanent'=>$address,'p_jd'=>$jd,'p_wd'=>$wd]);

  		if($result){
  			return message('','设置成功',2);
  		}else{
  			return message('','设置失败',3);
  		}
  	}

  	/**
  	* @param 骑手评价商家
  	*/
  	public function riderEvaluateShop(Request $request)
  	{
  		$rider_id=$request->param('rider_id');//骑手id

  		$shop_id =$request->param('shop_id');//店铺id

  		$stars   =$request->param('stars');//星星数

  		$content =$request->param('content');//内容

  		$img=implode(',', OssUplodas('img') );//配图

  		$result=Loader::model('rider')->getriderEvaluateShop($rider_id,$shop_id,$stars,$content,$img);

  		if($result){
  			return message('','评论成功',2);
  		}else{
  			return message('','评论失败',3);
  		}

  	}

  	/**
  	* @param 骑手添加银行卡
  	*/
  	public function addRiderBank(Request $request)
  	{
  		$data=$request->param();

  		$result=Loader::model('rider')->addBank($data);

  		if($result){
  			return message('','提交成功',2);
  		}else{
  			return message('','提交失败',3);
  		}

  	}
	public function getBankCode(){
		$mrider = new Mrider();
		$banklist = $mrider->getBank();
		return message($banklist,'获取信息成功',2);
	}
  	/**
  	* @param 骑手查看银行卡信息
  	*/
  	public function getRiderBank(Request $request)
  	{
  		$rider_id=$request->param('id');//当前登录的骑手id

  		$type=2;

  		$result=Db::name('bank_card')->where(['uid'=>$rider_id,'identity'=>2])->select();

  		return message($result,'获取信息成功',2);
  	}
  	
  	/**
  	* @param 骑手快捷消息设置
  	*/
  	public function setRiderShortcutKey(Request $request)
  	{
  		$rider_id=$request->param('id');//骑手id

  		$type   =$request->param('type');//1待取货 2 带送达

  		$content=$request->param('content');//内容

  		if(empty($rider_id)|| empty($type)|| empty($content)){
  			return message('','参数为空',1);
  		}

  		$new['rider_id'] =$rider_id;
  		$new['type']     =$type;
  		$new['content']  =$content;

  		$result=Db::name('rider_key')->insert($new);

  		if($result){
  			return message('','保存成功',2);
  		}else{
  			return message('','保存失败',3);
  		}
  	}

  	/**
  	*@param 骑手快捷消息列表
  	*/
  	public function getShortcutKeyList(Request $request)
  	{
  		$rider_id=$request->param('id');//骑手id

  		$type    =$request->param('type');//1 待取货 2带送达、

  		if(empty($rider_id) || empty($type)){
  			return message('','参数为空',1);
  		}

  		$result=Db::name('rider_key')->where(['rider_id'=>$rider_id,'type'=>$type])->select();

  		return message($result,'获取成功',2);
  	}

  	/**
  	* @param 骑手删除快捷消息
  	*/
  	public function delShortcutKey(Request $request)
  	{
  		$id=$request->param('id');//快捷键id 1,2,3

  		if(empty($id)){
  			return message('','参数为空',1);
  		}

  		$where['id']=array('in',$id);
  		$result=Db::name('rider_key')->where($where)->delete();

  		if($result){
  			return message('','删除成功',2);
  		}else{
  			return message('','删除失败',3);
  		}
  	}

  	/**
  	* @param 骑手仪容仪表规则标题
  	*/
  	public function getRiderRule(Request $request)
  	{
  		$result=Db::name('rider_rule')->where('pid',0)->field('id,title')->select();

  		return message($result,'获取成功',2);
  	}

  	/**
  	* @param 骑手仪容仪表规则内容 
  	*/
  	public function getRuleContent(Request $request)
  	{
  		$result=Db::name('rider_rule')->where('pid',0)->select();

  		$where['pid']=array('neq',0);

  		$content=Db::name('rider_rule')->where($where)->select();
 	
 		  //分组
  		$btitle=array();

  		$btitles=array();

  		foreach ($result as $val) {
  			if(!in_array($val['id'], $btitle)){
  				$btitle[]=$val['id'];
  			}
  			if(!in_array($val['title'], $btitles)){
  				$btitles[]=$val['title'];
  			}
  		}

  		$list=array();
  		foreach ($btitle as $k=>$v) {
  			
  			foreach ($content as $vall) {
  				if($vall['pid']==$v){
  					
  					$list[$k][]=$vall;
  				}
  			}
  		}

  		$new_list=array();
  		foreach ($list as $k1 => $v1) {
  			foreach ($btitles as $k2 => $v2) {
  				if($k1==$k2){
			  		$new_list[]=array(
			  			'title'=>$v2,
			  			'content'=>$v1,
			  		);
  				}
  			}
  		}


  		return message($new_list,'获取成功',2);
  	}

    /**
    *@param 骑手详情
    */
    public function getRiderDetails(Request $request)
    {
      $rider_id=$request->param('id');//骑手id

      if(empty($rider_id)){
        return message('','参数为空',1);
      }

      $result=Loader::model('Rider')->getRiderDetailsInfo($rider_id);

      return message($result,'获取成功',2);

    }

    /**
    *@param  骑手查看评价
    */
    public function getRiderEvaluate(Request $request)
    {
      $rider_id     =$request->param('id');//骑手id

      $evaluate_type=$request->param('evaluate_type');//查看评论类型 1 全部 2 好评 3 差评

      $type         =$request->param('type');// 1 用户评价骑手 2 商家评价骑手

      $page         =$request->param('page');

      $result=Loader::model('Rider')->getRiderEvaluateList($rider_id,$evaluate_type,$type,$page);

      //数量
      $user_good_num=Loader::model('Rider')->getRiderEvaluateListNum($rider_id,2,$type,1);//用户评价骑手的好评数量

      $user_bad_num=Loader::model('Rider')->getRiderEvaluateListNum($rider_id,3,$type,1);//用户评价骑手的差评数量

      $user_num=count($user_good_num,0)+count($user_bad_num,0);//用户评价骑手的全部数量

      $shop_good_num=Loader::model('Rider')->getRiderEvaluateListNum($rider_id,2,$type,2);//商家评价骑手的好评数量

      $shop_bad_num=Loader::model('Rider')->getRiderEvaluateListNum($rider_id,3,$type,2);//商家评价骑手的差评评数量

      $shop_num=count($shop_good_num,0)+count($shop_bad_num,0);//商家评价骑手的全部数量

      $user_num=array(
        'user_good_num'=>count($user_good_num,0),
        'user_bad_num'=>count($user_bad_num,0),
        'user_num'   =>$user_num,
      );

      $shop_num=array(
        'shop_good_num'=>count($shop_good_num,0),
        'shop_bad_num'=>count($shop_bad_num,0),
        'shop_num'   =>$shop_num,
      );

      $array=array(
        'list'=>$result,
        'user_num'=>$user_num,
        'shop_num'=>$shop_num,
      );

      return message($array,'获取成功',2);
    }

    /**
    *@param 骑手查看等级权益
    */
    public function getRiderGrade(Request $request)
    {
      $rider_id=$request->param('id');//骑手id

      $rider_info=Db::table('db_rider_grade')->alias('rg')->join('rider r','r.grade_num=rg.grade')->where('r.id',$rider_id)->field('r.id,r.img,rg.grade,rg.describe,rg.ordinary,rg.special_delivery,rg.run')->find();

      return message($rider_info,'获取成功',2);

    }

    /**
    * @param 骑手上传头像
    */
    public function getImg()
    {
      $id   =Request()->param('id');//当前登录的骑手id

      // 获取表单上传文件 例如上传了001.jpg
      $file = Request()->file('img');
      
      // 移动到框架应用根目录/public/uploads/ 目录下
      if($file){
          $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/api');
          if($info){
              // 成功上传后 获取上传信息
              
              // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
              $img= $info->getSaveName();
              
              $result=Db::name('rider')->where('id',$id)->update(['img'=>$img]);

             
          }else{
              // 上传失败获取错误信息
              return message($file->getError(),'上传错误',3); 
          }


      } 

      if($result){
        return message('','上传成功',2);
      }else{
        return message('','上传失败',3);
      } 
          
    }

    /**
    * @param 骑手选择是否接单
    */
    public function getCheckOrder()
    {
      $id=Request()->param('id');//当前登录的骑手id

      $status=Request()->param('status');//状态 1 接单  2 不接单

      $rider=Db::name('rider')->where('id',$id)->find();

      if($rider['status']==$status){
        return message('','骑手已是当前状态',3);
      }else{
        $result=Db::name('rider')->where('id',$id)->update(['status'=>$status]);

        if($result){
          return message('','提交成功',2);
        }else{
          return message('','提交失败',3);
        }
      }
    }


}
