<?php

namespace Mobile\Model;

use Think\Model;
/**
 * 选修培训班model
 */
class LineTrainModel extends CommonModel{


	/**
	 * 线下培训班列表
	 */
	public function lineTrainList($userId){
        $total_page = 10;
        $page = I("get.page",1,'int');
        $start_page = ($page-1) * $total_page;
        $plan_category = I('get.plan_category');
        $training_category = I('get.training_category');
        $keyword = I("get.keyword",'','trim,htmlspecialchars');
        //接收搜索关键字解码
        $keyword = urldecode($keyword);
        iconv( 'CP1252', 'UTF-8', $keyword);
        $typeid = I("get.typeId",0,'int');

        $where['a.typeid'] = array("eq",1);
        $where['a.user_id'] = array("eq",$userId);

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

            $total_number = M("designated_personnel")->where("typeId = 1 and sign_up = 1 and project_id =".$list['id'])->count();

            foreach($course_list as $rows){

                $credit_total += $rows['credit'];

                if($rows['course_way'] == 0){
                    $online += 1;
                }else{
                    $face_total += 1;
                }

            }

            if($list['project_covers'] == ''){
                $items[$k]['project_covers'] = "/Upload/20170912/59b77f7f45136.png";
            }else{
                $items[$k]['project_covers'] = $list['project_covers'];
            }

            //培训类别，0-内部培训，1-外派培训，2-外出授课
            if($list['training_category'] == 0){

                $items[$k]['training_category'] = "内部培训";

            }elseif($list['training_category'] == 1){

                $items[$k]['training_category'] = "外派培训";

            }else{
                $items[$k]['training_category'] = "外出授课";
            }

            //计划内外，0-计划内，1-计划外
            if($list['plan_category'] == 0){

                $items[$k]['plan_category'] = "计划内";

            }else{

                $items[$k]['plan_category'] = "计划外";

            }
            //查询报名状态
            $where = array();

            $where['project_id'] = array("eq",$list['id']);
            $where['sign_up'] = array("eq",0);
            $where['typeid'] = array("eq",1);
            $where['user_id'] = array("eq",$userId);

            $state = M("designated_personnel")->field("id")->where($where)->find();

            if(!empty($state['id'])){
                $project_data['state'] = 0;//立即报名
            }else{
                $project_data['state'] = 1;//已报名
            }

            $items[$k]['credit'] = $credit_total;
            $items[$k]['online_course'] = $online;
            $items[$k]['face_course'] = $face_total;
            $items[$k]['learning_number'] = $total_number;
            $items[$k]['state'] =  $project_data['state'];

        }

