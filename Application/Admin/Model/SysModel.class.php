<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 
 * @author Dujunqiang 20171114
 *
 */
class SysModel extends BaseModel {
     /**
      * 供应商方案页面
      */
     public function plan($param){
     	if($param["keyword"]){
     		$where["plan_name"] = array("like", "%".$param["keyword"]."%");
     	}
     	
     	$pageLen = 10;
     	$start = ($param["p"] - 1) * $pageLen;
     	
     	$plan = M("sys_plan")->where($where)->order("id desc")->limit($start, $pageLen)->select();
     	
     	//输出分页
		$count = M("sys_plan")->field("count(id) as num")->where($where)->select();
		$pageNav = $this->pageClass($count[0]["num"], $pageLen);
     	
     	return array("list"=>$plan, "pageNav"=>$pageNav);
     }
     
     /**
      * 新建配置方案
      */
     public function addPlan($param){
		//判断是否重复
		$where["plan_name"] = $param["plan_name"];
		$has = M("sys_plan")->where($where)->select();
		if($has){
			return array("code"=>1021, "message"=>"方案名称已存在请更换");
		}
		
     	$data["plan_name"] = $param["plan_name"];
    	if(strtolower(C('DB_TYPE')) == 'oracle'){
			$data['id'] = getNextId('sys_plan');
		}
		
		$lastId = M("sys_plan")->add($data);
		if($lastId){
			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$lastId = $data['id'];
			}
			
			write_login_log(7, 2, $param["plan_name"]);//添加配置写入日志
			
			return array("code"=>1000, "message"=>"添加成功", "lastId"=>$lastId);
		}else{
			return array("code"=>1022, "message"=>"添加失败");
		}
     }
     
     /**
      * 修改方案名称
      */
     public function editPlan($param){
     	//判断是否重复
     	$where["plan_name"] = $param["plan_name"];
     	$where["id"] = array("neq", $param["id"]);
     	$has = M("sys_plan")->where($where)->select();
     	if($has){
     		return array("code"=>1021, "message"=>"方案名称已存在请更换");
     	}
     	
     	$data["plan_name"] = $param["plan_name"];
     	$where2["id"] = $param["id"];
     	M("sys_plan")->where($where2)->save($data);
     	
     	write_login_log(7, 3, $param["plan_name"]);//编辑配置写入日志
     	
     	return array("code"=>1000, "message"=>"操作成功");
     }
     
     /**
      * 获取组织架构
      * checkStatus 0可选 1不可选 2选中状态
      */
     public function getRange($plan_id){
     	$treeInfo = D('AdminTissue')->tree(1);
     	$has = M("sys_tissue")->where("tissue_id=".$treeInfo["id"])->select();
     	$treeInfo["checkStatus"] = 0;
     	if($has){
     		if($has[0]["plan_id"] == $plan_id){
     			$treeInfo["checkStatus"] = 2;
     		}else{
	     		$treeInfo["checkStatus"] = 1;
     		}
     	}
     	
     	//----二级组织---
     	foreach ($treeInfo["_data"] as $key1=>$value1){
     		$has = M("sys_tissue")->where("tissue_id=".$value1["id"])->select();
     		$treeInfo["_data"][$key1]["checkStatus"] = 0;
     		if($has){
     			if($has[0]["plan_id"] == $plan_id){
	     			$treeInfo["_data"][$key1]["checkStatus"] = 2;
     			}else{
     				$treeInfo["_data"][$key1]["checkStatus"] = 1;
     			}
     		}
     		
     		//----三级组织---
     		foreach ($value1["_data"] as $key2=>$value2){
     			$has = M("sys_tissue")->where("tissue_id=".$value2["id"])->select();
     			$treeInfo["_data"][$key1]["_data"][$key2]["checkStatus"] = 0;
     			if($has){
     				if($has[0]["plan_id"] == $plan_id){
		     			$treeInfo["_data"][$key1]["_data"][$key2]["checkStatus"] = 2;
     				}else{
	     				$treeInfo["_data"][$key1]["_data"][$key2]["checkStatus"] = 1;
     				}
     			}
     		
     			//----四级组织---
     			foreach ($value2["_data"] as $key3=>$value3){
     				$has = M("sys_tissue")->where("tissue_id=".$value3["id"])->select();
     				$treeInfo["_data"][$key1]["_data"][$key2]["_data"][$key3]["checkStatus"] = 0;
     				if($has){
	     				if($has[0]["plan_id"] == $plan_id){
	     					$treeInfo["_data"][$key1]["_data"][$key2]["_data"][$key3]["checkStatus"] = 2;
	     				}else{
		     				$treeInfo["_data"][$key1]["_data"][$key2]["_data"][$key3]["checkStatus"] = 1;
	     				}
     				}
     			}
     		}
     	}
     	
     	return $treeInfo;
     }
     
     /**
      * 保存生效范围
      */
     public function saveRange($param){
     	//删除已有的
     	$where["plan_id"] = $param["plan_id"];
     	M("sys_tissue")->where($where)->delete();
     	
     	foreach ($param["tissueIds"] as $value){
     		$tissue_id = (int)$value;
     		if($tissue_id > 0){
     			$has = M("sys_tissue")->where("tissue_id=".$tissue_id)->find();
     			if($has){
     				continue;
     			}
     			
	     		$data["plan_id"] = $param["plan_id"];
	     		$data["tissue_id"] = $value;
	     		if(strtolower(C('DB_TYPE')) == 'oracle'){
	     			$data['id'] = getNextId('sys_tissue');
	     		}
	     		
	     		M("sys_tissue")->add($data);
     		}
     	}
     	
     	write_login_log(7, 3, $param["plan_name"]."修改范围");//编辑配置写入日志
     }
     
     /**
      * 删除方案
      */
     public function delPlan($param){
     	$where["id"] = $param["plan_id"];
     	
     	$getLogName = M("sys_plan")->where($where)->find();
     	$getLogName = $getLogName["plan_name"];
     	write_login_log(7, 4, $getLogName);//删除配置写入日志
     	
     	M("sys_plan")->where($where)->delete();
     	
     	$where2["plan_id"] = $param["plan_id"];
     	M("sys_tissue")->where($where2)->delete();
     }
     
     /**
      * 获取组织架构
      * checkStatus 0可选 1不可选 2选中状态
      */
     public function shareRange($source_id, $type){
     	$treeInfo = D('AdminTissue')->tree(1);
     	$has = M("resource_sharing")->where("tissue_id=".$treeInfo["id"]." and type=$type and source_id=$source_id")->select();
     	$treeInfo["checkStatus"] = 0;
     	if($has){
     		$treeInfo["checkStatus"] = 2;
     	}
     
     	//----二级组织---
     	foreach ($treeInfo["_data"] as $key1=>$value1){
     		$has = M("resource_sharing")->where("tissue_id=".$value1["id"]." and type=$type and source_id=$source_id")->select();
     		$treeInfo["_data"][$key1]["checkStatus"] = 0;
     		if($has){
     			$treeInfo["_data"][$key1]["checkStatus"] = 2;
     		}
     
     		//----三级组织---
     		foreach ($value1["_data"] as $key2=>$value2){
     			$has = M("resource_sharing")->where("tissue_id=".$value2["id"]." and type=$type and source_id=$source_id")->select();
     			$treeInfo["_data"][$key1]["_data"][$key2]["checkStatus"] = 0;
     			if($has){
     				$treeInfo["_data"][$key1]["_data"][$key2]["checkStatus"] = 2;
     			}
     
     			//----四级组织---
     			foreach ($value2["_data"] as $key3=>$value3){
     				$has = M("resource_sharing")->where("tissue_id=".$value3["id"]." and type=$type and source_id=$source_id")->select();
     				$treeInfo["_data"][$key1]["_data"][$key2]["_data"][$key3]["checkStatus"] = 0;
     				if($has){
     					$treeInfo["_data"][$key1]["_data"][$key2]["_data"][$key3]["checkStatus"] = 2;
     				}
     			}
     		}
     	}
     
     	return $treeInfo;
     }
}