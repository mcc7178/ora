<?php
namespace Scorm\Controller;
use Think\Controller;

class CourseController extends Controller {
	
	public function test(){
		$upload = THINK_PATH;
		$upload = str_replace('\\', "/", $upload);
		$upload = str_replace('ThinkPHP/', "", $upload);
		echo $upload;
	}
	
	/**
	 * 课程列表
	 */
	public function index(){
		$get = I("get.");
		$data["host"] = $_SERVER['HTTP_HOST'];
		$user_id = $_SESSION["user"]["id"];
		
		if(!preg_match("/^[1-3]$/", $get["type"])){
			$get["type"] = 1;
		}
		$data["type"] = $get["type"];
		
		$page = (int)$get["p"];
		if($page < 1) $page = 1;
		
		$get["title"] = trim($get["title"]);
		$where["status"] = 1;
		if($get["title"]){
			$where["name"] = array("like", "%".$get["title"]."%");
		}
		
		$pageLen = 20;
		if($get["type"] == 1){
			$specifiedUser = self::specifiedUser();
			$where["auth_user_id"] = array("in", $specifiedUser);
			$list = M("sco")->field("id,name,descs,addTime")->where($where)->order("id desc")->page($page, $pageLen)->select();
			$count = M("sco")->field("count(id) as t_num")->where($where)->select();
		}elseif($get["type"] == 2){
			$myTissue = M("tissue_group_access")->where("user_id=".$user_id)->find();
			$where["b.tissue_id"] = $myTissue["tissue_id"];
			$where["b.type"] = 6;
			$list = M("sco a")->field("a.id,a.name,a.descs,a.addTime")->join("join __RESOURCE_SHARING__ b on a.id=b.source_id")->where($where)->page($page, $pageLen)->select();
			$count = M("sco a")->field("count(a.id) as t_num")->join("join __RESOURCE_SHARING__ b on a.id=b.source_id")->where($where)->select();
		}elseif($get["type"] == 3){
			$pageLen = 5;
			$list = M("dy_sco")->field("id,name,descs,addTime")->where($where)->order("id desc")->page($page, $pageLen)->select();
			foreach ($list as $key=>$value){
				$subWhere["scoid"] = $value["id"];
				$chap = M("dy_sco_chap")->field("name,path")->where($subWhere)->order("orders asc")->select();
				$list[$key]["chap_list"] = $chap;
			}
			
			$count = M("dy_sco")->field("count(id) as t_num")->where($where)->select();
		}
		
		$Page = new \Think\Page($count[0]["t_num"], $pageLen);
		$pageNav = $Page->show();
		$data["list"] = $list;
		$data["title"] = $get["title"];
		$data["pageNav"] = $pageNav;
		$this->assign($data);
		$this->display();
	}
	
