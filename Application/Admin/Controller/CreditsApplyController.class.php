<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;
/**
 * 学分申请控制器
 */
class CreditsApplyController extends AdminBaseController
{

  	/**
     * 我的积分列表页的申请加分
     */
     public function apply()
	 {

        if(IS_POST){
        //  echo aa;    
         $orderno_data = D('Trigger')->orderNumber(8);
         $model = D('CreditsApply');
         $res = $model->isApply();
         if(!$res){
          $this->error($model->getError());
         }else{
          
          if($orderno_data['status'] == 0){
              $info = "申请成功";
          }else{
              $info = "申请成功,请等待审核结果！";
          }
          $this->success($info);
          
         }
        }else{
         $this->display('credits_apply');  
        }
        
     }
  	/**
     * 申请加分申请记录
     */
     public function applyhistory()
	 {
        $data = D('CreditsApply')->getApplyhistory();

        //    print_r($data);exit;
        $this->assign('list2',$data['list2']);
        $this->assign('page2',$data['page2']);
        $this->display('credits_apply');  
     }


}