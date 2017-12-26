<?php
namespace Mobile\Controller;

use Think\Controller;
/**
 * @首页公开课
 * @author lizhongjian
 *
 */
class PublicCourseController extends CommonController{

    public function __construct() {
        parent::__construct();
    }

    /**************************兼容oracle版**********************************/
    /**
     * @首页公开课程列表
     * @token       用户身份标识
     * @secret_key  秘钥
     */
    public function homePages(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        //分页参数
        $page = I("get.page",1,'int');
        //搜索条件 课程分类
        $cid = I("get.cid",0,'int');
        //搜索条件 $type 1最新(默认按照id倒序排序),2最热(按照点击数倒序排序),3好评(按照综合评分倒序排序)
        $style = I('get.type',1,'int');
        //搜索关键字 （该参数app端做了转码方式,后台需要进行解码处理）
        $keyword = I("get.keyword",'','trim,htmlspecialchars');
        if($page < 1){
            $this->error(1030,'分页参数有误');
        }
        $pageNum = 10;
        $data = D("PublicCourse")->homePages($userId,$page,$keyword,$pageNum,$cid,$style);
        if($data['code'] == 1000){
            $this->success(1000,$data['message'],$data['data']);
        }else{
            $this->error(1030,$data['message']);
        }
    }


    /**************************兼容oracle版**********************************/
    /**
     * @首页课程(底部导航)
     * @token  用户身份标识
     * @secret_key  秘钥
     */
    public function homePageCourses(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $type = I('get.type',1,'int');
        $page = I('get.page',1,'int');
        $pageLen = 15;
        $result = D('PublicCourse')->homePageCourses($type,$userId,$page,$pageLen);
        if($result['code'] == 1000){
            $this->success($result['code'],$result['message'],$result['data']);
        }else{
            $this->error($result['code'],$result['message']);
        }
    }




    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     * app首页公开课列表课程分类
     */
    public function courseCategory(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataGet();
        $tissue_id = M("tissue_group_access")->where(array('user_id' => $userId))->getField("tissue_id");
        //获取方案ID
        $plan_id = M("sys_tissue")->where("tissue_id=".$tissue_id)->getField("plan_id");
        if(!$plan_id){
            $plan_id = 0;
        }
            $info = D("PublicCourse")->courseCategory($plan_id);
            if(!empty($info[0])){
                $this->success(1000,'获取数据成功',$info);
            }else{
                $this->error(1030,'获取数据失败');
            }
    }


    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     * 课程分类(一级类)
     */
    public function getFatherTree(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $tissue_id = M("tissue_group_access")->where(array('user_id' => $userId))->getField("tissue_id");
        //获取方案ID
        $plan_id = M("sys_tissue")->where("tissue_id=".$tissue_id)->getField("plan_id");
        if(!$plan_id){
            $plan_id = 0;
        }
        $map['plan_id'] = array("eq",$plan_id);
        $map['pid'] = array("eq",0);
        //获取一级分类
        $courseCategory=M('course_category')->where($map)->select();
        if($courseCategory){
            $this->success(1000,'获取数据成功',$courseCategory);
        }else{
            $this->error(1030,'暂无数据返回');
        }
    }

    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     * @Param $courseCategory
     * @Param $id
     * @return array
     */
     public function getSonTree(){
         //判断用户是否存在,获取用户id,判断提交方式是否合法
         $userId = $this->verifyUserDataGet();
         $id = I('get.id','','int');
         if($id == '' || $id < 1){
             $this -> error(1030,'缺少分类id或参数有误');
         }
         static $Tree = array();
         $courseCategory=M('course_category')->where(array('pid' => $id))->select();
         if($courseCategory){
        foreach($courseCategory as $k => $v) {
            $courseCategorys=M('course_category')->where(array('pid' => $v['id']))->select();
            if(!empty($courseCategorys)){
                $v['sonTree'] = $courseCategorys;
            }else{
                $v['sonTree'] = array();
            }
                $Tree[] = $v;
            }
        }
         if($Tree){
             $this -> success(1000,'获取数据成功',$Tree);
         }else{
             $this -> error(1030,'暂无数据返回');
         }
    }


