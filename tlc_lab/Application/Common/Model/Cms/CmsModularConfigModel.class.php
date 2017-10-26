<?php
namespace Common\Model\Cms;

use Common\Model\Common\BaseModel;

class CmsModularConfigModel extends BaseModel {
        protected $tableName = 'cms_modular_config';
        
        public function addAttribute($data){
         return    $this->addAll($data);
        }
        
        public function deleteModularConfig($id){
            return $this->where("cmsModId=$id")->delete();
        }
        
        public function getPicPath($cmsId){
            return $this->where("cmsModid = $cmsId")->select();
        }
        
        
        public function getConfig($arrId){
            return $this->where("cmsModid in ($arrId)")->order("sort asc")->select();
        }
        
        public function getList($id){
            return $this->where("cmsModId = $id")->order("sort asc")->select();
        }
        
        public function updateNavSort($sort,$id){
            $this->where("anchorId=$id")->save($sort);
        }
         
        

        public function delModularById($id){
            $this->where("anchorId = $id")->delete();
        }
        
        
        public function delImg($id){
          return   $this->where("id=$id")->delete();
        }
        
        
        public function selpicPath($id){
          return   $this->where("id = $id")->find();
        }
        
        
        public function updateImgSort($data,$id){
        
           return   $this->where("id=$id")->save($data);
          
        }
        
        
        
        
        
}
?>
