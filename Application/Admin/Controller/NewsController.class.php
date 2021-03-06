<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;
/**
 * 资讯控制器
 */
class NewsController extends AdminBaseController
{
    /**
     *资讯列表页
     */
    public function page()
    {
   
        //设置分页大小
        $size = 15;
        $model = M('news');
        $User = M('users');
        $user_id = $_SESSION['user']['id'];
        //获取登录者当前所在的组织id
        $tissue_id = $this->getTissueId($user_id);

        // ->where(array('tissue_id'=>$tissue_id,'template'=>1))
        //列表数据取自己公司资讯和全部综合资讯
        // $where['_string'] = ' (tissue_id='.$tissue_id.' AND template=1 ) OR ( template=2 ) ';
        
        
        //隔离数据过滤
		$specifiedUser = D('IsolationData')->specifiedUser();

		$where['auth_user_id'] = array("in",$specifiedUser);
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$news = $model
					->where($where)
					->field("id,template,tissue_id,title,type,content,user_id,img,auth_user_id,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time")
					->page($_GET['p'], $size)
					->order('id desc')
					->select();
		}else{
			$news = $model->where($where)->page($_GET['p'], $size)->order('id desc')->select();
		}
        $count = $model->where($where)->count();
        // echo  M('news')->getLastSql();
        // dump($news);
        //关联用户表获取用户名，截取标题字串，模板和类型的转换文字
        $news = $this->handle($news);
        
         //隔离数据过滤
        $news = D('IsolationData')->isolationData($news);