	/**
	 * 添加课程
	 */
	public function addCourse(){
		header("Content-type: text/html; charset=utf-8");
		
		if(!function_exists("scandir")){
			$return = array("code" =>1011, "message"=>"函数scandir不可用，请修改启用");
			exit(json_encode($return));
		}
		
		$upload = THINK_PATH;
		$upload = str_replace('\\', "/", $upload);
		$upload = str_replace('ThinkPHP/', "", $upload);//E:/wamp64/www/mytest/oracle/
		$rootPath = $upload."Upload/Scorm";
		//$rootPath = "/home/scorm/Upload/Scorm";//生产环境
		
		//判断目录是否存在
		if(!is_dir($rootPath)){
			$return = array("code" =>1010, "message"=>"上传目录不存在");
			exit(json_encode($return));
		}
		
		if(!$_FILES["zipFile"]){
			$return = array("code" =>1010, "message"=>"上传的文件不要超过150M");
			exit(json_encode($return));
		}
		
		if($_FILES["zipFile"]["error"] > 0){
			$return = array("code" =>1011, "message"=>"上传文件出错");
			exit(json_encode($return));
		}
		
		if($_FILES["zipFile"]["size"] > 1024*1024*150){
			$return = array("code" =>1012, "message"=>"上传的文件不要超过150M");
			exit(json_encode($return));
		}
		
		if(!$_FILES["zipFile"]["tmp_name"]){
			$return = array("code" =>1013, "message"=>"请选择上传的文件");
			exit(json_encode($return));
		}
		
		$fileArr = explode(".",$_FILES["zipFile"]["name"]);
		$type = end($fileArr);
		if($type != 'zip'){
			$return = array("code" =>1014, "message"=>"文件格式必须为zip");
			exit(json_encode($return));
		}
		
		$newFolder = $rootPath."/sco".date("YmdHis");
		mkdir ($newFolder, 0777, true);
		if(!move_uploaded_file($_FILES["zipFile"]["tmp_name"], $newFolder."/scorm.zip")){
			rename($newFolder, $newFolder."_delete");
			$return = array("code" =>1014, "message"=>"文件保存失败，Stored failed:file save error");
			exit(json_encode($return));
		}
		
		$filename = $newFolder."/scorm.zip";
		$resource = zip_open ( $filename );
		if(!is_resource($resource)){
			rename($newFolder, $newFolder."_delete");
			$return = array("code" =>1014, "message"=>"请提交正确的课件zip压缩包");
			exit(json_encode($return));
		}
		
		//解压zip
		$resource = self::get_zip_originalsize($filename, $newFolder);
		
		//----添加课程------------
		F("fileName", null);//文件路径
		$fileTree = self::getFileTree($newFolder, 2);
		$imsPathCN = F("fileName");
		if(is_array($imsPathCN)){
			foreach ($imsPathCN as $key=>$value){
				$chapPath = explode("/", $value);
				$fileLevel = count($chapPath);
				$chapTitle = $chapPath[$fileLevel-3]."-".$chapPath[$fileLevel-2];
				$chapTitle = str_replace(" - Storyline output", "", $chapTitle);
				$scoSubTitle[] = $chapTitle;
				
				if(count($imsPathCN) == 1){
					//只有一个目录时，课程名称用最后一级
					$scoTitle = $chapPath[$fileLevel-2];
				}else{
					$scoTitle = $chapPath[$fileLevel-3];
				}
			}
		}else{
			$return = array("code" =>1014, "message"=>"未获取到imsmanifest.xml文件");
			exit(json_encode($return));
		}
		
		//有中文，变更文件夹名称(最多处理四层)，变更文件夹名称，资源需重新读取
		$fileTree = self::getFileTree($newFolder, 1);
		$fileTree = self::getFileTree($newFolder, 1);
		$fileTree = self::getFileTree($newFolder, 1);
		F("imsmanifest", null);
		$fileTree = self::getFileTree($newFolder, 1);
		$imsmanifest = F("imsmanifest");
		if($imsmanifest){
			$scoHref = array();//课时播放地址
			$identifier = "";//识别码
			$imsPath = implode(";", $imsmanifest);//imsmanifest文件地址
			$scoVersion = "";
			foreach ($imsmanifest as $imsvalue){
				$imsObject = simplexml_load_file($imsvalue);
				$imsObject = self::objToArray($imsObject);
				
				$identifier = $imsObject["@attributes"]["identifier"];//识别码
				$scoVersion = $imsObject["metadata"]["schemaversion"];//版本
				
				//课件播放文件
				$resources = $imsObject["resources"]["resource"];
				$path = str_replace("imsmanifest.xml", "", $imsvalue);
				if(isset($resources["@attributes"]["href"])){
					$scoHref[] = $path.$resources["@attributes"]["href"];
				}else{
					foreach ($resources as $value){
						if($value["@attributes"]["href"]){
							$scoHref[] = $path.$value["@attributes"]["href"];
						}
					}
				}
			}
		}else{
			rename($newFolder, $newFolder."_delete");
			$return = array("code" =>1022, "message"=>"未获取到imsmanifest.xml，请确保Scorm资源包完整");
			exit(json_encode($return));
		}
		/* 
		print_r($scoTitle);
		print_r($scoSubTitle);
		print_r($scoHref);
		//print_r($imsObject);
		exit;
		 */
		$scoFile = str_replace($rootPath, "", $newFolder);
		$imsPath = str_replace($rootPath, "", $imsPath);
		$where["sco_path"] = $scoFile;
		$hasSco = M("sco")->field("name")->where($where)->find();
		if($hasSco){
			$return = array("code" =>1021, "message"=>"此课件已添加过");
			exit(json_encode($return));
		}
		
		$user_id = $_SESSION["user"]["id"];
		$scoData["name"] = $scoTitle;
		$scoData["descs"] = "";
		$scoData["cid"] = "0";
		$scoData["identifier"] = $identifier;
		$scoData["sco_path"] = $scoFile;
		$scoData["ims_path"] = $imsPath;
		$scoData["version"] = $scoVersion;
		$scoData["status"] = "1";
		$scoData["addtime"] = date("Y-m-d H:i:s");
		$scoData["auth_user_id"] = $user_id;
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$scoData['id'] = self::getNextId('sco');
		}
		
