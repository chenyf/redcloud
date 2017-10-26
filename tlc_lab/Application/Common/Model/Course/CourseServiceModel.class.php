<?php

namespace Common\Model\Course;

use Think\Model;
use Common\Lib\ArrayToolkit;
use Common\Lib\StringToolkit;
use Common\Model\Common\BaseModel;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Common\Model\Util\LiveClientFactory;
use Common\Common\Url;
use Think\RedisModel;
use Common\Lib\FileToolkit;

class CourseServiceModel extends BaseModel {

    private $redisConn = false; //@auther Czq
    private static $_publicCourse = 0;
    /**
     * 每个课程可添加的最大的教师人数
     */

    const MAX_TEACHER = 100;
    
    /*
     * @author Czq
     * 2016/03/14
     * 操作虚拟实验室redis的存储
     */
    //@author Czq 初始数据 redis
    public function __construct() {
        parent::__construct();
    }
    //@author Czq 选择redis库
    public function initRedisCOMMON(){
        if($this->redisConn!=false) return false;
        $this->redisConn = RedisModel::getInstance(C('REDIS_DIST.COMMON'));
    }
    //@author Czq 获取虚拟实验室数据 Key
    public function getVirtualLabKey($webCode, $publicCourse, $id){
        return "virtual_lab_{$webCode}_{$publicCourse}_{$id}";
    }
    //@author Czq 建立虚拟实验室redis的数据
    public function createVirtualLab($webCode, $publicCourse, $id){
        
        if(!$id) return null;
        $this->initRedisCOMMON();
        $virtualLabStatus = I("post.virtualLabStatus") == 1 ? 1 : 0;
        if($virtualLabStatus != 1){
            $virtualLabKey = $this->getVirtualLabKey($webCode, $publicCourse, $id);
            $r = $this->redisConn->hashSet($virtualLabKey, array('virtualLabStatus'=>$virtualLabStatus));
            return $r;
        }
        $virtualLabList = I("post.virtualLabList");
        $list = array();
        
        foreach($virtualLabList as $key => $value){
            $virtualLabList[$key]['name'] = trim($value['name']);
            $name_len = $virtualLabList[$key]['name'] ? mb_strlen($virtualLabList[$key]['name'],"utf-8") : 0;
            if($name_len<1 || $name_len>30){
                 E('请输入有效的虚拟实验室名称'); 
            }
            $virtualLabList[$key]['url'] = trim($value['url']);
            // validate js 的url规则会多次编码符号 
            $virtualLabList[$key]['url'] = html_entity_decode($_POST[virtualLabList][$key]['url']);
            //$virtualLabUrlDecode = urldecode($virtualLabList[$key]['url']);
            $url_len = $virtualLabList[$key]['url'] ? mb_strlen($virtualLabList[$key]['url'],"utf-8") : 0; 
            if( $url_len<1 || $url_len>=1000 ){
                 E('请输入有效的虚拟实验室名称'); 
            }
            $list[] = $virtualLabList[$key];
        }
         $virtualLabKey = $this->getVirtualLabKey($webCode, $publicCourse, $id);
         $list = json_encode($list);
         $r = $this->redisConn->hashSet($virtualLabKey, array('virtualLabStatus'=>$virtualLabStatus,'list'=>$list));
         return $r;
    }
    //@auther Czq 获取虚拟实验室数据
    public function getVirtualLab($webCode, $publicCourse, $id){
        $this->initRedisCOMMON();
        $virtualLabKey = $this->getVirtualLabKey($webCode, $publicCourse, $id);
        return $this->redisConn->hGetAll($virtualLabKey);
    }
    
    public function getCourseDao() {
//        return $this->createDao("Course.Course");;
        return $this->createService('Course.CourseModel');
    }

    /**
     * 中心库不忽略webcode
     * @author fubaosheng 2015-12-01
     */
    public function getCourseModel() {
        return $this->createService('Course.CourseModel');
    }

    public function getLessonDao() {
//         return $this->createDao("Course.Lesson");;
        return $this->createService('Course.LessonModel');
    }

    public function getLessonLearnDao() {
        return $this->createDao("Course.LessonLearn");
        ;
    }

    public function getLessonViewDao() {
        return $this->createDao("Course.LessonView");
        ;
    }

    public function getLessonVieweDao() {
        return $this->createDao("Course.LessonViewe");
        ;
    }

    public function getMemberDao() {
//        return $this->createDao("Course.CourseMember");;
        return $this->createService('Course.CourseMemberModel');
    }

    public function getClassService(){
        return createService('Group.ClassService');
    }

    
    public function getCourceLessonLearnService() {
        return $this->createService('Course.CourceLessonLearnServiceModel');
    }
    //@author 褚兆前 2016-03-23
    public function findGoldBuyCourseIntro($courseId) {
        return $this->getCourseDao()->findGoldBuyCourseIntro($courseId);
    }
    public function findCourseLessonTimeLength($courseId) {
        $tileLength = $this->table('course_lesson')->where(array('courseId'=>$courseId))->sum('length');
        return $tileLength ? $tileLength: 0;
    }

    public function findCourseLessonList($condition){
        $list = $this->table('course_lesson')->where($condition)->select() ?: array();
        return $list;
    }
    
    /**
     * 中心库不忽略webCode
     * @author fubaosheng 2015-12-10
     */
    public function getMemberModel() {
        return $this->createService('Course.CourseMemberModel');
    }

    private function getCourseCategoryDao() {
        return $this->createDao('Course.CourseCategory');
    }

    private function getCourseCategoryRelDao() {
        return $this->createDao('Course.CourseCategoryRel');
    }

    public function getFavoriteDao() {
        return $this->createDao("Course.Favorite");
    }

    /**
     * 中心库不忽略webCode
     * @author fubaosheng 2015-12-10
     */
    public function getFavoriteModel() {
        return $this->createDao("Course.Favorite");
    }

    public function getChapterDao() {
        return $this->createDao("Course.CourseChapter");
    }

    public function getAnnouncementDao() {
        return $this->createDao("Course.CourseAnnouncement");
    }

    public function getCourseLessonReplayDao() {
        return $this->createDao("Course.CourseLessonReplay");
    }

    public function getCourseDraftDao() {
        return $this->createDao("Course.CourseDraft");
    }

    public function getCourseAnnouncementDao() {
        return $this->createDao("Course.CourseAnnouncement");
    }

    public function getCourseMaterialDao() {
        return $this->createDao("Course.CourseMaterial");
    }

    public function getCourseNoteDao() {
        return $this->createDao("Course.CourseNote");
    }

    private function getNoteDao() {
        return $this->createDao("Course.CourseNote");
    }

    private function getLogService() {
        return $this->createService('System.LogServiceModel');
    }

    private function getUserService() {
        return $this->createService('User.UserServiceModel');
    }

    private function getCategoryService() {
        return $this->createService('Taxonomy.CategoryServiceModel');
    }

    private function getTagService() {
        return $this->createService('Taxonomy.TagServiceModel');
    }

    private function getFileService() {
        return $this->createService('Content.FileServiceModel');
    }

    private function getUploadFileService() {
        return $this->createService('File.UploadFileServiceModel');
    }

    private function getCourseMaterialService() {
        return $this->createService('Course.MaterialServiceModel');
    }

    private function getStatusService() {
        return $this->createService('User.StatusServiceModel');
    }

    private function getVipService() {
        return $this->createService('Vip.VipServiceModel');
    }

    private function getOrderService() {
        return $this->createService('Order.OrderServiceModel');
    }

    private function getSettingService() {
        return $this->createService('System.SettingServiceModel');
    }

    private function getMessageService() {
        return $this->createService('User.MessageServiceModel');
    }

    private function getSchoolCourseService() {
        return createService('Center.SchoolCourseService');
    }

    public function getCourse($id, $inChanging = false) {
        $dao = $this->getCourseDao();
        return $this->courseSerializeUnserialize($dao->getCourse($id));
    }

    public function getCourseRaw($id){
        return $this->getCourseDao()->getCourse($id);
    }

    public function getCourseFind($id, $inChanging = false) {
        return $this->courseSerializeUnserialize($this->getCourseDao()->getCourseFind($id));
    }

    public function getCoursesCount($status) {
        return $this->getCourseDao()->getCoursesCount($status);
    }

    public function getGroupPushService(){
        return createService("Group.GroupPushService");
    }
    
    /**
     * 根据课程创建者userId获取创建课程的id
     * guojunqiang 2015-11-26
     */
    public function getUserCourseId($userId) {
        $data = $this->getCourseDao()->getUserCourseId($userId);
        //被邀请的教师的课程也包括进来：
        $courseMemberList = $this->getCourseMemberService()->getUserCourse($userId,'teacher');

        return array_unique(array_merge(ArrayToolkit::column($data, 'id'),ArrayToolkit::column($courseMemberList, 'courseId')));
    }
    
    //获取所有的课程
    public function getAllCourseId(){
        return ArrayToolkit::column($this->getCourseDao()->selectAllCourseList(), 'id');
    }

    private function getCourseMemberService(){
        return createService('Course.CourseMemberService');
    }

    public function getCurrentWebCourseCount($status = false) {
        return $this->getCourseDao()->getCurrentWebCourseCount($status);
    }

    public function getCurrentWebAllCourse($start, $limit, $onlyPublished = true) {
        return $this->courseSerializeUnserializes($this->getCourseDao()->getCurrentWebAllCourse($start, $limit, $onlyPublished));
    }

    public function courseApplyRecordCount($paramArr = array()) {
        extract($paramArr);
        $where = " 1=1 ";
        if (!empty($uid))
            $where.= " and applyUid = {$uid}";
        if (in_array($status, array(0, 1, 2, 3)))
            $where.= " and status = {$status}";
        return $this->table("course_apply")
                        ->where($where)
                        ->count() ? : 0;
    }

    public function courseApplyRecord($paramArr = array()) {
        extract($paramArr);
        $where = " 1=1 ";
        if (!empty($uid))
            $where.= " and applyUid = {$uid}";
        if (in_array($status, array(0, 1, 2, 3)))
            $where.= " and status = {$status}";
        return $this->table("course_apply")
                        ->where($where)
                        ->order("applyTm desc")
                        ->limit($start, $limit)
                        ->select() ? : array();
    }

    /**
     * 根据课程id获取课程的所有老师
     * @author ljm
     * @param $courseId
     * @return array
     */
    public function getCourseTeachers($courseId){
        $teachers = [];
        $members = $this->getCourseMemberService()->getMembersByCourseId($courseId);
        foreach ($members as $member){
            array_push($teachers, $this->getUserService()->getUser($member['userId']));
        }
        return $teachers;
    }

    /**
     * 根据课程id获取创建课程的老师的id
     * @author ljm
     * @param $courseId
     * @return mixed
     */
    public function getCourseCreaterId($courseId){
        $course = $this->getCourse($courseId);
        return $course['teacherIds'][0];
    }

    /**
     * 根据课程id判断用户是否是创建者
     * @author ljm
     * @param $uid
     * @param $courseId
     * @return bool
     */
    public function isUserCourseCreater($uid, $courseId){
        $createrId = $this->getCourseCreaterId($courseId).'';
        $uid = $uid.'';
        return $createrId == $uid;
    }

    public function courseApplyRecordInfo($uid, $applyId) {
        $where = " id = {$applyId} ";
        if (!isGranted("ROLE_ADMIN"))
            $where.= " and applyUid = {$uid}";
        return $this->table("course_apply")
                        ->where($where)
                        ->find() ? : array();
    }

    public function courseRemoveApply($applyId, $courseId, $uid) {
        $where = " id = {$applyId} ";
        if (!isGranted("ROLE_ADMIN"))
            $where.= " and applyUid = {$uid}";
        $r = $this->table("course_apply")
                ->where($where)
                ->save(array("status" => 3));
        if ($r) {
            $arr = array("relCommCourseId" => -1, "relCommCourseTm" => 0);
            $this->getCourseDao()->updateCourse($courseId, $arr);
        }
        return $r;
    }

    public function getCourseApplyId($courseId) {
        if (!$courseId)
            return 0;
        $where = " courseId = {$courseId} and status = 0 ";
        return $this->table("course_apply")
                        ->where($where)
                        ->order("applyTm desc")
                        ->getField("id") ? : 0;
    }

    /**
     * 根据分类id获取课程列表
     * @author fubaosheng 2015-05-06
     */
    public function getCourseByCategoryId($categoryId) {
        $courseList = $this->getCourseDao()->getCourseByCategoryId($categoryId);
        if ($courseList) {
            foreach ($courseList as $key => $value) {
                $courseList[$key]['price'] = intval($value['price']);
                $courseList[$key]['pic'] = Url::getCoursePic($value['largePicture']);
                unset($courseList[$key]['smallPicture']);
                unset($courseList[$key]['middlePicture']);
                unset($courseList[$key]['largePicture']);
                $courseList[$key]['teacherIds'] = empty($value['teacherIds']) ? array() : explode('|', trim($value['teacherIds'], '|'));
            }
        }
        return $courseList ? : array();
    }

    public function findCoursesByIds(array $ids, $findInSet = false) {
        $courses = $this->courseSerializeUnserializes($this->getCourseDao()->findCoursesByIds($ids, $findInSet));
        return ArrayToolkit::index($courses, 'id');
    }

    public function findCoursesByCourseIds(array $ids, $start, $limit) {
        $courses = $this->courseSerializeUnserializes($this->getCourseDao()->findCoursesByCourseIds($ids, $start, $limit));
        return ArrayToolkit::index($courses, 'id');
    }

    public function findCoursesByLikeTitle($title) {
        $courses = $this->courseSerializeUnserializes($this->getCourseDao()->findCoursesByLikeTitle($title));
        return ArrayToolkit::index($courses, 'id');
    }

    public function findMinStartTimeByCourseId($courseId) {
        return $this->getLessonDao()->findMinStartTimeByCourseId($courseId);
    }

    public function findCoursesByTagIdsAndStatus(array $tagIds, $status, $start, $limit) {
        $courses = $this->courseSerializeUnserializes($this->getCourseDao()->findCoursesByTagIdsAndStatus($tagIds, $status, $start, $limit));
        return ArrayToolkit::index($courses, 'id');
    }

    public function findCoursesByAnyTagIdsAndStatus(array $tagIds, $status, $orderBy, $start, $limit) {
        $courses = $this->courseSerializeUnserializes($this->getCourseDao()->findCoursesByAnyTagIdsAndStatus($tagIds, $status, $orderBy, $start, $limit));
        return ArrayToolkit::index($courses, 'id');
    }

    public function searchCourses($conditions, $sort = 'latest', $start, $limit) {
        $conditions = $this->_prepareCourseConditions($conditions);
        if ($sort == 'popular') {
            $orderBy = array('viewCount', 'DESC');
        } else if ($sort == 'recommended') {
            $orderBy = array('recommendedTime', 'DESC');
        } else if ($sort == 'Rating') {
            $orderBy = array('Rating', 'DESC');
        } else if ($sort == 'hitNum') {
            $orderBy = array('hitNum', 'DESC');
        }elseif ($sort == 'recommendedSeq') {
            $orderBy = array('recommendedSeq', 'ASC');
        } elseif ($sort == 'createdTimeByAsc') {
            $orderBy = array('createdTime', 'ASC');
        }else {
            $orderBy = array('createdTime', 'DESC');
        }
        return $this->courseSerializeUnserializes($this->getCourseDao()->searchCourses($conditions, $orderBy, $start, $limit));
    }

    public function searchCourseCount($conditions) {
        $conditions = $this->_prepareCourseConditions($conditions);
        return $this->getCourseDao()->searchCourseCount($conditions);
    }

    public function searchPublicCourse($conditions) {
        return $this->courseSerializeUnserializes($this->getCourseDao()->searchPublicCourse($conditions));
    }

    public function findCoursesCountByLessThanCreatedTime($endTime, $siteSelect = 'local') {
        return $this->getCourseDao()->findCoursesCountByLessThanCreatedTime($endTime, $siteSelect);
    }

    public function analysisCourseSumByTime($endTime) {
        return $this->getCourseDao()->analysisCourseSumByTime($endTime);
    }

    public function findUserLearnCourses($userId, $start, $limit) {
        $memberDao = $this->getMemberDao();
        $members = $memberDao->findMembersByUserIdAndRole($userId, 'student', $start, $limit);
        $courses = $this->findCoursesByIds(ArrayToolkit::column($members, 'courseId'));
        foreach ($members as $member) {
            if (empty($courses[$member['courseId']])) {
                continue;
            }
            $courses[$member['courseId']]['memberIsLearned'] = $member['isLearned'];
            $courses[$member['courseId']]['memberLearnedNum'] = $member['learnedNum'];
        }
        return $courses;
    }

