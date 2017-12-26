<?php

namespace Common\Model;

use Common\Model\BaseModel;

class CenterModel extends BaseModel
{

	/*
	 * 初始化
	 * 
	 * -------------- 注意：此功能涉及的的表太多，数据太慢，部分代码是纯oracle代码 ，mysql版本见对应的 function_old
	 * 
	 */
    function __construct(){}

    /**
     * 用户中心 - 学习目标 - oracle原生版本，mysql版本见 study_old
     * type 1全部 2在线培训 3线下培训
	 * startTime 开始时间
	 * endTime 查询结束时间
	 * p 页码
     */
    public function study($param){
    	$user_id = $_SESSION["user"]["id"];
    	$nowYearStamp = strtotime(date("Y")."-01-01 00:00:00");
    	$nowYear = date("Y");
    	
    	if(!$param["startTime"] || !$param["endTime"]){
    		//时间不存在，默认当前年份起止时间
    		$param["startTime"] = date('Y-01-01 00:00:00');
    		$endTime = date('Y-01-01 00:00:00', strtotime("+1 year"));
    		$param["endTime"] = date('Y-m-d H:i:s', strtotime($endTime) - 1);
    	}else{
    		$param["startTime"] = $param["startTime"]."-01 00:00:00";
    		$days = date("t", strtotime($param["endTime"]."-01"));//获取当月天数
    		$param["endTime"] = $param["endTime"]."-".$days." 23:59:59";
    	}
    	$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    		select * from __TISSUE_GROUP_ACCESS__ where user_id=$user_id
    	) thinkphp ) WHERE numrow=1";
    	$myTissue = M()->query($sql);
    	$myTissue = $myTissue[0];
    	
    	//每页条数，导出时设置pageLen=10000
    	$pageLen = $param["pageLen"];
    	if(!$param["pageLen"]){
	    	$pageLen = 20;
    	}
    	
    	$start = ($param["p"] - 1) * $pageLen;
    	$pageNav = "";
    	if($param["type"] == 1){
	    	//全部：姓名 所属部门	学习课程数	获得学分	内部培训班	外派培训班	外出授课	获得学分	学习时长	考试次数	问卷次数	笔记数
    		$key = 0;
    		
    		$upCourseNum = 0;//在线学习课程数量
    		$downCourseNum = 0;//线下课程学习数量
    		$upCredit = 0;//线上课程积分
    		$downCredit = 0;//线下课程积分
    		$total_time = 0;//培训项目总学时
    		$up_time = 0;//在线课程学时
    		$total_credit = 0;//培训项目总学分
    		
    		//获取在线课程数据
			$where2 = "user_id=".$user_id;
			$where2.= " and typeid in (4,5)";
			$where2.= " and create_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
			$where2.= " and create_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
    		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    			select credit,source_id,project_id from think_center_study where $where2
    		) thinkphp )";
    		$total_credit = M()->query($sql);
    		
    		//center_study只统计了在线课程的数据
    		foreach ($total_credit as $value){
    			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    				select * from __COURSE__ where id=".$value["source_id"]."
    			) thinkphp )";
    			$course = M()->query($sql);
    			$course = $course[0];
    			if($course){
    				if($course["course_way"] == 0){
    					$upCredit += $value["credit"];
    					$upCourseNum ++;
    					$up_time += $course["course_time"];
    				}
    				//是项目课程
    				if($value["project_id"] != 0){
    					$total_time += $course["course_time"];
    				}
    			}
    		}
    		
    		//获取线下课程数据
    		//培训班--线下课程数据
			$where4 = "a.user_id=".$user_id;
			$where4.= " and a.sign_up=1";
			$where4.= " and c.type in (0,4)";
			$where4.= " and b.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
			$where4.= " and b.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
    		
			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				select b.*, to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as n_end_time from __DESIGNATED_PERSONNEL__ a 
				join __PROJECT_COURSE__ b on a.project_id=b.project_id
				join __ADMIN_PROJECT__ c on a.project_id=c.id where $where4
			) thinkphp )";
			$downPro = M()->query($sql);
			
    		foreach ($downPro as $value){
    			if(strtotime($value["n_end_time"]) > time()){
    				//课程时间未结束，不计算在内
    				continue;
    			}
    			//面授课程数据计算
    			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    				select course_way,course_time from think_course where id=".$value["course_id"]."
    			) thinkphp ) where numrow=1";
    			$course = M()->query($sql);
    			$course = $course[0];
    			if($course["course_way"] == 1){
    				$whereatt = "a.course_id=".$value["course_id"];
    				$whereatt .= " and b.project_id=".$value["project_id"];
    				$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    					select a.attendance_project_id from __ATTENDANCE_COURSE__ a 
    					join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id
    					where $whereatt
    				) thinkphp ) where numrow=1";
    				$attendance_course = M()->query($sql);
	    			if($attendance_course){
	    				//考勤开启
	    				$awhere = "user_id=".$user_id;
	    				$awhere .= " and status in(1,2)";
	    				$awhere .= " and attendance_project_id=".$attendance_course[0]["attendance_project_id"];
	    				$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
	    					select * from __ATTENDANCE__ where $awhere
	    				) thinkphp ) where numrow=1";
	    				$attendance = M()->query($sql);
	    				
	    				if($attendance){
	    					$downCredit += $value["credit"];
	    					$downCourseNum ++;
	    					$total_time += $course["course_time"];
	    				}
	    			}else{
	    				//未设置考勤//考勤关闭
	    				$downCredit += $value["credit"];
	    				$downCourseNum ++;
    					$total_time += $course["course_time"];
	    			}
    			}
    		}
    		
    		$results[$key]["hours"] = round($total_time / 60, 2);
    		
    		$results[$key]["up_time"] = round($up_time / 60, 2);
    		
    		//姓名
    		$user = M("users")->where("id=".$user_id)->find();
    		$results[$key]["username"] = $user["username"];
    		
    		//所属部门
    		$results[$key]["part"] = "";
    		if($myTissue["tissue_id"]){
    			//$tissueRule = M("tissue_rule")->where("id=".$myTissue["tissue_id"])->find();
    			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    				select * from __TISSUE_RULE__ where id=".$myTissue["tissue_id"]."
    			) thinkphp ) where numrow=1";
    			$tissueRule = M()->query($sql);
    			$results[$key]["part"] = $tissueRule[0]["name"];
    		}
    		
    		//学习课程数
    		$results[$key]["course_num"] = $upCourseNum;
    		
    		//获得学分
    		$results[$key]["upCredit"] = $upCredit;
    		$results[$key]["downCredit"] = $downCredit;
    		
    		//内部培训班
			$whereCommon = "a.user_id=".$user_id;
			$whereCommon.= " and a.sign_up='1'";
			$whereCommon.= " and b.type in (0,4)";
			$whereCommon.= " and start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
			$whereCommon.= " and start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			
			$where5 = $whereCommon . " and b.training_category=0";
			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    				select count(a.user_id) as join_num from __DESIGNATED_PERSONNEL__ a
					join __ADMIN_PROJECT__ b on a.project_id=b.id
					where $where5
    			) thinkphp )";
			$project0 = M()->query($sql);
    		/* $project0 = M("designated_personnel a")
    					->field("count(a.user_id) as join_num")
    					->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
    					->where($where5)
    					->select();
    		*/
    		$results[$key]["project0"] = $project0[0]["join_num"];
    		
    		//外派培训班
			$where5 = $whereCommon . " and b.training_category=1";
    		
    		/* $project1 = M("designated_personnel a")
    					->field("count(a.user_id) as join_num")
    					->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
    					->where($where5)
    					->select(); */
			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				select count(a.user_id) as join_num from __DESIGNATED_PERSONNEL__ a
				join __ADMIN_PROJECT__ b on a.project_id=b.id
				where $where5
			) thinkphp )";
			$project1 = M()->query($sql);
	
    		$results[$key]["project1"] = $project1[0]["join_num"];
    		
    		//外出授课
			$where5 = $whereCommon . " and b.training_category=2";
    		
    		/* $project2 = M("designated_personnel a")
    					->field("count(a.user_id) as join_num")
    					->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
    					->where($where5)
    					->select(); */
			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				select count(a.user_id) as join_num from __DESIGNATED_PERSONNEL__ a 
				join __ADMIN_PROJECT__ b on a.project_id=b.id 
				where $where5
			) thinkphp )";
			$project2 = M()->query($sql);
			
    		
    		$results[$key]["project2"] = $project2[0]["join_num"];
    		
    		//考试次数--培训项目 
			$where8 = " a.user_id=".$user_id;
			$where8.= " and a.sign_up='1'";
			$where8.= " and b.type=4";
			$where8.= " and c.start_time > to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
			$where8.= " and c.start_time < to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
    		
    		/* $projectExam = M("designated_personnel a")
    					->field("count(a.user_id) as join_num")
		    			->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
		    			->join("join __PROJECT_EXAMINATION__ c on a.project_id=c.project_id")	
		    			->where($where8)
		    			->order("c.start_time desc")
		    			->select(); */
    		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				select count(a.user_id) as join_num from __DESIGNATED_PERSONNEL__ a
				join __ADMIN_PROJECT__ b on a.project_id=b.id
				join __PROJECT_EXAMINATION__ c on a.project_id=c.project_id
				where $where8 
			) thinkphp ) ";
    		
    		$projectExam = M()->query($sql);
    		
    		//考试次数--工具管理考试
			$where9 = "a.user_id=".$user_id;
			$where9.= " and b.audit_status=0";
			$where9.= " and b.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
			$where9.= " and b.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
    		
    		/* $toolExam = M("test_user_rel a")
    					->field("count(a.user_id) as join_num")
    					->join("join __TEST__ b on a.test_id=b.id")
    					->where($where9)
    					->select(); */
    		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
	    		select count(a.user_id) as join_num from __TEST_USER_REL__ a
	    		join __TEST__ b on a.test_id=b.id
	    		where $where9
    		) thinkphp )";
    		$toolExam = M()->query($sql);
    		
    		$results[$key]["exam_num"] = $projectExam[0]["join_num"] + $toolExam[0]["join_num"];
    		
    		//问卷次数--培训项目
			$where10 = "a.user_id=".$user_id;
			$where10.= " and b.type=4";
			$where10.= " and c.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
			$where10.= " and c.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			
    		/* $projectSurvey = M("survey_attendance a")
    						->field("count(a.user_id) as join_num")
				    		->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
				    		->join("join __PROJECT_SURVEY__ c on a.project_id=c.project_id")
				    		->where($where10)
				    		->order("c.start_time desc")
				    		->select(); */
    		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
	    		select count(a.user_id) as join_num from __SURVEY_ATTENDANCE__ a
	    		join __ADMIN_PROJECT__ b on a.project_id=b.id 
	    		join __PROJECT_SURVEY__ c on a.project_id=c.project_id 
	    		where $where10
    		) thinkphp )";
    		$projectSurvey = M()->query($sql);
    		
    		//问卷次数--工具管理
    		if($myTissue["tissue_id"]){
				$where11 = "a.tissue_id=".$myTissue["tissue_id"];
				$where11.= " and b.audit_state=1";
				$where11.= " and b.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where11.= " and b.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
	    		
	    		/* $toolSurvey = M("research_tissueid a")
	    					->field("count(a.research_id) as join_num")
	    					->join("join __RESEARCH__ b on a.research_id=b.id")
	    					->where($where11)
	    					->select(); */
	    		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
		    		select count(a.research_id) as join_num from __RESEARCH_TISSUEID__ a 
		    		join __RESEARCH__ b on a.research_id=b.id 
		    		where $where11 
	    		) thinkphp )";
	    		$toolSurvey = M()->query($sql);
    		}else{
    			$toolSurvey[0]["join_num"] = 0;
    		}
    		$results[$key]["survey_num"] = $projectSurvey[0]["join_num"] + $toolSurvey[0]["join_num"];
    		
    		//笔记数
			$where12 = "user_id=".$user_id;
			$where12.= " and time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
			$where12.= " and time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
    					
    		//$note = M("course_note")->field("count(id) as num")->where($where12)->select();
    		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
	    		select count(id) as num from __COURSE_NOTE__ 
	    		where $where12
    		) thinkphp )";
    		$note = M()->query($sql);
    		$results[$key]["note_num"] = $note[0]["num"];
    		
    		$iacStatus = D("Iac")->getIacStatus();//是否开启中保协
    		if($iacStatus == 1){
	    		//中保协课程数量
	    		$param["startTime"] = strtotime($param["startTime"]);
	    		$param["endTime"] = strtotime($param["endTime"]);
	    		
	    		$where13["a.user_id"] = $user_id;
	    		$where13['b.add_stamp'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$results = M("iac_content a")->field("a.*")->join("left join __IAC_FILE__ b on a.file_id=b.id")->where($where13)->select();
	    		
	    		$iac_num = 0;
	    		$iac_len = 0;
	    		$iac_credit = 0;
	    		foreach ($results as $key=>$value){
	    			$iac_num ++;
	    			$iac_len += $value["study_len"];
	    		}
	    		$iac_credit = round($iac_len / 3600, 2);
	    		$results[$key]["iac_num"] = $iac_num;
	    		$results[$key]["iac_len"] = $iac_len;
	    		$results[$key]["iac_credit"] = $iac_credit;
    		}
    	}elseif($param["type"] == 2){
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where3 = "user_id=".$user_id;
				$where3.= " and typeid in (4,5)";
				$where3.= " and create_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where3.= " and create_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where3['create_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$where3["user_id"] = $user_id;
	    		$where3["typeid"] = array("in", "4,5");
			}
    		
			if(strtolower(C('DB_TYPE')) == 'oracle'){
	    		$results = M("center_study")->field("id,typeid,credit,source_id,project_id,user_id,hours,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time")->where($where3)->limit($start, $pageLen)->order("create_time desc")->select();
			}else{
    			$results = M("center_study")->where($where3)->limit($start, $pageLen)->order("create_time desc")->select();
			}
    		foreach ($results as $key=>$value){
    			//在线培训：完成学习时间	课程名称	课程时长	学分	课程类型	操作
    			$course = M("course")->where("id=".$value["source_id"])->find();
    			$pcourse = M("project_course")->where("course_id=".$value["source_id"]." and project_id=".$value["project_id"])->find();
    			$course_name = $pcourse["course_names"];
    			if(!$pcourse["course_names"]){
    				$course_name = $course["course_name"];
    			}
    			$results[$key]["course_name"] = $course_name;
    			
    			$results[$key]["course_time"] = $course["course_time"];
    			$results[$key]["course_credit"] = $value["credit"];
    			
    			if($course["course_cat_id"]){
	    			$course_type = M("course_category")->where("id=".$course["course_cat_id"])->find();
	    			$results[$key]["course_type"] = $course_type["cat_name"];
    			}
    		}
    		
    		$totalNum = M("center_study")->where($where3)->select();
    		$pageNav = $this->pageClass($totalNum[0]["num"], $pageLen);
    	}elseif($param["type"] == 3){
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where3 = "a.user_id=".$user_id;
				$where3.= " and a.sign_up='1'";
				$where3.= " and b.type in (0,4)";
				$where3.= " and b.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where3.= " and b.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where3["a.user_id"] = $user_id;
				$where3["a.sign_up"] = 1;
	    		$where3['b.start_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$where3["b.type"] = array("in", "0,4");
			}
    		
    		//线下培训：主办部门	培训项目名称	培训类别	培训开始时间	培训结束时间	培训学时	培训对象	培训组织人员	获得学分	操作
    		$results = M("designated_personnel a")
    			->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")->where($where3)->limit($start, $pageLen)
    			->order("b.start_time desc")->select();
    		foreach ($results as $key=>$value){
    			$thisPro = M("admin_project")->field("to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time, 'YYYY-MM-DD HH24:MI:SS') as end_time")->where("id=".$value["project_id"])->find();
    			$results[$key]["start_time"] = $thisPro["start_time"];
    			$results[$key]["end_time"] = $thisPro["end_time"];
    			
	    		//所属部门
	    		$tissue = M("tissue_group_access")->where("user_id=".$value["user_id"])->find();
	    		$tissueRule = M("tissue_rule")->where("id=".$tissue["tissue_id"])->find();
	    		$results[$key]["part"] = $tissueRule["name"];
	    		
	    		$project_type = "";
	    		if($value["training_category"] == 0){
	    			$project_type = "内部培训";
	    		}elseif($value["training_category"] == 1){
	    			$project_type = "外派培训";
	    		}elseif($value["training_category"] == 2){
	    			$project_type = "外出授课";
	    		}
	    		$results[$key]["project_type"] = $project_type;
	    		
	    		$time = M("project_course a")->field("to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as pc_end_time,a.course_id,a.project_id,a.is_attachment,a.credit as p_credit,b.*")->join("join __COURSE__ b on a.course_id=b.id")->where("project_id=".$value["project_id"])->select();
	    		$total_time = 0;
	    		$total_credit = 0;
	    		foreach ($time as $value2){
	    			if(strtotime($value2["pc_end_time"]) > time()){
	    				//课程时间未结束，不计算在内
	    				continue;
	    			}
	    			if($value2["course_way"] == 1){
		    			$where5["a.course_id"] = $value2["course_id"];
		    			$where5["b.project_id"] = $value2["project_id"];
		    			$attendance_course = M("attendance_course a")->field("a.attendance_project_id")
		    			->join("join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id")->where($where5)->find();
		    			if($attendance_course){
		    				//考勤开启
		    				$awhere["user_id"] = $user_id;
		    				$awhere["status"] = array("in", "1,2");
		    				$awhere["attendance_project_id"] = $attendance_course["attendance_project_id"];
		    				$attendance = M("attendance")->where($awhere)->find();
		    				if($attendance){
		    					$total_time += (int)$value2["course_time"];
			    				$total_credit += $value2["p_credit"];
		    				}
		    			}else{
		    				$total_time += (int)$value2["course_time"];
			    			$total_credit += $value2["p_credit"];
		    			}
	    			}else{
	    				//获取在线课程数据
    					$whereCT["user_id"] = $user_id;
    					$whereCT["project_id"] = $value2["project_id"];
    					$whereCT["source_id"] = $value2["course_id"];
    					$whereCT["typeid"] = 4;//必修课程
	    				$upCourse = M("center_study")->field("credit,source_id,project_id")->where($whereCT)->find();
	    				if($upCourse){
	    					$total_time += (int)$value2["course_time"];
	    				}
	    			}
	    		}
	    		
	    		$results[$key]["total_time"] = round($total_time / 60, 2);
	    		$results[$key]["credit"] = $total_credit;
	    		
	    		$manager = M("users")->where("id=".$value["user_id"])->find();
	    		$results[$key]["manager"] = $manager["username"];
    		}
    		
    		$totalNum = M("designated_personnel a")->field("count(a.id) as num")->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")->where($where3)->select();
    		$pageNav = $this->pageClass($totalNum[0]["num"], $pageLen);
    	}elseif($param["type"] == 4){
    		$param["startTime"] = strtotime($param["startTime"]);
    		$param["endTime"] = strtotime($param["endTime"]);
    		
    		$where4["a.user_id"] = $user_id;
    		$where4['b.add_stamp'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
    		
    		$results = M("iac_content a")
    			->join("left join __IAC_FILE__ b on a.file_id=b.id")->where($where4)
    			->limit($start, $pageLen)->select();
    		
    		foreach ($results as $key=>$value){
    			$results[$key]["get_credit"] = round(($value["study_len"] / 3600), 2);
    		}
    		
    		$totalNum = M("iac_content a")->field("count(a.id) as t_num")
    			->join("left join __IAC_FILE__ b on a.file_id=b.id")->where($where4)->select();
    		$pageNav = $this->pageClass($totalNum[0]["t_num"], $pageLen);
    	}
    	
    	$data = array(
    			'pageNav' => $pageNav,
    			"list" => $results,
    	);
        return $data;
    }
	
    public function study_old($param){
    	$user_id = $_SESSION["user"]["id"];
    	$nowYearStamp = strtotime(date("Y")."-01-01 00:00:00");
    	$nowYear = date("Y");
    	
    	if(!$param["startTime"] || !$param["endTime"]){
    		$param["startTime"] = date('Y-01-01 00:00:00');
    		$endTime = date('Y-01-01 00:00:00', strtotime("+1 year"));
    		$param["endTime"] = date('Y-m-d H:i:s', strtotime($endTime) - 1);
    	}else{
    		$param["startTime"] = $param["startTime"]."-01 00:00:00";
    		$days = date("t", strtotime($param["endTime"]."-01"));
    		$param["endTime"] = $param["endTime"]."-".$days." 23:59:59";
    	}
    	
    	$myTissue = M("tissue_group_access")->where("user_id=".$user_id)->find();
    	
    	//每页条数，导出时设置pageLen=10000
    	$pageLen = $param["pageLen"];
    	if(!$param["pageLen"]){
	    	$pageLen = 20;
    	}
    	
    	$start = ($param["p"] - 1) * $pageLen;
    	$pageNav = "";
    	if($param["type"] == 1){
	    	//全部：姓名 所属部门	学习课程数	获得学分	内部培训班	外派培训班	外出授课	获得学分	学习时长	考试次数	问卷次数	笔记数
    		$key = 0;
    		
    		$upCourseNum = 0;//在线学习课程数量
    		$downCourseNum = 0;//线下课程学习数量
    		$upCredit = 0;//线上课程积分
    		$downCredit = 0;//线下课程积分
    		$total_time = 0;//培训项目总学时
    		$up_time = 0;//在线课程学时
    		$total_credit = 0;//培训项目总学分
    		
    		//获取在线课程数据
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where2 = "user_id=".$user_id;
				$where2.= " and typeid in (4,5)";
				$where2.= " and create_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where2.= " and create_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where2["user_id"] = $user_id;
	    		$where2['create_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$where2["typeid"] = array("in", "4,5");
			}
    		$total_credit = M("center_study")->field("credit,source_id,project_id")->where($where2)->select();
    		//center_study只统计了在线课程的数据
    		foreach ($total_credit as $value){
    			$course = M("course")->field("course_way,course_time")->where("id=".$value["source_id"])->find();
    			if($course){
    				if($course["course_way"] == 0){
    					$upCredit += $value["credit"];
    					$upCourseNum ++;
    					$up_time += $course["course_time"];
    				}
    				//是项目课程
    				if($value["project_id"] != 0){
    					$total_time += $course["course_time"];
    				}
    			}
    		}
    		
    		//获取线下课程数据
    		//培训班--线下课程数据
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where4 = "a.user_id=".$user_id;
				$where4.= " and a.sign_up=1";
				$where4.= " and c.type in (0,4)";
				$where4.= " and b.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where4.= " and b.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where4["a.user_id"] = $user_id;
				$where4["a.sign_up"] = 1;
	    		$where4['b.start_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$where4['c.type'] = array("in", "0,4");
			}
    		
    		$downPro = M("designated_personnel a")->field("b.*, to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as n_end_time")
    			->join("join __PROJECT_COURSE__ b on a.project_id=b.project_id")
    			->join("join __ADMIN_PROJECT__ c on a.project_id=c.id")
    			->where($where4)
    			->select();
    		
    		foreach ($downPro as $value){
    			if(strtotime($value["n_end_time"]) > time()){
    				//课程时间未结束，不计算在内
    				continue;
    			}
    			//面授课程数据计算
    			$course = M("course")->field("course_way,course_time")->where("id=".$value["course_id"])->find();
    			if($course["course_way"] == 1){
	    			$whereatt["a.course_id"] = $value["course_id"];
	    			$whereatt["b.project_id"] = $value["project_id"];
	    			$attendance_course = M("attendance_course a")->field("a.attendance_project_id")
	    			->join("join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id")->where($whereatt)->find();
	    			if($attendance_course){
	    				//考勤开启
	    				$awhere["user_id"] = $user_id;
	    				$awhere["status"] = array("in", "1,2");
	    				$awhere["attendance_project_id"] = $attendance_course["attendance_project_id"];
	    				$attendance = M("attendance")->where($awhere)->find();
	    				if($attendance){
	    					$downCredit += $value["credit"];
	    					$downCourseNum ++;
	    					$total_time += $course["course_time"];
	    				}
	    			}else{
	    				//未设置考勤//考勤关闭
	    				$downCredit += $value["credit"];
	    				$downCourseNum ++;
    					$total_time += $course["course_time"];
	    			}
    			}
    		}
    		
    		$results[$key]["hours"] = round($total_time / 60, 2);
    		
    		$results[$key]["up_time"] = round($up_time / 60, 2);
    		
    		//姓名
    		$user = M("users")->where("id=".$user_id)->find();
    		$results[$key]["username"] = $user["username"];
    		
    		//所属部门
    		$results[$key]["part"] = "";
    		if($myTissue["tissue_id"]){
    			$tissueRule = M("tissue_rule")->where("id=".$myTissue["tissue_id"])->find();
    			$results[$key]["part"] = $tissueRule["name"];
    		}
    		
    		//学习课程数
    		$results[$key]["course_num"] = $upCourseNum;
    		
    		//获得学分
    		$results[$key]["upCredit"] = $upCredit;
    		$results[$key]["downCredit"] = $downCredit;
    		
    		//内部培训班
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$whereCommon = "a.user_id=".$user_id;
				$whereCommon.= " and a.sign_up='1'";
				$whereCommon.= " and b.type in (0,4)";
				$whereCommon.= " and start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$whereCommon.= " and start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$whereCommon["a.user_id"] = $user_id;
				$whereCommon["a.sign_up"] = 1;
	    		$whereCommon['start_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$whereCommon['b.type'] = array("in", "0,4");//已完成的项目
			}
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where5 = $whereCommon . " and b.training_category=0";
			}else{
				$where5 = array_merge($whereCommon,array('b.training_category'=>0));
			}
			
    		$project0 = M("designated_personnel a")
    					->field("count(a.user_id) as join_num")
    					->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
    					->where($where5)
    					->select();
    		$results[$key]["project0"] = $project0[0]["join_num"];
    		
    		//外派培训班
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where5 = $whereCommon . " and b.training_category=1";
			}else{
				$where5 = array_merge($whereCommon,array('b.training_category'=>1));
			}
    		
    		$project1 = M("designated_personnel a")
    					->field("count(a.user_id) as join_num")
    					->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
    					->where($where5)
    					->select();
	
    		$results[$key]["project1"] = $project1[0]["join_num"];
    		
    		//外出授课
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where5 = $whereCommon . " and b.training_category=2";
			}else{
				$where5 = array_merge($whereCommon,array('b.training_category'=>2));
			}
    		
    		$project2 = M("designated_personnel a")
    					->field("count(a.user_id) as join_num")
    					->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
    					->where($where5)
    					->select();
    		$results[$key]["project2"] = $project2[0]["join_num"];
    		
    		//考试次数--培训项目 
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where8 = "a.user_id=".$user_id;
				$where8.= " and a.sign_up='1'";
				$where8.= " and b.type=4";
				$where8.= " and c.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where8.= " and c.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where8["a.user_id"] = $user_id;
				$where8["a.sign_up"] = 1;
	    		$where8['c.start_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$where8['b.type'] = 4;//已完成的项目
			}
    		
    		$projectExam = M("designated_personnel a")
    					->field("count(a.user_id) as join_num")
		    			->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
		    			->join("join __PROJECT_EXAMINATION__ c on a.project_id=c.project_id")	
		    			->where($where8)
		    			->order("c.start_time desc")
		    			->select();
    		
    		//考试次数--工具管理考试
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where9 = "a.user_id=".$user_id;
				$where9.= " and b.audit_status=0";
				$where9.= " and b.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where9.= " and b.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where9["a.user_id"] = $user_id;
	    		$where9["b.audit_status"] = 0;
	    		$where9['b.start_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
			}
    		
    		$toolExam = M("test_user_rel a")
    					->field("count(a.user_id) as join_num")
    					->join("join __TEST__ b on a.test_id=b.id")
    					->where($where9)
    					->select();
    		$results[$key]["exam_num"] = $projectExam[0]["join_num"] + $toolExam[0]["join_num"];
    		
    		//问卷次数--培训项目
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where10 = "a.user_id=".$user_id;
				$where10.= " and b.type=4";
				$where10.= " and c.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where10.= " and c.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where10["a.user_id"] = $user_id;
	    		$where10['c.start_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$where10['b.type'] = 4;//已完成的项目
			}
			
    		$projectSurvey = M("survey_attendance a")
    						->field("count(a.user_id) as join_num")
				    		->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
				    		->join("join __PROJECT_SURVEY__ c on a.project_id=c.project_id")
				    		->where($where10)
				    		->order("c.start_time desc")
				    		->select();
    		
    		//问卷次数--工具管理
    		if($myTissue["tissue_id"]){
    			if(strtolower(C('DB_TYPE')) == 'oracle'){
					$where11 = "a.tissue_id=".$myTissue["tissue_id"];
					$where11.= " and b.audit_state=1";
					$where11.= " and b.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
					$where11.= " and b.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
				}else{
					$where11["a.tissue_id"] = $myTissue["tissue_id"];
		    		$where11["b.audit_state"] = 1;
		    		$where11['b.start_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
				}
	    		
	    		$toolSurvey = M("research_tissueid a")
	    					->field("count(a.research_id) as join_num")
	    					->join("join __RESEARCH__ b on a.research_id=b.id")
	    					->where($where11)
	    					->select();
    		}else{
    			$toolSurvey[0]["join_num"] = 0;
    		}
    		$results[$key]["survey_num"] = $projectSurvey[0]["join_num"] + $toolSurvey[0]["join_num"];
    		
    		//笔记数
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where12 = "user_id=".$user_id;
				$where12.= " and time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where12.= " and time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where12["user_id"] = $user_id;
    			$where12['time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
			}
    		
    		$note = M("course_note")->field("count(id) as num")->where($where12)->select();
    		$results[$key]["note_num"] = $note[0]["num"];
    	}elseif($param["type"] == 2){
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where3 = "user_id=".$user_id;
				$where3.= " and typeid in (4,5)";
				$where3.= " and create_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where3.= " and create_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where3['create_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$where3["user_id"] = $user_id;
	    		$where3["typeid"] = array("in", "4,5");
			}
    		
			if(strtolower(C('DB_TYPE')) == 'oracle'){
	    		$results = M("center_study")->field("id,typeid,credit,source_id,project_id,user_id,hours,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time")->where($where3)->limit($start, $pageLen)->order("create_time desc")->select();
			}else{
    			$results = M("center_study")->where($where3)->limit($start, $pageLen)->order("create_time desc")->select();
			}
    		foreach ($results as $key=>$value){
    			//在线培训：完成学习时间	课程名称	课程时长	学分	课程类型	操作
    			$course = M("course")->where("id=".$value["source_id"])->find();
    			$pcourse = M("project_course")->where("course_id=".$value["source_id"]." and project_id=".$value["project_id"])->find();
    			$course_name = $pcourse["course_names"];
    			if(!$pcourse["course_names"]){
    				$course_name = $course["course_name"];
    			}
    			$results[$key]["course_name"] = $course_name;
    			
    			$results[$key]["course_time"] = $course["course_time"];
    			$results[$key]["course_credit"] = $value["credit"];
    			
    			if($course["course_cat_id"]){
	    			$course_type = M("course_category")->where("id=".$course["course_cat_id"])->find();
	    			$results[$key]["course_type"] = $course_type["cat_name"];
    			}
    		}
    		
    		$totalNum = M("center_study")->where($where3)->select();
    		$pageNav = $this->pageClass($totalNum[0]["num"], $pageLen);
    	}else{
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
				$where3 = "a.user_id=".$user_id;
				$where3.= " and a.sign_up='1'";
				$where3.= " and b.type in (0,4)";
				$where3.= " and b.start_time >to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
				$where3.= " and b.start_time <to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";
			}else{
				$where3["a.user_id"] = $user_id;
				$where3["a.sign_up"] = 1;
	    		$where3['b.start_time'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
	    		$where3["b.type"] = array("in", "0,4");
			}
    		
    		//线下培训：主办部门	培训项目名称	培训类别	培训开始时间	培训结束时间	培训学时	培训对象	培训组织人员	获得学分	操作
    		$results = M("designated_personnel a")
    			->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")->where($where3)->limit($start, $pageLen)
    			->order("b.start_time desc")->select();
    		foreach ($results as $key=>$value){
    			$thisPro = M("admin_project")->field("to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time, 'YYYY-MM-DD HH24:MI:SS') as end_time")->where("id=".$value["project_id"])->find();
    			$results[$key]["start_time"] = $thisPro["start_time"];
    			$results[$key]["end_time"] = $thisPro["end_time"];
    			
	    		//所属部门
	    		$tissue = M("tissue_group_access")->where("user_id=".$value["user_id"])->find();
	    		$tissueRule = M("tissue_rule")->where("id=".$tissue["tissue_id"])->find();
	    		$results[$key]["part"] = $tissueRule["name"];
	    		
	    		$project_type = "";
	    		if($value["training_category"] == 0){
	    			$project_type = "内部培训";
	    		}elseif($value["training_category"] == 1){
	    			$project_type = "外派培训";
	    		}elseif($value["training_category"] == 2){
	    			$project_type = "外出授课";
	    		}
	    		$results[$key]["project_type"] = $project_type;
	    		
	    		$time = M("project_course a")->field("to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as pc_end_time,a.course_id,a.project_id,a.is_attachment,a.credit as p_credit,b.*")->join("join __COURSE__ b on a.course_id=b.id")->where("project_id=".$value["project_id"])->select();
	    		$total_time = 0;
	    		$total_credit = 0;
	    		foreach ($time as $value2){
	    			if(strtotime($value2["pc_end_time"]) > time()){
	    				//课程时间未结束，不计算在内
	    				continue;
	    			}
	    			if($value2["course_way"] == 1){
		    			$where5["a.course_id"] = $value2["course_id"];
		    			$where5["b.project_id"] = $value2["project_id"];
		    			$attendance_course = M("attendance_course a")->field("a.attendance_project_id")
		    			->join("join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id")->where($where5)->find();
		    			if($attendance_course){
		    				//考勤开启
		    				$awhere["user_id"] = $user_id;
		    				$awhere["status"] = array("in", "1,2");
		    				$awhere["attendance_project_id"] = $attendance_course["attendance_project_id"];
		    				$attendance = M("attendance")->where($awhere)->find();
		    				if($attendance){
		    					$total_time += (int)$value2["course_time"];
			    				$total_credit += $value2["p_credit"];
		    				}
		    			}else{
		    				$total_time += (int)$value2["course_time"];
			    			$total_credit += $value2["p_credit"];
		    			}
	    			}else{
	    				//获取在线课程数据
    					$whereCT["user_id"] = $user_id;
    					$whereCT["project_id"] = $value2["project_id"];
    					$whereCT["source_id"] = $value2["course_id"];
    					$whereCT["typeid"] = 4;//必修课程
	    				$upCourse = M("center_study")->field("credit,source_id,project_id")->where($whereCT)->find();
	    				if($upCourse){
	    					$total_time += (int)$value2["course_time"];
	    				}
	    			}
	    		}
	    		
	    		$results[$key]["total_time"] = round($total_time / 60, 2);
	    		$results[$key]["credit"] = $total_credit;
	    		
	    		$manager = M("users")->where("id=".$value["user_id"])->find();
	    		$results[$key]["manager"] = $manager["username"];
    		}
    		
    		$totalNum = M("designated_personnel a")->field("count(a.id) as num")->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")->where($where3)->select();
    		$pageNav = $this->pageClass($totalNum[0]["num"], $pageLen);
    	}
    	
    	$data = array(
    			'pageNav' => $pageNav,
    			"list" => $results,
    	);
        return $data;
    }
	
    /**
	 * 学分统计筛选 - oracle模式
	 * creditType 1年度 2季度 3月份
	 */
	public function getStudyCredit($creditType){
		//echo "<br/>001".microtime();
		$user_id = $_SESSION["user"]["id"];
		$nowYearStamp = strtotime(date("Y")."-01-01 00:00:00");
		$nowYear = date("Y");
		if($creditType == 1){
			$startTime = date('Y-01-01 00:00:00');
			$endTime = date('Y-01-01 00:00:00', strtotime("+1 year"));
			$endTime = date('Y-m-d H:i:s', strtotime($endTime) - 1);
		}elseif($creditType == 2){
			$date = getdate();
			$month = $date['mon'];//当前第几个月
			$year = $date['year'];//但前的年份
			$startMonth = ceil($month / 3) * 3 - 2;//单季第一个月
			$strart = mktime(0, 0, 0, $startMonth, 1, $year);//当季第一天的时间戳
			$end = mktime(0, 0, 0, $startMonth + 3, 1, $year);//当季最后一天的时间戳
			$startTime = date('Y-m-d H:i:s', $strart);
			$endTime = date('Y-m-d H:i:s', $end - 1);
		}else{
			$startTime = date('Y-m-01 00:00:00');
			$endTime = date('Y-m-01 00:00:00', strtotime("+1 month"));
			$endTime = date('Y-m-d H:i:s', strtotime($endTime) - 1);
		}
		
		//申请学分 申请的学分通过后未加入center_study表中，是个bug,替代方案：由credits_apply直接获取数据
		$where1 = "user_id=$user_id";
		$where1 .= " and status=1";
		$where1 .= " and add_time>'".strtotime($startTime)."'";
		$where1 .= " and add_time<'".strtotime($endTime)."'";
		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    			SELECT sum(add_score) as apply_credit FROM __CREDITS_APPLY__ WHERE $where1
    		) thinkphp)";
    	$applyTotalScore = M()->query($sql);
    	
    	//echo "<br/>002".microtime();
		
		$upCourseNum = 0;//在线学习课程数量
		$downCourseNum = 0;//线下课程学习数量
		$upCredit = 0;//线上课程积分
		$downCredit = 0;//线下课程积分
		
		//获取在线课程数据
		$where2 = "user_id=".$user_id;
		$where2.= " and typeid in (4,5)";
		$where2.= " and create_time >to_date('".$startTime."','yyyy-mm-dd hh24:mi:ss')";
		$where2.= " and create_time <to_date('".$endTime."','yyyy-mm-dd hh24:mi:ss')";
		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
			SELECT credit,source_id FROM __CENTER_STUDY__ WHERE $where2
		) thinkphp)";
		$total_credit = M()->query($sql);
		
		//echo "<br/>003".microtime();
		
		//center_study只统计了在线课程的数据
		foreach ($total_credit as $value){
			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				SELECT course_way FROM __COURSE__ WHERE id=".$value["source_id"]." 
			) thinkphp) WHERE numrow=1";
			$course = M()->query($sql);
			if($course){
				if($course[0]["course_way"] == 0){
					$upCredit += $value["credit"];
					$upCourseNum ++;
				}
			}
		}
		
		//echo "<br/>004".microtime();
		
		//培训班--线下课程数据
		$where4 = "a.user_id=".$user_id;
		$where4.= " and a.sign_up='1'";
		$where4.= " and c.type in (0,4)";
		$where4.= " and b.start_time >to_date('".$startTime."','yyyy-mm-dd hh24:mi:ss')";
		$where4.= " and b.start_time <to_date('".$endTime."','yyyy-mm-dd hh24:mi:ss')";
		
		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
			SELECT b.*, to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as n_end_time FROM __DESIGNATED_PERSONNEL__ a
			join __PROJECT_COURSE__ b on a.project_id=b.project_id 
			join __ADMIN_PROJECT__ c on a.project_id=c.id 
			WHERE $where4
		) thinkphp)";
		$downPro = M()->query($sql);
		
		//echo "<br/>004".microtime();
		
		foreach ($downPro as $value){
			if(strtotime($value["n_end_time"]) > time()){
				//课程时间未结束，不计算在内
				continue;
			}
			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				SELECT course_way FROM __COURSE__ WHERE id=".$value["course_id"]."
			) thinkphp) WHERE numrow=1";
			$course = M()->query($sql);
			if($course[0]["course_way"] == 1){
				//面授课程数据计算
				$where5 = " a.course_id=".$value["course_id"];
				$where5.= " and b.project_id=".$value["project_id"];
				$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
					SELECT a.attendance_project_id FROM __ATTENDANCE_COURSE__ a 
					join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id
					WHERE $where5
				) thinkphp) WHERE numrow=1";
				$attendance_course = M()->query($sql);
				if($attendance_course){
					//考勤开启
					$awhere = " user_id=".$user_id;
					$awhere.= " and status in(1,2)";
					$awhere.= " and attendance_project_id=".$attendance_course[0]["attendance_project_id"];
					$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
						SELECT * FROM __ATTENDANCE__ WHERE $awhere
					) thinkphp) WHERE numrow=1";
					$attendance = M()->query($sql);
					if($attendance){
						$downCredit += $value["credit"];
						$downCourseNum ++;
					}
				}else{
					//考勤关闭
					$downCredit += $value["credit"];
					$downCourseNum ++;
				}
			}
		}
		
		//echo "<br/>005".microtime();
		
		//获取课程学分目标，目标年度学分完成率（当前时间段学分 / 学分目标）
		$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
			SELECT * FROM __TISSUE_GROUP_ACCESS__ WHERE user_id=$user_id
		) thinkphp) WHERE numrow=1";
		$myTissue = M()->query($sql);
		
		//echo "<br/>006".microtime();
		
		$myTissue = $myTissue[0];
		$myGole = 0;
		if($myTissue){
			$wheretl = " tissue_id=".$myTissue["tissue_id"];
			$wheretl.= " and job_id=".$myTissue["job_id"];
			$wheretl.= " and typeid=4";
			$sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				SELECT * FROM __TOOL_LEARNING__ WHERE $wheretl
			) thinkphp) WHERE numrow=1";
			$toolGoal = M()->query($sql);
			$toolGoal = $toolGoal[0];
			
			//按年统计，所有月份相加
			$myGole += $toolGoal["january"] + $toolGoal["february"] + $toolGoal["march"] +$toolGoal["april"]+$toolGoal["may"]+$toolGoal["june"];
			$myGole += $toolGoal["july"]+$toolGoal["august"]+$toolGoal["september"]+$toolGoal["october"]+$toolGoal["november"]+$toolGoal["december"];
			$myGole = ceil($myGole);
		}
		
		//echo "<br/>007".microtime();
		
		$iacStatus = D("Iac")->getIacStatus();//是否开启中保协
		$iac_credit = 0;
		if($iacStatus == 1){
			//中保协课程数量
			$param["startTime"] = strtotime($startTime);
			$param["endTime"] = strtotime($endTime);
			$where13["a.user_id"] = $user_id;
			$where13['b.add_stamp'] = array(array('gt', $param["startTime"]), array('lt', $param["endTime"]));
			$results = M("iac_content a")->field("a.*")->join("left join __IAC_FILE__ b on a.file_id=b.id")->where($where13)->select();
			
			$iac_num = 0;
			$iac_len = 0;
			$iac_credit = 0;
			foreach ($results as $key=>$value){
				$iac_num ++;
				$iac_len += $value["study_len"];
			}
			$iac_credit = round($iac_len / 3600, 2);
		}
		
		$finishRate = 0;
		$totalScore = $downCredit + $upCredit + $iac_credit + $applyTotalScore[0]["apply_credit"];
		if($myGole > 0){
			$finishRate = round($totalScore / $myGole, 2) * 100;
		}
		$return = array("totalScore"=>$totalScore, "upCredit"=>$upCredit, "downCredit"=>$downCredit, "iacCredit"=>$iac_credit, "finishRate"=>$finishRate);
		return $return;
	}
    
    /**
	 * 学分统计筛选 - mysql oracle双兼容模式
	 * creditType 1年度 2季度 3月份
	 */
	public function getStudyCredit_old($creditType){
		$user_id = $_SESSION["user"]["id"];
		$nowYearStamp = strtotime(date("Y")."-01-01 00:00:00");
		$nowYear = date("Y");
		if($creditType == 1){
			$startTime = date('Y-01-01 00:00:00');
			$endTime = date('Y-01-01 00:00:00', strtotime("+1 year"));
			$endTime = date('Y-m-d H:i:s', strtotime($endTime) - 1);
		}elseif($creditType == 2){
			$date = getdate();
			$month = $date['mon'];//当前第几个月
			$year = $date['year'];//但前的年份
			$startMonth = ceil($month / 3) * 3 - 2;//单季第一个月
			$strart = mktime(0, 0, 0, $startMonth, 1, $year);//当季第一天的时间戳
			$end = mktime(0, 0, 0, $startMonth + 3, 1, $year);//当季最后一天的时间戳
			$startTime = date('Y-m-d H:i:s', $strart);
			$endTime = date('Y-m-d H:i:s', $end - 1);
		}else{
			$startTime = date('Y-m-01 00:00:00');
			$endTime = date('Y-m-01 00:00:00', strtotime("+1 month"));
			$endTime = date('Y-m-d H:i:s', strtotime($endTime) - 1);
		}
		
		//申请学分 申请的学分通过后未加入center_study表中，是个bug,替代方案：由credits_apply直接获取数据
		/*
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$where1 = "user_id=".$user_id;
			$where1.= " and typeid not in (4,5)";
			$where1.= " and create_time >to_date('".$startTime."','yyyy-mm-dd hh24:mi:ss')";
			$where1.= " and create_time <to_date('".$endTime."','yyyy-mm-dd hh24:mi:ss')";
		}else{
			$where1["user_id"] = $user_id;
			$where1["typeid"] = array("not in","4,5");
			$where1['create_time'] = array(array('gt', $startTime), array('lt', $endTime));
		}
		$applyTotalScore = M("center_study")->field("sum(credit) as apply_credit")->where($where1)->select();
		*/
		$where1["user_id"] = $user_id;
		$where1["status"] = 1;
		$where1['add_time'] = array(array('gt', strtotime($startTime)), array('lt', strtotime($endTime)));
		$applyTotalScore = M("credits_apply")->field("sum(add_score) as apply_credit")->where($where1)->select();
		
		$upCourseNum = 0;//在线学习课程数量
		$downCourseNum = 0;//线下课程学习数量
		$upCredit = 0;//线上课程积分
		$downCredit = 0;//线下课程积分
		
		//获取在线课程数据
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$where2 = "user_id=".$user_id;
			$where2.= " and typeid in (4,5)";
			$where2.= " and create_time >to_date('".$startTime."','yyyy-mm-dd hh24:mi:ss')";
			$where2.= " and create_time <to_date('".$endTime."','yyyy-mm-dd hh24:mi:ss')";
		}else{
			$where2["user_id"] = $user_id;
			$where2['create_time'] = array(array('gt', $startTime), array('lt', $endTime));
			$where2["typeid"] = array("in", "4,5");
		}
		
		$total_credit = M("center_study")->field("credit,source_id")->where($where2)->select();
		//center_study只统计了在线课程的数据
		foreach ($total_credit as $value){
			$course = M("course")->field("course_way")->where("id=".$value["source_id"])->find();
			if($course){
				if($course["course_way"] == 0){
					$upCredit += $value["credit"];
					$upCourseNum ++;
				}
			}
		}
		
		//培训班--线下课程数据
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$where4 = "a.user_id=".$user_id;
			$where4.= " and a.sign_up='1'";
			$where4.= " and c.type in (0,4)";
			$where4.= " and b.start_time >to_date('".$startTime."','yyyy-mm-dd hh24:mi:ss')";
			$where4.= " and b.start_time <to_date('".$endTime."','yyyy-mm-dd hh24:mi:ss')";
		}else{
			$where4["a.user_id"] = $user_id;
			$where4["a.sign_up"] = 1;
			$where4['b.start_time'] = array(array('gt', $startTime), array('lt', $endTime));
			$where4['c.type'] = array("in", "0,4");
		}
		
		$downPro = M("designated_personnel a")->field("b.*, to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as n_end_time")
			->join("join __PROJECT_COURSE__ b on a.project_id=b.project_id")
			->join("join __ADMIN_PROJECT__ c on a.project_id=c.id")
			->where($where4)->select();
		foreach ($downPro as $value){
			if(strtotime($value["n_end_time"]) > time()){
				//课程时间未结束，不计算在内
				continue;
			}
			$course = M("course")->field("course_way")->where("id=".$value["course_id"])->find();
			if($course["course_way"] == 1){
				//面授课程数据计算
				$where5["a.course_id"] = $value["course_id"];
				$where5["b.project_id"] = $value["project_id"];
				$attendance_course = M("attendance_course a")->field("a.attendance_project_id")
				->join("join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id")->where($where5)->find();
				if($attendance_course){
					//考勤开启
					$awhere["user_id"] = $user_id;
					$awhere["status"] = array("in", "1,2");
					$awhere["attendance_project_id"] = $attendance_course["attendance_project_id"];
					$attendance = M("attendance")->where($awhere)->find();
					if($attendance){
						$downCredit += $value["credit"];
						$downCourseNum ++;
					}
				}else{
					//考勤关闭
					$downCredit += $value["credit"];
					$downCourseNum ++;
				}
			}
		}
		
		//获取课程学分目标，目标年度学分完成率（当前时间段学分 / 学分目标）
		$myTissue = M("tissue_group_access")->where("user_id=".$user_id)->find();
		$myGole = 0;
		if($myTissue){
			$wheretl["tissue_id"] = $myTissue["tissue_id"];
			$wheretl["job_id"] = $myTissue["job_id"];
			$wheretl["typeid"] = 4;
			$toolGoal = M("tool_learning")->where($wheretl)->find();
			
			//按年统计，所有月份相加
			$myGole += $toolGoal["january"] + $toolGoal["february"] + $toolGoal["march"] +$toolGoal["april"]+$toolGoal["may"]+$toolGoal["june"];
			$myGole += $toolGoal["july"]+$toolGoal["august"]+$toolGoal["september"]+$toolGoal["october"]+$toolGoal["november"]+$toolGoal["december"];
			$myGole = ceil($myGole);
		}
		
		$finishRate = 0;
		$totalScore = $downCredit + $upCredit + $applyTotalScore[0]["apply_credit"];
		if($myGole > 0){
			$finishRate = round($totalScore / $myGole, 2) * 100;
		}
		
		$return = array("totalScore"=>$totalScore, "upCredit"=>$upCredit, "downCredit"=>$downCredit, "finishRate"=>$finishRate);
		return $return;
	}
    
	/**
	 * 学分兑换积分页面
	 */
	public function exchangePage($page){
		F("valid_credits", null);
        $plan_id = getPlanId();//获取方案id
		$user_id = $_SESSION["user"]["id"];
		$where["user_id"] = $user_id;
		$pageLen = 5;
		$record = M("integration_erecord")->where($where)->order("id desc")->page($page, $pageLen)->select();
		foreach ($record as $key=>$value){
			$record[$key]["update_time"] = date("Y-m-d H:i:s", $value["update_stamp"]);
		}
		$total = M("integration_erecord")->field("count(id) as t_num")->where($where)->select();
		$pageNav = $this->pageClass($total[0]["t_num"], $pageLen);
		
		$yearData = self::getStudyCredit(1);
		$total_credits = $yearData["totalScore"];//年度总学分
		
		//已消耗学分
		$where2["user_id"] = $user_id;
		$where2["update_stamp"] = array(array("gt", strtotime(date("Y")."-01-01 00:00:00")),array("lt", time()));
		$used = M("integration_erecord")->field("sum(credits) as t_num")->where($where2)->select();
		$used_credits = $used[0]["t_num"] ? $used[0]["t_num"] : 0;
		
		//可用学分 = 年度总学分 - 已消耗学分
		$valid_credits = $total_credits - $used_credits;
		
		F("valid_credits", $valid_credits);
		
		//获取用户所属同一方案的积分兑换率
		$exchange = M("integration_exchange")->where(array('plan_id' => $plan_id))->find();
        if($exchange){
            $exc_rule = $exchange["exc_rule"];//兑换率
        }else{
            $exc_rule = 0;
        }
		return array("record"=>$record, "pageNav"=>$pageNav, "total_credits"=>$total_credits, "valid_credits"=>$valid_credits, "exc_rule"=>$exc_rule);
	}
	
	//根据用户组织ID获取所在中心
	public function getRulePid($pid){
		$pid += 0;
		if(!is_int($pid)){
			return array("code"=>1031, "message"=>"未获取到组织id");
		}
		$group = M("tissue_rule")->field("id,pid,name")->where("id=".$pid)->find();
		if(!$group){
			return array("pid" => $pid);
		}else{
			if($group["pid"] != 1){
				return self::getRulePid($group["pid"]);
			}else{
				return array("pid" => $group["id"]);
			}
		}
	}
	
	/**
	 * 学分兑换积分操作
	 */
	public function exchange($param){
		$user_id = $_SESSION["user"]["id"];
		$data["user_id"] = $user_id;
		$data["credits"] = $param["excVal"];
		$data["exc_rule"] = $param["excRule"];
		$data["exc_integral"] = $param["excVal"] * $param["excRule"];//消费学分*兑换率
		$data["update_time"] = date("Y-m-d H:i:s");
		$data["update_stamp"] = time();
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$data["id"] = getNextId('integration_erecord');
		}
		M("integration_erecord")->add($data);
		
		//兑换成功-积分记录增加数据
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$data2["id"] = getNextId('integration_record');
		}
		$data2["time"] = time();
		$data2["user_id"] = $user_id;
		$data2["department"] = "";
		$data2["score"] = $data["exc_integral"];
		$data2["type"] = "获取";
		$data2["describe"] = "使用".$param["excVal"]."学分进行兑换";
		$data2["apply_id"] = 0;
		M("integration_record")->add($data2);
	}
	
    /**
     * 找人Pk
     */
    public function pk(){

        $start_time = mktime(0,0,0,date('m'),1,date('Y'));

        $end_time = mktime(23,59,59,date('m'),date('t'),date('Y'));

        $where['time'] = array(array('egt',$start_time),array('elt',$end_time));

        $where['user_id'] = array("eq",$_SESSION['user']['id']);

        $where['score'] = array("gt",0);

        //获取当月积分
        $integral_items = M('integration_record')->where($where)->select();

        $total_integral = $this->pkData($integral_items);

        $total_integral = array_sum($total_integral);
        $where1['time'] = array(array('egt',$start_time),array('elt',$end_time));

        $where1['user_id'] = array("eq",$_SESSION['user']['id']);

        $where1['need_score'] = array("gt",0);
        //获取本月已兑换福利所减掉的积分
        $need_integral = M('Welfare_record')->where($where1)->sum('need_score');
        $this_month_score = $total_integral - $need_integral;
        $data = array(
            "username"=>$_SESSION['user']['username'],
            "avatar"=>$_SESSION['user']['avatar'],
            "integral"=>$this_month_score
        );

        return $data;
    }


    /**
     * PK 成员
     */
    public function pkMember(){

        $where['a.user_id'] = array("eq",$_SESSION['user']['id']);

        //获取用户上级组织名称
        $user = M("tissue_group_access a")
        		->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
        		->field("a.tissue_id,b.pid,b.name")
        		->where($where)
        		->find();

        $items = D('AdminTissue')->tree($user['tissue_id']);

        //获取当前用户所在级别
        $level = D('AdminTissue')->hierarchy($items['id']);

        //普通会员
        if($level == 4){

            $items = D('AdminTissue')->tree($user['pid']);

            $is_admin = false;

        }else{

            $is_admin = true;

        }

        $pkMember_list = $this->PeopleData($level,$items);

        $rule_list = array();

        if($is_admin){

			//获取所有组织数据
			$tissue_rule_list = D('IsolationData')->specifiedUser(false);

			foreach($tissue_rule_list as $id){

				$user_tissue_id = M("tissue_group_access")
					->where("user_id=".$id)
					->getField('tissue_id');

				$level_num = D('AdminTissue')->hierarchy($user_tissue_id);

				if($level_num < 4){
					$rule_arrid[] = $id;
				}

			}

			$rule_arrid = $rule_arrid ? $rule_arrid :  array(null);

			$conditions['a.user_id'] = array("in",$rule_arrid);
			$conditions['b.status'] = array("eq",1);

			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$group = 'a.user_id,b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id,a.manage_id';
			}else{
				$group = 'a.user_id';
			}

			$rule_list = M("tissue_group_access a")
				->field("b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id,a.manage_id")
				->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
				->where($conditions)
				->order('a.user_id desc')
				->group($group)
				->select();

        }


		//获取当前组织PK人员
		$map['tissue_id'] = array("eq",$user['tissue_id']);
		$map['b.id'] = array("gt",0);
		$map['b.status'] = array('eq',1);
		$admin_list = M("tissue_group_access a")->field("b.id,b.username")->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")->where($map)->select();


        $data = array(
            "items"=>$pkMember_list['items'],
            "admin_list"=> $admin_list,
            "is_admin"=>$is_admin,
			"tissue_name"=>$user['name'],
			"rule_list"=>$rule_list
        );

        return $data;

    }


    /**
     * 取出部门和人
     */
    public function PeopleData($level,&$data,&$pkMember_list,&$admin_list){

        $level_arr = array(1=>3,2=>2,3=>1,4=>1);

        foreach($data['_data'] as $item){

            if($item['_level'] == $level_arr[$level]){

                $admin_list[] = $item['pid'];

               $pkMember_list[] = $this->tissuePeople($item);

            }else{

                $admin_list[] = $item['id'];

                $this->PeopleData($level,$item,$pkMember_list,$admin_list);

            }

        }

        $data = array(
            "items"=>$pkMember_list,
            "admin_list"=>$admin_list
        );

        return $data;

    }

    /**
     * 取部门
     */
    public function departmentData($level,&$data,&$pkMember_list,&$admin_list){

        $level_arr = array(1=>2,2=>1);

        if($level >=3){

            $pkMember_list[] = $data;

        }else{


            foreach($data['_data'] as $item){

                if($item['_level'] == $level_arr[$level]){

                    $pkMember_list[] = $item;

                }else{

                    $this->departmentData($level,$item,$pkMember_list,$admin_list);

                }

            }


        }
        return  $pkMember_list;

    }

    /**
     * 查询PK人 从组织架构上取人
     */
    public function tissuePeople($item){

		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$condition = "a.tissue_id in " . $item['id'] . "and b.status != 3";
		}else{
			$condition['a.tissue_id'] = array("in",$item['id']);

	        $condition['b.status'] = array('neq',3);
		}
        $user_list = M("tissue_group_access a")
        		->field("b.id,b.username")
        		->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
        		->where($condition)
        		->select();

        $pkMember_list['name'] = $item['name'];

        $pkMember_list['_data'] = $user_list;

        return $pkMember_list;

    }

    /**
     * PK 成员Ajax
     */
    public function memberAjax(){

        $pk_id = I("post.pk_id");
        $start_time = mktime(0,0,0,date('m'),1,date('Y'));
        $end_time = mktime(23,59,59,date('m'),date('t'),date('Y'));

        //获取PK对象数据
        $where['user_id'] = array("eq",$pk_id);
        $where['score'] = array("gt",0);
        $where['time'] = array(array('egt',$start_time),array('elt',$end_time));

        $pk_items = M('integration_record')->where($where)->select();

        //查询用户名
        $pk_username =  M('users')->field("username,avatar")->where("id=".$pk_id)->find();

        $pk_list = $this->pkData($pk_items);


        //获取自己数据
        $condition['user_id'] = array("eq",$_SESSION['user']['id']);
        $condition['score'] = array("gt",0);
        $condition['time'] = array(array('egt',$start_time),array('elt',$end_time));

        $my_items = M('integration_record')->where($condition)->select();

        $my_list = $this->pkData($my_items);

        //$my_list = $pk_list = array();

        //比较PK数据
        /*
        for($i=0;$i<=5;$i++){
            $result = $this->percentage($pk_user[$i],$my_user[$i]);
            $pk_list[$i] = $result[0];
            $my_list[$i] = $result[1];
        }*/

        //PK月积分
        $pk_integral = array_sum($pk_list);
        $my_integral = array_sum($my_list);

        $data = array(
            "pk_name"=>$pk_username['username'],
            "pk_list"=>$pk_list,
            "pk_avatar"=>$pk_username['avatar'],
            "my_list"=>$my_list,
            "my_name"=>$_SESSION['user']['username'],
            "pk_integral"=>$pk_integral,
            "my_integral"=>$my_integral
        );

        return $data;

    }

    /**
     * @param $data
     * @return array
     * 获取PK数据
     */
    public function pkData($data){

        $type_name = array("好为人师","乐分享","系统达人","任务范儿","爱学习","我是学霸");

        $pk_user = array(0,0,0,0,0,0);

        foreach($data as $item){

            switch($item['type']){
                case $type_name[0]:
                    $pk_user[0] += $item['score'];
                    break;
                case $type_name[1]:
                    $pk_user[1] += $item['score'];
                    break;
                case $type_name[2]:
                    $pk_user[2] += $item['score'];
                    break;
                case $type_name[3]:
                    $pk_user[3] += $item['score'];
                    break;
                case $type_name[4]:
                    $pk_user[4] += $item['score'];
                    break;
                case $type_name[5]:
                    $pk_user[5] += $item['score'];
                    break;
            }

        }

        return $pk_user;

    }

    /**
     * PK 部门Ajax
     */
    public function departmentAjax(){

        $pk_id = I("post.pk_id");

        //获取PK对象数据
        $pk_total = $this->getpk($pk_id);
        $pk_name =  M('tissue_rule')->field("name")->where("id=".$pk_id)->find();

        //获取自己数据
        $condition['user_id'] = array("eq",$_SESSION['user']['id']);
        $my_tissue_id = M('tissue_group_access')->field('tissue_id')->where($condition)->find();
        $my_total = $this->getpk($my_tissue_id['tissue_id']);
        $my_name =  M('tissue_rule')->field("name")->where("id=".$my_tissue_id['tissue_id'])->find();

        //计算平均值
        $my = M('tissue_group_access')->field('tissue_id')->where("tissue_id=".$my_tissue_id['tissue_id'])->count();
        $pk = M('tissue_group_access')->field('tissue_id')->where("tissue_id=".$pk_id)->count();

        if(empty($my)){
            $my_total = 0;
        }else{
            $my_total = round($my_total / $my);
        }

        if(empty($pk)){
            $pk_total = 0;
        }else{
            $pk_total  = round($pk_total / $pk);
        }

        $data = array(
            "pk_name"=>$pk_name['name'],
            "pk_total"=>$pk_total,
            "my_total"=>$my_total,
            "my_name"=>$my_name['name']
        );

        return $data;

    }

    /**
     * 部门PK公共函数
     */
    public function getpk($tissue_id){

        $where['tissue_id'] = array("eq",$tissue_id);

        $list = array();

        $items = M('tissue_group_access')->field('user_id')->where($where)->select();

        if(empty($items)){

            $total = 0;

        }else{

            foreach($items as $item){
                $list[] = $item['user_id'];
            }

            $where = array();
            $where['score'] = array("gt",0);
            $where['user_id'] = array("in",$list);
            $start_time = mktime(0,0,0,date('m'),1,date('Y'));

            $end_time = mktime(23,59,59,date('m'),date('t'),date('Y'));

            $where['time'] = array(array('egt',$start_time),array('elt',$end_time));

            $integration_list = M('integration_record')->where($where)->select();

            $my_list = $this->pkData($integration_list);

            //合并部门总值
            $total = array_sum($my_list);

        }

        return $total;

    }

    /**
     * 部门PK
     */
    public function pkDepartment(){

        $where['a.user_id'] = array("eq",$_SESSION['user']['id']);

        //获取用户上级组织名称
        $tissue_name = M("tissue_group_access a")->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")->field("b.id,b.name,b.pid")->where($where)->find();


        $where['a.user_id'] = array("eq",$_SESSION['user']['id']);

        //获取用户上级组织名称
        $user = M("tissue_group_access a")->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")->field("a.tissue_id,b.pid")->where($where)->find();

        $items = D('AdminTissue')->tree($user['tissue_id']);

        //获取当前用户所在级别
        $level = D('AdminTissue')->hierarchy($items['id']);

        //普通会员
        if($level == 4){
            $items = D('AdminTissue')->tree($user['pid']);
        }

        $pkMember_list = $this->departmentData($level,$items);
        $data = array(
            "tree_items"=>$pkMember_list,
            "tissue_name"=>$tissue_name
        );

        return $data;


    }

    /**
     * [获取学习目标信息]
     * @return [array] [description]
     */
    public function getStudyData(){
        $user_id = session('user.id');
        $time = explode('-',date('Y-F'));
        $year = $time[0];
        $month = strtolower($time[1]);
        
        $info = M('tissue_group_access a')
                ->join('left join __TOOL_LEARNING__ b on a.tissue_id=b.tissue_id and a.job_id=b.job_id')
                ->where(array('a.user_id'=>$user_id,'b.year'=>$year))
                //->field('typeid,'.$month)
                ->select();

        //dump($info);//类别(0-必修,1-选修,2-修读，3-积分(新增类型)

        $integration = $this->getUserIntegration(); //总积分
        $hours_all = $this->getUserHours();          //总学时
        // $hours_bixiu = $this->getUserHours(false,false,false,4);          //必修学时
        // $hours_xuanxiu = $this->getUserHours(false,false,false,5);          //选修学时

        for($i = 1;$i <= 12;$i ++){
            if($i < 10){
                $i = '0'.$i;
            }
            $k = $this->getUserHours(false,false,$i,4);          //必修学时（一年）
            $v = $this->getUserHours(false,false,$i,5);          //选修学时（一年）

            $hours_bixiu += $k;
            $hours_xuanxiu += $v;
        }
        
        $course = D('Student')->getCourse($user_id);

        //公开课课程
        $where2['a.user_id'] = $user_id;
        $where2['a.project_id'] = 0;
        $where2['a.create_time'] = array('like','%'.date('Y').'%');
        $data2= M('course_chapter a')
                ->join('left join __COURSE__ b on a.course_id=b.id')
                ->where($where2)
                ->group('a.course_id,a.*,b.*')
                ->select();

        foreach($data2 as $k=>$v){
            $per = D('Student')->getCoursePer($v['user_id'],$v['course_id']);
            $data2[$k]['per'] = $per;
        }

        $courses = array_merge($course,$data2);

        $finishedCourseNum = $unFinishedCourseNum = 0;

        foreach($courses as $k=>$v){
            if($v['per'] == 100){
                $finishedCourseNum += 1;    //已完成的课程数量
            }
            $courses[$k]['project_id'] = $v['project_id'] == 0 ? 'true' : $v['project_id'];
        }
               
        $data = array();
        foreach($info as $k=>$v){
            switch ($v['typeid']) {
                case '0':
                    $s = $v['january']+$v['february']+$v['march']+$v['april']+$v['may']+$v['june']+$v['july']+$v['august']+$v['september']+$v['october']+$v['november']+$v['december'];
                    $data[] = round(($hours_bixiu / $s)*100);
                    break;
                case '1':
                    $s = $v['january']+$v['february']+$v['march']+$v['april']+$v['may']+$v['june']+$v['july']+$v['august']+$v['september']+$v['october']+$v['november']+$v['december'];
                    $data[] = round(($hours_xuanxiu / $s)*100);
                    break;
                case '2':
                    $s = $v['january']+$v['february']+$v['march']+$v['april']+$v['may']+$v['june']+$v['july']+$v['august']+$v['september']+$v['october']+$v['november']+$v['december'];
                    $data[] = round(($finishedCourseNum / $s)*100);
                    break;
                case '3':
                    $data[] = round(($integration / $v[$month])*100);
                    break;
            }
        }
        
        $hour_bixiu_1 = $this->getUserHours(false,false,false,4);          //必修学时（当前月份）
        $hours_xuanxiu_1 = $this->getUserHours(false,false,false,5);          //选修学时（当前月份）
        
        $data[] = round(( ($hour_bixiu_1+$hours_xuanxiu_1) / ($info[0][$month]+$info[1][$month]) )*100);

        return $data;
        //$data 0-必修% 1-选修% 2-课程% 3-总积分% 4-总学时%
    }

    /**
     * 获取某用户某年某月的积分  -- 总积分
     * @param  [type] $user_id   [用户id]
     * @param  [type] $year  [年份]
     * @param  [type] $month [月份]
     * @return [int]        [积分数值]
     */
    public function getUserIntegration($user_id=false,$year=false,$month=false){
        if(!$user_id){
            $user_id = session('user.id');
        }
        if(!$year){
            $year = date('Y');
        }
        if(!$month){
            $month = date('m');
        }
        $date = $year . '-' . $month;//2017-03
        $data = M('integration_record')->where(array('user_id'=>$user_id,'score'=>array('gt',0)))->select();
        
        foreach($data as $k=>$v){
            $data[$k]['time'] = date('Y-m-d H:i:s',$v['time']);
        }
        $integration = 0;
        
        foreach($data as $k=>$v){
            if(strpos($v['time'],$date) !== false){
                $integration += (int)$v['score'];
            }
        }
        return $integration;
    }

    /**
     * 获取某用户某年某月某个类型的总学时
     * @param  boolean $user_id   [用户id]
     * @param  boolean $year  [年份]
     * @param  boolean $month [月份]
     * @param  boolean $typeid [类型id]
     * @return [int]         [学时(分钟)]
     */
    public function getUserHours($user_id=false,$year=false,$month=false,$typeid=false){
        if(!$user_id){
            $user_id = session('user.id');
        }
        if(!$year){
            $year = date('Y');
        }
        if(!$month){
            $month = date('m');
        }

        if(!is_array($typeid)){
            settype($typeid,'array');
        }

        $date = $year . '-' . $month;//2017-03
        $data = M('center_study')
                ->where(array('user_id'=>$user_id,'create_time'=>array('like',"%$date%"),'typeid'=>array('in',$typeid)))
                ->sum('hours');
        $data = $data ? (int)$data : 0;
        return $data;
    }
    




}