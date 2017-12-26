<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 中保协学习相关
 * @author Dujuqiang 20171121
 * 
 * 中国保险行业协会（The Insurance Association of China，IAC），简称中保协
 */
class IacController extends AdminBaseController{
	public function index(){
		$resp = D("Iac")->index();
		$data["goStudy"] = 0;//是否前往学习 0否 1是
		if(!$resp){
			$data["code"] = 1001;
			$data["message"] = "未获取到用户数据";
		}else{
			$type = I("get.type");
			$type = (int)$type;
			if($type > 0 && $type < 4){
				$data["goStudy"] = 1;
			}
			
			if($type == 1) $data["name"] = "新课速递";
			if($type == 2) $data["name"] = "营销学院";
			if($type == 3) $data["name"] = "保险大讲堂";
			
			$data["type"] = $type;
			$data["sourceCode"] = "tpjh001";
			$data["trueName"] = $resp["username"];
			$data["telephone"] = $resp["phone"];
			$data["loginName"] = $resp["email"];
			$data["email"] = $resp["email"];
			
			//Md5加密（type+ trueName + telephone+ loginName+ email +  sud1HfuSXSvOhMoP3iYr26jg），最后的sud1HfuSXSvOhMoP3iYr26jg是md5加密串(私钥字符串) 
			$checkBuffer = $data["type"].$data["trueName"].$data["telephone"].$data["loginName"].$data["email"]."sud1HfuSXSvOhMoP3iYr26jg";
			$checkBuffer = urlencode($checkBuffer);//处理汉字编码问题
			$checkValue = strtoupper(md5($checkBuffer));
			$data["checkValue"] = $checkValue;
			$data["code"] = 1000;
			$data["message"] = "操作成功";
		}
		
		//print_r($data);
		
		$this->assign($data);
		$this->display();
	}
	
	/**
	 * 中保协数据导入页面
	 * 
	 */
	public function listPage(){
		$get = I("get.");
		$get["p"] = (int)$get["p"];
		if($get["p"] < 1) $get["p"] = 1;
		
		if(!preg_match("/^[0-9]{4}$/", $get["year"])){
			$get["year"] = "";
		}
		
		$data = D("Iac")->listPage($get);
		//print_r($data);
		
		$yearList = array();
		for ($i=0; $i<10; $i++){
			$yearList[$i] = date("Y", strtotime("-$i year"));
		}
		
		$data["yearList"] = $yearList;
		$data["year"] = $get["year"];
		
		//print_r($data);
		
		$this->assign($data);
		$this->display();
	}
	
	/**
	 * 设置是否展示中保协数据
	 */
	public function set(){
		$status = I("post.iacStatus");
		if($status != 1) $status = 0;
		
		$set = M("iac_set")->find();
		if($set){
			$data['status'] = $status;
			$where["auth_user_id"] = 0;
			M("iac_set")->where($where)->save($data);
		}else{
			$data['status'] = $status;
			$data['auth_user_id'] = 0;
			M("iac_set")->add($data);
		}
		$return = array("code" =>1000, "message"=>"操作成功");
		exit(json_encode($return));
	}
	
	/**
	 * 中保协文件内容展示页面
	 * 
	 */
	public function content(){
		$data = D("Iac")->content();
		$data["id"] = I("get.id");
		$data["title"] = I("get.title");
		$this->assign($data);
		$this->display();
	}
	
