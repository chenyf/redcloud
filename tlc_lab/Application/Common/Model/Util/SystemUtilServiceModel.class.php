<?php

namespace Common\Model\Util;

use Common\Model\Common\BaseModel;

class SystemUtilServiceModel extends BaseModel {

    //TODO 删除之前检查该文件是否被其他课程使用
    public function removeUnusedUploadFiles() {
        
        $targets = $this->getSystemUtilDao()->getCourseIdsWhereCourseHasDeleted();
        if (empty($targets))
            return;
        $targets = $this->plainTargetId($targets);
        foreach ($targets as $target) {
            $conditions = array(
                'targetType' => 'courselesson',
                'targetId' => $target
            );
            $uploadFiles = $this->getUploadFileService()->searchFiles(
                    $conditions, 'latestCreated', 0, 1000
            );
            $this->removeUploadFiles($uploadFiles);
        }
    }

    private function plainTargetId($targets) {
        $result = array();
        foreach ($targets as $target) {
            $result[] = $target['targetId'];
        }
        return $result;
    }

    private function removeUploadFiles($uploadFiles) {
        foreach ($uploadFiles as $file) {
            $this->getUploadFileService()->deleteFile($file['id']);
        }
    }

    protected function getSystemUtilDao() {
        return $this->createDao('Util.SystemUtilModel');
    }

    protected function getUploadFileService() {
        return $this->createService('File.UploadFileServiceModel');
    }

}