<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/4
 * Time: 17:11
 */

namespace Mobile\Controller;
use Think\Controller;
/**
 * 业务部落控制器
 * @auth-Andy-2017-08-04 17:12:30
 * Class BusinessTribe
 * @package Mobile\Controller
 */
class BusinessTribeController extends CommonController{
    /**初始化**/
    public function __construct(){
        parent::__construct();
    }

    /**
     * @获取业务部落列表
     * @Param @type=1(所有人的部落列表)
     * @Param @type=2(我的部落列表)
     * @Param @$page 分页参数  默认第一页
     */
    public function getBusinessTribeList(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $type = I('get.type',1,'int');
        $page = I('get.page',1,'int');
        $pageNum = 10;
        if($type == 1 || $type == 2){
            if($page < 1){
                $this->error(1030,'分页参数有误');
            }
            $data = D('BusinessTribe')->getBusinessTribeList($type,$page,$pageNum,$userId);
            if($data){
                $this->success(1000,'获取成功',$data);
            }else{
                $this->error(1030,'无数据返回');
            }
        }else{
            $this->error(1030,'参数有误');
        }
    }

    /**
     * @申请加入小组(部落)
     * @Param  id 话题小组id
     *
     */
    public function applyJoinTopicGroup(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $topicGroupId = I('post.id','','int');
        if($topicGroupId < 1) {
            $this->error(1030, '部落id参数有误');
        }
        $insertData = array(
                'topic_group_id'=>$topicGroupId,
                'user_id' => $userId,
                'apply_reason' => '',
                'status' => 0,
            );
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $insertData['id'] = getNextId('topic_personnel');
        }
        $map = array('topic_group_id'=>$topicGroupId,'user_id'=>$userId);
            $exist = M('topic_personnel')->where($map)->find();
            if($exist){
                $ret = M('topic_personnel')->where($map)->save(array('status'=>0));
            }else{
                $ret = M('topic_personnel')->add($insertData);
            }

