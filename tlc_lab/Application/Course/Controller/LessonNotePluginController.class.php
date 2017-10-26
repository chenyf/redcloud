<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;

class LessonNotePluginController extends \Home\Controller\BaseController
{

    public function initAction (Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $course = $this->getCourseService()->getCourse($request->query->get('courseId'));
        if(!empty($course)){
            $this->getCourseService()->addCourseStudent($course['id'],$currentUser['id']);
        }
        $lesson = array('id' => $request->query->get('lessonId'),'courseId' => $course['id']);
        $note = $this->getCourseNoteService()->getUserLessonNote($currentUser['id'], $lesson['id']);
//        dump($note);die;
        $formInfo = array(
            'courseId' => $course['id'],
            'lessonId' => $lesson['id'],
            'content'=>$note['content'],
            'id'=>$note['id'],
        );
        $form = $this->createNoteForm($formInfo);
        return $this->render('LessonNotePlugin:index', array(
            'isCloseUserWrite' => isCloseUserWrite(),
            'isLogin' => $currentUser->isLogin(),
            'form' => $form->createView(),
            'courseId' => $course['id'],
            'lessonId' => $lesson['id'],
            'noteContent' => $note['content'],
        ));
    }

    public function saveAction(Request $request)
    {
        $form = $this->createNoteForm();
        if ($request->getMethod() == 'POST') {
            if (isCloseUserWrite()) {
                return $this->createJsonResponse(array('success'=>false,'message'=>'抱歉，笔记功能已关闭'));
            }
            $form->bind($request);
            if(empty($_POST['courseId'])){
                return $this->createJsonResponse(array('success'=>false,'message'=>'课程ID为空'));
            }
            if(empty($_POST['lessonId'])){
                return $this->createJsonResponse(array('success'=>false,'message'=>'章节ID为空'));
            }
            if(1){
                $note = $form->getData();
                $note["courseId"] = $_POST['courseId']? : '';
                $note["lessonId"] = $_POST['lessonId']? : '';
                $this->getCourseNoteService()->saveNote($note);
                return $this->createJsonResponse(array('success'=>true,'message'=>'保存笔记成功'));
            } else {
                return $this->createJsonResponse(array('success'=>false,'message'=>'保存笔记失败'));
            }
        }
        return $this->createJsonResponse(array('success'=>false,'message'=>'保存笔记失败'));
    }
    private function createNoteForm($data = array())
    {
        return $this->createNamedFormBuilder('note', $data)
            ->add('id', 'hidden', array('required' => false))
            ->add('content', 'textarea',array('required' => false))
            ->add('courseId', 'hidden', array('required' => false))
            ->add('lessonId', 'hidden', array('required' => false))
            ->getForm();
    }
    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getCourseNoteService()
    {
        return createService('Course.NoteService');
    }
}