<?php

namespace Admin\Controller;

use Common\Controller\AdminBaseController;

/**
 * 供应商管理控制器
 * @Andy
 */
class SupplierController extends AdminBaseController
{
    public $total_page = 15;
    /*
     * 供应商管理,列表
     */
    public function supplierManage()
    {

        $typeid = I("get.typeid",0,'int');

        $total_page = $this->total_page;

        $this->assign('typeid',$typeid);
        $this->assign('supplierManage','供应商管理');
        $this->assign('supplierList','供应商列表');
        if($typeid == 1){

            $approved_data = D('Supplier')->sharingToMe($total_page);
            //接收返回的列表数据
            $this->assign('approved_list', $approved_data['list']);
            //接收返回的分页信息
            $this->assign('approved_page', $approved_data['page']);
            //接收返回的供应商类别数据
            $this->assign('data', $approved_data['xhr']);
            //接收返回的搜索条件
            $this->assign('keyword', $approved_data['keyword']);
            $this->assign('keywords', $approved_data['keywords']);

            $this->display('sharingtome');
        }else{

            $approved_data = D('Supplier')->supplierSearchList($total_page);
            //接收返回的列表数据
            $this->assign('approved_list', $approved_data['list']);
            //接收返回的分页信息
            $this->assign('approved_page', $approved_data['page']);
            //接收返回的供应商类别数据
            $this->assign('data', $approved_data['xhr']);
            //接收返回的搜索条件
            $this->assign('keyword', $approved_data['keyword']);
            $this->assign('keywords', $approved_data['keywords']);
            $this->display();
        }

    }

    /*
     * 供应商详情  外部讲师列表
     */
    public function supplierDetail()
    {
        $total_page = $this->total_page;
        $data = array();
        $data = I('get.');
        $id = $data['sid'];
        if (isset($id)) {
            $result = D('Supplier')->supplierStyleCheck($id);
            // print_r($result);
            $this->assign('ret', $result['list']);
            $this->assign('ret2', $result['main_courses']);
        }
        //供应商详情里关联的外部讲师列表
        $data = D('Supplier')->getOutsideLecturer($total_page, $id);
        $this->assign('supplierManage','供应商管理');
        $this->assign('supplierList','供应商列表');
        $this->assign('list', $data['list']);
        $this->assign('count', $data['count']);
        $this->assign('page', $data['page']);
        $this->display();
    }


    /*
     * 新增供应商
     */
    public function addSupplier()
    {
        if (IS_POST) {

            $data = I("post.");
            $data['main_course'] = implode(',', $data['tag']);//以,方式拼接主打课程
            if(strtolower(C('DB_TYPE')) == 'oracle'){
				$data['sid'] = getNextId('supplier');
			}
            $model = D('Supplier');
            
            if ($model->create($data, 1)) {

                $data['auth_user_id'] = $_SESSION['user']['id'];

                $res = $model->data($data)->add();
                write_login_log(4,2,$data['sname']);//日志类型（4-供应商） 操作类型（2新增，3编辑，4删除）
                if ($res) {
                    F('addSupplier',NULL);
                    $this->success('添加成功', U('supplierManage'));
                }
            } else {

                F('addSupplier',$data);
                $this->error($model->getError());
                
            }

        } else {

            $addSupplier = F('addSupplier');

            $planId = getPlanId();
            $where["plan_id"] = $planId;
            
            $ret = M('SupplierType')->where($where)->select();//获取供应商类型/领域

            // $ret = D('IsolationData')->isolationData($ret);

            $this->assign($addSupplier);
            $this->assign('res',$ret);
            $this->display();
        }

    }


