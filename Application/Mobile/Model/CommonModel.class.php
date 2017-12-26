<?php
/**
 * Created by PhpStorm.
 * User: @Andy-lizhongjian
 * Date: 2017/2/23
 * Time: 11:00
 */

namespace Mobile\Model;

use Think\Model;

/**
 * 基类模型
 * Class CommonModel
 * @package Mobile\Model
 */
class CommonModel extends Model
{
    const TIME_KEY = 'MOB_time';
    const HASH_PRE = '(%*&)(&^#@!Adadf$$^*shoes';//用来生成token的hash字符串
    const TOKEN_EXPIRE = 2592000;  // token有效期一个月
    const SECRET_KEY = 'rA21VeE8347bScsuIDNq';

    /**
     * 封装成功返回的方法(Model层返回）
     * @param $code  返回信息提示号
     * @param $message  返回信息
     * @param array $data 返回数据集
     * @return array
     */
    public function success($code, $message, $data = array())
    {
        $data = $this->dealNull($data);
        if (empty($data)) {//当需要返回数据为空时执行
            return array(
                'code' => $code,
                'message' => $message,
            );
        } else {
            return array(//当需要返回数据不为空时执行
                'code' => $code,
                'message' => $message,
                'data' => $data
            );
        }
    }

    /**
     * 封装失败返回的方法(Model层返回）
     * @param $code  返回信息提示号
     * @param $message  返回信息
     * @param array $data 返回数据集
     * @return array
     */
    public function error($code, $message, $data = array())
    {
        $data = $this->dealNull($data);
        if (empty($data)) {//当需要返回数据为空时执行
            return array(
                'code' => $code,
                'message' => $message,
            );
        } else {
            return array(//当需要返回数据不为空时执行
                'code' => $code,
                'message' => $message,
                'data' => $data
            );
        }
    }

    /**
     * 生成token
     * @param  int $mobile 手机号
     * @return string 加密后的token值
     */
    public function makeToken($mobile)
    {
        return md5(self::HASH_PRE . $mobile . time());
    }


