<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use \Tool\Qiniu\Storage\UploadManager;
/**
 * 七牛首页Controller
 */
class QiniuController extends HomeBaseController
{
    /**
     * 首页
     */
    public function index(){

        set_time_limit(0);
        ignore_user_abort(true);

        //查询尚未同步到外网的数据
        $data = M('course_video_sync')->where(array('status'=>0))->select();
        if(!$data){
            exit();
        }

        import('Tool.Qiniu.functions');
        $setting=C('UPLOAD_SITEIMG_QINIU');
        $auth = new \Think\Upload($setting,'Qiniu',$setting['driverConfig']);
        $upToken = $auth->UploadToken($setting['driverConfig']['secretKey'],$setting['driverConfig']['accessKey']);
        // 构建 UploadManager 对象
        $uploadMgr = new UploadManager();

        // 上传文件到七牛
        foreach($data as $k=>$v){
            $fileName = substr(strrchr($v['local_path'], '/'), 1);
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $v['local_path'];

            $info = array($fileName,$filePath,date('Y-m-d H:i:s'));

            if(!file_exists($filePath)){
                F('UPLOAD_ERROR',$info);
                continue;
            }
            list($ret, $err) = $uploadMgr->putFile($upToken, $fileName,$filePath);

            $saveData = array(
                'sync_path'=>$setting['driverConfig']['domain'] . $fileName,
                'status'=>2,
                'sync_time'=>date('Y-m-d H:i:s')
            );
            
            if ($err === null) {
                //保存数据
                M('course_video_sync')->where(array('id'=>$v['id']))->save($saveData);
                F('UPLOAD_SUCCESS',$info);
                exit();
            } else {
                for($i=1;$i<=3;$i++){
                    list($ret, $err) = $uploadMgr->putFile($upToken, $fileName, $filePath);
                    if($err !== null){
                        if($i == 3){//重试三次均失败
                            $saveData['status'] = 3;
                            $saveData['sync_path'] = '';
                            $saveData['msg'] = '同步失败';

                            M('course_video_sync')->where(array('id'=>$v['id']))->save($saveData);
                            F('UPLOAD_ERROR',$info);
                            exit();
                        }
                        continue;
                    }else{
                        M('course_video_sync')->where(array('id'=>$v['id']))->save($saveData);
                        F('UPLOAD_SUCCESS',$info);
                        exit();
                    }
                }
            }

            //暂停的秒数,防止内存溢出
            sleep(5);
        }
    }
}


