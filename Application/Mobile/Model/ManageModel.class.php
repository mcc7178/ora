<?php
namespace Mobile\Model;

use Think\Model;

/**
 * @author Andy 20170828
 * 培训管理
 */
class ManageModel extends CommonModel{
	protected $tablePrefix = 'think_';
	protected $tableName = 'admin_project';
    protected $_validate = array(
        array('project_id','require','项目id参数不能为空!',Model::EXISTS_VALIDATE), //默认情况下用正则进行验证
        array('project_id','checkId','项目id参数必须为非负整数！',Model::EXISTS_VALIDATE,'callback',1), //默认情况下用正则进行验证
        array('course_id','require','请选择课程!',Model::EXISTS_VALIDATE), //默认情况下用正则进行验证
        array('course_id','checkId','课程id参数必须为非负整数！',Model::EXISTS_VALIDATE,'callback',1), //默认情况下用正则进行验证
        array('trainingCategory','require','培训项目类型值的必须！',Model::EXISTS_VALIDATE),
        array('trainingCategory',array(0,1,2),'培训项目类型值的范围不正确！',2,'in'),
        array('planCategory', 'require', '培训项目计划内外必填', Model::EXISTS_VALIDATE),
        array('planCategory',array(0,1,2),'培训项目计划内外范围不正确！',2,'in'),
        array('project_name', 'require', '项目名称不能为空', Model::EXISTS_VALIDATE),
        array('project_name', '2,20', '用户名长度为2-20个字符组成', Model::EXISTS_VALIDATE, 'length'),
        array('start_time', 'require', '开始时间不能为空', Model::EXISTS_VALIDATE),
        array('start_time', 'timeVerify', '开始时间格式不正确', Model::EXISTS_VALIDATE, 'callback'),
        array('end_time', 'require', '结束时间不能为空', Model::EXISTS_VALIDATE),
        array('end_time', 'timeVerify', '结束时间格式不正确', Model::EXISTS_VALIDATE, 'callback'),
        array('start_time', 'time_verify', '开始时间必须大于当前时间', Model::EXISTS_VALIDATE, 'callback',1),
        array('end_time', 'time_verify', '结束时间必须大于当前时间', Model::EXISTS_VALIDATE, 'callback',1),
        array('start_time,end_time', 'seTimeVerify', '结束时间必须大于开始时间', Model::EXISTS_VALIDATE, 'callback',1),
        array('project_budget', 'require', '项目预算不能为空', Model::EXISTS_VALIDATE),
        array('project_budget', 'is_numeric', '项目预算必须为数字', Model::EXISTS_VALIDATE,'function',1),
        array('join_users', 'require', '项目指定参与人必选', Model::EXISTS_VALIDATE),
        array('join_users', 'checkJoinUser', '项目指定参与人格式不正确',Model::EXISTS_VALIDATE,'function',1),
        array('location', 'require', '面授课程地址必须填写', Model::EXISTS_VALIDATE),
        array('project_description', 'require', '简介必须填写', Model::EXISTS_VALIDATE),
        array('location', '2,30', '面授课上课地址需在2-30个字符', Model::EXISTS_VALIDATE,'length'),
        array('project_description', 'checkDescription', '简介支持汉字字母数字下划线，长度2-200', Model::EXISTS_VALIDATE,'callback',1),
        array('password', 'isPassword', '只允许输入6-20位由 数字、字母、下划线组成的密码', Model::EXISTS_VALIDATE, 'callback'),
        array('repassword','password','确认密码不正确',0,'confirm'),
        array('confirm', 'require', '确认密码不能为空', Model::EXISTS_VALIDATE),
        array('phone', 'empty', '手机号码不能为空', Model::EXISTS_VALIDATE),
        array('email', 'require', '邮箱不能为空', Model::EXISTS_VALIDATE),
        array('emailCode','require', '邮箱验证码不能为空',Model::EXISTS_VALIDATE),
        array('email', 'email', '邮箱格式不正确', Model::EXISTS_VALIDATE),
        array('emailCode', 'code', '邮箱验证码不正确',0,'confirm'),

    );


