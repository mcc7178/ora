<?php 

namespace Admin\Model;

use Common\Model\BaseModel;

    /**
    * 学分申请模型
    */
class CreditsApplyModel extends BaseModel{

 
     /**
     * 申请加分
     */
     public function isApply()
	 {
        $postData = I('post.');
        $apply_title = I('post.apply_title');
        $apply_description = I('post.apply_description');
        $attachment = I('post.attachment');
        $add_score = I('post.add_score');
        // echo mb_strlen($apply_title,'utf-8');
        
        if($apply_title == ''){
            $this->error = '申请标题不能为空！';
            return false;
        }else if(mb_strlen($apply_title,'utf-8') > 40){
            $this->error = '申请标题长度不能大于40字符！';
            return false;
        }else if($apply_description == ''){
            $this->error = '申请说明不能为空！';
            return false;
        }else if($add_score == ''){
            $this->error = '加分分值不能为空！';
            return false;
        }else if(!is_numeric($add_score)){
            $this->error = '加分分值必须为数字！';
            return false;
        }else if($add_score <= 0){ 
            $this->error = '加分分值必须大于0！';
            return false;
        }else if($add_score >= 999){
        	$this->error = '加分分值必须小于999！';
        	return false;
        }
        $add_score += 0;
        
        
        $orderno_data = D('Trigger')->orderNumber(8);
        $orderno = $orderno_data['no'];

        $where = array(
            'user_id'=>$_SESSION['user']['id'],
            'apply_title'=>$apply_title,
            'apply_description'=>$apply_description,
            'attachment'=>$attachment,
            'add_score'=>$add_score,
            'add_time'=>time(),
            'orderno'=>$orderno
        );
        if(strtolower(C('DB_TYPE')) == 'oracle'){ 
         $where['id'] = getNextId('credits_apply');
        }

        if($orderno_data['status'] == 0) $where['status'] = 1;

        $res = M('credits_apply')->add($where);
        
        if($res){
            return true;
        }else{
            $this->error = '系统错误！';
            return false;
        }

     }

  	/**
     * 申请加分记录
     */
     public function getApplyhistory()
	 {

       $size = 15;
       $p2 = I('p2') ?  I('p2') : 1 ;
       $list2 = M('credits_apply')->where(array('user_id'=>$_SESSION['user']['id']))->page($p2.','.$size)->order('add_time desc')->select();
       //2期审核标题直接显示 加分申请 
       foreach($list2 as $k=>$v){
               
           $list2[$k]['apply_description'] = msubstr(strip_tags($v['apply_description']),0,32);
       }
       $count2 = M('credits_apply')->count();
       $i=2; //设置url的tabType=2
       $show2 = tabPage($count2,$size,'p2',$i);
       
       $data = array(
         'list2'=>$list2,
         'page2'=>$show2
      );
      return $data;
     }



}