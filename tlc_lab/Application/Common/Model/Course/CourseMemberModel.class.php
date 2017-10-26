<?php

namespace Common\Model\Course;

use Think\Model;
use Common\Model\Common\BaseModel;

class CourseMemberModel extends BaseModel {

    protected $tableName = 'course_member';

    
    public function getMembersByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->select() ? : null;
    }

    public function getMember($id) {
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addMember($member) {
        $r = $this->add($member);
        if (!$r)
            E('Insert course member error.');
        return $this->getMember($r);
    }

    public function getMemberByCourseIdAndUserId($courseId, $userId) {
        return $this->where("userId = {$userId} and courseId = {$courseId}")->find() ? : null;
    }

    public function getRoleMemberByCourseIdAndUserId($courseId, $userId,$role){
        return $this->where("userId = {$userId} and courseId = {$courseId} and role = '{$role}'")->find() ? : null;
    }

    /**
     * 根据用户id获取课程(区分属于默认授课班)
     * @userId int
     * @expr  string 
     * @return array
     */
    public function getCourseByUserId($param) {
        $option = array(
            'userId' => array(),
            'classId' => array()
        );
        $param = array_merge($option, $param);
        return $this->where($param)->order("createdTime asc")->select() ? : null;
    }

    /**
     * @author fubaosheng 2015-11-27
     * @param type $userId
     * @param type $role
     * @param type $start
     * @param type $limit
     * @param type $onlyPublished
     * @return type $setCode
     */
    public function findMembersByUserIdAndRole($userId, $role, $start, $limit, $onlyPublished = true) {
        $this->filterStartLimit($start, $limit);
        $sql = "SELECT m.* FROM {$this->tableName} m ";
        $sql.= ' JOIN  ' . 'course' . ' AS c ON m.userId =  ' . $userId;
        $sql .= " AND m.role =  '{$role}' AND m.courseId = c.id ";
        if ($onlyPublished === 'draft') {
            $sql .= " AND c.status = 'draft' ";
        } elseif ($onlyPublished === true) {
            $sql .= " AND c.status = 'published' ";
        } elseif ($onlyPublished === 'unpublished') {
            $sql .= " AND c.status != 'published' ";
        }
        $sql .= " ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
        $model = $this;
        return $model->query($sql);
    }

    public function findMemberCountByUserIdAndRole($userId, $role, $onlyPublished = true) {
//        $where = "m.userId = {$userId} and m.role = '{$role}'";
//        if($onlyPublished){
//            $where.= " and  c.status = 'published'";
//        }
//        return M($this->tableName." as m")->join("course c on m.userId = c.id")->where($where)->count("m.courseId");
        $sql = "SELECT COUNT( m.courseId ) FROM {$this->tableName} m ";
        $sql.= " JOIN  " . " course " . " AS c ON m.userId =  " . $userId;
        $sql.= " AND m.role =  '$role' AND m.courseId = c.id ";
        if ($onlyPublished === true) {
            $sql.= " AND c.status = 'published' ";
        } elseif ($onlyPublished === "unpublished") {
            $sql.= " AND c.status != 'published' ";
        }
//        return $this->query($sql);
        return $this->getConnection()->fetchColumn($sql);
    }

    public function findMembersByUserIdAndCourseTypeAndIsLearned($userId, $role, $type, $isLearned, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $where = " m.userId = {$userId} and m.role = '{$role}' and c.type = '{$type}' and m.isLearned = {$isLearned}";
        return M($this->tableName . " as m")->field("m.*")->join("course c on m.courseId = c.id")->where($where)->order("createdTime desc")->limit($start, $limit)->select();
//        $sql  = "SELECT m.* FROM {$this->tableName} m ";
//        $sql.= ' JOIN  '. CourseDao::TABLENAME . ' AS c ON m.userId = ? ';
//        $sql .= " AND c.type =  ? AND m.courseId = c.id AND m.isLearned = ? AND m.role = ?";
//        $sql .= " ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($userId, $type, $isLearned, $role));
    }

    public function findMembersByUserIdAndRoleAndIsLearned($userId, $role, $isLearned, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        return $this->where("userId = {$userId} and role = '{$role}' and isLearned = {$isLearned}")->order("createdTime desc")->limit($start, $limit)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND role = ? AND isLearned = ? 
//            ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($userId, $role, $isLearned));
    }

    public function findMemberCountByUserIdAndCourseTypeAndIsLearned($userId, $role, $type, $isLearned) {
        $where = "m.userId = {$userId} and m.role = '{$role}' and c.type='{$type}' and  m.isLearned = {$isLearned}";
        return M($this->tableName . " as m")->join("course c on m.userId = c.id")->where($where)->count("m.courseId");
//        $sql = "SELECT COUNT( m.courseId ) FROM {$this->tableName} m ";
//        $sql.= " JOIN  ". CourseDao::TABLENAME ." AS c ON m.userId = ? ";
//        $sql.= " AND c.type =  ? AND m.courseId = c.id  AND m.isLearned = ? AND m.role = ?";
//        return $this->getConnection()->fetchColumn($sql,array($userId, $type, $isLearned, $role));
    }

    public function findMemberCountByUserIdAndRoleAndIsLearned($userId, $role, $isLearned) {
        return $this->where("userId = {$userId} and role ='{$role}' and isLearned = {$isLearned}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  userId = ? AND role = ? AND isLearned = ?";
//        return $this->getConnection()->fetchColumn($sql, array($userId, $role, $isLearned));
    }

    public function findLearnedCoursesByCourseIdAndUserId($courseId, $userId) {
        return $this->where("courseId = {$courseId} and userId = {$userId} and isLearned = 1")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? AND userId = ? AND isLearned = 1";
//        return $this->getConnection()->fetchAll($sql, array($courseId, $userId));
    }

    public function deleteMembersByCourseId($courseId) {
        return $this->where("courseId = {$courseId}")->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE courseId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($courseId));
    }

    public function getMembersByCourseIds($courseIds) {
        $str = implode(',', $courseIds);
        return $this->where("courseId in ({$str})")->select();
//        $marks = str_repeat('?,', count($courseIds) - 1) . '?';
//        $sql = "SELECT * FROM `course_member` WHERE courseId IN ({$marks})";
//        $courseMembers =  $this->getConnection()->fetchAll($sql, $courseIds);
//        return $courseMembers;
    }

    public function selectMembersByUserIdAndRole($userId, $role) {
        $map = array(
            'userId'  =>  $userId,
            'role'  =>  $role
        );
        return $this->where($map)->order("createdTime asc")->select() ?: array();
    }

    public function selectMembersByCourseIdAndRole($courseId, $role) {
        $map = array(
            'courseId'  =>  $courseId,
            'role'  =>  $role
        );
        return $this->where($map)->order("createdTime asc")->select() ?: array();
    }

    public function findMembersByCourseIdAndRole($courseId, $role, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        return $this->where("courseId = {$courseId} and role = '{$role}'")->order("createdTime desc")->limit($start, $limit)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? AND role = ? ORDER BY seq,createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($courseId, $role));
    }

    public function findMemberCountByCourseIdAndRole($courseId, $role) {
        return $this->where("courseId = {$courseId} and role = '{$role}'")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  courseId = ? AND role = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId, $role));
    }

    public function countMembersByStartTimeAndEndTime($startTime, $endTime) {
        $sql = $this->field("courseId,count(userId) as co,role")->where("createdTime > {$startTime} and createdTime < {$endTime} and role = 'student'")->group("courseId")->buildSql();
        return $this->table($sql . " coursemembers")->order("coursemembers.co desc")->limit(0, 5)->select();
//        $sql = "SELECT * FROM (SELECT courseId, count(userId) AS co,role FROM {$this->tableName} WHERE createdTime <  ? AND createdTime > ? AND role='student'  GROUP BY courseId) coursemembers ORDER BY coursemembers.co DESC LIMIT 0,5";
//        return $this->getConnection()->fetchAll($sql, array($endTime,$startTime));
    }

    public function updateMember($id, $member) {
        $this->where("id = {$id}")->save($member);
//        $this->getConnection()->update($this->tableName, $member, array('id' => $id));
        return $this->getMember($id);
    }

    public function deleteMember($id) {
        return $this->where("id = {$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function deleteMemberByCourseIdAndUserId($courseId, $userId) {

        if (is_array($userId)) {
            $map['userId'] = array('in', $userId);
        } else {
            $map['userId'] = intval($userId);
        }
        $map['courseId'] = $courseId;
        $map['role'] = 'student';
        return $this->where($map)->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE userId = ? AND courseId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($userId, $courseId));
    }

    public function findCourseMembersByUserId($userId) {
        return $this->where("userId = {$userId} and role = 'student' and deadlineNotified = 0 and deadline>0")->limit(0, 10)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND role = 'student' AND deadlineNotified=0 AND deadline>0 LIMIT 0,10";
//        return $this->getConnection()->fetchAll($sql, array($userId));
    }

    public function findCoursesByStudentIdAndCourseIds($studentId, $courseIds) {
        $str = implode(',', $courseIds);
        return $this->where("role = 'student' and courseId in ({$str}) and userId = {$studentId}")->select();
//        $marks = str_repeat('?,', count($courseIds) - 1) . '?';
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND role = 'student' AND courseId in ($marks)";
//        return $this->getConnection()->fetchAll($sql, array_merge(array($studentId), $courseIds));
    }

    public function searchMemberCount($conditions) {
//        $where = $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->count("id");   

        $builder = $this->_createSearchQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    //从course_member中根据条件查找可用的course_id列表
    public function searchMemberCourseId($conditions){
        $builder = $this->_createSearchQueryBuilder($conditions)->select('courseId');
        return $builder->execute()->fetchColumn(1);
    }

    public function searchMembers($conditions, $orderBy, $start, $limit) {
        $this->filterStartLimit($start, $limit);
//        return $this->where($where)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start, $limit)->select() ? : array();
        $builder = $this->_createSearchQueryBuilder($conditions)
                ->select('*')
                ->orderBy($orderBy[0], $orderBy[1])
                ->setFirstResult($start)
                ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }

    public function searchMember($conditions, $start, $limit) {
        $this->filterStartLimit($start, $limit);
//        $where = $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->order("createdTime asc")->limit($start, $limit)->select() ? : array();
        $builder = $this->_createSearchQueryBuilder($conditions)
                ->select('*')
                ->setFirstResult($start)
                ->setMaxResults($limit)
                ->orderBy('createdTime', 'ASC');
        return $builder->execute()->fetchAll() ? : array();
    }

    public function searchMemberIds($conditions, $orderBy, $start, $limit) {
        $this->filterStartLimit($start, $limit);
//        $where = $this->_createSearchQueryBuilder($conditions);
//        if(isset($conditions['unique'])) $field = "distinct userId";
//        else $field = "userId";
//        return $this->field($field)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start, $limit)->select() ? : array();
        $builder = $this->_createSearchQueryBuilder($conditions);
        if (isset($conditions['unique'])) {
            $builder->select('DISTINCT userId');
        } else {
            $builder->select('userId');
        }
        $builder->orderBy($orderBy[0], $orderBy[1]);
        $builder->setFirstResult($start);
        $builder->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }

    private function _createSearchQueryBuilder($conditions) {
        $where = " 1 = 1";
        $builder = $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, 'course_member')
                ->andWhere('userId = :userId')
                ->andWhere('courseId = :courseId')
                ->andWhere('classId = :classId')
                ->andWhere('isLearned = :isLearned')
                ->andWhere('noteNum > :noteNumGreaterThan')
                ->andWhere('role = :role')
                ->andWhere('createdTime >= :startTimeGreaterThan')
                ->andWhere('createdTime < :startTimeLessThan');

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
                $where.= "and courseId in ({$courseIds})";
            }
        }
        return $builder;
    }

    /**
     * 根据班级id获取分组对应的人数,数组键值是班级的id
     * @param array|int $classId
     */
    public function getClassMemberCount($courseId, $classId, $role = 'student') {

        $map['courseId'] = intval($courseId);
        if (is_array($classId))
            $map['classId'] = array('in', $classId);
        else
            $map['classId'] = intval($classId);

        #角色 qzw 2015-08-22
        if ($role)
            $map['role'] = $role;

        $data = $this->field("classId,count(*) as classNum")
                ->where($map)
                ->group('classId')
                ->select();

        $return = array();
        foreach ($data as $v) {
            $return[$v['classId']] = $v['classNum'];
        }

        return $return;
    }
    
    /**
     * 获得课程成员列表
     * @author tanhaitao 2016-04-20
     */
    public function getCourseMemberAllList($courseId) {
         $where['courseId'] = $courseId ;
         $where['role'] = 'student' ;
         return $this->where($where)->field('userId')->select();
    }

    /**
     * 获得课程班级成员列表
     * @author 钱志伟 2015-08-19
     */
    public function getCourseMemberList($conditions, $orderBy, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $builder = $this->_createSearchQueryBuilder($conditions)
                ->select('SQL_CALC_FOUND_ROWS *')
#            ->orderBy($orderBy[0], $orderBy[1])
                ->setFirstResult($start)
                ->setMaxResults($limit);
        $ret = $builder->execute()->fetchAll() ? : array();
        return $ret;
    }

    public function classMemberCount($conditions) {

        $builder = $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, $this->tableName)
                ->andWhere('courseId = :courseId')
                ->andWhere('classId = :classId')
                ->andWhere('role = :role');

        if (isset($conditions['uids']) && !empty($conditions['uids'])) {
            $uids = implode(',', $conditions['uids']);
            $builder->andStaticWhere("userId IN ($uids)");
        }

        if (isset($conditions['black']) && in_array($conditions['black'], array(0, 1))) {
            $builder->andStaticWhere("black = {$conditions['black']}");
        }

        return $builder->select('COUNT(id)')
                        ->execute()
                        ->fetchColumn(0);
    }

    public function getMemberCount($conditions){
        return $this->where($conditions)->count() ?: 0;
    }

    public function getSignInMemberList($conditions) {
        $where['courseId'] = $conditions['courseId'];
        $where['userId'] = array('in', $conditions['uids']);
        return $this->where($where)->select() ? : null;
    }

    public function classMember($conditions, $orderBy, $start, $limit) {

        $this->filterStartLimit($start, $limit);
        $classId = isset($conditions['classId']) ? intval($conditions['classId']) : 0;
        $builder = $this->createDynamicQueryBuilder($conditions)
                ->select('u.id,u.email,u.nickname,u.userNum,u.smallAvatar,cm.createdTime,cm.deadline,cm.black,cm.role')
                ->from($this->tableName, 'cm')
                ->leftJoin('cm', 'user', 'u', 'cm.userId = u.id')
                ->andWhere('cm.courseId = :courseId')
                ->andWhere('cm.role = :role');

        if (isset($conditions['uids']) && !empty($conditions['uids'])) {
            $uids = implode(',', $conditions['uids']);
            $builder->andStaticWhere("cm.userId IN ($uids)");
        }

        if (isset($conditions['black']) && in_array($conditions['black'], array(0, 1))) {
            $builder->andStaticWhere("black = {$conditions['black']}");
        }

        $builder->setFirstResult($start)
                ->setMaxResults($limit);

        if (!empty($orderBy)) {
            $builder->addOrderBy($orderBy[0], $orderBy[1]);
        }

        $data = $builder->execute()->fetchAll();

        return $data;
    }

    /**
     * 根据userId和courseId获取用户所在组的id
     * 郭俊强 2015-08-18
     */
    public function getClassMemberId($paramArr = array()) {
        return 0;
        
//        $option = array(
//            'userId' => '', //用户id
//            'courseId' => '', //课程id
//        );
//        $option = array_merge($option, $paramArr);
//        extract($option);
//        $map['userId'] = intval($userId);
//        $map['courseId'] = intval($courseId);
//        $classId = $this->where($map)->getField('classId');
//        return intval($classId);
    }

    /**
     * 根据用户id
     * @param int $courseId
     * @param array $uids
     * @reutrn array
     * @author LiangFuJian
     * @date 2015-08-21
     */
    public function getJoinUidByUid($courseId, $uids) {

        $map['courseId'] = intval($courseId);
        $map['userId'] = array('in', $uids);
        $data = $this->field('userId')->where($map)->select();
        $return = array();
        if ($data) {
            foreach ($data as $v)
                $return[] = $v['userId'];
        }

        return $return;
    }

    /**
     * 更新用户班级
     * @param int|array $uIds
     * @param int $courseId
     * @param int $classId
     * @author LiangFuJian
     * @date 2015-08-21
     */
    public function updateUserClass($uIds, $courseId, $classId) {

        $map['courseId'] = intval($courseId);
        if (is_array($uIds)) {
            $map['userId'] = array('in', $uIds);
        } else {
            $map['userId'] = intval($uIds);
        }
        $data['classId'] = $classId;
        return $this->where($map)->save($data);
    }

    /**
     * 将用户移入到某个班级
     * @param int $courseId
     * @param int $userId
     * @param int $classId
     * @author LiangFuJian
     * @date 2015-08-21
     */
    public function moveCourseClassUser($courseId, $userId, $classId) {
        $map['courseId'] = intval($courseId);
        $map['userId'] = intval($userId);
        return $this->where($map)->setField('classId', intval($classId));
    }

    /**
     * 将用户移出某个班级
     * @param int $courseId
     * @param int $userId
     * @author LiangFuJian
     * @date 2015-08-21
     */
    public function removeCourseClassUser($courseId, $userId) {
        $map['courseId'] = intval($courseId);
        $map['userId'] = intval($userId);
        return $this->where($map)->setField('classId', 0);
    }

}

?>
