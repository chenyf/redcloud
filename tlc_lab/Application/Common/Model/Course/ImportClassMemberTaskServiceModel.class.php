<?php
/*
 * 数据层
 * @package
 * @version    $Id$
 */
namespace  Common\Model\Course;
use Common\Model\Common\BaseModel;

class ImportClassMemberTaskServiceModel extends BaseModel
{
    
    /**
     * 根据id获取任务
     * @param int $classId
     * @return array
     * @date 2015-11-23
     * @author LiangFuJian <liangfujian@redcloud.com>
     */
    public function getClassMemberTaskById($id){

        return $this->getImportClassMemberTaskDao()->getClassMemberTaskById($id);
    }

    /**
     * 根据授课班id获取任务
     * @param int $classId
     * @return array
     * @date 2015-11-23
     * @author LiangFuJian <liangfujian@redcloud.com>
     */
    public function getClassMemberTaskByClassId($classId){

        return $this->getImportClassMemberTaskDao()->getClassMemberTaskByClassId($classId);

    }

    /**
     * 添加导入成员任务
     * @param array $data
     * @return int|boolean
     */
    public function addClassMemberTask($classId, $userId){

        $data['classId'] = intval($classId);
        $data['userId'] = intval($userId);
        $data['createdTime'] = time();
        return $this->getImportClassMemberTaskDao()->addClassMemberTask($data);

    }

    /**
     * 更新任务状态
     * @param int $id
     * @param array $data
     * @return boolean
     */
    public function updateClassMemberStatus($id, $status){

        $data['status'] = intval($status);
        $data['updatedTime'] = time();
        return $this->getImportClassMemberTaskDao()->updateClassMemberTask(intval($id),$data);
    }


    public function getImportClassMemberTaskDao(){

        return $this->createDao('Course.ImportClassMemberTask');  

    }

	

}