<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14
 * Time: 11:18
 */

namespace Mobile\Controller;
use Think\Controller;

/**
 * 所有接口的公共入口类
 * Class IndexController
 * @package Mobile\Controller
 */
class IndexController extends CommonController{


    public function __construct(){
       // parent::__construct();
    }
    /**
     * @Prarm 所有接口调用的公共方法
     * @Prarm $style 需要请求的接口类型参数（整型数字表示）
     *
     */
    public function index(){

        $style = !I('get.style',0,'int') ? I('post.style',0,'int') : I('get.style',0,'int');

        if($style == 0){
            $this->error(1030,'缺少style必要参数');
        }

        if(!C("$style")){
            $this->error(1030,'非法访问');
        }
        $controller = C("$style.0");

        $action = C("$style.1");
        if(!$controller || !$action){
            $this->error(1030,'非法访问');
        }

        A($controller)->$action();
    }
}