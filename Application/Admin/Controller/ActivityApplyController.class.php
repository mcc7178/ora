<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;
/**
 * 活动报名
 */
class ActivityApplyController extends AdminBaseController
{


   /***
    * 所有活动列表页  登录者在公开范围
    * $type 0所有活动 1已报名活动
    */
   public function index(){
      $type = I('get.type') + 0;

      if($type!=0 && $type!=1){
		   $type = 0;	
		}

      $return = D("ActivityApply")->index($type);
		
		$this->assign('type',$type); //输出type
		if($type == 0){
          $this->assign('keyword0',$return['keyword']);
          $this->assign('data0',$return['data']);
		  $this->assign('page0',$return['page']);
		}else if($type == 1){
          $this->assign('keyword1',$return['keyword']);
          $this->assign('data1',$return['data']);
		  $this->assign('page1',$return['page']);
		}

       

       $this->display();

   }


   /***
    * 所有活动 - 详情页
    */
   public function show(){
      $id = I('get.id') + 0;
      $login_id = $_SESSION['user']['id'];
      $data = D("ActivityApply")->show($id,$login_id);
    //   dump($data);
      $this->assign('data',$data);

      $this->display();
   }


   /***
    * 活动报名 - 立即报名
    */
   public function apply(){

         $res = D('ActivityApply')->apply();
         $this->ajaxreturn($res);

   }

}