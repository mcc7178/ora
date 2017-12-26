<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 菜单操作model
 */
class AdminJurisdictionModel extends BaseModel{

	//初始化
	public function __construct(){}

	/**
	 *  获取权限管理列表
	 */
	public function jurisdictionList($total_page = 10){

		$start_page = I("get.p",0,'int');

		//数据隔离
		$rule_arrid = D('IsolationData')->specifiedUser(false);

		$conditions['auth_user_id'] = array("in",$rule_arrid);

		$list = M("AuthGroup")->where($conditions)->order('id asc')->page($start_page,$total_page)->select();

		// 获取规则数据
		$rule_data=D('AuthRule')->getTreeData('level','id','title');

		foreach($list as $o=>$vo){

			$txt = array();

			$rules = explode(",",$vo['rules']);

			foreach($rule_data as $k=>$v){

				$html = array();

				if(in_array($v['id'],$rules)){
					$txt[$k]['title'] = $v['title'].":";
				}

				foreach($v['_data'] as $i=>$n){
					if(in_array($n['id'],$rules)){
						$html[] = $n['title'];
					}
				}

				$txt[$k]['name'] = implode(",",$html);

			}

			$list[$o] = $vo;
			$list[$o]['txt'] = $txt;
		}

		$count = M("AuthGroup")->where($conditions)->count();

		$show = $this->pageClass($count,$total_page);

		// 获取规则数据
		$rule_data=D('AuthRule')->getTreeData('level','id','title');

		$data = array(
			"rule_data"=>$rule_data,
			"list" =>$list,
			"page"=>$show
		);

		return $data;
	}

	/**
	 *  权限 - 查看用户
	 */
	public function view_user($total_page = 10){

		$group_id = I("get.group_id",0);

		$start_page = I("get.p",0,'int');

		$keywords = I("get.keywords");

		if(!empty($keywords)){
			$conditions['b.username'] = array("like","%$keywords%");
		}

		$conditions['a.group_id'] = array("eq",$group_id);
		$conditions['b.id'] = array("gt",0);
		$conditions['b.status'] = array("eq",1);

		//数据隔离获取关联用户ID
		$Userid_Data = D('IsolationData')->specifiedUser(false);

		$user_id_all = array(null);

		foreach($Userid_Data as $id){

			//if($id != $_SESSION['user']['id']){
				$user_id_all[] = $id;
			//}

		}

		$conditions['a.user_id'] = array("in",$user_id_all);

		$list = M("auth_group_access a")
			->field("a.group_id,b.id,b.username,b.job_number,c.title")
			->join("LEFT JOIN __USERS__ b ON a.user_id = b.id LEFT JOIN __AUTH_GROUP__ c on a.group_id = c.id LEFT JOIN __TISSUE_GROUP_ACCESS__ d on d.user_id = a.user_id")
			->where($conditions)
			->order('a.user_id desc')
			->page($start_page,$total_page)
			->select();

		$items = array();

		foreach($list as $k=>$vo){

			$items[$k] = $vo;

			$group_list = array();

			//查询所属组织
			$tissue_plan = M("tissue_plan")->where("user_id =".$vo['id'])->select();

			$plan_id = array();
			$group_data = array();

			foreach($tissue_plan as $plan){
				$plan_id[] = $plan['plan_id'];
			}

			if(!empty($plan_id)){

				$where['a.plan_id'] = array("in",$plan_id);

				$group_data = M("sys_tissue a")->field("b.name")->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")->where($where)->select();
			}

			//$group_data = M("tissue_auth a")->field("b.name")->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")->where("a.user_id =".$vo['id'])->select();

			foreach($group_data as $i=>$data){
				$group_list[] = $data['name'];
			}

			$jobs_manage = M("tissue_group_access a")->field("a.tissue_id,b.name")->join("LEFT JOIN __JOBS_MANAGE__ b ON a.job_id = b.id")->where("a.user_id =".$vo['id'])->find();

			$items[$k]['job_name'] = $jobs_manage['name'];

			$items[$k]['tissue_id'] = $jobs_manage['tissue_id'];

			$items[$k]['group'] = implode(",",$group_list);

		}

		$count = M("auth_group_access a")
			->join("LEFT JOIN __USERS__ b ON a.user_id = b.id LEFT JOIN __AUTH_GROUP__ c on a.group_id = c.id LEFT JOIN __TISSUE_GROUP_ACCESS__ d on d.user_id = a.user_id")->where($conditions)->count();
		
		$show = $this->pageClass($count,$total_page);

		$data = array(
			"list" =>$items,
			"page"=>$show,
		);

		return $data;
	}

