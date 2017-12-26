<?php
/**
 * Created by PhpStorm.
 * User: lizhongjian
 * Date: 2017/3/9
 * Time: 16:40
 * 我的积分模型
 */

namespace Mobile\Model;
use Think\Model;

class IntegrationModel extends CommonModel
{

   // protected $tablePrefix = 'think_';
   // protected $tableName = 'integration_rule';
    protected $tableName = 'integration_rule';

    protected $_validate = array(

        array('apply_title', 'require', '学分标题不能为空', Model::EXISTS_VALIDATE),
        array('apply_title', '2,20', '学分标题必须为2-20个字符长度', Model::EXISTS_VALIDATE, 'length'),
        array('add_score', 'require', '学分分值不能为空', Model::EXISTS_VALIDATE),
        array('add_score', 'number', '学分分值必须是大于或等于零的纯数字', Model::EXISTS_VALIDATE),
        array('add_score', '1,5', '学分分值必须为1-5位数', Model::EXISTS_VALIDATE, 'length'),
        array('apply_description','require','学分描述不能为空',Model::EXISTS_VALIDATE),
        array('apply_description', '5,50', '学分描述必须在5-50个字符长度范围内', Model::EXISTS_VALIDATE, 'length'),
       // array('attachment', 'require', '所上传的附件不能为空', Model::EXISTS_VALIDATE),
      /*  array('phone', '/1[34578]{1}\d{9}$/', '请输入正确的手机号格式', Model::EXISTS_VALIDATE, 'regex'),
        array('email', 'require', '邮箱不能为空', Model::EXISTS_VALIDATE),
        array('password', 'require', '密码不能为空', Model::EXISTS_VALIDATE),
        array('oldPassword', 'require', '原密码不能为空', Model::EXISTS_VALIDATE),
        //array('password', '6,20', '密码长度为6-20位', Model::EXISTS_VALIDATE, 'length'),
        array('password', 'isPassword', '只允许输入6-20位由 数字、字母、下划线组成的密码', Model::EXISTS_VALIDATE, 'callback'),
        array('repassword','password','确认密码不正确',0,'confirm'),
        array('confirm', 'require', '确认密码不能为空', Model::EXISTS_VALIDATE),
        array('phone', 'empty', '手机号码不能为空', Model::EXISTS_VALIDATE),
        array('phone', 'is_numeric', '手机号码必须为数字', Model::EXISTS_VALIDATE),
        array('emailCode','require', '邮箱验证码不能为空',Model::EXISTS_VALIDATE),
        array('email', 'email', '邮箱格式不正确', Model::EXISTS_VALIDATE),
        array('emailCode', 'code', '邮箱验证码不正确',0,'confirm')*/
    );
    /**
     * 积分规则列表
     */
    public function integrationList($userId)
    {
        $plan_id = getPlanId($userId);//获取方案id
        $list1 = M('integration_rule')->where(array('plan_id' => $plan_id))->field('id,name,score,oneday_score,type')->order('id asc')->select();
        if($list1){//如果是查询到该用户所在的组织有应用到方案的，则执行这里
            $_list = M('integration_rule')->where(array('plan_id' => $plan_id))->field('id,name,score,oneday_score,type,plan_id')->order('id asc')->select();
            foreach($_list as $k=>$v){
                if(strpos($v['oneday_score'],'/') === false){     //使用绝对等于
                    //不包含
                }else{
                    //包含
                    $arr = explode('/',$v['oneday_score']) ;
                    $_list[$k]['oneday_score'] = intval($arr[0])/intval($arr[1])*30;
                }
            }
            //调用公共Model里的方法,登录系统积分触发
            $res = D('Trigger')->intergrationTrigger($_SESSION['user']['id'], 4);
        }else{
            //没有查询到该用户所在组织有配置方案，则从 integration_rule_default 表获取默认数据
            $_list = M('integration_rule_default')->field('id,name,score,oneday_score,type,plan_id')->order('id asc')->select();
            //查询积分默认表数据，然后把获取到的数据添加到积分规则表
            foreach($_list as $k => $v){
                $v['plan_id'] = $plan_id;
                $res = M('integration_rule')->add($v);
            }
            foreach($_list as $k=>$v){
                if(strpos($v['oneday_score'],'/') === false){//使用绝对等于
                    //不包含
                }else{
                    //包含
                    $arr = explode('/',$v['oneday_score']) ;
                    $_list[$k]['oneday_score'] = $arr[0]/intval($arr[1])*30;
                }
            }

        }
        return $_list;
    }

