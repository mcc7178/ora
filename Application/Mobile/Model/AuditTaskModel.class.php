<?php
namespace Mobile\Model;

use Think\Model;
/**
 * @author Lizhongjian 20170519
 * 我的任务
 */
class AuditTaskModel extends CommonModel {
    protected $tablePrefix = 'think_';
    protected $tableName = 'auth_group_access';




//-----------------------------------------------------------------------列表------------------------------------------------------------------------------//
 






	/**
	 * 审核任务一级列表
	 * 
	 */
	public function getAuditOneList($userId){

            $arr = array(
                '1'=>'培训项目',
                '5'=>'用户互动',
                '9'=>'用户注册',
                '11'=>'申请加分',
                '12'=>'活动报名'
            );
             
            $data = array();

            // 自动启动事务支持
            $this->startTrans();
            try {
            foreach($arr as $k=>$v){
               //判断用户是否存在,获取用户id,判断提交方式是否合法
               
               $temp = $this->threeallauditlist($k,0,$userId);
               
               $num = $temp['count'];
               $data[] = array('type'=>$k,'title'=>$v,'num'=>$num);
            }
                // 提交事务
                $this->commit();
                // print_r($data);
                return array('code'=>1000,"message"=>"获取成功",'data'=>$data);
            }catch (ThinkException $e) {
                $this->rollback();
                return array('code'=>1030,"message"=>"系统错误");
            }
            

    }

	/**
	 * 审核任务二级列表
	 *  $userId 登录者id  $type 审核类型
	 */
	public function getAuditTwoList($userId,$type){
            $data = array();
            $tabHead = I('get.tabHead') !== '' ? I('get.tabHead') : '1'; //标签tabHead  1:待我审核栏目列表 2:我发起的栏目列表


            // 自动启动事务支持
            $this->startTrans();
            try {
            $temp = $this->threeallauditlist($type,1,$userId);
            // 提交事务
            $this->commit();
            
            $lists = $temp['lists'];
            foreach($lists as $k=>$v){
                $data[$k]['id'] = $v['id'];
                $data[$k]['num'] = $v['num']; //审核轮数
                $data[$k]['type'] = $type;

                $data[$k]['name'] = (string)$v['name'];  
                $data[$k]['add_time'] = $v['add_time'];
                $data[$k]['audit_id'] = $v['audit_id'];
                $data[$k]['audit_status'] = $v['audit_status']; //审核状态
                $data[$k]['tabHead'] = $tabHead;
            }
            // print_r($data);exit;
            if($data){
               return array('code'=>1000,"message"=>"获取成功",'data'=>$data);
            }else{
               return array('code'=>1030,"message"=>"暂无数据返回");
            }
            
             
            }catch (ThinkException $e) {
                $this->rollback();
                return array('code'=>1030,"message"=>"系统错误");
            }
    }








