<?php 

namespace Admin\Controller;

use Common\Controller\AdminBaseController;

class RsCourseController extends AdminBaseController{


	/**
	 * 资源管理模块 - 课程管理首页
	 */
	public function index()
	{
		$typeid = I("get.typeid");

		if($typeid == 3){

			//每页显示条数
			$total_page = $this->total_page;

			$data =D('RsCourse')->sharingToMe($total_page);

			$this->assign($data);

			$this->display("sharerange");

		}else{

			$data =D('RsCourse')->index();

			$this->assign($data);

			$this->display();
		}

	}

	/**
	 * 添加多媒体视频
	 */
	public function addVideo(){

		$upToken =D('RsCourse')->addVideo();

		$this->ajaxReturn($upToken,'json');
	}

	/**
	 * 多媒体页面
	 */
	public function video(){

		$setting=C('UPLOAD_SITEIMG_QINIU');
		$this->assign("domain",$setting['driverConfig']['domain']);
		$this->display();
	}

	/**
	 * 普通上传功能
	 */
	public function update(){



		$this->display();

	}

	/**
	 * 检查视频是否转码成功
	 */
	public function pfopStatus(){

		$url =D('RsCourse')->pfopStatus();

		echo $url;
		exit;
	}

	/**
	 * 资源管理模块 - 课程分类
	 */
	public function CourseClass(){

		$data =D('RsCourse')->CourseClass();

		$items = array("items"=>$data);

		$this->assign($items);

		$this->display();
	}

	/**
	 *增加分类
	 */
	public function addCategory()
	{
		$results = D('RsCourse')->addCategory();

		$data = array(
			"status"=> $results,
		);

		$this->ajaxReturn($data,'json');

	}

	/**
	 * 编辑分类
	 */
	public function editorCategory(){

		$results = D('RsCourse')->editorCategory();

		$this->ajaxReturn($results,'json');

	}

	/**
	 * 删除分类
	 */
	public function delCategory(){

		$results = D('RsCourse')->delCategory();

		$data = array(
			"status"=> $results,
		);

		$this->ajaxReturn($data,'json');
	}


	/**
	 * 添加课程
	 */
	public function addCourse(){

		//获取基本数据
		$data = D('RsCourse')->addCourse();

		$typeid = I('get.typeid');

		if($typeid == 1){

			//获取缓存
			$results = D('RsCourse')->courseCache($typeid);

			$this->assign("category_all",$data['category_all']);
			$this->assign("jobs_manage",$data['jobs_manage']);
			$this->assign("detail",$results);

		}else{
			//清除缓存
			F('course_data',NULL);
			
			$this->assign($data);
		}

		$this->display();

	}

	/**
	 * 编辑课程
	 */
	public function editorCourse(){

		$results = D('RsCourse')->editorCourse();

		if($results){

			$this->success('编辑成功',U('Admin/RsCourse/index'));

		}else{

			$this->error('编辑失败');

		}

	}

	/**
	 * 课程写入缓存
	 */
	public function courseCache(){

		//获取基本数据
		$data = D('RsCourse')->getLecturer();

		//获取课程数据
		$course_data = D('RsCourse')->courseCache();

		$results = D('RsCourse')->courseCacheAjax(1);

		$this->assign("course_way",$course_data['course_way']);
		$this->assign("external",$data['external']);
		$this->assign("internal",$data['internal']);
		$this->assign("detail",$results);
		$this->display();
	}

	/**
	 * Ajax 保存缓存
	 */
	public function courseCacheAjax(){

		//写入课件缓存
		D('RsCourse')->courseCacheAjax();

		$this->redirect('RsCourse/addCourse',array('typeid'=>1));

	}


	/**
	 * 设置公开课
	 */
	public function setOpen(){

		$results = D('RsCourse')->setOpen();

		$data = array(
			"status"=> $results,
		);

		$this->ajaxReturn($data,'json');

	}

	/**
	 * 设置是否禁用
	 */
	public function setTrigger(){

		$results = D('RsCourse')->setTrigger();

		$data = array(
			"status"=> $results,
		);

		$this->ajaxReturn($data,'json');


	}

	/**
	 * 删除课程
	 */
	public function delCourse(){

		$results = D('RsCourse')->delCourse();

		$data = array(
			"status"=> $results,
		);

		$this->ajaxReturn($data,'json');

	}

	/**
	 * 历史版本
	 */
	public function HistoryVersion(){

		$total_page = $this->total_page;

		$data = D('RsCourse')->HistoryVersion($total_page);

		$this->assign($data);

		$this->display();
	}

	/**
	 * 查看历史版本
	 */
	public function checkcourse(){

		$data = D('RsCourse')->checkcourse();
		
		$this->assign($data);
		$this->display();

	}

	/**
	 * 选择用户标签
	 */
	public function TagList(){

		$data = D('RsCourse')->TagList();

		$this->assign("taglist",$data);

		$this->display();

	}


	/**
	 * 文件转换-----无需七牛云，直接本机获取文件
	 * 参数： $httppath
	 * 作用：QINIU服务器返回的地址转换成pdf或HTML文件保存在服务器文件夹，并返回保存文件的路径$message;
	 */
	public function fileConvert(){
	   $path = I('post.src');
       $fileArr = explode(".",$path);
		$type = end($fileArr);
		$officeType = array("doc", "docx", "ppt", "pptx", "xls", "xlsx", "pdf");
		if(!in_array($type, $officeType)){
			$rdata['status'] = 1;
		    $rdata['info'] = '无需转换';
            $rdata['newpath'] = $path;
			$this->ajaxReturn($rdata);
		}
       $data = D('RsCourse')->fileConvert($path);
       if($data['code'] == 1000){
		  $rdata['status'] = 1;
		  $rdata['info'] = '文档转换完成';
		  $rdata['newpath'] = 'http://'.$_SERVER['SERVER_NAME'].$data['new_path'];
 
	   }else{
         $rdata['status'] = 0;
		 $rdata['info'] = $data['message'];
	   }
     

	   $this->ajaxReturn($rdata);
	}

}