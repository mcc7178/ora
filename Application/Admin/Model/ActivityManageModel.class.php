<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 活动管理模型
 */
class ActivityManageModel extends BaseModel{
    protected $tableName= 'activity';

	/**
     * 活动报名管理列表页
     * type 0已发布 1未发布 2待审核 3审核不通过 4报名截止
  	 */
	public function  index($type){
        
        $user_id = $_SESSION["user"]["id"];
		$size = 15;
        $keyword = I('get.table_search');
        $condition = array(
            'activity_name'=>array('like',"%$keyword%")
        );
		$p = I("get.p",1,'int');


        if($type == 0){
            //点击菜单选项，筛选已发布的超过截止日期的活动状态变为type = 4
            $this->changeType();

            $where = array('type'=>0);
            $data = M('activity')->where($condition)->where($where)->page($p.','.$size)->order('id desc')->select();  
            
            foreach($data as $k=>$v){
                $apply_count =  M('activity_personnel')->where(array('activity_id'=>$v['id'],'status'=>array('in','0,1')))->count();
                $apply_pass_count =  M('activity_personnel')->where(array('activity_id'=>$v['id'],'status'=>1))->count();
                $data[$k]['apply_count'] = $apply_count;
                $data[$k]['apply_pass_count'] = $apply_pass_count;
            } 
            
            

		}else if($type == 1){
            $where = array('type'=>1);
            $data = M('activity')->where($condition)->where($where)->page($p.','.$size)->order('id desc')->select();  
        }else if($type == 2){
            $where = array('type'=>2);
            $data = M('activity')->where($condition)->where($where)->page($p.','.$size)->order('id desc')->select();  
        }else if($type == 3){
            $where = array('type'=>3);
            $data = M('activity')->where($condition)->where($where)->page($p.','.$size)->order('id desc')->select();  
		}else if($type == 4){
            $where = array('type'=>4);
            $data = M('activity')->where($condition)->where($where)->page($p.','.$size)->order('id desc')->select();  
            foreach($data as $k=>$v){
                $apply_count =  M('activity_personnel')->where(array('activity_id'=>$v['id'],'status'=>array('in','0,1')))->count();
                $apply_pass_count =  M('activity_personnel')->where(array('activity_id'=>$v['id'],'status'=>1))->count();
                $data[$k]['apply_count'] = $apply_count;
                $data[$k]['apply_pass_count'] = $apply_pass_count;
            } 
		}
             
        // dump($data); 
           $count = count($data);
           $show = $this->pageClass($count,$size);
       
	      $return = array(
              'keyword'=>$keyword,
			  'data'=>$data,
			  'page'=>$show
		  );
	   return $return;
    }

   /***
    * 超过截止日期的活动状态变更
    */  
   public function changeType(){

        if(strtolower(C('DB_TYPE')) == 'oracle'){
        $list = M('activity')
                 ->where(array('type'=>0))
                 ->where("apply_end_time <= to_date('". date('Y-m-d H:i:s') ."','yyyy-mm-dd hh24:mi:ss')")
                 ->select(); 
        }else{
        $map = array(
            'apply_end_time'=>array('lt',date('Y-m-d H:i:s'))
        );
        $list = M('activity')
                 ->where(array('type'=>0))
                 ->where($map)
                 ->select();    
        }
        // dump($list);
        foreach($list as $k=>$v){
          $res =  M('activity')->where(array('id'=>$v['id']))->save(array('type'=>4));
        }

   }


