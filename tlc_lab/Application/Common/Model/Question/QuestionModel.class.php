<?php

/*
 * 数据层
 * @package
 * @author     wanglei@wyzc.com
 * @version    $Id$
 */

namespace Common\Model\Question;

use Common\Model\Common\BaseModel;

class QuestionModel extends BaseModel {

    protected $tableName = 'question';
//	public function getConnection() {
//		return $this;
//	}

    private $serializeFields = array(
        'answer' => 'json',
        'metas' => 'json',
    );

    public function getQuestion($id) {
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        $question = $this->where(array('id' => $id))->find();
        return $question ? $this->createSerializer()->unserialize($question, $this->serializeFields) : array();
    }

    public function getQuestions($map) {
        $question = $this->where($map)->select();
        return $question ? $this->createSerializer()->unserialize($question, $this->serializeFields) : null;
    }

    public function findQuestionsByIds(array $ids) {
        if (empty($ids)) {
            return array();
        }
        $marks = implode(',', $ids);
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
        $where['id'] = array('in', $marks);
        $questions = $this->where($where)->select() ? : array();
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }

    public function findQuestionsByParentId($id) {
//        $sql ="SELECT * FROM {$this->tableName} WHERE parentId = ? ORDER BY createdTime ASC";
        $questions = $this->where(array('parentId' => $id))->order('createdTime ASC')->select() ? : array();
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }

    public function findQuestionsbyTypes($types, $start, $limit) {
        if (empty($types)) {
            return array();
        }
//        $sql ="SELECT * FROM {$this->tableName} WHERE `parentId` = 0 AND type in ({$types})  LIMIT {$start},{$limit}";
        $where['parentId'] = 0;
        $where['type'] = array('in', $types);
        $questions = $this->where($where)->limit($start, $limit)->select() ? : array();
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }

    public function findQuestionsByTypesAndExcludeUnvalidatedMaterial($types, $start, $limit) {
        if (empty($types)) {
            return array();
        }

        $sql = "SELECT * FROM {$this->tableName} WHERE (`parentId` = 0) AND (`type` in ({$types})) AND ( not( `type` = 'material' AND `subCount` = 0 )) LIMIT {$start},{$limit} ";
        $questions = $this->query($sql);
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }

    public function findQuestionsByTypesAndSourceAndExcludeUnvalidatedMaterial($types, $start, $limit, $questionSource, $courseId, $lessonId) {
        if (empty($types)) {
            return array();
        }
        if ($questionSource == 'course') {
            $target = 'course-' . $courseId;
        } else if ($questionSource == 'lesson') {
            $target = 'course-' . $courseId . '/lesson-' . $lessonId;
        }
        $sql = "SELECT * FROM {$this->tableName} WHERE (`parentId` = 0) AND  (`type` in ($types)) AND ( not( `type` = 'material' AND `subCount` = 0 )) AND (`target` like '{$target}/%' OR `target` = '{$target}') LIMIT {$start},{$limit} ";

        $questions = $this->query($sql);
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }

    public function findQuestionsCountbyTypes($types) {
//        $sql ="SELECT count(*) FROM {$this->tableName} WHERE type in ({$types})";
        $where['type'] = array('in', $types);
        return $this->where($where)->count();
    }

    public function findQuestionsCountbyTypesAndSource($types, $questionSource, $courseId, $lessonId) {
        if ($questionSource == 'course') {
            $target = 'course-' . $courseId;
        } else if ($questionSource == 'lesson') {
            $target = 'course-' . $courseId . '/lesson-' . $lessonId;
        }
//        $sql ="SELECT count(*) FROM {$this->tableName} WHERE  (`parentId` = 0) AND (`type` in ({$types})) AND (`target` like '{$target}/%' OR `target` = '{$target}')";
        $where['parentId'] = 0;
        $where['type'] = array('in', $types);
        $where['_string'] = "`target` like '{$target}%' OR `target` = '{$target}'";
        return $this->where($where)->count();
    }

    public function findQuestionsByParentIds(array $ids) {
        if (empty($ids)) {
            return array();
        }
        $marks = implode(',', $ids);
//        $sql ="SELECT * FROM {$this->tableName} WHERE parentId IN ({$marks});";
        $where['parentId'] = array('in', $marks);
        $questions = $this->where($where)->select();
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }

