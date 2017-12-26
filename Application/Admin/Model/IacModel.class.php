<?php 
namespace Admin\Model;
use Common\Model\BaseModel;
/**
 * 中保协学习相关
 * @author Dujuqiang 20171121
 * 
 * 中国保险行业协会（The Insurance Association of China，IAC），简称中保协
 */
class IacModel extends BaseModel{
	//初始化
	public function __construct(){}
	
	/**
	 * 中保协学习首页
	 */
    public function index(){
    	$user_id = $_SESSION["user"]["id"];
    	$user = M("users")->where("id=".$user_id)->find();
    	return $user;
    }

    /**
     * 中保协数据导入页面
     *
     */
    public function listPage($param){
    	$pageLen = 15;
    	$start = ($param["p"] - 1) * $pageLen;
    	$where = array();
    	
    	if($param["year"]){
    		$where["add_time"] = array("like", $param["year"]."%");
    	}
    	
    	$list = M("iac_file")->where($where)->order("id desc")->limit($start, $pageLen)->select();
    	foreach ($list as $key=>$value){
    		$userInfo = M("users")->where("id=".$value["auth_user_id"])->find();
    		$list[$key]["username"] = $userInfo["username"];
    	}
    	
    	$count = M("iac_file")->field("count(id) as t_num")->where($where)->select();
    	$pageNav = $this->pageClass($count[0]["t_num"], $pageLen);
    	
    	$set = M("iac_set")->find();
    	$iacStatus = $set["status"];
    	
    	return array("list"=>$list, "pageNav"=>$pageNav, "iacStatus"=>$iacStatus);
    }
    
    //获取中保协设置
    public function getIacStatus(){
    	$set = M("iac_set")->find();
    	$iacStatus = $set["status"];
    	if(!$iacStatus){
    		$iacStatus = 0;
    	}
    	
    	return $iacStatus;
    }
    
    /**
     * 获取中保协总计数据
     * 输入：$user_id 用户ID,多个ID英文逗号隔开
     * 
     * 返回：
     * study_num 学习课程数量
     * study_len 学习时长单位秒
     * study_credit 获得学分
     */
    public function getIacStudy($user_id, $month=''){
    	if(!$user_id){
    		return array("study_num"=>0, "study_len"=>0, "study_credit"=>0);
    	}
    	$iacStatus = self::getIacStatus();
    	if($iacStatus == 0){
    		return array("study_num"=>0, "study_len"=>0, "study_credit"=>0);
    	}
    	
    	$where = array();
    	$users = explode(",", $user_id);
    	if(count($users) == 1){
    		$where["user_id"] = $users[0];
    	}else{
    		foreach ($users as $key=>$value){
    			if((int)$value == 0){
    				unset($users[$key]);
    			}
    		}
    		$where["user_id"] = array("in", $users);
    	}
    	
    	if(preg_match("/^[0-9]{4}-[0-9]{2}$/", $month)){
    		$where["study_time"] = array("like", $month."%");
    	}
    	
    	//学习的课程总数
    	$study_num = M("iac_content")->field("count(id) as t_num")->where($where)->group("course_name")->select();
        $study_num[0]["t_num"] = $study_num[0]["t_num"] ? $study_num[0]["t_num"] : 0;
    	//学习时长
    	$study_len = M("iac_content")->field("sum(study_len) as t_num")->where($where)->select();
    	$study_len = $study_len[0]["t_num"];
    	if(!$study_len) $study_len = 0;
    	
    	//获得学分(1小时1学分)
    	$study_credit = round($study_len / 3600, 2);
    	
    	return array("study_num"=>$study_num[0]["t_num"], "study_len"=>$study_len, "study_credit"=>$study_credit);
    }
    
    /**
     * 获取中保协课程学习的课程列表
     */
    public function getStudyList($user_id){
    	$iacStatus = self::getIacStatus();
    	if($iacStatus == 0){
    		return array();
    	}
    	
    	$list = M("iac_content")->where("user_id=".$user_id)->select();
    	return $list;
    }
    
    /**
     * 中保协文件内容展示页面
     *
     */
    public function content(){
    	$id = I("get.id");
    	$id = (int)$id;
    	$page = I("get.p");
    	if($page < 1){
    		$page = 1;
    	}
    	$pageLen = 15;
    	$start = ($page - 1) * $pageLen;
    	if($id > 1){
    		$title = I("get.title");
    		if($title){
	    		$where["username"] = array("like", "%$title%");
    		}
    		
    		$where["file_id"] = $id;
    		$list = M("iac_content")->where($where)->limit($start, $pageLen)->select();
    		
    		$count = M("iac_content")->field("count(id) as t_num")->where($where)->select();
    		$pageNav = $this->pageClass($count[0]["t_num"], $pageLen);
    	}
    	
    	return array("list"=>$list, "pageNav"=>$pageNav);
    }
    
    /**
     * 中保协数据导入
     *
     */
    public function import($fileName, $fileCon){
    	$user_id = $_SESSION["user"]["id"];
    	
    	$hasWhere["add_time"] = array("like", date("Y-m")."%");
    	$hasFile = M("iac_file")->where($hasWhere)->find();
    	if($hasFile){
    		return array("code"=>1021, "message"=>"本月已导入过数据，请勿重复导入");
    	}
    	
    	if(strtolower(C('DB_TYPE')) == 'oracle'){
    		$fileData['id'] = getNextId('iac_file');
    	}
    	$fileData["file_name"] = $fileName;
    	$fileData["add_time"] = date("Y-m-d H:i:s");
    	$fileData["add_stamp"] = time();
    	$fileData["status"] = 1;
    	$fileData["auth_user_id"] = $user_id;
    	$lastId = M("iac_file")->add($fileData);
    	if(!$lastId){
    		return array("code"=>1021, "message"=>"导入失败");
    	}
    	
    	foreach ($fileCon as $key=>$value){
    		if($key == 0) continue;//第一行为标题跳过
    		
    		if(!$value[1] || !$value[2] || !$value[3] || !$value[7]){
    			continue;
    		}
    		
    		if(!$value[4]) $value[4] = "--";
    		if(!$value[5]) $value[5] = date("Y-m-d H:i:s");
    		if(!$value[6]) $value[6] = 0;
    		
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
    			$conData['id'] = getNextId('iac_content');
    		}
    		$conData['file_id'] = $lastId;
    		$conData['login_name'] = $value[1];
    		$conData['username'] = $value[2];
    		$conData['email'] = $value[3];
    		$conData['phone'] = $value[4];
    		
    		$user = M("users")->field("id")->where("email='".$value[3]."'")->find();
    		if(!$user){
    			$user["id"] = 0;
    		}
    		$conData['user_id'] = $user["id"];
    		
    		if(preg_match("/^[0-9]{5}/", $value[5])){
	    		$get1970 = intval(($value[5] - 25569) * 3600 * 24); //转换成1970年以来的秒数
	    		$study_time = gmdate('Y-m-d H:i:s', $get1970);//格式化时间
    		}else{
    			$study_time = $value[5];
    		}
    		$conData['study_time'] = $study_time;
    		$conData['study_len'] = $value[6];
    		$conData['course_name'] = $value[7];
    		M("iac_content")->add($conData);
    	}
    	
    	return array("code"=>1000, "message"=>"导入成功");
    }

}