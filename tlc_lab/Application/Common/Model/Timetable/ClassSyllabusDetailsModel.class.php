<?php

namespace Common\Model\Timetable;

use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class ClassSyllabusDetailsModel extends BaseModel {

    protected $tableName = 'class_syllabus_details';

    public function getSyllabusDetails($id) {
        $data['id'] = $id;
        return $this->where($data)->find() ? : array();
    }

    /**
     * 获取课程id的课程列表
     * @author Pengjialin 2016-03-14
     */
    public function getSyllabusArr($id) {
        $data['syllabusId'] = $id;
        return $this->where($data)->select() ? : array();
    }
    /**
     * 添加课程详情
     * @author 谈海涛 2016-03-08
     */
    public function addAllSyllabusDetails($param) {
        
        $r = $this->addAll($param);  
        
        return $r ? true : false;
    }
    
    /**
     * 获得课程详情
     * @author 谈海涛 2016-03-14
     */
    public function getAllSyllabusList($param) {
        $where['a.startLessonTm'] = array('egt',$param['startTm']);
        $where['_logic'] = 'and';
        $where['a.endLessonTm'] = array('elt',$param['endTm']); 
        $where['a.classId'] = $param['classId'] ;
        $r = $this->table('class_syllabus_details a')->join('class_syllabus  b on a.syllabusId = b.id')->where($where)->order('a.timeLength desc')->field('a.* , b.courseName, b.startCourseTm, b.endCourseTm, b.courseCircle,b.remind')->select();
        return  $r ? $r : array();
    }
    
     /**
     * 获得某个课程详情
     * @author 谈海涛 2016-03-14
     */
    public function getcCourseDetails($lessonId) {
        $where['a.id'] = $lessonId ;
        $r = $this->table('class_syllabus_details a')->join('class_syllabus  b on a.syllabusId = b.id')->where($where)->order('a.timeLength desc')->field('a.* , b.courseName, b.startCourseTm, b.endCourseTm, b.courseCircle,b.remind')->find();
        return  $r ? $r : array();
    }
    
    /**
     * 获得相同上课时间的课程详情
     * @author 谈海涛 2016-03-15
     */
    public function getCourseDetailList($param) {
        $where['a.startLessonTm']   = $param['startLessonTm'];
        $where['a.endLessonTm']     = $param['endLessonTm']; 
        $where['a.classId']         = $param['classId']; 
        $r = $this->table('class_syllabus_details a')->join('class_syllabus  b on a.syllabusId = b.id')->where($where)->order('a.timeLength desc')->field('a.* , b.courseName, b.startCourseTm, b.endCourseTm, b.courseCircle ,b.remind')->group('a.syllabusId')->select();
        return  $r ? $r : array();
    }
    
    
    /**
     * 根据班级课程id获得当前周的所有课时
     * @author 谈海涛 2016-03-15
     */
    public function getLessonList($param) {
        $where['startLessonTm'] = array('egt',$param['startTm']);
        $where['_logic'] = 'and';
        $where['endLessonTm'] = array('elt',$param['endTm']); 
        $where['syllabusId'] = $param['syllabusId'];
        $r = $this->where($where)->select();
        return  $r ? $r : array();
    }
    
    /**
     * 删除课程详情
     * @author 谈海涛 2016-03-09
     */
    public function delAllSyllabusDetails($param) {
        if($param['startTm'] && $param['endTm'] ){
            $where['startLessonTm'] = array('egt',$param['startTm']);
            $where['endLessonTm'] = array('elt',$param['endTm']); 
        }
        $where['syllabusId'] = $param['syllabusId'] ; 
        
        $r = $this->where($where)->delete();  
        return $r ? true : false;
    }
    
    /**
     * 删除当前周本课程
     * @author 谈海涛 2016-03-09
     */
    public function delWeekCourseDetails($param) {
        $where['startLessonTm'] = array('egt',$param['startTm']);
        $where['_logic'] = 'and';
        $where['endLessonTm'] = array('elt',$param['endTm']); 
        $where['syllabusId'] = $param['syllabusId'];
        $where['classId'] = $param['classId'] ;
        $r = $this->where($where)->delete();  
        return $r ? true : false;
    }


}

?>
