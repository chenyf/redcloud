<?php

namespace Common\Model\Thread;

use Think\Model;
use Common\Lib\ArrayToolkit;
use Common\Model\Common\BaseModel;

class ThreadPostModel extends BaseModel {

    protected $tableName = 'course_thread_post';

    ###########################################################################
    /**
     * 获得问题回答/回复列表
     * @author lvwulong 2016.4.5
     */
    public function getAnswerList($threadId, $page, $preNum){
        # 回答列表
        $list = $this->table('course_thread_post as p')
            ->join("course_thread as t on t.id = p.threadId AND t.courseId = p.courseId")
            ->where("p.type='post' AND p.pid=0 AND t.id={$threadId}")
            ->field("t.id questionId,t.bestAnswerId,p.id answerId,p.role,p.audioId,p.createdTime ctime,"
                   ."p.userId uid,p.isBestAnswer isBest,p.content,p.score")
            ->order('p.isBestAnswer desc,p.createdTime asc')
            ->group('p.userId')
            ->page($page, $preNum)
            ->select();
        $tmp = $this;
        return $list && is_array($list) && ($x = array_map(function($y)use($threadId){
            $y['postNum'] = ($x = $this->getAnswerPostCount($threadId, $y['answerId'])) ? ("" . ($x+1)) : "1";
            return $y;
        }, $list)) ? $x : array();
    }
    /**
     * 获得问题回答列表统计
     * @author lvwulong 2016.4.5
     */
    public function getAnswerCount($threadId){
        $count = $this->where("type='post' AND pid=0 AND threadId={$threadId}")->group('userId')->select();
        return $count && ($x = count($count)) > 0 ? $x : 0;
    }
    /**
     * 获得问题回答的回复列表
     * @author lvwulong 2016.4.5
     */
    public function getAnswerPostList($threadId, $answerId, $page, $preNum){
        $answerUid = $this->field('userId uid')->where("threadId={$threadId} AND id={$answerId}")->find();
        $answerUid = intval($answerUid['uid']);
        $listTmp = $this->table('course_thread_post as p')
             ->join("course_thread as t on t.id=p.threadId AND t.courseId=p.courseId")
             ->field("t.id questionId,p.id,p.type,p.id answerId,p.pid,p.role,p.audioId,p.createdTime ctime,"
                   ."p.userId uid,p.isBestAnswer isBest,p.content,p.score")
             ->where("t.id={$threadId}")
             ->order('p.createdTime asc')
             ->group('p.id')
             ->select();
        $listTmp = $listTmp && is_array($listTmp) ? $listTmp : array();
        $list = $listTmp ? ArrayToolkit::getTreeNodeList($listTmp, $answerId) : array();
        $listTmpA = array_filter($listTmp, function($x)use(&$list,$answerUid, $answerId){
            $flag = ($answerUid == intval($x['uid']) && intval($x['id']) != intval($answerId) && intval($x['pid']) === 0) ? true : false;
            if($flag){
                $list[] = $x;
                return true;
            }else{
                return false;
            }
        });
        foreach($listTmp as $k => $v){
            $tmp = ArrayToolkit::getTreeNodeList($listTmp, $v['id']);
            $list = array_merge_recursive($list, $tmp);
        }
        $list = ArrayToolkit::sort2Array(array(
			'data'    => $list,
			'field'   => 'ctime',
			'asc'     => 1
		));
        $offset = $page && ($x = intval($page)) > 0 ? ($x-1) * $preNum : 0;
        $lastPost = end($list);
        $list = ($x = array_slice($list, $offset, $preNum)) && is_array($x) ? $x : array();
        return array(
            'lastPostId' => $lastPost ? $lastPost['answerId'] : $answerId,
            'list' => $list
        );
    }
    /**
     * 获得问题回答的回复是统计
     * @author lvwulong 2016.4.5
     */
    public function getAnswerPostCount($threadId, $answerId){
        $answerUid = $this->field('userId')->where("threadId={$threadId} AND id={$answerId}")->find();
        $answerUid = intval($answerUid['userId']);
        $list = $this->field('id,pid,userId')->where("threadId={$threadId}")->select();
        # 映射列表
        $mapList = $list && is_array($list) ? $list : array();
        $count = ArrayToolkit::getTreeNodeCount($mapList, $answerId);
        $listTmp = array_filter($list, function($x)use($answerUid, $answerId){
            return ($answerUid == intval($x['userId']) && intval($x['id']) != intval($answerId) && intval($x['pid']) === 0) ? true : false;
        });
        $count += count($listTmp);
        foreach($listTmp as $k => $v){
            $count += ArrayToolkit::getTreeNodeCount($list, $v['id']);
        }
        return $count;
    }
    ###########################################################################
    
    
    public function getPost($id) {
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getCourseThreadPost($askid) {
        return $this->field('id,audioId,threadId  qes_id,userId  ans_uid,createdTime as ctime,isElite good,content `count`')
                ->where(array('threadId' => $askid))
                ->order('isElite desc, id desc')
                ->select();
    }

    public function findPostsByThreadId($threadId, $orderBy, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $orderBy = join(' ', $orderBy);
        return $this->where("threadId = {$threadId}")->order($orderBy)->limit($start, $limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE threadId = ? ORDER BY {$orderBy} LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($threadId)) ? : array();
    }

    public function getPostCountByThreadId($threadId) {
        return $this->where("threadId = {$threadId}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE threadId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($threadId));
    }

    public function getPostCountByuserIdAndThreadId($userId, $threadId) {
        return $this->where("userId = {$userId} and threadId = {$threadId}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE userId = ? AND threadId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($userId,$threadId));
    }

    public function findPostsByThreadIdAndIsElite($threadId, $isElite, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        return $this->where("threadId = {$threadId} and isElite = {$isElite}")->order("createdTime ASC")->limit($start, $limit)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE threadId = ? AND isElite = ? ORDER BY createdTime ASC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($threadId,  $isElite)) ? : array();
    }

    public function addPost(array $post) {
        $r = $this->add($post);
        if (!$r)
            E("Insert course post error.");
        return $this->getPost($r);
//        $affected = $this->getConnection()->insert($this->tableName, $post);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert course post error.');
//        }
//        return $this->getPost($this->getConnection()->lastInsertId());
    }

    public function updatePost($id, array $fields) {
        $this->where("id = {$id}")->save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getPost($id);
    }

    public function deletePost($id) {
        return $this->where("id={$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function deletePostsByThreadId($threadId) {
        return $this->where("threadId = {$$threadId}")->delete();
//        $sql ="DELETE FROM {$this->tableName} WHERE threadId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($threadId));
    }

    /*
     * 设置最佳答案
     * edit by lvwulong 2016.3.31
     */

    public function setBestAnswer($id, $score = 0) {
        $re = $this->where("id = {$id}")->save(array('isBestAnswer' => 1, 'score' => $score));
        if (!$re) {
            E("Insert thread post error.");
        }
        return $this->getPost($id);
    }

    public function searchPosts($conditions, $orederBy, $start, $limit) {
        return $this->where($conditions)
                        ->order("$orederBy[0] $orederBy[1]")
                        ->limit($start, $limit)
                        ->select();
    }

    public function searchPostCounts($conditions) {
        return $this->where($conditions)->count();
    }

    public function searchPostManageCounts($conditions) {
        if (isset($conditions['startTime']) && isset($conditions['endTime'])) {
            $where['b.createdTime'] = array(between, array($conditions['startTime'], $conditions['endTime']));
        }
        if (isset($conditions['courseId'])) {
            $where['b.courseId'] = $conditions['courseId'];
        }
        if (isset($conditions['isCourseMember'])) {
            $where['b.isCourseMember'] = $conditions['isCourseMember'];
        }

        $where['a.userId'] = $conditions['userId'];
        $where['a.type'] = 'post';

        $results = $this->table("course_thread_post as a")
                ->field('a.id')
                ->join("course_thread as b on a.threadId = b.id")
                ->where($where)
                ->group('a.threadId')
                ->select();
        return count($results);
    }

    public function searchPostsManage($conditions, $orderBy, $start, $limit) {

        if (isset($conditions['startTime']) && isset($conditions['endTime'])) {
            $where['b.createdTime'] = array(between, array($conditions['startTime'], $conditions['endTime']));
        }
        if (isset($conditions['courseId'])) {
            $where['b.courseId'] = $conditions['courseId'];
        }
        if (isset($conditions['isCourseMember'])) {
            $where['b.isCourseMember'] = $conditions['isCourseMember'];
        }
        if (isset($orderBy)) {
            $orderBy[0] = "b." . $orderBy[0];
        }

        $where['a.userId'] = $conditions['userId'];
        $where['a.type'] = 'post';

        return $this->table("course_thread_post as a")
                        ->join("course_thread as b on a.threadId = b.id")
                        ->where($where)
                        ->group('a.threadId')
                        ->order("$orderBy[0] $orderBy[1]")
                        ->limit($start, $limit)
                        ->select();
    }

    /*
     * 根据类型获取数量
     */

    public function getPostNumberByType($conditions) {
        if (isset($conditions['startTime']) && isset($conditions['endTime'])) {
            $where['b.createdTime'] = array(between, array($conditions['startTime'], $conditions['endTime']));
        }
        if (isset($conditions['courseId'])) {
            $where['b.courseId'] = $conditions['courseId'];
        }
        if (isset($conditions['isCourseMember'])) {
            $where['b.isCourseMember'] = $conditions['isCourseMember'];
        }
        $where['a.userId'] = $conditions['userId'];
        $where['a.type'] = $conditions['type'];
        return $this->table("course_thread_post as a")
                        ->join("course_thread as b on a.threadId = b.id")
                        ->where($where)
                        ->group("a.threadId")
                        ->select("COUNT(a.id)");
    }

    public function getPostNumByTeacher($conditions) {
        $res = $this->where($conditions)->field('count(threadId) as count')->group('threadId')->select()?:[];
        return count($res);
    }

    public function getPostCountByTeacher($conditions) {
        return $this->where($conditions)->count();
    }

    public function getBestPostNum($conditions) {
        $option = array(
            'userId' => 0,
        );
        $conditions = array_merge($option, $conditions);
        return $this->where($conditions)
                        ->count();
    }

    public function getPostAvgScore($conditions) {
        if (isset($conditions['startTime']) && isset($conditions['endTime'])) {
            $where['b.createdTime'] = array(between, array($conditions['startTime'], $conditions['endTime']));
        }
        if (isset($conditions['courseId'])) {
            $where['b.courseId'] = $conditions['courseId'];
        }
        if (isset($conditions['isCourseMember'])) {
            $where['b.isCourseMember'] = $conditions['isCourseMember'];
        }
        $where['a.userId'] = $conditions['userId'];
        $where['a.type'] = $conditions['type'];
        $where['c.teacherId'] = $conditions['teacherId'];
        return $this->table("course_thread_post as a")
                        ->join("course_thread as b,course_thread_comment as c on a.threadId = b.id = c.threadId")
                        ->where($where)
                        ->group("a.threadId")
                        ->select("avg(c.satisficing) as AvgScore");
    }

    public function getPostBest($conditions) {
        if (isset($conditions['startTime']) && isset($conditions['endTime'])) {
            $where['b.createdTime'] = array(between, array($conditions['startTime'], $conditions['endTime']));
        }
        if (isset($conditions['courseId'])) {
            $where['b.courseId'] = $conditions['courseId'];
        }
        if (isset($conditions['isCourseMember'])) {
            $where['b.isCourseMember'] = $conditions['isCourseMember'];
        }
        $where['a.userId'] = $conditions['userId'];
        $where['a.type'] = $conditions['type'];
        $where['a.isBestAnswer'] = 1;
        return $this->table("course_thread_post as a")
                        ->join("course_thread as b on a.threadId = b.id")
                        ->where($where)
                        ->select("COUNT(a.id)");
    }

    private function _createThreadSearchBuilder($conditions) {
        $builder = $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, $this->tableName)
                ->andWhere('userId = :userId')
                ->andWhere('id < :id')
                ->andWhere('parentId = :parentId')
                ->andWhere('threadId = :threadId');
        return $builder;
    }

    public function getAppendCounts($conditions) {
        $option = array(
            'courseId' => 0,
            'userId' => 0,
            'replyUId' => 0,
            'type' => ''
        );
        $conditions = array_merge($option, $conditions);
        return $this->where($conditions)
                        ->select('COUNT(id)');
    }

    public function getReplyCounts($conditions) {
        return $this->where($conditions)->count();
        echo $this->getLastSql();
        die;
    }

    public function getAvgResponseTime($conditions) {
        return $this->where($conditions)->avg('responseTime');
    }

}