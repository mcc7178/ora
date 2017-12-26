<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/**
 * 我的考试控制器
 * @Andy
 */
class MyExamController extends AdminBaseController{
    /*
     * 待考考试列表
     */
    public function waitExam(){
        //每页显示条数
        $total_page = $this->total_page;
        $approved_data = D('MyExam')->examList($total_page);
        // dump($approved_data);
       //获取模型返回数据
        $this->assign('approved_list',$approved_data['map']);
        //获取返回的分页数据
        $this->assign('page',$approved_data['page']);
       //记录搜索关键字
        $this->assign('keyword',$approved_data['keyword']); 
        $this->assign('type',$approved_data['type']); 
        $this->assign('status',$approved_data['status']); 
        $this->assign('isnew',0);
        $this->display();
    }
    
    /**
     * 其他考试-工具考试
     * @return [type] [description]
     */
    public function otherExam(){
        //每页显示条数
        $total_page = $this->total_page;
        $approved_data = D('MyExam')->examList2($total_page);
        $user_id = $_SESSION['user']['id'];
        // dump($approved_data['data']);
       //获取模型返回数据
        $this->assign('data',$approved_data['data']);
        //获取返回的分页数据
        $this->assign('page',$approved_data['page']);
       //记录搜索关键字
        $this->assign('keyword',$approved_data['keyword']);
        $this->assign('status',$approved_data['status']);
        $this->assign('type',$approved_data['type']);
        $this->assign('isnew',1);   //标记
        $this->assign('user_id',$user_id);
        $this->display();
    }
	
	/**
	 * 全部考试，项目考试+工具考试
	 */
	public function allExam(){
        $data = D('MyExam')->examList3();

        $user_id = $_SESSION['user']['id'];
        $this->assign('data',$data['data']);
        $this->assign('page',$data['page']);
        $this->assign('keyword',$data['keyword']); 
        $this->assign('type',$data['type']); 
        $this->assign('status',$data['status']); 
        $this->assign('isnew',2);   //标记
        $this->assign('user_id',$user_id);
        $this->display();
	}
    
    /*
     * 参加考试
     */
    public function joinExam(){
        $get = I('get.');
        
        $exam = D('MyExam');
		
        //已考试，跳转结果页面
        $user_id = $_SESSION["user"]["id"];
        if($get["flag"] == "flag" && $get["test_id"]){
        	//工具考试
        	$hasWhere["test_id"] = $get["test_id"];
        }else{
        	//项目考试
        	$hasWhere["project_id"] = $get["project_id"];
        }
        $hasWhere["exam_id"] = $get["examination_id"];
        $hasWhere["u_exam_id"] = $user_id;
        $hasJoin = M("exam_answer")->field("u_exam_id")->where($hasWhere)->find();
		
		
		$counter = $exam->getCounter($get);		//第几次参加考试
		$can = $exam->getCanTest($get);			//总共可以考试的次数
		
        if($hasJoin && $counter >= $can){
        	if($get["flag"] == "flag" && $get["test_id"]){
        		$seeUrl = __MODULE__."/my_exam/result/tid/".$get["test_id"]."/eid/".$get["examination_id"];
        	}else{
        		$seeUrl = __MODULE__."/my_exam/result/pid/".$get["project_id"]."/eid/".$get["examination_id"];
        	}
        	header("Location:".$seeUrl);
        	exit;
        }
        
        $flag = $get['flag'];
        $data = $exam->getExamInfo($get,$flag);
		
        if(!$data){
        	$this->success('该试题已逾期',U('admin/MyExam/waitexam',array('typeid'=>1)));
        	exit;
        }
		
        $times = $data['detail']['test_length'] * 60;
		if(strtotime($data['detail']['end_time']) - time() < $times){
			$times = strtotime($data['detail']['end_time']) - time();
		}

		$this->assign('counter',$counter + 1);
		$this->assign('can',$can);
        //详情
        $this->assign('xhr',$data['detail']);
//		dump($data);
        $this->assign('flag',$flag);
        $this->assign('times',$times);
    
        //单选
        $this->assign('singleChoice',$data['singleChoice']);
        $this->assign('singleChoiceSum',$data['singleChoiceSum']);
        $this->assign('singleChoiceTotalScore',$data['singleChoiceTotalScore']);
        //多选
        $this->assign('multipleChoice',$data['multipleChoice']);
        $this->assign('multipleChoiceSum',$data['multipleChoiceSum']);
        $this->assign('multipleChoiceTotalScore',$data['multipleChoiceTotalScore']);
        //判断
        $this->assign('descriPtive',$data['descriPtive']);
        $this->assign('descriPtiveChoiceSum',$data['descriPtiveChoiceSum']);
        $this->assign('descriPtiveChoiceTotalScore',$data['descriPtiveChoiceTotalScore']);

        //简答题
        $this->assign('wd',$data['wd']);
        $this->assign('wdSum',$data['wdSum']);
        $this->assign('wdTotalScore',$data['wdTotalScore']);

        $this->assign('test_id',$get['test_id']);
        $this->assign('project_id',$get['project_id']);
        $this->assign('examination_id',$data['detail']['examination_id']);

        $num = $data['singleChoiceSum'] + $data['multipleChoiceSum'] + $data['descriPtiveChoiceSum'] + $data['wdSum'];
        $this->assign('questionsNum',$num);
        $this->display();
    }
    