    /*@param $str
     * 允许密码输入6-12位
     */
    public function isPassword($str)
    {
        if (!preg_match('/^[_0-9a-z]{6,16}$/i', $str)) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * 验证邮箱格式
     */
    public function is_email($email_address)
    {
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if (preg_match($pattern, $email_address)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 需要校验的字符串 $val
     * $min 最小长度  
     * $max  最大长度
     */
    function isStrLength($val, $min, $max)
    {
        $val = trim($val);
        return (preg_match("^[a-zA-Z0-9]{" . $min . "," . $max . "}$", $val)) ? true : false;
    }


    //去除数组null值
    public function dealNull($inputArray)
    {
        $newArr = array();
        foreach ($inputArray as $key => $value) {
            if (is_array($value)) {
                $newArr[$key] = self::dealNull($value);
            } else {
                if (is_null($value)) {
                    $value = "";
                }
                $newArr[$key] = $value;
            }
        }
        return $newArr;
    }

    /*
     * 获取树状结构方法
     */
    function tree($data = array(), $pid = 0, $deep = 0, &$new = array())
    {

        static $i;
        $i++;
        $deep++;

        foreach ($data as $k => $v) {
            //循环第一次把一级的评论数据放入一个数组中
            $item[$k] = $v;
            foreach ($data as $list) {

                if ($v['id'] == $list['pid']) {
                    $item[$k]['child'][] = $list;
                }
            }
        }
        return $item;
    }

    /**
     * 组织构架左侧树形
     */
    public function trees($pid)
    {

        $rule_list = M("tissue_rule")->select();
        if ($pid == null) {
            $pid = 0;
        }
        //获取一级分类
        $top = M("tissue_rule")->where("id=" . $pid)->find();
        // 获取一级下所有下级分类
        $item = \Org\Nx\Data::channelLevel($rule_list, $pid, '&nbsp;', 'id');

        $top['_data'] = $item;

        return $top;
    }

    /**
     * 查询当前所在层级
     */
    public function hierarchy($id, &$num = 0)
    {

        if (!empty($id)) {
            $is_display = M("tissue_rule")->field("pid")->where("id=" . $id)->find();
        }


        if (!empty($is_display)) {
            $num++;
            $this->hierarchy($is_display['pid'], $num);
        }

        return $num;
    }

    /**
     * 取部门
     */
    public function getDepartmentData($level, &$data, &$pkMember_list, &$admin_list)
    {

        $level_arr = array(1 => 2, 2 => 1);

        if ($level >= 3) {

            $pkMember_list[] = $data;

        } else {

            foreach ($data['_data'] as $items) {

                if ($items['_level'] == $level_arr[$level]) {

                    $pkMember_list[] = $items;
                } else {

                    $this->getDepartmentData($level, $items, $pkMember_list, $admin_list);
                }
            }
        }
        return $pkMember_list;

    }

    //获取课程评论子评论pid
    public function getCommentChild($cid, $cidStr)
    {
        $cid += 0;
        if (!is_int($cid) || $cid < 0) {
            return false;
        }

        $cat = M("colligate_comment")->where("pid=" . $cid)->select();
        if ($cat) {
            foreach ($cat as $key => $v) {
                $cidStr .= $v["id"] . ",";
                $cidStr = self::getCommentChild($v["id"], $cidStr);
            }
        }
        return $cidStr;
    }

    /*
     * 获取子评论pid
     */
    public function getFriendCommentChild($cid, $cidStr)
    {
        $cid += 0;
        if (!is_int($cid) || $cid < 0) {
            return false;
        }
        $cat = M("FriendsCircle")->where("pid=" . $cid)->select();
        if ($cat) {
            foreach ($cat as $key => $v) {
                $cidStr .= $v["id"] . ",";
                $cidStr = self::getFriendCommentChild($v["id"], $cidStr);
            }
        }
        return $cidStr;
    }

    /*
 * 获取子评论pid
 */
    public function getTopicCommentChild($cid, $cidStr)
    {
        $cid += 0;
        if (!is_int($cid) || $cid < 0) {
            return false;
        }
        $cat = M("topic_interaction")->where("pid=" . $cid)->select();
        if ($cat) {
            foreach ($cat as $key => $v) {
                $cidStr .= $v["id"] . ",";
                $cidStr = self::getFriendCommentChild($v["id"], $cidStr);
            }
        }
        return $cidStr;
    }

    /**
     * 获取子孙全部数据
     * @param  string $$arr  传入的数组
     * @param  string $type tree获取树形结构 level获取层级结构
     * @param  string $order 排序方式
     * @return array         结构数据
     */

    public function getTreeDatas($arr, $type = 'tree', $order = '', $name = 'name', $child = 'id', $parent = 'pid')
    {

        $data = $arr;
        // 获取树形或者结构数据
        if ($type == 'tree') {
            $data = \Org\Nx\Data::tree($data, $name, $child, $parent);
        } elseif ($type = "level") {
            $data = \Org\Nx\Data::channelLevel($data, 0, '&nbsp;', $child);
        }
        return $data;
    }


    /*
     * 根据子评论id查找父级id（工作圈）
     */
    public function findParentId($pid)
    {
        $info = M('FriendsCircle')->where(array('id' => $pid))->find();
        if ($info['pid'] != 0) {
            $info['id'] = $this->findParentId($info['pid']);
        }
        return $info['id'];

    }

    /*
    * 根据子评论id查找父级id(课程)
    */
    public function findCourseParentId($pid)
    {
        $info = M('Colligate_comment')->where(array('id' => $pid))->find();
        if ($info['pid'] != 0) {
            $info['id'] = $this->findParentId($info['pid']);
        }
        return $info['id'];

    }


    /**
     * 个人中心 学分 学时 学分来源
     * @$typeid 学分类型
     * @$source_id 课程id
     * @$project_id 项目id
     */
    public function centerStudy($typeid, $source_id)
    {

        switch ($typeid) {
            case 0:
                $examination = M("examination")->field("test_name")->where("id=" . $source_id)->find();
                $rows = "我的考试-" . $examination['test_name'];
                break;
            case 1:
                $rows = "项目调研";
                $survey = M("survey")->field("survey_name")->where("id=" . $source_id)->find();
                $rows = "项目调研-" . $survey['survey_name'];
                break;
            case 2:
                $rows = "我的课程";
                break;
            case 3:
                $research = M("research")->field("research_name")->where("id=" . $source_id)->find();
                $rows = "其它调研-" . $research['research_name'];
                break;
            case 4:
                $course = M("course")->field("course_name")->where("id=" . $source_id)->find();
                $rows = "必修课程-" . $course['course_name'];
                break;
            case 5:
                $course = M("course")->field("course_name")->where("id=" . $source_id)->find();
                $rows = "选修课程-" . $course['course_name'];
                break;
            default:
                $rows = "其它";
        }

        return $rows;

    }


    /**
     * 用户学分添加
     * $typeid 添加学分的类型
     * $credit 学分
     * $source_id 考试/调研/课程id
     * $project_id 关联项目id 如果是被指定的则有项目id,否则就给默认值0
     * $userId 用户id
     *
     */
    public function creditAdd($typeid, $credit, $source_id, $project_id = 0, $userId)
    {

        try {

            $DB = M('center_study');

            $DB->startTrans();

            $data = array(
                "create_time" => date("Y-m-d H:i:s", time()),
                "typeid" => $typeid,
                "credit" => $credit,
                "source_id" => $source_id,
                "project_id" => $project_id,
                "user_id" => $userId
            );

            $DB->data($data)->add();

            $DB->commit();

        } catch (Exception $e) {

            $DB->rollback();

        }
    }


    /**
     * @param $input
     * @param $columnKey
     * @数组按照某个字段作排序
     * @param null $indexKey
     * @return array
     */
    public function i_array_column($input, $columnKey, $indexKey = null)
    {
        if (!function_exists('array_column')) {
            $columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
            $indexKeyIsNull = (is_null($indexKey)) ? true : false;
            $indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
            $result = array();
            foreach ((array)$input as $key => $row) {
                if ($columnKeyIsNumber) {
                    $tmp = array_slice($row, $columnKey, 1);
                    $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : null;
                } else {
                    $tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
                }
                if (!$indexKeyIsNull) {
                    if ($indexKeyIsNumber) {
                        $key = array_slice($row, $indexKey, 1);
                        $key = (is_array($key) && !empty($key)) ? current($key) : null;
                        $key = is_null($key) ? 0 : $key;
                    } else {
                        $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                    }
                }
                $result[$key] = $tmp;
            }
            return $result;
        } else {
            return array_column($input, $columnKey, $indexKey);
        }
    }


    /**
     * @Param $array_data
     * @Param $sort
     * @遍历后的数组按某字段作排序,分页
     */
    public function array_sort($array_data, $sort, $page, $pageLen)
    {
        /*$sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => '',       //排序字段
        );*/
        $arrSort = array();
        foreach ($array_data AS $uniqid => $row) {
            foreach ($row AS $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if ($sort['direction']) {
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $array_data);
        }
        global $countpage; //定全局变量
        //$page=(empty($page))?'1':$page; //判断当前页面是否为空 如果为空就表示为第一页面
        // $start=($page-1)*$pageLen; //计算每次分页的开始位置
        /*if($order==1){
            $array=array_reverse($array);
        }*/
        $totals = count($array_data);
        $countpage = ceil($totals / $pageLen); //计算总页面数
        $arrayData = array_slice($array_data, $page, $pageLen);
        return $arrayData;
    }

    /**
     * 过滤字段内容中所包含的空格和html标签
     */
    public function  filterTag(&$str)
    {
        $str = trim($str);
        $str = str_replace('&nbsp;', '<br/>', $str);
        $str = str_replace(' ', '<br/>', $str);
        $str = mb_ereg_replace("\t", '', $str);
        $str = mb_ereg_replace("\r\n", '', $str);
        $str = mb_ereg_replace("\r", '', $str);
        $str = mb_ereg_replace("\n", '', $str);
        $str = strip_tags($str);
        return trim($str);

    }

    /******************************************************************已使用pc端审核触发规则(暂时保留)***************************************************************************/
    /**
     * @新建任务，查询审核规则表获取数据
     * @$taskId  对应任务表id(用户注册，则为用户id,新建项则为项目id)
     * @$type 类型   1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册
     */
    public function getTaskData($taskId, $type)
    {
        $audit_rule = M('audit_rule')->where(array('type' => $type))->find();
        $insertData['correlate_id'] = $taskId;
        $insertData['type'] = $type;
        $insertData['levalone_man'] = 0;
        $insertData['levaltwo_man'] = 0;
        $insertData['levalthree_man'] = 0;
        $insertData['oneaudit_role'] = $audit_rule['oneaudit_role'];
        $insertData['twoaudit_role'] = $audit_rule['twoaudit_role'];
        $insertData['threeaudit_role'] = $audit_rule['threeaudit_role'];
        $insertData['is_condition'] = $audit_rule['is_condition'];
        $insertData['condition_id'] = $audit_rule['condition_id'];
        $insertData['condition_id'] = $audit_rule['condition_id'];
        $insertData['one_level_type'] = $audit_rule['one_level_type'];
        $insertData['two_level_type'] = $audit_rule['two_level_type'];
        $insertData['three_level_type'] = $audit_rule['three_level_type'];
        $insertData['oneaudit_user_id'] = $audit_rule['oneaudit_user_id'];
        $insertData['twoaudit_user_id'] = $audit_rule['twoaudit_user_id'];
        $insertData['threeaudit_user_id'] = $audit_rule['threeaudit_user_id'];
        $insertData['audit_status'] = 0;
        $insertData['num'] = $audit_rule['num'];
        if (strtolower(C('DB_TYPE')) == 'oracle') {
            $insertData['id'] = getNextId('audit');
        }
        $res = M('audit')->data($insertData)->add();
        return $res;
    }

    /**
     *点击待审核时触发生成关联表数据，并返回登录者的角色id
     *$type:  1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册，10：业务部落，11：申请加学分
     */
    public function clickTrigger($type)
    {
        if ($type == 1) {

            $map = array(
                'type' => 2,

            );
            $lists = M('admin_project')
                ->where($map)
                ->field('*')
                ->select();

            // echo M('admin_project')->_sql();
        } else if ($type == 2) {
            $map = array(
                'status' => 0,

            );
            $lists = M('course')
                ->where($map)
                ->field('*')
                ->select();
            // echo M('course')->_sql(); die;
        } else if ($type == 3) {
            $map = array(
                'status' => 0,

            );
            $lists = M('examination')
                ->where($map)
                ->field('*')
                ->select();
        } else if ($type == 4) {
            $map = array(
                'status' => 0,

            );
            $lists = M('survey')
                ->where($map)
                ->field('*')
                ->select();
        } else if ($type == 5) {
            $map = array(
                'status' => 0,

            );
            $lists = M('friends_circle')
                ->where($map)
                ->field('*')
                ->select();
        } else if ($type == 6) {
            $map = array(
                'audit_state' => 0,

            );
            $lists = M('research')
                ->where($map)
                ->field('*')
                ->select();
        } else if ($type == 7) {
            $map = array(
                'audit_status' => 1,

            );
            $lists = M('test')
                ->where($map)
                ->field('*')
                ->select();
        } else if ($type == 8) {
            $map = array(
                'status' => 0,

            );
            $lists = M('integration_apply')
                ->where($map)
                ->field('*')
                ->select();
        } else if ($type == 9) {
            $map = array(
                'status' => 2,

            );
            $lists = M('users')
                ->where($map)
                ->field('*')
                ->select();
        } else if ($type == 10) {
            $map = array(
                'status' => 0,

            );
            $lists = M('topic_group')
                ->where($map)
                ->field('*')
                ->select();
        } else if ($type == 11) {
            $map = array(
                'status' => 0,

            );
            $lists = M('credits_apply')
                ->where($map)
                ->field('*')
                ->select();
        }
        //获取当前审核配置表的数据
        // $type = 1;
        $dataSet = $this->AuditSetData($type);
        // dump($dataSet); exit;
        foreach ($lists as $k => $v) {
            // if($v['type'] === null || $v['type'] !== 2 ){
            //判断为待审，把当前审核配置表配置 往审核表里生成关联数据，
            // 1:培训项目,2:新建课程,3:新建试卷审,4:新建问卷,5:新建互动,6:发起调研7:发起考试,8:发起加分,9:用户注册
            if ($type == 1) {
                $create_user_id = $v['user_id'];
            } else if ($type == 2) {
                $create_user_id = $v['user_id'];
            } else if ($type == 3) {
                $create_user_id = $v['test_heir'];
            } else if ($type == 4) {
                $create_user_id = $v['survey_heir'];
            } else if ($type == 5) {
                $create_user_id = $v['user_id'];
            } else if ($type == 6) {
                $create_user_id = $v['create_user'];
            } else if ($type == 7) {
                $create_user_id = $v['create_user'];
            } else if ($type == 8) {
                $create_user_id = $v['user_id'];
            } else if ($type == 9) {
                $create_user_id = $v['id'];
            } else if ($type == 10) {
                $create_user_id = $v['user_id'];
            } else if ($type == 11) {
                $create_user_id = $v['user_id'];
            }
            $res = $this->auditDataExist($v['id'], $type);
            //  echo $v['id'];

            $tissues = $this->getTissueid($create_user_id, $dataSet['one_level_type'], $dataSet['two_level_type'], $dataSet['three_level_type']);

            if ($tissues['num'] != '') {
                $dataSet['num'] = $tissues['num'];
            }


            if (!$res) {
                //  echo dd;die;
                $auditData = array();
                $auditData = array(
                    'type' => $type,
                    'correlate_id' => $v['id'],
                    'oneaudit_role' => $dataSet['oneaudit_role'],
                    'twoaudit_role' => $dataSet['twoaudit_role'],
                    'threeaudit_role' => $dataSet['threeaudit_role'],
                    'is_condition' => $dataSet['is_condition'],
                    'condition_id' => $dataSet['condition_id'],
                    'conditiona' => $dataSet['conditiona'],
                    'conditionb' => $dataSet['conditionb'],
                    'num' => $dataSet['num'],
                    'one_level_type' => $dataSet['one_level_type'],
                    'two_level_type' => $dataSet['two_level_type'],
                    'three_level_type' => $dataSet['three_level_type'],
                    'oneaudit_user_id' => $dataSet['oneaudit_user_id'],
                    'twoaudit_user_id' => $dataSet['twoaudit_user_id'],
                    'threeaudit_user_id' => $dataSet['threeaudit_user_id'],

                    'one_leader_tissueid' => $tissues['tissue1'],
                    'two_leader_tissueid' => $tissues['tissue2'],
                    'three_leader_tissueid' => $tissues['tissue3'],

                );

                if (strtolower(C('DB_TYPE')) == 'oracle') {
                    $auditData['id'] = getNextId('audit');
                }

                $res = M('audit')->add($auditData);
            }

            //  dump($res);
            // }else{
            //   //非初审，项目表

            // }
        }

        //根据登录者获取登录者的所属角色，可多重角色
        $lander = $_SESSION;
        $gruop_id_arr = M('auth_group_access')
            ->where(array('user_id' => $lander['user']['id']))
            ->field('group_id')
            ->select();

        $temp = array();
        foreach ($gruop_id_arr as $k => $v) {
            $temp[] = $v['group_id'];
        }
        $gruop_id = implode(',', $temp);
        return $gruop_id;
    }


    /**
     *审核设置的数据
     */
    public function AuditSetData($type)
    {
        if ($type == 11) $type = 8;  //申请加学分取申请加分的审核设置
        $map = array(
            'a.type' => $type
        );
        $dataSet = M('audit_rule')->alias('a')
            ->field('a.*,b.name,conditiona,conditionb')
            ->join('left join __AUDIT_CONDITION__ b on b.id = a.condition_id')
            ->where($map)
            ->find();

        return $dataSet;

    }


    /**
     * 各表的未审核数据是否在审核表中存在
     */
    public function auditDataExist($id, $type)
    {

        $res = M('audit')->where(array('type' => $type, 'correlate_id' => $id))->find();

        if ($res) {
            return true;
        } else {
            return false;
        }


    }

    /**
     * 返回创建者所在组织tissue1-上级tissue2-上上级组织id tissue3 ，对应审核轮数num
     *
     */
    public function getTissueid($create_user_id, $one_level_type, $two_level_type, $three_level_type)
    {


        if ($one_level_type == 3 && $two_level_type == 3 && $three_level_type == 3) {
            $temp1 = M('tissue_group_access')->where(array('user_id' => $create_user_id))->getField('tissue_id');
            $temp2 = M('tissue_rule')->where(array('id' => $temp1))->getField('pid');
            $temp2 = $temp2 ? $temp2 : 0;
            $temp3 = M('tissue_rule')->where(array('id' => $temp2))->getField('pid');
            $temp3 = $temp3 ? $temp3 : 0;
            if ($temp3 == 0) {
                $num = 2;
            } else if ($temp2 == 0) {
                $num = 1;
            }
        } else if (($one_level_type == 3 && $two_level_type == 3) || ($one_level_type == 3 && $three_level_type == 3) || ($two_level_type == 3 && $three_level_type == 3)) {
            $temp1 = M('tissue_group_access')->where(array('user_id' => $create_user_id))->getField('tissue_id');
            $temp2 = M('tissue_rule')->where(array('id' => $temp1))->getField('pid');
            $temp2 = $temp2 ? $temp2 : 0;
            $temp3 = M('tissue_rule')->where(array('id' => $temp2))->getField('pid');
            $temp3 = $temp3 ? $temp3 : 0;
            if ($temp2 == 0) {
                $num = 1;
            }
        } else if ($one_level_type == 3) {
            $temp1 = M('tissue_group_access')->where(array('user_id' => $create_user_id))->getField('tissue_id');

        } else if ($two_level_type == 3) {
            $temp2 = M('tissue_group_access')->where(array('user_id' => $create_user_id))->getField('tissue_id');

        } else if ($three_level_type == 3) {
            $temp3 = M('tissue_group_access')->where(array('user_id' => $create_user_id))->getField('tissue_id');

        }

        $data = array(
            'tissue1' => $temp1,
            'tissue2' => $temp2,
            'tissue3' => $temp3,
            'num' => $num,
        );
        return $data;

    }


    /**
     * 学分统计筛选
     * creditType 1年度 2季度 3月份
     */
    public function getStudyCredit($creditType,$userId){
        //echo "<br/>001".microtime();
        $user_id = $userId;
        $nowYearStamp = strtotime(date("Y")."-01-01 00:00:00");
        $nowYear = date("Y");
        if($creditType == 1){
            $startTime = date('Y-01-01 00:00:00');
            $endTime = date('Y-01-01 00:00:00', strtotime("+1 year"));
            $endTime = date('Y-m-d H:i:s', strtotime($endTime) - 1);
        }elseif($creditType == 2){
            $date = getdate();
            $month = $date['mon'];//当前第几个月
            $year = $date['year'];//但前的年份
            $startMonth = ceil($month / 3) * 3 - 2;//单季第一个月
            $strart = mktime(0, 0, 0, $startMonth, 1, $year);//当季第一天的时间戳
            $end = mktime(0, 0, 0, $startMonth + 3, 1, $year);//当季最后一天的时间戳
            $startTime = date('Y-m-d H:i:s', $strart);
            $endTime = date('Y-m-d H:i:s', $end - 1);
        }else{
            $startTime = date('Y-m-01 00:00:00');
            $endTime = date('Y-m-01 00:00:00', strtotime("+1 month"));
            $endTime = date('Y-m-d H:i:s', strtotime($endTime) - 1);
        }

        //申请学分 申请的学分通过后未加入center_study表中，是个bug,替代方案：由credits_apply直接获取数据
        $where1 = "user_id=$user_id";
        $where1 .= " and status=1";
        $where1 .= " and add_time>'".strtotime($startTime)."'";
        $where1 .= " and add_time<'".strtotime($endTime)."'";
        $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
    			SELECT sum(add_score) as apply_credit FROM __CREDITS_APPLY__ WHERE $where1
    		) thinkphp)";
        $applyTotalScore = M()->query($sql);

        //echo "<br/>002".microtime();

        $upCourseNum = 0;//在线学习课程数量
        $downCourseNum = 0;//线下课程学习数量
        $upCredit = 0;//线上课程积分
        $downCredit = 0;//线下课程积分

        //获取在线课程数据
        $where2 = "user_id=".$user_id;
        $where2.= " and typeid in (4,5)";
        $where2.= " and create_time >to_date('".$startTime."','yyyy-mm-dd hh24:mi:ss')";
        $where2.= " and create_time <to_date('".$endTime."','yyyy-mm-dd hh24:mi:ss')";
        $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
			SELECT credit,source_id FROM __CENTER_STUDY__ WHERE $where2
		) thinkphp)";
        $total_credit = M()->query($sql);

        //echo "<br/>003".microtime();

        //center_study只统计了在线课程的数据
        foreach ($total_credit as $value){
            $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				SELECT course_way FROM __COURSE__ WHERE id=".$value["source_id"]."
			) thinkphp) WHERE numrow=1";
            $course = M()->query($sql);
            if($course){
                if($course[0]["course_way"] == 0){
                    $upCredit += $value["credit"];
                    $upCourseNum ++;
                }
            }
        }

        //echo "<br/>004".microtime();

        //培训班--线下课程数据
        $where4 = "a.user_id=".$user_id;
        $where4.= " and a.sign_up='1'";
        $where4.= " and c.type in (0,4)";
        $where4.= " and b.start_time >to_date('".$startTime."','yyyy-mm-dd hh24:mi:ss')";
        $where4.= " and b.start_time <to_date('".$endTime."','yyyy-mm-dd hh24:mi:ss')";

        $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
			SELECT b.*, to_char(b.end_time,'YYYY-MM-DD HH24:MI:SS') as n_end_time FROM __DESIGNATED_PERSONNEL__ a
			join __PROJECT_COURSE__ b on a.project_id=b.project_id
			join __ADMIN_PROJECT__ c on a.project_id=c.id
			WHERE $where4
		) thinkphp)";
        $downPro = M()->query($sql);

        //echo "<br/>004".microtime();

        foreach ($downPro as $value){
            if(strtotime($value["n_end_time"]) > time()){
                //课程时间未结束，不计算在内
                continue;
            }
            $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				SELECT course_way FROM __COURSE__ WHERE id=".$value["course_id"]."
			) thinkphp) WHERE numrow=1";
            $course = M()->query($sql);
            if($course[0]["course_way"] == 1){
                //面授课程数据计算
                $where5 = " a.course_id=".$value["course_id"];
                $where5.= " and b.project_id=".$value["project_id"];
                $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
					SELECT a.attendance_project_id FROM __ATTENDANCE_COURSE__ a
					join __ATTENDANCE_PROJECT__ b on a.attendance_project_id=b.id
					WHERE $where5
				) thinkphp) WHERE numrow=1";
                $attendance_course = M()->query($sql);
                if($attendance_course){
                    //考勤开启
                    $awhere = " user_id=".$user_id;
                    $awhere.= " and status in(1,2)";
                    $awhere.= " and attendance_project_id=".$attendance_course[0]["attendance_project_id"];
                    $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
						SELECT * FROM __ATTENDANCE__ WHERE $awhere
					) thinkphp) WHERE numrow=1";
                    $attendance = M()->query($sql);
                    if($attendance){
                        $downCredit += $value["credit"];
                        $downCourseNum ++;
                    }
                }else{
                    //考勤关闭
                    $downCredit += $value["credit"];
                    $downCourseNum ++;
                }
            }
        }

        //echo "<br/>005".microtime();

        //获取课程学分目标，目标年度学分完成率（当前时间段学分 / 学分目标）
        $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
			SELECT * FROM __TISSUE_GROUP_ACCESS__ WHERE user_id=$user_id
		) thinkphp) WHERE numrow=1";
        $myTissue = M()->query($sql);

        //echo "<br/>006".microtime();

        $myTissue = $myTissue[0];
        $myGole = 0;
        if($myTissue){
            $wheretl = " tissue_id=".$myTissue["tissue_id"];
            $wheretl.= " and job_id=".$myTissue["job_id"];
            $wheretl.= " and typeid=4";
            $sql = "SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (
				SELECT * FROM __TOOL_LEARNING__ WHERE $wheretl
			) thinkphp) WHERE numrow=1";
            $toolGoal = M()->query($sql);
            $toolGoal = $toolGoal[0];

            //按年统计，所有月份相加
            $myGole += $toolGoal["january"] + $toolGoal["february"] + $toolGoal["march"] +$toolGoal["april"]+$toolGoal["may"]+$toolGoal["june"];
            $myGole += $toolGoal["july"]+$toolGoal["august"]+$toolGoal["september"]+$toolGoal["october"]+$toolGoal["november"]+$toolGoal["december"];
            $myGole = ceil($myGole);
        }

        //echo "<br/>007".microtime();

        $finishRate = 0;
        $totalScore = $downCredit + $upCredit + $applyTotalScore[0]["apply_credit"];
        if($myGole > 0){
            $finishRate = round($totalScore / $myGole, 2) * 100;
        }

        $return = array("totalScore"=>$totalScore, "upCredit"=>$upCredit, "downCredit"=>$downCredit, "finishRate"=>$finishRate);
        return $return;
    }


  /**
     *  写入操作日志
     */
    function write_login_log($class_id = 0,$typeid = 0,$msg='',$userId=''){
        $get_client_ip = D('Home/Public')->get_client_ip();
        $data = array(
            "login_user_id"=>$userId,
            "login_ip"=>$get_client_ip,
            "login_typeid"=>$typeid,
            "login_time"=>date("Y-m-d h:i:s",time()),
            "login_msg"=>$msg,
            "login_classid"=>$class_id
        );

        if(strtolower(C('DB_TYPE')) == 'oracle'){
            $data['id'] = getNextId('login_log');
            $data['login_time'] = array('exp',"to_date('".date('Y-m-d H:i:s')."','yy-mm-dd hh24:mi:ss')");
        }

        M("login_log")->data($data)->add();

    }
}