<?php

namespace Common\Model\Timetable;

use Common\Model\Common\BaseModel;
use Think\Cache;

class ClassSyllabusServiceModel extends BaseModel {

    protected $currentDb = CENTER;
    private $_memSyllabusExpire = 2592000; #缓存时间1月

    private function getSyllabusDao() {
        return $this->createDao("Timetable.ClassSyllabus");
    }

    private function getSyllabusDetailsDao() {
        return $this->createDao("Timetable.ClassSyllabusDetails");
    }

    /**
     * 添加课程
     * @author tanhaitao 2016-03-08
     */
    public function addSyllabus($data, $syllabusInfo) {
        //事务处理
        $this->startTrans();
        $syllabus = $this->getSyllabusDao()->createSyllabus($data);
        $data = $this->getSyllabusDetailArr( $syllabus , $syllabusInfo);
        $r = $this -> addAllSyllabusDetails($data);
        if( $r ){
                $this->commit();
            }else{
                $this->rollback();
            }
        return $r ? true : false;
    }
    
    
    /**
     * 编辑课程
     * @author tanhaitao 2016-1-18
     */
    public function updateSyllabus($data, $syllabusInfo) {
        //事务处理
        $this->startTrans();
        $flag = true;
        $syllabus = $this->judgeDelSyllabus($data) ;
        if(empty($syllabus)) $flag = false ;
        $data = $this->getSyllabusDetailArr( $syllabus , $syllabusInfo);
        $r = $this -> addAllSyllabusDetails($data);
        if( $r && $flag ){
                $this->commit();
            }else{
                $this->rollback();
            }
        
        return $r ? true : false;
    }

    
    /**
     * 判断是否删除原有课程
     * @author tanhaitao 2016-03-08
     */
    public function judgeDelSyllabus($data) {
        $syllabus = $this -> getSyllabusInfo($data['id']) ;
        if(empty($syllabus))  return array();
        $oldStartTm = $syllabus['startCourseTm'];
        $oldEndTm = $syllabus['endCourseTm'];
        $nowStartTm = $data['startCourseTm'];
        $nowEndTm = $data['endCourseTm'];
        $arr['syllabusId'] = $data['id'] ;
        if( $oldStartTm < $nowStartTm  || $nowEndTm < $oldEndTm ){
                $arr['startTm'] = $nowStartTm ;
                $arr['endTm'] = $nowEndTm ;
                $syllabus = $this->getSyllabusDao()->createSyllabus($data);
        }else{
            $syllabus = $this->getSyllabusDao()->saveSyllabus($data);
        }
        $r = $this->delAllSyllabusDetails($arr);
        #如果课程详情列表被删光，删除课程
        $_hasSyllabus = $this->getSyllabusArr($data['id']);
        if(empty($_hasSyllabus)){
            $this -> delSyllabus($data['id']);
        }
        
        return !empty($syllabus) ? $syllabus : array();
    }
    
    /**
     * 创建课程
     * @author tanhaitao 2016-03-08
     */
    public function createSyllabus($data) {
        return $this->getSyllabusDao()->createSyllabus($data);
    }
    
    /**
     * 删除课程
     * @author tanhaitao 2016-03-17
     */
    public function delSyllabus($id) {
        return $this->getSyllabusDao()->delSyllabus($id);
    }
    
    /**
     * 修改课程
     * @author tanhaitao 2016-03-08
     */
    public function saveSyllabus($data) {
        return $this->getSyllabusDao()->saveSyllabus($data);
    }
    
    /**
     * 获得课程信息
     * @author tanhaitao 2016-03-09
     */
    public function getSyllabusInfo($id) {
        return $this->getSyllabusDao()->getSyllabus($id);
    }
    
     /**
     * 获得一段时间内课程详情
     * @author tanhaitao 2016-03-14
     */
    public function getAllSyllabusList($param) {
//        $cacheMem = Cache::getInstance('Memcache');
//        $listKey = 'syllabus_index_' . $param['classId'] . '_' .$param['startTm']. '_' . $param['endTm'];
//        $data = $cacheMem->get($listKey);
//        if(!$data){
           $data = $this->getSyllabusDetailsDao()->getAllSyllabusList($param);
//           $cacheMem->set($listKey, $data , $this->_memSyllabusExpire); 
//        }
        return !empty($data) ? $data : array();
    }
    
