<?php

namespace Common\Model\Timetable;

use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class ClassSyllabusModel extends BaseModel {

    protected $tableName = 'class_syllabus';

    public function getSyllabus($id) {
        $data['id'] = $id;
        return $this->where($data)->find() ? : array();
    }

    /**
     * 添加课程
     * @author 谈海涛 2016-03-08
     */
    public function createSyllabus($param) {
        $fields = ArrayToolkit::filter($param, array(
                    'classId' => 0,
                    'courseName' => '',
                    'startCourseTm' => 0,
                    'endCourseTm' => 0,
                    'addUid' => 0,
                    'courseCircle' => 0,
                    'remind' => 0,
                    'courseColor' => 0,
        ));

        $fields['ctm'] = time();
        $r = $this->add($fields);
        return $this->getSyllabus($r) ? : array();
    }
    
     /**
     * 添加课程
     * @author 谈海涛 2016-03-08
     */
    public function saveSyllabus($param) {
        $fields = ArrayToolkit::filter($param, array(
                    'classId' => 0,
                    'courseName' => '',
                    'startCourseTm' => 0,
                    'endCourseTm' => 0,
                    'editUid' => 0,
                    'courseCircle' => 0,
                    'remind' => 0,
                    'courseColor' => 0,
        ));

        $fields['etm'] = time();
        $where['id'] = $param['id'] ;
        $r = $this->where($where)->save($fields);
        return $this->getSyllabus($param['id']) ? : array();
    }

    /**
     * 更新课程
     * @author 谈海涛 2016-03-08
     */
    public function updateSyllabus($id, $param) {
        $where['id'] = $id;
        $fields = ArrayToolkit::filter($param, array(
                    'classId' => 0,
                    'courseName' => '',
                    'startCourseTm' => 0,
                    'endCourseTm' => 0,
                    'editUid' => 0,
                    'courseCircle' => 0,
                    'courseColor' => 0,
        ));

        $fields['etm'] = time();
        $r = $this->where($where)->save($fields);
        return $r ? true : false;
    }
    public function delSyllabus($id) {
        $data['id'] = $id;
        $r =  $this->where($data)->find();
        return $r ?  true : false ;
    }

}

?>
