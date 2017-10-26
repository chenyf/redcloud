<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends \Home\Controller\BaseController
{

    public function uploadAction (Request $request)
    {
        $group = $request->query->get('group');
        $file = $this->get('request')->files->get('file');

        $record = $this->getFileService()->uploadFile($group, $file);
        //var_dump( $this->get('redcloud.twig.web_extension'));exit();
        $record['url'] =getFilePath($record['uri']);

        return $this->createJsonResponse($record);
    }

    protected function getFileService()
    {
        return createService('Content.FileServiceModel');
    }

}