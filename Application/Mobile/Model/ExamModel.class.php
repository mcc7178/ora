<?php
namespace Mobile\Model;

use Think\Model;

/**
 * @author Andy 20170304
 *
 */

class ExamModel extends CommonModel {
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


/**********************************************兼容oracle**********************************************/
    /**
     * 我的考试列表(项目指定的考试）
     * @Param $examStatus 考试类型  1待考试   2已结束
     * @Param $userId 用户id
     */
    public function myExam($examStatus,$userId,$page,$pageNum){

        //获取已经过审核的项目
        $where['a.user_id'] = array("eq",$userId);
        $where['a.sign_up'] = array("eq",1);
        $where['b.type'] = array("in",'0,4');

        if(!empty($keyword)){
            $where['_string']="(b.project_name like '%".$keyword."%')  OR (d.test_name like '%".$keyword."%')";
        }

        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.user_id,a.project_id,b.project_name,c.test_id,to_char(c.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(c.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,c.test_length,c.test_names,e.status,d.test_name,d.test_score,c.freq,c.cacheid";
        }else{
            $field = 'a.user_id,a.project_id,b.project_name,c.test_id,c.start_time,c.end_time,c.test_length,c.test_names,e.status,d.test_name,d.test_score,c.freq,c.cacheid';
        }
        $result = M("designated_personnel a")
            ->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id")
            ->join('LEFT JOIN __PROJECT_EXAMINATION__ c ON b.id = c.project_id')
            ->join('LEFT JOIN __EXAMINATION__ d on c.test_id = d.id')
            ->join('LEFT JOIN __EXAMINATION_ATTENDANCE__ e ON d.id = e.test_id AND b.id = e.project_id and e.user_id=a.user_id')
            ->where($where)
            ->order('c.start_time desc')
            ->field($field)
            ->select();

        foreach($result as $k=>$v){
            if(empty($v['test_name'])){
                $result[$k]['test_name'] = $v['test_names'];
            }
            $result[$k]['score'] = $this->getMaxScore($v['test_id'],false,$v['project_id'],$userId);
            $result[$k]['score'] = $result[$k]['score'] ? $result[$k]['score'] : 0;
            //考试次数
            $num = M('exam_score')
                ->where(array('project_id'=>$v['project_id'],'exam_id'=>$v['test_id'],'user_id'=>$userId))
                ->max('counter');
            $num = $num ? $num : 0;
            $result[$k]['counter'] = $num;	//已考次数

            if(strtotime($v['start_time']) > time()){
                $result[$k]['status'] = 1;//'未开始'
                $result[$k]['start_time'] = $v['start_time'];//开始时间
                $result[$k]['end_time'] = $v['end_time'];//结束时间

            }else if(strtotime($v['end_time']) < time()){
                $result[$k]['status'] = 3;//2/3'已逾期'
                $result[$k]['start_time'] = $v['start_time'];//开始时间
                $result[$k]['end_time'] = $v['end_time'];//结束时间
            }else{
                $result[$k]['status'] = 0;//'进行中'
                $result[$k]['start_time'] = $v['start_time'];//开始时间
                $result[$k]['end_time'] = $v['end_time'];//结束时间
            }

            if($num && $num >= $v['freq']){
                if(strtotime($v['end_time']) <= time()) {
                    $result[$k]['status'] = 3;//'已结束';
                }else {
                    $result[$k]['status'] = 2;//'已结束';
                }

                $result[$k]['start_time'] = $v['start_time'];//开始时间
                $result[$k]['end_time'] = $v['end_time'];//结束时间
            }
            if(!$v['start_time']){
                unset($result[$k]);
            }
        }
        $left = $middle = $right = array();
        foreach($result as $k=>$v){
            if($v['status'] == 2 || $v['status'] == 3){//已结束,已完成
                $right[] = $result[$k];
            }else if($v['status'] == 0){//'进行中'
                $left[] = $result[$k];
            }else if($v['status'] == 1){//'未开始'
                $middle[] = $result[$k];
            }
        }
        if($examStatus == 1){
            $final = array_merge($left,$middle);//待考试
        }elseif($examStatus == 2){
            $final = $right;//已结束
        }
        return $final;
    }


    /**********************************************兼容oracle******************************************/
    /**
     * 我的考试列表(工具管理指定的考试）
     * @Param $examStatus 考试类型  1待考试   2已结束
     * @Param $userId 用户id
     */

    public function toolExam($examStatus,$userId,$page,$pageNum){

        $where['b.user_id'] = $userId;
        $where['a.audit_status'] = 0;
        $db = M('test');

        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "b.user_id,a.freq,a.id as project_id,a.name as test_name,a.status,a.examination_id as test_id,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,c.status as attendance_status,d.test_score,a.test_length";
        }else{
            $field = "b.user_id,a.freq,a.id as project_id,a.name as test_name,a.status,a.examination_id as test_id,a.start_time,a.end_time,c.status as attendance_status,d.test_score";
        }

        $res = $db
            ->alias('a')
            ->join('left join __TEST_USER_REL__ b on a.id=b.test_id')
            ->join('left join __EXAMINATION_ATTENDANCE__ c on c.examination_id=b.test_id and c.user_id=b.user_id')
            ->join('left join __EXAMINATION__ d on a.examination_id=d.id')
            ->where($where)
            ->field($field)
            ->order('a.start_time desc')
//          ->page($start_page,$total_page)
            ->select();
        foreach($res as $k=>$v){

            //为了与项目指定的考试保持相同字段，工具管理把考试id拼接字符串tool赋值给project_id作为考试id
            $res[$k]['project_id'] = 'tool'.$v['project_id'];//考试id
            $res[$k]['project_name'] = $v['test_name'];//考试名称
            $res[$k]['score'] = $this->getMaxScore($v['test_id'], $v['project_id'], false,$userId);
            $res[$k]['score'] = $res[$k]['score'] ? $res[$k]['score'] : 0;
            //考试次数
            $num = M('exam_score')->where(array('test_id'=>$v['project_id'],'user_id'=>$userId))->max('counter');
            $num = $num ? $num : 0;
            $res[$k]['counter'] = $num;	//已考次数
            if(strtotime($v['start_time']) > time()){
                $res[$k]['status'] = 1;//'未开始'
                $res[$k]['start_time'] = $v['start_time'];//开始时间
                $res[$k]['end_time'] = $v['end_time'];//结束时间
            }else if( strtotime($v['end_time']) < time() ){
                $res[$k]['status'] = 3;//'已结束'
                $res[$k]['start_time'] = $v['start_time'];//开始时间
                $res[$k]['end_time'] = $v['end_time'];//结束时间
            }else{
                $res[$k]['status'] = 0;//'进行中'
                $res[$k]['start_time'] = $v['start_time'];//开始时间
                $res[$k]['end_time'] = $v['end_time'];//结束时间
            }
            if($num && $num >= $v['freq']){
                if(strtotime($v['end_time']) <= time()) {
                    $res[$k]['status'] = 3;//'已结束';
                }else {
                    $res[$k]['status'] = 2;//'已结束';
                }
                $res[$k]['start_time'] = $v['start_time'];//开始时间
                $res[$k]['end_time'] = $v['end_time'];//结束时间
            }
        }


        $left = $middle = $right = array();
        foreach($res as $k=>$v){
            if($v['status'] == 2 || $v['status'] == 3){//已结束，已逾期
                $right[] = $res[$k];
            }else if($v['status'] == 0){//进行中
                $left[] = $res[$k];
            }else if($v['status'] == 1){//未开始
                $middle[] = $res[$k];
            }
        }
       if($examStatus == 1){
           $final = array_merge($left,$middle);
       }elseif($examStatus ==2){
           $final = $right;
       }
        return $final;
    }


