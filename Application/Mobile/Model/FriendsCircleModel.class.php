<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2017/3/4
 * Time: 13:39
 */

namespace Mobile\Model;

use Think\Model;
     /**
      * 工作圈模型
      */
class FriendsCircleModel extends CommonModel
{
    //protected $tablePrefix = 'think_';
    //protected $tableName = 'designated_personnel';

/************************************************兼容oracle*****************************************************/
    /**
     * 工作圈列表
     * @param $get
     * @param $userId
     * @return array
     */

    public function friendsCircleList($get,$userId){
        //$user_id 对应如果有用户id为$user_id的就显示删除功能
        $start = ($get['page'] - 1)*$get['pageNum'];
        $end = $get['pageNum'];
        //按照中心进行数据隔离
        $auth_center_id = $this->getCenterId($userId);
        $condition = array();
        $condition['status'] = 1;
        $condition['pid'] = 0;
        $condition['auth_center_id'] = $auth_center_id;
       // $condition['content'] = array('NEQ','');
        if(strtolower(C('DB_TYPE')) == 'oracle') {
            $field = "id,user_id,content,images,status,pid,to_char(publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time";
        }else{
            $field = "*";
        }

        $comList = M("FriendsCircle")->field($field)->where($condition)->order("publish_time DESC")->limit($start, $end)->select();
        //获取子评论/回复
        foreach ($comList as $key=>$value){
            $user = M("Users")->field("username,avatar")->where(array('id' => $value["user_id"]))->select();
            $comList[$key]["username"] = $user[0]["username"];
            $comList[$key]["avatar"] = $user[0]["avatar"];
            $comList[$key]['content'] = str_replace('&nbsp;','',strip_tags($value['content']));;
            $zan = M("Friends_praise")->where("praise=1 AND cid=".$value["id"])->count();
            $comList[$key]["praise_total"] = $zan;//总赞
            $zanStatus = M("Friends_praise")->where("cid=".$value["id"]." AND user_id=".$userId)->find();
            if($zanStatus['praise'] == 0){
                ////我是否赞过  1已赞 0未赞
                $comList[$key]["is_praise"] = 0;
            }else{
                $comList[$key]["is_praise"] = 1;
            }

            //是否可删除
            $comList[$key]["is_myFriends"] = 0;
            if($userId == $value["user_id"]){
                $comList[$key]["is_myFriends"] = 1;
            }
            $pids = self::getFriendCommentChild($value["id"], $value["id"].",");
            $pids = substr($pids, 0, -1);

            if($pids){
                $sonCon = M("FriendsCircle")->where("pid in (".$pids.")")->select();

                if($sonCon){
                    $userCache = array();
                    foreach ($sonCon as $sk=>$sv){

                        $subUser = M("users")->field("id,username,avatar")->where("id=".$sv["user_id"])->limit(1)->select();
                        $sonCon[$sk]["username"] = $subUser[0]["username"];
                        $sonCon[$sk]["avatar"] = $subUser[0]["avatar"];
                        $userCache[$sv["id"]] = $subUser[0]["username"];;
                        $sonCon[$key]['content'] = str_replace('&nbsp;','',strip_tags($sv['content']));
                        //是否可是我的工作圈显示删除按钮
                        $sonCon[$sk]["is_myFriends"] = 0;
                        if($userId == $sv["user_id"]){
                            $sonCon[$sk]["is_myFriends"] = 1;
                        }
                        if($sv["pid"] != $value["id"]){
                            $sonCon[$sk]["reply_user"] = $userCache[$sv["pid"]];
                        }
                    }
                   // $comList[$key]["child"] = $sonCon;
                }
                //统计所有子评论条数
                $comList[$key]["sum"] = count($sonCon);
            }
        }
        if($comList){
            return $this->success(1000,'获取数据成功',$comList);
        }else{
            return $this->error(1030,'赞无数据返回');
        }
    }




/*******************************同步ORACLE*********************************/
    /**
     * 获取子评论pid
     */
    public function getFriendCommentChild($cid, $cidStr){
        $cat = M("FriendsCircle")->where("pid=".$cid)->select();
        if($cat){
            foreach ($cat as $key=>$v){
                $cidStr .= $v["id"] . ",";
                $cidStr = self::getFriendCommentChild($v["id"], $cidStr);
            }
        }
        return $cidStr;
    }

