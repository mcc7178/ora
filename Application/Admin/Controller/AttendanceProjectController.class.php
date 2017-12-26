<?php

namespace Admin\Controller;

use Common\Controller\AdminBaseController;

class AttendanceProjectController extends AdminBaseController
{

    /**
     *项目考列表
     */
    public function index()
    {
        //负责人可以看到自己负责课程的关联项目
 
        $data = D('AttendanceProject')->getIndex();
        
        
        $this->assign('list', $data[0]);
        $this->assign('list', $data[0]);
        $this->assign('page', $data[1]);
        $this->assign('keyword', $data[2]);
        
        

        $this->display();
    }
  
    /**
     *项目内考勤展示页
     */
    public function projectAttendanceList()
    {
        //项目内考勤展示页详情
 
        $data = D('AttendanceProject')->getProjectAttendanceList();

        $project_id = I('get.project_id')+0;
        $this->assign('project_id', $project_id);
        $this->assign('list', $data['list']);
        $this->assign('page', $data['page']);
        $this->assign('project_name', $data['project_name']);
        $this->assign('keyword', $data['keyword']);

        $this->display('the_project_attendance');
    }

  
    /**
     * 项目内考勤生效的课程列表
     */
    public function alreadyAttendanceCourse()
    {
        
 
        $data = D('AttendanceProject')->alreadyAttendanceCourse();

        $project_id = I('get.project_id')+0;
        $this->assign('project_id', $project_id);
        $this->assign('list', $data['list']);
        $this->assign('page', $data['page']);
        $this->assign('project_name', $data['project_name']);
        $this->assign('attendance_name', $data['attendance_name']);
        $this->assign('keyword', $data['keyword']);

        $this->display('already_attendance_course');
    }


    /**
     *项目内创建考勤
     */
 	public function createAttendance(){
       if(IS_AJAX){
           $project_id = I('post.project_id');
           $attendance_name = I('post.attendance_name');
           $start_time = I('post.start_time');
           $end_time = I('post.end_time');
           
           $insertdata = array(
               'project_id'=>$project_id,
               'attendance_name'=>$attendance_name,
               'status'=>0,
               'start_time'=>$start_time,
               'end_time'=>$end_time,
               'user_id'=>$_SESSION['user']['id'],
               'add_time'=>date()
           );

           
            if(strtolower(C('DB_TYPE')) == 'oracle'){
              $insertdata['id'] =  getNextId('attendance_project');
              $insertdata['start_time'] = array('exp',"to_date('".$start_time."','yy-mm-dd hh24:mi:ss')");
              $insertdata['end_time'] = array('exp',"to_date('".$end_time."','yy-mm-dd hh24:mi:ss')");
              $insertdata['add_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            }
          if(trim($attendance_name) == ''){
            $data['status'] = 0;  
            $data['info'] = '考勤名称必填';  
            $data['url'] = ''; 
            $this->ajaxReturn($data);
          }

          if(trim($start_time) == '' || trim($end_time) == ''){
            $data['status'] = 0;  
            $data['info'] = '考勤时间必填';  
            $data['url'] = ''; 
            $this->ajaxReturn($data);
          }

      
          $res = D('AttendanceProject')->createAttendance($insertdata);
          if($res){
            $data['status'] = 1;  
            $data['info'] = '创建成功';  
            $data['url'] = U('Admin/AttendanceProject/projectAttendanceList',array('project_id'=>$project_id)); 
            $this->ajaxReturn($data);
          }else{
            $data['status'] = 0;  
            $data['info'] = '创建失败';  
            $data['url'] = ''; 
            $this->ajaxReturn($data);
          }

       }
     }
 
 

    /**
     *项目内删除考勤
     */
 	public function delAttendance(){
         
         if(IS_AJAX){
            $id = I('post.id') + 0; //接收think_admin_project的id

            $res = D('AttendanceProject')->delAttendance($id);
          if($res){
            $data['status'] = 1;  
            $data['info'] = '删除成功';  
            $data['url'] = U('Admin/AttendanceProject/projectAttendanceList',array('project_id'=>$project_id)); 
            $this->ajaxReturn($data);
          }else{
            $data['status'] = 0;  
            $data['info'] = '删除失败';  
            $data['url'] = ''; 
            $this->ajaxReturn($data);
          }

         
   


       }
     }



























}