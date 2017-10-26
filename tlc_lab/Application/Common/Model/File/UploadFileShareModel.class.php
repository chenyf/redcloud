<?php

namespace Common\Model\File;

use Common\Model\Common\BaseModel;

class UploadFileShareModel extends BaseModel {

    protected $tableName = 'upload_files_share';

	public function getConnection() {
		return $this;
	}

    /**
     * 
     * @param type $targetUserId
     * @return array
     * @author ZhaoZuoWu 2015-03-26
     */
    public function findMySharingContacts($targetUserId) {
        $data = $this->where("targetUserId='" . $targetUserId . "' and isActive = 1 ")->select();
        return $data ? $data : null;
    }

	public function findShareHistoryByUserId($sourceUserId){
        $sql = "SELECT * FROM {$this->tableName} WHERE sourceUserId = {$sourceUserId} ORDER BY updatedTime DESC;";
        $result = $this->getConnection()->query($sql) ? : null;
        return $result;
    }

    public function findShareHistory($sourceUserId, $targetUserId) {
        $sql = "SELECT * FROM {$this->tableName} WHERE sourceUserId = {$sourceUserId} and targetUserId = {$targetUserId} LIMIT 1;";
        $result = $this->getConnection()->query($sql, array($sourceUserId, $targetUserId)) ? : null;
        return $result;
    }

    public function addShare($share) {
        $affected = $this->getConnection()->add($share);
        if ($affected <= 0) {
            E('Insert file share error.');
        }
        return $affected;
    }

    public function updateShare($id, $fields) {
        $this->getConnection()->where(array('id'=>$id))->save($fields);
        return $id;
    }

}
