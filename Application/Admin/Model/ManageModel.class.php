<?php
namespace Common\Model;

use Common\Model\BaseModel;
/**
 * 菜单操作model
 */
class ManageModel extends BaseModel{

	//初始化
	public function __construct(){}

	/**
	 * 插入试卷ID
	 */
	public function addTest($data){

		//判断试卷是否已添加

		$where['user_id'] = array("eq",$data['user_id']);
		$where['test_id'] = array("eq",$data['test_id']);
		$where['project_id'] = array("eq",$data['project_id']);

		$is_value = M("examination_attendance")->where($where)->find();

		if(empty($is_value)){

			M('examination_attendance')->data($data)->add();

		}

	}

	/**
	 * 查询符合条件用户
	 */
	public function quireUser($project_id){

		$rows = M("designated_personnel")->field("user_id")->where("project_id=".$project_id)->select();

		return $rows;
	}

	/**
	 * 修改项目考试信息
	 */
	public function updateTest(){

		$post = I("post.");

		$items = array();

		try {

			if($post['type'] == 'examination'){

				$examination_data = F('examination');

				foreach($examination_data as $k=>$data){

					if(!empty($post['cacheid'])){

						if($data['cacheid'] == $post['cacheid']){

							$items[$k]['id'] = $data['id'];
							$items[$k]['test_name'] = $post['test_name'];
							$items[$k]['test_score'] = $post['test_score'];
							$items[$k]['start_time'] = $post['start_time'];
							$items[$k]['end_time'] = $post['end_time'];
							$items[$k]['test_mode'] = $data['test_mode'];
							$items[$k]['test_length'] = $post['test_length'];
							$items[$k]['examination_address'] = $post['examination_address'];
							$items[$k]['freq'] = $post['freq'];
							if(!empty($post['principal'])){
								$items[$k]['manager_id'] = $post['principal'];
							}else{
								$items[$k]['manager_id'] = $data['manager_id'];
							}
							$items[$k]['cacheid'] = $post['cacheid'];

						}else{

							$items[$k] = $data;

						}

					}elseif($data['id'] == $post['test_id'] && !empty($data['id'])){

						$items[$k]['id'] = $data['id'];
						$items[$k]['test_name'] = $post['test_name'];
						$items[$k]['test_score'] = $post['test_score'];
						$items[$k]['start_time'] = $post['start_time'];
						$items[$k]['end_time'] = $post['end_time'];
						$items[$k]['test_mode'] = $data['test_mode'];
						$items[$k]['test_length'] = $post['test_length'];
						$items[$k]['examination_address'] = $post['examination_address'];
						$items[$k]['freq'] = $post['freq'];
						if(!empty($post['principal'])){
							$items[$k]['manager_id'] = $post['principal'];
						}else{
							$items[$k]['manager_id'] = $data['manager_id'];
						}

					}else{
						$items[$k] = $data;
					}

				}

				//清除当前考试缓存
				F('examination',null);

				//重新写入缓存
				F('examination',$items);

			}elseif($post['type'] == 'course'){

				$course_data = F('course');

				foreach($course_data as $k=>$data){

					if($data['id'] == $post['test_id']){

						$items[$k]['id'] = $data['id'];
						$items[$k]['course_name'] = $data['course_name'];
						$items[$k]['course_way'] = $data['course_way'];
						$items[$k]['lecturer'] = $data['lecturer'];
						$items[$k]['lecturer_name'] = $data['lecturer_name'];
						$items[$k]['name'] = $data['name'];
						$items[$k]['start_time'] = $post['start_time'];
						$items[$k]['end_time'] = $post['end_time'];
						$items[$k]['credit'] = $post['credit'];
						$items[$k]['location'] = $post['location'];

						if($post['attendance'] == 'true'){
							$items[$k]['is_attachment'] = 1;
						}else{
							$items[$k]['is_attachment'] = 0;
						}
						if(!empty($post['principal'])){
							$items[$k]['manager_id'] = $post['principal'];
						}else{
							$items[$k]['manager_id'] = $data['manager_id'];
						}

					}else{

						$items[$k] = $data;

					}

				}

				//清除当前课程缓存
				F('course',null);

				//重新写入缓存
				F('course',$items);

			}else{

				$research_data = F('research');

				foreach($research_data as $k=>$data){

					if($data['id'] == $post['test_id']){
						$items[$k]['id'] = $data['id'];
						$items[$k]['survey_name'] = $data['survey_name'];
						$items[$k]['start_time'] = $post['start_time'];
						$items[$k]['end_time'] = $post['end_time'];
						$items[$k]['survey_score'] = $post['survey_score'];
						if(!empty($post['principal'])){
							$items[$k]['manager_id'] = $post['principal'];
						}else{
							$items[$k]['manager_id'] = $data['manager_id'];
						}
					}else{
						$items[$k] = $data;
					}

				}

				//清除当前课程缓存
				F('research',null);

				//重新写入缓存
				F('research',$items);

			}

			$results = true;

		} catch ( Exception $e ) {

			$results = false;

		}

		return $results;
	}

	/**
	 * 删除项目信息
	 */
	public function DeleteTest(){

		$post = I("post.");

		$items = array();

		try {

			if($post['type'] == 'examination'){

				$examination_data = F('examination');

				foreach($examination_data as $k=>$data){
					if($data['id'] != $post['test_id']){
						$items[$k] = $data;
					}
				}

				//清除当前课程缓存
				F('examination',null);

				//重新写入缓存
				F('examination',$items);

			}elseif($post['type'] == 'course'){

				$course_data = F('course');

				foreach($course_data as $k=>$data){
					if($data['id'] != $post['test_id']){
						$items[$k] = $data;
					}
				}

				//清除当前课程缓存
				F('course',null);

				//重新写入缓存
				F('course',$items);

			}else{

				$research_data = F('research');

				foreach($research_data as $k=>$data){
					if($data['id'] != $post['test_id']){
						$items[$k] = $data;
					}
				}

				//清除当前课程缓存
				F('research',null);

				//重新写入缓存
				F('research',$items);

			}

			$results = true;

		} catch ( Exception $e ) {

			$results = false;

		}

		return $results;

	}

	/**
	 * 合并新添加重复试卷
	 */
	public function mergeTest($examination,$type){

		try {
			if($type == 2){

				//获取缓存试卷数据
				$examination_data = F('examination');

			}elseif($type == 1){

				//获取缓存课程数据
				$examination_data = F('course');

			}else{

				//获取调研缓存数据
				$examination_data = F('research');
			}


			//过滤新添加的试卷
			if(!empty($examination_data)){

				foreach($examination as $data){
					//新数据添加
					if($this->is_merge($examination_data,$data['id'])){
						$new_examination[] = $data;
					}
				}

				//合并数据
				if(empty($new_examination)){
					$items = $examination_data;
				}else{
					$items = array_merge($examination_data,$new_examination);
				}

			}else{

				$items =  $examination;
			}

			if($type == 2){

				//清除当前考试缓存
				F('examination',null);

				//重新写入缓存
				F('examination',$items);

			}elseif($type == 1){

				//清除当前课程缓存
				F('course',null);

				//重新写入缓存
				F('course',$items);

			}else{

				//清除调研缓存数据
				F('research',null);

				//重新写入缓存
				F('research',$items);
			}

			$results = true;

		}catch ( Exception $e ) {

			$results = false;

		}

		return $results;

	}

	/**
	 * 判断重复数据
	 */
	public function is_merge($examination_data,$id){

		$examination_id = array();

		foreach($examination_data as $list){
			$examination_id[] = $list['id'];
		}

		if($id == 0){
			return true;
		}else{
			if(in_array($id,$examination_id)){
				return false;
			}else{
				return true;
			}
		}

	}