    /**
     * 获得某个课程详情
     * @author tanhaitao 2016-03-14
     */
    public function getcCourseDetails($lessonId) {
        return $this->getSyllabusDetailsDao()->getcCourseDetails($lessonId);
    }
    
    
    /**
     * 获得相同上课时间的课程详情
     * @author 谈海涛 2016-03-15
     */
    public function getCourseDetailList($param) {
        return $this->getSyllabusDetailsDao()->getCourseDetailList($param);
    }
    
    
    /**
     * 根据班级课程id获得当前周的所有课时
     * @author 谈海涛 2016-03-15
     */
    public function getLessonList($param) {
        return $this->getSyllabusDetailsDao()->getLessonList($param);
    }
    
    /**
     * 获取课程列表
     * @author Pengjialin 2016-03-08
     */
    public function getSyllabusArr($id) {
        return $this->getSyllabusDetailsDao()->getSyllabusArr($id);
    }
    
    /**
     * 获得课时信息
     * @author 谈海涛 2016-03-15
     */
    public function getSyllabusDetails($id) {
        return $this->getSyllabusDetailsDao()->getSyllabusDetails($id);
    }
    /**
     * 添加课程详情
     * @author tanhaitao 2016-03-08
     */
    public function addAllSyllabusDetails($data) {
        return $this->getSyllabusDetailsDao()->addAllSyllabusDetails($data);
    }
    
    /**
     * 添加课程详情
     * @author tanhaitao 2016-03-08
     */
    public function delAllSyllabusDetails($data) {
        return $this->getSyllabusDetailsDao()->delAllSyllabusDetails($data);
    }
    
    /**
     * 删除课时信息
     * @author tanhaitao 2016-03-15
     */
    public function delWeekCourseDetails($data) {
        $r = $this->getSyllabusDetailsDao()->delWeekCourseDetails($data);
        #如果课程详情列表被删光，删除课程
        $_hasSyllabus = $this->getSyllabusArr($data['syllabusId']);
        if(empty($_hasSyllabus)){
            $this -> delSyllabus($data['syllabusId']);
        }
        return $r ;
    }

    
    
    function getSyllabusDetailArr($syllabus, $syllabusInfo){
        if(empty($syllabus) || empty($syllabusInfo)) return array();
        $allDayTm = $this->get_day_time($syllabus['startCourseTm'], $syllabus['endCourseTm']);
        $space = intval($syllabus['courseCircle']) + 1;
        
        $syllArr = array();
        if($syllabus['courseCircle'] != 0 && $syllabus['courseCircle'] != 5 ){
            foreach ($allDayTm as $key => $value) {
               if( $key%$space == 0 ){
                $syllArr[] =  $value;
               }
            }
        }else{
            $syllArr = $allDayTm ;
        }
       
        $data = array();
        foreach ($syllabusInfo as $k => $v) {
            $details = array();
            $details['classId'] = $syllabus['classId'];
            $details['syllabusId'] = $syllabus['id'];
            $details['courseColor'] = $syllabus['courseColor'];
            $details['lessonCircle'] = $v['lessonCircle'];
            $details['lessonTime'] = $v['lessonTime'];
            $details['timeLength'] = $v['timeLength'];
            $details['lessonPlace'] = $v['lessonPlace'];
            $lessonDayArr = array();
            $lessonDayArr = $this->getlessonDayArr($syllArr, $v['lessonCircle'] ) ;
            foreach ($lessonDayArr as $key => $val) {
                $details['startLessonTm'] = $val['startTime'] + $details['lessonTime'];
                $details['endLessonTm'] = $details['startLessonTm'] + $details['timeLength'];
                $details['weekday'] = $val['weekday'] ;
                $details['ctm'] = getMicroTm() ;
                $data[] = $details ;
            }
        }
        return $data ;
    }
    
    //根据每周上课频率获得上课的开始时间
    function getlessonDayArr($syllArr, $lessonCircle){
        if(empty($syllArr)  ) return array();
        $lesCirArr = $this->getLessonCircleArr($lessonCircle);
        
        $lessonDayArr = array();
        foreach ($syllArr as $key => $value) {
            foreach ($value as $k => $val) {
                $cirArr = array();
                if(in_array($k ,$lesCirArr)){
                    $cirArr['startTime'] = $val ;
                    $cirArr['weekday'] = $k ;
                    $lessonDayArr[] = $cirArr ;
                }
            }
        }
        
        return $lessonDayArr ;
        
    }
    
