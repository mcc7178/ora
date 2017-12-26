<?php

namespace Common\Model;

use Common\Model\BaseModel;

class RsCourseModel extends BaseModel
{

	//初始化
	public function __construct(){}

	/**
	 * 首页
	 */
	public function index($total_page = 10){

		//获取当前所属组织所有会员
		$start_page = I("get.p",0,'int');

		$keywords = I("get.keywords");

		$typeid = I("get.typeid");

		$course_way = I("get.course_way");

		if($typeid == ''){
			$typeid = 1;
		}

		$parameter = array("p"=>$start_page,"typeid"=>$typeid);

		$conditions['a.status'] = array("eq",$typeid);

		if(!empty($keywords)){
			$conditions['a.course_name'] = array("like","%$keywords%");
		}

		if(is_numeric($course_way) and $course_way != "-1"){
			$conditions['a.course_way'] = array("eq",$course_way);
		}

		//隔离数据过滤
		$specifiedUser = D('IsolationData')->specifiedUser();

		$conditions['a.auth_user_id'] = array("in",$specifiedUser);

		$list = M("course a")
			->field("a.id,a.course_name,a.course_time,a.is_public,a.course_way,a.create_time,a.auditing,a.auth_user_id,c.cat_name,d.username")
			->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id LEFT JOIN __COURSE_CATEGORY__ c ON a.course_cat_id = c.id LEFT JOIN __USERS__ d ON a.user_id = d.id")
			->order('a.id desc')
			->where($conditions)
			->page($start_page,$total_page)
			->select();

		$count = M("course a")
			->field("a.course_name,a.course_time,a.is_public,a.course_way,a.create_time,a.auth_user_id,c.cat_name")
			->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id LEFT JOIN __COURSE_CATEGORY__ c ON a.course_cat_id = c.id")
			->where($conditions)
			->order('a.id desc')
			->count();
	
		
		//隔离数据过滤
		$list = D('IsolationData')->isolationData($list,2);

		$show = $this->pageClass($count,$total_page,$parameter);

		$data = array(
			"typeid"=>$typeid,
			"course_way"=>$course_way,
			"list"=>$list,
			"pages"=>$show
		);

		return $data;


	}

	/**
	 * 资源管理模块 - 课程分类
	 */
	public function CourseClass(){

		//获取所有分类
		$itesm = $this->tree();

		return $itesm;
	}

	/**
	 *增加分类
	 */
	public function addCategory()
	{
		$cat_name = I("post.cat_name");

		$pid = I("post.pid");

		if(!empty($cat_name)){

			$where['user_id'] = array("eq",$_SESSION['user']['id']);

			$tissue_id = M("tissue_group_access")->where($where)->getField("tissue_id");

			//获取方案ID
			$plan_id = M("sys_tissue")->where("tissue_id=".$tissue_id)->getField("plan_id");

			$data = array("pid"=>$pid,"cat_name"=>$cat_name,"plan_id"=>$plan_id);

			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$data['id'] = getNextId('course_category');
			}
			$results = M('course_category')->add($data);

		}else{

			$results = false;

		}

