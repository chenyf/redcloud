<?php

namespace Course\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Course;


class CourseResourcePacketController extends \Home\Controller\BaseController
{

    public function _initialize(){
        $user = $this->getCurrentUser();
        if(!$user->isLogin()) {
            $this->redirect('User/Signin/index');
        }
    }

    //查看课程资源包情况
    public function indexAction(Request $request){
        $user = $this->getCurrentUser();
        if(!$user->isTeacher() && !isGranted("ROLE_ADMIN")){
            $this->redirect('User/Signin/index');
        }

        $courseId = I("id") ? : 0;
        $course = $this->getCourseService()->getCourseRaw($courseId);
        
        if(empty($course)){
            return $this->render("CourseResourcePacket:resource_packet",["resourcePacket" => null]);
        }

        $resourcePacket = $this->getResourcePacketService()->getByCourseId($courseId);

        if(empty($resourcePacket)){
            return $this->render("CourseResourcePacket:resource_packet",["resourcePacket" => null,"course" => $course]);
        }

        $downloads = $this->getResourcePacketDownloadService()->selectByPacketId($resourcePacket["id"]);
        $resourcePacket["name"] = basename($resourcePacket["filepath"]);
        $resourcePacket["downloadNum"] = count($downloads);

        return $this->render("CourseResourcePacket:resource_packet",["resourcePacket" => $resourcePacket,"course" => $course]);
    }

    //下载实验课程资源包
    public function downloadCourseResourcePacketAction(Request $request){

        $user = $this->getCurrentUser();
        if(!$user->isLogin()) {
            $this->redirect('User/Signin/index');
        }

        $courseId = I("courseId") ? : 0;
        $course = $this->getCourseService()->getCourseRaw($courseId);

        if(empty($course)){
            $this->tipMessageCloseWindow("该课程不存在");
            exit();
        }

        $resourcePacket = $this->getResourcePacketService()->getByCourseId($courseId);

        if(empty($resourcePacket) || empty($resourcePacket["filepath"]) || $resourcePacket["deleted"] > 0){
            $this->tipMessageCloseWindow("课程资源包不存在！");
            exit();
        }

        $filePath = pathjoin(DATA_PATH,$resourcePacket["filepath"]);

        if(!file_exists($filePath)){
            $this->tipMessageCloseWindow("该课程资源包不存在！");
            exit();
        }

        $tempDir = DATA_PATH . "/tmp/" . uniqid();
        $tempCryptFile  = $tempDir . "/resource_course_" . $courseId . "_" . date('Ymd_His') . ".red";

        if (true !== @mkdir($tempDir, 0777, true) || !is_dir($tempDir)){
            $this->tipMessageCloseWindow("创建临时目录失败");
            exit();
        }

        $cryptKey = "123456";
        $crypt_cmd = C("CRYPT_COURSE_CMD");
        if(!is_file($crypt_cmd)){
            Course::delDir($tempDir);
            $this->tipMessageCloseWindow("下载出现错误");
            exit();
        }
        $shell  = $crypt_cmd . " -e --key " . $cryptKey . " " . $filePath . " " . $tempCryptFile;

        //解密课程资源包
        @exec($shell,$err_info,$err_code);

        if(!is_file($tempCryptFile)){
            Course::delDir($tempDir);
            doLog($shell);
            $this->tipMessageCloseWindow("下载失败");
            exit();
        }

        //打开文件
        $file = fopen ( $tempCryptFile, "r" );
        //输入文件标签
        header ( "Content-type: application/octet-stream" );
        header ( "Accept-Ranges: bytes" );
        header ( "Accept-Length: " . filesize ( $tempCryptFile ) );
        $file_name = basename($tempCryptFile);
        header ( "Content-Disposition: attachment; filename=" . $file_name );
        //输出文件内容
        //读取文件内容并直接输出到浏览器
        echo fread ( $file, filesize ( $tempCryptFile ) );
        fclose ( $file );

        //添加下载记录
        $this->getResourcePacketDownloadService()->addRecord(
            array(
                "packetId" => $resourcePacket["id"],
                "userId" => $user->id,
                "createdTime" => time(),
            )
        );

        Course::delDir($tempDir);
        exit ();
    }

    //生成课程资源包
    public function makeCourseResourcePacketAction(Request $request){
        $user = $this->getCurrentUser();

        if(!$user->isAdmin()){
            return $this->createJsonResponse(array("success" => false,"msg" => "您没有该操作权限！"));
        }

        $courseId = I("courseId") ? : 0;
        $course = $this->getCourseService()->getCourseRaw($courseId);

        if(empty($course)){
            return $this->render("CourseResourcePacket:make_resource_packet_modal",["resourcePacket" => null,"msg" => "课程不存在！"]);
        }

        $resourcePacket = $this->getResourcePacketService()->getByCourseId($courseId);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $result = Course::generateResourceCourse($user->id,$data["courseId"]);

            return $this->createJsonResponse($result);
        }

        return $this->render("CourseResourcePacket:make_resource_packet_modal",["resourcePacket" => $resourcePacket,"course" => $course]);
    }

    //删除课程资源包
    public function deleteCourseResourcePacketAction(Request $request){
        $user = $this->getCurrentUser();
        
        if(!$user->isAdmin()){
            return $this->createJsonResponse(array("success" => false,"msg" => "您没有该操作权限！"));
        }
        
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $result = Course::deleteResourceCourse($data["courseId"]);

            return $this->createJsonResponse($result);
        }
        return $this->createJsonResponse(array("success" => false,"msg" => "发生错误！"));
    }

    private function getResourcePacketService(){
        return createService('Course.CourseResourcePacketService');
    }

    private function getResourcePacketDownloadService(){
        return createService('Course.CourseResourcePacketDownloadService');
    }

    protected function getCourseService(){
        return createService('Course.CourseService');
    }

}