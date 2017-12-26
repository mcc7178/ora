<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 定时任务处理
 * 20171020---更改为任务审核通过后就通知
 * --------为了oracle处理尽可能的快，此处使用原生语句-------------
 * 处理机制：没30分钟执行一次，已通知过的数据（项目、工具管理-考试、工具管理-调研）记录到cron_message表中，每次取差集数据进行处理
 * 
 */
class CronMessageController extends HomeBaseController{
	/**
	 * 定时处理消息推送 $execMin分钟执行一次，每次获取半小时前审核通过的数据
	 */
	public function cron(){
		//通用触发 D('Trigger')->messageTrigger("消息接收人", "通知标题", "创建时间", "消息类型", "消息发起人", "消息查看地址");
		/*
		1	课程制作	你有课程制作的任务，任务信息如下--no
		2	试卷制作	你有试卷制作的任务，任务信息如下--no
		3	问卷制作	你有问卷制作的任务，任务信息如下--no
		4	授课任务	你有面授课程即将开始开讲，信息如下-------ok
		5	成绩发布	你有考试成绩即将发布，信息如下-----ok
		6	调研结果	你有调研结果待查看，点击前往------ok
		7	审批任务	你有新的审批任务，点击前往 --行为触发--不推送
		8	统计调研	你有统计调研的任务，信息如下--no
		9	签到提醒	你有签到提醒的任务，信息如下-----ok
		10	课程学习	你有待学习课程即将开始，信息如下-----ok
		11	参加考试	你有待参加考试即将开始，信息如下-----ok
		12	参与调研	你有待参与调研即将开始，信息如下-----ok
		13	计划总结	你有已完成项目待写总结，信息如下-----ok
		14	互动消息	你的互动有新的评论/赞，请查看 --行为触发
		15	问答消息	你的问答有新消息，请查看： --行为触发
		*/
		$limitTime = time() - 180*86400;//只处理半年内180天的数据
		
		//项目相关-审核通过进行中
		$sql = "select a.id,a.user_id from __ADMIN_PROJECT__ a where type='0' and id not in(
			select source_id from __CRON_MESSAGE__ where type='1' and timestamp>'$limitTime')";
		$project = M()->query($sql);
    	foreach ($project as $proValue){
    		$projectId = $proValue["id"];
    		//项目关联学员
    		$sqlPerson = "select * from __DESIGNATED_PERSONNEL__ where project_id='$projectId' and sign_up='1'";
    		$students = M()->query($sqlPerson);
    		if(!$students){
    			continue;
    		}
    		
    		//项目课程 COURSE_ID
    		$sql01 = "select course_id,course_names,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time from __PROJECT_COURSE__ where project_id=".$projectId;
    		$proCourse = M()->query($sql01);
    		foreach ($proCourse as $courseValue){
    			if($courseValue["course_id"]){
	    			$cSql = "select course_name,course_way from __COURSE__ where id=".$courseValue["course_id"];
	    			$course = M()->query($cSql);
    			}
    			
    			if(!$courseValue["course_names"] && $course[0]["course_name"]){
    				$courseValue["course_names"] = $course[0]["course_name"];
    			}
    			$noticeName = $courseValue["course_names"];
    			$noticeTime = $courseValue["start_time"];
    			
    			foreach ($students as $key2=>$value2){
    				$seeUrl = "admin/my_course/detail/course_id/".$courseValue["course_id"]."/project_id/$projectId";
    				    			
    				//10课程学习 通知到学员
    				D('Trigger')->messageTrigger($value2["user_id"], "你有待学习课程[$noticeName]即将开始，开始时间：".$noticeTime, date("Y-m-d H:i:s"), 10, $proValue["user_id"], $seeUrl);
    			}
    			
    			//面授课通知到讲师
    			if($course[0]["course_way"] == 1){
    				//待定
    			}
    		}
    		
    		//项目考试  TEST_ID
    		$sql02 = "select test_id,test_names,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time from __PROJECT_EXAMINATION__ where project_id=".$projectId;
    		$proExam = M()->query($sql02);
    		foreach ($proExam as $examValue){
    			$exam = array();
    			if($examValue["test_id"]){
	    			$eSql = "select * from __EXAMINATION__ where id=".$examValue["test_id"];
	    			$exam = M()->query($eSql);
    			} 
    			if(!$examValue["test_names"] && $exam[0]["test_name"]){
    				$examValue["test_names"] = $exam[0]["test_name"];
    			}
    			
    			$noticeName = $examValue["test_names"];
    			$noticeTime = $examValue["start_time"];
    			
    			//通知到学员
    			foreach ($students as $key2=>$value2){
    				if($examValue["cacheid"] > 0){
    					//线下考试
    					$seeUrl = "admin/my_exam/lookresultoffline/examination_id/0/project_id/".$projectId;
    				}else{
    					//线上考试
    					$seeUrl = "admin/my_exam/joinexam/examination_id/".$examValue["test_id"]."/project_id/".$projectId;
    				}
    				D('Trigger')->messageTrigger($value2["user_id"], "你有待参加考试[$noticeName]即将开始，考试时间：".$noticeTime, date("Y-m-d H:i:s"), 11, $proValue["user_id"], $seeUrl);
    			}
    		}
    		
    		//项目调研  SURVEY_ID
    		$sql03 = "select survey_id,survey_names,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time from __PROJECT_SURVEY__ where project_id=".$projectId;
    		$proSurvey = M()->query($sql03);
    		foreach ($proSurvey as $surveyValue){
    			if($surveyValue["survey_id"]){
	    			$sSql = "select * from __SURVEY__ where id=".$surveyValue["survey_id"];
	    			$survey = M()->query($sSql);
    			}
    			
    			if(!$surveyValue["survey_names"] && $survey){
    				$surveyValue["survey_names"] = $survey[0]["survey_name"];
    			}
    			
    			$noticeName = $surveyValue["survey_names"];
    			$noticeTime = $examValue["start_time"];
    			
    			foreach ($students as $key2=>$value2){
    				$seeUrl = "admin/my_survey/joinsurvey/survey_id/".$surveyValue["survey_id"]."/project_id/$projectId/typeid/0";
    				D('Trigger')->messageTrigger($value2["user_id"], "你有待参与调研[$noticeName]即将开始，开始时间：".$noticeTime, date("Y-m-d H:i:s"), 12, $proValue["user_id"], $seeUrl);
    			}
    		}
    		
    		//保存已处理过的数据
    		$cronData["source_id"] = $proValue["id"];
    		$cronData["type"] = 1;
    		$cronData["timestamp"] = time();
    		$cronData["timestr"] = date("Y-m-d H:i:s");
    		M("cron_message")->add($cronData);
    	}
		