    //0=>工作日 1=>周一 2=>周二 3=>周三 4=>周四 5=>周五 6=>周六   7=>周日 8 => 每天
    function getLessonCircleArr($lessonCircle){
        $arr = array();
        if(!isset($lessonCircle)) return $arr ;
	switch ($lessonCircle) {
                case 0 :
                        $arr = array( 1 , 2 , 3 , 4 , 5);
			break;
                case 1 :
                        $arr = array( 1 );
			break;
		case 2 :
                        $arr = array( 2 );
			break;
		case 3 :
			$arr = array( 3 );
			break;
                case 4 :
                        $arr = array( 4 );
			break;
                case 5 :
                        $arr = array( 5 );
			break;
		case 6 :
                        $arr = array( 6 );
			break;
                case 7 :
                        $arr = array( 7 );
			break;
                case 8 :
                        $arr = array( 1 , 2 , 3 , 4 , 5 , 6 , 7 );
			break;
	}
        return $arr ;
    }
    
    /**
     * 获得一段时间内每天的开始时间戳
     * @author 谈海涛 2016-03-08
     */
    function get_day_time($start, $end) {
        $arr = $this->get_week_time($start, $end);
        $allDay = array();
        foreach ( $arr as $k => $v) {
           $days =  ($v[1] - $v[0] + 1 ) / 86400 ;
           $week = array() ;
           for ($x=0; $x < $days; $x++) {
                $time =  $v[0] + $x*86400 ;
                $key = date('w',$time);
                if($key == 0 ) $key = 7 ;
                $week[$key] = $time ;
              } 
           $allDay[] = $week ;
        }
        return $allDay;
    }

    /**
     * 获得一段时间内每周的起始时间戳
     * @author 谈海涛 2016-03-08
     */
    function get_week_time($start, $end) {
        $arr = array();
//        $start = strtotime($s . " 00:00:00");
//        $end = strtotime($e . " 23:59:59");
        $s_w = date('w', $start);
        $f_w = 8 - $s_w;
        if ($f_w) {
            $f_end = $start + 86400 * $f_w - 1;
        } else {
            $f_end = $start + 86400 - 1;
        }
        $new_end = $f_end;
        if ($end <= $new_end) {
            $arr[] = array($start, $end);
            return $arr;
        }
        while ($end > $new_end) {
            $arr[] = array($start, $new_end);
            $start = $new_end + 1;
            $new_end = $new_end + 7 * 86400;
        }
        $arr[] = array($new_end - 7 * 86400 + 1, $end);
        return $arr;
    }

    /**
     * 获得某天的一周开始时间和结束时间
     * @author 彭家宁 2016-03-11
     */
    function get_day_week($time=''){
        $time = $time ? $time : time();
        $time_week = date('w',$time);
        $time_week = $time_week == 0 ? 7 : $time_week;
        $timer = array(
            'start_time' => strtotime(date('Y-m-d',$time)) - ($time_week-1)*86400,
            'end_time' => strtotime(date('Y-m-d',$time)) + (8-$time_week)*86400 -1,
        );
        return $timer;
    }

    /**
     * 处理课程列表数据
     */
    function getSyllabusFormatData($info){
        foreach($info as $k => $v){
            $info[$k]['lessonCircle'] = array('id'=>$v['weekday'],'text'=>$this->getDayName($v['weekday']));
            $info[$k]['lessonTime'] = sprintf('%02d',floor($v['lessonTime']/3600)) . '：' . sprintf('%02d',floor(intval($v['lessonTime'])%3600/60));
            $info[$k]['timeLength'] = floor($v['timeLength']/60);
        }
        return $info;
    }

    /**
     * 获得周期名
     */
    function getDayName($id){
        $name_arr = array(
            0 => '工作日',
            1 => '周一',
            2 => '周二',
            3 => '周三',
            4 => '周四',
            5 => '周五',
            6 => '周六',
            7 => '周日',
            8 => '每天',
        );
        return $name_arr[$id];
    }
    
   /*
    * 获取指定日期所在星期的开始时间与结束时间的时间戳
    * param mx $date 参数形式如：2016-01-15 或者 1358179200 也可以为空
    * return array('begintime','endtime')
    */
    function getWeekTime($date=''){
            $timestamp=empty($date)?strtotime('now'):(is_numeric($date)?$date:strtotime($date));
            $w=strftime('%w',$timestamp);
            $date=array();
            $begintime = date('Y-m-d 00:00:00',$timestamp-($w-1)*86400);
            $date['begintime'] = strtotime($begintime);
            $endtime = date('Y-m-d 23:59:59',$timestamp+(7-$w)*86400);
            $date['endtime'] = strtotime($endtime);
            return $date;
    }
    
    /**
     * 获得周期名
     */
    function getWeekDayName(){
        $name_arr = array(
            1 => '周一',
            2 => '周二',
            3 => '周三',
            4 => '周四',
            5 => '周五',
            6 => '周六',
            7 => '周日',
        );
        return $name_arr;
    }
}

