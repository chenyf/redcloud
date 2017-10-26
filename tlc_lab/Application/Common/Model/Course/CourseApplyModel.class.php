<?php
namespace Common\Model\Course;

use Common\Model\Common\BaseModel;
use Common\Traits\DaoModelTrait;

class CourseApplyModel extends BaseModel {
     
    public function indexDate(){
        
        $arr = $this->select();
        return $arr;
    }
    
    
    
}


?>
