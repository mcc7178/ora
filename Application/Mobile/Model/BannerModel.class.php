<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/14
 * Time: 17:10
 */

namespace Mobile\Model;
use Think\Model;

/**
 * Class BannerModel
 * @package Mobile\Model
 * User: @Andy-lizhongjian
 */
class BannerModel extends CommonModel{

    protected $tableName = 'Company_banner';

    public function getCarouselFigure($planId){
        $date = date('Y-m-d');
        $time =  strtotime($date);
        if(strtolower(C('DB_TYPE')) == "oracle"){

            $field = "id,banner_img,title,banner_img_site,speed,type,correlate_item,to_char(start_date,'YYYY-MM-DD HH24:MI:SS') as start_date,to_char(end_date,'YYYY-MM-DD HH24:MI:SS') as end_date";
        }else{
            $field = "'company_name',true";
        }
        $company_banner = M('Company_banner')->where(array('plan_id' => $planId))->field($field)->order("banner_img_site ASC")->select();
        if($company_banner) {
            foreach ($company_banner as $value) {
                //1:资讯消息 2:课程培训 3:调研管理
                if($value['type'] == 1){
                    $value['name'] = M('News')->where(array('id' => $value['correlate_item']))->getField('title');
                }elseif($value['type'] == 2){
                    $value['name'] = M('Course')->where(array('id' => $value['correlate_item']))->getField('course_name');
                }elseif($value['type'] == 3){
                    $value['name'] = M('research')->where(array('id' => $value['correlate_item']))->getField('research_name');
                }
                $start_time = strtotime($value['start_date']);
                $end_time = strtotime($value['end_date']);
                $value['banner_img'] = $value['banner_img'] ? $value['banner_img'] : '/Upload/banner/20171020/1710200852107034.png';
                //判断轮播图片是否在有效时间内
                if (($start_time <= $time) && ($time <= $end_time)) {
                    $_company_banner[] = $value;
                }else{
                    $_company_banner[] = array(
                        'id' => 0,
                        'banner_img' => "/Upload/banner/20171020/1710200852107034.png",
                        'title' => '',
                        'banner_img_site' => 0,
                        'speed' => 0,
                        'type' => 0,
                        'start_date' => 0,
                        'end_date' => 0,
                        'correlate_item' => 0

                    );
                }
            }
        }else{
            $_company_banner[] = array(
                'id' => 0,
                'banner_img' => "/Upload/banner/20171020/1710200852107034.png",
                'title' => '',
                'banner_img_site' => 0,
                'speed' => 0,
                'type' => 0,
                'start_date' => 0,
                'end_date' => 0,
                'correlate_item' => 0

            );
        }
        return $_company_banner;
    }
}