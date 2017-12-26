<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 活动管理模型
 */
class ActivityApplyModel extends BaseModel{
    protected $tableName= 'activity';

	/**
     * 所有活动列表页  登录者在公开范围
     * type 0所有活动 1已报名活动
  	 */
	public function  index($type){
        $user_id = $_SESSION["user"]["id"];
		$size = 15;
        $keyword = I('get.table_search');
        
		$p = I("get.p",1,'int');

         //点击菜单选项，筛选已发布的超过截止日期的活动状态变为type = 4
         D('ActivityManage')->changeType();

        if($type == 0){
            //根据登录者获取 登录者在公开范围的所有报名活动
            $where = array('type'=>array('in','0,4'));
            
            $condition = array(
            'activity_name'=>array('like',"%$keyword%")
            );
            if(strtolower(C('DB_TYPE')) == 'oracle'){
            $arr = M('activity')
                  ->where($condition)
                  ->where($where)
                  ->page($p.','.$size)
                  ->order('id desc')
                  ->field("id,tissue_id,activity_name,activity_covers,activity_description,address,to_char(activity_start_time,'YYYY-MM-DD HH24:MI:SS') as activity_start_time,to_char(activity_end_time,'YYYY-MM-DD HH24:MI:SS') as activity_end_time,to_char(apply_start_time,'YYYY-MM-DD HH24:MI:SS') as apply_start_time,to_char(apply_end_time,'YYYY-MM-DD HH24:MI:SS') as apply_end_time")
                  ->select();  
            }else{
             $arr = M('activity')
                  ->where($condition)
                  ->where($where)
                  ->page($p.','.$size)
                  ->order('id desc')
                  ->select();    
            }
            $data = array();
            // dump($arr);
            //登录者的组织id
            $tissue_id = $this->getTissue($user_id);
            foreach($arr as $k=>$v){
               $tissue_arr =  json_decode($v['tissue_id']);
               if(in_array($tissue_id,$tissue_arr)){ //判断登录者是否在公开范围
                   $data[$k] = $v;

                   //登录者是否已经报名 $apply_status  0待审核 1通过 2未报名
                   $apply_status = $this->isApply($v['id']);
                   $data[$k]['apply_status'] = $apply_status;

               }
            }
		}else if($type == 1){
            $condition = array(
            	'a.activity_name'=>array('like',"%$keyword%")
            );
            
            $where = array('b.status'=>array('in','0,1'),'b.user_id'=>$user_id);
            $where['a.type'] = array('neq',1);
			
            //已报名的展示所有历史数据，包括报名时间截止和报名活动时间结束的
            if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data = M('activity a')
                     ->join('left join __ACTIVITY_PERSONNEL__ b on b.activity_id=a.id')
                     ->where($condition)
                     ->where($where)
                    //  ->where("a.activity_end_time >= to_date('". date('Y-m-d H:i:s') ."','yyyy-mm-dd hh24:mi:ss')")
                     ->page($p.','.$size)
                     ->order('a.id desc') 
                     ->field("b.status,a.id,a.tissue_id,a.activity_name,a.activity_covers,a.activity_description,a.address,to_char(a.activity_start_time,'YYYY-MM-DD HH24:MI:SS') as activity_start_time,to_char(a.activity_end_time,'YYYY-MM-DD HH24:MI:SS') as activity_end_time,to_char(a.apply_start_time,'YYYY-MM-DD HH24:MI:SS') as apply_start_time,to_char(a.apply_end_time,'YYYY-MM-DD HH24:MI:SS') as apply_end_time")
                     ->select(); 
            }else{
            	$data = M('activity a')
                        ->field('a.*')
        				->join('left join __ACTIVITY_PERSONNEL__ b on b.activity_id=a.id')
        				->where($condition)
        				->where($where)
        				->page($p.','.$size)
        				->order('a.id desc')
        				->select();  
            }
        }
             
       $count = count($data);
       $show = $this->pageClass($count,$size);
   
