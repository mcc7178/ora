<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/**
 * 配置方案控制器
 * 20171113 Dujunqiang
 */
class SysController extends AdminBaseController
{
    
    /**
     * 方案列表页面
     */
    public function plan(){
    	$get = I("get.");
    	$get["p"] = (int)$get["p"];
    	if($get["p"] < 1) $get["p"] = 1;
    	
    	$data = D("Sys")->plan($get);
    	$data["keyword"] = $get["keyword"];
    	
    	
    	$this->assign($data);
    	$this->display();
    }
    
    /**
     * 新建配置方案
     */
    public function addPlan(){
    	$post = I("post.");
    	if(!$post["plan_name"]){
    		exit(json_encode(array("code"=>1001, "message"=>"方案名称不能为空")));
    	}
    	
    	if(mb_strlen($post["plan_name"], 'utf8') > 50){
    		exit(json_encode(array("code"=>1002, "message"=>"方案名称不能超过50字")));
    	}
    	
    	$resp = D("Sys")->addPlan($post);
    	if($resp["code"] == 1000){
    		exit(json_encode(array("code"=>1000, "message"=>"添加成功", "lastId"=>$resp["lastId"])));
    	}else{
    		exit(json_encode(array("code"=>1003, "message"=>$resp["message"])));
    	}
    }
    
    /**
     * 修改方案名称
     */
    public function editPlan(){
    	$post = I("post.");
    	if(!$post["plan_name"]){
    		exit(json_encode(array("code"=>1001, "message"=>"方案名称不能为空")));
    	}
    	
    	if(mb_strlen($post["plan_name"]) > 50){
    		exit(json_encode(array("code"=>1002, "message"=>"方案名称不能超过50字")));
    	}
    	
    	$post["id"] = (int)$post["id"];
    	if($post["id"] < 1){
    		exit(json_encode(array("code"=>1003, "message"=>"未获取到方案ID")));
    	}
    	
    	$resp = D("Sys")->editPlan($post);
    	if($resp["code"] == 1000){
    		exit(json_encode(array("code"=>1000, "message"=>"修改成功")));
    	}else{
    		exit(json_encode(array("code"=>1003, "message"=>$resp["message"])));
    	}
    }
    
    /**
     * 获取组织架构
     */
    public function getRange(){
    	$plan_id = I("get.plan_id");
    	$plan_id = (int)$plan_id;
    	$treeInfo = D('Sys')->getRange($plan_id);
    	$this->assign('treeInfo',array($treeInfo));
    	$this->display();
    }
    
    /**
     * 保存生效范围
     */
    public function saveRange(){
    	$post = I("post.");
    	$post["plan_id"] = (int)$post["plan_id"];
    	if($post["plan_id"] < 1){
    		exit(json_encode(array("code"=>1001, "message"=>"未获取到方案ID")));
    	}
    	
    	$tissueIds = explode(",", $post["tissueIds"]);
    	if(count($tissueIds) == 0){
    		exit(json_encode(array("code"=>1002, "message"=>"请选择生效范围")));
    	}
    	
    	$post["tissueIds"] = $tissueIds;
    	D("Sys")->saveRange($post);
    	exit(json_encode(array("code"=>1000, "message"=>"保存成功")));
    }
    
    /**
     * 删除方案
     */
    public function delPlan(){
    	$post = I("post.");
    	$post["plan_id"] = (int)$post["plan_id"];
    	if($post["plan_id"] < 1){
    		exit(json_encode(array("code"=>1001, "message"=>"未获取到方案ID")));
    	}
    	
    	D("Sys")->delPlan($post);
    	exit(json_encode(array("code"=>1000, "message"=>"操作成功")));
    }
    
    /**
     * 共享范围组织架构-弹窗使用
     * 
     */
    public function shareRange(){
    	$source_id = I("get.source_id");
    	$type = I("get.type");
    	$source_id = (int)$source_id;
    	$type = (int)$type;
    	$treeInfo = D('Sys')->shareRange($source_id, $type);
    	$this->assign('treeInfo',array($treeInfo));
    	$this->display();
    }
    
}