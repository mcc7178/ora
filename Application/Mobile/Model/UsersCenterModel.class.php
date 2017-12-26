<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/28
 * Time: 17:18
 */

namespace Mobile\Model;

use Think\Model;

/**个人中心模型
 * @user lizhongjian
 * Class UsersCenterModel
 * @package Mobile\Model
 */

class UsersCenterModel extends CommonModel {

    protected $tablePrefix = 'think_';
    protected $tableName = 'Users';


    protected $_validate = array(
        array('username', 'require', '用户名不能为空', Model::EXISTS_VALIDATE),
        array('username', '2,10', '用户名长度为2-10个字符组成', Model::EXISTS_VALIDATE, 'length'),
        //array('username', 'unique', '用户名已被占用', Model::EXISTS_VALIDATE),
        //array('username','','用户名已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
        array('phone', '/1[34578]{1}\d{9}$/', '请输入正确的手机号格式', Model::EXISTS_VALIDATE, 'regex'),
        array('email', 'require', '邮箱不能为空', Model::EXISTS_VALIDATE),
        array('headImg', 'require', '头像图片不能为空', Model::EXISTS_VALIDATE),
        array('personalized_signature', '2,20', '简介长度为2-30个字符组成', Model::EXISTS_VALIDATE,'length'),
        array('password', 'require', '密码不能为空', Model::EXISTS_VALIDATE),
        array('oldPassword', 'require', '原密码不能为空', Model::EXISTS_VALIDATE),
        //array('password', '6,20', '密码长度为6-20位', Model::EXISTS_VALIDATE, 'length'),
        array('password', 'checkPassWord', '密码须含以下任意三项：数字、大写字母、小写字母、特殊字符(如:~!.@#$%^&*_等)且长度为8-20个字符！', Model::EXISTS_VALIDATE, 'callback'),
        array('repassword','password','确认密码不正确',0,'confirm'),
        array('confirm', 'require', '确认密码不能为空', Model::EXISTS_VALIDATE),
        array('phone', 'empty', '手机号码不能为空', Model::EXISTS_VALIDATE),
        array('phone', 'is_numeric', '手机号码必须为数字', Model::EXISTS_VALIDATE),
        array('emailCode','require', '邮箱验证码不能为空',Model::EXISTS_VALIDATE),
        array('email', 'email', '邮箱格式不正确', Model::EXISTS_VALIDATE),
        array('emailCode', 'code', '邮箱验证码不正确',0,'confirm')
    );


    public function checkPassWord($passWord) {
        if(!preg_match("/^(?![0-9a-z]+$)(?![0-9A-Z]+$)(?![0-9\W]+$)(?![a-z\W]+$)(?![a-zA-Z]+$)(?![A-Z\W]+$)[a-zA-Z0-9\W_]{8,20}+$/",$passWord)){
            //$data = array("code" => 0, "message" => "密码须含以下任意三项：数字、大写字母、小写字母、特殊字符(如:~!.@#$%^&*_等)且长度为8-20个字符！");
            return false;
        }else{
            return true;
        }
    }

    /**
     * 校验token
     */
    public function verifyToken($token,$secret_key){
        if (!empty($token) || !empty($secret_key)) {
            $info = M('Users')->where(array('token' => $token))->find();
            if (empty($info) || (time() >= $info['token_expires'])) {
                return $this->error(1030, '授权失败或过期');
            }elseif($info['token'] != $token){
                return $this->error(1030, '不合法的token');
            }elseif($info['secret_key'] != $token){
                return $this->error(1030, '不合法的secret_key');
            }
        }else{
            return $this->error(1030, '缺少必要参数');
        }
    }

    /**
     * 邮箱验证码登录设置新密码
     * @param $data
     * @return array
     */
    public function  setNewPassword($data,$userId){
        $this->verifyToken($data['token'],$data['secret_key']);
        $password = md5($data['password']);
        $where2["user_id"] = $userId;
        $list = M("pwd_history")->field("password)")->where($where2)->order("id desc")->limit(3)->select();
        $pass_array = array($list[0]['password'],$list[1]['password'],$list[2]['password']);
        if(in_array($password,$pass_array)){
            return $this->error(1008, '请勿与最近三个使用的密码相同');
        }else{
            $info = M('Users')->where(array('id'=>$userId))->setField('password',$password);
            if($info){
                $msg['token'] = $data['token'];
                $msg['secret_key'] = $data['secret_key'];
                return $this->success(1000,'修改成功',$msg);
            }else{
                return $this->error(1030,'修改失败');
            }
        }
    }

