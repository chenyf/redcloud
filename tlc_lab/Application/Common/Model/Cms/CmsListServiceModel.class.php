<?php

namespace Common\Model\Cms;

use Common\Model\Common\BaseModel;
use Common\Traits\ServiceTrait;

class CmsListServiceModel extends BaseModel {

    public function getData($arr) {
        return $this->getCmsListDao()->findCmsList($arr);
    }

    public function getUpdataCms($id, $arr) {
        return $this->getCmsListDao()->updateCms($id, $arr);
    }

    public function getCodeCms($arr,$id) {
        return $this->getCmsListDao()->CodeCmsList($arr,$id);
    }

    public function consultCms($arr,$id) {
        return $this->getCmsListDao()->consultCmsList($arr,$id);
    }
    public function getCmsList($id){ 
        return $this->getCmsListDao()->getCmsList($id);
    }
    public function getCmsListcode($id){
        return $this->getCmsListDao()->getCmsListcode($id);
    }
    public function getCms($id) {
        return $this->getCmsListDao()->getCms($id);
    }

    public function searchCmsList($conditions, $orderBy, $start, $limit) {
        $conditions = $this->SearchConditions($conditions);
        return $this->getCmsListDao()->searchCmsList($conditions, $orderBy, $start, $limit);
    }

    public function searchCmsListCount($conditions) {
        $conditions = $this->SearchConditions($conditions);
        return $this->getCmsListDao()->searchCmsCount($conditions);
    }

    public function getdelCms($id) {
        return $this->getCmsListDao()->Cmsdel($id);
    }
    public function getStatusCms($id) {
        return $this->getCmsListDao()->CmsStatus($id);
    }
    public function getStatusUpdateCms($id) {
        return $this->getCmsListDao()->CmsStatusUpdate($id);
    }
    public function getagStatusCms($id) {
        return $this->getCmsListDao()->CmsagStatus($id);
    }
    
    public function getDataConsulting($id){
        return $this->getCmsListDao()->getDataConsulting($id);
    }

    private function getCmsListDao() {
        return createService('Cms.CmsListModel');
    }
    
    private function SearchConditions($conditions) {
       if ($conditions['module'] != '') {
            $data['title'] = array('like', "%{$conditions['module']}%");
        } 
            $data['agstatus'] = $conditions['agstatus'];
        
       if($conditions['type'] != ''){
            $data['agstatus'] = $conditions['type'];
        }
       if($conditions['startDateTime'] > 0 || $conditions['endDateTime'] > 0) {
//            $data['createdTime'] =array('between',strtotime($conditions['startDateTime']),strtotime($conditions['endDateTime']));
            $data['createdTime'] = array(array('GT', strtotime($conditions['startDateTime'])), array('LT', strtotime($conditions['endDateTime'])));
        }
        return $data;
    }
}
