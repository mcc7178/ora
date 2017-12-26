<?php
namespace Common\Model;

use Common\Model\BaseModel;
/**
 * 菜单操作model
 */
class IsolationDataModel extends BaseModel{

	//初始化
	public function __construct(){}

	/**
	 *  数据隔离处理
	 */
	public function isolationData($data,$typeid = 1){

		return $data;

		$items = array();

		if($typeid == 1){

		}else{
			$specifiedUser_list = $this->specifiedUser(false);
		}


		foreach($data as $k=>$list){

			//获取数据隔离许可证
			if(!empty($list['auth_user_id'])){
				//判断组织同级

				if($typeid == 1){
					$result = $this->isEquality($list['auth_user_id']);
				}else{
					$result = $this->isEqualityTwo($list['auth_user_id'],$specifiedUser_list);
				}

				if($result){
					$isequality = 1;
				}else{
					$isequality = 0;
				}

				$items[$k] = $list;
				$items[$k]['isequality'] = $isequality;
			}
		}

		return $items;

	}

	/**
	 * 获取当前组织所有下级组织
	 */
	public function ruleData($tissue_id,&$rows){

		$rule_list = M("tissue_rule")->field("id")->where("pid=".$tissue_id)->select();

		foreach($rule_list as $list){

			$rows[] = $list['id'];

			$this->ruleData($list['id'],$rows);

		}

		return $rows;

	}

	/**
	 * 判断是否为同级组织
	 */
	public function isEquality($user_id){

		if(isset($user_id)){

			if($user_id != $_SESSION['user']['id']){

				//获取[Girl]用户所属组织
				$girl_tissue_id = M("tissue_group_access a")
								->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
								->where("user_id=".$user_id)
								->getField('b.pid');

				//获取[Boy]用户所属组织
				$boy_tissue_id = M("tissue_group_access a")
								->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
								->where("user_id=".$_SESSION['user']['id'])
								->getField('b.pid');

				if($girl_tissue_id == $boy_tissue_id){
					$state = true;
				}else{
					$state = false;
				}

			}else{
				$state = false;
			}

		}else{
			$state = false;
		}

		return $state;
	}

	/**
	 * 获取当前用户，允许编辑用户
	 *
	 */

	public function isEqualityTwo($user_id,$specifiedUser_list){

		if(isset($user_id)){

			if($user_id != $_SESSION['user']['id']){

				if(in_array($user_id,$specifiedUser_list)){

					$state = false;

				}else{

					$state = true;
				}

			}else{
				$state = false;
			}

		}else{
			$state = false;
		}

		return $state;

	}


	/**
	 * 获取该用户当前组织下 - 所有符合数据隔离用户user_id
	 * $type = true; 同级数据共享
	 * $type = false; 同级数据不共享
	 * 已废弃 2017-11-10 16:19
	 */
	/*
	public function specifiedUser($type = true){

		//获取[Boy]用户所属组织
		$boy_pid = M("tissue_group_access a")
			->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
			->field('a.tissue_id,b.pid')
			->where("a.user_id=".$_SESSION['user']['id'])
			->find();

		//获取所有下级组织
		$ruleData = $this->ruleData($boy_pid['tissue_id']);

		if(empty($ruleData)){
			$ruleData = array($boy_pid['tissue_id']);
		}else{
			array_unshift($ruleData,$boy_pid['tissue_id']);
		}

		if($type){

			//获取当前级别
			$boy_level = D('AdminTissue')->hierarchy($boy_pid['tissue_id']);

			//如果三级或四级可共享
			if($boy_level == 3 || $boy_level == 4){

				$three_list = M("tissue_rule")->field("id")->where("pid=".$boy_pid['pid'])->select();

				$rule_arrid = array();

				foreach($three_list as $list){
					$rule_arrid[] = $list['id'];
				}

				$ruleData = array_unique(array_merge($ruleData,$rule_arrid));
			}

		}

		//获取指定范围
		$tissue_auth_list = M("tissue_auth")->field("tissue_id")->where("user_id=".$_SESSION['user']['id'])->select();

		if(!empty($tissue_auth_list)){

			$ruleData = array();

			foreach($tissue_auth_list as $value){
				$ruleData[] = $value['tissue_id'];
			}

		}


		//获取所有用户ID
		$where['tissue_id'] = array("in",$ruleData);

		$user_list = M("tissue_group_access")->field("user_id")->where($where)->select();

		foreach($user_list as $list){
			$rows[] = $list['user_id'];
		}

		$rows = isset($rows) ? $rows : array(null);

		return $rows;

	}*/

