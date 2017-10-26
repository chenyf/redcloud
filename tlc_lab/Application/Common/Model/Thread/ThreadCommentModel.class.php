<?php

namespace Common\Model\Thread;

use Common\Model\Common\BaseModel;

class ThreadCommentModel extends BaseModel {

    protected $tableName = 'course_thread_comment';

    public function getThreadComment($commentId) {
        return $this->where("id = {$commentId}")->find();
    }

    public function findThreadComment($conditions) {
        return $this->where($conditions)->find();
    }

    public function searchCommentCounts($conditions) {
        return $this->where($conditions)->count();
    }

    public function searchComment($conditions, $orderBy, $start, $limit) {
        return $this->where($conditions)
                        ->order("$orderBy[0] $orderBy[1]")
                        ->limit($start, $limit)
                        ->select();
    }

    public function addComment($fields) {
        $option = array(
            'courseId' => 0, #课程ID
            'threadId' => 0, #提问ID
            'postId' => 0, #回答ID
            'userId' => 0, #用户ID
            'teacherId' => 0, #老师ID
            'content' => '', #评论内容
            'createdTime' => 0, #评论时间
            'satisficing' => 0, #评分
            'commentType' => 'system', #评分类型
        );
        $fields = array_merge($option, $fields);
        return $this->add($fields);
    }

    public function deleteComment() {
        
    }

    public function updateComment() {
        
    }

    public function getCommentNumber($userId) {
        return $this->where("teacherId = {$userId}")->select("COUNT(id)");
    }

    public function getAvgScore($conditions) {
        return $this->where($conditions)->avg('satisficing');
    }

}