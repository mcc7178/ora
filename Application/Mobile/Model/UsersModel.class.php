<?php
namespace Mobile\Model;
use Think\Model;

/**
 * Class UsersModel
 * @package Mobile\Model
 * User: @Andy-lizhongjian
 */
class UsersModel extends CommonModel {
    
    protected $tablePrefix = 'think_';
    protected $tableName = 'users';
    
    protected $_validate = array(
        array('tissue_id', 'require', '选择组织不能为空', Model::EXISTS_VALIDATE),
        array('username', 'require', '用户名不能为空', Model::EXISTS_VALIDATE),
        array('username', '2,10', '用户名长度为2-10个字符组成', Model::EXISTS_VALIDATE, 'length'),
        //array('username', 'unique', '用户名已被占用', Model::EXISTS_VALIDATE),
        //array('username','','用户名已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
        array('phone', '/1[34578]{1}\d{9}$/', '请输入正确的手机号格式', Model::EXISTS_VALIDATE, 'regex'),
        array('loginemail', 'require', '邮箱不能为空', Model::EXISTS_VALIDATE),
        array('loginemail', 'email', '邮箱格式不正确', Model::EXISTS_VALIDATE),
        array('password', 'require', '密码不能为空', Model::EXISTS_VALIDATE),
        //array('registerpassword', '8,20', '密码长度为8-20位', Model::EXISTS_VALIDATE, 'length'),
        array('password', 'checkPassWord', '密码须含以下任意三项：数字、大写字母、小写字母、特殊字符(如:~!.@#$%^&*_等)且长度为8-20个字符！', Model::EXISTS_VALIDATE, 'callback'),
        array('pwd', '2,6', '密码长度为8-20位', Model::EXISTS_VALIDATE, 'length'),
        array('repassword','password','确认密码不正确',0,'confirm'),
        array('confirm', 'require', '确认密码不能为空', Model::EXISTS_VALIDATE),
        array('phone', 'empty', '手机号码不能为空', Model::EXISTS_VALIDATE),
        array('phone', 'is_numeric', '手机号码必须为数字', Model::EXISTS_VALIDATE),
        array('email', 'require', '邮箱不能为空', Model::EXISTS_VALIDATE),
        array('emailCode','require', '邮箱验证码不能为空',Model::EXISTS_VALIDATE),
        array('email', 'email', '邮箱格式不正确', Model::EXISTS_VALIDATE),
        array('emailCode', 'code', '邮箱验证码不正确',0,'confirm')
    );
    /*protected $_auto = array (
        array('register_time','time',1,'function'), // 对update_time字段在更新的时候写入当前时间戳
       
    );*/

    public function checkPassWord($passWord) {
        if(!preg_match("/^(?![0-9a-z]+$)(?![0-9A-Z]+$)(?![0-9\W]+$)(?![a-z\W]+$)(?![a-zA-Z]+$)(?![A-Z\W]+$)[a-zA-Z0-9\W_]{8,20}+$/",$passWord)){
            //$data = array("code" => 0, "message" => "密码须含以下任意三项：数字、大写字母、小写字母、特殊字符(如:~!.@#$%^&*_等)且长度为8-20个字符！");
            return false;
        }else{
            return true;
        }
    }
   
    /**
     * 验证邮箱输入合法性
     */
    public function is_email($email){
        $pattern="/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if(preg_match($pattern,$email))
        {      
            return true;
        }else{
            return false;
        }        
    }