        $show = tabPage($count,$size,'p',1);
        // $show = $this->pageClass($count,$size);
        $this->assign('page', $show);
        $this->assign('news', $news);
        $this->display('list');
    }

    //分享给我的
    public function sharingtome(){
        //设置分页大小
        $size = 15;
        $model = M('news');
        $User = M('users');
        $user_id = $_SESSION['user']['id'];
        //获取登录者当前所在的组织id
        $tissue_id = $this->getTissueId($user_id);

        // ->where(array('tissue_id'=>$tissue_id,'template'=>1))
        //列表数据取自己公司资讯和全部综合资讯
        // $where['_string'] = ' (a.tissue_id='.$tissue_id.' AND a.template=1 ) OR ( a.template=2 ) ';
        $where['b.tissue_id'] = $tissue_id; 
        // echo $tissue_id;
        $where['b.type'] = 8;
        $where['b.tissue_id'] = $tissue_id;
		
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$news = $model->alias('a')
                    ->join('left join __RESOURCE_SHARING__ b on a.id=b.source_id')
					->where($where)
					->field("a.id,a.template,a.tissue_id,a.title,a.type,a.content,a.user_id,a.img,a.auth_user_id,to_char(a.create_time,'YYYY-MM-DD HH24:MI:SS') as create_time")
					->page($_GET['p'], $size)
					->order('id desc')
					->select();
		}else{
			$news = $model->where($where)->page($_GET['p'], $size)->order('id desc')->select();
		}
        $count = $model->alias('a')->join('left join __RESOURCE_SHARING__ b on a.id=b.source_id')->where($where)->count();
        // echo  M('news')->getLastSql();
        // dump($news);
        //关联用户表获取用户名，截取标题字串，模板和类型的转换文字
        $news = $this->handle($news);

        $show = tabPage($count,$size,'p',1);
        // $show = $this->pageClass($count,$size);
        $this->assign('page', $show);
        $this->assign('news', $news);       
        $this->display();
    }





    /**
     *关联用户表获取用户名，截取标题字串，模板和类型的转换文字
     */
    public function handle($news)
    {
        
        foreach ($news as $k => $v) {
             $name = M('users')->field('username')->find($v['user_id']);
             $news[$k]['username'] = $name['username'];

             $newstring = re_substr($v['title'],0,30);
             $news[$k]['title'] = $newstring;

             if($v['template'] == 1){
                 $news[$k]['template'] = '公司资讯';
             }else if($v['template'] == 2){
                 $news[$k]['template'] = '综合资讯';
             }else{
                 $news[$k]['template'] = '';
             }

            //  1要闻 2培训 3通知 4活动
             if($v['type'] == 1){
                 $news[$k]['type'] = '要闻';
             }else if($v['type'] == 2){
                 $news[$k]['type'] = '培训';
             }else if($v['type'] == 3){
                 $news[$k]['type'] = '通知';
             }else if($v['type'] == 4){
                 $news[$k]['type'] = '活动';
             }else{
                 $news[$k]['type'] = '';
             } 

           
        }
        return $news;
    }



    /**
     *关联用户表获取用户名，截取标题字串，模板和类型的转换文字
     */
    public function Transformation($data)
    {
        
          if($data['template'] == 1){
                 $data['template'] = '公司资讯';
             }else if($data['template'] == 2){
                 $data['template'] = '综合资讯';
             }else{
                 $data['template'] = '';
             }

            //  1要闻 2培训 3通知 4活动
             if($data['type'] == 1){
                $data['type'] = '要闻';
             }else if($data['type'] == 2){
                 $data['type'] = '培训';
             }else if($data['type'] == 3){
                 $data['type'] = '通知';
             }else if($data['type'] == 4){
                 $data['type'] = '活动';
             }else{
                 $data['type'] = '';
             } 
         return $data;
    }
    /**
     *资讯展示页面
     */
    public function show()
    {
        $id = I('get.id') + 0 ;
        if (empty($id)) {
            $this->error('请传入正确的数据');
        }
        $model = M('news');
        
		if(strtolower(C('DB_TYPE')) == 'oracle'){
			$data = $model->field("id,template,tissue_id,title,type,content,user_id,img,auth_user_id,to_char(create_time,'YYYY-MM-DD HH24:MI:SS') as create_time")->find($id);
		}else{
			$data = $model->find($id);
		}
        //关联用户表获取用户名，截取标题字串，模板和类型的转换文字

        $data = $this->Transformation($data);
    
        $User = M('users');
        $username = $User->field('username')->find($data['user_id']);
        $info = array_merge($username, $data);
        
        //接收资讯的id
        $news_id = I('get.id') + 0;
        
        //判断登录者是否为组织管理员 ，若是则具有删帖的权限
        $whether_leader = D('News')->getLeader($news_id);

        // dump($_SESSION['user']);
        $data = D('News')->getlist();
        // dump($list);
      
        $this->assign('news_id',$news_id);
       
        $this->assign('mytagid',$_SESSION['user']['id']);
        $this->assign('mytagavatar',$_SESSION['user']['avatar']);
        $this->assign('list',$data['list']);
        $this->assign('page',$data['page']);
        $this->assign('whether_leader',$whether_leader);

        $this->assign('info', $info);
        $this->display('show');
    }

    /**
     *新增页面展示页
     */
    public function add()
    {
        $this->assign('type', 'add');
        $this->display('add');
    }


    /**
     *编辑展示页
     */
    public function edit()
    {
        $id = I('get.id');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $new = M('news');
        $news_detail = $new->find($id);
        $news_detail['content'] = htmlspecialchars_decode($news_detail['content']); //把预定义的 HTML 实体 "<"（小于）和 ">"（大于）转换为字符：
        $this->assign('detail', $news_detail);
        $this->display('add');
    }

    /**
     *单个删除
     */
    public function delone()
    {
        if (IS_GET) {
            $id = I('get.id');
            if (empty($id)) {
                $this->error('数据不存在');
            }
            $new = M('news');
            $res = $new->delete($id);
            $title = $new->where(array('id'=>$id))->find();
            if (!$res) {
                $this->error('数据异常');
            }
          write_login_log(6,4,$title['title']);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
            $this->success('删除成功');
        }
    }

    /**
     *删除操作
     */
    public function del($id)
    {
        $new = M('news');
        $title = $new->where(array('id'=>$id))->find();
        write_login_log(6,4,$title['title']);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
        $res = $new->delete($id);
        if ($res) {
            $this->redirect('News/page');
        }
    }


    /**
     *批量删除
     */
    public function multidel()
    {

        if (IS_AJAX) {
            $id_array = I('post.ids');
            foreach ($id_array as $k => $v) {
                 $new = M('news');
                $title = $new->where(array('id'=>$v))->find();
                write_login_log(6,4,$title['title']);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
                 $res = $new->delete($v);
            }

          $data['status'] = 1;  
          $data['info'] = '删除成功';  
          $data['url'] = U('Admin/News/page');

          $this->ajaxReturn($data);
       }
    }



    /**
     *数据增加或修改
     */
    public function update()
    {
        if (IS_POST) {
            $template = I('post.template');
            $title = trim(I('post.title'));
            $type = I('post.type');
            $img = I('post.img');
            
            $content = trim(I('post.content'));
            $id = I('post.id');
            if (empty($template)) {
                $this->error('资讯所属模板类型不能为空');
            }
            if (empty($type)) {
                $this->error('资讯类型不能为空');
            }
             if (empty($img)) {
                $this->error('资讯app封面不能为空');
            }
            
            if (empty($title)) {
                $this->error('标题不能为空');
            }

            if (empty($content)) {
                $this->error('内容不为空');
            }
            $time = date("Y-m-d H:i:s");
            $user_id = $_SESSION['user']['id'];
            //获取登录者当前所在的组织id
            $tissue_id = $this->getTissueId($user_id);
            $data = [
                'template' => $template,
                'tissue_id' => $tissue_id,
                'img'=>$img,
                'title' => $title,
                'type' => $type,
                'content' => $content,
                'create_time' => $time,
                'user_id' => $user_id,
                'auth_user_id'=>$user_id
            ];
			
			if(strtolower(C('DB_TYPE')) == 'oracle'){
				$data['create_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
			}
			
            $news = M('news');
            if (!empty($id)) {
                write_login_log(6,3,$title);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
                $news->where(array('id' => $id))->save($data);

            } else {
            	if(strtolower(C('DB_TYPE')) == 'oracle'){
					$data['id'] = getNextId('news');
				}
				
                $news->add($data);
                write_login_log(6,2,$title);//日志类型（6-资讯） 操作类型（2新增，3编辑，4删除）
            }
            $this->success('成功',U('News/page'));
        }
    }


   public function getTissueId($user_id){
     $tissue_id = M('tissue_group_access')->where(array('user_id'=>$user_id))->getField('tissue_id');
     return $tissue_id; 
   }





//----------------------资讯评论-------------------------------



    /*
     *资讯互动详情
     * list
     */
    public function friendsCircleList()
    {   
        
        $this->display('show');
    }


    /*
     * 资讯互动管理详情
     * list
     */
    public function manageList()
    {   
        //接收资讯的id
        $project_id = I('get.project_id') + 0;

        $data = D('News')->getlist();

        $project_name = D('News')->getItemName($project_id);
        
        $this->assign('project_id',$project_id);
        $this->assign('project_name',$project_name);
        $this->assign('mytagid',$_SESSION['user']['id']);
        $this->assign('mytagavatar',$_SESSION['user']['avatar']);
        $this->assign('list',$data['list']);
        $this->assign('page',$data['page']);
        $this->display('friendsmanage');
    }


    /*
     * 发布工作圈
     * $content
     */
    public function publicFriendsCircle()
    {   
        
        if(IS_POST){
         $res = D('News')->add();
        //  echo $res;
         if($res){
            if(strtolower(C('DB_TYPE')) == 'oracle'){
             $arr = M('news_interaction')->where(array('id'=>$res))->field("to_char(publish_time,'YYYY-MM-DD HH24:MI:SS') as publish_time")->find();
            }else{
             $arr = M('news_interaction')->where(array('id'=>$res))->field("publish_time")->find();
            }

            $publish_time = $arr['publish_time'];
            $data = array(
                'id'=>$res,
                'status'=>1,
                'user_id'=>$_SESSION['user']['id'],
                'username'=>$_SESSION['user']['username'],
                'avatar'=>$_SESSION['user']['avatar'],
                'publish_time'=>$publish_time
            ); 
            //调用公共Model里的方法,发布工作圈状态 积分触发
             //@D('Trigger')->intergrationTrigger($_SESSION['user']['id'],14);           
         }else{
            $data = array(
                'status'=>0,
                'username'=>$_SESSION['user']['username']
            );
         }
         $this->ajaxreturn($data);
        }


    }
	/**
    *话题/评论的 删除
    */
     public function delFriendsCircle()
	 {

       $res = D('News')->del();
       $this->ajaxreturn($res); 
     }

	/**
    *话题的 点赞
    */
     public function praiseFriendsCircle()
	 {

       $res = D('News')->praise();
       //调用公共Model里的方法,对他人学课的问题回复／评论 积分触发
       //@D('Trigger')->intergrationTrigger($_SESSION['user']['id'],15);      
       $this->ajaxreturn($res); 
     }

    /*
     *回复评论-个人互动
     * 
     */
    public function replyFriendsCircle()
    { 
     if(IS_POST){
         $res = D('News')->add();
        
         if($res){
             //调用公共Model里的方法,对他人学课的问题回复／评论 积分触发
             //@D('Trigger')->intergrationTrigger($_SESSION['user']['id'],15);      
             
             $this->ajaxreturn($res); //为真则返回增加数据的id
   
         }else{
             $this->ajaxreturn(false);
         }
         
        }



    }



    /*
     *工作圈列表-判断该话题是否已点赞
     * list
     */
    public function whetherPraise()
    {  
        //  $map = array('cid'=>$v['id'],'user_id'=>$mytagid);
		//  $num = M('friends_praise')->where($map)->getField('praise');
       return $list = D('News')->whetherPraise();
    }


















   public function edituploadff(){
      clog(aa);
   }


   public function editupload(){
       if(IS_POST){
        // clog(aa);die;
        // if($ftype == 'image'){
        //     $ftype =  array('jpg', 'gif', 'png', 'jpeg', 'bmp');
        // }
        // header("Content-type:text/html");
        // import('ORG.Net.UploadFile');
        // $upload = new UploadFile(); // 实例化上传类
        // $upload->maxSize = -1; // 设置附件上传大小
        // $upload->allowExts = $ftype; // 设置附件上传类型
        // $upload->savePath = './Public/Uploads/'; // 设置附件上传目录
        // $upload->autoSub = true;
        // $upload->subType = 'date';
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     8*1024*1024 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif','png','jpeg','bmp');// 设置附件上传类型
            $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
            $upload->savePath  =    array('jpg', 'gif', 'png', 'jpeg', 'bmp'); // 设置附件上传（子）目录
            $upload->autoSub = true;
            $upload->subName = '';
            // 上传文件
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $this->error = $upload->getError();
                // return false;
                echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$this->_get('CKEditorFuncNum').", '/', '上传失败," . $upload->getErrorMsg() . "！');</script>";
            }else{// 上传成功
                foreach($info as $v){
                    $file_path1 = '/Upload'.'/'.$v['savepath'];
                    $file_path2 = $v['savename'];
                }
                $savepath = $file_path1.$file_path2;
                // $error = 0;
                // $data['error_info']=$error;
                // $data['file']=$file;
                // return  $data;
                  echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$this->_get('CKEditorFuncNum').",'$savepath','');</script>";
            }
       }
        // if (!$upload->upload()) {// 上传错误提示错误信息
        //     echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$this->_get('CKEditorFuncNum').", '/', '上传失败," . $upload->getErrorMsg() . "！');</script>";
        // } else {
                       
        //     $info = $upload->getUploadFileInfo();
        //   echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$this->_get('CKEditorFuncNum').",'$savepath','');</script>";
        // }
    }

}