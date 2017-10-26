<?php
namespace Common\Model\Course;
use Common\Model\Common\BaseModel;

class LessonLearnModel extends BaseModel{
    
    protected $tableName = 'course_lesson_learn';

    public function getLearn($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getLearnByUserIdAndLessonId($userId, $lessonId){
        return $this->where("userId = {$userId} and lessonId ={$lessonId}")->find() ? : null;
//    $sql ="SELECT * FROM {$this->tableName} WHERE userId=? AND lessonId=?";
//    return $this->getConnection()->fetchAssoc($sql, array($userId, $lessonId)) ? : null;
    }

    public function findLearnsByUserIdAndCourseId($userId, $courseId){
        return $this->where(" userId={$userId} AND courseId={$courseId}")->select() ? : array();
//        $sql ="SELECT * FROM {$this->tableName} WHERE userId=? AND courseId=?";
//        return $this->getConnection()->fetchAll($sql, array($userId, $courseId)) ? : array();
    }

    public function findLearnsByUserIdAndCourseIdAndStatus($userId, $courseId, $status){
        return $this->where(" userId={$userId} AND courseId={$courseId} and status = '{$status}'")->select() ? : array();
//        $sql ="SELECT * FROM {$this->tableName} WHERE userId=? AND courseId=? AND status = ?";
//        return $this->getConnection()->fetchAll($sql, array($userId, $courseId, $status)) ? : array();
    }

    public function getLearnCountByUserIdAndCourseIdAndStatus($userId, $courseId, $status){
         return $this->where(" userId={$userId} AND courseId={$courseId} and status = '{$status}'")->count() ;
//        $sql ="SELECT COUNT(*) FROM {$this->tableName} WHERE userId = ? AND courseId = ? AND status = ?";
//        return $this->getConnection()->fetchColumn($sql, array($userId, $courseId, $status));
    }

    public function findLearnsByLessonId($lessonId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("lessonId={$lessonId}")->order("startTime DESC")->limit($start, $limit)->select() ;
//        $sql = "SELECT * FROM {$this->tableName} WHERE lessonId = ? ORDER BY startTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($lessonId));
    }

    public function findLearnsCountByLessonId($lessonId){
         return $this->where("lessonId={$lessonId}")->count() ;
//        $sql ="SELECT COUNT(*) FROM {$this->tableName} WHERE lessonId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($lessonId));
    }

    public function findLatestFinishedLearns($start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("status = 'finished'")->order("finishedTime DESC")->limit($start, $limit)->select() ;
//        $sql = "SELECT * FROM {$this->tableName} WHERE status = 'finished' ORDER BY finishedTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql);
    }

    public function addLearn($learn){
        $r = $this->add($learn);
        if(!$r) E("Insert learn error.");
        return $this->getLearn($r);
//        $affected = $this->getConnection()->insert($this->tableName, $learn);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert learn error.');
//        }
//        return $this->getLearn($this->getConnection()->lastInsertId());
    }

    public function updateLearn($id, $fields){
        $this->where(" id = {$id}")->save($fields);
//        $id = $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getLearn($id);
    }

    public function deleteLearnsByLessonId($lessonId){
        return $this->where("lessonId = {$lessonId}")->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE lessonId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($lessonId));
    }
    

    public function searchLearnCount($conditions){
//        $where = $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->count();
        $builder=$this->_createSearchQueryBuilder($conditions);
        if(isset($conditions['siteSelect'])) $builder = processSqlObj(array('sqlObj'=>$builder, 'siteSelect'=>$conditions['siteSelect']));
        $builder = $builder->select('count(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchLearnTime($conditions){
//        $where = $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->sum('learnTime');
        $builder=$this->_createSearchQueryBuilder($conditions)->select('sum(learnTime)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchWatchTime($conditions){
//        $where = $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->sum('watchTime');
        $builder=$this->_createSearchQueryBuilder($conditions)->select('sum(watchTime)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchLearns($conditions, $orderBy, $start, $limit){
        $this->filterStartLimit($start, $limit);
//        $where =  $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start, $limit)->select()? : array();

        
        $builder = $this->_createSearchQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array(); 
    }

    private function _createSearchQueryBuilder($conditions){
        if (isset($conditions['targetType'])) {
            $builder=$this->createDynamicQueryBuilder($conditions)
            ->from($this->tableName,$this->tableName)
            ->andWhere("status = :status")
            ->andWhere("finishedTime >= :startTime")
            ->andWhere("finishedTime <= :endTime");
        }else{
             $builder=$this->createDynamicQueryBuilder($conditions)
            ->from($this->tableName,$this->tableName)
            ->andWhere("status = :status")
            ->andWhere("userId = :userId")
            ->andWhere("lessonId = :lessonId")
            ->andWhere("courseId = :courseId")
            ->andWhere("finishedTime >= :startTime")
            ->andWhere("finishedTime <= :endTime");
        }

        if (isset($conditions['courseIds'])) {
            $courseIds = array();
            foreach ($conditions['courseIds'] as $courseId) {
                if (ctype_digit($courseId)) {
                    $courseIds[] = $courseId;
                }
            }
            if ($courseIds) {
                $courseIds = join(',', $courseIds);
                $builder->andStaticWhere("courseId IN ($courseIds)");
            }
        }

        return $builder;
    }

    public function analysisLessonFinishedDataByTime($startTime,$endTime){
        return $this->field("count(id) as count,from_unixtime(finishedTime,'%Y-%m-%d') as date")->where("`finishedTime`>={$startTime} and `finishedTime`<={$endTime} and `status`='finished'")->group("from_unixtime(`finishedTime`,'%Y-%m-%d')")->order("date ASC")->select();
//        $sql="SELECT count(id) as count, from_unixtime(finishedTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE`finishedTime`>={$startTime} and `finishedTime`<={$endTime} and `status`='finished'  group by from_unixtime(`finishedTime`,'%Y-%m-%d') order by date ASC ";
//        return $this->getConnection()->fetchAll($sql);
    }
}
?>