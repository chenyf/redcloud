<?php
/*
 * 数据层
 * @package
 * @version    $Id$
 */
namespace  Common\Model\Course;
use Common\Model\Common\BaseModel;

class ImportClassMemberDetailModel extends BaseModel
{
    protected $currentDb = CENTER;
    protected $tableName = 'import_class_member_detail';

    public function getMemberDetailCount($conditions)
    {

        return $this->where($conditions)->count();
    }

    public function getMemberDetailPageList($conditions, $orderBy,$start,$limit)
    {
        $this->filterStartLimit($start, $limit);
        return $this->where($conditions)
                    ->limit($start, $limit)
                    ->order($orderBy)
                    ->select();
    }
    
    public function addMemberDetail($data){
        return $this->add($data);
    }
    
    public function addAllMemberDetail($dataList){
        return $this->addAll($dataList);
    }


    public function updateMemberDetail($taskId, $account, $data){

        $map['taskId'] = intval($taskId);
        $map['account'] = $account;
        $this->where($map)->save($data);
    }


}