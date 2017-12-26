<?php
namespace Mobile\Controller;
use Think\Controller;

/**@User lizhongjian
 * Class CommonController
 * @package Mobile\Controller
 */
class CommonController extends Controller {

    /**
     * @var string 生成token用的hash码
     */
    const HASH_PRE = '(%*&)(&^#@!Adadf$$^*shoes';
    /**
     * @var string token过期时间(1月)
     */
    const TOKEN_EXPIRE = 2592000; 

    /**
     * @var string 秘钥
     */
    const SECRET_KEY = 'rA21VeE8347bScsuIDNq';

    /* 不必验证的接口 */

    public $unCheckAction = array('getToken','login', 'register', 'sendCode','forgotPwdLogin');
    protected $token;
    protected $secret_key;

    /**
     *  所有接口的公共入口
     *  初始化相关对象与参数, 
     *  并检测登录状态(除不需验证的接口)
     */
    public function __construct() {
        parent::__construct();
        $style = !I('get.style',0,'int') ? I('post.style',0,'int') : I('get.style',0,'int');
        $code = I('request.code', '', 'int');
        $email = I('request.email', '', 'trim');
        //查询用户名
        $user_name = M('Users')->where(array('email' => $email))->find();
        if (('Users' == CONTROLLER_NAME) || (('Index' == CONTROLLER_NAME) && ($style == 1 || $style == 2 || $style == 3 || $style == 4 || $style == 23)) || in_array(ACTION_NAME, $this->unCheckAction)) {
            return;
        }elseif(('Index' == CONTROLLER_NAME) && $style == 8 && $code == 1009){//密码超过有效期，执行修改密码
            if(empty($user_name)){
                $this->error(1030, '缺少必要参数email');
            }else{
                return;
            }
        }else{
        $this->token = I('request.token', '', 'trim');
        $this->secret_key = I('request.secret_key', '', 'trim');
        if (empty($this->token) || empty($this->secret_key)) {
            $this->error(1023, '缺少必要参数token/secret_key');
        } 
        if (self::SECRET_KEY != $this->secret_key) {
       
            $this->error(1032, '不合法的secret_key');
        }
        $field = 'token_expires,status';

        $model = M('Users');

        $result = $model->where(array('token' => $this->token, 'is_login' => 1))->field($field)->find();
		
        if (empty($result)) {
           $this->error(1033, '不合法的token');
        }
        if (time() >= $result['token_expires']) {
           $this->error(1034, 'token超时');
        }
        if (isset($result['status']) && 1 != $result['status']) {
           $this->error(1031, '账号待审核中');
        }
        }
    }


    /*
       * token过期重新获取有效token
       * $user_id 用户id
       */
    public function getToken() {
        $user_id = I('get.id');
        $token = M('Users')->where(array('id' => $user_id))->getField('token');
        $this->success('成功', $token);
    }
   
    /**
     * 成功返回方法
     * @param String $code  信息提示号
     * @param String $message  返回客户端的内容
     * @param Array $data 返回客户端的数据体
     * @param String 数据传输格式(JSON,JSONP,XML)
     * @param Array 附加的数据体 
     */
    public function success($code,$message, $data = array(), $type = null) {
        $data = $this->dealNull($data);
        if(!empty($data)){
            $return = array(
                'code' => $code,
                'message' => $message,
                'data'    =>$data
            );
        }else{
            $return = array(
                'code' => $code,
                'message' => $message,
            );
        }

        if (!empty($type)) {
            $this->ajaxReturn($return, $type);
        } else {
            $this->ajaxReturn($return);
        }
    }
    
    /**
     * 错误返回方法
     * @param Int $code 错误码
     * @param String $message 错误信息
     * @param Array $data 附加数据体
     * @param String 数据传输格式(JSON,JSONP,XML)
     */
    public function error($code, $message, $data = array(), $type = null) {
        $data = $this->dealNull($data);
        if (!empty($data)) {
            $return = array(
                'code' => $code,
                'message' => $message,
                'data' => $data
            );
        }else{
            $return = array(
                'code' => $code,
                'message' => $message,
            );
        }

        if (!empty($type)) {
            $this->ajaxReturn($return, $type);
        } else {
            $this->ajaxReturn($return);
        }
    }


    /**
     * 过滤字段内容中所包含的空格和html标签
     */
    public function  filterTag(&$str){
        $str = trim($str);
        $str = str_replace('&nbsp;','<br/>',$str);
        $str = str_replace(' ','<br/>',$str);
        $str = mb_ereg_replace("\t",'',$str);
        $str = mb_ereg_replace("\r\n",'',$str);
        $str = mb_ereg_replace("\r",'',$str);
        $str = mb_ereg_replace("\n",'',$str);
        $str = strip_tags($str);
        return trim($str);

    }
    
    /**
     * 跨域设置
     */
    protected function setHeader()
    {
        header('Content-Type:application/json; charset=utf-8');
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:POST,GET");
    }

