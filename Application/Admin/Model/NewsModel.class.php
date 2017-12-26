<?php 

namespace Admin\Model;

use Common\Model\BaseModel;


/**
 * 资讯管理模型
 */
class NewsModel extends BaseModel{


//----------------------话题详情全部逻辑-------------------------------


	/**
     *话题详情
     */
     public function getlist()
	 {
       //接收话题的id
       $news_id = I('get.id') + 0;
       
       if(strtolower(C('DB_TYPE')) == 'oracle'){
       $arr = M('news_interaction')
       		->alias('a')
            ->join('left join __USERS__ b  on b.id=a.user_id')
            ->where(array('a.news_id'=>$news_id))
            ->field("b.username,b.avatar,a.id,a.news_id,a.user_id,a.content,a.images,a.status,a.pid,to_char(a.publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time") 
            ->select();
       }else{
        $arr = M('news_interaction')
       		->alias('a')
            ->join('left join __USERS__ b  on b.id=a.user_id')
            ->where(array('a.news_id'=>$news_id))
            ->field("b.username,b.avatar,a.id,a.news_id,a.user_id,a.content,a.images,a.status,a.pid,a.publish_time") 
            ->select();

       }

       
        foreach($arr as $k=>$v){
                //   M('news_interaction')->where($v['pid'])->getField('user_id') 
                //每条数据加上“对谁回复”的栈toname
            $usernameArr = M('news_interaction')
                        ->alias('a')
                        ->join('left join __USERS__ b  on b.id=a.user_id')
                        ->where(array('a.id'=>$v['pid']))
                        ->field('b.username,b.id')
                        ->find();
            $username = $usernameArr['username'];
            $tempuser_id = $usernameArr['id'];
            $arr[$k]['toname'] = $username;
            $arr[$k]['touser_id'] = $tempuser_id;
            //每条数据加上点赞的栈news_praise
            $praise = M('news_praise')->where(array('cid'=>$v['id']))->sum('praise');
            $arr[$k]['praise'] = $praise;
            
            $whetherPraise = M('news_praise')->where(array('cid'=>$v['id'],'user_id'=>$_SESSION['user']['id']))->getField('praise');
            $arr[$k]['whetherPraise'] = $whetherPraise;

            $arr[$k]['content'] =  htmlspecialchars_decode($v['content']);
        }

       $list = $this->getTreeDatas($arr,'tree','','id','id','pid'); //调用basemodel的的getTreeDatas，取得tree
   
       //重新组装二维数组
       static $key  = 1;
       foreach ($list as $k => $v) {
        if($v['pid'] == 0){
            $key = $k;
            $list[$key]=$v;
        }else{
            
            $list[$key]['subReply'][]=$v;
            unset($list[$k]);
        }  
     }

      //   array_multisort($list,SORT_DESC); 
       krsort($list); //对数组进行降序排序 
     //对每条话题加入评论数
        foreach ($list as $k => $v) {
          $list[$k]['subComments'] =  count($v['subReply']);

        }
       //数组分页
        $size = 15;
        $pageData = $this->arrayPage($list,$size);
        $list = $pageData['list'];
        $show = $pageData['show'];

        $data = array(
            'list'=>$list,
            'page'=>$show
        );
        
      return $data;
     }


