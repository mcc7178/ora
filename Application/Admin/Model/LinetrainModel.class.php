<?php
namespace Common\Model;

use Common\Model\BaseModel;
/**
 * 菜单操作model
 */
class LinetrainModel extends BaseModel{

	//初始化
	public function __construct(){}

	/**
	 * 首页
	 */
	public function index($total_page){

		$start_page = I("get.p",0,'int');
		$plan_category = I('get.plan_category');
		$training_category = I('get.training_category');
		$keyword=I("get.keyword")?I("get.keyword"):"";
		$typeid = I("get.typeid",0,'int');

		$where['a.typeid'] = array("eq",1);
		$where['a.user_id'] = array("eq",$_SESSION['user']['id']);

		if($plan_category != "" AND ($plan_category == 0 OR $plan_category == 1)){
			$where['b.plan_category'] = array("eq",$plan_category);
		}

		if($training_category != "" AND ($training_category == 0 OR $training_category == 1 OR $training_category == 2)){
			$where['b.training_category'] = array("eq",$training_category);
		}

		if(!empty($keyword)){

			$where['_string']="(b.project_name like '%".$keyword."%')  OR (b.class_name like '%".$keyword."%') OR (b.project_description like '%".$keyword."%') OR (c.username like '%".$keyword."%')";

		}

		$DB_PREFIX = strtolower(C('DB_PREFIX').'designated_personnel');

		if($typeid == 0){

			$order = "b.id desc";

		}else{

			$order = "num desc";
		}

		$results = M("designated_personnel a")->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id LEFT JOIN __USERS__ c ON b.user_id = b.id")->field("b.id,b.project_name,b.project_description,b.project_covers,b.plan_category,to_char(b.start_time,'yyyy-mm-dd hh24:mi:ss') as start_time,to_char(b.end_time,'yyyy-mm-dd hh24:mi:ss') as end_time,(select count(id) from $DB_PREFIX where project_id = a.project_id) as num")->where($where)->page($start_page,$total_page)->order($order)->select();

		$total_number = $credit_total = $online = $face_total = 0;

		//学分,课程,面授,参与人数
		foreach($results as $k=>$list){

			$items[$k] = $list;

			$course_list = M("project_course a")->join("LEFT JOIN __COURSE__ b ON a.course_id = b.id")->field("a.credit,b.course_way")->where("a.project_id =".$list['id'])->select();

			$total_number = M("designated_personnel")->where("typeid = 1 and sign_up = 1 and project_id =".$list['id'])->count();

			foreach($course_list as $list){

				$credit_total += $list['credit'];

				if($list['course_way'] == 0){
					$online += 1;
				}else{
					$face_total += 1;
				}

			}

			$items[$k]['credit_total'] = $credit_total;
			$items[$k]['online'] = $online;
			$items[$k]['face_total'] = $face_total;
			$items[$k]['total_number'] = $total_number;

		}

		$count = M("designated_personnel a")->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id = b.id LEFT JOIN __USERS__ c ON b.user_id = b.id")->where($where)->count();

		//输出分页
		$show = $this->pageClass($count,$total_page);

		$data = array(
			'page' => $show,
			'list' => $items,
			'keyword'=>$keyword,
			'plan_category'=>$plan_category,
			'training_category'=>$training_category,
			'typeid'=>$typeid
		);


		return $data;
	}

