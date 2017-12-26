<?php 
namespace Admin\Model;
use Common\Model\BaseModel;
/**
 * 资讯详情
 * @author Dujuqiang 20170406
 */
class NewsShowModel extends BaseModel{
	//初始化
	public function __construct(){}
	
    /**
     * 获取图表数据及数据列
     * page 当前页码，不传默认为1
     * pageLen 每页数据条数，不传默认15
     */
	public function index($param){

		$user_id = $_SESSION["user"]["id"];
        //查询当前所属组织
        $where['user_id'] = array("eq",$user_id);

        $tissue_id = M("tissue_group_access")->where($where)->getField("tissue_id");

        //获取方案ID
        $plan_id = M("sys_tissue")->where("tissue_id=".$tissue_id)->getField("plan_id");
        //隔离数据过滤
        //根据方案取出所有组织会员ID
        $sys_tissue_list = M("sys_tissue")->field("tissue_id")->where("plan_id=".$plan_id)->select();

        $userid_all = array();

        foreach($sys_tissue_list as $list){

            $group_access = M("tissue_group_access")->field("user_id")->where("tissue_id=".$list['tissue_id'])->select();

            foreach($group_access as $access){
                $userid_all[] = $access['user_id'];
            }
        }
        $conditions['user_id'] = array("in",$userid_all);
        //获取共享数据
        //type为8表示资讯类型
        $wheres['a.type'] = 8;
        $wheres['b.user_id'] = $user_id;
        $data_id = array();
        $data = M('resource_sharing')
            ->alias('a')
            ->join('left join __TISSUE_GROUP_ACCESS__ b on a.tissue_id=b.tissue_id')
            ->field('a.source_id')
            ->where($wheres)
            ->select();
        //遍历获取所有的资源id
        foreach($data as $v){
            $data_id[] = $v['source_id'];
        }

        if(!empty($data_id)){
            $conditions['id'] = array("in",$data_id);
            $conditions['_logic'] = 'or';
        }
		$size = 15;
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$news = M('news')
                    ->where($conditions)
					->page($param['p'], $size)
					->field("id,template,tissue_id,title,type,content,user_id,img,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time")
					->order('id desc')
					->select();
		}else{
			$news = M('news')->where($conditions)->field("id,template,tissue_id,title,type,content,user_id,img,create_time")->page($param['p'], $size)->order('id desc')->select();
		}
		//关联用户表获取用户名
		foreach ($news as $k => $v) {
			$name = M('users')->field('username')->find($v['user_id']);
			$news[$k]['username'] = $name['username'];
			
			if($v["type"] == 1){
				$news[$k]['type'] = "要闻";
			}elseif($v["type"] == 2){
				$news[$k]['type'] = "培训";
			}elseif($v["type"] == 3){
				$news[$k]['type'] = "通知";
			}elseif($v["type"] == 4){
				$news[$k]['type'] = "活动";
			}else{
				$news[$k]['type'] = "其他";
			}
		}
		$count = M('news')->field("count(id) as num")->select();
		$count = $count[0]["num"];
		$pageNav = $this->pageClass($count, $size);
		return array("pageNav"=>$pageNav, "list"=>$news);
	}
}