    /*
     * 处理考试提交的结果
     */
    public function handelExam()
    {
        if (IS_POST) {
            //接收提交考试的答案
            $post = I('post.');
            $post['user_id'] = $_SESSION['user']['id'];
            $ret = D('MyExam')->handelExamResult($post);

            $flag = I('post.flag');

            $backUrl = $flag ? 'otherexam' : 'waitexam';

            $tid = $post['test_id'];
            $eid = $post['examination_id'];
            $pid = $post['project_id'];
            
            if ($ret) {
                if($flag){
                    $url = U('result',array('eid'=>$eid,'flag'=>$flag,'tid'=>$tid));
                }else{
                    $url = U('result',array('eid'=>$eid,'pid'=>$pid));
                }
				
				if($post['counter'] < $post['can']){
					$url = U('allexam');
				}
                $this->success('提交成功',$url);
            } else {
                $this->error('不能重复提交',$backUrl);
            }
        }
    }

    /**
     * 考试结果页面
     */
    public function result(){
        $eid = I('get.eid');
        $tid = I('get.tid');
        $pid = I('get.pid');
        $user_id = $_SESSION['user']['id'];
		
        //最高得分的考试次数是第几次考试
        $max = D('MyExam')->getMaxScoreNum($eid,$tid,$pid);
        $res = D('ResourcesManage')->getExamDetail($eid,$tid,$pid,$max);

        for($i=1;$i<=4;$i++){
            foreach($res['type'.$i] as $k=>$v){
                $res['type'.$i][$k]['url'] = U("getExamAnswer#".$v['id'],array('tid'=>$tid,'eid'=>$eid,'pid'=>$pid));
            }
        }

        $this->assign('max',$max);
        $this->assign('eid',$eid);
        $this->assign('user_id',$user_id);
        $this->assign('tid',$tid);
        $this->assign('pid',$pid);
        $this->assign('data',$res);
        $this->display();
    }

    /**
     * 获取试题解析
     */
    public function getAnalysis(){
        $info = M('examination_item')->where(array('id'=>I('get.id')))->getField('analysis');
        $info = $info ? $info : '暂无解析';
        $this->ajaxReturn(array('info'=>$info));
    }

    //获取考试结果  项目考试+工具考试
    public function getExamAnswer(){
        $data = I('get.');
        $max = D('MyExam')->getMaxScoreNum($data['eid'],$data['tid'],$data['pid']);
        $res = D('ResourcesManage')->getExamDetail($data['eid'],$data['tid'],$data['pid'],$max);

        $this->assign('eid',$data['eid']);
        $this->assign('tid',$data['tid']);
        $this->assign('pid',$data['pid']);
        $this->assign('url',U('result',array('eid'=>$data['eid'],'tid'=>$data['tid'],'pid'=>$data['pid'],'max'=>$max)));
        $this->assign('data',$res);
        $this->display();
        
    }

    /**
     * 查看线下考试结果
     */
    public function lookResultOffline(){
        $data = D('ResourcesManage')->getExamDetail(I('get.eid'),I('get.tid'),I('get.pid'));
        
        $this->assign('data',$data);
        $this->display();
    }

}