    //浏览次数
    public function readNum(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $course_id = I('post.course_id','','int');
        if($course_id > 0){
            M('Course')->where(array('id'=>$course_id))->setInc('click_count', 1);
            $click_count = M('Course')->where(array('id'=>$course_id))->getField('click_count');
            $click_count = $click_count ? $click_count : 0;
            $this->success(1023,'获取成功',array('click_count'=>$click_count));
        }else{
            $this->error(1023,'课程id参数有误');
        }
    }

    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     * 公开课-课程详情-简介
     * @param course_id 课程id
     */
    public function courseDetails(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $course_id = I('get.id','','int');
        $info = D('PublicCourse')->getCourseDetails($course_id,$userId);
        if($info['code'] == 1000){
            $this->success($info['code'],$info['message'],$info['data']);
        }else{
            $this->error($info['code'],$info['message']);
        }
    }


    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     * 公开课-课程详情-目录
     * @param course_id 课程id
     */
    public function courseDetailsList(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $course_id = I('get.id','','int');
        $info = D('PublicCourse')->getCourseDetailsList($course_id,$userId);
        if($info['code'] == 1000){
            $this->success($info['code'],$info['message'],$info['data']);
        }else{
            $this->error($info['code'],$info['message']);
        }
    }

    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     * 公开课-课程详情-获取评论列表
     * $course_id  课程id
     */
    public function getCommentList(){
        //判断用户是否存在，提交方式是否为GET
        $userId = $this->verifyUserDataGet();
        $course_id = I('get.id');
        $page = I('get.page',1,'int');
        $info = D('PublicCourse')->getCourseComment($course_id,$userId,$page);
        if($info['code'] == 1000){
            $this->success($info['code'],$info['message'],$info['data']);
        }else{
            $this->error($info['code'],$info['message']);
        }
    }


    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     * 公开课-课程详情-评论/回复评论
     * $userId 评论者id
     * $id 课程id
     * $course_id 课程course_id
     */
    public function contentComment(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $post['id'] = I('post.id','','int');//评论/回复评论id
        $post['course_id'] = I('post.course_id','','int');//评论/回复评论id
        $post['comment_content'] = I('post.content','','trim');//评论内容
        $info = D('PublicCourse')->addComment($post,$userId);
        if($info['code'] === 1000){
            $this->success($info['code'],$info['message'],$info['data']);
        }else{
            $this->error($info['code'],$info['message']);
        }
    }


    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
    * 公开课程-课程详情-删除评论
    */
    public function deleteComment(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $post["course_id"] = I("post.course_id",'','int');;
        $post['id'] = I("post.id",'','int');
        $result  = D("PublicCourse")->delComment($post,$userId);
        if ($result['code'] == 1000) {
            $this->success($result['code'], $result['message'], $result['data']);
        } else {
            $this->error($result['code'], $result['message']);
        }
    }



    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     *公开课-课程详情-点赞/取消点赞
     * $id 发布评论id
     * type 1点赞 0取消点赞
     *
     */
    public function praise(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $get = I('get.');
        $info = D('PublicCourse')->doPraise($get,$userId);
        if($info['code'] == 1000){
            $this->success($info['code'],$info['message'],$info['data']);
        }else{
            $this->error($info['code'],$info['message']);
        }
    }


    /***********************************************兼容Oracle的版本**********************************************************************/
    /**
     * 公开课-笔记
     */
    public function note(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $course_id = I('get.id');
        $info = D('PublicCourse')->getNote($course_id,$userId);
        if(!empty($info['data'])){
            $this->success($info['code'],$info['message'],$info['data']);
        }else{
            $this->error($info['code'],$info['message']);
        }
    }


}