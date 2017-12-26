<?php

namespace Admin\Controller;

use Common\Controller\AdminBaseController;
/*
 * 资源管理控制器
 * @Andy 
 */
class ResourcesManageController extends AdminBaseController{
    
    
    /*
     * 课程管理列表
     */
    public function courseManage(){
        
        $this->display();
    } 
    
    /*
     * 下载试卷模板
     */
    public function downloadExam(){
       	
		$uploadpath="./Upload/exam/";

		$saveName="exam.xls";//文件保存名

		$showName="exam.xlsx";//文件原名
        
		$filename=$uploadpath.$saveName;//完整文件名（路径加名字）

		file_download($filename);
    }
    
    /*
     * 下载试卷模板
     */
    public function downloadSurvey(){
       
        	
        $uploadpath="./Upload/survey/";
    
        $saveName="survey.xls";//文件保存名
    
        $showName="survey.xls";//文件原名
    
        $filename=$uploadpath.$saveName;//完整文件名（路径加名字）
    
        file_download($filename);
    }
 
       

    /*
     * 新增试卷分类
     */
    public function addExamclassify(){
        $plan_id = getPlanId();
        if(!$plan_id){
            $this->ajaxReturn(array('status'=>0,'msg'=>'本部门尚未添加配置方案,请添加后重试!'));
        }

        $res = D('ResourcesManage')->addTestClass();
        $this->ajaxReturn($res);
    }

    //编辑试卷分类
    public function editTestClass(){
        $res = D('ResourcesManage')->editTestClass();
        $this->ajaxReturn($res);
    }

    /**
     * 删除试卷分类
     */
    public function delTestCategory(){
        $results = D('ResourcesManage')->delTestCategory();
        $this->ajaxReturn($results);
    }
    
