<?php
namespace Common\Controller;
use Common\Controller\BaseController;
/**
 * Home基类控制器
 */
class HomeBaseController extends BaseController{
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		//当检测到XSS攻击时阻止页面加载
		header("X-XSS-Protection: 1; mode=block");

		parent::_initialize();

	}




}

