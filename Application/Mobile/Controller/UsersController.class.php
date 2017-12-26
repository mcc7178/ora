<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\Upload;
class UsersController extends CommonController
{

    public function __construct()
    {

        parent::__construct();
    }


    /**
     * @免token验证的数据合法性检验公共方法
     * @get
     */
    public function is_get()
    {
        if (!IS_GET) {
            return $this->error(1030, '请求方式有误');
        }
    }

    /**
     * @免token验证的数据合法性检验公共方法
     * @get
     */
    public function is_post()
    {
        if (!IS_POST) {
            return $this->error(1030, '请求方式有误');
        }
    }

    
    /**
     * @lijin
     * @用户注册
     * @组织下拉选择
     * @return array 所有中心组织id及名称
     */
    public function getRegisteTissue(){
        //取出中心的组织id
        $tissue_arr = M('tissue_rule')->where(array('pid'=>1))->select();
         if ($tissue_arr) {
            $this->success(1000, '获取成功', $tissue_arr);
        } else {
            $this->success(1030, '暂无数据返回');
        }
    }

    /**
     * @lijin
     * @用户注册
     * @组织下拉选择
     * @return array 所有中心组织id及名称
     */
    public function test(){
        //取出中心的组织id
        $tissue_arr = D('Trigger')->orderNumber(1,0);
         if ($tissue_arr) {
            $this->success(1000, '获取成功', $tissue_arr);
        } else {
            $this->success(1030, '暂无数据返回');
        }
    }


    /**
     * @lizhongjian
     * @用户注册
     * @邮箱注册
     * @return array 状态码及状态信息
     */
    public function emailRegister(){
        //判断是否是post请求方式
        $this->is_post();
        $data['tissue_id'] = I('post.id', '', 'trim,htmlspecialchars,int');
        session('tissue_id',$data['tissue_id']);
        $data['username'] = I('post.username', '', 'trim,htmlspecialchars');
        $data['email'] = I('post.email', '', 'trim,htmlspecialchars');
        $password = I('post.password', '', 'trim,htmlspecialchars');
        $rePassword = I('post.repassword', '', 'trim,htmlspecialchars');
        $data['emailCode'] = I('post.emailCode', '', 'trim,htmlspecialchars,int');
        // $sendCode = F('sendCode');
        $sendCode = session('sendCode');
        $data['code'] = $sendCode['code'];
        //解密AES处理
        $newPassWord = $this->decryption($password);
        $newrePassWord = $this->decryption($rePassword);
        $data['password'] = $newPassWord;
        $data['repassword'] = $newrePassWord;
        $User = D("Users"); // 实例化User对象
        if (!$User->token(false)->create($data, 1)) { // 指定新增数据
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error(1030, $User->getError());
        } else {
            //验证注册时输入的邮箱和获取邮箱验证码所填写邮箱是否一致
            if ($sendCode['email'] != $data['email']) {
                $this->error(1030, '输入的邮箱和获取邮箱验证码所填写邮箱不一致');
            }
            if ($sendCode['time'] < time()) {
                $this->error(1030, '邮箱验证码已超时,请重新获取');
            }
            // 验证通过 可以进行其他数据操作
            $res = $User->emailRegister($data);
            if ($res['code'] == 1000) {
                $this->success(1000, $res['message']);
            } else {
                $this->success(1030, $res['message']);
            }
        }
    }

    /***********************************兼容oracle**********************************************/
    /**
     * 用户登录
     * 邮箱登录
     * @return array 用户信息数组
     */
    public function login(){
        //判断是否是post请求方式
        $this->is_post();
        $data['loginemail'] = I('post.email', '', 'trim,htmlspecialchars');
        $password = I('post.password', '', 'trim,htmlspecialchars');

        //解密AES处理
        $password = $this->decryption($password);
        $data['pwd'] = $password;
        $Users = D('Users');
        /*if (!$Users->token(false)->create($data, 1)) {
            //如果创建数据对象失败，表示验证不通过，输出错误提示信息
            $this->error(1030, $Users->getError());
        } else {*/
            $loginData = array(
                'email' => $data['loginemail'],
                'password' => $data['pwd']
            );
            $result = $Users->_login($loginData);
            if ($result['code'] == 1000) {
                $this->success($result['code'], $result['message'], $result['data']);
            } else {
                $this->error($result['code'], $result['message']);
            }

    }



    /**
     * 用户退出
     * @return array 状态信息
     */
    public function logout()
    {
        if (IS_POST) {
            $token = I('post.token');
            $model = M('Users');
            $data = array();
            $data['token'] = '';
            $data['token_expires'] = 0;
            $data['last_login_time'] = '';
            $data['is_login'] = 0;
            $result = $model->where(array('token' => $token))->save($data);
            if ($result) {
                $this->success(1000, '成功退出');
            } else {
                $this->error(1023, '请求参数有误');
            }
        } else {
            $this->error(1013, '不合法的请求方式');
        }


    }

    /***********************************兼容oracle**********************************************/
    /**
     * 邮箱+验证码登录
     */
    public function emailCodeLogin(){
        //判断是否是post请求方式
        $this->is_post();
        $data['email'] = I('post.email', '', 'trim,htmlspecialchars');
        $data['emailCode'] = I('post.emailCode', '', 'trim,htmlspecialchars');
        $sendCode = F('sendCode');
        $data['code'] = $sendCode['code'];
        $Users = D('Users');
        if (!$Users->create($data, 1)) {
            $this->error(1030, $Users->getError());
        } else {
            $result = $Users->emailCodeLogin($data, $sendCode);
            if ($result['code'] == 1000) {
                $this->success(1000, $result['message'], $result['data']);
            } else {
                $this->error(1030, $result['message']);
            }
        }
    }





    /**
     * @获取邮箱验证码
     * @Param $email 邮箱号码
     */
    public function getEmailCode($email){
             $email = I('post.email');
      R('Home/Public/sendEmailMessage',array('email',$email));
       // R('Home://Public/sendEmailMessage');
    }


    /*
     *@短信发送获取验证码
     *@return [type] [description]
     *@param $mobile 手机号
     *@param $type  1注册,2手机短信验证码登录
     *@return array 状态码&状态信息
     */

    public function sendCode()
    {
        $mobile = I("request.mobile", '', 'trim,htmlentities');
        $type = I('request.type', '', 'trim,htmlentities');   // 1注册,2手机短信验证码登录
        A('MobileSMS')->getCode($mobile, $type);
    }


     public function lizhongjiantest(){

         $pass = I("request.pass", '', 'trim,htmlentities');
         echo md5($pass);exit;
         //密码验证规则
         if(!preg_match("/^(?![0-9a-z]+$)(?![0-9A-Z]+$)(?![0-9\W]+$)(?![a-z\W]+$)(?![a-zA-Z]+$)(?![A-Z\W]+$)[a-zA-Z0-9\W_]{8,20}+$/",$pass)){
             echo $pass;
             echo '密码不符合';exit;
         }
         echo '密码符合';exit;
     }

}