	/**
	 * 获取该用户当前组织下 - 所有符合数据隔离用户user_id
	 * $type = true; 同级数据共享
	 * $type = false; 同级数据不共享
	 */
	public function specifiedUser($type = true){

		//获取指定范围
		$tissue_plan = M("tissue_plan")->where("user_id =".$_SESSION['user']['id'])->select();

		$plan_id = array();

		foreach($tissue_plan as $plan){
			$plan_id[] = $plan['plan_id'];
		}

		if(!empty($plan_id)){

			$term['plan_id'] = array("in",$plan_id);

			$tissue_auth_list = M("sys_tissue")->field("tissue_id")->where($term)->select();
		}

		//$tissue_auth_list = M("tissue_auth")->field("tissue_id")->where("user_id=".$_SESSION['user']['id'])->select();

		if(!empty($tissue_auth_list)){

			$ruleData = array();

			foreach($tissue_auth_list as $value){
				$ruleData[] = $value['tissue_id'];
			}

		}else{

			//获取[Boy]用户所属组织
			$boy_pid = M("tissue_group_access a")
				->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
				->field('a.tissue_id,b.pid')
				->where("a.user_id=".$_SESSION['user']['id'])
				->find();

			$ruleData = array($boy_pid['tissue_id']);

		}
		//获取所有用户ID
		$where['tissue_id'] = array("in",$ruleData);

		$user_list = M("tissue_group_access")->field("user_id")->where($where)->select();

		foreach($user_list as $list){
			$rows[] = $list['user_id'];
		}
		$rows = isset($rows) ? $rows : array(null);

		return $rows;

	}

	/**
	 * 返回符合条件组织
	 */
	public function rangeOrganization(){

		//获取指定范围
		//$tissue_auth_list = M("tissue_auth")->field("tissue_id")->where("user_id=".$_SESSION['user']['id'])->select();

		$tissue_plan = M("tissue_plan")->where("user_id =".$_SESSION['user']['id'])->select();

		$plan_id = array();

		foreach($tissue_plan as $plan){
			$plan_id[] = $plan['plan_id'];
		}

		if(!empty($plan_id)){

			$term['plan_id'] = array("in",$plan_id);

			$tissue_auth_list = M("sys_tissue")->field("tissue_id")->where($term)->select();
		}

		if(!empty($tissue_auth_list)){

			$ruleData = array();

			foreach($tissue_auth_list as $value){
				$ruleData[] = $value['tissue_id'];
			}

		}else{

			//获取[Boy]用户所属组织
			$boy_pid = M("tissue_group_access a")
				->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
				->field('a.tissue_id,b.pid')
				->where("a.user_id=".$_SESSION['user']['id'])
				->find();

			$ruleData = array($boy_pid['tissue_id']);

		}

		return $ruleData;
	}

	/**
	 * 不同中心数据分离规则
	 */
	public function centerSeparation(){

		//获取[Boy]用户所属组织
		$boy_pid = M("tissue_group_access a")
			->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
			->field('a.tissue_id,b.pid')
			->where("a.user_id=".$_SESSION['user']['id'])
			->find();

		$admin_level = D('AdminTissue')->hierarchy($boy_pid['tissue_id']);

		if($admin_level == 1){

			$rows = $this->specifiedUser();

		}else{

			//向上取所有级别
			$rule_items = D('AdminTissue')->leveldata($boy_pid['tissue_id']);

			//合并用户组织
			array_unshift($rule_items,$boy_pid['tissue_id']);

			foreach($rule_items as $rule_id){

				//获取中心层级ID
				$level = D('AdminTissue')->hierarchy($rule_id);

				if($level == 2){
					//获取所有下级组织
					$ruleData = $this->ruleData($rule_id);

					array_unshift($ruleData,$rule_id);
					break;
				}

			}

			if(empty($ruleData)){
				$ruleData = array($boy_pid['tissue_id']);
			}else{
				array_unshift($ruleData,$boy_pid['tissue_id']);
			}

			//获取中心层级所有符合规则用户ID
			$where['tissue_id'] = array("in",$ruleData);

			$user_list = M("tissue_group_access")->field("user_id")->where($where)->select();

			foreach($user_list as $list){
				$rows[] = $list['user_id'];
			}

			$rows = isset($rows) ? $rows : array(null);

		}

		return $rows;
	}


}
