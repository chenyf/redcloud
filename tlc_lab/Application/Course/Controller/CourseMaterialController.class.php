<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Common\Lib\ArrayToolkit;
use Common\Lib\FileToolkit;
use Common\Lib\Paginator;
use Common\Model\Util\CloudClientFactory;

class CourseMaterialController extends \Home\Controller\BaseController
{
    public function indexAction(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            return $this->createMessageResponse('info', '你好像忘了登录哦？', null, 3000, $this->generateUrl('login'));
        }
        
        $courseSerObj = $this->getCourseService();
        $MaterSerObj = $this->getMaterialService();
        $course = $courseSerObj->getCourse($id);
        if (empty($course) || $course['isDeleted']) {
            return $this->createMessageResponse('error',"课程不存在，或已删除。");
        }

        if (!$courseSerObj->canTakeCourse($course)) {
            return $this->createMessageResponse('info', "您还不是课程《{$course['title']}》的学员，请先购买或加入学习。", null, 3000, $this->generateUrl('course_show', array('id' => $id,'center'=>(CENTER == 'center') ? 1 : 0)));
        }


        list($course, $member) = $courseSerObj->tryTakeCourse($id);

        $paginator = new Paginator(
            $request,
            $MaterSerObj->getMaterialCount($id),
            20
        );

        $materials = $MaterSerObj->findCourseMaterials(
            $id,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $lessons = $courseSerObj->getCourseLessons($course['id']);
        $lessons = ArrayToolkit::index($lessons, 'id');

        return $this->render("CourseMaterial:index", array(
            'course' => $course,
            'lessons'=>$lessons,
            'materials' => $materials,
            'paginator' => $paginator,
        ));
    }

    public function downloadAction(Request $request, $courseId, $materialId)
    {
        list($course, $member) = $this->getCourseService()->tryTakeCourse($courseId);

        if ($member && !$this->getCourseService()->isMemberNonExpired($course, $member)) {
            return $this->redirect($this->generateUrl('course_materials',array('id' => $courseId)));
        }

        if ($member && $member['levelId'] > 0) {
            if ($this->getVipService()->checkUserInMemberLevel($member['userId'], $course['vipLevelId']) != 'ok') { // no exists
                return $this->redirect($this->generateUrl('course_show',array('id' => $id)));
            }
        }
        
        #如果加入黑名单 跳课程学习页
        if($member && $member["black"]){
            return $this->redirect($this->generateUrl('course_materials',array('id' => $id,'center'=>(CENTER == 'center') ? 1 : 0)));
        }

        $material = $this->getMaterialService()->getMaterial($courseId, $materialId);
        if (empty($material)) {
            throw $this->createNotFoundException();
        }

        $file = $this->getUploadFileService()->getFile($material['fileId']);
        if (empty($file)) {
            throw $this->createNotFoundException();
        }

        if ($file['storage'] == 'cloud') {
            $factory = new CloudClientFactory();
            $client = $factory->createClient();
            $client->download($client->getBucket(), $file['hashId'], 3600, $file['filename']);
        } else {
            return $this->createPrivateFileDownloadResponse($request, $file);
        }
    }

    public function deleteAction(Request $request, $id, $materialId)
    {
        $course = $this->getCourseService()->tryManageCourse($id);

        $this->getCourseService()->deleteCourseMaterial($id, $materialId);
        return $this->createJsonResponse(true);
    }

    public function latestBlockAction($course)
    {
        $materials = $this->getCourseService()->findMaterials($course['id'], 0, 10);
        return $this->render('CourseMaterial:latest-block', array(
            'course' => $course,
            'materials' => $materials,
        ));
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    private function getMaterialService()
    {
        return createService('Course.MaterialService');
    }

    private function getFileService()
    {
        return createService('Content.FileService');
    }

    private function getUploadFileService()
    {
        return createService('File.UploadFileService');
    }

    protected function getVipService()
    {
        return createService('Vip:Vip.VipService');
    }

    private function createPrivateFileDownloadResponse(Request $request, $file)
    {
        
        //by qzw
        createLocalMediaResponse($request, $file, $isDownload);
        
//        $response = BinaryFileResponse::create($file['fullpath'], 200, array(), false);
//        $response->trustXSendfileTypeHeader();
//
//        $file['filename'] = urlencode($file['filename']);
//        if (preg_match("/MSIE/i", $request->headers->get('User-Agent'))) {
//            $response->headers->set('Content-Disposition', 'attachment; filename="'.$file['filename'].'"');
//        } else {
//            $response->headers->set('Content-Disposition', "attachment; filename*=UTF-8''".$file['filename']);
//        }
//
//        $mimeType = FileToolkit::getMimeTypeByExtension($file['ext']);
//        if ($mimeType) {
//            $response->headers->set('Content-Type', $mimeType);
//        }
//
//        return $response;
    }
}