            if(!$ret){
                $this->error(1030,'插入消息数据失败');
            }else{
                //向具体用户发送消息
                $dataOne = M('topic_group')->where(array('id'=>$topicGroupId))->find();
                $send_user_id = $dataOne['user_id'];
                $title = $dataOne['name'];
                $contents_time = date('Y-m-d H:i:s');
                $type_id = 16;
                $from_id = $userId;
                $url = 'Admin/topic_group/index';
                $res = D('Trigger')->messageTrigger($send_user_id,$title,$contents_time,$type_id,$from_id,$url);
                if(!$res){
                    $this->error(1030,'插入消息数据失败');
                }else {
                    //关联话题小组消息
                    $messageData = array(
                        'message_id' => $res,
                        'type' => 1,
                        'correlate_id' => $topicGroupId
                    );
                    if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $messageData['id'] = getNextId('admin_message_topic');
                    }
                    $res = M('admin_message_topic')->add($messageData);
                    if (!$res) {
                        $this->error(1030, '插入消息数据失败');
                    }else{
                        $this->success(1000, '插入消息数据成功');
                    }
            }
        }
    }



    /**
     *
     * 退出小组
     * @ $id   小组id
     * @ $userId 用户id
     */
    public function exitTopicGroup(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //接收小组id
        $groupId = I('post.id','','int');
        if($groupId < 1){
            $this->error(1030,'小组id参数有误');
        }
        $list = D('BusinessTribe')->exitTopicGroup($userId,$groupId);
        if($list == true){
            $this->success(1000,'退出成功');
        }else{
            $this->error(1030,'退出失败');
        }
    }


    /**
     * 业务部落里的话题列表(业务部落和我的部落)(有加入或者是自己创建的才能发布话题和发布评论回复)
     */
    public function getTopicList(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
         //接收部落id,获取部落下面的所有话题列表
        $businessTribeId = I('get.id','','int');
       // $status = I('get.status','','int');
        //接收分页参数
        $page = I('get.page',1,'int');
        $pageNum = 10;
        if($businessTribeId == '' && $businessTribeId < 1){
           $this->error(1030,'部落id参数有误');
        }

        if($page < 1){
            $this->error(1030,'分页参数有误');
        }
        $data = D('BusinessTribe')->getTopicList($userId,$businessTribeId,$page,$pageNum);
        if($data){
            $this->success(1000,'获取成功',$data);
        }else{
            $this->error(1030,'无数据返回');
        }
    }



   /**
    * @业务部落里的一级话题评论
    * @Param $id 话题id
    */
    public function getOneTopicComment(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        //接收话题的id
        $topic_id = I('get.id',0,'int');
        if($topic_id < 1 || $topic_id == ''){
            $this->error(1030,'话题id参数有误');
        }
        //判断登录者是否是组长或部落创建者，若是则具有删帖的权限
       // $whether_leader = D('TopicGroup')->getLeader($topic_id);
        $data = D('BusinessTribe')->getOneTopicComment($topic_id,$userId);
        if($data){
            $this->success(1000,'获取成功',$data);
        }else{
            $this->error(1030,'无数据返回');
        }
    }

    /**
     * @业务部落里的二三级话题评论
     * @Param $id 话题id
     */
    public function getTwoThreeTopicComment(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        //接收话题的id
        $topic_id = I('get.id',0,'int');
        //接收分页参数
        $page = I('get.page',1,'int');
        $pageNum = 10;
        if($topic_id < 1 || $topic_id == ''){
            $this->error(1030,'话题id参数有误');
        }
        if($page < 1){
            $this->error(1030,'分页参数有误');
        }
        //判断登录者是否是组长或部落创建者，若是则具有删帖的权限
        // $whether_leader = D('TopicGroup')->getLeader($topic_id);
        $data = D('BusinessTribe')->getTwoThreeTopicComment($topic_id,$userId,$page,$pageNum);
        if($data){
            $this->success(1000,'获取成功',$data);
        }else{
            $this->error(1030,'无数据返回');
        }
    }


    /**
     * 业务部落一级二级话题评论
     * @Param $type 0一级话题评论，1二级话题评论
     * @Param $id 一级话题评论，$second_id二级话题评论
     *
     */
    public function topicComment(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //接收评论话题类型参数,$type 0是评论一级话题，1是评论二级话题
        $type = I('post.type',0,'int');
        $comment_content = I('post.comment_content','','trim,strip_tags');//strip_tags
        if($comment_content == ''){
            $this->error(1030,'输入内容不能为空');
        }
        if(strlen($comment_content) > 100){
            $this->error(1030,'输入内容长度必须在100个字符以内');
        }
       if($type == 0){
           //评论一级话题的id
           $id = I('post.id',0,'int');
           if($id < 1 && $id == ''){
               $this->error(1030,'话题id参数有误');
           }
           $data = D('BusinessTribe')->topicOneComment($id,$userId,$comment_content);
           if($data){
               $this->success(1000,'操作成功',$data);
           }else{
               $this->error(1030,'操作失败');
           }
       }elseif($type == 1){
           //评论一级话题的id
           $id = I('post.id',0,'int');
           //评论二级话题的id
           $second_id = I('post.second_id',0,'int');
           if($id < 1 && $id == ''){
               $this->error(1030,'话题id参数有误');
           }
           if($second_id < 1 && $second_id == ''){
               $this->error(1030,'话题second_id参数有误');
           }
           $data = D('BusinessTribe')->topicTwoComment($id,$second_id,$userId,$comment_content);
           if($data){
               $this->success(1000,'操作成功',$data);
           }else{
               $this->error(1030,'操作失败');
           }
       }else{
            $this->error(1030,'type类型参数有误');
        }
    }

    /**
     * 业务部落话题点赞
     */
    public function topicPraise(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //接收点赞类型参数,$type 1是话题点赞
        $type = I('post.type','','int');
        //点赞话题的id或者是评论话的id
        $cid = I('post.id',0,'int');
        //1点赞 0取消点赞
        $is_praise = I('post.is_praise','','int');
        if($cid < 1 && $cid == ''){
            $this -> error(1030,'点赞话题id参数有误');
        }
        $data_topic = D('BusinessTribe')->topicPraise($type,$cid,$userId,$is_praise);
        if($data_topic['code'] == 1000){
            $this -> success($data_topic['code'],$data_topic['message']);
        }else{
            $this -> error($data_topic['code'],$data_topic['message']);
        }
    }


    /**
     * 业务部落与我相关的点赞和评论通知
     * @Param token 用户标识
     * @Param secret_key 秘钥
     */
    public function getPraiseCommentNotice(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        //接收分页参数
        $page = I('get.page',1,'int');
        $pageNum = 10;
        $data_topic = D('BusinessTribe')->getPraiseCommentNotice($userId,$page,$pageNum);
        if($data_topic){
            $this->success(1000,'获取成功',$data_topic);
        }else{
            $this->error(1030,'暂无数据返回');
        }
    }
}