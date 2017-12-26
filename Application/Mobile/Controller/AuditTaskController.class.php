<?php
namespace Mobile\Controller;
use Think\Controller;
/**
 * @author Lizhongjian 20170519
 *
 */
class AuditTaskController extends CommonController {
	/**
	 * 初始化
	 */
	function __construct(){
		parent::__construct();
	}
	



//--------------------------------------------------------------------审核列表----------------------------------------------------------------------------------//
 

	/**
	 * 审核任务一级列表
	 * 
	 */
	public function getAuditOneList(){
            //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataGet();
            $data = D("AuditTask")->getAuditOneList($userId);
            if($data['code'] == 1000){
              $this->success(1000,$data['message'],$data['data']);
            }else{
             $this->error(1030,$data['message']);
            }
    }

     

	/**
	 * 审核任务二级列表
	 *  
	 */
	public function getAuditTwoList(){
            //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataGet();
            $type = I('get.type') + 0; // $type 审核类型   
            $data = D("AuditTask")->getAuditTwoList($userId,$type);
            // dump( $data['code'] );exit;
            
            if($data['code'] == 1000){
              $this->success(1000,$data['message'],$data['data']);
            }else{
             $this->error(1030,$data['message']);
            }
    }


//--------------------------------------------------------------------审核详情----------------------------------------------------------------------------------//
 
	/**
	 * 审核任务详情(可做审核操作)
     * type  1项目审核  5工作圈审核   8申请加分  9用户注册
     * id  对应不同审核类型表得主键id  (项目id or 课程id or 用户id or 申请加分表id。。。。。。)
	 * audit_id 审核表主键 id
	 * audit_status  当前获取的审核状态 用作显示一级审核/二级审核/三级审核
     * tabHead  标签tabHead 1:待我审核栏目列表 2:我发起的栏目列表
	 */
	public function auditTaskDetail(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
		$id = I('get.id','','int');
        $type = I('get.type','','int');
		$audit_id = I('get.audit_id','','int');
		// $audit_status = I('get.audit_status','','int');
        $tabHead = I('get.tabHead','','int');
        
        if($type < 1 || empty($type)){

            $this->error(1030,'参数有误:任务类型必须为非负整数');
        }

        if($id < 1 || empty($id)){

            $this->error(1030,'参数有误:对应审核类型id必须为非负整数');
        }

        if($audit_id < 1 || empty($audit_id)){

            $this->error(1030,'参数有误:审核id必须为非负整数');
        }

        if($audit_status < 0 && $audit_status == ''){

            $this->error(1030,'参数有误:任务审核状态必须为非负整数');
        }
        
        if($tabHead < 1 || empty($tabHead)){

            $this->error(1030,'参数有误:标签tabHead必须为非负整数');
        }

		$return = D("AuditTask")->auditTaskDetail($id,$type,$audit_id,$userId,$tabHead);
		if($return){
			$this->success(1000,'获取数据成功',$return);
		}else{
			$this->error(1030,'暂无数据返回');
		}
	}



//--------------------------------------------------------------------审核操作----------------------------------------------------------------------------------//
 


    /**
     * 操作审核（通过或拒绝）
     * $audit_id 用户审核表主键id
     * $audit_status  审核级别状态 0:待审核 1:一审通过 2:二审通过 3:三审通过 4:一审拒绝 5:二审拒绝 6:三审拒绝',
     * $type 任务类型 1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册,10:话题小组,11:申请加学分12:活动报名
     * $auditstyle 操作状态 0：拒绝 1：通过
     * $objection 拒绝理由
     * $userId 登录者userId
     * return  1：已审核过，不能重复审核  2：操作成功 3：操作失败
     */
    public function operateAuditTask(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();

        //接受的$audit_id为审核表的id
        $audit_id = I('post.audit_id','','int');
        if($audit_id === '') $this->error(1030,'参数有误');
       
        //接收页面当时该条数据的审核状态
        $audit_status = I('post.audit_status','','int');
         
        if($audit_status === '') $this->error(1030,'参数有误');
        
        //接收审核任务类型参数type 1项目审核  5工作圈审核   8申请加分  9用户注册
        $type = I('post.type','','int');
        if($type === '') $this->error(1030,'参数有误');
        
        //接收操作状态 0：不通过 1：通过
        $auditstyle =I('post.auditstyle','','int');
        if($auditstyle === '') $this->error(1030,'参数有误');

        //接收$objection 拒绝理由
        $objection =I('post.objection','','string');



        $result = D("AuditTask")->operateAuditTask($audit_id,$audit_status,$type,$auditstyle,$objection,$userId);
        
        $data['type'] = $type;
        
        if($result == 1){
            $this->error(1030,'已审核过，不能重复审核');
        }elseif($result == 2){
            $this->success(1000,'操作成功',$data);
        }elseif($result == 3){
            $this->error(1030,'操作失败');
        }
    }



















//--------------------------------------------------------------------旧的数据----------------------------------------------------------------------------------//
 

	/**
	 * 审核任务列表--废弃
	 * taskStatus 任务状态 1待我审核 (type 1待办任务 2已完成)   2我发起的  不传默认taskStatus为1，type为1
	 * page 页码，不传默认第1页
	 * pageLen 每页显示条数，默认30条
	 */
	public function getAuditTaskList(){

            //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataGet();

            //默认taskStatus参数的值为1
            $taskType = I('get.taskType',1,'int');
            //默认type参数的值为1
            $type = I('get.type',1,'int');

            //默认分页参数type的值为1
            $page = I('get.page',1,'int');

        if($taskType < 1 || $type < 1 || $page < 1){

            $this->error(1030,'参数有误');

        }else{

            $return = D("AuditTask")->getAuditTaskList($taskType,$type,$page,$userId,$pageLen = 15);

            if($return){
                $this->success(1000,'获取数据成功',$return);
            }else{
                $this->error(1030,'暂无数据返回');
            }
        }
	}






}