    /**
     * 忘记密码手机加验证码登录-设置新密码字段验证
     * $param $post 接收的参数
     * password 密码
     *
     *
     */
    public function setNewPassword($post){
        $data['token'] = $post['token'];
        if(!empty($data['token'])) {
            $info = M('Users')->where(array('token' => $data['token']))->find();
            if (empty($info) || (time() >= $info['token_expires'])) {
                return $this->error(4100, '授权失败或过期');
            } else {
                if ($info['token'] != $data['token']) {
                    return $this->error(1033, '不合法的token');
                }else{
                    if(empty($post['password'])){
                        return $this->error(1006,'密码不能为空');
                    }
                    $msg = $this->checkPassWord($post['password']);
                    if(!$msg){
                        return $this->error(1018,'密码须含以下任意三项：数字、大写字母、小写字母、特殊字符(如:~!.@#$%^&*_等)且长度为8-20个字符！');
                    }else{
                        $data['password'] = md5($post['password']);
                        $list = M("pwd_history")->field("password)")->where(array('user)id'=> $info['id']))->order("id desc")->limit(3)->select();
                        $pass_array = array($list[0]['password'],$list[1]['password'],$list[2]['password']);
                        if(in_array($data['password'],$pass_array)) {
                            return $this->error(1030, '请勿与最近三个使用的密码相同');
                        }
                        //设置新密码
                        $new_info = M('Users')->where(array('token'=>$data['token']))->setField('password',$data['password']);
                        //设置新密码的同时忘密码历史记录表写入一条数据
                        $result = write_pwd_history($info['id'],$data['password']);
                        if($result){
                            $msg['token'] = $data['token'];
                            return $this->success(1000,'修改成功',$msg);
                        }else{
                            return $this->error(1029,'修改失败');
                        }
                    }
                }
            }
        }else{
          return $this->error(1023,'缺少必要参数');
        }
    }
    
    /**
     * 注册
     * @param $sendCode          手机短信验证码
     * @param $confirm           确认密码
     * @param  array $post       注册所需字段
     * @return array             状态值&状态信息|用户标识字段
     */

