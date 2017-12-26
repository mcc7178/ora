<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 19:48
 */

namespace Mobile\Controller;
use Think\Controller;

/**
 * Andy 2017-09-01
 * Class IndexCourse
 * 首页课程
 * @package Mobile\Controller
 */
class IndexCourseController extends CommonController{
      //初始化
    function __construct(){
        parent::__construct();
    }

    /*************************************************兼容oracle************************************************************/
    /**
     * @首页课程
     * @token  用户身份标识
     * @secret_key  秘钥
     * @Param $typeId 1进行中 2已结束
     */
    public function homePageCourse(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $page = I('get.page', 1, 'int');//分页参数
        $typeId = I("get.type",1 ,'int');

        $info = D('IndexCourse')->homePageCourse($typeId,$page,$pageNum = 10,$userId);

        if($info['code'] == 1000){
            $this->success(1000,$info['message'],$info['data']);
        }else{
            $this->error(1030,$info['message']);
        }
    }



    /**
     *首页待办任务消息数量通知
     */
    public function homePageWaitingTaskNotice(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $info = D('IndexCourse')->homePageWaitingTaskNotice($userId);
        if($info['code'] == 1000){
            $this->success($info['code'],$info['message'],$info['data']);
        }else{
            $this->error($info['code'],$info['message'],$info['data']);
        }
    }
}