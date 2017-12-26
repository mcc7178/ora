<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 考勤model
 */
class AttendanceModel extends BaseModel{


	/**获取各种状态的考勤信息
	*/
	public function  salary($attendance_project_id,$project_id){

		$map["attendance_project_id"]=$attendance_project_id;

		$map["status"]=0;

		$data=array();

		//统计缺勤人数

		$data['absenteeism']=$this->where($map)->count();

		//统计迟到人数
        $arr["attendance_project_id"]=$attendance_project_id;

		$arr["status"]=2;

		$data['late']=$this->where($arr)->count();

		//统计按时人数
        $array["attendance_project_id"]=$attendance_project_id;

		$array["status"]=1;

		$data['on_time']=$this->where($array)->count();

		return $data;
	}
   

   	/**
	 *获取考勤时间
	 */
	public function getTime($id){
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
          $data = M('attendance_project')->where(array('id'=>$id))->field("to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")->find();
		}else{
		  $data = M('attendance_project')->where(array('id'=>$id))->find();
		}
		return $data;
	}

   	/**
	 *获取项目全部考勤的课程 参数$id为项目id
	 */
	public function getAttendanceCourse($project_id,$attendance_project_id=0){
		$user_id = $_SESSION['user']['id'];

		$where = array(
            'a.id'=>$project_id,
			'b.manager_id'=>$user_id,
			'c.course_way'=>1

		);

        $list = M('admin_project')->alias('a')
		                  ->join('left join __PROJECT_COURSE__ b on b.project_id=a.id')
						  ->join('left join __COURSE__ c on c.id=b.course_id')
                          ->where($where)
						  ->field('c.id,c.course_name')
						  ->select();
         
        foreach($list as $k=>$v){
			$map = array(
				'a.course_id'=>$v['id'],
				'b.project_id'=>$project_id,	
				);
			$exist = M('attendance_course')->alias('a')
			             ->join('left join __ATTENDANCE_PROJECT__ b on b.id=a.attendance_project_id')
			             ->where($map)
						 ->find();
           if(!$exist){
              $list[$k]['course_status'] = 0; //未考勤
	    	}else{
              $list[$k]['course_status'] = 1; //已考勤
			}
		} 
        

		// if(strtolower(C('DB_TYPE')) == 'oracle'){
        //   $list = M('attendance_project')->where(array('id'=>$id))->field("to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")->find();
		// }else{
		//   $data = M('attendance_project')->where(array('id'=>$id))->find();
		// }
		return $list;
	}



    /**
	 *根据课程id获取课程名称
	 */

	public function getCoursename($course_id){
      $course_name=M('course')->where(['id'=>$course_id])->getField('course_name');
	  return $course_name;

	}


	/**获取学生考勤信息列表,导入流程调用
	*/

	public function lists($course_id,$project_id){

		$map["course_id"]=$course_id;
        $map["pid"]=$project_id;
		$list= $this->field('a.id,a.status,a.mobile_scanning,b.username,c.tissue_id,c.job_id')
		                ->alias('a')
		                ->join('left join __USERS__ b on a.user_id=b.id')
		                ->join('left join __TISSUE_GROUP_ACCESS__ c on a.user_id=c.user_id')
		                ->where($map)
						->select();
		// print_r($list);
		$tissue=M("TissueRule");

		$jobsManage=M("JobsManage");

		for($i=0;$i<count($list);$i++){

			$maps["id"]=$list[$i]["tissue_id"];

			$arr["id"]=$list[$i]["job_id"];

			// $list[$i]['company_name']=$tissue->field("name")->where($maps)->find();
            $list[$i]['company_name']=$tissue->where($maps)->getField("name");

			// $list[$i]["job_name"]=$jobsManage->field("name")->where($arr)->find();
			$list[$i]["job_name"]=$jobsManage->where($arr)->getField("name");
		}


        // print_r($list);
		return $list;
		
	}

        /**
	     **获取学生考勤信息列表,点击保存按钮则save数据
      	 */
    public function newlists($project_id,$course_id){
		
	   $keyword = I('table_search'); 
        $condition = array(
            'b.username'=>array("like","%".$keyword."%"),
        ); 
       $project_id = I('get.project_id') + 0;
	   $attendance_project_id = I('get.attendance_project_id') + 0;
        //取得考勤关联表数据
        $where1["a.id"] = $project_id;
        $where1["b.sign_up"] = 1;//已报名（指定人员+公开范围）
		$datas= M('admin_project')->field('a.id as pid,b.user_id,3 status')
		                ->alias('a')
						->join('left join __DESIGNATED_PERSONNEL__ b on b.project_id=a.id')
						->join('left join __PROJECT_COURSE__ c on c.project_id=a.id')
		                ->where($where1)
						->select();
		
		// print_r($datas);exit;
	    
		// 初始项目考勤表加入考勤数据，第二次则不会重复加入
		foreach($datas as $k=>$v){
		  $arr = array();
		 $user_id = $v['user_id'];
		 if(!empty($user_id)){
            $res = M('attendance')->where(["attendance_project_id=$attendance_project_id and user_id=$user_id"])->select();
		   if(empty($res)){
			if(strtolower(C('DB_TYPE')) == 'oracle'){
                 $arr['id'] = getNextId("attendance");
		     }
			 $arr['attendance_project_id'] = $attendance_project_id;
			 $arr['user_id'] = $user_id;
			 $arr['status'] = $v['status'];
			 $arr['pid'] = $project_id;
		     M('attendance')->add($arr); 
		   }
		 }
         
		}

		$p = I('get.p',0,'int');
		$status = I('get.status');//考勤状态
		$size = 30;
		if($status != '-1' && $status != ''){
			$condition['a.status'] = $status;
		}
        $list= M('attendance')->alias('a')
                ->field('a.id,b.username,b.job_number,b.email,e.name as company_name,d.name as job_name,a.status')
                ->join('left join __USERS__ b on b.id=a.user_id')
				->join('left join __TISSUE_GROUP_ACCESS__ c on c.user_id=b.id ')
				->join('left join __JOBS_MANAGE__ d on d.id=c.job_id ')
				->join('left join __TISSUE_RULE__ e on e.id=c.tissue_id ')
                ->where(["a.attendance_project_id=$attendance_project_id"])
				->where($condition)
				->page($p.','.$size)
				->order('a.id asc')
				->select();

        $count= M('attendance')->alias('a')
                ->field('a.id,b.username,b.job_number,b.email,e.name as company_name,d.name as job_name,a.status')
                ->join('left join __USERS__ b on b.id=a.user_id')
				->join('left join __TISSUE_GROUP_ACCESS__ c on c.user_id=b.id ')
				->join('left join __JOBS_MANAGE__ d on d.id=c.job_id ')
				->join('left join __TISSUE_RULE__ e on e.id=c.tissue_id ')
                ->where(["a.attendance_project_id=$attendance_project_id"])
				->where($condition)
				->order('a.id asc')
				->count();
        
         $show = $this->pageClass($count,$size);
		 $data = array(
			'list'=>$list,
			'page'=>$show,     
			'keyword'=>$keyword,
			'status'=>$status
		 );
        return $data;
	}
     

     /***
       批量保存更新考勤信息
     */
    public function batchModify(){

         if(IS_AJAX){
             $ids = I('post.ids'); //接受的$ids位attendance表的ids
            //  dump($ids);
             $attendance_status = I('post.attendance_status') + 0;  //接收页面当时该条数据的审核状态
           
             foreach($ids as $k=>$v){

               $data = array(
				   'status'=>$attendance_status,
				   'type'=>1,
				//    'attendance_time'=>array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')")
				   );
               $res = M('attendance')->where(array('id'=>$v))->save($data);   
  
             }

			 if($res){
               $ret = array(
                     'status'=>1,
                     'info'=>'考勤成功',
					 'url'=>U('Admin/Attendance/index')
                      );
                return $ret;
               }else{
                $ret = array(
                     'status'=>0,
                     'info'=>'考勤失败',
					 'url'=>''
                      );
                return $ret; 
               }
         }

	}




     /***
       批量提交考勤-选择项目内考勤对哪几门课程生效
     */
    public function batchSubmit(){
         if(IS_AJAX){
          $ids = I('post.ids'); //接受的$ids为课程ids
          $attendance_project_id = I('post.attendance_project_id');


		  // 自动启动事务支持
         $this->startTrans();
         try {  
          foreach($ids as $k=>$v){

               $data = array('attendance_project_id'=>$attendance_project_id,'course_id'=>$v);
			   
				if(strtolower(C('DB_TYPE')) == 'oracle'){
                  $data['id'] = getNextId('attendance_course');
				}

               $res = M('attendance_course')->add($data);   
  
            }

			$status = array('status'=>1);
			M('attendance_project')->where(array('id'=>$attendance_project_id))->save($status); 
         // 提交事务
            $this->commit();

            $ret = array(
                     'status'=>1,
                     'info'=>'提交成功',
					 'url'=>U('Admin/Attendance/index')
                      );
		 } catch (ThinkException $e) {
            $this->rollback();
			$ret = array(
                     'status'=>0,
                     'info'=>'提交失败',
					 'url'=>''
                    );
         }

          return $ret;

		 }
         
    }








	/**更新学生考勤信息
	*/

	public function update($map){
		
		$maps["id"]=$map["id"];
        
		$arr["status"]=$map["status"];

		$res=$this->where($maps)->save($arr);

		return $res;
	}

	/***
	导入考勤,进行与数据库attendance表的考勤人员匹配，并save学员考勤状态
	*/
    public function importAttendance($file){
		
		 $attendance_project_id=I('post.attendance_project_id');
		 $project_id=I('post.project_id');
		 
         $data = import_excel_int($file);
		//  echo 22;die;
         unset($data[1]);
        //  print_r($data); 

         foreach($data as $k=>$v){
		  	
		 //手机号码不能为空，判断逻辑
            if($v[1]==''){
				 
				$this->error = "导入表格的第 $k 行用户邮箱不能为空";
				return false;
			}
			if($v[2]==''){
				$this->error = "导入表格的第 $k 行考勤信息不能为空";
				return false;
			}

	    		//用户是否在数据库考勤表中存在，判断逻辑:根据导入表格的手机号码做判断
	    	 // echo $v[1];
             $v[0]=(string)$v[0];
			 $v[1]=(string)$v[1];

	    	 $where = array(
	    			 'b.email'=>$v[1],
					 'b.username'=>$v[0],
	    	 		 'a.pid'=> $project_id,
	    	 		 'a.attendance_project_id'=>$attendance_project_id
	    	 );
	    	 $res = M('attendance')->alias('a')
	    	 						->join('left join __USERS__ b on b.id=a.user_id')
	    	 						->where($where)
	    	 						->find();
	    	 // dump($res);die;
	    	 if(!$res){
	    	 	$this->error = "导入表格的第 $k 行用户不存在";
	    	 	return false;
	    	 }	  

			// echo $v[2];
			if($v[2]!=='缺勤'&&$v[2]!=='按时'&&$v[2]!=='迟到'){
				$this->error = "导入表格的第 $k 行考勤信息不正确";
				return false;
			}	
		 }   
           //返回数据
			return $data;
	}

	/***
	导入考勤 old
	*/
	public function importAttendances($file){

		 $list = import_excel($file);
         print_r($list);
		 $lists=array();

		 for($a=0;$a<count($list);$a++){

			if(!empty($list[$a][1])){

				$lists[]=$list[$a];
		 	}
		 }
		
		 $data=array();

		 $user=D("Users");

		 $tissueGroupAccess=D("TissueGroupAccess");

		 $array=array("按时","迟到","缺席");


		 
		 for($i=1;$i<count($lists);$i++){

			$data[$i]["username"]=$lists[$i][0];

		 	$data[$i]["mobile"]=$lists[$i][1];

		 	$data[$i]["attendance"]=$lists[$i][2];

			if($data[$i]["attendance"]=="按时"){

		 		$data[$i]["attendance"]=1;
		 	
		 	}elseif($data[$i]["attendance"]=="迟到"){

		 		$data[$i]["attendance"]=2;

		 	}else{

		 		$data[$i]["attendance"]=0;

		 	}

			if(empty($data[$i]["username"])||empty($data[$i]["mobile"])||empty($data[$i]["attendance"])){

				$data[$i]["status"]=false;

		 		$data[$i]["message"]="请完善第".$i."行信息";
			
			}else{

				 $map["phone"]=$data[$i]["mobile"];

				 $userInfo=$user->where($map)->find();

				 $data[$i]["user_id"]= $userInfo["id"];

				 $maps["user_id"]=$data[$i]["user_id"];

				 $data[$i]['department_id']=$tissueGroupAccess->field("tissue_id")->where($maps)->find();

				 $data[$i]["position_id"]=$tissueGroupAccess->field("job_id")->where($maps)->find();

				 if($userInfo==null){

				 	$data[$i]["status"]=false;

				 	$data[$i]["message"]="第".$i."行用户不存在";

				 }

				 if(in_array($data[$i]["attendance"],$array)){

					$data[$i]["status"]=false;

				 	$data[$i]["message"]="第".$i."行考勤信息不正确";
				 }


			}

		 }

		
		
		return $data;
	}
    
	/***
	更新考勤信息 
	*/
    public function saveAttendance($project_id,$attendance_project_id,$list){
		// print_r($list);
		 $attendance = M('attendance');
         foreach($list as $v){
			// 考勤状态0表示缺勤，1表示按时,2表示迟到,3表示未考勤
		   $flag = array('缺勤','按时','迟到');
           if($v[2]==$flag[0]){
			   $v[2] = 0;
		   }else if($v[2]==$flag[1]){
			   $v[2] = 1;
		   }else if($v[2]==$flag[2]){
			   $v[2] = 2;
		   }
		   //逐条更改考勤信息
     
		    $user_id = M('users')->where(array('email'=>$v[1],'status'=>array('neq',3)))->getField('id'); 
	
		    $data['status'] = $v[2];
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
              $data['attendance_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
			}else{
			  $data['attendance_time'] = date('Y-m-d H:i:s');
			}
            $res = M('attendance')->where(['pid'=>$project_id,'user_id'=>$user_id,'attendance_project_id'=>$attendance_project_id])->save($data);
           

			// dump($res);
		                  
		 }
		  return $res;

	}

	/***
	更新考勤信息 old
	*/
	public function updateAttendance($course_id,$data){

		$user_id=$data["user_id"];
		
		$map["user_id"]=$data["user_id"];

		$map["status"]=$data["attendance"];

		$map["course_id"]=$course_id;

		$map["department_id"]=$data["department_id"]["tissue_id"];

		$map["position_id"]=$data["position_id"]["job_id"];

		$count=$this->where("user_id=$user_id")->count();

		

		if($count=0){

			$res=$this->add($map);
		
		}else{

			$res=$this->where("user_id=$user_id")->save($map);
		}

		return $res;

	}

}
