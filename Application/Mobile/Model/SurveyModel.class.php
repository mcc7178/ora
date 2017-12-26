<?php
namespace Mobile\Model;

use Think\Model;

/**
 * @author Dujunqiang 20170308
 * 我的学员--我的调研
 */

class SurveyModel extends CommonModel {
    protected $tablePrefix = 'think_';
    protected $tableName = 'designated_personnel';

    //自动验证
    protected $_validate = array(
        array('username', 'empty', '用户名不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('username', '5,100', '用户名长度超出限制', Model::EXISTS_VALIDATE, 'length'),
        array('phone', '/1[34578]{1}\d{9}$/', '请输入正确的手机号格式', Model::EXISTS_VALIDATE, 'regex'),
        array('password', 'empty', '密码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('confirm', 'empty', '确认密码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('password', 'checkValid', '密码不能有中文', Model::EXISTS_VALIDATE, 'callback'),
        array('oldPassword', '6,20', '密码长度为6-20位', Model::EXISTS_VALIDATE, 'length'),
        array('password', 'isPassword', '只允许输入6-20位由 数字、字母、下划线组成的密码', Model::EXISTS_VALIDATE, 'callback'),
        array('phone', 'empty', '手机号码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('phone', 'is_numeric', '手机号码必须为数字', Model::EXISTS_VALIDATE, 'function')
    );
    
    /*
     * 我的调研列表
     * @param param 一维数组
     * @param user_id 用户id
     * 注意：我的调研分为   调研  和 问卷两种
     *  问卷-- 管理员创建项目  指定参与人员
     *  调研-- 管理员指定给组织  组织中的一员皆可参与
     */
    public function getList($param,$user_id){

		//总页数
		$total_page = 10;

    	if(!$param || !$user_id){
    		return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
    	}
    	if($param["proStatus"] != 1 && $param["proStatus"] != 2){
    		return array("code"=>1022, "message"=>'proStatus 调研状态提交有误');
    	}

		$start_page = $param['page'];

		$keyword=I("get.keyword")?I("get.keyword"):"";

		$status_id = I("get.status_id",0,'int');

		$project_results = $survey_results = array();

		//培训项目数据
		if(strtolower(C('DB_TYPE')) == 'oracle'){

			$where = "a.user_id =". $user_id." and a.status != 2";

			if($status_id > 0){

				if($status_id == 1){

					$where .= " and c.start_time >= to_date('".date("Y-m-d H:i:s",time())."','YYYY-MM-DD HH24:MI:SS')";
					$where .= " and a.status = 0";

				}elseif($status_id == 3){

					$where .= " and c.end_time < to_date('".date("Y-m-d H:i:s",time())."','YYYY-MM-DD HH24:MI:SS')";

				}else{

					$where .= " and c.start_time < to_date('".date("Y-m-d H:i:s",time())."','YYYY-MM-DD HH24:MI:SS') and c.end_time > to_date('".date("Y-m-d H:i:s",time())."','YYYY-MM-DD HH24:MI:SS')";
					$where .= " and a.status = 0";
				}

			}

			if(!empty($keyword)){

				$where .=" and (b.project_name like '%".$keyword."%')  OR (d.survey_name like '%".$keyword."%') OR (f.username like '%".$keyword."%')";

				$project_results =  M("survey_attendance a")->field("a.id,a.user_id,a.project_id,a.survey_id,a.status,b.project_name,to_char(c.start_time,'YYYY-MM-DD HH24:MI:SS') start_time,to_char(c.end_time,'YYYY-MM-DD HH24:MI:SS') end_time,c.survey_names,d.survey_name,e.cat_name,f.username")->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id JOIN __PROJECT_SURVEY__ c ON b.id = c.project_id and c.survey_id = a.survey_id LEFT JOIN __SURVEY__ d ON c.survey_id = d.id LEFT JOIN __SURVEY_CATEGORY__ e ON d.survey_cat_id = e.id LEFT JOIN __USERS__  f ON f.id = b.user_id")->order("a.status asc,c.start_time asc,c.end_time asc")->where($where)->select();


				foreach($project_results as $results){

					if($results['user_id'] == $_SESSION['user']['id']){
						$items[] = $results;
					}

				}

				$project_results = $items;

			}else{

				$project_results =  M("survey_attendance a")->field("a.id,a.user_id,a.project_id,a.survey_id,a.status,b.project_name,to_char(c.start_time,'YYYY-MM-DD HH24:MI:SS') start_time,to_char(c.end_time,'YYYY-MM-DD HH24:MI:SS') end_time,c.survey_names,d.survey_name,e.cat_name,f.username")->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id JOIN __PROJECT_SURVEY__ c ON b.id = c.project_id and c.survey_id = a.survey_id LEFT JOIN __SURVEY__ d ON c.survey_id = d.id LEFT JOIN __SURVEY_CATEGORY__ e ON d.survey_cat_id = e.id LEFT JOIN __USERS__  f ON f.id = b.user_id")->order("a.status asc,c.start_time asc,c.end_time asc")->where($where)->select();

			}

		}else{

			//获取已经过审核的项目
			$where['a.user_id'] = array("eq",$user_id);
			$where['a.status'] = array("neq",2);

			if(!empty($keyword)){

				$where['_string']="(b.project_name like '%".$keyword."%')  OR (d.survey_name like '%".$keyword."%') OR (f.username like '%".$keyword."%')";

				$project_results =  M("survey_attendance a")->field("a.id,a.user_id,a.project_id,a.survey_id,a.status,b.project_name,c.start_time,c.end_time,c.survey_names,d.survey_name,e.cat_name,f.username")->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id JOIN __PROJECT_SURVEY__ c ON b.id = c.project_id and c.survey_id = a.survey_id LEFT JOIN __SURVEY__ d ON c.survey_id = d.id LEFT JOIN __SURVEY_CATEGORY__ e ON d.survey_cat_id = e.id LEFT JOIN __USERS__ as f ON f.id = b.user_id")->order("a.status asc,c.start_time asc,c.end_time asc")->where($where)->select();

				foreach($project_results as $results){

					if($results['user_id'] == $user_id){
						$items[] = $results;
					}

				}

				$project_results = $items;

			}else{

				$project_results =  M("survey_attendance a")->field("a.id,a.project_id,a.survey_id,a.status,b.project_name,c.start_time,c.end_time,c.survey_names,d.survey_name,e.cat_name,f.username")->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id JOIN __PROJECT_SURVEY__ c ON b.id = c.project_id and c.survey_id = a.survey_id LEFT JOIN __SURVEY__ d ON c.survey_id = d.id LEFT JOIN __SURVEY_CATEGORY__ e ON d.survey_cat_id = e.id LEFT JOIN __USERS__ as f ON f.id = b.user_id")->order("a.status asc,c.start_time asc,c.end_time asc")->where($where)->select();

			}

		}

		//调研数据
		if(strtolower(C('DB_TYPE')) == 'oracle'){

			$where = "a.user_id =". $user_id." and a.state in(0,1,3) and b.audit_state != 0";

			if($status_id > 0){

				if($status_id == 1){

					$where .= " and b.start_time >= to_date('".date("Y-m-d H:i:s",time())."','YYYY-MM-DD HH24:MI:SS')";
					$where .= " and a.state = 0";

				}elseif($status_id == 3){

					$where .= " and b.end_time < to_date('".date("Y-m-d H:i:s",time())."','YYYY-MM-DD HH24:MI:SS')";

				}else{

					$where .= " and b.start_time < to_date('".date("Y-m-d H:i:s",time())."','YYYY-MM-DD HH24:MI:SS') and b.end_time > to_date('".date("Y-m-d H:i:s",time())."','YYYY-MM-DD HH24:MI:SS')";
					$where .= " and a.state = 0";
				}

			}

			if(!empty($keyword)){
				$where .=" and (b.research_name like '%".$keyword."%') OR (f.username like '%".$keyword."%')";
			}

			$survey_results = M("research_attendance a")->join("LEFT JOIN __RESEARCH__ b ON a.survey_id = b.survey_id and a.research_id = b.id LEFT JOIN __SURVEY__ c ON a.survey_id = c.id LEFT JOIN __SURVEY_CATEGORY__ d ON c.survey_cat_id = d.id LEFT JOIN __USERS__ f ON f.id = b.auth_user_id")->field("b.auth_user_id,a.state as status,b.research_name,b.id,b.survey_id,to_char(b.start_time,'YYYY-MM-DD HH24:MI:SS') start_time,to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') end_time,d.cat_name,f.username")->order("a.state asc,b.start_time asc,b.end_time asc")->where($where)->select();

			/*
			foreach($survey_results as $k=>$results){

				if($results['auth_user_id'] == $user_id){

					if($results['id'] != $survey_results[$k+1]['id']){
						$list[] = $results;
					}

				}

			}

			$survey_results = $list;
			*/

		}else{

			$where['a.user_id'] = array("eq",$user_id);
			$where['a.state'] = array("in","0,1,3");
			$where['b.audit_state'] = array("neq",0);

			if(!empty($keyword)){
				$where['_string']="(b.research_name like '%".$keyword."%') OR (f.username like '%".$keyword."%')";
			}

			$survey_results = M("research_attendance a")->join("LEFT JOIN __RESEARCH__ b ON a.survey_id = b.survey_id and a.research_id = b.id LEFT JOIN __SURVEY__ c ON a.survey_id = c.id LEFT JOIN __SURVEY_CATEGORY__ d ON c.survey_cat_id = d.id LEFT JOIN __USERS__ f ON f.id = b.auth_user_id")->field("a.state as status,b.*,d.cat_name,f.username")->order("a.state asc,b.start_time asc,b.end_time asc")->where($where)->select();

			foreach($survey_results as $results){

				if($results['auth_user_id'] == $user_id){
					$items[] = $results;
				}

			}

			$survey_results = $items;

		}

		if(!empty($project_results) && !empty($survey_results)){
			$results = array_merge($project_results,$survey_results);
		}elseif(!empty($project_results)){
			$results = $project_results;
		}else{
			$results = $survey_results;
		}

		$data_list = array();

		foreach($results as $k=>$list){

			//参加问卷 - 状态
			if($list['status'] == 0){
				if(strtotime($list['start_time']) < time() AND strtotime($list['end_time']) > time()){
					$data[$k]['proStatus'] = 0;
				}else if(strtotime($list['start_time']) > time()){
					$data[$k]['proStatus'] = 1;
				}else{
					$data[$k]['proStatus'] = 2;
				}
			}else{
				$data[$k]['proStatus'] = 2;
			}

			if(!empty($list['project_id'])){
				$data[$k]['project_id'] = $list['project_id'];
				$data[$k]['research_id'] = 0;
				$data[$k]['survey_name'] = $list['survey_name'];
				$data[$k]['survey_type'] = 1;

			}else{
				$data[$k]['project_id'] = 0;
				$data[$k]['research_id'] = $list['id'];
				$data[$k]['survey_name'] = $list['research_name'];
				$data[$k]['survey_type'] = 2;
			}

			$data[$k]['survey_id'] = $list['survey_id'];
			$data[$k]['survey_score'] = 0;
			$data[$k]['start_time'] =  $list['start_time'];
			$data[$k]['end_time'] =  $list['end_time'];

		}

		foreach($data as $val){
			if($val['proStatus'] == 0 || $val['proStatus'] == 1){
				$data_list[1][] = $val;
			}else{
				$data_list[2][] = $val;
			}
		}

		if($start_page >= 1){
			$start_page = $start_page - 1;
		}

		$p = $start_page * $total_page;

		$rows = array_slice($data_list[$param["proStatus"]],$p,$total_page);

    	if($rows){
	    	return array("code"=>1000, "message"=>'操作成功', "data"=>$rows);
    	}else{
    		if($param["proStatus"] == 1){
		    	return array("code"=>1023, "message"=>'管理员还没有给该学员安排调研');
    		}else{
		    	return array("code"=>1024, "message"=>'当前没有已结束的数据');
    		}
    	}
    }
    
    /**
     * 获取调研题目
	 * survey_type 调研类型 int 必须 1培训项目问卷  2组织调研
	 * survey_id 问卷ID int 必传
	 * project_id 项目ID int survey_type=1 时必传
	 * research_id 调研ID int survey_type=2  时必传
     */
    public function getQues($param,$user_id){
    	if(!$param || !$user_id){
    		return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
    	}

        //根据问卷id获取问卷
    	$resp = M("survey_item")->where("survey_id=".$param["survey_id"])->order("classification ASC")->select();
    	if(!$resp){
    		return array("code"=>1023, "message"=>'当前调研没有调研题目');
    	}
    	foreach ($resp as $key=>$value){
    		if($param["survey_type"] == 1){
    			//项目调研
    			$aWhere["u_survey_id"] = $user_id;
    			$aWhere["question_number"] = $value["id"];
    			$hasAnswer = M("survey_answer")->field("survey_answer")->where($aWhere)->limit(1)->select();
    		}else{
    			//组织调研
    			$aWhere["user_id"] = $user_id;
    			$aWhere["question_number"] = $value["id"];
    			$hasAnswer = M("research_answer")->field("survey_answer")->where($aWhere)->limit(1)->select();
    		}
    	
    		if($hasAnswer){
    			$resp[$key]["my_answer"] = $hasAnswer[0]["survey_answer"];
    		}
            //单选 多选获取选项
            $itemOpt = M("survey_item_opt")->field("id,item_id,letter,options,opt_img,orders")->where("item_id=".$value["id"])->order("orders asc")->select();
           foreach($itemOpt as $rk => $rs){
               $itemOpt[$rk]['option'] = $rs['options'];
               unset($itemOpt[$rk]['options']);
           }
            if($value["classification"] == 1 || $value["classification"] == 2){
                $resp[$key]['option'] =$itemOpt;
            }elseif($value["classification"] == 3){
                $resp[$key]["classification"] = 4;
                //$resp[$key]['option'] =$itemOpt;
            }
            unset($resp[$key]["optiona"]);
            unset($resp[$key]["optionb"]);
            unset($resp[$key]["optionc"]);
            unset($resp[$key]["optiond"]);
            unset($resp[$key]["optione"]);
            unset($resp[$key]["optionf"]);
            unset($resp[$key]["ctime"]);
        }
    	return array("code"=>1000, "message"=>'操作成功', "data"=>$resp);
    }
    
    /**
     * 提交答案
   	 * survey_type 调研类型 int 必须 1培训项目问卷  2组织调研
	 * survey_id 问卷ID int 必传
	 * project_id 项目ID int survey_type=1 时必传
	 * research_id 调研ID int survey_type=2  时必传
	 * topicId 题目ID int 必选
	 * answer 题目答案 string 必选
     */
    public function answer($param,$user_id){
    	if(!$param || !$user_id){
    		return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
    	}
    	
    	$db = M("survey_item");
    	$where["id"] = $param["topicId"];
    	$where["survey_id"] = $param["survey_id"];
    	$survey = $db->where($where)->limit(1)->select();
    	if(!$survey){
    		return array("code"=>1025, "message"=>'提交的题目ID未获取到对应的题目');
    	}
    	
    	//验证答案合法性
    	if($survey[0]["classification"] == 1){
    		$param["answer"] =  strtoupper($param["answer"]);
    		if(!preg_match('/^[A-E]{1}$/', $param["answer"])){
    			return array("code"=>1026,"message"=>'单选答案必须为A-E的单一字母');
    		}
    	}elseif($survey[0]["classification"] == 2){
    		//多选注意顺序  有可能答题是 BA CA ca cdA
    		$param["answer"] = str_split($param["answer"]);
    		sort($param["answer"]);
    		$param["answer"] =  strtoupper(implode($param["answer"]));
    		if(!preg_match('/^[A-E]{2,5}$/', $param["answer"])){
    			return array("code"=>1027,"message"=>'多选答案必须为A-E的字母组合');
    		}
    	}elseif($survey[0]["classification"] == 3){
    		if($param["answer"] != "对" && $param["answer"] != "错"){
    			return array("code"=>1028,"message"=>'判断答案必须为对 or 错');
    		}
    	}elseif($survey[0]["classification"] ==4){
    		$param["answer"] = trim($param["answer"]);
    		if($param["answer"] == ""){
    			return array("code"=>1028,"message"=>'简答题答案不能未空');
    		}
    	}else{
    		return array("code"=>1029, "message"=>'调研题目目前支持单选、多选、判断、简答');
    	}
    	
    	//判断当前用户是否有此调研
    	if($param["survey_type"] == 1){
    		//项目调研
    		$db = M("designated_personnel");
    		$hasThis = $db
    			->field("user_id")
    			->where("project_id=".$param["project_id"]." AND user_id=".$user_id)
    			->limit(1)
    			->select();
    		if(!$hasThis){
    			return array("code"=>1027, "message"=>'当前调研不属于该学员');
    		}
    		//----------------OK------------------
    		//问卷是否进行中，问卷已结束提示交卷
    		$act = M("survey");
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
			    $field = "to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
			}else{
			    $field = 'start_time,end_time';
			}
    		$actTime = $act
    			->field($field)
    			->where("id=".$param["survey_id"]." AND status='1' AND is_available='1'")
    			->limit(1)
    			->select();
    		if(!$actTime){
    			return array("code"=>1029, "message"=>'当前问卷不存在或已关闭');
    		}
    		if(time() > strtotime($actTime[0]["end_time"])){
    			return array("code"=>1030, "message"=>'问卷已结束，请交卷');
    		}
    		if(time() < strtotime($actTime[0]["start_time"])){
    			return array("code"=>1031, "message"=>'问卷未开始，请等待');
    		}
    		//项目调研
    		$answer = M("survey_answer");
    		$aWhere["u_survey_id"] = $user_id;
    		$aWhere["question_number"] = $param["topicId"];
    		$hasAnswer = $answer->field("survey_answer")->where($aWhere)->limit(1)->select();
    		if($hasAnswer){
    			$data["survey_answer"] = $param["answer"];
    			$return = $answer->where($aWhere)->save($data);
    			if($return === false){
    				return array("code"=>1032,"message"=>'更新失败，可尝试重新操作');
    			}
    		}else{
    			$data["project_id"] = $param["project_id"];
    			$data["survey_id"] = $param["survey_id"];
    			$data["u_survey_id"] = $user_id;
    			$data["survey_answer"] = $param["answer"];
    			$data["classification"] = $survey[0]["classification"];
    			$data["question_number"] = $param["topicId"];
    			$data["describe"] = "";
    			
				if(strtolower(C('DB_TYPE')) == 'oracle'){
				    $data['id'] = getNextId('survey_answer');
				}
    			$return = $answer->add($data);
    			if($return < 0 || $return === false){
    				return array("code"=>1033,"message"=>'提交失败，可尝试重新操作');
    			}
    		}
    	}else{
    		//组织调研
    		$canJoin = M("research_tissueid");
    		$canJoin = $canJoin
    			->field("tissue_id,research_id")
    			->where("research_id=".$param["research_id"])
    			->limit(1)
    			->select();
    		if(!$canJoin){
    			return array("code"=>1028, "message"=>'当前调研不属于该学员');
    		}
    		
    		$tissue = M("tissue_group_access");
    		$tissuser_id = $tissue
    			->field("tissue_id")
    			->where("user_id=".$user_id." AND tissue_id=".$canJoin[0]["tissue_id"])
    			->limit(1)
    			->select();
    		$tissuser_id = $tissuser_id[0]["tissue_id"];
    		if(!$tissuser_id){
    			return array("code"=>1028, "message"=>'当前学员还没有指定组织');
    		}
    		
	    	//调研是否进行中，调研已结束提示交卷
	    	$act = M("research");
			if(strtolower(C('DB_TYPE')) == 'oracle'){
			    $field = "to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
			}else{
			    $field = 'start_time,end_time';
			}
	    	$research = $act
	    		->field("survey_id," . $field)
	    		->where("id=".$param["research_id"]." AND audit_state=1")
	    		->limit(1)
	    		->select();
	    	if(!$research){
	    		return array("code"=>1029, "message"=>'当前调研不存在');
	    	}
	    	if(time() > strtotime($research[0]["end_time"])){
	    		return array("code"=>1030, "message"=>'调研已结束，请交卷');
	    	}
	    	if(time() < strtotime($research[0]["start_time"])){
	    		return array("code"=>1031, "message"=>'调研未开始，请等待');
	    	}
	    	
	    	//此调研是否已提交过
	    	$answer = M("research_answer");
	    	$aWhere["user_id"] = $user_id;
	    	$aWhere["question_number"] = $param["topicId"];
	    	$hasAnswer = $answer->field("survey_answer")->where($aWhere)->limit(1)->select();
    		if($hasAnswer){
    			$data["survey_answer"] = $param["answer"];
    			$return = $answer->where($aWhere)->save($data);
    			if($return === false){
    				return array("code"=>1032, "message"=>'更新失败，可尝试重新操作');
    			}
    		}else{
    			$data["research_id"] = $param["research_id"];
    			$data["survey_id"] = $param["survey_id"];
    			$data["user_id"] = $user_id;
    			$data["survey_answer"] = $param["answer"];
    			$data["classification"] = $survey[0]["classification"];
    			$data["question_number"] = $param["topicId"];
    			$data["describe"] = "";
				
				if(strtolower(C('DB_TYPE')) == 'oracle'){
				    $data['id'] = getNextId('research_answer');
				}
    			$return = $answer->add($data);
    			if($return < 0 || $return === false){
    				return array("code"=>1033,"message"=>'提交失败，可尝试重新操作');
    			}
    		}
    	}
    	return array("code"=>1000, "message"=>'操作成功');
    }
    
    /**
     * 提交调研
	 * survey_type 调研类型 int 必须 1培训项目问卷  2组织调研
	 * survey_id 问卷ID int 必传
	 * project_id 项目ID int survey_type=1 时必传
	 * research_id 调研ID int survey_type=2  时必传
     */
    public function finish($param,$user_id){
    	if(!$param || !$user_id){
    		return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
    	}
    	if($param["survey_type"] == 1){
    		//保存调研结果
    		foreach ($param["answer"] as $key=>$value){
    			//获取正确答案
    			$where["id"] = $key;
    			$survey_item = M("survey_item")->field("classification")->where($where)->limit(1)->select();
    			if(!$survey_item){
    				continue;
    			}
    			
    			//1表示单选择题 2表示多选 3判断题 4问答题'
    			if($survey_item[0]["classification"] == 1){
    				$value =  strtoupper($value);
    				if(!preg_match('/^[A-E]{1}$/', $value)){
    					//return array("code"=>1025,"message"=>'单选答案必须为A-E的单一字母');
    					continue;
    				}
    			}elseif($survey_item[0]["classification"] == 2){
    				$value =  strtoupper($value);
    			}elseif($survey_item[0]["classification"] == 3){
    				$value =  strtoupper($value);
    			}else{
    				//简答题，判断关键字
    			}
    		
    			$data["survey_answer"] = $value;
    		
    			//是否已有答题数据
    			$aWhere["u_survey_id"] = $user_id;
    			$aWhere["question_number"] = $key;
    			$aWhere["project_id"] = $param["project_id"];
    			$aWhere["survey_id"] = $param["survey_id"];
    			$hasAnswer = M("survey_answer")->where($aWhere)->limit(1)->select();
    			if($hasAnswer){
    				//已答过题，修改答案
    				M("survey_answer")->where($aWhere)->save($data);
    			}else{
    				$data["project_id"] = $param["project_id"];
    				$data["survey_id"] = $param["survey_id"];
    				$data["u_survey_id"] = $user_id;
    				$data["classification"] = $survey_item[0]["classification"];
    				$data["question_number"] = $key;
    				$data["describe"] = $value;
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
					    $data['id'] = getNextId('survey_answer');
					}
    				M("survey_answer")->add($data);
    			}
    		}
    		
    		$db = M("survey_attendance");
    		$hasAtten = $db
    			->field("id")
    			->where("user_id=".$user_id." AND survey_id=".$param["survey_id"]." AND project_id=".$param["project_id"])
    			->limit(1)
    			->select();
    		if($hasAtten){
    			$data["status"] = 1;
    			$aWhere2["user_id"] = $user_id;
    			$aWhere2["survey_id"] = $param["survey_id"];
    			$aWhere2["project_id"] = $param["project_id"];
    			$return = $db->where($aWhere2)->save($data);
    			if($return === false){
    				return array("code"=>1032,"message"=>'更新失败，可尝试重新操作');
    			}
    		}else{
    			$data["user_id"] = $user_id;
    			$data["survey_id"] = $param["survey_id"];
    			//获取岗位部门
    			$job = M("tissue_group_access a");
    			$job = $job->where("a.user_id=".$user_id)->field("a.tissue_id,a.job_id")->select();
    			
    			$data["department_id"] = $job[0]["tissue_id"];//部门ID
    			$data["position_id"] = $job[0]["job_id"];//岗位ID
    			$data["status"] = 1;
    			$data["mobile_scanning"] = "";
    			$data["project_id"] = $param["project_id"];
				
				if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $map['id'] = getNextId('survey_attendance');
                }
    			$return = $db->add($data);
    			if($return < 0 || $return === false){
    				return array("code"=>1033,"message"=>'提交失败，可尝试重新操作');
    			}
    			
	    		//添加学分学时
    			$pro_survey = M("project_survey")
    				->where("project_id=".$param["project_id"]." AND survey_id=".$param["survey_id"])
    				->limit(1)
    				->select();
    			$diffTime = strtotime($pro_survey[0]["end_time"]) - strtotime($pro_survey[0]["start_time"]);
    			$hours = ceil($diffTime / 60);
	    		$data1["create_time"] = date("Y-m-d H:i:s");
	    		$data1["typeid"] = 1;
    			$data1["credit"] = $pro_survey[0]["credit"];
	    		$data1["source_id"] = $param["survey_id"];
	    		$data1["project_id"] = $param["project_id"];
	    		$data1["user_id"] = $user_id;
	    		$data1["hours"] = $hours;
				
				if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $data1['id'] = getNextId('center_study');
                    $data1['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                }
	    		M("center_study")->add($data1);
    		}
    		
    	}else{
    		//工具管理-->调研
    		if(!$param["research_id"]){
    			return array("code"=>1023, "message"=>'survey_type=2时，缺少参数： research_id 调研id');
    		}
    		
    		//保存调研结果
    		foreach ($param["answer"] as $key=>$value){
    			//获取正确答案
    			$where["id"] = $key;
    			$survey_item = M("survey_item")->field("classification")->where($where)->limit(1)->select();
    			if(!$survey_item){
    				continue;
    			}
    		
    			//1表示单选择题 2表示多选 3问答题'
    			if($survey_item[0]["classification"] == 1){
    				$value =  strtoupper($value);
    				if(!preg_match('/^[A-E]{1}$/', $value)){
    					//return array("code"=>1025,"message"=>'单选答案必须为A-E的单一字母');
    					continue;
    				}
    			}elseif($survey_item[0]["classification"] == 2){
	    			$value =  strtoupper($value);
    			}elseif($survey_item[0]["classification"] == 3){
    				//判断题 汉字对错
    				$value =  strtoupper($value);
    			}else{
    				//简答题，判断关键字
    			}
    			$data["survey_answer"] = $value;
    			//是否已有答题数据
    			$aWhere["user_id"] = $user_id;
    			$aWhere["question_number"] = $key;
    			$aWhere["research_id"] = $param["research_id"];
    			$aWhere["survey_id"] = $param["survey_id"];
    			$hasAnswer = M("research_answer")->where($aWhere)->limit(1)->select();
    			if($hasAnswer){
    				//已答过题，修改答案
    				M("research_answer")->where($aWhere)->save($data);
    			}else{
    				$data["research_id"] = $param["research_id"];
    				$data["survey_id"] = $param["survey_id"];
    				$data["user_id"] = $user_id;
    				$data["classification"] = $survey_item[0]["classification"];
    				$data["question_number"] = $key;
    				$data["describe"] = $value;
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
	                    $data['id'] = getNextId('research_answer');
	                }
    				M("research_answer")->add($data);
    			}
    		}
    		
    		$db = M("research_attendance");
    		$hasAtten = $db
    			->field("user_id")
    			->where("user_id=".$user_id." AND survey_id=".$param["survey_id"]." AND research_id=".$param["research_id"])
    			->limit(1)
    			->select();
    		if($hasAtten){
    			$data["state"] = 1;
    			$aWhere2["user_id"] = $user_id;
    			$aWhere2["survey_id"] = $param["survey_id"];
    			$aWhere2["research_id"] = $param["research_id"];
    			$return = $db->where($aWhere2)->save($data);
    			if($return === false){
    				return array("code"=>1032,"message"=>'更新失败，可尝试重新操作');
    			}
    		}else{
    			$data["survey_id"] = $param["survey_id"];
    			$data["research_id"] = $param["research_id"];
    			$data["user_id"] = $user_id;
    			$data["state"] = 1;
				
				if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $data['id'] = getNextId('research_attendance');
                }
    			$return = $db->add($data);
    			if($return < 0 || $return === false){
    				return array("code"=>1033,"message"=>'提交失败，可尝试重新操作');
    			}
    			
    			//添加学分学时
    			$research = M("research")->where("id=".$param["research_id"]." AND survey_id=".$param["survey_id"])->limit(1)->select();
    			$diffTime = strtotime($research[0]["end_time"]) - strtotime($research[0]["start_time"]);
    			$hours = ceil($diffTime / 60);
    			
    			$data1["create_time"] = date("Y-m-d H:i:s");
    			$data1["typeid"] = 3;
    			$data1["credit"] = $research[0]["credits"];
    			$data1["source_id"] = $param["research_id"];
    			$data1["project_id"] = 0;
    			$data1["user_id"] = $user_id;
    			$data1["hours"] = $hours;
				
				if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $data1['id'] = getNextId('center_study');
                    $data1['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                }
    			M("center_study")->add($data1);
    		}
    	}
    	
    	return array("code"=>1000, "message"=>'操作成功');
    }
    