      	$return = array(
         	'keyword'=>$keyword,
		 	'data'=>$data,
		 	'page'=>$show
	  	);
	   	return $return;
    }


	/**
     * 获取登录者所在组织id
  	 */
	public function  getTissue($user_id){
             $tissue_id = M('tissue_group_access')->where(array('user_id'=>$user_id))->getField('tissue_id');
             return $tissue_id;
    }

	/**
     * 判断登录者是否报名 
     * 参数 $id 活动id
     * return $apply_status  0待审核 1通过 2未报名
  	 */
	public function  isApply($id){
            $where = array(
                'a.id'=>$id,
                'b.user_id'=>$_SESSION["user"]["id"]
            );
            $res = M('activity')->alias('a')
                          ->join('left join __ACTIVITY_PERSONNEL__ b on b.activity_id=a.id')
                          ->where($where)
                          ->find();  
            if(!$res){
                $apply_status = 2;
            }else{
               if($res['status'] == 0){
                 $apply_status = 0;
               }else if($res['status'] == 1){
                 $apply_status = 1;
               }else if($res['status'] == 2){
                 $apply_status = 2;
               }
            }
        return $apply_status;
    }


   /***
    * 所有活动/已报名 - 详情页
    * 
    */
   public function show($id,$login_id){
        //根据登录者获取 登录者在公开范围的所有报名活动
            $where = array('id'=>$id);
            
            
            if(strtolower(C('DB_TYPE')) == 'oracle'){
            $arr = M('activity')
                  ->where($where)
                  ->field("id,tissue_id,activity_name,activity_covers,activity_description,population,address,to_char(activity_start_time,'YYYY-MM-DD HH24:MI:SS') as activity_start_time,to_char(activity_end_time,'YYYY-MM-DD HH24:MI:SS') as activity_end_time,to_char(apply_start_time,'YYYY-MM-DD HH24:MI:SS') as apply_start_time,to_char(apply_end_time,'YYYY-MM-DD HH24:MI:SS') as apply_end_time")
                  ->find();  
            }else{
             $arr = M('activity')
                  ->where($where)
                  ->find();    
            }
            $data = array();
            //登录者的组织id
            $tissue_id = $this->getTissue($login_id);
           
            $tissue_arr =  json_decode($arr['tissue_id']);
            if(in_array($tissue_id,$tissue_arr)){ //判断登录者是否在公开范围
                $data = $arr;
                //登录者是否已经报名 $apply_status  0待审核 1通过 2未报名
                $apply_status = $this->isApply($id);
                $data['apply_status'] = $apply_status;
                
            }
            
            //该活动已报名人数
            $map = array('activity_id'=>$id,'status'=>array('in','0,1'));
            $count = M('activity_personnel')->where($map)->count(); 
           $data['count'] = $count ? $count : 0;
           return $data;
   }


   /***
    * 活动报名 - 立即报名
    */
   public function apply(){
         if(IS_AJAX){
         $id = I('post.id'); //接受的$id为活动报名id
         $apply_reason = I('post.applyReason'); //接受的申请理由

         $user_id = $_SESSION["user"]["id"];
         
         $dataone = M('activity')->where(array('id'=>$id))->find(); 


		  // 自动启动事务支持
         $this->startTrans();
         try {  
          
            //判断think_activity_personnel表的该条活动是否存在登录者报名，存在则改变状态，不存在则增加数据
            $exist =  M('activity_personnel')->where(array('activity_id'=>$id,'user_id'=>$user_id))->find();
            if($exist){
                $data = array(
                    'status'=>0,
                    'apply_reason'=>$apply_reason
                );
                if($dataone['need_audit'] == 0) $data['status'] = 1;
                $res =  M('activity_personnel')->where(array('activity_id'=>$id,'user_id'=>$user_id))->save($data); 
            }else{
                $data = array(
                    'activity_id'=>$id,
                    'user_id'=>$user_id,
                    'status'=>0,
                    'apply_reason'=>$apply_reason
                );
                if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $data['id'] = getNextId('activity_personnel');
                    $data['add_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                }else{
                    $data['add_time'] = date('Y-m-d H:i:s');
                }

                if($dataone['need_audit'] == 0) $data['status'] = 1;
                $res = M('activity_personnel')->add($data); 
            }


            if(!$res) return false;

         // 提交事务
            $this->commit();

            $ret = array(
                     'status'=>1,
                     'info'=>'报名成功',
					 'url'=>''
                      );
		 } catch (ThinkException $e) {
            $this->rollback();
			$ret = array(
                     'status'=>0,
                     'info'=>'报名失败',
					 'url'=>''
                    );
         }
        //查询活动名称
       $activityName = M('Activity')->where(array('id' => $id))->find();
        write_login_log(12,2,$activityName['activity_name']);//日志类型（12-活动报名） 操作类型（2新增，3编辑，4删除）
          return $ret;

		 }

   }






}