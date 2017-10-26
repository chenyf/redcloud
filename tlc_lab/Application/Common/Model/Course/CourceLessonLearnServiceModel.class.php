<?php

namespace Common\Model\Course;

use Common\Model\Common\BaseModel;

//@author Czq 2016-03-18
class CourceLessonLearnServiceModel extends BaseModel {
    protected $tableName = 'course_lesson_learn';
    
    //检测用户是否兑换了这个课时
    public function checkUserGoldBuyThisLesson($userId, $courseId, $lessonId){
        $whereList = array(
            "userId"    =>  $userId,
            "courseId"  =>  $courseId,
            "lessonId"  =>  $lessonId,
            "goldBuyTm"  =>  array('gt',0),
            "goldNum"  =>  array('gt',0),
        );
        $resource = $this->where($whereList)->find();
        return $resource;
    }
    
    //获取用户兑换课程的课时
    public function getLessonGoldBuy($userId, $courseId){
        $whereList = array(
            "cll.userId"    =>  $userId,
            "cll.courseId"  =>  $courseId,
            "cll.goldBuyTm"  =>  array('gt',0),
            "cll.goldNum"  =>  array('gt',0),
        );
        $resource = $this->table('course_lesson_learn cll')
                ->field('cll.lessonId,cl.id,cl.courseId,cl.chapterId,cl.number,cl.free,cl.status,cl.title,cl.type,cl.mediaId,cl.mediaSource,cl.mediaName,cl.mediaUri,cl.polyvVid,cl.polyvVideoSize')
                ->join('right join course_lesson cl on cll.lessonId = cl.id')
                ->where($whereList)
                ->order('cll.goldBuyTm desc')->select();
        return !empty($resource)?$resource:"";
    }
    
    //获取用户兑换课程的课时
    public function getLessonGoldBuyByCouseList($userId, $courseList){
        $whereList = array(
            "cll.userId"    =>  $userId,
            "cll.courseId"  =>  array('IN',$courseList),
            "cll.goldBuyTm"  =>  array('gt',0),
            "cll.goldNum"  =>  array('gt',0),
        );
        $resource = $this->table('course_lesson_learn cll')
                ->field('cll.lessonId,cl.id,cl.courseId,cl.chapterId,cl.number,cl.free,cl.status,cl.title,cl.type,cl.mediaId,cl.mediaSource,cl.mediaName,cl.mediaUri,cl.polyvVid,cl.polyvVideoSize,cl.content')
                ->join('right join course_lesson cl on cll.lessonId = cl.id')
                ->where($whereList)
                ->order('cll.goldBuyTm desc')->select();
        return !empty($resource)?$resource:"";
    }
    
    //设置或者覆盖 课时列表 按钮
    public function getLessonState($lessonId,$supportGoldBuy,$free,$lessonGoldBuyArray,$userIsAdd=false){
        if($userIsAdd || in_array($lessonId, $lessonGoldBuyArray)){
            return 'goto';
        }
        if($supportGoldBuy){
            return 'glodBuy';
        }
        if($free){
            return 'free';
        }
        return false;
    }
    
    //获取用户兑换课程的数量
    public function getCourceGoldBuyCount($userId) {
        $whereList = array(
            "userId"    =>  $userId,
            "goldBuyTm"  =>  array('gt',0),
            "goldNum"  =>  array('gt',0),
        );
        $resource = $this->table('course_lesson_learn')
                         ->field('COUNT(DISTINCT courseId) as count')
                         ->where($whereList)
                         ->find();
        return $resource['count'];
    }
    
    /*
     * 课程是否可购买数据
     * @param $lessonInfo array('free','supportGoldBuy','needGoldNum')
     * @return bool
     * 
     */
    public function isLessonGoldBuy($lessonInfo){
        return false;
    }
}

