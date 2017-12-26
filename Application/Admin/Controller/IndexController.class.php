<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/**
 * 后台首页控制器
 */
class IndexController extends AdminBaseController{
	/**
	 * 首页
	 */
    public function delsession(){
        $_SESSION = array();
        echo 'ok!';
	}

	public function index(){
		$getGroups = D('AdminNav')->getGroups();

		$company = D('AdminTissue')->treeInfo();
		

		//查询当前级别ID
		$Tissue_id = D('AdminTissue')->superiorTissue();

		$Superior_name = D('AdminTissue')->parentName($Tissue_id['pid']);

		$home = session('home');

		if(!empty($Superior_name)){

			$tissue_name = $Superior_name." ".$company['name'];
		}else{
			$tissue_name = $company['name'];
		}
        
		if(!empty($home)){
			$url = 'Index/'.$home;
		}else{
			$url = 'Index/indexStudent';
		}

		/*导航位置*/
		$location="太平稽核学院";

		$assign = array(
			"location"=>$location,
			"getGroups"=>$getGroups,
			"tissue_name"=>$tissue_name,
			"home"=>$url
		);
		
		//未设置个人信息跳转个人信息页面（首次）
		$user = M("users")
			->field("id,username,email,job_number,skin,firstlogin")
			->where("id=".$_SESSION['user']['id'])
			->select();
		if($user){
			if($user[0]["firstlogin"] == 0){
				$data['firstlogin'] = 1;
				$res = M("users")->where("id=".$_SESSION['user']['id'])->save($data);
				if($res){
					$assign["home"] = "info/infopage/tabType/1";
				}
			}
		}
		
		//获取未读取的消息通知数目
		$messages = D('AdminMessage')->countMessage($_SESSION['user']['id']);
		if(!$user[0]["skin"]) $user[0]["skin"] = "skin-blue";
		$this->assign('skin',$user[0]["skin"]);
		$this->assign('messagesTotal',$messages);
		$this->assign($assign);
	    $this->display();
		
	}
	/**
	 * 管理员面板 - 首页
	 */
	public function indexDirector(){
		session('home','indexDirector');

		$url = "index_admin/index";

		$this->redirect($url);
	}

	/**
	 * 讲师面板 - 首页
	 */
	public function indexLecturer(){

		//新闻资讯
		$data_news = D("IndexAdmin")->news();

		//获取讲师面板数据
		$data = D("AdminIndex")->indexLecturer();

		//获取日程信息
		$model = D('Student');
		$schedule = $model->getSchedule();//当天有任务则加小红点标识
		$scheduleStr = "";
		if(is_array($schedule)){
			foreach ($schedule as $key=>$value){
				$formatTime = date("d-m-Y", strtotime($value));
				$scheduleStr .= '{"start": "' .$formatTime. ' 00:00:00",
                "end": "' .$formatTime. ' 22:22:22",
                "singleColor": "ff0000"},';
			}
			$scheduleStr = substr($scheduleStr, 0, -1);
		}

		$this->assign($data);
		$this->assign('schedule',$scheduleStr);
		$this->assign("data_news",$data_news);

