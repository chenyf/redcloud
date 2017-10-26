<?php
namespace Course\Controller;
use Common\Lib\FileToolkit;
use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Services\QcloudCosService;
use Common\Lib\Course;

class CourseResourceController extends \Home\Controller\BaseController {

    /*
     * author fubaosheng 2016-03-24
     * 购买页和加入页的资料
     * 没有登录或登录不是课程学员的 显示免费的和非本课程学员下载
     * 已经兑换过显示免费
     * 如果已经是本课程学员，必须登录才可显示免费
     */
    public function resourcePaneAction(Request $request){
        $user = $this->getCurrentUser();
        $courseId = I('id') ? : 0;

        $member = false;
        if( $user->isLogin() ){
            $member = $this->getCourseService()->getCourseMember($courseId,$user["id"]);
        }
        
        $paramArr = array();
        $paramArr['courseId'] = $courseId;
        if( $user->isLogin() && !$member ){
            $paramArr['power'] = array(1,2);
        }else{
            $paramArr['power'] = array(1,2);
        }
        $html = "resourcePane";
        $showDownloadNum = 0;
        
        $count = $this->getCourseResourceService()->courseResourceCount($paramArr);
        $paginator = new Paginator(
            $this->getRequest(),
            $count,
            10
        );
        $paramArr['start'] = $paginator->getOffsetCount();
        $paramArr['limit'] = $paginator->getPerPageCount();
        $courseResources = $this->getCourseResourceService()->courseResourceList($paramArr);
        $course = $this->getCourseService()->getCourse($courseId);

        return  $this->render("courseResource:resourcePane", array(
            'courseResources'   => array_values($courseResources),
            'course' => $course,
            'showDownloadNum' => $showDownloadNum,
            'paginator' => $paginator
        ));
    }
   
    //@author fbs 管理资料页面（课程管理页）
    public function indexAction(Request $request){
        $courseId = I('id') ? : 0;
        $user = $this->getCurrentUser();
        if( !$user->isLogin() )
            return $this->redirect(U('User/Signin/index'));
        $member = $this->getCourseService()->isCourseTeacher($courseId,$user["id"]);
        if( !$member && !isGranted("ROLE_ADMIN") )
            return $this->redirect(U('Course/Course/show',array('id'=>$courseId)));
        $paramArr = array();
        $paramArr['courseId'] = $courseId;
        $count = $this->getCourseResourceService()->courseResourceCount($paramArr);
        $paginator = new Paginator(
            $this->getRequest(),
            $count,
            10
        );
        $paramArr['start'] = $paginator->getOffsetCount();
        $paramArr['limit'] = $paginator->getPerPageCount();
        $courseResources = $this->getCourseResourceService()->courseResourceList($paramArr);
        $course = $this->getCourseService()->getCourse($courseId);
        return  $this->render("courseResource:index", array(
            'courseResources'   => $courseResources,
            'course' => $course,
            'paginator' => $paginator
        ));
    }

    //@author fbs 课程播放页的资料
    public function initAction(Request $request){
        $courseId = I('id') ? : 0;
        $user = $this->getCurrentUser();
        $paramArr = array();
        $paramArr['courseId'] = $courseId;
        $paramArr['power'] = array(0,1,2);
        $paramArr['start'] = 0;
        $paramArr['limit'] = 1000;
        $courseResources = $this->getCourseResourceService()->courseResourceList($paramArr);
        return  $this->render("courseResource:init", array(
            'courseResources'   => $courseResources
        ));
    }

    
    //@auhtor fbs 下载资料
    public function downloadAction(Request $request){
//        $user = $this->getCurrentUser();
//        if( !$user->isLogin() )
//            $this->jsonResponse(403, 'Unlogin');
        $courseId = I("courseId") ? : 0;
        $id = I("id") ? : 0 ;
        $paramArr = array("courseId"=>$courseId,"id"=>$id);
        $courseResource = $this->getCourseResourceService()->courseResource($paramArr);
        if( empty($courseResource) )
            $this->error("该资料不存在");

        if(file_exists($courseResource['accessPath'])){
            $this->getCourseResourceService()->updateResourceDownloadNum($paramArr);
            $uri = U('Home/Download/download',null,true,true)."?type=resource&id=".$courseResource['id'];
            $uri = str_replace("%2F","/",$uri);
            $this->success($uri);
        }else{
//            $this->getCourseResourceService()->removeResource($id);
            $this->error("资料不存在或已被删除");
        }

        //去腾讯云判断文件是否存在 存在修改文件的下载次数，拼连接进行下载
//        if(!empty($courseResource["url"])){
//            $qcloudCosService = new QcloudCosService();
//            $result = $qcloudCosService->statFile($courseResource["url"]);
//            if( $result["code"] === 0 ){
//                $this->getCourseResourceService()->updateResourceDownloadNum($paramArr);
//                $url = $result['data']['access_url'];
//                $this->success($url);
//            }else{
//                $this->error($result["message"]);
//            }
//        }else{
//            $this->error("该资料的链接地址为空");
//        }
    }