    /**
     * @修改密码
     * @param  修改密码前所需的字段
     * @param string $oldPassword 原密码
     */
    public function setPassword($data,$userId) {

        $model = M('Users');
        //查找原来的密码
        $result = $model->where(array("id" => $userId))->find();
        //用户输入的原来的密码
        $oldPassword = md5($data['oldPassword']);

        if ($oldPassword != $result['password'] ) {
            return $this->error(1030, '请输入正确的原密码');
        }else{
            unset($data['oldPassword']);
            //查询用户最近三次登录的密码
            //获取最近三次使用的密码
            $list = M("pwd_history")->field("password")->where(array('user_id' => $userId))->order('id DESC')->limit(3)->select();
            $pass_array = array($list[0]['password'],$list[1]['password'],$list[2]['password']);
            if(in_array(md5($data['password']),$pass_array)){
                return $this->error(1008, '请勿与最近三个使用的密码相同');
            }
            $saveData['firstlogin'] = 1;//FIRSTLOGIN
            $saveData['password'] = md5($data['password']);
            $res = $model->where(array('id' => $userId))->save($saveData);
            $result = write_pwd_history($userId,md5($data['password']));
            if (!$res) {
                return $this->error(1029, '修改失败');
            }else{
                $info['id'] = $res;
                return $this->success(1000,'修改成功',$info);
            }
        }
    }

    /********************************************兼容oracle******************************************************************/
    /**
     * 获取登录用户个人资料
     * @Param $user_id 用户id
     * @Return $info 用户个人信息数组
     */
    public function getPersonInfo($user_id){
        $info = M('Users a')
            ->field('a.id,a.avatar,a.username,a.phone,a.email,a.personalized_signature,c.name,d.name as dname')
            ->join('LEFT JOIN __TISSUE_GROUP_ACCESS__  b ON b.user_id = a.id LEFT JOIN  __TISSUE_RULE__ c ON b.tissue_id = c.id LEFT JOIN  __JOBS_MANAGE__ d ON b.job_id = d.id')
            ->where(array('a.id'=>$user_id))
            ->find();
        $info['avatar'] ? $info['avatar'] : '/Upload/avatar/20170216/58a55160712b1.jpg';
        return $info;
    }

    /********************************************兼容oracle******************************************************************/
    /*
     * 验证修改个人资料数据输入合法性
     * @param $post
     * @param $user_id
     */
    public function checkData($post,$user_id){
        $data['avatar'] = $post['headImg'];
        $data['personalized_signature'] = $post['personalized_signature'];
        $data['email'] = $post['email'];
        $info = M('Users')->where(array('id'=>$user_id))->data($data)->save();
        if($info){
            return $this->success(1000,'修改成功',array('id' =>$user_id));//修改成功
        }else{
            return $this->error(1030,'修改失败');//修改失败
        }
    }


    /**
     * 找人Pk
     */
    public function my_pk($userId){
        //mktime() 函数用于从日期取得时间戳，成功返回时间戳，否则返回 FALSE 。
        $start_time = mktime(0,0,0,date('m'),1,date('Y'));
       //计算当月至结束时间所包含的天数
        $end_time = mktime(23,59,59,date('m'),date('t'),date('Y'));

        $where['time'] = array(array('egt',$start_time),array('elt',$end_time));

        $where['user_id'] = array("eq",$userId);

        $where['score'] = array("gt",0);

        //获取当月积分
        //本月积分
        $months = date("Ym");
        $total_integral = M('integration_record')->where($where)->sum('score');
        //$this_month_score = M('integration_record')->field("user_id,sum(score) as sumscore,FROM_UNIXTIME(time,'%Y%m') months")->where($where)->group('months')->having('months='.$months)->select();
       // $this_month_score = $this_month_score[0]['sumscore'];

        $where1['time'] = array(array('egt',$start_time),array('elt',$end_time));

        $where1['user_id'] = array("eq",$userId);

        $where1['need_score'] = array("gt",0);
        //获取本月已兑换福利所减掉的积分
        $need_integral = M('Welfare_record')->where($where1)->sum('need_score');
        $this_month_score = $total_integral - $need_integral;
        //获取自己数据

        //查询自己当前月的积分
        $my_items = M('integration_record')->where($where)->select();

        $my_user = $this->pkData($my_items);

        $total_integral = array_sum($my_user);
       /* $my_list =  array();

        //比较PK数据
        for($i=0;$i<=5;$i++){

            $result = $this->myPercentage($my_user[$i]);
            $my_list[$i] = $result;

        }*/
        if($this_month_score < 0){
            $this_month_score = 0;
        }

       //获取我的头像和用户名
        $user = M('Users')->field('username,avatar')->where(array('id'=>$userId))->find();
        $data = array(
            "username"=>$user['username'],
            "avatar"=>$user['avatar'],
            "integral"=>$this_month_score,
            "my_list" => $my_user
        );

        return $data;
    }


