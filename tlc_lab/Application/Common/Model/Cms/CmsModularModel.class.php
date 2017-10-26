<?php
namespace Common\Model\Cms;

use Common\Model\Common\BaseModel;

class CmsModularModel extends BaseModel {
        protected $tableName = 'cms_modular';
        
    public function getFindModularData($id){
        return $this->where("cmsId=$id")->order('sort ')->select();
    }
    
    
    public function getFindModular($id){
        return $this->where("cmsType=3 and cmsId=$id")->find();
    }
    
    
    public function getFindShuffling($id){
        return $this->where("cmsType=1 and cmsId=$id")->find();
    }
    public function getFindImg($id){
        return $this->where("cmsType=2 and cmsId=$id")->find();
    }
    public function getFindNav($id){
        return $this->where("cmsType=0 and cmsId=$id")->find();
    }
    
    
    
    
    
    
    
    public function insertModular($data){
        $this->add($data);
    }
    
    public function getSingleData($id){
       return  $this->where("id =$id ")->find();
    }
    
    
    public function updateCmsModular($data,$id){
        $this->where("id=$id")->save($data);
    }
    
    
    public function getTitle($cmsId){
       return  $this->field("id,title,sort")->where("cmsId=$cmsId")->select();
    }
    
    
    public function deleteModular($id){
        return $this->where("id = $id")->delete();
    }
    
    
    public function updateSort($sort,$id){
        $this->where("id in($id)")->save($sort);
    }
    
    
    
    
}
?>
