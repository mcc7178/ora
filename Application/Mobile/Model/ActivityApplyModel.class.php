<?php
namespace Mobile\Model;

use Think\Model;
/**
 * 活动管理模型
 * @auth Andy 675283203@qq.com
 */
class ActivityApplyModel extends CommonModel{
    protected $tableName= 'activity';

	/**
     * 所有活动列表页  登录者在公开范围
     * type 0所有活动 1已报名活动
     * @Param $userId 用户id
     * @Param $page 分页参数
     * @Param $pageNum 每页显示总条数
     * @Param $keyword  搜索关键词
  	 */
	public function  activityList($type,$userId,$page,$pageNum,$keyword){
        //接收搜索关键字解码
        $keyword = urldecode($keyword);
        iconv( 'CP1252', 'UTF-8', $keyword);
        $start_page = ($page - 1) * $pageNum;
         //点击菜单选项，筛选已发布的超过截止日期的活动状态变为type = 4
       //  D('ActivityManage')->changeType();
        if(!empty($keyword)){
            $condition = array(
                'activity_name'=>array('like',"%$keyword%")
            );
        }
        if($type == 0){//全部
            //根据登录者获取 登录者在公开范围的所有报名活动
            $where = array('type'=>0);

            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "id,tissue_id,activity_name,activity_covers,activity_description,address,to_char(add_time,'YYYY-MM-DD HH24:MI:SS') as add_time,to_char(activity_start_time,'YYYY-MM-DD HH24:MI:SS') as activity_start_time,to_char(activity_end_time,'YYYY-MM-DD HH24:MI:SS') as activity_end_time,to_char(apply_start_time,'YYYY-MM-DD HH24:MI:SS') as apply_start_time,to_char(apply_end_time,'YYYY-MM-DD HH24:MI:SS') as apply_end_time";
            }else{
                $field = ".*";
            }
            $arr_data = M('activity')
                ->where($condition)
                ->where($where)
                ->field($field)
                ->limit($start_page,$pageNum)
                ->order('add_time desc')
                ->select();
            //登录者的组织id
            $tissue_id = $this->getTissue($userId);
            foreach($arr_data as $k => $v){
               $tissue_arr =  json_decode($v['tissue_id']);
               if(in_array($tissue_id,$tissue_arr)){ //判断登录者是否在公开范围
                   //登录者是否已经报名 $apply_status  0审核中 1已通过 3未报名(显示申请操作按钮) 2已拒绝
                   $apply_status = $this->isApply($v['id'],$userId);
                   $v['apply_status']  = $apply_status;
                   unset($v['tissue_id']);
                   $data[] = $v;
               }
            }
		}else if($type == 1){//已报名
             //status 1报名成功 0已报名，审核中  2已拒绝
            $where = array('b.status'=>array('in','0,1,2'));
            $where['a.type'] = array('neq',1);
            $where['b.user_id'] = array('eq',$userId);
            $where['_logic'] = "AND";
            if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data = M('activity a')
                     ->join('left join __ACTIVITY_PERSONNEL__ b on b.activity_id=a.id')
                     ->where($condition)
                     ->where($where)
                     ->where("a.activity_end_time >= to_date('". date('Y-m-d H:i:s') ."','yyyy-mm-dd hh24:mi:ss')")
                     ->limit($start_page,$pageNum)
                     ->field("a.id,a.activity_name,a.activity_covers,a.activity_description,a.address,a.apply_time,to_char(a.activity_start_time,'YYYY-MM-DD HH24:MI:SS') as activity_start_time,to_char(a.activity_end_time,'YYYY-MM-DD HH24:MI:SS') as activity_end_time,to_char(a.apply_start_time,'YYYY-MM-DD HH24:MI:SS') as apply_start_time,to_char(a.apply_end_time,'YYYY-MM-DD HH24:MI:SS') as apply_end_time,b.status")
                     ->order('a.apply_time desc')
                     ->select();
            }else{
            	$data = M('activity a')
                        ->field('a.*')
        				->join('left join __ACTIVITY_PERSONNEL__ b on b.activity_id=a.id')
        				->where($condition)
        				->where($where)
                        ->field("a.tissue_id,true")
                        ->limit($start_page,$pageNum)
        				->order('a.apply_time desc')
        				->select();
            }
        }
        if($data){
            return array('code'=>1000,"message"=>"获取成功",'data'=>$data);
        }else{
            return array('code'=>1030,"message"=>"无数据返回");
        }
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
     * return $apply_status  0审核中  1已通过  3未报名 2已拒绝
  	 */
	public function  isApply($id,$userId){
            $where = array(
                'a.id'=>$id,
                'b.user_id'=>$userId
            );
            $res = M('activity')->alias('a')
                          ->join('left join __ACTIVITY_PERSONNEL__ b on b.activity_id=a.id')
                          ->where($where)
                          ->find();  
            if(!$res){
                $apply_status = 3;//未报名
            }else{
               if($res['status'] == 0){
                 $apply_status = 0;//审核中
               }else if($res['status'] == 1){
                 $apply_status = 1;//已通过
               }else if($res['status'] == 2){
                 $apply_status = 2;//已拒绝
               }
            }
        return $apply_status;
    }


   /***
    * 所有活动/已报名 - 详情页
    * @Param $id 活动id
    * @Param $userId 用户id
    */
   public function activityDetails($id,$userId){
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
            $tissue_id = $this->getTissue($userId);
           
            $tissue_arr =  json_decode($arr['tissue_id']);
            if(in_array($tissue_id,$tissue_arr)){ //判断登录者是否在公开范围
                $data = $arr;
                //登录者是否已经报名 $apply_status  0待审核 1通过 2未报名
                $apply_status = $this->isApply($id,$userId);
                $data['apply_status'] = $apply_status;
                
            }
            
            //该活动已报名人数
           // $map = array('activity_id'=>$id,'status'=>array('in','0,1'));
           // $count = M('activity_personnel')->where($map)->count();
           //$data['count'] = $count ? $count : 0;
           unset($data['tissue_id']);
       if($data){
           return array('code'=>1000,"message"=>"获取成功",'data'=>$data);
       }else{
           return array('code'=>1030,"message"=>"无数据返回");
       }
   }


   /***
    * 活动报名 - 立即报名
    * @Param $id 活动id
    * @Param $userId 用户id
    */
   public function activityRegistration($id,$userId,$apply_reason){

         $act_data = M('activity')->where(array('id'=>$id))->find();
		  // 自动启动事务支持
         $this->startTrans();
         try {
            //判断think_activity_personnel表的该条活动是否存在登录者报名，存在则改变状态，不存在则增加数据
            $exist =  M('activity_personnel')->where(array('activity_id'=>$id,'user_id'=>$userId))->find();
            if($exist){
                $data = array(
                    'status'=>0,
                    'apply_reason'=>$apply_reason
                );
                if($act_data['need_audit'] == 0){
                    $data['status'] = 1;
                }

                $_data['apply_time'] = time();
                $resData =  M('activity')->where(array('id'=>$id))->save($_data);
                $res =  M('activity_personnel')->where(array('activity_id'=>$id,'user_id'=>$userId))->save($data);
            }else{
                $data = array(
                    'activity_id'=>$id,
                    'user_id'=>$userId,
                    'status'=>0,
                    'apply_reason'=>$apply_reason
                );
                if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $data['id'] = getNextId('activity_personnel');
                    $data['add_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                }else{
                    $data['add_time'] = date('Y-m-d H:i:s');
                }

                if($act_data['need_audit'] == 0){
                    $data['status'] = 1;
                }

                $_data['apply_time'] = time();
                $resData =  M('activity')->where(array('id'=>$id))->save($_data);
                $res = M('activity_personnel')->add($data); 
            }


            if(!$res){
                return $ret = array(
                    'code'=>1030,
                    'message'=>'报名失败',
                );
            }

         // 提交事务
            $this->commit();
            //查询活动名称
       $activityName = M('Activity')->where(array('id' => $id))-find();
        $this->write_login_log(12,2,$activityName['activity_name'],$userId);//日志类型（12-活动报名） 操作类型（2新增，3编辑，4删除）
            $ret = array(
                     'code'=>1000,
                     'message'=>'报名成功',
                      );
		 } catch (ThinkException $e) {
            $this->rollback();
			$ret = array(
                     'code'=>1030,
                     'message'=>'报名失败',
                    );
         }
          return $ret;
   }
}