    /**
     * 已结束查看调研结果-----套路和获取试题基本一样，统计答案占比
     * survey_type 调研类型 int 必须 1培训项目问卷  2组织调研
	 * survey_id 问卷ID int 必传
	 * project_id 项目ID int survey_type=1 时必传
	 * research_id 调研ID int survey_type=2  时必传
	 * topicId 题目ID int 可选，不填默认第一题
     */
    public function seeDetail($param,$user_id){

		/*

    	if(!$param || !$user_id){
    		return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
    	}
    	
    	$where["survey_id"] = $param["survey_id"];
    	if(is_int($param["topicId"]) && $param["topicId"] > 0){
    		$where["id"] = $param["topicId"];
    	}

    	$resp = M("survey_item")
    		->field('optionA,optionB,optionC,optionD,optionE,optionF',true)
    		->where($where)
    		->order("id ASC")
    		->find();
    	if(!$resp){
    		return array("code"=>1030, "message"=>'当前调研没有调研题目或者题号有误');
    	}
    	
		//单选 / 多选
		if($param["survey_type"] == 1){
    		$answer = M("survey_answer");
    		$aWhere["project_id"] = intval($param["project_id"]);
    		$aWhere["survey_id"] = intval($param["survey_id"]);
    		$aWhere["question_number"] = intval($resp["id"]);
		}else{
			$answer = M("research_answer");
			$aWhere["research_id"] = intval($param["research_id"]);
			$aWhere["survey_id"] = intval($param["survey_id"]);
			$aWhere["question_number"] = intval($resp["id"]);
		}
        //单选 多选获取选项
        //题目类型判断

        if(intval($resp["classification"]) == 1 || intval($resp["classification"]) == 2){
            //单选 多选
            $itemOpt = M("survey_item_opt")->where("item_id=".intval($resp["id"]))->order("orders asc")->select();

            if(intval($resp["item_type"]) == 1){
                //投票（展示百分比）
                $total = $answer->where($aWhere)->count('id');
                if($total > 0){
                    foreach ($itemOpt as $subKey=>$subValue){
                        $itemOpt[$subKey]["num"] = 0;//选项人数
                        $itemOpt[$subKey]["rate"] = "0%";//选项百分比
                        $optWhere = $aWhere;
                        $optWhere["survey_answer"] = array("like", "%".$subValue["letter"]."%");

                        $optNum_res = $answer->field("survey_answer")->where($optWhere)->select();
                        $optNum = count($optNum_res);
                        if($optNum > 0){
                            $optNum = $optNum;
                            $optRate = $optNum / $total * 100;
                            $optRate = (round($optRate, 2))."%";
                            $itemOpt[$subKey]["rate"] = $optRate;
                            $itemOpt[$subKey]["num"] = $optNum;
                        }
                    }
                }else{
                    foreach ($itemOpt as $subKey=>$subValue){
                        $itemOpt[$subKey]["num"] = 0;//选项人数
                        $itemOpt[$subKey]["rate"] = "0%";//选项百分比
                    }
                }
            }else{
                //普通（展示自己的选项）
                $ptWhere = $aWhere;
                $itemAnswer = $answer->where($ptWhere)->find();
                foreach ($itemOpt as $subKey=>$subValue){
                    $ptWhere["survey_answer"] = array("like", "%".$subValue["letter"]."%");
                    $optNum = $answer->field("survey_answer")->where($ptWhere)->select();
                    $itemOpt[$subKey]["ischeck"] = 0;
                    if($optNum){
                        $itemOpt[$subKey]["ischeck"] = 1;
                    }
                    $itemOpt[$subKey]["survey_answer"] = $itemAnswer["survey_answer"];
                }
            }
            $resp["option"] = $itemOpt;
    	}elseif($resp["classification"] == 3){
    		//简答
    		if($param["survey_type"] == 1){
    			$answer = M("survey_answer");
    			$aWhere["project_id"] = $param["project_id"];
    			$aWhere["survey_id"] = $param["survey_id"];
    			$aWhere["question_number"] = $resp["id"];
    		}else{
    			$answer = M("research_answer");
    			$aWhere["research_id"] = $param["research_id"];
    			$aWhere["survey_id"] = $param["survey_id"];
    			$aWhere["question_number"] = $resp["id"];
    		}

    		$resp["myAnswer"] = "";
    		$hasAnswer = $answer->field("survey_answer")->where($aWhere)->select();
    		if($hasAnswer){
    			$myAnswers = "";
    			foreach ($hasAnswer as $aValue){
    				$myAnswers .= $aValue["survey_answer"];
    				$myAnswers .= "<br><br>";
    			}
    			$resp["myAnswer"] = $myAnswers;
    		}
    	}
    	
    	$resp["topicId"] = $resp["id"];
    	$resp["survey_type"] = $param["survey_type"];
    	$resp["survey_id"] = intval($param["survey_id"]);
    	$resp["project_id"] = $param["project_id"];
    	$resp["research_id"] = $param["research_id"];
    	$resp["project_id"] = $param["project_id"];
    	
    	$allQues = M("survey_item");
    	$allWhere["survey_id"] = $param["survey_id"];
    	$allResp = $allQues->field("id")->where($allWhere)->order("id ASC")->select();
    	//获取当前试题在题库的位置，判断是否有上一题  下一题
    	$orderArr = array();
    	foreach ($allResp as $key=>$value){
    		$orderArr[$key] = $value["id"];
    	}
    	
    	$order = array_search($resp["id"],$orderArr);
    	if($order > 0 && $order < (count($orderArr) - 1)){
    		$resp["preId"] = $orderArr[$order - 1];
    		$resp["preStatus"] = 1;
    		$resp["nextId"] = $orderArr[$order + 1];
    		$resp["nextStatus"] = 1;
    	}elseif($order == (count($orderArr) - 1)){
    		$resp["preId"] = $orderArr[$order - 1];
    		$resp["preStatus"] = 1;
    		$resp["nextId"] = "";
    		$resp["nextStatus"] = 0;
    	}elseif($order == 0 && count($orderArr) > 0){
    		$resp["preId"] = '';
    		$resp["preStatus"] = 0;
    		$resp["nextId"] = $orderArr[$order + 1];
    		$resp["nextStatus"] = 1;
    	}elseif($order == 0 && count($orderArr) == 0){
    		$resp["preId"] = '';
    		$resp["preStatus"] = 0;
    		$resp["nextId"] = '';
    		$resp["nextStatus"] = 0;
    	}
    	return array("code"=>1000, "message"=>'操作成功', "data"=>$resp);*/

		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}

