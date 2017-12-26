<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;
/**
 * 活动管理
 */
class ActivityManageController extends AdminBaseController
{

   /***
    * 活动报名管理列表页
    * type 0已发布 1未发布 2待审核 3审核不通过 4报名截止
    */
   public function index(){
       $type = I('get.type');

      if($type!=0 && $type!=1 && $type!=2 && $type!=3 && $type!=4){
		   $type = 0;	
		}

        $return = D("ActivityManage")->index($type);
		
		$this->assign('type',$type); //输出type
		if($type == 0){
          $this->assign('keyword0',$return['keyword']);
          $this->assign('data0',$return['data']);
		  $this->assign('page0',$return['page']);
		}else if($type == 1){
          $this->assign('keyword1',$return['keyword']);
          $this->assign('data1',$return['data']);
		  $this->assign('page1',$return['page']);
		}else if($type == 2){
          $this->assign('keyword2',$return['keyword']);
          $this->assign('data2',$return['data']);
		  $this->assign('page2',$return['page']);
		}else if($type == 3){
          $this->assign('keyword3',$return['keyword']);
          $this->assign('data3',$return['data']);
		  $this->assign('page3',$return['page']);
		}else if($type == 4){
          $this->assign('keyword4',$return['keyword']);
          $this->assign('data4',$return['data']);
		  $this->assign('page4',$return['page']);
		}

       $this->display('index');



   }


   /***
    * 新增/编辑 活动报名
    */
   public function add(){
       if(IS_POST){
          
          $res = D('ActivityManage')->adds();
          if($res){
              $this->success('保存成功！',U('Admin/ActivityManage/add')) ;

          }else{
              $this->error('保存失败！');
          }
       }else{
          $this->display();
       }
    
   }


   /***
    * 活动报名 详情查看
    */
   public function show(){
       $type = I('get.type');
       if($type!=0 && $type!=1){
		   $type = 0;	
		}
       $id = I('get.id') + 0;
       $return = D('ActivityManage')->show($id,$type);

       $this->assign('id',$id);
       $this->assign('type',$type); //输出type
       if($type == 0){
          $this->assign('data0',$return['data']);
		}else if($type == 1){
          $this->assign('keyword1',$return['keyword']);
          $this->assign('data1',$return['data']);
		  $this->assign('page1',$return['page']);
		  $this->assign('status',$return['status']);
		}
        
       $this->display('');

   }


   /***
    * 活动报名 编辑查看
    */
   public function edit(){
       
       $id = I('get.id') + 0;
       $return = D('ActivityManage')->edit($id);
       

       $this->assign('id',$id);
       $this->assign('data',$return['data']);
       $this->assign('department',$return['department']);
      
       $this->display('add');

   }



   /***
    * 发布报名 变更状态为$type=2
    */
   public function publish(){
       
       $id = I('get.id') + 0;

       $orderno_data = D('Trigger')->orderNumber(12);
       if($orderno_data['status'] == 0){
         $type = 0;//无需审核，状态改为0-已发布
       }else{
         $type = 2; //待审核
       } 

       $res = M('activity')->where(array('id'=>$id))->save(array('type'=>$type));
       if($res){
          $this->success('发布成功！');
       }else{
          $this->error('发布失败！');
       }

   }



   /***
    * 批量刪除 活动报名
    * update--bug6144 删除改为未发布/草稿
    */
   public function batchdelete(){

         $res = D('ActivityManage')->batchdelete();
         $this->ajaxreturn($res);

   }


   /***
    *  活动报名管理 -- 通过报名学员申请
    */
   public function auditPass(){

         $res = D('ActivityManage')->auditPass();
         $this->ajaxreturn($res);

   }


   /***
    * 活动报名管理 -- 拒绝报名学员申请
    */
   public function auditRefuse(){

         $res = D('ActivityManage')->auditRefuse();
         $this->ajaxreturn($res);

   }


   /**
    *导出考勤Excel
    */
     public function createExcel(){ 
         $res = D('ActivityManage')->createExcel();
         
     }












}