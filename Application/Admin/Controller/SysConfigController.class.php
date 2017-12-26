<?php

namespace Admin\Controller;

use Common\Controller\AdminBaseController;
/*
 * 系统参数设置控制器
 */
class SysConfigController extends AdminBaseController{

	/**
	 * 课程分类
	 * @return [type] [description]
	 */
	public function index(){
		$this->display('course');
	}

	/**
	 * 试题库分类
	 * @return [type] [description]
	 */
	public function question(){
		$this->display('course');
	}

	/**
	 * 供应商领域
	 * @return [type] [description]
	 */
	public function supplier(){
		$this->display('course');
	}
}