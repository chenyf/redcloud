<?php
namespace Common\Model\Course;

use Common\Model\Common\BaseModel;
use Think\RedisModel;
use \Common\Lib\ArrayToolkit;
/**
 * 视频上报 model
 * @author tht 2015-9-16
 */
class CourseReportServiceModel extends BaseModel {
    private $redisConn = false;
    
    public function __construct() {
        parent::__construct();
        $this->initRedis();
    }
    
    public function initRedis(){
        if($this->redisConn!=false) return false;
        $this->redisConn = RedisModel::getInstance(C('REDIS_DIST.PLAY_REPORT'));
    }

    public function getPlayReportKey($courseId, $lessonId, $uid){
        return "play_report_".C('WEBSITE_CODE')."_{$courseId}_{$lessonId}_{$uid}";
    }


    /**
     * 上报学习数据
     * @param int $courseId, int $lessonId, int $uid ,array $data
     * @author 谈海涛 2015-09-15
     */
    public function reportStudyData($courseId, $lessonId, $uid ,$data){
        if(!$courseId || !$lessonId || !$uid || empty($data) ) return false;
        $playReportKey = $this->getPlayReportKey($courseId, $lessonId, $uid);
        $r = $this->redisConn->hashSet($playReportKey, array('courseId'=>$data['courseId'], 'lessonId'=>$data['lessonId'], 'position'=>$data['position'],'lastPlayPos'=>$data['lastPlayPos'], 'duration'=>$data['duration'], 'status'=>$data['status'], 'brower'=>$data['brower'], 'ip'=>$data['ip'], 'intervalTm'=>$data["intervalTm"],'report_time'=>$data['report_time'], 'report_date'=>$data['report_date'], 'uid'=>$data['uid']));
        return $r ? true : false;
    }
    
    /**
     * 获得学生上报信息
     * @author 谈海涛 2015-09-16
     */
    public function getReportInfo($courseId, $lessonId, $uid){
        if(!$courseId || !$lessonId || !$lessonId ) return false;
        $playReportKey = $this->getPlayReportKey($courseId, $lessonId, $uid);
        return $this->redisConn->hGetAll($playReportKey);
    }
    
    /**
     * 获得视频最后播放位置
     * @author 谈海涛 2015-09-16
     */
    public function getReportPosition($courseId, $lessonId, $uid){
        if(!$courseId || !$lessonId || !$lessonId ) return false;
        $playReportKey = $this->getPlayReportKey($courseId, $lessonId, $uid);
        $report = $this->redisConn->hGetAll($playReportKey);
        return intval($report['position']);
    }
    
    /**
     * 获得视频最远播放位置
     * @author 谈海涛 2015-09-16
     */
    public function getLastPlayPos($courseId, $lessonId, $uid){
        if(!$courseId || !$lessonId || !$lessonId ) return false;
        $playReportKey = $this->getPlayReportKey($courseId, $lessonId, $uid);
        $report = $this->redisConn->hGetAll($playReportKey);
        return intval($report['lastPlayPos']);
    }

   
}
