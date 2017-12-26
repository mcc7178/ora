<?php
namespace Home\Model;
use Think\Model;

/**
 * Class PublicModel
 * @package Home\Model
 * User: @Andy-lizhongjian
 */
class ResetpwdModel extends Model {
    
	public function reset($param){
		$uwhere["email"] = $param["email"];
		$uwhere["status"] = array("neq", 3);
		$user = M("users")->field("id,password")->where($uwhere)->find();
		if(!$user){
			return array("code"=>1021, "message"=>"当前邮箱未获取到用户");
		}
		
		$oldPwd = md5($param["oldPwd"]);
		if($oldPwd != $user["password"]){
			return array("code"=>1022, "message"=>"旧密码有误");
		}
		
		//获取最近三次使用的密码
		$where2["user_id"] = $user["id"];
		$getLog = M("pwd_history")->where($where2)->order("id desc")->limit(3)->select();
		
		//print_r($getLog);
		
		$newPwd = md5($param["password"]);
		foreach ($getLog as $value){
			if($newPwd == $value["password"]){
				return array("code"=>1022, "message"=>"请勿与最近三次使用的密码相同");
				break;
			}
		}
		
		$where3["id"] = $user["id"];
		$data["password"] = $newPwd;
		$resp = M("users")->where($where3)->save($data);
		
		write_pwd_history($user["id"], $newPwd);
		
		if($resp){
			return array("code"=>1000, "message"=>"密码修改成功");
		}else{
			return array("code"=>1023, "message"=>"修改失败，请稍后重试");
		}
	}
}