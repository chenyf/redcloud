<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Form\ReviewType;
use Topxia\Service\Course\CourseService;
use Common\Lib\ArrayToolkit;
use Topxia\Common\NumberToolkit;
use Common\Lib\Paginator;
use Common\Lib\FileToolkit;
use Common\Model\Util\LiveClientFactory;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;

class FeedbackController extends \Home\Controller\BaseController
{
    public function indexAction(Request $request)
    {   
        $types =  C('PROBLEM_TYPE');
        
        if($request->getMethod() == 'POST'){
            $data = $request->request->all();
            
            if(trim($data['keywordType']) == '') 
                return $this->createJsonResponse (array('status' => 'error','type' => 'type','info'=>'请输入问题类型'));
            
            if(trim($data['content']) == '')
                return $this->createJsonResponse (array('status' => 'error','type' => 'content','info'=>'请输入问题描述'));
                
            if(strlen(trim($data['content'])) > 1500 )
                return $this->createJsonResponse (array('status' => 'error','type' => 'content','info'=>'文本输入不得超过500字'));
            
            $parm['type'] = intval($data['keywordType']);
            $parm['content'] = trim($data['content']);
            $parm['picture'] = !empty($data['problemPic']) ? $data['problemPic']: '';
            $parm['from'] = 0;
            $r = $this->getFeedBackService()->addProblemFeedback($parm);
             return $this->createJsonResponse (array('status' => 'OK'));    
        }
        
        return $this->render("Feedback:feedback", array(
            'types' => $types,
        ));
    }
    
   /**
     * 问题反馈图片上传
     * @author 谈海涛 2015-10-27
     */
    public function problemPictureAction(Request $request)
    {   
        if($request->getMethod() == 'POST'){
            $file = $request->files->get('problem-picture');
            
            if($file -> getClientSize() > 1048576) 
                return $this->createJsonResponse (array('status' => 'error','info'=>'文件过大'));
            $record = $this->getFileService()->uploadDataFile('user', $file);
            
            $record['url'] =getFilePath($record['uri']);
            $src = SITE_PATH.$record['url'];
            $size = getimagesize($src);
            
            $width  = $size[0];
            $height = $size[1];
            
            if($width > $height){
                if($width > 200){
                    $height = (200/$width)*$height;
                    $width = 200;
                }
                    
            }else{
                if($height > 200){
                    $width = (200/$height)*$width;
                    $height = 200;
                }
            }
            
            $frdata = array( 'status' => 'success','pictureUrl' => $record['url'],'width'=>$width,'height'=>$height);
            return $this->createJsonResponse ($frdata);
            
        }

    }
    
    
    
    private function getFileService()
    {
        return createService('Content.FileService');
    }
    
    private function getFeedBackService()
    {
        return createService('User.FeedBackService');
    }
    
}