<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;

class ReviewModel extends BaseModel{
    
    protected $tableName = 'course_review';

    public function getReview($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function findReviewsByCourseId($courseId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("courseId = {$courseId}")->order("createdTime DESC")->limit($start,$limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($courseId)) ? : array();
    }

    public function getReviewCountByCourseId($courseId){
         return $this->where("courseId = {$courseId}")->count("id");
//        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE courseId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId));
    }

    public function addReview($review){
//        dump($review);die;
        $r = $this->add($review);
//        echo $this->getlastsql();die;
        if(!$r) E("Insert review error.");
        return  $this->getReview($r);
//        $affected = $this->getConnection()->insert($this->tableName, $review);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert review error.');
//        }
//        return $this->getReview($this->getConnection()->lastInsertId());
    }

    public function updateReview($id, $fields){
        $this->where("id = {$id}")->save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getReview($id);
    }

    public function getReviewByUserIdAndCourseId($userId, $courseId){
         return $this->where("courseId = {$courseId} and userId = {$userId}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? AND userId = ? LIMIT 1;";
//        return $this->getConnection()->fetchAssoc($sql, array($courseId, $userId)) ? : null;
    }

    public function getReviewRatingSumByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->sum("rating");
//        $sql = "SELECT sum(rating) FROM {$this->tableName} WHERE courseId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId));
    }

    public function searchReviewsCount($conditions){
//        $where = $this->createReviewSearchBuilder($conditions);
//        return $this->where($where)->count("id");
        $builder = $this->createReviewSearchBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchReviews($conditions, $orderBy, $start, $limit){
        $this->filterStartLimit($start, $limit);
//        $where = $this->createReviewSearchBuilder($conditions);
//        return $this->where($where)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start, $limit)->select() ? : array();
        $builder = $this->createReviewSearchBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }

    public function deleteReview($id){
        $this->where("id = {$id}")->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->executeUpdate($sql, array($id));
    }

    private function createReviewSearchBuilder($conditions){
	    if (isset($conditions['content'])) {
		    $conditions['content'] = "%{$conditions['content']}%";
	    }

	    $builder = $this->createDynamicQueryBuilder($conditions)
		    ->from($this->tableName, $this->tableName)
		    ->andWhere('userId = :userId')
		    ->andWhere('courseId = :courseId')
		    ->andWhere('rating = :rating')
		    ->andWhere('content LIKE :content');

	    if (isset($conditions['courseIds']) && count($conditions['courseIds'])>0 ){
		    $courseIdsRange = '('.implode(', ',$conditions['courseIds']).')';
		    $builder = $builder->andStaticWhere("courseId IN {$courseIdsRange}");
	    }

	    return $builder;
    }
}
?>
