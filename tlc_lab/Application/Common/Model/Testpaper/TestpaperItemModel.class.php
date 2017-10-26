<?php

namespace Common\Model\Testpaper;
use Common\Model\Common\BaseModel;
//use Topxia\Service\Testpaper\Dao\TestpaperItemDao;
//use Doctrine\DBAL\Query\QueryBuilder;
//use Doctrine\DBAL\Connection;

class TestpaperItemModel extends BaseModel
{
    protected $tableName = 'testpaper_item';

    public function getItem($id)
    {
        return $this-> where("id = {$id}")-> find() ?  : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addItem($item)
    {
        $rs = $this-> add($item);
        if($item <=0 ){
            E("Insert item error.");
        }
        return $this->getItem($rs);
//        $item = $this->getConnection()->insert($this->tableName, $item);
//        if ($item <= 0) {
//            throw $this->createDaoException('Insert item error.');
//        }
//        return $this->getItem($this->getConnection()->lastInsertId());
    }

    public function updateItem($id, $fields)
    {
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        $this-> where("id = {$id}")-> save($fields);
        return $this->getItem($id);
    }
    
    public function updateItem1($map,$fields){
        return $this->where($map)->save($fields);
    }
    public function deleteItem($id)
    {
        return $this-> where("id = {$id}")-> delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    } 

    public function deleteItemsByParentId($id)
    {
        return $this-> where("parentId = {$id}")-> delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE parentId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($id));
    }

    public function deleteItemsByTestpaperId($id)
    {
        return $this-> where("testId = {$id}")-> delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE testId = ? ";
//        return $this->getConnection()->executeUpdate($sql, array($id));
    }

    public function findItemByIds(array $ids)
    {
        if(empty($ids)){ 
            return array(); 
        }
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
        $marks = implode(',',$ids);
        $map['id'] = array("in", $marks);
        return $this-> where($map)-> select();
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $ids);
    }

    public function findItemsByTestPaperId($testPaperId)
    {
        return $this-> where("testId = $testPaperId")-> order("seq asc")->select()? : array();
//        $sql ="SELECT * FROM {$this->tableName} WHERE testId = ? order by `seq` asc ";
//        return $this->getConnection()->fetchAll($sql, array($testPaperId)) ? : array();
    }

    public function getItemsCountByTestId($testId)
    {
        return $this-> where("testId = {$testId}")-> count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE testId = ? ";
//        return $this->getConnection()->fetchColumn($sql, array($testId));
    }

    public function getItemsCountByTestIdAndParentId($testId, $parentId)
    {
        return $this-> where("testId = {$testId} and parentId = {$parentId}")-> count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE `testId` = ? and `parentId` = ?";
//        return $this->getConnection()->fetchColumn($sql, array($testId, $parentId));
    }

    public function getItemsCountByTestIdAndQuestionType($testId, $questionType)
    {
        return $this-> where("testId = {$testId} and questionType = '{$questionType}'")-> count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE `testId` = ? and `questionType` = ? ";
//        return $this->getConnection()->fetchColumn($sql, array($testId, $questionType));
    }

    public function deleteItemByIds(array $ids)
    {
        if(empty($ids)){ 
            return array(); 
        }
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
        $marks = implode(',',$ids);
        $map['id'] = array("in" ,$marks);
        return $this-> where($map)-> delete();
//        $sql ="DELETE FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->getConnection()->executeUpdate($sql, $ids);
    }

    public function updateItemsMissScoreByPaperIds(array $ids, $missScore)
    {
        if(empty($ids)){ 
            return array(); 
        }
        $params = array_merge(array($missScore), $ids);
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
        $marks = implode(',',$ids);
        $map['testId'] = array("in" ,$marks);
        return $this-> where($map)-> setField("missScore",$missScore);
//        $sql ="UPDATE {$this->tableName} SET missScore = ? WHERE testId IN ({$marks});";
//        return $this->getConnection()->executeUpdate($sql, $params);
    }
    public function getTestpaperItem($id){
        $map['testId'] =$id;
        return $this-> where($map)->select();
    }
    //获取简答题数量
    public function getTestpapertItemCount($testId){
        $map = array("testId"=>$testId,"questionType"=>'essay');
        return $this->where($map)->count();
    }
    
    public function getTestpaperItemByQuestionId($map){
        $option = array(
            'testId'     => '',
            'questionId' => ''
        );
        $map = array_merge($option,$map);
        return $this->where($map)->find();
    }
    public function addEachQuestionScore($map){
        
        $arrId = $this->query("select id from testpaper_item where questionType = '".$map['questionType']."' and testId=".$map['testId']);
        foreach (explode(',',$map['score'])  as $k=>$v){ 
             $arrId[$k]['score'] = $v;
        }
        foreach ($arrId as $arr){
             $re = $this->query("update testpaper_item set score=".$arr['score']." where testId = ".$map['testId']." and questionType = '".$map['questionType']."' and id=".$arr['id']);
        }
        return $re;
    }
    
    //xf
    public function getTestpaperItemQuestions($testId){
       return $this->query("select id,questionType as Questions,COUNT(id)  as QuestionsNumber,SUM(score) as totalScore,score  from (select * from testpaper_item where testId = $testId) as tt GROUP BY questionType");
    }
    public function getTestpaperItemAllQuestions($testId){
        return $this->query("select *,count(*) as itemcount,sum(score) as typescore from testpaper_item where testId={$testId} GROUP BY questionType");
    }
    public function updateTestpaperEveryItemScore($map){
        return $this->query("update testpaper_item set score =".$map['score']." where id=".$map['id']);
    }
    public function getTestpaperItemQuestionIds($testId){
        return $this->field('questionId')->where("testId=$testId")->select();
    }
    public function searchTestpapersItem($testId, $orderBy, $start, $limit)
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
       // $questions = $this-> where($conditions)-> order("$orderBy[0] $orderBy[1]")-> limit($start, $limit)->select();
        $questions = $this->query("SELECT t1.id,t1.testId,t1.questionType,t1.questionId,q1.stem,t1.score from testpaper_item as t1 JOIN question as q1 on t1.questionId = q1.id and t1.testId=$testId limit $start,$limit");
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }
}