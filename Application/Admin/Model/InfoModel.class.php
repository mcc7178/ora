<?php 
namespace Admin\Model;
use Common\Model\BaseModel;
/**
 * 个人资料
 * @author Dujuqiang 20170324
 * 
 * update 20170420 增加序列
 * 	员工可自主修改部门岗位
 */
class InfoModel extends BaseModel{
	//初始化
	public function __construct(){}
	
    /**
     * 获取个人资料
     */
	public function infoPage(){
		$user_id = $_SESSION["user"]["id"];
		$user = M("users")->field("id,username,password,avatar,email,email_code,phone,status,
				last_login_ip,last_login_time,job_number,province,city,personalized_signature,
				qrcode,token,token_expires,is_login,audit_time,orderno,objection,sequence,
				gender,age,to_char(group_time,'YYYY-MM-DD') as group_time,to_char(center_time,'YYYY-MM-DD') as center_time,area,room,rank,education,mobilephone,
				tel,ip_phone,register_time,skin,to_char(birthday,'YYYY-MM-DD') as birthday")->where("id=".$user_id)->limit(1)->select();
		if(!$user){
			return array("code"=>1021, "message"=>"未获取到用户");
		}
		
		if($user[0]["job_number"] == 0) $user[0]["job_number"] = "";
		
		//获取所属部门岗位
		$tissue = M("tissue_group_access")->field("tissue_id,job_id,serial_number")->where("user_id=".$user[0]["id"])->limit(1)->select();
		if($tissue){
			$user[0]["job_id"] = $tissue[0]["job_id"];
			$user[0]["part_id"] = $tissue[0]["tissue_id"];
			$user[0]["serial_number"] = $tissue[0]["serial_number"];
			
			$part = M("tissue_rule")->field("name")->where("id=".$tissue[0]["tissue_id"])->limit(1)->select();
			$user[0]["part_name"] = $part[0]["name"];
		
			$part = M("jobs_manage")->field("name")->where("id=".$tissue[0]["job_id"])->limit(1)->select();
			$user[0]["job_name"] = $part[0]["name"];
		}
		
		//获取部门组织架构
		//一级
		$tissue1 = M("tissue_rule")->field("id,pid,name")->where("pid=0")->limit(1)->select();
		$sub_list["id"] = $tissue1[0]["id"];
		$sub_list["name"] = $tissue1[0]["name"];
		foreach($tissue1 as $key=>$value){
			//二级（总公司部门 / 分公司）
			$tissue2 = M("tissue_rule")->field("id,pid,name")->where("pid=".$value["id"])->select();
			$key2 = 0;
			$sub_list = array();
			foreach($tissue2 as $key2=>$value2){
				$sub_list[$key2]["id"] = $value2["id"];
				$sub_list[$key2]["name"] = $value2["name"];
				$sub_list[$key2]["is_part"] = 0;
				
				//三级（分公司部门）
				$tissue3 = M("tissue_rule")->field("id,pid,name")->where("pid=".$value2["id"])->select();
				if($tissue3){
					$key3 = 0;
					$sub_list2 = array();
					foreach($tissue3 as $key3=>$value3){
						$sub_list2[$key3]["id"] = $value3["id"];
						$sub_list2[$key3]["name"] = $value3["name"];
						$key3 ++;
					}
					
					$sub_list[$key2]["is_part"] = 1;
					$sub_list[$key2]["sub_list"] = $sub_list2;
				}
				
				$key2 ++;
			}
		}
		$user[0]["part_list"] = $sub_list;
		
		//获取岗位数据
		$job_list = M("jobs_manage")->select();
		$user[0]["job_list"] = $job_list;

		//获取系统所有的用户标签 users_tag
        $tag_list = M("users_tag")->select();
        foreach($tag_list as $k=>$v){
			$res = M("users_tag_relation")->where(array('user_id'=>$user_id,'tag_id'=>$v['id']))->find();
			if($res){
			  $tag_list[$k]['tag_selected'] = 1;	
			}else{
              $tag_list[$k]['tag_selected'] = 0;
			}
		}
		$user[0]["tag_list"] = $tag_list;

		//获取当前用户所属的标签id和名称
		$user_tag = M("users_tag_relation")
				->alias('a')
				->join('left join __USERS_TAG__ b on a.tag_id=b.id')
				->where(array('a.user_id'=>$user_id))
				->select();
		$user[0]["user_tag"] = $user_tag;
	

		//获取当前用户所属的标签
		foreach($user_tag as $k=>$v){
			$user[0]["tag_ids"][] = $v['tag_id'];
		}

		$user[0]["tag_ids"] = implode(',',$user[0]["tag_ids"]);
		// echo $user[0]["tag_ids"];
		return $user[0];
	}
	
	/**
	 * 保存修改
	 * avatar 头像
	 * username 姓名
	 * email 邮箱
	 * imgType 头像是否修改 验证base64资源图片
	 */
	public function save($param){
		$user_id = $_SESSION["user"]["id"];
			
		if(!empty($param['tag_ids']) && $param['tag_ids'] != "undefined"){
            $param['tag_ids'] = explode(",",$param['tag_ids']);
		}else{
			$param['tag_ids'] = "";
		}
	
		//唯一性判断
		if($param["job_number"] > 0){
			$has = M("users")->where("id!=".$user_id." and job_number=".$param["job_number"])->limit(1)->select();
			if($has){
				return array("code"=>1021, "message"=>"当前工号已存在，请更换");
			}
		}
		/* if($param["serial_number"] != "" && $param["serial_number"] != 0){
			$has = M("tissue_group_access")->where("user_id!=".$user_id." and serial_number='".$param["serial_number"]."'")->limit(1)->select();
			if($has){
				return array("code"=>1021, "message"=>"当前序列号已存在，请联系管理员更换");
			}
		} */

		if(!empty($param['phone'])){

			$where['id'] = array("neq",$user_id);

			$where['phone'] = array("eq",$param['phone']);

			$is_phone = M("Users")->where($where)->find();

			if($is_phone){
				return array("code"=>1021, "message"=>"当前手机号已存在，请更换");
			}
		}
		
		if($param["imgType"] == 1){
			$fileName = date("Ymd").uniqid().".png";
			$image = explode(',', $param["avatar"]);
			$image = $image[1];
			$image = base64_decode($image);
			$result = file_put_contents("./Upload/avatar/".$fileName, $image);
			if($result > 0){
				$_SESSION['user']['avatar'] = "/Upload/avatar/".$fileName;
				$data["avatar"] = "/Upload/avatar/".$fileName;
			}else{
				return array("code"=>1021, "message"=>"头像上传失败");
			}
		}
		
		@D('Trigger')->intergrationTrigger($user_id, 3);
		
		/*
		* part_name:部门id
		* job_name:1 岗位id
		* serial_number: 序列
		* job_number:666111 工号
		*/
        
		$data["username"] = $param["username"];
		$_SESSION['user']['username'] = $param["username"];

		//$data["email"] = $param["email"];
		$data["job_number"] = $param["job_number"];
		
		$data["gender"] = $param["gender"];
		
		$data["birthday"] = array('exp',"to_date('".$param["birthday"]."','yy-mm-dd hh24:mi:ss')");
		
		// $data["age"] = $param["age"];
        $data["age"] = $this->getAge($param["birthday"]);
		$data["group_time"] = $param["group_time"];
		$data["center_time"] = $param["center_time"];
		$data["area"] = $param["area"];
		$data["room"] = $param["room"];
		$data["rank"] = $param["rank"];
		$data["education"] = $param["education"];
        $data["tel"] = $param["tel"];
		$data["phone"] = $param["phone"];
		$data["ip_phone"] = $param["ip_phone"];
		
        //用户表信息修改
		//$edit = M("users")->where("id=".$user_id)->limit(1)->save($data);
		  $edit = M("users")->where(array('id'=>$user_id))->save($data);
		  
		//部门、岗位信息修改
		//$tdata["tissue_id"] = $param["part_name"];
		//$tdata["job_id"] = $param["job_name"];
		//$tdata["serial_number"] = $param["serial_number"];
		//$edit = M("tissue_group_access")->where("user_id=".$user_id)->limit(1)->save($tdata);
		
		//用户标签修改
		M('users_tag_relation')->where(array('user_id'=>$user_id))->delete();
		if(!empty($param['tag_ids'])){
			foreach($param['tag_ids'] as $k=>$v){
				$tagdata = array(
						'user_id'=>$user_id,
						'tag_id'=>$v
					);
			
	              M('users_tag_relation')->add($tagdata); 
				
	
			 }
        }
		return array("code"=>1000, "message"=>"成功");
	}
	
	/**
	 * 修改密码
	 * 
	 */
	public function editPass_bak($param){
		$user_id = $_SESSION["user"]["id"];
		$pass = $param["newPass"];
		
		//使用密码加密规则
		$pass = md5($pass);
		$resp = M("users")->where("id=".$user_id)->limit(1)->save(array("password"=>$pass));
		return array("code"=>1000, "message"=>"成功");
	}

	/**
	 * 修改密码
	 * 
	 */
	public function editPass($param){
		$uwhere["id"] = $_SESSION['user']['id'];
		
		$user = M("users")->field("id,password")->where($uwhere)->find();
		
		if(!$user){
			return array("code"=>1021, "message"=>"未获取到用户");
		}
		
		$oldPwd = md5($param["oldPwd"]);
		if($oldPwd != $user["password"]){
			
			return array("code"=>1022, "message"=>"旧密码有误");
		}
		
		//获取最近三次使用的密码
		// if(strtolower(C('DB_TYPE')) == 'oracle'){
		// 	$sql = "SELECT * FROM (
		// 		SELECT thinkphp.*, rownum FROM (
		// 			SELECT max(id) as maxId,enter_password from (
		//     			SELECT id,enter_password from __USER_PASSWORD_LOCK__ where user_id='".$user["id"]."' and status='1'
		//     		) group by enter_password order by maxId desc
	    // 		) thinkphp
    	// 	) WHERE rownum<4";
		// 	$getLog = M()->query($sql);
		// }else{
		// 	$where2["user_id"] = $user["id"];
		// 	$where2["status"] = 1;
		// 	$getLog = M("user_password_lock")->field("distinct(enter_password)")->where($where2)->order("id desc")->limit(3)->select();
		// }
		
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

  	/**
	 * 根据出生日期计算年龄
	 * $birthday date
	 */
	public function getAge($birthday){
		list($year,$month,$day) = explode("-",$birthday);
		$year_diff = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff  = date("d") - $day;
		if ($day_diff < 0 || $month_diff < 0)
		$year_diff--;
		return $year_diff;
		}	
	
}