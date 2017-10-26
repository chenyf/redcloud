<?php

namespace Common\Model\Thread;

use Common\Traits\ServiceTrait;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class ThreadServiceModel extends BaseModel {

    use ServiceTrait;

    private function getThreadDao() {
        return $this->createService('Thread.ThreadModel');
    }

    private function getDao() {
        return $this->createService('Thread.ThreadModel');
    }

    private function getThreadPostDao() {
//        return $this->createDao("Course.ThreadPost");
        return $this->createService('Thread.ThreadPostModel');
    }

    private function getCourseService() {
        return $this->createService('Course.CourseServiceModel');
    }

    private function getUserService() {
        return $this->createService('User.UserServiceModel');
    }

    private function getNotifiactionService() {
        return $this->createService('User.NotificationServiceModel');
    }

    private function getLogService() {
        return $this->createService('System.LogServiceModel');
    }

    private function getThreadSettingDao() {
        return $this->createService('Thread.ThreadSettingModel');
    }

    private function getCourseMemberDao() {
        return $this->createDao("Course.CourseMember");
    }

    private function getThreadCollectDao() {
        return $this->createService('Thread.ThreadCollectModel');
    }

    private function getThreadStatisticDao() {
        return $this->createService('Thread.ThreadStatistic');
    }

    private function getThreadEventRelDao() {
        return $this->createService('Thread.ThreadEventRel');
    }

    public function getThread($courseId, $threadId, $isClosed = 0) {
        $thread = $this->getThreadDao()->getThread($threadId, $isClosed);
        if (empty($thread)) {
            return null;
        }
        return $thread['courseId'] == $courseId ? $thread : null;
    }

    public function getTeacherDao() {
        return $this->createService('Thread.ThreadTeacherModel');
    }

    public function getThreadCommentDao() {
        return $this->createService('Thread.ThreadCommentModel');
    }

    public function getThreadGiveUpDao() {
        return $this->createService('Thread.ThreadGiveUpModel');
    }

    public function findThreadsByType($courseId, $type, $sort = 'latestCreated', $start, $limit) {
        if ($sort == 'latestPosted') {
            $orderBy = array('latestPosted', 'DESC');
        } else {
            $orderBy = array('createdTime', 'DESC');
        }
        if (!in_array($type, array('question', 'discussion'))) {
            $type = 'all';
        }
        if ($type == 'all') {
            return $this->getThreadDao()->findThreadsByCourseId($courseId, $orderBy, $start, $limit);
        }
        return $this->getThreadDao()->findThreadsByCourseIdAndType($courseId, $type, $orderBy, $start, $limit);
    }

    public function getThreadsCountByType(array $cond,$type){
        $conditions = array();
        $userId = $this->getCurrentUser()->id;
        if($type == 'newReply'){
            $conditions['toUId'] = $userId;
            $conditions['type'] = array('in', 'post,reply');
            $threads = $this->searchThreadsEvent($conditions, array('createdTime', 'DESC'), 0, 999999);
            $postIds = array_unique(ArrayToolkit::column($threads, 'id'));
            $conditions = array();
            $conditions['ids'] = $postIds;
        }

        if($type == 'myThread'){
            $conditions['userId'] = $userId;
        }

        if($type == 'myPost'){
            $conditions['userId'] = $userId;
            $conditions['pid'] = 0;
            $posts = $this->searchPosts($conditions, array('createdTime', 'DESC'), 0, $this->searchPostCounts($conditions));
            $postIds = array_unique(ArrayToolkit::column($posts, 'threadId'));
            $conditions = array();
            $conditions['ids'] = $postIds;
        }

        if($type == 'myReply'){
            $conditions['userId'] = $userId;
            $conditions['pid'] = array('neq', 0);
            $posts = $this->searchPosts($conditions, array('createdTime', 'DESC'), 0, $this->searchPostCounts($conditions));
            $postIds = array_unique(ArrayToolkit::column($posts, 'threadId'));
            $conditions = array();
            $conditions['ids'] = $postIds;
        }

        if($type == 'myCollection'){
            $conditions = array();
            $conditions['userId'] = $userId;
            $myCollection = $this->searchThreadCollection($conditions, array('createdTime', 'DESC'), 0, $this->searchThreadCollectionCounts($conditions));
            $postIds = array_unique(ArrayToolkit::column($myCollection, 'threadId'));
            $conditions = array();
            $conditions['ids'] = $postIds;
        }

        $conditions = array_merge($cond,$conditions);

        if (isset($conditions['ids']) && empty($conditions['ids'])) {
            return 0;
        } else {
            $conditions = $this->prepareThreadSearchConditions($conditions);
            $countResult = $this->getThreadDao()->getThreadsCountByType($conditions);
            return $countResult?:0;
        }
    }

    public function findLatestThreadsByType($type, $start, $limit) {
        return $this->getThreadDao()->findLatestThreadsByType($type, $start, $limit);
    }

    public function getThreadFind($id) {
        return $this->getThreadDao()->getThread($id);
    }

    public function getThreadQuestionCount($map) {
        return $this->getThreadDao()->getThreadQuestionCount($map);
    }

    public function getThreadQuestion($map, $order, $p, $preNum) {
        return $this->getThreadDao()->getThreadQuestion($map, $order, $p, $preNum);
    }

    public function getFindThread($askid) {
        return $this->getThreadDao()->getFindThread($askid);
    }

    public function findEliteThreadsByType($type, $status, $start, $limit) {
        return $this->getThreadDao()->findEliteThreadsByType($type, $status, $start, $limit);
    }

    public function searchThreads($conditions, $sort, $start, $limit) {
        $orderBys = $this->filterSort($sort);
        $conditions = $this->prepareThreadSearchConditions($conditions);
        return $this->getThreadDao()->searchThreads($conditions, $orderBys, $start, $limit);
    }

    public function searchThreadCount($conditions) {

        $conditions = $this->prepareThreadSearchConditions($conditions);

        return $this->getThreadDao()->searchThreadCount($conditions);
    }

    public function searchThreadCountInCourseIds($conditions) {
        $conditions = $this->prepareThreadSearchConditions($conditions);
        return $this->getThreadDao()->searchThreadCountInCourseIds($conditions);
    }

    public function searchThreadInCourseIds($conditions, $sort, $start, $limit) {
        $orderBys = $this->filterSort($sort);
        $conditions = $this->prepareThreadSearchConditions($conditions);
        return $this->getThreadDao()->searchThreadInCourseIds($conditions, $orderBys, $start, $limit);
    }

    private function filterSort($sort) {
        switch ($sort) {
            case 'created':
                $orderBys = array(
                    array('isStick', 'DESC'),
                    array('createdTime', 'DESC'),
                );
                break;
            case 'posted':
                $orderBys = array(
                    array('isStick', 'DESC'),
                    array('latestPostTime', 'DESC'),
                );
                break;
            case 'createdNotStick':
                $orderBys = array(
                    array('createdTime', 'DESC'),
                );
                break;
            case 'postedNotStick':
                $orderBys = array(
                    array('latestPostTime', 'DESC'),
                );
                break;
            case 'popular':
                $orderBys = array(
                    array('hitNum', 'DESC'),
                );
                break;
            default:
                E('参数sort不正确。');
        }
        return $orderBys;
    }

    private function prepareThreadSearchConditions($conditions) {
        if (empty($conditions['keyword'])) {
            unset($conditions['keyword']);
            unset($conditions['keywordType']);
        }
        if (isset($conditions['keywordType']) && isset($conditions['keyword'])) {
            if (!in_array($conditions['keywordType'], array('title', 'content', 'courseId', 'courseTitle'))) {
                E('keywordType参数不正确');
            }
            $conditions[$conditions['keywordType']] = $conditions['keyword'];
            unset($conditions['keywordType']);
            unset($conditions['keyword']);
        }
        if (empty($conditions['author'])) {
            unset($conditions['author']);
        }
        if (isset($conditions['author'])) {
            $author = $this->getUserService()->getUserByNickname($conditions['author']);
            $conditions['userId'] = $author ? $author['id'] : -1;
        }

        if (!isset($conditions['isClosed'])) {
            $conditions['isClosed'] = 0;
        }


        return $conditions;
    }

    /**
     * 创建话题
     */
    public function createThread($thread) {
        if (empty($thread['courseId'])) {
            E('Course ID can not be empty.');
        }

        list($course, $member) = $this->getCourseService()->tryTakeCourse($thread['courseId']);
        $thread['userId'] = $this->getCurrentUser()->id; //

        $thread['title'] = empty($thread['title']) ? '' : $thread['title']; //插入数据库自动转义
        //创建thread过滤html
        $thread['content'] = $this->purifyHtml($thread['content']); //purifyHtml
        $thread['createdTime'] = time();
        $thread['latestPostUserId'] = $thread['userId'];
        $thread['latestPostTime'] = $thread['createdTime'];
        $thread['audioId'] = isset($thread['audioId']) ? intval($thread['audioId']) : 0;
        $thread['isCourseMember'] = $this->isCourseMember($thread['courseId'],$thread['userId']) ? 1 : 0;

        $thread = $this->getThreadDao()->addThread($thread);

        #插入统计数据
        $this->insertStatistic($thread['courseId'], 'threadNum');
        $this->insertStatistic($thread['courseId'], 'courseMemberNum');

        foreach ($course['teacherIds'] as $teacherId) {
            if ($teacherId == $thread['userId']) {
                continue;
            }
            $this->getNotifiactionService()->notify($teacherId, 'thread', array(
                'threadId' => $thread['id'],
                'threadUserId' => $thread['userId'],
                'threadUserNickname' => $this->getCurrentUser()->nickname,
                'threadTitle' => $thread['title'],
                'threadType' => $thread['type'],
                'courseId' => $course['id'],
                'courseTitle' => $course['title'],
            ));
        }
        return $thread;
    }

    public function updateThread($courseId, $threadId, $fields) {

        $thread = $this->getThread($courseId, $threadId);
        if (empty($thread)) {
            E('话题不存在，更新失败！');
        }
        $user = $this->getCurrentUser(); //
        ($user->isLogin() and $user->id == $thread['userId']) or $this->getCourseService()->tryManageCourse($courseId);
//        $fields = ArrayToolkit::parts($fields, array('title', 'content'));
//        
//        if (empty($fields)) {
//            E('参数缺失，更新失败。');
//        }
        //更新thread过滤html
        $fields['content'] = $this->purifyHtml($fields['content']); //purifyHtml
        return $this->getThreadDao()->updateThread($threadId, $fields);
    }

    /*
     * 更新锁定的提问
     * @isLock true重新锁定 false取消锁定
     * @author Yao 2016/4/7
     */

    public function updateLockedThread($courseId, $threadId, $fields, $isLock = true) {
        $thread = $this->getThread($courseId, $threadId);
        if (empty($thread)) {
            E('话题不存在，更新失败！');
        }
        $user = $this->getCurrentUser();

        if ($isLock) {
            $conditions['courseId'] = $courseId;
            $conditions['threadId'] = $threadId;
            $conditions['fromUId'] = $user['id'];
            $conditions['type'] = 'lock';
            if (!$this->getThreadEvent($conditions)) {
                $conditions['createdTime'] = time();
                $this->getThreadEventRelDao()->insertThreadEvent($conditions);
            }
        } else {
            $conditions['courseId'] = $courseId;
            $conditions['threadId'] = $threadId;
            $conditions['fromUId'] = $user['id'];
            $conditions['type'] = 'lock';
            $this->deleteThreadEvent($conditions);
        }
        return $this->getThreadDao()->updateThread($threadId, $fields);
    }

    /*
     * 获取提问抢答状况 
     * @return array;
     */

    public function getGrabStatus($courseId, $id) {
        $status = array(
            '1000' => '我抢占的题目，剩余回答时间为',
            '1001' => '对不起，该问题已经被其他老师抢占回答',
            '1003' => '可以回答',
        );
        $user = $this->getCurrentUser();
        $uid = $user->id;
        $thread = $this->getThread($courseId, $id);
        if ($thread['grabMode']) {
            
        }
    }

   

    /*
     * 是否是答疑老师
     */

    public function isAnswerTeacher($courseId, $uid) {
        $isTeacher = $this->isThreadTeacher($courseId, $uid);
        if ($isTeacher || in_array($courseId, $this->getCourseService()->getUserCourseId($uid))) {
            return true;
        }
        return false;
    }

    /*
     * 是否允许非本课程学员提问
     */

    public function isAllowNotMemberThread($courseId) {
        $setting = $this->getCourseThreadSetting($courseId);
        if ($setting['isAllowPost'])
            return true;
        else
            return false;
    }

    /*
     * 是否可以锁定提问
     */

    public function isCanLockThread($courseId) {
        $user = $this->getCurrentUser();
        $uid = $user->id;
        $setting = $this->getCourseThreadSetting($courseId);
        if ($setting['maxNumber'] == 0) {
            return ture;
        } else {
            $conditions['courseId'] = $courseId;
            $conditions['fromUId'] = $uid;
            $conditions['type'] = 'lock';
            $lockedCounts = $this->getThreadEventRelDao()->getThreadEventCount($conditions);
            if ($lockedCounts >= $setting['maxNumber']) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    public function getThreadEventCount($conditions) {
        return $this->getThreadEventRelDao()->getThreadEventCount($conditions);
    }

    public function deleteThread($threadId) {
        $thread = $this->getThreadDao()->getThreadById($threadId);
        if (empty($thread)) {
            E(sprintf('话题(ID: %s)不存在。', $threadId));
        }
        if (!$this->getCourseService()->canManageCourse($thread['courseId'])) {
            E('您无权限删除该话题');
        }
        $this->getThreadPostDao()->deletePostsByThreadId($threadId);
        $this->getThreadDao()->deleteThread($threadId);
        $this->getLogService()->info('thread', 'delete', "删除话题 {$thread['title']}({$thread['id']})");
    }

    public function recoverThread($threadId) {
        $thread = $this->getThreadDao()->getThreads(false, "t.id={$threadId}");
        $thread = $thread && count($thread) ? $thread[0] : false;
        if (!$thread) {
            E(sprintf('话题(ID: %s)不存在。', $threadId));
        }
        if (!$this->getCourseService()->canManageCourse($thread['courseId'])) {
            E('您无权限恢复该话题');
        }
        $this->getThreadPostDao()->deletePostsByThreadId($threadId);
        $this->getThreadDao()->recoverThread($threadId);
        $this->getLogService()->info('thread', 'recover', "恢复话题 {$thread['title']}({$thread['id']})");
    }

    public function stickThread($courseId, $threadId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $thread = $this->getThread($courseId, $threadId);
        if (empty($thread)) {
            E(sprintf('话题(ID: %s)不存在。', $thread['id']));
        }
        $this->getThreadDao()->updateThread($thread['id'], array('isStick' => 1));
    }

    public function unstickThread($courseId, $threadId) {
        $thread = $this->getThread($courseId, $threadId);
        if (empty($thread)) {
            E(sprintf('话题(ID: %s)不存在。', $thread['id']));
        }
        $this->getThreadDao()->updateThread($thread['id'], array('isStick' => 0));
    }

    public function eliteThread($courseId, $threadId) {
        $thread = $this->getThread($courseId, $threadId);
        if (empty($thread)) {
            E(sprintf('话题(ID: %s)不存在。', $thread['id']));
        }
        $this->getThreadDao()->updateThread($thread['id'], array('isElite' => 1));
    }

    public function uneliteThread($courseId, $threadId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $thread = $this->getThread($courseId, $threadId);
        if (empty($thread)) {
            E(sprintf('话题(ID: %s)不存在。', $thread['id']));
        }
        $this->getThreadDao()->updateThread($thread['id'], array('isElite' => 0));
    }

    /**
     * 点击查看话题
     *
     * 此方法，用来增加话题的查看数。
     * 
     * @param integer $courseId 课程ID
     * @param integer $threadId 话题ID
     * 
     */
    public function hitThread($courseId, $threadId) {
        $this->getThreadDao()->waveThread($threadId, 'hitNum', +1);
    }

    /**
     * 获得话题的回帖
     * 
     * @param integer  $courseId 话题的课程ID
     * @param integer  $threadId 话题ID
     * @param string  	$sort     排序方式： defalut按帖子的发表时间顺序；best按顶的次序排序。
     * @param integer 	$start    开始行数
     * @param integer 	$limit    获取数据的限制行数
     * 
     * @return array 获得的话题回帖列表。
     */
    public function findThreadPosts($courseId, $threadId, $sort = 'default', $start, $limit) {
        $thread = $this->getThread($courseId, $threadId);
        if (empty($thread)) {
            return array();
        }
        if ($sort == 'best') {
            $orderBy = array('score', 'DESC');
        } else if ($sort == 'elite') {
            $orderBy = array('createdTime', 'DESC', ',isElite', 'ASC');
        } else {
            $orderBy = array('createdTime', 'ASC');
        }
        return $this->getThreadPostDao()->findPostsByThreadId($threadId, $orderBy, $start, $limit);
    }

    public function getPostCountByuserIdAndThreadId($userId, $threadId) {
        return $this->getThreadPostDao()->getPostCountByuserIdAndThreadId($userId, $threadId);
    }

    public function getThreadPostCountByThreadId($threadId) {
        return $this->getThreadPostDao()->getPostCountByThreadId($threadId);
    }

    ## 吕五龙 新增 BEGIN  #########################################################################
    /**
     * 获取问题列表
     * @author lvwulong 2016.4.5
     */
    public function getThreads($tables, $fields, $map, $page, $preNum) {
        return $this->getThreadDao()->getThreads($tables, $fields, $map, $page, $preNum);
    }
    /**
     * 获取问题总数量
     * @author lvwulong 2016.4.1
     */
    public function getThreadsCount($tables, $map) {
        return $this->getThreadDao()->getThreadsCount($tables, $map);
    }
    /**
     * 获取问题回答/回复列表
     * @author lvwulong 2016.4.5
     */
    public function getThreadPosts($isAnswer, $threadId, $page, $preNum, $answerId = 0) {
        $dao = $this->getThreadPostDao();
        if($isAnswer){
            return $dao->getAnswerList($threadId, $page, $preNum);
        }else{
            return $answerId ? $dao->getAnswerPostList($threadId, $answerId, $page, $preNum) : array('list' => array(), 'lastPostId' => 0);
        }
    }
    /**
     * 获取问题回答/回复总数量
     * @author lvwulong 2016.4.5
     */
    public function getThreadPostsCount($isAnswer, $threadId, $answerId = 0) {
        $dao = $this->getThreadPostDao();
        $count = 0;
        if($isAnswer){
            $count = $dao->getAnswerCount($threadId);
        }else{
            $count = $answerId ? $dao->getAnswerPostCount($threadId, $answerId) : 0;
        }
        return "{$count}";
    }
    ## 吕五龙 新增 END  #########################################################################

    
    
    /**
     * 获得话题回帖的数量
     * @param  integer  $courseId 话题的课程ID
     * @param  integer  $threadId 话题ID
     * @return integer  话题回帖的数量
     */
    public function getThreadPostCount($courseId, $threadId) {
        return $this->getThreadPostDao()->getPostCountByThreadId($threadId);
    }

    public function findThreadElitePosts($courseId, $threadId, $start, $limit) {
        return $this->getThreadPostDao()->findPostsByThreadIdAndIsElite($threadId, 1, $start, $limit);
    }

    public function getCourseThreadPost($askid) {
        return $this->getThreadPostDao()->getCourseThreadPost($askid);
    }

    /**
     * 回复话题
     * */
    public function getPost($courseId, $id) {
        $post = $this->getThreadPostDao()->getPost($id);
        if (empty($post) or $post['courseId'] != $courseId) {
            return null;
        }
        return $post;
    }

    public function createPost($post) {

        $requiredKeys = array('courseId', 'threadId', 'content');
        if (!ArrayToolkit::requireds($post, $requiredKeys)) {
            E('参数缺失');
        }
        $thread = $this->getThread($post['courseId'], $post['threadId']);
        if (empty($thread)) {
            E(sprintf('课程(ID: %s)话题(ID: %s)不存在。', $post['courseId'], $post['threadId']));
        }

        list($course, $member) = $this->getCourseService()->tryTakeCourse($post['courseId']);
        $post['userId'] = $this->getCurrentUser()->id; //
        $post['isElite'] = $this->getCourseService()->isCourseTeacher($post['courseId'], $post['userId']) ? 1 : 0;
        $post['createdTime'] = time();

        //创建post过滤html
        $post['content'] = $this->purifyHtml($post['content']); //
        $post = $this->getThreadPostDao()->addPost($post);

        if ($post['type'] == 'post') {
            // 高并发的时候， 这样更新postNum是有问题的，这里暂时不考虑这个问题。
            $threadFields = array(
                'postNum' => $thread['postNum'] + 1,
                'latestPostUserId' => $post['userId'],
                'latestPostTime' => $post['createdTime'],
            );
            $this->getThreadDao()->updateThread($thread['id'], $threadFields);
        }

        return $post;
    }

    public function updatePost($courseId, $id, $fields) {
        $post = $this->getPost($courseId, $id);
        if (empty($post)) {
            E("回帖#{$id}不存在。");
        }
        $user = $this->getCurrentUser(); //
        ($user->isLogin() and $user->id == $post['userId']) or $this->getCourseService()->tryManageCourse($courseId);
        $fields = ArrayToolkit::parts($fields, array('content'));
        if (empty($fields)) {
            E('参数缺失。');
        }
        //更新post过滤html
        $fields['content'] = $this->purifyHtml($fields['content']); //
        return $this->getThreadPostDao()->updatePost($id, $fields);
    }

    public function deletePost($courseId, $id) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $post = $this->getThreadPostDao()->getPost($id);
        if (empty($post)) {
            E(sprintf('帖子(#%s)不存在，删除失败。', $id));
        }
        if ($post['courseId'] != $courseId) {
            E(sprintf('帖子#%s不属于课程#%s，删除失败。', $id, $courseId));
        }
        $this->getThreadPostDao()->deletePost($post['id']);
        $this->getThreadDao()->waveThread($post['threadId'], 'postNum', -1);
    }

    /**
     * 我的提问
     * @author fubaosheng 2015-11-30
     */
    public function myAsk($p, $preNum) {
        $user = $this->getCurrentUser();
        if (!$user->isLogin())
            E("尚未登录");
        return $this->getThreadDao()->myAsk($user['id'], $p, $preNum);
    }

    /**
     * 我的提问总数
     * @author fubaosheng 2015-11-30
     */
    public function myAskCount() {
        $user = $this->getCurrentUser();
        if (!$user->isLogin())
            E("尚未登录");
        $paramArr["userId"] = $user["id"];
        $paramArr["type"] = "question";
        return $this->getThreadDao()->searchThreadCount($paramArr);
    }

    /**
     * 我的回答
     * @author fubaosheng 2015-11-30
     */
    public function myAnswer($p, $preNum) {
        $user = $this->getCurrentUser();
        if (!$user->isLogin())
            E("尚未登录");
        return $this->getThreadDao()->myAnswer($user['id'], $p, $preNum);
    }

    /**
     * 我的回答总数
     * @author fubaosheng 2015-11-30
     */
    public function myAnswerCount() {
        $user = $this->getCurrentUser();
        if (!$user->isLogin())
            E("尚未登录");
        return $this->getThreadDao()->myAnswerCount($user['id']);
    }

    public function getCourseThreadSetting($courseId) {
        $setting = $this->getThreadSettingDao()->getCourseThreadSetting($courseId);
        if (!$setting) {
            #读取默认配置文件
            $setting = array();
            $conf = C('COURSE_THREAD');
            $setting['isAllowPost'] = $conf['is_allow_post'];
            $setting['isNeedPost'] = $conf['is_need_post'];
            $setting['isGrabMode'] = $conf['is_grab_mode'];
            $setting['maxNumber'] = $conf['max_number'];
            $setting['maxTime'] = $conf['max_time'];
        }

        $setting['maxTime'] = $setting['maxTime'] / 60;
        return $setting;
    }

    public function searchTeacherCount($conditions) {
        return $this->getTeacherDao()->searchTeacherCount($conditions);
    }

    public function searchTeacher($conditions, $orderBys, $start, $limi) {
        return $this->getTeacherDao()->searchTeacher($conditions, $orderBys, $start, $limi);
    }

    public function findAnswerTeacher($conditions) {
        return $this->getTeacherDao()->findAnswerTeacher($conditions);
    }

    /*
     * 判断用户是否本课程学员
     * @author Yao 2016-3-22
     */

    public function isCourseMember($courseId, $uid) {
        return true;
    }

    /*
     * 收藏
     */

    public function getThreadCollectByThreadId($courseId, $threadId, $userId) {
        return $this->getThreadCollectDao()->getThreadCollectByThreadId($courseId, $threadId, $userId);
    }

    public function searchThreadCollection($conditions, $orderBy, $start, $limit) {
        return $this->getThreadCollectDao()->searchThreadCollection($conditions, $orderBy, $start, $limi);
    }

    public function searchThreadCollectionCounts($conditions) {
        return $this->getThreadCollectDao()->searchThreadCollectionCounts($conditions);
    }

    public function threadCollect($fields) {
        $thread = $this->getThread($fields['courseId'], $fields['threadId']);
        if (empty($thread)) {
            E(sprintf('话题(ID: %s)不存在。', $thread['id']));
        }
        return $this->getThreadCollectDao()->addThreadCollect($fields);
    }

    public function unThreadCollect($conditions) {
        return $this->getThreadCollectDao()->deleteThreadCollect($conditions);
    }

    //添加答疑老师
    public function addThreadTeacher($fields) {
        $option = array(
            'courseId' => 0, #课程ID
            'userId' => 0, #用户ID
            'email' => '', #邮箱ID
            'addUId' => 0, #添加者用户
            'nickname' => '', #昵称
        );

        $fields = array_merge($option, $fields);
        $fields['createdTime'] = time();
        $course = $this->getCourseService()->tryManageCourse($fields['courseId']);

        return $this->getTeacherDao()->addThreadTeacher($fields);
    }

    public function deleteThreadTeacher($id) {
        return $this->getTeacherDao()->deleteThreadTeacher($id);
    }

    public function saveThreadSetting($fields) {
        $data = ArrayToolkit::filter($fields, array(
                    'courseId' => 0,
                    'isAllowPost' => 0,
                    'isNeedPost' => 0,
                    'isGrabMode' => 0,
                    'maxNumber' => 0,
                    'maxTime' => 0,
        ));
        $course = $this->getCourseService()->tryManageCourse($fields['courseId']);
        $data['createdTime'] = time();
        $data['maxTime'] = $data['maxTime'] * 60;
        $setting = $this->getThreadSettingDao()->getCourseThreadSetting($course['id']);
        if (!$setting) {
            return $this->getThreadSettingDao()->addSetting($data);
        }
        return $this->getThreadSettingDao()->updSeeting($course['id'], $data);
    }

    #获取抢答模式

    public function getGrabMode($threadId) {
        $thread = $this->getThreadDao()->getThread($threadId);
        if (!$thread) {
            E('thread is not find!');
        }
        return $thread['grabMode'] ? TRUE : FALSE;
    }

    public function searchPosts($conditions, $orderBy, $start, $limit) {
        return $this->getThreadPostDao()->searchPosts($conditions, $orderBy, $start, $limit);
    }

    public function searchPostCounts($conditions) {
        return $this->getThreadPostDao()->searchPostCounts($conditions);
    }

    public function getPostNumByTeacher($conditions) {
        $conditions = $this->_createConditions($conditions);
        return $this->getThreadPostDao()->getPostNumByTeacher($conditions);
    }

    public function getPostCountByTeacher($conditions) {
        $conditions = $this->_createConditions($conditions);
        return $this->getThreadPostDao()->getPostCountByTeacher($conditions);
    }

    public function getBestPostNum($conditions) {
        $conditions = $this->_createConditions($conditions);
        $conditions['isBestAnswer'] = 1;

        return $this->getThreadPostDao()->getBestPostNum($conditions);
    }

    public function searchCommentCounts($conditions) {
        return $this->getThreadCommentDao()->searchCommentCounts($conditions);
    }

    public function searchComment($conditions, $orderBy, $start, $limit) {
        return $this->getThreadCommentDao()->searchComment($conditions, $orderBy, $start, $limit);
    }

    public function getAvgScore($conditions) {
        if (isset($conditions['userId'])) {
            $conditions['teacherId'] = $conditions['userId'];
            unset($conditions['userId']);
        }
        $avgScore = $this->getThreadCommentDao()->getAvgScore($conditions);
        return round($avgScore, 2);
    }

    public function addComment($fields) {
        $fields['createdTime'] = time();
        return $this->getThreadCommentDao()->addComment($fields);
    }

    public function findThreadComment($conditions) {
        $tmpConditions = $conditions;
        unset($conditions);
        if (isset($tmpConditions['courseId'])) {
            $conditions['courseId'] = $tmpConditions['courseId'];
        }
        if (isset($tmpConditions['threadId'])) {
            $conditions['threadId'] = $tmpConditions['threadId'];
        }
        if (isset($tmpConditions['teacherId'])) {
            $conditions['teacherId'] = $tmpConditions['teacherId'];
        }
        if (isset($tmpConditions['postId'])) {
            $conditions['postId'] = $tmpConditions['postId'];
        }
        return $this->getThreadCommentDao()->findThreadComment($conditions);
    }

    public function getAppendCounts($conditions) {

        return $this->getThreadPostDao()->getAppendCounts($conditions);
    }

    public function getThreadsGroupByDate($conditions) {
        return $this->getThreadDao()->getThreadsGroupByDate($conditions);
    }

    public function getThreadStatistic($courseId) {
        return $this->getThreadStatisticDao()->getThreadStatistic($courseId);
    }

    public function getStatistic($courseId) {
        return $this->getThreadStatisticDao()->getStatistic($courseId);
    }

    public function insertStatistic($courseId, $fields) {
        $statisticDate = date('Y-m-d', time());
        $where['statisticDate'] = $statisticDate;
        $where['courseId'] = $courseId;
        $statistic = $this->getThreadStatisticDao()->getStatisticByDateTime($where);
        if (!$statistic) {
            $where[$fields] = 1;
            return $this->getThreadStatisticDao()->insertStatistic($where);
        }
        return $this->getThreadStatisticDao()->updateStatistic($where, $fields);
    }

    public function isThreadTeacher($courseId, $userId) {
        return $this->getTeacherDao()->isThreadTeacher($courseId, $userId);
    }

    public function getTeacherCourseId($userId) {
        $data = $this->getTeacherDao()->getTeacherCourseId($userId);
        return ArrayToolkit::column($data, 'courseId');
    }

    public function searchThreadEventCount($conditions) {
        return $this->getThreadEventRelDao()->searchThreadEventCount($conditions);
    }

    public function searchThreadsEvent($conditions, $orderBy, $start, $limit) {
        return $this->getThreadEventRelDao()->searchThreadsEvent($conditions, $orderBy, $start, $limit);
    }

    public function findEventThreadIds($ids) {
        return ArrayToolkit::column($this->getThreadEventRelDao()->findEventThreadIds($ids), 'threadId');
    }

    #是否是问题提问者

    public function isThreadUser($threadId, $userId) {
        return $this->getThreadDao()->isThreadUser($threadId, $userId) ? TRUE : false;
    }

    public function getThreadEvent($conditions) {
        return $this->getThreadEventRelDao()->getThreadEvent($conditions);
    }

    public function deleteThreadEvent($conditions) {
        return $this->getThreadEventRelDao()->deleteThreadEvent($conditions);
    }

    public function insertThreadEvent($fields) {
        $fields['createdTime'] = time();
        return $this->getThreadEventRelDao()->insertThreadEvent($fields);
    }

    public function updateThreadEvent($conditions, $fields) {
        $fields['createdTime'] = time();
        return $this->getThreadEventRelDao()->updateThreadEvent($conditions, $fields);
    }

    // edit by lvwulong 2016.4.11
    public function setBestAnswer($conditions, $answerId, $score = 0) {
        $post = $this->getThreadPostDao()->setBestAnswer($answerId, $score);
        if ($post && $this->getThreadDao()->setBestAnswerId($conditions, $answerId)) {
            return $post;
        }
        return FALSE;
    }

    public function getTeacher($userId) {
        return $this->getTeacherDao()->getTeacher($userId);
    }

    public function threadFieldsInc($id, $fields) {
        return $this->getThreadDao()->threadFieldsInc($id, $fields);
    }

    public function searchPostManageCounts($conditions) {
        return $this->getThreadPostDao()->searchPostManageCounts($conditions);
    }

    public function searchPostsManage($conditions, $orderBy, $start, $limit) {
        return $this->getThreadPostDao()->searchPostsManage($conditions, $orderBy, $start, $limit);
    }

    public function getReplyCounts($conditions) {
        return $this->getThreadPostDao()->getReplyCounts($conditions);
    }

    public function getAvgResponseTime($conditions) {
        return round($this->getThreadPostDao()->getAvgResponseTime($conditions), 2);
    }

    public function addThreadGiveup($fields) {
        $fields['createdTime'] = time();
        return $this->getThreadGiveUpDao()->addGiveUp($fields);
    }

    public function findGiveUpCounts($conditions) {
        return $this->getThreadGiveUpDao()->findGiveUpCounts($conditions);
    }

    public function getDataStatisticsByTeacher($userId, $threadIds, $courseId = 0) {

        $conditions['threadId'] = array('in', $threadIds);
        $conditions['userId'] = $userId;

        if (!empty($courseId)) {
            #答疑老师创建时间
            $answerTeacher = $this->findAnswerTeacher(array('courseId' => $courseId, 'userId' => $userId));
            $conditions['createdTime'] = array('GT', $answerTeacher['createdTime']);
        }

        #回答问题数量
        $conditions['type'] = 'post';
        $data['num'] = $this->getPostNumByTeacher($conditions);

        #回答问题次数
        $data['count'] = $this->getPostCountByTeacher($conditions);

        #被选为最佳数
        $conditions['isBestAnswer'] = 1;
        $data['bestAnswerNum'] = $this->getBestPostNum($conditions);
        unset($conditions['isBestAnswer']);
        unset($conditions['type']);

        #平均得分
        $data['avgScore'] = $this->getAvgScore($conditions);

        #回答响应时间
        $data['avgReponseTime'] = $this->getAvgResponseTime($conditions);

        #回答追问总次数
        $conditions['type'] = 'replyAppend';
        $data['replyAppendCounts'] = $this->getReplyCounts($conditions);

        #回复追问响应时间
        $data['avgReplyTime'] = $this->getAvgResponseTime($conditions);
        unset($conditions['type']);

        #回复问题问题总次数
        $conditions['type'] = 'reply';
        $data['replyCounts'] = $this->getReplyCounts($conditions);
        unset($conditions['type']);

        #放弃回答数
        $data['giveUpCounts'] = $this->findGiveUpCounts($conditions);

        return $data;
    }

    private function _createConditions($conditions) {
        if (!empty($conditions['startTime']) && !empty($conditions['endTime'])) {
            $conditions['threadTime'] = array('between', array($conditions['startTime'], $conditions['endTime']));
            unset($conditions['startTime']);
            unset($conditions['endTime']);
        }
        return $conditions;
    }

}

?>