	/**
    *新增互动/话题
    */
     public function add()
	 {
         
       $news_id = I('post.news_id');
       if(I('post.type') == 'public'){ //发布
           $orderno_data = D('Trigger')->orderNumber(5);
           $orderno = $orderno_data['no']; 

           $data = array(
           'orderno'=>$orderno,
           'news_id'=>$news_id,
           'user_id'=>$_SESSION['user']['id'],
           'content'=>I('post.content'),
           'publish_time'=>date('Y-m-d H:i:s'),
           'status'=>1,
           'pid'=>0
          );
       }else{  //回复
           $pid = I('post.pid');
           $content = I('post.content');
           $data = array(
           'news_id'=>$news_id,
           'user_id'=>$_SESSION['user']['id'],
           'content'=> $content,
           'publish_time'=>date('Y-m-d H:i:s'),
           'status'=>1,
           'pid'=> $pid
          );

          //@$this->hudongMessage($pid,'互动评论');  
       }
         if(strtolower(C('DB_TYPE')) == 'oracle'){
			$data['id'] = getNextId('news_interaction');
            $data['publish_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
		}
    //   dump($data['news_id']);die;
      if($orderno_data['status'] == 0) $data['status'] = 1; 
       $res = M('news_interaction')->add($data);
       
       return $res;

          
     }

	/**
    *话题/评论的 删除
    */
     public function del()
	 {
        $id = I('post.id','','intval') ; 
        
        $arr = M('news_interaction')->where(array('status'=>1))->select();
        
        $list = $this->getOneTree($arr,$id,1);  //调用basemodel的getOneTree公共方法

        $list[]=array( 'id'=>$id );
        // dump($list); 
        // 自动启动事务支持
        $this->startTrans();
        try {  
            foreach($list as $v) {  
             $res = M('news_interaction')->where(array('id'=>$v['id']))->delete();
              if (false === $res) {
                    // 发生错误自动回滚事务
                    $this->rollback();
                    return false;
                } 
            }  
         // 提交事务
            $this->commit();
           
             return true;
        } catch (ThinkException $e) {
            $this->rollback();
        }
 
     }

	/**
    *话题的 点赞
    */
     public function praise()
	 {
       $cid = I('post.id',0,'intval') ;
       $type =  I('post.type') ;
       $map = array(
              'cid'=>$cid,
              'user_id'=>$_SESSION['user']['id']
              );
       if($type == "praise"){ //点赞操作  
       $ret = M('news_praise')->where($map)->find(); //判断登陆者是否对该条话题进行点赞过
       if(!$ret){//初次点赞
           $arr = array(
               'cid'=> $cid,
               'praise'=>1,
               'praise_time'=>date('Y-m-d H:i:s'),
               'user_id'=>$_SESSION['user']['id']
           );
           if(strtolower(C('DB_TYPE')) == 'oracle'){
			$arr['id'] = getNextId('news_praise');
            $arr['praise_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
	    	} 
           $res = M('news_praise')->add($arr);
            if($res){
                //@$this->hudongMessage($cid,'互动点赞');
                return true;
            }else{
                return false;
            }
       }else{ //点赞后的二次操作，点赞操作
          $arr = array(
               'praise'=>1,      
           );
          $res = M('news_praise')->where($map)->save($arr);
           if($res){
                return true;
            }else{
                return false;
            }
       }

       }else if($type == "cancel"){ //取消点赞操作
          $arr = array(
               'praise'=>0,      
           );
          
        //   echo $cid;
          $res = M('news_praise')->where($map)->save($arr);
        //   print_r($res);
            if($res){
                return true;
            }else{
                return false;
            }       
       }
    
     }
	/**
    *互动-消息触发
    */
     public function hudongMessage($cid,$title)
	 {
        $user_id = M('news_interaction')->where(array('id'=>$cid))->getField('user_id');

        $contents_time = date('Y-m-d H:i:s');
        $type_id = 14;
        $from_id = $_SESSION['user']['id'];
        $url = 'Admin/FriendsCircle/friendsCircleList#c'.$cid;
        // D('Trigger')->messageTrigger($user_id,$title,$contents_time,$type_id,$from_id,$url);
     }





    /*
     *工作圈列表-判断该话题是否已点赞
     * list
     */
    public function whetherPraise()
    {  
         $id = I('id');
         $mytagid = $_SESSION['user']['id'];
         $map = array('cid'=>$id,'user_id'=>$mytagid);
		  $num = M('news_praise')->where($map)->getField('praise');
          if($num == 1){
              return 'aaaaa';
          }
    }



	/**
    *获取话题详情
    */
     public function getTheTopic($news_id)
	 {
        $data = M('group_topic')->alias('a')
                 ->join('left join __USERS__ b on a.user_id=b.id')
                 ->field('a.*,b.username,b.avatar')
                 ->where(array('a.id'=>$news_id))
                 ->find();

        return $data; 
     }




    public function circle($list){
         
         foreach($list as $k=>$v){
             if($v['_data']){
                //  echo '2'.'<br/>';
               foreach($v['_data'] as $k1=>$v1){ 
               $list[$k]['_data'][$k1]['toname'] = $v['username'];
        
               $this->circle($v1);
               
               }     
             }       
        }
        return $list;    
      }

	/**
    *判断是否为组织管理员 是则返回1
    */
    public function getLeader($id){
         $login_id = $_SESSION['user']['id'];

         //登录者是否为管理员
         $exist = M('auth_group_access')->where(array('user_id'=>$login_id,'group_id'=>1))->find();
         
         if($exist){
            $res = 1;
         }else{
            $res = 0;
         }
         return $res;
        }
}

