<?php
namespace Common\Model\Course;
use Common\Model\Common\BaseModel;

class LessonModel extends BaseModel{
    
    protected $tableName = 'course_lesson';

    public function getLesson($id){
        return $this->where(" id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function selectLessonsByCourseId($courseId){
        return $this->where(array('courseId'=>$courseId))->select() ?: array();
    }

    public function getLessonByCourseIdAndNumber($courseId, $number){
        return $this->where("courseId = {$courseId} and number = {$number}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? AND number = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($courseId, $number)) ? : null;
    }

    public function findLessonsByIds(array $ids){
        if(empty($ids)) return array(); 
        $str = implode(',', $ids);
        return $this->where("id in({$str})")->select();
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $ids);
    }

    public function findLessonsByTypeAndMediaId($type, $mediaId){
        return $this->where("type = '{$type}' and mediaId = {$mediaId}")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE type = ? AND mediaId = ?";
//        return $this->getConnection()->fetchAll($sql, array($type, $mediaId));
    }

    public function findMinStartTimeByCourseId($courseId){
        return $this->field("min(startTime) as startTime")->where("courseId = {$courseId}")->select();
//        $sql = "select min(`startTime`) as startTime from `course_lesson` where courseId =?;";
//        return $this->getConnection()->fetchAll($sql,array($courseId));
    }

    public function findLessonsByCourseId($courseId,$status=''){
         $map['courseId'] = $courseId;
         if(!empty($status))  $map['status'] = $status;
         return $this->where($map)->order("seq asc")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? ORDER BY seq ASC";
//        return $this->getConnection()->fetchAll($sql, array($courseId));
    }
    /**
     * 查课程内容
     * @author 钱志伟 2015-05-07
     */
    public function findLessons($courseId, $whereArr=array()){
         $whereArr['courseId'] = $courseId;
         return $this->where($whereArr)->order("seq asc")->select();
    }
    public function accordingSeqGetLesson($map){
                //print_r($map);
        return $this->where($map)->select();
    }
    public function findLessonIdsByCourseId($courseId){
        return $this->field("id")->where("courseId = {$courseId}")->order("number asc")->select();
//        $sql = "SELECT id FROM {$this->tableName} WHERE  courseId = ? ORDER BY number ASC";
//        return $this->getConnection()->executeQuery($sql, array($courseId))->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getLessonCountByCourseId($courseId){
         return $this->where("status = 'published' and courseId = {$courseId}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE courseId = ? ";
//        return $this->getConnection()->fetchColumn($sql, array($courseId));
    }
    public function getTestpaperId($conditions){
        $conditions['type']='practice';
         return $this->where($conditions)->getField('mediaId', true);
    }

    public function searchLessons($conditions, $orderBy, $start, $limit){
        $this->filterStartLimit($start, $limit);
        $builder = $this->_createSearchQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }

    public function searchLessonCount($conditions){
//        $where = $this->_createSearchQueryBuilder($conditions);
//        $count = $this->where($where)->count("id");
//        return $count;
        $builder = $this->_createSearchQueryBuilder($conditions);
        if(isset($conditions['siteSelect'])) $builder = processSqlObj(array('sqlObj'=>$builder, 'siteSelect'=>$conditions['siteSelect']));
        $builder = $builder->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function getLessonMaxSeqByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->max('seq');
//        $sql = "SELECT MAX(seq) FROM {$this->tableName} WHERE  courseId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId));
    }

    public function findTimeSlotOccupiedLessonsByCourseId($courseId,$startTime,$endTime,$excludeLessonId=0){
        $addtionalCondition = ";";
        if (!empty($excludeLessonId)) {
            $addtionalCondition = "and id != {$excludeLessonId};";
        }
        $where = " courseId = {$courseId} and ( (startTime<{$startTime} and endTime>{$startTime}) or (startTime between {$startTime} and {$endTime}) )";
        $where.=$addtionalCondition;
        return $this->where($where)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = {$courseId} and ((startTime  < {$startTime} and endTime > {$startTime}) or  (startTime between {$startTime} and {$endTime})) ".$addtionalCondition;
//        return $this->getConnection()->fetchAll($sql, array($courseId,$startTime,$endTime));
    }

    public function findTimeSlotOccupiedLessons($startTime,$endTime,$excludeLessonId=0){
        $addtionalCondition = ";";
        if (!empty($excludeLessonId)) {
            $addtionalCondition = "and id != {$excludeLessonId};";
        }
        $where = "  ( (startTime<{$startTime} and endTime>{$startTime}) or (startTime between {$startTime} and {$endTime}) )";
        $where.=$addtionalCondition;
        return $this->where($where)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE ((startTime  < {$startTime} and endTime > {$startTime}) or  (startTime between {$startTime} and {$endTime})) ".$addtionalCondition;
//        return $this->getConnection()->fetchAll($sql, array($startTime,$endTime));
    }

    public function findLessonsByChapterId($chapterId){
        return $this->where("chapterId = {$chapterId}")->order("seq asc")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE chapterId = ? ORDER BY seq ASC";
//        return $this->getConnection()->fetchAll($sql, array($chapterId));
    }

    public function addLesson($lesson){
        $r = $this->add($lesson);
        if(!$r) E("Insert course lesson error.");
        return $this->getLesson($r);
//        $affected = $this->getConnection()->insert($this->tableName, $lesson);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert course lesson error.');
//        }
//        return $this->getLesson($this->getConnection()->lastInsertId());
    }

    public function updateLesson($id, $fields){
        $this->where("id = {$id}")->save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getLesson($id);
    }

    public function deleteLessonsByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE courseId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($courseId));
    }