    /**
     * 获取考试的最高得分
     * $eid 试卷ID
     * $tid 考试ID
     * $pid 项目ID
     */
    public function getMaxScore($eid,$tid,$pid = 0,$userId = 0){
        $where = array('exam_id'=>$eid,'user_id'=>$userId);
        if($pid){
            $where['project_id'] = $pid;
        }else{
            $where['test_id'] = $tid;
        }
        $i = M('exam_score')->where($where)->order('total_score desc')->find();
        return $i['total_score'];
    }


    /**
     * 获取试题(工具管理添加的考试)
     * $param["examId"] 试卷ID int
     * test_id 考试id int 必选
     */
    public function getQuest_tool($param,$user_id){
       	$where = array(
	       	'a.test_id' => $param['test_id'],
	       	'b.examination_id' => $param['examId'],//试卷id
	       	'a.user_id' => $user_id
       	);
         $s_type = M('Examination')->where(array('id'=>$param['examId']))->field('s_type,number_score')->find();
         $info = explode(',',$s_type['number_score']);
        //如果是随机组卷触发生成试题
        if($s_type['s_type'] == 1){
            $exam_rle = $this->is_Quest($param['examId'],$param['test_id'],$user_id,$pid = 0,$info);;
        }
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "to_char(b.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,test_length";
		}else{
			$field = 'b.start_time,b.end_time,test_length';
		}
        $testTime = M('test_user_rel a')
        	->join('LEFT JOIN __TEST__ b ON a.test_id = b.id')
        	->field($field)
        	->where($where)
        	->find();
        if(time() < strtotime($testTime["start_time"])){
            return array("code"=>1030, "message"=>"考试还未开始，请等待考试开始");
        }
        if(time() > strtotime($testTime["end_time"])){
            return array("code"=>1030, "message"=>"考试考试已结束，无法答题");
        }
        $time_left = $testTime["test_length"] * 60;
        $quesWhere["a.examination_id"] = $param["examId"];//试卷id
        if($s_type['s_type'] == 1){
            $quesWhere["a.user_id"] = $user_id;
            $quesWhere["a.test_id"] = $param['test_id'];//考试id
        }
        $ques = M("examination_item_rel a")
            ->field("b.id,b.title,b.classification")//b.right_option
            ->join("join __EXAMINATION_ITEM__ b on a.examination_item_id=b.id")
            ->where($quesWhere)
            ->order("b.classification asc,b.id asc")
            ->select();
        if(!$ques){
            return array("code"=>1030, "message"=>"system error,当前考试中没有获取到试题");
        }
		
		//最新需求中，可以设置最多30个试题选项
		//重新查询，把选项并入结果数据中
		foreach($ques as $k=>$v){
			$ques[$k]['opt'] = M('examination_item_opt')
									->where(array('item_id'=>$v['id']))
									->field('letter,content')
									->order('letter asc')
									->select();
		}
		
