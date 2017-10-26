<?php

namespace Common\Model\Course;


use Common\Model\Common\BaseModel;
use Common\Traits\ServiceTrait;
class CourseCooperationServiceModel  extends BaseModel
{

    use ServiceTrait;

    public function findAllByCourseId($courseId){
        $list = $this->getCourseTeacherListByCourseId($courseId);
        return $this->convertBulk($list);
    }

    private function getCourseCooperationDao(){
        return $this->createDao("Course.CourseCooperation");
    }

    public function getCourseTeacherListByTeacherId($teacherId)
    {
        return $this->getCourseCooperationDao()->getCourseTeacherListByTeacherId($teacherId);
    }

    public function getCourseTeacherListByCourseId($courseId)
    {
        return $this->getCourseCooperationDao()->getCourseTeacherListByCourseId($courseId);
    }

    public function deleteByTeacherId($teacherId)
    {
        return $this->getCourseCooperationDao()->deleteByTeacherId($teacherId);
    }

    public function deleteByCourseId($courseId)
    {
        return $this->getCourseCooperationDao()->deleteByCourseId($courseId);
    }

    public function deleteByTeacherAndCourse($teacherId,$courseId){
        return $this->getCourseCooperationDao()->deleteByTeacherAndCourse($teacherId,$courseId);
    }


    public function convertBulk($ccs){
        foreach ($ccs as $key => $cc){
            $ccs[$key] = $this->convert($cc);
        }
        return $ccs;
    }

    public function convert($cc){
        $teacher = $this->getUserService()->getUser($cc['teacherId']);
        $cc['teacherName'] = $teacher['nickname'];
        $cc['teacherNum'] = $teacher['userNum'];
        $lessons = $this->getCourseService()->findCourseLessonList(array('userId' => $cc['teacherId']));
        $cc['teacherLessonNum'] = count($lessons);

        return $cc;
    }

    private function getUserService(){
        return createService("User.UserService");
    }

    private function getCourseService(){
        return createService("Course.CourseService");
    }

}