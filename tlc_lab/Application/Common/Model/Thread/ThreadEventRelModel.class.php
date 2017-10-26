<?php

namespace Common\Model\Thread;

use Common\Model\Common\BaseModel;

class ThreadEventRelModel extends BaseModel {

    protected $tableName = 'course_thread_event_rel';

    public function getThreadEvent($conditions) {
        return $this->where($conditions)->find();
    }

    public function getThreadEventCount($conditions) {
        return $this->where($conditions)->count();
    }

    public function deleteThreadEvent($conditions) {
        return $this->where($conditions)->delete();
    }

    public function insertThreadEvent($fields) {
        return $this->add($fields);
    }

    public function updateThreadEvent($conditions, $fields) {
        return $this->where($conditions)->save($fields);
    }

    public function searchThreadsEvent($conditions, $orderBy, $start, $limit) {

        if (isset($conditions['courseId'])) {
            $where['a.courseId'] = $conditions['courseId'];
        }
        if (isset($conditions['fromUId'])) {
            $where['a.fromUId'] = $conditions['fromUId'];
        }
        if (isset($conditions['toUId'])) {
            $where['a.toUId'] = $conditions['toUId'];
        }
        if (isset($orderBy)) {
            $orderBy[0] = 'a.' . $orderBy[0];
        }
        $where['a.type'] = $conditions['type'];


        return $this->table("course_thread_event_rel as a")
                        ->join("course_thread as b on a.threadId = b.id")
                        ->field('b.*')
                        ->where($where)
                        ->order("$orderBy[0] $orderBy[1]")
                        ->limit($start, $limit)
                        ->select();
    }

    public function findEventThreadIds($where) {
        if (isset($where['ids']) && !empty($where['ids'])) {
            $where['threadId'] = array('in', implode(',', $where['ids']));
            unset($where['ids']);
        }
        return $this->where($where)->select();
    }

    public function searchThreadEventCount($conditions) {
        return $this->where($conditions)->count();
    }

    private function createEventSearchQueryBuilder($conditions) {
        if (isset($conditions['title'])) {
            $conditions['title'] = "%{$conditions['title']}%";
        }

        if (isset($conditions['content'])) {
            $conditions['content'] = "%{$conditions['content']}%";
        }

        $builder = $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, $this->tableName)
                ->andWhere('courseId = :courseId')
                ->andWhere('threadId = :threadId')
                ->andWhere('fromUId = :fromUId')
                ->andWhere('toUId = :toUId')
                ->andWhere('type = :type')
                ->andWhere('createdTime > :startTime')
                ->andWhere('createdTime < :endTime');
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
                $builder->andStaticWhere("threadId IN ($threadIds)");
            }
        }
        return $builder;
    }

}