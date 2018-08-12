<?php
namespace app\shop\controller;
use think\Request;
use think\Loader;
use think\Db;
class Evaluate extends Index
{

	//显示用户全部评价
	public function showEvaluate(Request $request)
	{
	    $shopid=$request->param("shopid");
		$data=db::view("order_comment","id,num,content,imgurl,add_time at,is_anonymous,an_name")
            ->view("order","add_time,delivery_time","order_comment.order_id=order.id")
            ->view("user","nickname,head_img","order_comment.uid=user.id")
            ->where("order_comment.sid=".$shopid)
            ->select();

        for ($i=0;$i<count($data);$i++)
        {
            if($data[$i]["is_anonymous"]==1){
                $data[$i]["nickname"]=$data[$i]["an_name"];
            }
            //拆分图片
            $val[$i]=$data[$i]["imgurl"];
            $value= explode(',', $val[$i]);
            //当数组为空时去掉""
            if (count($value) == 1 || empty($value) || $value === [""]){
                $data[$i]["imgurl"]=[];
            }else{
                $data[$i]["imgurl"]=$value;
            }

            $data[$i]["at"]=date("Y-m-d",$data[$i]["at"]);
            $data[$i]["songda"]=number_format(($data[$i]["delivery_time"]-$data[$i]["add_time"])/60,0);
            $replay=Db::name("reply")->where("reply_id=".$data[$i]["id"])->find();
            if(empty($replay["reply_content"])){
                $replay["reply_content"]="";
            }
            $data[$i]["reply_content"]=$replay["reply_content"];
            //$data[$i]["replay"][$i]["reply_time"]=date("Y-m-d",$data[$i]["replay"][$i]["reply_time"]);
        }
        return $this->message($data,"成功",2);

	}
    //显示用户全部差评
    public function negativeComment(Request $request)
    {
        $shopid=$request->param("shopid");
        $type=$request->param("type");
        if($type==1){
            //查询差评
            $data=db::view("order_comment","id,num,content,imgurl,add_time at,is_anonymous,an_name")
                ->view("order","add_time,delivery_time","order_comment.order_id=order.id")
                ->view("user","nickname,head_img","order_comment.uid=user.id")
                ->where("order_comment.sid=".$shopid." and num>=0 and num <=2")
                ->select();
        }else{
            //查询好评
            $data=db::view("order_comment","id,num,content,imgurl,add_time at,is_anonymous,an_name")
                ->view("order","add_time,delivery_time","order_comment.order_id=order.id")
                ->view("user","nickname,head_img","order_comment.uid=user.id")
                ->where("order_comment.sid=".$shopid." and num>=3 and num <=5")
                ->select();
        }

        for ($i=0;$i<count($data);$i++)
        {
            if($data[$i]["is_anonymous"]==1){
                $data[$i]["nickname"]=$data[$i]["an_name"];
            }
            //拆分图片
            $val[$i]=$data[$i]["imgurl"];
            $value= explode(',', $val[$i]);
            $data[$i]["imgurl"]=$value;
            //当数组为空时去掉""
            if (count($value) == 1 || empty($value) || $value === [""]){
                $data[$i]["imgurl"]=[];
            }else{
                $data[$i]["imgurl"]=$value;
            }

            $data[$i]["at"]=date("Y-m-d",$data[$i]["at"]);
            $data[$i]["songda"]=number_format(($data[$i]["delivery_time"]-$data[$i]["add_time"])/60,0);
            $replay=Db::name("reply")->where("reply_id=".$data[$i]["id"])->find();
            if(empty($replay["reply_content"])){
                $replay["reply_content"]="";
            }
            $data[$i]["reply_content"]=$replay["reply_content"];
            //$data[$i]["replay"][0]["reply_time"]=date("Y-m-d",$data[$i]["replay"][0]["reply_time"]);
        }

        return $this->message($data,"成功",2);
    }