	/**
	 * 中保协数据导入
	 */
	public function import(){
		if(!$_FILES["iacFile"]){
			$return = array("code" =>1010, "message"=>"请上传文件");
			exit(json_encode($return));
		}
		
		if($_FILES["iacFile"]["error"] > 0){
			$return = array("code" =>1011, "message"=>"上传文件出错");
			exit(json_encode($return));
		}
		
		if($_FILES["iacFile"]["size"] > 5242880){
			$return = array("code" =>1012, "message"=>"上传的文件不要超过5M");
			exit(json_encode($return));
		}
		
		if(!$_FILES["iacFile"]["tmp_name"]){
			$return = array("code" =>1013, "message"=>"请选择上传的文件");
			exit(json_encode($return));
		}
		
		$fileArr = explode(".",$_FILES["iacFile"]["name"]);
		$type = end($fileArr);
		if($type != 'csv' && $type != 'xls' && $type != 'xlsx'){
			$return = array("code" =>1014, "message"=>"文件格式必须为csv、xls或xlsx");
			exit(json_encode($return));
		}
		
		$file = $_FILES["iacFile"]["tmp_name"];
		
		$excelCon = self::importExcel($file);
		$excelCon = $excelCon["data"];
		
		$data = D("Iac")->import($_FILES["iacFile"]["name"], $excelCon);
		exit(json_encode($data));
	}
	
	/**
	 * 数据删除--测试使用，正式环境不能用
	 */
	public function del(){
		$id = I("get.id");
		if(!$id){
			$where["add_time"] = array("like", date("Y-m")."%");
			$fileId = M("iac_file")->where($where)->find();
			if(!$fileId){
				exit;
			}else{
				$fileId = $fileId["id"];
			}
		}else{
			$fileId = $id;
		}
		M("iac_content")->where("file_id=".$fileId)->delete();
		M("iac_file")->where("id=".$fileId)->delete();
		
		echo "删除成功";
	}
	
	/**
	 * 导入Excel处理
	 * $file 有效的excel文件地址
	 */
	public function importExcel($file){
		Vendor("PHPExcel.PHPExcel");
		if(!$file){
			$return = array("code" =>1020, "message"=>"请上传文件");
			return $return;
		}
		$readType = \PHPExcel_IOFactory::identify($file);  //识别文件类型
		$excelReader = \PHPExcel_IOFactory::createReader($readType);
		if($excelReader instanceof \PHPExcel_Reader_CSV){
			$excelReader->setInputEncoding('GBK');
		}
	
		$PHPExcelObj = $excelReader->load($file);
		$currentSheet = $PHPExcelObj->getSheet(0);            //选取第一张表单(Sheet1)为当前操作的表单
		$excelRows = $currentSheet->getHighestRow();          //获取最大行
		$excelColumn = $currentSheet->getHighestColumn();     //获取最大列(获取到的是字母 ABCD......)
		$excelColumnIndex = \PHPExcel_Cell::columnIndexFromString($excelColumn);//字母转为数字
	
		if($excelRows <= 1){
			$return = array("code" =>1021, "message"=>"空文件");
			return $return;
		}
		if($excelRows > 1000){
			$return = array("code" =>1021, "message"=>"一次最多导入1000行数据");
			return $return;
		}
		if($excelColumn > 20){
			$return = array("code" =>1022, "message"=>"文件列数不能超过20列");
			return $return;
		}
	
		//没有0行
		$rowTop = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$excelContent = array();
		$noDataNum = 0;
		for($i=1; $i<=$excelRows; $i++){
			$rowValue = array();
			$isNullRow = false;
			for($j=0; $j<$excelColumnIndex; $j++){
				$rowValue[$j] = $currentSheet->getCell($rowTop[$j].$i)->getValue();
	
				//过滤非正常数据【文本存在换行】--排除时间格式
				if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}/", $rowValue[$j])){
					$rowValue[$j] = preg_replace("/([\s]+)/","", $rowValue[$j]);
				}
				
				if($rowValue[$j]){
					$isNullRow = true;
				}
			}
			if(!$isNullRow){
				$noDataNum ++;
			}
			//空行大于5行了，终止
			if($noDataNum > 5){
				break;
			}
			array_push($excelContent, $rowValue);
		}
	
		$return = array("code" =>1000, "message"=>"成功", "data"=>$excelContent);
		return $return;
	}
}
	