    public function register($post, $sendCode, $confirm) {
        $data = array();
        $data['username'] = $post['account'];
        $data['phone'] = $post['mobile'];
        $data['password'] = $post['password'];
        if (empty($data['username'])){
            return $this->error(1012, '请输入用户名');
        }
        if (empty($data['phone'])) {
            return $this->error(1017, '请输入手机号');
        }
        if (empty($data['password'])) {
            return $this->error(1006, '请输入密码');
        }

        if (empty($confirm)) {
            return $this->error(1006, '请输入确认密码');
        }
        if (empty($sendCode)) {
            return $this->error(1001, '请输入验证码');
        }
        $model = M('Users');
        $codeModel = M('VerifyCode');
        $info = $model->where(array('phone' => $data['phone']))->order('id desc')->find();
        if (!empty($info['username'])){
            return $this->error(1003, '号码已被注册');
        }
        $msg = $codeModel->where(array('phone' => $data['phone']))->order('id desc')->find();
        if (empty($msg['verify_code'])) {
            return $this->error(1001, '请获取验证码');
        }
        if (time() > $msg['verify_time']){
            return $this->error(1038, '验证码已过期');
        }
        if ($sendCode != $msg['verify_code']){
            return $this->error(1002, '请输入正确的验证码');
        }
        if($data['password'] !== $confirm){
            return $this->error(1007, '密码和确认密码不一致');
        }
        if (!$this->create($data)){
            return $this->error(1013, $this->getError());
        }
        $data['password'] = md5($data['password']);
        $data['register_time'] = date('Y-m-d H:i:s',time());
        $add = $model->add($data);
        if (!$add) {
            return $this->error(1009, '注册失败');
        }
        $message = $model->where(array('phone'=>$data['phone']))->find();
        M('auth_group_access')->add(array("user_id"=>$message['id'],"group_id"=>3));
        M('tissue_group_access')->add(array("user_id"=>$message['id'],"tissue_id"=>0,"job_id"=>0,"manage_id"=>0));
        $res = self::getTaskData($message['id'],9);
        if($res){
            return $this->success(1000,'注册成功',$message);
        }else{
            return $this->error(1030,'注册失败');
        }
    }


/******************************************************兼容oracle*****************************************************************************************/
    /**
     * 邮箱注册
     * @$data controller由model验证完的数组数据
     */
    public function emailRegister($data){
        //根据注册提交的邮箱查询是否已存在该注册邮箱信息
        $tissue_id = $data['tissue_id'];
        $User = M('Users');
        $where['email'] = $data['email'];
        $result = $User->where($where)->find();
        if(!empty($result)){
            if($result['status'] == 2){
                //待审核用户的注册
                return $this->error(1030,'用户待审核状态，请等待审核结果!');
            }else if($result['status'] == 3){
                //注册但被逻辑删除(禁用)的用户登录系统，处理逻辑
                return $this->error(1030,'用户禁用状态，请联系管理员处理!');
            }else if($result['status'] == 1){
                //已注册审核通过用户
                return $this->error(1030,'该号码已注册，请登陆!');

            }else if($result['status'] == 0){
                //已注册的用户但为拒绝状态
                $orderno_data = D('Trigger')->orderNumber(9,0);
                $orderno = $orderno_data['no'];
                $updateData = array(
                    'username' => $result['username'],
                    'password' => $result['password'],
                    'avatar'=> $result['avatar'],
                    "register_time"=> date('Y-m-d H:i:s'),
                    'orderno'=>$orderno,
                    'status'=>2
                );
                if($orderno_data['status'] == 0){
                    $updateData['status'] = 1;
                }
                $status = M('Users')->where(array('id'=>$result['id']))->save($updateData);
                if($status !== false){
                    //用户重新提交注册审核接口触发
                    $res = D('Trigger')->projectResubmit($result['id'],9);
                    $auth =  M('auth_group_access')
                    	->where(array('user_id'=>$result['id']))
                    	->save(array("group_id"=>3));
                    $tissue = M('tissue_group_access')
                    	->where(array('user_id'=>$result['id']))
                    	->save(array("tissue_id"=>$tissue_id,"job_id"=>0,"manage_id"=>0));
                    if($res && $auth && $tissue){
                        return $this->success(1000,'注册成功');
                    }else{
                        return $this->error(1030,'注册失败');
                    }
                }else{
                    return $this->error(1030,'注册失败');
                }
            }
        }else{

            $orderno_data = D('Trigger')->orderNumber(9,0);
            $orderno = $orderno_data['no'];
            $insertData = array(
                'username' => $data['username'],
                'password' => md5($data['password']),
                'avatar'=>'/Upload/avatar/default.png',
                // 'phone'   => $mobile,
                "register_time"=> date('Y-m-d H:i:s'),
                'orderno'=>$orderno,
                'email'=>$data['email']
            );

          //如果是oracle则执行此处，mysql则跳过
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $insertData['id'] = getNextId('users');
                $insertData['register_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            }
            if($orderno_data['status'] == 0){
                $insertData['status'] = 1;
            }
            $insertData['firstlogin'] = 1;
            $lastId = M('Users')->add($insertData);
            // $res = D('Trigger')->projectResubmit($lastId,9);
            if($lastId){
                $auth = M('auth_group_access')->add(array("user_id"=>$lastId,"group_id"=>3));
                $tissue = M('tissue_group_access')
                	->add(array("user_id"=>$lastId,"tissue_id"=>$tissue_id,"job_id"=>0,"manage_id"=>0));
                if($auth && $tissue){
                    $result = write_pwd_history($lastId,md5($data['password']));
                    return $this->success(1000,'注册成功');
                }else{
                    return $this->error(1030,'注册失败');
                }
            }else{
                return $this->error(1030,'注册失败');
            }
        }
    }

 /***********************************兼容oracle**********************************************/

    /**
     * 用户登录
     */
    public function _login($param){
        $data = $this->_checkValidateData($param);
        $User = M('Users');
        if(!empty($data['token'])){
            $info = M('Users')->where(array('token' => $data['token']))->find();
            //查询判断密码是否在90天有效期内
            $pwd_history = M("pwd_history")->where(array('user_id'=>$info['id']))->order('id DESC')->limit(1)->select();
            //密码修改的时间戳与当前时间戳的天数差
            $now_time = (time() - $pwd_history[0]['edit_time'])/86400;
            if($now_time > 90){
                return $this->error(1009, '密码已使用超过90天，请修改密码');
            }
            $UserId = $info['id'];
            unset($info['id']);
            if (empty($info) || (time() >= $info['token_expires'])) {
                return $this->error(4100, '授权失败或过期');
            }else{
                if($info['token'] != $data['token']){
                    return $this->error(1033, '不合法的token');
                }else{
                    if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $info['last_login_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                    }else{
                        $info['last_login_time'] = date('Y:m:d H:i:s',time());
                    }
                    $ip=$_SERVER['REMOTE_ADDR'];
                    $info['last_login_ip'] = $ip;
                    $info['secret_key'] = parent :: SECRET_KEY;
                    $User->where(array('id'=> $UserId,'token' => $data['token']))->save($info);
                    $user_id = $User->where(array('token' => $data['token']))->getField('id');
                    //查询用户身份
                    $user_type = M('Users a')->join('LEFT JOIN __AUTH_GROUP_ACCESS__ b ON a.id = b.user_id LEFT JOIN __AUTH_GROUP__ c ON b.group_id = c.id')->field('c.id')->where(array('a.id'=>$user_id))->select();
                    foreach($user_type as $key => $val){
                        $val['id'] = intval($val['id']);
                        array_push($id_array, $val['id']);
                    }
                    if(count($id_array) == 1 && in_array(3,$id_array)){
                        $user_type = 3;
                    }else{
                        $user_type = 1;
                    }
                    $info['user_type'] = $user_type;
                    $res = D('Trigger')->intergrationTrigger($user_id,1);
                    $info['id'] = $UserId;
                    //暂时加上做调试用
                   // $tokens = $User->where(array('id'=> 1))->getField('token');
                    //$info['token'] = $tokens;
                    return $this->success(1000, '登录成功!',$info);
                }
            }
        }else{
            $info = null;
            $info = $User->where(array('email' => $data['email']))->find();
            if (empty($info)) {
                return $this->error(1030, '用户不存在');
            }

            $UserId = $info['id'];
            unset($info['id']);
            $status = intval($info['status']);
            if ($status == 2) {
                return $this->error(1030, '账号待审核中');
            }
            if ($status == 3) {
                return $this->error(1030, '账号被禁用');
            }
            $account = $info['email'];
            //记录用户登录密码输入信息，写入用户登录记录表
            $user_login_data = array(
                'user_id'=>$UserId,
                'login_time'=>date('Y-m-d H:i:s'),
                'enter_password'=>md5($data['password']),
            );
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $user_login_data['login_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                $user_login_data['id'] = getNextId('user_password_lock');
            }
            if (md5($data['password']) !== $info['password']) {
                    $user_login_data['status'] = 0; //表示密码错误
                    $result = $this->checkLoginPwdTimes($user_login_data);
                if($result){
                    return $this->error(1030, '密码错误!');
                }else{
                    return $this->error(1030, '密码连续输入错误3次,账号已被系统锁定!');
                }
            }
            //判断账号有无被锁定，如果锁定了需要判断锁定时间是否超过30分钟
            //查询此时用户状态是否为被锁状态
            $lock_status = M('users')->where(array('id'=>$UserId,'lock_status'=> 0))->find();
            //已是被锁定状态，查询user_password_lock表获取最后一次被锁定时的登录时间，则判断是否超过30分钟
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $field = "id,to_char(login_time,'YYYY-MM-DD HH24:MI:SS') as login_time";
            }else{
                $field = "id,login_time";
            }
            if(!$lock_status){//锁定，判断锁定时间是否超过30分钟
                $login_time = M('user_password_lock')->field($field)->where(array('user_id'=>$UserId))->order('id desc')->limit(1)->select();
                $margin = time() - strtotime($login_time['login_time']);
                if($margin < 1800){
                    return $this->error(1030, '账号已被系统锁定,请在30分钟后再登录!');
                }else {//超过30分钟可以解锁
                    M('users')->where(array('id'=>$UserId))->save(array('lock_status'=>0));
                }
            }
            $Users_id = M('Users')->where(array('id' => $UserId))->find();
            //查询判断密码是否在90天有效期内
            $pwd_history = M("pwd_history")->where(array('user_id'=>$Users_id['id']))->order('id DESC')->limit(1)->select();
            //密码修改的时间戳与当前时间戳的天数差
            $now_time = (time() - $pwd_history[0]['edit_time'])/86400;
            if($now_time > 90){
                return $this->error(1009, '密码已使用超过90天，请修改密码');
            }
            $info['token'] = $this->makeToken($account);
            $info['token_expires'] = time() + self::TOKEN_EXPIRE;
            $info['is_login'] = 1;
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $info['last_login_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            }else{
                $info['last_login_time'] = date('Y:m:d H:i:s',time());
            }
            $ip=$_SERVER['REMOTE_ADDR'];
            $info['last_login_ip'] = $ip;
            $secret_key = parent :: SECRET_KEY;
            $User->where(array('id' => $UserId,'email' => $data['email']))->save($info);

            $user_id = $User->where(array('email' => $data['email']))->getField('id');
            //查询用户身份
            $id_array = array();
            $user_type = M('Users a')
            	->join('LEFT JOIN __AUTH_GROUP_ACCESS__ b ON a.id = b.user_id')
				->join('LEFT JOIN __AUTH_GROUP__ c ON b.group_id = c.id')
                ->field('c.id')
            	->where(array('a.id'=>$user_id))
            	->select();
            foreach($user_type as $key => $val){
                $val['id'] = intval($val['id']);
                array_push($id_array, $val['id']);
            }
            if(count($id_array) == 1 && in_array(3,$id_array)){
                $user_type = 3;
            }else{
                $user_type = 1;
            }
            //如果是存在1,2,3，那就返回1,2
            $info['user_type'] = $user_type;
            $info['secret_key'] = $secret_key;
            $user_login_data['status'] = 1; //表示密码成功
            $result = M('user_password_lock')->add($user_login_data);
             //调用公共方法 - >写入操作日志
            $this->write_login_log(0,1,"登录成功",$user_id);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
            if($result){
                $info['last_login_time'] = date('Y-m-d H:i:s');
                $info['id'] = $UserId;
                //登录系统触发积分

                $res = D('Trigger')->intergrationTrigger($user_id,1);

                return $this->success(1000, '登录成功!',$info);
            }else{
                return $this->error(1030, '您的账号已被锁定，请30分钟后再试!');
            }
        }
    }