    /**
     * 获取pk成员列表
     */

    public function pkMember($userId){

        $where['a.user_id'] = array("eq",$userId);

        //获取用户上级组织名称
        $user = M("tissue_group_access a")
            ->join("LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id")
            ->field("a.tissue_id,b.pid,b.name")
            ->where($where)
            ->find();

        $items = $this->tree($user['tissue_id']);

        //获取当前用户所在级别
        $level = $this->hierarchy($items['id']);

        //普通会员
        if($level == 4){

            $items = $this->tree($user['pid']);

            $is_admin = false;

        }else{

            $is_admin = true;

        }

        $pkMember_list = $this->PeopleData($level,$items);

        $rule_list = array();

        if($is_admin){

            //获取所有组织数据
            $tissue_rule_list = D('IsolationData')->specifiedUser($userId);

            foreach($tissue_rule_list as $id){

                $user_tissue_id = M("tissue_group_access")
                    ->where("user_id=".$id)
                    ->getField('tissue_id');

                $level_num = D('AdminTissue')->hierarchy($user_tissue_id);

                if($level_num < 4){
                    $rule_arrid[] = $id;
                }

            }

            $rule_arrid = $rule_arrid ? $rule_arrid :  array(null);

            $conditions['a.user_id'] = array("in",$rule_arrid);
            $conditions['b.status'] = array("eq",1);

            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $group = 'a.user_id,b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id,a.manage_id,b.avatar';
            }else{
                $group = 'a.user_id';
            }

            $rule_list = M("tissue_group_access a")
                ->field("b.id,b.username,b.job_number,b.phone,b.email,a.tissue_id,a.job_id,a.manage_id,b.avatar")
                ->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
                ->where($conditions)
                ->order('a.user_id desc')
                ->group($group)
                ->select();

        }

        //获取当前组织PK人员
        $map['tissue_id'] = array("eq",$user['tissue_id']);
        $map['b.id'] = array("gt",0);
        $map['b.status'] = array('eq',1);
        $admin_list = M("tissue_group_access a")->field("b.id,b.username,b.avatar")->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")->where($map)->select();

        $data = array(
            "list"=>$pkMember_list['items'],
            "admin_list"=> $admin_list,
            "is_admin"=>$is_admin,
            "tissue_name"=>$user['name'],
            "rule_list"=>$rule_list
        );

