<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/15
 * Time: 11:19
 */

namespace Mobile\Controller;
use Think\Controller;
/**
 * @资讯控制器
 * @2017-06-15 11:36:50
 * @author lizhongjian
 *
 */
class NewsController extends CommonController{
   public function __construct(){
       parent::__construct();
   }


    /**
     * @获取资讯列表
     * @
     */
    public function getNewsList(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        //接收资讯模板参数
        $type = I('get.type',2,'trim,htmlspecialchars,int');
        //接收分页参数
        $page = I('get.page',1,'trim,htmlspecialchars,int');
        //设置每页显示数据条数
        $total = 10;
        if($page < 1){
            $this->error(1030,'分页参数有误');
        }
        $page = ($page-1) * $total;
        $result = D('News')->getNewsList($type,$page,$total,$userId);
        if($result['code'] == 1000){
            $this->success(1000,$result['message'],$result['data']);
        }else{
            $this->error(1030,$result['message']);
        }

    }
    /**
     * 资讯管理
     * 创建资讯
     */
    public function createUpdateNews(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $method = I('post.method','','trim,htmlspecialchars,int');
        $data['template'] = I('post.template','','trim,htmlspecialchars,int');
        $data['type'] = I('post.type','','trim,htmlspecialchars,int');
        $data['title'] = I('post.title','','trim,htmlspecialchars');
        $data['content'] = I('post.content','','trim,htmlspecialchars');
        $data['img'] = I('post.image','','trim,htmlspecialchars');
        $News = D('News');
        if(!$News->token(false)->create($data,1)){
            //数据验证不通过则抛出错误
            $this->error(1030,$News->getError());
        }else{
            //验证通过，则进行数据操作
            $result = $News->createUpdateNews($data,$method,$userId);
           if($result['code'] == 1000){
               $this->success(1000,$result['message'],$result['data']);
           }else{
               $this->error(1030,$result['message']);
           }
        }
    }


    /**
     * 获取编辑资讯信息
     */
    public function getNewsInfo(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $id = I('get.id','','trim,htmlspecialchars,int');
        if($id == "" || $id < 1){
            $this->error(1030,'编辑id参数有误');
        }else{
            $result = M('News')->field('id,template,type,title,content,img')->where(array('id'=>$id))->find();
            if($result){
                $this->success(1000,'操作成功',$result);
            }else{
                $this->error(1030,'操作失败');
            }
        }
    }

    /**
     * @资讯删除(支持单条或批量删除)
     * @$arrayId 数组id
     */
    public function deleteNews(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        $strId = I('post.id');
        $info = D('News')->deleteNews($strId,$userId);
        if($info['code'] == 1000){
             $this->success(1000,$info['message']);
        }else{
             $this->error(1030,$info['message']);
        }
    }

    /**
     * 资讯上传图片
     */
    public function uploadFile(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        if(empty($_FILES["file"]["name"])){
            $this->error(1030,'没有文件被上传');
        }else{
            //图片上传设置
            $config = array(
                'maxSize' => 3145728,
                'savePath' => 'news/',//保存子目录
                'rootPath' => './Upload/',//保存根目录
                'saveName' => array('uniqid', ''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd')
            );
            $msg = $this->uploadImages($config);
            //保存图片路径
            if($msg){
                $this->success(1000,'上传成功',$msg);
            }else{
                $this->error(1030,'上传失败');
            }
        }
    }



    /**
     * 资讯评论列表
     * @Param
     */
    public function getNewsCommentList(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        //接收资讯的id
        $news_id = I('get.id',0,'int');
        //type 1 最热评论 2最新评论
        $type = I('get.type',0,'int');
        $page = I('get.page',1,'int');
        $pageNum = 10;
        if($news_id < 1 || $news_id == ''){
            $this->error(1030,'资讯id参数有误');
        }
        if($type != 1 && $type != 2){
            $this->error(1030,'资讯评论类型参数有误');
        }
        $data = D('News')->getNewsCommentList($userId,$news_id,$type,$page,$pageNum);
        if($data){
            $this->success(1000,'获取成功',$data);
        }else{
            $this->error(1030,'无数据返回');
        }
       // $project_name = D('News')->getItemName($project_id);
    }


    /**
     * 对资讯评论操作
     * $content 评论内容
     */
    public function createNewsComment(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //接收资讯id
        $news_id = I('post.news_id',0,'int');
        if($news_id < 1 || $news_id == ''){
            $this->error(1030,'资讯id参数有误');
        }
        //回复内容
        $content = I('post.content','','strip_tags,trim');
        if($content == ''){
            $this->error(1030,'资讯回复内容不能为空');
        }
        $data = D('News')->createNewsComment($userId,$news_id,$content);
        if($data){
            $this->success(1000,'操作成功',$data);
        }else{
            $this->error(1030,'操作失败');
        }
    }


    /**
     * 回复资讯评论
     * @Param $userId 用户id
     * @Param $news_id 资讯id
     * @Param $pid 资讯评论id
     * @Param $content 回复内容
     */
    public function replyNewsComment(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //接收资讯id
        $news_id = I('post.news_id',0,'int');
        if($news_id < 1 || $news_id == ''){
            $this->error(1030,'资讯news_id参数有误');
        }
        //接收回复哪条资讯评论的id即是id
        $pid = I('post.id',0,'int');
        if($pid < 1 || $pid == ''){
            $this->error(1030,'资讯评论id参数有误');
        }
        //回复内容
        $content = I('post.content','','strip_tags,trim');
        if($content == ''){
            $this->error(1030,'资讯回复内容不能为空');
        }
        $data = D('News')->replyNewsComment($userId,$news_id,$content,$pid);
        if($data){
            $this->success(1000,'操作成功',$data);
        }else{
            $this->error(1030,'操作失败');
        }
    }


    /**
     *资讯评论点赞 点赞
     */
    public function newsCommentPraise(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //点赞资讯评论的id或者是回复评论的id
        $cid = I('post.id',0,'int');
        //1点赞 0取消点赞
        $is_praise = I('post.is_praise','','int');
        if($cid < 1 && $cid == ''){
            $this -> error(1030,'点赞话题id参数有误');
        }
        $data_topic = D('News')->newsCommentPraise($cid,$userId,$is_praise);
        if($data_topic['code'] == 1000){
            $this -> success($data_topic['code'],$data_topic['message']);
        }else{
            $this -> error($data_topic['code'],$data_topic['message']);
        }
    }

}