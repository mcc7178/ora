<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 个人资料
 * @author Dujuqiang 20170324
 */
class InfoController extends AdminBaseController{
	/*
	 * 个人资料页
	 */
	public function infoPage(){
		$user_id = $_SESSION["user"]["id"];
		if(!$user_id){
			echo "未获取到用户，请登录";
			exit;
		}
		$data = D("Info")->infoPage();
		// dump($data['tag_list']);
		// dump($data['user_tag']);
		$this->assign($data);
    	$this->display();
    }
    
    /**
     * 保存修改
     * avatar 头像
     * username 姓名
     * email 邮箱
     * imgType 头像是否修改 验证base64资源图片
     * part_name:部门id
     * job_name:1 岗位id
     * serial_number: 序列
     * job_number:666111 工号
     */
    public function save(){
    	$post = I("post.");
    	$post["username"] = str_replace(" ", "", $post["username"]);
    	$post["serial_number"] = str_replace(" ", "", $post["serial_number"]);
    	
    	if(!$post["username"]){
    		$return = array("code"=>1011, "message"=>"请填写用户名称");
    		echo json_encode($return);
    		return;
    	}
    	
    	if(mb_strlen($post["username"],'utf8') > 10){
    		$return = array("code"=>1011, "message"=>"用户名称最长10个字符");
    		echo json_encode($return);
    		return;
    	}

    	if(mb_strlen($post["area"],'utf8') > 10){
    		$return = array("code"=>1011, "message"=>"区域最长10个字符");
    		echo json_encode($return);
    		return;
    	}
       /*
	   if($post["email"] == ''){
            $return = array("code"=>1011, "message"=>"邮箱为必填项");
    		echo json_encode($return);
    		return;
	   }
    	if($post["email"] && !filter_var($post["email"], FILTER_VALIDATE_EMAIL)){
    		$return = array("code"=>1011, "message"=>"邮箱格式有误");
    		echo json_encode($return);
    		return;
    	}*/
    	
    	if($post["imgType"] == 1){
    		if(!strstr($post["avatar"], "data:image")){
    			$return = array("code"=>1022, "message"=>"上传的头像有误，请重新选择");
    			echo json_encode($return);
    			return;
    		}
    	}
    	
    	$post["part_name"] += 0;
    	$post["job_name"] += 0;
    	$post["job_number"] += 0;
    	if($post["part_name"] > 0 && !is_int($post["part_name"])){
    		$return = array("code"=>1022, "message"=>"请选择部门");
    		echo json_encode($return);
    		return;
    	}
    	
    	if($post["job_name"] > 0 && !is_int($post["job_name"])){
    		$return = array("code"=>1022, "message"=>"请选择岗位");
    		echo json_encode($return);
    		return;
    	}
    	
    	if($post["job_number"] && (!is_int($post["job_number"]) || $post["job_number"] < 1)){
    		$return = array("code"=>1022, "message"=>"工号必须为大约0的整数");
    		echo json_encode($return);
    		return;
    	}
    	
    	if(mb_strlen($post["serial_number"],'utf8') > 10){
    		$return = array("code"=>1011, "message"=>"序列最长10个字符");
    		echo json_encode($return);
    		return;
    	}
    	if(mb_strlen($post["job_number"],'utf8') > 10){
    		$return = array("code"=>1011, "message"=>"工号最长10个字符");
    		echo json_encode($return);
    		return;
    	}
    	if(mb_strlen($post["room"],'utf8') > 10){
    		$return = array("code"=>1011, "message"=>"科室最长10个字符");
    		echo json_encode($return);
    		return;
    	}
    	if(mb_strlen($post["rank"],'utf8') > 10){
    		$return = array("code"=>1011, "message"=>"职级最长10个字符");
    		echo json_encode($return);
    		return;
    	}
        
		// if(!empty($post["mobilephone"])){ }
    	/* if((strlen($post["mobilephone"]) > 0 ) && ((strlen($post["mobilephone"]) != 11 ) || (!is_numeric($post["mobilephone"])))){
    		$return = array("code"=>1011, "message"=>"手机号码应该为11位数字");
    		echo json_encode($return);
    		return;
    	} */
    	
    	if($post["phone"] && !preg_match("/^1[0-9]{10}$/", $post["phone"])){
    		$return = array("code"=>1011, "message"=>"手机号码格式有误");
    		echo json_encode($return);
    		return;
    	}
    	
    	if($post["birthday"] && !preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $post["birthday"])){
    		$return = array("code"=>1011, "message"=>"生日格式有误");
    		echo json_encode($return);
    		return;
    	}
        