    public function findUserLearnCourseCount($userId) {
        $memberDao = $this->getMemberDao();
        return $memberDao->findMemberCountByUserIdAndRole($userId, 'student', 0);
    }

    public function findUserLeaningCourses($userId, $start, $limit, $filters = array()) {
        $memberDao = $this->getMemberDao();
        if (isset($filters["type"])) {
            $members = $memberDao->findMembersByUserIdAndCourseTypeAndIsLearned($userId, 'student', $filters["type"], '0', $start, $limit);
        } else {
            $members = $memberDao->findMembersByUserIdAndRoleAndIsLearned($userId, 'student', '0', $start, $limit);
        }
//        $members = $this->getMemberDao()->findMembersByUserIdAndRole($userId, 'student', $start, $limit);
        $courses = $this->findCoursesByIds(ArrayToolkit::column($members, 'courseId'));
        $sortedCourses = array();
        foreach ($members as $member) {
            if (empty($courses[$member['courseId']])) {
                continue;
            }
            $course = $courses[$member['courseId']];
            $course['memberIsLearned'] = 0;
            $course['memberLearnedNum'] = $member['learnedNum'];
            $learnTime = $this->searchLearnTime(array(
                "userId" => $userId,
                "courseId" => $course["id"],
                "status" => "finished"
            ));

            $learnTimeHour = $learnTime / 3600;
            $learnTimeMinutes = ($learnTime - $learnTimeHour * 3600) / 60;

            $course["learnTime"] = $learnTimeHour . "小时" . $learnTimeMinutes."分钟";

            $sortedCourses[] = $course;
        }
        return $sortedCourses;
    }

    public function findUserLeaningCourseCount($userId, $filters = array()) {
        $memberDao = $this->getMemberDao();
        if (isset($filters["type"])) {
            return $memberDao->findMemberCountByUserIdAndCourseTypeAndIsLearned($userId, 'student', $filters["type"], 0);
        }
        return $memberDao->findMemberCountByUserIdAndRoleAndIsLearned($userId, 'student', 0);
    }

    public function findUserLeanedCourseCount($userId, $filters = array()) {
        $memberDao = $this->getMemberDao();
        if (isset($filters["type"])) {
            return $memberDao->findMemberCountByUserIdAndCourseTypeAndIsLearned($userId, 'student', $filters["type"], 1);
        }
        return $memberDao->findMemberCountByUserIdAndRoleAndIsLearned($userId, 'student', 1);
    }

    public function findUserLeanedCourses($userId, $start, $limit, $filters = array()) {
        $memberDao = $this->getMemberDao();
        if (isset($filters["type"])) {
            $members = $memberDao->findMembersByUserIdAndCourseTypeAndIsLearned($userId, 'student', $filters["type"], '1', $start, $limit);
        } else {
            $members = $memberDao->findMembersByUserIdAndRoleAndIsLearned($userId, 'student', '1', $start, $limit);
        }
        $courses = $this->findCoursesByIds(ArrayToolkit::column($members, 'courseId'));
        $sortedCourses = array();
        foreach ($members as $member) {
            if (empty($courses[$member['courseId']])) {
                continue;
            }
            $course = $courses[$member['courseId']];
            $course['memberIsLearned'] = 1;
            $course['memberLearnedNum'] = $member['learnedNum'];
            $sortedCourses[] = $course;
        }
        return $sortedCourses;
    }

    public function findUserTeachCourseCount($userId, $onlyPublished = true) {
        return $this->getMemberDao()->findMemberCountByUserIdAndRole($userId, 'teacher', $onlyPublished);
    }

    //获取老师所有的在教课程1.0
    public function findTeacherCourseList($teacherId){
        $courseIds = $this->getUserCourseId($teacherId);
        $courses = $this->findCoursesByIds($courseIds);
        return $courses;
    }

    //获取老师所有的在教课程2.0
    public function findUserTeachCourses($userId, $start, $limit, $onlyPublished = true) {
        $members = $this->getMemberDao()->findMembersByUserIdAndRole($userId, 'teacher', $start, $limit, $onlyPublished);
        $courses = $this->findCoursesByIds(ArrayToolkit::column($members, 'courseId'));
        // @todo 以下排序代码有共性，需要重构成一函数。
        $sortedCourses = array();
        foreach ($members as $member) {
            if (empty($courses[$member['courseId']])) {
                continue;
            }
            $sortedCourses[] = $courses[$member['courseId']];
        }
        return $sortedCourses;
    }

    public function findUserFavoritedCourseCount($userId, $setCode = false) {
        $favoriteDao = $this->getFavoriteDao();
        if ($setCode)
            $favoriteDao = $this->getFavoriteModel();
        return $favoriteDao->getFavoriteCourseCountByUserId($userId);
    }

    public function findUserFavoritedCourses($userId, $start, $limit, $setCode = false) {
        $favoriteDao = $this->getFavoriteDao();
        $courseDao = $this->getCourseDao();
        if ($setCode) {
            $favoriteDao = $this->getFavoriteModel();
            $courseDao = $this->getCourseModel();
        }
        $courseFavorites = $favoriteDao->findCourseFavoritesByUserId($userId, $start, $limit);
        $favoriteCourses = $courseDao->findCoursesByIds(ArrayToolkit::column($courseFavorites, 'courseId'));
        return $this->courseSerializeUnserializes($favoriteCourses);
    }

    public function createCourse($course) {
        if (!ArrayToolkit::requireds($course, array('title','number'))) {
            throw $this->createServiceException('缺少必要字段，创建课程失败！');
        }
        //从数组中获取要邀请的老师
        $teacherIdsForInvitation = [];
        if (array_key_exists("teacherIdsForInvitation", $course)){
            $teacherIdsForInvitation = $course['teacherIdsForInvitation'];
            unset($course['teacherIdsForInvitation']);
        }

        $classId = $course['classId'];
        $course = ArrayToolkit::parts($course, array('title', 'type', 'about','number', 'lesson_time','lesson_address','categoryId', 'tags'));

        $course['status'] = 'draft';
        $course['about'] = !empty($course['about']) ? htmlspecialchars($course['about']) : ''; //$this->getHtmlPurifier()->purify($course['about']) getHtmlPurifier没有此方法 htmlspecialchars代替
        $course['tags'] = !empty($course['tags']) ? $course['tags'] : '';
        $course['userId'] = $this->getCurrentUser()->id;
        $course['createdTime'] = time();
        $course['teacherIds'] = array($course['userId']);
        $course = $this->getCourseDao()->addCourse($this->courseSerializeSerialize($course));

        /**
         * 创建课程是添加一个班
         * edit fubaosheng 2015-12-03
         */
        if (!empty($course)) {
            $classData = array();
            $classData["userId"] = $this->getCurrentUser()->id ? : 0;
            $classData["courseId"] = $course["id"];
        }
        $member = array(
            'courseId' => $course['id'],
            'userId' => $course['userId'],
            'role' => 'teacher',
            'createdTime' => time(),
        );
        $this->getMemberDao()->addMember($member); //这里是真正添加了一门课
        $course = $this->getCourse($course['id']);
        //$this->getLogService()->info('course', 'create', "创建课程《{$course['title']}》(#{$course['id']})");
	    //hook('created_course',$course);
        return $course;
    }


	
    public function updateCourse($id, $fields) {
        $course = $this->getCourseDao()->getCourse($id);
        if (empty($course)) {
            throw $this->createServiceException('课程不存在，更新失败！');
        }
        $fields = $this->_filterCourseFields($fields);

        $fields = $this->courseSerializeSerialize($fields);
        return $this->courseSerializeUnserialize($this->getCourseDao()->updateCourse($id, $fields));
    }

    public function updateCourseCounter($id, $counter) {
        $fields = ArrayToolkit::parts($counter, array('rating', 'ratingNum', 'lessonNum', 'giveCredit'));
        if (empty($fields)) {
            throw $this->createServiceException('参数不正确，更新计数器失败！');
        }
        $this->getCourseDao()->updateCourse($id, $fields);
    }

	private function _filterCourseFields($fields) {
		$fields = ArrayToolkit::filter($fields, array(
			'title'            => '',
			'subtitle'         => '',
			'about'            => '',
            'number'           => 0,
            'type'             => 'normal',
            'lesson_time'      => '',
            'lesson_address'   => '',
			'selectPicture' => "",
            'smallPicture'  => "",
            'middlePicture'  => "",
            'largePicture'  => "",
//			'serializeMode'    => 'none',
			'categoryId'       => 0,
			'goals'            => array(),
			'audiences'        => array(),
			'tags'             => '',
			'startTime'        => 0,
			'endTime'          => 0,
			'locationId'       => 0,
			'address'          => '',
		));
		if (!empty($fields['about'])) {
			$fields['about'] = $this->purifyHtml($fields['about'], true); //purifyHtml?
		}
		if (!empty($fields['tags'])) {
			$fields['tags'] = explode(',', $fields['tags']);
			$fields['tags'] = $this->getTagService()->findTagsByNames($fields['tags']);
			array_walk($fields['tags'], function (&$item, $key) {
				$item = (int)$item['id'];
			});
		}
		return $fields;
	}
 

    public function changeCoursePicture($courseId, $filePath, array $options) {
        $course = $this->getCourseDao()->getCourse($courseId);
        if (empty($course)) {
            throw $this->createServiceException('课程不存在，图标更新失败！');
        }

        $pathinfo = pathinfo($filePath);
        $imagine = new Imagine();
        $rawImage = $imagine->open($filePath);

        $largeImage = $rawImage->copy();
        $largeImage->crop(new Point($options['x'], $options['y']), new Box($options['width'], $options['height']));
        $largeImage->resize(new Box(480, 270));
        $largeFilePath = "{$pathinfo['dirname']}/{$pathinfo['filename']}_large.{$pathinfo['extension']}";
        $largeImage->save($largeFilePath, array('quality' => 90));
        $largeFileRecord = $this->getFileService()->uploadFile('course', new File($largeFilePath));

        $largeImage->resize(new Box(304, 171));
        $middleFilePath = "{$pathinfo['dirname']}/{$pathinfo['filename']}_middle.{$pathinfo['extension']}";
        $largeImage->save($middleFilePath, array('quality' => 90));
        $middleFileRecord = $this->getFileService()->uploadFile('course', new File($middleFilePath));

        $largeImage->resize(new Box(96, 54));
        $smallFilePath = "{$pathinfo['dirname']}/{$pathinfo['filename']}_small.{$pathinfo['extension']}";
        $largeImage->save($smallFilePath, array('quality' => 90));
        $smallFileRecord = $this->getFileService()->uploadFile('course', new File($smallFilePath));

        $fields = array(
            'smallPicture' => $smallFileRecord['uri'],
            'middlePicture' => $middleFileRecord['uri'],
            'largePicture' => $largeFileRecord['uri'],
        );

        @unlink($filePath);

        #edit tanhaitao 2015-09-24 删除原有课程图片
//		$oldPictures = array(
//			'smallPicture'  => $course['smallPicture'] ? getParameter('redcloud.upload.public_directory') . '/' . str_replace('public://', '', $course['smallPicture']) : null,
//			'middlePicture' => $course['middlePicture'] ? getParameter('redcloud.upload.public_directory') . '/' . str_replace('public://', '', $course['middlePicture']) : null,
//			'largePicture'  => $course['largePicture'] ? getParameter('redcloud.upload.public_directory') . '/' . str_replace('public://', '', $course['largePicture']) : null
//		);
//
//		$courseCount = $this->searchCourseCount(array('smallPicture' => $course['smallPicture']));
//		if ($courseCount <= 1) {
//			array_map(function ($oldPicture) {
//				if (!empty($oldPicture)) {
//					@unlink($oldPicture);
//				}
//			}, $oldPictures);
//		}
        $this->getLogService()->info('course', 'update_picture', "更新课程《{$course['title']}》(#{$course['id']})图片", $fields);

        return $this->getCourseDao()->updateCourseFind($courseId, $fields);
    }

    public function updateCourseFind($courseId, $fields) {
        return $this->getCourseDao()->updateCourseFind($courseId, $fields);
    }

    public function recommendCourse($id, $number) {
        $course = $this->tryAdminCourse($id);
        if (!is_numeric($number))
            E('推荐课程序号只能为数字！');
        $course = $this->getCourseDao()->updateCourse($id, array(
            'recommended' => 1,
            'recommendedSeq' => (int) $number,
            'recommendedTime' => time(),
        ));
        $this->getLogService()->info('course', 'recommend', "推荐课程《{$course['title']}》(#{$course['id']}),序号为{$number}");
        return $course;
    }

    public function hitCourse($id) {
        $checkCourse = $this->getCourse($id);
        if (empty($checkCourse))
            E("课程不存在，操作失败。");
        $this->getCourseDao()->waveCourse($id, 'hitNum', +1);
    }

    public function cancelRecommendCourse($id) {
        $course = $this->tryAdminCourse($id);
        $this->getCourseDao()->updateCourse($id, array(
            'recommended' => 0,
            'recommendedTime' => 0,
            'recommendedSeq' => 0
        ));
        $this->getLogService()->info('course', 'cancel_recommend', "取消推荐课程《{$course['title']}》(#{$course['id']})");
    }

    public function analysisCourseDataByTime($startTime, $endTime) {
        return $this->getCourseDao()->analysisCourseDataByTime($startTime, $endTime);
    }

    public function findLearnedCoursesByCourseIdAndUserId($courseId, $userId) {
        return $this->getMemberDao()->findLearnedCoursesByCourseIdAndUserId($courseId, $userId);
    }

    //添加课程内容文件：文档、视频
    public function uploadCourseFile($targetType, $targetId, array $fileInfo = array(), $implemtor = 'local', UploadedFile $originalFile = null) {
        return $this->getUploadFileService()->addFile($targetType, $targetId, $fileInfo, $implemtor, $originalFile);
    }

    //添加课程资料
    public function uploadCourseResource($targetType, $targetId, array $fileInfo = array(), $implemtor = 'local', UploadedFile $originalFile = null){
        return $this->getUploadFileService()->addResourceFile($targetType, $targetId, $fileInfo, $implemtor, $originalFile);
    }

    public function deleteCourseMaterial($id, $materialId) {
        return $this->getCourseMaterialDao()->deleteCourseMaterial($id, $materialId);
    }

    public function findMaterials($id, $start, $limit) {
        return $this->getCourseMaterialDao()->findMaterialsByCourseId($id, $start, $limit);
    }

    public function deleteCourse($id) {
        $course = $this->tryAdminCourse($id);
        $lessons = $this->getLessonDao()->findLessonsByCourseId($id);
        if (!empty($lessons)) {
            $fileIds = ArrayToolkit::column($lessons, "mediaId");
            if (!empty($fileIds))
                $this->getUploadFileService()->decreaseFileUsedCount($fileIds);
        }
        $this->getCourseMaterialService()->deleteMaterialsByCourseId($id);
        $this->getMemberDao()->deleteMembersByCourseId($id);
        $this->getLessonDao()->deleteLessonsByCourseId($id);
        $this->getChapterDao()->deleteChaptersByCourseId($id);
        $this->getCourseDao()->deleteCourse($id);
        if ($course["type"] == "live")
            $this->getCourseLessonReplayDao()->deleteLessonReplayByCourseId($id);
//        $this->dispatchEvent("course.delete", array("id" => $id)); //dispatchEvent ??
        $this->getLogService()->info('course', 'delete', "删除课程《{$course['title']}》(#{$course['id']})");
        return true;
    }

    public function deleteLessonByCourseAndTeacher($courseId,$teacherId){
        return $this->getLessonDao()->deleteLessonByCourseAndTeacher($courseId,$teacherId);
    }

    public function publishCourse($id) {
        $course = $this->tryManageCourse($id);
        $this->getCourseDao()->updateCourse($id, array('status' => 'published'));
        $this->getLogService()->info('course', 'publish', "发布课程《{$course['title']}》(#{$course['id']})");
    }

