<?php

namespace Common\Model\Thread;

use Think\Model;
use Common\Model\Common\BaseModel;

class ThreadModel extends BaseModel {

    protected $tableName = 'course_thread';

    ###########################################################################
    /**
     * 获得问题列表
     * @author lvwulong 2016.4.5
     */

    public function getThreads($tables, $fields, $map, $page, $preNum) {
        $this->table('course_thread as t')->join("course as c on t.courseId = c.id");
        # 连表查询
        if ($tables && is_array($tables) && count($tables) > 0) {
            foreach ($tables as $k => $v) {
                switch ($v) {
                    case 'post':
                        $this->join("course_thread_post as p on t.id = p.threadId AND t.courseId = p.courseId");
                        break;
                    case 'collect':
                        $this->join("course_thread_collect as clt on t.id = clt.threadId AND t.courseId = clt.courseId");
                        break;
                }
            }
        }
        # 附加条件
        $map = $map && trim($map) ? (trim($map) . ' AND ') : '';
        # 附加显示字段
        $fields = $fields && trim($fields) ? (trim($fields) . ',') : '';
        # 附加排序规则
        $orderby = $tables && in_array('collect', $tables) ? 'clt.createdTime desc' : (in_array('post', $tables) ? 'p.createdTime desc' : 't.createdTime desc');
        $data = $this->where("{$map} t.type = 'question'")
                ->field("{$fields} t.id as questionId, t.isClosed as isDelete, t.audioId, t.userId as uid, t.title, t.content, t.bestAnswerId, t.createdTime as ctime,"
                        . "t.hitNum, t.isElite as isFine, t.grabMode as isGrab, t.lockedUId as lockedUid,"
                        . "c.id as courseId, c.title as courseName, c.subtitle as courseTitle, c.rating score")
                ->order($orderby)
                ->group("t.id")
                ->page($page, $preNum)
                ->select();
        return $data && is_array($data) && count($data) > 0 ? $data : array();
    }

    //获取话题通过ID
    public function getThreadById($threadId){
        return $this->where(["id" => $threadId])->find() ?: null;
    }

    /**
     * 获得问题列表统计
     * @author lvwulong 2016.4.5
     */
    public function getThreadsCount($tables, $map) {
        $this->table('course_thread as t')->join("course as c on t.courseId = c.id");
        # 连表查询
        if ($tables && is_array($tables) && count($tables) > 0) {
            foreach ($tables as $k => $v) {
                switch ($v) {
                    case 'post':
                        $this->join("course_thread_post as p on t.id = p.threadId AND t.courseId = p.courseId");
                        break;
                    case 'collect':
                        $this->join("course_thread_collect as clt on t.id = clt.threadId AND t.courseId = clt.courseId");
                        break;
                }
            }
        }
        # 附加条件
        $map = $map && trim($map) ? (trim($map) . ' AND ') : '';
        # 统计结果
        $count = $this->where("{$map} t.type = 'question'")->count();
        return $count && intval($count) > 0 ? intval($count) : 0;
    }
    ###########################################################################

	
    public function getThread($id, $isClosed = 0) {
         return $this->where("id = {$id} and isClosed = {$isClosed}")->find() ? : array();
    }

    public function getThreadQuestion($map, $order, $p, $preNum) {
        return $this->table('course_thread a')
                        ->join('course b on a.courseId = b.id')
                        ->where($map)
                        ->field('a.id, a.courseId, a.audioId,a.userId uid,a.title, a.content, a.createdTime ctime,a.postNum, a.hitNum, b.title as treename,b.subtitle as treetitle,b.rating score')
                        ->order($order . ' a.id desc')
                        ->page($p, $preNum)
                        ->select();
    }

    public function getFindThread($askid) {
        return $this->table('course_thread a')
                        ->join('course b on a.courseId = b.id')
                        ->field('a.id,a.title,a.audioId, a.courseId treeid ,a.hitNum, a.createdTime ctime ,a.userId uid,a.content count_sup,b.title as treename,b.subtitle as treetitle,b.rating score, isClosed')
                        ->where(array('a.id' => $askid))
                        ->find();
    }

    public function getThreadQuestionCount($map) {
        return $this->table('course_thread a')->join('course b on a.courseId = b.id')->where($map)->count();
    }

    public function findLatestThreadsByType($type, $start, $limit) {
        return $this->where("type = '{$type}'")->order("createdTime DESC")->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE type = ? ORDER BY createdTime DESC";
//        return $this->getConnection()->fetchAll($sql, array($type)) ? : array();
    }

    public function findEliteThreadsByType($type, $status, $start, $limit) {
        return $this->where("type = '{$type}' and isElite = {$isElite}")->order("createdTime DESC")->limit($start, $limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE type = ? AND isElite = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($type, $status)) ? : array();
    }