    //商家回复用户评价
    public function Reply(Request $request)
    {
        $data=$request->param();
        $arr=[
            "shop_id"=>$data["shopid"],//店铺id
            "reply_id"=>$data["replyid"],//回复评价id
            "reply_content"=>$data["reply_content"],//回复内容
            "reply_time"=>time(),
        ];
        $reply=Db::name("reply")->where("shop_id=".$data["shopid"])->find();
        if($reply){
            return $this->message("","已回复,不能重复回复",3);
        }
        $result=db("reply")->insertGetId($arr);
        if($result){
            return $this->message($result,"回复成功",2);
        }else{
            return $this->message($result,"回复失败",3);
        }

    }

    //商家评价骑手
    public function JudgeRider(Request $request)
    {
        $data=$request->param();
        if(empty($data["evaluate"])==" " || empty($data["reason"])==" "){
            return $this->message("","请填写完整",3);
        }
        $arr=[
            'uid'=>$data["uid"],
            'type'=>2,
            'rid'=>$data["rid"],
            'oid'=>$data["oid"],
            'evaluate'=>$data["evaluate"],
            'reason'=>$data["reason"],
            'add_time'=>time()
        ];
        $evaluate=db("user_evaluate_rider")->insert($arr);
        if($evaluate){
            return $this->message($evaluate,"评价成功",2);
        }else{
            return $this->message($evaluate,"评价失败",3);
        }
    }

    /**
     * 查询差评和好评的数量
     */
    public function seCommentNum(Request $request)
    {
        $shopid=$request->param("shopid");
        $goodNum=Db::name("order_comment")->where("num>=3 and num<=5 and sid=".$shopid)->count("id");
        $badNum=Db::name("order_comment")->where("num>=0 and num<=2 and sid=".$shopid)->count("id");
        $commentNum=[
           'goodNum'=>$goodNum,
           'badNum'=>$badNum,
        ];
        return $this->message($commentNum,"成功",2);

    }

    /**
     * 查询骑手对商家的评价 2018-8-3
     */
    public function showRiderEva(Request $request)
    {
        $shopid=$request->param("shopid");
        $evaluate=Db::name("rider_evaluate")->field("id,rider_id,shop_id,content,stars num,img imgurl,add_time at")->where("shop_id=".$shopid)->select();
        for ($i=0;$i<count($evaluate);$i++)
        {
            //拆分图片
            $val[$i]=$evaluate[$i]["imgurl"];
            $evaluate[$i]["imgurl"]= explode(',', $val[$i]);
            $evaluate[$i]["at"]=date("Y-m-d",$evaluate[$i]["at"]);
        }
        return $this->message($evaluate,"成功",2);

    }

    /**
     * 显示骑手对商家的好评/差评 2018-8-3
     */
    public function RiderGoodsOrBad(Request $request)
    {
        $shopid=$request->param("shopid");
        $type=$request->param("type");

        if($type==1){
            //查询差评
            $comment=Db::name("rider_evaluate")->field("id,rider_id,shop_id,content,stars num,img imgurl,add_time at")->where("shop_id=".$shopid." and stars>=0 and stars<=2")->select();
        }else{
            //查询好评
            $comment=Db::name("rider_evaluate")->field("id,rider_id,shop_id,content,stars num,img imgurl,add_time at")->where("shop_id=".$shopid." and stars>=3")->select();
        }
        for ($i=0;$i<count($comment);$i++)
        {
            //拆分图片
            $val[$i]=$comment[$i]["imgurl"];
            $comment[$i]["imgurl"]= explode(',', $val[$i]);
            $comment[$i]["at"]=date("Y-m-d",$comment[$i]["at"]);
        }
        return $this->message($comment,"成功",2);
    }

    /**
     * 查询骑手对商家差评和好评的数量 2018-8-3
     */
    public function ReCommentNum(Request $request)
    {
        $shopid=$request->param("shopid");
        $goodNum=Db::name("rider_evaluate")->where("stars>=3 and shop_id=".$shopid)->count("id");
        $badNum=Db::name("rider_evaluate")->where("stars>=0 and stars<=2 and shop_id=".$shopid)->count("id");
        $commentNum=[
            'goodNum'=>$goodNum,
            'badNum'=>$badNum,
        ];
        return $this->message($commentNum,"成功",2);

    }
}