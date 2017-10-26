<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class NoteServiceModel extends BaseModel{
    
    CONST NOTE_TYPE_PRIVATE = 0;
    CONST NOTE_TYPE_PUBLIC = 1;
    
    private function getNoteDao(){
	    return $this->createService('Course.CourseNoteModel');
    }

    private function getCourseService(){
        return $this->createService('Course.CourseServiceModel');
    }

    private function getUserService(){
        return $this->createService('User.UserServiceModel');
    }

    private function getLogService(){
        return $this->createService('System.LogServiceModel');
    }
    
    public function getNote($id){
        return $this->getNoteDao()->getNote($id);
    }

    public function getUserLessonNote($userId, $lessonId){
        return $this->getNoteDao()->getNoteByUserIdAndLessonId($userId, $lessonId);
    }

    public function findUserCourseNotes($userId, $courseId){
         return $this->getNoteDao()->findNotesByUserIdAndCourseId($userId, $courseId);
    }

    public function searchNotes($conditions, $sort, $start, $limit){
        switch ($sort) {
            case 'created':
                $orderBy = array('createdTime', 'DESC');
                break;
            case 'updated':
                $orderBy =  array('updatedTime', 'DESC');
                break;
            default:
                E('参数sort不正确。');
        }
        $conditions = $this->prepareSearchNoteConditions($conditions);
        return $this->getNoteDao()->searchNotes($conditions, $orderBy, $start, $limit);
    }

    public function searchNoteCount($conditions){
        $conditions = $this->prepareSearchNoteConditions($conditions);
        return $this->getNoteDao()->searchNoteCount($conditions);
    }
    
    /**
     *类似这样的，提交数据保存到数据的流程是：
     *
     *  1. 检查参数是否正确，不正确就抛出异常
     *  2. 过滤数据
     *  3. 插入到数据库
     *  4. 更新其他相关的缓存字段
     */
    public function saveNote(array $note){
        if (!ArrayToolkit::requireds($note, array('lessonId', 'courseId', 'content'))) {
            E('缺少必要的字段，保存笔记失败');
        }
        list($course, $member) = $this->getCourseService()->tryTakeCourse($note['courseId']);
        $user = $this->getCurrentUser();//
        if(!$this->getCourseService()->getCourseLesson($note['courseId'], $note['lessonId'])) {
//            E('课程内容不存在，保存笔记失败');
        }
        $note = ArrayToolkit::filter($note, array(
            'courseId' => 0,
            'lessonId' => 0,
            'content' => '',
        ));
        $note['content'] = $this->purifyHtml($note['content']) ? : ''; //purifyHtml???
        $note['length'] = $this->calculateContnentLength($note['content']);

//        不管该lesson有没有记过笔记，都可以再添加，不限制同一course/lesson的笔记数
//        $note['userId'] = $user['id'];
//        $note['createdTime'] = time();
//        $note = $this->getNoteDao()->addNote($note);

        $existNote = $this->getUserLessonNote($user['id'], $note['lessonId']);
        if (!$existNote) {
            $note['userId'] = $user['id'];
            $note['createdTime'] = time();
            $note = $this->getNoteDao()->addNote($note);

            $this->getCourseService()->setMemberNoteNumber(
                $note['courseId'],
                $note['userId'],
                $this->getNoteDao()->getNoteCountByUserIdAndCourseId($note['userId'], $note['courseId'])
            );
        } else {
            $note['updatedTime'] = time();
            $note = $this->getNoteDao()->updateNote($existNote['id'], $note);
        }

        
        return $note;
    }

    public function deleteNote($id){
        $note = $this->getNote($id);
        if (empty($note)) {
            E("笔记(#{$id})不存在，删除失败");
        }
        $currentUser = $this->getCurrentUser();//
        if (($note['userId'] != $currentUser['id']) && !$this->getCourseService()->canManageCourse($note['courseId'])) {
            E("你没有权限删除笔记(#{$id})");
        }

        $this->getNoteDao()->deleteNote($id);
        $this->getCourseService()->setMemberNoteNumber(
            $note['courseId'],
            $note['userId'], 
            $this->getNoteDao()->getNoteCountByUserIdAndCourseId($note['userId'], $note['courseId'])
        );
        if ($note['userId'] != $currentUser['id']) {
            $this->getLogService()->info('note', 'delete', "删除笔记#{$id}");
        }
    }

    public function deleteNotes(array $ids){
        foreach ($ids as $id) {
            $this->deleteNote($id);
        }
    }
    
    private function calculateContnentLength($content){
        $content = strip_tags(trim(str_replace(array("\\t", "\\r\\n", "\\r", "\\n"), '',$content)));
        return mb_strlen($content, 'utf-8');
    }
    
    private function prepareSearchNoteConditions($conditions){
        $conditions = array_filter($conditions);
        if (isset($conditions['keywordType']) && isset($conditions['keyword'])) {
            if (!in_array($conditions['keywordType'], array('content', 'courseId', 'courseTitle' ))) {
                E('keywordType参数不正确');
            }
            $conditions[$conditions['keywordType']] = $conditions['keyword'];
        }
        unset($conditions['keywordType']);
        unset($conditions['keyword']);
        if (isset($conditions['author'])) {
            $author = $this->getUserService()->getUserByNickname($conditions['author']);
            $conditions['userId'] = $author ? $author['id'] : -1;
            unset($conditions['author']);
        }
        return $conditions;
    }
    
}
?>
