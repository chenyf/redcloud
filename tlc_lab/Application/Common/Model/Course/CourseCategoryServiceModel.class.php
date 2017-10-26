<?php
namespace Common\Model\Course;
use Common\Lib\ArrayToolkit;
use Common\Model\Common\BaseModel;
use Common\Traits\ServiceTrait;

class CourseCategoryServiceModel extends BaseModel
{

    use ServiceTrait;

    private static $fieldMaps = array(
        'name'  =>  '分类名称',
        'code'  =>  '分类代码',
        'courseCode'    =>  '分类课程前缀',
    );

    private function getCategoryDao(){
        return $this->createDao("Course.CourseCategory");
    }

    private function getCourseDao(){
        return $this->createDao("Course.Course");
    }

    private function getCourseService(){
        return createService("Course.CourseService");
    }

    public function selectCategory(){
        return $this->getCategoryDao()->selectCategory();
    }

    public function getCategory($id){
        return $this->getCategoryDao()->getCategory($id);
    }
    
    public function getCategoryByCondition($condition){
        return $this->getCategoryDao()->getCategoryByCondition($condition);
    }

    public function updateCategory($id,$data){
        $this->getCategoryDao()->updateCategory($id,$data);
        return true;
    }

    public function addCategory($data){
        return $this->getCategoryDao()->addCategory($data);
    }

    public function removeCategory($id){
        $courseIds = ArrayToolkit::column($this->getCourseService()->selectCoursesByCategory($id),'id');
        if($this->resetCourseCategory($courseIds)) {
            return $this->getCategoryDao()->deleteCategory($id);
        }
        return false;
    }

    public function resetCourseCategory($courseIds){
        if(!empty($courseIds)){
            $this->getCourseDao()->getConnection()->beginTransaction();
            try{
                foreach ($courseIds as $course_id){
                    $course_id = intval($course_id);
                    $this->getCourseDao()->updateCourseFind($course_id,array('categoryId' => 0));
                }
                $this->getCourseDao()->getConnection()->commit();
                return true;
            }catch(\Exception $e){
                $this->getCourseDao()->getConnection()->rollback();
                return false;
            }
        }
        return true;
    }

    public function checkDuplicate($id,$fields){
        $fields = ArrayToolkit::parts($fields,array_keys(self::$fieldMaps));
        foreach ($fields as $key=>$value){
            $cate = $this->getCategoryByCondition(array($key=>$value));
            if(!empty($cate) && $cate['id'] != $id){
                return self::$fieldMaps[$key] . "不能重复";
            }
        }

        return null;
    }

}