        foreach ($ques as $key=>$value){
            $aWhere["u_exam_id"] = $user_id;
            $aWhere["question_number"] = $value["id"];
            $aWhere["test_id"] = $param["test_id"];
            $aWhere["exam_id"] = $param["examId"];
            $answer = M("exam_answer")->field("exam_answer")->where($aWhere)->limit(1)->select();
            if($answer){
                $ques[$key]["my_answer"] = $answer[0]["exam_answer"];
            }
			
			//试题选项处理
			foreach($value['opt'] as $k=>$v){
				if($value["classification"] == 1 || $value["classification"] == 2){
	                $ques[$key]["options"][strtoupper($v['letter'])] = $v['content'];
	            }elseif($ques[$key]["classification"] == 3){
	                $ques[$key]["options"]["A"] = '对';
	                $ques[$key]["options"]["B"] = '错';
	            }
				unset($ques[$key]['opt'][$k]);
			}
            
            unset($ques[$key]["opt"]);
        }
	
        $data["time_left"] = $time_left;
        $data["examId"] = $param["examId"];
        $data["project_id"] = 'tool'.$param["test_id"];
        $data["list"] = $ques;
        
        return array('code'=>1000,'message'=>'获取数据成功','data'=>$data);
    }


    /**
     * 获取试题
     * $param["examId"] 试卷ID int
     * project_id 项目ID int 必选
     */
    public function getQuest($param,$user_id){
    	if(!$param || !$user_id){
    		return false;
    	}
    	
    	if(!is_int($param["examId"]) || $param["examId"] < 1){
    		return array("code"=>1021, "message"=>"请提交考试ID");
    	}
    	
    	if(!is_int($param["project_id"]) || $param["project_id"] < 1){
    		return array("code"=>1022, "message"=>"请提交项目ID");
    	}
        $s_type = M('Examination')->where(array('id'=>$param['examId']))->field('s_type,number_score')->find();
        $info = explode(',',$s_type['number_score']);
        //如果是组卷触发生成试题
        if($s_type['s_type'] == 1){
            $exam_rle = $this->is_Quest($param['examId'],$param['test_id'] = 0,$user_id,$param["project_id"],$info);
        }
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,test_length";
		}else{
			$field = 'start_time,end_time,test_length';
		}
    	$testTime = M("project_examination")
    		->field($field)
    		->where("project_id=".$param["project_id"]." and test_id=".$param["examId"])
    		->limit(1)
    		->select();
    	if(time() < strtotime($testTime[0]["start_time"])){
    		return array("code"=>1023, "message"=>"考试还未开始，请等待考试开始");
    	}
    	if(time() > strtotime($testTime[0]["end_time"])){
    		return array("code"=>1024, "message"=>"考试考试已结束，无法答题");
    	}
    	$time_left = $testTime[0]["test_length"] * 60;
    	
    	$quesWhere["a.examination_id"] = $param["examId"];

        if($s_type['s_type'] == 1){
            $quesWhere["a.user_id"] = $user_id;
            $quesWhere["a.project_id"] = $param["project_id"];
        }
    	$ques = M("examination_item_rel a")
    		->field("b.id,b.title,b.classification")//b.right_option
    		->join("join __EXAMINATION_ITEM__ b on a.examination_item_id=b.id")	
    		->where($quesWhere)
    		->order("b.classification asc")
    		->select();
    	if(!$ques){
    		return array("code"=>1025, "message"=>"system error,当前考试中没有获取到试题");
    	}
    	
		//最新需求中，可以设置最多30个试题选项
		//重新查询，把选项并入结果数据中
		foreach($ques as $k=>$v){
			$ques[$k]['opt'] = M('examination_item_opt')
									->where(array('item_id'=>$v['id']))
									->field('letter,content')
									->order('letter asc')
									->select();
		}
		
    	foreach ($ques as $key=>$value){
	    	$aWhere["u_exam_id"] = $user_id;
	    	$aWhere["question_number"] = $value["id"];
	    	$aWhere["project_id"] = $param["project_id"];
	    	$aWhere["exam_id"] = $param["examId"];
    		$answer = M("exam_answer")->field("exam_answer")->where($aWhere)->limit(1)->select();
    		if($answer){
    			$ques[$key]["my_answer"] = $answer[0]["exam_answer"];
    		}
    		
    		//试题选项处理
			foreach($value['opt'] as $k=>$v){
				if($value["classification"] == 1 || $value["classification"] == 2){
	                $ques[$key]["options"][strtoupper($v['letter'])] = $v['content'];
	            }elseif($ques[$key]["classification"] == 3){
	                $ques[$key]["options"]["A"] = '对';
	                $ques[$key]["options"]["B"] = '错';
	            }
				unset($ques[$key]['opt'][$k]);
			}
            
            unset($ques[$key]["opt"]);
    	}
    	
    	$data["time_left"] = $time_left;
    	$data["examId"] = $param["examId"];
    	$data["project_id"] = $param["project_id"];
    	$data["list"] = $ques;
        return array('code'=>1000,'message'=>'获取数据成功','data'=>$data);
    }
    

    /**
     * 交卷(工具管理考试交卷)
     * $param["examId"] 试卷ID int
     * $param["project_id"] => test_id   考试ID int
     */
    public function finish_tool($param,$user_id){

        $where = array(
            'a.test_id' => $param['test_id'],
            'b.examination_id' => $param['examId'],//试卷id
            'a.user_id' => $user_id
        );
        
        //当前考试可考次数
        $freq = M('test')->where(array('id'=>$param['test_id'],'examination_id'=>$param['examId']))->getField('freq');
		//当前考试已考次数
		$counter = M('exam_score')
			->where(array('test_id'=>$param['test_id'],'exam_id'=>$param['examId'],'user_id'=>$user_id))
			->max('counter');
		$counter = $counter ? $counter : 0;
        /*//判断是否重复提交试卷
        $message = M('examination_attendance')
        	->where(array('user_id'=>$user_id,'test_id'=>$param['examId'],'examination_id'=>$param['test_id']))
        	->find();*/
        
        if($counter >= $freq){
            return array("code"=>1030, "message"=>"本次考试已无可用次数");
        }
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "to_char(b.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
		}else{
			$field = 'b.start_time,b.end_time';
		}
        $testTime = M('test_user_rel a')
        	->join('LEFT JOIN __TEST__ b ON a.test_id = b.id')
        	->field($field)
        	->where($where)
        	->find();
		
        if(time() < strtotime($testTime["start_time"])){
            return array("code"=>1030, "message"=>"考试还未开始，请等待考试开始");
        }
        //判断试卷是否存在
        $exam = M("test");
        $hasData = $exam->where(array("examination_id" => $param["examId"]))->find();
        if(!$hasData){
            return array("code"=>1030, "message"=>"没有找到对应的试卷examId");
        }

        //判断当前用户是否有此试卷
        $hasThis['user_id'] = M('test_user_rel a')
        	->join('LEFT JOIN __TEST__ b ON a.test_id = b.id')
        	->field('a.user_id')
        	->where($where)
        	->find();
        if(!$hasThis['user_id']){
            return array("code"=>1030, "message"=>'当前考试不属于该学员');
        }
		
        //循环保存答案
        foreach ($param["answer"] as $key=>$value){
        	if(!$key || !$value){
        		continue;
        	}
            //获取正确答案
            $_where["id"] = $key;
            $exam = M("examination_item")
            	->field("right_option,classification,keywords")
            	->where($_where)
            	->find();
			
            if(!$exam){
                continue;
            }
            $isExam = 0;
            //1表示单选择题 2表示多选 3判断题 4问答题'
            $exam_answer = "";
            if(in_array($exam["classification"],array('1','2','3'))){
                $exam_answer =  strtoupper($value);
                if($exam_answer == $exam["right_option"]){
                    $isExam = 1;
                }
            }else{
                //获取此道题目分值
                $exam_answer = $value;
                $thisTest = M("examination_item_rel")
                	->where("examination_id=".$param["examId"]." and examination_item_id=".$key)
                	->find();
                if($thisTest){
                    //简答题，判断关键字
                    $keywordArr = explode(",", $exam["keywords"]);
                    $keywordNum = count($keywordArr);
                    $thisTestScore = $thisTest["score"];
                    $isAllRight = true;
                    $getScore = 0;
                    foreach ($keywordArr as $valueWord){
                        if(strstr($exam_answer, $valueWord)){
                            $getScore += $thisTestScore / $keywordNum;
                        }else{
                            $isAllRight = false;
                        }
                    }

                    if($isAllRight){
                        $getScore = $thisTestScore;
                    }else{
                        $getScore = ceil($getScore);
                    }
                    $data["wdscore"] = $getScore;
                    $data["checked"] = "0";
                    $isExam = 0;
                }else{
                    $isExam = 0;
                }
            }

            $data["exam_answer"] = $exam_answer;
            $data["isexam"] = $isExam;
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$data["data_tiem"] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
			}else{
				$data["data_tiem"] = date("Y-m-d H:i:s");
			}
            //是否已有答题数据
            $aWhere["u_exam_id"] = $user_id;
            $aWhere["question_number"] = $key;
            $aWhere["test_id"] = $param["test_id"];
            $hasAnswer = M("exam_answer")->where($aWhere)->limit(1)->select();

            $data["test_id"] = $param["test_id"];//考试ID
            $data["classification"] = $exam["classification"];//题型
            $data["correct_answer"] = $exam["right_option"];//正确答案
            $data["u_exam_id"] = $user_id;//考试者id
            $data["exam_id"] = $param["examId"];//试卷id
            $data["question_number"] = $key;//题号
            $data["counter"] = $counter + 1;//当前第几次考试
            
            if(strtolower(C('DB_TYPE')) == 'oracle'){
			    $data['id'] = getNextId('exam_answer');
			}
            $return = M("exam_answer")->add($data);
        }
        //计算总分
        $where2["a.u_exam_id"] = $user_id;
        $where2["a.exam_id"] = $param["examId"];
        $where2["a.test_id"] = $param["test_id"];
        $where2["a.isexam"] = 1;
        $where2["b.examination_id"] = $param["examId"];
        $resp = M("exam_answer a")->field("sum(b.score) as total_score")
            ->join("JOIN __EXAMINATION_ITEM_REL__ b ON a.question_number=b.examination_item_id")
            ->where($where2)
            ->select();
        //echo M("exam_answer as a")->getLastSql();
        $totalScore = $resp[0]["total_score"];

        //再加上简答题得分
        $where3["a.exam_id"] = $param["examId"];
        $where3["a.test_id"] = $param["test_id"];
        $where3["a.u_exam_id"] = $user_id;
        $where3["a.classification"] = 4;
        $wdscore = M("exam_answer a")->field("sum(wdscore) as total_score")->where($where3)->select();
        $wdscore = $wdscore[0]["total_score"];
        $totalScore = $totalScore + $wdscore;
        $totalScore = round($totalScore);

        $score = M("exam_score");
        $sWhere["user_id"] = $user_id;
        $sWhere["exam_id"] = $param["examId"];
        $sWhere["test_id"] = $param["test_id"];
        $hasScore = $score->where($sWhere)->select();

        $data3["user_id"] = $user_id;
        $data3["exam_id"] = $param["examId"];
        $data3["total_score"] = $totalScore;
        $data3["test_id"] = $param["test_id"];
        $data3["is_publish"] = 0;
        $data3["use_time"] = $param["use_time"];
        $data3["counter"] = $counter + 1; //当前第几次考试
		if(strtolower(C('DB_TYPE')) == 'oracle'){
		    $data3['id'] = getNextId('exam_score');
		}

        $return = $score->add($data3);
        if(!$return){
            return array("code"=>1030,"message"=>'提交失败，可尝试重新操作');
        }
        //think_examination_attendance 学员考试考勤表
        $attWhere["user_id"] = $user_id;
        $attWhere["test_id"] = $param["examId"];
        $atten = M("examination_attendance")->field("id")->where($attWhere)->find();
        //获取岗位部门
        $job = M("tissue_group_access a");
        $job = $job->where("a.user_id=".$user_id)->field("a.tissue_id,a.job_id")->select();
		
		if(!$atten){
			$attData["user_id"] = $user_id;
            $attData["test_id"] = $param["examId"];
            $attData["department_id"] = $job[0]["tissue_id"];//部门ID
            $attData["position_id"] = $job[0]["job_id"];//岗位ID
            $attData["status"] = 1;
            $attData["examination_id"] = $param["test_id"];
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
			    $attData['id'] = getNextId('examination_attendance');
			}
            $res = M("examination_attendance")->add($attData);
		}else{
			$res = M("examination_attendance")->where(array('id'=>$atten['id']))->save(array('status'=>1));
		}
        if($res){
            return array("code"=>1000,"message"=>'提交成功');
        }else{
            return array("code"=>1030,"message"=>'提交失败，可尝试重新操作');
        }
    }
    /**
     * 交卷
     * $param["examId"] 试卷ID int
     * $param["project_id"] 项目ID int
     */
    public function finish($param,$user_id){
    	if(!$param || !$user_id){
    		return array("code"=>1001,"message"=>"缺少参数");
    	}
    	$param["examId"] += 0;
    	$param["project_id"] += 0;
    	if(!is_int($param["examId"]) || $param["examId"] < 1){
    		return array("code"=>1002, "message"=>"缺少参数：examId，试卷id");
    	}
    	
    	if(!is_int($param["project_id"]) || $param["project_id"] < 1){
    		return array("code"=>1011, "message"=>"缺少参数：project_id，项目id");
    	}

        //当前考试可考次数
		$freq = M('project_examination')
			->where(array('test_id'=>$param['examId'],'project_id'=>$param['project_id']))
			->getField('freq');
		
		//当前考试已考次数
		$counter = M('exam_score')
			->where(array('project_id'=>$param['project_id'],'exam_id'=>$param['examId'],'user_id'=>$user_id))
			->max('counter');
		$counter = $counter ? $counter : 0;
		/*//判断是否重复提交试卷
		$message = M('examination_attendance')
			->where(array('user_id'=>$user_id,'test_id'=>$param['examId'],'examination_id'=>$param['test_id']))
			->find();*/
		if($counter >= $freq){
			return array("code"=>1030, "message"=>"本次考试已无可用次数");
		}

		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
		    $field = "to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
		}else{
		    $field = 'start_time,end_time';
		}
    	$testTime = M("project_examination")
    		->field($field)
    		->where("project_id=".$param["project_id"]." and test_id=".$param["examId"])
    		->limit(1)
    		->select();
    	if(time() < strtotime($testTime[0]["start_time"])){
    		return array("code"=>1011, "message"=>"考试还未开始，请等待考试开始");
    	}
    	/* if(time() > strtotime($testTime[0]["end_time"])){
    		return array("code"=>1011, "message"=>"考试考试已结束，无法答题");
    	} */
    	
    	//判断试卷是否存在
    	$exam = M("project_examination");
    	$hasData = $exam->where("test_id=".$param["examId"])->select();
    	if(!$hasData){
    		return array("code"=>1012, "message"=>"没有找到对应的试卷examId");
    	}
    	
    	//判断当前用户是否有此试卷
    	$db = M("designated_personnel");
    	$hasThis = $db
    		->field("user_id")
    		->where("project_id=".$param["project_id"]." AND user_id=".$user_id)
    		->limit(1)
    		->select();
    	if(!$hasThis){
    		return array("code"=>1028, "message"=>'当前考试不属于该学员');
    	}
    	//循环保存答案
    	foreach ($param["answer"] as $key=>$value){
    		//获取正确答案
    		$where["id"] = $key;
    		$exam = M("examination_item")
    			->field("right_option,classification,keywords")
				->where($where)
				->find();
    		if(!$exam){
    			continue;
    		}
    		$isExam = 0;
    		//1表示单选择题 2表示多选 3判断题 4问答题'
    		$exam_answer = "";
    		if(in_array($exam["classification"],array('1','2','3'))){
                $exam_answer =  strtoupper($value);
                if($exam_answer == $exam["right_option"]){
                    $isExam = 1;
                }
            }else{
    			//获取此道题目分值
    			$exam_answer = $value;
    			$thisTest = M("examination_item_rel")
    				->where("examination_id=".$param["examId"]." and examination_item_id=".$key)
    				->find();
    			if($thisTest){
    				//简答题，判断关键字
    				$keywordArr = explode(",", $exam["keywords"]);
    				$keywordNum = count($keywordArr);
    				$thisTestScore = $thisTest["score"];
    				$isAllRight = true;
    				$getScore = 0;
    				foreach ($keywordArr as $valueWord){
	    				if(strstr($exam_answer, $valueWord)){
	    					$getScore += $thisTestScore / $keywordNum;
	    				}else{
	    					$isAllRight = false;
	    				}
    				}
    				
    				if($isAllRight){
    					$getScore = $thisTestScore;
    				}else{
    					$getScore = ceil($getScore);
    				}
    				$data["wdscore"] = $getScore;
    				$data["checked"] = "0";
	    			$isExam = 0;
    			}else{
	    			$isExam = 0;
    			}
    		}
    		
    		$data["exam_answer"] = $exam_answer;
    		$data["isexam"] = $isExam;
    		$data["data_tiem"] = date("Y-m-d H:i:s");
    		
    		//是否已有答题数据
			$aWhere["u_exam_id"] = $user_id;
			$aWhere["question_number"] = $key;
			$aWhere["project_id"] = $param["project_id"];
			$hasAnswer = M("exam_answer")->where($aWhere)->limit(1)->select();
			$data["project_id"] = $param["project_id"];//项目ID
			$data["classification"] = $exam["classification"];
			$data["correct_answer"] = $exam["right_option"];
			$data["u_exam_id"] = $user_id;
			$data["exam_id"] = $param["examId"];
			$data["question_number"] = $key;
            $data["counter"] = $counter + 1;//当前第几次考试
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
			    $data['id'] = getNextId('exam_answer');
			    $data["data_tiem"] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
			}
			$return = M("exam_answer")->add($data);
    	}
    	
    	//计算总分
    	$where2["a.u_exam_id"] = $user_id;
    	$where2["a.exam_id"] = $param["examId"];
    	$where2["a.project_id"] = $param["project_id"];
    	$where2["a.isexam"] = 1;
    	$where2["b.examination_id"] = $param["examId"];
    	$resp = M("exam_answer a")
    		->field("sum(b.score) as total_score")
    		->join("JOIN __EXAMINATION_ITEM_REL__ b ON a.question_number=b.examination_item_id")
    		->where($where2)
    		->select();
    	$totalScore = $resp[0]["total_score"];
    	
    	//再加上简答题得分
    	$where3["a.exam_id"] = $param["examId"];
    	$where3["a.project_id"] = $param["project_id"];
    	$where3["a.u_exam_id"] = $user_id;
    	$where3["a.classification"] = 4;
    	$wdscore = M("exam_answer a")->field("sum(wdscore) as total_score")->where($where3)->select();
    	$wdscore = $wdscore[0]["total_score"];
    	$totalScore = $totalScore + $wdscore;
    	$totalScore = round($totalScore);
    	
    	$score = M("exam_score");
    	$sWhere["user_id"] = $user_id;
    	$sWhere["exam_id"] = $param["examId"];
    	$sWhere["project_id"] = $param["project_id"];
    	//$hasScore = $score->field("max(total_score)")->where($sWhere)->find();
    	
    	$data3["user_id"] = $user_id;
    	$data3["exam_id"] = $param["examId"];
    	$data3["total_score"] = $totalScore;
    	$data3["project_id"] = $param["project_id"];
    	$data3["is_publish"] = 0;
    	$data3["test_id"] = $param["examId"];
    	$data3["use_time"] = $param["use_time"];
    	$data3["counter"] = $counter + 1;	//当前第几次考试
    	/*if($hasScore){
	    	$return = $score->where($sWhere)->save($data3);
	    	if($return === false){
	    		return array("code"=>1031,"message"=>'更新失败，可尝试重新操作');
	    	}
    	}else{
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
			    $data3['id'] = getNextId('exam_score');
			}
	    	$return = $score->add($data3);
	    	if($return < 0 || $return === false){
	    		return array("code"=>1031,"message"=>'提交失败，可尝试重新操作');
	    	}
    	}*/
    	if(strtolower(C('DB_TYPE')) == 'oracle'){
		    $data3['id'] = getNextId('exam_score');
		}
    	$return = $score->add($data3);
    	if(!$return){
    		return array("code"=>1030,"message"=>'提交失败，可尝试重新操作');
    	}
    	
    	//think_examination_attendance 学员考试考勤表 
    	$attWhere["user_id"] = $user_id;
    	$attWhere["test_id"] = $param["examId"];
        $attWhere["project_id"] = $param["project_id"];
    	$atten = M("examination_attendance")->field("id")->where($attWhere)->find();
    	//获取岗位部门
    	$job = M("tissue_group_access a");
    	$job = $job->where("a.user_id=".$user_id)->field("a.tissue_id,a.job_id")->select();
    	if($atten){
			$res = M("examination_attendance")->where(array('id'=>$atten['id']))->save(array('status'=>1));
    	}else{
			$attData["user_id"] = $user_id;
			$attData["test_id"] = $param["examId"];//试卷id
			$attData["department_id"] = $job[0]["tissue_id"];//部门ID
			$attData["position_id"] = $job[0]["job_id"];//岗位ID
			$attData["status"] = 1;
			$attData["project_id"] = $param["project_id"];
			//$attData["examination_id"] = $param["examId"];;//?????where
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
			    $attData['id'] = getNextId('examination_attendance');
			}
			$res = M("examination_attendance")->add($attData);
    	}
		
        if($res){
            return array("code"=>1000,"message"=>'提交成功');
        }else{
            return array("code"=>1030,"message"=>'提交失败，可尝试重新操作');
        }
    }

    /**
     * 查看考试题目详情(工具管理添加的数据)
     * $param["examId"] 试卷ID int
     * $param['test_id'] 考试id
     */
    public function seeDetail_tool($param,$user_id){
        $s_type = M('Examination')->where(array('id'=>$param['examId']))->field('s_type')->find();
        if($s_type['s_type'] == 1){
            $quesWhere["a.user_id"] = $user_id;
        }
        $quesWhere["a.examination_id"] = $param["examId"];//试卷id
        $quesWhere["a.test_id"] = $param["test_id"];//考试id
        $ques = M("examination_item_rel a")
        	->field("id,title,classification,b.right_option,score")
        	->join("join __EXAMINATION_ITEM__ b on a.examination_item_id=b.id")
            ->where($quesWhere)->order("a.examination_item_id asc")->select();
        if(!$ques){
            return array('code'=> 1030,'message' => "system error,当前考试中没有获取到试题");
        }

        //考试时间未结束，不能查看详情
        $peWhere["id"] = $param["test_id"];
        $peWhere["examination_id"] = $param["examId"];
        $project_exam = M("test")->field("end_time")->where($peWhere)->select();
        if(time() <= strtotime($project_exam[0]["end_time"])){
            return array('code'=> 1030,'message' => "考试结束时间未到，请稍后查看");
        }
		
		//最新需求中，可以设置最多30个试题选项
		//重新查询，把选项并入结果数据中
		foreach($ques as $k=>$v){
			$ques[$k]['opt'] = M('examination_item_opt')
									->where(array('item_id'=>$v['id']))
									->field('letter,content')
									->order('letter asc')
									->select();
		}

        foreach ($ques as $key=>$value){
            $aWhere["u_exam_id"] = $user_id;
            $aWhere["question_number"] = $value["id"];
            $aWhere["test_id"] = $param["test_id"];//考试id
            $aWhere["exam_id"] = $param["examId"];
			
			//分数最高的是第几次考试
			$max = M('exam_score')
				->where(array('user_id'=>$user_id,'exam_id'=>$param["examId"],'test_id'=>$param["test_id"]))
				->order('total_score desc')
				->field('counter')
				->find();
				
			$aWhere["counter"] = $max['counter'];
			
            $answer = M("exam_answer")->field("exam_answer,isexam")->where($aWhere)->limit(1)->select();
            
            if($answer){
                $ques[$key]["isexam"] = $answer[0]["isexam"];
                $ques[$key]["my_answer"] = $answer[0]["exam_answer"];
            }else{
                $ques[$key]["isexam"] = 0;
                $ques[$key]["my_answer"] = "";
            }

            //试题选项处理
			foreach($value['opt'] as $k=>$v){
				if($value["classification"] == 1 || $value["classification"] == 2){
					$ques[$key]["options"][strtoupper($v['letter'])] = $v['content'];
				}elseif($ques[$key]["classification"] == 3){
					$ques[$key]["options"]["A"] = '对';
					$ques[$key]["options"]["B"] = '错';
				}
				unset($ques[$key]['opt'][$k]);
			}
			
			unset($ques[$key]["opt"]);
        }

        $where3["user_id"] = $user_id;
        $where3["exam_id"] = $param["examId"];
        $where3["test_id"] = $param["test_id"];
        $where3["counter"] = $max['counter'];
        $exam_score = M("exam_score")->where($where3)->select();
        $use_time = $exam_score[0]["use_time"];
        //转为时分秒--按一天内计算
        $use_time = gmstrftime('%H:%M:%S',$use_time);

        $data["use_time"] = $use_time;
        $data["total_score"] = $exam_score[0]["total_score"] ? $exam_score[0]["total_score"] : 0;
        $data["list"] = $ques;
        return array('code'=>1000,'message'=>'获取数据成功','data'=>$data);
    }


    /**
     * 查看考试题目详情
     * $param["examId"] 试卷ID int
     * $param["quesId"] 题目ID 不传默认试卷第一题,相应ID为对应的题目
     */
    public function seeDetail($param,$user_id){
        $s_type = M('Examination')->where(array('id'=>$param['examId']))->field('s_type')->find();
        if($s_type['s_type'] == 1){
            $quesWhere["a.user_id"] = $user_id;
        }
        $quesWhere["a.examination_id"] = $param["examId"];//试卷id
        $quesWhere["a.project_id"] = $param["project_id"];//考试id

    	$ques = M("examination_item_rel a")
    		->field("id,title,classification,b.right_option,score")
    		->join("join __EXAMINATION_ITEM__ b on a.examination_item_id=b.id")	
    		->where($quesWhere)
    		->order("a.examination_item_id asc")
    		->select();
    	if(!$ques){
            return array('code'=> 1030,'message' => "system error,当前考试中没有获取到试题");
    	}
    	
    	//考试时间未结束，不能查看详情
    	$peWhere["project_id"] = $param["project_id"];
    	$peWhere["test_id"] = $param["examId"];
    	$project_exam = M("project_examination")->field("end_time")->where($peWhere)->select();
    	if(time() <= strtotime($project_exam[0]["end_time"])){
            return array('code'=> 1030,'message' => "考试结束时间未到，请稍后查看");
    	}
		//最新需求中，可以设置最多30个试题选项
		//重新查询，把选项并入结果数据中
		foreach($ques as $k=>$v){
			$ques[$k]['opt'] = M('examination_item_opt')
									->where(array('item_id'=>$v['id']))
									->field('letter,content')
									->order('letter asc')
									->select();
		}
    	
    	foreach ($ques as $key=>$value){
	    	$aWhere["u_exam_id"] = $user_id;
	    	$aWhere["question_number"] = $value["id"];
	    	$aWhere["project_id"] = $param["project_id"];
	    	$aWhere["exam_id"] = $param["examId"];
			
			//分数最高的是第几次考试
			$max = M('exam_score')
				->where(array('user_id'=>$user_id,'exam_id'=>$param["examId"],'project_id'=>$param["project_id"]))
				->order('total_score desc')
				->field('counter')
				->find();
				
			$aWhere["counter"] = $max['counter'];
			
    		$answer = M("exam_answer")->field("exam_answer,isexam")->where($aWhere)->limit(1)->select();
    		if($answer){
    			$ques[$key]["isexam"] = $answer[0]["isexam"];
    			$ques[$key]["my_answer"] = $answer[0]["exam_answer"];
    		}else{
    			$ques[$key]["isexam"] = 0;
    			$ques[$key]["my_answer"] = "";
    		}
    		
    		//试题选项处理
			foreach($value['opt'] as $k=>$v){
				if($value["classification"] == 1 || $value["classification"] == 2){
					$ques[$key]["options"][strtoupper($v['letter'])] = $v['content'];
				}elseif($ques[$key]["classification"] == 3){
					$ques[$key]["options"]["A"] = '对';
					$ques[$key]["options"]["B"] = '错';
				}
				unset($ques[$key]['opt'][$k]);
			}
			
			unset($ques[$key]["opt"]);
    	}
    	
    	$where3["user_id"] = $user_id;
    	$where3["exam_id"] = $param["examId"];
    	$where3["project_id"] = $param["project_id"];
        $where3["counter"] = $max['counter'];
    	$exam_score = M("exam_score")->where($where3)->select();
    	$use_time = $exam_score[0]["use_time"];
    	//转为时分秒--按一天内计算
    	$use_time = gmstrftime('%H:%M:%S',$use_time);
    	
    	$data["use_time"] = $use_time;
    	$data["total_score"] = $exam_score[0]["total_score"] ? $exam_score[0]["total_score"] : 0;
    	$data["list"] = $ques;
        return array('code'=>1000,'message'=>'获取数据成功','data'=>$data);
    }

    /**
     * 判断是否有试题
     * $exam_id 试卷id
     * $pid = $get['project_id'];//项目id
     * $test_id 考试id
     */
    public function is_Quest($exam_id,$test_id,$user_id,$pid,$info){
        //根据组卷规则判断是读取试题信息还是实时组卷
       // $s_type = M('Examination')->where(array('id'=>$exam_id))->field('s_type,number_score')->find();
       // $info = explode(',',$s_type['number_score']);
        $a = array(
            'examination_id'=>$exam_id,
            'user_id'=>$user_id
        );
        if($pid){
            $a['project_id'] = $pid;//项目id
        }else{
            $a['test_id'] = $test_id;//考试id
        }
        $r = M('examination_item_rel')
            ->where($a)
            ->find();
        //单选题

            if(!$r){
                $random_dan_ids = $this->random_examinations($info[0],1,$info[1],array());
                foreach($random_dan_ids as $k=>$v){
                    $singleChoice[$k] = M('examination_item')->where(array('id'=>$v))->find();
                    $singleChoice[$k]['score'] = $info[1];

                    $examItemRelData = array(
                        'examination_id' => $exam_id,
                        'examination_item_id' => $v,
                        'score' => $info[1],
                        'user_id'=>$user_id,
                        'test_id'=>$test_id,
                        'project_id'=>$pid
                    );
                    M('examination_item_rel')->add($examItemRelData);
                }



        //多选题

                $random_duo_ids = $this->random_examinations($info[2],2,$info[3],array());
                foreach($random_duo_ids as $k=>$v){
                    $multipleChoice[$k] = M('examination_item')->where(array('id'=>$v))->find();
                    $multipleChoice[$k]['score'] = $info[3];
                    $examItemRelData = array(
                        'examination_id' => $exam_id,
                        'examination_item_id' => $v,
                        'score' => $info[3],
                        'user_id'=>$user_id,
                        'test_id'=>$test_id,
                        'project_id'=>$pid
                    );
                    M('examination_item_rel')->add($examItemRelData);
                }



        //判断题


                $random_pan_ids = $this->random_examinations($info[4],3,$info[5],array());
                foreach($random_pan_ids as $k=>$v){
                    $descriPtive[$k] = M('examination_item')->where(array('id'=>$v))->find();
                    $descriPtive[$k]['score'] = $info[5];
                    $examItemRelData = array(
                        'examination_id' => $exam_id,
                        'examination_item_id' => $v,
                        'score' => $info[5],
                        'user_id'=>$user_id,
                        'test_id'=>$test_id,
                        'project_id'=>$pid
                    );
                    M('examination_item_rel')->add($examItemRelData);
                }



        //问答


                $random_jian_ids = $this->random_examinations($info[6],4,$info[7],array());
                foreach($random_jian_ids as $k=>$v){
                    $wd[$k] = M('examination_item')->where(array('id'=>$v))->find();
                    $wd[$k]['score'] = $info[7];
                    $examItemRelData = array(
                        'examination_id' => $exam_id,
                        'examination_item_id' => $v,
                        'score' => $info[7],
                        'user_id'=>$user_id,
                        'test_id'=>$test_id,
                        'project_id'=>$pid
                    );
                    M('examination_item_rel')->add($examItemRelData);
                }
                return true;
            }else{
                return true;
            }
        }


    /**
     * $num 要获取的试题数量
     * $type 要获取的试题类型
     * $score 试题分数
     * $where 查询条件
     */
    public function random_examinations($num,$type,$score,$where){
        $where['classification'] = $type;
        $data = M('Examination_item')->where($where)->getField('id',true);

        $res = array_rand($data,$num);
        $res = is_array($res) ? $res : (array)$res;

        $ids = array();
        foreach($res as $k=>$v){
            $ids[] = $data[$v];
        }

        return $ids;
    }
}