     /** 
     *所有审核列表-模型
     *$type:  1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分9:用户注册,10:话题小组,11:申请加学分12:活动报名
     *$triggerType 0:不触发 1：触发
     */
     public function threeallauditlist($type,$triggerType,$userId)
     {  


        $size = 10;
        
        // $p = I('p') ? I('p') : 1 ;
        $p = I('get.page',0,'int');
        $tabHead = I('get.tabHead') !== '' ? I('get.tabHead') : '1'; //标签tabHead  1:待我审核栏目列表 2:我发起的栏目列表
	    $tabType = I('get.tabType') !== '' ? I('get.tabType') : '1'; //标签tabType  1:待办任务 2:已完成
        
        $keyword = I('get.table_search') !== '' ? I('get.table_search') : '';
        //判别audit表中是否存在相关type值条件
        $typeCondition = array("b.type"=>$type);
        // echo $keyword;
        if(is_numeric($keyword)){
            $condition  = array(
            	'a.orderno'=>array("like","%$keyword%"),           
          	);
        }else{
               switch ($type) {
                  case "1":
                    $condition  = array( 'a.project_name'=>array("like","%$keyword%"));
                    break;
                  case "2":
                    $condition  = array( 'a.course_name'=>array("like","%$keyword%"));
                    break;
                  case "3":
                    $condition  = array( 'a.test_name'=>array("like","%$keyword%"));
                    break;
                  case "4":
                    $condition  = array( 'a.survey_name'=>array("like","%$keyword%"));
                    break;
                  case "5":
                    $condition  = array( 'a.content'=>array("like","%$keyword%"));
                    break;
                  case "6":
                    $condition  = array( 'a.research_name'=>array("like","%$keyword%"));
                    break;
                  case "7":
                    $condition  = array( 'a.name'=>array("like","%$keyword%"));
                    break;
                  case "8":
                    $condition  = array( 'a.apply_title'=>array("like","%$keyword%"));
                    break;
                  case "9":
                    $condition  = array( 'a.username'=>array("like","%$keyword%"));
                    break;
                  case "10":
                    $condition  = array( 'a.name'=>array("like","%$keyword%"));
                    break;
                  case "11":
                    $condition  = array( 'a.apply_title'=>array("like","%$keyword%"));
                    break;
                  case "12":
                    $condition  = array( 'a.activity_name'=>array("like","%$keyword%"));
                    break;
                  default:
                    $condition  =  array('a.orderno'=>1);
                }
            

        }
  
       if($tabType == 1){
       
        if($triggerType == 1){
            //待审核列表的触发关联审核表think_audit插入数据的处理
            $this->clickTrigger($type);
        }
        




      //  echo  $gruop_id;die;
        //根据登录者获取登录者的所属角色，可多重角色
      
      $gruop_id_arr = M('auth_group_access')
                 ->where(array('user_id'=> $userId))
                 ->field('group_id')
                 ->select();

      $temp = array();
      foreach($gruop_id_arr as $k=>$v){
           $temp[] = $v['group_id'];
      }
      $gruop_id = implode(',',$temp);
      


       $Login_id = $userId;

       $tissue_id = $this->getPrincipalTissueId($userId);
      //  echo $tissue_id;
       if($tabHead == 1){
       $scopeMap = "     ( b.one_level_type = 2 AND b.audit_status = 0 AND b.oneaudit_role in ($gruop_id) ) 
                      OR ( b.two_level_type = 2 AND b.audit_status = 1 AND b.twoaudit_role in ($gruop_id) ) 
                      OR ( b.three_level_type = 2 AND b.audit_status = 2 AND b.threeaudit_role in ($gruop_id))

                      OR ( b.one_level_type = 1 AND b.audit_status = 0 AND b.oneaudit_user_id = $Login_id ) 
                      OR ( b.two_level_type = 1 AND b.audit_status = 1 AND b.twoaudit_user_id = $Login_id ) 
                      OR ( b.three_level_type = 1 AND b.audit_status = 2 AND b.threeaudit_user_id = $Login_id )
                         
                      OR ( b.one_level_type = 3 AND b.audit_status = 0 AND b.one_leader_tissueid = $tissue_id ) 
                      OR ( b.two_level_type = 3 AND b.audit_status = 1 AND b.two_leader_tissueid = $tissue_id) 
                      OR ( b.three_level_type = 3 AND b.audit_status = 2 AND b.three_leader_tissueid = $tissue_id)
                      
                      " ;
       }else if($tabHead == 2){
           //我发起的 列表
         $scopeMap = " a.user_id = $Login_id " ;
       }

       if($type == 1){
           
           if(strtolower(C('DB_TYPE')) == 'oracle'){
                //  echo 11;
               $lists = M('admin_project')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.type'=>2))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,to_char(a.add_time,\'YYYY-MM-DD HH24:MI:SS\') as add_time,a.user_id,a.project_name as name,to_char(a.start_time,\'YYYY-MM-DD HH24:MI:SS\') as start_time,to_char(a.end_time,\'YYYY-MM-DD HH24:MI:SS\') as end_time,a.project_budget,a.population,a.orderno,b.id as audit_id,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.num')
                //    ->field('a.id,b.id as audit_id,a.project_name as name,to_char(a.add_time,\'YYYY-MM-DD HH24:MI:SS\') as add_time,b.audit_status')
                   ->select();
            //      echo 11;
            //    //   echo haha;
            //    echo M('admin_project')->getLastSql();
               // echo '<hr />';
               $count = M('admin_project')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.type'=>2))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();
                   
           }else{

               $lists = M('admin_project')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.type'=>2))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               //   echo haha;
               // echo M('admin_project')->getLastSql();
               // echo '<hr />';
               $count = M('admin_project')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.type'=>2))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();


           }
        



       }else if($type == 2){


           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('course')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.user_id,a.course_time,a.course_name,a.orderno,a.credit,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   
                   ->select();
               // echo  M('course')->_sql();
               $count = M('course')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }else{

               $lists = M('course')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               // echo  M('course')->_sql();
               $count = M('course')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }

       }else if($type == 3){

           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('examination')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.test_heir,a.test_name,a.orderno,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('examination')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }else{

               $lists = M('examination')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('examination')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }

       }else if($type == 4){


           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('survey')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.survey_name,a.orderno,a.survey_heir,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('survey')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }else{

               $lists = M('survey')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('survey')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }
        




       }else if($type == 5){

           
           if(strtolower(C('DB_TYPE')) == 'oracle'){
              
               $lists = M('friends_circle')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0,'a.pid'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,to_char(a.content) as name,to_char(a.publish_time,\'YYYY-MM-DD HH24:MI:SS\') as add_time,b.audit_status,a.orderno,a.user_id,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
                   
                   
               //  echo  M('friends_circle')->_sql();die;
               foreach($lists as $k=>$v){
            //        $name = strip_tags(htmlspecialchars_decode($v['name']));
            // echo   $name = str_replace('&nbsp;','  ',strip_tags(htmlspecialchars_decode($v['name'])));die;
                   $content_result =  re_substr(str_replace('&nbsp;','  ',strip_tags(htmlspecialchars_decode($v['name']))), 0, 15, true, "utf-8"); //截取字符串15位
                   
                   if($content_result == ''){
                       $lists[$k]['name'] = '--';
                   }else{
                       $lists[$k]['name'] = $content_result;
                   }

               }
               $count = M('friends_circle')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0,'a.pid'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();
                   


           }else{


               $lists = M('friends_circle')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0,'a.pid'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
                   
               //  echo  M('friends_circle')->_sql();die;
               foreach($lists as $k=>$v){
                   $content_result =  re_substr(strip_tags($v['content']), 0, 15, true, "utf-8"); //截取字符串15位
                   if($content_result == ''){
                       $lists[$k]['content_result'] = '--';
                   }else{
                       $lists[$k]['content_result'] = $content_result;
                   }

               }
               $count = M('friends_circle')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0,'a.pid'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();


           }


       }else if($type == 6){


           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('research')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_state'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.orderno,a.research_name,a.create_user,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('research')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_state'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }else{

               $lists = M('research')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_state'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('research')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_state'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }

       

       }else if($type == 7){

           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('test')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_status'=>1))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.orderno,a.name,a.create_user,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('test')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }else{

               $lists = M('test')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_status'=>1))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('test')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();


           }
        

       }else if($type == 8){

           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('integration_apply')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.add_score,a.apply_title,a.orderno,a.user_id,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('integration_apply')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();


           }else{

               $lists = M('integration_apply')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('integration_apply')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }

       }else if($type == 9){
           
            if($tabHead == 2){
                //我发起的 列表
                $scopeMap = " a.id = $Login_id " ;
            }

           if(strtolower(C('DB_TYPE')) == 'oracle'){
                
               $lists = M('users')->alias('a') 
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->field("a.id,a.phone,a.username as name,to_char(a.register_time,'YYYY-MM-DD HH24:MI:SS') as add_time,a.orderno,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                   ->where(array('a.status'=>2))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->select();
                

               $count = M('users')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>2))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();


           }else{

               $lists = M('users')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>2))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               // echo  M('users')->getLastsql();
               // dump($lists);
               $count = M('users')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>2))
                   ->where($condition)
                   ->where($typeCondition)
                   ->where($scopeMap)
                   ->count();

           }


       }else if($type == 10){
            
             if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.status'=>0))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field("a.id,a.user_id,a.name,a.status,a.orderno,a.type,a.group_covers,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                  ->select(); 
     
           }else{

              $lists = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.status'=>0))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.num')
                  ->select(); 

           } 
        // echo  M('users')->getLastsql();    
        // dump($lists);
               
        $count = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>0))
                  ->where($condition)
                  ->where($typeCondition)
                  ->where($scopeMap)
                  ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->count();
     

       }else if($type == 11){
        
        if(strtolower(C('DB_TYPE')) == 'oracle'){
        // echo 11;
        $lists = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.status'=>0))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field("a.id,a.apply_title as name,a.add_time,a.user_id,a.apply_description,a.attachment,a.add_score,a.status,a.apply_title,a.orderno,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                  ->select();  


        $count = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>0))
                  ->where($condition)
                  ->where($typeCondition)
                  ->where($scopeMap)
                  ->count();

        }else{

        $lists = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.status'=>0))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->select();                
        $count = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>0))
                  ->where($condition)
                  ->where($typeCondition)
                  ->where($scopeMap)
                  ->count();

         }
       }else if($type == 12){
        
        if(strtolower(C('DB_TYPE')) == 'oracle'){
        // echo 11;
        $lists = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.type'=>2))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field("a.id,a.user_id,a.activity_name as name,to_char(a.add_time,'YYYY-MM-DD HH24:MI:SS') as add_time,a.type,a.orderno,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                  ->select();  

        $count = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.type'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->where($scopeMap)
                  ->count();
        }else{

        $lists = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.type'=>2))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->select();                
        $count = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.type'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->where($scopeMap)
                  ->count();

         }
       }
      
       $show = tabPage($count,$size,'p',1); 
       //  echo M('admin_project')->_sql();
       $lists = $this->listTransform($lists,$type);
     

    		 
      }else if($tabType == 2){   //待我审核-已完成列表
      
       $Login_id = $userId;   

       if($tabHead == 1){
       //此处“已完成列表”取出终审者是登录者的数据lists
        $scopeMap = "     (b.audit_status = 1 AND b.levalone_man = $Login_id ) 
                      OR (b.audit_status = 2 AND b.levaltwo_man = $Login_id ) 
                      OR (b.audit_status = 3 AND b.levalthree_man = $Login_id )
                      OR (b.audit_status = 4 AND b.levalone_man = $Login_id ) 
                      OR (b.audit_status = 5 AND b.levaltwo_man = $Login_id ) 
                      OR (b.audit_status = 6 AND b.levalthree_man = $Login_id )
                      " ;
       }else if($tabHead == 2){
         $scopeMap = "a.user_id = $Login_id";
       }
                   
       if($type == 1){
           
           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('admin_project')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.type'=>array('in','0,3,4')))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.user_id,a.project_name as name,to_char(a.add_time,\'YYYY-MM-DD HH24:MI:SS\') as add_time,to_char(a.start_time,\'YYYY-MM-DD HH24:MI:SS\') as start_time,to_char(a.end_time,\'YYYY-MM-DD HH24:MI:SS\') as end_time,a.project_budget,a.population,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man,b.num')
                   ->select();
                //    echo 11;exit;
               $count = M('admin_project')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where($scopeMap)
                   ->where(array('a.type'=>array('in','0,3,4')))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }else{

               $lists = M('admin_project')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.type'=>array('in','0,3,4')))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('admin_project')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.type'=>array('in','0,3,4')))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }




       }else if($type == 2){


           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('course')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.user_id,a.credit,a.course_time,a.course_name,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                   ->select();
               $count = M('course')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }else{

               $lists = M('course')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('course')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }




       }else if($type == 3){

           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('examination')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.test_heir,a.test_name,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                   ->select();
               $count = M('examination')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }else{

               $lists = M('examination')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('examination')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();


           }

       }else if($type == 4){


           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('survey')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.survey_name,a.survey_heir,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                   ->select();
               $count = M('survey')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }else{

               $lists = M('survey')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('survey')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();


           }

       }else if($type == 5){

           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('friends_circle')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>array('in','1,2'),'a.pid'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.orderno,to_char(a.content) as name,to_char(a.publish_time,\'YYYY-MM-DD HH24:MI:SS\') as add_time,a.user_id,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man,b.num')
                   ->select();
               foreach($lists as $k=>$v){
                   $content_result =  re_substr(str_replace('&nbsp;','  ',strip_tags(htmlspecialchars_decode($v['name']))), 0, 15, true, "utf-8"); //截取字符串15位
                   if($content_result == ''){
                       $lists[$k]['name'] = '--';
                   }else{
                       $lists[$k]['name'] = $content_result;
                   }

               }
               $count = M('friends_circle')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>array('in','1,2'),'a.pid'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }else{

               $lists = M('friends_circle')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>array('in','1,2'),'a.pid'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               foreach($lists as $k=>$v){
                   $content_result =  re_substr(strip_tags($v['content']), 0, 15, true, "utf-8"); //截取字符串15位
                   if($content_result == ''){
                       $lists[$k]['content_result'] = '--';
                   }else{
                       $lists[$k]['content_result'] = $content_result;
                   }

               }
               $count = M('friends_circle')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>array('in','1,2'),'a.pid'=>0))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }





       }else if($type == 6){

           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('research')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_state'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.orderno,a.research_name,a.create_user,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                   ->select();
               $count = M('research')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_state'=>1))
                   ->where($condition)
                   ->count();

           }else{

               $lists = M('research')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_state'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('research')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_state'=>1))
                   ->where($condition)
                   ->count();

           }


       }else if($type == 7){

           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('test')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.create_user,a.orderno,a.name,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                   ->select();
               $count = M('test')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }else{

               $lists = M('test')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('test')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.audit_status'=>0))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }

       }else if($type == 8){

           if(strtolower(C('DB_TYPE')) == 'oracle'){

               $lists = M('integration_apply')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.id,a.add_score,a.apply_title,a.orderno,a.user_id,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                   ->select();
               $count = M('integration_apply')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }else{

               $lists = M('integration_apply')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('integration_apply')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>1))
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }

       }else if($type == 9){
            if($tabHead == 2){
                $scopeMap = "a.id  = $Login_id";
            }
           
           if(strtolower(C('DB_TYPE')) == 'oracle'){
              
               $lists = M('users')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>array('in','0,1')))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field("a.id,a.phone,a.username as name,to_char(a.register_time,'YYYY-MM-DD HH24:MI:SS') as add_time,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man,b.num")
                   ->select();
               $count = M('users')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>array('in','0,1')))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();


           }else{

               $lists = M('users')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>array('in','0,1')))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->page($p.','.$size)
                   ->order('a.id desc')
                   ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                   ->select();
               $count = M('users')->alias('a')
                   ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                   ->where(array('a.status'=>array('in','0,1')))
                   ->where($scopeMap)
                   ->where($condition)
                   ->where($typeCondition)
                   ->count();

           }


       }else if($type == 10){
        

        if(strtolower(C('DB_TYPE')) == 'oracle'){
        $lists = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id')
                  ->where(array('a.status'=>1))
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field("a.id,a.user_id,a.name,a.status,a.orderno,a.type,a.group_covers,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role")
                  ->select(); 
        // echo  M('topic_group')->getLastsql();    
        // dump($lists);           
        $count = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id')
                  ->where(array('a.status'=>1))
                  ->where($condition)
                  ->where($typeCondition)
                  ->order('a.id desc')
                  ->count(); 
      
        }else{
        $lists = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id')
                  ->where(array('a.status'=>1))
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->select(); 
        // echo  M('topic_group')->getLastsql();    
        // dump($lists);           
        $count = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>1))
                  ->where($condition)
                  ->where($typeCondition)
                  ->where($scopeMap)
                  ->count();

        }





       }else if($type == 11){
         
          if(strtolower(C('DB_TYPE')) == 'oracle'){

         $lists = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.status'=>array('in','1,2')))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                   ->field("a.id,a.apply_title as name,a.add_time,a.user_id,a.apply_description,a.attachment,a.add_score,a.status,a.orderno,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.num")
                  ->select();                
        $count = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>array('in','1,2')))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();
 
          }else{
 
         $lists = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.status'=>array('in','1,2')))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->select();                
        $count = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>array('in','1,2')))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();
        }
       }else if($type == 12){
        
        if(strtolower(C('DB_TYPE')) == 'oracle'){
       
        $lists = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.type'=>array('in','0,3,4')))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field("a.id,a.activity_name as name,a.user_id,to_char(a.add_time,'YYYY-MM-DD HH24:MI:SS') as add_time,a.type,a.orderno,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                  ->select();  
          

        $count = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.type'=>array('in','0,3,4')))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();

        }else{

        $lists = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.type'=>array('in','0,3,4')))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->select();                
        $count = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.type'=>array('in','0,3,4')))
                  ->where($scopeMap)
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();

         }
       }

      //  dump($lists);
       $lists = $this->listTransform($lists,$type); //列表显示转换

      //  dump($lists);
      //  dump($lists);
       $show = tabPage($count,$size,'p',2); 


           
      }else if($tabType == 3){
        if($userId == 1){
           
        if($type == 1){

            if(strtolower(C('DB_TYPE')) == 'oracle'){

                $lists = M('admin_project')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.type'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p3.','.$size)
                    ->field('a.id,a.user_id,a.project_name,to_char(a.start_time,\'YYYY-MM-DD HH24:MI:SS\') as start_time,to_char(a.end_time,\'YYYY-MM-DD HH24:MI:SS\') as end_time,a.project_budget,a.population,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                    ->select();
                // dump($lists);
                $count = M('admin_project')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.type'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }else{

                $lists = M('admin_project')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.type'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p3.','.$size)
                    ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();
                // dump($lists);
                $count = M('admin_project')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.type'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();


            }




        }else if($type == 2){

            if(strtolower(C('DB_TYPE')) == 'oracle'){

                $lists = M('course')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>2))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.id,a.user_id,a.course_time,a.course_name,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                    ->select();
                $count = M('course')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>2))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }else{

                $lists = M('course')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>2))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();
                $count = M('course')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>2))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }



       }else if($type == 3){


            if(strtolower(C('DB_TYPE')) == 'oracle'){

                $lists = M('examination')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.id,a.test_heir,a.test_name,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                    ->select();
                $count = M('examination')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }else{

                $lists = M('examination')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();
                $count = M('examination')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }





       }else if($type == 4){

            if(strtolower(C('DB_TYPE')) == 'oracle'){

                $lists = M('survey')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.id,a.survey_name,a.orderno,a.survey_heir,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                    ->select();
                $count = M('survey')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }else{

                $lists = M('survey')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();
                $count = M('survey')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>3))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }
     

       }else if($type == 5){

            if(strtolower(C('DB_TYPE')) == 'oracle'){

                $lists = M('friends_circle')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>2,'a.pid'=>0))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.id,a.orderno,a.content,a.user_id,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                    ->select();
                foreach($lists as $k=>$v){
                    $content_result =  re_substr(strip_tags($v['content']), 0, 15, true, "utf-8"); //截取字符串15位
                    if($content_result == ''){
                        $lists[$k]['content_result'] = '--';
                    }else{
                        $lists[$k]['content_result'] = $content_result;
                    }

                }
                $count = M('friends_circle')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>2,'a.pid'=>0))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }else{

                $lists = M('friends_circle')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>2,'a.pid'=>0))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();
                foreach($lists as $k=>$v){
                    $content_result =  re_substr(strip_tags($v['content']), 0, 15, true, "utf-8"); //截取字符串15位
                    if($content_result == ''){
                        $lists[$k]['content_result'] = '--';
                    }else{
                        $lists[$k]['content_result'] = $content_result;
                    }

                }
                $count = M('friends_circle')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.status'=>2,'a.pid'=>0))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }

       }else if($type == 6){

            if(strtolower(C('DB_TYPE')) == 'oracle'){

                $lists = M('research')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.audit_state'=>2))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.id,a.create_user,a.orderno,a.research_name,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                    ->select();
                $count = M('research')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.audit_state'=>2))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }else{

                $lists = M('research')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.audit_state'=>2))
                    ->where($condition)
                    ->where($typeCondition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();
                $count = M('research')->alias('a')
                    ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                    ->where(array('a.audit_state'=>2))
                    ->where($condition)
                    ->where($typeCondition)
                    ->count();

            }





       }else if($type == 7){

        if(strtolower(C('DB_TYPE')) == 'oracle'){

            $lists = M('test')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.audit_status'=>2))
                ->where($condition)
                ->where($typeCondition)
                ->page($p.','.$size)
                ->order('a.id desc')
                ->field('a.id,a.create_user,a.orderno,a.name,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                ->select();
            $count = M('test')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.audit_status'=>2))
                ->where($condition)
                ->where($typeCondition)
                ->count();

        }else{

            $lists = M('test')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.audit_status'=>2))
                ->where($condition)
                ->where($typeCondition)
                ->page($p.','.$size)
                ->order('a.id desc')
                ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();
            $count = M('test')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.audit_status'=>2))
                ->where($condition)
                ->where($typeCondition)
                ->count();

        }

       }else if($type == 8){

        if(strtolower(C('DB_TYPE')) == 'oracle'){

            $lists = M('integration_apply')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.status'=>2))
                ->where($condition)
                ->where($typeCondition)
                ->page($p.','.$size)
                ->order('a.id desc')
                ->field('a.id,a.user_id,a.add_score,a.apply_title,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                ->select();
            $count = M('integration_apply')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.status'=>2))
                ->where($condition)
                ->where($typeCondition)
                ->count();

        }else{

            $lists = M('integration_apply')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.status'=>2))
                ->where($condition)
                ->where($typeCondition)
                ->page($p.','.$size)
                ->order('a.id desc')
                ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();
            $count = M('integration_apply')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.status'=>2))
                ->where($condition)
                ->where($typeCondition)
                ->count();

        }

       }else if($type == 9){


        if(strtolower(C('DB_TYPE')) == 'oracle'){

            $lists = M('users')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.status'=>0))
                ->where($condition)
                ->where($typeCondition)
                ->page($p.','.$size)
                ->order('a.id desc')
                ->field('a.id,a.phone,a.username,a.orderno,b.id as audit_id,b.audit_status,b.objection,b.levalone_man,b.levaltwo_man,levalthree_man')
                ->select();
            $count = M('users')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.status'=>0))
                ->where($condition)
                ->where($typeCondition)
                ->count();

        }else{

            $lists = M('users')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.status'=>0))
                ->where($condition)
                ->where($typeCondition)
                ->page($p.','.$size)
                ->order('a.id desc')
                ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();
            $count = M('users')->alias('a')
                ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                ->where(array('a.status'=>0))
                ->where($condition)
                ->where($typeCondition)
                ->count();

        }


       }else if($type == 10){
        if(strtolower(C('DB_TYPE')) == 'oracle'){
        $lists = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id')
                  ->where(array('a.status'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field("a.id,a.user_id,a.name,a.status,a.orderno,a.type,a.group_covers,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role")
                  ->select(); 
        // echo  M('users')->getLastsql();    
        // dump($lists);           
        $count = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();
        }else{
        $lists = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id')
                  ->where(array('a.status'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->select(); 
        // echo  M('users')->getLastsql();    
        // dump($lists);           
        $count = M('topic_group')->alias('a')
                  ->join('left join __AUDIT__  b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();

        }         
       }else if($type == 11){
         
        if(strtolower(C('DB_TYPE')) == 'oracle'){
         
         $lists = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.status'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field("a.id,a.apply_title as name,a.add_time,a.user_id,a.apply_description,a.attachment,a.add_score,a.status,a.orderno,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role")
                  ->select();   
                       
        $count = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();
       }else{

        $lists = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.status'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->select();                
        $count = M('credits_apply')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.status'=>2))
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();
         }
       }else if($type == 12){
        
        if(strtolower(C('DB_TYPE')) == 'oracle'){
        $lists = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.type'=>3))
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field("a.id,a.user_id,a.activity_name,to_char(a.add_time,'YYYY-MM-DD HH24:MI:SS') as add_time,a.type,a.orderno,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                  ->select();  

        $count = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.type'=>3))
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();

        }else{
        
        $lists = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id')
                  ->where(array('a.type'=>3))
                  ->where($condition)
                  ->where($typeCondition)
                  ->page($p.','.$size)
                  ->order('a.id desc')
                  ->field('a.*,b.oneaudit_user_id,b.twoaudit_user_id,b.threeaudit_user_id,b.one_level_type,b.two_level_type,b.three_level_type,b.id as audit_id,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                  ->select();                
        $count = M('activity')->alias('a')
                  ->join('left join __AUDIT__ b on b.correlate_id = a.id') 
                  ->where(array('a.type'=>3))
                  ->where($condition)
                  ->where($typeCondition)
                  ->count();

         }
       }


       $show = tabPage($count,$size,'p',3);  
       $lists = $this->listTransform($lists,$type); //列表显示转换
      //  dump($lists);

         }
      }

      $data = array(
				 'keyword'=>$keyword,
			    'lists'=>$lists,
                'count'=>$count,
			    'page'=>$show
		       );
		  return $data;
     }


     /** 
     * 审核列表的显示转换处理 --上级审批人、等待审批人、下级审批人 
     *$type:  1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册，10：业务部落，11：申请加学分
     */
     public function listTransform($lists,$type){
      
        foreach($lists as $k=>$v){
              $uname = $this->getUserName($v['user_id']);
              $levalone_man_name = $this->getUserName($v['levalone_man']);
              $levaltwo_man_name = $this->getUserName($v['levaltwo_man']);
              $levalthree_man_name = $this->getUserName($v['levalthree_man']);
              $oneaudit_role_name = $this->getGroupName($v['oneaudit_role']);
              $twoaudit_role_name = $this->getGroupName($v['twoaudit_role']);
              $threeaudit_role_name = $this->getGroupName($v['threeaudit_role']);
              
              if($type == 1){
                $population = $this->getpopulation($v['id']);
                $project_time = $this->diffBetweenTwoDays($v['start_time'],$v['end_time']);
                $lists[$k]['project_time'] = round($project_time,2);
                $lists[$k]['population'] = $population;
              }else if($type == 7 || $type == 6){
                $uname = $this->getUserName($v['create_user']); 
              }else if($type == 4){
                $uname = $this->getUserName($v['survey_heir']); 
              }else if($type == 2 || $type == 11){
                $uname = $this->getUserName($v['user_id']); 
              }
              
              $lists[$k]['uname'] = $uname;
              $lists[$k]['levalone_man_name'] = $levalone_man_name;
              $lists[$k]['levaltwo_man_name'] = $levaltwo_man_name;
              $lists[$k]['levalthree_man_name'] = $levalthree_man_name;

              if($v['one_level_type'] == 1){
               $oneaudit_role_name = $this->getUserName($v['oneaudit_user_id']);
              }else if($v['one_level_type'] == 2){
               $lists[$k]['oneaudit_role_name'] = $oneaudit_role_name;
              }else if($v['one_level_type'] == 3){
               $oneaudit_role_name = '负责人';
              }

              if($v['two_level_type'] == 1){
                // echo aa;
               $twoaudit_role_name = $this->getUserName($v['twoaudit_user_id']);
              }else if($v['two_level_type'] == 2){
               $lists[$k]['twoaudit_role_name'] = $twoaudit_role_name;
              }else if($v['two_level_type'] == 3){
               $twoaudit_role_name = '负责人';
              } 

              if($v['three_level_type'] == 1){
               $threeaudit_role_name = $this->getUserName($v['threeaudit_user_id']);
              }else if($v['three_level_type'] == 2){
               $lists[$k]['threeaudit_role_name'] = $threeaudit_role_name;
              }else if($v['three_level_type'] == 3){
               $threeaudit_role_name = '负责人';
              }  


              // $lists[$k]['oneaudit_role_name'] = $oneaudit_role_name;
              // $lists[$k]['twoaudit_role_name'] = $twoaudit_role_name;
              // $lists[$k]['threeaudit_role_name'] = $threeaudit_role_name;

              
             //待审核列表的审批人  
              if($v['audit_status'] == 0 ){
                $lists[$k]['preauditman'] = '--';
                if($v['num'] == 3 ){
                  $lists[$k]['currentauditman'] = $oneaudit_role_name;
                  $lists[$k]['laterauditman'] = $twoaudit_role_name.'>'.$threeaudit_role_name;
                }else if($v['num'] == 2){
                  $lists[$k]['currentauditman'] = $oneaudit_role_name;
                  $lists[$k]['laterauditman'] = $twoaudit_role_name;
                }else if($v['num'] == 1){
                  $lists[$k]['currentauditman'] = $oneaudit_role_name;
                  $lists[$k]['laterauditman'] = '--';
                }
              }else if($v['audit_status'] == 1){
                  $lists[$k]['preauditman'] = $levalone_man_name;
                  if($v['num'] == 3 ){
                  $lists[$k]['currentauditman'] = $twoaudit_role_name;
                  $lists[$k]['laterauditman'] = $threeaudit_role_name;
                }else if($v['num'] == 2){
                  $lists[$k]['currentauditman'] = $twoaudit_role_name;
                  $lists[$k]['laterauditman'] = '--';
                }
              }else if($v['audit_status'] == 2){
                 $lists[$k]['preauditman'] =  $levalone_man_name.'>'.$levaltwo_man_name;
                 $lists[$k]['currentauditman'] = $threeaudit_role_name;
                 $lists[$k]['laterauditman'] = '--';
              }
             
             //已通过或已拒绝审核列表的审批人 auditor
              if($v['audit_status'] == 1){
                  $lists[$k]['auditor'] = $levalone_man_name;
                  
              }else if($v['audit_status'] == 2){
                 $lists[$k]['auditor'] = $levalone_man_name.'>'.$levaltwo_man_name;
              }else if($v['audit_status'] == 3){
                $lists[$k]['auditor'] = $levalone_man_name.'>'.$levaltwo_man_name.'>'.$levalthree_man_name;
              }else if($v['audit_status'] == 4){
                  $lists[$k]['auditor'] = $levalone_man_name;
                  
              }else if($v['audit_status'] == 5){
                 $lists[$k]['auditor'] = $levalone_man_name.'>'.$levaltwo_man_name;
              }else if($v['audit_status'] == 6){
                $lists[$k]['auditor'] = $levalone_man_name.'>'.$levaltwo_man_name.'>'.$levalthree_man_name;
              }

         }
        //  dump($list);
         return $lists;

     }



     /** 
     *点击待审核时触发生成关联表数据，并返回登录者的角色id
     *$type:  1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册，10：业务部落，11：申请加学分
     */
     public function clickTrigger($type){
       if($type == 1){ 

        $map = array(
              'type'=>2,
              
              );
        $lists = M('admin_project')
                ->where($map)
                ->field('*')
                ->select();
        
      // echo M('admin_project')->_sql();
       }else if($type == 2){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('course')
                ->where($map)
                ->field('*')
                ->select();
        // echo M('course')->_sql(); die;
       }else if($type == 3){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('examination')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 4){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('survey')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 5){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('friends_circle')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 6){
        $map = array(
              'audit_state'=>0,
              
              );
        $lists = M('research')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 7){
        $map = array(
              'audit_status'=>1,
              
              );
        $lists = M('test')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 8){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('integration_apply')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 9){
        $map = array(
              'status'=>2,
              
              );
        $lists = M('users')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 10){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('topic_group')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 11){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('credits_apply')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 12){
        $map = array(
              'type'=>2,
              
              );
        $lists = M('activity')
                ->where($map)
                ->field('*')
                ->select();
       }
      
      //获取当前审核配置表的数据
      // $type = 1;
      
      // dump($dataSet); exit;
      foreach($lists as $k=>$v){
            // if($v['type'] === null || $v['type'] !== 2 ){
              //判断为待审，把当前审核配置表配置 往审核表里生成关联数据，
              // 1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起积分加分,9:用户注册,10：业务部落 11:发起学分加分 12.活动报名
                if($type == 1){
                  $create_user_id = $v['user_id'];
                }else if($type == 2){
                  $create_user_id = $v['user_id'];
                }else if($type == 3){
                  $create_user_id = $v['auth_user_id'];
                }else if($type == 4){
                  $create_user_id = $v['survey_heir'];
                }else if($type == 5){
                  $create_user_id = $v['user_id'];
                }else if($type == 6){
                  $create_user_id = $v['create_user'];
                }else if($type == 7){
                  $create_user_id = $v['create_user'];
                }else if($type == 8){
                  $create_user_id = $v['user_id'];
                }else if($type == 9){
                  $create_user_id = $v['id'];
                }else if($type == 10){
                  $create_user_id = $v['user_id'];
                }else if($type == 11){
                  $create_user_id = $v['user_id'];
                }else if($type == 12){
                  $create_user_id = $v['user_id'];
                }
               $res = $this->auditDataExist($v['id'],$type);
              //  echo $v['id'];
           
               if(!$res){
                //取审核规则数据
                $dataSet = $this->AuditSetData($type,$create_user_id);
                //负责人审核用到创建者所在组织
                $tissues = $this->getTissueid($create_user_id,$dataSet['one_level_type'],$dataSet['two_level_type'],$dataSet['three_level_type']);
               
                if($tissues['num'] != ''){                 
                  $dataSeted['num'] = $tissues['num'];
                }else{
                  $dataSeted['num'] = $dataSet['num'];
                }

                //  echo dd;die;
                $auditData = array();
                $auditData = array(
                       'type'=> $type,
                       'correlate_id'=> $v['id'],
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
                 //如果不需审核则改对应数据表的审核状态,否则往审核表插入审核数据
                 if($dataSet['is_condition'] == 2 ||  $dataSet['is_condition'] == null){
                     if($type == 1){ 

                        $res = M('admin_project')->where(array('id'=>$v['id']))->save(array('type'=>0));
                        
                      // echo M('admin_project')->_sql();
                      }else if($type == 2){
                        $res = M('course')->where(array('id'=>$v['id']))->save(array('status'=>1));
                       
                      }else if($type == 3){
                        $res = M('examination')->where(array('id'=>$v['id']))->save(array('status'=>1));
                       
                      }else if($type == 4){
                        $res = M('survey')->where(array('id'=>$v['id']))->save(array('status'=>1));
                        
                      }else if($type == 5){
                        $res = M('friends_circle')->where(array('id'=>$v['id']))->save(array('status'=>1));
                        
                      }else if($type == 6){
                        $res = M('research')->where(array('id'=>$v['id']))->save(array('audit_state'=>1));
                        
                      }else if($type == 7){
                        $res = M('test')->where(array('id'=>$v['id']))->save(array('audit_status'=>0));
                        
                      }else if($type == 8){
                        $res = M('integration_apply')->where(array('id'=>$v['id']))->save(array('status'=>1));
                        
                      }else if($type == 9){
                        $res = M('users')->where(array('id'=>$v['id']))->save(array('status'=>1));
                        
                      }else if($type == 10){
                        $res = M('topic_group')->where(array('id'=>$v['id']))->save(array('status'=>1));
                        
                      }else if($type == 11){
                        $res = M('credits_apply')->where(array('id'=>$v['id']))->save(array('status'=>1));
                        
                      }else if($type == 12){
                        $res = M('activity')->where(array('id'=>$v['id']))->save(array('type'=>0));
                        
                      }
                 }else{
                    $res =  M('audit')->add($auditData);
                 }
                 
               }
                

      }
     
     }


     public function clickTrigger_bak($type){
       if($type == 1){ 

        $map = array(
              'type'=>2,
              
              );
        $lists = M('admin_project')
                ->where($map)
                ->field('*')
                ->select();
        
      // echo M('admin_project')->_sql();
       }else if($type == 2){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('course')
                ->where($map)
                ->field('*')
                ->select();
        // echo M('course')->_sql(); die;
       }else if($type == 3){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('examination')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 4){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('survey')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 5){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('friends_circle')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 6){
        $map = array(
              'audit_state'=>0,
              
              );
        $lists = M('research')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 7){
        $map = array(
              'audit_status'=>1,
              
              );
        $lists = M('test')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 8){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('integration_apply')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 9){
        $map = array(
              'status'=>2,
              
              );
        $lists = M('users')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 10){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('topic_group')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 11){
        $map = array(
              'status'=>0,
              
              );
        $lists = M('credits_apply')
                ->where($map)
                ->field('*')
                ->select();
       }else if($type == 12){
        $map = array(
              'type'=>2,
              
              );
        $lists = M('activity')
                ->where($map)
                ->field('*')
                ->select();
       }
      
      //获取当前审核配置表的数据
      // $type = 1;
      $dataSet = $this->AuditSetData($type);
      // dump($dataSet); exit;
      foreach($lists as $k=>$v){
            // if($v['type'] === null || $v['type'] !== 2 ){
              //判断为待审，把当前审核配置表配置 往审核表里生成关联数据，
              // 1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起积分加分,9:用户注册,10：业务部落 11:发起学分加分 12.活动报名
                if($type == 1){
                  $create_user_id = $v['user_id'];
                }else if($type == 2){
                  $create_user_id = $v['user_id'];
                }else if($type == 3){
                  $create_user_id = $v['test_heir'];
                }else if($type == 4){
                  $create_user_id = $v['survey_heir'];
                }else if($type == 5){
                  $create_user_id = $v['user_id'];
                }else if($type == 6){
                  $create_user_id = $v['create_user'];
                }else if($type == 7){
                  $create_user_id = $v['create_user'];
                }else if($type == 8){
                  $create_user_id = $v['user_id'];
                }else if($type == 9){
                  $create_user_id = $v['id'];
                }else if($type == 10){
                  $create_user_id = $v['user_id'];
                }else if($type == 11){
                  $create_user_id = $v['user_id'];
                }else if($type == 12){
                  $create_user_id = $v['user_id'];
                }
               $res = $this->auditDataExist($v['id'],$type);
              //  echo $v['id'];

               $tissues = $this->getTissueid($create_user_id,$dataSet['one_level_type'],$dataSet['two_level_type'],$dataSet['three_level_type']);
               
                if($tissues['num'] != ''){
                  $dataSet['num'] = $tissues['num'];
                }


               if(!$res){
                //  echo dd;die;
                $auditData = array();
                $auditData = array(
                       'type'=> $type,
                       'correlate_id'=> $v['id'],
                       'oneaudit_role'=> $dataSet['oneaudit_role'],
                       'twoaudit_role'=> $dataSet['twoaudit_role'],
                       'threeaudit_role'=> $dataSet['threeaudit_role'],
                       'is_condition'=> $dataSet['is_condition'],
                       'condition_id'=> $dataSet['condition_id'],
                       'conditiona'=> $dataSet['conditiona'],
                       'conditionb'=> $dataSet['conditionb'],
                       'num'=> $dataSet['num'],
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
                
                //  dump($res);
            // }else{
            //   //非初审，项目表

            // }
      }
     
     
     }


     /** 
     *审核设置的数据
     */
     public function AuditSetData($type,$user_id)
     {  
       $planId = getUserPlanId($user_id); //获取方案
       if($type == 11) $type = 8;  //申请加学分取申请加分的审核设置
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
     * 各表的未审核数据是否在审核表中存在
     */
     public function auditDataExist($id,$type){
              
              $res = M('audit')->where(array('type'=>$type,'correlate_id'=>$id))->find();
             
               if($res){
                 return true;
               }else{
                 return false;
               }


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
                  if($temp3 == 0){
                    $num = 2;
                  }else if($temp2 == 0){
                    $num = 1;
                  }
             }else if(($one_level_type == 3 && $two_level_type == 3) || ($one_level_type == 3 && $three_level_type == 3)|| ($two_level_type == 3 && $three_level_type == 3) ){
                  $temp1 = M('tissue_group_access')->where(array('user_id'=>$create_user_id))->getField('tissue_id');
                  $temp2 = M('tissue_rule')->where(array('id'=>$temp1))->getField('pid') ;
                  $temp2 = $temp2 ? $temp2 : 0;
                  $temp3 = M('tissue_rule')->where(array('id'=>$temp2))->getField('pid');
                  $temp3 = $temp3 ? $temp3 : 0;
                  if($temp2 == 0){
                    $num = 1;
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



     /** 
     *获取用户名
     */
     public function getUserName($user_id)
     {  
      $username = M('users')->where(array('id'=>$user_id))->getField('username');
       return $username;

     }


     /** 
     *获取角色名
     */
     public function getGroupName($id)
     {  
      $title = M('auth_group')->where(array('id'=>$id))->getField('title');
       return $title;

     }

     /** 
     *获取角色名 ,$id为项目id
     */
     public function getpopulation($id)
     {  
      $population = M('designated_personnel')->field('count(*) as population')->where(array('project_id'=>$id))->select();
      // echo M('designated_personnel')->_sql();
       return $population[0]['population'];

     }



















//-----------------------------------------------------------------------详情------------------------------------------------------------------------------//
 
    /**
     * 审核任务详情
     * $id 相应数据主键id
     * $audit_id 审核表id
     * $type 任务类型 1 培训项目 2 新建课程、3 新建试卷、4 新建问卷、5 新建话题（工作圈内容发布）、6 发起调研、7 发起考试、8 申请积分加分、 9 用户注册
     * $audit_status 0:待审核(一级审核) 1:一审通过（二级审核） 2:二审通过（三级审核） 3:三审通过 4:一审拒绝 5:二审拒绝 6:三审拒绝
     * $tabHead  标签tabHead 1:待我审核栏目列表 2:我发起的栏目列表
     */
    public function auditTaskDetail($id,$type,$audit_id,$userId,$tabHead){
        $detail = array();
             
        if($type == 1){
            
            $detail['common_name'] = '培训项目';
            //培训详情
           if(strtolower(C('DB_TYPE')) == 'oracle'){
             $data = M('admin_project')
                    ->field("user_id,type,project_name,training_category,plan_category,project_description,to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time")
                    ->where(array('id'=>$id))
                    ->find();
            
           }else{
             $data = M('admin_project')->field('*')->where(array('id'=>$id))->find();
           }
           $detail['project_name'] = $data['project_name'];
           $detail['start_time'] = $data['start_time'];
           $detail['end_time'] = $data['end_time'];
           $detail['project_description'] = $data['project_description'];
           
           if($data['training_category'] == 0){ //培训类别(0-内部培训,1-外派培训,2-外出授课)
               $detail['training_category'] = '内部培训';
           }else if($data['training_category'] == 1){
               $detail['training_category'] = '外派培训';
           }else if($data['training_category'] == 2){
               $detail['training_category'] = '外出授课';
           }
           
           if($data['plan_category'] == 0){ //计划内外(0-计划内,1-计划外)
               $detail['plan_category'] = '计划内';
           }else if($data['plan_category'] == 1){
               $detail['plan_category'] = '计划外';
           }
           
           //判断是否需要审核-显示审核按钮
           if($data['type'] == 2 && $tabHead == 1){
              $detail['whether_audit'] = 1; //需要审核
           }else{
              $detail['whether_audit'] = 0; //不需审核
           }
           
           
            
          
        }elseif ($type == 5){
            $detail['common_name'] = '用户互动';
            //互动详情
            $data = M('friends_circle')->field('*')->where(array('id'=>$id))->find();
            // dump($data);exit;
            $detail['content'] = strip_tags($data['content']); //话题详情
            
            if(!empty($data['images'])){
              $arr = explode(',',$data['images']); 
              $detail['images'] = $arr[0]; //互动详情图片
            }else{
              $detail['images'] = ''; //互动详情默认图片
            //   $detail['images'] = '/Public/Dist/img/topic.jpg'; //互动详情默认图片
            }
            
            
            //判断是否需要审核-显示审核按钮
            if($data['status'] == 0 && $tabHead == 1){
                $detail['whether_audit'] = 1; //需要审核
            }else{
                $detail['whether_audit'] = 0; //不需审核
            }



        }elseif ($type == 9){
            $detail['common_name'] = '用户注册';
            
            //用户注册详情
            if(strtolower(C('DB_TYPE')) == 'oracle'){
              $data = M('users')->field("username,status,email,to_char(register_time,'YYYY-MM-DD HH24:MI:SS') as register_time")->where(array('id'=>$id))->find();
            }else{
              $data = M('users')->field("*")->where(array('id'=>$id))->find();  
            }
            $detail['email'] = $data['email'];
            $detail['register_time'] = $data['register_time'];
           
            //判断是否需要审核-显示审核按钮
            if($data['status'] == 2 && $tabHead == 1){
                $detail['whether_audit'] = 1; //需要审核
            }else{
                $detail['whether_audit'] = 0; //不需审核
            }

        }elseif ($type == 11){
            $detail['common_name'] = '申请加分';
            //申请加分详情
            $data = M('credits_apply')->field("*")->where(array('id'=>$id))->find();
            $detail['apply_title'] = $data['apply_title']; 
            $detail['apply_description'] = $data['apply_description']; 
            $detail['attachment'] = $data['attachment']; //申请附件
            
            
            //判断是否需要审核-显示审核按钮
            if($data['status'] == 0 && $tabHead == 1){
                $detail['whether_audit'] = 1; //需要审核
            }else{
                $detail['whether_audit'] = 0; //不需审核
            }

        }elseif ($type == 12){
            $detail['common_name'] = '活动报名';
            //申请加分详情
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $data = M('activity')
                        ->field("type,user_id,activity_name,address,to_char(activity_start_time,'YYYY-MM-DD HH24:MI:SS') as activity_start_time,to_char(activity_end_time,'YYYY-MM-DD HH24:MI:SS') as activity_end_time")
                        ->where(array('id'=>$id))
                        ->find(); 
            }else{
                $data = M('activity')->field("*")->where(array('id'=>$id))->find();   
            }
            // echo 1;die;
            $detail['activity_name'] = $data['activity_name'];
            $detail['address'] = $data['address'];
            $detail['activity_start_time'] = $data['activity_start_time'];
            $detail['activity_end_time'] = $data['activity_end_time'];
            
            //判断是否需要审核-显示审核按钮
            if($data['type'] == 2 && $tabHead == 1){
                $detail['whether_audit'] = 1; //需要审核
            }else{
                $detail['whether_audit'] = 0; //不需审核
            }
        }
         

            $detail['username'] = $this->getUserName($data['user_id']); //返回申请人姓名
            if ($type == 9){
               $detail['username'] = $data['username'];
            } 
            
            $detail['tabHead'] = $tabHead;


            //头部审核图标显示
            $data_set = M('audit')
                    ->field("*")
                    ->where(array('id'=>$audit_id))
                    ->find();
            $detail['audit_status'] = $data_set['audit_status']; 
            $detail['audit_id'] = $data_set['id']; 
            $detail['num'] = $data_set['num'];  //审核级数
         
           return $detail;

    }



    /**
     * 审核任务详情
     * $id 审核任务id
     * $audit_id 审核表id
     * $type 任务类型 1 培训项目 2 新建课程、3 新建试卷、4 新建问卷、5 新建话题（工作圈内容发布）、6 发起调研、7 发起考试、8 申请积分加分、 9 用户注册
     * $audit_status 0:待审核(一级审核) 1:一审通过（二级审核） 2:二审通过（三级审核） 3:三审通过 4:一审拒绝 5:二审拒绝 6:三审拒绝
     * $tabHead  标签tabHead 1:待我审核栏目列表 2:我发起的栏目列表
     */
    public function auditTaskDetail_bak($id,$type,$audit_id,$audit_status,$userId,$tabHead){

        //接收用户id,获取用户角色
        $userRole = $this->getUserRole($userId);

        //循环获取用户角色id放入新数组容器中
        $arr = array();
        foreach ($userRole as $v) {
            array_push($arr, $v['group_id']);//array_push往数组中插入一个或者多个元素
        }

        //把数组用英文逗号拼接成字符串
        $group_id = implode(',', $arr);

        //判断审核级别  $audit_status 0一级审核中  1二级审核中  2三级审核中
		//1新建项目 admin_project
        $where['id'] =  $id;
		if($type == 1){
			$detail = M("admin_project")->field("id,project_name,start_time,end_time,add_time as create_time")->where($where)->find();
            $detail['common_name'] = '新建项目';
		//2新建课程 course
		}elseif ($type == 2){
			$detail = M("course")->field("id,course_name,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")->where($where)->find();
            $detail['common_name'] = '新建课程';
		//3新建试卷 examination
		}elseif ($type == 3){
			$detail = M("examination")->field("*,test_upload_time as create_time")->where($where)->find();
            $detail['common_name'] = '新建试卷';
		//4新建问卷 survey
		}elseif ($type == 4){
			$detail = M("survey")->field("*,survey_upload_time as create_time")->where($where)->find();
            $detail['common_name'] = '新建问卷';

		//5 新建话题（工作圈内容发布） friends_circle
        }elseif ($type == 5){
			$detail = M("friends_circle")->field("id,uid,content,images,publish_time as create_time")->where($where)->find();
            $userName = M("users")->where(array('id' => $detail['uid']))->getField('username');
            $detail['username'] = $userName;
            $detail['common_name'] = '新建话题';
		//6发起调研 research
		}elseif ($type == 6){
			$detail = M("research")->field("*")->where($where)->find();
            $detail['common_name'] = '发起调研';
		//7发起考试 test
		}elseif ($type == 7){
			$detail = M("test")->field("*")->where($where)->find();
            $detail['common_name'] = '发起考试';
        //8申请加分
        }elseif ($type == 8){
            $detail = M("integration_apply")->field("id,add_score,apply_description,attachment,FROM_UNIXTIME(add_time,'%Y-%m-%d %H:%i:%s') as create_time")->where($where)->find();
            $detail['common_name'] = '申请加分';

            //9 用户注册 integration_apply
        }elseif ($type == 9){
            $detail = M("users")->field("id,username,avatar,phone,register_time as create_time")->where($where)->find();
            $detail['common_name'] = '用户注册';
            //status 审核状态:0-待审核,1-已通过,2-已拒绝
            //数据库 status` '用户状态 0：拒绝； 1：审核通过 ；2：待审核 ; 3 逻辑删除',  强制转化下
        }
        if(!empty($detail['id'])){
            $detail['audit_status'] = $audit_status;//根据此字段判断通过或拒绝 3为通过
            $detail['audit_id'] = $audit_id;
            $detail['type'] = $type;
            return $detail;
        }else{
            return false;
        }

    }
 
 
 
 
 
 
 
 
 
 
 
 
 
 //--------------------------------------------------------------------审核操作----------------------------------------------------------------------------------//
 


    /**
     * 三期批量审核
     * $audit_id 用户审核表主键id
     * $audit_status  审核级别状态 0:待审核 1:一审通过 2:二审通过 3:三审通过 4:一审拒绝 5:二审拒绝 6:三审拒绝',
     * $type 任务类型 1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册,10:话题小组,11:申请加学分12:活动报名
     * $auditstyle 操作状态 0：不通过 1：通过
     * $objection 拒绝理由
     * $userId 登录者userId
     * return  1：已审核过，不能重复审核  2：操作成功 3：操作失败
     */

    public function operateAuditTask($audit_id,$audit_status,$type,$auditstyle,$objection,$userId){

        //  $ids = I('post.ids'); //接受的$ids为审核表的ids array
         $ids = array();
         $ids[] = $audit_id;

         //判断是否存在重复审核
         $res = $this->repetitiveAudit($ids,$audit_status);
          if($res == 0){
            return 1;   //已审核过，不能重复审核
         }
		//  print_r($tablenames);
		//   print_r($ids); exit;
         if($auditstyle == 0){ //批量拒绝部分
            if($type == 1){ //项目审核-拒绝
             foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>3,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
               
               
               
            }else if($type == 2){ //课程审核-拒绝
               foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>2,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
               
            }else if($type == 3){ //新建试卷审核-拒绝
                foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('examination')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>3,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
               
            }else if($type == 4){ //新建问卷审核-拒绝
               foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('survey')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>3,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
               
            }else if($type == 5){ //新建互动审核-拒绝
               foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('friends_circle')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>2,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
               
            }else if($type == 6){ //发起调研审核-拒绝
               foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>2,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
               
            }else if($type == 7){ //发起考试审核-拒绝
               foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>2,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
             
            }else if($type == 8){ //加分申请审核-拒绝
               foreach($ids as $k=>$v){
                $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>2,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
            }else if($type == 9){ //用户注册审核-拒绝
               foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('users')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>0,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
            }else if($type == 10){ //话题小组审核-拒绝
               foreach($ids as $k=>$v){
               $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('topic_group')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>2,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
            }else if($type == 11){ //加学分申请审核-拒绝
               foreach($ids as $k=>$v){
                $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>2,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
            }else if($type == 12){ //活动报名申请审核-拒绝
               foreach($ids as $k=>$v){
                $dataone = M('audit')->where(array('id'=>$v))->find();
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>4));
                 
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>5)); 
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>6)); 
               }
                $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
                $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>3,'objection'=>$objection));
                $res = M('audit')->where(array('id'=>$v))->save(array('objection'=>$objection));
             }
  
            }

         }else if($auditstyle == 1){ //批量通过部分
            if($type == 1){ //项目审核-通过
             foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
               }

               }else{ //有条件
        
                  $tianjian = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->find();
                  $project_time = $this->diffBetweenTwoDays($tianjian['start_time'],$tianjian['end_time']);
                 //项目指定人员（人数）
                 $designeeNum = M('admin_project')->alias('a')
                              ->join('left join __DESIGNATED_PERSONNEL__ b on b.project_id=a.id')
                              ->where(array('a.id'=>$dataone['correlate_id']))
                              ->count();
                 
                  if($dataone['audit_status'] == 0){
                      $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                    if($dataone['num'] == 1){
                      $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                    }else{
                      if($dataone['condition_id'] == 1){
                         //预留项目时长
                         if($project_time < $dataone['conditiona']){
                         $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0)); 
                        }
                     }else if($dataone['condition_id'] == 2){
                        if($tianjian['project_budget'] < $dataone['conditiona']){
                         $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0)); 
                        }  
                     }else if($dataone['condition_id'] == 3){
                        if($designeeNum < $dataone['conditiona']){
                          $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0)); 
                        }
                     }
                    }

                    
                  }else if($dataone['audit_status'] == 1){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                    if($dataone['num'] == 2){
                      $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                    }else{
                      if($dataone['condition_id'] == 1){
                         //预留项目时长
                        if($project_time < $dataone['conditiona']){
                         $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0)); 
                        }
                     }else if($dataone['condition_id'] == 2){
                        if($tianjian['project_budget'] < $dataone['conditionb']){
                         $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0)); 
                        }  
                     }else if($dataone['condition_id'] == 3){
                        if($designeeNum < $dataone['conditionb']){
                          $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0)); 
                        }
                     }
                    }
                  }else if($dataone['audit_status'] == 2){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                    $res = M('admin_project')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                  }
               }
                   $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
               
            }else if($type == 2){ //新建课程审核-通过
              foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
               }

               }else{ //有条件
        
                  $tianjian = M('course')->where(array('id'=>$dataone['correlate_id']))->find();

                  if($dataone['audit_status'] == 0){
                      $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                    if($dataone['num'] == 1){
                      $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                    }else{
                      if($dataone['condition_id'] == 4){
                         //授课时长
                        if($tianjian['course_time'] < $dataone['conditiona']){
                         $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                        }  
                     }else if($dataone['condition_id'] == 5){
                        if($tianjian['credit'] < $dataone['conditiona']){
                          $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1)); 
                        }
                    
                    }

                    }
                  }else if($dataone['audit_status'] == 1){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                    if($dataone['num'] == 2){
                      $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1)); 
                    }else{
                      if($dataone['condition_id'] == 4){
                        if($tianjian['course_time'] < $dataone['conditionb']){
                         $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1)); 
                        }  
                     }else if($dataone['condition_id'] == 5){
                        if($tianjian['credit'] < $dataone['conditionb']){
                          $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1)); 
                        }
                     }
                    }
                  }else if($dataone['audit_status'] == 2){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                    $res = M('course')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1)); 
                  }
               }
                   $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
               
            }else if($type == 3){ //新建试卷审核-通过
               foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('examination')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('examination')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('examination')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
               }

               }
              $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
              $ret = array(
                     'status'=>1,
                     'info'=>'批量审核通过成功',
					           'url'=>U('Admin/Audit/examinationauditlist')
                      );
              return $ret;
            }else if($type == 4){ //新建问卷审核-通过
                foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('survey')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('survey')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('survey')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
               }

               }
              $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
                        
            }else if($type == 5){ //新建互动审核-通过
                foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('friends_circle')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('friends_circle')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('friends_circle')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
               }

               }
               $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
              
            }else if($type == 6){ //发起调研审核-通过
               foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1));
               }

               }else{ //有条件
        
                  $tianjian = M('research')->where(array('id'=>$dataone['correlate_id']))->find();

                  if($dataone['audit_status'] == 0){
                      $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                    if($dataone['num'] == 1){
                      $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1));
                    }else{
                      if($dataone['condition_id'] == 6){
                         //调研时长
                         $survey_time = $this->diffBetweenTwoDays($tianjian['start_time'], $tianjian['end_time']);
                        if($survey_time < $dataone['conditiona']){
                         $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1));
                        }  
                     }else if($dataone['condition_id'] == 7){
                        if($tianjian['credits'] < $dataone['conditiona']){
                          $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1)); 
                        }
                    
                    }

                    }
                  }else if($dataone['audit_status'] == 1){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                    if($dataone['num'] == 2){
                      $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1)); 
                    }else{
                      if($dataone['condition_id'] == 6){
                        //调研时长
                         $survey_time = $this->diffBetweenTwoDays($tianjian['start_time'], $tianjian['end_time']);
                        if($survey_time < $dataone['conditionb']){
                         $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1)); 
                        }  
                     }else if($dataone['condition_id'] == 7){
                        if($tianjian['credits'] < $dataone['conditionb']){
                          $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1)); 
                        }
                     }
                    }
                  }else if($dataone['audit_status'] == 2){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                    $res = M('research')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_state'=>1)); 
                  }
               }
                   $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
               
            }else if($type == 7){ //发起考试审核-通过
               foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
               }

               }else{ //有条件
        
                  $tianjian = M('test')->where(array('id'=>$dataone['correlate_id']))->find();
                  $test_time = $this->diffBetweenTwoMinutes($tianjian['start_time'], $tianjian['end_time']);
                  $testerNum = M('test')->alias('a')
                              ->join('left join __TEST_USER_REL__ b on b.test_id=a.id')
                              ->where(array('a.id'=>$dataone['correlate_id']))
                              ->count();

                  if($dataone['audit_status'] == 0){
                      $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                    if($dataone['num'] == 1){
                      $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
                    }else{
                      if($dataone['condition_id'] == 8){
                         //考试时长（分钟）
                         
                        if($test_time < $dataone['conditiona']){
                         $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
                        }  
                     }else if($dataone['condition_id'] == 9){ //学分（分）
                        if($tianjian['score'] < $dataone['conditiona']){
                          $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0)); 
                        }
                    
                     }else if($dataone['condition_id'] == 10){ //指定人员（人数）
                        if($testerNum < $dataone['conditiona']){
                          $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0)); 
                        }
                    
                     }

                    }
                  }else if($dataone['audit_status'] == 1){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                    if($dataone['num'] == 2){
                     $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
                    }else{
                      if($dataone['condition_id'] == 8){
                        //调研时长
                        if($test_time < $dataone['conditionb']){
                         $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
                        }  
                     }else if($dataone['condition_id'] == 9){
                        if($tianjian['score'] < $dataone['conditionb']){
                         $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
                        }
                     }else if($dataone['condition_id'] == 10){
                        if($testerNum < $dataone['conditionb']){
                         $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0));
                        }
                     }
                    }
                  }else if($dataone['audit_status'] == 2){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                    $res = M('test')->where(array('id'=>$dataone['correlate_id']))->save(array('audit_status'=>0)); 
                  }
               }
                   $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
               
            }else if($type == 8){ //加分申请审核-通过
              foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                
                if($dataone['num'] == 1){
                  $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                  $rest = $this->applyIntegration($dataone['correlate_id']);
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                  $rest = $this->applyIntegration($dataone['correlate_id']);
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                $rest = $this->applyIntegration($dataone['correlate_id']);
               }

               }else{ //有条件
        
                  $tianjian = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->find();
 
                  if($dataone['audit_status'] == 0){
                      $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                    if($dataone['num'] == 1){
                      $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                      $rest = $this->applyIntegration($dataone['correlate_id']);
                    }else{
                      if($dataone['condition_id'] == 11){
                         //积分分值 
                        if($tianjian['add_score'] < $dataone['conditiona']){
                         $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                         $rest = $this->applyIntegration($dataone['correlate_id']);
                        }  
                      }
                     } 
                  }else if($dataone['audit_status'] == 1){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                    if($dataone['num'] == 2){
                     $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                     $rest = $this->applyIntegration($dataone['correlate_id']);
                    }else{
                      if($dataone['condition_id'] == 11){
                        //调研时长
                        if($tianjian['add_score'] < $dataone['conditionb']){
                         $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                         $rest = $this->applyIntegration($dataone['correlate_id']);
                        }  
                     }
                    }
                  }else if($dataone['audit_status'] == 2){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                    $res = M('integration_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                    $rest = $this->applyIntegration($dataone['correlate_id']);
                  }
               }
                   $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
               
            }else if($type == 9){ //用户注册审核-通过
            
              foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
            //    if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                    
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('users')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('users')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('users')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
               }

            //    }
               
               $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
             
            }else if($type == 10){ //话题小组审核-通过
              foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                if($dataone['num'] == 1){
                  $res = M('topic_group')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('topic_group')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('topic_group')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
               }

               }
               $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
              
            }else if($type == 11){ //加学分申请审核-通过
              foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                
                if($dataone['num'] == 1){
                  $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                  $rest = $this->applyCredits($dataone['correlate_id']);
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                  $rest = $this->applyCredits($dataone['correlate_id']);
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                $rest = $this->applyCredits($dataone['correlate_id']);
               }

               }else{ //有条件
        
                  $tianjian = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->find();
 
                  if($dataone['audit_status'] == 0){
                      $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                    if($dataone['num'] == 1){
                      $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                      $rest = $this->applyCredits($dataone['correlate_id']);
                    }else{
                      if($dataone['condition_id'] == 11){
                         //学分分值 
                        if($tianjian['add_score'] < $dataone['conditiona']){
                         $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                         $rest = $this->applyCredits($dataone['correlate_id']);
                        }  
                      }
                     } 
                  }else if($dataone['audit_status'] == 1){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                    if($dataone['num'] == 2){
                     $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                     $rest = $this->applyCredits($dataone['correlate_id']);
                    }else{
                      if($dataone['condition_id'] == 11){
                        //学分分值 
                        if($tianjian['add_score'] < $dataone['conditionb']){
                         $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                         $rest = $this->applyCredits($dataone['correlate_id']);
                        }  
                     }
                    }
                  }else if($dataone['audit_status'] == 2){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                    $res = M('credits_apply')->where(array('id'=>$dataone['correlate_id']))->save(array('status'=>1));
                    $rest = $this->applyCredits($dataone['correlate_id']);
                  }
               }
                   $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
               
            }else if($type == 12){ //活动报名审核-通过
              foreach($ids as $k=>$v){
               
               $dataone = M('audit')->where(array('id'=>$v))->find();
               if($dataone['is_condition'] == 0){ //无条件
                 if($dataone['audit_status'] == 0){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                
                if($dataone['num'] == 1){
                  $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                 
                }
               }else if($dataone['audit_status'] == 1){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                if($dataone['num'] == 2){
                  $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                  
                }
               }else if($dataone['audit_status'] == 2){
                $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
               
               }

               }else{ //有条件
        
                  $tianjian = M('activity')->where(array('id'=>$dataone['correlate_id']))->find();
 
                  if($dataone['audit_status'] == 0){
                      $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>1));
                    if($dataone['num'] == 1){
                      $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                     
                    }else{
                      if($dataone['condition_id'] == 12){
                         //人数上限 
                        if($tianjian['population'] < $dataone['conditiona']){
                         $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                   
                        }  
                      }
                     } 
                  }else if($dataone['audit_status'] == 1){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>2)); 
                    if($dataone['num'] == 2){
                     $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                  
                    }else{
                      if($dataone['condition_id'] == 12){
                        //
                        if($tianjian['population'] < $dataone['conditionb']){
                         $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
                 
                        }  
                     }
                    }
                  }else if($dataone['audit_status'] == 2){
                    $res = M('audit')->where(array('id'=>$v))->save(array('audit_status'=>3)); 
                    $res = M('activity')->where(array('id'=>$dataone['correlate_id']))->save(array('type'=>0));
            
                  }
               }
                   $res = $this->threeAuditMan($v,$dataone['audit_status'],$userId);
             }
               
            }
         }
         
         if($res){
            return 2;
        }else{
            return 3;
        }

     }




    /**
     * 审核通过后，申请加分记录插入积分记录表
     */
    public function applyIntegration($id){
        
        $dataone = M('integration_apply')->where(array('id'=>$id))->find();
        $arr = array(
            'time'=>time(),
            'uid'=>$dataone['uid'],
            'score'=>$dataone['add_score'],
            'type'=>'申请加分',
            'describe'=>'申请加分-'.$dataone['apply_title'],
            'apply_id'=>$dataone['id'],
        );
        $ret1 = M('integration_record')->add($arr);
        if ($ret1 === false) {
            $this->rollback();
            return false;
        }else{
            return true;
        }
    }
    
    
    
    /*
     *判断是否已经审核过，若审核过则提示“该条数据已审核，请勿重复审核”
     *@param array $ids
     *@param array $audit_status
     *@return bool
     */
    public function repetitiveAudit($ids,$audit_status)
    {
        if(count($ids) == 1){
            $exist  = M('audit')->where(array('id'=>$ids[0],'audit_status'=>$audit_status))->find();
        }

        if(!$exist){
            return 0;
        }else{
            return 1;
        }

    }


    /**
     * 审核通过后，申请加学分记录插入学分统计表think_center_study
     */
    public function applyCredits($id){
        $dataone = M('credits_apply')->where(array('id'=>$id))->find();

        $arr = array(
            'create_time'=>array('exp',"to_date('".date('Y-m-d H:i:s',$dataone['add_time'])."','yy-mm-dd hh24:mi:ss')"),
            'typeid'=>6,
            'credit'=>$dataone['add_score'],
            'user_id'=>$dataone['user_id'],
        );

        if(strtolower(C('DB_TYPE')) == 'oracle'){
        $arr['id'] = getNextId('center_study');
        }

        $ret1 = M('center_study')->add($arr);
        if ($ret1 === false) {
           
           return false;
        }else{
            return true;
        }
    }

    /**
     *三期批量审核时写入审核人
     */
    public function threeAuditMan($id,$auditstatus,$userId){

        if($auditstatus == 0){
            $res = M('audit')->where(array('id'=>$id))->save(array('levalone_man'=>$userId));
        }else if($auditstatus == 1){
           $res =  M('audit')->where(array('id'=>$id))->save(array('levaltwo_man'=>$userId));
        }else if($auditstatus == 2){
           $res = M('audit')->where(array('id'=>$id))->save(array('levalthree_man'=>$userId));
        }
        return $res;
    }


    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return number
     */
    public function diffBetweenTwoDays ($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }

    /**
     * 求两个日期之间相差的分钟数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $time1
     * @param string $time2
     * @return number
     */
    public function diffBetweenTwoMinutes ($time1, $time2)
    {
        $second1 = strtotime($time1);
        $second2 = strtotime($time2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 60;
    }



    








    
 
 
 //--------------------------------------------------------------------旧的数据------------------------------------------------------------------------------------//
    /*
     * 获取用户角色
     *$uid 用户id
     */
    public function getUserRole($uid){

    	$result = M("Auth_group_access a")
            ->join('LEFT JOIN __AUTH_GROUP__ b ON b.id = a.group_id')
            ->field("a.*,b.title,b.status")
            ->where(array("a.uid" => $uid))->select();

        return $result;
    }



    /**
     *根据登陆者是否为负责人--并返回所在组织的tissue_id
     *
     */
    public function getPrincipalTissueId($userId)
    {
        $tissue_id = M('tissue_group_access')->where(array('user_id'=>$userId,'manage_id'=>2))->getField('tissue_id');
        return $tissue_id = $tissue_id ? $tissue_id : '-1';
    }

    /**
     * 任务列表
     * taskStatus 任务状态 1待我审核 (type 1待办任务 2已完成)   2我发起的  不传默认taskStatus为1，type为1
     * page 页码，不传默认第一页
     */
    public function getAuditTaskList($taskType,$type,$page,$userId,$pageLen)
    {

        //接收用户id,获取用户角色
        $userRole = $this->getUserRole($userId);

        //循环获取用户角色id放入新数组容器中
        $arr = array();
        foreach ($userRole as $v) {
            array_push($arr, $v['group_id']);//array_push往数组中插入一个或者多个元素
        }

        $tissue_id = $this -> getPrincipalTissueId($userId);
        //把数组用英文逗号拼接成字符串
        $group_id = implode(',', $arr);

        $where = "     ( b.one_level_type = 2 AND b.audit_status = 0 AND b.oneaudit_role in ($group_id) )
                      OR ( b.two_level_type = 2 AND b.audit_status = 1 AND b.twoaudit_role in ($group_id) )
                      OR ( b.three_level_type = 2 AND b.audit_status = 2 AND b.threeaudit_role in ($group_id))

                      OR ( b.one_level_type = 1 AND b.audit_status = 0 AND (b.oneaudit_uid = $userId OR b.twoaudit_uid = $userId OR b.threeaudit_uid = $userId))
                      OR ( b.two_level_type = 1 AND b.audit_status = 1 AND (b.oneaudit_uid = $userId OR b.twoaudit_uid = $userId OR b.threeaudit_uid = $userId))
                      OR ( b.three_level_type = 1 AND b.audit_status = 2 AND (b.oneaudit_uid = $userId OR b.twoaudit_uid = $userId OR b.threeaudit_uid = $userId))




                      OR ( b.one_level_type = 3 AND b.audit_status = 0 AND b.one_leader_tissueid = $tissue_id )
                      OR ( b.two_level_type = 3 AND b.audit_status = 1 AND b.two_leader_tissueid = $tissue_id)
                      OR ( b.three_level_type = 3 AND b.audit_status = 2 AND b.three_leader_tissueid = $tissue_id)

                      " ;
        //"( b.audit_status = 0 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 1 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 2 AND b.threeaudit_role in ($group_id) )"
        //根据用户拥有角色和审核状态判断审核级别
        //待我审核 $taskType = 1
        //b.type 1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册
        if ($taskType == 1) {
            //$type = 1 待办任务   待审核状态的任务列表
            if ($type == 1) {

                //项目审核条件
                $where1['b.type'] = 1;
                $where1['a.type'] = 2;

                //课程审核条件
                $where2['b.type'] = 2;
                $where2['a.status'] = 0;

                //新建试卷审核条件
                $where3['b.type'] = 3;
                $where3['a.status'] = 0;

                //课程审核条件 array('b.type' => 4, 'a.status' => 0)
                $where4['b.type'] = 4;
                $where4['a.status'] = 0;

                //发布工作圈审核条件 array('a.status' => 0, 'a.pid' => 0, 'b.type' => 5)
                $where5['b.type'] = 5;
                $where5['a.pid'] = 0;
                $where5['a.status'] = 0;

                //发起调研审核条件 array('a.audit_state' => 0, 'b.type' => 6)
                $where6['b.type'] = 6;
                $where6['a.audit_state'] = 0;

                //发起考试审核条件 array('a.audit_status' => 1, 'b.type' => 7)
                $where7['b.type'] = 7;
                $where7['a.audit_status'] = 1;

                //加分申请审核条件 array('a.status' => 0, 'b.type' => 8)
                $where8['b.type'] = 8;
                $where8['a.status'] = 0;

                //用户注册审核条件 array('a.status' => 2, 'b.type' => 9)
                $where9['b.type'] = 9;
                $where9['a.status'] = 2;

                //项目审核  b.type = 1
                //a.type'=>2 表示待审核的项目
                $list1 = M('admin_project')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where1)
                    ->order('a.id desc')
                    ->field('a.*,b.id as audit_id,b.audit_status,a.add_time as create_time,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //新建课程审核  b.type = 2
                //a.status=>0 表示待审核的新建课程
                $list2 = M('course')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where2)
                    ->order('a.id desc')
                    ->field("a.*,FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s') as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                    ->select();


                //新建试卷  b.type = 3
                //a.status=>0 表示待审核的新建试卷
                $list3 = M('examination')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where3)
                    ->order('a.id desc')
                    ->field('a.*,a.test_upload_time as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //新建问卷  b.type = 4
                //a.status=>0 表示待审核的新建问卷
                $list4 = M('survey')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where4)
                    ->order('a.id desc')
                    ->field('a.*,a.survey_upload_time as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //发布工作圈  b.type = 5
                //a.status=>0 表示待审核的工作圈
                $list5 = M('friends_circle')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where5)
                    ->order('a.id desc')
                    ->field('a.*,a.publish_time as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //发起调研  b.type = 6
                //a.audit_state=>0=>1 表示待审核的调研
                $list6 = M('research')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where6)
                    ->order('a.id desc')
                    ->field('a.*,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();

                //发起考试  b.type = 7
                //a.audit_status'=>1 表示待审核的考试
                $list7 = M('test')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where7)
                    ->order('a.id desc')
                    ->field('a.*,a.audit_status as status,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //加分申请  b.type = 8
                //a.status=>0 表示待审核的加分申请
                $list8 = M('integration_apply')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where8)
                    ->order('a.id desc')
                    ->field("a.*,FROM_UNIXTIME(a.add_time,'%Y-%m-%d %H:%i:%s') as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                    ->select();


                //用户注册  b.type = 9
                //a.status=>2 表示待审核的注册用户
                $list9 = M('users')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where)
                    ->where($where9)
                    ->order('a.id desc')
                    ->field('a.*,a.register_time as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();
                $data = array_merge_recursive($list1,$list2,$list3,$list4,$list5,$list6,$list7,$list8,$list9);
                //把指定我审核和我具有指定角色或者我是指定用户才可显示的数据

                    $array_data = array();
                    foreach($data as $k => $v){
                        if(in_array($v['oneaudit_role'],$arr) || in_array($v['twoaudit_role'],$arr)  || in_array($v['threeaudit_role'],$arr) || $userId ==  $v['levalone_man'] || $userId ==  $v['levalwo_man'] || $userId ==  $v['levalthree_man'] || $userId ==  $v['oneaudit_uid'] || $userId ==  $v['twoaudit_uid'] || $userId ==  $v['threeaudit_uid']){
                        $array_data[$k]['id'] = $v['id'];
                        $array_data[$k]['audit_id'] = $v['audit_id'];
                        $array_data[$k]['audit_status'] = $v['audit_status'];
                        $array_data[$k]['create_time'] = $v['create_time'];
                        switch($v['type']){
                            //type=1 新建项目
                            case 1;
                                $array_data[$k]['common_name'] = '新建项目';
                                $array_data[$k]['type'] = 1;
                                break;

                            //type=2 新建课程
                            case 2;
                                $array_data[$k]['common_name'] = '新建课程';
                                $array_data[$k]['type'] = 2;
                                break;

                            //type=3 新建试卷
                            case 3;
                                $array_data[$k]['common_name'] = '新建试卷';
                                $array_data[$k]['type'] = 3;
                                break;


                            //type=4 新建问卷
                            case 4;
                                $array_data[$k]['common_name'] = '新建问卷';
                                $array_data[$k]['type'] = 4;
                                break;


                            //type=5 新建互动
                            case 5;
                                $array_data[$k]['common_name'] = '新建互动';
                                $array_data[$k]['type'] = 5;
                                break;


                            //type=6 发起调研
                            case 6;
                                $array_data[$k]['common_name'] = '发起调研';
                                $array_data[$k]['type'] = 6;
                                break;


                            //type=7 发起考试
                            case 7;
                                $array_data[$k]['common_name'] = '发起考试';
                                $array_data[$k]['type'] = 7;
                                break;


                            //type=8 发起加分
                            case 8;
                                $array_data[$k]['common_name'] = '发起加分';
                                $array_data[$k]['type'] = 8;
                                break;

                            //type=9 用户注册
                            case 9;
                                $array_data[$k]['common_name'] = '用户注册';
                                $array_data[$k]['type'] = 9;
                                break;
                        }
                }

                }
            } else if ($type == 2) {//$type = 2 已完成(包括已拒绝和已通过的审核)

                //项目审核条件  array('a.type'=>array('in','0,3,4')) 0已通过 3已拒绝 4已完成
                $where1['b.type'] = 1;
                $where1['a.type'] = array('in', '0,3,4');

                //课程审核条件   array('a.status'=>array('in','1,2')) 1已通过 2已拒绝
                $where2['b.type'] = 2;
                $where2['a.status'] = array('in', '1,2');

                //新建试卷审核条件   array('a.status'=>array('in','1,2')) 1已通过 2已拒绝
                $where3['b.type'] = 3;
                $where3['a.status'] = array('in', '1,2');

                //新建问卷审核条件 array('a.status'=>array('in','1,3')) 1已通过 3已拒绝
                $where4['b.type'] = 4;
                $where4['a.status'] = array('in', '1,3');

                //发布工作圈审核条件 array('a.status' => 0, 'a.pid' => 0, 'b.type' => 5)  1已通过 2已拒绝
                $where5['b.type'] = 5;
                $where5['a.pid'] = 0;
                $where5['a.status'] = array('in', '1,2');

                //发起调研审核条件 array('a.audit_state' => 0, 'b.type' => 6) 1已通过  2已拒绝
                $where6['b.type'] = 6;
                $where6['a.audit_state'] = array('in', '1,2');

                //发起考试审核条件 array('a.audit_status' => 1, 'b.type' => 7)  1已通过  2已拒绝
                $where7['b.type'] = 7;
                $where7['a.audit_status'] = array('in', '1,2');

                //加分申请审核条件 array('a.status' => 0, 'b.type' => 8)   1已通过  2已拒绝
                $where8['b.type'] = 8;
                $where8['a.status'] = array('in', '1,2');

                //用户注册审核条件 array('a.status' => 2, 'b.type' => 9)  1已通过  0已拒绝
                $where9['b.type'] = 9;
                $where9['a.status'] = array('in', '1,0');

                //已拒绝和已完成的培训班
                $list1 = M('admin_project')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where1)
                    ->field('a.*,a.add_time as create_time,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //已拒绝和已通过的课程审核
                $list2 = M('course')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where2)
                    ->order('a.id desc')
                    ->field("a.*,FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s') as create_time,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                    ->select();


                //已拒绝和已通过的试卷审核
                $list3 = M('examination')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where3)
                    ->order('a.id desc')
                    ->field('a.*,a.test_upload_time as create_time,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //已拒绝和已通过的问卷审核
                $list4 = M('survey')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where4)
                    ->order('a.id desc')
                    ->field('a.*,a.survey_upload_time as create_time,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //已拒绝和已通过的工作圈审核
                $list5 = M('friends_circle')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where5)
                    ->order('a.id desc')
                    ->field('a.*,a.publish_time as create_time,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //已拒绝和已通过的调研审核
                $list6 = M('research')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where6)
                    ->order('a.id desc')
                    ->field('a.*,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //已拒绝和已通过的考试审核
                $list7 = M('test')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where7)
                    ->order('a.id desc')
                    ->field('a.audit_status as status,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();


                //已拒绝和已通过的加分审核
                $list8 = M('integration_apply')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where8)
                    ->order('a.id desc')
                    ->field("a.*,FROM_UNIXTIME(a.add_time,'%Y-%m-%d %H:%i:%s') as create_time,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                    ->select();


                //已拒绝和已通过的用户注册审核
                $list9 = M('users')->alias('a')
                    ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                    ->where($where9)
                    ->order('a.id desc')
                    ->field('a.*,a.register_time as create_time,b.id as audit_id,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.audit_status,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.oneaudit_uid,b.twoaudit_uid,b.threeaudit_uid,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                    ->select();

                $data = array_merge_recursive($list1, $list2, $list3, $list4, $list5, $list6, $list7, $list8, $list9);
                $array_data = array();

                foreach ($data as $k => $v) {
                    //把指定我审核和我具有指定角色或者我是指定用户才可显示的数据
                    if(in_array($v['oneaudit_role'],$arr) || in_array($v['twoaudit_role'],$arr)  || in_array($v['threeaudit_role'],$arr) || $userId ==  $v['levalone_man'] || $userId ==  $v['levalwo_man'] || $userId ==  $v['levalthree_man'] || $userId ==  $v['oneaudit_uid'] || $userId ==  $v['twoaudit_uid'] || $userId ==  $v['threeaudit_uid']){
                        $array_data[$k]['id'] = $v['id'];
                        $array_data[$k]['audit_id'] = $v['audit_id'];
                        $array_data[$k]['audit_status'] = $v['audit_status'];
                        $array_data[$k]['create_time'] = $v['create_time'];
                        switch ($v['type']) {
                            //type=1 新建项目
                            case 1;
                                $array_data[$k]['common_name'] = '新建项目';
                                $array_data[$k]['type'] = 1;
                                break;

                            //type=2 新建课程
                            case 2;
                                $array_data[$k]['common_name'] = '新建课程';
                                $array_data[$k]['type'] = 2;
                                break;

                            //type=3 新建试卷
                            case 3;
                                $array_data[$k]['common_name'] = '新建试卷';
                                $array_data[$k]['type'] = 3;
                                break;


                            //type=4 新建问卷
                            case 4;
                                $array_data[$k]['common_name'] = '新建问卷';
                                $array_data[$k]['type'] = 4;
                                break;


                            //type=5 新建互动
                            case 5;
                                $array_data[$k]['common_name'] = '新建互动';
                                $array_data[$k]['type'] = 5;
                                break;


                            //type=6 发起调研
                            case 6;
                                $array_data[$k]['common_name'] = '发起调研';
                                $array_data[$k]['type'] = 6;
                                break;


                            //type=7 发起考试
                            case 7;
                                $array_data[$k]['common_name'] = '发起考试';
                                $array_data[$k]['type'] = 7;
                                break;


                            //type=8 发起加分
                            case 8;
                                $array_data[$k]['common_name'] = '发起加分';
                                $array_data[$k]['type'] = 8;
                                break;

                            //type=9 用户注册
                            case 9;
                                $array_data[$k]['common_name'] = '用户注册';
                                $array_data[$k]['type'] = 9;
                                break;
                        }
                    }

                }
            }



        } else if ($taskType == 2) {//我发起的  $taskType = 2

            //项目审核条件
            $where1['b.type']  = 1;
            $where1['a.type'] = array('in','0,2,3,4');


            //课程审核条件
            $where2['b.type'] = 2;
            $where2['a.status'] = array('in','0,1,2');


            //新建试卷审核条件
            $where3['b.type'] = 3;
            $where3['a.status'] = array('in','0,1,2');


            //问卷审核条件 array('b.type' => 4, 'a.status' => 0)
            $where4['b.type'] = 4;
            $where4['a.status'] = array('in','0,1,2');


            //发布工作圈审核条件 array('a.status' => 0, 'a.pid' => 0, 'b.type' => 5)
            $where5['b.type'] = 5;
            $where5['a.pid'] = 0;
            $where5['a.status'] = array('in','0,1,2');


            //发起调研审核条件 array('a.audit_state' => 0, 'b.type' => 6)
            $where6['b.type'] = 6;
            $where6['a.audit_state'] = array('in','0,1,2');


            //发起考试审核条件 array('a.audit_status' => 1, 'b.type' => 7)
            $where7['b.type'] = 7;
            $where7['a.audit_status'] = array('in','0,1,2');


            //加分申请审核条件 array('a.status' => 0, 'b.type' => 8)
            $where8['b.type'] = 8;
            $where8['a.status'] = array('in','0,1,2');


            //用户注册审核条件 array('a.status' => 2, 'b.type' => 9)
            $where9['b.type'] = 9;
            $where9['a.status'] = array('in','0,1,2');

            //关联用户id为uid
            $condition1 = "a.uid = ".$userId." AND (( b.audit_status = 0 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 1 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 2 AND b.threeaudit_role in ($group_id)) OR b.audit_status = 3 ) OR ( b.audit_status = 4 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 5 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 6 AND b.threeaudit_role in ($group_id))";

            //关联用户id为user_id
            $condition2 = "a.user_id = ".$userId." AND (( b.audit_status = 0 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 1 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 2 AND b.threeaudit_role in ($group_id)) OR b.audit_status = 3 ) OR ( b.audit_status = 4 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 5 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 6 AND b.threeaudit_role in ($group_id))";

            //关联用户id为test_heir
            $condition3 = "a.test_heir = ".$userId." AND (( b.audit_status = 0 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 1 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 2 AND b.threeaudit_role in ($group_id)) OR b.audit_status = 3 ) OR ( b.audit_status = 4 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 5 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 6 AND b.threeaudit_role in ($group_id))";

            //关联用户id为create_user
            $condition4 = "a.survey_heir = ".$userId." AND (( b.audit_status = 0 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 1 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 2 AND b.threeaudit_role in ($group_id)) OR b.audit_status = 3 ) OR ( b.audit_status = 4 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 5 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 6 AND b.threeaudit_role in ($group_id))";

            //关联用户id为survey_heir
            $condition5 = "a.create_user = ".$userId." AND (( b.audit_status = 0 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 1 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 2 AND b.threeaudit_role in ($group_id)) OR b.audit_status = 3 ) OR ( b.audit_status = 4 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 5 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 6 AND b.threeaudit_role in ($group_id))";

            //关联用户id为id
            $condition6 = "a.id = ".$userId." AND (( b.audit_status = 0 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 1 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 2 AND b.threeaudit_role in ($group_id)) OR b.audit_status = 3 ) OR ( b.audit_status = 4 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 5 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 6 AND b.threeaudit_role in ($group_id))";


            //$where = "( b.audit_status = 3 ) OR ( b.audit_status = 4 AND b.oneaudit_role in ($group_id) ) OR ( b.audit_status = 5 AND b.twoaudit_role in ($group_id) ) OR ( b.audit_status = 6 AND b.threeaudit_role in ($group_id)";
            //项目审核  b.type = 1
            //a.type'=>2 表示待审核的项目
            //a.type'=>2 表示待审核的项目 audit_status=0 audit_status=1 audit_status=2  audit_status=3 (已通过)   audit_status=4 audit_status=5 audit_status=6(已拒绝)
            $list1 = M('admin_project')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition1)//uid
                ->where($where1)
                ->order('a.id desc')
                ->field('a.*,b.id as audit_id,b.audit_status,a.add_time as create_time,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();





            //新建课程审核  b.type = 2
            //a.status=>0 表示待审核的新建课程
            $list2 = M('course')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition2)//user_id
                ->where($where2)
                ->order('a.id desc')
                ->field("a.*,FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s') as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                ->select();




            //新建试卷  b.type = 3
            //a.status=>0 表示待审核的新建试卷
            $list3 = M('examination')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition3)//test_heir
                ->where($where3)
                ->order('a.id desc')
                ->field('a.*,a.test_upload_time as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();



            //新建问卷  b.type = 4
            //a.status=>0 表示待审核的新建问卷
            $list4 = M('survey')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition4)//survey_heir
                ->where($where4)
                ->order('a.id desc')
                ->field('a.*,a.survey_upload_time as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();




            //发布工作圈  b.type = 5
            //a.status=>0 表示待审核的工作圈
            $list5 = M('friends_circle')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition1)//uid
                ->where($where5)
                ->order('a.id desc')
                ->field('a.*,a.publish_time as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();




            //发起调研  b.type = 6
            //a.audit_state=>0=>1 表示待审核的调研
            $list6 = M('research')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition5)//create_user
                ->where($where6)
                ->order('a.id desc')
                ->field('a.*,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();



            //发起考试  b.type = 7
            //a.audit_status'=>1 表示待审核的考试
            $list7 = M('test')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition5)//create_user
                ->where($where7)
                ->order('a.id desc')
                ->field('a.audit_status as status,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();




            //加分申请  b.type = 8
            //a.status=>0 表示待审核的加分申请
            $list8 = M('integration_apply')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition1)
                ->where($where8)
                ->order('a.id desc')
                ->field("a.*,FROM_UNIXTIME(a.add_time,'%Y-%m-%d %H:%i:%s') as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num")
                ->select();




            //用户注册  b.type = 9
            //a.status=>2 表示待审核的注册用户
            $list9 = M('users')->alias('a')
                ->join('left join __AUDIT__ as b on b.correlate_id = a.id')
                ->where($condition6)
                ->where($where9)
                ->order('a.id desc')
                ->field('a.*,a.register_time as create_time,b.id as audit_id,b.audit_status,b.type,b.levalone_man,b.levaltwo_man,b.levalthree_man,b.objection,b.oneaudit_role,b.twoaudit_role,b.threeaudit_role,b.is_condition,b.condition_id,b.conditiona,b.conditionb,b.num')
                ->select();

            $data = array_merge_recursive($list1,$list2,$list3,$list4,$list5,$list6,$list7,$list8,$list9);

            $array_data = array();
            foreach($data as $k => $v){
                $array_data[$k]['id'] = $v['id'];
                $array_data[$k]['audit_id'] = $v['audit_id'];
                $array_data[$k]['audit_status'] = $v['audit_status'];
                $array_data[$k]['create_time'] = $v['create_time'];
                switch($v['type']){
                    //type=1 新建项目
                    case 1;
                        $array_data[$k]['common_name'] = '新建项目';
                        $array_data[$k]['type'] = 1;
                        break;

                    //type=2 新建课程
                    case 2;
                        $array_data[$k]['common_name'] = '新建课程';
                        $array_data[$k]['type'] = 2;
                        break;

                    //type=3 新建试卷
                    case 3;
                        $array_data[$k]['common_name'] = '新建试卷';
                        $array_data[$k]['type'] = 3;
                        break;


                    //type=4 新建问卷
                    case 4;
                        $array_data[$k]['common_name'] = '新建问卷';
                        $array_data[$k]['type'] = 4;
                        break;


                    //type=5 新建互动
                    case 5;
                        $array_data[$k]['common_name'] = '新建互动';
                        $array_data[$k]['type'] = 5;
                        break;


                    //type=6 发起调研
                    case 6;
                        $array_data[$k]['common_name'] = '发起调研';
                        $array_data[$k]['type'] = 6;
                        break;


                    //type=7 发起考试
                    case 7;
                        $array_data[$k]['common_name'] = '发起考试';
                        $array_data[$k]['type'] = 7;
                        break;


                    //type=8 发起加分
                    case 8;
                        $array_data[$k]['common_name'] = '发起加分';
                        $array_data[$k]['type'] = 8;
                        break;

                    //type=9 用户注册
                    case 9;
                        $array_data[$k]['common_name'] = '用户注册';
                        $array_data[$k]['type'] = 9;
                        break;
                }
            }
        }
        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'create_time',       //排序字段
        );
        $arrSort = array();
        foreach($array_data AS $uniqid => $row){
            foreach($row AS $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $array_data);
        }
        global $countpage; //定全局变量
        //$page=(empty($page))?'1':$page; //判断当前页面是否为空 如果为空就表示为第一页面
        $start=($page-1)*$pageLen; //计算每次分页的开始位置
        /*if($order==1){
            $array=array_reverse($array);
        }*/
        $totals=count($array_data);
        $countpage=ceil($totals/$pageLen); //计算总页面数
        $arrayData=array_slice($array_data,$start,$pageLen);
        return $arrayData;
    }





}