    	//----------------工具相关--------------------
		//工具相关--考试
    	$sql = "select id,name,examination_id,create_user,type,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time from __TEST__ a 
		where audit_status='0' and id not in(select source_id from __CRON_MESSAGE__ where type='2' and timestamp>'$limitTime')";
    	$toolExam = M()->query($sql);
		foreach ($toolExam as $key=>$value){
			
			$noticeName = $value["name"];
			$noticeTime = $value["start_time"];
			
			//通知到学员
			$students = M("test_user_rel")->where("test_id=".$value["id"])->select();
			foreach ($students as $key2=>$value2){
				if($value["type"] == 1){
					//线下考试
					$seeUrl = "admin/my_exam/lookresultoffline/test_id/".$value["id"]."/examination_id/0/flag/flag";
				}else{
					//线上考试
					$seeUrl = "admin/my_exam/joinexam/test_id/".$value["id"]."/examination_id/".$value["examination_id"]."/flag/flag";
				}
				D('Trigger')->messageTrigger($value2["user_id"], "你有待参加考试[$noticeName]即将开始，开始时间：".$noticeTime, date("Y-m-d H:i:s"), 11, $value["create_user"], $seeUrl);
			}
			
			//保存已处理过的数据
			$cronData["source_id"] = $value["id"];
			$cronData["type"] = 2;
			$cronData["timestamp"] = time();
			$cronData["timestr"] = date("Y-m-d H:i:s");
			M("cron_message")->add($cronData);
		}
		
		//工具相关--调研
		$sql = "select id,research_name,survey_id,create_user,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time from __RESEARCH__ a 
		where audit_state='1' and id not in(select source_id from __CRON_MESSAGE__ where type='3' and timestamp>'$limitTime')";
		$toolSurvey = M()->query($sql);
		foreach ($toolSurvey as $key=>$value){
			$noticeName = $value["research_name"];
			$noticeTime = $value["start_time"];
			
			//通知到学员
			$tissue = M("research_tissueid")->where("research_id=".$value["id"])->select();
			foreach ($tissue as $thisTissue){
				$students = M("tissue_group_access")->where("tissue_id=".$thisTissue["tissue_id"])->select();
				foreach ($students as $key2=>$value2){
					$seeUrl = "admin/my_survey/joinsurvey/survey_id/".$value["survey_id"]."/research_id/".$value["id"]."/typeid/1";
					D('Trigger')->messageTrigger($value2["user_id"], "你有待参与调研[$noticeName]即将开始，开始时间：".$noticeTime, date("Y-m-d H:i:s"), 12, $value["create_user"], $seeUrl);
				}
			}
			
			//保存已处理过的数据
			$cronData["source_id"] = $value["id"];
			$cronData["type"] = 3;
			$cronData["timestamp"] = time();
			$cronData["timestr"] = date("Y-m-d H:i:s");
			M("cron_message")->add($cronData);
		}
		
		//此处验证脚本是否执行--随便找的表
		$testData["id"] = mt_rand(1, 10000);
		$testData["user_id"] = date("mdHis");
		$testData["company_id"] = 10;
		M("user_company")->add($testData);
	}
}
