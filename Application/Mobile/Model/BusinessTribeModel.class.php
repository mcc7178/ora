<?php
/**
 * Created by PhpStorm.
 * User: lizhongjian
 * Date: 2017/8/4
 * Time: 17:49
 */

namespace Mobile\Model;

use Think\Model;

/**
 * 业务部落控制器模型
 * @auth-Andy-2017-08-04 17:12:30
 * Class BusinessTribe
 * @package Mobile\Model
 */

class BusinessTribeModel extends CommonModel{

    protected $tableName = "topic_group";
    /**
     * 获取部落列表
     */
    public function getBusinessTribeList($type,$page,$pageNum,$userId){
        //分页参数
        $start = ($page - 1) * $pageNum;
        //业务部落
        if ($type == 1){
            //小组列表状态   0待审核 1审核通过 2审核拒绝 3申请加入 4由我创建
            $list = M('topic_group')->where(array('status' => 1))->limit($start,$pageNum)->field('id,name,theme,status,user_id')->select();
            foreach($list as $k => $v){
                $list[$k]['theme'] = parent::filterTag($v['theme']);
                if($v['user_id'] == $userId){
                    $list[$k]['flag'] = '由我创建';
                    $list[$k]['status'] = 4;
                }else{
                    $exist = M('topic_personnel')->where(array('topic_group_id' => $v['id'], 'user_id' => $userId))->find();
                    if($exist){
                        if ($exist['status'] == 0) {
                            $list[$k]['flag'] = '审核中';
                            $list[$k]['status'] = 0;
                        } else if ($exist['status'] == 1) {
                            $list[$k]['flag'] = '已加入该部落';
                            $list[$k]['status'] = 1;
                        } else if ($exist['status'] == 2) {
                            $list[$k]['flag'] = '已拒绝';
                            $list[$k]['status'] = 2;
                        } else if ($exist['status'] == 3) {
                            $list[$k]['flag'] = '申请加入';
                            $list[$k]['status'] = 3;
                        }
                    }else{
                        $list[$k]['flag'] = '申请加入';
                        $list[$k]['status'] = 3;
                    }
                }
            }
            return $list;

        }elseif($type == 2){//我的部落
            //小组列表状态  1审核通过  4由我创建
            //我加入的小组
            $where["a.user_id"] = $userId;
            $where["a.status"] = 1;
            $where["b.status"] = 1;
            $myJoin = M("topic_personnel a")->field('b.id,b.name,b.theme,b.status,b.user_id')->join("join __TOPIC_GROUP__ b ON a.topic_group_id=b.id")->where($where)->limit($start,$pageNum)->select();

            foreach($myJoin as $k => $v){
                $myJoin[$k]['flag'] = '退出';
            }
            //我创建的小组 think_topic_group
            $_where["user_id"] = $userId;
            $_where["status"] = 1;
           // $myCreate = M("topic_group")->where($where2)->limit($start,$pageNum)->select();
            //$where['status'] = array('in','1,4');
            $myCreate = M('topic_group')->where($_where)->limit($start,$pageNum)->field('id,name,theme,status,user_id')->select();
            foreach($myCreate as $key => $val){
                $list[$key]['theme'] = parent::filterTag($val['theme']);
                $myCreate[$key]['status'] = 4;
                $myCreate[$key]['flag'] = '由我创建';
            }
            //合并结果集
            $list = array_merge_recursive($myJoin,$myCreate);
            return $list;
        }
    }


    /**
     * 申请加入小组
     */
    public function applyJoinTopicGroup(){

    }


    /**
     * @ 退出小组
     * @ $userId 用户id
     * @ $groupId 小组id
     */
    public function exitTopicGroup($userId,$groupId){
        $where = array(
            'user_id'=>$userId,
            'topic_group_id'=>$groupId,
        );
        $result = M("topic_personnel")->where($where)->save(array('status'=>3)); //更改为删除状态
        //触发小组系统消息
        @$res = D('Trigger')->sendTopicMessage($userId,'',$groupId,0,0,2);
        if($result && $res){
            return true;
        }else{
            return false;
        }
    }