    public function findThreadsByUserIdAndType($userId, $type) {
        return $this->where("type = '{$type}' and userId = {$userId}")->order("createdTime DESC")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND type = ? ORDER BY createdTime DESC";
//        return $this->getConnection()->fetchAll($sql, array($userId, $type));
    }

    public function findThreadsByCourseId($courseId, $orderBy, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $orderBy = join(' ', $orderBy);
        return $this->where("courseId = {$courseId}")->order($orderBy)->limit($start, $limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? ORDER BY {$orderBy} LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($courseId)) ? : array();
    }

    public function findThreadsByCourseIdAndType($courseId, $type, $orderBy, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $orderBy = join(' ', $orderBy);
        return $this->where("courseId = {$courseId} and type = '{$type}'")->order($orderBy)->limit($start, $limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? AND type = ? ORDER BY {$orderBy} LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($courseId, $type)) ? : array();
    }

    public function getThreadsCountByType($conditions){
        $builder = $this->createThreadSearchQueryBuilder($conditions)->select('id');
        $results = $builder->execute()->fetchAll() ? : array();
        return count($results);
    }

    public function searchThreads($conditions, $orderBys, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $builder = $this->createThreadSearchQueryBuilder($conditions)
                ->select('*')
                ->setFirstResult($start)
                ->setMaxResults($limit);


        foreach ($orderBys as $orderBy) {
            $builder->addOrderBy($orderBy[0], $orderBy[1]);
        }
        return $builder->execute()->fetchAll() ? : array();
    }

