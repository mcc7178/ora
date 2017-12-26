<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;

class AttendanceController extends AdminBaseController
{
   /***
   显示项目内的考勤列表详情
   */
   public function index(){
     
       C('TOKEN_ON',false);
      
        $flag = I('get.flag') + 0; //标识是否为查看已考勤课程的考勤结果，1表示是

        $from_teach = I('get.from_teach') + 0; //标识入口来着我的授课列表
        
        $attendance_project_id = I('get.attendance_project_id') + 0;
        $project_id = I('get.project_id') + 0;
        if($attendance_project_id == 0){
           $this->error('非法的id值');
        }
        // $list=D("Attendance")->lists($course_id);//获取学员考勤列表
        $data = D("Attendance")->newlists($project_id,$attendance_project_id);//根据关联项目id//获取学员考勤列表
        
        $statistics=D("Attendance")->salary($attendance_project_id,$project_id);//获取各种状态的考勤人数

        $times=D("Attendance")->getTime($attendance_project_id);   //项目内考勤时间
        // dump($list);
        $course_list=D("Attendance")->getAttendanceCourse($project_id,$attendance_project_id);

        $this->assign("flag",$flag);
        $this->assign("from_teach",$from_teach);
        $this->assign("project_id",$project_id);
        $this->assign("statistics",$statistics);
        $this->assign("list",$data['list']);
        $this->assign("course_list",$course_list);
        $this->assign("page",$data['page']);
        $this->assign("keyword",$data['keyword']);
        $this->assign("start_time",$times['start_time']);
        $this->assign("end_time",$times['end_time']);
        $this->assign("attendance_project_id",$attendance_project_id);
        $this->assign("status",$data['status']);
        // $this->assign("type",$type);

        $this->display('Attendance/index');
   }





   /***
   二维码生成
   */
   public function  qrcode(){
        $course_id=I("get.id");
        $project_id=I("get.project_id");    
        $attendance_project_id=I("get.attendance_project_id");
        // $url=json_encode(array("course_id"=>$course_id,"project_id"=>$project_id,"type"=>2));

        //&parm id  int 关联项目考勤表id
        //&parm pid int 项目id
        $url=json_encode(array("id"=>$attendance_project_id,"pid"=>$project_id,"type"=>2));
        qrcode($url,40);
   }


   /**
    *导出考勤Excel
    */
     public function createExcel(){ 
         $attendance_project_id = I('get.attendance_project_id') + 0;
         $project_id = I('get.project_id') + 0;
            if($attendance_project_id == 0){
              $this->error('非法的id值');
            }
          
         $arr = D("Attendance")->newlists($project_id,$attendance_project_id);//根据关联项目id获取学员考勤列表
        //  dump($arr);die;
         
         $list = $arr['list'];

         $data = array();
         $data[0] = array(
           'username'=>'姓名',
           'job_number' => '工号',
           'email'=>'邮箱',
           'company_name'=>'组织机构',
           'job_name'=>'岗位',
           'status'=>'考勤结果'
         );
         
        
         foreach($list as $k=>$v){

           if($v['status'] == 0){
              $v['status'] = '缺勤';
           }else if($v['status'] == 1){
              $v['status'] = '正常';
           }else if($v['status'] == 2){
              $v['status'] = '迟到/早退';
           }else if($v['status'] == 3){
              $v['status'] = '未考勤';
           }

           $data[$k+1] =  array(
           'username'=>$v['username'],
           'job_number'=> $v['job_number'],
           'email'=>$v['email'],
           'company_name'=>$v['company_name'],
           'job_name'=>$v['job_name'],
           'status'=>$v['status']
         );
          
         }
        //  $data = array(
        //          array(NULL, 2010, 2011, 2012),
        //          array('Q1', 12, 15, 21),
        //          array('Q2', 56, 73, 86),
        //          array('Q3', 52, 61, 69),
        //          array('Q4', 30, 32, 0),
        //        );

               
         $subName = date(YmdHis).time();
         $filename= $subName.'.xls';
         if(I('type') == 'excel'){
           create_xls($data,$filename);
         }
         
     }




