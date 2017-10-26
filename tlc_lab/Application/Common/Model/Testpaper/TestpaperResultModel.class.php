<?php

namespace Common\Model\Testpaper;

use Common\Model\Common\BaseModel;

class TestpaperResultModel extends BaseModel {

    protected $tableName = 'testpaper_result';

    public function getTestpaperResult($id) {
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getTestpaperResultByTestId($testId) {
        return $this->where("testId = {$testId}")->find() ? : null;
    }

    public function getTestpaperResultsByTestId($testId) {
        return $this->where("testId = {$testId}")->select();
    }

    public function findTestpaperResultByTestpaperIdAndUserIdAndActive($testpaperId, $userId) {
        $sql = "SELECT * FROM {$this->tableName} WHERE testId = {$testpaperId} AND userId = {$userId} AND testpaperType=2";
        return $this->getConnection()->fetchAssoc($sql, array($testpaperId, $userId));
    }

    public function findTestPaperResultsByTestIdAndStatusAndUserId($testpaperId, array $status, $userId, $type) {
        $marks = implode(',', $status);
        $map ['status'] = array("in", $marks);
        $map['testId'] = array("eq", $testpaperId);
        $map['userId'] = array("eq", $userId);
        $map['testpaperType'] = array("eq", $type);
        return $this->where($map)->find() ? : null;
    }

    public function findTestPaperResultByUserIdAndTestId($userId,$testId){
        $where['userId'] = $userId;
        $where['testId'] = $testId;
        return $this->where($where)->find() ? : null;
    }

    public function findTestPaperResultsByStatusAndTestIds($ids, $status, $start, $limit) {
        if (empty($ids)) {
            return array();
        }
        $marks = implode(',', $ids);
        array_push($ids, $status);

        $this->filterStartLimit($start, $limit);
        $map ['testId'] = array("in", $marks);
        $map['status'] = array("eq", $status);
        return $this->where($map)->order("endTime desc")->limit($start, $limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE `testId` IN ({$marks}) AND `status` = ? ORDER BY endTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, $ids) ? : array();
    }

    public function findTestPaperResultCountByStatusAndTestIds($ids, $status) {
        if (empty($ids)) {
            return null;
        }
        $marks = implode(',', $ids);
        $map ['testId'] = array("in", $marks);
        $map['status'] = array("eq", $status);
        return $this->where($map)->count("id");
    }

    public function findTestPaperResultsByStatusAndTeacherIds($ids, $status, $start, $limit) {
        if (empty($ids)) {
            return array();
        }
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
        $marks = implode(',', $ids);
        array_push($ids, $status);

        $this->filterStartLimit($start, $limit);
        $map ['checkTeacherId'] = array("in", $marks);
        $map['status'] = array("eq", $status);
        return $this->where($map)->order("endTime desc")->limit($start, $limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE `checkTeacherId` IN ({$marks}) AND `status` = ? ORDER BY endTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, $ids) ? : array();
    }

    public function findTestPaperResultCountByStatusAndTeacherIds($ids, $status) {
        if (empty($ids)) {
            return null;
        }
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
        $marks = implode(',', $ids);
        array_push($ids, $status);
        $map ['checkTeacherId'] = array("in", $marks);
        $map['status'] = array("eq", $status);
        return $this->where($map)->count("id");
//        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE `checkTeacherId` IN ({$marks}) AND `status` = ?";
//        return $this->getConnection()->fetchColumn($sql, $ids);
    }

    public function findTestPaperResultsByUserId($id, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        return $this->where("userId = {$id}")->order("beginTime desc")->limit($start, $limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE `userId` = ? ORDER BY beginTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($id)) ? : array();
    }

    //新版获取
    public function findTestPaperResultsByUserIdNew($id, $start, $limit, $type, $courseId) {
        $map['a.userId'] = $id;
        $map['c.userId'] = $id;
        $map['b.type'] = $type;
        if ($courseId) {
            $map['b.courseId'] = $courseId;
        }
        return $this->table('testpaper_result a')->join('course_class_test b on a.testId=b.testId')->join('course_member c on b.courseId=c.courseId')->where($map)
                        ->field("a.*,b.title,b.show,b.startTime,b.endTime,b.createdTime,b.limit")->order("a.beginTime desc")->limit($start, $limit)->select() ? : array();
    }

    public function findTestPaperResultsCountByUserId($id) {
        return $this->where("userId = {$id}")->count("id");
    }

    //新版获取数量
    public function findTestpaperResultsCountByUserIdNew($id, $type, $courseId) {
        $map['a.userId'] = $id;
        $map['c.userId'] = $id;
        $map['b.type'] = $type;
        if ($courseId) {
            $map['b.courseId'] = $courseId;
        }
        return $this->table('testpaper_result a')->join('course_class_test b on a.testId=b.testId and a.classId=b.classId')->join('course_member c on b.courseId=c.courseId and a.classId=c.classId')->where($map)
                        ->where($map)->count("a.id");
    }

    public function searchTestpaperResults($conditions, $sort, $start, $limit) {
        
    }

    public function findTestpaperResultsOfClass($testId,$status, $start, $limit) {
        if ($status == 'all') {
            $status = ['finished', 'doing', 'submitted'];
        }
        if ($status == 'hasNot') {
            $status = ['doing'];
        }
        if($status == 'finished'){
            $status = ['finished', 'submitted'];
        }
        return $this
                        ->where([
                            'testId' => $testId,
                            'status' => array('IN', $status),
                        ])
                        ->limit($start, $limit)
                        ->select();
    }

    public function findTestpaperResultsCountOfClass($testId, $status) {
        if ($status == 'all') {
            $status = ['finished', 'doing', 'submitted'];
        }
        if ($status == 'hasNot') {
            $status = ['doing'];
        }
        if($status == 'finished'){
            $status = ['finished', 'submitted'];
        }
        return $this->where([
                    'testId' => $testId,
                    'status' => array('IN', $status),
                ])->count('id');
    }

    public function searchTestpaperResultsCount($conditions) {
        return $this-> where($conditions)-> count();
//        $builder = $this->_createSearchQueryBuilder($conditions)
//                ->select('COUNT(id)');
//        return $builder->execute()->fetchColumn(0);
    }

    public function addTestpaperResult($fields) {
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
        $affected = $this->add($fields);
        if ($affected <= 0) {
            E('Insert testpaperResult error.');
        }
        return $affected;
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert testpaperResult error.');
//        }
//        return $this->getTestpaperResult($this->getConnection()->lastInsertId());
    }

    public function updateTestpaperResult($id, $fields) {
        $this->where("id = {$id}")->save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getTestpaperResult($id);
    }

    public function searchTestpapersScore($conditions) {
//        $this-> where($conditions)-> sum("score");
        $builder = $this->_createSearchQueryBuilder($conditions)
                ->select('sum(score)');
        return $builder->execute()->fetchColumn(0);
    }

    private function _createSearchQueryBuilder($conditions) {
        $conditions = array_filter($conditions);

        $builder = $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, $this->tableName)
                ->andWhere('testId = :testId');

        return $builder;
    }

    public function deleteTestpaperResultByTestpaperId($id){
        return $this-> where("testId = {$id}")-> delete();
    }

    protected function getCourseMember() {
        return $this->createService('Course.CourseMember');
    }

}