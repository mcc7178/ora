<?php
namespace Mobile\Controller;

use Think\Controller;

/**
 * 活动报名控制器
 * @auth Andy 675283203@qq.com
 * 2017-09-25 09:20:22
 */
class ActivityApplyController extends CommonController{


   /***
    * @Param @所有活动列表页  登录者在公开范围
    * @Param @$type 0全部 1已报名
    * @Param @$token 用户唯一标识token
    * @Param $secret_key  通讯秘钥
    */
   public function activityList(){

       //判断用户是否存在,获取用户id,判断提交方式是否合法
       $userId = $this->verifyUserDataGet();
       //接受类型参数$type 0表示全部 1已报名
       $type = I('get.type',0,'int');
       //接收分页参数
       $page = I('get.page',1,'int');
       //接收搜索关键字解码(需要app端作编码处理)
       $keyword = I('get.keyword');
       $pageNum = 10;
      if($type != 0 && $type != 1){
         $this->error(1030,'类型参数有误');
      }
       $data = D("ActivityApply")->activityList($type,$userId,$page,$pageNum,$keyword);
       if($data['code'] == 1000){
           $this->success(1000,$data['message'],$data['data']);
       }else{
           $this->error(1030,$data['message']);
       }
   }


   /**
    * 活动 - 详情页
    * @Param @id 活动id
    * @Param @$token 用户唯一标识token
    * @Param $secret_key  通讯秘钥
    */
   public function activityDetails(){
       //判断用户是否存在,获取用户id,判断提交方式是否合法
       $userId = $this->verifyUserDataGet();
       $id = I('get.id',0,'int');
       if($id == '' || $id < 1){
           $this->error(1030,'活动id参数有误');
       }
       $data = D("ActivityApply")->activityDetails($id,$userId);
       if($data){
           $this->success(1000,$data['message'],$data['data']);
       }else{
           $this->error(1030,$data['message']);
       }
   }


   /***
    * 活动报名 - 立即报名
    * @Param $id 活动id
    * @Param $applyReason 申请理由
    * @Param @$token 用户唯一标识token
    * @Param $secret_key  通讯秘钥
    */
   public function activityRegistration(){
       //判断用户是否存在,获取用户id,判断提交方式是否合法
       $userId = $this->verifyUserDataPost();
       $id = I('post.id',0,'int');//接受的申请活动的id
       $apply_reason = I('post.applyReason'); //接受的申请理由
       if($id == '' || $id < 1){
           $this->error(1030,'活动id参数有误');
       }
       if($apply_reason == '' || strlen($apply_reason) > 50){
           $this->error(1030,'申请理由参数不能为空或超出50个字符的长度');
       }
     
       $data = D('ActivityApply')->activityRegistration($id,$userId,$apply_reason);

       if($data){
           $this->success(1000,$data['message'],$data['data']);
       }else{
           $this->error(1030,$data['message']);
       }
   }
}
?>