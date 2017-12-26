<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/15
 * Time: 11:24
 */

namespace Mobile\Model;
use Think\Model;
/**
 * @首页公开课
 * @author lizhongjian
 *
 */
class NewsModel extends CommonModel
{

    protected $tableName = 'News';//如果模型名称与数据表名称一致这个可以没有，否则该条件必须
    //定义验证规则
    protected $_validate = array(
        array('template', 'require', '资讯所属模板不能为空', Model::EXISTS_VALIDATE),
        array('template', array(1, 2), '资讯所属模板参数有误！', 2, 'in'), // 当值不为空的时候判断是否在一个范围内
        array('type', 'require', '资讯类型不能为空', Model::EXISTS_VALIDATE),
        array('type', array(1, 2, 3, 4), '资讯类型参数有误！', 2, 'in'), // 当值不为空的时候判断是否在一个范围内
        array('title', 'require', '资讯标题不能为空', Model::EXISTS_VALIDATE),
        array('title', '2,20', '标题长度为2-20个字符组成', Model::EXISTS_VALIDATE, 'length'),
        array('content', 'require', '资讯内容不能为空', Model::EXISTS_VALIDATE),
        array('content', '2,200', '内容长度为2-200个字符组成', Model::EXISTS_VALIDATE, 'length'),
    );