		session('home','indexLecturer');
		$this->display();
	}

	/**
	 * 学员面板 - 首页
	 */
	public function indexStudent(){
		session('home','indexStudent');
		$model = D('Student');
		$data = $model->getStudentInfo();
		
		//获取中保协学习数据
		$iacStudy = D("Iac")->getIacStudy($_SESSION['user']['id']);
		$data["credit"] = $data["credit"] + $iacStudy["study_credit"];
		$data["hours"] = $data["hours"] + round($iacStudy["study_len"] / 3600, 2);
		$data["finishedCourseNum"] = $data["finishedCourseNum"] + $iacStudy["study_num"];
		
        //获取日程信息
        $schedule = $model->getSchedule();//当天有任务则加小红点标识
        $scheduleStr = "";
        if(is_array($schedule)){
            foreach ($schedule as $key=>$value){
                $formatTime = date("d-m-Y", strtotime($value));
                $scheduleStr .= '{"start": "' .$formatTime. ' 00:00:00",
                "end": "' .$formatTime. ' 22:22:22",
                "singleColor": "ff0000"},';
            }
            $scheduleStr = substr($scheduleStr, 0, -1);
        }
        
        $this->assign('schedule',$scheduleStr);
		$this->assign('data',$data);
		$this->display();
	}

	/**
	 * 获取当天日程数据
	 */
	public function dayTask(){
		$post = I("post.");
		if(!$post["chooseDay"]){
			$post["chooseDay"] = date("Y-m-d");
		}else{
			//chooseDay 前端传过来 cmvDay-5-2-2017  处理为 2017-02-05
			if(preg_match('/^cmvDay-[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$/', $post["chooseDay"])){
				$chooseDay = explode("-", $post["chooseDay"]);
				$chooseDay[2] += 1;//国外月份从0开始算起   12月份为11
				$post["chooseDay"] = $chooseDay[3]."-".$chooseDay[2]."-".$chooseDay[1];
				$post["chooseDay"] = date("Y-m-d", strtotime($post["chooseDay"]));
			}else{
				$post["chooseDay"] = date("Y-m-d");
			}
		}
		
		$resp = D("Student")->dayTask($post['chooseDay']);
		$return = array("code"=>1000, "message"=>"成功", "list"=>$resp["list"]);
		echo json_encode($return);
	}

	/**
	 * 获取当前首页控制面板
	 */
	public function home(){

		$home = session('home');

		if(!empty($home)){
			$url = 'Index/'.$home;
		}else{
			$url = 'Index/indexStudent';
		}

		$this->redirect($url);
	}

	/**
	 * 左侧菜单 - 加载
	 */
	public function leftMenu(){

		//获取未读取的消息通知数目
		$messages = D('AdminMessage')->countMessage($_SESSION['user']['id']);
		
		//根据用户权限 - 获取左侧菜单
		$MenuHtml = D('AdminNav')->MenuHtml($messages);

		$this->ajaxReturn($MenuHtml,'json');
	}


	/**
	 * 退出
	 */
	public function logout(){
		//调用公共方法 - >写入操作日志
		write_login_log(0,0,'用户退出成功');
		F('specifiedUser_true',NULL);
		F('specifiedUser_false',NULL);
		F('centerSeparation',NULL);
		F('Admin_MenuHtml',NULL);
		F('Lecturer_MenuHtml',NULL);
		F('Student_MenuHtml',NULL);
		F('Ordinary_MenuHtml',NULL);
		session('user',null);
		$this->logoutTpl('退出成功、前往登录页面',1,U('Home/Index/index'));
	}




	/**
	 * elements
	 */
	public function elements(){

		$this->display();
	}
	


	/**
	 * 加载尾部部分
	 */
	public function footer(){
	    
	    $this->display("Public:header");
	}


	/**
	 * 退出登录跳转方法  --重写$this->success();
	 * @param  [type]  $message [description]
	 * @param  integer $status  [description]
	 * @param  string  $jumpUrl [description]
	 * @return [type]           [description]
	 */
	public function logoutTpl($message, $status = 1, $jumpUrl = ''){
		if (!empty($jumpUrl)) {
            $this->assign('jumpUrl', $jumpUrl);
        }

        // 提示标题
        $this->assign('msgTitle', $status ? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if ($this->get('closeWin')) {
            $this->assign('jumpUrl', 'javascript:window.close();');
        }

        $this->assign('status', $status); // 状态
        //保证输出不受静态缓存影响
        C('HTML_CACHE_ON', false);
        if ($status) {
            //发送成功信息
            $this->assign('message', $message); // 提示信息
            // 成功操作后默认停留1秒
            if (!isset($this->waitSecond)) {
                $this->assign('waitSecond', '1');
            }

            $this->display(C('TMPL_ACTION_LOGOUT'));
        }
	}
	
	//保存主题
	public function setSkin(){
		$skin = I("post.skin");
		if(!$skin) $skin = "skin-green";
		$data = D('AdminIndex')->setSkin($skin);
		exit(json_encode($data));
	}
}
