<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;

class CourseMaterialManageController extends \Home\Controller\BaseController
{

    public function indexAction(Request $request, $courseId, $lessonId)
    {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);
        $materials = $this->getMaterialService()->findLessonMaterials($lesson['id'], 0, 100);
        return $this->render('CourseMaterialManage:material-modal', array(
            'course' => $course,
            'lesson' => $lesson,
            'materials' => $materials,
            'storageSetting' => $this->setting('storage'),
            'targetType' => 'coursematerial',
            'targetId' => $course['id'],
        ));
    }

    public function uploadAction(Request $request, $courseId, $lessonId)
    {

        $course = $this->getCourseService()->tryManageCourse($courseId);
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);
        if (empty($lesson)) {
            throw $this->createNotFoundException();
        }

        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();

            if (empty($fields['fileId']) && empty($fields['link'])) {
                throw $this->createNotFoundException();
            }

            $fields['courseId'] = $course['id'];
            $fields['lessonId'] = $lesson['id'];

            $material = $this->getMaterialService()->uploadMaterial($fields);

            return $this->render('CourseMaterialManage:list-item', array(
                'material' => $material,
            )); // no exists
        }

        return $this->render('CourseMaterial:upload-modal', array(
            'form' => $form->createView(),
            'course' => $course,
        ));

    }

    public function deleteAction(Request $request, $courseId, $lessonId, $materialId)
    {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $this->getMaterialService()->deleteMaterial($courseId, $materialId);
        return $this->createJsonResponse(true);
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    private function getMaterialService()
    {
        return createService('Course.MaterialService');
    }

    private function getUploadFileService()
    {
        return createService('File.UploadFileService');
    }
}