        return $data;

    }

    /**
     * 取出部门和人
     */
    public function PeopleData($level,&$data,&$pkMember_list,&$admin_list){

        $level_arr = array(1=>3,2=>2,3=>1,4=>1);

        foreach($data['_data'] as $item){

            if($item['_level'] == $level_arr[$level]){

                $admin_list[] = $item['pid'];

                $pkMember_list[] = $this->tissuePeople($item);

            }else{

                $admin_list[] = $item['id'];

                $this->PeopleData($level,$item,$pkMember_list,$admin_list);

            }

        }

        $data = array(
            "items"=>$pkMember_list,
            "admin_list"=>$admin_list
        );

        return $data;

    }

    /**
     * 查询PK人 从组织架构上取人
     */
    public function tissuePeople($item){

        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $condition = "a.tissue_id in " . $item['id'] . "and b.status != 3";
        }else{
            $condition['a.tissue_id'] = array("in",$item['id']);

            $condition['b.status'] = array('neq',3);
        }
        $user_list = M("tissue_group_access a")
            ->field("b.id,b.username,b.avatar")
            ->join("LEFT JOIN __USERS__ b ON a.user_id = b.id")
            ->where($condition)
            ->select();

        $pkMember_list['name'] = $item['name'];

        $pkMember_list['_data'] = $user_list;

        return $pkMember_list;

    }

    /**
     * 查询当前所在层级
     */
    public function hierarchy($id,&$num = 0){

        $is_display = M("tissue_rule")->field("pid")->where("id=".$id)->find();

        if(!empty($is_display)){
            $num++;
            $this->hierarchy($is_display['pid'],$num);
        }

        return $num;
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

    /**
     * 自己和个人pk结果计算
     */
    public function memberAjax($pk_id,$userId){

       if($pk_id == ''){
           return $this->error(1023,'缺少必要参数');
       }
        //获取PK对象数据
        $where['user_id'] = array("eq",$pk_id);
        $where['score'] = array("gt",0);
        //mktime() 函数用于从日期取得时间戳，成功返回时间戳，否则返回 FALSE 。
        //当前月开始时间
        $start_time = mktime(0,0,0,date('m'),1,date('Y'));
        //当前月结束时间
        $end_time = mktime(23,59,59,date('m'),date('t'),date('Y'));

        $where['time'] = array(array('egt',$start_time),array('elt',$end_time));
        //查询pk对象当前月的积分
        $pk_items = M('integration_record')->where($where)->select();

        //查询用户名
        $pk_username =  M('users')->field("username,avatar")->where("id=".$pk_id)->find();
        $my_username =  M('users')->field("username,avatar")->where(array('id'=>$userId))->find();
        $pk_user = $this->pkData($pk_items);

        //获取自己数据
        $condition['user_id'] = array("eq",$userId);
        $condition['score'] = array("gt",0);
        $condition['time'] = array(array('egt',$start_time),array('elt',$end_time));
       //查询自己当前月的积分
        $my_items = M('integration_record')->where($condition)->select();
        //$my_integral = M('integration_record')->where($condition)->sum('score');

        $my_user = $this->pkData($my_items);

       /* $my_list = $pk_list = array();

        //比较PK数据
        for($i=0;$i<=5;$i++){

            $result = $this->percentage($pk_user[$i],$my_user[$i]);
            $pk_list[$i] = $result[0];
            $my_list[$i] = $result[1];

        }*/

        //PK对象当月月积分
        //$pk_integral = M('integration_record')->where($where)->sum('score');


        //PK月积分
        $pk_integral = array_sum($pk_user);
        $pk_integral = $pk_integral ? $pk_integral : 0;
        $my_integral = array_sum($my_user);
        $my_integral = $my_integral ? $my_integral : 0;
        $data = array(
            "pk_name"=>$pk_username['username'],
            "pk_avatar"=>$pk_username['avatar'],
            "pk_integral"=>$pk_integral,
            "pk_list"=>$pk_user,
            "my_name"=>$my_username['username'],
            "my_avatar"=>$my_username['avatar'],
            "my_integral" => $my_integral,
            "my_list"=>$my_user,
        );
        return $data;

    }


    /**
     * @param $data
     * @return array
     * 获取PK前用户数据
     */
    public function myPkData($data){

        $type_name = array("好为人师","乐分享","系统达人","任务范儿","爱学习","我是学霸");

        $pk_user = array(0,0,0,0,0,0);

        foreach($data as $item){

            switch($item['type']){
                case $type_name[0]:
                    $pk_user[0] += $item['score'];
                    break;
                case $type_name[1]:
                    $pk_user[1] += $item['score'];
                    break;
                case $type_name[2]:
                    $pk_user[2] += $item['score'];
                    break;
                case $type_name[3]:
                    $pk_user[3] += $item['score'];
                    break;
                case $type_name[4]:
                    $pk_user[4] += $item['score'];
                    break;
                case $type_name[5]:
                    $pk_user[5] += $item['score'];
                    break;
            }

        }

        return $pk_user;

    }

    /**
     * @param $data
     * @return array
     * 获取PK后 自己和pk对象的数据
     */
    public function pkData($data){

        $type_name = array("好为人师","乐分享","系统达人","任务范儿","爱学习","我是学霸");

        $pk_user = array(0,0,0,0,0,0);

        foreach($data as $item){

            switch($item['type']){
                case $type_name[0]:
                    $pk_user[0] += $item['score'];
                    break;
                case $type_name[1]:
                    $pk_user[1] += $item['score'];
                    break;
                case $type_name[2]:
                    $pk_user[2] += $item['score'];
                    break;
                case $type_name[3]:
                    $pk_user[3] += $item['score'];
                    break;
                case $type_name[4]:
                    $pk_user[4] += $item['score'];
                    break;
                case $type_name[5]:
                    $pk_user[5] += $item['score'];
                    break;
            }

        }

        return $pk_user;

    }

    /**
     * 选择部门PK列表
     */
    public function pkDepartment($userId){

        $where['a.user_id'] = array('eq',$userId);

        $part = array();
        //获取用户上级组织名称
        $tissue_name = M('Tissue_group_access a')
        	->join('LEFT JOIN __TISSUE_RULE__ b ON a.tissue_id = b.id')
        	->field("a.tissue_id,b.id,b.name,b.pid")
        	->where($where)
        	->find();

        $items = $this->treeInfo($tissue_name['tissue_id']);

        //获取当前用户所在级别
        $level = parent::hierarchy($items['id']);

        //普通会员
        if($level == 4){
            $items = $this->treeInfo($tissue_name['pid']);
        }

        $pkMember_list = parent::getDepartmentData($level,$items);

        return $data = array(
            "tree_items"=>$pkMember_list,
            "tissue_name"=>$tissue_name
        );
    }

    /**
     * 获取组织架信息
     */
    public function treeInfo($id){

        //获取左侧树形结构
        $tree_items = parent::trees($id);

        return $tree_items;
    }

    /**
     * 计算部门pk结果
     */
    public function departmentPk($dpk_id,$userId){


           if($dpk_id === ''){
               return $this->error(1023,'缺少部门参数id');
           }

            //获取PK对象数据
            $dpk_total = $this->getpk($dpk_id);
            $dpk_name =  M('tissue_rule')->field("name")->where("id=".$dpk_id)->find();

            //获取自己数据
            $condition['user_id'] = array("eq",$userId);
            $my_tissue_id = M('tissue_group_access')->field('tissue_id')->where($condition)->find();
            $my_total = $this->getpk($my_tissue_id['tissue_id']);
            $my_name =  M('tissue_rule')->field("name")->where("id=".$my_tissue_id['tissue_id'])->find();

            //计算平均值   getpk 已计算平均值
            /*
            $my = M('tissue_group_access')->field('tissue_id')->where("tissue_id=".$my_tissue_id['tissue_id'])->count();
           	$pk = M('tissue_group_access')->field('tissue_id')->where("tissue_id=".$dpk_id)->count();

            if(empty($my)){
                $my_total = 0;
            }else{
                $my_total = round($my_total / $my);
            }

            if(empty($pk)){
                $pk_total = 0;
            }else{
                $pk_total  = round($dpk_total / $pk);
            }
			*/
            
            $pk_total = $dpk_total;
            
            $data = array(
                "pk_name"=>$dpk_name['name'],
                "pk_total"=>$pk_total,
                "my_total"=>$my_total,
                "my_name"=>$my_name['name']
            );

            return $data;
    }

    /**
     * 部门PK公共函数
     */
    public function getpk($tissue_id){

        $where['tissue_id'] = array("eq",$tissue_id);

        $list = array();

        $items = M('tissue_group_access')->field('user_id')->where($where)->select();

        if(empty($items)){

            $total = 0;

        }else{

            foreach($items as $item){
                $list[] = $item['user_id'];
            }

            $where = array();
            $where['score'] = array("gt",0);
            $where['user_id'] = array("in",$list);
            $start_time = mktime(0,0,0,date('m'),1,date('Y'));
            $end_time = mktime(23,59,59,date('m'),date('t'),date('Y'));
            $where['time'] = array(array('egt',$start_time),array('elt',$end_time));

            $integration_list = M('integration_record')->where($where)->avg('score');
            $integration_list = $integration_list ? $integration_list : 0;

            //合并部门总值
            $total = round($integration_list);

        }

        return $total;

    }
}