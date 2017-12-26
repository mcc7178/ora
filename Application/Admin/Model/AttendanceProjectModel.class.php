<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 项目考勤model
 */
class AttendanceProjectModel extends BaseModel{


    /**
     *项目考勤
     */
 	public function getIndex(){
        
         
         $keyword = I('table_search') ? I('table_search') : '';
         $size=15;
         $p = I('get.p') ? I('get.p') : 1 ;
      	//根据登陆者查找相关的授课，项目关联的课程的讲师具有授课资格
         $user_id=$_SESSION["user"]["id"];
        // 为考勤管理
            $where = array(
                //将所有的课程考勤的列表数据取出来
                'b.manager_id'=>$user_id, //只有负责人可查看考勤
                'a.type'=>array('in','0,4'),
                'c.course_way'=>1,
                'a.project_name' =>array('like',"%".$keyword."%")
             ); 
         
         
		if(strtolower(C('DB_TYPE')) == 'oracle'){

            $list = M('admin_project a')
                     ->field('a.id,a.project_name')
                     ->distinct('a.project_name') 
                     ->join('left join __PROJECT_COURSE__ b on a.id=b.project_id')
                     ->join('left join __COURSE__ c on b.course_id=c.id')
                     ->where($where)
                     ->page($p, $size)
		       	     ->select();
            
		}else{
			

            $list = M('admin_project a')
                     ->distinct('a.project_name')
                     ->field('a.id,a.project_name')
                     ->join('left join __PROJECT_COURSE__ b on a.id=b.project_id')
                     ->join('left join __COURSE__ c on b.course_id=c.id')
                     ->where($where)
                     ->order('b.start_time desc')
                     ->page($p, $size)
		       	     ->select();
		}

        
        
        $count  = count($list);
        
        foreach($list as $k=>$v){
            //获取项目的课程总数
           $course_list=D("Attendance")->getAttendanceCourse($v['id']);
           $coursecount = count($course_list); 

           //获取项目的已考勤课程数
           $alreadycount = $this->getAlreadyAttendanceCourse($v['id']);
           $list[$k]['rate'] = $alreadycount.'/'.$coursecount;

        }
        
       
     $show = $this->pageClass($count,$size);
     $data = array(
        '0'=>$list,
        '1'=>$show,
        '2'=>$keyword
         );
     return $data;
     }


    /**
     *获取项目的已考勤课程数
     */
 	public function getAlreadyAttendanceCourse($project_id){

          $where = array(
              'b.project_id'=>$project_id
          );
          $count = M('attendance_course a')  
                     ->field('a.id,a.project_name')
                     ->join('left join __ATTENDANCE_PROJECT__ b on b.id=a.attendance_project_id')
                     ->where($where)
		       	     ->count();

         return $count;
     }
    /**
     *项目内考勤展示页
     */
 	public function getProjectAttendanceList(){
        
        $size = 15;
	    $p = I("get.p",0,'int');
        $keyword = I('table_search'); 
        $condition = array(
            'attendance_name'=>array("like","%".$keyword."%"),
        ); 

        $project_id = I('get.project_id')+0;
        $project_name = M('admin_project')->where(array('id'=>$project_id))->getField('project_name');
        
              
        if(strtolower(C('DB_TYPE')) == 'oracle'){
           $list = M('attendance_project')
                ->field("id,project_id,attendance_name,status,user_id,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,to_char(add_time,'YYYY-MM-DD HH24:MI:SS') as add_time")
                ->where(array('project_id'=>$project_id))
                ->where($condition)
                ->page($p.','.$size)
                ->select();
        }else{
            $list = M('attendance_project')
                ->where(array('project_id'=>$project_id))
                ->where($condition)
                ->page($p.','.$size)
                ->select();

        }

        $count = M('attendance_project')
                ->where(array('project_id'=>$project_id))
                ->where($condition)
                ->count();


        $show = $this->pageClass($count,$size);
        $data = array(
            'list'=>$list,
            'page'=>$show,
            'project_name'=>$project_name,         
            'keyword'=>$keyword
        );
        return $data;
     }

    /**
     * 项目内考勤生效的课程列表
     */
    public function alreadyAttendanceCourse()
    {

        $size = 15;
        $p = I("get.p",0,'int');
        $keyword = I('table_search'); 

        $condition = array(
            'b.course_name'=>array("like","%".$keyword."%"),
        ); 
       
        $attendance_project_id = I('get.attendance_project_id')+0;
         
        $project_id = I('get.project_id')+0;
        $project_name = M('admin_project')->where(array('id'=>$project_id))->getField('project_name');
        $attendance_name = M('attendance_project')->where(array('id'=>$attendance_project_id))->getField('attendance_name');


        $list = M('attendance_course')->alias('a')
                      ->join('left join __COURSE__ b on a.course_id=b.id')
                      ->join('left join __LECTURER__ c on b.lecturer=c.id')
                      ->where(array('a.attendance_project_id'=>$attendance_project_id))
                      ->where($condition)
                      ->page($p.','.$size)
                      ->field("a.attendance_project_id,b.course_name,c.name,b.start_time,b.end_time,b.course_time,b.credit")
                      ->select();
     
         
         $count = M('attendance_course')->alias('a')
                      ->join('left join __COURSE__ b on a.course_id=b.id')
                      ->join('left join __LECTURER__ c on b.lecturer=c.id')
                      ->where(array('a.attendance_project_id'=>$attendance_project_id))
                      ->where($condition)
                      ->count();

       
        $show = $this->pageClass($count,$size);
        
        


        $data = array(
            'list'=>$list,
            'page'=>$show,
            'project_name'=>$project_name, 
            'attendance_name'=>$attendance_name,         
            'keyword'=>$keyword
        );
        return $data;






    }
    /**
     *项目内创建考勤
     */
 	public function createAttendance($data){
         
       $res =  M('attendance_project')->add($data);
       return $res;
         

     }


    /**
     *项目内删除考勤
     */
 	public function delAttendance($id){

         echo $id;
         $res =  M('attendance_project')->where(array('id'=>$id))->delete();
         return $res;


     }

}