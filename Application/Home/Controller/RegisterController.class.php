<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
class RegisterController extends HomeBaseController {
   
	  /**
    *注册显示页面
    *@return [type] [description]
    */
    public function register(){

        $this->display();

    }
    /**
     * 注册动作-手机号码注册
     * @return [type] [description]
     */
    public function signup(){
        $code = I('post.code');
        $mobile=I("post.mobile");

        $sendCode = session('sendCode');
        if($code!=$sendCode['code']){
        	$data=array("code"=>0,"message"=>"验证码不正确");
            $this->ajaxReturn($data);
        }elseif($sendCode['mobile']!=$mobile){
            $data=array("code"=>0,"message"=>"接收短信手机号码不正确");
            $this->ajaxReturn($data);
        }elseif($sendCode['time']<time()){
            $data=array("code"=>0,"message"=>"验证码已过期,请重新获取");
            $this->ajaxReturn($data);
        }
        $username=I("post.username");
        $password=I("post.password");

        $number=I("post.number");
        $map=array();
        $map["phone"]=$mobile;
        //验证账号密码
        $data=M('Users')->where($map)->find();

        //加密处理
        $password = D('Register')->decryption($password);

        if(!empty($data)){
           
            if($data['status'] == 2){
            //待审核用户的注册
            $data=array("code"=>1 ,"message"=>"用户待审核状态，请等待审核结果!");
            $this->ajaxReturn($data);  

            }else if($data['status'] == 3){
            //注册但被逻辑删除(禁用)的用户登录系统，处理逻辑
            $data=array("code"=>1 ,"message"=>"用户禁用状态，请联系管理员处理!");
            $this->ajaxReturn($data);
            }else if($data['status'] == 1){
            //已注册审核通过用户
            $data=array("code"=>1 ,"message"=>"该号码已注册，请登陆!");
            $this->ajaxReturn($data);
            }else if($data['status'] == 0){
            //注册但拒绝用户,重新注册
            $orderno_data = D('Trigger')->orderNumber(9);
            $orderno = $orderno_data['no'];
            
            $fdata = array(
                'username' => $username,
                'password' => $password,
                'avatar'=>'/Upload/avatar/default.png',
                "register_time"=> date('Y-m-d H:i:s'),
                'orderno'=>$orderno,
                'status'=>2
                );
            
            if($orderno_data['status'] == 0) $data['status'] = 1; 
            $status = M('Users')->where(array('id'=>$data['id']))->save($fdata);
            if($status !== false){ 
                //用户重新提交注册审核接口触发
                $res = D('Trigger')->projectResubmit($data['id'],9);
                
                M('auth_group_access')->where(array('user_id'=>$data['id']))->save(array("group_id"=>3));
                M('tissue_group_access')->where(array('user_id'=>$data['id']))->save(array("tissue_id"=>0,"job_id"=>0,"manage_id"=>0));
                $data=array("code"=>3,"message"=>"注册成功，等待跳转！");
                $this->ajaxReturn($data);
            }else{
                $data=array("code"=>3,"message"=>"注册失败，请重新注册");
                $this->ajaxReturn($data);
            }
          }

        }else{

         $orderno_data = D('Trigger')->orderNumber(9);
         $orderno = $orderno_data['no'];
        
        $data = array(
            'username' => $username,
            'password' =>$password,
            'avatar'=>'/Upload/avatar/default.png',
            'phone'   => $mobile,
            "register_time"=> date('Y-m-d H:i:s'),
            'orderno'=>$orderno
            );

        $data = D('Register')->signup($data);
        
        if($orderno_data['status'] == 0) $data['status'] = 1;
        $status = M('Users')->add($data);
        if($status){ 
            M('auth_group_access')->add(array("user_id"=>$status,"group_id"=>3));
            M('tissue_group_access')->add(array("user_id"=>$status,"tissue_id"=>0,"job_id"=>0,"manage_id"=>0));
            $data=array("code"=>3,"message"=>"注册成功，等待跳转！");
            $this->ajaxReturn($data);
        }else{
            $data=array("code"=>3,"message"=>"注册失败，请重新注册");
            $this->ajaxReturn($data);
        }

       }
    }



