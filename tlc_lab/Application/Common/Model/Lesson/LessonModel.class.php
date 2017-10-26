<?php
namespace Common\Model\Lesson;
use Common\Model\Common\BaseModel;
class LessonModel extends BaseModel
{
    protected $tableName = 'course_lesson';

//    public function getConnection(){
//        return $this;
//    }
    public function getLesson($id)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getLessonByCourseIdAndNumber($courseId, $number)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? AND number = ? LIMIT 1";
        return $this->fetchAssoc($sql, array($courseId, $number)) ? : null;
    }

    public function findLessonsByIds(array $ids)
    {
        if(empty($ids)){ return array(); }
        $marks = implode(",", $ids);
        $map['id'] = array("in",$marks);
        return $this->where($map)->select();
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->fetchAll($sql, $ids);
    }

    public function findLessonsByTypeAndMediaId($type, $mediaId)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE type = ? AND mediaId = ?";
        return $this->fetchAll($sql, array($type, $mediaId));
    }

    public function findMinStartTimeByCourseId($courseId)
    {
        $sql = "select min(`startTime`) as startTime from `course_lesson` where courseId =?;";
        return $this->fetchAll($sql,array($courseId));
    }

    public function findLessonsByCourseId($courseId)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? ORDER BY seq ASC";
        return $this->fetchAll($sql, array($courseId));
    }

    public function findLessonIdsByCourseId($courseId)
    {
        $sql = "SELECT id FROM {$this->tableName} WHERE  courseId = ? ORDER BY number ASC";
        return $this->executeQuery($sql, array($courseId))->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getLessonCountByCourseId($courseId)
    {
        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE courseId = ? ";
        return $this->fetchColumn($sql, array($courseId));
    }

    public function searchLessons($conditions, $orderBy, $start, $limit)
    {
       
        $this->filterStartLimit($start, $limit);
        
        $data = $this
                ->where($conditions)
                ->order("{$orderBy[0]} {$orderBy[1]}")
                ->limit("{$start},{$limit}")
                ->select();
        return $data;
    }

    public function searchLessonCount($conditions)
    {
        $builder = $this->_createSearchQueryBuilder($conditions)
            ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function getLessonMaxSeqByCourseId($courseId)
    {
        $sql = "SELECT MAX(seq) FROM {$this->tableName} WHERE  courseId = ?";
        return $this->fetchColumn($sql, array($courseId));
    }

    public function findTimeSlotOccupiedLessonsByCourseId($courseId,$startTime,$endTime,$excludeLessonId=0)
    {
        $addtionalCondition = ";";

        if (!empty($excludeLessonId)) {
            $addtionalCondition = "and id != {$excludeLessonId};";
        }

        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = {$courseId} and ((startTime  < {$startTime} and endTime > {$startTime}) or  (startTime between {$startTime} and {$endTime})) ".$addtionalCondition;
        
        return $this->fetchAll($sql, array($courseId,$startTime,$endTime));
    }

    public function findTimeSlotOccupiedLessons($startTime,$endTime,$excludeLessonId=0)
    {
        $addtionalCondition = ";";

        if (!empty($excludeLessonId)) {
            $addtionalCondition = "and id != {$excludeLessonId};";
        }

        $sql = "SELECT * FROM {$this->tableName} WHERE ((startTime  < {$startTime} and endTime > {$startTime}) or  (startTime between {$startTime} and {$endTime})) ".$addtionalCondition;
        
        return $this->fetchAll($sql, array($startTime,$endTime));
    }

    public function findLessonsByChapterId($chapterId)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE chapterId = ? ORDER BY seq ASC";
        return $this->fetchAll($sql, array($chapterId));
    }

    public function addLesson($lesson)
    {
        $affected = $this->insert($this->tableName, $lesson);
        if ($affected <= 0) {
            throw $this->createDaoException('Insert course lesson error.');
        }
        return $this->getLesson($this->lastInsertId());
    }

    public function updateLesson($id, $fields)
    {
        $this->update($this->tableName, $fields, array('id' => $id));
        return $this->getLesson($id);
    }

    public function deleteLessonsByCourseId($courseId)
    {
        $sql = "DELETE FROM {$this->tableName} WHERE courseId = ?";
        return $this->executeUpdate($sql, array($courseId));
    }

    public function deleteLesson($id)
    {
        return $this->delete($this->tableName, array('id' => $id));
    }

    public function sumLessonGiveCreditByCourseId($courseId)
    {
        $sql = "SELECT SUM(giveCredit) FROM {$this->tableName} WHERE  courseId = ?";
        return $this->fetchColumn($sql, array($courseId)) ? : 0;
    }

    public function sumLessonGiveCreditByLessonIds(array $lessonIds)
    {
        if(empty($lessonIds)){ 
            return 0; 
        }
        $marks = implode(",", $lessonIds);
        $map["id"] = array("in",$marks);
        return $this->where($map)->sum("giveCredit");
//        $marks = str_repeat('?,', count($lessonIds) - 1) . '?';
//        $sql ="SELECT SUM(giveCredit) FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->fetchColumn($sql, $lessonIds);
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

    public function analysisLessonNumByTime($startTime,$endTime)
    {
              $sql="SELECT count( id)  as num FROM `{$this->tableName}` WHERE  `createdTime`>={$startTime} and `createdTime`<={$endTime}  ";

              return $this->fetchColumn($sql);
    }

    public function analysisLessonDataByTime($startTime,$endTime)
    {
             $sql="SELECT count( id) as count, from_unixtime(createdTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE  `createdTime`>={$startTime} and `createdTime`<={$endTime} group by from_unixtime(`createdTime`,'%Y-%m-%d') order by date ASC ";

            return $this->fetchAll($sql);
    }

}