    /***********************************************兼容oracle*****************************************************/
    /**
     * 工作圈列表详情(获取子评论列表)
     * @Param $get
     * @Param $userId
     * @return array
     */
    public function getChildComment($id,$type,$userId){

         if($id === ''){
             return $this->error(1023,'缺少回复评论id');
         }
         if(strtolower(C('DB_TYPE')) == 'oracle'){
             $field = "id,user_id,to_char(content) as content,images,to_char(publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,status,pid";
         }else{
             $field = "*";
         }
        if($type == 1){
            //主评论
            //点击工作圈列表评论，传主评论id
            $data = M("FriendsCircle")->field($field)->where(array("id" => $id))->find();
            //获取发布评论人的头像和用户名
            $user = M("Users")->field("username,avatar")->where(array('id' => $data["user_id"]))->find();
            $comList["id"] = $data["id"];
            $comList["user_id"] = $data["user_id"];
            $comList["content"] = str_replace('&nbsp;','',strip_tags($data["content"]));
            $comList["images"] = $data["images"];
            $comList["publish_time"] = $data["publish_time"];
            $comList["status"] = $data["status"];
            $comList["pid"] = $data["pid"];
            $comList["username"] = $user["username"];
            $comList["avatar"] = $user["avatar"];
            $zan = M("Friends_praise")->where("praise=1 AND cid=".$data["id"])->count();
            $comList["praise_total"] = $zan;//总赞
            $zanStatus = M("Friends_praise")->where("cid=".$data["id"]." AND user_id=".$data["user_id"])->limit(1)->select();
            if(!$zanStatus){
                $zanStatus = 0;
            }else{
                $zanStatus = 1;
            }
            $comList["is_praise"] = $zanStatus;//我是否赞过  1已赞 0未赞

            //是否可删除
            $comList["is_myFriends"] = 0;
            if($userId == $data["user_id"]){
                $comList["is_myFriends"] = 1;
            }
            $pids = self::getFriendCommentChild($data["id"], $data["id"].",");
            $pids = substr($pids, 0, -1);

        }else{
            //接收子评论id，递归查找父级id
            $data = M("FriendsCircle")->where(array("id" => $id))->find();
            //根据子类id查询第一级id
            $infoId = $this->findParentId($data['pid']);
            $info = M("FriendsCircle")->field($field)->where(array("id" => $infoId))->find();

            if($info == null){
                return $this->error(1023,'评论id参数有误');
            }
            //获取子评论/回复
            //获取该子评论相对应的主评论人的头像和用户名
            $user = M("Users")->field("username,avatar")->where(array('id' => $info["user_id"]))->limit(1)->find();
            $comList["id"] = $info["id"];
            $comList["user_id"] = $info["user_id"];
            $comList["content"] = str_replace('&nbsp;','',strip_tags($info["content"]));;
            $comList["images"] = $info["images"];
            $comList["publish_time"] = $info["publish_time"];
            $comList["status"] = $info["status"];
            $comList["pid"] = $info["pid"];
            $comList["username"] = $user["username"];
            $comList["avatar"] = $user["avatar"];
            $zan = M("Friends_praise")->where("praise=1 AND cid=".$info["id"])->count();
            $comList["praise_total"] = $zan;//总赞

            $zanStatus = M("Friends_praise")->where("cid=".$info["id"]." AND user_id=".$info["user_id"])->limit(1)->select();
            if(!$zanStatus){
                $zanStatus = "0";
            }else{
                $zanStatus = $zanStatus["praise"] + 0;
            }
            $comList["is_praise"] = $zanStatus;//我是否赞过  1已赞 0未赞

            //是否可删除
            $comList["is_myFriends"] = 0;
            if($userId == $info["user_id"]){
                $comList["is_myFriends"] = 1;
            }

            $subList = array();
            $pids = self::getFriendCommentChild($info["id"], $info["id"].",");
            $pids = substr($pids, 0, -1);
        }
            if($pids){
                $sonCon = M("FriendsCircle")->field($field)->where("pid in (".$pids.")")->order('publish_time DESC')->select();

                if($sonCon){
                    $userCache = array();
                    foreach ($sonCon as $sk=>$sv){
                      //  dump($sv);exit;
                        //上一条评论的用户的头像和用户名
                        $subUser = M("users")->field("username,avatar")->where("id=".$sv["user_id"])->find();
                        $_subUser = M("users")->field("username")->where(array("id" => $userId))->find();
                        //获取子评论上一条评论的用户名
                        $sonCon[$sk]["content"] = str_replace('&nbsp;','',strip_tags($sv["content"]));
                        if($subUser["username"] == $_subUser['username']){
                            $sonCon[$sk]["username"] = '我';
                        }else{
                            $sonCon[$sk]["username"] = $subUser["username"];
                        }
                        $sonCon[$sk]["avatar"] = $subUser["avatar"];
                        $userCache[$sv["id"]] = $subUser["username"];
                        //是否可是我的工作圈显示删除按钮
                        $sonCon[$sk]["is_myFriends"] = 0;
                        if($userId == $sv["user_id"]){
                            $sonCon[$sk]["is_myFriends"] = 1;
                        }
                        //添加回复谁的名字
                        if($sv["pid"] !=  $comList["id"]) {
                            $reply_userId = M("FriendsCircle")->field('user_id')->where(array('id' => $sv['pid']))->find();
                            $reply_username = M("Users")->field('username')->where(array('id' => $reply_userId['user_id']))->find();
                            $sonCon[$sk]["reply_user"] = $reply_username["username"];
                        }else{
                            $sonCon[$sk]["reply_user"] = '';
                        }
                       /* if($sv["pid"] != $info["id"]){
                        if($userCache[$sv["pid"]] == $_subUser['username']){
                            $sonCon[$sk]["reply_user"] = '我';
                        }else{
                            $sonCon[$sk]["reply_user"] = $subUser["username"];;
                        }

                        }*/
                        if($sonCon){
                            $comList["child"] = $sonCon;
                        }else{
                            $comList["child"] = array();
                        }

                    }

                }
                //统计所有子评论条数
                $comList["sum"] = count($sonCon);
            }
        if($comList){
            return $this->success(1000,'获取数据成功',$comList);
        }else{
            return $this->error(1030,'赞无数据返回');
        }
    }