    /*
     * 导入试卷
     */
    public function importExam(){
        if(IS_POST){
            $options = I('post.');

            /*if(!$options['importExam']){
                $this->error('请选择试卷分类!');
            }*/
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 300*1024*1024 ;// 设置附件上传大小
            $upload->exts = array('xls','xlsx');// 设置附件上传类型
            $upload->rootPath = './Upload/'; // 设置附件上传根目录
            $upload->savePath = 'excel/'; // 设置附件上传（子）目录
            $upload->autoSub = true;
            $upload->subName = '';
            // 上传文件
            $info = $upload->upload();
            
            if(!$info) {// 上传错误提示错误信息 
                $this->error($upload->getError());
            }else{// 上传成功
                foreach($info as $v){
                    $file_path1 = './Upload/'.$v['savepath'];
                    $file_path2 = $v['savename'];
                }
                $file = $file_path1.$file_path2;
                if(file_exists($file)){
                    $list = D('ResourcesManage')->uploadExam($file);
					
                    if($list === false){
                        $this->error('试卷格式错误或有必填项为空,请重新填写!',U('passExam'));
                    }

                    $orderno =  D('Trigger')->orderNumber(3);

                    $arr['test_heir'] = $_SESSION['user']['username'];
                    $arr['test_upload_time'] = date("Y-m-d H:i:s",time());
                    $arr['test_cat_id'] = $options['importExam'];
                    $arr['test_name'] = $list['test_name'];
                    $arr['test_score'] = $list['totalscore'];
                    $arr['status'] = 0;
                    $arr['is_available'] = 1;
                    $arr['principal'] = $_SESSION['user']['username'];
                    $arr['type'] = 1;
                    $arr['orderno'] = $orderno['no'];
					if($orderno['status'] == 0){
                        $arr['status'] = 1;
                    }

					if(strtolower(C('DB_TYPE')) == 'oracle'){
						$arr['id'] = getNextId('examination');
						$arr['test_upload_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
					}
					$arr['auth_user_id'] = session('user.id');
					
                    $ret = M('Examination')->add($arr);
					
                    if($ret){
                        foreach($list['a'] as $k => $v){
                            // $msg[$k]['examination_id'] = $ret;//试卷id
                            $msg['title'] = $v['title'];
                            $msg['right_option'] = trim(str_replace('，',',',$v['right_option']));
                            $msg['classification'] = $v['classification'];
                            $msg['ctime'] = date("Y-m-d H:i:s",time());
                            $msg['creater_id'] = $_SESSION['user']['id'];
                            $msg['keywords'] = trim(str_replace('，',',',$v['keywords']));
                            $msg['analysis'] = $v['analysis'];
                            $msg['belongcourse'] = $options['importExam'];
							
							if(strtolower(C('DB_TYPE')) == 'oracle'){
								$msg['id'] = getNextId('examination_item');
								$msg['ctime'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
							}
							
                            $res = M('ExaminationItem')->add($msg);//试题id

                            //试卷试题关联表
                            if($res){
                                $info['examination_id'] = $ret;
                                $info['examination_item_id'] = $res;
                                $info['score'] = $v['item_score'];
                                $res2 = M('examination_item_rel')->add($info);
                                if(!$res2){
                                    $this->error('导入失败',U('passExam'));
                                }
								
								//试题选项处理
								foreach($v as $kk=>$vv){
									if(substr($kk,0,6) == 'option'){
										$opt_data = array('item_id'=>$res);
										
										$opt_data['letter'] = strtoupper(substr($kk,-1,1));
										$opt_data['content'] = $vv;
										if(strtolower(C('DB_TYPE')) == 'oracle'){
											$opt_data['id'] = getNextId('examination_item_opt');
										}
										$res3 = M('examination_item_opt')->add($opt_data);
										if(!$res3){
											$this->error('导入失败',U('passExam'));
										}
									}
								}
                            }else{
                                $this->error('导入失败',U('passExam'));
                            }
                        }
                    }else{
                        $this->error('导入失败',U('passExam'));
                    }
					//写入日志
					write_login_log(2,2,$list['test_name']);
                    $this->success('导入成功',U('passExam'));
                }
            }
    
        }else{
            $this->error('非法数据提交',U('passExam'));
        }
    
    }
    /*
     * 已通过的试卷列表
     */
    public function passExam(){
        M('examination_temp')->where(array('user_id'=>session('user.id')))->delete();
        //每页显示条数
        $total_page = $this->total_page;
        $approved_data = D('ResourcesManage')->passExamList($total_page);
		
        $cate = D('ResourcesManage')->getExamCate(array('plan_id'=>getPlanId()));
        $this->assign('category',$cate);
        $this->assign('approved_list',$approved_data['list']);
        $this->assign('approved_page',$approved_data['page']);
        $this->assign('data',$approved_data['xhr']);
        //搜索项
        $this->assign('keyword',$approved_data['keyword']);
        $this->assign('cate',$approved_data['cate']);
        $this->assign('heir',$approved_data['heir']);
        $this->display();
    }
    
    /*
     * 自定义删除试卷列表数据
     */
    public function del_all(){
        $post = (I('post.id'));
		$name = M('examination')->where(array('id'=>$post))->getField('test_name');
        $res = M('Examination')->where(array('id'=>$post))->delete();
        $res2= M('resource_sharing')->where(array('source_id'=>$post,'type'=>2))->delete();
		
        if($res!== false && $res2 !== false){
			//写入日志
			write_login_log(2,4,$name);
			$this->ajaxReturn(1);
        }else{
			$this->ajaxReturn(0);
        }
    }
    /*
     * 待审核的试卷列表
     */
    public function auditExam(){
        M('examination_temp')->where(array('user_id'=>session('user.id')))->delete();
        //每页显示条数
        $total_page = $this->total_page;
        $approved_data = D('ResourcesManage')->auditExamList($total_page);

        $cate = D('ResourcesManage')->getExamCate(array('plan_id'=>getPlanId()));

        $this->assign('category',$cate);

        $this->assign('approved_list',$approved_data['list']);
        $this->assign('approved_page',$approved_data['page']);
        $this->assign('data',$approved_data['xhr']);
        $this->assign('keyword',$approved_data['keyword']);
        $this->display();
    }
    
    /*
     * 已拒绝的试卷列表
     */
    public function refusedExam(){
        M('examination_temp')->where(array('user_id'=>session('user.id')))->delete();
        //每页显示条数
        $total_page = $this->total_page;
        $approved_data = D('ResourcesManage')->refusedExamList($total_page);

        $cate = D('ResourcesManage')->getExamCate(array('plan_id'=>getPlanId()));

        $this->assign('category',$cate);

        $this->assign('approved_list',$approved_data['list']);
        $this->assign('approved_page',$approved_data['page']);
        $this->assign('data',$approved_data['xhr']);
        $this->assign('keyword',$approved_data['keyword']);
        $this->display();
    }
    
    /*
     * 删除已拒绝审核的试卷
     */
    public function DeleteForbiddenExam(){
        if(IS_POST){
            $id = I('post.eid');
            $res = D('ResourcesManage')->examDelete($id);
            !$res and $this->ajaxReturn(0);
            $this->ajaxReturn(1);
        }
    }
    
    
    /**
     * 试卷详情
     */
    public function examDestail(){
        $id = I('get.id');
        $_SESSION['exam_id'] = $id;
        $exam = D('ResourcesManage');
        $data = $exam->getExamDetail2($id,session('user.id'));
		
        $_SESSION['exam_name'] = $data['detail']['test_name'];
        //详情
        $this->assign('xhr',$data['detail']);
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

        //问答
        $this->assign('wd',$data['wd']);
        $this->assign('wdSum',$data['wdSum']);
        $this->assign('wdTotalScore',$data['wdTotalScore']);
        
        $this->assign('refused',I('get.refused'));
        $this->assign('status',I('get.status'));
        $this->display();
        
    }
    
    /*
     * 试卷统计
     */
    public function examCount(){
        $exam = D('ResourcesManage');
        $id = I('get.id');
        
        $data = $exam->getExamInfo($id);
        // dump($data);
        $status = $exam->getExaminationStatus($id);

        $this->assign('name',session('exam_name'));
        $this->assign('id',session('exam_id'));
        $this->assign('data',$data);
        $this->assign('status',$status);
        $this->display();
    }
    
    /*
     * 已通过的问卷列表
     */
    public function passQuestionNaire(){
        //每页显示条数
        $total_page = $this->total_page;
        $item = M("SurveyCategory")->select();
        $approved_data = D('ResourcesManage')->passQuestionNaireList($total_page);
        $this->assign('list',$item);
        $this->assign('approved_list',$approved_data['list']);
        $this->assign('approved_page',$approved_data['page']);
        $this->assign('data',$approved_data['xhr']);
        $this->assign('keyword',$approved_data['keyword']);
        $this->display();
    }

    /**
     * 分类管理
     */
    public function classManagement(){

        $data = D('ResourcesManage')->classManagement();

        $this->assign("items",$data);
        $this->display();
    }

    /**
     * 试卷分类管理
     * @return [type] [description]
     */
    public function testClassManagement(){
        $data = D('ResourcesManage')->testClassManagement();

        $this->assign("items",$data);
        $this->display();
    }
    
    /*
     * 待审核的问卷列表
     */
    public function auditQuestionNaire(){
        //每页显示条数
        $total_page = $this->total_page;
        $approved_data = D('ResourcesManage')->auditQuestionNaireList($total_page);

        $this->assign('approved_list',$approved_data['list']);
        $this->assign('approved_page',$approved_data['page']);
        $this->assign('data',$approved_data['xhr']);
        $this->assign('keyword',$approved_data['keyword']);
        $this->display();
    }
    
    /*
     * 已拒绝的问卷列表
     */
    public function refusedQuestionNaire(){
        //每页显示条数
        $total_page = $this->total_page;
        $approved_data = D('ResourcesManage')->refusedQuestionNaireList($total_page);
        $this->assign('approved_list',$approved_data['list']);
        $this->assign('approved_page',$approved_data['page']);
        $this->assign('data',$approved_data['xhr']);
        $this->assign('keyword',$approved_data['keyword']);
        $this->display();
    }
    /*
     * 问卷管理详情
     */
    public function questionNaireDetail(){
        $id = I('get.id');
        $_SESSION['questionNaire_id'] = $id;
        $questionNaire = D('ResourcesManage');
        $data = $questionNaire->getQuestionNaireDetail($id);
        session('questionNaire_name',$data['detail']['survey_name']);
        
        $this->assign('base',$data['base']);//详情
        $this->assign('list',$data['list']);//题目
        $this->assign('refused',I('get.refused'));
        $this->assign('status',I('get.status'));
        $this->display();
    }
    
    /*
     * 问卷统计
     */
    public function questionNaireCount(){
        $model = D('ResourcesManage');
        $id = I('get.id');

        $data = $model->getQUestionNaireInfo($id);
        $status = $model->getSurveyStatus($id);

        $this->assign('id',$_SESSION['questionNaire_id']);
        $this->assign('name',session('questionNaire_name'));
        $this->assign('data',$data);
        $this->assign('status',$status);
        $this->display();
    }
    /*
     * 问卷禁用
     */
    public function questionNaireForbidden(){
        if(IS_AJAX){
            $id = I('post.did');
            $res = M('Survey')->where(array('id'=>$id))->setField('is_available','0');
            !$res and $this->ajaxReturn(0);
            $this->ajaxReturn(1);
        }
    }
    
    
    /*
     * 问卷启用
     */
    public function questionNaireOpen(){
        if(IS_AJAX){
            $id = I('post.did');
            $res = M('Survey')->where(array('id'=>$id))->setField('is_available','1');
            !$res and $this->ajaxReturn(0);
            $this->ajaxReturn(1);
        }
    }
    
    /**
     * 单个删除问卷
     */
    public function delSurvey(){
    	$post = I('post.');
    	$post["survey_id"] += 0;
    	if(!is_int($post["survey_id"]) || $post["survey_id"] < 1){
    		exit(json_encode(array("code"=>1011, "message"=>"请提交问卷id")));
    	}
    	
    	$resp = D('ResourcesManage')->delSurvey($post["survey_id"]);
    	if($resp["code"] != 1000){
    		exit(json_encode(array("code"=>1012, "message"=>$resp["message"])));
    	}else{
    		exit(json_encode(array("code"=>1000, "message"=>"成功")));
    	}
    }
    
    /*
     * 批量删除问卷
     */
    public function del_alls(){
      
            $post = (I('post.id'));
            $res = M('Survey')->where(array('id'=>array('in',$post)))->delete();
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }  
    }
    
    /*
     * 导入问卷--废弃
     */
    public function importQuestionNaire(){}
    
    /*
     * 添加问卷分类
     */
    public function questionNaireStyle(){

        $results = D('ResourcesManage')->addClass();

        $data = array(
            "status"=> $results,
        );

        $this->ajaxReturn($data,'json');
 
    }

    /**
     * 删除问卷
     */
    public function delCategory(){

        $results = D('ResourcesManage')->delCategory();

        $data = array(
            "status"=> $results,
        );

        $this->ajaxReturn($data,'json');

    }

    /*
    * 智能组卷页面展示
     */
    public function smartExam(){
        $model = D('ResourcesManage');
        $cate = $model->getExamCate();  //试卷分类
        $count = $model->getExamNum();

        //课程
        $where['auditing'] = 1;
        $where['status'] = 1;
        $course= D('Course')->getAllCourse($where);

        $this->assign('cate',$cate);
        $this->assign('count',$count);
        $this->assign('course',$course);
        $this->display();
    }
	
	/*
    * 智能组卷页面展示2
     */
    public function smartExam2(){
//  	M('examination_temp')->where(array('user_id'=>session('user.id')))->delete();
		$url = array(U('passexam','','',true),U('auditexam','','',true),U('refusedexam','','',true));
		/*if(in_array($_SERVER['HTTP_REFERER'],$url)){
			M('examination_temp')->where(array('user_id'=>session('user.id')))->delete();
		}*/
        $model = D('ResourcesManage');
        $cate = $model->getExamCate(array('plan_id'=>getPlanId()));  //试卷分类
        $count = $model->getExamNum();
		$data = $model->getTempExam();
		$scoreInfo = $model->getNumInfo();
		$baseinfo = $model->getBaseInfo();
		$bank = D('QuestionBank')->getAllBank();
		$examinationInfo = D('ResourcesManage')->getExamNum();
		
        //课程
        $where['auditing'] = 1;
        $where['status'] = 1;
        $course= D('Course')->getAllCourse($where);
		
		$this->assign('bank',$bank);
		$this->assign('examinationInfo',$examinationInfo);
		$this->assign('data',$data['data']);
		$this->assign('page',$data['page']);
		$this->assign('scoreInfo',$scoreInfo);
        $this->assign('cate',$cate);
        $this->assign('count',$count);
        $this->assign('course',$course);
        $this->assign('baseinfo',$baseinfo);
        $this->display();
    }
	
    /**
     * 智能组卷-添加题目
     */
	public function add_examination_item(){
		$model = D('ResourcesManage');
		$count = $model->getExamNum();
        //课程
		$specifiedUser = D('IsolationData')->specifiedUser(false);
		$where['auth_user_id'] = array('in',$specifiedUser);
        $where['auditing'] = 1;
        $where['status'] = 1;
        $course= D('Course')->getAllCourse($where);

        //分享给我的课程
		$course2 = $model->getSharingCourse();
        $course2 = $course2 ? $course2 : array();
        $course = array_merge($course,$course2);

        foreach($course as $k=>$v){
            $temp[] = $v['course_name'];
        }

		//试题库
        $where2['auth_user_id'] = array('in',$specifiedUser);
		$question_bank = M('question_bank')->where($where2)->select();

        //分享给我的试题库
        $question_bank2 = $model->getSharingQb();
        $question_bank2 = $question_bank2 ? $question_bank2 : array();
        $question_bank = array_merge($question_bank,$question_bank2);

        //去重
        $temp = array();
        foreach($question_bank as $k=>$v){
            if(!isset($temp[$v['id']])){
                $temp[$v['id']] = $v;
           }
        }
        $temp = array_values($temp);

        $this->assign('question_bank',$temp);
        $this->assign('course',$course);
        $this->assign('count',$count);
		$this->display();
	}

    public function array_unique_m($data){
        foreach($data as $k=>$v){
            $ids[] = $v['id'];
        }

    }
	
	/**
	 * 删除试题临时数据
	 */
	public function del_temp(){
		$data = D('ResourcesManage')->del_temp();
		$this->ajaxReturn($data);
	}

	public function getExamNumBy(){
		$res = D('ResourcesManage')->getExamNumBy();
		$this->ajaxReturn($res);
	}
	
	//智能组卷表单处理
	public function formHandle(){
		D('ResourcesManage')->formHandle();
	}
	
	/**
	 * 预览试卷-临时表
	 */
	public function preview_temp(){
		$data = D('ResourcesManage')->getTempData();

		//单选
        $this->assign('singleChoice',$data['dan-info']);
        $this->assign('singleChoiceSum',$data['dan-num']);
        $this->assign('singleChoiceScore',$data['dan-fen']);
        $this->assign('singleChoiceTotalScore',$data['dan-num'] * $data['dan-fen']);
        //多选
        $this->assign('multipleChoice',$data['duo-info']);
        $this->assign('multipleChoiceSum',$data['duo-num']);
        $this->assign('multipleChoiceScore',$data['duo-fen']);
        $this->assign('multipleChoiceTotalScore',$data['duo-num'] * $data['duo-fen']);
        //判断
        $this->assign('descriPtive',$data['pan-info']);
        $this->assign('descriPtiveChoiceSum',$data['pan-num']);
        $this->assign('descriPtiveChoiceScore',$data['pan-fen']);
        $this->assign('descriPtiveChoiceTotalScore',$data['pan-num'] * $data['pan-fen']);

        //问答
        $this->assign('wd',$data['jian-info']);
        $this->assign('wdSum',$data['jian-num']);
        $this->assign('wdScore',$data['jian-fen']);
        $this->assign('wdTotalScore',$data['jian-num'] * $data['jian-fen']);

//      $this->assign('examname',$data['examname']);
//      $this->assign('examcate',$cate[0]['cat_name']);
        $this->assign('totalScore',$data['totalScore']);
        $this->display('preview');
		
	}

	public function save_temp(){
		$data = D('ResourcesManage')->save_temp();
		$this->ajaxReturn($data);
	}
	
	public function del_temp_data(){
		M('examination_temp')->where(array('user_id'=>session('user.id')))->delete();
	}
    /**
     * 预览试卷
     * @return [type] [description]
     */
    public function preview(){
        $data = I('post.');
        $model = D('ResourcesManage');
        $exams = $model->randomExam($data);
        
        $exams = $model->randomExam($data);
        //预览试卷后自动存session,提交表单后就不再重复查询数据库
        //防止预览到的试题与实际提交的试题不一致 或 多次预览到的试题不一致
        session('dan',$exams['dan']['ids']);
        session('duo',$exams['duo']['ids']);
        session('pan',$exams['pan']['ids']);
        session('jian',$exams['jian']['ids']);
        session('exams',$exams);
        session('formdata',$data);
        

        $cate = $model->getExamCate(array('id'=>$data['examcate']));
        //单选
        $this->assign('singleChoice',$exams['dan']['info']);
        $this->assign('singleChoiceSum',$data['dan']);
        $this->assign('singleChoiceScore',$data['dan-fen']);
        $this->assign('singleChoiceTotalScore',$data['dan'] * $data['dan-fen']);
        //多选
        $this->assign('multipleChoice',$exams['duo']['info']);
        $this->assign('multipleChoiceSum',$data['duo']);
        $this->assign('multipleChoiceScore',$data['duo-fen']);
        $this->assign('multipleChoiceTotalScore',$data['duo'] * $data['duo-fen']);
        //判断
        $this->assign('descriPtive',$exams['pan']['info']);
        $this->assign('descriPtiveChoiceSum',$data['pan']);
        $this->assign('descriPtiveChoiceScore',$data['pan-fen']);
        $this->assign('descriPtiveChoiceTotalScore',$data['pan'] * $data['pan-fen']);

        //问答
        $this->assign('wd',$exams['jian']['info']);
        $this->assign('wdSum',$data['jian']);
        $this->assign('wdScore',$data['jian-fen']);
        $this->assign('wdTotalScore',$data['jian'] * $data['jian-fen']);

        $this->assign('examname',$data['examname']);
        $this->assign('examcate',$cate[0]['cat_name']);
        $this->assign('totalScore',$exams['totalScore']);
        $this->display();
    }

    /**
     * 智能组卷-表单处理
     * @return [type] [description]
     */
    public function examHandle(){
        $model = D('ResourcesManage');
        $data = session('formdata');session('formdata',null);
        
        $data['danids'] = session('dan');session('dan',null);
        $data['duoids'] = session('duo');session('duo',null);
        $data['panids'] = session('pan');session('pan',null);
        $data['jianids']= session('jian');session('jian',null);

        $info = $model->addExamination($data);
        
        if($info){
            $this->ajaxReturn(array('status'=>1));
        }else{
            $this->ajaxReturn(array('status'=>0));
        }
    }

    /**
     * 试题管理-列表
     * @return [type] [description]
     */
    public function examination(){
        $model = D('ResourcesManage');
        $data = $model->examList();
		
		//试题库
		$res = D('QuestionBank')->index();
        $where['auditing'] = 1;
        $where['status'] = 1;
        $specifiedUser = D('IsolationData')->specifiedUser();
        $where['auth_user_id'] = array('in',$specifiedUser);
        
        $allCourse= D('Course')->getAllCourse($where);
        $course_name = D('Course')->getAllCourse($where,'id,course_name');
        $search = $this->getcourse();

        $this->assign('search',$search);
        $this->assign('list',$data['list']);
        $this->assign('page',$data['page']);
        $this->assign('title',$data['title']);
        $this->assign('type',$data['type']);
        $this->assign('course',$data['course']);
        $this->assign('course_name',$course_name);
        $this->assign('allCourse',$allCourse);
		
		//试题库ID
		$this->assign('id',I('get.id'));
		$this->assign('res',$res);//试题库信息

        //来源类型，1-自有，0-共享
        $this->assign('stype',I('get.stype',0,'int'));
        $this->display();
    }

    /**
     * 试题详情
     * @return [type] [description]
     */
    public function examDetails(){
        $id = I('post.id');
        $data = D('ResourcesManage')->examDetails($id);

        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 添加试题
     */
    public function addQuestion(){
        if(IS_POST){
            if(I('post.title') == ''){
                $this->ajaxReturn(array('status'=>0,'info'=>'请输入试题描述'));
            }
            if(I('post.analysis') == ''){
                $this->ajaxReturn(array('status'=>0,'info'=>'请输入试题解析'));
            }
			
            $data = I('post.');
			$right_option = I('post.right_option');
			if($data['classification'] == '3'){
				$data['right_option'] = strtoupper($right_option[0]);
			}else{
				$data['right_option'] = $right_option[1];
			}
			
            $data['ctime'] = time();
            $data['creater_id'] = $_SESSION['user']['id'];
            $data['title'] = strip_tags($data['title']);
            $data['right_option'] = trim(str_replace('，',',',$data['right_option']));
            $data['keywords'] = trim(str_replace('，',',',$data['keywords']));
			$data['question_bank_id'] = I('get.id');
						
            $model = D('ResourcesManage');
			$isset = $model->issetExamination($data);
			if($isset){
				$this->ajaxReturn(array('status'=>0,'info'=>'该题目已存在'));
			}
			
			$letterArray = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
			switch($data['classification']){
				case 1:
					if(!in_array($data['right_option'],$letterArray)){
//						$this->ajaxReturn(array('status'=>0,'info'=>'请输入A-Z其中一项'));
					}
					break;
				case 2:
                    $arr = explode($data['right_option']);
                    foreach($arr as $k=>$v){
                        if(!in_array($v,$letterArray)){
//                          $this->ajaxReturn(array('status'=>0,'info'=>'请输入A-Z其中一项或多项'));
                        }
                    }
                    break;
				case 3:
					$data['optiona'] = '对';
					$data['optionb'] = '错';
					if(!in_array(strtoupper($data['right_option']),array('A','B'))){
						$this->ajaxReturn(array('status'=>0,'info'=>'请输入A/B其中一项'));
					}
					break;
			}
			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$data['id'] = getNextId('examination_item');
			}
			
            $res = $model->addQuestion($data,$letterArray);
            if($res){
                $this->ajaxReturn(array('status'=>1,'url'=>U('examination',array('id'=>I('get.id')))));
            }else{
                $this->ajaxReturn(array('status'=>0));
            }
        }
        //课程
        $where['auditing'] = 1;
        $where['status'] = 1;
        $specifiedUser = D('IsolationData')->specifiedUser();
        $where['auth_user_id'] = array('in',$specifiedUser);
        
        $course= D('Course')->getAllCourse($where);
        $this->assign('course',$course);
        $this->display();
    }

    public function optionsHandle($data){
        foreach($data['optiona'] as $k=>$v){
            if($v != ''){
                $data['optiona'] = $v;
            }
        }
        foreach($data['optionb'] as $k=>$v){
            if($v != ''){
                $data['optionb'] = $v;
            }
        }
        foreach($data['optionc'] as $k=>$v){
            if($v != ''){
                $data['optionc'] = $v;
            }
        }
        foreach($data['optiond'] as $k=>$v){
            if($v != ''){
                $data['optiond'] = $v;
            }
        }
        foreach($data['right_option'] as $k=>$v){
            if($v != ''){
                $data['right_option'] = $v;
            }
        }
        return $data;
    }

    /*
     * 删除试题
     */
    public function delQuestion(){
        $post = (I('post.id'));
        $res = M('Examination_item')->where(array('id'=>array('in',$post)))->delete();
        if($res){
           $this->ajaxReturn(array('status'=>1));
        }else{
           $this->ajaxReturn(array('status'=>0));
        }
    }

    /*
     * 下载试题模板
     */
    public function downloadQuestion(){
        $uploadpath="./Upload/exam/";
        $saveName="question.xls";//文件保存名
        $showName="question.xlsx";//文件原名
        $filename=$uploadpath.$saveName;//完整文件名（路径加名字）
        file_download($filename);
    }

    /*
     * 导入试题
     */
    public function importQuestion(){
        if(IS_POST){
            $course = I('post.belongcourse');
			$question_bank_id = I('post.bank_id');
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 300*1024*1024 ;// 设置附件上传大小
            $upload->exts = array('xls','xlsx');// 设置附件上传类型
            $upload->rootPath = './Upload/'; // 设置附件上传根目录
            $upload->savePath = 'excel/'; // 设置附件上传（子）目录
            $upload->autoSub = true;
            $upload->subName = '';
            // 上传文件
            $info = $upload->upload();

            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                foreach($info as $v){
                    $file_path1 = './Upload/'.$v['savepath'];
                    $file_path2 = $v['savename'];
                }
                $file = $file_path1.$file_path2;
                if(file_exists($file)){
                    $list = D('ResourcesManage')->uploadQuestion($file);
					if($list === false){
						$this->error('模板格式错误',U('examination',array('id'=>I('get.id'))));
					}
					
                    $count = count($list);
                    $errNum = 0;
                    foreach($list as $k => $v){
                        $isset = M('examination_item')
								->where(array('title'=>$v['title'],'question_bank_id'=>$question_bank_id))
								->find();
                        if($isset){//题目重复
                            $errNum += 1;
                            continue;
                        }
                        if($v['classification'] == 1){
                            if(empty($v['title'])||empty($v['optiona'])||empty($v['right_option'])){
                                $errNum += 1;
                                continue;
                            }
                        }else if($v['classification'] == 2){
                            if(empty($v['title'])||empty($v['optiona'])||empty($v['right_option'])){
                                $errNum += 1;
                                continue;
                            }
                        }else if($v['classification'] == 3){
                            if(empty($v['title'])||empty($v['optiona'])||empty($v['optionb'])||empty($v['right_option'])){
                                $errNum += 1;
                                continue;
                            }
                        }else if($v['classification'] == 4){
                            if(empty($v['title'])||empty($v['right_option'])){
                                $errNum += 1;
                                continue;
                            }
                        }
                        $msg['title'] = (string)$v['title'];
                        $msg['creater_id'] = $_SESSION['user']['id'];
                        $msg['right_option'] = (string)strtoupper($v['right_option']);
                        $msg['classification'] = $v['classification'];
                        $msg['keywords'] = (string)$v['keywords'];
                        $msg['ctime'] = date('Y-m-d H:i:s');
                        $msg['belongcourse'] = $course;
                        $msg['analysis'] = (string)$v['analysis'];
						$msg['question_bank_id'] = $question_bank_id;

                        $msg['right_option'] = trim(str_replace('，',',',$msg['right_option']));
                        $msg['keywords'] = trim(str_replace('，',',',$msg['keywords']));
						
						if(strtolower(C('DB_TYPE')) == 'oracle'){
							$msg['id'] = getNextId('examination_item');
							$msg['ctime'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
						}
						
                        $res = M('ExaminationItem')->add($msg);
                        if(!$res){
                            $errNum += 1;
                        }
						
						
						//试题选项处理
						foreach($v as $kk=>$vv){
							if(substr($kk,0,6) == 'option'){
								$opt_data = array('item_id'=>$res);
								
								$opt_data['letter'] = strtoupper(substr($kk,-1,1));
								$opt_data['content'] = $vv;
								if(strtolower(C('DB_TYPE')) == 'oracle'){
									$opt_data['id'] = getNextId('examination_item_opt');
								}
								$res2 = M('examination_item_opt')->add($opt_data);
								if(!$res2){
									$errNum += 1;
								}
							}
						}
                    }
                    $this->success('导入成功,本次总共导入'.$count.'条,导入失败'.$errNum.'条',U('examination',array('id'=>$question_bank_id)));
                }
            }
        }else{
            $this->error('非法数据提交',U('examination'));
        }
    
    }

    /**
     * 模糊搜索所属课程
     * @return [type] [description]
     */
    public function getcourse(){
        $where['auditing'] = 1;
        $where['status'] = 1;
        $course= D('Course')->getAllCourse($where);
        foreach($course as $k=>$v){
            $str .= "'".$v['course_name']."',";
        }
        return '['.rtrim($str,',').']';
    }

    /**
     * 导入问卷
     */

    public function add_classify(){

        $item = M("SurveyCategory")->select();
        $this->assign('list',$item);

        $this->display();
    }
	
	/**
	 * 根据试题库ID获取各题型的数量
	 */
	public function getNumsByBankid(){
		$res = D('QuestionBank')->getNumsByBankid(I('post.id'));
		echo json_encode($res);
	}

    //分享给我的
    public function sharingtome(){
        $approved_data = D('ResourcesManage')->sharingtome();
        
        $cate = D('ResourcesManage')->getExamCate(array('plan_id'=>getPlanId()));
        $this->assign('category',$cate);
        $this->assign('approved_list',$approved_data['list']);
        $this->assign('approved_page',$approved_data['page']);
        $this->assign('data',$approved_data['xhr']);
        //搜索项
        $this->assign('keyword',$approved_data['keyword']);
        $this->assign('cate',$approved_data['cate']);
        $this->display();
    }
}