    //author tanhaitao  2015-09-22  课程图片上传处显示调用
    public function tryManageCourseFind($courseId) {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            redirect(U('User/Signin/index'));
        }
        $course = $this->getCourseModel()->getCourseFind($courseId);
        if (empty($course))
            E("课程为空");
        if (!$this->hasCourseManagerRole($courseId, $user->id))
            E('您不是课程的教师或管理员，无权操作！');
        return $this->courseSerializeUnserialize($course);
    }

    public function tryManageCourse($courseId) {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            redirect(U('User/Signin/index'));
        }

        $course = $this->getCourseModel()->getCourse($courseId);
        if (empty($course))
            E("课程为空");
        if (!$this->hasCourseManagerRole($courseId, $user->id))
            E('您不是课程的教师或管理员，无权操作！');
        return $this->courseSerializeUnserialize($course);
    }

    public function hasCourseManagerRole($courseId, $userId) {
        #edit fubaosheng 2015-12-01
        //只有当前站的管理员才有权限
        if (isGranted("ROLE_ADMIN"))
            return true;
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if ($member and ($member['role'] == 'teacher'))
            return true;
        return false;
    }

    public function closeCourse($id) {
        $course = $this->tryManageCourse($id);
        $this->getCourseDao()->updateCourse($id, array('status' => 'closed'));
        $this->getLogService()->info('course', 'close', "关闭课程《{$course['title']}》(#{$course['id']})");
        //关闭课程 推送通知
//        $isCenter = (CENTER == "center") ? 1 : 0;
//        $pushData = array("publicCourse"=>$isCenter,"courseId"=>$course['id'],"courseTitle"=>$course['title']);
//        $this->getGroupPushService()->pushUser($course["userId"],"course","closeCourse",$pushData,true,false);
    }

    public function canManageCourse($courseId) {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            return false;
        }
        $course = $this->getCourse($courseId, false, true);
        if (empty($course)) {
            return false;
        }
        #edit fubaosheng 2015-12-01
        //只有当前站的管理员才有权限
        if (isGranted("ROLE_ADMIN")) {
            return true;
        }
        $courses = $this->getUserCourseId($user['id']);
        if(in_array($courseId, $courses)){
            return true;
        }
        return false;
    }

    public function canTakeCourse($course) {
        $course = !is_array($course) ? $this->getCourse(intval($course)) : $course;
        if (empty($course)) {
            return false;
        }
        $user = $this->getCurrentUser(); //
        if (!$user->isLogin()) {
            return false;
        }

        return true;
    }

    /**
     * iOS设备屏蔽
     * @author fubaosheng 2015-06-23
     */
    public function forbidIos($id) {
        $course = $this->tryManageCourse($id);
        $this->getCourseDao()->updateCourse($id, array('appleForbid' => 1));
        $this->getLogService()->info('course', 'forbid_ios', "iOS设备屏蔽《{$course['title']}》(#{$course['id']})");
    }

    /**
     * iOS设备取消屏蔽
     * @author fubaosheng 2015-06-23
     */
    public function allowIos($id) {
        $course = $this->tryManageCourse($id);
        $this->getCourseDao()->updateCourse($id, array('appleForbid' => 0));
        $this->getLogService()->info('course', 'allow_ios', "iOS设备取消屏蔽《{$course['title']}》(#{$course['id']})");
    }

    public function findLessonsByIds(array $ids) {
        $lessons = $this->getLessonDao()->findLessonsByIds($ids);
        $lessons = $this->lessonSerializeUnserializes($lessons);
        return ArrayToolkit::index($lessons, 'id');
    }

    /**
     * 加载默认的上传图片封面
     */
    public function loadDefaultCoursePoster(){
        $postDir = getParameter("course.poster");
        $path = getRelativePath("course.poster");
        $pictureExt = array('png','jpg','jpeg','bmp','gif');
        $posterList = [];
        foreach (glob("$postDir/*") as $file){
            $suffix = substr(strrchr($file, '.'), 1);
            $suffix = strtolower($suffix);
            if(in_array($suffix,$pictureExt)){
                $posterList[] = $path . "/" . basename($file);
            }
        }

        return $posterList;
    }

    /**
     *根据文件夹加载文件夹下面的文件列表，根据不同的后缀
     *
     */
    public function loadFilesUnderDir($type,$dir){
        $dir = $dir ?: "/";
        $fileSuffix = array();
        if($type == "document"){
            $fileSuffix = array("pdf", "doc", "docx", "ppt", "pptx");
        }else if($type == "video"){
            $fileSuffix = array("mp4", "webm","ogg","wmv", "mov","avi","flv", "rmvb", "3gp");
        }else{
            return array();
        }

        $user = $this->getCurrentUser();

        $totalDir = $this->getCloudFilePath($user['userNum'],$dir);

        $arr = array();
        foreach (glob("$totalDir/*") as $item) {
            $suffix = substr(strrchr($item, '.'), 1);
            $suffix = strtolower($suffix);
            //判断文件后缀是否为视频格式 如果有 添加到返回文件中
            if(in_array($suffix, $fileSuffix) || is_dir($item)) {
                $file = array();
                $file['ext'] = $suffix;
                $file["size"] = filesize($item);
                $file["name"] = substr(strrchr($item, "/"),1);
//                $file["name"] = mb_convert_encoding($file["name"], "UTF-8", "GBK");
                $file["fullName"] = pathjoin($dir,$file["name"]);
                $file["time"] = date("Y-m-d H:i:s",filemtime($item));
                $file["currFolder"] = trim($dir,"/")."/";
                if(is_dir($item)) {
                    $file["type"] = "directory";
                } elseif (is_file($item)) {
                    //filesize() 函数在碰到大于 2GB 的文件时可能会返回非预期的结果。
                    //对于 2GB 到 4GB 之间的文件通常可以使用 sprintf("%u", filesize($file)) 来克服此问题。
                    $file["type"] = "file";
                }
                array_push($arr, $file);
            }
        }

        return $arr;
    }

    public function getCloudFilePath($userNum,$dir){
        $dirPrefix = getPanPathPrefix($userNum);
        $totalDir = pathjoin($dirPrefix,$dir);

        return $totalDir;
    }

    public function lessonSerializeSerialize(array $lesson) {
        return $lesson;
    }

    public function lessonSerializeUnserialize(array $lesson = null) {
        return $lesson;
    }

    public function lessonSerializeUnserializes(array $lessons) {
        return array_map(function ($lesson) {
                    return LessonSerialize::unserialize($lesson);
                }, $lessons);
    }

    public function getCourseLesson($courseId, $lessonId) {
        $lesson = $this->getLessonDao()->getLesson($lessonId);

        if (empty($lesson) or ($lesson['courseId'] != $courseId))
            return null;
        return $this->lessonSerializeUnserialize($lesson);
    }

    public function findCourseDraft($courseId, $lessonId, $userId) {
        $draft = $this->getCourseDraftDao()->findCourseDraft($courseId, $lessonId, $userId);
        if (empty($draft) or ($draft['userId'] != $userId))
            return null;
        return $this->lessonSerializeUnserialize($draft);
    }

    public function getCourseLessons($courseId) {
        $lessons = $this->getLessonDao()->findLessonsByCourseId($courseId);
        return $this->lessonSerializeUnserializes($lessons);
    }

    public function deleteCourseDrafts($courseId, $lessonId, $userId) {
        return $this->getCourseDraftDao()->deleteCourseDrafts($courseId, $lessonId, $userId);
    }

    public function findLessonsByTypeAndMediaId($type, $mediaId) {
        $lessons = $this->getLessonDao()->findLessonsByTypeAndMediaId($type, $mediaId);
        return $this->lessonSerializeUnserializes($lessons);
    }

    public function searchLessons($conditions, $orderBy, $start, $limit) {
        return $this->getLessonDao()->searchLessons($conditions, $orderBy, $start, $limit);
    }

    public function searchLessonCount($conditions) {
        return $this->getLessonDao()->searchLessonCount($conditions);
    }

    private function getCourseResourceService(){
        return createService('Course.CourseResourceService');
    }

    //从网盘导入文件的时候，将文件信息同步到resource表和upload_files表
    public function asyncPanToFilesAndResource(&$lesson){

        $media = $lesson['media'];
        if (empty($media) or empty($media['source']) or empty($media['name'])) {
           return E("media参数不正确，添加课程内容失败！");
        }

        $fileObj['createdTime'] = time();
        $fileObj['updatedTime'] = time();
        $fileObj['targetId'] = $lesson['courseId'];
        $fileObj['targetType'] = 'courselesson';
        $fileObj['filename'] = $media['name'];
        $fileObj['ext'] = $media['fileext']?:"";
        $fileObj['size'] = $media['filesize']?:0;
        $fileObj['type'] = $lesson['type'];
        $fileObj['storage'] = 'cloud';
        $fileObj['isPublic'] = 1;
        $fileObj['canDownload'] = 1;
        $fileObj['updatedUserId'] = $this->getCurrentUser()->id;
        $fileObj['createdUserId'] = $this->getCurrentUser()->id;

//        $fileObj['hashId'] = $media['filepath'];
        $fileObj['hashId'] = "{$fileObj['targetType']}/{$fileObj['targetId']}/".basename($media['filepath']);

        $cloud_file_absolute_path = $this->getCourseResourceService()->getCloudFileAbsolutePath($fileObj['createdUserId'],$media['filepath']);

        $local_file_absolute_path = $this->getCourseResourceService()->getResourceAccessPath($fileObj['hashId']);

        if(!is_file($cloud_file_absolute_path)){
            return E("文件不存在，导入文件失败！");
        }

        if(!is_dir(dirname($local_file_absolute_path)) && false === @mkdir(dirname($local_file_absolute_path), 0777, true)){
            return E("文件夹创建失败！");
        }

        if(!copy($cloud_file_absolute_path,$local_file_absolute_path)){
            return E("导入文件出错！");
        }

        $fileObj['convertHash'] = "ch-{$fileObj['hashId']}";

        $filetype = FileToolkit::getFileTypeByExtension($fileObj['ext']);
        if(trim(strtolower($fileObj['ext'])) != 'pdf' && ($filetype == 'document' || $filetype == 'ppt')){
            $fileObj['convertStatus'] = 'waiting';
            $fileObj['ifConvert'] = 1;
            $uploadFile['hashId'] = "ch-{$fileObj['targetType']}/{$fileObj['targetId']}/{$fileObj['filename']}";
        }else{
            $fileObj['convertStatus'] = 'success';
            $fileObj['ifConvert'] = 0;
        }

        //网盘文件上传到upload_files表
        $fileAddAffected = $this->getUploadFileDao()->add($fileObj);
        if ($fileAddAffected <= 0) {
            E('Insert Course File disk file error.');
        }
        $uploadFile = $this->getUploadFileDao()->getFile($fileAddAffected);

        //如果课程发布，网盘文件上传到resource表
        if($lesson['status'] == 'published') {
            $this->getUploadFileService()->saveResourceToDb($uploadFile['id']);
        }

        //需要转码的创建转码队列进行转码
        if($uploadFile['convertStatus'] == 'waiting'){
            $this->getLocalFile()->convertFile($uploadFile);
        }

        $lesson['mediaId'] = $uploadFile['id'];
        $lesson['mediaName'] = $uploadFile['filename'];
        $lesson['mediaSource'] = 'cloud';
        $lesson['mediaUri'] = '';

        unset($lesson['media']);
        return $lesson;
    }

    protected function getLocalFile()
    {
        return createService('File.LocalFile');
    }

    private function getUploadFileDao() {
        return $this->createService('File.UploadFileModel');
    }

    public function createLesson($lesson) {
        $lesson = ArrayToolkit::filter($lesson, array(
                    'courseId' => 0,
                    'chapterId' => 0,
                    'free' => 0,
                    'title' => '',
                    'summary' => '',
                    'tags' => array(),
                    'type' => 'text',
                    'content' => '',
                    'media' => array(),
                    'mediaId' => 0,
                    'length' => 0,
                    'startTime' => 0,
                    'giveCredit' => 0,
                    'requireCredit' => 0,
                    'testCount' => 0,
                    'liveProvider' => 'none',
                    'polyvVid' => '',
                    'polyvVideoSize' => 0,
                    'polyvExtra' => '',
                    "supportGoldBuy" => 0, //@author  Czq 2016-03-18
                    "needGoldNum" => 0
        ));

        if (!ArrayToolkit::requireds($lesson, array('courseId', 'title', 'type'))) {
            E('参数缺失，创建课程内容失败！');
        }
        if (empty($lesson['courseId'])) {
            E('添加课程内容失败，课程ID为空。');
        }
        $course = $this->getCourse($lesson['courseId'], true);
        if (empty($course)) {
            E('添加课程内容失败，课程不存在。');
        }
        if (!in_array($lesson['type'], array('text', 'audio', 'video', 'testpaper', 'ppt', 'document', 'flash', 'testtask', 'practice'))) {
            E('课程内容类型不正确，添加失败！');
        }

        $lesson['status'] = $course['status'] == 'published' ? 'unpublished' : 'published';

        //从网盘导入文件
        if(!empty($lesson['media']) && $lesson['media']['fromcloud'] == 1){
            $this->asyncPanToFilesAndResource($lesson);
        }else{
            $this->fillLessonMediaFields($lesson);
        }

        if (isset($fields['title'])) {
            $fields['title'] = $this->purifyHtml($fields['title']); // purifyHtml??
        }
        // 课程处于发布状态时，新增课程内容，课程内容默认的状态为“未发布"
        $lesson['number'] = $this->getNextLessonNumber($lesson['courseId']);
        $lesson['seq'] = $this->getNextCourseItemSeq($lesson['courseId']);
        $lesson['userId'] = $this->getCurrentUser()->id;
        $lesson['createdTime'] = time();

        if(!isset($lesson['chapterId']) || empty($lesson['chapterId'])) {
            $lastChapter = $this->getChapterDao()->getLastChapterByCourseId($lesson['courseId']);
            $lesson['chapterId'] = empty($lastChapter) ? 0 : $lastChapter['id'];
        }

        $lesson = $this->getLessonDao()->addLesson($this->lessonSerializeSerialize($lesson));
        if (!empty($lesson['mediaId'])) {
            $this->getUploadFileService()->increaseFileUsedCount(array($lesson['mediaId']));
            if($lesson['status'] == 'published') {
                $this->getUploadFileService()->saveResourceToDb($lesson['mediaId'],$lesson['title']);//将上传的课程内容作为课程资料上传
            }
        }
        $this->updateCourseCounter($course['id'], array(
            'lessonNum' => $this->getLessonDao()->getLessonCountByCourseId($course['id']),
            'giveCredit' => $this->getLessonDao()->sumLessonGiveCreditByCourseId($course['id']),
        ));

        $this->getLogService()->info('course', 'add_lesson', "添加课程内容《{$lesson['title']}》({$lesson['id']})", $lesson);

        return $lesson;
    }

    //获取课时信息
    public function findLesson($id) {
        return $this->getLessonDao()->find($id);
    }

    //获取已经添加的试卷id
    public function getTestpaperId($conditions) {
        return $this->getLessonDao()->getTestpaperId($conditions);
    }

    public function getNextLessonNumber($courseId) {
        return $this->getLessonDao()->getLessonCountByCourseId($courseId) + 1;
    }

    private function getNextCourseItemSeq($courseId) {
        $chapterMaxSeq = $this->getChapterDao()->getChapterMaxSeqByCourseId($courseId);
        $lessonMaxSeq = $this->getLessonDao()->getLessonMaxSeqByCourseId($courseId);
        return ($chapterMaxSeq > $lessonMaxSeq ? $chapterMaxSeq : $lessonMaxSeq) + 1;
    }

    public function getCourseDraft($id) {
        return $this->getCourseDraftDao()->getCourseDraft($id);
    }

    public function createCourseDraft($draft) {
        $draft = ArrayToolkit::parts($draft, array('userId', 'title', 'courseId', 'summary', 'content', 'lessonId', 'createdTime'));
        $draft['userId'] = $this->getCurrentUser()->id;
        $draft['createdTime'] = time();
        $draft = $this->getCourseDraftDao()->addCourseDraft($draft);
        return $draft;
    }

    /**
     * 获取课程下的课程内容总时长(分钟)
     * @author fubaosheng 2015-05-27
     */
    public function getCourseLessonLength($courseId) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程(#{$courseId})不存在！");
        }
        $length = $this->getLessonDao()->getCourseLessonLength($courseId);
        $length = $length ? floor($length / 60) : 0;
        return $length;
    }

    public function updateLesson($courseId, $lessonId, $fields) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程(#{$courseId})不存在！");
        }
        $lesson = $this->getCourseLesson($courseId, $lessonId);
        if (empty($lesson)) {
            E("课程内容(#{$lessonId})不存在！");
        }
        $fields = ArrayToolkit::filter($fields, array(
                    'title' => '',
                    'summary' => '',
                    'content' => '',
                    'media' => array(),
                    'mediaId' => 0,
                    'length' => 0,
                    'startTime' => 0,
                    'testCount' => 0,
                    'giveCredit' => 0,
                    'requireCredit' => 0,
                    'polyvVid' => '',
                    'polyvVideoSize' => 0,
                    'polyvExtra' => '',
                    "supportGoldBuy" => 0, //@author  Czq 2016-03-18
                    "needGoldNum" => 0
        ));

        if (isset($fields['title'])) {
            $fields['title'] = $this->purifyHtml($fields['title']); // purifyHtml??
        }
        $fields['type'] = $lesson['type'];
        if ($fields['type'] == 'live') {
            $fields['endTime'] = $fields['startTime'] + $fields['length'] * 60;
        }
        $this->fillLessonMediaFields($fields);

       $updatedLesson = $this->lessonSerializeUnserialize(
                $this->getLessonDao()->updateLesson($lessonId, $this->lessonSerializeSerialize($fields))
        );
        $this->updateCourseCounter($course['id'], array(
            'giveCredit' => $this->getLessonDao()->sumLessonGiveCreditByCourseId($course['id']),
        ));
        if ($fields['mediaId'] != $lesson['mediaId']) {
            if (!empty($fields['mediaId'])) {
                $this->getUploadFileService()->increaseFileUsedCount(array($fields['mediaId']));
            }
            if (!empty($lesson['mediaId'])) {
                $this->getUploadFileService()->decreaseFileUsedCount(array($lesson['mediaId']));
            }
        }
        $this->getLogService()->info('course', 'update_lesson', "更新课程内容《{$updatedLesson['title']}》({$updatedLesson['id']})", $updatedLesson);
        return $updatedLesson;
    }

    public function updateCourseDraft($courseId, $lessonId, $userId, $fields) {
        $draft = $this->findCourseDraft($courseId, $lessonId, $userId);
        if (empty($draft)) {
            E('草稿不存在，更新失败！');
        }
        $fields = $this->_filterDraftFields($fields);
        $this->getLogService()->info('draft', 'update', "更新草稿《{$draft['title']}》(#{$draft['id']})的信息", $fields);
        $fields = $this->lessonSerializeSerialize($fields);
        return $this->lessonSerializeUnserialize(
                        $this->getCourseDraftDao()->updateCourseDraft($courseId, $lessonId, $userId, $fields)
        );
    }

    public function deleteLesson($courseId, $lessonId) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程(#{$courseId})不存在！");
        }
        $lesson = $this->getCourseLesson($courseId, $lessonId, true);
        if (empty($lesson)) {
            E("课程内容(#{$lessonId})不存在！");
        }
        // 更新已学该课程内容学员的计数器
        $learnCount = $this->getLessonLearnDao()->findLearnsCountByLessonId($lessonId);
        if ($learnCount > 0) {
            $learns = $this->getLessonLearnDao()->findLearnsByLessonId($lessonId, 0, $learnCount);
            foreach ($learns as $learn) {
                if ($learn['status'] == 'finished') {
                    $member = $this->getCourseMember($learn['courseId'], $learn['userId']);
                    if ($member) {
                        $memberFields = array();
                        $memberFields['learnedNum'] = $this->getLessonLearnDao()->getLearnCountByUserIdAndCourseIdAndStatus($learn['userId'], $learn['courseId'], 'finished') - 1;
                        $memberFields['isLearned'] = $memberFields['learnedNum'] >= $course['lessonNum'] ? 1 : 0;
                        $this->getMemberDao()->updateMember($member['id'], $memberFields);
                    }
                }
            }
        }

        $this->getLessonLearnDao()->deleteLearnsByLessonId($lessonId);
        //删除lesson表记录
        $this->getLessonDao()->deleteLesson($lessonId);

        // 更新课程内容序号
        $this->updateCourseCounter($course['id'], array(
            'lessonNum' => $this->getLessonDao()->getLessonCountByCourseId($course['id'])
        ));

        //删除课程内容对应的upload_files、resource
        if (!empty($lesson['mediaId'])) {
            $this->getUploadFileService()->deleteFile($lesson['mediaId']);
        }

