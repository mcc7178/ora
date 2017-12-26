<?php
namespace Common\Model;

use Common\Model\BaseModel;
/**
 * ------------------------------------------------------------------------------
 * Description  供应商类别操作模型
 * @filename SupplierTypeModel.class.php
 * @author Andy
 * @datetime 2016-12-25 11:37:29
 * -------------------------------------------------------------------------------
 */
class SupplierTypeModel extends BaseModel {

    //定义表前缀表名，过滤表单字段
    protected $tablePrefix = 'think_';
    protected $tableName = 'supplier_type';
    protected $insertFields = array('id','tname');
    protected $pk = 'id';
    //定义验证规则
    protected $_validate = array(
        array('tname','require','供应商类别名称不能为空',1),
    ); 
    
    /*
     * 供应商类别列表
     */
    public function getSupplierStyle($total_page = 10){
        $start_page = I("get.p",0,'int');
        $keyword = I('get.keyword');
        
        $planId = getPlanId();
        if(!$planId){
        	return false;
        }
        
        $where['plan_id'] = $planId;
		if(!empty($keyword)){
           //供应商类别搜索
           $where['tname'] = array(
               'like','%'.$keyword.'%'
           );
		}
		$result = M('SupplierType')->where($where)->page($start_page,$total_page)->select();
		$count = M('SupplierType')->where($where)->page($start_page,$total_page)->count();
        $show = $this->pageClass($count,$total_page);
	    $data = array(
	        "list" =>$result,
	        "page"=>$show,
	        'keyword' => $keyword,
	    );
	    return $data;
    }
    
    /*
     * 新增供应商擅长类别
    */
    public function addSupplierStyle()
    {
    	$planId = getPlanId();
    	if(!$planId){
	    	return array("code"=>1021, "message"=>"未获取到对应配置方案，请联系管理员处理");
    	}
    	
    	$where['tname'] = I('post.tname');
    	$where['plan_id'] = $planId;
    	$has = M('SupplierType')->where($where)->find();
    	if($has){
	    	return array("code"=>1022, "message"=>"领域名称已存在，请更换");
    	}
    	
    	$data = array();
    	$data['tname'] = I('post.tname');
    	$data['plan_id'] = $planId;
    
    	if(strtolower(C('DB_TYPE')) == 'oracle'){
    		$data['id'] = getNextId('suppliertype');
    	}
    	M('SupplierType')->data($data)->add();
    	return array("code"=>1000, "message"=>"添加成功");
    }
    
    /*
     * 编辑供应商
     */
     public function updateSupplierType(){
         if(IS_POST)
         {
         	
         	$data = array();
         	$data = I("post.");
         	
         	$planId = getPlanId();
         	$where['tname'] = I('post.tname');
         	$where['plan_id'] = $planId;
         	$where['id'] = array("neq", $data['tid']);
         	$has = M('SupplierType')->where($where)->find();
         	if($has){
         		return array("code"=>1022, "message"=>"领域名称已存在，请更换");
         	}
             
             $ret = M('SupplierType')->where(array('id'=>$data['tid']))->save($data);
             return array("code"=>1000, "message"=>"修改成功");
         }else{
             return array("code"=>1023, "message"=>"未获取到提交数据");
         }
     }

}