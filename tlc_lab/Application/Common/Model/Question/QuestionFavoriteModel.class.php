<?php

/*
 * 数据层
 * @package
 * @author     wanglei@wyzc.com
 * @version    $Id$
 */

namespace Common\Model\Question;

use Common\Model\Common\BaseModel;

class QuestionFavoriteModel extends BaseModel {

	protected $currentDb = CENTER;

    protected $tableName = "question_favorite";

    public function getFavorite($id) {
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->where(array('id' => $id))->find() ? : null;
    }

    public function addFavorite($favorite) {
        $favorite = $this->add($favorite);
        if ($favorite <= 0) {
            E('Insert favorite error.');
        }
        return $this->getFavorite($favorite);
    }

    public function getFavoriteByQuestionIdAndTargetAndUserId($favorite) {
//        $sql = "SELECT * FROM {$this->tableName} WHERE questionId = ? AND target = ? AND userId = ? LIMIT 1";
        $where['questionId'] = $favorite['questionId'];
        $where['target'] = $favorite['target'];
        $where['userId'] = $favorite['userId'];
        return $this->where($where)->find() ? : null;
    }

    public function deleteFavorite($favorite) {
        return $this->where($favorite)->delete();
    }

    public function findFavoriteQuestionsByUserId($id, $start, $limit) {
        $this->filterStartLimit($start, $limit);
//        $sql = "SELECT * FROM {$this->tableName} WHERE `userId` = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
        return $this->where(array('userId' => $id))->order('createdTime DESC')->limit($start, $limit)->select() ? : array();
    }

    public function findFavoriteQuestionsCountByUserId($id) {
//        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE `userId` = ?";

        return $this->where(array('userId' => $id))->count('id');
    }

    public function findAllFavoriteQuestionsByUserId($id, $testid) {
        $map['userId'] = $id;
        if($testid!=0) {
            $map['target'] = "testpaper-" . $testid;
        }
//        $sql = "SELECT * FROM {$this->tableName} WHERE `userId` = ? ";
        return $this->where($map)->select() ? : array();
    }
    
    public function findFavoriteQuestionByUserIdAndQesId($userId,$qesId){
        $map['userId'] = $userId;
        $map['questionId'] = $qesId;
        return $this->where($map)->select() ? : array();
    }

}