       if(!empty($items)){
           return array('code'=>1000,"message"=>"获取成功",'data'=>$items);
       }else{
           return array('code'=>1030,"message"=>"无数据返回",'data'=>array());
       }
	}

	/**
	 * 详情页
	 */
	public function lineTrainDetail($userId){

        $project_id = I("get.id",0,'int');

        $typeid = I("get.typeId",0,'int');

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

        //查询报名状态
        $where['project_id'] = array("eq",$project_id);
        $where['sign_up'] = array("eq",0);
        $where['typeid'] = array("eq",1);
        $where['user_id'] = array("eq",$userId);

        $state = M("designated_personnel")->field("id")->where($where)->find();

        if(!empty($state['id'])){
            $state = 0;//立即报名
        }else{
            $state = 1;//已报名
        }

        //课程数据
        $course_data = M("project_course a")->join("LEFT JOIN __COURSE__ b ON a.course_id = b.id LEFT JOIN __LECTURER__ c ON b.lecturer = c.id")->field("a.project_id,a.course_id,a.credit,to_char(a.start_time,'yyyy-mm-dd hh24:mi:ss') as start_time,to_char(a.end_time,'yyyy-mm-dd hh24:mi:ss') as end_time,b.course_name,b.course_way,b.lecturer_name,c.name,a.location")->where("a.project_id =".$project_id)->select();

        //获取考试
        $examination_data = M("project_examination a")
            ->field("a.freq,a.examination_address,a.cacheid,a.test_names,a.manager_id,a.test_length,a.credits,a.test_id,a.project_id,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,b.test_name,c.username")
            ->join("LEFT JOIN __EXAMINATION__ b ON a.test_id = b.id LEFT JOIN __USERS__ c ON a.manager_id = c.id")
            ->where("a.project_id = ".$project_id)
            ->select();

        foreach($examination_data as $k=>$v){

            $examination_data[$k]['total_score'] = $this->getMaxScore($v['test_id'],false,$v['project_id'],$userId);

            //考试次数
            $num = M('exam_score')
                ->where(array('project_id'=>$v['project_id'],'exam_id'=>$v['test_id'],'user_id'=>$userId))
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

            $condition['user_id'] = array("eq",$userId);
            $condition['survey_id'] = array("eq",$data['survey_id']);
            $condition['project_id'] = array("eq",$data['project_id']);

            $status = M("survey_attendance")->where($condition)->getField("status");

            $survey_data[$k]['status'] = $status ? $status : 0;
        }

        //返回数据
        if($typeid == 1){

            $data = array(
                "id"=>$project_data['id'],
                "project_name"=>$project_data['project_name'],
                "project_description"=>$project_data['project_description'],
                "project_covers"=>$project_data['project_covers'],
                "start_time"=>$project_data['start_time'],
                "end_time"=>$project_data['end_time'],
                "state"=>$state,
            );

            if($project_data['plan_category'] == 0){
                $data['plan_category'] = "计划内";
            }else{
                $data['plan_category'] = "计划外";
            }

            if($project_data['training_category'] == 0){
                $data['training_category'] = "内部培训";
            }elseif($project_data['training_category'] == 1){
                $data['training_category'] = "外派培训";
            }else{
                $data['training_category'] = "外出授课";
            }

        }else if($typeid == 2){

            $data = array();

            foreach($course_data as $k=>$course){

                if($course['course_way'] == 0){
                    $data[$k]['course_way'] = '在线';
                }else{
                    $data[$k]['course_way'] = '面授';
                }

                if(strtotime($course['start_time']) > time()){
                    $data[$k]['status'] = 0;//未开始
                }else if(strtotime($course['end_time']) < time()){
                    $data[$k]['status'] = 2;//已结束
                }else{
                    $data[$k]['status'] = 1;//进行中
                }

                $data[$k]['id'] = $course['course_id'];
                $data[$k]['course_name'] = $course['course_name'];
                $data[$k]['lecturer_name'] = $course['lecturer_name'];
                $data[$k]['start_time'] = $course['start_time'];
                $data[$k]['end_time'] = $course['end_time'];
                $data[$k]['credit'] = $course['credit'];
                $data[$k]['location'] = $course['location'];
                $data[$k]['state'] =$state;
            }

        }else if($typeid == 3){

            $data = array();

            foreach($examination_data as $k=>$examination){

                $data[$k]['id'] = $k+1;
                $data[$k]['test_id'] = $examination['test_id'];
                $data[$k]['project_id'] = $examination['project_id'];
                $data[$k]['test_name'] = $examination['test_name'];
                $data[$k]['examination_address'] = $examination['examination_address'];
                $data[$k]['start_time'] = $examination['start_time'];
                $data[$k]['end_time'] = $examination['end_time'];
                $data[$k]['counter'] = $examination['freq'];
                $data[$k]['test_length'] = $examination['test_length'];
                $data[$k]['cacheid'] = $examination['cacheid'];

                if(strtotime($examination['start_time']) > time()){
                    $data[$k]['status'] = 0;//未开始
                }else if(strtotime($v['end_time']) < time()){
                    $data[$k]['status'] = 2;//已结束
                }else{
                    $data[$k]['status'] = 1;//进行中
                }
                $data[$k]['state'] = $state;

            }


        }else if($typeid == 4){

            $data = array();

            foreach($survey_data as $k=>$survey){

                $data[$k]['survey_id'] = $survey['survey_id'];
                $data[$k]['survey_name'] = $survey['survey_name'];
                $data[$k]['project_id'] = $survey['project_id'];
                $data[$k]['start_time'] = $survey['start_time'];
                $data[$k]['end_time'] = $survey['end_time'];
                $data[$k]['credit'] = $survey['credit'];

                if($survey['status'] == 0){
                    if(strtotime($survey['start_time']) < time() AND strtotime($survey['end_time']) > time()){
                        $data[$k]['status'] = 1;
                    }else if(strtotime($survey['start_time']) > time()){
                        $data[$k]['status'] = 0;
                    }else{
                        $data[$k]['status'] = 2;
                    }

                }else{
                    $data[$k]['status'] = 2;
                }

                $data[$k]['state'] = $state;

            }

        }else{
            $data = array();
        }


        if($data){
            return array('code' => 1000,'message' => '获取成功','data' => $data);
        }else{
            return array('code' => 1030,'message' => '无数据返回');
        }
	}

    /**
     * 获取考试的最高得分
     * $eid 试卷ID
     * $tid 考试ID
     * $pid 项目ID
     */
    public function getMaxScore($eid,$tid,$pid,$user_id){
        $where = array('exam_id'=>$eid,'user_id'=>$user_id);
        if($pid){
            $where['project_id'] = $pid;
        }else{
            $where['test_id'] = $tid;
        }

        $i = M('exam_score')->where($where)->order('total_score desc')->find();
        return $i['total_score'];
    }

	/**
	 * 申请报名
	 */
	public function applyRegistration($userId){
        //接收指定项目id
		$project_id = I("post.project_id",0,'int');
        if(empty($project_id)){
            return array('code' => 1030,'message' => '指定项目id参数有误');
        }
		$where['project_id'] = array("eq",$project_id);
		$where['sign_up'] = array("eq",0);
		$where['typeid'] = array("eq",1);
		$where['user_id'] = array("eq",$userId);

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

                $status = array('code'=>1000,'message'=>'报名成功');

            }else{

                $status = array('code'=>1031,'message'=>'报名人数已满');
            }

        }else{

            $status = array('code'=>1030,'message'=>'该报名已结束');
        }

		return $status;
	}
}