    public function searchQuestions($conditions, $orderBy, $start, $limit, $orderByFindSet = false) {
//        print_r($conditions);die;
        $this->filterStartLimit($start, $limit);
        $this->checkOrderBy($orderBy, array('createdTime'));
//        if($conditions["targetPrefix"]){
//            $conditions["target"] = array("like","%".$conditions["targetPrefix"]."%");
//            unset($conditions["targetPrefix"]);
//        }
        if (!isset($conditions['isRecycle'])) {
            $conditions['isRecycle'] = 0;
        }
        if ($orderByFindSet) {
            $builder = $this->_createSearchQueryBuilder($conditions)
                    ->select('*')
                    ->setFirstResult($start)
                    ->setMaxResults($limit)
                    ->orderByFindSet('id', $conditions['ids']);
        } else {
            $builder = $this->_createSearchQueryBuilder($conditions)
                    ->select('*')
                    ->setFirstResult($start)
                    ->setMaxResults($limit)
                    ->orderBy($orderBy[0], $orderBy[1]);
        }

        #->orderBy($orderBy[0], $orderBy[1]);

        $questions = $builder->execute()->fetchAll() ? : array();

//        $questions = $this->where($conditions)->order("{$orderBy[0]} $orderBy[1]")->limit($start,$limit)->select();
        return $this->createSerializer()->unserializes($questions, $this->serializeFields);
    }

    //题目搜索
    public function getSearchQuestion($conditions) {
        return $this->where($conditions)->order('type desc')->select();
    }

    public function searchQuestionsCount($conditions) {
//        if(isset($conditions['targetPrefix'])){
//            $conditions["target"] = array("like","%".$conditions['targetPrefix']."%");
//            unset($conditions['targetPrefix']);
//        }
        if (!isset($conditions['isRecycle'])) {
            $conditions['isRecycle'] = 0;
        }
        $builder = $this->_createSearchQueryBuilder($conditions)
                ->select('COUNT(id)');

        return $builder->execute()->fetchColumn(0);

//	    $info  =  $this->where($conditions)->count('id');
//          
//            return $info;
    }

    public function findQuestionsCountByParentId($parentId) {
//        $sql ="SELECT count(*) FROM {$this->tableName} WHERE parentId = ?";
        return $this->where(array('parentId' => $parentId))->count();
    }

    public function addQuestion($fields) {
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $affected = $this->add($fields);
        if ($affected <= 0) {
            E('Insert question error.');
        }
        return $this->getQuestion($affected);
    }

    public function updateQuestion($id, $fields) {
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $this->where(array('id' => $id))->save($fields);
        return $this->getQuestion($id);
    }

    public function deleteQuestion($id) {
        return $this->where(array('id' => $id))->delete();
    }

    /**
     * 放入回收站
     * @param type $id
     */
    public function putRecycle($id) {
        return $this->where(array('id' => $id))->save($arr = array(
                    'isRecycle' => 1,
                    'updatedTime' => time(),
        ));
    }

    public function recoveryRecycle($map) {
        return $this->where($map)->save(array('isRecycle' => 0));
    }

    public function deleteQuestionsByParentId($id) {
//        $sql = "DELETE FROM {$this->tableName} WHERE parentId = ?";
        return $this->where(array('parentId' => $id))->delete();
    }

    public function updateQuestionCountByIds($ids, $status) {
        if (empty($ids)) {
            return array();
        }
        $marks = implode(',', $ids);
//        $sql = "UPDATE {$this->tableName} SET {$status} = {$status}+1 WHERE id IN ({$marks})";
        $where['id'] = array('in', $marks);
        return $this->where($where)->setInc($status);
    }

    public function getQuestionCountGroupByTypes($conditions) {
        $sqlConditions = array();
        $sql = "";
        if (isset($conditions["types"])) {
            $marks = str_repeat('?,', count($conditions["types"]) - 1) . '?';
            $sql .= " AND type IN ({$marks}) ";
            $sqlConditions = array_merge($sqlConditions, $conditions["types"]);
        }
        if (isset($conditions["targets"])) {
            $targetMarks = str_repeat('?,', count($conditions["targets"]) - 1) . '?';
            $sqlConditions = array_merge($sqlConditions, $conditions["targets"]);
            $sql .= " AND target IN ({$targetMarks}) ";
        }
        if (isset($conditions["courseId"])) {
            $sql .= " AND (target='course-{$conditions['courseId']}' or target like 'course-{$conditions['courseId']}/%') ";
        }
        $sql = "SELECT COUNT(*) AS questionNum, type FROM {$this->tableName} WHERE parentId = '0' {$sql} GROUP BY type ";
        return $this->query($sql);
    }

