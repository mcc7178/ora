<?php
namespace Common\Model;
use Think\Model;
/**
 * 触发器公共模型
 */
class TriggerModel extends Model{
     protected $tableName = 'users';

    /**
     *  积分事件触发公共模型
     * @$i 积分规则类型id
     * $plan_id 方案id
     */
    public function intergrationTrigger($user_id,$i){
         $plan_id = getPlanId($user_id);//获取方案id

        if(!$plan_id){
            $info = '未获取到方案id！';
            return $info;
        }else{
            $data = $this->rule($i,$plan_id);

            $arr = array(
                'time'=>time(),
                'user_id'=>$user_id,
                'score'=>$data['score'],
                'type'=>$data['type'],
                'describe'=>$data['name'],
            );
            $res = $this->intergrationrule($user_id,$i);
  
            // 当天或当月所得积分未封顶则插入记录
            if($res){
                if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $arr['id'] = getNextId('integration_record');
                }
                $ret = M('integration_record')->add($arr);
                if($ret){
                    $info = $data['name'].','.'恭喜你获得'.$data['score'].'积分！';
                    return $info;
                }else{
                    $info = '数据库发生错误！';
                    return $info;
                }
            }else{
                $info = '\''.$data['name'].'\''.'规则'.'已达到封顶积分！';
                return $info;
            }
        }
    }



    /**
     *  积分事件触发的封顶限制
     */
    public function intergrationrule($user_id,$i){
        $plan_id = getPlanId($user_id);//获取方案id
        $data = $this->rule($i,$plan_id);
        if($data){
            $where = array(
                'user_id'=>$user_id,
                'describe'=>$data['name'],
                '_logic'=>'and'
            );
            if($i == 3 || $i == 4){
                if(C('DB_TYPE') == "mysqli") {
                    //获取当月的登录系统的积分记录之和
                    $months = date("Ym");
                    $this_month_score = M('integration_record')
                        ->field("user_id,sum(score) as sumscore,FROM_UNIXTIME(time,'%Y%m') months")
                        ->where($where)->group('months')
                        ->having('months='.$months)
                        ->select();
                    $this_month_score = $this_month_score[0]['sumscore'];
                }else {
                    //oracle-获取当月的登录系统的积分记录之和
                    $months_start_time = strtotime(date("Y-m-1 00:00:00")); //本月第一天时间戳
                    $months_end_time = strtotime(date("Y-m-t 23:59:59")); //本月  天时间戳
                    $where['time'] = array('between',array($months_start_time,$months_end_time));
                    $this_month_score = M('integration_record')->where($where)->sum('score');

                }
                $this_month_score = $this_month_score<0 || $this_month_score =='' ? 0 : $this_month_score;
                //获取登录系统的积分规则-封顶积分-当月
                $month_score = explode('/',$data['oneday_score']);
                $month_score = $month_score[0];
                if($this_month_score < $month_score){
                    return true;
                }else{
                    return false;
                }

            }else{
                if(C('DB_TYPE') == "mysqli") {
                    //获取当天的登录系统的积分记录之和
                    $day = date("Ymd");
                    $this_day_score = M('integration_record')
                        ->field("user_id,sum(score) as sumscore,FROM_UNIXTIME(time,'%Y%m%d') months")
                        ->where($where)
                        ->group('months')
                        ->having('months='.$day)
                        ->select();
                    $this_day_score = $this_day_score[0]['sumscore'];

                }else{
                    //oracle-获取当天的登录系统的积分记录之和
                    $day_start_time = strtotime(date("Y-m-d 00:00:00")); //当天时间戳
                    $day_end_time = strtotime(date("Y-m-d 23:59:59")); //当天时间戳
                    $where['time'] = array('between',array($day_start_time,$day_end_time));
                    $this_day_score = M('integration_record')->where($where)->sum('score');
                }

                $this_day_score = $this_day_score<0 || $this_day_score =='' ? 0 : $this_day_score;
                //获取登录系统的积分规则-封顶积分-当天
                $oneday_score = $data['oneday_score']== 0 || $data['oneday_score']=='' ? 99999999 : $data['oneday_score'];
                if($this_day_score < $oneday_score){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }



    /**
    *  积分事件触发的rules
     * @$i 规则类型id
     * @$plan_id 方案id
     *
    */
     public function rule($i,$plan_id){
         $where['id']  = $i;
         $where['plan_id']  = $plan_id;
         $where['_logic'] = 'and';
         if(empty($where['plan_id'])){//如果方案id为空，则获取默认规则
             $data = M('integration_rule_default')->where(array('id' => $where['id']))->find();
         }else{
             $data = M('integration_rule')->where($where)->find();
         }
         return $data;
    }

    /**
     *  项目审核通过--课程、考试、调研 消息通知触发公共模型
     *  调用： D('Trigger')->projectMessageTrigger("项目id");
     */
    public function projectMessageTrigger($project_id){
        //指定人
        $designated_personnel = M('designated_personnel')->where(array('project_id'=>$project_id))->select();
        $project_user_id =  M('admin_project')->where(array('id'=>$project_id))->getField('user_id');
          //课程
          $project_course = M('project_course')->where(array('project_id'=>$project_id))->select();
          foreach ($project_course as $key => $value) {
              $seeUrl = "admin/my_course/detail/course_id/".$value["course_id"]."/project_id/true";
						  foreach ($designated_personnel as $key1 => $value1) {
               D('Trigger')->messageTrigger($value1["user_id"], "你有待学习课程即将开始，信息如下", date("Y-m-d H:i:s"), 10, $project_user_id, $seeUrl);
              }
           }
          //考试
          $test = M('project_examination')->where(array('project_id'=>$project_id))->select();
          foreach ($test as $key => $value) {
               $seeUrl = "admin/my_exam/allexam";
						  foreach ($designated_personnel as $key1 => $value1) {
               D('Trigger')->messageTrigger($value1["user_id"], "你有面授课程即将开始开讲，信息如下", date("Y-m-d H:i:s"), 11, $project_user_id, $seeUrl);
              }
           }

          //调研
          $survey = M('project_survey')->where(array('project_id'=>$project_id))->select();
          foreach ($survey as $key => $value) {
               $seeUrl = "admin/my_survey/joinsurvey/survey_id/".$value["survey_id"]."/project_id/".$value["project_id"]."/typeid/0";
						  foreach ($designated_personnel as $key1 => $value1) {
               D('Trigger')->messageTrigger($value1["user_id"], "你有待参与调研即将开始，信息如下", date("Y-m-d H:i:s"), 12, $project_user_id, $seeUrl);
              }
           }

         }
          


    /**
     *  消息通知触发公共模型
     *  调用： D('Trigger')->messageTrigger("消息接收人", "通知标题", "创建时间", "消息类型", "消息发起人", "消息查看地址");
     */
    public function messageTrigger($user_id,$title,$contents_time,$type_id,$from_id,$url){
         $data = array(
             'user_id'=>$user_id,
             'title'=>$title,
             'contents_time'=>$contents_time,
             'newstime'=>date('Y-m-d H:i:s'),
             'type_id'=>$type_id,
             'from_id'=>$from_id,
             'status'=>0,
             'is_delete'=>0,
             'url'=>$url
         );

    if(strtolower(C('DB_TYPE')) == 'oracle'){
			$data['id'] = getNextId('admin_message');
			$data['newstime'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
			$data['contents_time'] = array('exp',"to_date('".$contents_time."','yy-mm-dd hh24:mi:ss')");
		}else{
			$data['newstime'] = date('Y-m-d H:i:s');
			$data['contents_time'] = $contents_time;
		}
        $res = M('admin_message')->add($data);
        // ECHO M('admin_message')->getLastSql();die;
        if($type_id == 10){//只发课程学习邮件
        //user_id即为收信人，根据user_id查找email
        $address = M('users')->where(array('id'=>$user_id))->getField('email');

        if($res){
          
           if($address){
            
            $this->sys_send_email($user_id,$res,$type_id,$title,$contents_time,$url,$address);
           }
        }          
      }
       return $res;
      
    }




    /**
     *  消息通知详情"点击前往"跳转公共模型
     */
    public function messageEntryTrigger($i){
       
       $data = M('admin_message')->where(array('id'=>$i))->find();
       $this->redirect($data['url']);

    }


   /**
    *邮箱推送
    */
    public function sys_send_email($user_id,$message_id,$type_id,$title,$contents_time,$url,$address){
      
    	return "发送成功";
    	//--------------------测试环境暂时屏蔽--------------------
    	
    	
      //根据$type_id联表think_admin_message_type查找emailtitle
       $emailtitle = M('admin_message_type')
                    ->where(array('id'=>$type_id))
                    ->getField('cat_detail');

      //消息跳转url
       $os = $this->os();
       if($os == "Linux"){
         $url = U($url,$vars='',$suffix=true,$domain=true);
         $url = str_replace('/index.php','',$url);
       }else{
         $url = U($url,$vars='',$suffix=true,$domain=true);
       }
       

  
       $emailcontent = "名称：".$title
                     ."<br />"
                     ."时间：".$contents_time
                     ."<br /><br /><br />"
                     ."点击前往：".$url
                     ."<br /><br />"
                     ."此邮件由培训平台系统自动发出,请勿回复！";

        $res = send_email($address,$emailtitle,$emailcontent);
        if($res['error'] == 0){
          $retinfo = "发送成功";
        }else{
          $retinfo =  "发送失败";
          // dump($res);
        }
        return $retinfo; 
    }



    /**    
     *    工单号-是否审核公共模型
     *    @parm $user_id=false pc端调用此接口，否则为app端调用
     *    $i参数1-12分别代表：1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册 10.业务部落 12.活动报名
     *    return  $data :  $data['no']工单号， $data['status']审核类型 0不需审核 1需审核
     */
    public function orderNumber($i,$user_id=false){
      
        switch(intval($i))
        {
              case 1:
                $no =  '01'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
              case 2:
                $no =  '02'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
              case 3:
                $no =  '03'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
              case 4:
                $no =  '04'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
              case 5:
                $no =  '05'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
              case 6:
                $no =  '06'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
              case 7:
                $no =  '07'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
              case 8:
                $no =  '08'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
              case 9:
                $no =  '09'.substr(time(),6).rand(1000,9999);
                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
                break;
             
              default:
                $no =  $i.substr(time(),6).rand(1000,9999);

                $status = $this->isAuditStatus($i,$user_id);
                $data = array( 'no'=>$no,'status'=>$status );
                return $data;
        }
        
        
 
    }



    /** 
     * @parm $user_id=false pc端调用，否则为app端调用
     * 是否审核
     * return  $status  0不需审核 1需审核
     */
     public function isAuditStatus($i,$user_id=false)
     {   
         
         //登录者所在组织未设置方案，返回的$planId为空，则默认不需审核
          if($user_id !== false){ //app
         
            if($user_id == 0){//注册用户
            
              $tissue_id = session('tissue_id');
              $planId_tmp = M('sys_tissue')->where(array('tissue_id'=>$tissue_id))->find();
              $planId = $planId_tmp['plan_id'];
            }else{
              $planId = getPlanId($user_id);
            }
       
          }else{ //pc
        
            if(!empty($_SESSION['user']['id'])){
            $planId = getPlanId();
            }else{//注册用户
            $tissue_id = session('tissue_id');
            $planId_tmp = M('sys_tissue')->where(array('tissue_id'=>$tissue_id))->find();
            $planId = $planId_tmp['plan_id'];
           }
          }
          
          if($planId){
           //取审核设置表的数据 
            $is_condition = M('audit_rule')->where(array('type'=>$i,'plan_id'=>$planId))->getField('is_condition');
            if($is_condition == 2){
                $status = 0;
            }else{
                $status = 1;
            }
          }else{
            $status = 0;
          }
          
         return $status;
     }
     
    /** 
     *审核设置的数据
     */
     public function AuditSetData($type,$user_id)
     {  
        $planId = getUserPlanId($user_id); //获取方案
        $dataSet = array();
        
        $map = array(
          'a.type'=>$type,
          'a.plan_id'=>$planId
        );
        $dataSet = M('audit_rule')->alias('a')
                ->field('a.*,b.name,conditiona,conditionb')
                ->join('left join __AUDIT_CONDITION__ b on b.id = a.condition_id')
                ->where($map)
                ->find();
               
        return $dataSet;

     }
     
     /** 
      *项目重新（即将表数据的状态变为待审核）提交接口
      * @param $id 项目id
      * @param $type 类型(1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册) 
      * @return $res, 返回结果 bool
      *调用方法 实例：$res = D('Trigger')->projectResubmit($id,1);
      */
     public function projectResubmit($id,$type=1)
     {
       
       $exist = M('audit')->where(array('type'=>$type,'correlate_id'=>$id))->find();
       $planId = getPlanId();
       $orderno_data = $this->orderNumber($type);
       
       if((!$planId) || ($orderno_data['status']==0)){//没有设置方案时或者无需审核,删除审核表该条旧的数据
         
         $res = M('audit')->where(array('id'=>$exist['id']))->delete();
         return true;
       }
       

       $map = array(
         'id'=>$id
       );

       if($type == 1){
                  $result = M('admin_project')->where($map)->find();
                  $create_user_id = $result['user_id'];
                }else if($type == 2){
                  $result = M('course')->where($map)->find();
                  $create_user_id = $result['user_id'];
                }else if($type == 3){
                  $result = M('examination')->where($map)->find();
                  $create_user_id = $result['test_heir'];
                }else if($type == 4){
                  $result = M('survey')->where($map)->find();
                  $create_user_id = $result['survey_heir'];
                }else if($type == 5){
                  $result = M('friends_circle')->where($map)->find();
                  $create_user_id = $result['user_id'];
                }else if($type == 6){
                  $result = M('research')->where($map)->find();
                  $create_user_id = $result['create_user'];
                }else if($type == 7){
                  $result = M('test')->where($map)->find();
                  $create_user_id = $result['create_user'];
                }else if($type == 8){
                  $result = M('integration_apply')->where($map)->find();
                  $create_user_id = $result['user_id'];
                }else if($type == 9){
                  $result = M('users')->where($map)->find();
                  $create_user_id = $result['id'];
                }else if($type == 10){
                  $result = M('topic_group')->where($map)->find();
                  $create_user_id = $result['user_id'];
                }else if($type == 11){
                  $result = M('credits_apply')->where($map)->find();
                  $create_user_id = $result['user_id'];
                }else if($type == 12){
                  $result = M('activity')->where($map)->find();
                  $create_user_id = $result['user_id'];
                }
             

               

       // dump($dataSet); exit;
       if($exist){
           //读取当前审核配置表的配置
           $dataSet = $this->AuditSetData($type,$create_user_id);

           //负责人审核用到创建者所在组织
            $tissues = $this->getTissueid($create_user_id,$dataSet['one_level_type'],$dataSet['two_level_type'],$dataSet['three_level_type']);
            
            if($tissues['num'] != ''){                 
              $dataSeted['num'] = $tissues['num'];
            }else{
              $dataSeted['num'] = $dataSet['num'];
            }
           //变更该条数据值
            $auditData = array();
            $auditData = array(
                    'type'=> $type,
                    'correlate_id'=> $id,
                    'levalone_man'=> 0,
                    'levaltwo_man'=> 0,
                    'levalthree_man'=> 0,
                    'audit_status'=> 0,
                    'objection'=>'',
                    'oneaudit_role'=> $dataSet['oneaudit_role'],
                    'twoaudit_role'=> $dataSet['twoaudit_role'],
                    'threeaudit_role'=> $dataSet['threeaudit_role'],
                    'is_condition'=> $dataSet['is_condition'],
                    'condition_id'=> $dataSet['condition_id'],
                    'conditiona'=> $dataSet['conditiona'],
                    'conditionb'=> $dataSet['conditionb'],
                    'num'=> $dataSeted['num'],
                    'one_level_type'=> $dataSet['one_level_type'],
                    'two_level_type'=> $dataSet['two_level_type'],
                    'three_level_type'=> $dataSet['three_level_type'],
                    'oneaudit_user_id'=> $dataSet['oneaudit_user_id'],
                    'twoaudit_user_id'=> $dataSet['twoaudit_user_id'],
                    'threeaudit_user_id'=> $dataSet['threeaudit_user_id'],

                    'one_leader_tissueid'=> $tissues['tissue1'],
                    'two_leader_tissueid'=> $tissues['tissue2'],
                    'three_leader_tissueid'=>$tissues['tissue3'],     
              );
              $res =  M('audit')->where(array('type'=>$type,'correlate_id'=>$id))->save($auditData);
              
       }else{
            
              $auditData = array();
  
              $auditData = array(
                       'type'=> $type,
                       'correlate_id'=> $id,
                       'oneaudit_role'=> $dataSet['oneaudit_role'],
                       'twoaudit_role'=> $dataSet['twoaudit_role'],
                       'threeaudit_role'=> $dataSet['threeaudit_role'],
                       'is_condition'=> $dataSet['is_condition'],
                       'condition_id'=> $dataSet['condition_id'],
                       'conditiona'=> $dataSet['conditiona'],
                       'conditionb'=> $dataSet['conditionb'],
                       'num'=> $dataSeted['num'],
                       'one_level_type'=> $dataSet['one_level_type'],
                       'two_level_type'=> $dataSet['two_level_type'],
                       'three_level_type'=> $dataSet['three_level_type'],
                       'oneaudit_user_id'=> $dataSet['oneaudit_user_id'],
                       'twoaudit_user_id'=> $dataSet['twoaudit_user_id'],
                       'threeaudit_user_id'=> $dataSet['threeaudit_user_id'],

                       'one_leader_tissueid'=> $tissues['tissue1'],
                       'two_leader_tissueid'=> $tissues['tissue2'],
                       'three_leader_tissueid'=>$tissues['tissue3'],     
                 );

			  if(strtolower(C('DB_TYPE')) == 'oracle'){
					$auditData['id'] = getNextId('audit');
				}
              $res =  M('audit')->add($auditData);


       }
        return $res;
       
     }


     /** 
     * 返回创建者所在组织tissue1-上级tissue2-上上级组织id tissue3 ，对应审核轮数num
     *
     */
     public function getTissueid($create_user_id,$one_level_type,$two_level_type,$three_level_type){
             
             
             if($one_level_type == 3 && $two_level_type == 3 && $three_level_type == 3 ){
                  $temp1 = M('tissue_group_access')->where(array('user_id'=>$create_user_id))->getField('tissue_id');
                  $temp2 = M('tissue_rule')->where(array('id'=>$temp1))->getField('pid') ;
                  $temp2 = $temp2 ? $temp2 : 0;
                  $temp3 = M('tissue_rule')->where(array('id'=>$temp2))->getField('pid');
                  $temp3 = $temp3 ? $temp3 : 0;
                  if($temp3 == 0 && $temp2 == 0){
                    $num = 1;
                  }else if($temp3 == 0){
                    $num = 2;
                  }
             }else if(($one_level_type == 3 && $two_level_type == 3) || ($one_level_type == 3 && $three_level_type == 3)|| ($two_level_type == 3 && $three_level_type == 3) ){
                  $temp11 = M('tissue_group_access')->where(array('user_id'=>$create_user_id))->getField('tissue_id');
                  $temp22 = M('tissue_rule')->where(array('id'=>$temp11))->getField('pid') ;
                  $temp22 = $temp22 ? $temp22 : 0;
                  $temp33 = M('tissue_rule')->where(array('id'=>$temp22))->getField('pid');
                  $temp33 = $temp33 ? $temp33 : 0;
                  if($temp22 == 0){
                    $num = 1;
                  }
                  if(($one_level_type == 3 && $two_level_type == 3)){
                    $temp1 = $temp11;  
                    $temp2 = $temp22;
                  }else if($one_level_type == 3 && $three_level_type == 3){
                    $temp1 = $temp11;  
                    $temp3 = $temp22;
                  }else if($two_level_type == 3 && $three_level_type == 3){
                    $temp2 = $temp11;  
                    $temp3 = $temp22;
                  }

             }else if($one_level_type == 3){
                 $temp1 = M('tissue_group_access')->where(array('user_id'=>$create_user_id))->getField('tissue_id');

             }else if($two_level_type == 3){
                 $temp2 = M('tissue_group_access')->where(array('user_id'=>$create_user_id))->getField('tissue_id');

             }else if($three_level_type == 3){
                 $temp3 = M('tissue_group_access')->where(array('user_id'=>$create_user_id))->getField('tissue_id');

             }
             
            $data = array(
                'tissue1' => $temp1,
                'tissue2' => $temp2,
                'tissue3' => $temp3,
                'num' => $num,
            ) ; 

            return $data;

     }



   /***
    *扫码考勤接口
        MOBILE接口调用实例,参数：$project_id,$attendance_project_id,$user_id
        public function  test1(){
		 $data =  D('Trigger')->qrcodeAttendance(157,85,312);
        }
    */

   public function  qrcodeAttendance($project_id,$attendance_project_id,$user_id){
       $map = array(
         'pid'=>$project_id,
         'user_id'=>$user_id,
         'attendance_project_id'=>$attendance_project_id,
       );

      
       $where = array(
         'project_id'=>$project_id,
         'id'=>$attendance_project_id,
       );
      $res = M('attendance')->where($map)->find();
       
      // echo 11;exit;
      if(!$res){
        
         $data = array(
          "code" => 1023,
          "message" => "用户不在考勤范围!",
         );
         return $data;
      }
      
      if($res['mobile_scanning'] == 1 || $res['status'] != 3){
         $data = array(
          "code" => 1024,
          "message" => "你已考勤，请勿重复考勤!",
         );
         return $data;
      }
     
      if(strtolower(C('DB_TYPE')) == 'oracle'){
        $res = M('attendance_project')->field("to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")->where($where)->find();
        $date_field = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
      }else{
        $res = M('attendance_project')->where($where)->find();
        $date_field = date('Y-m-d H:i:s');
      }
      if(time() <= strtotime($res['start_time'])){
         $svaeStatus = array('status'=>1,'mobile_scanning'=>1,'attendance_time'=>$date_field);
         $res = M('attendance')->where($map)->save($svaeStatus);
         $data = array(
          "code" => 1000,
          "message" => "考勤-按时",
         );
         return $data;
      }else if(strtotime($res['end_time']) > time() && time() > strtotime($res['start_time'])){
         $svaeStatus = array('status'=>2,'mobile_scanning'=>1,'attendance_time'=>$date_field);
         $res = M('attendance')->where($map)->save($svaeStatus);
          $data = array(
          "code" => 1025,
          "message" => "考勤-迟到",
         );
         return $data;
      }else if(strtotime($res['end_time']) < time()){
        // dump($res);die;
         $svaeStatus = array('status'=>0,'mobile_scanning'=>1,'attendance_time'=>$date_field);
         $res = M('attendance')->where($map)->save($svaeStatus);
          $data = array(
          "code" => 1026,
          "message" => "考勤-缺勤",
         );
         
         return $data;
      }
      
         
   }


   /***
    *检测当前的操作系统  Windows/Linux
    */

   public function os(){
        $os_name=php_uname();
        if(strpos($os_name,"Linux")!==false){
            $os_str="Linux";
        }else if(strpos($os_name,"Windows")!==false){
            $os_str="Windows";
        }
        return  $os_str;
         
   }



   /***
    *部落系统消息触发  $type  1加入小组 2退出小组 3设置管理员 4创建话题
    *调用： $res = D('Trigger')->sendTopicMessage($user_id,$content,$topic_group_id=0,$topic_id=0,$audit_user_id=0,$type);
    */

   public function sendTopicMessage($user_id,$content,$topic_group_id=0,$topic_id=0,$audit_user_id=0,$type){
        $data = array(
          'user_id'=>$user_id,
          'topic_group_id'=>$topic_group_id,
          'topic_id'=>$topic_id,
          'audit_user_id'=>$audit_user_id,
          'time'=>date('Y-m-d H:i:s'),
          'type'=>$type
        );
        
        if(strtolower(C('DB_TYPE')) == 'oracle'){
          $data['id'] = getNextId('topic_message');
          $data['time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
          
        if($type == 1){
           $data['content'] = "加入部落，审核人：";
        }else if($type == 2){
           $data['content'] = "退出部落";
        }else if($type == 3){
           $data['content'] = "被创建者设置为管理员";
        }else if($type == 4){
           $groupName = M('topic_group')->where(array('id'=>$topic_group_id))->getField('name');
           $topicName = M('group_topic')->where(array('id'=>$topic_id))->getField('name');
            
           $data['content'] = '在 '.$groupName.'部落 中创建话题 "'.$topicName.'"';
        }else{
           $data['content'] = $content;
        }

        $res = M('topic_message')->add($data);
        return $res;
   }







}