<?php

namespace Common\Model\Cms;

use Common\Model\Common\BaseModel;

class CmsListModel extends BaseModel {

    protected $tableName = 'cms_list';

    public function IndexCmslist() {
        return $this->select();
    }
    
    public function getDataConsulting($id){
        return $this->where("id = $id")->find();
    }

        public function getCms($id) {
        return $this->where("id={$id}")->find();
    }
    public function Cmsdel($id) {
        return $this->where("id={$id}")->setField('agstatus', '1');
    }
    public function CmsStatus($id) {
        return $this->where("id={$id}")->setField('status', '1');
    }
    public function CmsStatusUpdate($id) {
        return $this->where("id={$id}")->setField('status', '0');
    }
     public function CmsagStatus($id) {
        return $this->where("id={$id}")->setField('agstatus', '0');
    }
    public function searchCmsCount($conditions) {
          return $this->where($conditions)->count();
    }
    public function searchCmsList($conditions, $orderBy, $start, $limit) {
        return $this->where($conditions)->order("$orderBy[0] $orderBy[1]")->limit($start, $limit)->select();
    }
    public function findCmsList($arr) {
        return $this->add($arr);
    }
    public function CodeCmsList($arr,$id) {
        return $this->where("id={$id}")->save($arr);
    }
    public function consultCmsList($arr,$id) {
        return $this->where("id={$id}")->save($arr);
    }
    public function getCmsList($id){
        return $this->where("id={$id}")->field('statisticalCode')->find();
    }
    public function getCmsListcode($id){
        return $this->where("id={$id}")->field('consultCode')->find();
    }
    public function updateCms($id, $arr) {
        return $this->where("id={$id}")->save($arr);
    }
 
}