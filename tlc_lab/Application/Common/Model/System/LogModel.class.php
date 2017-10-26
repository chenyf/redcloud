<?php
namespace Common\Model\System;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\WebCode;

class LogModel extends BaseModel{
    
    protected $tableName = "log";
    
    /**
     * 日志 中心站=>中心库，高校站=>私有库
     * @author fubaosheng 2015-08-11
     */
    public function addLog($log){
        $table = $this;
        $id = $table->add($log);
        return $id;
    }
    
    public function searchLogs($conditions, $sort, $start, $limit){
        $builder = $this->createLogQueryBuilder($conditions)->select('*')->from($this->tableName, $this->tableName);
        $builder->addOrderBy($sort[0], $sort[1]);
        $builder->setFirstResult($start)->setMaxResults($limit);
       	return $builder->execute()->fetchAll() ? : array();
    }
    public function searchLogCount($conditions){
        $builder = $this->createLogQueryBuilder($conditions)
                ->select('count(`id`) AS count')
                ->from($this->tableName, $this->tableName);
        return $builder->execute()->fetchColumn(0);
    }
        
    protected function createLogQueryBuilder($conditions){
        $conditions = array_filter($conditions);
        $obj = $this->createDynamicQueryBuilder($conditions);
        $builder = $obj
                ->andWhere('module = :module')
                ->andWhere('action = :action')
                ->andWhere('level = :level')
                ->andWhere('userId = :userId')
                ->andWhere('createdTime > :startDateTime')
                ->andWhere('createdTime < :endDateTime');
        if (isset($conditions['userIds'])) {
            $userIds = join(',', $conditions['userIds']);
            $builder->andStaticWhere("userId IN ($userIds)");
        }
        return $builder;
    }
    
    public function analysisLoginNumByTime($startTime,$endTime , $siteSelect = 'local'){
        $builder = $this;
        if(!empty($siteSelect)){
            $builder = processSqlObj(array("sqlObj" => $builder, "siteSelect" => $siteSelect));
        }
        $num = $builder->field('count(distinct userId) as num')->where("action = 'login_success' and createdTime >={$startTime} and createdTime <={$endTime}")->find();
        return $num["num"];
//        $sql="SELECT count(distinct userid)  as num FROM `{$this->tableName}` WHERE `action`='login_success' and  `createdTime`>={$startTime} and `createdTime`<={$endTime}  ";
//        return $this->getConnection()->fetchColumn($sql);
    }
    
    public function analysisLoginDataByTime($startTime,$endTime){
        $arr = $this->field("count(distinct userId) as count, from_unixtime(createdTime,'%Y-%m-%d') as date")->where("action='login_success' and createdTime>={$startTime} and createdTime<={$endTime}")->group("from_unixtime(`createdTime`,'%Y-%m-%d')")->order("date asc")->select();
        return $arr;
//        $sql="SELECT count(distinct userid) as count, from_unixtime(createdTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE `action`='login_success' and `createdTime`>={$startTime} and `createdTime`<={$endTime} group by from_unixtime(`createdTime`,'%Y-%m-%d') order by date ASC ";
//        return $this->getConnection()->fetchAll($sql);
    }
}
?>
