<?php
namespace Mobile\Model;

use Think\Model;

class MineModel extends CommonModel {
   
    protected $tablePrefix = 'think_';
    protected $tableName = 'designated_personnel';
    static  $sum;

    protected $_validate = array(
        array('title', 'empty', '标题不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('title', '5,100', '用户名长度超出限制', Model::EXISTS_VALIDATE, 'length'),
        array('content', 'empty', '内容不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('content', '5,1000', '内容长度超出限制', Model::EXISTS_VALIDATE, 'length'),
        array('phone', '/1[34578]{1}\d{9}$/', '请输入正确的手机号格式', Model::EXISTS_VALIDATE, 'regex'),
        array('password', 'empty', '密码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('confirm', 'empty', '确认密码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('password', '6,20', '密码长度为6-20位', Model::EXISTS_VALIDATE, 'length'),
        array('password', 'isPassword', '只允许输入6-20位由 数字、字母、下划线组成的密码', Model::EXISTS_VALIDATE, 'callback'),
        array('phone', 'empty', '手机号码不能为空', Model::EXISTS_VALIDATE, 'function'),
        array('phone', 'is_numeric', '手机号码必须为数字', Model::EXISTS_VALIDATE, 'function')
    );

    /**
     * 初始化
     */
    function __construct(){
        parent::__construct();
    }

  /**
   * APP版本更新
   * @param 
   */
    public function appVersionUpdate(){

        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.version_number,a.version_path,a.version_describe,to_char(a.create_time,'YYYY-MM-DD HH24:MI:SS') as create_time,a.user_id,b.username";
        }else{
            $field = 'a.id,a.version_number,a.version_path,a.version_describe,a.create_time,a.user_id,b.username';
        }

        $data = M('app_version')->alias('a')
                ->join("left join __USERS__ b on b.id = a.user_id")
                ->field($field)
                ->order('id desc')
                ->find();
        
        
     
        return $data;          

    }


    /**
    * 我的课程
    * @param typeId 1必修   2选修  默认为1必修
    */

    public function myCourse($typeId,$page,$pageNum,$userId){

           //分页参数
            $start_page = ($page - 1) * $pageNum;

           //获取所属标签
            $tag_list = M("users_tag")->order("id desc")->select();

           //我的必修课程
            if($typeId == 1){
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
                    ->where($where)->order("c.start_time desc")
                    ->limit($start_page,$pageNum)
                    ->select();
                //计算当年所有月份的必修总学时
                $tiaoJian['typeid'] = 0;
                $rets = M('Tool_learning')
	                ->field('january,february,march,april,may,june,july,august,september,october,november,december')
	                ->where($tiaoJian)
	                ->find();
                $_total = 0;
                foreach($rets as $value){
                    $_total += $value;
                }
                //计算当年实际修读的总学时
                $con['create_time'] = array('like','%'.date('Y').'%');
                $con['user_id'] = $userId;
                $con['typeid'] = 4;
                $sum = M('Center_study')->where($con)->sum('hours');
                if(!$sum){
                    $sum = 0;
                }
                $info = array();
                foreach($results as $i=>$v){
                    //如果课程表的讲师字段为空值则则去讲师表的讲师名称，否则去课程表讲师名称
                    if(empty($v["lecturer_name"]) && !empty($v["lecturer"])){
                        $lecturer = M("lecturer")->field("name")->where("id=".$v["lecturer"])->find();
                        $info_data[$i]["lecturer_name"] = $lecturer["name"];
                    }else{
                        $info[$i]["lecturer_name"] = $v["lecturer_name"];
                    }
                    $course_detail = M("course_detail")->where("id=".$v["course_id"])->find();
                    $start_time = strtotime($v['start_time']) - time();//大于0表示待考试
                    $end_time = strtotime($v['end_time']) - time();//小于0表示逾期（已结束）
                    if($start_time > 0){
                        $info[$i]['_status'] = 1;//未开始
                    }else if($end_time > 0  && $start_time < 0){
                        $info[$i]['_status'] = 2;//学习中
                        $info[$i]['typeId'] = $typeId;
                    }else if($end_time < 0){
                        $info[$i]['_status'] = 3;//已结束
                        $info[$i]['typeId'] = $typeId;
                    }
                    
                    $info[$i]["course_intro"] = $course_detail["course_intro"] ? $course_detail["course_intro"] : '暂无介绍';
                    $info[$i]['course_cover'] = $v['course_cover'] ? $v['course_cover'] : '/Upload/20170117/587dd64685c0b.jpg';
                    $info[$i]['cat_name'] = $v['cat_name'];
                    $info[$i]['score'] = $v['score'] ? $v['score'] : 0;
                    $info[$i]["credit"] = $v["credit"] ? $v["credit"] : 0;
                    $info[$i]['course_id'] = $v['course_id'];
                    $info[$i]['project_id'] = $v['project_id'];
                    $info[$i]['course_name'] = $v['course_name'];
                    $info[$i]["lecturer_name"] = $info_data[$i]["lecturer_name"];
                    $info[$i]['start_time'] = $v['start_time'];
                    $info[$i]['end_time'] = $v['end_time'];
                    $info[$i]['typeId'] = $typeId;
                }
                $result['_total'] = $_total = $_total ? $_total : 0;
                $result['total'] = $sum = $sum ? $sum : 0;
                $result['list'] = $info;
            }elseif($typeId == 2){
                //公开课选修课程
                //获取已经过审核的项目
                $where['a.is_public'] = array("eq",1);
                $where['a.status'] = array("eq",1);
                $where['a.auditing'] = array("eq",1);

                //在线课程
                $where["a.course_way"] = 0;//只要在线课程
                $where['c.user_id'] = array("eq",$userId);
                $res = M("course a")
                    ->join("LEFT JOIN __COURSE_CATEGORY__ b ON a.course_cat_id = b.id")
                    ->join("JOIN __COURSE_RECORD__ c ON a.id = c.course_id")
                    ->field("a.*,b.cat_name")->where($where)->limit($start_page, $pageNum)->select();
                //计算当年所有月份的选修总学时
                $tiaoJian['typeid'] = 1;
                $rets = M('Tool_learning')
	                ->field('january,february,march,april,may,june,july,august,september,october,november,december')
	                ->where($tiaoJian)
	                ->find();
                $_total = 0;
                foreach($rets as $value){
                    $_total += $value;
                }

                //计算当年应修读的总学时
                $con['create_time'] = array('like','%'.date('Y').'%');
                $con['user_id'] = $userId;
                $con['typeid'] = 5;
                $sum = M('Center_study')->where($con)->sum('hours');
                $info = array();
                foreach($res as $k=>$v){
                   //如果课程表的讲师字段为空值则则去讲师表的讲师名称，否则去课程表讲师名称
                    if(empty($v["lecturer_name"]) && !empty($v["lecturer"])){
                        $lecturer = M("lecturer")->field("name")->where("id=".$v["lecturer"])->find();
                        $info_data[$k]["lecturer_name"] = $lecturer["name"];
                    }else{
                        $info_data[$k]["lecturer_name"] = $v["lecturer_name"];
                    }
                    $chapter = json_decode($v["chapter"], true);//总章节
                    $wheres['project_id'] = 0;
                    $wheres['course_id'] = intval($v['id']);
                    $wheres['user_id'] = intval($userId);
                    //查询已学习的章节

                    $isStudy = M("Course_chapter")->where($wheres)->count("id");

                    $status = M("Course_chapter")->where($wheres)->sum("status");
                    $course_detail = M("course_detail")->where("id=".$v["id"])->find();

                    if($isStudy == 0){//未开始
                        $info[$k]['_status'] = 1;
                    }else if($isStudy == count($chapter) && ($status == ($isStudy * 3))){//已结束
                        $info[$k]['_status'] = 3;
                    }else{//学习中
                        $info[$k]['_status'] = 2;
                    }
					
                    $info[$k]['course_id'] = $v['id'];
                    $info[$k]['course_name'] = $v['course_name'];
                    $info[$k]['cat_name'] = $v['cat_name'];
                    $info[$k]["course_intro"] = $course_detail["course_intro"] ? $course_detail["course_intro"] : '暂无介绍';
                    $info[$k]["score"] = $v["score"] ? $v["score"] : 0;
                    $info[$k]["credit"] = $v["credit"] ? $v["credit"] : 0;
                    $info[$k]['course_cover'] = $v['course_cover'] ? $v['course_cover'] : '/Upload/20170117/587dd64685c0b.jpg';
                    $info[$k]["lecturer_name"] = $info_data[$k]["lecturer_name"];
                    $info[$k]['typeId'] = $typeId;
                }
                $result['_total'] = $_total = $_total ? $_total : 0;
                $result['total'] = $sum;
                $result['list'] = $info;
            }
        return $result;
    }


    /*
     * 我的-导航页面
     */
    public function mine($userId){
        //查询用户名，头像，简介，总积分，总学时
        $userInfo = M('Users')->field('id,username,avatar,personalized_signature')->where(array('id'=>$userId))->find();
        $useJiFen = M('Integration_record')->where(array('user_id'=>$userId))->sum('score');
        $useJiFen = $useJiFen ? $useJiFen : 0;
        $learn_time_a = $this->getLearningTarget(1,1,$userId);
        $learn_time_b = $this->getLearningTarget(2,1,$userId);
        
        //$userStudyTime = M('Center_study')->where(array('user_id'=>$userId,'typeid'=>array('in','4,5')))->sum('hours');
        $userStudyTime = $learn_time_a['total'] + $learn_time_b['total'];
        
        $userStudyTime = $userStudyTime ? $userStudyTime : 0;
        //把学时分钟转化为小时
        $userInfo['userStudyTime'] = $userStudyTime."小时";
        $userInfo['useJiFen'] = $useJiFen;
        if($userInfo){
            return $this->success(1000,'获取数据成功',$userInfo);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }
    /**
     * 我的课程-课程详情-简介(选修)
     * @$course_id  课程id
     *
     */
    public function getBriefIntroduction($course_id,$user_id){
        if($course_id == ''){
            return $this->error(1030,'缺少必要参数');
        }
        $condition['a.course_id'] = $course_id;
        $condition['b.status'] = 1;//通过审核的课程
       // $condition['c.type'] = 0;//内部讲师
        $condition['a.user_id'] = $user_id;
        $condition['_logic'] = 'AND';
        $info = M('CourseRecord a')
            ->field('a.course_id,b.course_name,b.course_way,b.media_src,b.click_count,b.lecturer as lecturer_id,b.score as lecturerColligateComment,b.lecturer_name,c.name,c.description,e.course_intro')
            ->join('LEFT JOIN __COURSE__ b ON a.course_id = b.id LEFT JOIN __LECTURER__ c ON b.lecturer = c.id  LEFT JOIN __COURSE_DETAIL__ e ON a.course_id = e.id')
            ->where($condition)->find();
               $_info['course_id'] = $info['course_id'];
               $_info['lecturer_id'] = $info['lecturer_id'];
               $_info['course_name'] = $info['course_name'];
               $_info['click_count'] = $info['click_count'];
               $_info['media_src'] = $info['media_src'];
               $_info['course_intro'] = $info['course_intro'] ? $info['course_intro'] : '暂无简介';
        if(!empty($_info['media_src'])){
            //判断是否是课件
            if(strstr($_info['media_src'],'.html')){
                $_info['srcType'] = 1;//课件
            }else{
                $_info['srcType'] = 2;//普通视频
            }

        }else{
            //获取课程章节
            $course_chapter = M('course')->field('chapter')->where(array('id' => $course_id))->find();
            $course_chapter = json_decode($course_chapter['chapter'],true);
            $_info['srcImage'] = '/Upload/20170411/58ec391c147ed.png';
            $_info['srcType'] = 2;//普通视频
            $course_chapter_after = str_replace("&amp;","&",$course_chapter[0]['src']);
            $_info['media_src'] = $course_chapter_after;
        }
        if($_info){
            return $this->success(1000,'获取数据成功',$_info);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }


    /**
     * 我的课程-课程详情-简介(必修)
     * @$course_id  课程id
     * @$pid  课程关联项目id
     */
    public function getCourseDetails($course_id,$pid,$user_id){
        if($course_id == '' || $pid == ''){
            return $this->error(1030,'缺少必要参数');
        }
        $condition['a.project_id'] = $pid;
        $condition['a.course_id'] = $course_id;
        $condition['b.status'] = 1;//通过审核的课程
        //$condition['c.type'] = 0;//内部讲师
        //计算该课程对应的讲师的综合评价等级
        $info = M('Project_course a')
            ->field('a.course_id,b.course_name,b.course_way,b.media_src,b.click_count,b.lecturer as lecturer_id,b.score as lecturerColligateComment,b.lecturer_name,c.name,c.description,e.course_intro')
            ->join('LEFT JOIN __COURSE__ b ON a.course_id = b.id LEFT JOIN __LECTURER__ c ON b.lecturer = c.id  LEFT JOIN __COURSE_DETAIL__ e ON a.course_id = e.id')
            ->where($condition)->find();
        //获取课程章节
        $course_chapter = M('course')->field('chapter')->where(array('id' => $course_id))->find();
        $course_chapter = json_decode($course_chapter['chapter'],true);

        $info['srcImage'] = '/Upload/20170411/58ec391c147ed.png';
        $course_chapter_after = str_replace("&amp;","&",$course_chapter[0]['src']);
        $info['media_src'] = $course_chapter_after;
        $info['srcType'] = 2;//普通视频
        if($info){
            return $this->success(1000,'获取数据成功',$info);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }

    /**
     * 获取我的课程-课程详情-目录
     * $course_id  课程id
     */
    public function getCourseList($course_id,$project_id,$user_id){
        $project_id  =  $project_id ? $project_id : 0;
        if($course_id != ''){
            $where['b.status'] = 1;
            $where['a.user_id'] = $user_id;
            $where['a.course_id'] = $course_id;
            //$info = M('CourseRecord a')->field('b.id,b.chapter')->join('LEFT JOIN __COURSE__ b ON a.course_id = b.id')->where($where)->find();
            $where_course['status'] = 1;
            $where_course['id'] = $course_id;
            $info = M('Course')->field('id,chapter')->where($where_course)->find();
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

                }elseif(strstr($fileType, ".ppt") || strstr($fileType, ".pptx") || strstr($fileType, ".png") || strstr($fileType, ".jpeg") || strstr($fileType, ".jpg")){

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
                    if(strstr($fileType, ".jpg")){
                        $info['chapter'][$key]['style'] = 'jpg';//jpeg
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
                    $info['chapter'][$key]['style'] = 'mp4';//课件
                }

                //进度
                $info['chapter'][$key]['id'] = $course_id;//课程id
                $info['chapter'][$key]['pid'] = $project_id;//项目id
                $info['chapter'][$key]['chapter_id'] = $key + 1;//章节id
                $info['chapter'][$key]['user_id'] = $user_id;//用户id
               	//获取文件大小
                $value['src'] = htmlspecialchars_decode($value['src']);//&amp; 转为 &

                $course_chapter_after = str_replace("&amp;","&", $value['src']);
                $info['chapter'][$key]['src'] = $course_chapter_after;//课件地址
                $header_array = get_headers($value['src'], true);
                $size = $header_array['Content-Length'];
                $info['chapter'][$key]['size'] = $size;//章节视频大小
                $info['chapter'][$key]['name'] = $value['name'];//章节名称
                //$info['chapter'][$key]["time_percent"] = 0;
                $cwhere["user_id"] = $user_id;
                $cwhere["course_id"] = $course_id;
                $cwhere["project_id"] = $project_id = $project_id ? $project_id : 0;
                $cwhere["name"] = $value["name"];
                $chapter = M("course_chapter")->where($cwhere)->limit(1)->find();
                if($chapter){
                    if($info['chapter'][$key]['type'] == 1){
                        $info['chapter'][$key]["time_percent"] = $chapter["time_percent"];//百分比进度
                        $info['chapter'][$key]["timeLen"] = $chapter["timelen"];//时长
                    }else if($info['chapter'][$key]['type'] == 2){
                        $info['chapter'][$key]["time_percent"] = 100;
                        $info['chapter'][$key]["timeLen"] = 100;
                    }else if($info['chapter'][$key]['type'] == 3){
                        $info['chapter'][$key]["time_percent"] = 0;
                        $info['chapter'][$key]["timeLen"] = 0;
                    }
                }else{
                    if($info['chapter'][$key]['type'] == 1){
                        $info['chapter'][$key]["time_percent"] = 0;
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
        }else{
            $where['b.status'] = 1;
            $where['a.user_id'] = $user_id;
            $where['a.project_id'] = $project_id;
            $info = M('CourseRecord a')
            	->field('b.id,b.chapter')
            	->join('LEFT JOIN __COURSE__ b ON a.course_id = b.id')
            	->where($where)
            	->find();

        }
        if($info['chapter']){
           return $this->success(1000,'获取数据成功',$info['chapter']);
       }else{
           return $this->error(1000,'暂无数据返回');
       }
    }


    /**
     * 我的课程-课程详情-获取评论列表
     * @$course_id  课程id
     * @$userId   用户id
     * @$page    分页参数
     */
    public function getCourseComment($course_id,$userId,$page){

        $pageNum = 15;
        $start = ($page - 1) * $pageNum;
        $end = $pageNum;
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "id,lecturer_id,user_id,comment_content,to_char(comment_time,'YYYY-MM-DD HH24:MI:SS') as comment_time,state,pid";
        }else{
            $field = "*";
        }
        $comList = M("colligate_comment")
            ->where("course_id=".$course_id." AND pid=0")
            ->field($field)
            ->order("comment_time DESC")->limit($start, $end)->select();
        //获取课程综合评价和数量
        $condition['course_score'] = array('EGT',0);
        $condition['course_id'] = $course_id;
        $where['course_id'] = $course_id;
        $where['pid'] =0;
       // $where['comment_content'] = array('neq','');
        //课程的综合评分（平均值）
        $course_evaluation = M("Course_score")->where($condition)->AVG('course_score');
        //课程的评价数量
        $courseEvaluationCount = M("Colligate_comment")->where($where)->count('id');
        $course_evaluation = ceil($course_evaluation);
        //获取子评论/回复
        foreach ($comList as $key=>$value){
            $user = M("users")->field("username,avatar")->where("id=".$value["user_id"])->find();
            $comList[$key]["username"] = $user["username"];
            $comList[$key]["avatar"] = $user["avatar"];
            $comList[$key]["course_id"] =$course_id;
            $comList[$key]["comment_content"] = str_replace('&nbsp;','',strip_tags($value["comment_content"]));
            $zan = M("course_praise")->where("praise=1 AND id=".$value["id"])->count();
            $comList[$key]["zan"] = $zan;//总赞
            $zanStatus = M("course_praise")->where("id=".$value["id"]." AND user_id=".$userId)->getField('praise');
            if($zanStatus == 0){
                $comList[$key]["zan_status"] = 0;//我是否赞过  1已赞 0未赞
            }else{
                $zanStatus = $zanStatus[0]["praise"] + 0;
                $comList[$key]["zan_status"] = 1;//我是否赞过  1已赞 0未赞
            }

            //是否可删除
            if($userId == $value["user_id"]){
                $comList[$key]["del_status"] = 1;
            }else{
                $comList[$key]["del_status"] = 0;
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
                        $subUser = M("users")->field("username,avatar")->where("id=".$sv["user_id"])->find();
                        $sonCon[$sk]["username"] = $subUser["username"];
                        $sonCon[$sk]["avatar"] = $subUser["avatar"];
                        $sonCon[$sk]["comment_content"] = str_replace('&nbsp;','',strip_tags($sv["comment_content"]));
                        $userCache[$sv["id"]] = $subUser["username"];

                        //是否可删除
                        $sonCon[$sk]["del_status"] = 0;
                        if($userId == $sv["user_id"]){
                            $sonCon[$sk]["del_status"] = 1;
                        }
                        if($sv["pid"] != $value["id"]){
                            $sonCon[$sk]["reply_user"] = $userCache[$sv["pid"]];
                        }
                        $comList[$key]["sub_list"] = $sonCon;//$sonCon;
                    }
                }else{
                    $comList[$key]["sub_list"] = array();
                }
                //统计所有子评论条数
                $comList[$key]["sub_total"] = count($sonCon);
            }
        }
        //查询该用户是否对该课程评分过，有is_mark=1,否则is_mark=0
        $mark = M('Course_score')->where(array('user_id'=>$userId,'course_id'=>$course_id))->find();
        if($mark){
            $data['is_mark'] = 1;
        }else{
            $data['is_mark'] = 0;
        }
        $data['courseColligateEvaluation'] = $course_evaluation;
        $data['courseEvaluationCount'] = $courseEvaluationCount;
        $data['comment'] = $comList;
        if($comList){
            return $this->success(1000,'获取数据成功',$data);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }

    /*
     * 获取回复评论（页面用于数据实时刷新）(互动消息课程)
     *
     */
    public function getChildComment($id,$type,$userId){

        if($id === ''){
            return $this->error(1023,'缺少回复评论id');
        }
        if($type == 1){
            //主评论
            //点击工作圈列表评论，传主评论id
            $data = M("Colligate_comment")->where(array("id" => $id))->limit(1)->find();
            //获取发布评论人的头像和用户名
            $user = M("Users")->field("username,avatar")->where(array('id' => $data["user_id"]))->limit(1)->find();
            $comList["id"] = $data["id"];
            $comList["user_id"] = $data["user_id"];
            $comList["course_id"] = $data["course_id"];
            $comList["comment_content"] = str_replace('&nbsp;','',strip_tags($data["comment_content"]));
            $comList["comment_time"] = $data["comment_time"];
            $comList["pid"] = $data["pid"];
            $comList["username"] = $user["username"];
            $comList["avatar"] = $user["avatar"];
            $zan = M("Course_praise")->where("praise=1 AND id=".$data["id"])->count();
            $comList["zan"] = $zan;//总赞
            $zanStatus = M("Course_praise")->where("id=".$data["id"]." AND user_id=".$data["user_id"])->limit(1)->select();
            if(!$zanStatus){
                $zanStatus = 0;
            }else{
                $zanStatus = $zanStatus["praise"] + 0;
            }
            $comList["zan_status"] = $zanStatus;//我是否赞过  1已赞 0未赞

            //是否可删除
            $comList["del_status"] = 0;
            if($userId == $data["user_id"]){
                $comList["del_status"] = 1;
            }

            $subList = array();
            $pids = self::getCommentChild($data["id"], $data["id"].",");
            $pids = substr($pids, 0, -1);

        }else{
            //接收子评论id，递归查找父级id
            $pid = M("Colligate_comment")->where(array("id" => $id))->limit(1)->getField('pid');

            //根据子类id查询第一级id
            $infoId = $this->findCourseParentId($pid);
            $info = M("Colligate_comment")->where(array("id" => $infoId))->limit(1)->find();
            if($info == null){
                return $this->error(1023,'评论id参数有误');
            }
            //获取子评论/回复
            //获取该子评论相对应的主评论人的头像和用户名
            $user = M("Users")->field("username,avatar")->where(array('id' => $info["user_id"]))->find();
            $comList["id"] = $info["id"];
            $comList["user_id"] = $info["user_id"];
            $comList["course_id"] = $info["course_id"];
            $comList["comment_content"] = str_replace('&nbsp;','',strip_tags($info["comment_content"]));
            $comList["comment_time"] = $info["comment_time"];
            $comList["pid"] = $info["pid"];
            $comList["username"] = $user["username"];
            $comList["avatar"] = $user["avatar"];
            $zan = M("Course_praise")->where("praise=1 AND id=".$info["id"])->count();
            $comList["zan"] = $zan;//总赞

            $zanStatus = M("Course_praise")->where("id=".$info["id"]." AND user_id=".$info["user_id"])->limit(1)->select();
            if(!$zanStatus){
                $zanStatus = "0";
            }else{
                $zanStatus = $zanStatus["praise"] + 0;
            }
            $comList["zan_status"] = $zanStatus;//我是否赞过  1已赞 0未赞

            //是否可删除
            $comList["del_status"] = 0;
            if($userId == $info["user_id"]){
                $comList["del_status"] = 1;
            }

            $subList = array();
            $pids = self::getCommentChild($info["id"], $info["id"].",");
            $pids = substr($pids, 0, -1);
        }
        if($pids){
            $sonCon = M("Colligate_comment")->where("pid in (".$pids.")")->select();
            if($sonCon){
                $userCache = array();
                foreach ($sonCon as $sk=>$sv){
                    //上一条评论的用户的头像和用户名
                    $subUser = M("users")->field("username,avatar")->where("id=".$sv["user_id"])->limit(1)->select();
                    //获取子评论上一条评论的用户名
                    $sonCon[$sk]["username"] = $subUser[0]["username"];
                    $sonCon[$sk]["avatar"] = $subUser[0]["avatar"];
                    $userCache[$sv["id"]] = $subUser[0]["username"];;

                    //是否可是我的工作圈显示删除按钮
                    $sonCon[$sk]["del_status"] = 0;
                    if($userId == $sv["user_id"]){
                        $sonCon[$sk]["del_status"] = 1;
                    }
                    if($sv["pid"] != $info["id"]){
                        $sonCon[$sk]["reply_user"] = $userCache[$sv["pid"]];;
                    }
                }
                $comList["child"] = $sonCon;

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

    /**
     * 对讲师、课程的评价
     * $course_id 课程id
     */
     public function doComment($post,$user_id,$is_mark){
             //查询课程指定的讲师
             $lecturer_id = M('Course')->where(array('id'=>$post['course_id']))->getField('lecturer');
             if($lecturer_id){
                 $post['lecturer_id'] = $lecturer_id;
             }else{
                 $post['lecturer_id'] = 0;
             }
             if ($is_mark == 0) {
                 //没有评过星级的
                 $cdata['user_id'] = $user_id;
                 $cdata['course_id'] = $post['course_id'];
                 $cdata['lecturer_id'] = 0;
                 $cdata['comment_content'] = $post['comment_content'];
                 $cdata['comment_time'] = date('Y-m-d H:i:s', time());
                 $cdata['pid'] = 0;
                 $cdata['state'] = 0;
                 $array['user_id'] = $user_id;
                 $array['course_id'] = $post['course_id'];
                 $array['lecturer_id'] = $post['lecturer_id'];
                 $array['lecturer_score'] = $post['lecturer_score'];
                 $array['course_score'] = $post['course_score'];
                 $array['score_time'] = date('Y-m-d H:i:s', time());;
                 $where['user_id'] = $user_id;
                 $where['course_id'] = $array['course_id'];
                // $result = M('Course_score')->where($where)->find();
                 	if(strtolower(C('DB_TYPE')) == 'oracle'){
	                    $cdata['id'] = getNextId('colligate_comment');
                        $cdata['comment_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
	                }
                     $info = M('ColligateComment')->data($cdata)->add();
                     if(strtolower(C('DB_TYPE')) == 'oracle'){
                         $array['id'] = getNextId('course_score');
                         $array['score_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                     }
                     $infop = M('Course_score')->data($array)->add();
                     //查询对课程的平均评分
                     $course_score = M('course_score')->where(array('course_id' => $post['course_id']))->AVG('course_score');
                     $course_score = ceil($course_score);
                     M('course')->where(array('id' => $post['course_id']))->save(array('score' => $course_score));
                     if($info && $infop){
                         return true;
                     }else{
                         return false;
                     }

             } else if ($is_mark == 1) {
                 //评过星级的
                $data['user_id'] = $user_id;
                $data['course_id'] = $post['course_id'];
                $data['lecturer_id'] = 0;
                $data['comment_content'] = $post['comment_content'];
                $data['comment_time'] = date('Y-m-d H:i:s', time());
                $data['pid'] = 0;
                $data['state'] = 0;
				if(strtolower(C('DB_TYPE')) == 'oracle'){
                	$data['id'] = getNextId('colligate_comment');
                	$data['comment_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                }
                $info = M('ColligateComment')->data($data)->add();
                return $info;
             }
       /*  }else{
             $cdata['user_id'] = $user_id;
             $cdata['course_id'] = $post['course_id'];
             $cdata['lecturer_id'] = $post['lecturer_id'];
             $cdata['comment_content'] = $post['comment_content'];
             $cdata['comment_time'] = date('Y-m-d H:i:s',time());
             $cdata['pid'] = 0;
             $cdata['state'] = 0;
             $array['user_id'] = $user_id;
             $array['course_id'] = $post['course_id'];
             $array['lecturer_id'] = $post['lecturer_id'];
             $array['lecturer_score'] = $post['lecturer_score'];
             $array['course_score'] = $post['course_score'];
             $array['score_time'] = date('Y-m-d H:i:s',time());
             $where['user_id'] = $user_id;
             $where['course_id'] = $array['course_id'];
             $result = M('Course_score')->where($where)->find();
             if($result){
                 return false;
             }else{
             	if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $cdata['id'] = getNextId('colligate_comment');
                    $cdata['comment_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                }
                 $info = M('ColligateComment')->data($cdata)->add();
                 if(strtolower(C('DB_TYPE')) == 'oracle'){
                     $array['id'] = getNextId('course_score');
                     $array['score_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                 }
                 $infop = M('Course_score')->data($array)->add();
                 //查询对课程的平均评分
                 $course_score = M('course_score')->where(array('course_id' => $post['course_id']))->AVG('course_score');
                 $course_score = ceil($course_score);
                 M('course')->where(array('id' => $post['course_id']))->setField('score',$course_score);
                 if($info && $infop){
                     return true;
                 }else{
                     return false;
                 }
             }*/
     }


    /**
     * 评论或回复评论
     */
    public function addComment($post,$userId){
        $data['pid'] = $post['id'];
        $data['user_id'] = $userId;
        $data['course_id'] = $post['course_id'];
        $data['comment_content'] = $post['comment_content'];
        $data['state'] = 0;
        $data['lecturer_id'] = 0;
        if($data['pid']== '' || $data['course_id'] == ''){
            $this->error(1030,'回复评论id参数有误');
        }
        if($data['course_id'] == '' || $data['course_id'] == 0){
            $this->error(1030,'课程id参数有误');
        }
        if(empty($data['comment_content'])){
            return $this->error(1030,'请输入内容');
        }
        $data['comment_time'] = date('Y-m-d H:i:s',time());
		if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('colligate_comment');
            $data['comment_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        $id = M('ColligateComment')->data($data)->add();
        if($id){
            $info = M('ColligateComment')->where(array('id'=>$id))->find();
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
        $resp = M("colligate_comment")->where(array("id" => $post['id'],'course_id' => $post['course_id']))->limit(1)->delete();
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
     *我的课程-课程详情-点赞/取消点赞
     * $user_id 评论者id
     * $course_id 课程course_id
     *
     */
    public function doPraise($get,$user_id){
        $CourseComment = M('CoursePraise');
        $data['user_id'] = $user_id;
        $data['id'] = $get['id'];
        $type =  $get['type'];
        if(isset($type) && ($type == 0 || $type == 1)){
            if($type == 0){//取消点赞
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
                if($res['praise'] == null){
                    $data['praise'] = 1;
                    $data['praise_time'] = date('Y-m-d H:i:s',time());
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
                       // $data['id'] = getNextId('course_praise');
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
            return $this->error(1023,'参数有误');
        }
    }


    /**************************************************兼容oracle***********************************************************/
    /**
     * 加入我的课程
     * @ $course_id 课程id
     */
    public function addMyCourse($course_id,$user_id){
       if($course_id == '' || $course_id < 1){
           return $this->error(1030,'课程id参数有误');
       }else{
           $res = M('CourseRecord')->where(array('user_id'=>$user_id,'course_id'=>$course_id))->find();
           if(!$res){
               if(strtolower(C('DB_TYPE')) == 'oracle'){
                   $insertData['id'] = getNextId('course_record');
                   $insertDatas['id'] = getNextId('attendance');
               }
               $insertData['user_id'] = $user_id;
               $insertData['course_id'] = $course_id;
               $info = M('CourseRecord')->data($insertData)->add();
               //type为2是移动端的数据
               $insertDatas['user_id'] = $user_id;
               $insertDatas['course_id'] = $course_id;
               $insertDatas['type'] = 2;
               $infop = M('Attendance')->data($insertDatas)->add();
               if($info && $infop){
                   return $this->success(1000,'添加成功');
               }else{
                   return $this->error(1030,'操作失败');
               }
           }else{
               return $this->error(1030,'已经添加过了');//已经加入过我的课程了
           }
       }
    }
   

    /**执行操作
     * 我的课程-课程详情-新建笔记
     * 
     */
    public function executeCreateNote($post,$user_id){

        if(!empty($post['id'])){
            $data['course_id'] = $post['id'];
            $data['user_id'] = $user_id;
            $data['note_content'] = $post['content'];
            if(empty($data['note_content'])){
                return $this->error(1023,'缺少必要参数');
            }
            if(empty($post['is_public'])){
                $data['is_public'] = 1;
            }else{
                $data['is_public'] = $post['is_public'];
            }
            $data['time'] = date('Y:m:d H:i:s',time());
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
               $data['id'] = getNextId('course_note');
               $data['time'] = array('exp',"to_date('".date('Y:m:d H:i:s')."','yy:mm:dd hh24:mi:ss')");
           }
            $info = M('CourseNote')->data($data)->add();
            if($info){
                //新建笔记触发获取积分
                $res = D('Trigger')->intergrationTrigger($user_id,10);
                return $this->success(1000,'操作成功',array('id'=>$info));

            }else{
                return $this->error(1030,'操作失败');
            }
        }else{
            return $this->error(1023,'缺少必要参数');
        }
    }


    /*
     * 获取我关注的课程
     * $user_id 用户id
     * @return $info
     */
    public function getMyConcern($user_id){
        $where = array(
            'b.user_id' => $user_id,
            'b.care_status' => 1,
        );
        $info = M('Course a')
        	->field('a.id as course_id,a.click_count,a.course_cover,a.course_name,b.care_status')
        	->join('LEFT JOIN __COURSE_CARE__ b ON a.id = b.course_id')
        	->where($where)
        	->select();
       foreach($info as $k => $v){
           //计算课程关注总数
           $caerNum = M('Course_care')
           	->where(array('course_id'=>$v['course_id'],'care_status'=>1))
           	->sum('care_status');
           $info[$k]['care_num'] = $caerNum;
       }
       return $info;
    }


    /*
     * 加入我的关注
     * 检验关注输入参数合法性
     */
    public function checkConcernData($data,$userId){
      if(empty($data['course_id']) || empty($data['care_status'])){
          return $this->error(1023,'缺少必要参数');
      }
        switch($data['care_status']){
            case 2;//取消关注
                $care_status = M('CourseCare')->where(array('user_id'=>$userId,'course_id'=>$data['course_id']))->getField('care_status');
                if($care_status == 2){
                    return $this->error(1030,'已取消过，不能重复操作');die();
                }else{
                    M('CourseCare')->where(array('user_id'=>$userId,'course_id'=>$data['course_id']))->setField('care_status',2);
                    return $this->success(1000,'操作成功',array('care_status'=>2));
                }
                break;

            case 1;//加入关注
                $care_status = M('CourseCare')->where(array('user_id'=>$userId,'course_id'=>$data['course_id']))->getField('care_status');
                if($care_status == 1){
                    return $this->error(1023,'已关注过了');die();
                }elseif($care_status == 2){
                    M('CourseCare')->where(array('user_id'=>$userId,'course_id'=>$data['course_id']))->setField('care_status',1);
                    //加入我的关注触发获取积分
                    $res = D('Trigger')->intergrationTrigger($userId,7);
                    return  $this->success(1000,'操作成功',array('care_status'=>1));
                }else{
                    $data['user_id'] = $userId;
					
					if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $data['id'] = getNextId('course_care');
                    }
                    M('CourseCare')->data($data)->add();
                    //加入我的关注触发获取积分
                    $res = D('Trigger')->intergrationTrigger($userId,7);
                    return  $this->success(1000,'操作成功',array('care_status'=>1));
                }
                break;
         }
    }

    /*
     * 检验资讯表单数据合法性
     * @param  $post
     */
    public function checkData($post,$user_id,$type){
        $data['title'] = $post['heading'];
        $data['content'] = $post['substance'];
        $data['img'] = $post['image'];
        $news = M('News');
        if(empty($data['title'])){
           return  $this->error(1013,'标题不能为空');die();
        }
        if(empty($data['content'])){
            return  $this->error(1013,'内容不能为空');die();
        }
        if(empty($data['img'])){
            $data['img'] = "";
        }
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['user_id'] = $user_id;
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('news');
            $data['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        if($type == 1){
            $msg = $news->data($data)->add();
        }
        if($type == 2){
            $data['id'] = $post['news_id'];
            if(empty($data['id'])){return $this->error(1023,'缺少必要参数');die();}
            if(empty($data['title'])){
                return  $this->error(1013,'标题不能为空');die();
            }
            if(empty($data['content'])){
                return  $this->error(1013,'内容不能为空');die();
            }
            if(empty($data['img'])){
                $data['img'] = "";
            }
            $msg = $news->data($data)->save();
        }
            if($msg){
                $info['id'] = $msg;
                return $this->success(1000,'提交成功',$info);
            }else{
                return $this->error(1030,'操作失败');
            }
        }

    /**
     * 笔记
     * $course_id 课程id
     * $typeId 1我的笔记  2分享笔记
     */
    public function note($course_id,$userId){
        if(empty($course_id)){
            return $this->error(1023,'缺少必要参数');
        }else{
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "a.id,a.user_id,a.project_id,a.course_id,a.note_content,a.is_public,to_char(a.time,'YYYY-MM-DD HH24:MI:SS') as time,b.username,b.avatar";
            }else{
                $field = "a.id,a.user_id,a.project_id,a.course_id,a.note_content,a.is_public,a.time,b.username,b.avatar";
            }
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $fields = "a.id,a.user_id,a.project_id,a.course_id,a.note_content,a.is_public,to_char(a.time,'YYYY-MM-DD HH24:MI:SS') as time";
            }else{
                $fields = "a.id,a.user_id,a.project_id,a.course_id,a.note_content,a.is_public,a.time";
            }
            $info = M('CourseNote a')->where(array('a.course_id'=>$course_id,'a.user_id'=>$userId))->field($fields)->order('a.id DESC')->select();
            $data = M('CourseNote a')
            	->join('LEFT JOIN __USERS__ b ON a.user_id = b.id')
            	->where(array('a.course_id'=>$course_id,'a.is_public'=>1))
            	->field($field)
            	->order('a.id DESC')
            	->select();
           }
        $res = array(
            'myNote' => $info,
            'shareNote' => $data,
        );
        if($res['myNote'] || $res['shareNote']){
            return $this->success(1000,'获取成功',$res);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }


    /*
     * 资讯删除
     */
    public function delete($id){
        if(!empty($id)){
            //将字符串拼装成数组
            $arr_id = explode(',',$id);
            $where['id'] = array('in',$arr_id);
            return $info = M('News')->where($where)->delete();
        }else{
            return $this->error(1023,'缺少必要参数');
        }
    }
    /*
     * 获取用户的系统消息、互动消息
     * @param  $user_id 用户id
     * @param  $user_type 用户id
     * @param  $tabType 0:系统消息列表  1:互动消息列表
     */
    public function getSystemNews($user_id,$page,$tabType){
        if($page < 0){
            return $this->error(1023,'分页参数输入有误');
        }
        $pageNum = 15;
        $start = ($page - 1)*$pageNum;
        $end = $pageNum;
        $where['user_id'] = array('eq',$user_id);
        if($tabType == 0){
            $where['type_id'] = array('in',array(10,11,12));
        }else{
            $where['type_id'] = array('in',array(14));
        }
        
        $info = M('AdminMessage a')
	        ->field("a.id,a.user_id,a.title,to_char(a.contents_time,'YYYY-MM-DD HH24:MI:SS') as contents_time,a.type_id,a.status,a.from_id,a.url")
	        ->where($where)
	        ->limit($start,$end)
	        ->order('contents_time DESC')
	        ->select();
      // dump($info);exit;
        foreach($info as $k => $value){
            if($tabType == 0){
            
        
           /*if($value['type_id'] == 1){
               $info[$k]['newsType'] = '课程制作';
           }
           if($value['type_id'] == 2){
               $info[$k]['newsType'] = '试卷制作';
           }
           if($value['type_id'] == 3){
               $info[$k]['newsType'] = '问卷制作';
           }
           if($value['type_id'] == 4){
               $info[$k]['newsType'] = '授课任务';
           }
           if($value['type_id'] == 5){
               $info[$k]['newsType'] = '成绩发布';
           }
           if($value['type_id'] == 6){
               $info[$k]['newsType'] = '调研结果';
           }*/
        //    if($value['type_id'] == 7){
        //        $info[$k]['newsType'] = '审批任务';
        //    }
           /*if($value['type_id'] == 8){
               $info[$k]['newsType'] = '统计调研';
           }
           if($value['type_id'] == 9){
               $info[$k]['newsType'] = '签到提醒';
           }*/
           if($value['type_id'] == 10){
               $info[$k]['newsType'] = '课程学习';
           }
           if($value['type_id'] == 11){
               $info[$k]['newsType'] = '参加考试';
           }
           if($value['type_id'] == 12){
               $info[$k]['newsType'] = '参与调研';
           }
           /*if($value['type_id'] == 13){
               $info[$k]['newsType'] = '计划总结';
           }*/
           

           }else{  //互动消息
              if($value['type_id'] == 14){
               $info[$k]['newsType'] = '互动消息';
              }
              $from_username = M('users')->where(array('id'=>$value['from_id']))->getField('username');
              $info[$k]['title'] = $from_username.'回复了我';
             
              $allString = $value['url'];
              $searchString = "#c";
              $newString = strstr($allString, $searchString);
              $length = strlen($searchString);
              $cid = substr($newString, $length);
              //查找回复 pid=0 的评论
              $parent = $this->findParent($cid);
              if(!empty($parent)){
                 foreach($parent as $k1=>$v1){
                     $v1['pid'] = 0;
                     $cid = $v1['id'];
                 }
              }

              $info[$k]['cid'] = $cid;
              
           }
             unset($info[$k]['url']);
       }

              
        if($info){
            //查看系统消息触发获取积分
            $res = D('Trigger')->intergrationTrigger($user_id,2);
            return $this->success(1000,'获取数据成功',$info);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
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


   /*
    *更改消息未读状态
    */
    public function changeSystemNews($id,$status){

        if($id === '' || $id < 0){
            return $this->error(1023,'未获取到消息id');
        }

        if($status === '' || $status < 0){
            return $this->error(1023,'消息状态参数必须且为0');
        }

        if($status == 0){
            $res = M('AdminMessage')->where(array('id'=>$id))->setField('status',1);
            if($res){
                return $this->success(1000,'操作成功',array('status'=>1));
            }else{
                return $this->error(1041,'不能重复操作');
            }
        }else{
            return $this->error(1023,'参数有误');
        }
    }

    /**
     * 获取系统消息消息详情
     * $id 消息id
     *
     */
    public function getSystemNewsDetail($id){
        if($id == '' || $id < 0){
            return $this->error(1023,'未获取到消息id');
        }
        $info = M('AdminMessage a')
        	->field('a.id,a.user_id,a.title,a.contents_time,a.type_id,a.status')
        	->where(array('id' => $id,'status'=>1))
        	->find();

        if($info['type_id'] == 1){
            $info['newsType'] = '课程制作';
        }
        if($info['type_id'] == 2){
            $info['newsType'] = '试卷制作';
        }
        if($info['type_id'] == 3){
            $info['newsType'] = '问卷制作';
        }
        if($info['type_id'] == 4){
            $info['newsType'] = '授课任务';
        }
        if($info['type_id'] == 5){
            $info['newsType'] = '成绩发布';
        }
        if($info['type_id'] == 6){
            $info['newsType'] = '调研结果';
        }
        if($info['type_id'] == 7){
            $info['newsType'] = '审批任务';
        }
        if($info['type_id'] == 8){
            $info['newsType'] = '统计调研';
        }
        if($info['type_id'] == 9){
            $info['newsType'] = '签到提醒';
        }
        if($info['type_id'] == 10){
            $info['newsType'] = '课程学习';
        }
        if($info['type_id'] == 11){
            $info['newsType'] = '参加考试';
        }
        if($info['type_id'] == 12){
            $info['newsType'] = '参与调研';
        }
        if($info['type_id'] == 13){
            $info['newsType'] = '计划总结';
        }
        if($info){
            return $this->success(1000,'获取数据成功',$info);
        }else{
            return $this->error(1030,'暂无数据返回');
        }

    }

    /*
     * 互动消息
     */
    public function interactNews($userId,$model,$page){

        if($page < 0){
            return $this->error(1023,'分页参数输入有误');
        }
        $pageNum = 15;
        $start = ($page - 1)*$pageNum;
        $end = $pageNum;
        //工作圈评论
        //查询主评论数据
        $wheres['pid'] = array('eq',0);
        $info = $model->where($wheres)
            ->field('id,content,publish_time,state')->limit($start,$end)->order('publish_time DESC')->select();

        //循环第一次取出第一条主评论的所有子评论
        if(!empty($info)){
            foreach($info as $k => $v){
                $where['cid'] =  $v['id'];
                $where['praise'] =  array('gt',0);
                $pids = self::getFriendCommentChild($v["id"], $v["id"].",");
                $pids = substr($pids, 0, -1);
                $tiaojian['pid'] = array('in',$pids);
                $msg = $model
                	->where($tiaojian)
                    ->field('id,content,publish_time,state,pid,user_id')
                    ->limit($start,$end)
                    ->order('publish_time DESC')
                    ->select();
                //查询主评论点赞总数
                foreach($msg as $i => $j){
                    $ret[$i]['praiseTotal'] = M('Friends_praise')->where($where)->sum('praise');
                    $ret[$i]['praiseTotal'] = $ret[$i]['praiseTotal'] ? $ret[$i]['praiseTotal'] : 0;
                    $msg[$i]['type'] = 1;//表示工作圈
                    $msg[$i]["touser_id"] =  M("FriendsCircle")->where(array("id" => $j["pid"]))->getField('user_id');
                    $msg[$i]['praiseTotal'] = $ret[$i]['praiseTotal'];
                }
                foreach($msg as $m => $n){
                    if($n['touser_id'] != $userId){
                        unset($msg[$m]);
                    }
                }
                $mseeage[$k]['sub'] = $msg;
            }

            //把三维数组循环拼装成二维数组
            foreach($mseeage as $key => $val){
                foreach($val['sub'] as $x => $y){
                    $new[] = $y;
                }
            }
        }else{
            $new = array();
        }
        //课程评论动态
        //查询主评论数据
        $condition['pid'] = array('eq',0);
        $infof = M('Colligate_comment')
        	->where($condition)
            ->field('id,comment_content as content,comment_time as publish_time,state')
            ->limit($start,$end)
            ->order('publish_time DESC')
            ->select();
        //循环第一次取出第一条主评论的所有子评论

        if(!empty($infof)){
            foreach($infof as $ks => $vs){
                $conditions['id'] =  $vs['id'];
                $conditions['praise'] =  array('gt',0);
                $pidsc = $this->getCommentChild($vs["id"], $vs["id"].",");
                $pidsc = substr($pidsc, 0, -1);
                $tiaojians['pid'] = array('in',$pidsc);
                $msgs = M('Colligate_comment')
                	->where($tiaojians)
                    ->field('id,comment_content as content,comment_time as publish_time,state,user_id,pid')
                    ->limit($start,$end)
                    ->order('publish_time DESC')
                    ->select();
                //查询主评论点赞总数
                foreach($msgs as $is => $js){
                    $rets[$is]['praiseTotal'] = M('Course_praise')->where($conditions)->sum('praise');
                    $rets[$is]['praiseTotal'] = $rets[$is]['praiseTotal'] ? $rets[$is]['praiseTotal'] : 0;
                    $msgs[$is]['type'] = 2;//表示课程
                    $msgs[$is]["touser_id"] =  M("Colligate_comment")->where(array("id" => $js["pid"]))->getField('user_id');
                    $msgs[$is]['praiseTotal'] = $rets[$is]['praiseTotal'];
                }
                foreach($msgs as $ms => $ns){
                    if($ns['touser_id'] != $userId){
                        unset($msgs[$ms]);
                    }
                }
                $mseeages[$ks]['sub'] = $msgs;

            }
            //把三维数组循环拼装成二维数组
            foreach($mseeages as $vals){
                foreach($vals['sub'] as $xs => $ys){
                    $news[] = $ys;
                }
            }
        }else{
            $news = array();
        }

        if(empty($new)){
            $data = $news;
        }
        if(empty($news)){
            $data = $new;
        }
        if(!empty($new) && !empty($news)){
            $data = array_merge_recursive($new,$news);
        }

        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'publish_time',       //排序字段
        );
        $arrSort = array();
        foreach($data AS $uniqid => $row){
            foreach($row AS $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $data);
        }
        if($data){
            return $this->success(1000,'获取数据成功',$data);
        }else{
            return $this->error(1030,'暂无数据返回');
        }
    }

    /*
    *更改互动消息未读状态为已读
    */
    public function changeInteractNews($id,$state,$type){

        if($id === '' || $id < 0){
            return $this->error(1023,'未获取到消息id');
        }
        if($state === '' || $state < 0){
            return $this->error(1023,'消息状态不能为空');
        }
        if($state == 0){
            if($type == 1){
                $res = M('Friends_circle')->where(array('id'=>$id))->setField('state',1);
                if($res){
                    return $this->success(1000,'操作成功',array('state'=>1));
                }else{
                    return $this->error(1041,'不能重复操作');
                }
            }elseif($type ==2){
                $res = M('Colligate_comment')->where(array('id'=>$id))->setField('state',1);
                if($res){
                    return $this->success(1000,'操作成功',array('state'=>1));
                }else{
                    return $this->error(1041,'不能重复操作');
                }
            }
        }else{
            return $this->error(1023,'参数有误');
        }
    }


    /*
     * 互动消息详情
     */
    public function getInteractNewsDetail($id){
        if($id < 0 || $id === ''){
            return $this->error(1023,'缺少消息id');
        }
       $res =  M('Friends_circle')->where(array('id' => $id))->find();
        if($res){
            return $this->success(1000,'获取数据成功',$res);
        }else{
            return $this->success(1030,'赞无数据返回');
        }
    }

   /**
   * 我的学分
   */
    public function  credit($userId,$start_page,$pageNum){

       //统计总学分
        $yearData = $this->getStudyCredit(1,$userId);
        $total_credit = $yearData["totalScore"];//年度总学分
       // $total_credit =$model->where(array('user_id'=>$userId))->sum('credit');
        $total_credit = $total_credit ? $total_credit : 0;
        //查询学分来源
       /* if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "id,add_score,add_time,apply_title,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time";
        }else{
        	$field = "create_time";
        }*/
       /* $info = $model
        	->where(array('user_id'=>$userId))
            ->field($field . ',id,typeid as type,credit as score,source_id as course_id,user_id')
            ->select();*/

       /* foreach($info as $k=>$v){
            $info[$k]['source'] = $this->centerStudy($v['type'],$v['course_id']);
            $info[$k]['add_time'] = $v['create_time'];
            $info[$k]['score'] = $v['score'] ? $v['score'] : 0;
        }*/
        $field = "id,add_score as score,add_time,apply_title as source";
        //查询我获取的积分记录
        $info = M('credits_apply')->field($field)->where(array('user_id' => $userId))->limit($start_page,$pageNum)->order('add_time DESC')->select();
        foreach($info as $k=>$v) {
            $info[$k]['source'] = $v['source'];
            $info[$k]['add_time'] = date("Y-m-d H:i:s", $v['add_time']);
            $info[$k]['score'] = $v['score'] ? $v['score'] : 0;
        }
        return $data = array(
            'total_score' => $total_credit,
            'data' => $info
        );
    }


    /*
    * 学习目标
    */
    public function learningTarget($typeId,$page,$userId){
        if($typeId == ''){
            return $this->error(1024,'不合法参数');
        }
        $data = $this->getLearningTarget($typeId,$page,$userId);
        if($data){
            //查看学习目标触发获取积分
            D('Trigger')->intergrationTrigger($userId,5);
            return $this->success(1000,'获取成功',$data);
        }else{
            return $this->error(1040,'暂无数据返回');
        }
    }


    public function getLearningTarget($typeId,$page,$user_id){
        $param["page"] = $page;
        $param["type"] = $typeId;

        if(!$page["page"]) $param["page"] = 1;
        if(!$param["pageLen"]) $param["pageLen"] = 15;
        $start = ($param["page"] - 1) * $param["pageLen"];

        //我的必修课程--培训课程（线上+线下）
        if($param["type"] == 1){
        	$where = array();
        	$where["a.user_id"] = $user_id;
        	$where["a.sign_up"] = array("eq",1);
        	$where["b.type"] = array("in","0,4");//进行中或已完成
        	$where["_string"] = "c.start_time >= to_date('".date("Y-01-01 00:00:00")."','yyyy-mm-dd hh24:mi:ss')";
        	
        	if(strtolower(C('DB_TYPE')) == 'oracle'){
        		$field = "course_names,c.credit as p_credit,c.course_id,c.project_id,
        		to_char(c.start_time,'YYYY-MM-DD HH24:MI:SS') as n_start_time,to_char(c.end_time,'YYYY-MM-DD HH24:MI:SS') as n_end_time";
        	}else{
        		$field = "course_names,c.credit as p_credit,c.course_id,c.project_id";
        	}
        	$results = M("designated_personnel a")
        		->join("join __ADMIN_PROJECT__ b on a.project_id=b.id")
        		->join("join __PROJECT_COURSE__ c on a.project_id=c.project_id")
        		->where($where)->order("c.start_time desc")
                //->limit($start, $param["pageLen"])
                ->field($field)
                ->select();
        	
        	$list = array();
        	$total_time = 0;//培训项目总学时
        	$total_credit = 0;//获取到的学分
        	$finishList = array();//已完成课程列表
        	$finishKey = 0;
        	foreach ($results as $key=>$proValue){
        		$thisCourse = M("course")->where("id=".$proValue["course_id"])->find();
        		if($proValue["course_names"]){
        			$results[$key]['course_name'] = $proValue["course_names"];
        		}else{
        			$results[$key]['course_name'] = $thisCourse["course_name"];
        		}
        		
        		$start_time = strtotime($proValue['n_start_time']) - time();//大于0表示待考试
        		$end_time = strtotime($proValue['n_end_time']) - time();//小于0表示逾期（已结束）
        		if($start_time > 0){
        			$results[$key]['_status'] = 1;//未开始
        			continue; 			
        		}else if($end_time > 0  && $start_time < 0){
        			$results[$key]['_status'] = 2;//学习中
        		}else if($end_time < 0){
        			$results[$key]['_status'] = 4;//已过期/已逾期
        		}
        		
        		if($thisCourse){
	        		if($thisCourse["course_way"] == 1){
	        			//面授课程
	        			$where5["a.course_id"] = $proValue["course_id"];
	        			$where5["b.project_id"] = $proValue["project_id"];
	        			$attendance_course = M("attendance_course a")->field("a.attendance_project_id")
	        			->join("join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id")->where($where5)->find();
	        			if($attendance_course){
	        				//考勤开启
	        				$awhere["user_id"] = $user_id;
	        				$awhere["status"] = array("in", "1,2");
	        				$awhere["attendance_project_id"] = $attendance_course["attendance_project_id"];
	        				$attendance = M("attendance")->where($awhere)->find();
	        				if($attendance){
	        					$results[$key]['_status'] = 3;//已完成
	        					$total_time += (int)$thisCourse["course_time"];
	        					$total_credit += $proValue["p_credit"];
	        					$finishList[$finishKey] = $results[$key];
	        					$finishKey ++;
	        				}
	        			}else{
	        				$results[$key]['_status'] = 3;//已完成
	        				$total_time += (int)$thisCourse["course_time"];
	        				$total_credit += $proValue["p_credit"];
	        				$finishList[$finishKey] = $results[$key];
	        				$finishKey ++;
	        			}
	        		}else{
	        			//在线课程
	        			$whereCT["user_id"] = $user_id;
	        			$whereCT["project_id"] = $proValue["project_id"];
	        			$whereCT["source_id"] = $proValue["course_id"];
	        			$whereCT["typeid"] = 4;//必修课程
	        			$upCourse = M("center_study")->field("credit,source_id,project_id")->where($whereCT)->find();
	        			if($upCourse){
	        				$results[$key]['_status'] = 3;//已完成
	        				$total_time += (int)$thisCourse["course_time"];
	        				$total_credit += $proValue["p_credit"];
	        				$finishList[$finishKey] = $results[$key];
	        				$finishKey ++;
	        			}
	        		}
        		}
        	}
        	
        	//获取课程学分目标，目标年度学分完成率（当前时间段学分 / 学分目标）
        	$myTissue = M("tissue_group_access")->where("user_id=".$user_id)->find();
        	$myGole = 0;
        	if($myTissue){
        		$wheretl["tissue_id"] = $myTissue["tissue_id"];
        		$wheretl["job_id"] = $myTissue["job_id"];
        		$wheretl["typeid"] = 0;//0-必修,1-选修,2-修读，3-积分,4-学分
        		$toolGoal = M("tool_learning")->where($wheretl)->find();
        	
        		//按年统计，所有月份相加
        		$myGole += $toolGoal["january"] + $toolGoal["february"] + $toolGoal["march"] +$toolGoal["april"]+$toolGoal["may"]+$toolGoal["june"];
        		$myGole += $toolGoal["july"]+$toolGoal["august"]+$toolGoal["september"]+$toolGoal["october"]+$toolGoal["november"]+$toolGoal["december"];
        		$myGole = ceil($myGole);
        	}

        	$total_time = round($total_time / 60, 2);//学时分钟转为小时
        	
        	$finishRate = 0;
        	if($myGole > 0){
        		$finishRate = round($total_time / $myGole, 2) * 100;
        	}
        	
            $data = array('list' => $results, "total"=>$total_time, "_total"=>$myGole, "goalRate"=>$finishRate, "finishList"=>$finishList);
        }elseif($param["type"] == 2){
        	//选修课时--只有在线课
        	$where2["user_id"] = $user_id;
        	$list = M("course_record")->where($where2)
                //->limit($start, $param["pageLen"])
                ->select();
        	$total_time = 0;//选修总学时
        	$finishList = array();//已完成课程列表
        	$finishKey = 0;
        	foreach ($list as $key=>$value){
        		$thisCourse = M("course")->field("course_name,course_time")->where("id=".$value["course_id"])->find();
        		if(!$thisCourse){
	        		$list[$key]['course_name'] = "课程已删除或不存在";
        			continue;
        		}
        		$list[$key]['course_name'] = $thisCourse["course_name"];
        		
        		$where3["user_id"] = $user_id;
        		$where3["typeid"] = 5;
        		$where3["source_id"] = $value["course_id"];
        		$where3["_string"] = " create_time >to_date('".date("Y-01-01 00:00:00")."','yyyy-mm-dd hh24:mi:ss')";
        		$thisStudy = M("center_study")->field("credit,source_id")->where($where3)->find();
        		if($thisStudy){
        			$total_time += $thisCourse["course_time"];
        			$list[$key]['_status'] = 3;//已结束
        			$finishList[$finishKey] = $list[$key];
	        		$finishKey ++;
        			continue;
        		}
        		
        		$where4["user_id"] = $user_id;
        		$where4["project_id"] = 0;
        		$where4["course_id"] = $value["course_id"];
        		$where4["_string"] = " create_time >to_date('".date("Y-01-01 00:00:00")."','yyyy-mm-dd hh24:mi:ss')";
        		$chapter = M("course_chapter")->where($where4)->find();
        		if($chapter){
        			$list[$key]['_status'] = 2;//学习中
        		}else{
        			$list[$key]['_status'] = 1;//未开始
        		}
        	}
        	
        	//获取课程学分目标，目标年度学分完成率（当前时间段学分 / 学分目标）
        	$myTissue = M("tissue_group_access")->where("user_id=".$user_id)->find();
        	$myGole = 0;
        	if($myTissue){
        		$wheretl["tissue_id"] = $myTissue["tissue_id"];
        		$wheretl["job_id"] = $myTissue["job_id"];
        		$wheretl["typeid"] = 1;//0-必修,1-选修,2-修读，3-积分,4-学分
        		$toolGoal = M("tool_learning")->where($wheretl)->find();
        	
        		//按年统计，所有月份相加
        		$myGole += $toolGoal["january"] + $toolGoal["february"] + $toolGoal["march"] +$toolGoal["april"]+$toolGoal["may"]+$toolGoal["june"];
        		$myGole += $toolGoal["july"]+$toolGoal["august"]+$toolGoal["september"]+$toolGoal["october"]+$toolGoal["november"]+$toolGoal["december"];
        		$myGole = ceil($myGole);
        	}
        	
        	$total_time = round($total_time / 60, 2);//学时分钟转为小时
        	
        	$finishRate = 0;
        	if($myGole > 0){
        		$finishRate = round($total_time / $myGole, 2) * 100;
        	}

            $data = array('list' => $list, "total"=>$total_time, "_total"=>$myGole, "goalRate"=>$finishRate, "finishList"=>$finishList);
        }else{
        	$data1 = self::getLearningTarget(1,$page,$user_id);
        	$data2 = self::getLearningTarget(2,$page,$user_id);
        	
        	$list = array();
        	$key = 0;
        	foreach ($data1["finishList"] as $value){
        		$list[$key] = $value;
        		$key ++;
        	}
        	foreach ($data2["finishList"] as $value){
        		$list[$key] = $value;
        		$key ++;
        	}
        	
        	//获取课程学分目标，目标年度学分完成率（当前时间段学分 / 学分目标）
        	$myTissue = M("tissue_group_access")->where("user_id=".$user_id)->find();
        	$myGole = 0;
        	if($myTissue){
        		$wheretl["tissue_id"] = $myTissue["tissue_id"];
        		$wheretl["job_id"] = $myTissue["job_id"];
        		$wheretl["typeid"] = 2;//0-必修,1-选修,2-修读，3-积分,4-学分
        		$toolGoal = M("tool_learning")->where($wheretl)->find();
        	
        		//按年统计，所有月份相加
        		$myGole += $toolGoal["january"] + $toolGoal["february"] + $toolGoal["march"] +$toolGoal["april"]+$toolGoal["may"]+$toolGoal["june"];
        		$myGole += $toolGoal["july"]+$toolGoal["august"]+$toolGoal["september"]+$toolGoal["october"]+$toolGoal["november"]+$toolGoal["december"];
        		$myGole = ceil($myGole);
        	}
        	
        	$finish_course = count($list);
        	$finishRate = 0;
        	if($myGole > 0){
        		$finishRate = round($finish_course / $myGole, 2) * 100;
        	}
        	
            $data = array('list' => $list, "total"=>$finish_course, "_total"=>$myGole, "goalRate"=>$finishRate);
        }
        
        return $data;
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
     * 获取学员某门课程的学习进度
     * @param $user_id 用户id
     * @param $cid 课程id
     * @param $pid 项目id
     */
    public function getCoursePer($user_id,$cid,$pid=false){
        if($pid){
            $count = M('course_chapter')->where(array('user_id'=>$user_id,'course_id'=>$cid,'project_id'=>$pid))->count();
            $finishedNum = M('course_chapter')
                ->where(array('user_id'=>$user_id,'course_id'=>$cid,'project_id'=>$pid,'status'=>3))
                ->count();
            $per = floor( ($finishedNum / $count) * 100 );
            return $per;
        }else{
            $count = M('course_chapter')->where(array('user_id'=>$user_id,'course_id'=>$cid))->count();
            $finishedNum = M('course_chapter')
                ->where(array('user_id'=>$user_id,'course_id'=>$cid,'status'=>3))
                ->count();
            $per = floor( ($finishedNum / $count) * 100 );
            return $per;
        }
    }

    /**
     * 获取课程信息
     */
    public function getCourse($user_id,$day=false){
        $where['b.user_id'] = $user_id;
        $where['c.status'] = 1;
        $where['c.auditing'] = 1;
        $where['c.course_name'] = array('neq','');
        if($day){
            $where['a.start_time'] = array('egt',$day['start_time']);
            $where['a.end_time'] = array('elt',$day['end_time']);
        }
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$field = "to_char(a.start_time,'YYYY-MM-DD HH24:MI:SS') as start_time";
			$field.= "to_char(a.end_time,'YYYY-MM-DD HH24:MI:SS') as end_time,";
		}else{
			$field = 'a.start_time,a.end_time,';
		}
        $data = M('project_course')
            ->alias('a')
            ->join('left join __DESIGNATED_PERSONNEL__ b on a.project_id=b.project_id')
            ->join('left join __COURSE__ c on a.course_id=c.id')
            ->join('left join __ADMIN_PROJECT__ d on a.project_id=d.id')
            ->join('left join __COURSE_DETAIL__ e on c.id=e.id')
            ->field($field . 'a.project_id,a.course_id,c.course_name,c.course_cover,e.course_intro')
            ->where($where)
            ->select();


        foreach($data  as $k => $v){
            //根据课程id获取本课程学习进度
            $per = $this->getCoursePer($v['user_id'],$v['course_id'],$v['project_id']);
            $data[$k]['per'] = $per;
        }
        return $data;
    }

    /**
     * 记录视频播放时长
     * project_id
     * course_id
     * fileName
     * fileSrc
     * fileType
     * status
     * timeLen
     * time_percent
     */
    public function recordVideoTimeLong($userId,$course_id,$pid,$fileName,$path,$type,$timeLen,$time_percent){
        //项目id
        $pid = $pid ? $pid : 0;
        $cwhere["user_id"] = $userId;
        $cwhere["course_id"] = $course_id;
        $cwhere["project_id"] = $pid;
        $cwhere["name"] = $fileName;
        //查询有没有学习过
        //echo $userId,$course_id,$pid,$fileName,$type,$path,$timeLen,$time_percent;exit;
        $chapter = M("course_chapter")->where($cwhere)->find();
        $data = array();
		if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('course_chapter');
			$data['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
		}else{
			$data["create_time"] = date("Y-m-d H:i:s");
		}
        if($chapter){
            if($type == 1){//说明是视频
                if($time_percent == 100){
                    $data["status"] = 3;
                }else{
                    $data["status"] = 2;
                }
                $data["user_id"] = $userId;
                $data["course_id"] = $course_id;
                $data["project_id"] = $pid;
                $data["name"] = $fileName;
                $data["type"] = $type;
                $data["timeLen"] = $timeLen;
                $data["path"] = $path;
                $data["time_percent"] = $time_percent;
                $res = M("course_chapter")->where($cwhere)->save($data);
                return $res;
            }
        }else if($type == 2 || $type == 3) {//ppt //文档
            $data["user_id"] = $userId;
            $data["course_id"] = $course_id;
            $data["project_id"] = $pid;
            $data["name"] = $fileName;
            $data["path"] = $path;
            $data["type"] = $type;
            $data["status"] = 3;
            $data["timeLen"] = 100;
            $data["time_percent"] = 100;
            $res = M("course_chapter")->add($data);
            return $res;
        }else if($type == 4){//文档
            $data["user_id"] = $userId;
            $data["course_id"] = $course_id;
            $data["project_id"] = $pid;
            $data["name"] = $fileName;
            $data["path"] = $path;
            $data["type"] = $type;
            $data["status"] = 3;
            $data["timeLen"] = 100;
            $data["time_percent"] = 100;
            $res = M("course_chapter")->add($data);
            return $res;
        }
    }


    /*
     * 记录文件下载
     */
    public function recordData($user_id,$course_id,$chapter_id,$project_id,$name,$path,$type,$style){
        $data['user_id'] = $user_id;
        $data['course_id'] = $course_id;
        $data['project_id'] = $project_id;
        $data['chapter_id'] = $chapter_id;
        $data['name'] = $name;
        $data['path'] = $path;
        $data['type'] = $type;
        $data['style'] = $style;
		
        $info = M('File_download')->where($data)->find();
        if($info){
            $result = 2;
        }else{
			if(strtolower(C('DB_TYPE')) == 'oracle'){
                $data['id'] = getNextId('file_download');
				$data['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
			}else{
				$data["create_time"] = date("Y-m-d H:i:s");
			}
            $results = M('File_download')->data($data)->add();
            $result = 1;
        }
        return $result;
    }

    /*
     * 获取文件缓存
     */
    public function getRecordData($user_id){

        $result = M('File_download')->where(array('user_id'=>$user_id))->select();

        foreach($result as $k => $v){
            $img = M('Course')->field('course_cover,course_name')->where(array('id'=>$v['course_id']))->find();
            $result[$k]['course_cover'] = $img['course_cover'];
            $result[$k]['course_name'] = $img['course_name'];
        }
        return $result;
    }


}