		if(strlen($post["ip_phone"]) > 20){
    		$return = array("code"=>1011, "message"=>"IP电话最长20个字符");
    		echo json_encode($return);
    		return;
    	}
        // if(!empty($post["tel"])){
        // if( !$this->funcphone($post["tel"])){
		//     $return = array("code"=>1011, "message"=>"请输入正确的办公号码");
    	// 	echo json_encode($return);
    	// 	return;
		// }
		// }
		
    	$resp = D("Info")->save($post);
    	if($resp["code"] != 1000){
    		$return = array("code"=>1023, "message"=>$resp["message"]);
    		echo json_encode($return);
    		return;
    	}
    	
    	$return = array("code"=>1000, "message"=>"修改成功");
    	echo json_encode($return);
    	return;
    }
    

		public function funcphone($str)//电话号码正则表达试
		{
	    
         return (preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$str))?true:false;
		
		
		} 

    /**
     * 修改密码
     * rePass
     * newPass
     * oldPass
     */
    public function editPass(){
    	$post = I("post.");
		
    	$post["oldPwd"] = self::aseDecode($post["oldPwd"]);
		$post["password"] = self::aseDecode($post["password"]);
		$post["rePwd"] = self::aseDecode($post["rePwd"]);
		
		
		$pwdAuth = self::pwdAuth($post["password"]);

    	if($pwdAuth["code"] != 1000){
			$this->ajaxReturn($pwdAuth);
		
		}
		
		if($post["oldPwd"] == $post["password"]){
			$return = array("code"=>1002, "message"=>"新密码不能和旧密码一样");
			$this->ajaxReturn($return);
			
		}

    	$resp = D("Info")->editPass($post);
		
		$this->ajaxReturn($resp);
    
    	
    	
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
	
	public function pwdAuth($pwd){
		if(strlen($pwd) < 8 || strlen($pwd) > 20){
			return array("code"=>"1021", "message"=>"密码长度为8-20位");
		}
		
		if(!preg_match("/^(\d|\w|\.|_|@|\/|#){8,20}$/", $pwd)){
			return array("code"=>"1021", "message"=>"新密码须含以下任意三项：数字；大写字母；小写字母；特殊字符（限：._@/#）");
		}
		
		$passNum = 0;
		if(preg_match("/[0-9]{1,20}/", $pwd)){
			$passNum ++;
		}
		if(preg_match("/[a-z]{1,20}/", $pwd)){
			$passNum ++;
		}
		if(preg_match("/[A-Z]{1,20}/", $pwd)){
			$passNum ++;
		}
		
		$specialChar = array(".","_","@","/","#");
		foreach($specialChar as $value){
			if(strstr($pwd, $value)){
				$passNum ++;
				break;
			}
		}
		
		if($passNum < 3){
			return array("code"=>"1021", "message"=>"新密码须含以下任意三项：数字；大写字母；小写字母；特殊字符（限：._@/#）");
		}
		
		return array("code"=>"1000", "message"=>"格式无误");
	}
    
    public function test(){
    	$fileName = date("Ymd").uniqid().".png";
    	$image = 'data:image/png;base64,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';
    	$image = explode(',', $image);
    	$image = $image[1];
    	$image = base64_decode($image);
    	$result = file_put_contents("./Upload/avatar/1024/".$fileName, $image);
    	var_dump($result);
    }
}