    /**
     * $data  用户输入的登录信息数组
     *校验密码连续输入三次错误后锁则把该用户改为禁用状态
     */
    public function checkLoginPwdTimes($data){
        $user_id = $data['user_id'];
        $list = M('user_password_lock')->where(array('user_id'=>$user_id))->order('id desc')->limit(3)->select();
        //三次连续输入错误，则锁定账号
        $status = 0;
        foreach($list as $key => $val){
            $status += intval($val['status']);
        }
        if($status == 0){
                    //连续三次错误,更改用户状态
                        M('users')->where(array('id'=>$user_id))->save(array('lock_status'=>0));
                        return false;
            }else{
                //返回密码错误
                $res = M('user_password_lock')->add($data);
                return true;
            }
    }


    /**
     * 密码
     */
    /***********************************兼容oracle**********************************************/
    /**
     * 检测登录数据合法性
     * @return array 数据集
     */
    protected function _checkValidateData($post) {
        $data = array();
        if (isset($post['token'])) {
            $data['token'] = $post['token'];
            $data['secret_key'] = $post['secret_key'];
            if (empty($data['token']) || empty($data['secret_key']) || self::SECRET_KEY != $data['secret_key']) {
                $this->error(1023, '请求参数有误');
            }
        } else {
            $data['email'] = $post['email'];
            $data['password'] = $post['password'];
        }
        return $data;
    }


