<?php 
namespace Admin\Model;
use Common\Model\BaseModel;
/**
 * 数据管理 - 费用统计
 * @author Dujunqiang 20170315
 */
class DataModel extends BaseModel{
	//初始化
	public function __construct(){}
	
    /**
     * 获取图表数据及数据列
     * startTime 开始时间
     * endTime 结束时间
     * keyword 搜索关键字
     * page 当前页码，不传默认为1
     * pageLen 每页数据条数，不传默认30
     */
	public function getData($param){
		//已完成的项目
		$start = ($param["page"] - 1) * $param["pageLen"];
		if($param["startTime"]){
			$where["start_time"] = array("gt", $param["startTime"]);
		}
		if($param["endTime"]){
			$where["end_time"] = array("lt", $param["endTime"]);
		}
		
		if($param["keyword"] && $param["keyword"] != ""){
			$where["project_name"] = array("like", "%".$param["keyword"]."%");
		}
		$where["type"] = 4;

		//隔离数据过滤
		$specifiedUser = D('IsolationData')->specifiedUser(false);
		$where['auth_user_id'] = array("in",$specifiedUser);

		if(strtolower(C('DB_TYPE')) == 'oracle'){

			$user_id_all = implode(",",$specifiedUser);

			$where = "1=1";
			$where .= " and start_time > to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss')";
			$where .= " and end_time < to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";

			$where .= " and type = 4";
			$where .= " and auth_user_id in($user_id_all)";


			$list = M("admin_project")->field("id,project_name,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') end_time,auth_user_id")->where($where)->limit($start, $param["pageLen"])->select();

		}else{

			$list = M("admin_project")->where($where)->limit($start, $param["pageLen"])->select();
		}

		
		$count = M("admin_project")->where($where)->count();
		
		$list = D('IsolationData')->isolationData($list);
		
		$page = $this->pageClass($count, $param["pageLen"]);
		
		$return = array("page"=>$page, "list"=>$list);
		return $return;
	}
	
    /**
     * 获取图表数据
     * 制作图表数据 时间：月份，计划费用，实际费用，实际参与人数
     * startTime 开始时间
     * endTime 结束时间
     * keyword 搜索关键字
	 * 获取进行中 or 已完成的项目
     */
	public function getEchart($param){
		if($param["keyword"] && $param["keyword"] != ""){
			$where["project_name"] = array("like", "%".$param["keyword"]."%");
		}
		
		//数据按月连续出现，防止断层
		$maxDate = explode("-", $param["endTime"]);
		$minDate = explode("-", $param["startTime"]);
		$diffMonth = ($maxDate[0] - $minDate[0]) * 12 + $maxDate[1] - $minDate[1];
		for($i=0; $i<$diffMonth; $i++){
			$themonth = date("Y-m",strtotime($i.' month',strtotime($param["startTime"])));
			$data["time"][] = $themonth;
			
			$where["type"] = 4;
			$where["end_time"] = array("like",$themonth."%");

			//隔离数据过滤
			$specifiedUser = D('IsolationData')->specifiedUser(false);
			$where['a.auth_user_id'] = array("in",$specifiedUser);
			
			$project = M("admin_project a")->field("a.id,a.project_budget,b.actual_expenses")
				->field("a.*,b.total_expenses")
				->join("left join __PROJECT_SUMMARY__ b ON a.id=b.project_id")
				->where($where)->select();
			$project = D('IsolationData')->isolationData($project);
			
			$planFee = 0;//项目预算
			$realFee = 0;//实际费用
			$joinUser = 0;//实际参与人数
			foreach ($project as $thisPro){
				$planFee += $thisPro["project_budget"];
				$realFee += $thisPro["actual_expenses"];
				
				//实际参与人数
				$proUser = M("designated_personnel")->where("project_id=".$thisPro["id"])->count();
				$joinUser += $proUser;
			}
			
			$data["planFee"][] = round($planFee, 2);
			$data["realFee"][] = round($realFee, 2);
			$data["joinUser"][] = $joinUser;
		}
		
		$return = array("echart"=>$data);
		return $return;
	}
	
