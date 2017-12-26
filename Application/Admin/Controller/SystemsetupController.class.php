<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/**
 * 系统参数设置
 *
 */
class SystemsetupController extends AdminBaseController
{

	public function index(){

		$typeid = I('get.typeid');
		$this->assign("typeid",$typeid);

		//课程分类
		if($typeid == 1){	//试卷分类
			$data = D('ResourcesManage')->testclassmanagement();
			$this->assign('data',$data['data']);
			$this->assign('page',$data['page']);
			$this->assign('keywords',$data['keywords']);
			$this->display('testcatelist');
		}else if($typeid == 2){

			$total_page = $this->total_page;
			$approved_data = D('SupplierType')->getSupplierStyle($total_page);
			//接收返回的列表数据
			$this->assign('approved_list', $approved_data['list']);
			//接收返回的分页信息
			$this->assign('approved_page', $approved_data['page']);
			//接收返回的搜索条件
			$this->assign('keyword', $approved_data['keyword']);
			$this->assign('supplierManage','供应商管理');
			$this->assign('supplierStyle','领域管理');

			$this->display('suppliercategory');
		}else{
			$data =D('RsCourse')->CourseClass();
			$items = array("items"=>$data);
			$this->assign($items);

			$this->display();
		}


	}
    

}