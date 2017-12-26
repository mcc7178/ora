<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/**
 *
 * @Andy
 */
class LogController extends AdminBaseController{
	
	/**
	 * 操作日志
	*/
	public function index(){

		$result = D("Log")->index();

		$this->assign($result);
		$this->display();

	}

	/**
	 * 导出操作日志
	 */
	public function exportLog(){

		$result = D("Log")->index();

		$items = array();

		foreach($result['list'] as $k=>$list){
			$items[$k]['email'] = $list['email'];
			$items[$k]['username'] = $list['username'];
			$items[$k]['tissue_name'] = $list['tissue_name'];
			$items[$k]['login_ip'] = $list['login_ip'];

			if($list['login_typeid'] == 2){
				$type = "新增";
			}else if($list['login_typeid'] == 3){
				$type = "编辑";
			}else if($list['login_typeid'] == 4){
				$type = "删除";
			}else{
				$type ='';
			}

			if($list['login_classid'] == 0){
				if($list['login_typeid'] == 1){
					$content = "登录";
				}else{
					$content = "登出";
				}
			}else if($list['login_classid'] == 1){
				$content = "课程";
			}else if($list['login_classid'] == 2){
				$content = "试卷";
			}else if($list['login_classid'] == 3){
				$content = "讲师";
			}else if($list['login_classid'] == 4){
				$content = "供应商";
			}else if($list['login_classid'] == 5){
				$content = "课件";
			}else if($list['login_classid'] == 6){
				$content = "资讯";
			}else if($list['login_classid'] == 7){
				$content = "配置方案";
			}else if($list['login_classid'] == 8){
				$content = "培训班";
			}else if($list['login_classid'] == 9){
				$content = "调研";
			}else if($list['login_classid'] == 10){
				$content = "考试";
			}else if($list['login_classid'] == 11){
				$content = "问卷";
			}else if($list['login_classid'] == 12){
				$content = "活动报名";
			}else if($list['login_classid'] == 13){
				$content = "审核规则";
			}else{
				$content = '';
			}

			$items[$k]['type'] = $type.$content;
			$items[$k]['login_time'] = $list['login_time'];
			$items[$k]['content'] = $list['login_msg'];

			if($list['login_classid'] == 0){
				$module = "用户登录";
			}else if($list['login_classid'] == 1){
				$module = "资源管理";
			}else if($list['login_classid'] == 2){
				$module = "资源管理";
			}else if($list['login_classid'] == 3){
				$module = "资源管理";
			}else if($list['login_classid'] == 4){
				$module = "资源管理";
			}else if($list['login_classid'] == 5){
				$module = "资源管理";
			}else if($list['login_classid'] == 6){
				$module = "资源管理";
			}else if($list['login_classid'] == 7){
				$module = "系统管理";
			}else if($list['login_classid'] == 8){
				$module = "培训班管理";
			}else if($list['login_classid'] == 9){
				$module = "工具管理";
			}else if($list['login_classid'] == 10){
				$module = "工具管理";
			}else if($list['login_classid'] == 11){
				$module = "工具管理";
			}else{
				$module = "审核中心";
			}

			$items[$k]['module'] = $module;

			if($list['login_typeid'] == 0 OR $list['login_typeid'] == 1){
				$log_type = "系统登录";
			}else if($list['login_typeid'] == 2){
				$log_type = "新增操作";
			}else if($list['login_typeid'] == 3){
				$log_type = "编辑操作";
			}else{
				$log_type = "删除操作";
			}

			$items[$k]['log_type'] = $log_type;

		}

		$xlsName  = "操作日志-".date('Y-m-d');

		array_unshift($items,array('用户账号','用户名称','所属部门','终端地址','操作类型','操作时间','操作内容','所属模块','日志类型'));

		create_xls($items,$xlsName);
	}


}
