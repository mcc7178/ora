<?php
namespace Mobile\Controller;
use Think\Controller;
   /**
    * @author Andy 20170304
    * 我的考试
    *
    */
class ExamController extends CommonController{
	//初始化
	function __construct(){
		parent::__construct();
	}
	/*************************************兼容oracle*********************************************/
	/**
	 * 获取列表exam_method  考试方式（1培训考试，2其他考试（工具考试））
	 * @param examStatus 考试状态  1待考试  2已结束  默认为1待考试
	 */
	public function myExam(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
		$page = I('get.page',1,'int');
		$examStatus = I('get.examStatus',1,'int');
        $exam_method = I('get.exam_method','','int');
        $pageNum = 10;
        $start_page = ($page - 1) * $pageNum;
        if($page < 0){
            $this->error(1030,'分页参数有误');
        }
		
        $array = array(1,2);
        if(!in_array($examStatus,$array)){
            $this->error(1030,'考试状态参数有误');
        }
      	if($exam_method == 1){
            $Exam_data = D('Exam')->myExam($examStatus,$userId);
        }elseif($exam_method == 2){
            $Exam_data = D('Exam')->toolExam($examStatus,$userId);
        }else{
            $info = D('Exam')->myExam($examStatus,$userId);
            $infos = D('Exam')->toolExam($examStatus,$userId);
			if(!$info){
				$Exam_data = $infos;
			}elseif(!$infos){
				$Exam_data = $info;
			}elseif($infos && $info){
				$Exam_data = array_merge($info,$infos);
			}else{
				$Exam_data = array();
			}
        }
        if($examStatus == 1){
            $sort = array(
                'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => 'start_time',       //排序字段
            );
        }else{
            $sort = array(
                'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => 'end_time',       //排序字段
            );
        }
         //排序后分页
        $arrayData = D('Common')->array_sort($Exam_data,$sort,$start_page,$pageNum);
        if(empty($arrayData)){
            $this->error(1030,'暂无考试数据');
           /* if($examStatus == 1){
                $this->error(1030,'管理员还没有给当前用户安排考试');
            }else{
                $this->error(1030,'还没有已结束的考试数据');
            }*/
        }else{
            $this->success(1000,'获取数据成功',$arrayData);
        }
    }

	/**
	 * 参加考试-获取试题 post
	 * @param["examId"] 试卷ID int 必须
     * param["project_id"] 项目ID 必传
     */
	public function getQuest(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
		$post['examId'] = I("post.examId",0,'int');
		$project_id = I("post.project_id");
        if($post["examId"] < 0){
            $this->error(1030, '缺少必要参数试卷ID examId');
        }
        if(strstr($project_id,'tool')){//工具管理的数据
            $project_id = substr($project_id,4);
            $post['test_id'] = intval($project_id);
            if($post["test_id"] < 0){
                $this->error(1030, '缺少必要参数考试ID');
            }
            $resp = D('Exam')->getQuest_tool($post,$userId);
            if($resp['code'] == 1000){
                $this->success(1000, $resp['message'], $resp['data']);
            }else{
                $this->error(1030, $resp['message']);
            }
        }else{//项目关联的数据
            $post['project_id'] = intval($project_id);
            if($post["project_id"] < 0){
                $this->error(1030, '缺少必要参数项目ID:project_id');
            }

            $resp = D('Exam')->getQuest($post,$userId);
            if($resp['code'] == 1000){
                $this->success(1000, $resp['message'], $resp['data']);
            }else{
                $this->error(1030, $resp['message']);
            }
        }
	}
	
	/**
	 * 参加考试-交卷
	 * $param["examId"] 试卷ID int
	 * $param["project_id"] 项目ID int
	 * $param["answer"] 考试答案 json格式 {"89":"A","90":"B"}
	 * use_time 考试用时
	 */
	public function finish(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
		$post['examId'] = I("post.examId",0,'int');
        $project_id = I("post.project_id");
		$post['use_time'] = I("post.use_time",0,'int');
		$answer= I("post.answer",'','trim');
		if($post["examId"] < 0){
			$this->error(1030, '缺少必要参数试卷id examId');
		}
        if($post["use_time"] < 1){
            $this->error(1030, '缺少必要参数：考试花费时间 use_time');
        }
        $answerArray = json_decode($answer, true);
        $post['answer'] = $answerArray;
        if(strstr($project_id,'tool')){//工具管理的数据
            $project_id = substr($project_id,4);
            $post['test_id'] = intval($project_id);
            if($post["test_id"] < 0){
                $this->error(1030, '缺少必要参数考试ID');
            }
            $_info = D('Exam')->finish_tool($post,$userId);
            if($_info['code'] == 1000){
                $this->success(1000,$_info['message']);
            }else{
                $this->error(1030,$_info['message']);
            }
        }else{//项目关联的数据
            $post['project_id'] = intval($project_id);
            if($post["project_id"] < 0){
                $this->error(1030, '缺少必要参数项目ID:project_id');
            }
            $info = D('Exam')->finish($post,$userId);
        }
        if($info['code'] == 1000){
            $this->success(1000,$info['message']);
        }else{
            $this->error(1030,$info['message']);
        }
	}
	
	/**
	 * 已结束-查看详情
	 * @param["examId"] 试卷ID int 必须
     * @param["project_id"] 项目ID 必传
	 */
	public function seeDetail(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
		$post['examId'] = I("post.examId",0,'int');
        $project_id = I("post.project_id");
        if($post["examId"] < 0){
            $this->error(1030, '缺少必要参数试卷id examId');
        }
        if(strstr($project_id,'tool')){//工具管理的数据
            $project_id = substr($project_id,4);
            $post['test_id'] = intval($project_id);
            if($post["test_id"] < 0){
                $this->error(1030, '缺少必要参数考试ID');
            }
            $_info = D('Exam')->seeDetail_tool($post,$userId);
            if($_info['code'] == 1000){
                $this->success(1000, $_info['message'], $_info['data']);
            }else{
                $this->error(1030, $_info['message']);
            }
        }else{//项目关联的数据
            $post['project_id'] = intval($project_id);
            if($post["project_id"] < 0){
                $this->error(1030, '缺少必要参数项目ID:project_id');
            }
            $info = D('Exam')->seeDetail($post,$userId);
        }
        if($info['code'] == 1000){
            $this->success(1000, $info['message'], $info['data']);
        }else{
            $this->error(1030, $info['message']);
        }
       /* if($_info && !$info){
            $res = $_info;
        }elseif(!$_info && $info){
            $res = $info;
        }elseif($_info &&  $info){
            $res = array_merge_recursive($_info,$info);
        }
        if($res){
            $this->success(1000, "获取成功", $res);
        }else{
            $this->error(1030, "暂无数据返回");
        }*/
	}    
}