	/**
	 * 取消管理员
	 */
	public function cancelAdmin(){

		$group_id = I("post.group_id",0);
		$user_id = I("post.user_id",0);

		$data = array("user_id"=>array("eq",$user_id),"group_id"=>array("eq",$group_id));

		$results = M("auth_group_access")->where($data)->delete();

		return $results;
	}

	/**
	 * 添加角色
	 */
	public function addRole(){

		$data=I('post.');

		$map=array(
			'title'=>$data['role_name'],
			'rules'=>$data['rule_ids'],
			"auth_user_id"=>$_SESSION['user']['id']
		);
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$map['id'] = getNextId('auth_group');
		}
		
		if(!empty($map['title'])){

			$result = M('AuthGroup')->data($map)->add();

		}else{

			$result = false;
		}

		return $result;

	}

	/**
	 * 删除角色
	 */
	public function delRole(){

		$id = I('post.id');

		$data['id'] = array('eq',$id);

		$result = M('AuthGroup')->where($data)->delete();

		return $result;

	}

	/**
	 * 编辑角色
	 */
	public function editorHtml(){

		$id=I('post.id');
		// 获取用户组数据
		$group_data=M('Auth_group')->where(array('id'=>$id))->find();
		$group_data['rules']=explode(',', $group_data['rules']);
		// 获取规则数据
		$rule_data=D('AuthRule')->getTreeData('level','id','title');

		$html = '';
		$checked = '';
		$html .= '<form class="form-horizontal" id="popupForm">';
			$html .= '<div class="box-body">';
					$html .= '<div class="form-group" style="padding: 10px 0px; border-bottom: #c9c5c5 1px dashed;">';
						$html .= '<label for="inputEmail3" class="col-sm-2 control-label">角色名称：</label>';
						$html .= '<div class="col-sm-10 pt7">';
							$html .= "<input type=\"text\" class=\"form-control\" placeholder=\"请输入要添加的角色名称\" name=\"role_name\" value=\"".$group_data['title']."\" maxlength=\"12\"/>";
						$html .= '</div>';
					$html .= '</div>';
					$html .= '<div class="perm_div">';
						$html .= '<div class="form-group">';
												$html .= '<table class="table table-striped table-bordered table-hover table-condensed">';
												foreach($rule_data as $v){

														if(in_array($v['id'],$group_data['rules'])){
															$checked = 'checked="checked"';
														}else{
															$checked = "";
														}

														if(empty($v['_data'])){

															$html .= '<tr class="b-group">';
															$html .= '<th width="15%">';
															$html .= '<label>';
															$html .=  $v['title'];
															$html .= "<input type=\"checkbox\" name=\"rule_ids[]\" value=\"".$v['id']."\" $checked onclick=\"checkAll(this)\">";
															$html .= '</label>';
															$html .= '</th>';
															$html .= '<td></td>';
															$html .= '</tr>';
														}else{

															$html .= '<tr class="b-group">';
															$html .= '<th width="15%">';
															$html .= '<label>';
															$html .=  $v['title'];
															$html .= "<input type=\"checkbox\" name=\"rule_ids[]\" value=\"".$v['id']."\" $checked onclick=\"checkAll(this)\">";
															$html .= '</label>';
															$html .= '</th>';
															$html .= '<td class="b-child">';
															foreach($v['_data'] as $n){

																if(in_array($n['id'],$group_data['rules'])){
																	$checked = 'checked="checked"';
																}else{
																	$checked = "";
																}

																$html .= '<table class="table table-striped table-bordered table-hover table-condensed" style="width:25%; float:left;">';
																$html .= '<tr class="b-group">';
																$html .= '<th width="10%">';
																$html .= '<label>';
																$html .=  $n['title'];
																$html .= "<input type=\"checkbox\" name=\"rule_ids[]\" value=\"".$n['id']."\" $checked onclick=\"checkAll(this)\">";
																$html .= '</label>';
																$html .= '</th>';
																$html .= '<td style="display:none;">';

																foreach($n['_data'] as $c){

																	if(in_array($c['id'],$group_data['rules'])){
																		$checked = 'checked="checked"';
																	}else{
																		$checked = "";
																	}

																	//$html .= '<label>';
																	//$html .=  $c['title'];
																	$html .= "<input type=\"checkbox\" name=\"rule_ids[]\" value=\"".$c['id']."\" $checked onclick=\"checkAll(this)\">";
																	//$html .= '</label>';
																}

																$html .= '</td>';
																$html .= '</tr>';
																$html .= '</table>';

															}

															$html .= '</td>';
															$html .= '</tr>';
														}
												}
													$html .= '</table>';

						$html .= '</div>';
					$html .= '</div>';
			$html .= '</div>';
		$html .= '</form>';

		return $html;

	}

	/**
	 * 编辑角色权限
	 */
	public function editorRole(){

		$post = I('post.');

		$data=array(
			'title'=>$post['role_name'],
			'rules'=>$post['rule_ids']
		);

		if(!empty($data['title'])){

			$result = M('AuthGroup')->where('id='.$post['id'])->setField($data);

		}else{

			$result = false;

		}

		return $result;

	}

	/**
	 * 设置管理员页面
	 */
	public function view_admin($total_page = 10){

		//获取当前所属组织所有会员
		$start_page = I("get.p",0,'int');

		$keywords = I("get.keywords");

		//数据隔离获取关联用户ID
		$Userid_Data = D('IsolationData')->specifiedUser(false);

		//array_unshift($Userid_Data,$items['pid']);

		$conditions['a.user_id'] = array("in",$Userid_Data);
		$conditions['b.id'] = array("gt",0);
		$conditions['b.status'] = array("eq",1);

		if(!empty($keywords)){
			$conditions['b.username'] = array("like","%$keywords%");
		}

		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$list = M("tissue_group_access a")
				->field("a.user_id,b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id")
				->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
				->where($conditions)
				->order('a.user_id desc')
				->group('a.user_id,b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id')
				->page($start_page,$total_page)
				->select();

			$count = M("tissue_group_access a")
				->field("a.user_id,b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id")
				->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
				->where($conditions)
				->order('a.user_id desc')
				->group('a.user_id,b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id')
				->select();
		}else{

			$list = M("tissue_group_access a")
				->field("a.user_id,b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id")
				->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
				->where($conditions)
				->order('a.user_id desc')
				->group('a.user_id')
				->page($start_page,$total_page)
				->select();

			$count = M("tissue_group_access a")
				->field("a.user_id,b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id")
				->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
				->where($conditions)
				->order('a.user_id desc')
				->group('a.user_id')
				->select();
		}

		$count = count($count);

		$show = $this->pageClass($count,$total_page);

		$data = array(
			"list"=>$list,
			"pages"=>$show
		);

		return $data;

	}


	/**
	 * 设置管理员
	 */
	public function updateAdmin(){

		$user_id = I("post.user_id",0,'int');
		$group_id = I("post.group_id",0,'int');

		$where['user_id'] = array("eq",$user_id);
		$where['group_id'] = array("eq",$group_id);

		$user = M("auth_group_access")->where($where)->find();

		$data=array('group_id'=>$group_id);

		if(!empty($user_id)){

			if(!empty($user)){

				$result = M('auth_group_access')->where($where)->setField($data);

			}else{

				$data['user_id'] = $user_id;

				$result = M('auth_group_access')->data($data)->add();

			}
		}else{

			$result = false;

		}

		return $result;

	}

	/**
	 * 获取更改权限范围状态
	 */
	public function authority(){

		$user_id = I("get.user_id");

		$tissue_auth = M("tissue_plan")->where("user_id =".$user_id)->select();

		if(empty($tissue_auth)){
			$code = 0;
		}else{
			$code = 1;
		}

		return $code;
	}

	/**
	 * 移除该用户更改范围数据
	 */
	public function removeuser(){

		$user_id = I("post.user_id");

		$status = M("tissue_plan")->where("user_id =".$user_id)->delete();

		return $status;
	}


	/**
	 * 获取最新方案数据隔离
	 */
	public function sysplan(){

		//查询所有
		$sys_plan = M("sys_plan")->select();

		$user_id = I("get.user_id");

		$where['user_id'] = array("eq",$user_id);

		$tissue_plan = M("tissue_plan")->where($where)->select();

		$plan_id = array();

		foreach($tissue_plan as $plan){
			$plan_id[] = $plan['plan_id'];
		}

		$data = array(
			"sys_plan"=>$sys_plan,
			"tissue_plan"=>$plan_id
		);

		return $data;

	}


}
