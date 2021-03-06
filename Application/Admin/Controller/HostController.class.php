<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/**
 * 主持考试控制器
 */
class HostController extends AdminBaseController{
	
	public static $num = 0;
	/***
	显示主持考试列表
	*/
	public function index(){

		$total_page = $this->total_page;
	    //根据当前用户身份获取指定负责人所主持的调研
	    $approved_data = D('HostExamination')->getProjectExamination($total_page);
	    $this->assign('approved_list',$approved_data['list']);
	    $this->assign('approved_page',$approved_data['page']);
	    $this->assign('keyword',$approved_data['keyword']);
	    $this->display();
	    
	}
    
	
	/*
	 * 预览考试
	 */
	public function previewExamination(){
		$user_id = session('user.id');	//用户id
		$pid = I('get.pid');			//项目id
		$eid = I('get.id');				//试卷id
		
		if($eid == 0){
			$data = M('project_examination a')
					->join('LEFT JOIN __ADMIN_PROJECT__ b on a.project_id=b.id')
					->where(array('a.project_id'=>$pid,'a.test_id'=>0))
					->field('a.start_time,a.end_time,a.examination_address,a.test_names,b.project_name')
					->find();
			$this->assign('data',$data);
			$this->display('previewonline');
		}else{
			$res = D('ResourcesManage')->getExamDetail2($eid,$user_id,false,$pid);//根据试卷id获取试卷信息
			//单选
	        $this->assign('singleChoice',$res['singleChoice']);
	        $this->assign('singleChoiceSum',$res['singleChoiceSum']);
	        $this->assign('singleChoiceTotalScore',$res['singleChoiceTotalScore']);
	        //多选
	        $this->assign('multipleChoice',$res['multipleChoice']);
	        $this->assign('multipleChoiceSum',$res['multipleChoiceSum']);
	        $this->assign('multipleChoiceTotalScore',$res['multipleChoiceTotalScore']);
	        //判断
	        $this->assign('descriPtive',$res['descriPtive']);
	        $this->assign('descriPtiveChoiceSum',$res['descriPtiveChoiceSum']);
	        $this->assign('descriPtiveChoiceTotalScore',$res['descriPtiveChoiceTotalScore']);
	
	        //问答
	        $this->assign('wd',$res['wd']);
	        $this->assign('wdSum',$res['wdSum']);
	        $this->assign('wdTotalScore',$res['wdTotalScore']);
			
		    //详情
	        $this->assign('xhr',$res['detail']);
	
		    $this->display('lookexamination');
		}
	}

	/**
	*查看考试结果
	**/
	public function lookExaminationResult(){
		$data=I("get.");
        
		$total_page = $this->total_page;
		// $info=D("ExamScore")->getAllExaminationResults($total_page,$data);

		//计算应考人数
		// $number=D('DesignatedPersonnel')->counts($data["project_id"]);
		$info = D("ExamScore")->getAllExaminationResults2($total_page,$data);
//		dump($info['list']);
		$number = $info['total'];

		$data["status"]=0;
		//考试实到人数
		$real = D("ExamScore")->getAllExamNum($info['project_id'],$info['test_id']);
		$absence = $number - $real;

		$is_publish = D("ExamScore")->getPublishStatus($info['project_id'],$info['test_id']);//发布状态
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
		}else{
			$field = "end_time";
		}
		$end_time = M('project_examination')->where(array('project_id'=>$data['project_id'],'test_id'=>$data['exam_id']))->field($field)->find();
		if(strtotime($end_time['end_time']) <= time()){
			$finish = 1;
		}else{
			$finish = 0;
		}
		
		$this->assign("absence",$absence);
		$this->assign("finish",$finish);
		$this->assign("number",$number);
		$this->assign("test_name",$info['test_name']);
		$this->assign("test_id",$info['test_id']);
		$this->assign("project_id",$info['project_id']);
		$this->assign("show",$info["page"]);
		$this->assign("isPublish",$is_publish);
		$this->assign("information",$info["list"]);

		if(!isset($data["test_mode"])){
			$this->display("lookresultonline");
		}else{
			$this->display("lookresultoffline");
		}
		
	}

	
	
	/*
	 * 查看考试答案
	 */
	public function checkExaminationAnswer(){
	    
	    $model = D('MyExam');
        $data = I('get.');

        $pid = $data['pid'];         	//项目id
        $eid = $data['eid'];     		//试卷id
        $user_id = $data['user_id'];    //用户id
        $max = I('get.counter');		//第几次
        $res = D('ResourcesManage')->getExamDetail($eid,false,$pid,$max,$user_id);
// dump($res);
		
		$this->assign('data',$res);
		$this->assign('eid',$eid);
		$this->assign('pid',$pid);
		$this->assign('user_id',$user_id);
		$this->assign('max',$max);
        $this->display();
	}
	/**
	*发布考试成绩
	**/
	public function publish(){
		$res=D("ExamScore")->publish();
		if($res!==false){
			$this->ajaxReturn(array('status'=>1));
		}else{
			$this->ajaxReturn(array('status'=>0));
		}
	}
	/**
	*更改考试成绩
	**/
	public function save(){
		$data=I("post.");
		
		$exam_id = I('post.exam_id') ? I('post.exam_id') : '0';
		$ids = array_unique($data['user_id']);
		unset($data['user_id']);
		$arr["test_mode"]=array_pop($data);
		$arr["exam_id"]=array_pop($data);
		$arr["project_id"]=array_pop($data);
		unset($data['project_id']);
		
		foreach($data as $k=>$v){
			$res=D("ExamScore")->saveScore($k,$v,I('post.project_id'),$exam_id,$ids[self::$num]);
			++self::$num;
		}

		$this->success('更改成功');
	}


  	/***
   	导入学员考勤信息
   	*/
  	public function import(){
        if(IS_POST){
            $options = I('post.examattendance');
            $exam_id=I('post.exam_id');
            $project_id=I('post.project_id');
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
				if(file_exists($file)){
                    $list = D('ExaminationAttendance')->importAttendance($file);

                    if($list === false){
                    	$this->error('模板格式错误,请重新选择模板文件');
                    }
                    
					foreach($list as $k=>$v){
                      	if(isset($v['status'])){
                        	$this->error($v["message"]);
                      	}
                      	$map["user_id"]=$v["user_id"];
                      	$map["total_score"]=$v["total_score"];
                      	$map["project_id"]=$project_id;
                      	$map["exam_id"]=$exam_id;
                      
                      	$res=D('ExamScore')->importScore($map);
					}
                   	$this->success('导入成功');
                  }
            }
        }else{
            $this->error('非法数据提交');
        };
  	}

    /**
     * 人工阅卷
     * @return [type] [description]
     */
    public function saveWdScore(){
        $model = D('MyExam');
        $res = $model->changeWdScore();
        if($res){
            $this->ajaxReturn(array('status'=>1));
        }else{
            $this->ajaxReturn(array('status'=>0));
        }
    }
	
	/**
	 * 导出考试成绩
	 */
	public function export(){
		$type = I('get.type') == 1 ? '所有成绩' : '最高成绩';
        $data = D('ExamScore')->export();
        $xlsName  = "考试成绩(".$type.")-".date('Y-m-d H:i:s');

        array_unshift($data,array('姓名','部门','职位','参与状态','次数','得分','提交时间'));
        create_xls($data,$xlsName);
	}
}