    public function searchThreadCount($conditions) {
        $builder = $this->createThreadSearchQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchThreadCountInCourseIds($conditions) {
        $builder = $this->createThreadSearchQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchThreadInCourseIds($conditions, $orderBys, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $builder = $this->createThreadSearchQueryBuilder($conditions)
                ->select('*')
                ->setFirstResult($start)
                ->setMaxResults($limit);
        foreach ($orderBys as $orderBy) {
            $builder->addOrderBy($orderBy[0], $orderBy[1]);
        }
        return $builder->execute()->fetchAll() ? : array();
    }

    private function createThreadSearchQueryBuilder($conditions) {
        if (isset($conditions['title'])) {
            $conditions['title'] = "%{$conditions['title']}%";
        }

        if (isset($conditions['content'])) {
            $conditions['content'] = "%{$conditions['content']}%";
        }

        $builder = $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, $this->tableName)
                ->andWhere('courseId = :courseId')
                ->andWhere('lessonId = :lessonId')
                ->andWhere('userId = :userId')
                ->andWhere('type = :type')
                ->andWhere('isStick = :isStick')
                ->andWhere('isElite = :isElite')
                ->andWhere('isClosed = :isClosed')
                ->andWhere('postNum = :postNum')
                ->andWhere('postNum > :postNumLargerThan')
                ->andWhere('title LIKE :title')
                ->andWhere('createdTime > :startTime')
                ->andWhere('createdTime < :endTime')
                ->andWhere('isCourseMember = :isCourseMember')
                ->andWhere('teacherPostNum = :teacherPostNum')
                ->andWhere('content LIKE :content');
        if (isset($conditions['bestAnswerId'])) {
            if ($conditions['bestAnswerId'][0] == 'eq') {
                $builder->andStaticWhere("bestAnswerId = {$conditions['bestAnswerId'][1]}");
            }
            if ($conditions['bestAnswerId'][0] == 'neq') {
                $builder->andStaticWhere("bestAnswerId <> {$conditions['bestAnswerId'][1]}");
            }
        }
        if (isset($conditions['courseIds'])) {
            $courseIds = array();
            foreach ($conditions['courseIds'] as $courseId) {
                if (ctype_digit($courseId)) {
                    $courseIds[] = $courseId;
                }
            }
            if ($courseIds) {
                $courseIds = join(',', $courseIds);
                $builder->andStaticWhere("courseId IN ($courseIds)");
            }
        }

        if (isset($conditions['ids'])) {
            $threadIds = array();
            foreach ($conditions['ids'] as $threadId) {
                if (!empty($threadId)) {
                    $threadIds[] = $threadId;
                }
            }

            if ($threadIds) {
                $threadIds = join(',', $threadIds);
                $builder->andStaticWhere("id IN ($threadIds)");
            }
        }
        return $builder;
    }

    public function addThread($fields) {
        $r = $this->add($fields);
        if (!$r)
            E("Insert course thread error.".mysql_error());
        return $this->getThread($r);
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert course thread error.');
//        }
//        return $this->getThread($this->getConnection()->lastInsertId());
    }

    public function updateThread($id, $fields) {
        $this->where("id = {$id}")->save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getThread($id);
    }

    public function threadFieldsInc($id, $fields) {
        $this->where("id = {$id}")->setInc($fields);
    }

    public function deleteThread($id) {
        $this->where("id = {$id}")->save(array('isClosed' => 1, 'closedTime' => time()));
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function recoverThread($id) {
        $this->where("id = {$id}")->save(array('isClosed' => 0));
    }

    public function restoreThread($id) {
        $this->where("id = {$id}")->save(array('isClosed' => 0));
    }

    public function waveThread($id, $field, $diff) {
        $fields = array('postNum', 'hitNum', 'followNum');
        if (!in_array($field, $fields)) {
            E(sprintf("%s字段不允许增减，只有%s才被允许增减", $field, implode(',', $fields)));
        }
        $diff = ltrim($diff, '+');
        return $this->where(array('id' => $id))->setInc($field, $diff);
//        $sql = "UPDATE {$this->tableName} SET {$field} = {$field} + ? WHERE id = ? LIMIT 1";
//        return $this->getConnection()->executeQuery($sql, array($diff, $id));
    }

    public function myAsk($uid, $p, $preNum) {
        return $this
                        ->table($this->tableName . " as a")
                        ->join("course b on a.courseId = b.id")
                        ->where("a.userId = {$uid} and a.type='question'")
                        ->field("a.postNum ,a.audioId, a.content, a.content ans_count, a.hitNum, a.createdTime ctime,a.id as threadId,a.title,a.userId uid,a.courseId,b.title treename,b.subtitle treetitle,b.rating score")
                        ->page($p, $preNum)
                        ->order('a.createdTime desc')
                        ->select() ? : array();
    }

    public function myAnswer($uid, $p, $preNum) {
        return $this
                        ->table($this->tableName . " as b")
                        ->join('course_thread_post a on a.threadId = b.id')
                        ->join("course c on b.courseId = c.id")
                        ->field("b.id threadId, b.content, b.courseId, b.postNum, b.hitNum, b.audioId,b.isClosed qes_state, b.createdTime ctime,b.title, b.userId uid,b.id,c.title as treename,c.subtitle as treetitle,c.rating score")
                        ->group('b.id')
                        ->where("a.userId = {$uid} and b.isClosed = 0 and b.type='question'")
                        ->page($p, $preNum)
                        ->order('a.id desc')
                        ->select() ? : array();
    }

    public function myAnswerCount($uid) {
        $myAnswer = $this
                        ->table($this->tableName . " as b")
                        ->join('course_thread_post a on a.threadId = b.id')
                        ->join("course c on b.courseId = c.id")
                        ->field("b.id")
                        ->group('b.id')
                        ->where("a.userId = {$uid} and b.isClosed = 0 and b.type='question'")
                        ->select() ? : array();
        return count($myAnswer) ? : 0;
    }

    #==============================================#


    /*
     * 获取提问锁定用户
     */

    public function getLockedUId($threadId) {
        $thread = $this->where("id = {$threadId}")->find();
        if (empty($thread) || $thread['isClosed'])
            E('问题不存在!');
        return $thread['lockedUId'];
    }

    /**
     * 判断用户是否可回答
     * @threadId 提问ID
     * @uid 用户ID
     * @author Yao 2016/3/18
     */
    public function isThreadPost($threadId, $uid) {
        $thread = $this->where("id = {$threadId}")->find();
        if (empty($thread) || $thread['isClosed'])
            E('问题不存在!');
        if (!$thread['grabMode'] || isGranted($uid) == 'ROLE_USER') {
            return true;
        }
        if (!$thread['unlockTime'] > time()) {
            if ($thread['lockedUId'] == $uid) {
                return true;
            }
            return false;
        }
    }

    /*
     * 是否是抢答模式
     */

    public function isGrabMode($threadId) {
        return $this->where("id = {$threadId}")->field('grabMode')->find();
    }

    /*
     * 提问上锁 
     */

    public function lockThread($threadId, $fields) {
        return $this->where("id = {$threadId}")->save($fields);
    }

    /*
     * 设置最佳答案
     */

    public function setBestAnswerId($conditions, $id) {
        $option = array(
            'courseId' => 0,
            'id' => 0
        );
        $conditions = array_merge($option, $conditions);

        return $this->where($conditions)->save(array('bestAnswerId' => $id));
    }

    /*
     * 获取老师回答数
     */

    public function getTeacherPostNum($threadId) {
        return $this->where("id = {$threadId}")->field("teacherPostNum")->find();
    }

    /*
     * 设置老师回答数
     * $isInc 是否自增 (1=>自增,0=>自减)
     */

    public function setTeacherPostNum($threadId, $isInc) {
        if ($isInc) {
            return $this->where("id = {$threadId}")->setInc("teacherPostNum");
        } else {
            return $this->where("id = {$threadId}")->setDec("teacherPostNum");
        }
    }

    #获取根据日期分组的提问

    public function getThreadsGroupByDate($conditions) {
        $time = "date_format(FROM_UNIXTIME(createdTime),'%Y-%m-%d')";
        return $this->where($conditions)
                        ->field("{$time} as time,count(*) as count")
                        ->group($time)
                        ->select();
    }

    public function isThreadUser($threadId, $userId) {
        return $this->where("id = {$threadId} and userId ={$userId}")->find();
    }

}