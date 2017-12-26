<?php
namespace Common\Model;

use Common\Model\BaseModel;
/**
 * 版本更新模型
 */
class AppVersionModel extends BaseModel
{

    /***
    * 版本更新展示页
    */
    public function index(){
        $size = 15;
        $p = I("get.p",1,'int');
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "a.id,a.version_number,a.version_path,a.version_describe,to_char(a.create_time,'YYYY-MM-DD HH24:MI:SS') as create_time,a.user_id,b.username";
        }else{
            $field = 'a.id,a.version_number,a.version_path,a.version_describe,a.create_time,a.user_id,b.username';
        }

        $list = M('app_version')->alias('a')
                ->join("left join __USERS__ b on b.id = a.user_id")
                ->field($field)
                ->page($p.','.$size)
                ->order('id desc')
                ->select();
        
        $count = M('app_version')
                ->field($field)
                ->order('id desc')
                ->count();
        
        $show = $this->pageClass($count,$size);
   
      	$return = array(
		 	'list'=>$list,
		 	'page'=>$show
	  	);
        return $return;    
    }

    /***
    * 增加新版本
    */
    public function addNewVersion($version_number,$version_path){
        $data = array();
        $data = array(
            'version_number'=>$version_number,
            'version_path'=>$version_path,
            'user_id'=>$_SESSION['user']['id'],
            'create_time'=>date('Y:m:d H:i:s')
        );
        
        if(strtolower(C('DB_TYPE')) == 'oracle'){
		    $data['id'] = getNextId('app_version');
            $data['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }

        $res = M('app_version')->add($data);
        if($res){
            $ret = array(
                'status'=>1,
                'info'=>'保存成功'
            );
        }else{
            $ret = array(
                'status'=>0,
                'info'=>'保存失败'
            );
        }
        return $ret;   
    }













}