    public function setQuestionsCallNumber($arr, $tag = '') {
        if ($tag == 'callNumber') {
            $field = $tag;
        } elseif ($tag == 'releaseNumber') {
            $field = $tag;
        } else {
            return false;
        }
        $map['id'] = array('in', $arr);
        return $this->where($map)->setInc($field, 1);
    }

    private function _createSearchQueryBuilder($conditions) {
        $conditions = array_filter($conditions, function($value) {
                    if ($value === '' or is_null($value)) {
                        return false;
                    }
                    return true;
                });

        if (isset($conditions['targetPrefix'])) {
            $conditions['targetLike'] = "{$conditions['targetPrefix']}/%";
            unset($conditions['target']);
        }

        if (isset($conditions['stem'])) {
            $conditions['stem'] = "%{$conditions['stem']}%";
        }
        $builder = $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, 'questions');

        if (isset($conditions['ids'])) {
            $idsarr = array();
            foreach ($conditions['ids'] as $id) {
                if (empty($id)) {
                    continue;
                }
                $idsarr[] = $id;
            }
            if (!empty($idsarr)) {
                $ids = implode(',', $idsarr);
                $builder->andStaticWhere("id IN ({$ids})");
            }
            else
                $builder->andStaticWhere("id IN ('')");
        }
        if (isset($conditions['notids'])) {
            $idsarr = array();
            foreach ($conditions['notids'] as $id) {
                if (empty($id)) {
                    continue;
                }
                $idsarr[] = $id;
            }
            if (!empty($idsarr)) {
                $ids = implode(',', $idsarr);
                $builder->andStaticWhere("id NOT IN ({$ids})");
            }
        }
        if (isset($conditions['targets']) and is_array($conditions['targets'])) {
            $targets = array();
            foreach ($conditions['targets'] as $target) {
                if (empty($target)) {
                    continue;
                }
                if (preg_match('/^[a-zA-Z0-9_\-\/]+$/', $target)) {
                    $targets[] = $target;
                }
            }
            if (!empty($targets)) {
                $targets = "'" . implode("','", $targets) . "'";
                $builder->andStaticWhere("target IN ({$targets})");
            }
        } else {
            $builder->andWhere('target = :target')
                    ->andWhere('target = :targetPrefix OR target LIKE :targetLike');
        }

        $builder->andWhere('parentId = :parentId')
                ->andWhere('type = :type')
                ->andWhere('isRecycle = :isRecycle')
                ->andWhere('stem LIKE :stem');

        if (isset($conditions['types'])) {
            $types = array();
            foreach ($conditions['types'] as $type) {
                if (empty($type)) {
                    continue;
                }
                if (preg_match('/^[a-zA-Z0-9_\-\/]+$/', $type)) {
                    $types[] = $type;
                }
            }
            if (!empty($types)) {
                $types = "'" . implode("','", $types) . "'";
                $builder->andStaticWhere("type IN ({$types})");
            }
        }
        if (isset($conditions['difficulty'])) {
            $difficulty = array();
            foreach ($conditions['difficulty'] as $diff) {
                if (empty($diff)) {
                    continue;
                }
                if (preg_match('/^[a-zA-Z0-9_\-\/]+$/', $diff)) {
                    $difficulty[] = $diff;
                }
            }
            if (!empty($difficulty)) {
                $difficulty = "'" . implode("','", $difficulty) . "'";
                $builder->andStaticWhere("difficulty IN ({$difficulty})");
            }
        }

        if (isset($conditions['excludeIds']) and is_array($conditions['excludeIds'])) {
            $excludeIds = array();
            foreach ($conditions['excludeIds'] as $id) {
                $excludeIds[] = intval($id);
            }

            if (!empty($excludeIds)) {
                $builder->andStaticWhere("id NOT IN (" . implode(',', $excludeIds) . ")");
            }
        }

        if (isset($conditions['excludeUnvalidatedMaterial']) and ($conditions['excludeUnvalidatedMaterial'] == 1)) {
            $builder->andStaticWhere(" not( type = 'material' and subCount = 0 )");
        }
        return $builder;
    }

}
