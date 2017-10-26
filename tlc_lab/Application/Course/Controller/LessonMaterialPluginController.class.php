<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;

class LessonMaterialPluginController extends \Home\Controller\BaseController
{

    public function initAction (Request $request)
    {
        // 现在不用了 跳转到Course/CourseResource下
        return $this->redirect(U('Course/CourseResource/init',array('id'=>$request->query->get('courseId'))));
        
        list($course, $member) = $this->getCourseService()->tryTakeCourse($request->query->get('courseId'));
        $lesson = $this->getCourseService()->getCourseLesson($course['id'], $request->query->get('lessonId'));

        if ($lesson['mediaId'] > 0) {
            $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
        } else {
            $file = null;
        }

        $lessonMaterials = $this->getMaterialService()->findLessonMaterials($lesson['id'], 0, 100);
        return $this->render('LessonMaterialPlugin:index', array(
            'materials' => $lessonMaterials,
            'course' => $course,
            'lesson' => $lesson,
            'file' => $file,
        ));
    }

    protected function getUploadFileService()
    {
        return createService('File.UploadFileService');
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getMaterialService()
    {
        return createService('Course.MaterialService');
    }
}