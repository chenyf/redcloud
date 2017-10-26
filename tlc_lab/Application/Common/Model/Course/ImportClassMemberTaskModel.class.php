<?php
/*
 * 数据层
 * @package
 * @version    $Id$
 */
namespace  Common\Model\Course;
use Common\Model\Common\BaseModel;

class ImportClassMemberTaskModel extends BaseModel
{

    protected $currentDb = CENTER;
    protected $tableName = 'import_class_member_task';
    
    /**
     * 根据id获取任务
     * @param int $classId
     * @return array
     * @date 2015-11-23
     * @author LiangFuJian <liangfujian@redcloud.com>
     */
    public function getClassMemberTaskById($id){

        $map['id'] = intval($id);
        return $this->where($map)->find();

    }

    /**
     * 根据授课班id获取任务
     * @param int $classId
     * @return array
     * @date 2015-11-23
     * @author LiangFuJian <liangfujian@redcloud.com>
     */
    public function getClassMemberTaskByClassId($classId){

        $map['classId'] = intval($classId);
        return $this->where($map)->order('id desc')->find();

    }

    /**
     * 添加导入成员任务
     * @param array $data
     * @return int|boolean
     */
    public function addClassMemberTask($data){

        return $this->add($data);

    }

    /**
     * 更新任务状态
     * @param int $id
     * @param array $data
     * @return boolean
     */
    public function updateClassMemberTask($id, $data){

        $map['id'] = $id;
        return $this->where($map)->save($data);
    }

	

}