    public function deleteLesson($id){
        return $this->where("id ={$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function deleteLessonByCourseAndTeacher($courseId,$teacherId){
        $map = array(
            'courseId'  =>  $courseId,
            'userId'    =>  $teacherId
        );
        return $this->where($map)->delete();
    }

    public function sumLessonGiveCreditByCourseId($courseId){
        return $this->where("status = 'published' and courseId = {$courseId}")->sum('giveCredit') ? : 0;
//        $sql = "SELECT SUM(giveCredit) FROM {$this->tableName} WHERE  courseId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId)) ? : 0;
    }

    public function sumLessonGiveCreditByLessonIds(array $lessonIds){
        if(empty($lessonIds)) return 0; 
        $str = implode(',', $lessonIds);
        return $this->where("id in ({$str})")->sum('giveCredit');
//        $marks = str_repeat('?,', count($lessonIds) - 1) . '?';
//        $sql ="SELECT SUM(giveCredit) FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->getConnection()->fetchColumn($sql, $lessonIds);
    }

    private function _createSearchQueryBuilder($conditions)
    {

        $builder = $this->createDynamicQueryBuilder($conditions)
            ->from($this->tableName, $this->tableName)
            ->andWhere('courseId = :courseId')
            ->andWhere('status = :status')
            ->andWhere('type = :type')
            ->andWhere('free = :free')
            ->andWhere('userId = :userId')
            ->andWhere('mediaId = :mediaId')
            ->andWhere('startTime >= :startTimeGreaterThan')
            ->andWhere('endTime < :endTimeLessThan')
            ->andWhere('startTime <= :startTimeLessThan')
            ->andWhere('endTime > :endTimeGreaterThan')
            ->andWhere('title LIKE :titleLike')
            ->andWhere('createdTime >= :startTime')
            ->andWhere('createdTime <= :endTime');

        if (isset($conditions['courseIds'])) {
            $courseIds = array();
            foreach ($conditions['courseIds'] as $courseId) {
                if (ctype_digit((string)abs($courseId))) {
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

    public function analysisLessonNumByTime($startTime,$endTime){
        return $this->where("`createdTime`>={$startTime} and `createdTime`<={$endTime}")->field("count(id) as num")->count("id");
//        $sql="SELECT count( id)  as num FROM `{$this->tableName}` WHERE  `createdTime`>={$startTime} and `createdTime`<={$endTime}  ";
//        return $this->getConnection()->fetchColumn($sql);
    }

    public function analysisLessonDataByTime($startTime,$endTime){
        return $this->field("count(id) as count, from_unixtime(createdTime,'%Y-%m-%d') as date")->where("`createdTime`>={$startTime} and `createdTime`<={$endTime}")->group("from_unixtime(`createdTime`,'%Y-%m-%d')")->order("date ASC")->select();
//        $sql="SELECT count( id) as count, from_unixtime(createdTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE  `createdTime`>={$startTime} and `createdTime`<={$endTime} group by from_unixtime(`createdTime`,'%Y-%m-%d') order by date ASC ";
//        return $this->getConnection()->fetchAll($sql);
    }
    
    /**
     * 获取课程下的课程内容总时长
     * @author fubaosheng 2015-05-27
     */
    public function getCourseLessonLength($courseId){
        return $this->where("courseId = {$courseId}")->sum("length") ? :0;
    }
    
}
?>
