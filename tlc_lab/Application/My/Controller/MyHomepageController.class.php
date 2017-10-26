<?php

namespace My\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\FileUpload;

class MyHomepageController extends \Home\Controller\BaseController
{
    private $vaidTplIndex = array(0,1,2);

    public function _initialize(){
        $user = $this->getCurrentUser();
        if(!$user->isLogin())
            return $this->redirect('User/Signin/index');
        if(!$user->isTeacher()){
            return $this->redirect('User/User/show',array('id'=>$user->id));
        }
    }

    //我的个人主页
    
    public function indexAction(Request $request,$id){
        if(!empty($id)){
            $this->redirect("/teacher/homepage/{$id}.html");
            return ;
        }
        $user = $this->getCurrentUser();
        return $this->redirect("/teacher/homepage/{$user->userNum}/index.html");
    }

    //教师主页编辑
    public function editAction(Request $request){
        $user = $this->getCurrentUser();
        return $this->render('MyHomepage:make', array(
            'user' => $user,
        ));
    }

    //教师个人信息编辑
    public function teacherinfoEditAction(Request $request){
        $user = $this->getCurrentUser();
        $profile = $this->getUserService()->getUserProfile($user['id']);

        if ($request->getMethod() == 'POST') {

        }

        return $this->render('MyHomepage:teacher_info_edit',array('profile'=>$profile));
    }

    //返回初始化用户数据
    public function initDataAction(Request $request){
        $user = $this->getCurrentUser();
        $teacherInfo = $this->getTeacherInfoService()->getTeacherInfoByTeacherId($user->id);
        return $this->createJsonResponse($teacherInfo);
    }

    //保存数据
    public function saveAction(Request $request){
        $user = $this->getCurrentUser();

        $data['contacts'] = json_encode($this->filterPost($_POST['contacts']));
        $data['intros'] = json_encode($this->filterPost($_POST['intros']));
        $data['teaches'] = json_encode($this->filterPost($_POST['teaches']));
        $data['researches'] = json_encode($this->filterPost($_POST['researches']));
        $data['publications'] = json_encode($this->filterPost($_POST['publications'],'updateable'));
        $data['tpl'] = isset($_POST['tpl']) ? intval($_POST['tpl']) : 0;

        if($this->getTeacherInfoService()->getTeacherInfoByTeacherId($user->id) != null){
            $result = $this->getTeacherInfoService()->updateTeacherInfo($user->id,$data);
            if(empty($result)){
                return $this->createJsonResponse(['code' => -1]);
            }
        }else{
            $data['teacher_id'] = $user->id;
            $result = $this->getTeacherInfoService()->addTeacherInfo($data);
            if(empty($result)){
                return $this->createJsonResponse(['code' => -1]);
            }
        }

        $viewPath = $this->buildTeacherHtml($user);

        return $this->createJsonResponse(['code' => 0]);
    }

    //预览教师主页
    public function previewAction(Request $request){
        $user = $this->getCurrentUser();

        $filename = $user['userNum'] . "/index.html";
        if($request->getMethod() == "POST") {
            $data['contacts'] = $this->filterPost($_POST['contacts']);
            $data['intros'] = $this->filterPost($_POST['intros']);
            $data['teaches'] = $this->filterPost($_POST['teaches']);
            $data['researches'] = $this->filterPost($_POST['researches']);
            $data['publications'] = $this->filterPost($_POST['publications']);

            $tpl_index = intval($_POST['tpl']);
            if(!in_array($tpl_index,$this->vaidTplIndex)){
                return $this->createJsonResponse(array('error'=>true,'msg' => "模板不存在！"));
            }

            $viewpath = $this->buildTeacherHtml($user,true,$data,$tpl_index);
            return $this->createJsonResponse(array('error'=>false,'url' => "/teacher/homepage/preview/{$user->userNum}/index.html"));
        }

        $static_dir = getParameter("user.teacher.homepage_dir");
        return $this->redirect("/teacher/homepage/preview/{$user->userNum}/index.html");
//        return $this->showHtml(rtrim($static_dir, "/") . "/preview/" . $filename);
    }

    //上传出版物
    public function uploadPaperAction(Request $request){
        if($request->getMethod() == "POST"){
            $user = $this->getCurrentUser();
            $save_dir = $this->getPaperFilePath(). DIRECTORY_SEPARATOR . $user['userNum'] . DIRECTORY_SEPARATOR;

            $uploader = new FileUpload('uploadfile',1024 * 1024 * 20);

            $uploader->newFileName = $this->create_uuid() . "." . $uploader->getExtension();

            // Handle the upload
            $result = $uploader->handleUpload($save_dir);

            if (!$result) {
                return $this->createJsonResponse(array('success' => false, 'msg' => $uploader->getErrorMsg()));
            }

            $paperHref = $this->generateUrl('teacher_homepage_download') . "?path=" . $user['userNum'] . DIRECTORY_SEPARATOR . $uploader->newFileName;

            return $this->createJsonResponse(array('success' => true, 'href'=> $paperHref));
        }

        return $this->createJsonResponse(array('success'    =>  false,'msg' => '方法不正确'));
    }

    //出版物下载
    public function downloadAction(Request $request){
        $path = $request->query->get("path");
        if(empty($path)){
            return $this->createMessageResponse("info","参数有误，下载失败！",5);
        }

        $accessPath = $this->getPaperFilePath() . DIRECTORY_SEPARATOR . $path;

        $accessPath = str_replace(["\\",'/'],DIRECTORY_SEPARATOR,$accessPath);

        doLog($accessPath);

        if(!is_file($accessPath)){
            return $this->createMessageResponse("info","文件不存在！",5);
        }

        //打开文件
        $file = fopen ( $accessPath, "r" );
        //输入文件标签
        Header ( "Content-type: application/octet-stream" );
        Header ( "Accept-Ranges: bytes" );
        Header ( "Accept-Length: " . filesize ( $accessPath ) );
        $file_name = basename($accessPath);
        Header ( "Content-Disposition: attachment; filename=" . $file_name );
        //输出文件内容
        //读取文件内容并直接输出到浏览器
        echo fread ( $file, filesize ( $accessPath ) );
        fclose ( $file );
        exit ();
    }

    private function filterPost($json_array,$column=''){
        $array = json_decode($json_array);
        $newArr = [];
        foreach ($array as $a) {
            if (isset($a->key)) {

                if(is_array($a->value)){
                    $value = [];
                    foreach ($a->value as $a_value){
                        $a_value = trim($a_value);
                        if(!empty($a_value)) {
                            $value[] = $a_value;
                        }
                    }

                    if(empty($column)) {
                        array_push($newArr, array('key' => $a->key, 'value' => $value));
                    }else{
                        array_push($newArr, array('key' => $a->key, 'value' => $value,$column => $a->{$column}));
                    }

                }else{
                    if(empty($column)) {
                        array_push($newArr, array('key' => $a->key, 'value' => $a->value));
                    }else{
                        array_push($newArr, array('key' => $a->key, 'value' => $a->value,$column => $a->{$column}));
                    }
                }

            }
        }

        return $newArr;
    }

    protected function getTeacherInfoService()
    {
        return createService('User.TeacherInfoService');
    }
    
    public function getCourseService(){
        return createService("Course.CourseService");
    }

    protected function getUserService()
    {
        return createService('User.UserServiceModel');
    }

    public function getPaperFilePath(){
        $save_dir = getParameter("teacher.homepage.paper_file");
        return $save_dir;
    }

    private function create_uuid($prefix = ""){    //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $prefix . $uuid;
    }

}