    /*
     * 编辑供应商
     */
    public function editSupplier()
    {
        if (IS_GET) {
            $id = I('get.sid');
            $model = D('Supplier');
            $result = $model->supplierStyleCheck($id);
            
            $planId = getPlanId();
            $where["plan_id"] = $planId;
            $ret = M('SupplierType')->where($where)->select();//获取供应商类型/领域
            
            $this->assign('res', $ret);
            $this->assign('list', $result['list']);
            $this->assign('main_courses', $result['main_courses']);
            // C('TOKEN_ON',false);
            $this->display();
        } else {
            $data = I('post.');
            unset($data['main_course']);
            $data['main_course'] = implode(",", $data['tags']);
            unset($data['tags']);

            // C('TOKEN_ON',false); //此处关闭表单验证,表单令牌验证一般用于表单的添加操作，编辑时不能用
          

            if (D('Supplier')->token(false)->create($data,1)) {
                $xhr = D('Supplier')->where(array('sid' => $data['sid']))->save();
                write_login_log(4,3,$data['sname']);//日志类型（4-供应商） 操作类型（2新增，3编辑，4删除）
                if ($xhr) {
                    $this->success('修改成功', U('supplierManage'));
                } else {
                    $this->error('未作任何修改');
                }
            } else {
                // $this->error('输入电话或手机有误');
                $this->error(D('Supplier')->getError());
            }
        }

    }

    /*
     * 供应商删除操作
     */
    function delSupplier()
    {
        if (IS_POST) {
            $id = I('post.id');
            $exist = M('lecturer')->where(array("sid" => $id))->select();
            if ($exist) {
                $data = array('status' => 0, 'msg' => '该供应商下有相关的讲师！');
                $this->ajaxReturn($data);
            } else {
                $result = M("Supplier")->where(array('sid' => $id))->find();
                $res = M("Supplier")->where(array('sid' => $id))->delete();
                if ($res) {
                     write_login_log(4,4,$result['sname']);//日志类型（4-供应商） 操作类型（2新增，3编辑，4删除）
                    $data = array('status' => 1, 'msg' => '删除成功');
                    $this->ajaxReturn($data);
                } else {
                    $data = array('status' => 0, 'msg' => '删除失败');
                    $this->ajaxReturn($data);
                }
            }

        }
    }


    /*
     * 新增供应商擅长类别
     */
    public function addSupplierStyle()
    {
        $tName = I('post.tname');
    	$resp = D('SupplierType')->addSupplierStyle();
        write_login_log(4,2,$tName);//日志类型（4-供应商） 操作类型（2新增，3编辑，4删除）
    	echo json_encode($resp);
    }                

    /*
     * 供应商类别
     */
    public function supplierCategory()
    {
        $total_page = $this->total_page;
        $approved_data = D('SupplierType')->getSupplierStyle($total_page);
        //接收返回的列表数据
        $this->assign('approved_list', $approved_data['list']);
        //接收返回的分页信息
        $this->assign('approved_page', $approved_data['page']);
        //接收返回的搜索条件
        $this->assign('keyword', $approved_data['keyword']);
        $this->assign('supplierManage','供应商管理');
        $this->assign('supplierStyle','领域管理');
        $this->display();
    }

    /*
     * 供应商类别编辑
     */
    public function editSupplierCategory()
    {
        $tName = I('post.tname');
        $resp = D('SupplierType')->updateSupplierType();
        write_login_log(4,3,$tName);//日志类型（4-供应商） 操作类型（2新增，3编辑，4删除）
        echo json_encode($resp);
    }

    /*
     * 批量删除
     */
    public function del_all()
    {
        $post = (I('post.id'));
        $res = M('SupplierType')->where(array('id' => array('in', $post)))->delete();
        if ($res) {
            //查询要删除的类别的名称
            foreach($post as $key => $val){
                $result = M('SupplierType')->where(array('id' => $val['id']))->find();
                write_login_log(4,4,$result['tname']);//日志类型（4-供应商） 操作类型（2新增，3编辑，4删除）
            }
            $this->ajaxReturn(1);//表示成功
        } else {
            $this->ajaxReturn(0);//表示失败
        }
    }

    /*
     * 删除供应商类别
     */
    public function delSupplierCategory()
    {
        if (IS_POST) {
            $data = I("post.sid");
            $res = M('SupplierType')->where(array('id' => $data))->delete();
            if ($res) {
                $result = M('SupplierType')->where(array('id' => $data))->find();
                write_login_log(4,4,$result['tname']);//日志类型（4-供应商） 操作类型（2新增，3编辑，4删除）
                $this->ajaxReturn($res);//表示成功
            } else {
                $this->ajaxReturn(0);//表示失败
            }
        } else {
            $this->ajaxReturn(array('info' => '不合法请求'));
        }
    }
}