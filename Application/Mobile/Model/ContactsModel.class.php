<?php
namespace Mobile\Model;

use Think\Model;

/**
 * @author Dujunqiang 20170308
 * 通讯录--22222222
 */

class ContactsModel extends CommonModel {
    protected $tablePrefix = 'think_';
    protected $tableName = 'tissue_rule';

    //自动验证
    protected $_validate = array(
        array('username', 'empty', '用户名不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('username', '5,100', '用户名长度超出限制', Model::EXISTS_VALIDATE, 'length'),
        array('phone', '/1[34578]{1}\d{9}$/', '请输入正确的手机号格式', Model::EXISTS_VALIDATE, 'regex'),
        array('password', 'empty', '密码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('confirm', 'empty', '确认密码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('password', 'checkValid', '密码不能有中文', Model::EXISTS_VALIDATE, 'callback'),
        array('oldPassword', '6,20', '密码长度为6-20位', Model::EXISTS_VALIDATE, 'length'),
        array('password', 'isPassword', '只允许输入6-20位由 数字、字母、下划线组成的密码', Model::EXISTS_VALIDATE, 'callback'),
        array('phone', 'empty', '手机号码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('phone', 'is_numeric', '手机号码必须为数字', Model::EXISTS_VALIDATE, 'function')
    );

	/**
	 * 通讯录首页
	 */
    public function index($user_id){
        if(!$user_id){
            return array("code"=>1021, "message"=>'提交失败，未获取到用户id');
        }
        $part = array();

        //一级
        $tissue1 = M("tissue_rule")->field("id,pid,name")->where("pid=0")->limit(1)->find();
        $part["id"] = $tissue1["id"];
        $part["name"] = $tissue1["name"];

        //获取所属中心
        $tissue = M("tissue_group_access a")->field("b.id,b.pid,b.name")
            ->join("JOIN __TISSUE_RULE__ b ON a.tissue_id=b.id")
            ->where("user_id=".$user_id)->limit(1)->select();
        if($tissue){
            if($tissue[0]["id"] == 1){
                $getPid = "1";
            }else{
                $getPid = self::getRulePid($tissue[0]["id"]);
                $getPid = $getPid["pid"];
            }
        }else{
            return array("code"=>1030, "message"=>'无数据，未添加到组织');
        }

        if($getPid == 1){
            //超级管理员，所有部门
            $tissue2 = M("tissue_rule")->field("id,pid,name")->where("pid=".$getPid)->select();
        }else{
            //其他，所属中心
            $tissue2 = M("tissue_rule")->field("id,pid,name")->where("id=".$getPid)->select();
        }
        $key2 = 0;
        $sub_list = array();
        foreach($tissue2 as $key2=>$value2){
            $sub_list[$key2]["id"] = $value2["id"];
            //$sub_list[$key2]["pid"] = $value2["pid"];
            $sub_list[$key2]["name"] = $value2["name"];
            $sub_list[$key2]["is_part"] = 0;

            //三级（分公司部门）
            $tissue3 = M("tissue_rule")->field("id,pid,name")->where("pid=".$value2["id"])->select();
            if($tissue3){
                $sub_list2 = array();
                $key3 = 0;

                $sub_list2[$key3]["id"] = $value2["id"];
                $sub_list2[$key3]["name"] = "本部门直属人员";
                $key3 ++;

                foreach($tissue3 as $value3){
                    $sub_list2[$key3]["id"] = $value3["id"];
                    $sub_list2[$key3]["name"] = $value3["name"];
                    $key3 ++;
                }

                $sub_list[$key2]["is_part"] = 1;
                $sub_list[$key2]["sub_list"] = $sub_list2;
            }

            $key2 ++;
        }


        $part["part_list"] = $sub_list;
        return array("code"=>1000, "message"=>'操作成功', "data"=>$part);
    }

    /**
     * @获取组织id子类别
     * @Param $cid
     * @Param $cidStr
     * @$ids = self::getCourseChild(11, "11,");
	 * @$ids = substr($ids, 0, -1);
     */
    public function getRuleChild($cid, $cidStr){
        $cid += 0;
        if(!is_int($cid) || $cid < 0){
            return false;
        }

        $cat = M("tissue_rule")->where("pid=".$cid)->select();
        if($cat){
            foreach ($cat as $key=>$v){
                $cidStr .= $v["id"] . ",";
                $cidStr = self::getRuleChild($v["id"], $cidStr);
            }
        }
        return $cidStr;
    }

    //根据用户组织ID获取所在中心
    public function getRulePid($pid){
        $pid += 0;
        if(!is_int($pid)){
            return array("code"=>1031, "message"=>"未获取到组织id");
        }
        $group = M("tissue_rule")->field("id,pid,name")->where("id=".$pid)->select();
        if(!$group){
            return array("pid" => $pid);
        }else{
            if($group[0]["pid"] != 1){
                return self::getRulePid($group[0]["pid"]);
            }else{
                return array("pid" => $group[0]["id"]);
            }
        }
    }

    //获取子类
    public function getCourseChild($cid, $cidStr){
        $cid += 0;
        if(!is_int($cid) || $cid < 0){
            return false;
        }

        $cat = M("tissue_rule")->where("pid=".$cid)->select();
        if($cat){
            foreach ($cat as $key=>$v){
                $cidStr .= $v["id"] . ",";
                $cidStr = self::getCourseChild($v["id"], $cidStr);
            }
        }
        return $cidStr;
    }


    /**
	 * 分组通讯录列表
	 * id 部门id 必须
	 */
	public function groupList($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		
		$thisTissue = M("tissue_rule")->field("id,pid,name")->where("id=".$param["id"])->find();
		$where["b.status"] = 1;
		if($thisTissue["pid"] == 1){
			$where["a.tissue_id"] = $param["id"];
		}else{
			$ids = self::getRuleChild($param["id"], $param["id"].",");
			$ids = substr($ids, 0, -1);
			$where["a.tissue_id"] = array("in", $ids);
		}
		
		$list = M("tissue_group_access a")
			->field("a.tissue_id,a.job_id,b.username,b.id as user_id,b.avatar,b.email,b.phone,b.job_number")
			->join("JOIN __USERS__ b ON a.user_id=b.id")
			->where($where)->select();
		if($list){
			foreach ($list as $key=>$value){
				$name = M("tissue_rule")->where(array("id" => $value["tissue_id"]))->getField('name');
				$list[$key]["part_name"] = $name;
                $_name = M("jobs_manage")->where(array("id" => $value["job_id"]))->getField('name');
				$list[$key]["job_name"] = $_name;
				
				$list[$key]["username"] = $list[$key]["username"]."-".$name;
			}
			return array("code"=>1000, "message"=>'操作成功', "data"=>$list);
		}else{
			return array("code"=>1030, "message"=>'当前部门没有成员');
		}
        /*if(!$param || !$user_id){
            return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
        }

        $param["id"] += 0;
        if(!$param["id"]){
            return array("code"=>1022, "message"=>'缺少参数: id 部门id');
        }

        $list = M("tissue_group_access a")
            ->field("a.tissue_id,a.job_id,b.username,b.id as user_id,b.avatar,b.email,b.phone,b.job_number")
            ->join("JOIN __USERS__ b ON a.user_id=b.id")
            ->where("a.tissue_id=".$param["id"])->select();
        if($list){
            foreach ($list as $key=>$value){
                $part = M("tissue_rule")->field("name")->where("id=".$value["tissue_id"])->find();
                $list[$key]["part_name"] = $part["name"];

                $part = M("jobs_manage")->field("name")->where("id=".$value["job_id"])->find();
                $list[$key]["job_name"] = $part["name"];
            }
            return array("code"=>1000, "message"=>'操作成功', "data"=>$list);
        }else{
            return array("code"=>1023, "message"=>'当前部门没有成员');
        }*/
	}
	
	/**
	 * 联系人详细信息
	 * user_id 联系人id
	 */
	public function detail($user_id){
		$list = M("tissue_group_access a")
			->field("a.tissue_id,a.job_id,b.username,b.id as user_id,b.avatar,b.email,b.phone,b.job_number")
			->join("JOIN __USERS__ b ON a.user_id=b.id")
			->where(array("a.user_id" => $user_id))->find();
		if($list){
			$part = M("tissue_rule")->field("pid,name")->where("id=".$list["tissue_id"])->find();
            //如果是部门则执行这里
            if($part['pid'] != 1){
                $parts = M("tissue_rule")->where("id=".$part['pid'])->find();
                //拼接公司部门
                $list["part_name"] = $parts['name']."-".$part["name"];
            }else{
                //如果是分公司则执行这里
                $list["part_name"] = $part["name"];
            }
			$partg = M("jobs_manage")->field("name")->where("id=".$list["job_id"])->find();
			$list["job_name"] = $partg["name"];
			return array("code"=>1000, "message"=>'操作成功', "data"=>$list);
		}else{
			return array("code"=>1023, "message"=>'没有获取到相应用户，请确认user_id是否属于当前公司');
		}
	}
	
	/**
	 * 联系人查询
	 * keyword 搜索关键字 必须
	 */
	public function search($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		
		if(!$param["keyword"]){
			return array("code"=>1022, "message"=>'缺少参数： keyword 搜索关键字');
		}
		
		$tissue = M("tissue_group_access")->where(array("user_id" => $user_id))->limit(1)->select();
		if(!$tissue){
			return array("code"=>1023, "message"=>'当前用户没有加入组织');
		}
		
		$tissue = M("tissue_group_access a")
			->field("b.id,b.pid,b.name")
			->join("JOIN __TISSUE_RULE__ b ON a.tissue_id=b.id")->where(array("user_id" => $user_id))->find();

		if(!$tissue){
			return array("code"=>1022, "message"=>'您还没有加入组织');
		}
		$pid = $tissue["id"];
		$getPid = self::getRulePid($pid);
		if(!$getPid){
			return array("code"=>1022, "message"=>'您还没有加入组织');
		}
		$getPid = $getPid["pid"];
		
		//获取该pid下的组织i
		$tissueIds = self::getRuleChild($getPid, $getPid.",");
		$tissueIds = substr($tissueIds, 0, -1);
		
		//获取该公司下的所有用户
		$users = M("tissue_group_access a")
			->field("a.tissue_id,a.job_id,b.username,b.id as user_id,b.avatar,b.email,b.phone,b.job_number")
			->join("JOIN __USERS__ b ON a.user_id=b.id")
			->where("a.tissue_id in(".$tissueIds.") AND username LIKE '%".$param["keyword"]."%'")->select();
		if(!$users){
			return array("code"=>1030, "message"=>'没有搜索到用户');
		}

		foreach ($users as $key=>$value){

			$part = M("tissue_rule")->where("id=".$value["tissue_id"])->find();
             //如果是部门则执行这里
            if($part['pid'] != 1){
                $parts = M("tissue_rule")->where("id=".$part['pid'])->find();
                //拼接公司部门
			    $users[$key]["part_name"] = $parts['name']."-".$part["name"];
            }else{
                //如果是分公司则执行这里
                $users[$key]["part_name"] = $part["name"];
            }
            //查询相应岗位
			$partg = M("jobs_manage")->field("name")->where(array("id" => $value["job_id"]))->find();
			$users[$key]["job_name"] = $partg["name"];

		}
		return array("code"=>1000, "message"=>'操作成功', "data"=>$users);
	}
}