	/**
	 * 详情页
	 */
	public function sign_up(){

		$project_id = I("get.id",0,'int');

		$population_total = $total_number = $credit_total = $online = $face_total = 0;

		//描述
		$project_data = M("admin_project")->field("training_category,population,id,project_name,project_description,project_covers,plan_category,to_char(start_time,'yyyy-mm-dd hh24:mi:ss') as start_time,to_char(end_time,'yyyy-mm-dd hh24:mi:ss') as end_time")->where("id =".$project_id)->find();

		//学分,课程,面授,参与人数
		$course_list = M("project_course a")->join("LEFT JOIN __COURSE__ b ON a.course_id = b.id")->field("a.credit,b.course_way")->where("a.project_id =".$project_id)->select();

		$total_number = M("designated_personnel")->where("project_id =".$project_id)->count();

		$map['project_id'] = array("eq",$project_id);
		$map['sign_up'] = array("eq",1);
		$map['typeid'] = array("eq",1);

		$population_total = M("designated_personnel")->where($map)->count();

		foreach($course_list as $list){

			$credit_total += $list['credit'];

			if($list['course_way'] == 0){
				$online += 1;
			}else{
				$face_total += 1;
			}

		}

		$project_data['credit_total'] = $credit_total;
		$project_data['online'] = $online;
		$project_data['face_total'] = $face_total;
		$project_data['total_number'] = $total_number;
		$project_data['population_total'] = $population_total;

		//课程数据
		$course_data = M("project_course a")->join("LEFT JOIN __COURSE__ b ON a.course_id = b.id LEFT JOIN __LECTURER__ c ON b.lecturer = c.id")->field("a.project_id,a.course_id,a.credit,to_char(a.start_time,'yyyy-mm-dd hh24:mi:ss') as start_time,to_char(a.end_time,'yyyy-mm-dd hh24:mi:ss') as end_time,b.course_name,b.course_way,b.lecturer_name,c.name,a.location")->where("a.project_id =".$project_id)->select();

		//获取考试
		$examination_data = M("project_examination a")
			->field("a.freq,a.examination_address,a.cacheid,a.test_names,a.manager_id,a.test_length,a.credits,a.test_id,a.project_id,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,b.test_name,c.username")
			->join("LEFT JOIN __EXAMINATION__ b ON a.test_id = b.id LEFT JOIN __USERS__ c ON a.manager_id = c.id")
			->where("a.project_id = ".$project_id)
			->select();


		foreach($examination_data as $k=>$v){

			$examination_data[$k]['total_score'] = D('MyExam')->getMaxScore($v['test_id'],false,$v['project_id']);

			//考试次数
			$num = M('exam_score')
				->where(array('project_id'=>$v['project_id'],'exam_id'=>$v['test_id'],'user_id'=>session('user.id')))
				->max('counter');
			$examination_data[$k]['counter'] = $num;	//已考次数

			if(strtotime($v['start_time']) > time()){
				$examination_data[$k]['statusinfo'] = '未开始';
			}else if(strtotime($v['end_time']) < time()){
				$examination_data[$k]['statusinfo'] = '已结束';
			}else{
				$examination_data[$k]['statusinfo'] = '进行中';
			}

			if($num && $num >= $v['freq']){
				$examination_data[$k]['statusinfo'] = '已结束';
			}

			if(!$v['start_time']){
				unset($examination_data[$k]);
			}

		}


		//获取调研
		$survey_data = M("project_survey a")
			->field("a.survey_id,a.project_id,a.survey_names,a.credit,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,b.survey_name,c.username")
			->join("LEFT JOIN __SURVEY__ b ON a.survey_id = b.id LEFT JOIN __USERS__ c ON a.manager_id = c.id")
			->where("a.project_id = ".$project_id)
			->select();

		foreach($survey_data as $k=>$data){

			$condition['user_id'] = array("eq",$_SESSION['user']['id']);
			$condition['survey_id'] = array("eq",$data['survey_id']);
			$condition['project_id'] = array("eq",$data['project_id']);

			$status = M("survey_attendance")->where($condition)->getField("status");

			$survey_data[$k]['status'] = $status ? $status : 0;
		}

		//查询报名状态
		$where['project_id'] = array("eq",$project_id);
		$where['sign_up'] = array("eq",0);
		$where['typeid'] = array("eq",1);
		$where['user_id'] = array("eq",$_SESSION['user']['id']);

		$state = M("designated_personnel")->field("id")->where($where)->find();

		$data = array(
			'project_data'=>$project_data,
			'course_data'=>$course_data,
			'examination_data'=>$examination_data,
			'survey_data'=>$survey_data,
			'state'=>$state
		);


		return $data;


	}

	/**
	 * 申请报名
	 */
	public function form_sign(){

		$project_id = I("post.id",0,'int');

		$where['project_id'] = array("eq",$project_id);
		$where['sign_up'] = array("eq",0);
		$where['typeid'] = array("eq",1);
		$where['user_id'] = array("eq",$_SESSION['user']['id']);

		//查询项目限制次数
		$project = M("admin_project")->field("population,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') end_time")->where("id=".$project_id)->find();

		//查已报名次数
		$map = $where;
		$map['sign_up'] = array("eq",1);
		unset($map['user_id']);

		$population_total = M("designated_personnel")->where($map)->count();


		if(strtotime($project['start_time']) < time() && strtotime($project['end_time']) > time()){

			if($project['population'] > $population_total){

				M("designated_personnel")->where($where)->setField('sign_up',1);

				$status = 200;

			}else{

				$status = 400;
			}


		}else{
			$status = 402;

		}

		return $status;
	}



}
