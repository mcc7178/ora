<?php
namespace Common\Model;

use Common\Model\BaseModel;
/**
 * ------------------------------------------------------------------------------
 * Description  供应商操作模型
 * @filename SupplierModel.class.php
 * @author Andy
 * @datetime 2016-12-27 02:37:29
 * -------------------------------------------------------------------------------
 */
class SupplierModel extends BaseModel {
   
    //定义表前缀表名，过滤表单字段
    protected $tablePrefix = 'think_';
    protected $trueTableName = 'think_supplier';
    protected $insertFields = array('sid','sname','style','sc_type','main_course','linkman','position','tel','phone_number');
	protected $pk = 'sid';
	//定义验证规则
	protected $_validate = array(
		array('sname','require','供应商名称不能为空',1),
		array('style','require','供应商类型不能为空',1),
	    array('sc_type','require','擅长类别不能为空',1),
		array('main_course','require','主打课程不能为空',1),
		array('linkman','require','联系人不能为空',1),
		array('position','require','职位不能为空',1),
	    // array('tel','require','电话号码必须填写',1),
	    //array('tel','/(^(0\d{2})-(\d{8})$)|(^(0\d{3})-(\d{7})$)|(^(0\d{3})-(\d{8})$)|(^(0\d{2})-(\d{8})-(\d+)$)|(^(0\d{3})-(\d{7})-(\d+)$)/','电话号码格式错误',"Model::EXISTS_TO_VAILIDATE",1),
		array('phone_number','require','手机号必须填写',1),
	    array('phone_number','/^1[3|4|5|8][0-9]\d{4,8}$/','手机号码格式错误！',"Model::EXISTS_TO_VAILIDATE",'regex',1),
	);
	
	protected function _before_update(&$data,$options) {
	    //$data['main_course'] = implode(',',$data['tag']);
	}
	//供应商关联类型查询
	function supplierStyleCheck($id){
	   
	      $items = M('Supplier')
	      ->table('think_supplier supplier,think_supplier_type type')
	      ->where("supplier.sc_type = type.id and supplier.sid=".$id)
	      ->field("supplier.*,type.tname")->find();
	      $arr =array();
	      $arr = explode(',',$items['main_course']); 
	      return $result = array(
	          'list' => $items,
	          'main_courses' => $arr
	      );
	}
	

	/*
	 * 供应商列表
	 */
	public function supplierSearchList($total_page = 10){
	
	    $start_page = I("get.p",0,'int');
	    $ret = M('Supplier_type')->select();
	    if(IS_GET){
	        $get['keyword'] = I("get.keyword",'','trim'); //搜索输入框值
	        $get['style_search'] = I("get.style_search","",'int');  ////搜索选项值
			
	        $conditions['sname'] = array("like","%".$get['keyword']."%");
			
	        if(!empty($get['style_search'])){
	            $conditions['style'] = array("eq",$get['style_search']);    
	        }

			//隔离数据过滤
			$specifiedUser = D('IsolationData')->specifiedUser();

			$conditions['b.auth_user_id'] = array("in",$specifiedUser);

	        if(!empty($conditions['sname']) && !empty($conditions['style'])){
	            $conditions['_logic'] = 'and';
	            $result = M('Supplier_type')
	            ->alias('a')
	            ->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
	            ->where($conditions)
	            ->order('sid DESC')
	            ->page($start_page,$total_page)
	            ->select();
	            $count = M('Supplier_type')
	            ->alias('a')
	            ->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
	            ->where($conditions)->count();
	            
	        }else{
	            $result = M('Supplier_type')
	            ->alias('a')
	            ->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
	            ->where($conditions)
	            ->order('sid DESC')
	            ->page($start_page,$total_page)
	            ->select();
	            $count = M('Supplier_type')
	            ->alias('a')
	            ->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
	            ->where($conditions)->count();

	        }
	    }else{
	
	        $result = M('Supplier_type')
	        ->alias('a')
	        ->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
	        ->order('sid DESC')
	        ->page($start_page,$total_page)
	        ->select();
	        $count = M('Supplier_type')
	        ->alias('a')
	        ->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
	        ->count();

	    }

		// print_r($result);
	    foreach($result as $k => $v){
			$v1 = explode(',',$v['main_course']);
			$result[$k]['main_courses'] = $v1['0'];
			$lecturerNum = M('Lecturer')->where(array('type' => 1,'sid'=>$v['sid']))->count();
			$result[$k]['lecturerNum'] = $lecturerNum;
		}

		//隔离数据过滤
		$result = D('IsolationData')->isolationData($result,2);

		$show = $this->pageClass($count,$total_page);

	    $data = array(
	        "list" =>$result,
	        "page"=>$show,
	        'xhr' => $ret,
	        'keyword' => $get['keyword'],
	        'keywords' => $get['style_search'],
	        
	    );
		
	    return $data;

	}
	
	
	/*
	 * 查询外部讲师
	 */
	public function getOutsideLecturer($total_page=10,$id){
	    $start_page = I("get.p",0,'int');
		if(strtolower(C('DB_TYPE')) == 'oracle'){
	     $ret = M('Lecturer')->field("id,name,age,type,levels,year,price,address,to_char(employed_time,'YYYY-MM-DD HH24:MI:SS') as employed_time")->where(array('type' => 1,'sid'=>$id))->page($start_page,$total_page)->select();
		}else{
         $ret = M('Lecturer')->where(array('type' => 1,'sid'=>$id))->page($start_page,$total_page)->select();
		}
		
	    $count = M('Lecturer')->where(array('type' => 1,'sid'=>$id))->count();
	    $show = $this->pageClass($count,$total_page);
	    $data = array(
	        "list" =>$ret,
	        "page"=>$show,
	        'count'=>$count,
	    );
	    return $data;
	}

