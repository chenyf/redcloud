<?php
/**
 * 用户意见反馈
 * @author fubaosheng 2015-05-08
 */
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class ProblemFeedbackModel extends BaseModel{
    protected $tableName = 'problem_feedback';
    
    public function getProblemFeedback($id){
        return $this->where("id = {$id}")->find() ? : array();
    }
    
    public function addProblemFeedback($feedBack){
        $r = $this->add($feedBack);
        if(!$r) E("Insert FeedBack error.");
        else synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$r));
        return  $this->getProblemFeedback($r);
    }
    
    public function searchProFeedbackCount($conditions){
        $builder = $this->createProFeedbackQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }
    
    public function searchProFeedbacks($conditions, $orderBy, $start, $limit){
        $builder = $this->createProFeedbackQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }
    
    private function createProFeedbackQueryBuilder($conditions){
        $conditions = array_filter($conditions,function($v){
            if($v === 0){
                return true;
            }
            if(empty($v)){
                return false;
            }
            return true;
        });
        
//        $this->switchCenterDBOverWebCode()->setCommCenterWebUseWebCodeRule();
        $builder = $this->createDynamicQueryBuilder($conditions);

        return  $builder
                ->from($this->tableName,'problem_feedback')
                ->andWhere('type = :type')
                ->andWhere('roles = :roles')
                ->andWhere("`from` = :from");
    }
}
?>
