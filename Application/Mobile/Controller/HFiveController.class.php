<?php
namespace Mobile\Controller;
use Think\Controller;
/**
 * @author Dujunqiang 20170329
 * APP h5页面相关
 */
class HFiveController extends CommonController {
	/**
	 * 初始化
	 */
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * 调研结果
	 * method：post
	 * survey_id 调研id
	 */
	public function surveyResult(){
		$post = I("post.");
		$post["survey_id"] += 0;
		if(!is_int($post["survey_id"]) || $post["survey_id"] < 1){
			$data["code"] = 1011;
			$data["message"] = "未获取到问卷id";
		} 
		
		$this->display("HFive/survey");
	}
	
	/**
	 * 日程提醒
	 * method:get
	 */
	public function calendar(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $user_id = $this->verifyUserDataGet();
		$resp = D("HFive")->calendar($user_id);
		$jsonStr = "";
		$date1 = array();//已有日期-考试
		$date2 = array();//已有日期-调研
		if(is_array($resp)){
			foreach ($resp as $key=>$value){
				//不要连线 不要多任务
				$thisDate = date("Y-m-d", strtotime($value["start_time"]));
				if($value["type"] == 1){
					$singleColor = "ff0000";
					if(in_array($thisDate, $date1)){
						continue;
					}
					$date1[] = $thisDate;
				}else{
					$singleColor = "fff100";
					if(in_array($thisDate, $date2)){
						continue;
					}
					$date2[] = $thisDate;
				}
				
				$value["start_time"] = date("d-m-Y 12:01", strtotime($value["start_time"]));
				$value["end_time"] = date("d-m-Y 13:01", strtotime($value["start_time"]));
				
				$jsonStr .= '{"start": "' .$value["start_time"]. '",
                  "end": "' .$value["end_time"]. '",
                  "singleColor": "'. $singleColor .'"},';
			}
			$jsonStr = substr($jsonStr, 0, -1);
		}
		$data["data"] = $jsonStr;
		$this->assign($data);
		$this->display("HFive/calendar");
	}
	
	/**
	 * 资讯详细
	 * method:get
	 * new_id 资讯id
	 */
	public function news(){
        //$this->setHeader();
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
		$new_id = I("get.new_id",0,'int');
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "id,title,type,content,img,template,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time";
        }else{
            $field = "id,title,type,content,create_time,img,template";
        }
        if($new_id > 1){
            $resp = M("news")->where(array("id" =>$new_id))->field($field)->find();
            $content =  strip_tags(htmlspecialchars_decode($resp['content']));
            $resp['content'] = $content;
            if($resp['template'] == 1){//公司资讯
                if(empty($resp['img'])){
                    //如果没有图片就给默认图片
                    $resp['img'] = C('APP_DEFAULT_IMAGES').'/Upload/news/default/company_news.png';
                }
            }elseif($resp['template'] == 2){//综合资讯
                if(empty($resp['img'])){
                    //如果没有图片就给默认图片
                    $resp['img'] = C('APP_DEFAULT_IMAGES').'/Upload/news/default/colligate_news.png';
                }
            }
            if($resp){
                $data = array(
                    'code' => 1000,
                    'message' => '获取成功',
                    'data' => $resp
                );
            }else{
                $data = array(
                    'code' => 1030,
                    'message' => '暂无数据返回',
                );
            }
        }else{
            $data = array(
                'code' => 1030,
                'message' => '未获取到资讯id'
            );
        }
		$this->assign($data);
		$this->display("HFive/news");
	}
	
	/**
	 * 积分规则
	 */
	public function score(){
        $userId = $this->verifyUserDataGet();
        $plan_id = getPlanId($userId);//获取方案id
        $_list = D("HFive")->integrationList($plan_id,$userId);
		$data["list"] = $_list;
		$this->assign($data);
		$this->display("HFive/score");
	}
}