    /*
     * 申请加分
     */
    public function applyAddStore($post,$model,$user_id){
        if(isset($post['description'])){
            $postData['apply_description'] = $post['description'];
        }else{
            $postData['apply_description'] = '';
        }
        $postData['attachment'] = $post['attachment'];
        $postData['add_score'] = $post['score'];
        if( $postData['apply_description'] == ''){
            return $this->error(1030,'申请说明不能为空!');
           
        }
        /*if($postData['attachment'] == ''){
            return $this->error(1030,'请先上传附件！');
            
        }*/
        if($postData['add_score'] == ''){
            return  $this->error(1030,'加分分值不能为空！');
            
        }
        if(!(is_int($postData['add_score']) || $postData['add_score'] > 0)){
            return $this->error(1030,'加分分值必须是大于零的整数！');
        }
        $postData['user_id'] = $user_id;
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $postData['id'] = getNextId('integration_apply');
            $postData['add_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }else{
            $postData['add_time'] = date('Y-m-d H:i:s',time());
        }
        $res = $model->add($postData);
        $resp = self::getTaskData($res,8);
        if($resp){
            return $this->success(1000,'操作成功',array('id'=>$res));
        }else{
            return $this->error(1030,'操作失败');
        }
    }

    /************************************************兼容oracle***************************************************************/
    /**
     * 学分兑换积分获取积分详情
     */
    public function getExchangeIntergration($userId){
        //初始化缓存
        F("valid_credits", null);
        $where["user_id"] = $userId;
        $plan_id = getPlanId($userId);//获取方案id
        $pageLen = 5;
        //$record = M("integration_erecord")->where($where)->order("id desc")->page($page, $pageLen)->select();
       // $total = M("integration_erecord")->field("count(id) as t_num")->where($where)->select();
       // $pageNav = $this->pageClass($total[0]["t_num"], $pageLen);

        $yearData = $this->getStudyCredit(1,$userId);
        $total_credits = $yearData["totalScore"];//年度总学分

        //已消耗学分
        $where2["user_id"] = $userId;
        $where2["update_stamp"] = array(array("gt", strtotime(date("Y")."-01-01 00:00:00")),array("lt", time()));
        $used = M("integration_erecord")->field("sum(credits) as t_num")->where($where2)->select();
        $used_credits = $used[0]["t_num"] ? $used[0]["t_num"] : 0;

        //可用学分 = 年度总学分 - 已消耗学分
        $valid_credits = $total_credits - $used_credits;

        F("valid_credits", $valid_credits);

        //获取用户所属同一方案的积分兑换率
        if($plan_id){
            $exchange = M("integration_exchange")->field('exc_rule')->where(array('plan_id' => $plan_id))->find();
           //兑换率
        }else{
            $exchange["exc_rule"] = 0;
        }
        $exc_rule = $exchange["exc_rule"];//兑换规则
        $result = array(
            "total_credits"=>$total_credits,
            "valid_credits"=>$valid_credits,
            "exc_rule"=>$exc_rule
        );
        if($result){
            return array('code' => 1000,'message' => '获取成功','data' => $result);
        }else{
            return array('code' => 1030,'message' => '暂无数据返回');
        }
    }



    /**
     * 根据用户组织ID获取所在中心
     */
    public function getRulePid($pid){
        $pid += 0;
        if(!is_int($pid)){
            return array("code"=>1031, "message"=>"未获取到组织id");
        }
        $group = M("tissue_rule")->field("id,pid,name")->where("id=".$pid)->find();
        if(!$group){
            return array("pid" => $pid);
        }else{
            if($group["pid"] != 1){
                return self::getRulePid($group["pid"]);
            }else{
                return array("pid" => $group["id"]);
            }
        }
    }


