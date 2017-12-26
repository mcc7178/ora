<?php
namespace Mobile\Model;

use Think\Model;
/**
 * @首页公开课
 * @author lizhongjian
 *
 */
class PublicCourseModel extends CommonModel{

    protected $tableName = 'Course';

    /****************兼容oracle版*************************************/
    /**
     *@首页公开课程列表
     *@Prarm $userId用户id
     *@Prarm $page数组分割参数
     *@Prarm $keyword搜索关键字
     *@Prarm $pageNum数组分割参数
     *@Prarm $cid课程分类参数
     */
    public function homePages($userId,$page,$keyword,$pageNum,$cid,$style){

        //接收搜索关键字解码
        $keyword = urldecode($keyword);
        iconv( 'CP1252', 'UTF-8', $keyword);
        //分页参数不传则默认为第一页
        $start = ($page - 1) * $pageNum;
        $where["a.is_public"] = array("eq",1);
        //$where["a.course_way"] = array("eq",0);
        $where['a.status'] = array("eq",1);
        $where['a.auditing'] = array("eq",1);
        $DB_PREFIX = strtolower(C('DB_PREFIX').'course_score');
        $tissue_id = M("tissue_group_access")->where(array('user_id' => $userId))->getField("tissue_id");
        //获取方案ID
        $plan_id = M("sys_tissue")->where("tissue_id=".$tissue_id)->getField("plan_id");
        $plan_id = $plan_id ? $plan_id : 0;
        //隔离数据过滤
        //根据方案取出所有组织会员ID
        $sys_tissue_list = M("sys_tissue")->field("tissue_id")->where("plan_id=".$plan_id)->select();
        $specifiedUser = array();
        foreach($sys_tissue_list as $list){
            $group_access = M("tissue_group_access")->field("user_id")->where("tissue_id=".$list['tissue_id'])->select();
            foreach($group_access as $access){
                $specifiedUser[] = $access['user_id'];
            }
        }
        //获取学员组织架构范围内所属的课程id
        if($plan_id > 0){
            $_where['auth_user_id'] = array("in",$specifiedUser);
        }else{
            $_where['auth_user_id'] = 0;
        }
        $course_arrId = array();
        $tissue_course = M("course")->where($_where)->field("id")->select();
        foreach($tissue_course as $tc){
            $course_arrId[] = $tc['id'];
        }
         if(!empty($cid)){
             $courseCategory_list = $this->courseCategory($plan_id);

             foreach($courseCategory_list as $list){
                 $cid_list[] = $list['id'];
             }
             if(in_array($cid,$cid_list)){
                 //课程分类-获取子类
                 $cids = self::getCourseChild($cid,$cid.",");
                 $cids = substr($cids, 0, -1);
                 $where["b.id"] = array("in", $cids);
             }else{
                 $where["b.id"] = array("eq", $cid);
             }
         }

        //查询共享数据
        $wheres['a.type'] = array("eq",1);
        $wheres['b.user_id'] = array("eq",$userId);
        $resource_sharing = M("resource_sharing a")->join("LEFT JOIN __TISSUE_GROUP_ACCESS__ b ON a.tissue_id = b.tissue_id")->field("a.source_id")->where($wheres)->select();
        foreach($resource_sharing as $sharing){
            $course_arrId[] = $sharing['source_id'];
        }
        //如果存在共享资源，根据共享资源id获取共享数据
        if(!empty($course_arrId)){
            $where['a.id'] = array("in",$course_arrId);
        }
        if($style == 3){//好评
            $order = "a.score desc";
        }else if($style == 2){//最热
            $order = "a.click_count desc";
        }elseif($style == 1){//最新
            $order = "a.id desc";
        }else{
            return $this->error(1030,'非法参数');
        }
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.score as course_score,a.course_name,a.course_time,a.course_cat_id,a.lecturer,";
            $field .= "a.course_cover,a.credit,a.status,a.click_count,a.location,";
            $field .= "a.lecturer_name,a.user_id,a.tag_id,a.jobs_id,a.auth_user_id,";
            if(!empty($keyword)){
                $where["a.course_name"] = array("like", "%".$keyword."%");
            }

        }else{
            $field = "a.*";
            if(!empty($keyword)){
                $where["a.course_name"] = array("like", "%".$keyword."%");
            }
        }
        $field .= "b.cat_name,d.course_intro as course_description";
        $results = M("course a")
            ->join("LEFT JOIN __COURSE_CATEGORY__ b ON a.course_cat_id = b.id LEFT JOIN __COURSE_DETAIL__ d ON d.id = a.id")
            ->field($field)
            ->where($where)
            ->order($order)
            ->select();

        //判断是否有加入我的课程
        foreach($results as $k => $v){
            //进一取整，计算课程平均评价分数
            $results[$k]['course_score'] = ceil($v['course_score']) ? ceil($v['course_score']) : 0;
            $results[$k]['course_cover'] = $v['course_cover'] ? $v['course_cover'] : '/Upload/xxx/20170920/xxx.jpg';
            if(!empty($v['lecturer'])){
                $results[$k]['lecturer_name'] =  M("lecturer")->where(array("id" => $v['lecturer']))->getField('name');
            }

            $hasJoinRecord = M("course_record")->where("user_id=".$userId." AND course_id=".$v["id"])->find();
            if($hasJoinRecord == null){
                $results[$k]['is_joinMyCourse'] = 0; //没有加入我的课程
            }else{
                $results[$k]['is_joinMyCourse'] = 1; //已经加入我的课程
            }
        }

        //采用数组分页方式进行分页
        $results = array_slice($results,$start,$pageNum);

