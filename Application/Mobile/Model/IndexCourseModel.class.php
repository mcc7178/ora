<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2017/9/4
 * Time: 20:20
 */

namespace Mobile\Model;

use Think\Model;


class IndexCourseModel extends CommonModel{


    /**
     * @我的课程
     * @Param $typeId 1进行中   2已结束  默认为1进行中
     * @Param $page 第几页
     * @Param $pageNum 显示数据条数
     * @Param $userId 用户id
     *
     */

    public function homePageCourse($typeId,$page,$pageNum,$userId){

        //分页参数
        $start_page = ($page - 1) * $pageNum;
        //获取所属标签
        $tag_list = M("users_tag")->order("id desc")->select();

        $where = array();
        $where['a.status'] = array("eq",1);
        $where['a.auditing'] = array("eq",1);

        //在线课程
        $where["a.course_way"] = 0;

        if(!empty($param["cid"])){
            //课程分类-获取子类
            $cids = self::getCourseChild($param["cid"], $param["cid"].",");
            $cids = substr($cids, 0, -1);
            $where["course_cat_id"] = array("in", $cids);
        }
        $where["d.user_id"] = $userId;
        $where["d.sign_up"] = array("eq",1);
        $where["e.type"] = array("in","0,4");

        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.course_name,a.course_code,a.course_time,a.course_cat_id,a.lecturer,a.course_way,a.media_src,";
            $field .= "a.maker,a.chapter,a.course_cover,a.auditing,a.status,a.is_public,a.click_count,a.location,a.restrictions,";
            $field .= "a.lecturer_name,a.user_id,a.is_trigger,a.score,a.arrangement_id,a.orderno,a.objection,a.tag_id,a.jobs_id,a.auth_user_id,";
            $field .= "to_char(c.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(c.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
        }else{
            $field = "a.id,a.course_name,a.course_code,a.course_time,a.course_cat_id,a.lecturer,a.course_way,a.media_src,";
            $field .= "a.maker,a.chapter,a.course_cover,a.auditing,a.status,a.is_public,a.click_count,a.location,a.restrictions,";
            $field .= "a.lecturer_name,a.user_id,a.is_trigger,a.score,a.arrangement_id,a.orderno,a.objection,a.tag_id,a.jobs_id,a.auth_user_id,c.start_time,c.end_time";
        }
        $results = M("course a")
            ->join("LEFT JOIN __COURSE_CATEGORY__ b ON a.course_cat_id = b.id")
            ->join("JOIN __PROJECT_COURSE__ c ON a.id=c.course_id")
            ->join("JOIN __DESIGNATED_PERSONNEL__ d ON c.project_id = d.project_id")
            ->join("JOIN __ADMIN_PROJECT__ e ON c.project_id = e.id")
            ->field($field .",c.project_id,b.cat_name,c.course_id,c.credit,e.project_name")
            ->where($where)
            ->order("c.start_time desc")
            ->limit($start_page,$pageNum)
            ->select();
        $info = array();
        foreach($results as $i=>$v){
            //如果课程表的讲师字段为空值则则去讲师表的讲师名称，否则去课程表讲师名称
            if(empty($v["lecturer_name"]) && !empty($v["lecturer"])){
                $lecturer = M("lecturer")->field("name")->where("id=".$v["lecturer"])->find();
                $info_data[$i]["lecturer_name"] = $lecturer["name"];
            }else{
                $info_data[$i]["lecturer_name"] = $v["lecturer_name"];
            }
            $course_detail = M("course_detail")->where("id=".$v["course_id"])->find();
            $start_time = strtotime($v['start_time']) - time();//大于0表示待考试
            $end_time = strtotime($v['end_time']) - time();//小于0表示逾期（已结束）
           	if($typeId == 1){//进行中
               	if($start_time > 0){
                   	$info[$i]['_status'] = 1;//未开始
                    $info[$i]["course_intro"] = $course_detail["course_intro"] ? $course_detail["course_intro"] : '暂无介绍';
                    $info[$i]['course_cover'] = $v['course_cover'] ? $v['course_cover'] : '/Upload/20170117/587dd64685c0b.jpg';
                    $info[$i]['cat_name'] = $v['cat_name'];
                    $info[$i]['score'] = $v['score'] ? $v['score'] : 0;
                    $info[$i]["credit"] = $v["credit"] ? $v["credit"] : 0;
                    $info[$i]["lecturer_name"] =  $info_data[$i]["lecturer_name"];
                    $info[$i]['course_id'] = $v['course_id'];
                    $info[$i]['project_id'] = $v['project_id'];
                    $info[$i]['course_name'] = $v['course_name'];
                    $info[$i]['typeId'] = $typeId;
               	}else if($end_time > 0  && $start_time < 0){
                   	$info[$i]['_status'] = 2;//学习中
                    $info[$i]["course_intro"] = $course_detail["course_intro"] ? $course_detail["course_intro"] : '暂无介绍';
                    $info[$i]['course_cover'] = $v['course_cover'] ? $v['course_cover'] : '/Upload/20170117/587dd64685c0b.jpg';
                    $info[$i]['cat_name'] = $v['cat_name'];
                    $info[$i]['score'] = $v['score'] ? $v['score'] : 0;
                    $info[$i]["credit"] = $v["credit"] ? $v["credit"] : 0;
                    $info[$i]["lecturer_name"] =  $info_data[$i]["lecturer_name"];
                    $info[$i]['course_id'] = $v['course_id'];
                    $info[$i]['project_id'] = $v['project_id'];
                    $info[$i]['course_name'] = $v['course_name'];
                    $info[$i]['typeId'] = $typeId;
               	}
           	}elseif($typeId == 2){
               	if($end_time < 0){
                    $info[$i]["course_intro"] = $course_detail["course_intro"] ? $course_detail["course_intro"] : '暂无介绍';
                    $info[$i]['course_cover'] = $v['course_cover'] ? $v['course_cover'] : '/Upload/20170117/587dd64685c0b.jpg';
                    $info[$i]['cat_name'] = $v['cat_name'];
                    $info[$i]['score'] = $v['score'] ? $v['score'] : 0;
                    $info[$i]["credit"] = $v["credit"] ? $v["credit"] : 0;
                    $info[$i]["lecturer_name"] =  $info_data[$i]["lecturer_name"];
                    $info[$i]['course_id'] = $v['course_id'];
                    $info[$i]['project_id'] = $v['project_id'];
                    $info[$i]['course_name'] = $v['course_name'];
                    $info[$i]['typeId'] = $typeId;
                   	$info[$i]['_status'] = 3;//已结束
               	}/*else{
                    $info[$i]["course_intro"] = $course_detail["course_intro"] ? $course_detail["course_intro"] : '暂无介绍';
                    $info[$i]['course_cover'] = $v['course_cover'] ? $v['course_cover'] : '/Upload/20170117/587dd64685c0b.jpg';
                    $info[$i]['cat_name'] = $v['cat_name'];
                    $info[$i]['score'] = $v['score'] ? $v['score'] : 0;
                    $info[$i]["credit"] = $v["credit"] ? $v["credit"] : 0;
                    $info[$i]["lecturer_name"] =  $info_data[$i]["lecturer_name"];
                    $info[$i]['course_id'] = $v['course_id'];
                    $info[$i]['project_id'] = $v['project_id'];
                    $info[$i]['course_name'] = $v['course_name'];
                    $info[$i]['typeId'] = $typeId;
                    $info[$i]['_status'] = 3;//已结束
                }*/
           	}

        }
        //公开课选修课程
        $_where['a.is_public'] = array("eq",1);
        $_where['a.status'] = array("eq",1);
        $_where['a.auditing'] = array("eq",1);

