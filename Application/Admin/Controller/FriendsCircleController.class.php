<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;
/**
 * 工作圈控制器
 */
class FriendsCircleController extends AdminBaseController
{

    /*
     * 发布工作圈
     * $content
     */
    public function publicFriendsCircle()
    {   
        
        if(IS_POST){
         $res = D('FriendsCircle')->add();

         $orderno_data = D('Trigger')->orderNumber(5);  //接口调用
        
         if($res){
            // $publish_time = M('friends_circle')->where(array('id'=>$res))->getField('publish_time');
            if(strtolower(C('DB_TYPE')) == 'oracle'){
             $arr = M('friends_circle')->where(array('id'=>$res))->field("to_char(publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time")->find();
            }else{
             $arr = M('friends_circle')->where(array('id'=>$res))->field("publish_time")->find();
            }
            $publish_time = $arr['publish_time'];

            $data = array(
                'id'=>$res,
                'status'=>1,
                'user_id'=>$_SESSION['user']['id'],
                'username'=>$_SESSION['user']['username'],
                'avatar'=>$_SESSION['user']['avatar'],
                'publish_time'=>$publish_time
            ); 
            //调用公共Model里的方法,发布工作圈状态 积分触发
             @D('Trigger')->intergrationTrigger($_SESSION['user']['id'],14);           
         }else{
            $data = array(
                'status'=>0,
                'username'=>$_SESSION['user']['username']
            );
         }
         
         //判断是否需要审核
         if($orderno_data['status'] == 0){  //$orderno_data['status'] == 0 不需审核
              $data['info'] =  '话题发布成功!';
              $data['audit_flag'] =  0;
         }else{
              $data['info'] =  '话题发布成功，请等待审核结果!';
              $data['audit_flag'] =  1;
         } 	
         
     
         $this->ajaxreturn($data);
        }


    }
	/**
    *话题/评论的 删除
    */
     public function delFriendsCircle()
	 {

       $res = D('FriendsCircle')->del();
       $this->ajaxreturn($res); 
     }

	/**
    *话题的 点赞
    */
     public function praiseFriendsCircle()
	 {

       $res = D('FriendsCircle')->praise();
       //调用公共Model里的方法,对他人学课的问题回复／评论 积分触发
       @D('Trigger')->intergrationTrigger($_SESSION['user']['id'],15);      
       $this->ajaxreturn($res); 
     }

    /*
     *回复评论-个人互动
     * 
     */
    public function replyFriendsCircle()
    { 
     if(IS_POST){
         $res = D('FriendsCircle')->add();
        
         if($res){
             //调用公共Model里的方法,对他人学课的问题回复／评论 积分触发
             @D('Trigger')->intergrationTrigger($_SESSION['user']['id'],15);      
             
             $this->ajaxreturn($res); //为真则返回增加数据的id
   
         }else{
             $this->ajaxreturn(false);
         }
         
        }



    }
    /*
     *工作圈列表-个人互动
     * list
     */
    public function friendsCircleList()
    {   
        // dump($_SESSION['user']);
        $data = D('FriendsCircle')->getlist();
        // dump($data['list']);
        $this->assign('mytagid',$_SESSION['user']['id']);
        $this->assign('mytagavatar',$_SESSION['user']['avatar']);
        $this->assign('list',$data['list']);
        $this->assign('page',$data['page']);
        $this->display('friendscirclelist');
    }





    /*
     *工作圈列表-管理员互动列表
     * list
     */
    public function manageList()
    {  
        $data = D('FriendsCircle')->getlist();
        $this->assign('mytagid',$_SESSION['user']['id']);
        $this->assign('mytagavatar',$_SESSION['user']['avatar']);
        $this->assign('list',$data['list']);
        $this->assign('page',$data['page']);
        $this->display('friendsmanage');
    }


    /*
     *工作圈列表-判断该话题是否已点赞
     * list
     */
    public function whetherPraise()
    {  
        //  $map = array('cid'=>$v['id'],'user_id'=>$mytagid);
		//  $num = M('friends_praise')->where($map)->getField('praise');
       return $list = D('FriendsCircle')->whetherPraise();
    }



}