<?php
namespace Home\Controller;

use Common\Controller\HomeBaseController;
use \Tool\Qiniu\Storage\UploadManager;
use \Tool\Qiniu\Auth;

/**
 */
class ResetpwdController extends HomeBaseController {
	public function index(){
		$email = I("get.email");
		$data["email"] = $email;
		$this->assign($data);
		$this->display("Index/resetpwd");
	}
	
	/*
	 *重置密码 
	*/
	public function reset(){
		$post = I("post.");
		if(!preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $post["email"])){
			exit(json_encode(array("code"=>1001, "message"=>"邮箱格式有误")));
		}
		$post["oldPwd"] = self::aseDecode($post["oldPwd"]);
		$post["password"] = self::aseDecode($post["password"]);
		$post["rePwd"] = self::aseDecode($post["rePwd"]);
		
		$pwdAuth = pwdAuth($post["password"]);
		if($pwdAuth["code"] != 1000){
			exit(json_encode($pwdAuth));
		}
		
		if($post["oldPwd"] == ""){
			exit(json_encode(array("code"=>1001, "message"=>"旧密码不能为空")));
		}
		
		if($post["oldPwd"] == $post["password"]){
			exit(json_encode(array("code"=>1002, "message"=>"新密码不能和旧密码一样")));
		}

		$sendCode = session("sendCode");
		$sendCode = $sendCode["code"];
		if($post["code"] != $sendCode){
			exit(json_encode(array("code"=>1004, "message"=>"验证码有误")));
		}
		
		$resp = D("Resetpwd")->reset($post);
		exit(json_encode($resp));
	}
	
	public function aseDecode($pwd){
		//解密key
		$privateKey = "04eb029e72b446e7";
		$iv = "04eb029e72b446e7";
		
		//解密
		$encryptedData = base64_decode($pwd);
		$clearText = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
		return trim($clearText);
	}
	
}