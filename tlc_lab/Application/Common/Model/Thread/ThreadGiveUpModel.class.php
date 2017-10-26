<?php

namespace Common\Model\Thread;

use Common\Model\Common\BaseModel;

class ThreadGiveUpModel extends BaseModel {

    protected $tableName = 'course_thread_giveup';

    public function getGiveUp($id) {
        return $this->where("id = {$id}")->find();
    }

    public function addGiveUp($fields) {
        return $this->add($fields);
    }

    public function findGiveUpCounts($conditions) {
         return $this->where($conditions)->count();
    }

    public function deleteThreadCollect($conditions) {
        $option = array(
            'userId' => 0,
            'threadId' => 0
        );
        $conditions = array_merge($option, $conditions);
        return $this->where($conditions)->delete();
    }

    public function searchThreadCollectionCounts($conditions) {
        return $this->where($conditions)->count();
    }

    public function searchThreadCollection($conditions, $orderBy, $start, $limit) {
        return $this->where($conditions)
                        ->order("$orderBy[0] $orderBy[1]")
                        ->limit($start, $limit)
                        ->select();
    }

    /*
     * 根据提问Id 获取收藏
     */

    public function getThreadCollectByThreadId($courseId, $threadId, $userId) {
        $where = array(
            'courseId' => $courseId,
            'threadId' => $threadId,
            'userId' => $userId
        );
        return $this->where($where)->find();
    }

    /*
     * 获取用户收藏的问题列表
     * edit by lvwulong 2016.4.1
     */

    public function getMyThreadCollects($map, $start, $limit) {
        $data = $this->table("course_thread_collect b")
                ->join("course_thread a on b.threadId = a.id")
                ->join("course c on a.courseId = c.id")
                ->field('a.id, a.courseId, a.audioId, a.userId uid, a.title, a.content, a.createdTime ctime, a.postNum, a.hitNum, c.title as treename, c.subtitle as treetitle, c.rating score, a.isElite, a.isClosed as questionStatus')
                ->where($map)
                ->order('a.createdTime desc')
                ->group('a.id')
                ->page($start, $limit)
                ->select();
        return $data && is_array($data) && count($data) > 0 ? $data : array();
    }

    /*
     * 获取用户收藏的问题数量
     * edit by lvwulong 2016.4.1
     */

    public function getMyThreadCollectsCount($map) {
        return $this->table("course_thread_collect b")
                        ->join("course_thread a on b.threadId = a.id")
                        ->join("course c on a.courseId = c.id")
                        ->where($map)
                        ->group('a.id')
                        ->count();
    }

}