		$where["survey_id"] = $param["survey_id"];
		if(is_int($param["topicId"]) && $param["topicId"] > 0){
			$where["id"] = $param["topicId"];
		}

		$resp = M("survey_item")
			->field('optionA,optionB,optionC,optionD,optionE,optionF',true)
			->where($where)
			->order("id ASC")
			->find();
		if(!$resp){
			return array("code"=>1030, "message"=>'当前调研没有调研题目或者题号有误');
		}

		$project_id = $param['project_id'];

		if(!empty($param['project_id'])){
			$typeid = 0;
		}else{
			$typeid = 1;
		}

		$survey_id = $param['survey_id'];

		if($typeid == 0){
			//关联项目

			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$admin_project = M('admin_project')->field("project_name,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') end_time")->where(array("id"=>$project_id))->find();
			}else{

				$admin_project = M('admin_project')->field("project_name,end_time")->where(array("id"=>$project_id))->find();
			}

			$info = array(
				"id"=>$project_id,
				"survey_id"=>$survey_id
			);

		}else{
			$research_id = I('get.research_id');
			$where['id'] = array("eq",$research_id);
			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$admin_project = M('research')->field("to_char(end_time,'YYYY-MM-DD HH24:MI:SS') end_time")->where($where)->find();
			}else{

				$admin_project = M('research')->field("end_time")->where($where)->find();
			}

			$info = array(
				"id"=>$research_id,
				"survey_id"=>$survey_id
			);
		}

		if(strtotime($admin_project['end_time']) > time()){
			$survey_attendance['status'] = 0;
		}else{
			$survey_attendance['status'] = 1;
		}

		$survey = M('survey a')->field("a.*,b.cat_name")->join("LEFT JOIN __SURVEY_CATEGORY__ b ON a.survey_cat_id = b.id")->where(array("a.id"=>$survey_id))->find();

		$surveyItem = M('survey_item')->where(array("survey_id"=>$survey_id))->select();

		foreach ($surveyItem as $key=>$value){
			if($typeid == 0){
				$answer = M("survey_answer");
				$aWhere["project_id"] = $project_id;
				$aWhere["survey_id"] = $survey_id;
				$aWhere["question_number"] = $value["id"];
			}else{
				$answer = M("research_answer");
				$aWhere["research_id"] = $research_id;
				$aWhere["survey_id"] = $survey_id;
				$aWhere["question_number"] = $value["id"];
			}
			//题目类型判断
			if($value["classification"] == 1 || $value["classification"] == 2){
				//单选 多选
				$itemOpt = M("survey_item_opt")->where("item_id=".$value["id"])->order("orders asc")->select();
				if($value["item_type"] == 1){
					//投票（展示百分比）
					$total = $answer->field("count(id) as num")->where($aWhere)->select();
					$total = $total[0]["num"];
					if($total > 0){
						foreach ($itemOpt as $subKey=>$subValue){
							$itemOpt[$subKey]["num"] = 0;//选项人数
							$itemOpt[$subKey]["rate"] = "0%";//选项百分比
							$optWhere = $aWhere;
							$optWhere["survey_answer"] = array("like", "%".$subValue["letter"]."%");
							$optNum = $answer->field("count(id) as num")->where($optWhere)->select();
							if($optNum > 0){
								$optNum = $optNum[0]["num"];
								$optRate = $optNum / $total * 100;
								$optRate = (round($optRate, 2))."%";
								$itemOpt[$subKey]["rate"] = $optRate;
								$itemOpt[$subKey]["num"] = $optNum;
							}
						}
					}else{
						foreach ($itemOpt as $subKey=>$subValue){
							$itemOpt[$subKey]["num"] = 0;//选项人数
							$itemOpt[$subKey]["rate"] = "0%";//选项百分比
						}
					}
				}else{
					//普通（展示自己的选项）
					$ptWhere = $aWhere;
					if($typeid == 0){
						$ptWhere["u_survey_id"] = $user_id;
					}else{
						$ptWhere["user_id"] = $user_id;
					}
					$itemAnswer = $answer->where($ptWhere)->find();
					$itemOpt[$subKey]["survey_answer"] = $itemAnswer["survey_answer"];
					foreach ($itemOpt as $subKey=>$subValue){
						$ptWhere["survey_answer"] = array("like", "%".$subValue["letter"]."%");
						$optNum = $answer->field("survey_answer")->where($ptWhere)->select();
						$itemOpt[$subKey]["ischeck"] = 0;
						if($optNum){
							$itemOpt[$subKey]["ischeck"] = 1;
						}
					}
				}
				$surveyItem[$key]["option"] = $itemOpt;
			}else{
				$fillWhere = $aWhere;
				if($typeid == 0){
					$fillWhere["u_survey_id"] = $user_id;
				}else{
					$fillWhere["user_id"] = $user_id;
				}
				$itemAnswer = $answer->where($fillWhere)->find();
				$surveyItem[$key]["survey_answer"] = $itemAnswer["describe"];
			}
		}

		//统计总数
		$total = count($surveyItem) -1;

		//默认页数
		$page = I('get.page',0);

		$rows = $surveyItem[$page];

		$items = array(
			"status"=>$survey_attendance['status'],
			"cat_name"=>$survey['cat_name'],
			"survey_id"=>$survey_id,
			"typeid"=>$typeid,
			"survey"=>$survey,
			"survetItem"=>$rows,
			"total"=>$total,
			"page"=>$page
		);

		if($typeid == 0){
			$items['project_name'] = $admin_project['project_name'];
			$items['project_id'] = $project_id;

		}else{
			$items['research_id'] = $research_id;
		}

		return array("code"=>1000, "message"=>'操作成功', "data"=>$items);
    }

	/**
	 * @return array
	 * 处理试卷过期
	 */
	public function overdue($userId){

		$where['a.user_id'] = array("eq",$userId);
		$where['a.sign_up'] = array("eq",1);
		$where['b.type'] = array("eq",0);
		$where['c.project_id'] = array("gt",0);
		$where['c.survey_id'] = array("gt",0);

		//查询过期没有提交的试卷

		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$overdue_data =M("designated_personnel a")
				->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id LEFT JOIN __PROJECT_SURVEY__ c ON b.id = c.project_id")
				->where($where)
				->field('c.project_id,c.survey_id,to_char(c.start_time,\'YYYY-MM-DD HH24:MI:SS\') as start_time,to_char(c.end_time,\'YYYY-MM-DD HH24:MI:SS\') as end_time')
				->select();

		}else{
			$overdue_data =M("designated_personnel a")
				->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id LEFT JOIN __PROJECT_SURVEY__ c ON b.id = c.project_id")
				->where($where)
				->field('c.project_id,c.survey_id,c.start_time,c.end_time')
				->select();
		}

		foreach($overdue_data as $k=>$data) {

			$map['user_id'] = array("eq",$userId);
			$map['project_id'] = array("eq",$data['project_id']);
			$map['survey_id'] = array("eq",$data['survey_id']);

			$is_survey = M("survey_attendance")->where($map)->find();

			if(empty($is_survey)){

				try {

					$add = array(
						"user_id" => $userId,
						"survey_id" => $data['survey_id'],
						"project_id" => $data['project_id'],
						"status" =>0,
						"mobile_scanning"=>0
					);

					if(strtotime($data['end_time']) < time()){
						$add['status'] = 3;
					}

					$DB = M('survey_attendance');

					$DB->startTrans();

					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$add['id'] = getNextId('survey_attendance');
					}

					$DB->data($add)->add();

					$DB->commit();


				} catch ( Exception $e ) {

					$DB->rollback();

				}
			}else{

				if((strtotime($data['end_time'])) < time() and ($is_survey['status'] == 0)){
					M('survey_attendance')-> where('id='.$is_survey['id'])->setField('status',3);
				}

			}


		}
	}

	/**
	 * @return array
	 * 试卷状态添加
	 */
	public function researchAdd($userId){

		//查询所属组织
		$userGroup = M('tissue_group_access')->field("tissue_id")->where("user_id=".$userId)->find();

		$where['a.tissue_id'] = array("eq",$userGroup['tissue_id']);
		$where['b.audit_state'] = array("eq",1);

		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$items = M("research_tissueid a")
				->field("a.research_id,b.survey_id,to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') end_time")
				->join("LEFT JOIN __RESEARCH__ b ON a.research_id = b.id")
				->where($where)
				->select();
		}else{
			$items = M("research_tissueid a")
				->field("a.research_id,b.survey_id,b.end_time")
				->join("LEFT JOIN __RESEARCH__ b ON a.research_id = b.id")
				->where($where)
				->select();

		}

		foreach($items as $item){

			$condition['survey_id'] = array("eq",$item['survey_id']);
			$condition['research_id'] = array("eq",$item['research_id']);
			$condition['user_id'] = array("eq",$userId);

			//判断是否存在试卷
			$is_research = M('research_attendance')->where($condition)->find();

			if(empty($is_research)){

				$add = array(
					"user_id" => $userId,
					"survey_id" => $item['survey_id'],
					"research_id" => $item['research_id'],
					"state" => 0
				);

				if(strtotime($item['end_time']) < time()){
					$add['state'] = 3;
					$add['id'] = getNextId('research_attendance');
				}

				if(strtolower(C('DB_TYPE')) == 'oracle'){
					$add['id'] = getNextId('research_attendance');
				}
				M('research_attendance')->data($add)->add();

			}else{

				if(strtotime($item['end_time']) < time() and ($is_research['state'] == 0)){
					M('research_attendance')->where('id='.$is_research['id'])->setField('state',3);
				}

			}

		}

	}




}