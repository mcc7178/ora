<?php 
/**
 * 删除30天前的日志文件及缓存  0 2 * * * curl 域名/cronDelLog.php  #每天凌晨2点执行 
 * $path 绝对路径地址
 */
function scanRuntime($path){
	if(!is_dir($path)){
		return false;
	}
	$fileTree = scandir($path);
	foreach ($fileTree as $key=>$value){
		if($value == "." || $value == ".."){
			continue;
		}

		$sonPath = $path ."/". $value;
		if(is_dir($sonPath)){
			$fileTree[$key] = scanRuntime($sonPath);
		}else{
			//filemtime：最后一次修改时间
			$editTime = filemtime($sonPath);//最后一次修改时间-时间戳
			$diffDay = (time() - $editTime) / 86400;
			if($diffDay > 30){
				@unlink($sonPath);
			}
			
			$fileTree[$key] = $sonPath;
		}
	}
}

//清除缓存
$cacheDir = $_SERVER['DOCUMENT_ROOT']."/Runtime/Cache";
scanRuntime($cacheDir);

//清除系统运行日志
$logsDir = $_SERVER['DOCUMENT_ROOT']."/Runtime/Logs";
scanRuntime($logsDir);

?>