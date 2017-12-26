<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/**
 * 资源中心-线下培训班
 *
 */
class LinetrainController extends AdminBaseController{

	/**
	 * 首页
	 */
	public function index(){

		$total_page = $this->total_page;

		$data = D('Linetrain')->index($total_page);

		$this->assign($data);
		$this->display();
	}

	/**
	 * 详情页
	 */
	public function sign_up(){

		//处理试卷过期
		D('MySurvey')->overdue();

		//添加所属组织参加试卷状态
		D('MySurvey')->researchAdd();

		$total_page = $this->total_page;

		$data = D('Linetrain')->sign_up($total_page);

		$this->assign($data);
		$this->display();

	}

	/**
	 * 申请报名
	 */
	public function form_sign(){

		$data = D('Linetrain')->form_sign();

		$this->ajaxReturn($data);
	}

}