   /***
    * 新增 活动报名
    */  
   public function adds(){
        $fdata = I('post.');
        
        $orderno_data = D('Trigger')->orderNumber(12);
        $orderno = $orderno_data['no'];

        $data = array(
            'user_id'=>(int)$_SESSION['user']['id'],
            'activity_name'=>$fdata['activity_name'],
            'add_time'=>date('Y-m-d H:i:s'),
            'activity_start_time'=>$fdata['activity_start_time'],
            'activity_end_time'=>$fdata['activity_end_time'],
            'activity_covers'=>$fdata['activity_covers'],
            'activity_description'=>$fdata['activity_description'],
            'apply_start_time'=>$fdata['apply_start_time'],
            'apply_end_time'=>$fdata['apply_end_time'],
            'address'=>$fdata['address'],
            'tissue_id'=>json_encode($fdata['tissue_id']),
            'population'=>(int)$fdata['population'],
            'orderno'=>(int)$orderno,
            'need_audit'=>(int)$fdata['need_audit'],
            'auth_user_id'=>(int)$_SESSION['user']['id'],
        );
        
       if(strtolower(C('DB_TYPE')) == 'oracle'){
            if(empty($fdata['id'])){ 
             $data['id'] =  getNextId('activity');
            }else{
             $data['id'] = (int)$fdata['id'];
            }
            $data['add_time'] =  array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            $data['activity_start_time'] =  array('exp',"to_date('".$fdata['activity_start_time']."','yy-mm-dd hh24:mi:ss')");
            $data['activity_end_time'] =  array('exp',"to_date('".$fdata['activity_end_time']."','yy-mm-dd hh24:mi:ss')");
            $data['activity_start_time'] =  array('exp',"to_date('".$fdata['activity_start_time']."','yy-mm-dd hh24:mi:ss')");
            $data['apply_start_time'] = array('exp',"to_date('".$fdata['apply_start_time']."','yy-mm-dd hh24:mi:ss')"); 
            $data['apply_end_time'] =  array('exp',"to_date('".$fdata['apply_end_time']."','yy-mm-dd hh24:mi:ss')");
       }
         
      if($fdata['typeid'] == 1){
          $data['type'] = 1;
      }else if($fdata['typeid'] == 2){
          $data['type'] = 2;
          if($orderno_data['status'] == 0) $data['type'] = 0;//无需审核，状态改为0-已发布

      }

    //   dump($data); die;
      if(empty($fdata['id'])){
       $res = M('activity')->add($data);
          write_login_log(12,2,$data['activity_name']);
      }else{
       $res = M('activity')->save($data);
          write_login_log(12,3,$data['activity_name']);
      }

      return $res;
  
   }