        if($results){
            return array('code'=>1000,'message'=>'获取数据成功','data'=>$results);
        }else{
            return array('code'=>1030,'message'=>'暂无数据返回');
        }
    }
    /****************app2.0版本*************************************/
    /**
     * @首页课程
     * @token  用户身份标识
     * @secret_key  秘钥
     * @$type 类型1进行中 2已结束
     * @userId 用户id
     * @$page 分页参数(第几页)
     * @$pageLen 每页显示数据条数
     */
    public function homePageCourse($type,$userId,$page,$pageLen){
        if($page < 1){
            return $this->error(1030,'分页参数有误');
        }
        if($type == 1){
            //进行中（必须首先根据项目的开始和结束时间判断，其次再根据对课程章节浏览记录进度判断)
            //进行中（选修根据对课程章节浏览记录进度判断)
            //必修
            $where = array(
                'a.status' => 1,
                'a.auditing' => 1,
                'a.course_way' => 0,
                'c.user_id'  => $userId,
                'd.type' => 0,
            );
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "to_char(d.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(d.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
            }else{
                $field = 'd.start_time,d.end_time';
            }
			
            $results = M("course a")
                ->join("JOIN __PROJECT_COURSE__ b ON a.id=b.course_id")
                ->join("JOIN __DESIGNATED_PERSONNEL__ as c ON b.project_id = c.project_id")
                ->join("JOIN __ADMIN_PROJECT__ d ON b.project_id = d.id")
                ->join("JOIN __COURSE_DETAIL__ e ON a.id = e.id")
                ->field("b.project_id,a.id as course_id,a.course_name,a.course_cover,e.course_intro," . $field)
                ->where($where)
                ->select();
            if(!empty($results)){
                $key = 0;
                $info = array();
                foreach ($results as $value){
                    //进度
                    $startTime = strtotime($value['start_time']);
                    $endTime = strtotime($value['end_time']);
                    $time = time();

                    $chapter = json_decode($value["chapter"], true);//总章节
                    $chapterWhere["user_id"] = $userId;
                    $chapterWhere["course_id"] = $value["course_id"];
                    $chapterWhere["project_id"] = $value["project_id"];
                    if($startTime < $time){//开始时间小于当前时间=======》未开始
                        $isStudyChapter = M("course_chapter")->field("id")->where($chapterWhere)->count();
                        if($isStudyChapter == 0){
                            $info[$key]['_status'] = 1;
                            $info[$key]['status'] = '未开始';
                        }
                    }elseif(($startTime < $time) && ($time < $endTime)){//当前时间小于结束时间大于开始时间=======》进行中
                        $isStudyChapter = M("course_chapter")->field("id")->where($chapterWhere)->count();
                        if(($isStudyChapter > 0) &&  (count($chapter) > $isStudyChapter)){
                            $info[$key]['_status'] = 2;
                            $info[$key]['status'] = '进行中';
                        }
                    }
					
                    $info[$key]['course_id'] = $value['course_id'];
                    $info[$key]['course_name'] = $value['course_name'];
                    $info[$key]['course_cover'] = $value['course_cover'];
                    $course_intro = $value['course_intro'] ? $value['course_intro'] : '该课程暂无简介';
                    $info[$key]['course_intro'] = $course_intro;
                    $info[$key]['project_id'] = $value['project_id'];
                    ++$key;
                }
            }


            //选修
            //公开课选修课程
            //获取已经过审核的项目
            $condition = array(
                'a.is_public' => 1,
                'a.status' => 1,
                'a.auditing' => 1,
                'a.course_way' => 0,
                'c.user_id'  => $userId
            );

            $data = M("Course a")
                ->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id")
                ->join("JOIN __COURSE_RECORD__ c ON a.id = c.course_id")
                ->field("a.id as course_id,a.course_name,a.course_cover,b.course_intro,c.project_id")
                ->where($condition)
                ->select();
            $k = 0;
            foreach ($data as $val){
                //进度
                $_chapter = json_decode($val["chapter"], true);//总章节
                $_chapterWhere["user_id"] = $userId;
                $_chapterWhere["course_id"] = $val["course_id"];
                $_chapterWhere["project_id"] = 0;
                $_isStudyChapter = M("Course_chapter")->field("id")->where($_chapterWhere)->count();
				
                if($_isStudyChapter == 0){
                    //未开始
                    $res[$k]['_status'] = 1;
                    $res[$k]['status'] = '未开始';
                }elseif(($_isStudyChapter > 0) && ($_isStudyChapter < count($_chapter))){
                    //进行中
                    $res[$k]['_status'] = 2;
                    $res[$k]['status'] = '进行中';
                }
                
                $res[$k]['course_id'] = $val['course_id'];
                $res[$k]['course_name'] = $val['course_name'];
                $res[$k]['course_cover'] = $val['course_cover'];
                $_course_intro = $val['course_intro'] ? $val['course_intro'] : '该课程暂无简介';
                $res[$k]['course_intro'] = $_course_intro;
                $res[$k]['project_id'] = $val['project_id'];
                ++$k;
            }

            if(!empty($info) && empty($res)){
                $array_data = $info;
            }elseif(!empty($res) && empty($info)){
                $array_data = $res;
            }else{
                $array_data = array_merge($info,$res);
            }

            $sort = array(
           'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
           'field'     => 'course_id',       //排序字段
       );
            $arrayData = parent::array_sort($array_data,$sort,$page,$pageLen);


        }elseif($type == 2){
            //已结束（必须首先根据项目的开始和结束时间判断，其次再根据对课程章节浏览记录进度判断)
            //已结束（选修根据对课程章节浏览记录进度判断)
            $where = array(
                'a.status' => 1,
                'a.auditing' => 1,
                'a.course_way' => 0,
                'c.user_id'  => $userId,
                'd.type' => 4,
            );
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "to_char(d.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(d.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
            }else{
                $field = 'd.start_time,d.end_time';
            }
			
            $results = M("course a")
                ->join("JOIN __PROJECT_COURSE__ b ON a.id=b.course_id")
                ->join("JOIN __DESIGNATED_PERSONNEL__ as c ON b.project_id = c.project_id")
                ->join("JOIN __ADMIN_PROJECT__ d ON b.project_id = d.id")
                ->join("JOIN __COURSE_DETAIL__ e ON a.id = e.id")
                ->field("b.project_id,a.id as course_id,a.course_name,a.course_cover,e.course_intro," . $field)
                ->where($where)
                ->select();

            if(!empty($results)){
                $key = 0;
                $info = array();
                foreach ($results as $value){
                    //进度
                    $startTime = strtotime($value['start_time']);
                    $endTime = strtotime($value['end_time']);
                    $time = time();
                   if($endTime > $time){
                        //结束时间大于当前时间=======》已结束
                        // $chapterWhere["_string"] = "status = 1 OR status = 3";
                        // $isStudyChapter = M("course_chapter")->field("id")->where($chapterWhere)->count();
                        // if($isStudyChapter < count($chapter) || $isStudyChapter == 0){
                       $info[$key]['course_id'] = $value['course_id'];
                       $info[$key]['course_name'] = $value['course_name'];
                       $info[$key]['course_cover'] = $value['course_cover'];
                       $course_intro = $value['course_intro'] ? $value['course_intro'] : '该课程暂无简介';
                       $info[$key]['course_intro'] = $course_intro;
                       $info[$key]['project_id'] = $value['project_id'];
                       $info[$key]['_status'] = 3;
                       $info[$key]['status'] = '已结束';
                        // }
                    }elseif($endTime < $time){
                       $chapter = json_decode($value["chapter"], true);//总章节
                       $chapterWhere["user_id"] = $userId;
                       $chapterWhere["course_id"] = $value["course_id"];
                       $chapterWhere["project_id"] = $value["project_id"];
                       $isStudyChapter = M("course_chapter")->field("id")->where($chapterWhere)->count();
                       if($isStudyChapter == count($chapter)){
                           $info[$key]['course_id'] = $value['course_id'];
                           $info[$key]['course_name'] = $value['course_name'];
                           $info[$key]['course_cover'] = $value['course_cover'];
                           $course_intro = $value['course_intro'] ? $value['course_intro'] : '该课程暂无简介';
                           $info[$key]['course_intro'] = $course_intro;
                           $info[$key]['project_id'] = $value['project_id'];
                           $info[$key]['_status'] = 3;
                           $info[$key]['status'] = '已结束';
                            }
                   }
                    ++$key;
                }
            }


            //选修
            //公开课选修课程
            //获取已经过审核的项目
            $condition = array(
                'a.is_public' => 1,
                'a.status' => 1,
                'a.auditing' => 1,
                'a.course_way' => 0,
                'c.user_id'  => $userId
            );

            $data = M("Course a")
                ->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id")
                ->join("JOIN __COURSE_RECORD__ c ON a.id = c.course_id")
                ->field("a.id as course_id,a.course_name,a.course_cover,b.course_intro,c.project_id")
                ->where($condition)
                ->select();

            $k = 0;
            foreach ($data as $val){

                //进度
                $_chapter = json_decode($val["chapter"], true);//总章节
                $_chapterWhere["user_id"] = $userId;
                $_chapterWhere["course_id"] = $val["course_id"];
                $_chapterWhere["project_id"] = 0;
                $_isStudyChapter = M("Course_chapter")->field("id")->where($_chapterWhere)->count();
                $status = M("Course_chapter")->where($_chapterWhere)->getField('status');
                if($_isStudyChapter == count($_chapter) && $status == 3){
                    //已完成
                    $res[$k]['course_id'] = $val['course_id'];
                    $res[$k]['course_name'] = $val['course_name'];
                    $res[$k]['course_cover'] = $val['course_cover'];
                    $_course_intro = $val['course_intro'] ? $val['course_intro'] : '该课程暂无简介';
                    $res[$k]['course_intro'] = $_course_intro;
                    $res[$k]['project_id'] = $val['project_id'];
                    $res[$k]['_status'] = 3;
                    $res[$k]['status'] = '已结束';
                }
                ++$k;
            }
            if(!empty($info) && empty($res)){
                $array_data = $info;
            }elseif(!empty($res) && empty($info)){
                $array_data = $res;
            }else{
                $array_data = array_merge($info,$res);
            }
            $sort = array(
                'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => 'course_id',       //排序字段
            );
            $arrayData = parent::array_sort($array_data,$sort,$page,$pageLen);
        }else{
            return $this->error(1030,'参数有误');
        }
        if($arrayData){
            return $this->success(1000,'获取成功',$arrayData);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }



    /****************兼容oracle版本*************************************/
    /**
     * @首页课程(底部导航)
     * @token  用户身份标识
     * @secret_key  秘钥
     * @$type 类型1进行中 2已结束
     * @userId 用户id
     * @$page 分页参数(第几页)
     * @$pageLen 每页显示数据条数
     */
    public function homePageCourses($type,$userId,$page,$pageLen){
        if($page < 1){
            return $this->error(1030,'分页参数有误');
        }
        if($type == 1){
            //进行中（必须首先根据项目的开始和结束时间判断，其次再根据对课程章节浏览记录进度判断)
            //进行中（选修根据对课程章节浏览记录进度判断)
            //必修
            $where = array(
                'a.status' => 1,
                'a.auditing' => 1,
                'a.course_way' => 0,
                'c.user_id'  => $userId,
                'd.type' => 0,
            );
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "to_char(d.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(d.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
            }else{
                $field = 'd.start_time,d.end_time';
            }
            $results = M("course a")
                ->join("JOIN __PROJECT_COURSE__ b ON a.id=b.course_id")
                ->join("JOIN __DESIGNATED_PERSONNEL__ c ON b.project_id = c.project_id")
                ->join("JOIN __ADMIN_PROJECT__ d ON b.project_id = d.id")
                ->join("JOIN __COURSE_DETAIL__ e ON a.id = e.id")
                ->field("b.project_id,a.id as course_id,a.course_name,a.course_cover,e.course_intro," . $field)
                ->where($where)
                ->select();
            if(!empty($results)){
                $key = 0;
                $info = array();
                foreach ($results as $value){
                    //进度
                    $startTime = strtotime($value['start_time']);
                    $endTime = strtotime($value['end_time']);
                    $time = time();

                    $chapter = json_decode($value["chapter"], true);//总章节
                    $chapterWhere["user_id"] = $userId;
                    $chapterWhere["course_id"] = $value["course_id"];
                    $chapterWhere["project_id"] = $value["project_id"];
                    if($startTime < $time){
                        //开始时间小于当前时间=======》未开始
                        $isStudyChapter = M("course_chapter")->field("id")->where($chapterWhere)->count();
                        if($isStudyChapter == 0){
                            $info[$key]['course_id'] = $value['course_id'];
                            $info[$key]['course_name'] = $value['course_name'];
                            $info[$key]['course_cover'] = $value['course_cover'];
                            $course_intro = $value['course_intro'] ? $value['course_intro'] : '该课程暂无简介';
                            $info[$key]['course_intro'] = $course_intro;
                            $info[$key]['project_id'] = $value['project_id'];
                            $info[$key]['_status'] = 1;
                            $info[$key]['status'] = '未开始';
                        }
                    }elseif(($startTime < $time) && ($time < $endTime)){
                        //当前时间小于结束时间大于开始时间=======》进行中
                        $isStudyChapter = M("course_chapter")->field("id")->where($chapterWhere)->count();
                        if(($isStudyChapter > 0) &&  (count($chapter) > $isStudyChapter)){
                            $info[$key]['course_id'] = $value['course_id'];
                            $info[$key]['course_name'] = $value['course_name'];
                            $info[$key]['course_cover'] = $value['course_cover'];
                            $course_intro = $value['course_intro'] ? $value['course_intro'] : '该课程暂无简介';
                            $info[$key]['course_intro'] = $course_intro;
                            $info[$key]['project_id'] = $value['project_id'];
                            $info[$key]['_status'] = 2;
                            $info[$key]['status'] = '进行中';
                        }
                    }
                    ++$key;
                }
            }


            //选修
            //公开课选修课程
            //获取已经过审核的项目
            $condition = array(
                'a.is_public' => 1,
                'a.status' => 1,
                'a.auditing' => 1,
                'a.course_way' => 0,
                'c.user_id'  => $userId
            );

            $data = M("Course a")
                ->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id")
                ->join("JOIN __COURSE_RECORD__ c ON a.id = c.course_id")
                ->field("a.id as course_id,a.course_name,a.course_cover,b.course_intro,c.project_id")
                ->where($condition)
                ->select();
            $k = 0;
            foreach ($data as $val){

                //进度
                $_chapter = json_decode($val["chapter"], true);//总章节
                $_chapterWhere["user_id"] = $userId;
                $_chapterWhere["course_id"] = $val["course_id"];
                $_chapterWhere["project_id"] = 0;
                $_isStudyChapter = M("Course_chapter")->field("id")->where($_chapterWhere)->count();
                if($_isStudyChapter == 0){
                    //未开始
                    $res[$k]['course_id'] = $val['course_id'];
                    $res[$k]['course_name'] = $val['course_name'];
                    $res[$k]['course_cover'] = $val['course_cover'];
                    $_course_intro = $val['course_intro'] ? $val['course_intro'] : '该课程暂无简介';
                    $res[$k]['course_intro'] = $_course_intro;
                    $res[$k]['project_id'] = $val['project_id'];
                    $res[$k]['_status'] = 1;
                    $res[$k]['status'] = '未开始';
                }elseif(($_isStudyChapter > 0) && ($_isStudyChapter < count($_chapter))){
                    //进行中
                    $res[$k]['course_id'] = $val['course_id'];
                    $res[$k]['course_name'] = $val['course_name'];
                    $res[$k]['course_cover'] = $val['course_cover'];
                    $_course_intro = $val['course_intro'] ? $val['course_intro'] : '该课程暂无简介';
                    $res[$k]['course_intro'] = $_course_intro;
                    $res[$k]['project_id'] = $val['project_id'];
                    $res[$k]['_status'] = 2;
                    $res[$k]['status'] = '进行中';
                }
                ++$k;
            }

            if(!empty($info) && empty($res)){
                $array_data = $info;
            }elseif(!empty($res) && empty($info)){
                $array_data = $res;
            }else{
                $array_data = array_merge($info,$res);
            }

            $sort = array(
                'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => 'course_id',       //排序字段
            );
            $arrayData = parent::array_sort($array_data,$sort,$page,$pageLen);


        }elseif($type == 2){
            //已结束（必须首先根据项目的开始和结束时间判断，其次再根据对课程章节浏览记录进度判断)
            //已结束（选修根据对课程章节浏览记录进度判断)
            $where = array(
                'a.status' => 1,
                'a.auditing' => 1,
                'a.course_way' => 0,
                'c.user_id'  => $userId,
                'd.type' => 4,
            );
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "to_char(d.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time,to_char(d.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
            }else{
                $field = 'd.start_time,d.end_time';
            }
            $results = M("course a")
                ->join("JOIN __PROJECT_COURSE__ b ON a.id=b.course_id")
                ->join("JOIN __DESIGNATED_PERSONNEL__ c ON b.project_id = c.project_id")
                ->join("JOIN __ADMIN_PROJECT__ d ON b.project_id = d.id")
                ->join("JOIN __COURSE_DETAIL__ e ON a.id = e.id")
                ->field("b.project_id,a.id as course_id,a.course_name,a.course_cover,e.course_intro," . $field)
                ->where($where)
                ->select();

            if(!empty($results)){
                $key = 0;
                $info = array();
                foreach ($results as $value){
                    //进度
                    $startTime = strtotime($value['start_time']);
                    $endTime = strtotime($value['end_time']);
                    $time = time();
                    if($endTime > $time){
                        //结束时间大于当前时间=======》已结束
                        // $chapterWhere["_string"] = "status = 1 OR status = 3";
                        // $isStudyChapter = M("course_chapter")->field("id")->where($chapterWhere)->count();
                        // if($isStudyChapter < count($chapter) || $isStudyChapter == 0){
                        $info[$key]['course_id'] = $value['course_id'];
                        $info[$key]['course_name'] = $value['course_name'];
                        $info[$key]['course_cover'] = $value['course_cover'];
                        $course_intro = $value['course_intro'] ? $value['course_intro'] : '该课程暂无简介';
                        $info[$key]['course_intro'] = $course_intro;
                        $info[$key]['project_id'] = $value['project_id'];
                        $info[$key]['_status'] = 3;
                        $info[$key]['status'] = '已结束';
                        // }

                    }elseif($endTime < $time){
                        $chapter = json_decode($value["chapter"], true);//总章节
                        $chapterWhere["user_id"] = $userId;
                        $chapterWhere["course_id"] = $value["course_id"];
                        $chapterWhere["project_id"] = $value["project_id"];
                        $isStudyChapter = M("course_chapter")->field("id")->where($chapterWhere)->count();
                        if($isStudyChapter == count($chapter)){
                            $info[$key]['course_id'] = $value['course_id'];
                            $info[$key]['course_name'] = $value['course_name'];
                            $info[$key]['course_cover'] = $value['course_cover'];
                            $course_intro = $value['course_intro'] ? $value['course_intro'] : '该课程暂无简介';
                            $info[$key]['course_intro'] = $course_intro;
                            $info[$key]['project_id'] = $value['project_id'];
                            $info[$key]['_status'] = 3;
                            $info[$key]['status'] = '已结束';
                        }
                    }
                    ++$key;
                }
            }


            //选修
            //公开课选修课程
            //获取已经过审核的项目
            $condition = array(
                'a.is_public' => 1,
                'a.status' => 1,
                'a.auditing' => 1,
                'a.course_way' => 0,
                'c.user_id'  => $userId
            );

            $data = M("Course a")
                ->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id")
                ->join("JOIN __COURSE_RECORD__ c ON a.id = c.course_id")
                ->field("a.id as course_id,a.course_name,a.course_cover,b.course_intro,c.project_id")
                ->where($condition)
                ->select();

            $k = 0;
            foreach ($data as $val){
                //进度
                $_chapter = json_decode($val["chapter"], true);//总章节
                $_chapterWhere["user_id"] = $userId;
                $_chapterWhere["course_id"] = $val["course_id"];
                $_chapterWhere["project_id"] = 0;
                $_isStudyChapter = M("Course_chapter")->field("id")->where($_chapterWhere)->count();
                $status = M("Course_chapter")->where($_chapterWhere)->getField('status');
                if($_isStudyChapter == count($_chapter) && $status == 3){
                    //已完成
                    $res[$k]['course_id'] = $val['course_id'];
                    $res[$k]['course_name'] = $val['course_name'];
                    $res[$k]['course_cover'] = $val['course_cover'];
                    $_course_intro = $val['course_intro'] ? $val['course_intro'] : '该课程暂无简介';
                    $res[$k]['course_intro'] = $_course_intro;
                    $res[$k]['project_id'] = $val['project_id'];
                    $res[$k]['_status'] = 3;
                    $res[$k]['status'] = '已结束';
                }
                ++$k;
            }
            if(!empty($info) && empty($res)){
                $array_data = $info;
            }elseif(!empty($res) && empty($info)){
                $array_data = $res;
            }else{
                $array_data = array_merge($info,$res);
            }
            $sort = array(
                'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => 'course_id',       //排序字段
            );
            $arrayData = parent::array_sort($array_data,$sort,$page,$pageLen);
        }else{
            return $this->error(1030,'参数有误');
        }
        if($arrayData){
            return $this->success(1000,'获取成功',$arrayData);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }


    /**************************兼容oracle版************************/
    /**
     * app首页公开课列表课程分类
     */
    public function courseCategory($plan_id){
        //课程类别
        $courseCategory = M('CourseCategory')
            ->field("id,id as cid,pid,cat_name")
            ->where(array('pid' => 0,'plan_id' => $plan_id))->select();
        foreach ($courseCategory as $k => $v) {

            $courseCategory[$k]['id'] = intval($v['id']);
            $courseCategory[$k]['cid'] = intval($v['cid']);
            $courseCategory[$k]['pid'] = intval($v['pid']);
            $courseCategory[$k]['cat_name'] = $v['cat_name'];
        }

        return $courseCategory;
    }



    //获取课程分类子类
    public function getCourseChild($cid, $cidStr){
        $cid += 0;
        if(!is_int($cid) || $cid < 0){
            return false;
        }

        $cat = M("course_category")->where("pid=".$cid)->select();
        if($cat){
            foreach ($cat as $key=>$v){
                $cidStr .= $v["id"] . ",";
                $cidStr = self::getCourseChild($v["id"], $cidStr);
            }
        }
        return $cidStr;
    }
    /**
     * @param $input
     * @param $columnKey
     * @param null $indexKey
     * @return array
     * 公开课筛选用户标签
     */
    public function items_screen($results,$users_tag,$user_jobid){
        foreach($results as $list) {
            //判断用户是否存在该标签
            if (!empty($list['tag_id'])) {
                $tag_id = explode(",", $list['tag_id']);
                foreach ($users_tag as $tag) {
                    if (in_array($tag['tag_id'], $tag_id)) {
                        $tag_jump = true;
                        break;
                    } else {
                        $tag_jump = false;
                    }
                }
            }

            //判断用户是否存在该岗位
            if (!empty($list['jobs_id'])) {
                if ($user_jobid['job_id'] == $list['jobs_id']) {
                    $job_jump = true;
                } else {
                    $job_jump = false;
                }
            }

            if ((empty($list['tag_id']) && empty($list['jobs_id'])) OR ($tag_jump OR $job_jump)) {
                $itmes[] = $list;
            }
        }

        return $itmes;

    }
    /**
     * app首页公开课列表
     */
    public function publicCourse($get,$user_id){
        if($get['cid'] != ''){
            $info = $this->getAllChildId($get['cid']);
            $info[] = $get['cid'];
            $where['a.course_cat_id'] = array('in',$info);//course_cat_id在这个数组中，
            $get['course_name'] = $get['course_name'] ? $get['course_name'] : "";

        }

        $get['page'] = $get['page'] ? $get['page'] : 1;
        $get['pageNum'] =  15;
        $start = ($get['page'] - 1)*$get['pageNum'];
        $end = $get['pageNum'];
        $where["a.is_public"] = 1;
        $where["a.course_way"] = 0;
        $where["a.status"] = 1;
        if(!empty($get['course_name'])){
            $where['a.course_name']=array('like',"%".$get['course_name']."%");
        }

        if($get['type'] == 1){//按照热门排序
            $publicCourses=M('Course a')
                ->field("a.id as course_id,a.course_name,a.course_cover,a.click_count")
                ->join('LEFT JOIN __COURSE_CATEGORY__ b ON a.course_cat_id = b.id')
                ->where($where)
                ->limit($start,$end)
                ->order('click_count desc')
                ->select();
        }

        if($get['type'] == 2){//按照好评数排序
           	$DB_PREFIX = strtolower(C('DB_PREFIX').'course_score');
		   	$results = M("course a")
		        ->join("LEFT JOIN __COURSE_CATEGORY__ b ON a.course_cat_id = b.id")
		        ->field("a.id as course_id,a.course_name,a.course_cover,a.click_count,b.cat_name,(select sum(c.course_score) from $DB_PREFIX c where a.id = c.course_id) as coursenum")
               	->where($where)
               	->limit($start,$end)
               	->order('a.id DESC')
               	->select();

            $publicCourses = array();
            foreach($results as $k=>$items){
                $publicCourses[$k] = $items;
                if(empty($items['coursenum'])){
                    $publicCourses[$k]['coursenum'] = 0;
                }
            }
            array_multisort($this->i_array_column($publicCourses,'coursenum'),SORT_DESC,$results);
        }

        if($get['type'] == 3){//按照最新添加时间排序
            $publicCourses=M('Course a')
                ->field("a.id as course_id,a.course_name,a.create_time,a.course_cover,a.click_count")
                ->join('LEFT JOIN __COURSE_CATEGORY__ b ON a.course_cat_id = b.id')
                ->where($where)
                ->limit($start,$end)->order('create_time desc')
                ->select();
        }
        if($get['type'] == ""){//无条件排序
        	$publicCourses=M('Course a')
                ->field("a.id as course_id,a.course_name,a.create_time,a.course_cover,a.click_count")
                ->join('LEFT JOIN __COURSE_CATEGORY__ b ON a.course_cat_id = b.id')
                ->where($where)
                ->limit($start,$end)
                ->select();
        }

        $model = M('course_care');
        foreach($publicCourses as $k=>$v){
            $publicCourses[$k]['care_total'] = $model->where(array('course_id'=> $v['course_id'],'care_status'=>1))->count('care_status');
            $user_id_m = $model->where(array('course_id'=> $v['course_id'],'user_id'=>$user_id,'care_status'=>1))->find();
            //查询是否加入过我的课程
            $is_joinMyCourse = M('Course_record')->where(array('user_id'=>$user_id,'course_id'=>$v['course_id']))->find();
            if($is_joinMyCourse){
                $publicCourses[$k]['is_joinMyCourse'] = 1;//有加入过我的课程
            }else{
                $publicCourses[$k]['is_joinMyCourse'] = 0;//没有加入过我的课程
            }
            if($user_id_m){
                $publicCourses[$k]['whether_concern'] = 1;//加入过我的关注
            }else{
                $publicCourses[$k]['whether_concern'] = 0;//没有加入过我的关注
            }
        }
        if($publicCourses){
            return $this->success(1000,'获取数据成功',$publicCourses);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }


    /*
     * @根据课程类别id查询所有子类的id
     * @param $cid 类别id
     */
    public function getAllChildId($cid){
        //实例化类别表模型
        $info = M('CourseCategory')->where(array('pid' => $cid))->select();
        static $list = array();
        foreach($info as $k=>$v){
            $list[] = $v['id'];
            $this->getAllChildId($v['id']);
        }
            return $list; 
    }

    /**
     * 获取我的课程-课程详情-目录
     * $course_id  课程id
     */
    public function getCourseDetailsList($course_id,$user_id){
        if($course_id == ''){
            return $this->error(1023,'缺少课程id');
        }
        $where['a.status'] = 1;
        $where['a.id'] = $course_id;
        $info = M('Course a')->field('a.id,a.chapter')->where($where)->find();
        $info['chapter'] = json_decode($info['chapter'],true);
        foreach ($info['chapter'] as $key=>$value){
            $fileType = substr($value["src"], -5, 5);
            if(strstr($fileType, ".mp4") || strstr($fileType, ".avi") || strstr($fileType, ".flv") || strstr($fileType, ".mp3") || strstr($fileType, ".wmv")){

                $info['chapter'][$key]['type'] = 1;//视频，音频
                if(strstr($fileType, ".mp4")){
                    $info['chapter'][$key]['style'] = 'mp4';//mp4
                }
                if(strstr($fileType, ".avi")){
                    $info['chapter'][$key]['style'] = 'avi';//avi
                }
                if(strstr($fileType, ".flv")){
                    $info['chapter'][$key]['style'] = 'flv';//flv
                }
                if(strstr($fileType, ".mp3")){
                    $info['chapter'][$key]['style'] = 'mp3';//mp3
                }
                if(strstr($fileType, ".wmv")){
                    $info['chapter'][$key]['style'] = 'wmv';//wmv
                }

            }elseif(strstr($fileType, ".ppt") || strstr($fileType, ".pptx") || strstr($fileType, ".png") || strstr($fileType, ".jpeg")){

                $info['chapter'][$key]['type'] = 2;//ppt
                if(strstr($fileType, ".ppt")){
                    $info['chapter'][$key]['style'] = 'ppt';//ppt
                }
                if(strstr($fileType, ".pptx")){
                    $info['chapter'][$key]['style'] = 'pptx';//pptx
                }

                if(strstr($fileType, ".png")){
                    $info['chapter'][$key]['style'] = 'png';//png
                }
                if(strstr($fileType, ".jpeg")){
                    $info['chapter'][$key]['style'] = 'jpeg';//jpeg
                }

            }elseif(strstr($fileType, ".doc") || strstr($fileType, ".docx") || strstr($fileType, ".xls") || strstr($fileType, ".xlsx") || strstr($fileType, ".pdf")){

                $info['chapter'][$key]['type'] = 2;//文档类型
                if(strstr($fileType, ".doc")){
                    $info['chapter'][$key]['style'] = 'doc';//doc
                }
                if(strstr($fileType, ".docx")){
                    $info['chapter'][$key]['style'] = 'docx';//docx
                }
                if(strstr($fileType, ".xls")){
                    $info['chapter'][$key]['style'] = 'xls';//xls
                }
                if(strstr($fileType, ".xlsx")){
                    $info['chapter'][$key]['style'] = 'xlsx';//xlsx
                }
                if(strstr($fileType, ".pdf")){
                    $info['chapter'][$key]['style'] = 'pdf';//pdf
                }

            }else{//mdzz
                $info['chapter'][$key]['type'] = 3;//课件
                $info['chapter'][$key]['style'] = 3;//课件

            }

            //进度
            $info['chapter'][$key]['id'] = $course_id;//课程id
            $info['chapter'][$key]['user_id'] = $user_id;//用户id
            $course_chapter_after = str_replace("&amp;","&", $value['src']);
            $info['chapter'][$key]['src'] = $course_chapter_after;//课件地址
            $info['chapter'][$key]['name'] = $value['name'];//章节名称
            //$info['chapter'][$key]["time_percent"] = 0;
            $cwhere["user_id"] = $user_id;
            $cwhere["course_id"] = $course_id;
            $cwhere["name"] = $value["name"];
            $chapter = M("course_chapter")->where($cwhere)->limit(1)->find();
            if($chapter){
                if($info['chapter'][$key]['type'] == 1){
                    //百分比进度
                    $info['chapter'][$key]["time_percent"] = $chapter["time_percent"];
                    //时长
                    $info['chapter'][$key]["timeLen"] = $chapter["timelen"];
                }else if($info['chapter'][$key]['type'] == 2){

                   $info['chapter'][$key]["time_percent"] = 100;
                   $info['chapter'][$key]["timeLen"] = 100;

            }else if($info['chapter'][$key]['type'] == 3){
                $info['chapter'][$key]["time_percent"] = 0;
                $info['chapter'][$key]["timeLen"] = 0;
              }
            }else {
                if($info['chapter'][$key]['type'] == 1){
                    //百分比进度
                    $info['chapter'][$key]["time_percent"] = 0;
                    //时长
                    $info['chapter'][$key]["timeLen"] = 0;
                }else if($info['chapter'][$key]['type'] == 2){

                    $info['chapter'][$key]["time_percent"] = 0;
                    $info['chapter'][$key]["timeLen"] = 0;

                }else if($info['chapter'][$key]['type'] == 3){
                    $info['chapter'][$key]["time_percent"] = 0;
                    $info['chapter'][$key]["timeLen"] = 0;
                }
            }

        }
        if($info['chapter']){
            return $this->success(1000,'获取数据成功',$info['chapter']);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }

    /**
     * 获取公开课课程详情-简介
     * @ $course_id 课程id
     */
    public function getCourseDetails($course_id,$userId){
        if(empty($course_id)){
            return $this->error(1030,'缺少必要参数');
        }else{
            $condition['a.id'] = $course_id;
            $condition['a.status'] = 1;//通过审核的课程
            $info = M('Course a')
                ->field('a.id,a.course_name,a.media_src,a.click_count,a.lecturer as lecturer_id,a.lecturer_name,b.name,b.description,d.course_intro')
                ->join('LEFT JOIN __LECTURER__ b ON b.id = a.lecturer')
				->join('LEFT JOIN __COURSE_DETAIL__ d ON a.id = d.id')
                ->where($condition)
                ->find();
            if(!empty($info['media_src'])){
                //判断是否是课件
                if(strstr($info['media_src'],'.html')){
                    $info['srcType'] = 1;//课件
                }else{
                    $info['srcType'] = 2;//普通视频
                }

            }else{
                //获取课程章节
                $course_chapter = M('course')->field('chapter')->where(array('id' => $course_id))->find();
                $course_chapter = json_decode($course_chapter['chapter'],true);
                $info['srcImage'] = '/Upload/20170411/58ec391c147ed.png';
                $course_chapter_after = str_replace("&amp;","&",$course_chapter[0]['src']);
                $info['media_src'] = $course_chapter_after;
                $info['srcType'] = 2;//普通视频
            }
            if(empty($info['course_intro'])){
                $info['course_intro'] = '该课程暂无简介信息';
            }
            if($info['lecturer_id'] == 0){
                $info['lecturer_name'] = $info['lecturer_name'];
            }else{
                $info['lecturer_name'] = $info['name'];
            }
            //查询该用户是否有加入过我的课程
            $ret = M('CourseRecord')->where(array('user_id'=>$userId,'course_id'=>$course_id))->find();
            if($ret){
                $info['is_joinMyCourse'] = 1;//有加入过我的课程了
            }else{
                $info['is_joinMyCourse'] = 0;//没有加入过我的课程
            }
            if($info){
                return $this->success(1000,'获取数据成功',$info);
            }else{
                return $this->error(1030,'暂无数据返回');
            }
        }
    }

    /**
     * 我的课程-课程详情-获取评论列表
     * @ $course_id  课程id
     */
    public function getCourseComment($course_id,$userId,$page){
        $page = $page ? $page : 1;
        $pageNum = 20;
        $start = ($page - 1) * $pageNum;
        $end = $pageNum;
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "id,user_id,to_char(comment_content) as comment_content,course_id,to_char(comment_time,'YYYY-MM-DD HH24:MI:SS') as comment_time,state,pid";
        }else{
            $field = "id,user_id,comment_content,course_id,comment_time,state,pid";
        }
        $comList = M("colligate_comment")
            ->where("course_id=".$course_id." AND pid=0")
            ->field($field)
            ->order("comment_time DESC")->limit($start, $end)->select();

        //获取课程综合评价和数量
        $condition['course_score'] = array('EGT',0);
        $condition['course_id'] = $course_id;
        $where['course_id'] = $course_id;
        $where['pid'] = array('EQ',0);
       /* $course_evaluation = M("colligate_comment")->where($where)->AVG('course_evaluation');
        $courseEvaluationCount = M("colligate_comment")->where($where)->count('course_evaluation');
        $course_evaluation = ceil($course_evaluation);*/
        //课程的综合评分（平均值）
        $course_evaluation = M("Course_score")->where($condition)->AVG('course_score');
        //课程的评价数量
        $courseEvaluationCount = M("Colligate_comment")->where($where)->count('id');
        $course_evaluation = ceil($course_evaluation);
        //获取子评论/回复
        foreach ($comList as $key=>$value){
            $user = M("users")->field("username,avatar")->where("id=".$value["user_id"])->limit(1)->select();
            $comList[$key]["username"] = $user[0]["username"];
            $comList[$key]["avatar"] = $user[0]["avatar"];
            $comList[$key]["comment_content"] = str_replace('&nbsp;','',strip_tags($value["comment_content"]));
            $zan = M("course_praise")->where("praise=1 AND id=".$value["id"])->count();
            $comList[$key]["zan"] = $zan;//总赞

            $zanStatus = M("course_praise")->where("id=".$value["id"]." AND user_id=".$userId)->limit(1)->select();
            if(!$zanStatus){
                $zanStatus = "0";
            }else{
                $zanStatus = $zanStatus[0]["praise"] + 0;
            }
            $comList[$key]["zan_status"] = $zanStatus;//我是否赞过  1已赞 0未赞

            //是否可删除
            $comList[$key]["del_status"] = 0;
            if($userId == $value["user_id"]){
                $comList[$key]["del_status"] = 1;
            }

            $subList = array();
            $pids = self::getCommentChild($value["id"], $value["id"].",");
            $pids = substr($pids, 0, -1);
            if($pids){
                $sonCon = M("colligate_comment")
                    ->where("course_id=".$course_id." AND pid in (".$pids.")")->field($field)->select();
                if($sonCon){
                    $userCache = array();
                    foreach ($sonCon as $sk=>$sv){
                        $subUser = M("users")->field("username,avatar")->where("id=".$sv["user_id"])->limit(1)->select();
                        $sonConsd = M("Colligate_comment a")
                        	->join('LEFT JOIN __USERS__ b ON a.user_id = b.id')
                        	->where(array("a.id" => $sv['pid']))
                        	->find();
                        $sonCon[$sk]["username"] = $subUser[0]["username"];
                        $sonCon[$sk]["avatar"] = $subUser[0]["avatar"];
                        $sonCon[$sk]["comment_content"] = str_replace('&nbsp;','',strip_tags($sv["comment_content"]));
                        $userCache[$sv["id"]] = $subUser[0]["username"];

                        //是否可删除
                        $sonCon[$sk]["del_status"] = 0;
                        if($userId == $sv["user_id"]){
                            $sonCon[$sk]["del_status"] = 1;
                        }

                        if($sv["pid"] != $value["id"]){
                            $sonCon[$sk]["reply_user"] = $userCache[$sv["id"]];
                        }
                        $sonCon[$sk]["reply_user"] = $sonConsd["username"];
                        if($sonCon){
                            $comList[$key]["sub_list"] = $sonCon;
                        }else{
                            $comList[$key]["sub_list"] = array();
                        }
                    }
                }
                //统计所有子评论条数
                $comList[$key]["sub_total"] = count($sonCon);
            }
        }

        $data['courseColligateEvaluation'] = $course_evaluation;
        $data['courseEvaluationCount'] = $courseEvaluationCount;
        $data['comment'] = $comList;
        if($comList){
            return $this->success(1000,'获取数据成功',$data);
        }else{
            return $this->error(1030,'赞无数据返回');
        }
    }

    /*
     * 评论或回复评论
     * 执行评论插入操作
     */
    public function addComment($post,$user_id){

        $data['pid'] = $post['id'];//评论id
        $data['course_id'] = $post['course_id'];//课程id
        $data['lecturer_id'] = 0;//课程id
        $data['user_id'] = $user_id;
        $data['comment_content'] = $post['comment_content'];
        if($data['pid'] == '' ||  $data['course_id'] == ''){
            $this->error(1023,'缺少课程id或评论id');die();
        }
        if($data['comment_content'] == ''){
            return $this->error(1030,'请输入内容');
        }
        $data['comment_time'] = date('Y-m-d H:i:s',time());
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('colligate_comment');
            $data['comment_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        $id = M('colligate_comment')->data($data)->add();
        if($id){
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "id,user_id,to_char(comment_content) as comment_content,course_id,to_char(comment_time,'YYYY-MM-DD HH24:MI:SS') as comment_time,state,pid";
            }else{
                $field = "id,user_id,comment_content,course_id,comment_time,state,pid";
            }
            $info = M('colligate_comment')->where(array('id'=>$id))->field($field)->find();
            return $this->success(1000,'操作成功',$info);
        }else{
            return $this->error(1030,'操作失败');
        }
    }

    /**
     * 删除评价
     * comment_id 评价id
     */
    public function delComment($post,$userId){

        if($post['id'] == '' || $post['course_id'] == ''){
            return $this->error(1023,'评论id或课程id不能为空');
        }
        $resp = M("colligate_comment")->where(array("id" => $post['id'],'course_id' => $post['course_id']))->delete();
        if($resp) {
            //删除关联的回复评论
            $pids = self::getCommentChild($post['id'], $post['id'] . ",");
            $pidp = substr($pids, 0, -1);
            if ($pidp) {
                $where['pid'] = array('in',$pidp);
                $res = M("Colligate_comment")->where($where)->delete();
                if ($res) {
                    return $this->success(1000, "操作成功");
                } else {
                    return $this->error(1030, "操作失败");
                }
            }
        }
    }


    /**
     *公开课程-课程详情-点赞/取消点赞
     * $user_id 点赞者id
     * $id 评论内容id
     */
    public function doPraise($get,$user_id){
        $CourseComment = M('CoursePraise');
        $data['user_id'] = $user_id;
        $data['id'] = $get['id'];//评论id
        settype($get['type'], "string");
        if($get['type'] == '0' || $get['type'] == '1'){
            if($get['type'] == '0'){//取消点赞
                $praise = $CourseComment->where($data)->getField('praise');
                if($praise == 0){
                    return $this->error(1031,'不能重复操作');
                }else{
                    $data['praise'] = 0;
                    $data['praise_time'] = date('Y-m-d H:i:s',time());
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
	                    $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
	                }
                    $CourseComment->where(array('user_id'=>$user_id,'id'=>$data['id']))->save($data);
                    $praise_total = $CourseComment->where(array('id'=>$data['id']))->sum('praise');
                    $data['praise_total'] = $praise_total;
                    return $this->success(1000,'操作成功',$data);
                }
            }else{//点赞
                $res = $CourseComment->field('praise')->where($data)->find();
                if($res){
                    $data['praise'] = 1;
                    $data['praise_time'] = date('Y-m-d H:i:s',time());
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
	                    $data['id'] = getNextId('course_praise');
	                    $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
	                }
                    $CourseComment->data($data)->add();
                    //统计点赞总数
                    $praise_total = $CourseComment->where(array('id'=>$data['id']))->sum('praise');
                    $data['praise_total'] = $praise_total;
                    return $this->success(1000,'操作成功',$data);
                }elseif($res['praise'] == 0){
                    $data['praise'] = 1;
                    $data['praise_time'] = date('Y-m-d H:i:s',time());
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
	                    $data['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
	                }
                    $CourseComment->where(array('user_id'=>$user_id,'id'=>$data['id']))->save($data);
                    //统计点赞总数
                    $praise_total = $CourseComment->where(array('id'=>$data['id']))->sum('praise');
                    $data['praise_total'] = $praise_total;
                    return $this->success(1000,'操作成功',$data);
                }else{
                    return $this->error(1031,'不能重复操作');
                }
            }
        }else{
            return $this->error(1023,'缺少必要参数');
        }
    }



    /**
     * 公开课-笔记
     */
    public function getNote($course_id,$user_id){
        if(!empty($course_id)){
            $this->error(1030,'缺少必要参数');
        }
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.user_id,a.project_id,a.course_id,a.note_content,a.is_public,to_char(a.time,'YYYY-MM-DD HH24:MI:SS') as time,b.username,b.avatar";
        }else{
            $field = "a.id,a.user_id,a.project_id,a.course_id,a.note_content,a.is_public,a.time,b.username,b.avatar";
        }
        $info = M('CourseNote a')
        	->field($field)
        	->join('LEFT JOIN __USERS__ b ON a.user_id = b.id')
        	->where(array('a.course_id'=>$course_id,'is_public'=>1))
        	->select();
        if($info){
            return $this->success(1000,'获取数据成功',$info);
        }else{
            return $this->error(1030,'赞无数据返回');
        }
    }




}