		return $results;
	}

	/**
	 * 获取树形
	 */
	public function tree(){

		//查询当前所属组织

		$where['user_id'] = array("eq",$_SESSION['user']['id']);

		$tissue_id = M("tissue_group_access")->where($where)->getField("tissue_id");

		//获取方案ID
		$plan_id = M("sys_tissue")->where("tissue_id=".$tissue_id)->getField("plan_id");

		$map['plan_id'] = array("eq",$plan_id);

		$rule_list = M("course_category")->where($map)->order("sort asc")->select();

		// 获取一级下所有下级分类
		$item = \Org\Nx\Data::channelLevel($rule_list,0,'&nbsp;','id');

		return $item;
	}

	/**
	 * 编辑分类
	 */
	public function editorCategory(){

		$cat_name = I("post.cat_name");

		$pid = I("post.pid");

		$type = I("post.type");

		if($type == 'get'){

			$category = M("course_category")->field("cat_name")->where("id=".$pid)->find();

			$data = array(
				"category"=>$category['cat_name']
			);


		}else{

			if(!empty($cat_name)){

				$results = M('course_category')->where("id=".$pid)->setField("cat_name",$cat_name);
			}else{

				$results = false;
			}

			$data = array(
				"status"=> $results,
			);

		}

		return $data;
	}

	/**
	 * 删除分类
	 */
	public function delCategory(){

		$pid = I("post.pid");

		$is_category = M("course_category")->where("pid=".$pid)->find();


		if(empty($is_category)){

			$results = M("course_category")->where("id=".$pid)->delete();

		}else{

			$results = false;
		}

		return $results;

	}

	/**
	 * 添加课程
	 */
	public function addCourse(){

		//获取课程分类
		$category = $this->CourseClass();

		//获取内外部讲师
		$getLecturer = $this->getLecturer();

		//获取当前课程编辑内容
		$UpdateCourse = $this->UpdateCourse();

		//编辑写入缓存
		$this->editCache($UpdateCourse);

		if(!empty($UpdateCourse['tag_id'])){

			$where['id'] = array("in",$UpdateCourse['tag_id']);

			$tag_list = M("users_tag")->where($where)->select();

			$UpdateCourse['tag_list'] = $tag_list;

		}else{

			$UpdateCourse['tag_list'] = array();

		}

		//获取所属岗位
		$jobs_manage = M("jobs_manage")->order("id desc")->select();

		$data = array(
			"category_all"=>$category,
			"external"=>$getLecturer['external'],
			"internal"=>$getLecturer['internal'],
			"detail"=>$UpdateCourse,
			"jobs_manage"=>$jobs_manage
		);

		return $data;

	}

	/**
	 * 编辑写入缓存
	 */
	public function editCache($data){

		$id = I("post.id") ? I("post.id") : I("get.id");

		if(!empty($id)){

			$courseware_data = array(
				'lecturer_name'=>$data['lecturer_name'],
				'lecturer'=>$data['lecturer'],
				'chapter'=>$data['chapter'],
				'auditing'=>$data['auditing'],
			);

			F('courseware_data',NULL);

			F('courseware_data',$courseware_data);

		}else{

			$typeid = I('get.typeid');

			if(empty($typeid)){
				F('courseware_data',NULL);
			}

		}

	}


	/**
	 * 选择用户标签
	 */
	public function TagList(){

		//获取所属标签
		$tag_list = M("users_tag")->order("id desc")->select();

		return $tag_list;

	}


	/**
	 * 获取课程内容
	 */
	public function UpdateCourse(){

		$id = I("get.id") ? I("get.id") : I("post.id");

		$conditions['a.id'] = array("eq",$id);

		$list = M("course a")
			->field("a.id,a.course_name,a.course_code,a.course_time,a.course_cat_id,a.is_public,a.course_way,a.create_time,a.lecturer,a.media_src,a.maker,a.chapter,a.course_cover,a.credit,a.auditing,a.lecturer_name,a.arrangement_id,a.tag_id,a.jobs_id,b.course_intro,b.course_aim,b.course_summary,b.course_outline,c.cat_name")
			->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id LEFT JOIN __COURSE_CATEGORY__ c ON a.course_cat_id = c.id")
			->order('a.id desc')
			->where($conditions)
			->find();
		return $list;

	}


	/**
	 * 编辑课程
	 */
	public function editorCourse(){
		$data = F('course_data');

		$form_data = I('post.');
		$chapter = I('post.chapter','','stripslashes,trim');

		$courseware_data = array(
			'lecturer_name'=>$form_data['lecturer_name'],
			'lecturer'=>$form_data['online_lecturer'],
			'chapter'=>$chapter,
			'auditing'=>$form_data['auditing'],
		);

		$auditing = empty($courseware_data['auditing']) ? 0 : 1;

		//线上课程和线下课程的默认封面图
		if($data['course_way'] == 0){
			$data['course_cover'] = empty($data['course_cover']) ? '' : $data['course_cover'];
		}else if($data['course_way'] == 1){
			$data['course_cover'] = empty($data['course_cover']) ? '' : $data['course_cover'];
		}

		$course_data = array(
			"course_name"=>$data['course_name'],
			"course_code"=>$data['course_code'],
			"course_cat_id"=>$data['course_cat_id'],
			"arrangement_id"=>$data['arrangement_id'],
			"course_way"=>$data['course_way'],
			"course_time"=>$data['course_time'],
			"media_src"=>$data['media_src'],
			"maker"=>$data['maker'],
			"chapter"=>$courseware_data['chapter'],
			"course_cover"=>$data['course_cover'],
			"credit"=>$data['credit'],
			"auditing"=>$auditing,
			"create_time"=>time(),
			"user_id"=>$_SESSION['user']['id'],
			"orderno"=>$data['orderno'],
			"tag_id"=>$data['tag_id'],
			"jobs_id"=>$data['jobs_id'],
			"auth_user_id"=>$_SESSION['user']['id'],
			"status"=>$data['status']
		);

		if($data['course_way'] == 0){
			$course_data['lecturer_name'] = $courseware_data['lecturer_name'];
			$course_data['lecturer'] = $courseware_data['lecturer'] ? $courseware_data['lecturer'] : 0;
		}else{
			$course_data['lecturer'] = $courseware_data['lecturer'] ? $courseware_data['lecturer'] : 0;
			$course_data['lecturer_name'] = '';
		}

		$course_detail_data = array(
			"course_intro"=>$data['course_intro'],
			"course_aim"=>$data['course_aim'],
			"course_outline"=>$data['course_outline'],
		);

		if(empty($data['id'])){

			//新增
			try {

				$DB = M();

				$DB->startTrans();

				if(strtolower(C('DB_TYPE')) == 'oracle'){
					$course_data['id'] = getNextId('course');
				}
                
				$increment_id = $DB->table('__COURSE__')->data($course_data)->add();
                
				//视频章节加入到同步列表
				$this->intoSync($increment_id, $course_data['chapter']);
				
				$course_detail_data['id'] = $increment_id;

				$is_course_detail = $DB->table('__COURSE_DETAIL__')->data($course_detail_data)->add();

				
				if(!empty($increment_id) && !empty($is_course_detail)){

					write_login_log(1,2,$course_data['course_name']);

					$DB->commit();

					$results = true;

				}

			} catch ( Exception $e ) {

				$DB->rollback();

				$results = false;
			}


		}else{

			//编辑
			try {

				$where['id'] = array("eq",$data['id']);

				$DB = M();

				$DB->startTrans();
				//查询上个版本并插入

				$course_bak = M("course")->where($where)->find();

				$course_detail_bak = M("course_detail")->where($where)->find();

				$course_bak['course_id'] = $data['id'];
				$course_bak['user_id'] = $_SESSION['user']['id'];
				$course_bak['update_time'] = time();

				unset($course_bak['id']);

				if(strtolower(C('DB_TYPE')) == 'oracle'){
					$course_bak['id'] = getNextId('course_bak');
					unset($course_bak['numrow']);
				}

				$course_bak_id = $DB->table('__COURSE_BAK__')->data($course_bak)->add();

				$course_detail_bak['course_id'] = $data['id'];
				$course_detail_bak['id'] = $course_bak_id;

				if(strtolower(C('DB_TYPE')) == 'oracle'){
					unset($course_detail_bak['numrow']);
				}

				$DB->table('__COURSE_DETAIL_BAK__')->data($course_detail_bak)->add();

				$exist = M("course")->where(array('chapter'=>$data['chapter']))->find();

				//更新课程
				unset($course_data["status"]);//编辑后已通过还是通过  未审核还是未审核
				unset($course_data["user_id"]);
				unset($course_data['auth_user_id']);

				$DB->table('__COURSE__')->where($where)->save($course_data);

				$DB->table('__COURSE_DETAIL__')->where($where)->save($course_detail_data);
				
				//视频章节加入到同步列表
				$this->intoSync($data['id'], $course_data['chapter']);

				$DB->commit();

				write_login_log(1,3,$course_data['course_name']);

				$results = true;

			} catch ( Exception $e ) {

				$DB->rollback();

				$results = false;
			}

		}

		return $results;
	}
	
	/**
	 * 视频章节写入同步表
	 * $course_id 课程ID
	 * $chapter 章节字符串，json格式
	 */
	public function intoSync($course_id, $chapter){
		if(!$course_id || !$chapter){
			return false;
		}
		
		$chapter = json_decode($chapter, true);
		if(!is_array($chapter)){
			return false;
		}
		
		$has = M("course_video_sync")->field("local_path,sync_path")
			->where("course_id=".$course_id." and status='2'")->select();
		$oldData = array();
		if($has){
			foreach ($has as $oldValue){
				$oldData[$oldValue["local_path"]] = $oldValue["sync_path"];
			}
		}
		
		//每次删除旧数据，章节排序什么的已经混乱干脆删掉
		M("course_video_sync")->where("course_id=".$course_id)->delete();
		
		foreach ($chapter as $key=>$value){
			$videoPath = $value["src"];//地址为：域名/Upload/resources/20171128/5a1d0398c5a31.mp4 
			$videoPath = explode("Upload", $videoPath);
			$videoPath = "/Upload".$videoPath[1];
			
			$fileArr = explode(".", $videoPath);
			$type = end($fileArr);
			$videoType = array("mp4");//此处只有mp4,其他视频类型还有："mp4", "rmvb", "avi", "3gp"
			if(!in_array($type, $videoType)){
				continue;
			}
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$data["id"] = getNextId('course_video_sync');
			}
			$data["course_id"] = $course_id;
			$data["chapter_id"] = $key;
			$data["upload_time"] = date("Y-m-d H:i:s");
			$data["sync_time"] = "";
			
			$sync_path = "";
			$status = 0;
			//修改课程时，如果视频还是原视频文件则使用旧数据
			if($oldData[$videoPath]){
				$sync_path = $oldData[$videoPath];
				$status = 2;
			}
			
			$data["local_path"] = $videoPath;
			$data["sync_path"] = $sync_path;
			$data["status"] = $status;
			$data["msg"] = "";
			$resp = M("course_video_sync")->add($data);
		}
		return true;
	}
	
	/**
	 * 课程写入缓存
	 */
	public function courseCache($typeid){

		if($typeid == 1){

			$course_data = F('course_data');

			if(!empty($course_data['tag_id'])){

				$where['id'] = array("in",$course_data['tag_id']);

				$tag_list = M("users_tag")->where($where)->select();

				$course_data['tag_list'] = $tag_list;

			}else{

				$course_data['tag_list'] = array();

			}

			return $course_data;

		}else{

			$data = I("post.");

			if(!empty($data['user_id'])){
				$tag_id = implode(",",$data['user_id']);
			}else{
				$tag_id = '';
			}

			if(empty($data['course_name']) || empty($data['course_cat_id'])){

				$results = false;

			}else{

				//线上课程和线下课程的默认封面图
				if($data['course_way'] == 0){
					$data['course_cover'] = empty($data['course_cover']) ? '' : $data['course_cover'];
				}else if($data['course_way'] == 1){
					$data['course_cover'] = empty($data['course_cover']) ? '' : $data['course_cover'];
				}

				$orderno =  D('Trigger')->orderNumber(2);

				if($orderno['status'] == 0){
					$status = 1;
				}else{
					$status = 0;
				}
                
				$course_data = array(
					"course_name"=>$data['course_name'],
					"course_code"=>$data['course_code'],
					"course_cat_id"=>$data['course_cat_id'],
					"arrangement_id"=>$data['arrangement_id'],
					"course_way"=>$data['course_way'],
					"course_time"=>$data['course_time'],
					"media_src"=>$data['media_src'],
					"maker"=>$data['maker'],
					"chapter"=>$data['chapter'],
					"course_cover"=>$data['course_cover'],
					"credit"=>$data['credit'],
					"create_time"=>time(),
					"user_id"=>$_SESSION['user']['id'],
					"orderno"=>$orderno['no'],
					"tag_id"=>$tag_id,
					"jobs_id"=>$data['jobs_id'],
					"auth_user_id"=>$_SESSION['user']['id'],
					"course_intro"=>$data['course_intro'],
					"course_aim"=>$data['course_aim'],
					"course_outline"=>$data['course_outline'],
					"status"=>$status
				);

				if(!empty($data['id'])){
					$course_data['id'] = $data['id'];
				}

				F('course_data',$course_data);
			}

			return F('course_data');
		}
	}

	/**
	 * Ajax 保存缓存
	 */
	public function courseCacheAjax($typeid){

		if($typeid == 1){

			$courseware_data = F('courseware_data');

			return $courseware_data;

		}else{

			$data = I("post.");

			if(!empty($data['lecturer_name'])){
				$courseware_data['lecturer_name'] = $data['lecturer_name'] ? $data['lecturer_name'] : '';
			}

			if(!empty($data['online_lecturer'])){
				$courseware_data['lecturer'] = $data['online_lecturer'];
			}

			if(!empty($data['chapter'])){
				$courseware_data['chapter'] = $data['chapter'];
			}

			if(!empty($data['auditing'])){
				$courseware_data['auditing'] = $data['auditing'];
			}

			F('courseware_data',$courseware_data);

		}

	}


	/**
	 * 生成章节转换文件表的关联数据
	 * 参数： $course_id ； $chapter json格式课程章节;   $type:1新增 2编辑
	 * 作用：向章节转换文件表的关联数据，用于课件文档预览
	 */
	
	/**
	 * 文件转换-----无需七牛云，直接本机获取文件
	 * 参数： $httppath
	 * 作用：QINIU服务器返回的地址转换成pdf或HTML文件保存在服务器文件夹，并返回保存文件的路径$message;
	 */
	public function fileConvert($httppath){
		$fileArr = explode(".", $httppath);
		$type = end($fileArr);
		$officeType = array("doc", "docx", "ppt", "pptx", "xls", "xlsx", "pdf");
		if(!in_array($type, $officeType)){
			return array("code"=>1001, "message"=>"非office,无需转换");
		}
		
		$fileArr = explode("/Upload", $httppath);
		$inputFile = "/Upload".$fileArr[1];
		// $inputFile = "/Upload/docConvert/survey999.xls";
		$message = A("Office")->convert($inputFile);
		return $message;  //返回相对路径
	}

	/**
	 * 获取内外部讲师数据
	 */
	public function getLecturer(){

		$rows = array();

		//隔离数据过滤
		$specifiedUser = D('IsolationData')->specifiedUser();

		$conditions['auth_user_id'] = array("in",$specifiedUser);

		$items = M("lecturer")->field("auth_user_id,id,name,type")->where($conditions)->order("id asc")->select();

		foreach($items as $k=>$item){

			if($item['type'] == 1){
				$rows['external'][] = $item;
			}else{

				$rows['internal'][] = $item;
			}

		}
		return $rows;
	}

	/**
	 * 设置公开课
	 */
	public function setOpen(){
		$ids = I("post.ids");
		$is_public = I("post.is_public");
		try {

			$data['id'] = array("in",$ids);

			$course_list = M('course')->field("id,auditing")->where($data)->select();

			foreach($course_list as $list){
				if($list['auditing'] == 1){
					$course_id[] = $list['id'];
				}
			}

			$where['id'] = array("in",$course_id);

			$DB = M('course');

			$DB->startTrans();

			if(empty($course_id)){
				$increment_id = 402;
			}else{
				$increment_id =  $DB->where($where)->setField("is_public",$is_public);
			}

			if($increment_id){
				$DB->commit();
				$increment_id = 1;
			}

		} catch ( Exception $e ) {

			$DB->rollback();

		}

		return $increment_id;

	}

	/**
	 * 设置是否禁用
	 */
	public function setTrigger(){

		$id = I("post.id");

		$items = M("course")->field("auditing")->where("id=".$id)->find();

		try {

			$data['id'] = array("eq",$id);

			$DB = M('course');

			$DB->startTrans();

			if($items['auditing']){

				$increment_id =  $DB->where($data)->setField(array("is_public"=>0,"auditing"=>0));

			}else{

				$increment_id =  $DB->where($data)->setField(array("auditing"=>1));
			}

			if($increment_id){

				$DB->commit();
			}

		} catch ( Exception $e ) {

			$DB->rollback();

		}

		return $increment_id;

	}

	/**
	 * 删除课程
	 */
	public function delCourse(){

		$ids = I("post.ids");

		try {

			$data['id'] = array("in",$ids);

			$where['course_id'] = array("in",$ids);

			$DB = M();

			$DB->startTrans();

			$course_list = M('course')->field("COURSE_NAME")->where($data)->select();

			$increment_id = $DB->table('__COURSE__')->where($data)->delete();

			$is_course_detail = $DB->table('__COURSE_DETAIL__')->where($data)->delete();

			$DB->table('__COURSE_BAK__')->where($where)->delete();

			$DB->table('__COURSE_DETAIL_BAK__')->where($where)->delete();

			if(!empty($increment_id) && !empty($is_course_detail)){

				$DB->commit();

				foreach($course_list as $list){
					write_login_log(1,4,$list['course_name']);
				}

				$results = true;
			}

		} catch ( Exception $e ) {

			$DB->rollback();

			$results = false;

		}

		return $results;


	}

	/**
	 * 添加多媒体视频
	 */
	public function addVideo(){

		/*视频转码
		$setting=C('UPLOAD_SITEIMG_QINIU');

		$policy = array(
			'persistentOps' => $setting['driverConfig']['persistentOps'],
			'persistentPipeline'=>$setting['driverConfig']['persistentPipeline'],
		);

		$auth = new \Think\Upload($setting,'Qiniu',$setting['driverConfig']);

		$upToken = $auth->UploadToken($setting['driverConfig']['secretKey'],$setting['driverConfig']['accessKey'],$policy);
		*/

		$setting=C('UPLOAD_SITEIMG_QINIU');

		$auth = new \Think\Upload($setting,'Qiniu',$setting['driverConfig']);

		$upToken = $auth->UploadToken($setting['driverConfig']['secretKey'],$setting['driverConfig']['accessKey']);

		$data = array(
			"uptoken"=>$upToken
		);

		return $data;
	}

	/**
	 * 检查视频是否转码成功
	 */
	public function pfopStatus(){

		$id = I("get.id");

		$url = "http://api.qiniu.com/status/get/prefop?id=$id";

		$curl = curl_get_contents($url);

		return $curl;
	}


	/**
	 *  分页公共方法
	 */
	public function pageClass($count,$total_page,$parameter){

		$Page = new \Think\Page($count,$total_page);

		if(!empty($parameter)){

			foreach($parameter as $key=>$val) {
				$Page->parameter[$key]   =   urlencode($val);
			}

		}

		$Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');

		$Page->setConfig('header',"<span>%TOTAL_PAGE%</span>");

		$Page->setConfig('prev','上一页');

		$Page->setConfig('next','下一页');

		$show = $Page->show();

		return $show;
	}

	/**
	 * 课程历史版本
	 */
	public function HistoryVersion($total_page = 10){

		$course_id = I('get.id');

		$start_page = I("get.p",0,'int');

		$conditions['a.course_id'] = array("eq",$course_id);

		$course = M("course")->field("course_name")->where("id=".$course_id)->find();


		$list = M("course_bak a")
			->field("a.id,a.update_time,b.username")
			->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
			->order('a.update_time desc')
			->where($conditions)
			->page($start_page,$total_page)
			->select();

		$count = M("course_bak a")
			->where($conditions)
			->count();


		$show = $this->pageClass($count,$total_page);

		$data = array(
			'course_name'=>$course['course_name'],
			'list'=>$list,
			'show'=>$show
		);

		return $data;
	}

	/**
	 * 查看历史版本
	 */
	public function checkcourse(){

		$course_id = I('get.id');

		$conditions['a.id'] = array("eq",$course_id);

		$data = M("course_bak a")
			->field("a.*,b.username,c.course_intro,c.course_aim,c.course_summary,c.course_outline")
			->join("LEFT JOIN __USERS__ b ON a.user_id = b.id LEFT JOIN __COURSE_DETAIL_BAK__ c ON a.id = c.id")
			->order('a.id desc')
			->where($conditions)
			->find();

		if(!empty($data['chapter'])){

			$data['chapter'] = json_decode($data['chapter'],true);

		}

		if(!empty($data['lecturer'])){
			$lecturer = M("lecturer")->field("name")->where("id=".$data['lecturer'])->find();
		}


		if(!empty($data['tag_id'])){

			$tag_id['id'] = array("in",$data['tag_id']);

			$users_tag = M("users_tag")->field("tag_title")->where($tag_id)->select();

			foreach($users_tag as $tag){
				$tag_str[] = $tag['tag_title'];
			}

			$data['tag_name'] = implode(",",$tag_str);
		}

		//查看讲师名称
		if(empty($data['course_way'])){
			if(!empty($data['lecturer'])){
				$data['lecturer_name'] = $lecturer['name'];
			}
		}else{
			$data['lecturer_name'] = $lecturer['name'];
		}

		//查看上下版本

		$where['course_id'] = array("eq",$data['course_id']);

		$page_list = M("course_bak a")->field("a.id")->order('a.id desc')->where($where)->select();

		foreach($page_list as $k=>$list){

			if($course_id == $list['id']){

				if(!empty($page_list[$k-1])){
					$up  = $page_list[$k-1]['id'];
				}else{
					$up  = false;
				}

				if(!empty($page_list[$k+1])){
					$next  = $page_list[$k+1]['id'];
				}else{
					$next  = false;
				}

			}

		}

		$data['page'] = array(
			"up"=>$up,
			"next"=>$next
		);

		return $data;
	}

	/**
	 * 获取共享给我的课程
	 * @return [type] [description]
	 */
	public function sharingToMe($total_page = 10){

		//获取当前所属组织所有会员
		$start_page = I("get.p",0,'int');

		$keywords = I("get.keywords");

		$typeid = I("get.typeid");

		$course_way = I("get.course_way");

		$parameter = array("p"=>$start_page,"typeid"=>$typeid);

		$conditions['a.status'] = array("eq",1);

		if(!empty($keywords)){
			$conditions['a.course_name'] = array("like","%$keywords%");
		}

		if(is_numeric($course_way) and $course_way != "-1"){
			$conditions['a.course_way'] = array("eq",$course_way);
		}

		//查询共享数据
		$course_arrid = array();
		$where['a.type'] = array("eq",1);
		$where['b.user_id'] = array("eq",$_SESSION['user']['id']);

		$resource_sharing = M("resource_sharing a")->join("LEFT JOIN __TISSUE_GROUP_ACCESS__ b ON a.tissue_id = b.tissue_id")->field("a.source_id")->where($where)->select();

		foreach($resource_sharing as $sharing){
			$course_arrid[] = $sharing['source_id'];
		}

		if(!empty($course_arrid)){
			$conditions['a.id'] = array("in",$course_arrid);
		}else{
			$conditions['a.id'] = array("in",array(0));
		}

		$list = M("course a")
			->field("a.id,a.course_name,a.course_time,a.is_public,a.course_way,a.create_time,a.auditing,a.auth_user_id,c.cat_name,d.username")
			->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id LEFT JOIN __COURSE_CATEGORY__ c ON a.course_cat_id = c.id LEFT JOIN __USERS__ d ON a.user_id = d.id")
			->order('a.id desc')
			->where($conditions)
			->page($start_page,$total_page)
			->select();

		$count = M("course a")
			->field("a.course_name,a.course_time,a.is_public,a.course_way,a.create_time,a.auth_user_id,c.cat_name")
			->join("LEFT JOIN __COURSE_DETAIL__ b ON a.id = b.id LEFT JOIN __COURSE_CATEGORY__ c ON a.course_cat_id = c.id")
			->where($conditions)
			->order('a.id desc')
			->count();


		//隔离数据过滤
		$list = D('IsolationData')->isolationData($list,2);

		$show = $this->pageClass($count,$total_page,$parameter);

		$data = array(
			"typeid"=>$typeid,
			"course_way"=>$course_way,
			"list"=>$list,
			"pages"=>$show
		);

		return $data;

	}

}