   /***
    * 活动报名 详情查看  $type==0 活动信息,$type==1 报名列表
    */
   public function show($id,$type){
    

        $user_id = $_SESSION["user"]["id"];
		$size = 15;
        $keyword = I('get.table_search');
        
		$p = I("get.p",1,'int');
		
		$need_audit = M('activity')->where(array('id'=>$id))->getField('need_audit');
		$status = I('get.status','-1');
        if($type == 0){
           	
			$where = array('a.id'=>$id);
		   	if($status != '-1'){
		   		$where['b.status'] = $status;
		   	}
           	if(strtolower(C('DB_TYPE')) == 'oracle'){
	            $data = M('activity a')
	            	->join('left join __ACTIVITY_PERSONNEL__ b on b.activity_id=a.id')
	                ->where($where)
	                ->field("a.id,a.activity_name,a.activity_covers,a.activity_description,a.address,to_char(a.activity_start_time,'YYYY-MM-DD HH24:MI:SS') as activity_start_time,to_char(a.activity_end_time,'YYYY-MM-DD HH24:MI:SS') as activity_end_time,to_char(a.apply_start_time,'YYYY-MM-DD HH24:MI:SS') as apply_start_time,to_char(a.apply_end_time,'YYYY-MM-DD HH24:MI:SS') as apply_end_time")
	                ->find();
           	}else{
            	$data = M('activity a')
	            	->join('left join __ACTIVITY_PERSONNEL__ b on b.activity_id=a.id')
	                ->where($where)
	                ->find();
           	}
		}else if($type == 1){
            $condition = array(
            	'b.username'=>array('like',"%$keyword%")
            );
            $where = array(
                'a.activity_id'=>$id,
//              'a.status'=>array('neq','2')
            );
			
		   	if($status != '-1'){
		   		$where['a.status'] = $status;
		   	}
            if(strtolower(C('DB_TYPE')) == 'oracle'){
            	$data = M('activity_personnel a')
                    ->field("a.id,a.status,a.activity_id,to_char(a.add_time,'YYYY-MM-DD HH24:MI:SS') as add_time,to_char(a.audit_time,'YYYY-MM-DD HH24:MI:SS') as audit_time,a.user_id,a.apply_reason,b.username,b.job_number,b.phone,b.email")
                    ->join('left join __USERS__ b on b.id=a.user_id')
                    ->where($where)
                    ->where($condition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->select();  
            }else{
            	$data = M('activity_personnel a')
                    ->field("a*,b.username,b.job_number,b.phone,b.email")
                    ->join('left join __USERS__ b on b.id=a.user_id')
                    ->where($where)
                    ->where($condition)
                    ->page($p.','.$size)
                    ->order('a.id desc')
                    ->select();    
            }
            //取报名的具体信息

            foreach($data as $k=>$v){
                $map = array('a.user_id'=>$v['user_id']);
                $tissue_name = M('tissue_group_access a')->join('left join __TISSUE_RULE__ b on b.id=a.tissue_id')->where($map)->getfield('b.name');
                $job_name = M('tissue_group_access a')->join('left join __JOBS_MANAGE__ b on b.id=a.job_id')->where($map)->getfield('b.name');
                $data[$k]['tissue_name'] = $tissue_name;
                $data[$k]['job_name'] = $job_name;
				$data[$k]['need_audit'] = $need_audit;
            }
            
            // //导出考勤
            // if(I('get.createexcel') == 1){
            //     $this->createExcel($data);
            // }


        }
             
        
           $count = count($data);
           $show = $this->pageClass($count,$size);
       
	      $return = array(
              'keyword'=>$keyword,
			  'data'=>$data,
			  'page'=>$show,
			  'status'=>$status
		  );
	   return $return;
   }


   /***
    * 活动报名 编辑查看
    */
   public function edit($id){
       if(strtolower(C('DB_TYPE')) == 'oracle'){
        $data = M('activity')
                 ->where(array('id'=>$id))
                 ->field("id,tissue_id,population,need_audit,activity_name,activity_covers,activity_description,address,to_char(activity_start_time,'YYYY-MM-DD HH24:MI:SS') as activity_start_time,to_char(activity_end_time,'YYYY-MM-DD HH24:MI:SS') as activity_end_time,to_char(apply_start_time,'YYYY-MM-DD HH24:MI:SS') as apply_start_time,to_char(apply_end_time,'YYYY-MM-DD HH24:MI:SS') as apply_end_time")
                 ->find();  

       }else{
        $data = M('activity')->where(array('id'=>$id))->field("*")->find();    
       }
        $tissue = json_decode($data['tissue_id']); 
        
        $department = array();
        foreach($tissue as $k=>$v){
            $tissue_name =  M('tissue_rule')->where(array('id'=>$v))->getField('name');
            $department[$k]['name'] = $tissue_name;
            $department[$k]['id'] = $v;
        } 
       
        
        $return = array(
            'data'=>$data,
            'department'=>$department
        );
         
       return $return;
   }


    /***
    *  批量刪除 活动报名
    */
    public function batchdelete(){
         if(IS_AJAX){
          $ids = I('post.ids'); //接受的$ids为活动报名ids

		  // 自动启动事务支持
         $this->startTrans();
         try {  
          	foreach($ids as $k=>$v){
	            $status = array('type'=>5);//改为取消发布状态    //改为草稿(未发布)
				$res = M('activity')->where(array('id'=>$v))->save($status);
				//修改成功，审核状态改变
				/*
				 重新（即将表数据的状态变为待审核）提交接口 $res = D('Trigger')->projectResubmit($id,1);
				@param $type 类型(1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册)
				*/
				// D('Trigger')->projectResubmit($v, 12);

                $activity_list = M('activity')->field("activity_name")->where(array('id'=>$v))->find();

                write_login_log(12,4,$activity_list['activity_name']);
				
	            if(!$res) return false;
            }

			
         // 提交事务
            $this->commit();
            $ret = array(
                     'status'=>1,
                     'info'=>'修改成功',
					 'url'=>U('Admin/Attendance/index')
                      );
           
           
		 } catch (ThinkException $e) {
            $this->rollback();
			$ret = array(
                     'status'=>0,
                     'info'=>'修改失败',
					 'url'=>''
                    );
         }
          return $ret;
		 }
    }



   /***
    *  活动报名管理 -- 通过报名学员申请
    */
   public function auditPass(){
         if(IS_AJAX){
          $id = I('post.id'); //接受的$ids为活动报名ids


            $status = array('status'=>1);
			$res = M('activity_personnel')->where(array('id'=>$id))->save($status); 
            if($res){
            $ret = array(
                     'status'=>1,
                     'info'=>'审核通过成功',
					 'url'=>''
                      );
            }else{
			$ret = array(
                     'status'=>0,
                     'info'=>'审核通过失败',
					 'url'=>''
                    );
         
            }
          return $ret;

		 }

   }


   /***
    * 活动报名管理 -- 拒绝报名学员申请  
    */
   public function auditRefuse(){
         if(IS_AJAX){
          $id = I('post.id'); //接受的$ids为活动报名ids


            $status = array('status'=>2);
			$res = M('activity_personnel')->where(array('id'=>$id))->save($status); 
            if($res){
            $ret = array(
                     'status'=>1,
                     'info'=>'审核拒绝成功',
					 'url'=>''
                      );
            }else{
			$ret = array(
                     'status'=>0,
                     'info'=>'审核拒绝失败',
					 'url'=>''
                    );
         
            }
          return $ret;

		 }

   }


   /**
    *导出考勤Excel
    */
     public function createExcel(){ 
       
         $activity_id = I('get.activity_id') + 0;  //活动报名id

  
            $where = array(
                'a.activity_id'=>$activity_id,
                // 'a.status'=>array('neq','2')
                );
            if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data = M('activity_personnel a')
                    ->field("a.id,a.status,a.activity_id,to_char(a.add_time,'YYYY-MM-DD HH24:MI:SS') as add_time,to_char(a.audit_time,'YYYY-MM-DD HH24:MI:SS') as audit_time,a.user_id,a.apply_reason,b.username,b.job_number,b.phone,b.email")
                    ->join('left join __USERS__ b on b.id=a.user_id')
                    ->where($where)
                    ->order('a.id desc')
                    ->select();  
            }else{
            $data = M('activity_personnel a')
                    ->field("a*,b.username,b.job_number,b.phone,b.email")
                    ->join('left join __USERS__ b on b.id=a.user_id')
                    ->where($where)
                    ->order('a.id desc')
                    ->select();    
            }
            //取报名的具体信息

            foreach($data as $k=>$v){
                $map = array('a.user_id'=>$v['user_id']);
                $tissue_name = M('tissue_group_access a')->join('left join __TISSUE_RULE__ b on b.id=a.tissue_id')->where($map)->getfield('b.name');
                $job_name = M('tissue_group_access a')->join('left join __JOBS_MANAGE__ b on b.id=a.job_id')->where($map)->getfield('b.name');
                $data[$k]['tissue_name'] = $tissue_name;
                $data[$k]['job_name'] = $job_name;
                if($v['status'] == 0){
                    $data[$k]['status_name'] = '待审核';
                }else if($v['status'] == 1){
                    $data[$k]['status_name'] = '已通过';
                }else{
                    $data[$k]['status_name'] = '未通过';
                }
            }
            

            $out_data = array();
            $out_data[0] = array(
            '0'=>'姓名',
            '1' =>'状态',
            '2'=>'组织架构',
            '3'=>'岗位',
            '4'=>'工号',
            '5'=>'手机号',
            '6'=>'邮箱',
            '7'=>'留言',
            );


            foreach($data as $k=>$v){

              $out_data[$k+1] =  array(
                '0'=>$v['username'],
                '1' =>$v['status_name'],
                '2'=>$v['tissue_name'],
                '3'=>$v['job_name'],
                '4'=>$v['job_number'],
                '5'=>$v['phone'],
                '6'=>$v['email'],
                '7'=>$v['apply_reason'],
                );

            }

         $subName = date(YmdHis).time();
         $filename= $subName.'.xls';
         if(I('type') == 'excel'){
           create_xls($out_data,$filename);
         }
         
     }





}