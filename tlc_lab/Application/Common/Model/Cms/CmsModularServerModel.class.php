<?php
namespace Common\Model\Cms;;
use Common\Model\Common\BaseModel;
/**
 * 
 * @author linhaowei <lin30426@163.com>
 * Createtime 2016/01/06
 * 
 */
class CmsModularServerModel extends BaseModel
{
    
    
    public function insertModular($data){
        $this->getModular()->insertModular($data);
    }
    
    public function getConfig($cmsIdArr){
        return  $this->insertConfig()->getConfig($cmsIdArr);
    }

    

    public function getFindModular($id){
        return $this->getModular()->getFindModular($id);
        
    }
    
    
    public function getFindImg($id){
        return $this->getModular()->getFindImg($id);
        
    }
    public function getFindShuffling($id){
        return $this->getModular()->getFindShuffling($id);
        
    }
    public function getFindNav($id){
        return $this->getModular()->getFindNav($id);
        
    }
   
    
     public function getModularData($id){
        return $this->getModular()->getFindModularData($id);
        
    }
    
    public function getSingleData($id){
         return $this->getModular()->getSingleData($id);
    }
    
    public function addAttribute($data){
      return  $this->insertConfig()->addAttribute($data);
    }
    
    
    public function getPicPath($cmsId){
        return $this->insertConfig()->getPicPath($cmsId);
    }
    
    public function updateCmsModular($data,$id){
        $this->getModular()->updateCmsModular($data,$id);
    }
    
    public function getTitle($cmsId){
       return  $this->getModular()->getTitle($cmsId);
    }
    
    public function deleteModularConfig($id){
        return $this->insertConfig()->deleteModularConfig($id);
    }
    
    public function delModularById($id){
        return $this->insertConfig()->delModularById($id);
    }

        public function deleteModular($id){
        return $this->getModular()->deleteModular($id);
    }
    
    public function updateSort($sort,$id){
        
        $this->getModular()->updateSort($sort,$id);
    }
    
    public function updateNavSort($sort,$id){
        
        $this->insertConfig()->updateNavSort($sort,$id);
    }

    

    public function getList($id){
       return  $this->insertConfig()->getList($id);
    }
    
    public function delImg($id){
        return $this->insertConfig()->delImg($id);
    }
    
    
    public function selpicPath($id){
        return $this->insertConfig()->selpicPath($id);
    }
    
    
    public function updateImgSort($data,$id){
    
        return $this->insertConfig()->updateImgSort($data,$id);
    }

    

















    private function getModular(){
        return $this->createService('Cms.CmsModularModel');
    }
    
    private function insertConfig(){
        return $this->createService("Cms.CmsModularConfigModel");
    }
    
    
    
}
?>