    /**
     * 时间格式验证
     * @param $time
     * @return  Boolean
     */
    public function timeVerify($time){
        if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $time)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 开始时间和结束时间与当前时间比较
     * @param $time
     * @return Boolean
     */
    public function time_verify($time){
        if(strtotime($time) < time()){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 比较开始时间和结束时间
     * @param $data
     * @return Boolean
     */
    public function seTimeVerify($data){
        if(strtotime($data['start_time']) >= strtotime($data['end_time'])){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 验证所有id参数的方法
     * @param $id
     * @return bool
     */
    public function checkId($id){
        if($id < 0){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 校验参与人格式
     * @param $join_users
     */
    public function checkJoinUser($join_users){
        if(!preg_match("/^([0-9]|,){1,}$/", $join_users)){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 校验项目简介数据输入合法性
     * @param $project_description
     * @return Boolean
     */
    public function checkDescription($project_description){
        if(!preg_match('/^([a-zA-Z]|[\x{4e00}-\x{9fa5}]|_|\.|[0-9]){2,200}$/u', $project_description)){
            return false;
        }else{
            return true;
        }
    }


	//验证是否有权限审核
	//think_auth_group用户组表
	//think_auth_group_access 用户组明细表 这两个表也可以判断权限 ???
	public function judgeUser($user_id){
		$role = M("tissue_group_access")->field("manage_id")->where("user_id=".$user_id)->find();
		if(!$role){
			return array("code"=>1022, "message"=>'您还没有加入组织');
		}
		/*if($role[0]["manage_id"] == 0){
			return array("code"=>1023, "message"=>'您的身份为普通学员，没有权限审核');
		}*/
		return array("code"=>1000, "message"=>'身份符合');
	}
	
	/**
	 * 培训班首页-培训班列表
	 */
	public function index($param,$user_id){
		if(!$param || !$user_id){
		}
		
		$judgeUser = self::judgeUser($user_id);
		if($judgeUser["code"] != 1000){
			return array("code"=>$judgeUser["code"], "message"=>$judgeUser["message"]);
		}
		
		$proList = M("admin_project")->field("id as project_id,project_name,start_time,end_time,class_name,population")->where("user_id=".$user_id)->order('id DESC')->select();
		return array("code"=>1000, "message"=>'操作成功', "data"=>$proList);
	}

    /**************************************兼容oracle*********************************************/
    /**
     *新建培训班
     *@param $userId
     *@param $data
     */
    public function createTrainingClass($data,$userId){
        //去除字符串后面的英文逗号，使用explode函数，用英文逗号把字符串组装成数组
        $arr_join_users = explode(',', rtrim($data['join_users'], ','));
        //统计项目指定的参与人数
        $joinUsersNum = count($arr_join_users);
        $orderNo = D('Trigger')->orderNumber(1,$userId);
        //插入项目表
        $project_items = array(
            "project_name" => $data['project_name'],//培训班名称
            "class_name" => '',//班级，默认为空字符串
            "project_description" => $data['project_description'],//项目描述
            "project_covers" => '',//默认为空字符串
            "project_budget" => $data['project_budget'],//项目预算
            "is_public" => 1,//默认是公开
            "population" => $joinUsersNum,//项目指定参与人数
            "type" => 2,//状态，默认为待审核
            "user_id" => $userId,//项目发起人
            "tissue_id" => '',//指定部门，app默认为空字符串
            "training_category" => $data['trainingCategory'],//培训类型
            "plan_category" => $data['planCategory'],//计划内外
            "orderno" => $orderNo,//工单号
            'auth_user_id' => $userId//创建者
        );

        if (strtolower(C('DB_TYPE')) == 'oracle') {
            $project_items['id'] = getNextId('admin_project');
            $project_items['add_time'] = array('exp', "to_date('" . date('Y-m-d H:i:s') . "','yy-mm-dd hh24:mi:ss')");
            $project_items['start_time'] = array('exp', "to_date('" . $data['start_time'] . "','yy-mm-dd hh24:mi:ss')");
            $project_items['end_time'] = array('exp', "to_date('" . $data['end_time'] . "','yy-mm-dd hh24:mi:ss')");
        } else {
            $project_items['add_time'] = date("Y-m-d H:i:s", time());
            $project_items['start_time'] = $data['start_time'];
            $project_items['end_time'] = $data['end_time'];
        }
        $project_id = M('admin_project')->data($project_items)->add();
        //把项目关联的指定人员插入指定人员表
        //开启事务，循环插入保证数据完整性
        if (!empty($project_id)){
            try {
                $DB = M('designated_personnel');
                $DB->startTrans();
                foreach ($arr_join_users as $k => $user_id) {
                    $personnel_data['user_id'] = $user_id;
                    $personnel_data['project_id'] = $project_id;
                    if (strtolower(C('DB_TYPE')) == 'oracle') {
                        $personnel_data['id'] = getNextId('designated_personnel');
                    }
                    $id = $DB->data($personnel_data)->add();
                    if ($id) {
                        $DB->commit();
                    }
                }
                return $project_id;
            } catch (Exception $e) {

                $DB->rollback();
            }
        }
    }


	/**
	 * 新建培训班
	 * project_name 培训班名称
	 * start_time 开始时间
	 * end_time 结束时间
	 * join_users 指定参与人user_id,英文逗号隔开 123,124,125,....
	 * project_budget 项目预算
	 * project_description 培训班简介
	 * project_id 项目Id，-------修改时用到
	 */
	public function creates($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		
		$judgeUser = self::judgeUser($user_id);
		if($judgeUser["code"] != 1000){
			return array("code"=>$judgeUser["code"], "message"=>$judgeUser["message"]);
		}
		
		if($param["project_id"] > 0){
			$project = M("admin_project")->field("type,end_time")->where("id=".$param["project_id"])->limit(1)->select();
			if(!$project){
				return array("code"=>1022, "message"=>'未获取到课程所属的项目');
			}
			if($project[0]["type"] == 0){
				return array("code"=>1023, "message"=>'项目进行中，不能修改');
			}
			if($project[0]["type"] == 4){
				return array("code"=>1023, "message"=>'项目已完成，不能修改');
			}
			
			$db = M("admin_project");
			$data["project_name"] = $param["project_name"];
			$data["start_time"] = $param["start_time"];
			$data["end_time"] = $param["end_time"];
			$data["project_budget"] = $param["project_budget"];
			$data["project_description"] = $param["project_description"];
			$data["type"] = 2;
			$return = $db->where("project_id=".$param["project_id"])->limit(1)->save($data);
			if($return < 0 || $return === false){
				return array("code"=>1033,"message"=>'提交失败，可尝试重新操作');
			}
			$createId = $param["project_id"];
			
			//删除原有关联用户
			M("designated_personnel")->where("project_id=".$param["project_id"])->delete();
		}else{
			$db = M("admin_project");
			$data["user_id"] = $user_id;
            $data['auth_user_id'] = $user_id;//创建数据者id
			$data["project_name"] = $param["project_name"];
			$data["start_time"] = $param["start_time"];
			$data["end_time"] = $param["end_time"];
			$data["project_budget"] = $param["project_budget"];
			$data["project_description"] = $param["project_description"];
			$data["type"] = 2;
			$data["add_time"] = date("Y-m-d H:i:s");
			$createId = $db->add($data);
			if($createId < 0 || $createId === false){
				return array("code"=>1033,"message"=>'提交失败，可尝试重新操作');
			}else{
                $res = self::getTaskData($createId,1);
                if(!$res) {
                    return array("code" => 1033, "message" => '提交失败，可尝试重新操作');
                }
            }
		}
		
		if($param["join_users"] != ""){
			$join_users = explode(",", $param["join_users"]);
			foreach ($join_users as $value){
				$value += 0;
				if(is_int($value) && $value > 0){
					$personnel = M("designated_personnel");
					$pData["project_id"] = $createId;
					$pData["user_id"] = $value;
					$personnel->add($pData);
				}
			}
		}
		
		$return = array("project_id"=>$createId);

		return array("code"=>1000, "message"=>'创建成功', "data"=>$return);
	}
	
	//根据用户ID获取公司第一层级
	public function getRulePid($pid){

		if(!is_int($pid) && $pid < 0){
			return array("code"=>1031, "message"=>"未获取到组织id");
		}
           //如果pid为0则是最高级即是总公司，则返回对应的id
        if($pid == 0){
            $group = M("tissue_rule")->field("id,pid,name")->where(array('pid' => 0))->select();
            return array("pid" => $group[0]["id"]);
        }else{
            // //如果pid不为0则不是最高级(总公司)，则需要进行递归查找后返回对应的id
            $group = M("tissue_rule")->field("id,pid,name")->where("id=".$pid)->select();
            return self::getRulePid($group[0]["pid"]);
        }
       /* dump($group);exit;
		if(!$group){
			return array("pid" => $pid);
		}else{
			if(!strstr($group[0]["name"], "分公司") || strstr(!$group[0]["name"], "公司")){
				return self::getRulePid($group[0]["pid"]);
			}else{
				return array("pid" => $group[0]["id"]);
			}
		}*/
	}
	
	/**
	 * 获取负责人列表
	 *
	 */
	public function getManage($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		
		$db = M("tissue_group_access as a");
		$tissue = $db->field("pid")->join("JOIN __TISSUE_RULE__ as b ON a.tissue_id=b.id")->where("user_id=".$user_id)->limit(1)->select();
		if(!$tissue){
			return array("code"=>1022, "message"=>'您还没有加入组织');
		}
		$pid = $tissue[0]["pid"];
		
		$getPid = self::getRulePid($pid);
		if(!$getPid){
			return array("code"=>1022, "message"=>'您还没有加入组织');
		}
		$getPid = $getPid["pid"];
		
		//获取该pid下的组织id（三层）
		$tissueIds = "";
		$tissueList = M("tissue_rule")->field("id")->where("pid=".$getPid)->select();
		foreach ($tissueList as $tk=>$tv){
			$tissueIds .= $tv["id"].",";
			$subList = M("tissue_rule")->field("id")->where("pid=".$tv["id"])->select();
			foreach ($subList as $sv){
				$tissueIds .= $sv["id"].",";
			}
		}
		
		$tissueIds = substr($tissueIds,0,-1);
		if($tissueIds == ""){
			$tissueIds = $getPid;
		}
		//获取该公司下的所有负责人
		$list = M("tissue_group_access as a")->field("a.user_id,b.username,b.avatar")
			->join("JOIN __USERS__ as b ON a.user_id=b.id")	
			->where("a.tissue_id in(".$tissueIds.")")->select();
		
		return array("code"=>1000, "message"=>'成功', "data"=>$list);
	}


    /**************************************************兼容oracle新建培训班获取指定人员******************************************************************/
	/**
	 * @获取参与人列表--分组查询，按部门树状结构列出
	 * @param $project_id 项目id，创建不传，修改传
     * @param $keyword
     * @param $user_id
	 */
	public function getJoin($project_id,$keyword,$user_id){
		/*if(!$project_id || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}*/
		//根据当前登录用户id查询其所属组织的pid和者所属组织
		$tissue = M("tissue_group_access a")
            ->field("pid")
            ->join("JOIN __TISSUE_RULE__  b ON a.tissue_id=b.id")->where("user_id=".$user_id)->find();
        if(!$tissue){
			return array("code"=>1023, "message"=>'您还没有加入组织');
		}
		$pid = $tissue["pid"];
		//递归查询所属组织的上一层组织
		$getPid = self::getRulePid($pid);
		if(!$getPid){
			return array("code"=>1024, "message"=>'您还没有加入组织');
		}
		$getPid = $getPid["pid"];
		
		//获取该pid下的组织id（三层）
		$tissueIds = "";
		$groupList = array();
		$groupKey = 0;

		$tissueList = M("tissue_rule")->field("id,name")->where("pid=".$getPid)->select();

		foreach ($tissueList as $tv){
			$groupList[$groupKey]["tissue_name"] = $tv["name"];
			$groupList[$groupKey]["tissue_id"] = $tv["id"];
			$groupKey ++;
            if(!empty($keyword)){
                $condition['b.username'] = array('like','%'.$keyword.'%');
            }
            $condition['a.tissue_id'] = array('eq',$tv["id"]);

			$list = M("tissue_group_access a")->field("a.user_id,b.username,b.avatar")
				->join("JOIN __USERS__  b ON a.user_id=b.id")
				->where($condition)->select();
			foreach ($list as $lv){
				$groupList[$groupKey]["tissue_name"] = $tv["name"];
				$groupList[$groupKey]["tissue_id"] = $tv["id"];
				$groupList[$groupKey]["user_id"] = $lv["user_id"];
				$groupList[$groupKey]["username"] = $lv["username"];
				$groupList[$groupKey]["avatar"] = $lv["avatar"];
				$groupKey ++;
			}
			
			$subList = M("tissue_rule")->field("id,name")->where("pid=".$tv["id"])->select();
			foreach ($subList as $sv){
				$groupList[$groupKey]["tissue_name"] = $sv["name"];
				$groupList[$groupKey]["tissue_id"] = $sv["id"];
				$groupKey ++;
				//搜索
                if(!empty($keyword)){
                    $where['b.username'] = array('like','%'.$keyword.'%');
                    //$where['b.username'] = array('like',$keyword);
                }
                $where['a.tissue_id'] = array('eq',$sv["id"]);
				$list2 = M("tissue_group_access  a")->field("a.user_id,b.username,b.avatar")
					->join("JOIN __USERS__  b ON a.user_id=b.id")
					->where($where)->select();
				foreach ($list2 as $lv2){
					$groupList[$groupKey]["tissue_name"] = $sv["name"];
					$groupList[$groupKey]["tissue_id"] = $sv["id"];
					$groupList[$groupKey]["user_id"] = $lv2["user_id"];
					$groupList[$groupKey]["username"] = $lv2["username"];
					$groupList[$groupKey]["avatar"] = $lv2["avatar"];
					$groupKey ++;
				}
			}
		}
		//如果是执行编辑操作，则会携带project_id参数，过滤已经选择了的人员，以防重复选择
		if($project_id > 0){
			foreach ($groupList as $key=>$value){
				if(isset($value["user_id"])){
					$hasJoin = M("designated_personnel")->field("user_id")->where("project_id=".$project_id." AND user_id=".$value["user_id"])->find();
					if($hasJoin){
						$groupList[$key]["join_status"] = 1;
					}else{
						$groupList[$key]["join_status"] = 0;
					}
				}
			}
		}
		return array("code"=>1000, "message"=>'操作成功', "data"=>$groupList);
	}


    /**************************************************兼容oracle新建培训班获取指定人员******************************************************************/
	/**
	 * 添加课程-获取选择面授或在线课程
	 * @param $course_way
     * @param $userId
	 */
	public function getCourse($course_way,$userId){

            switch(intval($course_way)){
                case 1;
                    //如果讲师id为0，则讲师名称则取lecturer_name字段的值
                    $where["course_way"] = 0;//在线
                    $where["status"] = 1;//审核通过
                    $where["auditing"] = 1;//已启用
                    $course = M("Course")->field("id as course_id,course_name,create_time,lecturer as lecturer_id,lecturer_name")->where($where)->select();
                    foreach ($course as $key=>$value){
                        if(!empty($value["lecturer_id"])){
                            $_lecturer_name = M("lecturer")->where(array("id" => $value["lecturer_id"]))->getField('name');
                            $course[$key]["lecturer_name"] = $_lecturer_name;
                        }
                    }
                    return $this->success(1000, '获取成功', $course);
                    break;
                case 2;
                    $where["course_way"] = 1;//面授
                    $where["status"] = 1;//审核通过
                    $where["auditing"] = 1;//已启用

                    $course = M("course")->field("id as course_id,course_name,create_time,lecturer as lecturer_id")->where($where)->select();
                    foreach ($course as $key=>$value){
                        $_lecturer_name = M("lecturer")->field("name")->where(array("id" => $value["lecturer_id"]))->getField('name');
                        $course[$key]["lecturer_name"] = $_lecturer_name;
                    }
                    return $this->success(1000, '获取成功', $course);
                    break;
                default:
                    return $this->error(1030,'授课方式参数错误');
            }
	}
	
	/**
	 * 添加课程-选择授课讲师
	 * 
	 */
	public function getLector($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		
		$lector = M("lecturer")->field("id as lecturer_id,name,user_id,type,level")->select();
		return array("code"=>1000, "message"=>'操作成功', "data"=>$lector);
	}


    /**************************************************兼容oracle新建培训班获取指定人员******************************************************************/
	/**
	 * 添加课程-保存课程
	 * project_id 项目id
	 * course_id 课程id
	 * lecturer_name 课程名称
	 * lecturer_id 讲师id
	 * start_time 开始时间
	 * end_time 结束时间
	 * location 授课地址
	 * is_attachment 考勤 (0-关闭,1-开启)
	 * edit_id 修改数据对应id 修改时需传入
	 */
	public function saveCourse($param,$user_id,$type){


        //判断用户是否具有合适身份
        $judgeUser = self::judgeUser($user_id);
        if($judgeUser["code"] != 1000){
            return array("code"=>$judgeUser["code"], "message"=>$judgeUser["message"]);
        }
        //判断当前项目是否选择相同课程
        $repeatWhere["project_id"] = $param["project_id"];
        $repeatWhere["course_id"] = $param["course_id"];
        $repeat = M("project_course")->field("project_id")->where($repeatWhere)->find();
        if($repeat){
            return array("code"=>1030, "message"=>"当前项目已选择过该课程，请换一个");
        }
		if($repeat){
            //执行修改操作
            if (strtolower(C('DB_TYPE')) == 'oracle') {
                $param['start_time'] = array('exp', "to_date('" . $param['start_time'] . "','yy-mm-dd hh24:mi:ss')");
                $param['end_time'] = array('exp', "to_date('" . $param['end_time'] . "','yy-mm-dd hh24:mi:ss')");
            }
            $result = M("project_course")->where(array("id" => $param["id"]))->save($param);

        }else{
            //执行添加操作
            if (strtolower(C('DB_TYPE')) == 'oracle') {
                $param['id'] = getNextId('project_course');
                $param['start_time'] = array('exp', "to_date('" . $param['start_time'] . "','yy-mm-dd hh24:mi:ss')");
                $param['end_time'] = array('exp', "to_date('" . $param['end_time'] . "','yy-mm-dd hh24:mi:ss')");
            }
            $result = M("project_course")->data($param)->add();
        }
        if($result){
             return array("code"=>1000, "message"=>"操作成功");
        }else{
             return array("code"=>1030, "message"=>"操作失败");
        }
	}

    /********************************************兼容oracle************************************************************/
	/**
	 * 项目验证 是否可以修改新增
	 * 判断项目是否存在 && 项目通过审核 （通过审核的不能再编辑）
	 * $project_id 项目id
	 */
	public function projectVerifi($project_id){
        if (strtolower(C('DB_TYPE')) == 'oracle') {
            $field = "type,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time  ,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
        }else{
            $field = "type,start_time,end_time";
        }
		$project = M("admin_project")->field($field)->where(array("id" => $project_id)) -> find();
		if(!$project){
			return array("code"=>1030, "message"=>'未获取到所属的项目');
		}
		if($project["type"] == 0){
			return array("code"=>1030, "message"=>'项目进行中，不能修改');
		}
		if($project["type"] == 4){
			return array("code"=>1030, "message"=>'项目已完成，不能修改');
		}
		return array("code"=>1000, "message"=>'项目可操作', "project"=>$project);
	}
	
	/**
	 * 获取试卷
	 */
	public function getTest(){
		$where["status"] = 1;
		$where["is_available"] = 1;
		$test = M("examination")->field("id as test_id,test_name,test_cat_id,test_score")->where($where)->select();
		return array("code"=>1000, "message"=>'操作成功', "data"=>$test);
	}
	
	/**
	 * 保存考试
	 * project_id 项目id
	 * test_id 试卷id 
	 * manager_id 负责人id
	 * start_time 开始时间
	 * end_time 结束时间
	 */
	public function saveExam($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		
		$judgeUser = self::judgeUser($user_id);
		if($judgeUser["code"] != 1000){
			return array("code"=>$judgeUser["code"], "message"=>$judgeUser["message"]);
		}
		
		$projectVerifi = self::projectVerifi($param["project_id"]);
		if($projectVerifi["code"] != 1000){
			return array("code"=>1021, "message"=>$projectVerifi["message"]);
		}
		
		if(strtotime($param["start_time"]) < strtotime($projectVerifi["project"]["start_time"]) || strtotime($param["start_time"]) > strtotime($projectVerifi["project"]["end_time"])){
			return array("code"=>1022, "message"=>"考试开始时间必须在项目时间范围内");
		}
		
		if(strtotime($param["end_time"]) < strtotime($projectVerifi["project"]["start_time"]) || strtotime($param["end_time"]) > strtotime($projectVerifi["project"]["end_time"])){
			return array("code"=>1023, "message"=>"考试结束时间必须在项目时间范围内");
		}
		
		$has = M("project_examination")->where("project_id=".$param["project_id"]." AND test_id=".$param["test_id"])->limit(1)->select();
		$data["project_id"] = $param["project_id"];
		$data["test_id"] = $param["test_id"];
		$data["start_time"] = $param["start_time"];
		$data["end_time"] = $param["end_time"];
		$data["credits"] = $param["credits"];
		$data["test_length"] = $param["test_length"];
		$data["manager_id"] = $param["manager_id"];
		$data["test_names"] = $param["test_names"];
		if($has){
			$return = M("project_examination")->where("project_id=".$param["project_id"]." and test_id=".$param["test_id"])->limit(1)->save($data);
			if($return === false){
				return array("code"=>1022, "message"=>"修改失败，请稍后重试");
			}
			return array("code"=>1000, "message"=>"修改成功");
		}else{
			$return = M("project_examination")->add($data);
			if($return < 1 || $return === false){
				return array("code"=>1022, "message"=>"添加失败，请稍后重试");
			}
			return array("code"=>1000, "message"=>"添加成功");
		}
	}
	
	/**
	 * 添加调研 / 评估 - 选择问卷
	 */
	public function getSurvey($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		
		$where["status"] = 1;
		$where["is_available"] = 1;
		$list = M("survey")->field("id as survey_id,survey_name,survey_cat_id,survey_score")->where($where)->select();
		return array("code"=>1000, "message"=>'操作成功', "data"=>$list);
	}
	
	/**
	 * 添加调研 / 评估 - 保存调研
	 * project_id 项目id
	 * survey_id 调研id 
	 * manager_id 负责人id
	 * start_time 开始时间
	 * end_time 结束时间
	 */
	public function saveSurvey($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		$judgeUser = self::judgeUser($user_id);
		if($judgeUser["code"] != 1000){
			return array("code"=>$judgeUser["code"], "message"=>$judgeUser["message"]);
		}
		
		$projectVerifi = self::projectVerifi($param["project_id"]);
		if($projectVerifi["code"] != 1000){
			return array("code"=>1021, "message"=>$projectVerifi["message"]);
		}
		
		if(strtotime($param["start_time"]) < strtotime($projectVerifi["project"]["start_time"]) || strtotime($param["start_time"]) > strtotime($projectVerifi["project"]["end_time"])){
			return array("code"=>1022, "message"=>"调研开始时间必须在项目时间范围内");
		}
		
		if(strtotime($param["end_time"]) < strtotime($projectVerifi["project"]["start_time"]) || strtotime($param["end_time"]) > strtotime($projectVerifi["project"]["end_time"])){
			return array("code"=>1023, "message"=>"调研结束时间必须在项目时间范围内");
		}
		
		$has = M("project_survey")->where("project_id=".$param["project_id"]." AND survey_id=".$param["survey_id"])->limit(1)->select();
		$data["project_id"] = $param["project_id"];
		$data["survey_id"] = $param["survey_id"];
		$data["start_time"] = $param["start_time"];
		$data["end_time"] = $param["end_time"];
		$data["credits"] = $param["credits"];
		$data["manager_id"] = $param["manager_id"];
		$data["survey_names"] = $param["survey_names"];
		if($has){
			$return = M("project_survey")->where("project_id=".$param["project_id"]." and survey_id=".$param["survey_id"])->limit(1)->save($data);
			if($return === false){
				return array("code"=>1022, "message"=>"修改失败，请稍后重试");
			}
			return array("code"=>1000, "message"=>"修改成功");
		}else{
			$return = M("project_survey")->add($data);
			if($return < 1 || $return === false){
				return array("code"=>1022, "message"=>"添加失败，请稍后重试");
			}
			return array("code"=>1000, "message"=>"添加成功");
		}
	}
	
	/**
	 * 获取已添加的（课程 / 考试 / 调研问卷） 列表
	 * project_id 项目ID
	 */
    /*public function getAddList($param,$user_id){
        if(!$param || !$user_id){
            return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
        }
        $where["project_id"] = $param["project_id"];
        $course = M("project_course")->field("id as edit_id")->where($where)->select();
        $exam = M("project_examination")->field("test_id")->where($where)->select();
        $survey = M("project_survey")->field("survey_id")->where($where)->select();
        $list = array();
        $key = 0;
        foreach ($course as $value){
            $list[$key] = $value;
            $list[$key]["proType"] = 1;
            $list[$key]["project_id"] = $param["project_id"];
            $key ++;
        }
        foreach ($exam as $value){
            $list[$key] = $value;
            $list[$key]["proType"] = 2;
            $list[$key]["project_id"] = $param["project_id"];
            $key ++;
        }
        foreach ($survey as $value){
            $list[$key] = $value;
            $list[$key]["proType"] = 3;
            $list[$key]["project_id"] = $param["project_id"];
            $key ++;
        }

        return array("code"=>1000, "message"=>'操作成功', "data"=>$list);
    }*/

    /********************************************兼容oracle************************************************************/
    /**
     * 获取课程，考试，调研的编辑数据
     * $proType  该字段区分课程，考试，调研 1课程 2考试 3调研
     */
    public function getUpdateDetail($proType,$project_id,$editId,$userId){

        //查询项目关联的课程详情
        if($proType == 1){
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                if (strtolower(C('DB_TYPE')) == 'oracle') {
                    $field = "to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time  ,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
                    $info_data = M('Project_course')->field($field)->where(array('project_id'=>$project_id,'course_id'=>$editId))->find();
                    $info = M('Project_course')->where(array('project_id'=>$project_id,'course_id'=>$editId))->find();
                    $info['start_time'] = $info_data['start_time'];
                    $info['end_time'] = $info_data['end_time'];
                }else{
                    $info = M('Project_course')->where(array('project_id'=>$project_id,'course_id'=>$editId))->find();
                }
            }
            //查询该课程关联的讲师
            if(!empty($info['lecturer_id'])){
                $lecturer_name = M('Lecturer')->where(array('id'=>$info['lecturer_id']))->field('name')->find();
                $info['lecturer_name'] = $lecturer_name['name'];
            }else{
                $info['lecturer_name'] = '没有指定的讲师';
            }
            $info['type'] = 1;

        }
        //查询项目关联的考试详情
        if($proType == 2){
            if (strtolower(C('DB_TYPE')) == 'oracle') {
                $field = "to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time  ,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
                $info_data = M('Project_examination')->field($field)->where(array('project_id'=>$project_id,'test_id'=>$editId))->find();
                $info = M('Project_examination')->where(array('project_id'=>$project_id,'test_id'=>$editId))->find();
                $info['start_time'] = $info_data['start_time'];
                $info['end_time'] = $info_data['end_time'];
            }else{
                $info = M('Project_examination')->where(array('project_id'=>$project_id,'test_id'=>$editId))->find();
            }
            //获取试卷名称
            $test_name = M('Examination')->where(array('id'=>$editId))->field('test_name')->find();
            $info['test_name'] = $test_name['test_name'];
            //查询该考试指定的负责人
            if(!empty($info['manager_id'])){
                $manager_name = M('Users')->where(array('id'=>$info['manager_id']))->field('username')->find();
                $info['manager_name'] = $manager_name['username'];
            }else{
                $info['manager_name'] = '没有指定的负责人';
            }
            $info['type'] = 2;
        }
        //查询项目关联的调研详情
        if($proType == 3){
            if (strtolower(C('DB_TYPE')) == 'oracle') {
                $field = "to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time  ,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
                $info_data = M('Project_survey')->field($field)->where(array('project_id'=>$project_id,'survey_id'=>$editId))->find();
                $info = M('Project_survey')->where(array('project_id'=>$project_id,'survey_id'=>$editId))->find();
                $info['start_time'] = $info_data['start_time'];
                $info['end_time'] = $info_data['end_time'];
            }else{
                $info = M('Project_survey')->where(array('project_id'=>$project_id,'survey_id'=>$editId))->find();
            }
            //获取调研名称
            $survey_name = M('Survey')->where(array('id'=>$editId))->field('survey_name')->find();
            $info['survey_name'] = $survey_name['survey_name'];
            //查询该调研指定的负责人
            if(!empty($info['manager_id'])){
                $manager_name = M('Users')->where(array('id'=>$info['manager_id']))->field('username')->find();
                $info['manager_name'] = $manager_name['username'];
            }else{
                $info['manager_name'] = '没有指定的负责人';
            }
            $info['type'] = 3;
        }
        return $info;
    }
	
	/**
	 * 项目详情查看
	 * project_id 项目ID
	 * seeType 查看类型 0查看项目介绍 1查看课程 2查看考试 3查看调研
	 */
	public function seeDetail($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		$where["a.project_id"] = $param["project_id"];
		if($param["seeType"] == 0){

			$list = M("admin_project")->field("*,id as project_id")->where("id=".$param["project_id"])->limit(1)->select();
            $list[0]['project_budget'] = round($list[0]['project_budget']).'元';
            $join_users = M("users as a")->field("a.username,a.id")->join("JOIN __DESIGNATED_PERSONNEL__ as b ON a.id=b.user_id")->where("b.project_id=".$param["project_id"])->select();
            $users = "";
            $join_user_id = "";
           //循环拼接指定参与人
			foreach ($join_users as $key=>$uv){
				if($uv["username"]){
					$users .= $uv["username"].",";
					$join_user_id .= $uv["id"].",";
				}
			}

			$users = substr($users, 0, -1);
            $join_user_id = substr($join_user_id, 0, -1);
			$list[0]["users"] = $users;
			$list[0]["join_user_id"] = $join_user_id;
			
		}elseif($param["seeType"] == 1){
            //课程
			$list = M("project_course as a")->field("a.*")
				//->join("RIGHT JOIN __COURSE__ as b ON a.course_id=b.id")
				->where($where)->select();

            $join_users = M("users as a")->field("a.username,a.id")->join("JOIN __DESIGNATED_PERSONNEL__ as b ON a.id=b.user_id")->where("b.project_id=".$param["project_id"])->count();
            $join_sum = $join_users;


            foreach ($list as $key=>$value){
                //如果课程id是不存在的
                if($value['course_id'] != '' || $value['course_id'] != null){
                    $course = M("course")->field("course_name")->where("id=".$value["course_id"])->find();
                    $list[$key]["course_names"] = $course["course_name"];
                }else{
                    $list[$key]["course_names"] = '暂无课程名称';
                }

                if($value['lecturer_id'] != '' || $value['lecturer_id'] != null){
                    $lecturer = M("lecturer")->field("name")->where("id=".$value["lecturer_id"])->find();
                    $list[$key]["lecturer_name"] = $lecturer["name"];//讲师名称
                }else{
                    $list[$key]["lecturer_name"] = '暂无安排讲师';//讲师名称
                }
                $list[$key]["join_sum"] = $join_sum;//参与人数
			}

		}elseif($param["seeType"] == 2){
            //考试
			$list = M("project_examination as a")->field("a.*,b.test_name")
				->join("RIGHT JOIN __EXAMINATION__ as b ON a.test_id=b.id")
				->where($where)->select();
			foreach ($list as $key=>$value){
				if(!$value["test_names"] || $value["test_names"] == ""){
					//$value["test_names"] = $value["test_name"];
                    $list[$key]['test_names'] = $list[$key]["test_name"];
				}
				unset($list[$key]["test_name"]);
				
				$user = M("users")->field("username")->where("id=".$value["manager_id"])->limit(1)->select();
				$list[$key]["manager_name"] = $user[0]["username"];
			}
		}elseif($param["seeType"] == 3){
            //调研
			$list = M("project_survey as a")->field("a.*,b.survey_name")
				->join("JOIN __SURVEY__ as b ON a.survey_id=b.id")
				->where($where)->select();
			foreach ($list as $key=>$value){
				if(!$value["survey_names"] || $value["survey_names"] == ""){
					//$value["survey_names"] = $value["survey_name"];
                    $list[$key]['survey_names'] = $list[$key]["survey_name"];
				}
				unset($list[$key]["survey_name"]);
				
				$user = M("users")->field("username")->where("id=".$value["manager_id"])->limit(1)->select();
				$list[$key]["manager_name"] = $user[0]["username"];
			}
		}
		return $list;
	}
	
	/**
	 * 编辑项目--获取编辑数据
	 * project_id 项目ID
	 */
	public function getEdit($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
		$project = M("admin_project")->where("id=".$param["project_id"]." AND user_id=".$user_id)->limit(1)->select();
		if(!$project){
			return array("code"=>1022, "message"=>'提交失败，当前项目id未获项目数据');
		}
		return array("code"=>1000, "message"=>'操作成功', "data"=>$project);
	}
	
	/**
	 *
	 */
	public function exampleAction($param,$user_id){
		if(!$param || !$user_id){
			return array("code"=>1021, "message"=>'提交失败，未获取到参数或用户数据');
		}
	}
}