<?php
namespace Mobile\Controller;
use Think\Controller;
   /**
    * @author Dujunqiang 20170310
    * 我的通讯录
    *
    */
class ContactsController extends CommonController {
	/**
	 * 初始化
	 */
	public function __construct(){
		parent::__construct();
	}

	
	/**
	 * 通讯录首页
	 */
	public function index(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
		$return = D("Contacts")->index($userId);
       // dump($return);exit;
		if($return["code"] == 1000){
			$this->success(1000,'获取数据成功',$return["data"]);
		}else{
			$this->error($return["code"],$return["message"]);
		}
	}
	
	/**
	 * 分组通讯录列表
	 * 
	 */
	public function groupList(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
        //接收部门id
		$post['id'] = I('post.id',0,'int');
		if($post['id'] < 1){
			$this->error(1011, "缺少参数： 部门id");
		}
		$return = D("Contacts")->groupList($post,$userId);
		if($return["code"] == 1000){
			$this->success(1000,'获取数据成功',$return["data"]);
		}else{
			$this->error($return["code"],$return["message"]);
		}
	}
	
	/**
	 * 联系人详细信息
	 * user_id 联系人id
	 */
	public function detail(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
		$user_id = I('post.user_id','','int');
        if(!isset($user_id)){
            $this->error(1011, "缺少参数：user_id 联系人id ");
        }
		if($user_id < 1){
			$this->error(1011, "用户id需为大于0的正整数");
		}
		$return = D("Contacts")->detail($user_id);
		if($return["code"] == 1000){
			$this->success(1000,'获取数据成功',$return["data"]);
		}else{
			$this->error($return["code"],$return["message"]);
		}
	}
	
	/**
	 * 联系人查询
	 */
	public function search(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
		$post['keyword'] = I("post.keyword",'','trim,htmlspecialchars');
		if(!$post["keyword"] || $post["keyword"] == ""){
			$this->error(1011, "缺少参数：keyword 搜索关键字");
		}
		$return = D("Contacts")->search($post,$userId);
		if($return["code"] == 1000){
			$this->success(1000,'获取数据成功',$return["data"]);
		}else{
			$this->error($return["code"],$return["message"]);
		}
	}
}