<?php
namespace Common\Model\Testpaper;
use Common\Model\Common\BaseModel;

class TestpaperModel extends BaseModel
{
	protected $tableName = 'testpaper';

    private $serializeFields = array(
            'metas' => 'json'
    );

    public function getTestpaper($id)
    {
        $testpaper = $this-> where("id = {$id}")-> find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        $testpaper = $this->getConnection()->fetchAssoc($sql, array($id));
        return $testpaper ? $this->createSerializer()->unserialize($testpaper, $this->serializeFields) : array();
    }

    public function selectTestPapersByCourseId($courseId){
        $target = "course-" . $courseId;
        return $this-> where(["target" => $target])-> select() ? : array();
    }

    public function findTestpapersByIds(array $ids)
    {
        if(empty($ids)){ 
            return array(); 
        }
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
        $marks = implode(',', $ids);
        $map['id']  = array("in", $marks);
        return $this-> where($map)-> select() ? : array();
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $ids);
    }


    public function searchTestpapers($conditions, $orderBy, $start, $limit)
    {
//        $this->filterStartLimit($start, $limit);
//        $this->checkOrderBy($orderBy, array('createdTime'));
//
//        $builder = $this->_createSearchQueryBuilder($conditions)
//            ->select('*')
//            ->setFirstResult($start)
//            ->setMaxResults($limit)
//            ->orderBy($orderBy[0], $orderBy[1]);
//
//        $questions = $builder->execute()->fetchAll() ? : array();
        $questions = $this-> where($conditions)-> order("$orderBy[0] $orderBy[1]")-> limit($start, $limit)->select();
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }

    public function searchTestpapersCount($conditions)
    {
//        return $this-> where($conditions)-> count("id");
        $builder = $this->_createSearchQueryBuilder($conditions)
             ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function addTestpaper($fields)
    {
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $affected = $this-> add($fields);
        if($affected <= 0){
            E("Insert testpaper error.");
        }
        return  $this->getTestpaper($affected);
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert testpaper error.');
//        }
//        return $this->getTestpaper($this->getConnection()->lastInsertId());
    }

    public function updateTestpaper($id, $fields)
    {
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        
        $this-> where("id = {$id}")-> save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getTestpaper($id);
    }

    public function deleteTestpaper($id)
    {
        return $this-> where("id = {$id}")-> delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function findTestpaperByTargets(array $targets)
    {
        if(empty($targets)){ 
            return array(); 
        }
//        $marks = str_repeat('?,', count($targets) - 1) . '?';
        $marks = implode(',',$targets);
        $map['target'] = array("in", $marks);
        $results = $this-> where($map)-> select() ? : array();
//        $sql ="SELECT * FROM {$this->tableName} WHERE target IN ({$marks});";
        
//        $results = $this->getConnection()->fetchAll($sql, $targets) ? : array();
        return $this->createSerializer()->unserialize($results, $this->serializeFields);
    }

    private function _createSearchQueryBuilder($conditions)
    {
        $conditions = array_filter($conditions);

        if (isset($conditions['targetPrefix'])) {
            $conditions['targetLike'] = "{$conditions['targetPrefix']}%";
            unset($conditions['target']);
        }

        $builder = $this->createDynamicQueryBuilder($conditions)
            ->from($this->tableName, 'testpaper')
            ->andWhere('target = :target')
            ->andWhere('target LIKE :targetLike')
            ->andWhere('status LIKE :status');
            

        return $builder;
    }
    
    //xf
    public function getItemCount($testId){
      $option = array(
          'id' => $testId
      );
      return $this->where($option)->Field('itemCount,score')->select();
    }

    public function updateTestpaperScore($map){
        return $this->query("update testpaper set score=".$map['score'].",scoreSetType=".$map['scoreSetType']." where id=".$map['id']);
    }
    public function searchTestpapersItemCount($conditions)
    {
//        return $this-> where($conditions)-> count("id");
        $builder = $this->_createSearchQueryBuilder($conditions)
             ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

}