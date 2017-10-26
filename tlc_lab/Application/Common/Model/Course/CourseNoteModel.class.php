<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;

class CourseNoteModel extends BaseModel{
    
    protected $tableName = 'course_note';
	
    public function getNote($id){
        return $this->where(" id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function findNotesByUserIdAndCourseId($userId, $courseId){
        return $this->where("userId = {$userId} and courseId = {$courseId}")->order("createdTime desc")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND courseId = ? ORDER BY createdTime DESC";
//        return $this->getConnection()->fetchAll($sql, array($userId, $courseId));
    }

    public function findNotes($where){
        return $this->where($where)->find()?:null;
    }

    public function addNote($noteInfo){
        $r = $this->add($noteInfo);
        if(!$r) E("Insert noteInfo error.");
        return $this->getNote($r);
//        $affected = $this->getConnection()->insert($this->tableName, $noteInfo);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert noteInfo error.');
//        }
//        return $this->getNote($this->getConnection()->lastInsertId());
    }

    public function updateNote($id,$noteInfo){
        $this->where("id = {$id}")->save($noteInfo);
//        $this->getConnection()->update($this->tableName, $noteInfo, array('id' => $id));
        return $this->getNote($id);
    }

    public function deleteNoteByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->delete();
    }

    public function deleteNote($id){
        return $this->where("id = {$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function getNoteByUserIdAndLessonId($userId,$lessonId){
        return $this->where("userId = {$userId} and lessonId = {$lessonId}")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND lessonId = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($userId, $lessonId));
    }

    public function findNotesByUserIdAndStatus($userId,$status){
        return $this->where("userId = {$userId} and status = {$status}")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND status = ?";
//        return $this->getConnection()->fetchAll($sql, array($userId, $status));
    }
    	
    public function getNoteCountByUserIdAndCourseId($userId, $courseId){
        return $this->where("userId = {$userId} and courseId = {$courseId}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE userId = ? AND courseId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($userId, $courseId));
    }
    
    public function searchNotes($conditions, $orderBy, $start, $limit){
        $this->filterStartLimit($start, $limit);
//        $where = $this->createSearchNoteQueryBuilder($conditions);
//        return $this->where($where)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start, $limit)->select() ? : array();
        $builder = $this->createSearchNoteQueryBuilder($conditions)
                        ->select('*')
                        ->addOrderBy($orderBy[0], $orderBy[1])
                        ->setFirstResult($start)
                        ->setMaxResults($limit);

        return $builder->execute()->fetchAll() ? : array();
    }
    
    public function searchNoteCount($conditions){
//         $where = $this->createSearchNoteQueryBuilder($conditions);
//         return $this->where($where)->field('id')->count();
        $builder = $this->createSearchNoteQueryBuilder($conditions)->select('count(id)');
        return $builder->execute()->fetchColumn(0);
    }

    private function createSearchNoteQueryBuilder($conditions){
        $where = " 1 = 1";
        if (isset($conditions['content'])) {
            $conditions['content'] = "%{$conditions['content']}%";
//            $where.=" and content like '{$conditions['content']}'";
        }
        $builder = $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, 'note')
                ->andWhere('userId = :userId')
                ->andWhere('courseId = :courseId')
                ->andWhere('lessonId = :lessonId')
                ->andWhere('status = :status')
                ->andWhere('content LIKE :content');

        if (isset($conditions['courseIds']) && count($conditions['courseIds'])>0 ){
            $courseIdsRange = '('.implode(', ',$conditions['courseIds']).')';
            $builder = $builder->andStaticWhere("courseId IN {$courseIdsRange}");
//            $where.= " and courseId in ({$courseIdsRange})";
        }
        return $builder;
    }
}
?>
