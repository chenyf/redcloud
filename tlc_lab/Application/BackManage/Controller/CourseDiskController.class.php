<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class CourseDiskController extends BaseController
{
    public function indexAction(Request $request)
    {
        $conditions = $request->query->all();
        $paginator = new Paginator(
            $request,
            $this->getCourseDiskService()->searchFileCount($conditions),
            20
        );
        $files = $this->getCourseDiskService()->searchFiles(
            $conditions,
            'latestCreated',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($files, 'userId'));
        return $this->render('CourseDisk:index',array(
            'files' => $files,
            'paginator' => $paginator,
            'users'=>$users,
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $this->getCourseDiskService()->deleteFile($id);
        return $this->createJsonResponse(true);
    }

    public function batchDeleteAction(Request $request)
    {
        $ids = $request->request->get('ids', array());
        $this->getCourseDiskService()->deleteFiles($ids);

        return $this->createJsonResponse(true);
    }

    protected function getCourseDiskService()
    {
        return createService('User.DiskService');
    }

}