    #//@author fbs 添加和编辑资料
    //添加资料，资料不需要编辑，直接删除就行了
    public function addResourceAction(Request $request){
        $user = $this->getCurrentUser();
        if( !$user->isLogin() || $user["id"] <= 0)
            $this->jsonResponse(403, 'Unlogin');
        $courseId = I("courseId") ? : 0;
        $member = $this->getCourseService()->isCourseTeacher($courseId,$user["id"]);
        if( !$member && !isGranted("ROLE_ADMIN") )
            return $this->createMessageResponse('error','没有权限');
        $courseResource = array();
        $fileName = "";

        if($request->getMethod() == "POST"){
            $data = $request->request->all();
            $courseId = isset($data["courseId"]) ? intval($data["courseId"]) : 0;
            $title = isset($data["title"]) ? trim($data["title"]) : "";
            $url = isset($data["media"]) ? rawurldecode($data["media"]) : "";
            $sync_cloud = isset($data["async_cloud"]) ? $data["async_cloud"] : 0;

            $course = $this->getCourseService()->getCourse($courseId);
            if( empty($course) || $course['isDeleted'] )
                $this->createJsonResponse(array('status'=>0,'info'=>"课程不存在或已被删除"));
            $member = $this->getCourseService()->isCourseTeacher($courseId,$user["id"]);
            if( !$member && !isGranted("ROLE_ADMIN") )
                $this->createJsonResponse(array('status'=>0,'info'=>"没有权限"));
            if( empty($title) )
                $this->createJsonResponse(array('status'=>0,'info'=>"请输入资料名称"));
            $lenth = mb_strlen($title, 'utf-8');
            if ( $lenth < 1 || $lenth > 40 )
                $this->createJsonResponse(array('status'=>0,'info'=>"资料名称的长度范围为1-40个字符"));
            if( !preg_match("/^.+$/iu", $title) )
                $this->createJsonResponse(array('status'=>0,'info'=>"资料名称由中英文、数字和字符组成"));

            if( empty($url) )
                $this->createJsonResponse(array('status'=>0,'info'=>"请先上传资料"));


            $resourceFilePath = $this->getCourseResourceService()->getResourceAccessPath($url);
            $result = FileToolkit::statFile($resourceFilePath);

//            $qcloudCosService = new QcloudCosService();
//            $result = $qcloudCosService->statFile($url);
            if( $result["code"] !== 0 )
                $this->createJsonResponse(array('status'=>0,'info'=>$result["message"]));
            $op = strrpos($url, ".");
            $ext = substr($url, $op+1);
            $size = $result["data"]["filesize"];
            $gsize = $size/1024/1024/1024;
            //如果超过1G，删除文件
            if( $gsize > 1 ){
//                $qcloudCosService->delFile($url);
                FileToolkit::deleteFile($user,$resourceFilePath);
                $this->createJsonResponse(array('status'=>0,'info'=>"文件大小不超过1GB"));
            }

            $arr = array();
            $arr["updateUid"] = $user["id"];
            $arr["updateTm"] = time();
            $arr["title"] = $title;
            $arr["url"] = $url;
            $arr["ext"] = $ext;
            $arr["size"] = $size;

            $arr["courseId"] = $courseId;
            $arr["createUid"] = $user["id"];
            $arr["createTm"] = time();

            $r = $this->getCourseResourceService()->addResource($arr);
            if($r) {
                if($sync_cloud){
                    $this->getCourseResourceService()->syncResource($r);
                }
                $this->createJsonResponse(array('status' => 1, 'info' => "上传课程资料成功"));
            }else {
                $this->createJsonResponse(array('status' => 0, 'info' => "上传课程资料失败"));
            }
        }

        return $this->render("courseResource:addResource-modal", array(
            'courseId'   => $courseId, //对应的课程的ID
            'targetType' => 'resource',
            'upload_mode' => 'local',
            'fileName' => $fileName,
            'sess'      => session_id(),
            'courseResource' => $courseResource,
        ));
    }

