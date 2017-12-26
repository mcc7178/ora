<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/11
 * Time: 17:45
 */

namespace Mobile\Controller;
use Think\Exception;

/**
 * @author Dujunqiang 20170312
 * 培训班管理
 */
class ManageController extends CommonController{
	/**
	 * 初始化
	 */
	function __construct(){
		parent::__construct();
	}
	
	//验证是否获取到用户，是否提交数据
	public function currVerifi(){
		$userId = $this->getUserId();
		if(empty($userId)){
			$this->error(1024, '用户不存在');
		}
	
		if(!IS_POST || !$_POST){
			$this->error(1025, '不合法数据请求');
		}
	
		return $userId;
	}
	
	/**
	 * 培训班首页-培训班列表
	 */
	public function index(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //$keyword = I('')
		$return = D("Manage")->index($_POST, $userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}


    /*********************************************兼容oracle**********************************************************/
    /**
     * 新建培训班
     */
    public function  createTrainingClass(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //接收培训类型参数   0内部培训 1外派培训 2外出授课  默认0
        $data['trainingCategory'] = I('post.trainingCategory',0,'int');
        //接收计划内外参数   0计划内  1计划外  默认0
        $data['planCategory'] = I('post.planCategory',0,'int');
        //接收培训班名称参数
        $data['project_name'] = I('post.project_name','','trim');
         //接收培训班开始时间参数
         $data['start_time'] = I('post.start_time','','trim');
         //接收培训班结束时间参数
         $data['end_time'] = I('post.end_time','','trim');
         //接收培训班预算参数
         $data['project_budget'] = I('post.project_budget','','trim');
        if($data['project_budget'] < 0){
            $this->error(1030,'项目预算为非负数');
        }
         //接收培训班指定参与人参数,用英文逗号分隔开的字符串
         $data['join_users'] = I('post.join_users','','trim');
         $data['project_description'] = I('post.project_description','','trim');
        $Manage = D("Manage");
       if(!$Manage->token(false)->create($data,1)){
           //验证不通过，则抛出错误提示
           $this->error(1030,$Manage->getError());
       }else{
           $project_id = $Manage->createTrainingClass($data,$userId);
               if($project_id){
                   $this->success(1000,'添加培训班成功，请继续添加课程/考试/调研', array('project_id'=>$project_id));
               }else{
                   $this->error(1030, '添加失败，请重新添加');
               }
         }
    }
	/**
	 * 新建培训班
	 * project_name 培训班名称
	 * start_time 开始时间
	 * end_time 结束时间
	 * join_users 指定参与人user_id,英文逗号隔开 123,124,125,....
	 * project_budget 项目预算
	 * project_description 培训班简介
	 * project_id 项目Id，-------修改时用到
	 */
	public function creates(){
		$userId = $this->currVerifi();
		$post = I("post.");
		
		if(!$post["project_name"] || $post["project_name"] == ""){
			$this->error(1011, "请填写培训班名称");
		}
		
		/* if (!preg_match('/^([a-zA-Z]|[\x{4e00}-\x{9fa5}]|_|\.|[0-9]){2,30}$/u', $post["project_name"])){
			$this->error(1011, "培训班名称支持汉字字母数字下划线，长度2-30");
		} */
		
		$timeVerifi = self::timeVerifi($post["start_time"], $post["end_time"]);
		if($timeVerifi["code"] != 1000){
			$this->error(1016, $timeVerifi["message"]);
		}
		
		if($post["join_users"] != "" && !preg_match("/^([0-9]|,){1,}$/", $post["join_users"])){
			$this->error(1017, "请选择参与人");
		}

		$post["project_budget"] += 0;
		if($post["project_budget"] < 0){
			$this->error(1017, "请输入项目预算，必须为不小于0的数字");
		}
		
		if($post["project_description"] != "" && !preg_match('/^([a-zA-Z]|[\x{4e00}-\x{9fa5}]|_|\.|[0-9]){2,200}$/u', $post["project_description"])){
			$this->error(1011, "简介支持汉字字母数字下划线，长度2-200");
		}
		
		$return = D("Manage")->creates($post,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
	
	/**
	 * 获取负责人列表
	 * 
	 */
	public function getManage(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
		$return = D("Manage")->getManage($_POST,$userId);
		if($return["code"] == 1000){
			$this->success($return["code"], $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}


    /**************************************************兼容oracle新建培训班获取指定人员******************************************************************/
	/**
	 * 培训项目 - 指定人员
	 * project_id 项目id，创建不传，修改传
	 */
	public function getJoin(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $project_id = I('post.project_id',0,'int');
        //搜索关键字
        $keyword = I('post.keyword','','trim,htmlspecialchars');
		if($project_id < 0){
				$this->error(1030, "参数有误： project_id 项目id");
		}
		$return = D("Manage")->getJoin($project_id,$keyword,$userId);
		if($return["code"] == 1000){
			$this->success($return["code"], $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
	
	/**
	 * 保存项目 - 不需要，项目基本信息创建完 返回project_id 由此关联其下创建的考试 调研 课程
	 */
	/* public function saveProject(){
		$userId = $this->currVerifi();
	
		$return = D("Manage")->saveProject($_POST,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	} */

	
	/**
	 * 添加课程-选择授课讲师
	 */
	public function getLector(){
		$userId = $this->currVerifi();
	
		$return = D("Manage")->getLector($_POST,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
	
	/*//验证时间
	public function timeVerifi($startTime, $endTime){
		if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $startTime)){
			return array("code"=>1030, "message"=>"开始时间格式不正确");
		}
		
		if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $endTime)){
			return array("code"=>1030, "message"=>"结束时间格式不正确");
		}
		
		if(strtotime($startTime) < time()){
			return array("code"=>1030, "message"=>"开始时间必须大于当前时间");
		}
		
		if(strtotime($endTime) < time()){
			return array("code"=>1030, "message"=>"结束时间必须大于当前时间");
		}
		
		if(strtotime($startTime) >= strtotime($endTime)){
			return array("code"=>1030, "message"=>"结束时间必须大于开始时间");
		}
		return array("code"=>1000, "message"=>"输入正确");
	}*/

    /**************************************************兼容oracle******************************************************************/
    /**
     * 新建项目添加课程-选择面授或在线课程
     * @course_way 1在线  2面授
     */
    public function getCourse(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $course_way = I('post.type',1,'int');
        $return = D("Manage")->getCourse($course_way,$userId);
        if($return["code"] == 1000){
            $this->success(1000, $return["message"], $return["data"]);
        }else{
            $this->error($return["code"], $return["message"]);
        }
    }

    /**************************************************兼容oracle******************************************************************/
	/**
	 * 添加课程-保存课程
	 * project_id 项目id
	 * course_id 选择的课程id
	 * course_name 课程名称
	 * lecturer_id 讲师id
	 * start_time 开始时间
	 * end_time 结束时间
	 * location 授课地址
	 * is_attachment 考勤 (0-关闭,1-开启)
	 *
	 */
	public function saveCourse(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $post["project_id"] = I("post.project_id",'','int');
        $post["course_id"] = I("post.course_id",'','int');
        $post["lecturer_id"] = I("post.lecturer_id",0,'int');
        $post["is_attachment"] = I("post.is_attachment",0,'int');
        $post["lecturer_id"] = I("post.lecturer_id",0,'int');
        $post["location"] = I("post.location",'','trim,htmlspecialchars');
        $post["lecturer_name"] = I("post.lecturer_name",'','trim,htmlspecialchars');
        $post["start_time"] = I("post.start_time",'','trim,htmlspecialchars');
        $post["end_time"] = I("post.end_time",'','trim,htmlspecialchars');
        $post["auth_user_id"] = $userId;
        $type = I("post.type",1,'trim,int');
        if($type == 1){
            $post["location"] = 11;//如果是在线课程则地址默认为11
        }
        $ProjectCourse = D("Manage"); // 实例化User对象
        //create方法调用数组中的规则进行验证
        if (!$ProjectCourse->token(false)->create($post,1)){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error(1030,$ProjectCourse->getError());
        }else{
            // 验证通过 可以进行其他数据操作
            // 判断课程添加时间是否在项目设定时间范围内
            if(!$this->checkTime($post["project_id"],$post["start_time"],$post["end_time"])){
                $this->error(1030,'课程开始时间或结束必须在项目设定时间范围内');
            };
        }
		$result = $ProjectCourse->saveCourse($post,$userId,$type);
		if($result['code'] == 1000){
			$this->success(1000, $result['message']);
		}else{
			$this->error(1030, $result['message']);
		}
	}



	/**
	 * 获取试卷
	 */
	public function getTest(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
		$return = D("Manage")->getTest($userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
	
	/**
	 * 保存考试
	 * project_id 项目id
	 * test_id 试卷id 
	 * manager_id 负责人id
	 * start_time 开始时间
	 * end_time 结束时间
	 */
	public function saveExam(){
		$userId = $this->currVerifi();
		$post = I("post.");
		$post["project_id"] += 0;
		$post["test_id"] += 0;
		$post["manager_id"] += 0;
		if(!is_int($post["project_id"]) || $post["project_id"] < 1){
			$this->error(1011, "请提交项目id");
		}
		if(!is_int($post["test_id"]) || $post["test_id"] < 1){
			$this->error(1012, "请选择试卷");
		}
		if(!is_int($post["manager_id"]) || $post["manager_id"] < 1){
			$this->error(1013, "请选择负责人");
		}
		
		$timeVerifi = self::timeVerifi($post["start_time"], $post["end_time"]);
		if($timeVerifi["code"] != 1000){
			$this->error(1016, $timeVerifi["message"]);
		}
		
		$return = D("Manage")->saveExam($_POST,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
	
	/**
	 * 添加调研 / 评估 - 选择问卷
	 */
	public function getSurvey(){
		$userId = $this->currVerifi();
	
		$return = D("Manage")->getSurvey($_POST,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
	
	/**
	 * 添加调研 / 评估 - 保存调研
	 * project_id 项目id
	 * survey_id 调研id 
	 * manager_id 负责人id
	 * start_time 开始时间
	 * end_time 结束时间
	 */
	public function saveSurvey(){
		$userId = $this->currVerifi();
		$post = I("post.",'trim');
		$post["project_id"] += 0;
		$post["survey_id"] += 0;
		$post["manager_id"] += 0;
		
		if(!is_int($post["project_id"]) || $post["project_id"] < 1){
			$this->error(1011, "请提交项目id");
		}
		if(!is_int($post["survey_id"]) || $post["survey_id"] < 1){
			$this->error(1012, "请选择问卷");
		}
		if(!is_int($post["manager_id"]) || $post["manager_id"] < 1){
			$this->error(1013, "请选择负责人");
		}
		
		$timeVerifi = self::timeVerifi($post["start_time"], $post["end_time"]);
		if($timeVerifi["code"] != 1000){
			$this->error(1016, $timeVerifi["message"]);
		}
		
		$return = D("Manage")->saveSurvey($_POST,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
	
	/**
	 * 获取已添加的（课程 / 考试 / 调研问卷） 列表
	 * project_id 项目id
	 */
	public function getAddList(){
		$userId = $this->currVerifi();
		$_POST["project_id"] += 0;
		if(!is_int($_POST["project_id"]) || $_POST["project_id"] < 1){
			$this->error(1011, "缺少参数： project_id 项目id");
		}
		
		$return = D("Manage")->getAddList($_POST,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}

    /**
     * 获取课程，考试，调研的编辑数据
     * @$proType  该字段区分课程，考试，调研 1课程 2考试 3调研
     * @$project_id  项目id
     */
    public function getUpdateDetail(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        //$proType 1课程 2考试 3调研
        $proType = I('get.proType','','int');
        $project_id = I('get.project_id','','int');
        if($proType == 1){
            $editId = I('get.course_id','','int');
        }
        else if($proType == 2){
            $editId = I('get.test_id','','int');
        }
        else if($proType == 3){
            $editId = I('get.survey_id','','int');
        }
        if($proType == '' || $project_id == '' || $editId == ''){
             $this->error(1023,'缺少需要编辑的项目id或课程或考试或调研id');
        }
        $info = D('Manage')->getUpdateDetail($proType,$project_id,$editId,$userId);
        if($info){
            $this->success(1000,'获取数据成功',$info);
        }else{
            $this->error(1030,'暂无数据返回');
        }
    }
	/**
	 * 项目详情查看 
	 * project_id 项目ID
	 * seeType 查看类型 0查看项目介绍 1查看课程 2查看考试 3查看调研,默认0
	 */
	public function seeDetail(){
		$userId = $this->currVerifi();
		$_POST["project_id"] += 0;
		$_POST["seeType"] += 0;
		if(!is_int($_POST["project_id"]) || $_POST["project_id"] < 1){
			$this->error(1011, "缺少参数： project_id 项目id");
		}
		if(!is_int($_POST["seeType"]) || $_POST["seeType"] > 3 || $_POST["seeType"] < 0){
			$this->error(1012, "缺少参数： seeType 查看类型");
		}
		
		$return = D("Manage")->seeDetail($_POST,$userId);
		if($return){
			$this->success(1000,'获取数据成功', $return);
		}else{
			$this->error(1030, '暂无数据返回');
		}
	}
	
	/**
	 * 编辑项目--获取编辑数据
	 * project_id 项目id
	 */
	public function getEdit(){
		$userId = $this->currVerifi();
		
		$_POST["project_id"] += 0;
		if(!is_int($_POST["project_id"]) || $_POST["project_id"] < 1){
			$this->error(1011, "缺少参数： project_id");
		}
		
		$return = D("Manage")->getEdit($_POST,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
	
	/**
	 * 保存编辑后的项目 --- 传入 create 处理
	 */
	public function saveEdit(){
		$_POST["project_id"] += 0;
		if(!is_int($_POST["project_id"]) || $_POST["project_id"] < 1){
			$this->error(1011, "缺少参数： project_id");
		}
		
		self::create($_POST);
	}
	
	/*
	课程编辑 保存 删除
	考试编辑 保存 删除
	调研编辑 保存 删除
	 */
	
	/**
	 *
	 */
	public function exampleAction(){
		$userId = $this->currVerifi();
	
		$return = D("Manage")->exampleAction($_POST,$userId);
		if($return["code"] == 1000){
			$this->success(1000, $return["message"], $return["data"]);
		}else{
			$this->error($return["code"], $return["message"]);
		}
	}
}