	/**
	 * 项目 - 添加课程
	 */
	public function getCourse($arr_id){

		$conditions["a.id"]=array("in",$arr_id);
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$list = M("course a")
			->field("a.id,a.course_name,a.course_way,a.lecturer,a.lecturer_name,a.credit,b.name,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")
			->join("LEFT JOIN __LECTURER__ b ON a.lecturer = b.id")
			->order('a.id desc')
			->where($conditions)
			->select();
		}else{
			$list = M("course a")
			->field("a.id,a.course_name,a.course_way,a.lecturer,a.lecturer_name,a.start_time,a.end_time,a.credit,b.name")
			->join("LEFT JOIN __LECTURER__ b ON a.lecturer = b.id")
			->order('a.id desc')
			->where($conditions)
			->select();
		}
		
		return $list;
	}

	/**
	 * 项目 - 添加调研
	 */
	public function getResearch($arr_id){

		$conditions["a.id"]=array("in",$arr_id);

		$list = M("survey a")
			->field("a.id,a.survey_name,a.survey_score,a.principal")
			->order('a.id desc')
			->where($conditions)
			->select();

		return $list;

	}

	/**
	 * 提交 - 生成培训项目
	 */
	public function form_save(){

		//提交类型
		$typeid = I("post.typeid");

		//获取项目缓存
		$project_data = F('admin_project');

		//获取课程缓存
		$course_data = F('course');

		//获取考试缓存
		$examination_data = F('examination');

		//获取调研缓存
		$research_data = F('research');

		$serialize_tissue = $project_data['tissue_id'] ? serialize($project_data['tissue_id']) : '';


		$orderno_data  =  D('Trigger')->orderNumber(1);
        $orderno = $orderno_data['no']; 
		
		if($project_data['is_public'] == 1){
			if(empty($project_data['population'])){
				$project_data['population'] = 99999999;
			}
		}

		//插入项目表
		$project_items = array(
			"project_name"=>$project_data['project_name'],
			"training_category"=>$project_data['training_category'],
			"plan_category"=>$project_data['plan_category'],
			"class_name"=>$project_data['class_name'],
			"project_description"=>$project_data['project_description'],
			"project_covers"=>$project_data['filename'],
			"project_budget"=>$project_data['project_budget'],
			"is_public"=>$project_data['is_public'] ? $project_data['is_public'] : 0,
			"population"=>$project_data['population'],
			"type"=>$typeid,
			"user_id"=>$_SESSION['user']['id'],
			"tissue_id"=>$serialize_tissue,
			"orderno"=>$orderno,
			'auth_user_id'=>$_SESSION['user']['id']
		);

		if($orderno_data['status'] == 0 && $project_items['type'] == 2){
			$project_items['type'] = 0;	 //判断是否需要审核
		}

		if(empty($project_data['project_id'])){

			//新增
			try {
				
				$DB = M();

				$DB->startTrans();
				
				if(strtolower(C('DB_TYPE')) == 'oracle'){
					$project_items['id'] = getNextId('admin_project');
					$project_items['add_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");

					if(empty($project_data['start_time'])){
						$project_items['start_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
					}else{
						$project_items['start_time'] = array('exp',"to_date('".$project_data['start_time']."','yy-mm-dd hh24:mi:ss')");
					}

					if(empty($project_data['end_time'])){
						$project_items['end_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
					}else{
						$project_items['end_time'] = array('exp',"to_date('".$project_data['end_time']."','yy-mm-dd hh24:mi:ss')");
					}

				}else{
					$project_items['add_time'] = date("Y-m-d H:i:s",time());
					$project_items['start_time'] = $project_data['start_time'];
					$project_items['end_time'] = $project_data['end_time'];
				}

				$project_id = $DB->table('__ADMIN_PROJECT__')->data($project_items)->add();

				//培训预算
				$box = $project_data['box'];
				$amount = $project_data['amount'];
				foreach($box as $k=>$v){
					$iData = array('project_id'=>$project_id,'option_name'=>trim($v),'amount'=>$amount[$k]);
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$iData['id'] = getNextId('project_budget');
					}
					M('project_budget')->add($iData);
				}
				
				//插入指定用户表
				$personnel_data = array();
				
				foreach($project_data['user_id'] as $k=>$user_id){
					/*$personnel_data[$k]['user_id'] = $user_id;
					$personnel_data[$k]['project_id'] = $project_id;*/
					$personnel_data['user_id'] = $user_id;
					$personnel_data['project_id'] = $project_id;
					$personnel_data['sign_up'] = 1;
					$personnel_data['typeid'] = 0;
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$personnel_data['id'] = getNextId('designated_personnel');
					}
					$DB->table('__DESIGNATED_PERSONNEL__')->add($personnel_data);
				}
				foreach($project_data['tissue_id'] as $k=>$tissue_id){
					$items = M("tissue_group_access")
							->field("user_id")
							->where("tissue_id=".$tissue_id)
							->select();
					$i = 0;
					foreach($items as $item){
						if(!in_array($item['user_id'],$project_data['user_id'])){
							/*$tissue_list[$i]['user_id'] = $item['user_id'];
							$tissue_list[$i]['project_id'] = $project_id;
							$i++;*/
							
							$tissue_list['user_id'] = $item['user_id'];
							$tissue_list['project_id'] = $project_id;
							$tissue_list['sign_up'] = 0;
							$tissue_list['typeid'] = 1;

							if(strtolower(C('DB_TYPE')) == 'oracle'){
								$tissue_list['id'] = getNextId('designated_personnel');
							}
							$DB->table('__DESIGNATED_PERSONNEL__')->add($tissue_list);
						}
					}
					/*if(!empty($tissue_list)){
						$personnel_data = array_merge($personnel_data,$tissue_list);
					}*/
				}
				/*$DB->table('__DESIGNATED_PERSONNEL__')->addAll($personnel_data);*/

				//插入课程表
				$course_items = array();


				foreach($course_data as $k=>$data){
					$course_items['project_id'] = $project_id;
					$course_items['course_id'] = $data['id'];
					$course_items['credit'] = $data['credit'];
					$course_items['location'] = $data['location'];
					$course_items['manager_id'] = $data['manager_id'];
					$course_items['is_attachment'] = $data['is_attachment'];
					$course_items['auth_user_id'] = session('user.id');
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$course_items['id'] = getNextId('project_course');
						$course_items['start_time'] = array('exp',"to_date('".$data['start_time']."','yy-mm-dd hh24:mi:ss')");
						$course_items['end_time'] = array('exp',"to_date('".$data['end_time']."','yy-mm-dd hh24:mi:ss')");
					}else{
						$course_items['start_time'] = $data['start_time'];
						$course_items['end_time'] = $data['end_time'];
					}
					
					if(!empty($course_items)){
						$DB->table('__PROJECT_COURSE__')->add($course_items);
					}
				}
				
				//插入参加考试表
				$examination_items = array();

				foreach($examination_data as $k=>$data){
			
					$examination_items['project_id'] = $project_id;
					$examination_items['test_id'] = $data['id'];
					$examination_items['credits'] = $data['test_score'];
					$examination_items['manager_id'] = $data['manager_id'];
					$examination_items['test_names'] = $data['test_name'];
					$examination_items['cacheid'] = $data['cacheid'];
					$examination_items['test_length'] = $data['test_length'];
					$examination_items['auth_user_id'] = session('user.id');
					$examination_items['examination_address'] = $data['examination_address'];
					$examination_items['freq'] = $data['freq'];
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$examination_items['id'] = getNextId('project_examination');
						$examination_items['start_time'] = array('exp',"to_date('".$data['start_time']."','yy-mm-dd hh24:mi:ss')");
						$examination_items['end_time'] = array('exp',"to_date('".$data['end_time']."','yy-mm-dd hh24:mi:ss')");
					}else{
						$examination_items['start_time'] = $data['start_time'];
						$examination_items['end_time'] = $data['end_time'];
					}
					
					if(!empty($examination_items)){
						$DB->table('__PROJECT_EXAMINATION__')->add($examination_items);
					}
				}

				//插入调研

				$research_items = array();

				foreach($research_data as $k=>$data){
					$research_items['project_id'] =  $project_id;
					$research_items['survey_id'] =  $data['id'];
					$research_items['manager_id'] =  $data['manager_id'];
					$research_items['credit'] =  $data['survey_score'];
					$research_items['auth_user_id'] = session('user.id');
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$research_items['id'] = getNextId('project_survey');
						$research_items['start_time'] = array('exp',"to_date('".$data['start_time']."','yy-mm-dd hh24:mi:ss')");
						$research_items['end_time'] = array('exp',"to_date('".$data['end_time']."','yy-mm-dd hh24:mi:ss')");
					}else{
						$research_items['start_time'] =  $data['start_time'];
						$research_items['end_time'] =  $data['end_time'];
					}

					if(!empty($research_items)){
						$DB->table('__PROJECT_SURVEY__')->add($research_items);
					}
				}

				if(!empty($project_id)){

					$DB->commit();

					write_login_log(8,2,$project_data['project_name']);

					//清除当前项目缓存
					F('admin_project',NULL);
					F('examination',NULL);
					F('course',NULL);
					F('research',NULL);

					$results = true;
				}

			} catch ( Exception $e ) {

				$DB->rollback();

				$results = false;
			}


		}else{
			
			//项目ID
			$project_id = $project_data['project_id'];

			$admin_project = M("admin_project")
				->field("type")
				->where("id=".$project_id)
				->find();
            
			// 已拒绝状态
			if($admin_project['type'] == 3){
				D('Trigger')->projectResubmit($project_id,1);
			}
			
			//编辑
			try {

				$DB = M();

				$DB->startTrans();

				unset($project_items['auth_user_id']);
				unset($project_items['user_id']);

				$DB->table('__ADMIN_PROJECT__')->where("id=".$project_id)->save($project_items);

				//删除项目关联表数据
				M("designated_personnel")->where('project_id='.$project_id)->delete();
				M("project_course")->where('project_id='.$project_id)->delete();
				M("project_examination")->where('project_id='.$project_id)->delete();
				M("project_survey")->where('project_id='.$project_id)->delete();
				
				//培训预算
				M('project_budget')->where(array('project_id'=>$project_id))->delete();
				$box = $project_data['box'];
				$amount = $project_data['amount'];;
				foreach($box as $k=>$v){
					$iData = array('project_id'=>$project_id,'option_name'=>trim($v),'amount'=>$amount[$k]);
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$iData['id'] = getNextId('project_budget');
					}

					
					M('project_budget')->add($iData);
				}
				
				
				//插入指定用户表
				$personnel_data = array();

				foreach($project_data['user_id'] as $k=>$user_id){
					/*$personnel_data[$k]['user_id'] = $user_id;
					$personnel_data[$k]['project_id'] = $project_id;*/
					$personnel_data['user_id'] = $user_id;
					$personnel_data['project_id'] = $project_id;
					$personnel_data['sign_up'] = 1;
					$personnel_data['typeid'] = 0;
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$personnel_data['id'] = getNextId('designated_personnel');
					}
					$DB->table('__DESIGNATED_PERSONNEL__')->add($personnel_data);
				}
				
				if($project_data['tissue_id']){

					foreach($project_data['tissue_id'] as $k=>$tissue_id){
						$items = M("tissue_group_access")
							->field("user_id")
							->where("tissue_id=".$tissue_id)
							->select();

						foreach($items as $item){
							if(!in_array($item['user_id'],$project_data['user_id'])){
								/*$tissue_list[$i]['user_id'] = $item['user_id'];
                                $tissue_list[$i]['project_id'] = $project_id;
                                $i++;*/

								$tissue_list['user_id'] = $item['user_id'];
								$tissue_list['project_id'] = $project_id;
								$tissue_list['sign_up'] = 0;
								$tissue_list['typeid'] = 1;

								if(strtolower(C('DB_TYPE')) == 'oracle'){
									$tissue_list['id'] = getNextId('designated_personnel');
								}
								$DB->table('__DESIGNATED_PERSONNEL__')->add($tissue_list);
							}
						}
						/*if(!empty($tissue_list)){
                            $personnel_data = array_merge($personnel_data,$tissue_list);
                        }*/
					}
				}
				

				//插入课程表
				$course_items = array();

				foreach($course_data as $k=>$data){
					$course_items['project_id'] = $project_id;
					$course_items['course_id'] = $data['id'];
					$course_items['credit'] = $data['credit'];
					$course_items['location'] = $data['location'];
					$course_items['manager_id'] = $data['manager_id'];
					$course_items['is_attachment'] = $data['is_attachment'] ? $data['is_attachment'] : 0;
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$course_items['id'] = getNextId('project_course');
						$course_items['start_time'] = array('exp',"to_date('".$data['start_time']."','yy-mm-dd hh24:mi:ss')");
						$course_items['end_time'] = array('exp',"to_date('".$data['end_time']."','yy-mm-dd hh24:mi:ss')");
					}else{
						$course_items['start_time'] = $data['start_time'];
						$course_items['end_time'] = $data['end_time'];
					}
					
					$DB->table('__PROJECT_COURSE__')->add($course_items);
				}

				

				//插入参加考试表
				$examination_items = array();

				foreach($examination_data as $k=>$data){
					$examination_items['project_id'] = $project_id;
					$examination_items['test_id'] = $data['id'];
					$examination_items['credits'] = $data['test_score'];
					$examination_items['test_length'] = $data['test_length'];
					$examination_items['manager_id'] = $data['manager_id'];
					$examination_items['test_names'] = $data['test_name'];
					$examination_items['cacheid'] = $data['cacheid'];
					$examination_items['examination_address'] = $data['examination_address'];
					$examination_items['freq'] = $data['freq'];
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$examination_items['id'] = getNextId('project_examination');
						$examination_items['start_time'] = array('exp',"to_date('".$data['start_time']."','yy-mm-dd hh24:mi:ss')");
						$examination_items['end_time'] = array('exp',"to_date('".$data['end_time']."','yy-mm-dd hh24:mi:ss')");
					}else{
						$examination_items['start_time'] = $data['start_time'];
						$examination_items['end_time'] = $data['end_time'];
					}
					$DB->table('__PROJECT_EXAMINATION__')->add($examination_items);
				}


				//插入调研

				$research_items = array();

				foreach($research_data as $k=>$data){
					$research_items['project_id'] =  $project_id;
					$research_items['survey_id'] =  $data['id'];
					$research_items['manager_id'] =  $data['manager_id'];
					$research_items['credit'] =  $data['survey_score'];
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$research_items['id'] = getNextId('project_survey');
						$research_items['start_time'] = array('exp',"to_date('".$data['start_time']."','yy-mm-dd hh24:mi:ss')");
						$research_items['end_time'] = array('exp',"to_date('".$data['end_time']."','yy-mm-dd hh24:mi:ss')");
					}else{
						$research_items['start_time'] =  $data['start_time'];
						$research_items['end_time'] =  $data['end_time'];
					}
					$DB->table('__PROJECT_SURVEY__')->add($research_items);
				}

				write_login_log(8,3,$project_items['project_name']);


				$DB->commit();

				//清除当前项目缓存
				F('admin_project',NULL);
				F('examination',NULL);
				F('course',NULL);
				F('research',NULL);

				$results = true;


			} catch ( Exception $e ) {

				$DB->rollback();

				$results = false;
			}


		}

		return $results;
	}

	/**
	 * 获取项目编辑内容
	 */
	public function edit_item($project_id){
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			//课程
			$project_course = M("project_course a")
				->field("b.id,b.course_name,b.course_way,b.lecturer_name,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,a.credit,a.location,a.manager_id,a.is_attachment,c.name")
				->join("LEFT JOIN __COURSE__ b ON a.course_id = b.id LEFT JOIN __LECTURER__ c ON b.lecturer = c.id")
				->order('a.id desc')
				->where("a.project_id = ".$project_id)
				->select();
	
	
			//写入课程缓存
			F('course',$project_course);
	
			//考试
			$project_examination = M("project_examination a")
				->field("a.freq,a.test_names as test_name,a.credits as test_score,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,a.manager_id,a.test_length,a.cacheid,b.id,b.principal")
				->join("LEFT JOIN __EXAMINATION__ b ON a.test_id = b.id")
				->order('a.test_id desc')
				->where("a.project_id = ".$project_id)
				->select();
	
			//写入考试缓存
			F('examination',$project_examination);
	
			//调研
			$project_survey = M("project_survey a")
				->field("b.survey_name,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,a.credit as survey_score,a.manager_id,a.survey_id as id")
				->join("LEFT JOIN __SURVEY__ b ON a.survey_id = b.id")
				->order('a.survey_id desc')
				->where("a.project_id = ".$project_id)
				->select();
	
			//写入调研缓存
			F('research',$project_survey);
		}else{
			//课程
			$project_course = M("project_course a")
				->field("b.id,b.course_name,b.course_way,b.lecturer_name,a.start_time,a.end_time,a.credit,a.location,a.manager_id,a.is_attachment,c.name")
				->join("LEFT JOIN __COURSE__ b ON a.course_id = b.id LEFT JOIN __LECTURER__ c ON b.lecturer = c.id")
				->order('a.id desc')
				->where("a.project_id = ".$project_id)
				->select();
	
	
			//写入课程缓存
			F('course',$project_course);
	
			//考试
			$project_examination = M("project_examination a")
				->field("a.freq,a.test_names as test_name,a.credits as test_score,a.start_time,a.end_time,a.manager_id,a.test_length,a.cacheid,b.id,b.principal")
				->join("LEFT JOIN __EXAMINATION__ b ON a.test_id = b.id")
				->order('a.test_id desc')
				->where("a.project_id = ".$project_id)
				->select();
	
			//写入考试缓存
			F('examination',$project_examination);
	
			//调研
			$project_survey = M("project_survey a")
				->field("b.survey_name,a.start_time,a.end_time,a.credit as survey_score,a.manager_id,a.survey_id as id")
				->join("LEFT JOIN __SURVEY__ b ON a.survey_id = b.id")
				->order('a.survey_id desc')
				->where("a.project_id = ".$project_id)
				->select();
	
			//写入调研缓存
			F('research',$project_survey);
		}
	}

	/**
	 * 获取项目详细
	 */
	public function detail(){

		$id = I("get.id");

		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			//获取项目详情
			$admin_project = M("admin_project")
				->field("id,user_id,project_name,class_name,project_description,project_covers,project_budget,is_public,population,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,type,tissue_id,plan_category,training_category")
				->where("id=".$id)
				->find();
	
			//获取课程
			$project_course = M("project_course a")
				->field("a.course_id,a.course_names,a.credit,a.location,a.is_attachment,a.manager_id,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,b.course_name,b.course_way,b.lecturer_name,c.name,d.username")
				->join("LEFT JOIN __COURSE__ b ON a.course_id = b.id LEFT JOIN __LECTURER__ c ON b.lecturer = c.id")
				->join("LEFT JOIN __USERS__ d ON a.manager_id = d.id")
				->where("a.project_id = ".$id)
				->select();
			foreach ($project_course as $key=>$pcourse){
				//判断是否考勤
				$whereatt["a.course_id"] = $pcourse["course_id"];
				$whereatt["b.project_id"] = $id;
				$attendance_course = M("attendance_course a")->field("a.attendance_project_id")
					->join("join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id")->where($whereatt)->find();
				if($attendance_course){
					$project_course[$key]["is_attachment"] = 1;
				}else{
					$project_course[$key]["is_attachment"] = 0;
				}
			}
			
			//获取考试
			$project_examination = M("project_examination a")
				->field("a.freq,a.examination_address,a.cacheid,a.test_names,a.manager_id,a.test_length,a.credits,a.test_id,a.project_id,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,c.username")
				->join("LEFT JOIN __EXAMINATION__ b ON a.test_id = b.id LEFT JOIN __USERS__ c ON a.manager_id = c.id")
				->where("a.project_id = ".$id)
				->select();

			//获取调研
			$project_survey = M("project_survey a")
				->field("a.project_id,a.survey_names,a.credit,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,b.survey_name,c.username")
				->join("LEFT JOIN __SURVEY__ b ON a.survey_id = b.id LEFT JOIN __USERS__ c ON a.manager_id = c.id")
				->where("a.project_id = ".$id)
				->select();
		}else{
			//获取项目详情
			$admin_project = M("admin_project")
				->field("*")
				->where("id=".$id)
				->find();
	
			//获取课程
			$project_course = M("project_course a")
				->field("a.*,b.course_name,b.course_way,b.lecturer_name,c.name,d.username")
				->join("LEFT JOIN __COURSE__ b ON a.course_id = b.id LEFT JOIN __LECTURER__ c ON b.lecturer = c.id")
				->join("LEFT JOIN __USERS__ d ON a.manager_id = d.id")
				->where("a.project_id = ".$id)
				->select();
	
			//获取考试
			$project_examination = M("project_examination a")
				->field("a.*,c.username")
				->join("LEFT JOIN __EXAMINATION__ b ON a.test_id = b.id LEFT JOIN __USERS__ c ON a.manager_id = c.id")
				->where("a.project_id = ".$id)
				->select();
	
			//获取调研
			$project_survey = M("project_survey a")
				->field("a.*,b.survey_name,c.username")
				->join("LEFT JOIN __SURVEY__ b ON a.survey_id = b.id LEFT JOIN __USERS__ c ON a.manager_id = c.id")
				->where("a.project_id = ".$id)
				->select();
		}
		//提定人员
		$dpersonnels = M("designated_personnel a")
			->field("b.id,b.username")
			->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
			->where("a.typeid = 0 and a.project_id = ".$id)
			->select();
		
		//项目预算
		$budget = M('project_budget')->where(array('project_id'=>$id))->select();
		
		$user_id = array();
		$tissue_id = array();

		foreach($dpersonnels as $data){
			$user[] = $data['username'];
			$user_id[] = $data['id'];
		}

		$str_designee = implode(",",$user);

		if(strtotime($admin_project['end_time']) < time()){
			$is_end = 1;
		}else{
			$is_end = 0;
		}

		//指定部门
		if(!empty($admin_project['tissue_id'])){

			$tissue_id = unserialize($admin_project['tissue_id']);

			if(!empty($tissue_id)){

				$where['id'] = array("in",$tissue_id);

				$department = M('tissue_rule')->field('id,name')->where($where)->select();

			}else{
				$department = array();
			}

		}else{
			$department = array();
		}

		$admin_project['user_id'] = $user_id;
		$admin_project['tissue_id'] = $tissue_id;

		foreach($budget as $v){
			$box[] = $v['option_name'];
			$amount[] = $v['amount'];
		}

		$admin_project['box'] = $box;
		$admin_project['amount'] = $amount;

		//判断读取数据为同级
		$is_isolation = D('IsolationData')->isEquality($admin_project['auth_user_id']);

		$items = array(
			"project"=>$admin_project,
			"course"=>$project_course,
			"examination"=>$project_examination,
			"survey"=>$project_survey,
			"dpersonnels"=>$str_designee,
			"designee"=>$dpersonnels,
			"department"=>$department,
			"is_end"=>$is_end,
			'budget'=>$budget,	//项目预算
			'is_isolation'=>$is_isolation
		);

		return $items;
	}

	/**
	 * 获取编辑指定人员
	 */
	public function creatc_edit($user_id,$type){

		if($type == 1){

			$where['id'] = array("in",implode(",",$user_id));

			$data = M("users")
				->field("id,username")
				->where($where)
				->select();

		}else{

			$where['id'] = array("in",implode(",",$user_id));

			$data = M("tissue_rule")
				->field("id,name")
				->where($where)
				->select();
		}

		return $data;
	}

	/**
	 * 删除创建项目
	 */
	public function delItems(){

		$project_id = I("get.id");

		try {

			$DB = M();

			$DB->startTrans();

			$items = M("admin_project")->field("project_name")->where("id=".$project_id)->find();

			//删除项目关联表数据
			$DB->table('__ADMIN_PROJECT__')->where("id=".$project_id)->delete();
			$DB->table('__DESIGNATED_PERSONNEL__')->where("project_id=".$project_id)->delete();
			$DB->table('__PROJECT_COURSE__')->where("project_id=".$project_id)->delete();
			$DB->table('__PROJECT_EXAMINATION__')->where("project_id=".$project_id)->delete();
			$DB->table('__PROJECT_SURVEY__')->where("project_id=".$project_id)->delete();

			$DB->commit();

			//清除当前项目缓存
			F('admin_project',NULL);
			F('examination',NULL);
			F('course',NULL);
			F('research',NULL);

			write_login_log(8,4,$items['project_name']);

			$results = true;

		} catch ( Exception $e ) {

			$DB->rollback();

			$results = false;
		}

		return $results;

	}

	/**
	 * 获取培训项目列表
	 */
	public function getProjectlist($total_page){

		$start_page = I("get.p",0,'int');
		$keyword=I("get.keyword")?I("get.keyword"):"";
		$typeid = I("get.typeid",0,'int');
		$training_category = I("get.training_category");
		$plan_category = I("get.plan_category");

		//获取已经过审核的项目
		//$where['a.uid'] = array("eq",$_SESSION['user']['id']);
		$where['a.type'] = array("eq",$typeid);

		if(!empty($keyword)){
			$where['_string']="(a.project_name like '%".$keyword."%')  OR (a.class_name like '%".$keyword."%') OR (a.project_description like '%".$keyword."%') OR (b.username like '%".$keyword."%')";
		}

		if(($plan_category >=0) and ($plan_category != '')){
			$where['a.plan_category'] = array("eq",$plan_category);
		}

		if(($training_category >=0) and ($training_category != '')){
			$where['a.training_category'] = array("eq",$training_category);
		}

		$DB_PREFIX = strtolower(C('DB_PREFIX').'project_course');

		$DB_COURSE = strtolower(C('DB_PREFIX').'course');

		//隔离数据过滤
		$specifiedUser = D('IsolationData')->specifiedUser();

		$where['a.auth_user_id'] = array("in",$specifiedUser);

		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$results = M("admin_project a")
			->join("LEFT JOIN __USERS__ b ON a.user_id = b.id LEFT JOIN __TISSUE_GROUP_ACCESS__ c ON a.user_id = c.user_id LEFT JOIN __TISSUE_RULE__ d ON c.tissue_id = d.id")
			->field("a.project_name,a.id,a.user_id,a.class_name,a.project_description,a.project_covers,a.project_budget,a.is_public,a.population,a.type,a.tissue_id,a.training_category,a.auth_user_id,a.objection,a.orderno,a.plan_category,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,b.username,d.name as tissuename,(select SUM(f.course_time) from $DB_PREFIX e LEFT JOIN $DB_COURSE f ON e.course_id = f.id where a.id = e.project_id) as test_length")
			->where($where)
			->order('a.add_time desc')
			->page($start_page,$total_page)
			->select();

			$count = M("admin_project a")
				->join("LEFT JOIN __USERS__ b ON a.user_id = b.id LEFT JOIN __TISSUE_GROUP_ACCESS__ c ON a.user_id = c.user_id LEFT JOIN __TISSUE_RULE__ d ON c.tissue_id = d.id")->where($where)->count();

		}else{
			$results = M("admin_project a")
			->join("LEFT JOIN __USERS__ b ON a.user_id = b.id LEFT JOIN __TISSUE_GROUP_ACCESS__ c ON a.user_id = c.user_id LEFT JOIN __TISSUE_RULE__ d ON c.tissue_id = d.id")
			->field("a.*,b.username,d.name as tissuename,(select SUM(f.course_time) from $DB_PREFIX e LEFT JOIN $DB_COURSE f ON e.course_id = f.id where a.id = e.project_id) as test_length")
			->where($where)
			->order('a.add_time desc')
			->page($start_page,$total_page)
			->select();

			$count = M("admin_project a")->where($where)->count();
		}

		//隔离数据过滤
		$results = D('IsolationData')->isolationData($results);


		//输出分页
		$show=$this->pageClass($count,$total_page);

		$data = array(
			'typeid'=>$typeid,
			'page' => $show,
			'list' => $results,
			'keyword'=>$keyword,
			'plan_category'=>$plan_category,
			'training_category'=>$training_category
		);

		return $data;
	}

	/**
	 * 修改项目过期状态到已完成
	 */
	function upstate(){
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$data = M('admin_project')->field("id,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")->select();
			foreach($data as $k=>$v){
				if(strtotime($v['end_time']) < time()){
					M("admin_project")->where(array('id'=>$v['id']))->setField('type','4');
				}
			}
		}else{
			$timedate = date("Y-m-d H:i:s",time());
			M("admin_project")->where("unix_timestamp(end_time) < unix_timestamp('{$timedate}')")->setField('type','4');
		}
	}

	/**
	 * 查看课程
	 */
	public function project_course($project_id,$course_id){

		$where['pid'] = array("eq",$project_id);
		$where['course_id'] = array("eq",$course_id);

		$attendance = array("tobe"=>0,"late"=>0,"notto"=>0);

		$results = M("attendance")->field("status")->where($where)->select();

		foreach($results as $item){
			if($item['status'] == 2){
				$attendance['late']++;
			}elseif($item['status'] == 3){
				$attendance['notto']++;
			}
			$attendance['tobe']++;
		}

		return $attendance;

	}

	/**
	 * 查看考试情况
	 */
	public function project_examination($project_id,$test_id){

		//统计人数
		$items = M("designated_personnel")->where("project_id=".$project_id)->select();

		$attendance = array("tobe"=>count($items),"notest"=>0);

		foreach($items as $item){

			$where['user_id'] = array("eq",$item['user_id']);
			$where['test_id'] = array("eq",$test_id);
			$where['project_id'] = array("eq",$project_id);

			$results = M("examination_attendance")->field("status")->where($where)->find();

			if(empty($results['status'])){
				$attendance['notest']++;
			}

		}

		return $attendance;
	}

	/**
	 * 统计项目考卷平均分和合格率
	 */
	public function getQualifiedrate($project_id,$test_id,$pass_line = 0){

		$items = M("designated_personnel")->where("project_id=".$project_id)->select();

		$pass_num = 0;

		$total_score = 0;

		$pass_line = $pass_line ? $pass_line : 0;

		foreach($items as $item){

			$where['user_id'] = array("eq",$item['user_id']);
			$where['exam_id'] = array("eq",$test_id);
			$where['project_id'] = array("eq",$project_id);

			$results = M("exam_score")->field("total_score")->where($where)->find();

			if(!empty($results['total_score'])){

				if($pass_line <= $results['total_score']){
					$pass_num++;
				}

				$total_score += $results['total_score'];
			}

		}

		//合格率
		if($pass_num > 0){
			$pass_rate = ($pass_num/count($items)) * 100;
		}else{
			$pass_rate = 0;
		}

		//平均分
		if($total_score > 0){

			$average = $total_score / count($items);

		}else{

			$average = 0;

		}


		$data = array(
			'pass_rate'=>round($pass_rate,1),
			'average'=>round($average,1)
		);

		return $data;
	}



	/**
	 * 查看问卷情况
	 */
	public function project_survey($project_id,$survey_id){

		//统计人数
		$items = M("designated_personnel")->where("project_id=".$project_id)->select();

		$attendance = array("tobe"=>count($items),"notest"=>0);

		foreach($items as $item){

			$where['user_id'] = array("eq",$item['user_id']);
			$where['survey_id'] = array("eq",$survey_id);
			$where['project_id'] = array("eq",$project_id);

			$results = M("survey_attendance")->field("status")->where($where)->find();

			if(empty($results['status'])){
				$attendance['notest']++;
			}

		}

		return $attendance;


	}

	/**
	 * 查看项目总结
	 */
	public function project_summary(){

		$project_id = I('get.id');

		$project_summary = M("project_summary")->where('project_id='.$project_id)->find();

		$project_budget = M("project_budget")->where('project_id='.$project_id)->select();

		$items = array(
			'project_summary'=>$project_summary,
			'project_budget'=>$project_budget
		);

		return $items;
	}


	/**
	 * 项目总结编辑
	 */
	public function viewedit(){

		$data = I("post.");

		$items = array(
			'total_expenses'=>$data['total_expenses'],
			'summary'=>$data['summary'],
			'enclosure'=>$data['enclosure'],
			'project_id'=>$data['project_id']
		);


		if(IS_POST){

			if(!empty($items['enclosure'])){
				$items['enclosure'] = serialize($items['enclosure']);
			}else{
				unset($items['enclosure']);
			}

			$project_summary = M("project_summary")->where('project_id='.$data['project_id'])->find();

			if(empty($project_summary)){

				if(strtolower(C('DB_TYPE')) == 'oracle'){
					$items['id'] = getNextId('project_summary');
				}

				M('project_summary')->data($items)->add();

			}else{

				M('project_summary')->where('project_id='.$data['project_id'])->save($items);

			}

			foreach($data['actual_amount'] as $k=>$amount){
				M("project_budget")->where('id='.$k)->setField('actual_amount',$amount);
			}

			$result = true;

		}else{

			$result = false;

		}

		return $result;

	}


	/**
	 * 查看项目总结 - 上传文档
	 */
	public function upload(){

		$setting=C('UPLOAD_SITEIMG_QINIU');

		$auth = new \Think\Upload($setting,'Qiniu',$setting['driverConfig']);

		$upToken = $auth->UploadToken($setting['driverConfig']['secretKey'],$setting['driverConfig']['accessKey']);

		$data = array(
			"uptoken"=>$upToken
		);

		return $data;


	}


	/**
	 * 项目总结 - 导出Word
	 */
	public function exportWord(){

		header('Cache-Control: no-cache, must-revalidate');

		header('Pragma: no-cache');

		$wordStr = $this->replace();

		$fileContent = getWordDocument($wordStr);

		$fileName = iconv('utf-8', 'GBK', '培训项目总结报告' .date("Ymd",time()));

		header('Content-Type: application/doc');

		header('Content-Disposition: attachment; filename=' . $fileName . '.doc');

		echo $fileContent;
		exit;
	}

	/**
	*替换培训项目模板
	 **/

	public function replace(){

		$file_path = $_SERVER['DOCUMENT_ROOT'].'/tpl/Admin/Summary/summary.html';

		$fp = fopen($file_path, "r");

		$summary = fread($fp, filesize($file_path));

		$items = D("Manage")->detail();

		$number = count($items['designee']);

		$survey_list = $examination_list = $course_list = array();

		//查看课程考勤情况
		foreach($items['course'] as $k=>$course){
			$course_list[$k] = $course;
			$course_list[$k]['attendance'] = D("Manage")->project_course($course['project_id'],$course['course_id']);
		}


		//查看考试情况
		foreach($items['examination'] as $k=>$examination){
			$examination_list[$k] = $examination;
			$examination_list[$k]['attendance'] = D("Manage")->project_examination($examination['project_id'],$examination['test_id']);

			$qualifiedrate = D("Manage")->getQualifiedrate($examination['project_id'],$examination['test_id'],$examination['pass_line']);
			$examination_list[$k]['pass_rate'] = $qualifiedrate['pass_rate'];
			$examination_list[$k]['average'] = $qualifiedrate['average'];
		}

		//查看问卷情况
		foreach($items['survey'] as $k=>$survey){

			$survey_list[$k] = $survey;
			$survey_list[$k]['attendance'] = D("Manage")->project_survey($survey['project_id'],$survey['survey_id']);

		}

		//查看项目总结描述
		$summary_list = D("Manage")->project_summary();

		$course_str = '';

		foreach($course_list as $k=>$v){
			if($v['course_way']==0){
				$course_way ='在线';
			}else{
				$course_way ='面授';
			}
			$course_str.='<tr><td>'. $v['course_name'].'</td><td>'.$course_way.'</td><td>'.$v['attendance']['tobe'].'</td><td>'.$v['attendance']['late'].'</td><td>'.$v['attendance']['notto'].'</td><td>----</td></tr>';
		}


		$examination ='';

		foreach($examination_list as $k=>$v){
			$examination .="<tr role='row' class='odd text-center'><td>".$v['test_names']."</td><td>".$v['attendance']['tobe']."</td><td>".$v['attendance']['notest']."</td><td>".$v['average']."</td><td>".$v['pass_rate']."%</td><td>".$v['username']."</td></tr>";
		}

		$survey='';

		foreach($survey_list as $k=>$v){
			$survey.="<tr role='row' class='odd text-center'><td>".$v['survey_name']."</td><td>".$v['attendance']['tobe']."</td><td>".$v['attendance']['notest']."</td><td>".$v['username']."</td></tr>";
		}

		//实际预算
		$budget = '';
		foreach($summary_list['project_budget'] as $project_budget){
			$budget .= "<label class='col-sm-2 control-label'>".$project_budget['option_name']."：".$project_budget['actual_amount']."元</label><br/>";
		}

		$summary=str_replace('{$project.project_name}',$items['project']['project_name'],$summary);
		$summary=str_replace('{$project.class_name}',$items['project']['class_name'],$summary);
		$summary=str_replace('{$project.start_time}',$items['project']['start_time'],$summary);
		$summary=str_replace('{$project.end_time}',$items['project']['end_time'],$summary);
		$summary=str_replace('{$project.project_budget}',$items['project']['project_budget'],$summary);
		$summary=str_replace('{$count}',$number,$summary);
		$summary=str_replace('{$course}',$course_str,$summary);
		$summary=str_replace('{$examination}',$examination,$summary);
		$summary=str_replace('{$survey}',$survey,$summary);
		$summary=str_replace('{$budget}',$budget,$summary);
		$summary=str_replace('{$actual_expenses}',$summary_list['project_summary']["total_expenses"],$summary);
		$summary=str_replace('{$actual_summary}',$summary_list['project_summary']['summary'],$summary);

		return $summary;

	}

	/**
	 * 项目总结 - 导出PDF
	 */
	public function exportPdf(){
		$summary=$this->replace();

		$fileName = 'ProjectSummary'.date("Ymd",time()).'.pdf';

		pdf($fileName,$summary);
	}

	/**
	 * 培训项目 - 指定人员
	 */
	public function personnel(){

		$treeInfo = D('AdminTissue')->treeInfo();

		if($treeInfo['pid'] == 0){

			//循环三级

			foreach($treeInfo['_data'] as $k=>$_data){

				foreach($_data['_data'] as $i=>$data){

					$where['a.tissue_id'] = array("eq",$data['id']);
					$where['b.id'] = array("gt",0);
					$where['b.status'] = array("neq",3);

					$user_list = M('tissue_group_access a')->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")->field('b.id,b.username')->where($where)->select();

					$treeInfo['_data'][$k]['_data'][$i]['_data'] = $user_list;

				}

			}

			if(!empty($treeInfo['rules'])){

				$treeInfo_id = $treeInfo['id'].",".$treeInfo['rules'];

			}else{

				$treeInfo_id = $treeInfo['id'];

			}

			$condition['a.tissue_id'] = array("in",$treeInfo_id);
			$condition['b.id'] = array("gt",0);

			$admin_list = M('tissue_group_access a')->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")->field('b.id,b.username')->where($condition)->select();

		}else{

			//循环二级

			foreach($treeInfo['_data'] as $k=>$_data){

				$where['a.tissue_id'] = array("eq",$_data['id']);
				$where['b.id'] = array("gt",0);
				$where['b.status'] = array("neq",3);

				$user_list = M('tissue_group_access a')->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")->field('b.id,b.username')->where($where)->select();

				$treeInfo['_data'][$k]['_data'] = $user_list;

			}

			$condition['a.tissue_id'] = array("eq",$treeInfo['id']);
			$condition['b.id'] = array("gt",0);

			$admin_list = M('tissue_group_access a')->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")->field('b.id,b.username')->where($condition)->select();


		}

		$data = array(
			"tree_items"=>$treeInfo,
			"admin_list"=>$admin_list
		);

		return $data;
	}

	/**
	 * 培训项目 - 指定部门
	 */
	public function department(){

		$treeInfo = D('AdminTissue')->treeInfo();

		return $treeInfo;

	}

	/**
	 * 培训项目 - 获取负责人
	 */
	public function getOrganization(){

		//隔离数据过滤
		//$specifiedUser = D('IsolationData')->specifiedUser();
		//$where['a.user_id'] = array("in",$specifiedUser);

		//根据方案取出所有组织会员ID
		$where['user_id'] = array("eq",$_SESSION['user']['id']);
		$tissue_id = M("tissue_group_access")->where($where)->getField("tissue_id");
		$plan_id = M("sys_tissue")->where("tissue_id=".$tissue_id)->getField("plan_id");

		if(!empty($plan_id)){
			$sys_tissue_list = M("sys_tissue")->field("tissue_id")->where("plan_id=".$plan_id)->select();

			$userid_all = array();

			foreach($sys_tissue_list as $list){

				$group_access = M("tissue_group_access")->field("user_id")->where("tissue_id=".$list['tissue_id'])->select();

				foreach($group_access as $access){
					$userid_all[] = $access['user_id'];
				}
			}

			if(!empty($userid_all)){
				$map['id'] = array("in",$userid_all);
			}else{
				$map['id'] = array("in",array(0));
			}
		}else{
			$map['id'] = array("in",array(0));
		}

		//$where['a.group_id'] = array("eq",1);
		//$where['b.id'] = array("gt",0);
		/*$items = M("auth_group_access a")->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
					->field("b.id,b.username")
					->where($where)
					->select();*/
		$items = M("users")
					->field("id,username")
					->where($map)
					->select();

		return $items;
	}

	/**
	 * 获取所有公司的部门、人员信息
	 * @return [type]      [description]
	 */
	public function getCompanys(){
		$post = I('post.');

		$job = I('post.job');
		$tag = I('post.tag');
		if($job && $job != -1){
			$where['d.id'] = $job;
		}
		

		$data = M('tissue_rule')->where(array('pid'=>1))->select();
		foreach($data as $k=>$v){
			$data[$k]['tissue_ids'] = M('tissue_rule')->where(array('pid'=>$v['id']))->getField('id',true);
		}
		foreach($data as $k=>$v){
			if($v['tissue_ids']){
				$data[$k]['user_number'] = 0;
				foreach($v['tissue_ids'] as $k1=>$v1){
					//$where['b.username'] = array('neq','');
					$where['a.tissue_id'] = $v1;
					$data[$k]['tissues'][$k1]['values'] = M('tissue_group_access a')
						->join('left join __USERS__ b on a.user_id=b.id')
						->join('left join __TISSUE_RULE__ c on a.tissue_id=c.id')
						->join('left join __JOBS_MANAGE__ d on a.job_id=d.id')
						->field('a.*,b.username,b.job_number,b.phone,c.name as tissue_name,d.name as job_name')
						->where($where)
						->select();
					$data[$k]['tissues'][$k1]['tissue_name'] = M('tissue_rule')
							->where(array('id'=>$v1))
							->getField('name');
					$data[$k]['user_number'] += count($data[$k]['tissues'][$k1]['values']);
				}
			}
		}
		$count_all = 0;
		foreach($data as $k=>$v){
			$count_all += $v['user_number'];
			foreach($v['tissues'] as $k1=>$v1){
				foreach($v1['values'] as $k2=>$v2){
					$where1['a.user_id'] = $v2['user_id'];
					$tags = M('users_tag_relation a')
						->join('left join __USERS_TAG__ b on a.tag_id=b.id')
						->where($where1)
						->select();
					foreach($tags as $k3=>$v3){
						$tags_str .= $v3['tag_title'] . ',';
					}
					$data[$k]['tissues'][$k1]['values'][$k2]['tag_title'] = rtrim($tags_str,',');
					if($tag && $tag != -1){
						if(strpos($tags_str,$tag) === false){
							unset($data[$k]['tissues'][$k1]['values'][$k2]);
						}
					}
					$tags_str = '';
				}
			}
		}

		return array('data'=>$data,'job'=>$job,'tag'=>$tag,'count_all'=>$count_all);
	}

	/**
	 * 获取所有岗位
	 * @return [type] [description]
	 */
	public function getJobs(){
		return M('jobs_manage')->select();
	}

	/**
	 * 获取所有标签
	 * @return [type] [description]
	 */
	public function getTags(){
		return M('users_tag')->select();
	}

	/**
	 * 删除培训项目
	 */
	public function del_project(){

		$id = I('post.id');

		try {

			$DB = M();

			$DB->startTrans();

			$increment_id = $DB->table('__ADMIN_PROJECT__')->where("id=".$id)->delete();

			$DB->table('__DESIGNATED_PERSONNEL__')->where("project_id=".$id)->delete();

			$DB->table('__PROJECT_BUDGET__')->where("project_id=".$id)->delete();

			$DB->table('__PROJECT_COURSE__')->where("project_id=".$id)->delete();

			$DB->table('__PROJECT_EXAMINATION__')->where("project_id=".$id)->delete();

			$DB->table('__PROJECT_SUMMARY__')->where("project_id=".$id)->delete();

			$DB->table('__PROJECT_SURVEY__')->where("project_id=".$id)->delete();

			if(!empty($increment_id)){

				$DB->commit();

				$results = true;
			}

		} catch ( Exception $e ) {

			$DB->rollback();

			$results = false;

		}

		return $results;


	}
	
	/**
	 * 获取线下培训班信息
	 */
	public function getTrainingData($total_page=10){
		$start_page = I("get.p",1,'int');
		$keyword = I("get.keyword") ? I("get.keyword") : "";
		$typeid = I("get.typeid",0,'int');
		$training_category = I("get.training_category");
		$plan_category = I("get.plan_category");
		
		//进行中和已完成的项目
		$where['b.type'] = array("in",array('0','4'));
		
		//必修项目or选修项目
		if($typeid == 0){//必修-指定人员
			$where['a.user_id'] = session('user.id');//指定部门-当前人员-已报名
			$where['a.typeid'] = 0;
			$where['a.sign_up'] = 1;
		}else{
			$where['a.user_id'] = session('user.id');
			$where['a.sign_up'] = 1;
			$where['a.typeid'] = 1;
		}
		if(!empty($keyword)){
			$where['_string']="(b.project_name like '%".$keyword."%') OR (b.project_description like '%".$keyword."%')";
		}

		if(($plan_category >=0) and ($plan_category != '')){
			$where['b.plan_category'] = array("eq",$plan_category);
		}

		if(($training_category >=0) and ($training_category != '')){
			$where['b.training_category'] = array("eq",$training_category);
		}
		
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "b.id,b.project_name,b.project_description,b.project_covers,b.project_budget,b.is_public,b.population,b.type,b.training_category,b.plan_category,b.auth_user_id,";
			$field .= "to_char(b.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
		}else{
			$field = "a.*,b.*";
		}
		
		$results = M('designated_personnel a')
				->join('LEFT JOIN __ADMIN_PROJECT__ b on a.project_id=b.id')
				->where($where)
				->field($field)
				->page($start_page,$total_page)
				->select();
		foreach($results as $k=>$v){
			if($typeid == 0){//必修-指定人员
				$where1['typeid'] = 0;
				$where1['sign_up'] = 1;
			}else{
				$where1['sign_up'] = 1;
				$where1['typeid'] = 1;
			}
			$where1['project_id'] = $v['id'];
			$results[$k]['users'] = M('designated_personnel')
						->where(array('sign_up'=>1,'typeid'=>0,'project_id'=>$v['id']))
						->count();//指定的用户数量
			
			//学分
			$course = M('project_course a')
					->join('left join __COURSE__ b on a.course_id=b.id')
					->where(array('a.project_id'=>$v['id']))
					->field('a.credit,b.course_way')
					->select();
			$credit = $on = $off = 0;
			foreach($course as $kk=>$vv){
				$credit += $vv['credit'];
				if($vv['course_way'] == 1){
					$off += 1;
				}else{
					$on += 1;
				}
			}
			
			$results[$k]['credit'] = $credit;
			$results[$k]['off'] = $off;
			$results[$k]['on'] = $on;
		}
		$count = M('designated_personnel a')
				->join('LEFT JOIN __ADMIN_PROJECT__ b on a.project_id=b.id')
				->where($where)
				->count();
		
		$show = $this->pageClass($count,$total_page);
		$data = array(
			'typeid' => $typeid,
			'page' => $show,
			'list' => $results,
			'keyword' => $keyword,
			'plan_category' => $plan_category,
			'training_category' => $training_category
		);

		return $data;
		
	}
	
	/**
	 * 线下培训班详情
	 */
	public function detail2(){
		$pid = I('get.pid');
		$typeid = I('get.typeid');
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "b.id,b.project_name,b.project_description,b.project_covers,b.project_budget,b.is_public,b.population,b.type,b.training_category,b.plan_category,b.auth_user_id,";
			$field .= "to_char(b.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
		}else{
			$field = "a.*,b.*";
		}
		
		//基础信息
		$where['a.project_id'] = $pid;
		$base = M('designated_personnel a')
				->join('LEFT JOIN __ADMIN_PROJECT__ b on a.project_id=b.id')
				->where($where)
				->field($field)
				->find();
				
		if($typeid == 0){//必修-指定人员
			$where1['typeid'] = 0;
			$where1['sign_up'] = 1;
		}else{
			$where1['sign_up'] = 1;
			$where1['typeid'] = 1;
		}
		
		$where1['project_id'] = $pid;
		$base['users'] = M('designated_personnel')
				->where(array('sign_up'=>1,'typeid'=>1,'project_id'=>$pid))
				->count();//已报名的用户数量
		
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "a.credit,a.project_id,b.id,b.course_name,b.course_way,b.lecturer,b.lecturer_name,a.location,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,c.name";
		}else{
			$field = "a.credit,a.project_id,b.id,b.course_name,b.course_way,b.lecturer,b.lecturer_name,a.location,a.start_time,a.end_time,c.name";
		}
		//学分/课程信息
		$course = M('project_course a')
				->join('left join __COURSE__ b on a.course_id=b.id')
				->join('left join __LECTURER__ c on b.lecturer=c.id')
				->where(array('a.project_id'=>$pid))
				->field($field)
				->select();
		
		$credit = $on = $off = 0;
		foreach($course as $kk=>$vv){
			$course[$kk]['username'] = empty($vv['lecturer_name']) ? $vv['name'] : $vv['lecturer_name'];
			$credit += $vv['credit'];
			if($vv['course_way'] == 1){
				$off += 1;
			}else{
				$on += 1;
			}
			$course[$kk]['url'] = U('Admin/MyCourse/detail',array('project_id'=>$pid,'course_id'=>$vv['id']));
		}