	//获取下载数据
	public function getDownload($param){
		$where["type"] = 4;

		//隔离数据过滤
		$specifiedUser = D('IsolationData')->specifiedUser(false);

		if(strtolower(C('DB_TYPE')) == 'oracle'){

			$where = "start_time > to_date('".$param["startTime"]."','yyyy-mm-dd hh24:mi:ss') and end_time < to_date('".$param["endTime"]."','yyyy-mm-dd hh24:mi:ss')";

			if($param["keyword"] && $param["keyword"] != ""){
				$where .= " and like '".$param["keyword"]."'";
			}

			$where .= " and type = 4";

			$user_id_all = implode(",",$specifiedUser);

			$where .= " and auth_user_id in($user_id_all)";

			$project = M("admin_project")->field("id,project_name,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') end_time,auth_user_id")->where($where)->order("end_time desc")->select();

		}else{
			$where["end_time"][] = array("gt",$param["startTime"]);
			$where["end_time"][] = array("lt",$param["endTime"]);

			if($param["keyword"] && $param["keyword"] != ""){
				$where["project_name"] = array("like", "%".$param["keyword"]."%");
			}

			$where['auth_user_id'] = array("in",$specifiedUser);

			$project = M("admin_project")->where($where)->order("end_time desc")->select();
		}

		$project = D('IsolationData')->isolationData($project);
		//echo M("admin_project a")->getLastSql();
		
		$orderData = array();
		foreach ($project as $key=>$thisPro){
			//实际参与人数
			$proUser = M("designated_personnel")->where("project_id=".$thisPro["id"])->count();
			
			//预算费用
			$amount = M("project_budget")->where("project_id=".$thisPro["id"])->sum('amount');

			//实际费用
			$actual_amount = M("project_budget")->where("project_id=".$thisPro["id"])->sum('actual_amount');

			$orderData[$key]["id"] = $thisPro["id"];
			$orderData[$key]["project_name"] = $thisPro["project_name"];
			$orderData[$key]["start_time"] = $thisPro["start_time"];
			$orderData[$key]["end_time"] = $thisPro["end_time"];
			$orderData[$key]["join_user"] = $proUser;
			$orderData[$key]["project_budget"] = $amount;
			$orderData[$key]["actual_expenses"] = $actual_amount;
		}
		return $orderData;
	}

	/**
	 * 获取当前选择部门
	 * 所有计划名称
	 */
	public function getPlanName(){

		$tissue_id = I("post.tissue_id");

		//获取组织关联用户
		$map['tissue_id'] = array("eq",$tissue_id);

		$user_list = M("tissue_group_access")->field("user_id")->where($map)->select();

		foreach($user_list as $list){
			$user_id[] = $list['user_id'];
		}

		$user_id = $user_id ? $user_id : array(null);

		//获取项目名称
		$where['auth_user_id'] = array("in",$user_id);
		$project_budget = M("admin_project")->field("id,project_name")->where($where)->select();

		return $project_budget;
	}


	/**
	 * 获取费用统计 指定项目
	 */
	public function itemParameter($project_id){
		$project_id = I("post.project_id") ? I("post.project_id") : $project_id;
		$project_id = (int)$project_id;
		if($project_id > 1){
			$where['project_id'] = array("eq",$project_id);
		}

		$amount_data = array(0,0,0,0,0,0,0,0,0,0);

		$actual_amount_data = array(0,0,0,0,0,0,0,0,0,0);

		$option_name = array("内部讲师费","外部讲师费","课程开发费","外部培训费","培训咨询费","培训器材费","场地费","食宿费","交通差旅费","其他费用");

		if(is_numeric($project_id)){

			$data_list = M("project_budget")->where($where)->select();

		}else{

			$data_list = M("project_budget")->select();

		}

		foreach($data_list as $list){

			$i = array_search($list['option_name'],$option_name);

			if($i){
				$amount_data[$i] += round($list['amount']);
				$actual_amount_data[$i] += round($list['actual_amount']);
			}else{
				$amount_data[9] += round($list['amount']);
				$actual_amount_data[9] += round($list['actual_amount']);
			}
		}

		$data = array(
			'amount_data'=>$amount_data,
			'actual_amount_data'=>$actual_amount_data,
		);

		return $data;

	}

	/**
	 * 费用统计详细页
	 */
	public function article(){

		$project_id = I("get.id");

		$items = $this->itemParameter($project_id);

		$where['id'] = array("eq",$project_id);
		//隔离数据过滤
		$specifiedUser = D('IsolationData')->specifiedUser(false);
		$where['auth_user_id'] = array("in",$specifiedUser);

		$admin_project = M("admin_project")->field("id,project_name,class_name")->where($where)->find();

		//$admin_project = D('IsolationData')->isolationData($admin_project);

		$items['admin_project'] = $admin_project;

		return $items;

	}



}