  /***
   导入学员考勤信息
   */
  public function import(){
          if(IS_POST){
            // echo aa;
            // $options = I('post.attendance');
            $attendance_project_id=I('post.attendance_project_id');
		        $project_id=I('post.project_id');
           
            // $course_name=D('Attendance')->getCoursename($course_id);
            // $start_time = date('Y-m-d H:i:s', $start_time);
            
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 300*1024*1024 ;// 设置附件上传大小
            $upload->exts = array('xls','xlsx');// 设置附件上传类型
            $upload->rootPath = './Upload/file'; // 设置附件上传根目录
            $upload->savePath = 'excel/'; // 设置附件上传（子）目录
            $upload->autoSub = true;
            $upload->subName = '';
            $info = $upload->upload();// 上传文件
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                foreach($info as $v){
                    $file_path1 = './Upload/file'.$v['savepath'];
                    $file_path2 = $v['savename'];
                }
                $file = $file_path1.$file_path2;
                // echo $file;
                if(file_exists($file)){
                            
                    // echo 11;die;
                    $list = D('Attendance')->importAttendance($file);
                    $Model = D('Attendance');
                    // dump($list);die;
                    //返回回错误信息，并抛出错误信息
                    if(!$list){
                       $this->error($Model->getError()); 
                    }
                 
                    //更改考勤数据
                     $res=D('Attendance')->saveAttendance($project_id,$attendance_project_id,$list);
                     if($res !== false){ //save 若更改相同数据则返回0
     
                       $this->success('导入考勤成功！',U('Admin/attendance/index',array('attendance_project_id'=>$attendance_project_id,'project_id'=>$project_id)));
                     }
                  
                  }
            }
        }else{
            $this->error('非法数据提交');
        };
    }
    

  /***
   *考勤管理模块,list
   */
  public function attendanceManage(){
         $data = D('Teach')->teachList(2);
        
         $condition = I('course_name') ? I('course_name') : '';
	       $list = $data[0];
	  	   $show = $data[1];
         $arg = $data['arg'];
      	 $this->assign("lessons",$list);
	       $this->assign("condition",$condition);
	       $this->assign("show",$show);
         $this->assign("arg",$arg);
         $this->assign("type",2);  //type=2代表 考勤管理模块
         
         $this->display('Teach/index');


  }


     /***
       批量保存更新考勤信息
     */
    public function batchModify(){
         $res = D('Attendance')->batchModify();
         $this->ajaxreturn($res);
         
    }



     /***
       批量提交考勤-选择项目内考勤对哪几门课程生效
     */
    public function batchSubmit(){
         $res = D('Attendance')->batchSubmit();
         $this->ajaxreturn($res);
         
    }



     /***
     保存更新考勤信息
     */
    public function save(){
              $data=I("post.");
              $course_id=$data["course_id"];
              $course_name=$data["course_name"];
              $start_time=$data["start_time"];
              foreach($data as $k=>$v){
                $decide=substr($v,-1,1);
                if($decide=="a"){
                  $map["status"]=0;
                }elseif($decide=="l"){
                  $map["status"]=2;
                }else{
                  $map["status"]=1;
                }
                $map["id"]=$k;
                $res=D("Attendance")->update($map);
              }
              $statistics=D("Attendance")->salary($course_id,$project_id);//获取各种状态的考勤人数
              $list=D("Attendance")->lists($course_id);//获取学员考勤列表
              $this->assign("course_id",$course_id);
              $this->assign("statistics",$statistics);
              $this->assign("list",$list);
              $this->assign("start_time",$start_time);
              $this->assign("course_name",$course_name);
              $this->assign("id",$course_id);
              $this->display("index");
     }

}