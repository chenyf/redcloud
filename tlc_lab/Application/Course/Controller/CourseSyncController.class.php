<?php

namespace Course\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Lib\FileToolkit;
use Common\Lib\Course;

class CourseSyncController extends \Home\Controller\BaseController
{

    public function _initialize(){
        $user = $this->getCurrentUser();
        if(!$user->isLogin()) {
            $this->redirect('User/Signin/index');
        }
    }

    private function getUploadRecordService(){
        return createService('Course.CourseUploadRecordServiceModel');
    }

    private function getCourseService() {
        return createService('Course.CourseServiceModel');
    }

    public function hello(){
        return $this->createMessageResponse('error', '上传图片格式错误，请上传jpg, gif, png格式的文件。');
    }

    //上传课程
    public function uploadCourseAction(Request $request){
        $user = $this->getCurrentUser();

        if ($request->getMethod() == 'POST'){
            $data = $request->request->all();
            $course_resource_path = $data["course_resource_path"];

            $result = Course::addResourceCourse($course_resource_path);

            return $this->createJsonResponse($result);
        }
        
        return $this->render("CourseSync:upload_course");
    }

    //删除某上传的课程
    public function deleteCourseAction(Request $request){

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $courseId = $data["courseId"];
            $record = $this->getUploadRecordService()->getByCourseId($courseId);

            if(empty($record)){
                return $this->createJsonResponse(array("success" => false,"msg" => "记录不存在！"));
            }

            $result = Course::deleteCourse($courseId);

            return $this->createJsonResponse($result);
        }
        return $this->createJsonResponse(array("success" => false,"msg" => "发生错误！"));
    }
    

    //查看上传课程记录
    public function uploadCourseRecordAction(){
        $records = $this->getUploadRecordService()->findAll();
        return $this->render("CourseSync:upload_course_record",array("records" => $records));
    }

    //上传课程，并进行课程导入
    public function uploadCourseResourceAction(Request $request){
        if ($request->getMethod() == 'POST') {

            $options = $_FILES['course_resource_file'];

            $validFileExt = array("red");

            extract($options);
            $info = pathinfo($name);
            $fileType = $info['extension'];
            if (empty($_FILES['course_resource_file']['name']) || $_FILES['course_resource_file']['error'] != 0) {
                $result = array("status" => false, 'msg' => "请选择文件");
                return $this->createJsonResponse($result);
            }
            if (!in_array($fileType, $validFileExt)) {
                $result = array("status" => false, 'msg' => "文件格式不符合规范");
                return $this->createJsonResponse($result);
            }
            if ($size > 1024 * 1024 * 1024 * 5) {
                $result = array("status" => false, 'msg' => "文件的大小不能超过5G");
                return $this->createJsonResponse($result);
            }

            $savePath = DATA_PATH . "/tmp/" . date('Ymd') . "/" . uniqid() . "/";

            if (!is_dir($savePath)) {
                if (false === @mkdir($savePath, 0777, true)) {
                    $result = array("status" => false, 'msg' => "上传文件夹不存在！");
                    return $this->createJsonResponse($result);
                }
            }

            $saveFileName = $savePath . basename($name);

            /* 是否上传成功 */
            if (@copy($tmp_name, $saveFileName)) {
                doLog("file path:" . $saveFileName);
                if(!is_file($saveFileName)){
                    return $this->createJsonResponse(array("status" => false, 'msg' => "文件上传失败"));
                }
                $result = Course::addResourceCourse($saveFileName);
                return $this->createJsonResponse($result);
            } else {
                $result = array("status" => false, 'msg' => "文件上传失败");
                return $this->createJsonResponse($result);
            }
        }
    }

    //获取文件列表窗口
    public function getFileListAction(Request $request){

        $path = "~";

        $fileList = FileToolkit::getFileList($path);

        $backward_path = dirname(dirname($path));

        if ($request->getMethod() == 'POST'){
            $data = $request->request->all();
            $path = $data["path"];

            $backward_path = dirname(dirname($path));

            if(!is_dir($path)){
                return $this->createJsonResponse(array(
                    "success" => "false",
                    "backward_path" => $backward_path,
                    "current_path" => $path,
                    "msg" => "目录不存在",
                    "toplevel" => false,
                    "fileList" => array()
                ));
            }

            if($data["backward"] === true){
                $path = dirname(dirname($path));
                if(!is_dir($path)){
                    return $this->createJsonResponse(array(
                        "success" => "false",
                        "backward_path" => dirname($backward_path),
                        "current_path" => $path,
                        "msg" => "不存在上一级目录",
                        "toplevel" => false,
                        "fileList" => array()
                    ));
                }
            }

            $fileList = FileToolkit::getFileList($path);

            return $this->createJsonResponse(array(
                "success" => "true",
                "backward_path" => $backward_path,
                "current_path" => $path,
                "msg" => "",
                "toplevel" => true,
                "fileList" => $fileList
            ));
        }

        return $this->render("CourseSync:file_list_modal",array("current_path" => $path,"backward_path" => $backward_path,"fileList" => $fileList));
    }

}