	/**
	 * 获取共享给我的供应商
	 * @return [type] [description]
	 */
	public function sharingToMe($total_page = 10){

		$start_page = I("get.p",0,'int');
		$ret = M('Supplier_type')->select();


		//查询共享数据
		$course_arrid = array();
		$where['a.type'] = array("eq",5);
		$where['b.user_id'] = array("eq",$_SESSION['user']['id']);

		$resource_sharing = M("resource_sharing a")->join("LEFT JOIN __TISSUE_GROUP_ACCESS__ b ON a.tissue_id = b.tissue_id")->field("a.source_id")->where($where)->select();

		foreach($resource_sharing as $sharing){
			$course_arrid[] = $sharing['source_id'];
		}

		if(!empty($course_arrid)){
			$conditions['b.sid'] = array("in",$course_arrid);
		}else{
			$conditions['b.sid'] = array("in",array(0));
		}

		if(IS_GET){
			$get['keyword'] = I("get.keyword",'','trim'); //搜索输入框值
			$get['style_search'] = I("get.style_search","",'int');  ////搜索选项值

			$conditions['sname'] = array("like","%".$get['keyword']."%");

			if(!empty($get['style_search'])){
				$conditions['style'] = array("eq",$get['style_search']);
			}

			if(!empty($conditions['sname']) && !empty($conditions['style'])){
				$conditions['_logic'] = 'and';
				$result = M('Supplier_type')
					->alias('a')
					->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
					->where($conditions)
					->order('sid DESC')
					->page($start_page,$total_page)
					->select();
				$count = M('Supplier_type')
					->alias('a')
					->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
					->where($conditions)->count();

			}else{
				$result = M('Supplier_type')
					->alias('a')
					->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
					->where($conditions)
					->order('sid DESC')
					->page($start_page,$total_page)
					->select();
				$count = M('Supplier_type')
					->alias('a')
					->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
					->where($conditions)->count();

			}
		}else{

			$result = M('Supplier_type')
				->alias('a')
				->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
				->order('sid DESC')
				->page($start_page,$total_page)
				->select();
			$count = M('Supplier_type')
				->alias('a')
				->join("LEFT JOIN __SUPPLIER__ b ON a.id = b.sc_type")
				->count();

		}

		// print_r($result);
		foreach($result as $k => $v){
			$v1 = explode(',',$v['main_course']);
			$result[$k]['main_courses'] = $v1['0'];
			$lecturerNum = M('Lecturer')->where(array('type' => 1,'sid'=>$v['sid']))->count();
			$result[$k]['lecturerNum'] = $lecturerNum;
		}

		//隔离数据过滤
		$result = D('IsolationData')->isolationData($result,2);

		$show = $this->pageClass($count,$total_page);

		$data = array(
			"list" =>$result,
			"page"=>$show,
			'xhr' => $ret,
			'keyword' => $get['keyword'],
			'keywords' => $get['style_search'],

		);

		return $data;

	}


	
}