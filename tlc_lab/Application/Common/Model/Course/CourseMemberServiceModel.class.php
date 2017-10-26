<?php
namespace Common\Model\Course;
use Common\Model\Common\BaseModel;
use Common\Services\QueueService;
use Think\Exception;//2015-12-15 LiangFuJian
class CourseMemberServiceModel extends BaseModel{
    
    private  $_errorMsg = '';
    /**
     * 获取错误信息
     * @access public
     * @return string
     * @author LiangFuJian
     */
    public function getErrorMsg(){

        return $this->_errorMsg;

    }

    /**
     * 根据courseID获取所有在教老师（多人授课）
     * @param $courseId
     * @return null
     */
    public function getMembersByCourseId($courseId){
        return $this->getCourseMemberDao()->getMembersByCourseId($courseId);
    }

    //获得教师的在教课程或学生的在学课程
    public function getUserCourse($userId,$role){
        if($role != 'teacher'){
            $role = 'student';
        }
//        $role = $role == 'teacher' ?: 'student';
        return $this->getCourseMemberDao()->selectMembersByUserIdAndRole($userId,$role);
    }

    //获取课程下的学生或教师
    public function getCourseUser($courseId,$role){
        if($role != 'teacher'){
            $role = 'student';
        }

        return $this->getCourseMemberDao()->selectMembersByCourseIdAndRole($courseId,$role);
    }
    
    private function getCourseDao(){
        return $this->createDao("Course.Course");
    }

    private function getCourseMemberDao(){
        return $this->createDao("Course.CourseMember");
    }

    private function getCourseMemberService(){
        return $this->createDao("Course.CourseMemberService");
    }
    
    private function getUserService(){
        return createService('User.UserService');
    }
    
    private function getOrderService(){
        return createService('Order.OrderService');
    }
    
    private function getCourseService()
    {
        return createService('Course.CourseService');
    }
    
    private function getGroupService(){
        return createService('Group.GroupService');
    }

    private function getClassService(){
        return createService('Group.ClassService');
    }

    //绑定班级成员和课程的ID，使得学员加入开始学习课程
    public function bindCouseClassMember($courseId){
        $userIdList = $this->getClassService()->getCourseClassStudentIds($courseId);
        foreach ($userIdList as $userId) {
            $userCourseMember = $this->findUserCourseMember($courseId,$userId);
            if(empty($userCourseMember)){
                $member = array(
                    'courseId' => $courseId,
                    'userId' => $userId,
                    'role' => 'student',
                    'createdTime' => time(),
                );
                $this->getCourseMemberDao()->addMember($member);
            }
        }
    }

    //查询用户、课程关系
    public function findUserCourseMember($courseId,$userId,$role="student"){
        return $this->getCourseMemberDao()->getRoleMemberByCourseIdAndUserId($courseId, $userId,$role);
    }
    
    /**
     * 获得课程所有成员列表
     * @author tanhaitao 2016-04-20
     */
    public function getCourseMemberAllList($courseId){
        return $this->getCourseMemberDao()->getCourseMemberAllList($courseId);
    }
    
     /**
     * 获得课程班级成员列表
     * @author 钱志伟 2015-08-19
     */
    public function getCourseMemberList($conditions, $orderBy=array('joinTime', 'desc'), $start, $limit=10){
        return $this->getCourseMemberDao()->getCourseMemberList($conditions, $orderBy, $start, $limit);
    }
    
    public function getSignInMemberList($conditions){
        
        return $this->getCourseMemberDao()->getSignInMemberList($conditions);
    }
    
    public function searchMemberCount($conditions){
        
        return $this->getCourseMemberDao()->classMemberCount($conditions);
    }
    
    public function searchMember($conditions, $orderBy, $start, $limit){
        
        return $this->getCourseMemberDao()->classMember($conditions, $orderBy, $start, $limit);
    }

    //获取用户所在课程的组
    public function getClassMemberId($paramArr = array()){
        return $this->getCourseMemberDao()->getClassMemberId($paramArr);
    }

    
    public function moveCourseClassUser($courseId, $userId, $classId){

        return $this->getCourseMemberDao()->moveCourseClassUser($courseId, $userId, $classId);

    }

    public function removeCourseClassUser($courseId, $userId){

        return $this->getCourseMemberDao()->removeCourseClassUser($courseId, $userId);

    }
    
}
?>