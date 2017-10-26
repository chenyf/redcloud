<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Twig\Util\UploadToken;
use Common\Lib\FileToolkit;


class EditorController extends \Home\Controller\BaseController
{
    public function uploadAction(Request $request)
    {
        try {

            $token = $request->query->get('token');

            $maker = new UploadToken();
            $token = $maker->parse($token);
            if (empty($token)) {
                throw new \RuntimeException("上传授权码已过期，请刷新页面后重试！");
            }

            $file = $request->files->get('upload');

            if ($token['type'] == 'image') {
                if (!FileToolkit::isImageFile($file)) {
                    throw new \RuntimeException("您上传的不是图片文件，请重新上传。");
                }
            } elseif ($token['type'] == 'flash') {
                $errors = FileToolkit::validateFileExtension($file, 'swf');
                if (!empty($errors)) {
                    throw new \RuntimeException("您上传的不是Flash文件，请重新上传。");
                }
            } else {
                throw new \RuntimeException("上传类型不正确！");
            }

            $record = $this->getFileService()->uploadFile($token['group'], $file);

            $funcNum = $request->query->get('CKEditorFuncNum');
            $url = getFilePath($record['uri']);
            #$url = $this->get('redcloud.twig.web_extension')->getFilePath($record['uri']);
            if ($token['type'] == 'image') {
                $response = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$funcNum}, '{$url}', function(){ this._.dialog.getParentEditor().insertHtml('<img src=\"{$url}\">'); this._.dialog.hide(); return false; });</script>";
            } else {
                $response = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$funcNum}, '{$url}');</script>";
            }
            die($response);//by qzw
            return new Response($response);


        } catch (\Exception $e) {
            $message = $e->getMessage();
            $funcNum = $request->query->get('CKEditorFuncNum');
            $response = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$funcNum}, '', '{$message}');</script>";
            die($response);//by qzw
            return new Response($response);
        }
    }

    private function getFileService()
    {
        return createService('Content.FileServiceModel');
    }

}