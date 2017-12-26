<?php

namespace Mobile\Controller;

use Think\Controller;

/**
 * 线下培训班(选修)
 * @author Andy
 *
 */
class LineTrainController extends CommonController{

    public function __construct() {
        parent::__construct();
    }

    /*******************************兼容oracle************************************************************/

	/**
	 * 线下培训班列表
	 */
	public function lineTrainList(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
		$data = D('LineTrain')->lineTrainList($userId);
        if(!empty($data['data'])){
            $this->success(1000,$data['message'],$data['data']);
        }else{
            $this->error(1030,$data['message']);
        }
	}


    /*******************************兼容oracle************************************************************/
	/**
	 * 线下培训班详情
	 */
	public function lineTrainDetail(){

        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
		$data = D('LineTrain')->lineTrainDetail($userId);
        if($data){
            $this->success(1000,$data['message'],$data['data']);
        }else{
            $this->error(1030,$data['message']);
        }

	}

    /*******************************兼容oracle************************************************************/
	/**
	 * 申请报名
	 */
	public function applyRegistration(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();
		$data = D('LineTrain')->applyRegistration($userId);
        if($data['code'] == 1000){
            $this->success(1000,$data['message']);
        }else{
            $this->error(1030,$data['message']);
        }
	}
}