    /***********************兼容oracle***********************/
    /**
     * 学分兑换积分
     */
    public function IntergrationExchange($param){
        $data["user_id"] = $param['userId'];
        $data["credits"] = $param["excVal"];
        $data["exc_rule"] = $param["excRule"];
        $data["exc_integral"] = $param["excVal"] * $param["excRule"];//消费学分*兑换率(可以兑换多少积分)
        $data["update_time"] = date("Y-m-d H:i:s");
        $data["update_stamp"] = time();
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data["id"] = getNextId('integration_erecord');
            $data["update_time"] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        $res = M("integration_erecord")->add($data);

        //兑换成功-积分记录增加数据
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data_record["id"] = getNextId('integration_record');
        }
        $data_record["time"] = time();
        $data_record["user_id"] = $param['userId'];
        $data_record["department"] = "";
        $data_record["score"] = $data["exc_integral"];
        $data_record["type"] = "获取";
        $data_record["describe"] = "使用".$param["excVal"]."学分进行兑换";
        $data_record["apply_id"] = 0;
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data_record["id"] = getNextId('integration_record');
           // $data_record["time"] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        $result = M("integration_record")->data($data_record)->add();
        if($res && $result){
            return true;
        }else{
            return false;
        }
    }

    /**
    * 申请加学分
     * @$postData 接收申请加分的数组信息
    */
    public function applyAddCredits($postData,$model){
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $postData['id'] = getNextId('credits_apply');
           // $postData['add_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }
        
        $orderno_data = D('Trigger')->orderNumber(8,$userId);
        $orderno = $orderno_data['no'];
        $postData['orderno'] = $orderno;
        if($orderno_data['status'] == 0){
            $postData['status'] = 1;
        }
        $res = $model->add($postData);
       // $resp = self::getTaskData($res,11);
        if($res){
            return $this->success(1000,'操作成功');
        }else{
            return $this->error(1030,'操作失败');
        }
    }


    /**
     * 我的积分列表
     */
    public function myIntegration($page,$userId)
    {
        //我的积分模块的列表页显示：总积分，可用积分，本月积分
        $page = $page ? $page : 1;
        $pageNum =  20;
        $where1 = array(
            'user_id'=>$userId,
            'score'=>array('gt',0),
            '_logic'=>'and'
        );
        $where2 = array(
            'user_id'=>$userId,
        );
        $where3 = array(
            'user_id'=>$userId,

        );
        //计算本年度总积分
        $all_score_arr = M('integration_record')->field('user_id,score,time')->where($where1)->select();
        $year = date("Y");
        $all_score = 0;
        foreach($all_score_arr as $k=>$v){
            $t = date('Y',$v['time']);
            if(strpos($t,$year) !== false){
                $all_score += $v['score'];
            }
        }
        $all_score = $all_score<0 || $all_score =='' ? 0 : $all_score;
       // $all_score = M('integration_record')->where($where1)->sum('score');
        //计算可用积分

        $available_score_arr =  M('integration_record')->field('user_id,score,time')->where($where2)->select();
        $available_score = 0;
        foreach($available_score_arr as $k=>$v){
            $t = date('Y',$v['time']);
            if(strpos($t,$year) !== false){
                $available_score += $v['score'];
            }
        }
        $available_score = $available_score<0 || $available_score == ''? 0 : $available_score;



        //本月可用积分
        //$months = date("Ym");
        //$this_month_score = M('integration_record')->field("FROM_UNIXTIME(time,'%Y%m')")->where($where3)->sum('score');
        //$this_month_score = $this_month_score<0 || $this_month_score =='' ? 0 : $this_month_score;
       /* $this_month_score = M('integration_record')->field("user_id,sum(score) as sumscore,FROM_UNIXTIME(time,'%Y%m') months")->where($where3)->group('months')->having('months='.$months)->select();
        $this_month_score = $this_month_score[0]['sumscore'];*/
        $year_month = date("Ym");
        $month_available_score = 0;
        foreach($available_score_arr as $k=>$v){
            $t = date('Ym',$v['time']);
            if(strpos($t,$year_month) !== false){
                $month_available_score += $v['score'];
            }
        }
        if($month_available_score < 0 || $month_available_score == ''){
            $month_available_score = 0;
        }
        $personInfo = M('Users')->field('id,username,avatar,phone')->where(array('id'=>$userId))->find();
        //隔离数据过滤
        $specifiedUser = D('IsolationData')->specifiedUser($userId);
        $map['auth_user_id'] = array("in",$specifiedUser);
        $course_arrId = array();
        $tissue_course = M("welfare")->where($map)->field("id")->select();
        foreach($tissue_course as $tc){
            $course_arrId[] = $tc['id'];
        }
        //查询共享数据
        $wheres['a.type'] = array("eq",9);//福利社9
        $wheres['b.user_id'] = array("eq",$userId);
        $resource_sharing = M("resource_sharing a")->join("LEFT JOIN __TISSUE_GROUP_ACCESS__ b ON a.tissue_id = b.tissue_id")->field("a.source_id")->where($wheres)->select();
        foreach($resource_sharing as $sharing){
            $course_arrId[] = $sharing['source_id'];
        }
        //如果存在共享资源，根据共享资源id获取共享数据
        if(!empty($course_arrId)){
            $where['id'] = array("in",$course_arrId);
        }
        //福利社的展示
        $list = M('welfare')->where($where)->limit(($page-1) * $pageNum . ',' . $pageNum)->order('add_time DESC')->select();
        //$_list = D('IsolationData')->isolationData($list,$userId);
        $data = array(
            'personInfo' => $personInfo,
            'all_score'=>$all_score,
            'available_score'=>$available_score,
            'this_month_score'=>$month_available_score,
            'list'=>$list
        );
        return $data;
    }



    /**
     * 我的积分兑换
     */
    public function integrationExchange($get,$user_id){
        $name = $get['name'];
        $need_score = $get['need_score'];
        $where['user_id'] = array('eq',$user_id);
       if(empty($name)){
           return $this->error(1030,'缺少必要参数');
       }
        if($need_score == ''){
            return $this->error(1030,'缺少必要参数');
        }
        if(!(is_int($need_score) || $need_score >= 0)){
            return $this->error(1030,'所需积分必须是非负整数');
        }
        //本月可用积分
        $months = date("Ym");
        $available_score_arr =  M('integration_record')->field('user_id,score,time')->where(array( 'user_id'=>$user_id))->select();
        $available_score = 0;
        foreach($available_score_arr as $k=>$v){
            $t = date('Ym',$v['time']);
            if(strpos($t,$months) !== false){
                $available_score += $v['score'];
            }
        }
        $available_score = $available_score<0 || $available_score == ''? 0 : $available_score;
       /* $this_month_score = M('integration_record')
        	->field("user_id,sum(score) as sumscore,FROM_UNIXTIME(time,'%Y%m') months")
        	->where($where)
        	->group('months')
        	->having('months='.$months)
        	->select();
        $this_month_score = $this_month_score[0]['sumscore'];*/

       // $available_score = M('integration_record')->where(array('user_id'=>$user_id))->sum('score');

        if($available_score >= $need_score){

            // 自动启动事务支持
            $this->startTrans();
            try {
                //把相应的兑换记录插入积分记录表
                $arr = array(
                    'time'=>time(),
                    'user_id'=>$user_id,
                    'score'=>$need_score*(-1),
                    'type'=>'积分兑换',
                    'describe'=>'积分兑换-'.$name,
                ) ;
                if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $arr['id'] = getNextId('integration_record');
                    // $arr['add_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                }
                $ret = M('integration_record')->add($arr);
                if (false === $ret) {
                    // 发生错误自动回滚事务
                    $this->rollback();
                    return $this->error(1030,'系统错误');
                }
                //把相应的兑换记录插入福利记录表
                $map = array(
                    'name'=>$name,
                    'user_id'=>$user_id,
                    'need_score'=>$need_score,
                    'time'=>time()
                ) ;
                if(strtolower(C('DB_TYPE')) == 'oracle'){
                    $map['id'] = getNextId('welfare_record');
                    // $arr['add_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
                }
                $ret = M('welfare_record')->add($map);
                if (false === $ret) {
                    // 发生错误自动回滚事务
                    $this->rollback();
                    return $this->error(1030,'系统错误');
                }
                // 提交事务
                $this->commit();
                return $this->success(1000,'操作成功');
            } catch (ThinkException $e) {
                $this->rollback();
            }
        }else{
            //    $res = '可用积分不足！';
            return $this->error(1030,'可用积分不足');
        }

    }


    /**
     * 积分记录 type 1全部 2获取  3使用
     * page 分页参数
     */
    public function myIntegrationWater($page,$type,$userId){

        if(empty($page)){
            $page = 1;
        }
        $pageNum = 20;
        $type = intval($type);

        $where = array(
            'user_id'=>$userId,
            '_logic'=>'and',
            'score' => array('gt',0)
        );
        $_where = array(
            'user_id'=>$userId,
            '_logic'=>'and',
            'score' => array('lt',0)
        );
        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $field = "time,describe,user_id,score";
        }else{
            $field = "time,describe,user_id,score";
        }
        //获取的积分
        $_month_get_score = 0;
        $month_get_score = M('integration_record')
        	->field($field)
            ->where($where)
            ->select();
        foreach($month_get_score as $key => $val){
            $_month_get_score += $val['score'];
        }
       //使用的积分
        $_month_use_score = 0;
        $month_use_score = M('integration_record')
        	->field($field)
            ->where($_where)
            ->select();
        foreach($month_use_score as $k => $v){
            $_month_use_score += $v['score'];
        }
        switch($type){
            case 1;
                //当年“全部”积分列表
                //当月获取的积分
                $condition = array(
                    'user_id'=>$userId,
                );
                $month_all_score = M('integration_record')->field($field)->where($condition)
                    ->limit(($page-1) * $pageNum . ',' . $pageNum)->order('id DESC')->select();
                foreach($month_all_score as $_key=>$_val){
                    $month_all_score[$_key]["time"] = date('Y-m-d',$_val['time']);

                }
                $list = array(
                    'month_get_score' => $_month_get_score,//总获取积分
                    'month_use_score' => $_month_use_score,//总使用积分
                    'newArr'     =>  $month_all_score
                );

                if($list){
                    return array('code' => 1000,'message' => '获取成功','data'=>$list);
                }else{
                    return array('code' => 1030,'message' => '无数据返回');
                }
                break;

            case 2;

                $condition = array(
                    'user_id'=>$userId,
                    'score' => array('gt',0)
                );
                $month_all_score = M('integration_record')->field($field)->where($condition)
                    ->limit(($page-1) * $pageNum . ',' . $pageNum)->order('id DESC')->select();
                foreach($month_all_score as $_key=>$_val){
                    $month_all_score[$_key]["time"] = date('Y-m-d',$_val['time']);

                }
                foreach($month_all_score as $key=>$v){
                    $month_all_score[$key]["time"] = date('Y-m-d',$v['time']);

                }
                $list = array(
                    'month_get_score' => $_month_get_score,//总获取积分
                    'newArr'     =>  $month_all_score
                );
                if($list){
                    return array('code' => 1000,'message' => '获取成功','data'=>$list);
                }else{
                    return array('code' => 1030,'message' => '无数据返回');
                }
                break;

            case 3;
                $condition = array(
                    'user_id'=>$userId,
                    'score' => array('lt',0)
                );
                $month_all_score = M('integration_record')->field($field)->where($condition)
                    ->limit(($page-1) * $pageNum . ',' . $pageNum)->order('id DESC')->select();
                foreach($month_all_score as $_key=>$_val){
                    $month_all_score[$_key]["time"] = date('Y-m-d',$_val['time']);

                }

                foreach($month_all_score as $key=>$v){
                    $month_all_score[$key]["time"] = date('Y-m-d',$v['time']);

                }
                $list = array(
                    'month_use_score' => $_month_use_score,//总使用积分
                    'newArr'     =>  $month_all_score
                );
                if($list){
                    return array('code' => 1000,'message' => '获取成功','data'=>$list);
                }else{
                    return array('code' => 1030,'message' => '无数据返回');
                }
                break;
        }
    }
}