    /***********************************兼容oracle**********************************************/
    /**
     * 检测邮箱+验证码登录数据合法性
     * @return array 数据集
     */
     protected function emailVerifyCode($post) {
         $data = array();
         if (isset($post['token'])) {
             $data['token'] = $post['token'];
             $data['secret_key'] = $post['secret_key'];
             if (empty($data['token']) || empty($data['secret_key']) || self::SECRET_KEY != $data['secret_key']) {
                 return $this->error(1030, '请求参数有误');
             }
         } else {
             $data['email'] = $post['email'];
             $data['emailCode'] = $post['emailCode'];
         }
         return $data;
     }


    /***********************************兼容oracle**********************************************/
    /**
     * 验证忘记密邮箱+码验证码登录数据合法性
     */
    public function emailCodeLogin($param,$sendCode)
    {
        $data = $this->emailVerifyCode($param);
        $Users = M('Users');
        if (!empty($data['token'])) {
            $info = $Users->where(array('token' => $data['token']))->find();
            if (empty($info) || (time() >= $info['token_expires'])) {
                return $this->error(4100, '授权失败或过期');
            } else {
                if ($info['token'] != $data['token']) {
                    return $this->error(1033, '不合法的token');
                } else {
                    if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $info['last_login_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                    }else{
                        $info['last_login_time'] = date('Y:m:d H:i:s',time());
                    }
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $info['last_login_ip'] = $ip;
                    $secret_key = parent :: SECRET_KEY;
                    $Users->where(array('token' => $data['token']))->save($info);
                    $user_id = $Users->where(array('token' => $data['token']))->getField('id');
                    //查询用户身份
                    $user_type = M('Users a')
                    	->join('LEFT JOIN __AUTH_GROUP_ACCESS__ b ON a.id = b.user_id')
						->join('LEFT JOIN __AUTH_GROUP__ c ON b.group_id = c.id')
                    	->where(array('a.id' => $user_id))
                    	->getField('c.id');
                    $info['user_type'] = $user_type;
                    $info['secret_key'] = $secret_key;
                    $res = D('Trigger')->intergrationTrigger($user_id, 1);
                    if ($res) {
                        $info['last_login_time'] = date('Y:m:d H:i:s', time());
                        return $this->success(1000, '登录成功!', $info);
                    } else {
                        return $this->error(1030, '登录失败,未知错误!');
                    }
                }
            }
        } else {
            if ($sendCode['time'] < time()) {
                return $this->error(1030, '邮箱验证码超时，请重新获取!');
            }
            if ($sendCode['email'] != $data['email']) {
                return $this->error(1030, '输入邮箱与获取验证码邮箱不一致!');
            }
            $info = M('Users')->where(array('email' => $data['email']))->find();
            if(!$info) {
                return $this->error(1030, '用户不存在');
            }
                $info['token'] = $this->makeToken($data['email']);
                $info['token_expires'] = time() + self::TOKEN_EXPIRE;
                $info['is_login'] = 1;
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $info['last_login_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
            }else{
                $info['last_login_time'] = date('Y:m:d H:i:s',time());
            }
                $ip = $_SERVER['REMOTE_ADDR'];
                $info['last_login_ip'] = $ip;
                $secret_key = parent :: SECRET_KEY;
                $Users->where(array('email' => $data['email']))->save($info);
                $user_id = $Users->where(array('email' => $data['email']))->getField('id');
                //查询用户身份
                $user_type = M('Users a')
                	->join('LEFT JOIN __AUTH_GROUP_ACCESS__ b ON a.id = b.user_id')
                	->join('LEFT JOIN __AUTH_GROUP__ c ON b.group_id = c.id')
                	->where(array('a.id' => $user_id))
                	->getField('c.id');
                $info['user_type'] = $user_type;
                $info['secret_key'] = $secret_key;
                $res = D('Trigger')->intergrationTrigger($user_id, 1);
                if ($res) {
                    $info['last_login_time'] = date('Y:m:d H:i:s', time());
                    return $this->success(1000, '登录成功!', $info);
                } else {
                    return $this->error(1030, '登录失败,未知错误!');
                }
        }
    }
    /**
     * 允许密码输入6-20位
     */
   public function isPassword($str) {
      
       //preg_match('/^[_0-9a-z]{6,16}$/i',$str)
       ///^((?=.*[a-zA-Z])(?=.*\d)|(?=[a-zA-Z])(?=.*[#@!~%^&*])|(?=.*\d)(?=.*[#@!~%^&*]))[a-zA-Z\d#@!~%^&*]{6,20}$/i
       if (!preg_match('/^((?=.*[a-zA-Z])(?=.*\d)|(?=[a-zA-Z])(?=.*[#@!~%^&*])|(?=.*\d)(?=.*[#@!~%^&*]))[a-zA-Z\d#@!~%^&*]{8,20}$/i',$str)){
            return false;
        }else{
            return true;
       }
}


    /*
     * 验证手机号是否正确
     * @author lizhongjian
     * @param  $mobile
     */
    public function isMobile($mobile) {
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',$mobile) ? true : false;
    }





    /**
     * 获取客户端ip的方法
     * @param int $type
     * @return mixed
     */
    function get_client_ip($type = 0) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($_SERVER['HTTP_X_REAL_IP']){//nginx 代理模式下，获取客户端真实IP
            $ip=$_SERVER['HTTP_X_REAL_IP'];
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}