		$scoId = M("sco")->add($scoData);
		if($scoId){
			write_login_log(5, 2, $scoTitle);//添加课件写入日志
			
			foreach($scoSubTitle as $key=>$value){
				$chapData["scoid"] = $scoId;
				$chapData["name"] = $value;
				$scoHref[$key] = str_replace($rootPath, "", $scoHref[$key]);
				$chapData["path"] = $scoHref[$key];
				$chapData["orders"] = '0';
				if(strtolower(C('DB_TYPE')) == 'oracle'){
					$chapData['id'] = self::getNextId('sco_chapter');
				}
				M("sco_chapter")->add($chapData);
			}
			
			$return = array("code" =>1000, "message"=>"课程添加成功");
			exit(json_encode($return));
		}else{
			rename($newFolder, $newFolder."_delete");
			$return = array("code" =>1021, "message"=>"课件添加失败，请稍后重试");
			exit(json_encode($return));
		}
	}
	
	/**
	 * 修改课程
	 */
	public function editPage(){
		$scoid = $_GET["scoid"] + 0;
		if(!is_int($scoid) || $scoid < 1){
			echo "非法操作，未获取到课程id";
			exit;
		}
		
		$sco = M("sco")->field("id,name,descs")->where("id=".$scoid)->find();
		$scoChap = M("sco_chapter")->field("id,name,time_len")->where("scoid=".$scoid)->select();
		if(!$sco || !$scoChap){
			echo "非法操作，未获取到课程数据";
			exit;
		}
		
		$data["sco"] = $sco;
		$data["scoChap"] = $scoChap;
		$this->assign($data);
		$this->display("edit_page");
	}
	
	/**
	 * 保存课程
	 */
	public function saveSco(){
		$post = I("post.");
		if(count($post["entry"]) != count($post["chapName"])){
			exit(json_encode(array("code"=>1001, "message"=>"参数不匹配")));
		}
		
		/* if(count($post["entry"]) != count($post["time"])){
			exit(json_encode(array("code"=>1002, "message"=>"参数不匹配")));
		} */
		
		$scoid = $post["scoid"] + 0;
		if(!is_int($scoid) || $scoid < 1){
			exit(json_encode(array("code"=>1003, "message"=>"课程id有误")));
		}
		
		$scoName = trim($post["scoName"]);
		if(!$scoName){
			exit(json_encode(array("code"=>1004, "message"=>"请填写课程名称")));
		}
		
		$scoWhere["id"] = array("neq", $scoid);
		$scoWhere["name"] = $scoName;
		$scoWhere["status"] = 1;
		$specifiedUser = self::specifiedUser();
		$scoWhere["auth_user_id"] = array("in", $specifiedUser);
		$has = M("sco")->where($scoWhere)->find();
		if($has){
			exit(json_encode(array("code"=>1004, "message"=>"课程名称已存在，请更换")));
		}
		
		foreach ($post["entry"] as $key=>$entry){
			$chapName = trim($post["chapName"][$key]);
			if(!$chapName){
				exit(json_encode(array("code"=>1005, "message"=>"请填写章节名称")));
			}
			
			$time = $post["time"][$key] + 0;
			if(!is_int($time) || $time < 0){
				exit(json_encode(array("code"=>1006, "message"=>"章节时长必须为整数，不能小于0")));
			}
		}
		
		$scoData["name"] = $scoName;
		$scoData["descs"] = $post["scoDesc"];
		M("sco")->where("id=".$scoid)->save($scoData);
		
		write_login_log(5, 3, $scoName);//编辑课件写入日志
		
		foreach ($post["entry"] as $key=>$entry){
			$chapName = trim($post["chapName"][$key]);
			$time = $post["time"][$key] + 0;
			$chapData["name"] = $chapName;
			$chapData["time_len"] = $time;
			M("sco_chapter")->where("id=".$entry)->save($chapData);
		}
		
		exit(json_encode(array("code"=>1000, "message"=>"操作成功")));
	}
	
	/**
	 * 删除课件
	 */
	public function delSco(){
		$post = I("post.");
		$scoid = $post["scoid"] + 0;
		if(!is_int($scoid) || $scoid < 1){
			exit(json_encode(array("code"=>1003, "message"=>"课程id有误")));
		}
		
		$scoData["status"] = 0;
		M("sco")->where("id=".$scoid)->save($scoData);
		
		$getLogName = M("sco")->where("id=".$scoid)->find();
		$getLogName = $getLogName["name"];
		write_login_log(5, 4, $getLogName);//删除课件写入日志
		
		exit(json_encode(array("code"=>1000, "message"=>"已删除")));
	}
	
    /**
     * 课程播放
     */
    public function course(){
    	header('Content-Type:text/html; charset=utf-8');
    	$get = I("get.");
    	$scoid = $get["scoid"] + 0;
    	$comid = $get["comid"] + 0;
    	$student_id = $get["student_id"] + 0;
    	$outid = $get["outid"];
    	if(!is_int($scoid) || $scoid < 1){
    		echo "课程id有误";
    		exit;
    	}
    	if(!is_int($comid) || $comid < 1){
    		echo "comid有误";
    		exit;
    	}
    	
    	$sco = M("sco")->where("id=".$scoid)->find();
    	$scoChap = M("sco_chapter")->where("scoid=".$scoid)->select();
    	if(is_int($student_id) && $student_id > 0){
    		$stuWhere["scoid"] = $scoid;
    		$stuWhere["comid"] = $comid;
    		$stuWhere["student_id"] = $student_id;
    		$study = M("study")->where($stuWhere)->limit(1)->select();
    	}
    	
    	$data["scoid"] = $scoid;
    	$data["comid"] = $comid;
    	$data["student_id"] = $student_id;
    	$data["outid"] = $outid;
    	$data["v"] = $get["v"];
    	
    	$data["sco"] = $sco;
    	$data["scoChap"] = $scoChap;
    	$data["study"] = $study[0];
    	$this->assign($data);
    	$this->display();
    }
    
    /**
     * 获取课程
     */
    public function getCourse(){
    	$get = I("get.");
    	$scoid = $get["scoid"] + 0;
    	$comid = $get["comid"] + 0;
    	$entry = $get["entry"] + 0;
    	$student_id = $get["student_id"] + 0;
    	
    	$data["code"] = 1000;
    	$data["message"] = "操作正确";
    	
    	if(!is_int($scoid) || $scoid < 1){
    		$data["code"] = 1002;
    		$data["message"] = "课程id有误";
    		$this->assign($data);
    		$this->display("get_course");
    		exit;
    	}
    	if(!is_int($comid) || $comid < 1){
    		$data["code"] = 1002;
    		$data["message"] = "comid有误";
    		$this->assign($data);
    		$this->display("get_course");
    		exit;
    	}
    	
    	if(is_int($student_id) && $student_id > 0){
    		$stuWhere["scoid"] = $scoid;
    		$stuWhere["comid"] = $comid;
    		$stuWhere["student_id"] = $student_id;
    		$stuWhere["entry"] = $entry;
    		$stuWhere["outid"] = $get["outid"];
    		$study = M("study")->where($stuWhere)->find();
    	}
    	
    	$sco = M("sco")->where("id=".$scoid)->find();
    	if(!$sco){
    		$data["code"] = 1001;
    		$data["message"] = "课程不存在";
    	}
    	$scoChap = M("sco_chapter")->where("scoid=".$scoid." and id=".$entry)->select(); 
    	if(!$scoChap){
    		$data["code"] = 1002;
    		$data["message"] = "章节不存在";
    	}
    	
    	$scoPath = $scoChap[0]["path"];
    	if(!$scoPath || $scoPath == ""){
    		$data["code"] = 1003;
    		$data["message"] = "未获取到章节地址";
    	}
    	$data["scoPath"] = $scoPath;
    	$data["comid"] = $comid;
    	$data["scoid"] = $scoid;
    	$data["entry"] = $entry;
    	$data["student_id"] = $student_id;
    	$data["student_name"] = $student_id;
    	$data["study"] = $study;
    	$data["outid"] = $get["outid"];
    	$data["v"] = $get["v"];
    	$this->assign($data);
    	$this->display("get_course");
    }
    
    /**
     * 负责建立学习对象与平台之间的数据传输管道。当学习者进入开始阅读一个SCO时，SCO第一步就是先要呼叫LMSInitialize，
     * LMSInitialize function判断该学员之上课记录，当学员第一次阅读该门课的该SCO时，LMSInitialize就会将设定初值至相关的环境变量；
     * 若学习者并不是第一次阅读该SCO，LMSInitialize则必须将该学习者之前的上课记录取出，并存入环境变量中，如此即完成启动SCO之动作。
     * LMSInitialize 负责启动SCO，当学习者进入开始阅读一个SCO时，SCO第一步就是先要呼叫LMSInitialize，LMSInitialize function判断该学员之上课记录，
     * 当学员第一次阅读该门课的该SCO时，LMSInitialize就会将设定初值至相关的环境变量；若学习者并不是第一次阅读该SCO，
     * LMSInitialize则必须将该学习者之前的上课记录取出，并存入环境变量中，如此即完成启动SCO之动作。
     * 初始化函数，负责在当前学习的内容对象和LMS之间建立通信连接，并从LMS取得该当前用户关于当前内容对象的学习记录信息，
     * 即整个DM数据结构。内容对象在载入时均会通过该方法获得初始运行时数据。函数成功执行返回"true"(字符串，非布尔值，下同)，否则返回"false"。
     */
    public function LMSInitialize(){
    	$post = I("post.");
    	$LMSAuth = self::LMSAuth($post);
    	if($LMSAuth["code"] != 1000){
    		$return = array("d"=>"false");
    		exit(json_encode($return));
    	}
    	
    	$scoKey = $LMSAuth["scoKey"];
    	if($scoKey){
	    	F($scoKey, null);//清空缓存
    	}else{
    		$return = array("d"=>"false");
    		exit(json_encode($return));
    	}
    	
    	$where["scoid"] = $post["scoid"];
    	$where["comid"] = $post["comid"];
    	$where["student_id"] = $post["student_id"];
    	$where["entry"] = $post["entry"];
    	$where["outid"] = $post["outid"];
    	$has = M("study")->where($where)->select();
    	if($has){
    		$return = array("d"=>"true");
    		exit(json_encode($return));
    	}else{
    		$data["scoid"] = $post["scoid"];//课程id
    		$data["comid"] = $post["comid"];//组织id、银行id
    		$data["student_id"] = $post["student_id"];//学员id
    		$data["student_name"] = $post["student_name"];//学员名称
    		$data["entry"] = $post["entry"];//章节、条目
    		$data["score"] = 0;//学分
    		$data["total_time"] = 0;//总时间
    		$data["lesson_status"] = "not attempted";//课程学习状态 passed（通过）、completed（已完成）、browsed（浏览）、incomplete（非完成）、failed（失败）、not attempted（未尝试）
    		$data["lesson_location"] = 0;//学习位置
    		$data["session_time"] = "0000:00:00";//学习时间
    		$data["core_exit"] = "";//退出状态   ""空，强制退出或是未退出 time-out超时后退出suspend 暂停(非正常退出时标识为“挂起”状态)logout 正常退出
    		$data["lesson_mode"] = "Normal";//访问模式 Browse（预览， 不跟踪学习）、Normal （ 跟踪学习）、Review （ 复习）
    		$data["suspend_data"] = "";//
    		$data["outid"] = $post["outid"];
    		$data["addtime"] = date("Y-m-d H:i:s");
    		$data["endtime"] = date("Y-m-d H:i:s");
    		if(strtolower(C('DB_TYPE')) == 'oracle'){
    			$data['id'] = self::getNextId('study');
    		}
    		$lastId = M("study")->add($data);
    		
    		$return = array("d"=>"true");
    		exit(json_encode($return));
    	}
    }
    
    /**
     * 负责结束学习对象与平台之间的数据传输管道。当学习者阅读完并要离开一个SCO时，在结束时SCO便会将呼叫LMSFinish，LMSFinish主要负责将环境变量重设，
     * 并判断该SCO是否在结束之前己经有呼叫LMSCommit将所有记录回存至LMS，若尚未储存，则会自动呼叫将所有学习者在该SCO的上课记录回存。
	 * 当学习者阅读完并要离开一个SCO时，在结束时SCO便会将呼叫LMSFinish，LMSFinish主要负责将环境变量重设，
	 * 并判断该SCO是否在结束之前己经有呼叫LMSCommit将所有记录回存至LMS，若尚未储存，则会自动呼叫将所有学习者在该SCO的上课记录回存。
     */
    public function LMSFinish(){
    	$return = array("d"=>"true");
    	exit(json_encode($return));
    }
    
    /**
     * 负责储存学员之学习信息。当SCO呼叫欲将某个data model回存时，LMSSetValue第一步先判断所欲回存之data model，判断该data model是否可以set(写入)，
     * 其次判断其型别，当型别错误时，记录其Error Code，当型别检查通过时，则依SCORM 1.2 RTE所订定该data model的处理规则，并将数据存入内存中。
     * 在LMSSetValue 是相当复杂的Function，负责储存所有相关的学习记录，当SCO呼叫欲将某个data model回存时，
     * LMSSetValue第一步先判断所欲回存之data model，判断该data model是否可以set(写入)，其次判断其型别，
     * 当型别错误时，记录其Error Code，当型别检查通过时，则依SCORM1.2 RTE所订定该data model的处理规则，并将数据存入内存中。
     * 
     * cmi.core.lesson_status值，即当前用户对当前SCO的学习状态，包括passed （通过） completed （已完成） browsed （浏览） incomplete （非完成） failed （失败） not attempted （未尝试） 6种状态
     */
    public function LMSSetValue(){
    	$post = I("post.");
		$LMSAuth = self::LMSAuth($post);
    	if($LMSAuth["code"] != 1000){
    		$return = array("d"=>"false");
    		exit(json_encode($return));
    	}
    	
    	$scoKey = $LMSAuth["scoKey"];
    	if(!$scoKey){
    		$return = array("d"=>"false");
    		exit(json_encode($return));
    	}
		
    	$memValue = array($post['LMSName'] => $post["LMSValue"]);
    	self::mergeMem($scoKey, $memValue);
    	
    	//setValue每次更新
    	$strRe = self::CommiteLMSData($post);
    	$return = array("d"=>$strRe);
    	exit(json_encode($return));
    	
    	/* 
    	$return = array("d"=>"true","mem"=>$mem);
    	exit(json_encode($return));
    	 */
    }
    
    /**
     * 负责将章节的所有学习信息数据写入到学习文件中。
     * 相较于LMSSetValue和LMSGetValue，LMSCommit可以说简单多了，其主要负责将所有暂存在内存中的学习记录，回存到LMS，
     * 在设计时应用了XMLHTTP之技术，所以当LMSCommit被呼叫时，会将所有之暂存数据组成XML文件，
     * 再应用XMLHTTP对象将数据POST到 Receiver，当Receiver收到这个Request时，就会解译所传入之XML文件，再将XML文件中的数据直接存入数据库中。
     */
    public function LMSCommit(){
    	$return = array("d"=>"true");
    	exit(json_encode($return));
    }
    
    /**
     * 负责获取学习时产生的错误代码。
     * 该函数将返回一个错误代码，每次API function呼叫后，该函数的值将被重置。（LMSGetErrorString及LMSGetDiagnostic除外）。
     */
    public function LMSGetLastError(){
    	$return = array("d"=>"");
    	exit(json_encode($return));
    }
    
    /**
     * 获得错误码对应的字符串说明，参数为错误码。
     */
    public function LMSGetErrorString(){
    	$return = array("d"=>"");
    	exit(json_encode($return));
    }
    
    /**
     * 获得针对当前错误的诊断信息，参数为错误码。
     */
    public function LMSGetDiagnostic(){
    	$return = array("d"=>"");
    	exit(json_encode($return));
    }
    
    /**
     * LMS公共验证 
     * scoid 课程id 必填
     * comid 组织id 必填
     * student_id 学员id 可选
     */
    private function LMSAuth($param){
    	$scoid = $param["scoid"] + 0;
    	$comid = $param["comid"] + 0;
    	$comid = 1;
    	$student_id = $param["student_id"] + 0;
    	
    	if(!is_int($scoid) || $scoid < 1){
    		return array("code"=>1001, "message"=>"课程id未获取到");
    	}
    	if(!is_int($comid) || $comid < 1){
    		return array("code"=>1002, "message"=>"组织id未获取到");
    	}
    	
    	$scoKey = null;
    	if(is_int($student_id) && $student_id > 0){
    		$scoKey = $scoid+"_"+$comid+"_"+$student_id;//确定缓存数据唯一性
    	}
    	return array("code"=>1000, "message"=>"操作成功", "scoKey"=>$scoKey);
    }
    
    /**
     * $scoid, $student_id, $post["student_name"], $data
     */
    private function CommiteLMSData($param){
    	$scoid = $param["scoid"];
    	$comid = $param["comid"];
    	$student_id = $param["student_id"];
    	$entry = $param["entry"];
    	$outid = $param["outid"];
    	$scoKey = $scoid+"_"+$comid+"_"+$student_id;//确定数据唯一性
    	
    	$where["scoid"] = $scoid;
    	$where["comid"] = $comid;
    	$where["student_id"] = $student_id;
    	$where["entry"] = $entry;
    	$where["outid"] = $outid;
    	$sco = M("study")->where($where)->limit(1)->select();
    	if($sco){
	    	$status = $sco[0]["lesson_status"];
	    	$updata = array();
	    	$LMSDataList = F($scoKey);
	    	if($LMSDataList["cmi.core.lesson_status"]){
		    	if($status == "passed"){
		    		$updata["lesson_status"] = "passed";
		    	}else{
		    		$updata["lesson_status"] = $LMSDataList["cmi.core.lesson_status"];
		    	}
	    	}
	    	
    		if($LMSDataList["cmi.core.lesson_location"]){
    			$updata["lesson_location"] = $LMSDataList["cmi.core.lesson_location"];
    		}else{
    			if($LMSDataList["cmi.core.session_time"]){
    				$session_time = $LMSDataList["cmi.core.session_time"];//0000:05:04.84
    				if($session_time){
    					$session_time_sec = self::sessionTimeToSec($session_time);
    					$updata["lesson_location"] = $session_time_sec;
    				}
    			}
    		}
	    	
	    	if($LMSDataList["cmi.core.session_time"]){
	    		$session_time = $LMSDataList["cmi.core.session_time"];//0000:05:04.84
	    		if(!$session_time || $session_time == ""){
	    			$sessiontime = "0000:00:0.01";
	    		}else{
	    			$sessiontime = $sco[0]["session_time"];
	    		}
	    		
	    		$oldTime = self::sessionTimeToSec($sessiontime);
	    		$nowTime = self::sessionTimeToSec($LMSDataList["cmi.core.session_time"]);
	    		/* if($status != "passed"){
	    			if($oldTime > $nowTime){
	    				$updata["session_time"] = $sessiontime;
	    			}else{
		    			$updata["session_time"] = $LMSDataList["cmi.core.session_time"];
	    			}
	    		} */
    			if($oldTime > $nowTime){
    				$updata["session_time"] = $sessiontime;
    			}else{
	    			$updata["session_time"] = $LMSDataList["cmi.core.session_time"];
    			}
	    	}
	    	
	    	if($LMSDataList["cmi.core.exit"]){
	    		$updata["core_exit"] = $LMSDataList["cmi.core.exit"];
	    	}
	    	if($LMSDataList["cmi.core.lesson_mode"]){
	    		$updata["lesson_mode"] = $LMSDataList["cmi.core.lesson_mode"];
	    	}
	    	if($LMSDataList["cmi.core.student_id"]){
	    		$updata["student_id"] = $LMSDataList["cmi.core.student_id"];
	    	}
	    	if ($LMSDataList["cmi.core.student_name"]){
	    		$updata["student_name"] = $LMSDataList["cmi.core.student_name"];
	    	}
	    	if ($LMSDataList["cmi.core.credit"]){
	    		$updata["credit"] = $LMSDataList["cmi.core.credit"];
	    	}
	    	if ($LMSDataList["cmi.core.entry"]){
	    		$updata["entry"] = $LMSDataList["cmi.core.entry"];
	    	}
	    	if ($LMSDataList["cmi.core.score"]){
	    		$updata["score"] = $LMSDataList["cmi.core.score"];
	    	}
	    	if ($LMSDataList["cmi.core.score.raw"]){
	    		$updata["score"] = $LMSDataList["cmi.core.score.raw"];
	    	}
	    	if ($LMSDataList["cmi.core.total_time"]){
	    		$updata["total_time"] = $LMSDataList["cmi.core.total_time"];
	    	}
	    	if ($LMSDataList["cmi.suspend_data"]){
	    		$updata["suspend_data"] = $LMSDataList["cmi.suspend_data"];
	    	}
	    	
	    	// 更新结果时间：
	    	if($status != "passed"){
	    		$updata["endtime"] = date("Y-m-d H:i:s");
	    	}
	    	
	    	$resp = M("study")->where($where)->save($updata);
	    	if($resp === false){
	    		return "false";
	    	}else{
	    		//F($scoKey, null);
	    		return "true";
	    	}
    	}else{
	    	return "false";
    	}
    }
    
    /**
     * 设置共享范围
     */
    public function saveShare(){
    	$user_id = $_SESSION["user"]["id"];
    	$post = I("post.");
    	
    	$post["source_id"] = (int)$post["source_id"];
    	if($post["source_id"] < 1){
    		exit(json_encode(array("code"=>1001, "message"=>"未获取到来源ID")));
    	}
    	
    	//删除已有的
    	$where["source_id"] = $post["source_id"];
    	$where["type"] = 6;
    	M("resource_sharing")->where($where)->delete();
    	
    	$post["tissueIds"] = explode(",", $post["tissueIds"]);
    	foreach ($post["tissueIds"] as $value){
    		$tissue_id = (int)$value;
    		if($tissue_id > 0){
    			if(strtolower(C('DB_TYPE')) == 'oracle'){
    				$data['id'] = getNextId('resource_sharing');
    			}
    			$data["source_id"] = $post["source_id"];
    			$data["create_user"] = $user_id;
    			$data["create_time"] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
    			$data["type"] = 6;
    			$data["tissue_id"] = $value;
    			$resp = M("resource_sharing")->add($data);
    		}
    	}
    	
    	exit(json_encode(array("code"=>1000, "message"=>"保存成功")));
    }
    
    //更新第三方学习状态
    public function getUPurl($comid){
    	
    }
    
    private function updateStauts(){
    	
    }
    
	/* ------------------------------------- 
	 * 
	 *                开放接口
	 *           
	 * -------------------------------------
	 */    
    /**
     * 获取课程信息
     * scoid 课程id
     * comid 组织id
     */
    public function apiGetCourse(){
    	$post = I("post.");
    	$scoid = $post["scoid"];
    	$comid = $post["comid"];
    	$LMSAuth = self::LMSAuth($post);
    	if($LMSAuth["code"] != 1000){
    		exit(json_encode($LMSAuth));
    	}
    	
    	$sco = M("sco")->field("id,name")->where("id=".$scoid)->find();
    	$scoChap = M("sco_chapter")->field("id,scoid,name")->where("scoid=".$scoid)->select();
    	
    	$return = array("code"=>1000, "message"=>"ok", "sco"=>$sco, "scoChap"=>$scoChap, "pd"=>$post);
    	exit(json_encode($return));
    }
    
    /**
     * 获取课程完成情况
     * scoid 课件id
     * comid 组织id
     * student_id 学员id
     * entry 章节id
     * outid 外部课程id
     */
    public function apiGetStudy(){
    	$post = I("post.");
    	$scoid = $post["scoid"] + 0;
    	$comid = $post["comid"] + 0;
    	$student_id = $post["student_id"] + 0;
    	$entry = $post["entry"] + 0;
    	$LMSAuth = self::LMSAuth($post);
    	if($LMSAuth["code"] != 1000){
    		exit(json_encode($LMSAuth));
    	}
    	
    	if(!is_int($student_id) || $student_id < 1){
    		exit(json_encode(array("code"=>1002, "message"=>"未获取到学员id")));
    	}
    	
    	$where["scoid"] = $scoid;
    	$where["comid"] = $comid;
    	$where["student_id"] = $student_id;
    	$where["entry"] = $entry;
    	$where["outid"] = $post["outid"];
    	$study = M("study")->field("lesson_status")->where($where)->find();
    	$return = array("code"=>1000, "message"=>"ok", "data"=>$study, "postData"=>$post);
    	exit(json_encode($return));
    }
    
    //授权码(一个授权码只给一个机构)
    public function makeAuthCode(){
    	$pre = "ryd-".rand(0, 9999);
    	$code = md5(uniqid($pre));
    	$code = strtoupper($code);
    	$code = str_split($code, 4);
    	unset($code[4]);
    	unset($code[5]);
    	unset($code[6]);
    	unset($code[7]);
    	$code = implode("-", $code);
    	return $code;
    }
    
    //curl请求案例
    public function curlDemo(){
    	//get方式
    	$requestUrl = "http://www.xxxxx.com/apiGetChapter";
    	$ch = curl_init($requestUrl);
    	curl_setopt($ch, CURLOPT_HEADER, 0);//不要header加快效率
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//结果为字符串且输出
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);//设置CURL允许执行的最长秒数
    	$result = curl_exec($ch);
    	curl_close($ch);
    	
    	//post方式
    	$requestUrl = 'http://www.xxxxx.com/apiGetChapter';
    	$post = array(
    			"imgUrl" => "http://www.xxxxx.com/eg.img",
    			"fileName" => "egimg"
    	);
    	$options = array(
    			CURLOPT_RETURNTRANSFER => true,
    			CURLOPT_HEADER         => false,
    			CURLOPT_POST           => true,
    			CURLOPT_POSTFIELDS     => $post
    	);
    	$ch = curl_init($requestUrl);
    	curl_setopt_array($ch,$options);
    	$result = curl_exec($ch);
    	curl_close($ch);
    }
    
    /**
     * 解压zip
     * @param zip压缩包路径 $filename  /data/home/example.zip
     * @param 解压路径 $path 末尾必须带“/”，示例：/data/home/Upload/Scorm/
     */
    public function get_zip_originalsize($filename, $path) {
		//先判断待解压的文件是否存在
		if (! file_exists ( $filename )) {
			return false;
		}
		
		$pathLast = substr($path, -1);
		if($pathLast != "/"){
			$path .= "/";
		}
		
		$starttime = explode ( ' ', microtime () ); //解压开始的时间

		//将文件名和路径转成windows系统默认的GB2312编码，否则将会读取不到
		$encode = mb_detect_encoding($filename, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
		if($encode == "ASCII"){
			$filename = iconv ( "ASCII", "GBK//IGNORE", $filename);
		}else if($encode == "UTF-8"){
			$filename = iconv ( "UTF-8", "GBK//IGNORE", $filename);
		}else if($encode == "BIG5"){
			$filename = iconv ( "BIG5", "GBK//IGNORE", $filename);
		}
		
		$encode2 = mb_detect_encoding($path, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
		if($encode2 == "ASCII"){
			$path = iconv ( "ASCII", "GBK//IGNORE", $path);
		}else if($encode2 == "UTF-8"){
			$path = iconv ( "UTF-8", "GBK//IGNORE", $path);
		}else if($encode2 == "BIG5"){
			$path = iconv ( "BIG5", "GBK//IGNORE", $path);
		}
		
		$resource = zip_open ( $filename );//打开压缩包
		//遍历读取文件
		while ( $dir_resource = zip_read ( $resource ) ) {
			if (zip_entry_open ( $resource, $dir_resource )) {
				//获取当前项目的名称,即压缩包里面当前对应的文件名
				$file_name = $path . zip_entry_name ( $dir_resource );
				$encode3 = mb_detect_encoding($file_name, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
				if($encode3 == "ASCII"){
					$file_name = iconv ( "ASCII", "GBK//IGNORE", $file_name);
				}else if($encode3 == "UTF-8"){
					$file_name = iconv ( "UTF-8", "GBK//IGNORE", $file_name);
				}else if($encode3 == "BIG5"){
					$file_name = iconv ( "BIG5", "GBK//IGNORE", $file_name);
				}
				
				//以最后一个 / 分割,再用字符串截取出路径部分
				$file_path = substr ( $file_name, 0, strrpos ( $file_name, "/" ) );
				
				//如果路径不存在，则创建一个目录，true表示可以创建多级目录
				if (! is_dir ( $file_path )) {
					mkdir ( $file_path, 0777, true);
				}
				
				//如果不是目录，则写入文件
				if (! is_dir ( $file_name )) {
					$file_size = zip_entry_filesize ( $dir_resource );//读取这个文件
					//最大读取6M，如果文件过大，跳过解压，继续下一个
					if ($file_size < (1024 * 1024 * 6)) {
						$file_content = zip_entry_read ( $dir_resource, $file_size );
						file_put_contents ( $file_name, $file_content );
					} else {
						//echo "<p> " . $i ++ . " 此文件已被跳过，原因：文件过大， -> " . iconv ( "GB2312", "UTF-8", $file_name ) . " </p>";
					}
				}
				zip_entry_close ( $dir_resource );//关闭当前
			}
		}
		zip_close ( $resource );//关闭压缩包
		$endtime = explode ( ' ', microtime () ); //解压结束的时间
		$thistime = $endtime [0] + $endtime [1] - ($starttime [0] + $starttime [1]);
		$thistime = round ( $thistime, 3 ); //保留3为小数
		//echo "<p>解压完毕！，本次解压花费：$thistime 秒。</p>";
		return true;
	}
    
	/**
	 * 获取文件--获取到imsmanifest.xml即可终止（最多两层目录即可获取到）
	 * @param $path 文件路径
	 * @param $rename 是否重命名 1是 2否
	 */
	private function getFileTree($path, $rename){
		if(!is_dir($path)){
			return false;
		}
		$fileTree = scandir($path);
		foreach ($fileTree as $key=>$value){
			if($value == "." || $value == ".."){
				continue;
			}
	
			$sonPath = $path ."/". $value;
			if(preg_match("/[\x7f-\xff]/", $value)) {
				if($rename == 1){
					//重命名中文文件/文件夹
					$oldFilePath = $path ."/".$value;
					$newFilePath = $path ."/chap".($key-2);
					rename($oldFilePath, $newFilePath);
					$value = $newFilePath;
				}
			}
			
			if(is_dir($sonPath)){
				$fileTree[$key] = self::getFileTree($sonPath, $rename);
				$fileTree[$key][1] = "folderName-".$value;
			}else{
				if($value == "imsmanifest.xml"){
					if($rename == 2){
						//保存原始路径，(中文文件夹名称将作为章节名称)
						$valueCN = $sonPath;
						$encode = mb_detect_encoding($sonPath, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
						if($encode == "GBK"){
							$valueCN = iconv ( "GBK", "UTF-8//IGNORE", $sonPath);
						}else if($encode == "GB2312"){
							$valueCN = iconv ( "GB2312", "UTF-8//IGNORE", $sonPath);
						}else if($encode == "EUC-CN"){
							$valueCN = iconv ( "EUC-CN", "UTF-8//IGNORE", $sonPath);
						}
						$memKey = mt_rand().mt_rand();
						$memValue[$memKey] = $valueCN;
						self::mergeMem("fileName", $memValue);
					}
					
					//保存imsmanifest.xml路径
					$imsmKey = mt_rand().mt_rand();
					$imsValue[$imsmKey] = $sonPath;
					self::mergeMem("imsmanifest", $imsValue);
					
					$fileTree[$key] = $sonPath;
					break;
				}
			}
		}
		return $fileTree;
	}
    
    //处理xmlObject为数组
    private function objToArray($obj){
    	if(is_object($obj)){
    		$obj = get_object_vars($obj);
    	}
    	if(is_array($obj)){
    		if(count($obj) == 0){
    			$obj = "";
    		}else{
    			foreach ($obj as $k=>$v){
    				$obj[$k] = self::objToArray($v);
    			}
    		}
    	}
    	return $obj;
    }
    
    //处理 0000:05:04.84为秒
    private function sessionTimeToSec($timeStr){
    	if(!$timeStr) return 0;
    	$timeArr = explode(":", $timeStr);
    	$hour = $timeArr[0];
    	$min = $timeArr[1];
    	$sec = $timeArr[2];
    	$totalSec = $hour * 3600 + $min * 60 + $sec;
    	$totalSec = round($totalSec);
    	return $totalSec;
    }
    
    /**
     * 设置数组缓存，防止数据丢失
     * 缓存组合缓存数据 $value为数组
     * $memKey 缓存的key 
     * $memValue 要缓存的数组
     */
    private function mergeMem($memKey, $memValue){
    	if(!is_array($memValue)){
    		F($memKey, $memValue);
    	}else{
	    	$oldMem = F($memKey);
	    	if($oldMem){
	    		foreach ($memValue as $key=>$value){
	    			$oldMem[$key] = $value;
	    		}
	    		F($memKey, $oldMem);
	    	}else{
	    		F($memKey, $memValue);
	    	}
    	}
    }
    
    /**
     * 获取下一个主键ID--用于支持oracle
     * @param  [type] $tbName [表名]
     * @param  [type] $field  [字段名]自增id
     */
    function getNextId($tbName,$field='id'){
    	$sql = "select seq_".$tbName.".nextval id from sys.dual";
    	$result = M()->query($sql);
    	$nextId = $result[0][$field];
    	return $nextId;
    }

    /**
     * 获取该用户当前组织下 - 所有符合数据隔离用户user_id
     * $type = true; 同级数据共享
     * $type = false; 同级数据不共享
     */
    public function specifiedUser($type = true){
    
    	//获取指定范围
    	$tissue_plan = M("tissue_plan")->where("user_id =".$_SESSION['user']['id'])->select();
    
    	$plan_id = array();
    
    	foreach($tissue_plan as $plan){
    		$plan_id[] = $plan['plan_id'];
    	}
    
    	if(!empty($plan_id)){
    
    		$term['plan_id'] = array("in",$plan_id);
    
    		$tissue_auth_list = M("sys_tissue")->field("tissue_id")->where($term)->select();
    	}
    
    	//$tissue_auth_list = M("tissue_auth")->field("tissue_id")->where("user_id=".$_SESSION['user']['id'])->select();
    
    	if(!empty($tissue_auth_list)){
    
    		$ruleData = array();
    
    		foreach($tissue_auth_list as $value){
    			$ruleData[] = $value['tissue_id'];
    		}
    
    	}else{
    
    		//获取[Boy]用户所属组织
    		$boy_pid = M("tissue_group_access a")
    		->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
    		->field('a.tissue_id,b.pid')
    		->where("a.user_id=".$_SESSION['user']['id'])
    		->find();
    
    		$ruleData = array($boy_pid['tissue_id']);
    
    	}
    
    	//获取所有用户ID
    	$where['tissue_id'] = array("in",$ruleData);
    
    	$user_list = M("tissue_group_access")->field("user_id")->where($where)->select();
    
    	foreach($user_list as $list){
    		$rows[] = $list['user_id'];
    	}
    
    	$rows = isset($rows) ? $rows : array(null);
    
    	return $rows;
    
    }
}