//        $this->getCourseMaterialService()->deleteMaterialsByLessonId($lessonId);
        $this->getLogService()->info('lesson', 'delete', "删除课程《{$course['title']}》(#{$course['id']})的课程内容 {$lesson['title']}");
    }

    public function publishLesson($courseId, $lessonId) {
        $course = $this->tryManageCourse($courseId);
        $lesson = $this->getCourseLesson($courseId, $lessonId);
        if (empty($lesson)) {
            E("课程内容#{$lessonId}不存在");
        }
        $r = $this->getLessonDao()->updateLesson($lesson['id'], array('status' => 'published'));
        #談海濤 2015-09-19 修改
        if (!empty($r)) {
            $this->updateCourseCounter($courseId, array(
                'lessonNum' => $this->getLessonDao()->getLessonCountByCourseId($courseId),
                'giveCredit' => $this->getLessonDao()->sumLessonGiveCreditByCourseId($courseId),
            ));
        }
    }

    public function unpublishLesson($courseId, $lessonId) {
        $course = $this->tryManageCourse($courseId);
        $lesson = $this->getCourseLesson($courseId, $lessonId);
        if (empty($lesson)) {
            E("课程内容#{$lessonId}不存在");
        }

        $r = $this->getLessonDao()->updateLesson($lesson['id'], array('status' => 'unpublished'));
        #談海濤 2015-09-19 修改
        if (!empty($r)) {
            $this->updateCourseCounter($courseId, array(
                'lessonNum' => $this->getLessonDao()->getLessonCountByCourseId($courseId),
                'giveCredit' => $this->getLessonDao()->sumLessonGiveCreditByCourseId($courseId),
            ));
        }
    }

    private function _filterDraftFields($fields) {
        $fields = ArrayToolkit::filter($fields, array(
                    'title' => '',
                    'summary' => '',
                    'content' => '',
                    'createdTime' => 0
        ));
        return $fields;
    }

    private function fillLessonMediaFields(&$lesson) {
        if (in_array($lesson['type'], array('video', 'audio', 'ppt', 'document', 'flash'))) {
            $media = empty($lesson['media']) ? null : $lesson['media'];
            if (empty($media) or empty($media['source']) or empty($media['name'])) {
                E("media参数不正确，添加课程内容失败！");
            }
            if ($media['source'] == 'self') {
                $media['id'] = intval($media['id']);
                if (empty($media['id'])) {
                    E("media id参数不正确，添加/编辑课程内容失败！");
                }
                $file = $this->getUploadFileService()->getFile($media['id']);
                if (empty($file)) {
                    E('文件不存在，添加/编辑课程内容失败！');
                }
                $lesson['mediaId'] = $file['id'];
                $lesson['mediaName'] = $file['filename'];
                $lesson['mediaSource'] = 'self';
                $lesson['mediaUri'] = '';
            } else {
                if (empty($media['uri'])) {
                    E("media uri参数不正确，添加/编辑课程内容失败！");
                }
                $lesson['mediaId'] = 0;
                $lesson['mediaName'] = $media['name'];
                $lesson['mediaSource'] = $media['source'];
                $lesson['mediaUri'] = $media['uri'];
            }
        } elseif ($lesson['type'] == 'testpaper') {
            $lesson['mediaId'] = $lesson['mediaId'];
        } elseif ($lesson['type'] == 'testtask') {
            $lesson['mediaId'] = $lesson['mediaId'];
        } elseif ($lesson['type'] == 'practice') {
            $lesson['mediaId'] = $lesson['mediaId'];
        } elseif ($lesson['type'] == 'live') {
            
        } else {
            $lesson['mediaId'] = 0;
            $lesson['mediaName'] = '';
            $lesson['mediaSource'] = '';
            $lesson['mediaUri'] = '';
        }
        unset($lesson['media']);
        return $lesson;
    }

    public function tryAdminCourse($courseId) {
        $course = $this->getCourseDao()->getCourse($courseId);
        if (empty($course))
            E("课程为空");
        $user = $this->getCurrentUser();
        if (empty($user->id)) {
            redirect(U('User/Signin/index'));
        }
        if (count(array_intersect($user->roles, array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'))) == 0)
            E('您不是管理员，无权操作！');
        return $this->courseSerializeUnserialize($course);
    }

    public function courseSerializeSerialize(array &$course) {
        if (isset($course['tags'])) {
            if (is_array($course['tags']) and !empty($course['tags'])) {
                $course['tags'] = '|' . implode('|', $course['tags']) . '|';
            } else {
                $course['tags'] = '';
            }
        }
        if (isset($course['goals'])) {
            if (is_array($course['goals']) and !empty($course['goals'])) {
                $course['goals'] = '|' . implode('|', $course['goals']) . '|';
            } else {
                $course['goals'] = '';
            }
        }
        if (isset($course['audiences'])) {
            if (is_array($course['audiences']) and !empty($course['audiences'])) {
                $course['audiences'] = '|' . implode('|', $course['audiences']) . '|';
            } else {
                $course['audiences'] = '';
            }
        }
        if (isset($course['teacherIds'])) {
            if (is_array($course['teacherIds']) and !empty($course['teacherIds'])) {
                $course['teacherIds'] = '|' . implode('|', $course['teacherIds']) . '|';
            } else {
                $course['teacherIds'] = null;
            }
        }
        return $course;
    }

    public function courseSerializeUnserialize(array $course = null) {
        if (empty($course))
            return $course;
        $course['tags'] = empty($course['tags']) ? array() : explode('|', trim($course['tags'], '|'));
        $course['goals'] = empty($course['goals']) ? array() : explode('|', trim($course['goals'], '|'));
        $course['audiences'] = empty($course['audiences']) ? array() : explode('|', trim($course['audiences'], '|'));
        $course['teacherIds'] = empty($course['teacherIds']) ? array() : explode('|', trim($course['teacherIds'], '|'));
        $course['teacher'] = $this->getUserService()->getUser($course['userId']);
        $course['studentCount'] = $this->getCourseStudentCount($course['id']);
        $course['viewCount'] = $course['viewCount'] >= 999999 ? "999999+" : $course['viewCount'];
        $course['lessonNum'] = $this->getLessonDao()->getLessonCountByCourseId($course['id']);
        $course['selectPicture'] = empty(trim($course['selectPicture'])) ? "" : "/" . DATA_FETCH_URL_PREFIX .$course['selectPicture'];
        return $course;
    }

    public function increaseCourseViewCount($courseId,$count=-1){
        $course = $this->getCourseDao()->getCourse($courseId);

        if($count == -1){
            $count = $course['viewCount'] + 1;
        }

        $count = max($count,0);
        $count = min($count,999999);

        return $this->getCourseDao()->updateCourseFind($courseId,array('viewCount' => $count));
    }

    public function courseSerializeUnserializes(array $courses) {
        return array_map(function ($course) {
                    return $this->courseSerializeUnserialize($course);
                }, $courses);
    }

    private function _prepareCourseConditions($conditions) {
        $courseIds = $conditions['courseIds'];
        $conditions = array_filter($conditions);
        $conditions['courseIds'] = $courseIds;
        if (isset($conditions['date'])) {
            $dates = array(
                'yesterday' => array(
                    strtotime('yesterday'),
                    strtotime('today'),
                ),
                'today' => array(
                    strtotime('today'),
                    strtotime('tomorrow'),
                ),
                'this_week' => array(
                    strtotime('Monday this week'),
                    strtotime('Monday next week'),
                ),
                'last_week' => array(
                    strtotime('Monday last week'),
                    strtotime('Monday this week'),
                ),
                'next_week' => array(
                    strtotime('Monday next week'),
                    strtotime('Monday next week', strtotime('Monday next week')),
                ),
                'this_month' => array(
                    strtotime('first day of this month midnight'),
                    strtotime('first day of next month midnight'),
                ),
                'last_month' => array(
                    strtotime('first day of last month midnight'),
                    strtotime('first day of this month midnight'),
                ),
                'next_month' => array(
                    strtotime('first day of next month midnight'),
                    strtotime('first day of next month midnight', strtotime('first day of next month midnight')),
                ),
            );

            if (array_key_exists($conditions['date'], $dates)) {
                $conditions['startTimeGreaterThan'] = $dates[$conditions['date']][0];
                $conditions['startTimeLessThan'] = $dates[$conditions['date']][1];
                unset($conditions['date']);
            }
        }

        if (isset($conditions['creator'])) {
            $user = $this->getUserService()->getUserByNickname($conditions['creator']);
            $conditions['userId'] = $user ? $user['id'] : -1;
            unset($conditions['creator']);
        }

        if (isset($conditions['categoryId'])) {
            $childrenIds = $this->getCategoryService()->findCategoryChildrenIds($conditions['categoryId']);
            $conditions['categoryIds'] = array_merge(array($conditions['categoryId']), $childrenIds);
            unset($conditions['categoryId']);
        }

        if (isset($conditions['nickname'])) {
            $user = $this->getUserService()->getUserByNickname($conditions['nickname']);
            $conditions['userId'] = $user ? $user['id'] : -1;
            unset($conditions['nickname']);
        }

        return $conditions;
    }

    public function liveLessonTimeCheck($courseId, $lessonId, $startTime, $length) {
        $course = $this->getCourseDao()->getCourse($courseId);
        if (empty($course)) {
            E('此课程不存在！');
        }
        $thisStartTime = $thisEndTime = 0;
        if ($lessonId) {
            $liveLesson = $this->getCourseLesson($course['id'], $lessonId);
            $thisStartTime = empty($liveLesson['startTime']) ? 0 : $liveLesson['startTime'];
            $thisEndTime = empty($liveLesson['endTime']) ? 0 : $liveLesson['endTime'];
        } else {
            $lessonId = "";
        }
        $startTime = is_numeric($startTime) ? $startTime : strtotime($startTime);
        $endTime = $startTime + $length * 60;
        $thisLessons = $this->getLessonDao()->findTimeSlotOccupiedLessonsByCourseId($courseId, $startTime, $endTime, $lessonId);
        if (($length / 60) > 8) {
            return array('error_timeout', '时长不能超过8小时！');
        }
        if ($thisLessons) {
            return array('error_occupied', '该时段内已有直播课程内容存在，请调整直播开始时间');
        }
        return array('success', '');
    }

    public function calculateLiveCourseLeftCapacityInTimeRange($startTime, $endTime, $excludeLessonId) {
        $client = LiveClientFactory::createClient();
        $liveStudentCapacity = $client->getCapacity();
        $liveStudentCapacity = empty($liveStudentCapacity['capacity']) ? 0 : $liveStudentCapacity['capacity'];
        $lessons = $this->getLessonDao()->findTimeSlotOccupiedLessons($startTime, $endTime, $excludeLessonId);
        $courseIds = ArrayToolkit::column($lessons, 'courseId');
        $courseIds = array_unique($courseIds);
        $courseIds = array_values($courseIds);
        $courses = $this->getCourseDao()->findCoursesByIds($courseIds);
        $maxStudentNum = ArrayToolkit::column($courses, 'maxStudentNum');
        $timeSlotOccupiedStuNums = array_sum($maxStudentNum);
        return $liveStudentCapacity - $timeSlotOccupiedStuNums;
    }

    public function canLearnLesson($courseId, $lessonId) {
        list($course, $member) = $this->tryTakeCourse($courseId);
        $lesson = $this->getCourseLesson($courseId, $lessonId);
        if (empty($lesson) or $lesson['courseId'] != $courseId) {
            E("课程内容为空会课程ID错误");
        }
        $user = $this->getCurrentUser(); //
        if (empty($lesson['requireCredit'])) {
            return array('status' => 'yes');
        }
        if ($member['credit'] >= $lesson['requireCredit']) {
            return array('status' => 'yes');
        }
        return array('status' => 'no', 'message' => sprintf('本课程内容需要%s学分才能学习，您当前学分为%s分。', $lesson['requireCredit'], $member['credit']));
    }

    public function tryTakeCourse($courseId) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程为空");
        }
        $user = $this->getCurrentUser(); //
//        $user = array("id"=>1);
////        var_dump($user);
////        exit();
        //        if (!$user->isLogin()) {
        //            E('您尚未登录用户，请登录后再查看！');
        //        }
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $user->id);
        #edit fubaosheng 2015-12-01
        //只有当前站的管理员才有权限
        if (isGranted("ROLE_ADMIN"))
            return array($course, $member);
//		if (count(array_intersect($user->roles, array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'))) > 0) {
//			return array($course, $member);
//		}
        #edit fubaosheng 2015-05-19
//        if (empty($member) or !in_array($member['role'], array('teacher', 'student'))) {
//            E('您不是课程学员，不能查看课程内容，请先购买课程！ uid:'.$user->id);
//        }
        return array($course, $member);
    }

    private function simplifyCousrse($course) {
        return array(
            'id' => $course['id'],
            'title' => $course['title'],
            'picture' => $course['middlePicture'],
            'type' => $course['type'],
            'rating' => $course['rating'],
            'about' => StringToolkit::plain($course['about'], 100),
            'price' => $course['price'],
        );
    }

    private function simplifyLesson($lesson) {
        return array(
            'id' => $lesson['id'],
            'number' => $lesson['number'],
            'type' => $lesson['type'],
            'title' => $lesson['title'],
            'summary' => StringToolkit::plain($lesson['summary'], 100),
        );
    }

    public function startLearnLesson($courseId, $lessonId) {
        list($course, $member) = $this->tryTakeCourse($courseId);
        $user = $this->getCurrentUser(); ///

        $lesson = $this->getCourseLesson($courseId, $lessonId);
        if ($course['status'] == 'published') {
            $this->getStatusService()->publishStatus(array(
                'type' => 'start_learn_lesson',
                'objectType' => 'lesson',
                'objectId' => $lessonId,
                'properties' => array(
                    'course' => $this->simplifyCousrse($course),
                    'lesson' => $this->simplifyLesson($lesson),
                )
            ));
        }
        if (!empty($lesson) && $lesson['type'] != 'video') {
            $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($user->id, $lessonId);
            if ($learn)
                return false;
            $this->getLessonLearnDao()->addLearn(array(
                'userId' => $user->id,
                'courseId' => $courseId,
                'lessonId' => $lessonId,
                'status' => 'learning',
                'startTime' => time(),
                'finishedTime' => 0,
            ));
            return true;
        }

        $createLessonView['courseId'] = $courseId;
        $createLessonView['lessonId'] = $lessonId;
        $createLessonView['fileId'] = $lesson['mediaId'];

        $file = array();
        if (!empty($createLessonView['fileId'])) {
            $file = $this->getUploadFileService()->getFile($createLessonView['fileId']);
        }

        $createLessonView['fileStorage'] = empty($file) ? "net" : $file['storage'];
        $createLessonView['fileType'] = $lesson['type'];
        $createLessonView['fileSource'] = $lesson['mediaSource'];

        $this->createLessonView($createLessonView);
        $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($user->id, $lessonId);
        if ($learn)
            return false;

        $this->getLessonLearnDao()->addLearn(array(
            'userId' => $user->id,
            'courseId' => $courseId,
            'lessonId' => $lessonId,
            'status' => 'learning',
            'startTime' => time(),
            'finishedTime' => 0,
        ));
        return true;
    }

    public function createLessonView($createLessonView) {
        $createLessonView = ArrayToolkit::parts($createLessonView, array('courseId', 'lessonId', 'fileId', 'fileType', 'fileStorage', 'fileSource'));
        $createLessonView['userId'] = $this->getCurrentUser()->id; //
        $createLessonView['createdTime'] = time();
        $lessonView = $this->getLessonViewDao()->addLessonView($createLessonView);
        $lesson = $this->getCourseLesson($createLessonView['courseId'], $createLessonView['lessonId']);
        $this->getLogService()->info('course', 'create', "{$this->getCurrentUser()->nickname}观看课程内容《{$lesson['title']}》");
        return $lessonView;
    }

    public function finishLearnLesson($courseId, $lessonId) {
        list($course, $member) = $this->tryLearnCourse($courseId);
        $lesson = $this->getCourseLesson($courseId, $lessonId);
        if (empty($lesson)) {
            E("课程内容#{$lessonId}不存在！");
        }
        $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($member['userId'], $lessonId);
        if ($learn) {
            $this->getLessonLearnDao()->updateLearn($learn['id'], array(
                'status' => 'finished',
                'finishedTime' => time(),
            ));
        } else {
            $this->getLessonLearnDao()->addLearn(array(
                'userId' => $member['userId'],
                'courseId' => $courseId,
                'lessonId' => $lessonId,
                'status' => 'finished',
                'startTime' => time(),
                'finishedTime' => time(),
            ));
        }

        $learns = $this->getLessonLearnDao()->findLearnsByUserIdAndCourseIdAndStatus($member['userId'], $course['id'], 'finished');
        $totalCredits = $this->getLessonDao()->sumLessonGiveCreditByLessonIds(ArrayToolkit::column($learns, 'lessonId'));

        $memberFields = array();
        $memberFields['learnedNum'] = count($learns);
        $course = $this->getCourseDao()->getCourse($courseId);
        if ($course['serializeMode'] != 'serialize') {
            $memberFields['isLearned'] = $memberFields['learnedNum'] >= $course['lessonNum'] ? 1 : 0;
        }
        $memberFields['credit'] = $totalCredits;
        if ($course['status'] == 'published') {
            $this->getStatusService()->publishStatus(array(
                'type' => 'learned_lesson',
                'objectType' => 'lesson',
                'objectId' => $lessonId,
                'properties' => array(
                    'course' => $this->simplifyCousrse($course),
                    'lesson' => $this->simplifyLesson($lesson),
                )
            ));
        }
        $this->getMemberDao()->updateMember($member['id'], $memberFields);
    }

    public function tryLearnCourse($courseId) {
        $course = $this->getCourseDao()->getCourse($courseId);
        if (empty($course)) {
            E("课程为空");
        }
        $user = $this->getCurrentUser();
        if (empty($user)) {
            redirect(U('User/Signin/index'));
        }
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $user->id);
        if (empty($member) or !in_array($member['role'], array('admin', 'teacher', 'student'))) {
            E('您不是课程学员，不能学习！');
        }
        return array($course, $member);
    }

    public function findLatestFinishedLearns($start, $limit) {
        return $this->getLessonLearnDao()->findLatestFinishedLearns($start, $limit);
    }

    public function cancelLearnLesson($courseId, $lessonId) {
        list($course, $member) = $this->tryLearnCourse($courseId);
        $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($member['userId'], $lessonId);
        if (empty($learn)) {
            E("课程内容#{$lessonId}尚未学习，取消学习失败。");
        }
        if ($learn['status'] != 'finished') {
//            E("课程内容#{$lessonId}尚未学完，取消学习失败。");
            return;
        }
        $this->getLessonLearnDao()->updateLearn($learn['id'], array(
            'status' => 'learning',
            'finishedTime' => 0,
        ));
        $learns = $this->getLessonLearnDao()->findLearnsByUserIdAndCourseIdAndStatus($member['userId'], $course['id'], 'finished');
        $totalCredits = $this->getLessonDao()->sumLessonGiveCreditByLessonIds(ArrayToolkit::column($learns, 'lessonId'));
        $memberFields = array();
        $memberFields['learnedNum'] = count($learns);
        $memberFields['isLearned'] = $memberFields['learnedNum'] >= $course['lessonNum'] ? 1 : 0;
        $memberFields['credit'] = $totalCredits;
        $this->getMemberDao()->updateMember($member['id'], $memberFields);
    }

    public function getUserLearnLessonStatus($userId, $courseId, $lessonId) {
        $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($userId, $lessonId);
        if (empty($learn)) {
            return null;
        }
        return $learn['status'];
    }

    public function getUserLearnLessonStatuses($userId, $courseId) {
        $learns = $this->getLessonLearnDao()->findLearnsByUserIdAndCourseId($userId, $courseId) ? : array();
        $statuses = array();
        foreach ($learns as $learn) {
            $statuses[$learn['lessonId']] = $learn['status'];
        }
        return $statuses;
    }

    public function getUserNextLearnLesson($userId, $courseId) {
        $lessonIds = $this->getLessonDao()->findLessonIdsByCourseId($courseId);
        $learns = $this->getLessonLearnDao()->findLearnsByUserIdAndCourseIdAndStatus($userId, $courseId, 'finished');
        $learnedLessonIds = ArrayToolkit::column($learns, 'lessonId');
        $unlearnedLessonIds = array_diff($lessonIds, $learnedLessonIds);
        $nextLearnLessonId = array_shift($unlearnedLessonIds);
        if (empty($nextLearnLessonId)) {
            return null;
        }
        return $this->getLessonDao()->getLesson($nextLearnLessonId);
    }

    public function searchLearnCount($conditions) {
        return $this->getLessonLearnDao()->searchLearnCount($conditions);
    }

    public function searchLearns($conditions, $orderBy, $start, $limit) {
        return $this->getLessonLearnDao()->searchLearns($conditions, $orderBy, $start, $limit);
    }

    public function analysisLessonDataByTime($startTime, $endTime) {
        return $this->getLessonDao()->analysisLessonDataByTime($startTime, $endTime);
    }

    public function analysisLessonFinishedDataByTime($startTime, $endTime) {
        return $this->getLessonLearnDao()->analysisLessonFinishedDataByTime($startTime, $endTime);
    }

    public function searchAnalysisLessonViewCount($conditions) {
        return $this->getLessonViewDao()->searchLessonViewCount($conditions);
    }

    public function getAnalysisLessonMinTime($type) {
        if (!in_array($type, array('all', 'cloud', 'net', 'local'))) {
            E("error");
        }
        return $this->getLessonViewDao()->getAnalysisLessonMinTime($type);
    }

    public function searchAnalysisLessonView($conditions, $orderBy, $start, $limit) {
        return $this->getLessonViewDao()->searchLessonView($conditions, $orderBy, $start, $limit);
    }

    public function analysisLessonViewDataByTime($startTime, $endTime, $conditions) {
        return $this->getLessonViewDao()->searchLessonViewGroupByTime($startTime, $endTime, $conditions);
    }

    public function waveLearningTime($lessonId, $userId, $time) {
        $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($userId, $lessonId);
        $this->getLessonLearnDao()->updateLearn($learn['id'], array(
            'learnTime' => $learn['learnTime'] + intval($time),
        ));
    }

    public function findLearnsCountByLessonId($lessonId) {
        return $this->getLessonLearnDao()->findLearnsCountByLessonId($lessonId);
    }

    public function waveWatchingTime($userId, $lessonId, $time) {
        $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($userId, $lessonId);
        if ($learn['videoStatus'] == "playing")
            $this->getLessonLearnDao()->updateLearn($learn['id'], array(
                'watchTime' => $learn['watchTime'] + intval($time),
                'updateTime' => time(),
            ));
    }

    public function watchPlay($userId, $lessonId) {
        $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($userId, $lessonId);
        $this->getLessonLearnDao()->updateLearn($learn['id'], array(
            'videoStatus' => 'playing',
            'updateTime' => time(),
        ));
    }

    public function watchPaused($userId, $lessonId) {
        $learn = $this->getLessonLearnDao()->getLearnByUserIdAndLessonId($userId, $lessonId);
        $time = time() - $learn['updateTime'];
        $time = ceil($time / 60);
        $this->waveWatchingTime($userId, $lessonId, $time);
        $this->getLessonLearnDao()->updateLearn($learn['id'], array(
            'videoStatus' => 'paused',
            'updateTime' => time(),
        ));
    }

    public function searchLearnTime($conditions) {
        return $this->getLessonLearnDao()->searchLearnTime($conditions);
    }

    public function searchWatchTime($conditions) {
        return $this->getLessonLearnDao()->searchWatchTime($conditions);
    }

    public function getChapter($courseId, $chapterId) {
        $chapter = $this->getChapterDao()->getChapter($chapterId);
        if (empty($chapter) or $chapter['courseId'] != $courseId) {
            return null;
        }
        return $chapter;
    }

	/**
	 * 开启聊天室
	 * @param $id
	 * @return mixed
	 */
	public function openChatRoom($id){
		$course = $this->getCourse($id);
		if(empty($course['chatRoomId'])){
			$groupId =  $this->getChatRoomService()->create($course['title'],$course['userId']);
			$this->getCourseModel()->where(array('id'=>$id))->save(array(
				'chatRoomStatus'=>1,
				'chatRoomId'=>$groupId,
			));
			$course['chatRoomId']  = $groupId;
		}else{
			$this->getCourseModel()->where(array('id'=>$id))->setField(array('chatRoomStatus'=>1));
		}
		return $course['chatRoomId'];
	}

	/**
	 * 关闭聊天室
	 * @param $id
	 */
	public function closeChatRoom($id){
		$this->getCourseModel()->where(array('id'=>$id))->setField(array('chatRoomStatus'=>0));
	}

	/**
	 * 关闭聊天室
	 * @param $id
	 * @param $chatRoomVisitor
	 */
	public function chatRoomVisitor($id,$chatRoomVisitor){
		$chatRoomVisitor = $chatRoomVisitor == 1 ? 1 : 0;
		$this->getCourseModel()->where(array('id'=>$id))->setField(array('chatRoomVisitor'=>$chatRoomVisitor));
	}

	private function getChatRoomService(){
		return createService('ChatRoom.ChatRoomService');
	}


	public function getCourseChapters($courseId) {
        return $this->getChapterDao()->findChaptersByCourseId($courseId);
    }

    public function createChapter($chapter) {
        if (!in_array($chapter['type'], array('chapter', 'unit'))) {
            E("章节类型不正确，添加失败！");
        }
        if ($chapter['type'] == 'unit') {
            $chapter['number'] = $this->getNextUnitNumber($chapter['parentId']);
        } else {
            $chapter['number'] = $this->getNextChapterNumber($chapter['courseId']);
        }
        $chapter['seq'] = $this->getNextCourseItemSeq($chapter['courseId']);
        $chapter['createdTime'] = time();
        return $this->getChapterDao()->addChapter($chapter);
    }

    public function updateChapter($courseId, $chapterId, $fields) {
        $chapter = $this->getChapter($courseId, $chapterId);
        if (empty($chapter)) {
            E("章节#{$chapterId}不存在！");
        }
        $fields = ArrayToolkit::parts($fields, array('title'));
        return $this->getChapterDao()->updateChapter($chapterId, $fields);
    }

    //删除章以及其下的所有节
    public function deleteChapterAndLessons($courseId, $chapterId){
        //删除章
        $course = $this->tryManageCourse($courseId);
        $deletedChapter = $this->getChapter($course['id'], $chapterId);
        if (empty($deletedChapter)) {
            E(sprintf('章节(ID:%s)不存在，删除失败！', $chapterId));
        }
        $this->getChapterDao()->deleteChapter($deletedChapter['id']);

        //删除节
        $units = $this->getChapterDao()->findChaptersByChapterId($deletedChapter['id']);
        foreach ($units as $unit) {
            $this->deleteUnit($courseId,$unit['id']);
        }

        //删除章下面的lesson课程内容
        $lessons = $this->getLessonDao()->findLessonsByChapterId($deletedChapter['id']);
        foreach ($lessons as $lesson) {
            $this->deleteLesson($courseId,$lesson['id']);
        }

        //重置章节序
        if($deletedChapter['type'] == 'chapter'){
            $this->resetChapterNumber($courseId);
        }else if($deletedChapter['type'] == 'unit'){
            $this->resetUnitNumber($deletedChapter['parentId']);
        }
    }

    //删除节
    public function deleteUnit($courseId,$unitId){
        $this->getChapterDao()->deleteChapter($unitId);
        //删除节下的所有的lesson课程内容
        $lessons = $this->getLessonDao()->findLessonsByChapterId($unitId);
        foreach ($lessons as $lesson) {
            $this->deleteLesson($courseId,$lesson['id']);
        }
    }

    //只删除章不删除节
    public function deleteChapter($courseId, $chapterId) {
        $course = $this->tryManageCourse($courseId);
        $deletedChapter = $this->getChapter($course['id'], $chapterId);
        if (empty($deletedChapter)) {
            E(sprintf('章节(ID:%s)不存在，删除失败！', $chapterId));
        }
        $this->getChapterDao()->deleteChapter($deletedChapter['id']);
        $prevChapter = array('id' => 0);
        foreach ($this->getCourseChapters($course['id']) as $chapter) {
            if ($chapter['number'] < $deletedChapter['number']) {
                $prevChapter = $chapter;
            }
        }

        //章下的节lesson对应的章改换为小于章序号的章
        $lessons = $this->getLessonDao()->findLessonsByChapterId($deletedChapter['id']);
        foreach ($lessons as $lesson) {
            $this->getLessonDao()->updateLesson($lesson['id'], array('chapterId' => $prevChapter['id']));
        }
    }

    public function getNextChapterNumber($courseId) {
        $counter = $this->getChapterDao()->getChapterCountByCourseIdAndType($courseId, 'chapter');
        return $counter + 1;
    }

    public function getNextUnitNumber($chapterId){
        $counter = $this->getChapterDao()->getChapterCountByChapterIdAndType($chapterId,'unit');
        return $counter + 1;
    }

    public function getNextUnitNumberAndParentId($courseId) {
        $lastChapter = $this->getChapterDao()->getLastChapterByCourseIdAndType($courseId, 'chapter');
        $parentId = empty($lastChapter) ? 0 : $lastChapter['id'];
        $unitNum = 1 + $this->getChapterDao()->getChapterCountByCourseIdAndTypeAndParentId($courseId, 'unit', $parentId);
        return array($unitNum, $parentId);
    }

    public function getCourseItems($courseId, $status = '') {
        $lessons = $this->lessonSerializeUnserializes($this->getLessonDao()->findLessonsByCourseId($courseId, $status));
        $chapters = $this->getChapterDao()->findChaptersByCourseId($courseId);
        $items = array();
        $lesson['fileExt'] = "";
        foreach ($lessons as &$lesson) {
            $lesson['itemType'] = 'lesson';
            if(!empty($lesson['mediaId'])){
                $fileExt = $this->getUploadFileService()->getFileExtById($lesson['mediaId']);
                $lesson['fileExt'] = $fileExt?$fileExt['ext']:"";
            }
            $chapter = $this->getChapterDao()->getChapter($lesson['chapterId']);
            $lesson['lessonLevel'] = $chapter?$chapter['type']:'chapter';
//            $items["lesson-{$lesson['id']}"] = $lesson;
        }
       
        foreach ($chapters as &$chapter) {
            $chapter['itemType'] = 'chapter';
//            $items["chapter-{$chapter['id']}"] = $chapter;
        }
      
//        uasort($items, function ($item1, $item2) {
//                    return $item1['seq'] > $item2['seq'];
//                });
        $items = $this->courseItemsSort($chapters,$lessons);
        return $items;
    }

    //对章节和内容进行排序
    public function courseItemsSort($chapters,$lessons){
        $chapterArr = array();
        $unitArr = array();
        $lessonArr = array();

        //遍历章节
        foreach($chapters as $key=>$chapter){
            $pid = $chapter['parentId'];
            if($pid == 0){
                $chapterArr[$chapter['id']] = $chapter;
            }else{
                ArrayToolkit::pushInArray($unitArr,$pid,$chapter);
            }
        }

        //遍历lesson课程内容
        foreach($lessons as $key=>$lesson){
            $cid = $lesson['chapterId'];
            if(array_key_exists($cid,$chapterArr)){ //章下面的lesson
                ArrayToolkit::pushInArray($unitArr,$cid,$lesson);
            }else{
                ArrayToolkit::pushInArray($lessonArr,$cid,$lesson);
            }
        }

        //排序节
        $unitArr = array_map(function($unit){
            uasort($unit,function($item1,$item2){
                return $item1['createdTime'] > $item2['createdTime'];
            });
            return $unit;
        },$unitArr);

        //排序课程内容
        $lessonArr = array_map(function($lesson){
            uasort($lesson,function($item1,$item2){
                return $item1['createdTime'] > $item2['createdTime'];
            });
            return $lesson;
        },$lessonArr);

        //将所有的章节、lesson内容放入 $courseItems
        $courseItems = array();
        foreach($chapterArr as $chapterId => $chapter){
            $courseItems["chapter-{$chapter['id']}"] = $chapter;
            $unitList = $unitArr[$chapter['id']];
            if(!empty($unitList)){
                foreach($unitList as $unit){
                    if($unit['type'] == 'unit'){    //这是一个节
                        $courseItems["chapter-{$unit['id']}"] = $unit;
                        $lessonList = $lessonArr[$unit['id']];
                        if(!empty($lessonList)){
                            foreach ($lessonList as $lesson){
                                $courseItems["lesson-{$lesson['id']}"] = $lesson;
                            }
                        }
                    }else{  //这是一个lesson课程内容
                        $courseItems["lesson-{$unit['id']}"] = $unit;
                    }
                }
            }
        }

        return $courseItems;
    }

    //重置节序
    public function resetUnitNumber($chapterId){
        $unitList = $this->getChapterDao()->findChaptersByChapterId($chapterId);
        uasort($unitList,function($item1,$item2){
            return $item1['createdTime'] > $item2['createdTime'];
        });

        $this->getChapterDao()->getConnection()->beginTransaction();

        try{
            foreach ($unitList as $key => $unit){
                $unit['number'] = $key+1;
                $this->getChapterDao()->updateChapter($unit['id'],$unit);
            }
            $this->getChapterDao()->getConnection()->commit();
            return true;
        }catch(\Exception $e){
            $this->getChapterDao()->getConnection()->rollback();
            return false;
        }
    }

    //重置章序
    public function resetChapterNumber($courseId){
        $chapterList = $this->getChapterDao()->findChaptersByCourseId($courseId);
        uasort($chapterList,function($item1,$item2){
            return $item1['createdTime'] > $item2['createdTime'];
        });

        $this->getChapterDao()->getConnection()->beginTransaction();

        try{
            foreach ($chapterList as $key => $chapter){
                $chapter['number'] = $key+1;
                $this->getChapterDao()->updateChapter($chapter['id'],$chapter);
            }
            $this->getChapterDao()->getConnection()->commit();
            return true;
        }catch(\Exception $e){
            $this->getChapterDao()->getConnection()->rollback();
            return false;
        }
    }

    /**
     * 获得两级课程内容：章->时
     * @author 钱志伟 2015-05-07
     */
    public function get2LevelLesson($param = array(), $userId = false) {
        $options = array(
            'courseId' => 0, #课程id
            'lessonType' => array(), #video、testpaper、text、audio 、testtask
            'status' => '', #发布状态
        );
        $options = array_merge($options, $param);
        extract($options);

        $courseLessonArr = array();

        #章节
        $chapters = $this->getChapterDao()->findChaptersByCourseId($courseId);
        $chapters = ArrayToolkit::index($chapters, 'id');
        
        //@author 褚兆前 更具登陆获取本课程兑换的全部课时
        $lessonGoldBuyArray = array();
        if($userId){
            $getLessonGoldBuy = $this->getCourceLessonLearnService()->getLessonGoldBuy($userId,$courseId);
            if(!empty($getLessonGoldBuy)){
                foreach($getLessonGoldBuy as $value){
                    $lessonGoldBuyArray[] = $value['lessonId'];
                }
            }
            $isCenter = CENTER == 'center' ? 1 : 0;
        }
        #课程内容
        //$where = array('mediaSource'=>'polyv');
        if ($status)
            $where['status'] = $status;
        if ($lessonType)
            $where['type'] = array('in', $lessonType);
        $lessonArr = $this->getLessonDao()->findLessons($courseId, $where);
        if (!$lessonArr)
            return array();
        #分级
        $directIdx = 99900;
        foreach ($lessonArr as $row) {
            
            //@author 褚兆前 是否已经 金币兑换
            if(!$isCenter && !empty($lessonGoldBuyArray) && in_array($row['id'], $lessonGoldBuyArray)){
                $row['isGoldBuy'] = 1;
            }else{
                $row['isGoldBuy'] = 0;
            }
            
            $chapterId = $row['chapterId'];
            $number = $row['number'];
            
            if ($row['mediaSource'] == 'polyv' && !$row['polyvVid'] && $row['mediaUri'])
                $row['polyvVid'] = substr($row['mediaUri'], -38, 34);

            if ($row['mediaSource'] == 'polyv' && $row['polyvVid'])
                $row['mediaUri'] = getVideoUrl($row['polyvVid']);

            $row = ArrayToolkit::parts($row, array('id', 'chapterId', 'number', 'status', 'title', 'type', 'mediaSource', 'mediaName', 'mediaUri', 'polyvVid', 'polyvVideoSize', 'courseId', 'mediaId','isGoldBuy','supportGoldBuy','needGoldNum'));
            $row['friendTm'] = getVideoFriendTm($row['polyvVideoSize']);

            //如果是文档类型
            if ($row['type'] == 'document' && $row['mediaId']) {
                $tmp = createService('File.UploadFileService')->getFile($row['mediaId']);
                if ($tmp['convertStatus'] == 'success' && $tmp['ext'] == 'pdf') {
                    $row['mediaUri'] = getPdfWebUrl($tmp['hashId']);
                }
            }
            //如果是练习类型(practice)
            if ($row['type'] == 'practice') {
                $row['mediaId'] = $row['mediaId'] . '2';
            }
            //如果是文本类型(text)
            if ($row['type'] == 'text') {
                $row['mediaUri'] = C('SITE_URL') . U("Course/CourseLesson/showText", array('courseId' => $row['courseId'], 'lessonId' => $row['id']));
            }

            if (!$chapterId) { #课程直属
                $directIdx++;
                $courseLessonArr[$directIdx] = array('id' => $directIdx, 'title' => $row['title'] ? $row['title'] : '', 'top' => 1, 'sort' => $number, 'courseId' => $courseId, 'type' => $row['type']);
                $row['chapterId'] = $directIdx;
                $courseLessonArr[$directIdx]['video'][] = $row;
            } else { #有章或节
                $chapter = $chapters[$chapterId];
                if ($chapter['parentId']) {
                    $chapterId = $chapter['parentId'];
                    $chapter = $chapters[$chapterId];
                }
                if (!isset($courseLessonArr[$chapterId]))
                    $courseLessonArr[$chapterId] = array('id' => $chapterId, 'title' => $chapter['title'] ? $chapter['title'] : '', 'top' => 0, 'sort' => $number, 'courseId' => $courseId);
                $row['chapterId'] = $chapterId;
                $courseLessonArr[$chapterId]['video'][] = $row;
            }
        }
        $courseLessonArr = ArrayToolkit::sort2Array(array('data' => $courseLessonArr, 'field' => 'sort', 'asc' => 1, 'numeric' => 1));

        return $courseLessonArr;
    }

    public function sortCourseItems($courseId, array $itemIds) {
        $items = $this->getCourseItems($courseId);
        $existedItemIds = array_keys($items);
        if (count($itemIds) != count($existedItemIds)) {
            \Think\Log::write(print_r("courseIdA : {$courseId} itemIds : ".count($itemIds)."  existedItemIds  : ".count($existedItemIds), true), \Think\Log::CRIT, '', LOG_PATH.'courseIdA-'.  date('y-m').'.log');
            E('itemdIds参数不正确');
        }
        $diffItemIds = array_diff($itemIds, array_keys($items));
        if (!empty($diffItemIds)) {
            \Think\Log::write(print_r("courseIdB : {$courseId} itemIds : ".count($itemIds)."  existedItemIds  : ".count($existedItemIds), true), \Think\Log::CRIT, '', LOG_PATH.'courseIdB-'.  date('y-m').'.log');
            E('itemdIds参数不正确');
        }
        $lessonNum = $chapterNum = $unitNum = $seq = 0;
        $currentChapter = $rootChapter = array('id' => 0);
        foreach ($itemIds as $itemId) {
            $seq++;
            list($type, ) = explode('-', $itemId);
            switch ($type) {
                case 'lesson':
                    $lessonNum++;
                    $item = $items[$itemId];
                    $fields = array('number' => $lessonNum, 'seq' => $seq, 'chapterId' => $currentChapter['id']);
                    if ($fields['number'] != $item['number'] or $fields['seq'] != $item['seq'] or $fields['chapterId'] != $item['chapterId']) {
                        $this->getLessonDao()->updateLesson($item['id'], $fields);
                    }
                    break;
                case 'chapter':
                    $item = $currentChapter = $items[$itemId];
                    if ($item['type'] == 'unit') {
                        $unitNum++;
                        $fields = array('number' => $unitNum, 'seq' => $seq, 'parentId' => $rootChapter['id']);
                    } else {
                        $chapterNum++;
                        $unitNum = 0;
                        $rootChapter = $item;
                        $fields = array('number' => $chapterNum, 'seq' => $seq, 'parentId' => 0);
                    }
                    if ($fields['parentId'] != $item['parentId'] or $fields['number'] != $item['number'] or $fields['seq'] != $item['seq']) {
                        $this->getChapterDao()->updateChapter($item['id'], $fields);
                    }
                    break;
            }
        }
    }

    public function searchMembers($conditions, $orderBy, $start, $limit) {
        $conditions = $this->_prepareCourseConditions($conditions);
        return $this->getMemberDao()->searchMembers($conditions, $orderBy, $start, $limit);
    }

    public function searchMember($conditions, $start, $limit) {
        $conditions = $this->_prepareCourseConditions($conditions);
        return $this->getMemberDao()->searchMember($conditions, $start, $limit);
    }

    public function countMembersByStartTimeAndEndTime($startTime, $endTime) {
        return $this->getMemberDao()->countMembersByStartTimeAndEndTime($startTime, $endTime);
    }

    public function searchMemberCount($conditions) {
        $conditions = $this->_prepareCourseConditions($conditions);
        return $this->getMemberDao()->searchMemberCount($conditions);
    }

    //从course_member中根据条件查找可用的course_id列表
    public function searchMemberCourseId($conditions){
        $courseIdList = $this->getMemberDao()->searchMemberCourseId($conditions);

        $courseIdList = $this->filterNotExitCourse($courseIdList,'courseId');

        return $courseIdList;
    }

    //过滤不存在的课程
    public function filterNotExitCourse(&$courseList,$column,$referCourseIdList = null){
        if(empty($courseList)){
            return [];
        }

        foreach ($courseList as $k => $course){
            if(empty($referCourseIdList)){
                $r = $this->getCourseDao()->getCourseFind($course[$column]);
                if(empty($r)){
                    unset($courseList[$k]);
                }
            }else{
                if(!in_array($course[$column],$referCourseIdList)){
                    unset($courseList[$k]);
                }
            }
        }

        return $courseList;
    }

    public function findWillOverdueCourses() {
        $currentUser = $this->getCurrentUser(); //
        if (!$currentUser->isLogin()) {
            E('用户未登录');
        }
        $courseMembers = $this->getMemberDao()->findCourseMembersByUserId($currentUser->id);
        $courseIds = ArrayToolkit::column($courseMembers, "courseId");
        $courses = $this->findCoursesByIds($courseIds);
        $courseMembers = ArrayToolkit::index($courseMembers, "courseId");
        $shouldNotifyCourses = array();
        $shouldNotifyCourseMembers = array();
        $currentTime = time();
        foreach ($courses as $key => $course) {
            $courseMember = $courseMembers[$course["id"]];
            if ($course["expiryDay"] > 0 && $currentTime < $courseMember["deadline"] && (10 * 24 * 60 * 60 + $currentTime) > $courseMember["deadline"]) {
                $shouldNotifyCourses[] = $course;
                $shouldNotifyCourseMembers[] = $courseMember;
            }
        }
        return array($shouldNotifyCourses, $shouldNotifyCourseMembers);
    }

    public function getCourseMember($courseId, $userId) {
        return $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
    }
    
    public function getCourseByUserId($param){
        return $this->getMemberDao()->getCourseByUserId($param);
    }

    public function searchMemberIds($conditions, $sort = 'latest', $start, $limit) {
        $conditions = $this->_prepareCourseConditions($conditions);
        if ($sort = 'latest') {
            $orderBy = array('createdTime', 'DESC');
        }
        return $this->getMemberDao()->searchMemberIds($conditions, $orderBy, $start, $limit);
    }

    public function updateCourseMember($id, $fields) {
        return $this->getMemberDao()->updateMember($id, $fields);
    }

    public function isMemberNonExpired($course, $member) {
        if (empty($course) or empty($member)) {
            E("course, member参数不能为空");
        }
        /*
          如果课程设置了限免时间，那么即使expiryDay为0，学员到了deadline也不能参加学习
          if ($course['expiryDay'] == 0) {
          return true;
          }
         */
        if ($member['deadline'] == 0) {
            return true;
        }
        if ($member['deadline'] > time()) {
            return true;
        }
        return false;
    }

    public function findCourseStudents($courseId, $start, $limit) {
        return $this->getMemberDao()->findMembersByCourseIdAndRole($courseId, 'student', $start, $limit);
    }

    public function findCourseStudentsByCourseIds($courseIds) {
        return $this->getMemberDao()->getMembersByCourseIds($courseIds);
    }

    public function getCourseStudentCount($courseId) {
        return $this->getMemberDao()->findMemberCountByCourseIdAndRole($courseId, 'student');
    }

    //获取课程主要教师
    public function findCourseTeacher($courseId){
        $course = $this->getCourseDao()->getCourse($courseId);
        return $this->getUserService()->getUser($course['userId']);
    }

    //获取课程的所有教师
    public function findCourseTeachers($courseId) {
        $course = $this->getCourseDao()->getCourse($courseId);
        $courseMemberList = $this->getCourseMemberService()->getCourseUser($courseId,'teacher');
        $courseMemberUserIdList = array_unique(ArrayToolkit::column($courseMemberList,'userId'));
        $courseMemberUserIdList = ArrayToolkit::remove_value($courseMemberUserIdList,$course['userId']);
        $courseMemberUsers =  $this->getUserService()->getUsers($courseMemberUserIdList);

        if(empty($courseMemberUsers)){
            $courseMemberUsers = array();
        }

        array_unshift($courseMemberUsers,$this->findCourseTeacher($courseId));
        return $courseMemberUsers;
    }

    public function getCourseTeacher($courseId) {
        return $this->findCourseTeacher($courseId);
    }

    public function isCourseTeacher($courseId, $userId) {
        $course = $this->getCourse($courseId);
        if(empty($courseId) || $course['userId'] != $userId){
            return false;
        }else{
            return true;
        }
//        $teacherMember = $this->getMemberDao()->getRoleMemberByCourseIdAndUserId($courseId, $userId,'teacher');
//        if(empty($teacherMember)){
//            return false;
//        }else{
//            return true;
//        }
    }

    public function isCourseStudent($courseId, $userId) {
        $studentMember = $this->getMemberDao()->getRoleMemberByCourseIdAndUserId($courseId, $userId,'student');
        if(empty($studentMember)){
            return false;
        }else{
            return true;
        }
    }

    public function addCourseStudent($courseId,$userId){
        if(empty($courseId) || empty($userId) || $this->isCourseStudent($courseId,$userId)){
            return ;
        }

        $memberData = array(
            'courseId' => $courseId,
            'userId' => $userId,
            'role' => 'student',
            'isVisible' => 1,
            'createdTime' => time(),
        );
        
        $this->getMemberDao()->addMember($memberData);
    }

    public function setCourseTeachers($courseId, $teachers) {
        // 过滤数据
        $teacherMembers = array();
        foreach (array_values($teachers) as $index => $teacher) {
            if (empty($teacher['id'])) {
                E("教师ID不能为空，设置课程(#{$courseId})教师失败");
            }
            $user = $this->getUserService()->getUser($teacher['id']);
            if (empty($user)) {
                E("用户不存在或没有教师角色，设置课程(#{$courseId})教师失败");
            }
            $teacherMembers[] = array(
                'courseId' => $courseId,
                'userId' => $user['id'],
                'role' => 'teacher',
                'isVisible' => empty($teacher['isVisible']) ? 0 : 1,
                'createdTime' => time(),
            );
        }
        // 先清除所有的已存在的教师学员
        $existTeacherMembers = $this->findCourseTeachers($courseId);
        foreach ($existTeacherMembers as $member) {
            $this->getMemberDao()->deleteMember($member['id']);
        }
        // 逐个插入新的教师的学员数据
        $visibleTeacherIds = array();
        foreach ($teacherMembers as $member) {
            // 存在学员信息，说明该用户先前是学生学员，则删除该学员信息。
            $existMember = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $member['userId']);
            if ($existMember) {
                $this->getMemberDao()->deleteMember($existMember['id']);
            }
            $this->getMemberDao()->addMember($member);
            if ($member['isVisible']) {
                $visibleTeacherIds[] = $member['userId'];
            }
        }
        $this->getLogService()->info('course', 'update_teacher', "更新课程#{$courseId}的教师", $teacherMembers);
        // 更新课程的teacherIds，该字段为课程可见教师的ID列表
        $fields = array('teacherIds' => $visibleTeacherIds);
        $this->getCourseDao()->updateCourse($courseId, $this->courseSerializeSerialize($fields));
