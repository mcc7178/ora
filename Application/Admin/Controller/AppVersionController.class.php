<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;
/**
 * 版本更新管理
 */
class AppVersionController extends AdminBaseController
{

    /***
        * 版本更新展示页
        * 
        */
    public function index(){
        if(IS_AJAX){
            $version_number = I('post.version_number');
            $version_path = I('post.version_path');
            $data = D('AppVersion')->addNewVersion($version_number,$version_path);
            $this->ajaxreturn($data); 
        }else{
            $return = D('AppVersion')->index();
            $this->assign('list',$return['list']);
            $this->assign('page',$return['page']);
            $this->assign('title1','版本更新');
            $this->assign('title2','APP版本迭代');
            $this->display();
        }

        
    }













}