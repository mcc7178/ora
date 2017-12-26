<?php
namespace Home\Controller;

use Common\Controller\HomeBaseController;

/**
 * 商城首页Controller
 */
class IndexController extends HomeBaseController
{
    /**
     * 首页
     */
    public function index()
    {
        if (IS_POST) {

            // 做一个简单的登录 组合where数组条件
            $Verify = new \Think\Verify();

            $captcha = I('post.code');

            if (!$Verify->check($captcha)) {

                $data = array("code" => 0, "message" => "验证码输入错误");

                $this->ajaxReturn($data);

            } else {


                $map = array();
                $account = I("post.username"); //账号

                $password = I("post.keycode");


                $map['status'] = array('neq',3);


                if (strtolower($account) == "admin") {
                    $map["username"] = strtolower($account);
                } else {
                    // $map["phone"]=I("post.username"); //手机号码登录
                    $map["email"] = I("post.username");  //邮箱登录
                }

                $user_data = M('Users')->where($map)->find();
                if (empty($user_data)) {

                    $rdata = array("code" => 1, "message" => "该用户不存在，请马上注册！");

                    $this->ajaxReturn($rdata);

                 }
                

                //检测锁定时间是否超过30分钟，超过则解锁，不删除连续密码错误的登录记录-think_user_password_lock表
                $this->lockCheck($user_data['id']);

                $user_data = M('Users')->where(array('id'=>$user_data['id']))->find();
                //已注册但被逻辑删除(禁用)的用户登录系统，处理逻辑
                if ($user_data['lock_status'] == 1) {

                    $data = array("code" => 1, "message" => "您的账号已被锁定，请30分钟后再试！");

                    $this->ajaxReturn($data);

                }

                $map['password'] = D('Register')->decryption($password);
                // $map['status'] = array('neq',3);
                $data = M('Users')->where($map)->find();

                if (empty($data)) {

                    //写入用户登录记录表
                    $user_login_data = array(
                        'user_id'=>$user_data['id'],
                        'login_time'=>date('Y-m-d H:i:s'),
                        'enter_password'=>$map['password'],
                        'status'=>0
                    );
                    if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $user_login_data['login_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                        $user_login_data['id'] = getNextId('user_password_lock');
                    }
                    $this->conLog($user_login_data);

                    $data = array("code" => 1, "message" => "账号或密码错误！");
                    $this->ajaxReturn($data);

                } else {
                    //已注册但待审核的用户登录系统，处理逻辑
                    if ($data['status'] == 2) {

                        $data = array("code" => 1, "message" => "用户待审核状态，请等待审核结果！");

                        $this->ajaxReturn($data);

                    }
                    //已被审核拒绝的用户登录系统，处理逻辑
                    if ($data['status'] == 0) {

                        $data = array("code" => 1, "message" => "审核已拒绝，请重新注册！");

                        $this->ajaxReturn($data);

                    }

                    //已注册但被逻辑删除(禁用)的用户登录系统，处理逻辑
                    if ($data['status'] == 3) {

                        $data = array("code" => 1, "message" => "该用户已删除，请重新注册！");

                        $this->ajaxReturn($data);

                    }
					
                    //查看密码是否已使用90天
                    if($user_data){
                    	$where2["user_id"] = $user_data["id"];
                    	$gethistory = M("pwd_history")->where($where2)->order("id desc")->find();
                    	if($gethistory["edit_time"]){
                    		$useDays = time() - $gethistory["edit_time"];
                    		$useDays = round($useDays / 86400, 2);
                    		if($useDays > 90){
                    			//密码已使用90天需更换
                    			$errorData = array("code" => 1003, "message" => "密码已过90天有效期，请修改", "email"=>$user_data["email"]);
                    			$this->ajaxReturn($errorData);
                    		}
                    	}
                    }
                    
                    $where['a.user_id'] = array("eq", $data['id']);

                    $array = M('auth_group_access a')
                        ->join("LEFT JOIN __AUTH_GROUP__ b ON a.group_id = b.id")
                        ->order("a.group_id asc")
                        ->where($where)
                        ->select();

                    foreach ($array as $arr) {
                        if ($arr['group_id'] != 3) {
                            $title = $arr['title'];
                            break;
                        } else {
                            $title = $arr['title'];
                        }
                    }

                    $_SESSION['user'] = array(
                        'id' => $data['id'],
                        'username' => $data['username'],
                        'avatar' => $data['avatar'],
                        'type' => $title
                    );

                    $lastdate = date('Y-m-d H:i:s');

                    $data = array();

                    $data['id'] = $data['id'];

                    $data['last_login_time'] = $lastdate;

                    $data['last_login_ip'] = $_SERVER["REMOTE_ADDR"];

                    M('Users')->where(array("id" => array("eq", $data['id'])))->setField($data);
                   
                    //写入用户登录记录表
                    $user_login_data = array(
                        'user_id'=>$_SESSION['user']['id'],
                        'login_time'=>date('Y-m-d H:i:s'),
                        'enter_password'=>$map['password'],
                        'status'=>1
                    );
                    if(strtolower(C('DB_TYPE')) == 'oracle'){
                        $user_login_data['login_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                        $user_login_data['id'] = getNextId('user_password_lock');
                    }
                    $this->conLog($user_login_data);


                    $data = array("code" => 3, "message" => "即将登陆");

                    //调用公共Model里的方法,登录系统积分触发
                    D('Trigger')->intergrationTrigger($_SESSION['user']['id'],1);

                    //调用公共方法 - >写入操作日志
                    write_login_log(0,1,"登录成功");

                    $this->ajaxReturn($data);
                
                }
            }
        } else {
            if (check_login()) {

                // 显示有权限的菜单
                $auth = new \Think\Auth();

                $groups = $auth->getGroups($_SESSION['user']['id']);

                foreach ($groups as $item) {
                    if ($item['group_id'] == 1) {
                        session('home', 'indexDirector');
                        break;
                    } elseif ($item['group_id'] == 2) {
                        session('home', 'indexLecturer');
                        break;
                    } else {
                        session('home', 'indexStudent');
                        break;
                    }
                }

                $this->redirect('Admin/Index');
            } else {

                $typeid = I('get.typeid') ? I('get.typeid') : 1;
                $this->assign('typeid', $typeid);

                //取出注册时的省份和城市显示  ["pid = 0 and name!='台湾' and name!='香港' and name!='澳门'"]
                $where = array(
                    'pid' => 0,
                    'name' => array('not in', array('台湾', '香港', '澳门'))
                );
                $province = M('province_city_area')->where($where)->select();
                foreach ($province as $k => $v) {
                    $province[$k]['city'] = M('province_city_area')->where(array('pid' => $v['id']))->select();
                }


                //取出中心的组织id
                $tissue_arr = M('tissue_rule')->where(array('pid'=>1))->select();


                $this->assign('province', $province);
                $this->assign('tissue_arr', $tissue_arr);
                // print_r($province);
                
                $this->display();
            }
        }
    }

    /**
     *  检测用户登录密码错误3次是否超过30分钟
     */
    public function lockCheck($user_id)
    {
        $lock_status = M('users')->where(array('id'=>$user_id))->getField('lock_status');
        if($lock_status == 1){
        $data = M('user_password_lock')->field("to_char(login_time,'YYYY-MM-DD HH24:MI:SS') as login_time")->where(array('user_id'=>$user_id))->order('id desc')->limit(1)->find();
        $margin = time() - strtotime($data['login_time']);
        if($margin > 1800){
          
          M('users')->where(array('id'=>$user_id))->save(array('lock_status'=>0));  
        //   $res = M('user_password_lock')->where(array('user_id'=>$user_id))->order('id desc')->limit(3)->delete();
        }

        }

    }

    /**
     *记录登录情况-三次连续错误后则把该用户改为锁定状态
     */
    public function conLog($data)
    {

        $res = M('user_password_lock')->add($data);
        $user_id = $data['user_id'];
        $list = M('user_password_lock')->field('status')->where(array('user_id'=>$user_id))->order('id desc')->limit(3)->select();
        if($list){
            $count = count($list);
            if($count >= 3){
                foreach($list as $k=>$v){
                    if($v['status'] != 0) return;
                }
                //三次错误
                M('users')->where(array('id'=>$user_id))->save(array('lock_status'=>1));
            }
        }
    }


    /**
     *注册时二级联动菜单
     */
    public function Linkage($province = "广东")
    {
        if (IS_POST) {
            $province = I('get.province');
            // clog($province);
            $id = M('province_city_area')->where(array('name' => $province))->getField('id');
            // ECHO $id;
            $data = M('province_city_area')->where(array('pid' => $id))->select();
            // clog($array);
            // PRINT_R($array);
            $this->ajaxReturn($data);
        }
    }

    /**
     *短信验证码校验登陆,忘记密码登录---手机号码
     * @return [type] [description]
     */

    public function checkcode()
    {

        $mobile = I("post.mobile");

        $code = I("post.code");   //手机验证码

        $codes = I("post.codes");  //图片验证码

        // $sendCode = F('sendCode');
        
        $sendCode = session('sendCode');

        //手机号码及验证码逻辑
        if ($code == $sendCode['code'] && $sendCode['mobile'] == $mobile) {

            $map = array();

            $map["phone"] = $mobile;

            $data = M('Users')->where($map)->find();

            if (empty($data)) {

                $data = array("code" => 1, "message" => "手机号码未注册！");

                $this->ajaxReturn($data);

            } else {
                //图片验证码验证
                $Verify = new \Think\Verify();

                $captcha = I('post.codes');

                if (!$Verify->check($captcha)) {

                    $data = array("code" => 0, "message" => "图片验证码输入错误");

                    $this->ajaxReturn($data);

                }

                $_SESSION['user'] = array(
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'avatar' => $data['avatar']
                );

                $lastdate = date('Y-m-d H:i:s');

                $data = array();

                $data['id'] = $data['id'];

                $data['last_login_time'] = $lastdate;

                $data['last_login_ip'] = $_SERVER["REMOTE_ADDR"];

                M('Users')->where(array("id" => array("eq", $data['id'])))->setField($data);

                $data = array("code" => 3, "message" => "即将登陆");

                $this->ajaxReturn($data);
            }

        } else {

            $data = array("code" => 0, "message" => "手机号码或手机验证码输入错误！");

            $this->ajaxReturn($data);

        }

    }

    /**
     *邮箱验证码校验登陆,忘记密码登录---邮箱忘记密码
     * @return [type] [description]
     */

    public function emaillogincheckcode()
    {

        $email = I("post.email");

        $emailcode = I("post.emailcode");   //邮箱验证码

        $codes = I("post.codes");  //图片验证码

        // $sendCode = F('sendCode');

       $sendCode = session('sendCode');

        //邮箱及验证码逻辑
        if ($emailcode == $sendCode['code'] && $sendCode['email'] == $email) {

            $map = array();

            $map["email"] = $email;

            $data = M('Users')->where($map)->find();

            if (empty($data)) {

                $data = array("code" => 1, "message" => "邮箱未注册！");

                $this->ajaxReturn($data);

            } else {
                //图片验证码验证
                $Verify = new \Think\Verify();

                $captcha = I('post.codes');

                if (!$Verify->check($captcha)) {

                    $data = array("code" => 0, "message" => "图片验证码输入错误");

                    $this->ajaxReturn($data);

                }

                $_SESSION['user'] = array(
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'avatar' => $data['avatar']
                );

                $lastdate = date('Y-m-d H:i:s');

                $data = array();

                $data['id'] = $data['id'];

                $data['last_login_time'] = $lastdate;

                $data['last_login_ip'] = $_SERVER["REMOTE_ADDR"];

                M('Users')->where(array("id" => array("eq", $data['id'])))->setField($data);

                $data = array("code" => 3, "message" => "即将登陆");

                $this->ajaxReturn($data);
            }

        } else {

            $data = array("code" => 0, "message" => "邮箱或邮箱验证码输入错误！");

            $this->ajaxReturn($data);

        }

    }


    /**
     * 退出
     */
    public function logout()
    {
        //调用公共方法 - >写入操作日志
        write_login_log(0);
        session('user', null);
        $this->success('退出成功、前往登录页面', U('Home/Index/index'));
    }

    /**
     * 忘记密码，密码修改
     */
    public function newpasswordSave()
    {
        if (IS_AJAX) {

            $pw = I('post.newpassword');
            $pw = self::aseDecode($pw);
            $pwdAuth = self::pwdAuth($pw);

            if($pwdAuth["code"] != 1000){
                $this->ajaxReturn($pwdAuth);
            }

            if (strlen($pw) < 8 || strlen($pw) > 20) {
                $data = array("code" => 0, "message" => "设置密码的长度应在8-20个字符！");
                $this->ajaxReturn($data);
            }
            $user_id = $_SESSION['user']['id'];
            $pw = array(
                'password' => md5($pw)
            );
            $res = M('users')->where(array('id' => $user_id))->save($pw);
            if ($res) {
                $data = array("code" => 1, "message" => "密码修改成功！");
                $this->ajaxReturn($data);
                //  $this->redirect('Admin/Index');
            } else {
                $data = array("code" => 0, "message" => "与原密码相同修改失败！");
                $this->ajaxReturn($data);
            }
        }


    }

	public function aseDecode($pwd){
		//解密key
		$privateKey = "04eb029e72b446e7";
		$iv = "04eb029e72b446e7";
		
		//解密
		$encryptedData = base64_decode($pwd);
		$clearText = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
		return trim($clearText);
	}
    
    /**
     * 密码组合-密码须含以下任意三项：数字；大写字母；小写字母；特殊字符（如：._@/#）
     */
	public function pwdAuth($pwd){
		if(strlen($pwd) < 8 || strlen($pwd) > 20){
			return array("code"=>"1021", "message"=>"密码长度为8-20位");
		}
		
		$passNum = 0;
		if(preg_match("/[0-9]{1,20}/", $pwd)){
			$passNum ++;
		}
		if(preg_match("/[a-z]{1,20}/", $pwd)){
			$passNum ++;
		}
		if(preg_match("/[A-Z]{1,20}/", $pwd)){
			$passNum ++;
		}
		
		$specialChar = array(".","_","@","/","#");
		foreach($specialChar as $value){
			if(strstr($pwd, $value)){
				$passNum ++;
				break;
			}
		}
		
		if($passNum < 3){
			return array("code"=>"1021", "message"=>"新密码须含以下任意三项：数字、大写字母、小写字母、特殊字符(._@/#)");
		}
		
		return array("code"=>"1000", "message"=>"格式无误");
	}


    /**
     * 生成登录二维码,扫码登录用
     */
    public function qrcodes()
    {
        $qrcode = getRandCode(32);
        $qrcode = $qrcode . time();
        // F('loginqrcode', $qrcode);
        session('loginqrcode', $qrcode);  //设置session
        $jsonstr = json_encode(array("qrcode" => $qrcode, "type" => 1));
        qrcode($jsonstr);

    }

    /**
     * 扫码登录逻辑,$qrcode是扫码登录的参数
     */
    public function scanCode()
    {

        if (IS_AJAX) {
            // $qrcode = F('loginqrcode');
            $qrcode = session('loginqrcode');
            // session('loginqrcode',null); // 删除loginqrcode
            $data = M('users')->where(array('qrcode' => $qrcode))->find();
            $title = $this->getUserGroup($data['id']);
            if ($data) {
                $_SESSION['user'] = array(
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'avatar' => $data['avatar'],
                    'type'=>$title
                );
                //    F('loginqrcode',null);
                $this->ajaxReturn(true);

                //    $this->redirect('Admin/index/index');
            } else {
                $this->ajaxReturn(false);
            }

        }
    }


    /**
     * 根据用户id返回角色名
     */
    public function getUserGroup($user_id)
    {
                    $where['a.user_id'] = array("eq", $user_id);

                    $array = M('auth_group_access a')
                        ->join("LEFT JOIN __AUTH_GROUP__ b ON a.group_id = b.id")
                        ->order("a.group_id asc")
                        ->where($where)
                        ->select();

                    foreach ($array as $arr) {
                        if ($arr['group_id'] != 3) {
                            $title = $arr['title'];
                            break;
                        } else {
                            $title = $arr['title'];
                        }
                    }
                 return $title;
    }
    /***
     *扫码登录接口
     * 参数：$qrcod,$user_id    $qrcod扫码登录code，$user_id用户user_id
     * 返回$data
     * $data = array(
     * "code" => 0,
     * "message" => "扫码登录失败，请刷新二维码后重新扫描!",
     * );
     * $data = array(
     * "code" => 1,
     * "message" => "扫码登录成功!",
     * );
     *
     * MOBILE接口调用示例
     * public function  test1(){
     * $data =  R('Home/Index/scanCodeLogin');
     * }
     */
    public function scanCodeLogin($qrcod, $user_id)
    {

        $res = M('users')->where(array('id' => $user_id))->save(array('qrcode' => $qrcode));
        if (!$res) {
            $data = array(
                "code" => 0,
                "message" => "扫码登录失败，请刷新二维码后重新扫描!",
            );
            $this->ajaxReturn($data);
        } else {
            if (!$res) {
                $data = array(
                    "code" => 1,
                    "message" => "扫码登录成功!",
                );
                $this->ajaxReturn($data);
            }

        }
    }


    public function check()
    {
        echo $qrcode = F('loginqrcode');
    }


    /**
     * 发送邮件
     */
    public function send_email()
    {
        $email = I('post.email');
        $result = send_email($email, '邮件标题', '邮件内容');
        if ($result['error'] == 1) {
            p($result);
            die;
        }
        $this->success('发送完成', U('Home/Index/index'));
    }

    /**
     * 生成二维码
     */
    public function qrcode()
    {
        $url = I('post.url');
        qrcode($url);
    }

    /**
     * 生成pdf
     */
    public function pdf()
    {
        $content = $_POST['content'];
        pdf($content);
    }

    /**
     * 支付宝
     */
    public function alipay()
    {
        $price = I('post.price');
        $data = array(
            'out_trade_no' => time(),
            'price' => $price,
            'subject' => '测试'
        );
        alipay($data);
    }

    /**
     * 微信 公众号jssdk支付
     */
    public function weixinpay_js()
    {
        // 此处根据实际业务情况生成订单 然后拿着订单去支付

        // 用时间戳虚拟一个订单号  （请根据实际业务更改）
        $out_trade_no = time();
        // 组合url
        $url = U('Api/Weixinpay/pay', array('out_trade_no' => $out_trade_no));
        // 前往支付
        redirect($url);
    }

    /**
     * 微信 扫描支付
     */
    public function weixinpay_qrcode()
    {
        // 此处根据实际业务情况生成订单 然后拿着订单去支付

        // 虚拟的订单 请根据实际业务更改
        $time = time();
        $order = array(
            'body' => 'test',
            'total_fee' => 1,
            'out_trade_no' => strval($time),
            'product_id' => 1
        );
        weixinpay($order);
    }

    /**
     * 融云用户1
     */
    public function user1()
    {
        // 模拟id为89的用户的登陆过程
        $user_data = M('Users')->field('id,username,avatar')->find(88);
        $_SESSION['user'] = array(
            'id' => $user_data['id'],
            'username' => $user_data['username'],
            'avatar' => $user_data['avatar']
        );
        // 获取融云key
        $rong_key_secret = get_rong_key_secret();
        $assign = array(
            'user_id' => $user_data['id'], // 用户id
            'avatar' => $user_data['avatar'],// 头像
            'username' => $user_data['username'],// 用户名
            'rong_key' => $rong_key_secret['key'],// 融云key
            'rong_token' => get_rongcloud_token($user_data['id'])//获取融云token
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 融云用户2
     */
    public function user2()
    {
        // 模拟id为89的用户的登陆过程
        $user_data = M('Users')->field('id,username,avatar')->find(89);
        $_SESSION['user'] = array(
            'id' => $user_data['id'],
            'username' => $user_data['username'],
            'avatar' => $user_data['avatar']
        );
        // 获取融云key
        $rong_key_secret = get_rong_key_secret();
        $assign = array(
            'user_id' => $user_data['id'], // 用户id
            'avatar' => $user_data['avatar'],// 头像
            'username' => $user_data['username'],// 用户名
            'rong_key' => $rong_key_secret['key'],// 融云key
            'rong_token' => get_rongcloud_token($user_data['id'])//获取融云token
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 生成xls格式的表格
     */
    public function xls()
    {
        $data = I('post.data');
        create_xls($data);
    }

    /**
     * 生成csv格式的表格
     */
    public function csv()
    {
        $data = I('post.data');
        array_walk($data, function (&$v) {
            $v = implode(',', $v);
        });
        create_csv($data);
    }

    /**
     * 导入xls格式的数据
     * 也可以用来导入csv格式的数据
     * 但是csv建议使用 下面的import_csv 效率更高
     */
    public function import_xls()
    {
        $data = import_excel('./Upload/excel/simple.xls');
        p($data);
    }

    /**
     * 导入csv格式的数据
     */
    public function import_csv()
    {
        $data = file_get_contents('./Upload/excel/simple.csv');
        $data = explode("\r\n", $data);
        p($data);
    }

    /**
     * geetest生成验证码
     */
    public function geetest_show_verify()
    {
        $geetest_id = C('GEETEST_ID');
        $geetest_key = C('GEETEST_KEY');
        $geetest = new \Org\Xb\Geetest($geetest_id, $geetest_key);
        $user_id = "test";
        $status = $geetest->pre_process($user_id);
        $_SESSION['geetest'] = array(
            'gtserver' => $status,
            'user_id' => $user_id
        );
        echo $geetest->get_response_str();
    }

    /**
     * geetest submit 提交验证
     */
    public function geetest_submit_check()
    {
        $data = I('post.');
        if (geetest_chcek_verify($data)) {
            echo '验证成功';
        } else {
            echo '验证失败';
        }
    }

    /**
     * geetest ajax 验证
     */
    public function geetest_ajax_check()
    {
        $data = I('post.');
        echo intval(geetest_chcek_verify($data));
    }

    /**
     * webuploader 上传文件
     */
    public function ajax_upload()
    {
        // 根据自己的业务调整上传路径、允许的格式、文件大小
        ajax_upload('/Upload/image/');
    }

    /**
     * webuploader 上传demo
     */
    public function webuploader()
    {
        // 如果是post提交则显示上传的文件 否则显示上传页面
        if (IS_POST) {
            p($_POST);
            die;
        } else {
            $this->display();
        }
    }


    /**
     * 组织架构选择
     */
    public function authority(){

        $typeid = I('get.typeid');
        //获取组织架构信息
        $treeInfo = $this->tree(1);

        $this->assign('treeInfo',array($treeInfo));

        $this->assign('typeid',$typeid);

        $this->display();

    }

    /**
     * 组织构架左侧树形
     */
    public function tree($pid){

        $rule_list = M("tissue_rule")->select();
        if($pid==null){
            $pid=0;
        }

        //获取一级分类
        $top = M("tissue_rule")->where("id=".$pid)->find();

        // 获取一级下所有下级分类
        $item = \Org\Nx\Data::channelLevel($rule_list,$pid,'&nbsp;','id');

        $top['_data'] = $item;

        return $top;
    }



}