    /*********************************************兼容oracle***********************************************/
    /**
     * 删除评价
     * comment_id 评价id
     */
    public function delComment($get,$userId){

        if($get['id'] == ''){
            return $this->error(1030,'评论id不能为空');
        }
        $resp = M("FriendsCircle")->where(array("id" => $get['id']))->delete();
        $result = M("Friends_praise")->where(array("cid" => $get['id'],'user_id'=>$userId))->delete();
        if($resp && $result) {
            //删除关联的回复评论
            $pids = self::getFriendCommentChild($get['id'], $get['id'] . ",");
            $pidp = substr($pids, 0, -1);
            if ($pidp) {
                $where['pid'] = array('in',$pidp);
                $res = M("FriendsCircle")->where($where)->delete();
                return $res;
            }
        }
    }

/*********************************************兼容oracle***********************************************/
    /**
     * 发布工作圈输入数据合法性校验,并作插入操作
     * @Param $post 输入数据集
     * @Param $user_id 用户id
     */
    public function checkData($content,$image,$userId){
        $auth_center_id = $this->getCenterId($userId);
        if($content == ''){
            return $this->error(1030,'不能发布空内容');
        }else{
            $data['content'] = $content;
            $data['images'] = $image ? $image : '';
            $data['user_id'] = $userId;
            $data['pid'] = 0;//发布评论pid为0
            $data['status'] = 0;//发布评论为待审核状态
            $data['auth_center_id'] = $auth_center_id;

            $orderno_data = D('Trigger')->orderNumber(5,$userId);
            
            $orderno = $orderno_data['no'];  //赋值工单号
            
            $data['orderno'] =  $orderno;
            if($orderno_data['status'] == 0){

                $data['status'] = 1;   //判断是否需要审核
            }

            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $data['id'] = getNextId('friends_circle');
                $data['publish_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            }else{
                $data['publish_time'] = date('Y-m-d H:i:s',time());
            }
            $res = $this->add($data);
            // $res['user_id'] = $userId;
            // $resp = self::getTaskData($res,5); //审核数据
            if($res){
                return $this->success(1000,'操作成功',$res);
            }else{
                return $this->error(1030,'操作失败');
            }
        }
    }

    /**
    *互动-消息触发
    */
     public function hudongMessage($cid,$title,$user_id)
	 {

        $from_id = $user_id; //消息互动发送者
        $user_id = M('friends_circle')->where(array('id'=>$cid))->getField('user_id'); //消息互动接收者

        $contents_time = date('Y-m-d H:i:s');
        $type_id = 14;
        
        $url = 'Admin/FriendsCircle/friendsCircleList#c'.$cid;
        
        D('Trigger')->messageTrigger($user_id,$title,$contents_time,$type_id,$from_id,$url);
     }

    /**
     * 查找祖先(给个id,找出他上面的父亲)
     * $id 就是被评论或被回复的互动id
     */

    public function findParent($id){
        
        static $parent=array();
        
        $arr = M('friends_circle')->where(array('id'=>array('gt',0)))->select();
        foreach($arr as $v){
            //从小到大 排列
            if($v["id"]==$id){
                $parent[]=$v;
                
                if($v["pid"] > 0){
                    $this->findParent($v["pid"]);
                }
            }
        }
        
        return $parent;
    }