//		dump($course);
		$base['credit'] = $credit;
		$base['off'] = $off;
		$base['on'] = $on;
		
		//考试信息
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "freq,project_id,test_id,credits,test_length,test_names,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,cacheid";
		}else{
			$field = "freq,project_id,test_id,credits,test_length,test_names,start_time,end_time,cacheid";
		}
		//学分/课程信息
		$test = M('project_examination')
				->where(array('project_id'=>$pid))
				->field($field)
				->select();
//		dump($test);
		foreach($test as $k=>$v){
			$test[$k]['total_score'] = M('examination')->where(array('id'=>$v['test_id']))->getField('test_score');
			//考试次数
			$num = M('exam_score')
				->where(array('project_id'=>$pid,'exam_id'=>$v['test_id'],'user_id'=>session('user.id')))
				->max('counter');
			$test[$k]['counter'] = $num;	//已考次数
			$test[$k]['max_score'] = D('MyExam')->getMaxScore($v['test_id'],false,$pid);
			
			if(strtotime($v['start_time']) > time()){
                $test[$k]['statusinfo'] = '未开始';
                $test[$k]['url'] = "javascript:layer.alert('考试尚未开始')";
            }else if(strtotime($v['end_time']) < time() || $num >= $v['freq']){
                $test[$k]['statusinfo'] = '已结束';
                $test[$k]['url'] = U('Admin/MyExam/result',array('pid'=>$pid,'eid'=>$v['test_id']));
            }else{
                $test[$k]['statusinfo'] = '进行中';
                if($v['test_id']){
                	$test[$k]['url'] = U('Admin/MyExam/joinExam',array('examination_id'=>$v['test_id'],'project_id'=>$pid));
                }else{
                	$test[$k]['url'] = U('Admin/MyExam/lookresultoffline',array('cacheid'=>$v['cacheid'],'project_id'=>$pid));
                }
            }
		}

		//调研信息
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "a.project_id,a.survey_id,a.survey_names,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,b.survey_name,c.cat_name";
		}else{
			$field = "a.project_id,a.survey_id,a.survey_names,a.test_names,a.start_time,a.end_time,b.survey_name,c.cat_name";
		}
		$survey = M('project_survey a')
				->join('left join __SURVEY__ b on a.survey_id=b.id')
				->join('left join __SURVEY_CATEGORY__ c on b.survey_cat_id=c.id')
				->where(array('a.project_id'=>$pid))
				->field($field)
				->select();
		foreach($survey as $k=>$v){
			if(strtotime($v['start_time']) > time()){
                $survey[$k]['statusinfo'] = '未开始';
                $survey[$k]['url'] = "javascript:layer.alert('尚未开始')";
            }else if(strtotime($v['end_time']) < time()){
                $survey[$k]['statusinfo'] = '已结束';
                $survey[$k]['url'] = U('Admin/MySurvey/checkSurveyResult',array('project_id'=>$pid,'survey_id'=>$v['survey_id'],'typeid'=>0));
            }else{
                $survey[$k]['statusinfo'] = '进行中';
                $survey[$k]['url'] = U('Admin/MySurvey/joinSurvey',array('project_id'=>$pid,'survey_id'=>$v['survey_id'],'typeid'=>0));
            }
		}
		return array(
			'base'=>$base,
			'course'=>$course,
			'test'=>$test,
			'survey'=>$survey
		);
	}

}