//        $this->dispatchEvent("course.teacher.update", array(
//            "courseId"=>$courseId
//        )); // dispatchEvent???
    }

    public function cancelTeacherInAllCourses($userId) {
        $count = $this->getMemberDao()->findMemberCountByUserIdAndRole($userId, 'teacher', false);
        $members = $this->getMemberDao()->findMembersByUserIdAndRole($userId, 'teacher', 0, $count, false);
        foreach ($members as $member) {
            $course = $this->getCourse($member['courseId']);
            $this->getMemberDao()->deleteMember($member['id']);
            $fields = array(
                'teacherIds' => array_diff($course['teacherIds'], array($member['userId']))
            );
            $this->getCourseDao()->updateCourse($member['courseId'], $this->courseSerializeSerialize($fields));
        }
        $this->getLogService()->info('course', 'cancel_teachers_all', "取消用户#{$userId}所有的课程老师角色");
    }

    public function remarkStudent($courseId, $userId, $remark) {
        if (empty($member)) {
            E('课程学员不存在，备注失败!');
        }
        $fields = array('remark' => empty($remark) ? '' : (string) $remark);
        return $this->getMemberDao()->updateMember($member['id'], $fields);
    }

    public function becomeStudent($courseId, $userId, $info = array(), $webCode) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程为空");
        }
        if ($course['status'] != 'published') {
            E('不能加入未发布课程');
        }
        $user = $this->getUserService()->getUser($userId);
        if (empty($user)) {
            E("用户(#{$userId})不存在，加入课程失败！");
        }
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if ($member['locked']==0 && $member) {
            E("用户(#{$userId})已加入该课程！");
        }
        $levelChecked = '';
        if (!empty($info['becomeUseMember'])) {
            $levelChecked = $this->getVipService()->checkUserInMemberLevel($user['id'], $course['vipLevelId']); //no exists
            if ($levelChecked != 'ok') {
                E("用户(#{$userId})不能以会员身份加入课程！");
            }
            $userMember = $this->getVipService()->getMemberByUserId($user['id']); ////no exists
        }
        if($info['payment']!='minebuy'){
            if ($course['expiryDay'] > 0) {
                //如果处在限免期，则deadline为限免结束时间 减 当前时间
                $deadline = $course['expiryDay'] * 24 * 60 * 60 + time();
                if ($course['freeStartTime'] <= time() && $course['freeEndTime'] > time()) {
                    if ($course['freeEndTime'] < $deadline) {
                        $deadline = $course['freeEndTime'];
                    }
                }
            } else {
                $deadline = 0;
                //如果处在限免期，则deadline为限免结束时间 减 当前时间
                if ($course['freeStartTime'] <= time() && $course['freeEndTime'] > time() && $levelChecked != 'ok') {
                    $deadline = $course['freeEndTime'];
                }
            }
        }else{
            $buyCourse = $this->getSchoolCourseService()->getSchoolCourse(array('webCode' => C('WEBSITE_CODE'), 'courseId' => $courseId));
            $deadline = $buyCourse['endTm'];
        }
        if (!empty($info['orderId'])) {
            $order = $this->getOrderService()->getOrder($info['orderId']);
            if (empty($order)) {
                E("订单(#{$info['orderId']})不存在，加入课程失败！");
            }
        } else {
            $order = null;
        }
        $fields = array(
            'courseId' => $courseId,
            'userId' => $userId,
            'orderId' => empty($order) ? 0 : $order['id'],
            'deadline' => $deadline,
            'levelId' => empty($info['becomeUseMember']) ? 0 : $userMember['levelId'],
            'role' => 'student',
            'remark' => empty($order['note']) ? '' : $order['note'],
            'createdTime' => time(),
            'classId' => isset($info['classId']) ? intval($info['classId']) : 0
        );
        if($buyCourse){
             $fields['payType'] = 'minebuy';
             $this->getSchoolCourseService()->setIncSchool(array('webCode' => C('WEBSITE_CODE'), 'courseId' => $courseId));
        }
        if (!empty($webCode)) {
            $fields['webCode'] = $webCode;
        }
        if (empty($fields['remark'])) {
            $fields['remark'] = empty($info['note']) ? '' : $info['note'];
        }
        if(!$member){
            $member = $this->getMemberDao()->addMember($fields);
            $this->setMemberNoteNumber($courseId, $userId, $this->getNoteDao()->getNoteCountByUserIdAndCourseId($userId, $courseId));
        }else{
            $fields['locked']=0;
            $this->getMemberDao()->updateMember($member['id'],$fields);
        }
        $setting = $this->getSettingService()->get('course', array());
        if (!empty($setting['welcome_message_enabled']) && !empty($course['teacherIds'])) {
            $message = $this->getWelcomeMessageBody($user, $course);
            $this->getMessageService()->sendMessage($course['teacherIds'][0], $user['id'], $message);
        }
        $fields = array(
            'studentNum' => $this->getCourseStudentCount($courseId),
        );
        if ($order) {
            $fields['income'] = $this->getOrderService()->sumOrderPriceByTarget('course', $courseId);
        }
        $this->getCourseDao()->updateCourse($courseId, $fields);
        if ($course['status'] == 'published') {
            $this->getStatusService()->publishStatus(array(
                'type' => 'become_student',
                'objectType' => 'course',
                'objectId' => $courseId,
                'userId' => $member["userId"],
                'properties' => array(
                    'course' => $this->simplifyCousrse($course),
                )
            ));
        }
        return $member;
    }

    public function setMemberNoteNumber($courseId, $userId, $number) {
        $member = $this->getCourseMember($courseId, $userId);
        if (empty($member)) {
            return false;
        }
        $this->getMemberDao()->updateMember($member['id'], array(
            'noteNum' => (int) $number,
            'noteLastUpdateTime' => time(),
        ));
        return true;
    }

    private function getWelcomeMessageBody($user, $course) {
        $setting = $this->getSettingService()->get('course', array());
        $valuesToBeReplace = array('{{nickname}}', '{{course}}');
        $valuesToReplace = array($user['nickname'], $course['title']);
        $welcomeMessageBody = str_replace($valuesToBeReplace, $valuesToReplace, $setting['welcome_message_body']);
        return $welcomeMessageBody;
    }

    public function removeStudent($courseId, $userId) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程(#${$courseId})不存在，退出课程失败。");
        }
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if (empty($member) or ($member['role'] != 'student')) {
            E("用户(#{$userId})不是课程(#{$courseId})的学员，退出课程失败。");
        }
        $this->getMemberDao()->deleteMember($member['id']);
        $this->getCourseDao()->updateCourse($courseId, array(
            'studentNum' => $this->getCourseStudentCount($courseId),
        ));
        $this->getLogService()->info('course', 'remove_student', "课程《{$course['title']}》(#{$course['id']})，移除学员#{$member['id']}");
    }

    public function lockStudent($courseId, $userId) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程(#${$courseId})不存在，封锁学员失败。");
        }
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if (empty($member) or ($member['role'] != 'student')) {
            E("用户(#{$userId})不是课程(#{$courseId})的学员，封锁学员失败。");
        }
        if ($member['locked']) {
            return;
        }
        $this->getMemberDao()->updateMember($member['id'], array('locked' => 1));
    }

    public function unlockStudent($courseId, $userId) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程(#${$courseId})不存在，封锁学员失败。");
        }
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if (empty($member) or ($member['role'] != 'student')) {
            E("用户(#{$userId})不是课程(#{$courseId})的学员，解封学员失败。");
        }
        if (empty($member['locked'])) {
            return;
        }
        $this->getMemberDao()->updateMember($member['id'], array('locked' => 0));
    }
    
    /**
     * 加入黑名单
     * @author fubaosheng 2016-01-08
     */
    public function blackStudent($courseId, $userId) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程(#${$courseId})不存在，学员加入黑名单失败。");
        }
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if (empty($member) or ($member['role'] != 'student')) {
            E("用户(#{$userId})不是课程(#{$courseId})的学员，学员加入黑名单失败。");
        }
        if ($member['black']) {
            return;
        }
        $this->getMemberDao()->updateMember($member['id'], array('black' => 1));
        $this->getLogService()->info('course', 'black_student', "课程《{$course['title']}》(#{$course['id']})，学员加入黑名单#{$member['id']}");
    }
    
    /**
     * 移出黑名单
     * @author fubaosheng 2016-01-08
     */
    public function unblackStudent($courseId, $userId) {
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("课程(#${$courseId})不存在，学员移出黑名单失败。");
        }
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if (empty($member) or ($member['role'] != 'student')) {
            E("用户(#{$userId})不是课程(#{$courseId})的学员，学员移出黑名单失败。");
        }
        if (empty($member['black'])) {
            return;
        }
        $this->getMemberDao()->updateMember($member['id'], array('black' => 0));
        $this->getLogService()->info('course', 'unblack_student', "课程《{$course['title']}》(#{$course['id']})，学员移出黑名单#{$member['id']}");
    }

    public function increaseLessonQuizCount($lessonId) {
        $lesson = $this->getLessonDao()->getLesson($lessonId);
        $lesson['quizNum'] += 1;
        $this->getLessonDao()->updateLesson($lesson['id'], $lesson);
    }

    public function resetLessonQuizCount($lessonId, $count) {
        $lesson = $this->getLessonDao()->getLesson($lessonId);
        $lesson['quizNum'] = $count;
        $this->getLessonDao()->updateLesson($lesson['id'], $lesson);
    }

    public function increaseLessonMaterialCount($lessonId) {
        $lesson = $this->getLessonDao()->getLesson($lessonId);
        $lesson['materialNum'] += 1;
        $this->getLessonDao()->updateLesson($lesson['id'], $lesson);
    }

    public function resetLessonMaterialCount($lessonId, $count) {
        $lesson = $this->getLessonDao()->getLesson($lessonId);
        $lesson['materialNum'] = $count;
        $this->getLessonDao()->updateLesson($lesson['id'], $lesson);
    }

    public function favoriteCourse($courseId) {
        $user = $this->getCurrentUser(); //
        if (empty($user->id)) {
            E("UID为空");
        }
        $course = $this->getCourse($courseId);
        if ($course['status'] != 'published') {
            E('不能收藏未发布课程');
        }
        if (empty($course)) {
            E("该课程不存在,收藏失败!");
        }
        $favorite = $this->getFavoriteDao()->getFavoriteByUserIdAndCourseId($user->id, $course['id']);
        if ($favorite) {
            E("该收藏已经存在，请不要重复收藏!");
        }
        //添加动态
        if ($course['status'] == 'published') {
            $this->getStatusService()->publishStatus(array(
                'type' => 'favorite_course',
                'objectType' => 'course',
                'objectId' => $courseId,
                'properties' => array(
                    'course' => $this->simplifyCousrse($course),
                )
            ));
        }
        $this->getFavoriteDao()->addFavorite(array(
            'courseId' => $course['id'],
            'userId' => $user->id,
            'createdTime' => time()
        ));
        return true;
    }

    public function unFavoriteCourse($courseId) {
        $user = $this->getCurrentUser(); //
        if (empty($user->id)) {
            E("UID为空");
        }
        $course = $this->getCourse($courseId);
        if (empty($course)) {
            E("该课程不存在,收藏失败!");
        }
        $favorite = $this->getFavoriteDao()->getFavoriteByUserIdAndCourseId($user->id, $course['id']);
        if (empty($favorite)) {
            E("你未收藏本课程，取消收藏失败!");
        }
        $this->getFavoriteDao()->deleteFavorite($favorite['id']);
        return true;
    }

    public function hasFavoritedCourse($courseId) {
        $user = $this->getCurrentUser(); //
        if (empty($user->id)) {
            return false;
        }
        #LiangFuJian 2015-08-06
//        $course = $this->getCourse($courseId);
//        if (empty($course)) {
//            E("课程{$courseId}不存在");
//        }
        $favorite = $this->getFavoriteDao()->getFavoriteByUserIdAndCourseId($user->id, $courseId);
        return $favorite ? true : false;
    }

    public function createAnnouncement($courseId, $fields) {
        $course = $this->tryManageCourse($courseId);
        if (!ArrayToolkit::requireds($fields, array('content'))) {
            E("课程公告数据不正确，创建失败。");
        }
        if (isset($fields['content'])) {
            $fields['content'] = $this->purifyHtml($fields['content']);  // purifyHtml???
        }
        $announcement = array();
        $announcement['courseId'] = $course['id'];
        $announcement['content'] = $fields['content'];
        $announcement['userId'] = $this->getCurrentUser()->id; //
        $announcement['createdTime'] = time();
       
        return $this->getAnnouncementDao()->addAnnouncement($announcement);
    }

    public function getCourseAnnouncement($courseId, $id) {
        $announcement = $this->getAnnouncementDao()->getAnnouncement($id);
        if (empty($announcement) or $announcement['courseId'] != $courseId) {
            return null;
        }
        return $announcement;
    }

    public function deleteCourseAnnouncement($courseId, $id) {
        $course = $this->tryManageCourse($courseId);
        $announcement = $this->getCourseAnnouncement($courseId, $id);
        if (empty($announcement)) {
            E("课程公告{$id}不存在。");
        }
        $this->getAnnouncementDao()->deleteAnnouncement($id);
    }

    public function findAnnouncements($courseId, $start, $limit) {
        return $this->getAnnouncementDao()->findAnnouncementsByCourseId($courseId, $start, $limit);
    }

    public function findAnnouncementsByCourseIds(array $ids, $start, $limit) {
        return $this->getAnnouncementDao()->findAnnouncementsByCourseIds($ids, $start, $limit);
    }

    public function updateAnnouncement($courseId, $id, $fields) {
        $course = $this->tryManageCourse($courseId);
        $announcement = $this->getCourseAnnouncement($courseId, $id);
        if (empty($announcement)) {
            E("课程公告{$id}不存在。");
        }
        if (!ArrayToolkit::requireds($fields, array('content'))) {
            E("课程公告数据不正确，更新失败。");
        }
        if (isset($fields['content'])) {
            $fields['content'] = $this->purifyHtml($fields['content']); //
        }
        return $this->getAnnouncementDao()->updateAnnouncement($id, array(
                    'content' => $fields['content']
        ));
    }

    public function generateLessonReplay($courseId, $lessonId) {
        $course = $this->tryManageCourse($courseId);
        $lesson = $this->getLessonDao()->getLesson($lessonId);
        $mediaId = $lesson["mediaId"];
        $client = LiveClientFactory::createClient();
        $replayList = $client->createReplayList($mediaId, "录播回放", $lesson["liveProvider"]);

        if (array_key_exists("error", $replayList)) {
            return $replayList;
        }
        $this->getCourseLessonReplayDao()->deleteLessonReplayByLessonId($lessonId);
        if (array_key_exists("data", $replayList)) {
            $replayList = json_decode($replayList["data"], true);
        }

        foreach ($replayList as $key => $replay) {
            $fields = array();
            $fields["courseId"] = $courseId;
            $fields["lessonId"] = $lessonId;
            $fields["title"] = $replay["subject"];
            $fields["replayId"] = $replay["id"];
            $fields["userId"] = $this->getCurrentUser()->id; //
            $fields["createdTime"] = time();
            $this->getCourseLessonReplayDao()->addCourseLessonReplay($fields);
        }
        $fields = array("replayStatus" => "generated");
        $this->getLessonDao()->updateLesson($lessonId, $fields);
        return $replayList;
    }

    public function entryReplay($lessonId, $courseLessonReplayId) {
        $lesson = $this->getLessonDao()->getLesson($lessonId);
        list($course, $member) = $this->tryTakeCourse($lesson['courseId']);
        $courseLessonReplay = $this->getCourseLessonReplayDao()->getCourseLessonReplay($courseLessonReplayId);
        $user = $this->getCurrentUser(); //

        $args = array(
            'liveId' => $lesson["mediaId"],
            'replayId' => $courseLessonReplay["replayId"],
            'provider' => $lesson["liveProvider"],
            'user' => $user->email,
            'nickname' => $user->nickname
        );
        $client = LiveClientFactory::createClient();
        $url = $client->entryReplay($args);
        return $url['url'];
    }

    public function getCourseLessonReplayByLessonId($lessonId) {
        return $this->getCourseLessonReplayDao()->getCourseLessonReplayByLessonId($lessonId);
    }

    public function deleteCourseLessonReplayByLessonId($lessonId) {
        $this->getCourseLessonReplayDao()->deleteLessonReplayByLessonId($lessonId);
    }

    public function becomeStudentByClassroomJoined($courseId, $userId, $classRoomId, array $info) {
        $fields = array(
            'courseId' => $courseId,
            'userId' => $userId,
            'orderId' => empty($info["orderId"]) ? 0 : $info["orderId"],
            'deadline' => empty($info['deadline']) ? 0 : $info['deadline'],
            'levelId' => empty($info['levelId']) ? 0 : $info['levelId'],
            'role' => 'student',
            'remark' => empty($info["orderNote"]) ? '' : $info["orderNote"],
            'createdTime' => time(),
            'classroomId' => $classRoomId,
            'joinedType' => 'classroom'
        );
        $member = $this->getMemberDao()->addMember($fields);
        return $member;
    }

    public function findCoursesByStudentIdAndCourseIds($studentId, $courseIds) {
        if (empty($courseIds) || count($courseIds) == 0) {
            return array();
        }
        $courseMembers = $this->getMemberDao()->findCoursesByStudentIdAndCourseIds($studentId, $courseIds);
        return $courseMembers;
    }

    private function autosetCourseFields($courseId) {
        $fields = array('type' => 'text', 'lessonNum' => 0);
        $lessons = $this->getCourseLessons($courseId);
        if (empty($lessons)) {
            $this->getCourseDao()->updateCourse($courseId, $fields);
            return;
        }
        $counter = array('text' => 0, 'video' => 0);
        foreach ($lessons as $lesson) {
            $counter[$lesson['type']]++;
            $fields['lessonNum']++;
        }
        $percents = array_map(function ($value) use ($fields) {
                    return $value / $fields['lessonNum'] * 100;
                }, $counter);
        if ($percents['video'] > 50) {
            $fields['type'] = 'video';
        } else {
            $fields['type'] = 'text';
        }
        $this->getCourseDao()->updateCourse($courseId, $fields);
    }

    /*
     * 无限制 不操作
     * 有限制（判断是否过期，过期在当前时间增加，没过期在截止时间增加，并解锁学员）
     * @author edit fubaosheng 2016-01-08
     */
    public function addMemberExpiryDays($courseId, $userId, $day) {
        $member = $this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if ($member['deadline'] > 0) {
            if( ($member['deadline'] - time()) > 0 ){
                $deadline = $day * 24 * 60 * 60 + $member['deadline'];
            }else{
                $deadline = $day * 24 * 60 * 60 + time();
            }
            return $this->getMemberDao()->updateMember($member['id'], array('deadline' => $deadline,'locked' => 0));
        } else {
            return true;
        }
    }

    /**
     * 添加课程分类
     * @param $id
     * @param $categoryIds
     */
    public function addCourseCategory($id, $categoryIds) {
//        $user = $this->getCurrentUser();
//        $categoryIds = explode(',', $categoryIds);
//        if (!empty($categoryIds)) {
//            $dataList = array();
//            foreach ($categoryIds as $categoryId) {
//                $tmp['categoryId'] = $categoryId;
//                $tmp['courseId'] = $id;
//                $tmp['ctm'] = time();
//                $tmp['uid'] = $user['id'];
//                $num = sprintf("%.6f", (rand(100000, 999999) / 1000000));
//                $tmp['microTm'] = sprintf("%.6f", ($tmp['ctm'] + $num));
//                $dataList[] = $tmp;
//            }
//        }
//
//        if (!empty($dataList)) {
//            $this->getCourseCategoryDao()->destroyBy('courseId', $id);
//            $res = $this->getCourseCategoryDao()->storeAll($dataList);
//        }

        $categoryIds = explode(',', $categoryIds);
        $cateId = 0;
        if(!empty($categoryIds)){
            $cateId = $categoryIds[0];
        }
        $arr = array('categoryId'=>$cateId);
        return $this->getCourseDao()->updateCourse($id, $arr);
    }

    public function selectCoursesByCategory($categoryId){
        return $this->getCourseDao()->selectCoursesByCategory($categoryId);
    }

    /**
     * 课程是否开放
     * @param $id
     * @return bool
     */
    public function isOpen($id) {
        $user = $this->getCurrentUser();
        $course = $this->getCourse($id);
        //对所有人开放
        if ($course['openRange'] == 0) {
            return true;
        }
        //只对本校学员开放
        if ($course['openRange'] == 1 && $user->isSchoolStudent()) {
            return true;
        }
        //不对任何人开放
        if ($course['openRange'] == 2) {
            return false;
        }
        return false;
    }

    /**
     * 课程是否免费
     * @param $id
     * @return bool
     */
    public function isFree($id) {
        return true;
    }

    /**
     * 获取课程分类
     * @param        $id
     * @param string $type
     * @return string
     */
    public function getCourseCategory($id, $type = '') {
        $ids = $this->getCourseCategoryRelDao()->where(array('courseId' => $id))->getField('categoryId', true);
        if ($type == 'array') {
            return $ids;
        }
        return implode(',', $ids);
    }

    /**
     * 获取课程信息
     * @param        $id
     * @return array
     */
    public function findCourseIntro($courseId) {
        return $this->getCourseDao()->findCourseIntro($courseId);
    }

    public function search($field = array('*')) {
        return $this->getCourseDao()->search($field);
    }

    public function searchCourseList($conditions = array(), $field = array('*')) {
        return $this->getCourseDao()->condition($conditions)->search($field);
    }

    public function accordingSeqGetLesson($map) {
        return $this->getLessonDao()->accordingSeqGetLesson($map);
    }

    public function accordingSeqGetChapter($map) {
        return $this->getChapterDao()->accordingSeqGetChapter($map);
    }

    /**
     * 返回课程的加入按钮显示状态
     * @author guojunqiang@redcloud.com
     *  0 禁止加入 1 加入 2 收费
     * $isPublic  0本校课程 1资源课程库
     */
    public function getCourseStudyStatus($id, $isPublic = 0) {
        $user = $this->getCurrentUser();
        $course = $this->getCourse($id);
        if (!$user->isLogin()) {
            if (CENTER == "center" || $isPublic == 1) {
                if ($course['price'] > 0) {
                    return 2;
                } else {
                    return 1;
                }
            } else {
                if ($course['openRange'] == 2) {
                    return 0;
                }
                if ($course['price'] > 0) {
                    return 2;
                } else {
                    return 1;
                }
            }
        }

        if ($user->isAdmin() && $user->webCode == $course['webCode']) {
            return 1;
        }
        if ($user->isTeacher() && $course['userId'] == $user->id && $user->webCode == $course['webCode']) {
            return 1;
        }
        if (CENTER == "center" || $isPublic == 1) {
            $buyCourse = $this->getSchoolCourseService()->getSchoolCourseInfo(array('webCode' => C('WEBSITE_CODE'), 'courseId' => $id));
            if ($buyCourse && $user->isSchoolStudent()) {
                return 1;
            }
            if ($course['price'] > 0) {
                return 2;
            } else {
                return 1;
            }
        } else {
            switch ($course['openRange']) {
                case 0://对所有人开放

                    if ($course['payRange'] == 0) {  //对所有人免费
                        $return = 1;
                    }
                    if ($course['payRange'] == 1) {  //对所有学员收费
                        $return = 2;
                    }
                    if ($course['payRange'] == 2 && $user->isSchoolStudent()) { //对本校学员免费
                        $return = 1;
                    }
                    if ($course['payRange'] == 2 && !$user->isSchoolStudent()) {
                        $return = 2;
                    }
                    break;
                case 1://只对本校学员开放
                    if (!$user->isSchoolStudent()) {
                        $return = 0;
                    } else {
                        if ($course['payRange'] == 1) {  //对所有学员收费
                            $return = 2;
                        }
                        if ($course['payRange'] == 0) {  //对所有人免费
                            $return = 1;
                        }
                    }
                    break;
                case 2://不对任何人开放
                    $return = 0;
                    break;
                default:
                    $return = 0;
                    break;
            }
            return $return;
        }
        return 0;
    }
    
    //判断用户是否可以继续学习
    public function getCourseMemberPower($courseId,$userId){
        $courseInfo=$this->getMemberDao()->getMemberByCourseIdAndUserId($courseId, $userId);
        if(!$courseInfo){
            return false;
        }
        if(($courseInfo['deadline']==0 || $courseInfo['deadline']>time()) && $courseInfo['locked']==0 && $courseInfo['payType']!='minebuy'){
            return false;
        }
        $setDec=0;
        if($courseInfo['deadline'] < time()){
             $this->lockStudent($courseId,$userId);
             if($courseInfo['payType']=='minebuy' && $courseInfo['locked']==0){
                $this->getSchoolCourseService()->setDecSchool(array('webCode' => C('WEBSITE_CODE'), 'courseId' => $courseId));
                $setDec=1;
             }
        }
        $buyCourse = $this->getSchoolCourseService()->getSchoolCourse(array('webCode' => C('WEBSITE_CODE'), 'courseId' => $courseId)); 
        if(!$buyCourse){
             return false;
        }
        if($buyCourse['isClosed']==0 && $buyCourse['endTm'] >time() && ( $buyCourse['maxNum']==0 || $buyCourse['maxNum'] > $buyCourse['useNum'] )){
             if($courseInfo['payType']=='minebuy'){
                 if($courseInfo['locked']!=0){
                    $this->getMemberDao()->updateMember($courseInfo['id'], array('locked' => 0,'deadline'=>$buyCourse['endTm']));
                    $this->getSchoolCourseService()->setIncSchool(array('webCode' => C('WEBSITE_CODE'), 'courseId' => $courseId));
                 }
             }else{
                 $this->getMemberDao()->updateMember($courseInfo['id'], array('locked' => 0,'payType'=>'minebuy','deadline'=>$buyCourse['endTm']));
                 $this->getSchoolCourseService()->setIncSchool(array('webCode' => C('WEBSITE_CODE'), 'courseId' => $courseId));
             }
        }else{
             if($courseInfo['payType']=='minebuy' && $setDec==0 && $courseInfo['locked']==0 && ($buyCourse['isClosed']!=0 ||$buyCourse['endTm'] <time() )){
                $this->lockStudent($courseId,$userId);
                $this->getSchoolCourseService()->setDecSchool(array('webCode' => C('WEBSITE_CODE'), 'courseId' => $courseId)); 
             }
        }        
    }

}