    /**
     *判断是否为小组组长或创建者 是则返回1
     */
    public function getLeader($id){
        $login_id = $_SESSION['user']['id'];
        $group_id = M('group_topic')->where(array('id'=>$id))->getField('topic_group_id');
        //登录者是否为创建者
        $exist1 = M('topic_group')->where(array('id'=>$group_id,'user_id'=>$login_id))->find();
        //登录者是否为组长
        $exist2 = M('topic_personnel')->where(array('topic_group_id'=>$group_id,'user_id'=>$login_id,'group_leader'=>1))->find();
        if($exist1 || $exist2){
            $res = 1;
        }else{
            $res = 0;
        }
        return $res;
    }


    /**
     * @一个业务部落里的话题列表
     * @Param $businessTribeId 部落id
     * @Param $status 是否有加入部落 状态 0待审核 1审核通过 2审核拒绝 3申请加入 4由我创建
     * @Param $page 分页参数(第几页,默认第一页)
     * @Param $businessTribeId 部落id
     * @Param $userId 用户id
     */
    public function getTopicList($userId,$businessTribeId,$page,$pageNum){
            $start_page = ($page - 1) * $pageNum;
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "a.id,a.topic_group_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.praise,a.name,a.describe,a.orderno,a.status,a.topic_covers,b.username,b.avatar";
            }else{
                $field = "a.id,a.topic_group_id,a.user_id,a.publish_time,a.praise,a.name,a.describe,a.orderno,a.status,a.topic_covers,b.username,b.avatar";
            }
           //根据小组id获取该小组下的所有话题
           $list = M('group_topic')->alias('a')
            ->join('left join __USERS__ b on a.user_id=b.id')
            ->field($field)
            ->where(array('topic_group_id'=>$businessTribeId))
            ->limit($start_page,$pageNum)
            ->order('a.publish_time desc')
            ->select();

            foreach($list as $k=>$v){
                //根据每个话题id,获取对该话题的评论数
                $comment_num =  M('topic_interaction')->where(array('topic_id'=>$v['id'],'pid' => 0))->count();
                $comment_data =  M('topic_interaction')->field('id')->where(array('topic_id'=>$v['id'],'pid' => 0))->select();
                $list_data[$k]['id'] = $v['id'];
                $list_data[$k]['name'] = $v['name'];
                //根据每个话题id,获取对该话题的点赞数
                $praise_num =  M('topic_praise')->where(array('cid'=>$v['id'],'praise' => 1))->count();
                //是否为我点赞过的
                $is_praise =  M('topic_praise')->where(array('cid'=>$v['id'],'praise' => 1,'user_id' => $userId))->count();
                if($is_praise == 0){
                    $list_data[$k]['is_praise'] = 0; //0没有点赞过
                }else{
                    $list_data[$k]['is_praise'] = 1;//1为点赞过
                }
                $list_data[$k]['publish_time'] = $v['publish_time'];
                $list_data[$k]['comment_num'] = $comment_num ? $comment_num : 0;
              /*  foreach($comment_data as $i => $val){
                    //根据每个话题id,获取对该话题的点赞数
                    $praise_num =  M('topic_praise')->where(array('cid'=>$v['id'],'praise' => 1))->count();
                    //是否为我点赞过的
                    $is_praise =  M('topic_praise')->where(array('cid'=>$v['id'],'praise' => 1,'user_id' => $userId))->count();
                    if($is_praise == 0){
                        $list_data[$k]['is_praise'] = 0; //1为点赞过
                    }else{
                        $list_data[$k]['is_praise'] = 1;//0没有点赞过
                    }
                }*/
                $list_data[$k]['topic_covers'] = $v['topic_covers'] ? $v['topic_covers'] : '/Upload/20170918/59bf32cc4e746.png';
                $list_data[$k]['username'] = $v['username'];
                $list_data[$k]['avatar'] = $v['avatar'] ? $v['avatar'] : '/Upload/avatar/default.png';
                $list_data[$k]['praise_num'] = $praise_num ? $praise_num : 0;
                $list_data[$k]['describe'] = strip_tags($v['describe']);
            }