    //@author rsk 同步资料到云盘
    public function asyncResourceCloudAction(Request $request){
        $courseId = I("courseId") ? : 0;
        $id = I("id") ? : 0 ;
        $paramArr = array("courseId"=>$courseId,"id"=>$id);
        $courseResource = $this->getCourseResourceService()->courseResource($paramArr);
        if($request->getMethod() == "POST"){
            $courseId = I("courseId") ? : 0;
            $id = I("id") ? : 0 ;
            $user = $this->getCurrentUser();
            if( !$user->isLogin() )
                $this->jsonResponse(403, 'Unlogin');
            $member = $this->getCourseService()->isCourseTeacher($courseId,$user["id"]);
            if( !$member && !isGranted("ROLE_ADMIN") )
                $this->createJsonResponse(array('error'=>1,'message'=>"没有权限操作"));
            $paramArr = array("courseId"=>$courseId,"id"=>$id,"createUid"=>$user["id"]);
            $courseResource = $this->getCourseResourceService()->courseResource($paramArr);
            if( empty($courseResource) )
                $this->createJsonResponse(array('error'=>1,'message'=>"该资料不存在"));

            //调用同步资料接口
            $r = $this->getCourseResourceService()->syncResource($id);
            if($r){
                $this->createJsonResponse(array('error'=>0,'message'=>"开始同步资料!"));
            }else{
                $this->createJsonResponse(array('error'=>1,'message'=>"同步资料失败"));
            }
        }
        return $this->render("courseResource:asyncResource-modal", array(
            'courseResource'   => $courseResource
        ));
    }
    
    //@author fbs 删除资料
    public function deleteResourceAction(Request $request){
        $courseId = I("courseId") ? : 0;
        $id = I("id") ? : 0 ;
        $paramArr = array("courseId"=>$courseId,"id"=>$id);
        $courseResource = $this->getCourseResourceService()->courseResource($paramArr);
        if($request->getMethod() == "POST"){
            $courseId = I("courseId") ? : 0;
            $id = I("id") ? : 0 ;
            $user = $this->getCurrentUser();
            if( !$user->isLogin() )
                $this->jsonResponse(403, 'Unlogin');
            $member = $this->getCourseService()->isCourseTeacher($courseId,$user["id"]);
            if( !$member && !isGranted("ROLE_ADMIN") )
                $this->error("没有权限删除");
            $paramArr = array("courseId"=>$courseId,"id"=>$id);
            $courseResource = $this->getCourseResourceService()->courseResource($paramArr);
            if( empty($courseResource) )
                $this->error("该资料不存在");

            //删除数据库的记录，成功后请求腾讯的删除接口
            $r = $this->getCourseResourceService()->deleteResource($paramArr);
            if($r){
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }
        return $this->render("courseResource:deleteResource-modal", array(
            'courseResource'   => $courseResource
        ));
    }
    
    //@author fbs 检查标题是否合法
    public function checkTitleAction(Request $request){
        $title = $request->query->get('value');
        $title = trim($title);
        if(!empty($title)){
            $lenth = mb_strlen($title, 'utf-8');
            if ($lenth < 1 || $lenth > 40){
                $response = array('success' => false, 'message' => '资料名称的长度范围为1-40个字符');
            }else{
                if(preg_match("/^.+$/iu", $title))
                    $response = array('success' => true, 'message' => '');
                else
                    $response = array('success' => false, 'message' => '资料名称由中英文、数字和字符组成'); 
            }
        }else{
            $response = array('success' => false, 'message' => '请输入资料名称');
        }
        return $this->createJsonResponse($response);
    }

    private function getCourseResourceService(){
        return createService('Course.CourseResourceService');
    }
    
    protected function getCourseService(){
        return createService('Course.CourseService');
    }

}
