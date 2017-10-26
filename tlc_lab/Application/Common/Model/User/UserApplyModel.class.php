<?php
/**
 * 用户申请老师
 * @author fubaosheng 2015-12-16
 */
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserApplyModel extends BaseModel{
    
    protected $tableName = 'user_apply';
    
    public function getUserLastApply($uid){
        $where["applyUid"] = $uid;
        return $this->where($where)->order("applyTm desc")->find() ? : array();
    }
    
    public function addUserApply($apply){
        $id = $this->add($apply);
        return $id;
    }
    
    public function getApply($id){
        $where["id"] = $id;
        return $this->where($where)->find() ? : array();
    }
    
    public function removeApply($id){
        $where["id"] = $id;
        return $this->where($where)->save(array("status"=>3));
    }
    
    public function checkApply($id,$data){
        $where["id"] = $id;
        return $this->where($where)->save($data);
    }
    
    public function searchApplyCount($conditions){
        $builder = $this->createApplyQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }
    
    public function searchApplys($conditions, $orderBy, $start, $limit){
        $builder = $this->createApplyQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }
    
    private function createApplyQueryBuilder($conditions){
        
        if(isset($conditions["status"]) && $conditions["status"] == -1){
            $conditions["status"] = "";
        }
        
        $conditions = array_filter($conditions,function($v){
            if($v === 0){
                return true;
            }
            if(empty($v)){
                return false;
            }
            return true;
        });
        
        if (isset($conditions["applyName"])) {
            $conditions["applyName"] = "%{$conditions["applyName"]}%";
        }

        return  $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, 'user_apply')
                ->andWhere('applyName like :applyName')
                ->andWhere('status = :status');
    }
}
?>
