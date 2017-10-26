<?php
/*
 * 数据层
 * @package
 * @version    $Id$
 */
namespace  Common\Model\Course;

use Common\Model\Common\BaseModel;

class CourseCategoryRelServiceModel extends BaseModel
{

	    protected $tableName = 'course_category_rel';
        public function getCourseIdsByCid($cid = 0){
            
            $courseIds = array();
            $data = $this->getCourseCategoryRelDao()->getCourseIdsByCid($cid);
            
            if ($data) {
                foreach ($data as $v)
                    $courseIds[] = $v['courseId'];
            }
            
            return $courseIds;
        }
        

        public function getCourseCategoryRelDao(){
            
            return $this->createDao('Course.CourseCategoryRel');  
            
        }

	

}