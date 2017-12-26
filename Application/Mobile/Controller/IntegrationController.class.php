<?php
/**
 * Created by PhpStorm.
 * User: lizhongjian
 * Date: 2017/3/9
 * Time: 16:38
 * 我的积分控制器
 */

namespace Mobile\Controller;
use Think\Controller;

class IntegrationController extends CommonController
{


    /**
     * 初始化
     */
    function __construct()
    {
        parent::__construct();
    }

    /*************************同步oracle***********************************/
    /**
     * 积分规则列表
     */
    public function integrationList(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataGet();
            $info = D('Integration')->integrationlist($userId);
            if($info){
                $this->success(1000,'获取成功',$info);
            }else{
                $this->error(1030,'获取失败');
            }
    }


    /**
     * 申请加积分
     */
    public function applyAddStore(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataPost();
            $postData = I('post.');
            $model = M('Integration_apply');
            $info = D('Integration')->applyAddStore($postData,$model,$userId);
            if(!empty($info['data'])){
                $this->success($info['code'],$info['message'],$info['data']);
            }else{
                $this->error($info['code'],$info['message']);
            }
    }

/*************************************************兼容oracle******************************************************/
    /**
     * 申请加学分
     */
    public function applyAddCredits(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataPost();

        $table = 'think_credits_apply';
        //接收学分申请标题字段
        $postData['apply_title'] = I('post.title','','trim');
        //接收学分申请描述字段
        $postData['apply_description'] = I('post.description','','trim');
        //接收申请加学分分值
        $postData['add_score'] = I('post.score',0,'int');
        //接收申请加学分上传的附件
        $postData['attachment'] = I('post.attachment','','trim');
        $Integration = D('Integration')->table($table);
        if(!$Integration->token(false)->create($postData,1)){
            $this->error(1030,$Integration->getError());
        }else{
            //字段合法性验证通过
            $postData['user_id'] = $userId;
            $postData['add_time'] = time();
            $model = M('Credits_apply');
            $info = D('Integration')->applyAddCredits($postData,$model);
            if($info['code'] == 1000){
                $this->success($info['code'],$info['message']);
            }else{
                $this->error($info['code'],$info['message']);
            }
        }
    }


    /******************************************************兼容oracle********************************************************************/
    /**
     * 获取学分兑换积分所需显示的数据
     *
     *
     */
    public function  getExchangeIntergration(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $info = D('Integration')->getExchangeIntergration($userId);
        if($info['code'] === 1000){
            $this->success(1000,$info['message'],$info['data']);
        }else{
            $this->error(1030,$info['message']);
        }
    }


    /*********************************************************兼容oracle****************************************************************************/
    /**
     * 学分兑换积分
     * @Parmam $credits 输入的学分值
     */
    public function IntergrationExchange(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $post['userId'] = $userId = $this->verifyUserDataPost();
        //接收兑换积分所需要的学分
        $post["excVal"] = I('post.excVal',0,'int');
        //学分兑换积分的兑换率
        $post["excRule"] = I('post.excRule',0,'int');

        if($post["excVal"] < 1){
            $this->error(1030,"输入学分必须为大于0的整数");
        }

        if($post["excRule"] < 1){
           $this->error(1030,"当前兑换率必须为大于0的整数");
        }
        $valid_credits = F("valid_credits");
        if($post["excVal"] > $valid_credits){
           $this->error(1003, "啊哦...可兑换的学分不足");
        }
        $data = D("Integration")->IntergrationExchange($post);
        if($data){
            $this->success(1000, "操作成功");
        }else{
            $this->error(1030, "操作失败");
        }

    }
    /******************************************************兼容oracle********************************************************************/
    /*
     * 申请加分上传附件
     */
    public function uploadImg(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $post['userId'] = $userId = $this->verifyUserDataPost();
            if (!empty($_FILES["file"]["name"])) {
                //图片上传设置
                $config = array(
                    'maxSize' => 3145728,
                    'savePath' => 'creditsApply/',//保存子目录
                    'rootPath' => './Upload/',//保存根目录
                    'saveName' => array('uniqid', ''),
                    'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                    'autoSub' => true,
                    'subName' => array('date', 'Ymd')
                );
                $msg = $this->uploadImages($config);
                //保存图片路径
                if($msg){
                    $this->success(1000,'上传成功',$msg);
                }else{
                    $this->error(1025,'上传失败');
                }
            }else{
                $this->error(1025,'没有文件被上传');
            }
    }


    /**
     * 申请加分记录
     */
    public function applyAddStoreRecord(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
        $userId = $this->verifyUserDataGet();
        $get = I('get.');
        $get['page'] = $get['page'] ? $get['page'] : 1;
        $get['pageNum'] =  15;
        $data = M('integration_apply')
            ->where(array('user_id'=>$userId))
            ->limit(($get['page']-1) * $get['pageNum'] . ',' . $get['pageNum'])->order('add_time DESC')->select();
        foreach($data as $k=>$v){
            $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        if($data){
            $this->success(1000,'获取成功',$data);
        }else{
            $this->error(1030,'暂无数据返回');
        }
    }


    /**
     * 我的积分列表
     */
    public function myIntegration(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataGet();
            $page = I('get.page');
            $data = D('Integration')->myIntegration($page,$userId);
            if($data){
                $this->success(1000,'获取成功',$data);
            }else{
               $this->error(1030,'获取失败');
            }
    }

    /**
     * 兑换积分-福利社
     */
    public function myIntegrationExchange(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataGet();
            $get = I('get.');
            $res = D('Integration')->integrationExchange($get,$userId);
            if($res){
                $this->success($res['code'],$res['message']);
            }else{
                $this->error($res['code'],$res['message']);
            }
    }

    /**
     * 积分记录
     * type 1全部  2获取 3使用
     */
    public function myIntegrationWater(){
        //判断用户是否存在,获取用户id,判断提交方式是否合法
            $userId = $this->verifyUserDataGet();
            $page = I('get.page',1,'int');
            $type = I('get.type',0,'int');
            if(empty($type)){
               return $this->error(1030,'缺少必要参数');
            }
            $info = D('Integration')->myIntegrationWater($page,$type,$userId);
            if ($info['code'] == 1000) {
                $this->success(1000, $info['message'], $info['data']);
            } else {
                $this->error(1030,$info['message']);
            }
    }
}