    /**
     * @获取资讯列表
     * @$type  资讯所属公司模板  1综合资讯  2公司资讯
     * @$page 分页参数（第几页）
     * @$total  分页参数（每页显示多少条）
     * @$userId   用户id
     */
    public function getNewsList($type, $page, $total, $userId){
        
        $News = M('News');
        //隔离数据过滤
        // $specifiedUser = D('IsolationData')->specifiedUser($userId);

        // $where['auth_user_id'] = array("in",$specifiedUser);
        if ($type <= 2) {//综合资讯
            
            
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "id,title,type,content,img,template,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time";
            }else{
                $field = "id,title,type,content,create_time,img,template";
            }
            // $result = $News->where($where)->field($field)->limit($page, $total)->order('create_time desc')->select();
            //查询当前所属组织
        $where['user_id'] = array("eq",$userId);

        $tissue_id = M("tissue_group_access")->where($where)->getField("tissue_id");

        //获取方案ID
        $plan_id = M("sys_tissue")->where("tissue_id=".$tissue_id)->getField("plan_id");
        //隔离数据过滤
        //根据方案取出所有组织会员ID
        $sys_tissue_list = M("sys_tissue")->field("tissue_id")->where("plan_id=".$plan_id)->select();

        $userid_all = array();

        foreach($sys_tissue_list as $list){

            $group_access = M("tissue_group_access")->field("user_id")->where("tissue_id=".$list['tissue_id'])->select();

            foreach($group_access as $access){
                $userid_all[] = $access['user_id'];
            }
        }
        $conditions['user_id'] = array("in",$userid_all);
        //获取共享数据
        //type为8表示资讯类型
        $wheres['a.type'] = 8;
        $wheres['b.user_id'] = $userId;
        $data_id = array();
        $data = M('resource_sharing')
            ->alias('a')
            ->join('left join __TISSUE_GROUP_ACCESS__ b on a.tissue_id=b.tissue_id')
            ->field('a.source_id')
            ->where($wheres)
            ->select();
            
        //遍历获取所有的资源id
        foreach($data as $v){
            $data_id[] = $v['source_id'];
        }

        if(!empty($data_id)){
            $conditions['id'] = array("in",$data_id);
            $conditions['_logic'] = 'or';
        }
		if ($type == 1){
            $condition=array(
            'template'=>1,
            '_complex'=>$conditions,
            '_logic'=>'and'
             );
        }else if($type == 2){
            $condition=array(
            'template'=>2,
            '_complex'=>$conditions,
            '_logic'=>'and'
             );
        }
            
        

        
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$result = M('news')
                    ->where($condition)
					->page($page, $total)
					->field($field)
					->order('id desc')
					->select();
            //  echo M('news')->_sql(); die;
		}else{
			$result = M('news')->where($condition)->where($condition)->field($field)->page($page, $total)->order('id desc')->select();
		}

//  dump($result);die;
            foreach($result as $key=>$value){
                if(empty($value['img'])){
                    $value['img'] = '/Upload/news/default/colligate_news.png';
                }
                $result[$key]['img'] =$value['img'];
                $str = htmlspecialchars_decode($value['content']);
                $result[$key]['content'] = parent::filterTag($str);
                //资讯类型 1要闻 2培训 3通知 4活动
                switch ($value['type']) {
                    case 1;
                        $result[$key]['type'] = '【要闻】';
                        break;

                    case 2;
                        $result[$key]['type'] = '【培训】';
                        break;

                    case 3;
                        $result[$key]['type'] = '【通知】';
                        break;

                    case 4;
                        $result[$key]['type'] = '【活动】';
                        break;
                }
            }
            if(!$result){
                return $this->error(1030, '暂无数据返回');
            }else{
                return $this->success(1000, '获取成功',$result);
            }

        } elseif ($type == 1) {//公司资讯

            //获取登录者当前所在的组织id
            $tissue_id = $this->getTissueId($userId);

            //列表数据取自己公司资讯和全部综合资讯
            $where = array(
                'tissue_id' => $tissue_id,
                'template' => 1,
                "_logic" => "AND"
            );
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "id,title,type,content,img,template,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time";
            }else{
                $field = "id,title,type,content,create_time,img,template";
            }
            $result = $News->where($where)->field($field)->limit($page, $total)->order('create_time desc')->select();
            if (!$result) {
                return $this->error(1030, '暂无数据返回');
            }else{
                foreach ($result as $key => $value) {
                    if(empty($value['img'])){
                        $value['img'] = '/Upload/news/default/company_news.png';
                    }
                    $result[$key]['img'] =$value['img'];
                    $str = htmlspecialchars_decode($value['content']);
                    $result[$key]['content'] = parent::filterTag($str);
                    //资讯类型 1要闻 2培训 3通知 4活动
                    switch (intval($value['type'])) {
                        case 1;
                            $result[$key]['type'] = '【要闻】';
                            break;

                        case 2;
                            $result[$key]['type'] = '【培训】';
                            break;

                        case 3;
                            $result[$key]['type'] = '【通知】';
                            break;

                        case 4;
                            $result[$key]['type'] = '【活动】';
                            break;
                    }
                }
                return $this->success(1000, '获取成功',$result);
            }
        } else {
            return $this->error(1030, '缺少资讯所属模板参数');
        }
    }


    /**
     * @创建资讯
     */
    public function createUpdateNews($data,$method,$userId){
        //获取登录者当前所在的组织id
        $tissueId = $this->getTissueId($userId);
        $data['tissue_id'] = $tissueId;
        $data['user_id'] = $userId;
        $data['auth_user_id'] = $userId;//创建数据者id
        $data['create_time'] = date('Y-m-d H:i:s', time());
        if ($method == 2) {//编辑
            $id = I('post.id', '', 'trim,htmlspecialchars,int');
            if ($id == '') {
                return $this->error(1030, '编辑id参数不能为空');
            }
            if ($id < 1) {
                return $this->error(1030, '编辑id参数有误');
            }
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $data['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            }
            $result = M('News')->where(array('id' => $id))->save($data);
             write_login_log(6,3,$data['title'],);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
            if ($result) {
                return $this->success(1000, '操作成功');
            } else {
                return $this->error(1030, '操作失败');
            }
        }elseif($method == 1){//新建
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $data['id'] = getNextId('news');
                $data['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            }
             $this->write_login_log(6,2,$data['title'],$userId);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
            $result = M('News')->data($data)->add();
            if ($result) {
                return $this->success(1000, '操作成功');
            } else {
                return $this->error(1030, '操作失败');
            }
        }else{
            return $this->error(1030, '未知操作方式');
        }
    }


    /**
     * @资讯删除
     */
    public function deleteNews($strId,$userId){
        if(!empty($strId)){
            //将字符串拼装成数组
            $arrId = explode(',',$strId);
            foreach($arrId as $val){
                $val = intval($val);
                if(!is_int($val)){
                    return $this->error(1030,'删除id参数必须为大于0的非负整数');
                }
                if($val < 0){
                    return $this->error(1030,'删除id参数必须为大于0的非负整数');
                }
                $info = M('News')->where(array('id' => $val))->find();
                $this->write_login_log(6,4,$info['title'],$userId);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
                $arr[] = $val;
            }
            $where['id'] = array('in',$arr);
            $info = M('News')->where($where)->delete();
            if($info){
                return $this->success(1000,'操作成功');
            }else{
                return $this->error(1030,'操作失败');
            }
        }else{
            return $this->error(1030,'删除id参数有误');
        }
    }


    /**
     * @获取用户TissueId
     */
    public function getTissueId($userId){

        $tissue_id = M('Tissue_group_access')->where(array('user_id'=>$userId))->getField('tissue_id');
        return $tissue_id;
    }


    /**
     *获取资讯评论详情
     */
    public function getNewsCommentList($userId,$news_id,$type,$page,$pageLen){//$userId,$news_id,$type,$page,$pageNum
        $start_page = ($page - 1) * $pageLen;
    if(strtolower(C('DB_TYPE')) == 'oracle'){
        $arr = M('news_interaction')
            ->alias('a')
            ->join('left join __USERS__ b  on b.id=a.user_id')
            ->where(array('a.news_id'=>$news_id))
            ->field("b.username,b.avatar,a.id,a.news_id,a.user_id,a.content,a.images,a.status,a.pid,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time")
            ->select();
    }else{
        $arr = M('news_interaction')
            ->alias('a')
            ->join('left join __USERS__ b  on b.id=a.user_id')
            ->where(array('a.news_id'=>$news_id))
            ->field("b.username,b.avatar,a.id,a.news_id,a.user_id,a.content,a.images,a.status,a.pid,a.publish_time")
            ->select();

    }
        foreach($arr as $k=>$v){
            //   M('news_interaction')->where($v['pid'])->getField('user_id')
            //每条数据加上“对谁回复”的栈toname
            $usernameArr = M('news_interaction')
                ->alias('a')
                ->join('left join __USERS__ b  on b.id=a.user_id')
                ->where(array('a.id'=>$v['pid']))
                ->field('b.username,b.id')
                ->find();
            $username = $usernameArr['username'];
            $tempuser_id = $usernameArr['id'];
            $arr[$k]['toname'] = $username;
            $arr[$k]['touser_id'] = $tempuser_id;
            //每条数据加上点赞的栈news_praise
            $praise_num = M('news_praise')->where(array('cid'=>$v['id'],'praise' => 1))->count();
            $arr[$k]['praise_num'] = $praise_num;
            $arr[$k]['content'] = strip_tags(htmlspecialchars_decode($v['content']));
            $whetherPraise = M('news_praise')->where(array('cid'=>$v['id'],'user_id'=>$userId,'praise' => 1))->getField('praise');
            if($whetherPraise){
                $whetherPraise = 1;//为我点赞过的
            }else{
                $whetherPraise = 0;//没有点赞过的
            }
            $arr[$k]['is_praise'] = $whetherPraise;
        }

        $list = $this->getTreeDatas($arr,'tree','','id','id','pid'); //调用basemodel的的getTreeDatas，取得tree

        //重新组装二维数组
        static $key  = 1;
        foreach ($list as $k => $v) {
            $v['content'] = strip_tags(htmlspecialchars_decode($v['content']));
            if($v['pid'] == 0){
                $key = $k;
                $list[$key]=$v;
               //if($type == 2){
                    $list[$key]['subReply'] = array();
               // }

            }else{
               // if($type == 2){
                    $list[$key]['subReply'][]=$v;
                //}
                unset($list[$k]);
            }
        }
        //对每条话题加入评论数
        foreach ($list as $k => $v) {
            $list[$k]['subComments'] =  count($v['subReply']);

        }
        foreach($list as $val_data){
            $list_data[] = $val_data;
        }
     if($type == 1){//热门评论
        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'praise_num',       //排序字段
        );
          $arrayData = parent::array_sort($list_data,$sort,0,8);
       }elseif($type ==2){//最新评论
        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'publish_time',       //排序字段
        );

         $arrayData = parent::array_sort($list_data,$sort,$start_page,$pageLen);
       }
        return $arrayData;
    }


    /**
     *新增资讯评论
     */
    public function createNewsComment($userId,$news_id,$content){
            $orderno_data = D('Trigger')->orderNumber(5,$userId);
            $orderno = $orderno_data['no'];
            $data = array(
                'orderno' => $orderno,
                'news_id' => $news_id,
                'user_id' => $userId,
                'content' => $content,
                'publish_time' => date('Y-m-d H:i:s'),
                'status'=>1,
                'pid'=>0
            );
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('news_interaction');
            $data['publish_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        if($orderno_data['status'] == 0){
            $data['status'] = 1;
        }
        $result = M('news_interaction')->add($data);
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.news_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.content,a.images,a.orderno,a.status,b.username,b.avatar";
        }else{
            $field = "a.id,a.news_id,a.user_id,a.publish_time,a.content,a.images,a.orderno,a.status,b.username.b.avatar";
        }
        $res = M('news_interaction a')->join("LEFT JOIN __USERS__ b ON b.id = a.user_id")->where(array('a.id' => $result))->field($field)->find();
        $res['toname'] = '';
        $res['touser_id'] = '';
        $res['is_praise'] = 0;
        $res['praise_num'] = 0;
        $res['_level'] = '';
        $res['_html'] = '';
        $res['_name'] = '';
        $res['subReply'] = array();
        $res['subComments'] = 0;
        return $res;
    }

    /**
     * 回复资讯评论
     */
    public function replyNewsComment($userId,$news_id,$content,$pid){
        $orderno_data = D('Trigger')->orderNumber(5,$userId);
        $orderno = $orderno_data['no'];
        $data = array(
            'orderno' => $orderno,
            'news_id' => $news_id,
            'user_id' => $userId,
            'content' => $content,
            'publish_time' => date('Y-m-d H:i:s'),
            'status' => 1,
            'pid' => $pid
        );
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('news_interaction');
            $data['publish_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        if($orderno_data['status'] == 0){
            $data['status'] = 1;
        }
        $result = M('news_interaction')->add($data);
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.news_id,a.user_id,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time,a.content,a.orderno,a.pid,a.status,b.username as toname,b.id as touser_id";
        }else{
            $field = "a.id,a.news_id,a.user_id,a.publish_time,a.orderno,a.pid,a.status,b.username as toname,b.id as touser_id";
        }
        $res = M('news_interaction a')->join("LEFT JOIN __USERS__ b ON b.id = a.user_id")->where(array('a.id' => $result))->field($field)->find();
        $info = M('users')->where(array('id' => $userId))->field("username,avatar")->find();
        $res['username'] = $info['username'];
        $res['avatar'] = $info['avatar'];
        $res['is_praise'] = 0;
        $res['praise_num'] = 0;
        $res['_level'] = '';
        $res['_html'] = '';
        $res['_name'] = '';
        $res['_first'] = true;
        $res['_end'] = true;
        return $res;
    }


    /**
     *话题的 点赞
     */
    /**
     * 资讯评论点赞，评论话题点赞
     * @Param $type 1话题点赞，否则评论话题点赞
     */
    public function newsCommentPraise($cid,$userId,$is_praise){
            $map = array(
                'cid' => $cid,
                'user_id' => $userId,
            );
            if ($is_praise == 1) { //点赞操作
                $ret = M('news_praise')->where($map)->find(); //判断登陆者是否对该条话题进行点赞过
                if (!$ret) {//初次点赞
                    $data = array(
                        'cid' => $cid,
                        'praise' => 1,
                        'praise_time' => date('Y-m-d H:i:s'),
                        'user_id' => $userId,
                    );
                    if (strtolower(C('DB_TYPE')) == 'oracle') {
                        $data['id'] = getNextId('news_praise');
                        $data['praise_time'] = array('exp', "to_date('" . date('Y-m-d H:i:s') . "','yy-mm-dd hh24:mi:ss')");
                    }
                    $result = M('news_praise')->add($data);
                    if ($result) {
                        return array('code' => 1000, 'message' => '点赞成功');
                    } else {
                        return array('code' => 1030, 'message' => '点赞失败');
                    }
                } else { //如果是存在数据，则判断是否是取消点赞后再点赞还是连续重复二次点赞
                    //这里是取消点赞后再次点赞操作
                    if ($ret['praise'] == 0) {
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
                        if (strtolower(C('DB_TYPE')) == 'oracle') {
                            // $data['id'] = getNextId('topic_praise');
                            $data['praise_time'] = array('exp', "to_date('" . date('Y-m-d H:i:s') . "','yy-mm-dd hh24:mi:ss')");
                            $data['type'] = 1;
                        }
                        $result = M('news_praise')->where($data_where)->save($data);
                        if ($result) {
                            return array('code' => 1000, 'message' => '点赞成功');
                        } else {
                            return array('code' => 1030, 'message' => '点赞失败');
                        }
                    } else {
                        return array('code' => 1031, 'message' => '已经点赞过，不能重复操作了');
                    }

                }
            } else if ($is_praise == 0) { //取消点赞操作
                $praise = M('news_praise')->where($map)->getField('praise');
                if ($praise == 0) {
                    return array('code' => 1032, 'message' => '已取消点赞，不能重复操作');
                } else {
                    $res = M('news_praise')->where($map)->setField('praise', 0);
                    if ($res) {
                        return array('code' => 1000, 'message' => '取消点赞成功');
                    } else {
                        return array('code' => 1032, 'message' => '已取消点赞，不能重复操作');
                    }
                }
            }
        }
    /**
     *互动-消息触发
     */
    public function hudongMessage($cid,$title)
    {
        $user_id = M('news_interaction')->where(array('id'=>$cid))->getField('user_id');

        $contents_time = date('Y-m-d H:i:s');
        $type_id = 14;
        $from_id = $_SESSION['user']['id'];
        $url = 'Admin/FriendsCircle/friendsCircleList#c'.$cid;
        // D('Trigger')->messageTrigger($user_id,$title,$contents_time,$type_id,$from_id,$url);
    }


    /**
     *获取话题详情
     */
    public function getTheTopic($news_id)
    {
        $data = M('group_topic')->alias('a')
            ->join('left join __USERS__ b on a.user_id=b.id')
            ->field('a.*,b.username,b.avatar')
            ->where(array('a.id'=>$news_id))
            ->find();

        return $data;
    }
}