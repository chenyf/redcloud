<?php
namespace Common\Model\Course;
use Common\Model\Common\BaseModel;

class CourseResourceModel extends BaseModel{
    
    protected $tableName = 'resource';

    public function findResource($id){
        return $this->where("id = {$id}")->find() ?: null;
    }

    public function selectResourceByCourseId($courseId){
        return $this->where(array('courseId'=>$courseId))->select() ?: array();
    }
    
    public function addResource($paramArr){
        return $this->add($paramArr);
    } 
    
    public function saveResource($paramArr,$arr){
        return $this->where($paramArr)->save($arr);
    }
    
    private function filterWhere($paramArr){
        $where = " 1=1 ";
        if( isset($paramArr['courseId']) && !empty($paramArr['courseId']) ){
            $where.= " and courseId = {$paramArr['courseId']}";
        }
        if( isset($paramArr['power']) && !empty($paramArr['power']) ){
            $power = implode(",", $paramArr['power']);
            $where.= " and power in ({$power})";
        }
        if( isset($paramArr['ids']) && !empty($paramArr['ids']) ){
            $ids = implode(",", $paramArr['ids']);
            $where.=" or id in ({$ids})";
        }
        return $where;
    }
    
    public function courseResourceCount($paramArr){
        $where = $this->filterWhere($paramArr);
        return $this->where($where)->count() ? : 0;
    }
    
    public function courseResourceList($paramArr){
        $where = $this->filterWhere($paramArr);
        $start = $paramArr["start"] ? : 0;
        $limit = $paramArr["limit"] ? : 10;
        return $this->where($where)->order("updateTm desc")->limit($start,$limit)->select() ? : array();
    }
    
    public function courseResource($paramArr){
        return $this->where($paramArr)->find() ? : array();
    }
    
    public function deleteResource($paramArr){
        return $this->where($paramArr)->delete();
    }
    
    public function updateResourceDownloadNum($paramArr){
        return $this->where($paramArr)->setInc("downloadNum");
    }
    
    public function getResourceDownloadNum($paramArr){
        return $this->where($paramArr)->getField("downloadNum");
    }
}
?>
