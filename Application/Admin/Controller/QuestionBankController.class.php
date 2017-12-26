<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

/**
 * 试题库控制器
 */
class QuestionBankController extends AdminBaseController{
	
	/**
	 * 首页
	 */
	public function index(){
		$model = D('QuestionBank');
		$res = D('QuestionBank')->index();

		$where = array('plan_id'=>getPlanId());
		$cate = D('ResourcesManage')->getExamCate($where);
		
		$this->assign('cate',json_encode($cate));
		$this->assign('data',$res['data']);
		$this->assign('page',$res['page']);
		$this->assign('name',$res['name']);
		$this->display();
	}

	/**
	 * 查找试题库
	 */
	public function search(){
		$res = D('QuestionBank')->search();
		$this->display('index');
	}
	
	/**
	 * 新增试题库
	 */
	public function add(){
		$res = D('QuestionBank')->add();
		$this->ajaxReturn($res);
	}
	
	public function addhtml(){
		$where = array('plan_id'=>getPlanId());
		$cate = D('ResourcesManage')->getExamCate($where);

		$this->assign('cate',$cate);
		$this->display('add');
	}
	/**
	 * 删除试题库
	 */
	public function del(){
		$res = D('QuestionBank')->del();
		$this->ajaxReturn($res);
	}
	
	/**
	 * 重命名试题库
	 */
	public function reName(){
		$res = D('QuestionBank')->reName();
		$this->ajaxReturn($res);
	}
	
	/**
     * 导出试题
     * @return [type] [description]
     */
    public function export(){
        $model = D('QuestionBank');
        $data = $model->export();
        $xlsName  = "试题-".date('Y-m-d-H:i:s');
		
		$base = array('试题题目','正确答案','题目类型','得分关键字','所属课程','所属试题库');
		
		$index = 0;
		foreach($data as $k=>$v){
			if(count($v) > count($data[$index])){
				$index = $k;
			}
		}
		foreach($data as $k=>$v){
			if($k == $index){
				foreach($v as $kk=>$vv){
					if(stripos($kk,'option') !== false && $kk != 'right_option'){
						$base[] = '选项'.strtoupper(substr($vv,-1,1));
					}
				}
			}
			unset($data[$k]['id']);
		}
        array_unshift($data,$base);
        create_xls($data,$xlsName);
    }

	//移动到--复制到
	public function move(){
		$res = D('QuestionBank')->index(I('get.id'));
		
		$this->assign('data',$res['data']);
		$this->assign('page',$res['page']);
		$this->assign('name',$res['name']);
		$this->display();
	}
	
	//移动试题
	public function moveto(){
		$res = D('QuestionBank')->moveto();
		$this->ajaxReturn($res);
	}
	
	//复制试题
	public function copyto(){
		$res = D('QuestionBank')->copyto();
		$this->ajaxReturn($res);
	}

	//试题库共享
	public function share(){
		$res = D('QuestionBank')->share();
		$this->ajaxReturn($res);
	}

	//共享给我的试题库
	public function sharingtome(){
		$model = D('QuestionBank');
		$res = D('QuestionBank')->sharingtome();

		$where = array('plan_id'=>getPlanId());
		$cate = D('ResourcesManage')->getExamCate($where);
		
		$this->assign('cate',json_encode($cate));
		$this->assign('data',$res['data']);
		$this->assign('page',$res['page']);
		$this->assign('name',$res['name']);
		$this->display();
	}
}
