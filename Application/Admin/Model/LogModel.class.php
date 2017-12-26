<?php 
namespace Admin\Model;
use Common\Model\BaseModel;

/**
 * Class LogModel
 * @package Admin\Model
 * 用户操作日志
 */
class LogModel extends BaseModel{

	//初始化
	public function __construct(){}
	

	public function index($total_page = 10){

        $get['start_time']= I("get.start_time");
        $get['end_time']= I("get.end_time");

        $map = "1=1";

        //获取当前所属组织所有会员
        $start_page = I("get.p",0,'int');

        $keywords = I("get.keywords");

        $typeid = I("get.typeid");

        if($get['start_time'] && $get['end_time']){
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $map .= " and a.login_time >=to_date('".$get['start_time']."','yy-mm-dd hh24:mi:ss')";
                $map .= " and a.login_time <= to_date('".$get['end_time']."','yy-mm-dd hh24:mi:ss')";
            }else{
                $map['a.login_time'] = array('EGT',$get['start_time']);
                $map['a.login_time'] = array('ELT',$get['end_time']);
            }
        }else if($get['start_time']){
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $map .= " and a.login_time > to_date('".$get['start_time']."','yy-mm-dd hh24:mi:ss')";
            }else{
                $map['a.login_time'] = array('GT',$get['start_time']);
            }
        }else if($get['end_time']){
            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $map .= "and a.login_time < to_date('".$get['end_time']."','yy-mm-dd hh24:mi:ss')";
            }else{
                $map['a.login_time'] = array('LT',$get['end_time']);
            }
        }

        if(!empty($keywords)){

            if(strtolower(C('DB_TYPE')) == 'oracle'){
                $map .= " and (b.email like '".$keywords."' OR a.login_ip like '".$keywords."')";
            }else{
                $map['b.email|a.login_ip'] = array('like',"%".$keywords."%");
            }

        }

        $list = M("login_log a")
            ->field("a.login_ip,a.login_typeid,to_char(a.login_time,'YYYY-MM-DD HH24:MI:SS') as login_time,b.username,b.email,b.phone,d.name as tissue_name,a.login_msg,a.login_classid")
            ->join("LEFT JOIN __USERS__ b ON a.login_user_id = b.id LEFT JOIN __TISSUE_GROUP_ACCESS__ c ON a.login_user_id = c.user_id LEFT JOIN __TISSUE_RULE__ d ON c.tissue_id = d.id")
            ->where($map)
            ->order('a.id desc')
            ->page($start_page,$total_page)
            ->select();

        $count = M("login_log a")
            ->where($map)
            ->join("LEFT JOIN __USERS__ b ON a.login_user_id = b.id LEFT JOIN __TISSUE_GROUP_ACCESS__ c ON a.login_user_id = c.user_id LEFT JOIN __TISSUE_RULE__ d ON c.tissue_id = d.id")
            ->where($map)
            ->count();


        $show = $this->pageClass($count,$total_page);

        $data = array(
            "typeid"=>$typeid,
            "list"=>$list,
            "pages"=>$show,
            'start_time'=>$get['start_time'],
            'end_time'=>$get['end_time'],
            'keywords'=>$keywords
        );

        return $data;

	}

}