    /**
     * 获取用户id
     * @return int 用户id
     */
    protected function getUserId() {
        $model = null;
        $model = M('Users');
        $id = $model->where(array('token' => $this->token))->getField('id');
        if (empty($id)) {
            return false;
        }
        return $id;
    }

    /**
     * 获取用户id,判断用户提交方式是否是POST请求
     * @return int 用户id
     */
    protected function  verifyUserDataPost() {
        $model = null;
        $model = M('Users');
        if (!IS_POST) {
            return $this->error(1030,'错误请求方式');
        }
        $id = $model->where(array('token' => $this->token))->getField('id');
        if (empty($id)) {
            return $this->error(1024,'用户不存在');
        }
        return $id;
    }

    /**
     * 获取用户id,判断用户提交方式是否是GET请求
     * @return int 用户id
     */
    protected function  verifyUserDataGet() {
        $model = null;
        $model = M('Users');
        if (!IS_GET) {
            return $this->error(1030,'错误请求方式');
        }
        $id = $model->where(array('token' => $this->token))->getField('id');

        if(empty($id)) {
            return $this->error(1030,'用户不存在');
        }
        return $id;
    }

    /**
     * 图片上传公共方法
     * @param  $config
     */
    public function uploadImages($config){
        /*$config = array(
            'maxSize' => 3145728,
            'savePath' => '',//保存子目录
            'rootPath' => './Upload',//保存根目录
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub' => false,
            'subName' => array('date', 'Ymd')
        );*/

        $upload = new \Think\Upload($config);// 实例化上传类
        $info = $upload->upload();
        if (!$info) {
            return $this->error(1030,$upload->getError());
        } else {

            if(!empty($info[0])){
                foreach($info as $k => $v){

                    $photo['img_url'][$k] =  '/Upload/'.$v['savepath'] . $v['savename'];
                }
            }
            if(!empty($info['file'])){

                $photo['img_url'] =  '/Upload/'.$info['file']['savepath'] . $info['file']['savename'];

            }
            //保存图片路径
            //$image = new \Think\Image();
            //$image->open($photo);
            //$image->thumb(271, 188)->save($photo);
            //$maps["img"] = substr_replace($photo, "", 0, 1);
            return  $photo;
        }
    }

    //去除数组null值
    public function dealNull($inputArray){
        $newArr = array();
        foreach ($inputArray as $key=>$value){
            if(is_array($value)){
                $newArr[$key] = self::dealNull($value);
            }else{
                if(is_null($value)){
                    $value = "";
                }
                $newArr[$key] = $value;
            }
        }
        return $newArr;
    }

    /**
     * 判断添加课程，考试，调研的时间必须在项目设定的时间范围内
     * @param $startTime 开始时间
     * @param $endTime   结束时间
     * @param $projectId  关联项目的id
     */
    public function checkTime($projectId,$startTime,$endTime){
         //实例化模
        //查询项目开始和结束时间
        if (strtolower(C('DB_TYPE')) == 'oracle') {
            $field = "to_char(start_time,'YYYY-MM-DD HH24:MI:SS') as start_time  ,to_char(end_time,'YYYY-MM-DD HH24:MI:SS') as end_time";
            }else{
            $field = "start_time,end_time";
        }
        $Admin_project  = M('Admin_project') -> field($field) -> where(array('id' => $projectId)) -> find();
        if($startTime > $Admin_project['start_time'] && $endTime < $Admin_project['end_time']){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 密码解密AES
     */
    public function decryption($password){

        //解密
        $privateKey = "04eb029e72b446e7";

        $iv= "04eb029e72b446e7";

        //解密
        $encryptedData = base64_decode($password);

        $decrypted =  $this->stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv));

        return $decrypted;
    }

    /**
     * 除去pkcs7 padding
     *
     * @param String 解密后的结果
     *
     * @return String
     */
    private function stripPkcs7Padding($string){
        //计算解密后的字符串与输入的字符串长度的差值
        $slast = ord(substr($string, -1));
        //从指定的 ASCII 值返回字符
        $slastc = chr($slast);
        //$pcheck = substr($string, -$slast);
        //正则判断字符串中是否包含有返回的字符和差值数字
        if(preg_match("/$slastc{".$slast."}/", $string)){
            $string = substr($string, 0, strlen($string)-$slast);
            return $string;
        } else {
            return false;
        }
    }

    /**
     * @Prarm 密码验证
     * @Prarm $passWord
     */
    public function checkPassWord($passWord){
        if(!preg_match("/^(?![0-9a-z]+$)(?![0-9A-Z]+$)(?![0-9\W]+$)(?![a-z\W]+$)(?![a-zA-Z]+$)(?![A-Z\W]+$)[a-zA-Z0-9\W_]{8,20}+$/",$passWord)){
            //$data = array("code" => 0, "message" => "密码须含以下任意三项：数字、大写字母、小写字母、特殊字符(如:~!.@#$%^&*_等)且长度为8-20个字符！");
            return false;
        }else{
            return true;
        }
    }
}