    /**
     * 获取树状结构数据
     */
    public function gettree($arr,$id,$name,$childName,$parentName){
        static $list = array();
        $list = array();
        static $num = 0;
        foreach($arr as $v) {
            if($v[$childName] == $id) {
                $num = 0;
                $this->gettree($arr,$v[$parentName],$name,$childName,$parentName);
                if(empty($name)){
                    $list[] = $v;
                }else{
                    $list[] = $v[$name];
                }
            }
        }
        return $list;
    }


    /**
     *获取登录者所在顶级组织的二级组织
     */
    public function getCenterId($userId){

        //获取登录者当前所在组织id
        $login_id = $userId;
        $tissue = M('tissue_group_access')->where(array('user_id'=>$login_id))->find();
        $tissue_id = $tissue['tissue_id'];
        $arr = M('tissue_rule')->select();
        $data = $this->gettree($arr,$tissue_id,'','id','pid');
        return $data[1]['id'];

    }

    /****************************************************************兼容oracle*********************************/
    /**
     * 评论和回复评论
     */
    public function addComment($post,$userId){
        //回复一级评论，需要参数：一级评论id
        if(isset($post['id']) && !empty($post['id'])){
            $data['pid'] = $post['id'];
            $data['user_id'] = $userId;
            $data['content'] = $post['content'];
            //获取当前用户所在中心
            $auth_center_id = $this->getCenterId($userId);
            $data['auth_center_id'] = $auth_center_id;
            if(empty($data['content'])){
                return $this->error(1030,'请输入内容');
            }
            $data['status'] = 1;//子评论状态为1，即已通过审核的
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $data['id'] = getNextId('friends_circle');
                $data['publish_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            }else{
                $data['publish_time'] = date('Y-m-d H:i:s',time());
            }

            @$this->hudongMessage($data['pid'],'互动评论',$userId); // $userId 是发布者
            $id = $this->data($data)->add();
            if($id){
                if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $field = "id,user_id,to_char(content) as content,pid,to_char(publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time";
                }else{
                    $field = "id,user_id,content,publish_time,pid";
                }
                $info = $this->field($field)->where(array('id'=>$id))->find();

                return $this->success(1000,'操作成功',$info);
            }else{
                return $this->error(1030,'操作失败');
            }
        }else{
            return $this->error(1023,'缺少必要参数');
        }
    }


    /**
     *工作圈-点赞
     * $user_id 点赞者id
     * $content_id 课程content_id
     *
     */
    public function doPraise($get,$user_id){
        $friendsPraise = M('FriendsPraise');
        $data['user_id'] = $user_id;
        $data['cid'] = $get['content_id'];
        if(empty($data['cid'])){
            return $this->error(1030,'参数有误');
        }
        settype($get['type'], "string");
        if($get['type'] == '0' || $get['type'] == '1'){
            if($get['type'] == 0){//取消点赞
                $praise = $friendsPraise->where($data)->getField('praise');
                if($praise == 0){
                    return $this->error(1030,'不能重复操作');
                }else{
                    $data['praise'] = 0;
                    if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                    }else{
                        $data['praise_time'] = date('Y-m-d H:i:s',time());
                    }
                    $friendsPraise->where(array('user_id'=>$user_id,'cid'=>$data['cid']))->save($data);
                    $praise_total = $friendsPraise->where(array('cid'=>$data['cid']))->sum('praise');
                    $data['praise_total'] = $praise_total;
                    return $this->success(1000,'操作成功',$data);
                }
            }else{//点赞
                $res = $friendsPraise->field('praise')->where($data)->find();
                if($res['praise'] == null){
                    $data['praise'] = 1;
                    if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $data['id'] = getNextId('friends_praise');
                        $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                    }else{
                        $data['praise_time'] = date('Y-m-d H:i:s',time());
                    }
                    $friendsPraise->data($data)->add();
                    //统计点赞总数
                    $praise_total = $friendsPraise->where(array('cid'=>$data['cid']))->sum('praise');
                    $data['praise_total'] = $praise_total;
                    return $this->success(1000,'操作成功',$data);
                }elseif($res['praise'] == 0){
                    $data['praise'] = 1;
                    if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                    }else{
                        $data['praise_time'] = date('Y-m-d H:i:s',time());
                    }
                    $friendsPraise->where(array('user_id'=>$user_id,'cid'=>$data['cid']))->save($data);
                    //统计点赞总数
                    $praise_total = $friendsPraise->where(array('cid'=>$data['cid']))->sum('praise');
                    $data['praise_total'] = $praise_total;
                    $data['praise_time'] = date('Y-m-d H:i:s',time());
                    return $this->success(1000,'操作成功',$data);
                }else{
                    return $this->error(1030,'不能重复操作');
                }
            }
        }else{
            return $this->error(1030,'参数有误');
        }
    }


    /**
     * 工作圈评论和点赞消息
     */
    public function getPraiseCommentNotice($userId,$start_page,$pageLen){
         //按照中心进行数据隔离
        $auth_center_id = $this->getCenterId($userId);
        if(strtolower(C('DB_TYPE')) == 'oracle') {
            $field = "a.id,a.user_id,a.content,a.images,a.status,a.pid,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,b.username,b.avatar";
        }else{
            $field = "a.id,a.user_id,a.content,a.images,a.status,a.pid,a.publish_time,b.username,b.avatar";
        }
       $result =  M('FriendsCircle a')-> join("LEFT JOIN __USERS__ b ON a.user_id = b.id ")->where(array('a.auth_center_id' => $auth_center_id))->field($field)->select();
       // static $data = array();
        $data = array();
        foreach($result as $key => $val){
            //查询谁对我发布的评论做了回复
            $res =  M('FriendsCircle a')-> join("LEFT JOIN __USERS__ b ON a.user_id = b.id ")->where(array('a.pid' => $val['id']))->field($field)->select();
            if(!empty($res)){
                if(count($res) > 1){//判断如果大于一个元素就循环，把循环出来的一个数组array_push到新数组中
                    foreach($res as $k => $v){
                        $v['images'] = $val['images'];
                        $v['contents'] = str_replace('&nbsp;','',strip_tags($val['content']));
                        array_push($data,$v);
                    }
                }else{//只有一个元素则直接放入新数组中
                    $res[0]['images'] = $v['images'];
                    $res[0]['contents'] = str_replace('&nbsp;','',strip_tags($v['content']));
                    array_push($data,$res[0]);
                }
            }
        }
        $_data = array();
        if(strtolower(C('DB_TYPE')) == 'oracle') {
            $fields = ",to_char(c.praise_time,'YYYY-MM-DD HH24:MI:SS') as praise_time";
        }else{
            $fields = ",a.praise_time";
        }
        foreach($result as $_key => $_val){
            //查询谁对我发布的评论点赞了
            $_res = M('FriendsCircle a')
            	->join("LEFT JOIN __USERS__ b ON a.user_id = b.id LEFT JOIN __FRIENDS_PRAISE__ c ON a.id = c.cid")
            	->where(array('a.id' => $_val['id'],'c.praise' => 1))
            	->field($field.$fields)
            	->select();
            if(!empty($_res)){
                if(count($_res) > 1){
                    foreach($_res as $_k => $_v){
                        $_v['images'] = $_val['images'];
                        $_v['contents'] = str_replace('&nbsp;','',strip_tags($_val['content']));
                        array_push($_data,$_v);
                    }
                }else{
                    $_res[0]['images'] = $_val['images'];
                    $_res[0]['contents'] = str_replace('&nbsp;','',strip_tags($_val['content']));
                    array_push($_data,$_res[0]);
                }
            }
        }

        foreach($data as $j => $y){
            $data[$j]['style'] = 0;//评论
            $content = htmlspecialchars_decode($y['contents']);
            $data[$j]['contents'] = str_replace('&nbsp;','',strip_tags($content));//评论内容
            if(!strpos($y['images'],',')){//无逗号，说明只有一张图片
                $data[$j]['images'] = $y['images'];
             }else{//有逗号则去第一张图片
                $data[$j]['images'] = strstr($y["images"], ',', TRUE);
            }
        }

        foreach($_data as $h => $g){
            $_data[$h]['style'] = 1;//点赞
            $g['publish_time'] = $g['praise_time'];
            $_data[$h]['publish_time'] = $g['publish_time'];
            $contents = htmlspecialchars_decode($g['contents']);
            $_data[$h]['contents'] = str_replace('&nbsp;','',strip_tags($contents));//评论内容
            if(!strpos($g['images'],',')){//无逗号，说明只有一张图片
                $_data[$h]['images'] = $g['images'];
            }else{//有逗号则去第一张图片
                $_data[$h]['images'] = strstr($g["images"], ',', TRUE);
            }
        }
        $list_common = array_merge($data,$_data);
        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'publish_time',       //排序字段
        );

        $arrayData = parent::array_sort($list_common,$sort,$start_page,$pageLen);
        return $arrayData;
    }
}