class CourseSerialize {

    public static function serialize(array &$course) {
        if (isset($course['tags'])) {
            if (is_array($course['tags']) and !empty($course['tags'])) {
                $course['tags'] = '|' . implode('|', $course['tags']) . '|';
            } else {
                $course['tags'] = '';
            }
        }

        if (isset($course['goals'])) {
            if (is_array($course['goals']) and !empty($course['goals'])) {
                $course['goals'] = '|' . implode('|', $course['goals']) . '|';
            } else {
                $course['goals'] = '';
            }
        }

        if (isset($course['audiences'])) {
            if (is_array($course['audiences']) and !empty($course['audiences'])) {
                $course['audiences'] = '|' . implode('|', $course['audiences']) . '|';
            } else {
                $course['audiences'] = '';
            }
        }

        if (isset($course['teacherIds'])) {
            if (is_array($course['teacherIds']) and !empty($course['teacherIds'])) {
                $course['teacherIds'] = '|' . implode('|', $course['teacherIds']) . '|';
            } else {
                $course['teacherIds'] = null;
            }
        }

        return $course;
    }

    public static function unserialize(array $course = null) {
        if (empty($course)) {
            return $course;
        }

        $course['tags'] = empty($course['tags']) ? array() : explode('|', trim($course['tags'], '|'));

        if (empty($course['goals'])) {
            $course['goals'] = array();
        } else {
            $course['goals'] = explode('|', trim($course['goals'], '|'));
        }

        if (empty($course['audiences'])) {
            $course['audiences'] = array();
        } else {
            $course['audiences'] = explode('|', trim($course['audiences'], '|'));
        }

        if (empty($course['teacherIds'])) {
            $course['teacherIds'] = array();
        } else {
            $course['teacherIds'] = explode('|', trim($course['teacherIds'], '|'));
        }

        return $course;
    }

    public static function unserializes(array $courses) {
        return array_map(function ($course) {
                    return CourseSerialize::unserialize($course);
                }, $courses);
    }

}

class LessonSerialize {

    public static function serialize(array $lesson) {
        return $lesson;
    }

    public static function unserialize(array $lesson = null) {
        return $lesson;
    }

    public static function unserializes(array $lessons) {
        return array_map(function ($lesson) {
                    return LessonSerialize::unserialize($lesson);
                }, $lessons);
    }
    
}

?>