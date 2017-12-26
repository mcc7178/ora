<?php
namespace Mobile\Model;

use Think\Model;

/**
 * @author Dujunqiang 20170308
 * 我的学员--我的调研
 */

class HFiveModel extends CommonModel {
    protected $tablePrefix = 'think_';
    protected $tableName = 'admin_project';
    
    /**
     * 日程提醒
     */
    public function calendar($user_id){
    	if(!$user_id){
    		return false;
    	}

		if(strtolower(C('DB_TYPE')) == 'oracle'){

			//我的考试任务
			$course = M("project_examination a")->field("a.project_id,b.project_name,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")
				->join("LEFT JOIN __ADMIN_PROJECT__ b ON a.project_id=b.id")
				->join("LEFT JOIN __DESIGNATED_PERSONNEL__ c ON a.project_id=c.project_id")
				->where("b.type=0 OR b.type=4 AND c.user_id=".$user_id)->order("a.start_time desc")->limit(30)->select();
		}else{
			//我的考试任务
			$course = M("project_examination a")->field("a.project_id,b.project_name,a.start_time,a.end_time")
				->join("LEFT JOIN __ADMIN_PROJECT__ as b ON a.project_id=b.id")
				->join("LEFT JOIN __DESIGNATED_PERSONNEL__ as c ON a.project_id=c.project_id")
				->where("b.type=0 OR b.type=4 AND c.user_id=".$user_id)->order("a.start_time desc")->limit(30)->select();
		}

    	$data = array();

		$key = 0;
    	foreach ($course as $value){
    		$data[$key]["type"] = 1;
    		$data[$key]["start_time"] = $value["start_time"];
    		$data[$key]["end_time"] = $value["end_time"];
    		$key ++;
    	}

		if(strtolower(C('DB_TYPE')) == 'oracle'){

			//我的调研任务
			$survey = M("project_survey a")->field("a.project_id,project_name,to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")
				->join("JOIN __ADMIN_PROJECT__ b ON a.project_id=b.id")
				->join("JOIN __DESIGNATED_PERSONNEL__ c ON a.project_id=c.project_id")
				->where("b.type=0 OR b.type=4 AND c.user_id=".$user_id)->order("a.start_time desc")->limit(30)->select();

		}else{

			//我的调研任务
			$survey = M("project_survey a")->field("a.project_id,project_name,a.start_time,a.end_time")
				->join("JOIN __ADMIN_PROJECT__ as b ON a.project_id=b.id")
				->join("JOIN __DESIGNATED_PERSONNEL__ as c ON a.project_id=c.project_id")
				->where("b.type=0 OR b.type=4 AND c.user_id=".$user_id)->order("a.start_time desc")->limit(30)->select();
		}


    	foreach ($survey as $value){
    		$data[$key]["type"] = 2;
    		$data[$key]["start_time"] = $value["start_time"];
    		$data[$key]["end_time"] = $value["end_time"];
    		$key ++;
    	}

		if(strtolower(C('DB_TYPE')) == 'oracle'){
			//系统调研任务
			$research = M("research")->field("id,research_name,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")->where("audit_state=1")->order("start_time desc")->select();
		}else{

			//系统调研任务
			$research = M("research")->field("id,research_name,start_time,end_time")->where("audit_state=1")->order("start_time desc")->select();

		}

    	foreach ($research as $value){
    		$data[$key]["type"] = 2;
    		$data[$key]["start_time"] = $value["start_time"];
    		$data[$key]["end_time"] = $value["end_time"];
    		$key ++;
    	}
    	
    	return $data;
    }

    public function integrationList($plan_id,$userId){
        $list1 = M('integration_rule')->where(array('plan_id' => $plan_id))->field('id,name,score,oneday_score,type')->order('id asc')->select();
        if($list1){//如果是查询到该用户所在的组织有应用到方案的，则执行这里
            $_list = M('integration_rule')->where(array('plan_id' => $plan_id))->field('id,name,score,oneday_score,type,plan_id')->order('id asc')->select();
            foreach($_list as $k=>$v){
                if(strpos($v['oneday_score'],'/') === false){     //使用绝对等于
                    //不包含
                }else{
                    //包含
                    $arr = explode('/',$v['oneday_score']) ;
                    $_list[$k]['oneday_score'] = intval($arr[0])/intval($arr[1])*30;
                }
            }
            //调用公共Model里的方法,登录系统积分触发
            $res = D('Trigger')->intergrationTrigger($userId, 4);
        }else{
            //没有查询到该用户所在组织有配置方案，则从 integration_rule_default 表获取默认数据
            $_list = M('integration_rule_default')->field('id,name,score,oneday_score,type,plan_id')->order('id asc')->select();
            //查询积分默认表数据，然后把获取到的数据添加到积分规则表
            foreach($_list as $k => $v){
                $v['plan_id'] = $plan_id;
                $res = M('integration_rule')->add($v);
            }
            foreach($_list as $k=>$v){
                if(strpos($v['oneday_score'],'/') === false){//使用绝对等于
                    //不包含
                }else{
                    //包含
                    $arr = explode('/',$v['oneday_score']) ;
                    $_list[$k]['oneday_score'] = $arr[0]/intval($arr[1])*30;
                }
            }

        }
        return $_list;
    }
}
