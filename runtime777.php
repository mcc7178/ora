<?php 
/**
 * 修改runtime权限为0777
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
		chmod($sonPath, 0777);
		if(is_dir($sonPath)){
			$fileTree[$key] = scanRuntime($sonPath);
		}
	}
}

$cacheDir = $_SERVER['DOCUMENT_ROOT']."/Runtime";
scanRuntime($cacheDir);
?>