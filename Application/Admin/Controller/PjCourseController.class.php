<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台权限管理
 */
class PjCourseController extends AdminBaseController{

//******************权限***********************
    /**
     * 培训项目-考试
     */
    public function index(){

        $total_page = $this->total_page;

        //$data['list'] 属于我的试卷
        $data=D('PjCourse')->index($total_page);
        
        $this->assign($data);

        $this->display();
    }

    /**
     * 培训项目 - 课程
     */
    public function course(){

        $total_page = $this->total_page;

        $typeid = I("get.typeid",0,'int');

        if($typeid == 1){

            $data =D('RsCourse')->sharingToMe($total_page);

        }else{

            $data=D('PjCourse')->course($total_page);

        }

        $this->assign($data);

        $this->assign("typeid",$typeid);

        $this->display();

    }

    /**
     * 培训项目 - 调研
     */
    public function research(){
        $typeid = I('get.typeid') ? I('get.typeid') : 0;
        $total_page = $this->total_page;

        $data=D('PjCourse')->research($total_page);
        $this->assign($data);
        $this->assign("typeid",$typeid);
        $this->display();
    }

    /**
     * 对我共享的问卷
     */
    public function sharingtome_r(){
        $data=D('RsSurvey')->sharingtome();
        $this->assign('list',$data['list']);
        $this->assign('keyword',$data['keyword']);
        $this->assign('page',$data['page']);
        $this->display();
    }

    /**
     * 对我共享的试卷
     */
    public function sharingToMe(){
        $approved_data = D('ResourcesManage')->sharingtome();
        
        $cate = D('ResourcesManage')->getExamCate(array('plan_id'=>getPlanId()));
        $this->assign('category',$cate);
        $this->assign('list',$approved_data['list']);
        $this->assign('page',$approved_data['page']);
        //搜索项
        $this->assign('keywords',$approved_data['keyword']);
        $this->assign('cate',$approved_data['cate']);
        $this->display();
    }




}