    /**
     * 注册动作-邮箱注册
     * @return [type] [description]
     */
    public function emailSignup(){
        $code = I('post.code');
        $email=I("post.email");
        
        $sendCode = session('sendCode');
        if($code!=$sendCode['code']){
        	$data=array("code"=>0,"message"=>"验证码不正确");
            $this->ajaxReturn($data);
        }elseif($sendCode['email']!=$email){
            $data=array("code"=>0,"message"=>"接收验证码的邮箱不正确");
            $this->ajaxReturn($data);
        }elseif($sendCode['time']<time()){
            $data=array("code"=>0,"message"=>"验证码已过期，请重新获取");
            $this->ajaxReturn($data);
        }
        $username=I("post.username");
        $password=I("post.password");

        $number=I("post.number");

        $tissue_id=I("post.tissue_id");
        session('tissue_id',$tissue_id);
        $map=array();
        $map["email"]=$email;
        $map['status'] = array('neq',3);

        //验证账号密码
        $data=M('Users')->where($map)->find();

        //解密处理
        $password = D('Register')->decryptionOriginal($password);
        //密码验证规则，须含以下任意三项：数字、大写字母、小写字母、特殊字符(如:~!.@#$%^&*_等)且长度为8-20个字符！
       if(!preg_match("/^(?![0-9a-z]+$)(?![0-9A-Z]+$)(?![0-9\W]+$)(?![a-z\W]+$)(?![a-zA-Z]+$)(?![A-Z\W]+$)[a-zA-Z0-9\W_]{8,20}+$/",$password)){
            $data = array("code" => 0, "message" => "密码须含以下任意三项：数字；大写字母；小写字母；特殊字符（限：._@/#）且长度为8-20个字符！");
            $this->ajaxReturn($data);
        }

        
        $password = md5($password);
        if(!empty($data)){
            if($data['status'] == 2){
            //待审核用户的注册
            $data=array("code"=>1 ,"message"=>"用户待审核状态，请等待审核结果!");
            $this->ajaxReturn($data);
            }else if($data['status'] == 1){
            //已注册审核通过用户
            $data=array("code"=>1 ,"message"=>"该号码已注册，请登陆!");
            $this->ajaxReturn($data);
            }else if($data['status'] == 0){ 
            //注册但拒绝用户
            $orderno_data = D('Trigger')->orderNumber(9);
            $orderno = $orderno_data['no'];
    
            $fdata = array(
                'username' => $username,
                'password' =>$password,
                'avatar'=>'/Upload/avatar/default.png',
                "register_time"=> date('Y-m-d H:i:s'),
                'orderno'=>$orderno,
                'status'=>2,
                'firstlogin'=>1,
                'lock_status'=>0
                );
            
            if($orderno_data['status'] == 0) $fdata['status'] = 1;
            $status = M('Users')->where(array('id'=>$data['id']))->save($fdata);

            if($status !== false){
                //用户重新提交注册审核接口触发
                $res = D('Trigger')->projectResubmit($data['id'],9);
                
                M('auth_group_access')->where(array('user_id'=>$data['id']))->save(array("group_id"=>3));
                M('tissue_group_access')->where(array('user_id'=>$data['id']))->save(array("tissue_id"=>$tissue_id,"job_id"=>0,"manage_id"=>0));
                if($orderno_data['status'] == 0){
                    $data=array("code"=>3,"message"=>"注册成功，等待跳转！",'audit_flag'=>0);
                }else{
                    $data=array("code"=>3,"message"=>"注册成功，等待跳转！",'audit_flag'=>1);
                }
                
                $this->ajaxReturn($data);
            }else{
                $data=array("code"=>3,"message"=>"注册失败，请重新注册");
                $this->ajaxReturn($data);
            }
          }

        }else{

        $orderno_data = D('Trigger')->orderNumber(9);
        // echo $orderno_data['no'];die;
        $orderno = $orderno_data['no'];
        
        $data = array(
            'username' => $username,
            'password' => $password,
            'avatar'=>'/Upload/avatar/default.png',
            // 'phone'   => $mobile,
            "register_time"=> date('Y-m-d H:i:s'),
            'orderno'=>$orderno,
            'email'=>$email,
            'firstlogin'=>1,
            'lock_status'=>0
            );

		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$data['id'] = getNextId('users');
			$data['register_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
		}
		
        if($orderno_data['status'] == 0) $data['status'] = 1;
        $status = M('Users')->add($data);
        if($status){ 
            write_pwd_history($data['id'], $data['password']);
            M('auth_group_access')->add(array("user_id"=>$status,"group_id"=>3));
            M('tissue_group_access')->add(array("user_id"=>$status,"tissue_id"=>$tissue_id,"job_id"=>0,"manage_id"=>0));

            if($orderno_data['status'] == 0){
                $data=array("code"=>3,"message"=>"注册成功，等待跳转！",'audit_flag'=>0);
            }else{
                $data=array("code"=>3,"message"=>"注册成功，等待跳转！",'audit_flag'=>1);
            }
            // $data=array("code"=>3,"message"=>"注册成功，等待跳转！");
            $this->ajaxReturn($data);
        }else{
            $data=array("code"=>3,"message"=>"注册失败，请重新注册");
            $this->ajaxReturn($data);
        }

       }
    }
}