        //在线课程
        $_where["a.course_way"] = 0;//只要在线课程
        $_where['c.user_id'] = array("eq",$userId);
        $res = M("course a")
            ->join("LEFT JOIN __COURSE_CATEGORY__ b ON a.course_cat_id = b.id")
            ->join("JOIN __COURSE_RECORD__ c ON a.id = c.course_id")
            ->field("a.*,b.cat_name")
            ->where($_where)
            ->limit($start_page, $pageNum)
            ->select();
        $_info = array();
        foreach($res as $k=>$val){
            //如果课程表的讲师字段为空值则则去讲师表的讲师名称，否则去课程表讲师名称
            if(empty($v["lecturer_name"]) && !empty($val["lecturer"])){
                $lecturer = M("lecturer")->field("name")->where("id=".$val["lecturer"])->find();
                $_lecturer = $lecturer["name"];
            }else{
                $_lecturer = $val["lecturer_name"];
            }
            $chapter = json_decode($val["chapter"], true);//总章节
            $wheres['project_id'] = 0;
            $wheres['course_id'] = intval($val['id']);
            $wheres['user_id'] = intval($userId);
            //查询已学习的章节

            $isStudy = M("Course_chapter")->where($wheres)->count("id");

            $status = M("Course_chapter")->where($wheres)->sum("status");
            $course_detail = M("course_detail")->where("id=".$val["id"])->find();
    if($typeId == 1){//进行中
        if($isStudy == 0){//未开始
            $_info[$k]['_status'] = 1;
        }elseif($isStudy == count($chapter) && ($status < ($isStudy * 3))){//学习中
            $_info[$k]['_status'] = 2;
        }
        $_info[$k]['course_id'] = $val['id'];
        $_info[$k]['project_id'] = 0;
        $_info[$k]['course_name'] = $val['course_name'];
        $_info[$k]['cat_name'] = $val['cat_name'];
        $_info[$k]["course_intro"] = $course_detail["course_intro"] ? $course_detail["course_intro"] : '暂无介绍';
        $_info[$k]["score"] = $val["score"] ? $val["score"] : 0;
        $_info[$k]["credit"] = $val["credit"] ? $val["credit"] : 0;
        $_info[$k]['course_cover'] = $val['course_cover'] ? $val['course_cover'] : '/Upload/20170117/587dd64685c0b.jpg';
        $_info[$k]["lecturer_name"] = $_lecturer;
        $_info[$k]['typeId'] = $typeId;
    }elseif($typeId == 2){//已结束
        if($isStudy == count($chapter) && ($status == ($isStudy * 3))){
            $_info[$k]['_status'] = 3;
            $_info[$k]['course_id'] = $val['id'];
            $_info[$k]['project_id'] = 0;
            $_info[$k]['course_name'] = $val['course_name'];
            $_info[$k]['cat_name'] = $val['cat_name'];
            $_info[$k]["course_intro"] = $course_detail["course_intro"] ? $course_detail["course_intro"] : '暂无介绍';
            $_info[$k]["score"] = $val["score"] ? $val["score"] : 0;
            $_info[$k]["credit"] = $val["credit"] ? $val["credit"] : 0;
            $_info[$k]['course_cover'] = $val['course_cover'] ? $val['course_cover'] : '/Upload/20170117/587dd64685c0b.jpg';
            $_info[$k]["lecturer_name"] = $_lecturer;
            $_info[$k]['typeId'] = $typeId;
        }
    }

        }
        if(!empty($info) && empty($_info)){
            $array_data = $info;
        }elseif(!empty($_info) && empty($info)){
            $array_data = $_info;
        }else{
            $array_data = array_merge($info,$_info);
        }
        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'course_id',       //排序字段
        );
        $arrayData = parent::array_sort($array_data,$sort,$start_page,$pageNum);
       	if($arrayData){
          return array('code' => 1000,'message' => '获取成功','data' => $arrayData);
        }else{
          return array('code' => 1030,'message' => '无数据返回');
        }
    }

    /**
     * 首页待办任务消息通知
     */
    public function homePageWaitingTaskNotice($userId){

        $where['a.user_id'] = array('eq', $userId);
        $where['a.type_id'] = array('in', array(10, 11, 12,14));
        $where['a.status'] = array('eq', 0);
        $info = M('AdminMessage a')
            ->field("a.id,a.user_id,a.title,to_char(a.contents_time,'YYYY-MM-DD HH24:MI:SS') as contents_time,a.type_id,a.status,a.from_id,a.url")
            ->where($where)
            ->order('a.contents_time DESC')
            ->select();
        foreach ($info as $k => $value) {

                if ($value['type_id'] == 10) {
                    $info[$k]['newsType'] = '课程学习';
                }
                if ($value['type_id'] == 11) {
                    $info[$k]['newsType'] = '参加考试';
                }
                if ($value['type_id'] == 12) {
                    $info[$k]['newsType'] = '参与调研';
                }
                if ($value['type_id'] == 14) {//互动消息
                    $info[$k]['newsType'] = '互动消息';
                }
                $from_username = M('users')->where(array('id' => $value['from_id']))->getField('username');
                $info[$k]['title'] = $from_username . '回复了我';

                $allString = $value['url'];
                $searchString = "#c";
                $newString = strstr($allString, $searchString);
                $length = strlen($searchString);
                $cid = substr($newString, $length);
                //查找回复 pid=0 的评论
                $parent = D('Mine')->findParent($cid);
                if (!empty($parent)) {
                    foreach ($parent as $k1 => $v1) {
                        $v1['pid'] = 0;
                        $cid = $v1['id'];
                    }
                }
                $info[$k]['cid'] = $cid;
            }
            unset($info[$k]['url']);
        if ($info) {
            return $this->success(1000, '获取数据成功', array('messageNum' => count($info)));
        } else {
            return $this->error(1030, '暂无数据返回',array('messageNum' => 0));
        }
    }
}