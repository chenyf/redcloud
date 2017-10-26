<?php

namespace Common\Model\Util;

use Common\Model\Common\BaseModel;

class SystemUtilModel extends BaseModel {

    public function getCourseIdsWhereCourseHasDeleted() {
        $sql = "SELECT DISTINCT  targetId FROM upload_files WHERE ";
        $sql .= " targetType='courselesson' and targetId NOT IN (SELECT id FROM course)";
        $data = M()->query($sql);
        return $data;
    }

}