            //小组名称
            //$group_name = M('topic_group')->where(array('id'=>$businessTribeId))->getField('name');
            return $list_data;
        }


    /**
     * @业务部落里的二三级话题评论
     * @Param $topic_id 话题id
     */
    public function getTwoThreeTopicComment($topic_id,$userId,$page,$pageNum){
        $start_page = ($page - 1) * $pageNum;
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.topic_group_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.praise,a.name,a.describe,a.orderno,a.status,a.topic_covers,b.username,b.avatar";
        }else{
            $field = "a.id,a.topic_group_id,a.user_id,a.publish_time,a.praise,a.name,a.describe,a.orderno,a.status,a.topic_covers,b.username.b.avatar";
        }

        $list = M('group_topic')->alias('a')
            ->join('left join __USERS__ b on a.user_id=b.id')
            ->field($field)
            ->where(array('a.id'=>$topic_id))
            ->find();
        //根据话题id获取所有二级评论话题
        $second_where = array(
            'topic_id' => $list['id'],
            'pid' => 0
        );

        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id as second_id,a.topic_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.content,a.orderno,a.status,b.username,b.avatar";
        }else{
            $field = "a.id as second_id,a.topic_id,a.user_id,a.publish_time,a.orderno,a.status,b.username.b.avatar";
        }
        $second_data = M('topic_interaction a')
            ->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
            ->field($field)->where($second_where)->limit($start_page,$pageNum)->order('publish_time DESC')->select();

        foreach($second_data as $key => $value){
            $second_data[$key]['content'] = strip_tags(htmlspecialchars_decode($value['content']));
            //获取第二级下的所有评论
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "a.id,a.topic_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.content,a.images,a.orderno,a.status,b.username,b.avatar";
            }else{
                $field = "a.id,a.topic_id,a.user_id,a.publish_time,a.orderno,a.status,b.username.b.avatar";
            }
            $third_data = M('topic_interaction a')->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")->field($field)->where(array('topic_id' => $list['id'],'pid' => $value['second_id']))->select();

            //第二级点赞数
            $second_praise_num = M('topic_praise')->where(array('cid' => $value['second_id'],'praise' => 1,'type' => 0))->count();
            //是否为我点赞
            $is_praise = M('topic_praise')->where(array('cid' => $value['second_id'],'praise' => 1,'type' => 0,'user_id' => $userId))->find();
            if($is_praise){
                $second_data[$key]['is_praise'] = 1;
            }else{
                $second_data[$key]['is_praise'] = 0;
            }
            //第二级评论数
            $second_data[$key]['comment_num'] = count($third_data);
            $second_data[$key]['second_praise_num'] = $second_praise_num;
            $second_data[$key]['third'] = $third_data;
        }
        return $second_data;
    }


    /**
     * @业务部落里的一级话题评论
     * @Param $topic_id 话题id
     * @Param $userId 用户id
     */
    public function getOneTopicComment($topic_id,$userId){
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.topic_group_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.praise,a.name,a.describe,a.orderno,a.status,a.topic_covers,b.username,b.avatar";
        }else{
            $field = "a.id,a.topic_group_id,a.user_id,a.publish_time,a.praise,a.name,a.describe,a.orderno,a.status,a.topic_covers,b.username.b.avatar";
        }
        //根据小组id获取该小组下的所有话题

        /* if(strtolower(C('DB_TYPE')) == 'oracle'){
             $field = "b.username,b.avatar,a.id,a.topic_id,a.user_id,a.content,a.images,a.status,a.pid,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time";
         }else{
             $field = "b.username,b.avatar,a.id,a.topic_id,a.user_id,a.content,a.images,a.status,a.pid,a.publish_time";
         }*/

        $list = M('group_topic')->alias('a')
            ->join('left join __USERS__ b on a.user_id=b.id')
            ->field($field)
            ->where(array('a.id'=>$topic_id))
            ->find();

        //根据每个话题id,获取对该话题的评论数
        $comment_num =  M('topic_interaction')->where(array('topic_id'=>$list['id'],'pid' => 0))->count();
        $comment_data =  M('topic_interaction')->field('id')->where(array('topic_id'=>$list['id'],'pid' => 0))->select();
        $list_data['id'] = $list['id'];
        $list_data['name'] = $list['name'];
        $list_data['user_id'] = $list['user_id'];
        $list_data['username'] = $list['username'];
        $list_data['topic_covers'] = $list['topic_covers'] ? $list['topic_covers'] : '/Upload/20170918/59bf32cc4e746.png';
        $list_data['avatar'] = $list['avatar'];
        $list_data['publish_time'] = $list['publish_time'];
        //  $list_data['publish_time'] = $list['publish_time'];
        $list_data['comment_num'] = $comment_num ? $comment_num : 0;
            //根据每个话题id,获取对该话题的点赞数
            $praise_num =  M('topic_praise')->where(array('cid'=>$list['id'],'praise' => 1,'type' => 1))->count();
            //是否为我点赞过的
            $is_praise =  M('topic_praise')->where(array('cid'=>$list['id'],'praise' => 1,'user_id' => $userId,'type' => 1))->find();
            if($is_praise){
                $list_data['is_praise'] = 1; //1为点赞过
            }else{
                $list_data['is_praise'] = 0;//0没有点赞过
            }
        $list_data['praise_num'] = $praise_num ? $praise_num : 0;
        $list_data['describe'] = strip_tags($list['describe']);
        return $list_data;
    }


    /**
     * @业务部落里的一级话题评论
     * @Param $id 话题id
     * @Param $userId 用户id
     */
    public function topicOneComment($id,$userId,$comment_content){

        $data = array(
            'topic_id' => $id,
            'pid' => 0,
            'content' => $comment_content,
            'images' => '',
            'status' => 1,
            'audit_time' => 0,
            'orderno' => '',
            'publish_time' => date('Y-m-d H:i:s'),
            'user_id' => $userId,
        );
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('topic_interaction');
            $data['publish_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        $TopicModel = M('topic_interaction');
        $result = $TopicModel->add($data);
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id as second_id,a.topic_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.content,a.orderno,a.status,b.username,b.avatar";
        }else{
            $field = "a.id as second_id,a.topic_id,a.user_id,a.publish_time,a.orderno,a.status,b.username.b.avatar";
        }
        $res = M('topic_interaction a')->join("LEFT JOIN __USERS__ b ON b.id = a.user_id")->where(array('a.id' => $result))->field($field)->find();
        $res['is_praise'] = 0;
        $res['comment_num'] = 0;
        $res['second_praise_num'] = 0;
        $res['third'] = array();
        return $res;
    }


    /**
     * @业务部落里的二级话题评论
     * @Param $id 一级话题id
     * @Param $second_id 二级话题id
     * @Param $userId 用户id
     * @Param $comment_content 评论内容
     */
    public function topicTwoComment($id,$second_id,$userId,$comment_content){

        $data = array(
            'topic_id' => $id,
            'pid' => $second_id,
            'content' => $comment_content,
            'images' => '',
            'status' => 1,
            'audit_time' => 0,
            'orderno' => '',
            'publish_time' => date('Y-m-d H:i:s'),
            'user_id' => $userId,
        );
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('topic_interaction');
            $data['publish_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        $TopicModel = M('topic_interaction');
        $result = $TopicModel->add($data);
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.topic_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.content,a.images,a.orderno,a.status,b.username,b.avatar";
        }else{
            $field = "a.id,a.topic_id,a.user_id,a.publish_time,a.orderno,a.status,a.images,b.username.b.avatar";
        }
        $res = M('topic_interaction a')->join("LEFT JOIN __USERS__ b ON b.id = a.user_id")->where(array('a.id' => $result))->field($field)->find();
        return $res;
    }


    /**
     * 话题点赞，评论话题点赞
     * @Param $type 1话题点赞，否则评论话题点赞
     */
    public function topicPraise($type,$cid,$userId,$is_praise){
        if($type == 1){
                $map = array(
                    'cid' => $cid,
                    'user_id' => $userId,
                    'type' => 1
                );
                if($is_praise == 1){ //点赞操作
                    $ret = M('topic_praise')->where($map)->find(); //判断登陆者是否对该条话题进行点赞过
                    if(!$ret){//初次点赞
                        $data = array(
                            'cid' => $cid,
                            'praise' => 1,
                            'praise_time' => date('Y-m-d H:i:s'),
                            'user_id' => $userId,
                        );
                        if(strtolower(C('DB_TYPE')) == 'oracle'){

                            $data['id'] = getNextId('topic_praise');
                            $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                            $data['type'] = 1;
                        }
                        //$res= $this->hudongMessage($cid,'互动点赞',$userId); // $userId 是发布者
                        $result = M('topic_praise')->add($data);
                        if($result){
                            return array('code' => 1000,'message' => '点赞成功');
                        }else{
                            return array('code' => 1030,'message' => '点赞失败');
                        }
                    }else{ //如果是存在数据，则判断是否是取消点赞后再点赞还是连续重复二次点赞
                        //这里是取消点赞后再次点赞操作
                        if($ret['praise'] == 0){
                            $data_where = array(
                                'cid' => $cid,
                                'praise' => 0,
                                'user_id' => $userId,
                            );
                            $data = array(
                                'cid' => $cid,
                                'praise' => 1,
                                'praise_time' => date('Y-m-d H:i:s'),
                                'user_id' => $userId,
                            );
                            if(strtolower(C('DB_TYPE')) == 'oracle'){
                               // $data['id'] = getNextId('topic_praise');
                                $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                                $data['type'] = 1;
                            }
                            $result = M('topic_praise')->where($data_where)->save($data);
                            if($result){
                                return array('code' => 1000,'message' => '点赞成功');
                            }else{
                                return array('code' => 1030,'message' => '点赞失败');
                            }
                        }else{
                            return array('code' => 1031,'message' => '已经点赞过，不能重复操作了');
                        }

                    }
                }else if($is_praise == 0){ //取消点赞操作
                    $canCle_where = array(
                        'cid' => $cid,
                        'user_id' => $userId,
                        'type' => 1,
                    );
                    $praise = M('topic_praise')->where($canCle_where)->getField('praise');
                    if($praise == 0){
                        return array('code' => 1032,'message' => '已取消点赞，不能重复操作');
                    }else{
                        $res = M('topic_praise')->where($canCle_where)->setField('praise',0);
                        if($res){
                            return array('code' => 1000,'message' => '取消点赞成功');
                        }else{
                            return array('code' => 1032,'message' => '已取消点赞，不能重复操作');
                        }
                    }
                }
            }elseif($type == 0){
            $map = array(
                'cid' => $cid,
                'user_id' => $userId,
                'type' => 0
            );
            if($is_praise == 1){ //点赞操作
                $ret = M('topic_praise')->where($map)->find(); //判断登陆者是否对该条话题进行点赞过
                if(!$ret){//初次点赞
                    $data = array(
                        'cid' => $cid,
                        'praise' => 1,
                        'praise_time' => date('Y-m-d H:i:s'),
                        'user_id' => $userId,
                    );
                    if(strtolower(C('DB_TYPE')) == 'oracle'){

                        $data['id'] = getNextId('topic_praise');
                        $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                    }
                    $result = M('topic_praise')->add($data);
                    if($result){
                        return array('code' => 1000,'message' => '点赞成功');
                    }else{
                        return array('code' => 1030,'message' => '点赞失败');
                    }
                }else{ //如果是存在数据，则判断是否是取消点赞后再点赞还是连续重复二次点赞
                    //这里是取消点赞后再次点赞操作
                    if($ret['praise'] == 0){
                        $data_where = array(
                            'cid' => $cid,
                            'praise' => 0,
                            'user_id' => $userId,
                            'type' => 0
                        );
                        $data = array(
                            'cid' => $cid,
                            'praise' => 1,
                            'praise_time' => date('Y-m-d H:i:s'),
                            'user_id' => $userId,
                        );
                        if(strtolower(C('DB_TYPE')) == 'oracle'){
                            // $data['id'] = getNextId('topic_praise');
                            $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                        }
                        $result = M('topic_praise')->where($data_where)->save($data);
                        if($result){
                            return array('code' => 1000,'message' => '点赞成功');
                        }else{
                            return array('code' => 1030,'message' => '点赞失败');
                        }
                    }else{
                        return array('code' => 1031,'message' => '已经点赞过，不能重复操作了');
                    }

                }
            }else if($is_praise == 0){ //取消点赞操作
                $canCle_where = array(
                    'cid' => $cid,
                    'user_id' => $userId,
                    'type' => 0
                );
                $praise = M('topic_praise')->where($canCle_where)->getField('praise');
                if($praise == 0){
                    return array('code' => 1032,'message' => '已取消点赞，不能重复操作');
                }else{
                    $res = M('topic_praise')->where($canCle_where)->setField('praise',0);
                    if($res){
                        return array('code' => 1000,'message' => '取消点赞成功');
                    }else{
                        return array('code' => 1032,'message' => '已取消点赞，不能重复操作');
                    }
                }
            }
        }
    }


    /**
     * 业务部落与我相关的点赞和评论通知
     * @Param token 用户标识
     * @Param secret_key 秘钥
     */
    public function getPraiseCommentNotice($userId,$page,$pageNum){

        $start_page = ($page - 1) * $pageNum;
        if (strtolower(C('DB_TYPE')) == 'oracle') {
            $field_one = "a.id,a.topic_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.content,a.orderno,a.status,a.pid,b.username,b.avatar,c.name";
        } else {
            $field_one = "a.id,a.topic_id,a.user_id,a.content,a.orderno,a.publish_time,a.status,a.pid,b.username,b.avatar,c.name";
        }
        if (strtolower(C('DB_TYPE')) == 'oracle') {
            $field = "a.id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.name,b.username,b.avatar";
        } else {
            $field = "a.id,a.user_id,a.publish_time,a.status,a.name,b.username,b.avatar";
        }
        //查询我发布的所有话题
        $comment_data = M('group_topic a')
            ->join("LEFT JOIN __USERS__ b　ON b.id = a.user_id ")
            ->field($field)
            ->where(array('a.user_id' => $userId))
            ->limit($start_page,$pageNum)
            ->order('a.publish_time DESC')
            ->select();

        $idArr = array();
        foreach($comment_data as $k=>$v){

            $idArr[] = $v['id'];
        }

        $ids = implode(',',$idArr);
        if($ids){
            $comment_all_data = M('topic_interaction a')
                ->join("LEFT JOIN __USERS__ b　ON b.id = a.user_id LEFT JOIN __GROUP_TOPIC__ c ON a.topic_id = c.id ")
                ->field($field_one)
                ->where(array('a.topic_id' => array('in',$ids)))
                ->select();

            //查出对我发布的所有评论的点赞
            $comment_data_prise_one = M('topic_interaction a')
                ->join("LEFT JOIN __USERS__ b　ON b.id = a.user_id LEFT JOIN __GROUP_TOPIC__ c ON a.topic_id = c.id LEFT JOIN __TOPIC_PRAISE__ d ON c.id = d.cid AND d.praise = 1 AND d.type = 0")
                ->field($field_one)
                ->where(array('a.topic_id' => array('in',$ids),'d.type' => 0))
                ->limit($start_page,$pageNum)
                ->order('a.publish_time DESC')
                ->select();

            //获取对我发布的话题的所有回复和对我评论过的话题的回复
            $comment_info = M('topic_interaction')
                ->field('*')
                ->where(array('user_id' => $userId))
                ->select();
        }

        $idArr = array();
        foreach($comment_info as $k=>$v){
            $idArr[] = $v['id'];
        }
        $ids = implode(',',$idArr);
        if($ids){
            $comment_all_datas = M('topic_interaction a')
                ->join("LEFT JOIN __USERS__ b　ON b.id = a.user_id LEFT JOIN __GROUP_TOPIC__ c ON a.topic_id = c.id ")
                ->field($field_one)
                ->where(array('a.pid' => array('in',$ids)))
                ->select();


            //查出对我评论的点赞
            $comment_data_prise_two = M('topic_interaction a')
                ->join("LEFT JOIN __USERS__ b　ON b.id = a.user_id LEFT JOIN __GROUP_TOPIC__ c ON a.topic_id = c.id LEFT JOIN __TOPIC_PRAISE__ d ON c.id = d.cid AND d.praise = 1 AND d.type = 0")
                ->field($field_one)
                ->where(array('a.pid' => array('in',$ids),'d.type' => 1))
                ->select();
        }

        $list_common_data = array_merge($comment_all_data,$comment_all_datas);
        $list_common_data_praise = array_merge($comment_data_prise_one,$comment_data_prise_two);

        foreach($list_common_data as $j => $y){
            $list_common_data[$j]['style'] = 0;//评论
            $content = htmlspecialchars_decode($y['content']);
            $list_common_data[$j]['content'] = strip_tags($content);//评论
        }

        foreach($list_common_data_praise as $h => $g){
            $list_common_data_praise[$h]['style'] = 1;//点赞
            $content = htmlspecialchars_decode($g['content']);
            $list_common_data_praise[$h]['content'] = strip_tags($content);//点赞
        }
        $common_all_info = array_merge($list_common_data,$list_common_data_praise);
        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'publish_time',       //排序字段
        );
        $arrayData = parent::array_sort($common_all_info,$sort,$start_page,$pageNum=999999);
       return $arrayData;
    }


    /*******************************同步ORACLE*********************************/
    /**
     * 获取子评论pid
     */
    public function getTopicCommentChild($cid, $cidStr){
        $cat = M("topic_interaction")->where("pid=".$cid)->select();
       // dump($cat);exit;
        $id_array = '';
        if($cat){
            foreach ($cat as $key=>$v){
                 $id_array .= $v["id"] . ",";
               // $cidStr = self::getTopicCommentChild($v["id"], $cidStr);
            }
        }
        return substr($id_array,0,-1);
    }
}










