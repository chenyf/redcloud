<?php

namespace Common\Model\Testpaper;

use Common\Model\Common\BaseModel;

class TestpaperItemResultModel extends BaseModel {

    protected $tableName = "testpaper_item_result";
    private $serializeFields = array(
        'answer' => 'json'
    );

    public function getItemResult($id) {
        $ItemResult = $this->where("id = {$id}")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        $ItemResult = $this->getConnection()->fetchAssoc($sql, array($id));
        return $ItemResult ? $this->createSerializer()->unserialize($ItemResult, $this->serializeFields) : null;
    }

    public function getItemResultByTestpaperIdAndUserId($testResultId,$userId){
        $where['testPaperResultId'] = $testResultId;
        $where['userId'] = $userId;
        $ItemResult = $this->where($where)->find();
        return $ItemResult ? $this->createSerializer()->unserialize($ItemResult, $this->serializeFields) : null;
    }

    public function addItemResult($fields) {
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $affected = $this->add($fields);
        if ($affected <= 0) {
            E("Insert ItemResult error.");
        }
        return $this->getItemResult($affected);
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert ItemResult error.');
//        }
//        return $this->getItemResult($this->getConnection()->lastInsertId());
    }

    public function addItemAnswers($testPaperResultId, $answers, $testPaperId, $userId) {
        if (empty($answers))
            return array();

        $answers = array_map(function($answer) {
                    return json_encode($answer);
                }, $answers);

        $answersForSQL = array();
        $i = 0;
        foreach ($answers as $key => $value) {
            $answersForSQL[$i]['testId'] = (int) $testPaperId;
            $answersForSQL[$i]['testPaperResultId'] = (int) $testPaperResultId;
            $answersForSQL[$i]['userId'] = (int) $userId;
            $answersForSQL[$i]['questionId'] = (int) $key;
            $answersForSQL[$i]['answer'] = $value;
            $i++;
        }

        return $this->addAll($answersForSQL);
    }

    public function addItemResults($testPaperResultId, $answers, $testId, $userId) {
        // if(empty($answers)){ 
        //     return array(); 
        // }
        // $answers = $this->createSerializer()->serializes($answers, $this->serializeFields);
        // $mark = "(".str_repeat('?,', 6)."? )";
        // $marks = str_repeat($mark.',', count($answers) - 1).$mark;
        // $answersForSQL = array();
        // foreach ($answers as $key => $value) {
        //     array_push($answersForSQL, (int)$testId, (int)$testPaperResultId, (int)$userId, (int)$key, $value['status'], $value['score'], $value['answer']);
        // }
        // $sql = "INSERT INTO {$this->tableName} (`testId`, `testPaperResultId`, `userId`, `questionId`, `status`, `score`, `answer`) VALUES {$marks};";
        // return $this->getConnection()->executeUpdate($sql, $answersForSQL);
    }

    //要不要给这三个字段加上索引呢
    public function updateItemAnswers($testPaperResultId, $answers) {
        //事务
        if (empty($answers)) {
            return array();
        }

        $answers = array_map(function($answer) {
                    return json_encode($answer);
                }, $answers);

        $sql = '';
        $answersForSQL = array();

        $this->getConnection()->beginTransaction();
        try {
            foreach ($answers as $key => $value) {
                $data['answer'] = $value;
                $map['questionId'] = $key;
                $map['testPaperResultId'] = $testPaperResultId;
                $this->where($map)->save($data);
//            	$sql = "UPDATE {$this->tableName} set `answer` = ? WHERE `questionId` = ? AND `testPaperResultId` = ?;";
//            	$answersForSQL = array($value, (int)$key, (int)$testPaperResultId); 
//              $this->getConnection()->executeQuery($sql, $answersForSQL);
            }
     
            $this->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateItemResults($answers, $testPaperResultId) {
        //事务
        if (empty($answers)) {
            return array();
        }
        $sql = '';
        $answersForSQL = array();

        $this->getConnection()->beginTransaction();
        try {
            foreach ($answers as $key => $value) {
                $data['status'] = $value['status'];
                $data['score'] = $value['score'];
                $map['questionId'] = $key;
                $map['testPaperResultId'] = $testPaperResultId;
                $this->where($map)->save($data);
//                $sql = "UPDATE {$this->tableName} set `status` = ?, `score` = ? WHERE `questionId` = ? AND `testPaperResultId` = ?;";
//                $answersForSQL = array($value['status'], $value['score'], (int)$key, (int)$testPaperResultId); 
//                $this->getConnection()->executeQuery($sql, $answersForSQL);
            }
            $this->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateItemEssays($answers, $testPaperResultId) {
        //事务
        if (empty($answers)) {
            return array();
        }
        $sql = '';
        $answersForSQL = array();

        $this->getConnection()->beginTransaction();
        try {
            foreach ($answers as $key => $value) {
                $data['status'] = $value['status'];
                $data['teacherSay'] = $value['teacherSay'];
                $data['score'] = $value['score'];
                $map['questionId'] = $key;
                $map['testPaperResultId'] = $testPaperResultId;
                $this->where($map)->save($data);

//                $sql = "UPDATE {$this->tableName} set `score` = ?, `teacherSay` = ?, `status` = ? WHERE `questionId` = ? AND `testPaperResultId` = ?;";
//                $answersForSQL = array($value['score'], $value['teacherSay'], $value['status'], (int)$key, (int)$testPaperResultId); 
//                $this->getConnection()->executeQuery($sql, $answersForSQL);
            }
            $this->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getConnection()->rollback();
            throw $e;
        }
    }

    public function findTestResultsByItemIdAndTestId($questionIds, $testPaperResultId) {
        if (empty($questionIds)) {
            return array();
        }
//            $marks = str_repeat('?,', count($questionIds) - 1) . '?';
        $marks = implode(',', $questionIds);
        $questionIds[] = $testPaperResultId;

        $map['questionId'] = array("in", $marks);
        $map['testPaperResultId'] = array("eq", $testPaperResultId);
        return $this->where($map)->select() ? : array();
//        $sql ="SELECT * FROM {$this->tableName} WHERE questionId IN ({$marks}) AND testPaperResultId = ?";
//        return $this->getConnection()->fetchAll($sql, $questionIds) ? : array();
    }

    public function findTestResultsByTestPaperResultId($testPaperResultId) {
//        $sql = "SELECT * FROM {$this->tableName} WHERE testPaperResultId = ?";
//        $results = $this->getConnection()->fetchAll($sql, array($testPaperResultId));
        $results = $this->where("testPaperResultId = {$testPaperResultId}")->select();
        return $this->createSerializer()->unserializes($results, $this->serializeFields);
    }

    public function findRightItemCountByTestPaperResultId($testPaperResultId) {
        return $this->wherer("testPaperResultId = {$testPaperResultId} AND status = 'right'")->count("id");
//        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE testPaperResultId = ? AND status = 'right' ";
//        return $this->getConnection()->fetchColumn($sql, array($testPaperResultId));
    }

    public function findWrongResultByUserId($id, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        return $this->where("`userId` = {$id} AND `status` in ('wrong')")->limit($start, $limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE `userId` = ? AND `status` in ('wrong') LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($id)) ? : array();
    }

    public function findWrongResultCountByUserId($id) {
        return $this->where("`userId` = {$id} AND `status` in ('wrong')")->count("id");

//        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE `userId` = ? AND `status` in ('wrong')";
//        return $this->getConnection()->fetchColumn($sql, array($id));
    }

    public function deleteItemResultsByTestpaperId($id){
        return $this-> where("testId = {$id}")-> delete();
    }
}