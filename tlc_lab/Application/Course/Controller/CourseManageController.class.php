<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Form\ReviewType;
use Topxia\Service\Course\CourseService;
use Common\Lib\ArrayToolkit;
use Topxia\Common\NumberToolkit;
use Common\Lib\Paginator;
use Common\Lib\FileToolkit;
use Common\Model\Util\LiveClientFactory;
use Common\Lib\WebCode;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;

class CourseManageController extends \Home\Controller\BaseController
{
    public function indexAction(Request $request, $id)
    {
        $id = $request->query->get('id');
        return $this->forward('Course:CourseManage:base',  array('id' => $id,'center'=>(CENTER == 'center') ? 1 : 0));
    }

    public function baseAction(Request $request, $id, $create){
        $id = $request->query->get('id');
        $create = $request->query->get('create');
        $create = $create ? : 0;
        $courseSerObj = $this->getCourseService();
        $courseMemberObj = $this->getCourseMemberService();
        $settingSerObj = $this->getSettingService();

        if($id){
            $course = $courseSerObj->tryManageCourseFind($id);
            $course_teachers = $this->getCourseCooperationService()->findAllByCourseId($id);
            $teacherids = implode(';',\Org\Util\ArrayToolkit::column($course_teachers,'teacherId'));
        }else{
            if ( (!isGranted('ROLE_TEACHER')) && (!isGranted('ROLE_ADMIN')) ) {
                E('您不是老师或管理员，无权操作！');
            }
            $course = array();
            $course_teachers = array();
            $teacherids = "";
        }

        if($request->getMethod() == 'POST'){
            $data = $request->request->all();
            $title_len = $data["title"] ? mb_strlen($data["title"],"utf-8") : 0;

            if( !($id && CENTER == 'center') ){
                if( ($title_len < 1) || ($title_len > C("COURSE_NAME_MAX_LENGTH")) ){
                     return $this->error('课程名称的长度范围为1~60个字符');
                }
            }

            $data['number'] = strtolower(trim($data['number']));

            if(!empty($data['categoryId'])){
                $category = $this->getCategoryService()->getCategory($data['categoryId']);
                if(empty($category)){
                    return $this->error('您选择的课程分类不存在');
                }

            }else{
                return $this->error('请至少选择一个课程分类');
            }

            $number_len = $data['number'] ? mb_strlen($data['number'],"utf-8") : 0;
            if($number_len < 4 || $number_len >10){
                return $this->error('课程编号的长度范围为4~10个字符');
            }

            if($id){

                if(!$create){
                    $data['about'] =  isset($data['about']) ? remove_xss(trim($data['about'])) : '';
                    $data['selectPicture'] =  isset($data['selectPicture']) ? trim($data['selectPicture']) :"";

                    if($data['selectPicture'] != ""){
                        $data['smallPicture'] = "";
                        $data['middlePicture'] = "";
                        $data['largePicture'] = "";
                    }
                }
                $updatedCourse = $courseSerObj->updateCourse($id, $data);

                $this->getCourseService()->addCourseCategory($id,trim($data['categoryId'],','));
            }else{
                $course = $courseSerObj->createCourse($data);
                $id = $course['id'];

                $this->getCourseService()->addCourseCategory($id,trim($data['categoryId'],','));
            }

            $this->setFlashMessage('success', '课程基本信息已保存！');
            $redirect = $create ? "course_manage_detail" : "course_manage_base";
            $url = $this->generateUrl($redirect,array('id' => $id,'create' => $create));
            return $this->success('success',$url);
        }


        $tags = $this->getTagService()->findTagsByIds($course['tags']);

        $default = $settingSerObj->get('default', array());

        $course_cateName =  $this->getCategoryService()->getNameById($course['categoryId']);
        $cate_courseCode = $this->getCategoryService()->getCourseCodeById($course['categoryId']);
        $course_category = array('id'=>$course['id'],'name'=>$course_cateName,'courseCode'=>$cate_courseCode);

        $render = $create ? "CourseManage:setbase" : "CourseManage:base";

        return $this->render($render, array(
            'course_category' => $course_category,
            'course' => $course,
            'tags' => ArrayToolkit::column($tags, 'name'),
            'default'=> $default,
            'create'=>$create,
            'course_teachers'  =>  $course_teachers,
            'teacherids'   =>  $teacherids
        ));
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function removeTeacherFromCourseAction(Request $request){
        if($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $teacherId = isset($data['tid']) ? intval($data['tid']) : 0;
            $courseId = isset($data['cid']) ? intval($data['cid']) : 0;
            if(empty($teacherId) || empty($courseId)){
                return $this->createJsonResponse(array('status' => false,'message' => '教师或课程ID不能为空'));
            }

            $this->getCourseCooperationService()->deleteByTeacherAndCourse($teacherId,$courseId);
            $this->getCourseService()->deleteLessonByCourseAndTeacher($courseId,$teacherId);

            return $this->createJsonResponse(array('status' => true,'message' => '删除教师和课程成功！'));
        }
    }


    /*
     * @author Czq
     * 前台课程 虚拟实验室 调用
     */
    public function virtualLabAction(Request $request, $id){
//            $id = $request->query->get('id');
//            $vaitualLabStatus = C('VIRTUAL_LAB_STATUS');
//            $publicCourse = CENTER == 'center'? 1 : 0;
//            $webCode = C('WEBSITE_CODE');
//            if(!$id || !$vaitualLabStatus){return false;}
//
//            $virtualLab = $this->getCourseService()->getVirtualLab($webCode, $publicCourse, $id);
//            $virtualLab['virtualLabStatus'] = $virtualLab['virtualLabStatus']? 1 : 0;
//            if($virtualLab && $virtualLab['virtualLabStatus']){
//                $virtualLab['list'] = json_decode($virtualLab['list']);
//            }else{
//                return false;
//            }
//            return $this->render("CourseManage:virtual-lab#Course", array(
//                'virtualLab'=>$virtualLab,
//            ));
    }

    public function nicknameCheckAction(Request $request, $courseId)
    {
        $nickname = $request->query->get('value');
        $result = $this->getUserService()->isNicknameAvaliable($nickname);
        if ($result) {
            $response = array('success' => false, 'message' => '该用户还不存在！');
        } else {
            $user = $this->getUserService()->getUserByNickname($nickname);
            $isCourseStudent = $this->getCourseService()->isCourseStudent($courseId, $user['id']);
            if($isCourseStudent){
                $response = array('success' => false, 'message' => '该用户已是本课程的学员了！');
            } else {
                $response = array('success' => true, 'message' => '');
            }
            
            $isCourseTeacher = $this->getCourseService()->isCourseTeacher($courseId, $user['id']);
            if($isCourseTeacher){
                $response = array('success' => false, 'message' => '该用户是本课程的教师，不能添加!');
            }
        }
        return $this->createJsonResponse($response);
    }

    public function detailAction(Request $request, $id,$create)
    {   
        $courseSerObj = $this->getCourseService();
        if($id) $course = $courseSerObj->tryManageCourseFind($id);
        else $course =array();
        $create = $create ? : 0;
        
        if($request->getMethod() == 'POST'){
            $detail = $request->request->all();

            $deta['about'] =  isset($detail['about']) ? remove_xss(trim($detail['about'])) : '';
            $deta['selectPicture'] =  isset($detail['selectPicture']) ? trim($detail['selectPicture']) :"";
            $deta['coursePrice'] =  isset($detail['coursePrice']) ? intval($detail['coursePrice']) :0; 
            $deta['_csrf_token'] =  isset($detail['_csrf_token']) ? $detail['_csrf_token'] : '';
            $deta['goals'] = (empty($detail['goals']) or !is_array($detail['goals'])) ? array() : $detail['goals'];
            $deta['audiences'] = (empty($detail['audiences']) or !is_array($detail['audiences'])) ? array() : $detail['audiences'];
            $courseSerObj->updateCourse($id, $deta);
            $this->setFlashMessage('success', '课程详细信息已保存！');
            $url = $this->generateUrl('course_manage_set',array('id' => $id,'create'=>$create));
            return $this->success('success',$url);
        }
        return $this->render('CourseManage:detail', array(
            'course' => $course,
            'create' => $create,
        ));
    }
    
    public function setAction(Request $request, $id,$create)
    {
        $courseSerObj = $this->getCourseService();
        if($id) $course = $courseSerObj->tryManageCourse($id);
        else $course =array();
        $create = $create ? : 0;

        if($request->getMethod() == 'POST'){
          
        }

        return $this->render('CourseManage:publish', array(
            'course' => $course,
            'create' => $create,
        ));
    }

    public function pictureAction(Request $request, $id)
    {
        $course = $this->getCourseService()->tryManageCourse($id);

        if($request->getMethod() == 'POST'){
            $file = $request->files->get('picture');
            if (!FileToolkit::isImageFile($file)) {
                return $this->createMessageResponse('error', '上传图片格式错误，请上传jpg, gif, png格式的文件。');
            }

            $filenamePrefix = "course_{$course['id']}_";
            $hash = substr(md5($filenamePrefix . time()), -8);
            $ext = $file->getClientOriginalExtension();
            $filename = $filenamePrefix . $hash . '.' . $ext;

            $directory = getParameter('redcloud.upload.public_directory') . '/tmp';
            $file = $file->move($directory, $filename);

            $fileName = str_replace('.', '!', $file->getFilename());
            
            return $this->redirect($this->generateUrl('course_manage_picture_crop', array(
                'id' => $course['id'],
                'file' => $fileName)
            ));
        }

        return $this->render('CourseManage:picture', array(
            'course' => $course,
        ));
    }
    /**
     * 课程图片上传
     * @author tanhaitao  2015-09-19
     */
    public function pictureAutoAction(Request $request, $id)
    {
        $course = $this->getCourseService()->tryManageCourseFind($id);
        $picEdit = $request->query->get('edit') ? : 0;
        $picTab = $request->query->get('tab') ? : 0;

        if($request->getMethod() == 'POST'){
            $file = $request->files->get('picture');
           // var_dump($file);die("@3");
            if (!FileToolkit::isImageFile($file)) {
                return $this->createJsonResponse(["status"=>false,"msg"=>"上传图片格式错误，请上传jpg, gif, png格式的文件。"]);
//                return $this->createMessageResponse('error', '上传图片格式错误，请上传jpg, gif, png格式的文件。');
            }

            $filenamePrefix = "course_{$course['id']}_";
            $hash = substr(md5($filenamePrefix . time()), -8);
            $ext = $file->getClientOriginalExtension();
            $filename = $filenamePrefix . $hash . '.' . $ext;

            $directory = getParameter('redcloud.upload.public_directory') . '/tmp';
            $file = $file->move($directory, $filename);

            $fileName = str_replace('.', '!', $file->getFilename());
            
            //@todo 文件名的过滤
            $filename = str_replace('!', '.', $filename);
            $filename = str_replace(array('..' , '/', '\\'), '', $filename);

            $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;
            
            $picturePic = '/tmp/' . $filename;
            try {
            $imagine = new Imagine();
                $image = $imagine->open($pictureFilePath);
            } catch (\Exception $e) {
                @unlink($pictureFilePath);
                return $this->createJsonResponse(["status"=>false,"msg"=>"该文件为非图片格式文件，请重新上传。"]);
//                return $this->createMessageResponse('error', '该文件为非图片格式文件，请重新上传。');
            }

            $naturalSize = $image->getSize();
            $scaledSize = $naturalSize->widen(480)->heighten(270);
            $pictureUrl = getParameter('redcloud.upload.public_url_path') . '/tmp/' . $filename;
            $natural = explode(' ',$naturalSize.height);
            $natural = explode('x', $natural[0]);
            $scale = explode(' ',$scaledSize.height);
            $scale = explode('x', $scale[0]);
        
            $frdata = array('status' => true, 'id' => $course['id'],'pictureUrl' => $pictureUrl,'nwidth' => $natural[0],'nheight' =>$natural['1'],'swidth' => $scale[0],'sheight' => $scale['1'] ,'picturePic'=>$picturePic);
            echo json_encode($frdata);
            //echo "<script>parent.upload_search_callback('" . $datajson . "');</script>";
            exit;
        }
        
        
        return $this->render('CourseManage:pictureAuto', array(
            'course' => $course,
            'picEdit'=>$picEdit,
            'picTab'=>$picTab,
        ));
    }
    /**
     * 课程图片裁剪并提交数据库
     * @author tanhaitao  2015-09-20
     */
    public function pictureCropAutoAction(Request $request, $id)
    {
        $picEdit = $request->query->get('edit') ? : 0;
        
        $c = $request->request->all();
        $data['x'] = ($c['x']<0) ? 0 : $c['x'];
        $data['y'] = ($c['y']<0) ? 0 : $c['y'];
        $data['width'] = $c['width'];
        $data['height'] = $c['height'];
        $pictureFilePath = DATA_PATH . '/' . ltrim($c['pictureFilePath'],'/');
        $r = $this->getCourseService()->changeCoursePicture($c['id'], $pictureFilePath, $data);
        
        if($r){
            if($picEdit == 1) $r = $this->getCourseService()->updateCourseFind($r['id'], array('selectPicture'=>""));
            $r['largePicture'] =  C('SITE_URL') . getDefaultPath('course', $r['largePicture']);
            
            return $this->createJsonResponse(array('status' => 'ok','data'=>$r,'edit'=>$picEdit));
        }
        return $this->createJsonResponse(array('status' => 'error'));
    }
    
    public function pictureCropAction(Request $request, $id)
    {
        $course = $this->getCourseService()->tryManageCourse($id);

        //@todo 文件名的过滤
        $filename = $request->query->get('file');
        $filename = str_replace('!', '.', $filename);
        $filename = str_replace(array('..' , '/', '\\'), '', $filename);

        $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;

        if($request->getMethod() == 'POST') {
            $c = $request->request->all();
            $this->getCourseService()->changeCoursePicture($course['id'], $pictureFilePath, $c);
            return $this->redirect($this->generateUrl('course_manage_picture', array('id' => $course['id'])));
        }

        try {
        $imagine = new Imagine();
            $image = $imagine->open($pictureFilePath);
        } catch (\Exception $e) {
            @unlink($pictureFilePath);
            return $this->createMessageResponse('error', '该文件为非图片格式文件，请重新上传。');
        }

        $naturalSize = $image->getSize();
        $scaledSize = $naturalSize->widen(480)->heighten(270);
        // @todo fix it.
//        $assets = $this->container->get('templating.helper.assets');
        $pictureUrl = getParameter('redcloud.upload.public_url_path') . '/tmp/' . $filename;
//        $pictureUrl = ltrim($pictureUrl, ' /');
//        $pictureUrl = $assets->getUrl($pictureUrl);
        
        return $this->render('CourseManage:picture-crop', array(
            'course' => $course,
            'pictureUrl' => $pictureUrl,
            'naturalSize' => $naturalSize,
            'scaledSize' => $scaledSize,
        ));
    }

    public function priceAction(Request $request, $id)
    {
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($id);
        

        $coinSetting=$this->getSettingService()->get('coin',array());
        if(isset($coinSetting['cash_rate'])){
            $cashRate=$coinSetting['cash_rate'];
        }else{
            $cashRate=1;
        }

        $canModifyPrice = true;
        $teacherModifyPrice = $this->setting('course.teacher_modify_price', true);
        if ($this->setting('vip.enabled')) {
            $levels = $this->getLevelService()->findEnabledLevels();
        } else {
            $levels = array();
        }
        if (empty($teacherModifyPrice)) {
            if (!$this->getCurrentUser()->isAdmin()) {
                $canModifyPrice = false;
                goto response;
            }
        }

        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();

            if(isset($fields['freeStartTime'])){
                $fields['freeStartTime'] = strtotime($fields['freeStartTime']);
                $fields['freeEndTime'] = strtotime($fields['freeEndTime']);
            }
            
            $course = $courseSerObj->updateCourse($id, $fields);

            $this->setFlashMessage('success', '课程价格已经修改成功!');
        }



        response:
        return $this->render('CourseManage:price', array(
            'course' => $course,
            'canModifyPrice' => $canModifyPrice,
            'levels' => $this->makeLevelChoices($levels),
            'cashRate'=> empty($cashRate)? 1 : $cashRate
        ));
    }
    /**
     * 课程分成比例 
     * 郭俊强 2015-09-10
     */
    public function ratioAction(Request $request, $id){
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($id);
         if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();
            $course = $courseSerObj->updateCourse($id, $fields);
            $this->setFlashMessage('success', '课程老师提成已经修改成功!');
        }

        return $this->render('CourseManage:ratio', array(
            'course' => $course
        ));
    }
    public function dataAction(Request $request, $id)
    {
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($id);

        $isLearnedNum=$courseSerObj->searchMemberCount(array('isLearned'=>1,'courseId'=>$id));

        $learnTime=$courseSerObj->searchLearnTime(array('courseId'=>$id));
        $learnTime=$course['studentNum']==0 ? 0 : intval($learnTime/$course['studentNum']);

        $noteCount=$this->getNoteService()->searchNoteCount(array('courseId'=>$id));

        $questionCount=$this->getThreadService()->searchThreadCount(array('courseId'=>$id,'type'=>'question'));

        $lessons=$courseSerObj->searchLessons(array('courseId'=>$id),array('createdTime', 'ASC'),0,1000);

        foreach ($lessons as $key => $value) {
            $lessonLearnedNum=$courseSerObj->findLearnsCountByLessonId($value['id']);

            $finishedNum=$courseSerObj->searchLearnCount(array('status'=>'finished','lessonId'=>$value['id']));
            
            $lessonLearnTime=$courseSerObj->searchLearnTime(array('lessonId'=>$value['id']));
            $lessonLearnTime=$lessonLearnedNum==0 ? 0 : intval($lessonLearnTime/$lessonLearnedNum);

            $lessonWatchTime=$courseSerObj->searchWatchTime(array('lessonId'=>$value['id']));
            $lessonWatchTime=$lessonWatchTime==0 ? 0 : intval($lessonWatchTime/$lessonLearnedNum);

            $lessons[$key]['LearnedNum']=$lessonLearnedNum;
            $lessons[$key]['length']=intval($lessons[$key]['length']/60);
            $lessons[$key]['finishedNum']=$finishedNum;
            $lessons[$key]['learnTime']=$lessonLearnTime;
            $lessons[$key]['watchTime']=$lessonWatchTime;

        }

        return $this->render('CourseManage:learning-data', array(
            'course' => $course,
            'isLearnedNum'=>$isLearnedNum,
            'learnTime'=>$learnTime,
            'noteCount'=>$noteCount,
            'questionCount'=>$questionCount,
            'lessons'=>$lessons,
        ));
    }

    private function makeLevelChoices($levels)
    {
        $choices = array();
        foreach ($levels as $level) {
            $choices[$level['id']] = $level['name'];
        }
        return $choices;
    }

    public function teachersAction(Request $request, $id)
    {
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($id);

        if($request->getMethod() == 'POST'){
            
            $data = $request->request->all();
            $data['ids'] = empty($data['ids']) ? array() : array_values($data['ids']);

            $teachers = array();
            foreach ($data['ids'] as $teacherId) {
                $teachers[] = array(
                    'id' => $teacherId,
                    'isVisible' => empty($data['visible_' . $teacherId]) ? 0 : 1
                );
            }

            $courseSerObj->setCourseTeachers($id, $teachers);
            $this->setFlashMessage('success', '教师设置成功！');

            return $this->redirect($this->generateUrl('course_manage_teachers',array('id' => $id,'center'=>(CENTER == 'center') ? 1 : 0))); 
        }

        $teacherMembers = $courseSerObj->findCourseTeachers($id);
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($teacherMembers, 'userId'));

        $teachers = array();
        foreach ($teacherMembers as $member) {
            if (empty($users[$member['userId']])) {
                continue;
            }
            $teachers[] = array(
                'id' => $member['userId'],
                'nickname' => $users[$member['userId']]['nickname'],
                'avatar'  => $this->getWebExtension()->getFilePath($users[$member['userId']]['smallAvatar'], 'avatar.png'),
                'isVisible' => $member['isVisible'] ? true : false,
            );
        }

        return $this->render('CourseManage:teachers', array(
            'course' => $course,
            'teachers' => $teachers
        ));
    }

    
    public function classTimecardAction(Request $request, $courseId, $classId)
    {
       
        $courseSerObj = $this->getCourseService();
        $classMemberObj = $this->getCourseMemberService();
        $course = $courseSerObj->tryManageCourse($courseId);

        $conditions['page'] = $request->query->get('page');
        if(CENTER && CENTER== 'center')
            $this->getCourseSignInService()->setCourseType(1);
        $count = $this->getCourseSignInService()->searchSignInNumCount(array('courseId'=>$courseId, 'classId'=>$classId));
        $paginator = new Paginator($this->get('request'), $count, 10);
        
        $limit = array($paginator->getOffsetCount(),$paginator->getPerPageCount());
        $parm['courseId'] = $courseId;
        $parm['classId']  = $classId;
        $parm['limit']    = $limit;
        $SignInNum = $this->getCourseSignInService()->getClassSignInListNum($parm);
        $SignInList = $this->getCourseSignInService()->getCourseSignInList($courseId,$classId);
        $attendance = array();
        foreach ($SignInNum as $key => $value) {
            foreach ($SignInList as $v) {
                if($value == $v['number']){
                  $attendance[$key] = $v;  
                }
            }
        }
       // var_dump($attendance);die("123");
        return $this->render('CourseManage:classTimecard', array(
            'attendance' => $attendance,
            'paginator' => $paginator,
            'course' => $course,
            'courseId' => $courseId,
            'classId' => $classId
        ));

    }
    
    public function timecardNumberListAction(Request $request, $courseId, $classId ,$number,$type)
    {
        if(CENTER && CENTER== 'center')
            $this->getCourseSignInService()->setCourseType(1);
        $courseSerObj = $this->getCourseService();
        $classMemberObj = $this->getCourseMemberService();
        $course = $courseSerObj->tryManageCourse($courseId);
        
        $conditions['page'] = $request->query->get('page');
        
        if(empty($type)){
            $count = $this->getCourseSignInService()->getSignInTotalNum($courseId, $classId, $number);
            $paginator = new Paginator($this->get('request'), $count, 10);
            $start = $paginator->getOffsetCount();
            $limit = $paginator->getPerPageCount();  
        }
        
        $SignInList = $this->getCourseSignInService()->getCourseSignInList($courseId,$classId);
        $numberList = array();
        foreach ($SignInList as $v) {
            if($number == $v['number']){
               $numberList= $v;  
             }
         }
        $allMemberList = $this->getCourseSignInService()->getSignInAllUserList($courseId,$classId,$number,$start,$limit);
        if($type > 0){
            $type--;
            $arr = array();
            foreach ($allMemberList as $k => $v) {
                if($v['isSignIn'] == $type ){
                    $arr[$k] = $v;
                }
            }
            
            $type ++; 
            $count = count($arr);
            $paginator = new Paginator($this->get('request'), $count, 10);
            $start = $paginator->getOffsetCount();
            $limit = $paginator->getPerPageCount(); 
            $allMemberList = array_slice($arr,$start,$limit);
        }
        
        return $this->render('CourseManage:timecardNumberList', array(
            'numberList' => $numberList,
            'allMemberList' => $allMemberList,
            'type' => $type,
            'paginator' => $paginator,
            'course' => $course,
            'courseId' => $courseId,
            'classId' => $classId,
            'number' => $number,
        ));

    }


    public function publishAction(Request $request, $id)
    {
        $this->getCourseService()->publishCourse($id);

        $user = $this->getCurrentUser();
//        if($user->isTeacher()) {
//            $this->buildTeacherHtml($user);
//        }

        return $this->createJsonResponse(true);
    }
    
    public function closeAction(Request $request, $id)
    {
        $this->getCourseService()->closeCourse($id);

        $user = $this->getCurrentUser();
//        if($user->isTeacher()) {
//            $this->buildTeacherHtml($user);
//        }

        return $this->createJsonResponse(true);
    }

    public function teachersMatchAction(Request $request)
    {
        $likeString = $request->query->get('q');
        $nickName = $request->request->get('name');
        
        if(!empty($likeString))
            $users = $this->getUserService()->searchUsers(array('nickname'=>$likeString, 'roles'=> 'ROLE_TEACHER'), 0, 10);
        if(!empty($nickName)){
            $use= $this->getUserService()->getTeacherByNickname($nickName);
            if(empty($use)) return $this->createJsonResponse(array('status' => 'error'));
            return $this->createJsonResponse(array('status' => 'ok', 'data' => array('id' => $use['id'],'nickname' => $use['nickname'],'avatar' => $this->getWebExtension()->getFilePath($use['smallAvatar'], 'avatar.png'), 'isVisible' => 1 ,'matchKey'=> $use['nickname'] )));
        }
        
        $teachers = array();
        foreach ($users as $user) {
            $teachers[] = array(
                'id' => $user['id'],
                'nickname' => $user['nickname'],
                'avatar' => $this->getWebExtension()->getFilePath($user['smallAvatar'], 'avatar.png'),
                'isVisible' => 1
            );
        }
        
        return $this->createJsonResponse($teachers);
    }

    public function categoryAction(){
        if(I('id')) $categoryIds = $this->getCourseService()->getCourseCategory(I('id'));
        else $categoryIds = '';

        if($_POST){
            if(!I('categoryId')){
                $this->error('请先选择课程分类');
            }
            $this->getCourseService()->addCourseCategory(I('id'),I('categoryId'));
            $this->success('保存成功');
        }

        $this->render('CourseManage:category-modal',compact('categoryIds','id'));
    }
    
    public function courseCategoryAction(){
        if(I('id')) $categoryIds = $this->getCourseService()->getCourseCategory(I('id'));
        else $categoryIds = '';
        $this->render('CourseManage:course-category-modal',array(
            "courseid" => I('id') ? : 0,
            "categoryIds" => $categoryIds
        ));
    }
    
    /**
     * 编辑课程标题
     * @author fubaosheng 2015-10-15
     */
    public function editTitleAction(Request $request){
        if($request->getMethod() == "GET")
            $this->ajaxReturn(array('status'=>'error','info'=>'提交方式错误'));
        if(CENTER == "center")
            $this->ajaxReturn(array('status'=>'error','info'=>C("PUBLIC_COURSE_NAME")."禁止修改课程名称"));
        $data = $request->request->all();
        $id = isset($data['id']) ? intval($data['id']) : 0;
        $title = isset($data['title']) ? trim($data['title']) : '';
        if(empty($id))
            $this->ajaxReturn(array('status'=>'error','info'=>'ID为空'));
        if(empty($title))
            $this->ajaxReturn(array('status'=>'error','info'=>'标题为空'));
        $title_len = mb_strlen($title,"utf-8");
        if( ($title_len < 1) || ($title_len > C("COURSE_NAME_MAX_LENGTH")) )
            $this->ajaxReturn(array('status'=>'error','info'=>'课程名称的长度范围为1~60个字符'));
        $user = $this->getCurrentUser();
        if(!$user->isLogin())
            $this->ajaxReturn(array('status'=>'error','info'=>'您尚未登录'));
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->getCourse($id);
        if(empty($course) || $course['isDeleted'])
            $this->ajaxReturn(array('status'=>'error','info'=>'课程不存在或已被删除'));
        $manageCourse =  $courseSerObj->hasCourseManagerRole($id,$user['id']);
        if(!$manageCourse)
            $this->ajaxReturn(array('status'=>'error','info'=>'您不是课程的教师或管理员，无权操作'));
        $courseSerObj->updateCourse($id,array('title'=>$title));
        $this->ajaxReturn(array('status'=>'success'));
    }
    
    /**
     * 编辑课程分类
     * @author fubaosheng 2015-10-15
     */
    public function editCategoryAction(Request $request){
        if($request->getMethod() == "GET")
            $this->ajaxReturn(array('status'=>'error','info'=>'提交方式错误'));
        $data = $request->request->all();
        $id = isset($data['id']) ? intval($data['id']) : 0;
        $categoryId = isset($data['categoryId']) ? trim($data['categoryId']) : '';
        if(empty($id))
            $this->ajaxReturn(array('status'=>'error','info'=>'ID为空'));
        if(empty($categoryId))
            $this->ajaxReturn(array('status'=>'error','info'=>'请至少选择一个课程分类'));
        $user = $this->getCurrentUser();
        if(!$user->isLogin())
            $this->ajaxReturn(array('status'=>'error','info'=>'您尚未登录'));
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->getCourse($id);
        if(empty($course) || $course['isDeleted'])
            $this->ajaxReturn(array('status'=>'error','info'=>'课程不存在或已被删除'));
        $manageCourse =  $courseSerObj->hasCourseManagerRole($id,$user['id']);
        if(!$manageCourse)
            $this->ajaxReturn(array('status'=>'error','info'=>'您不是课程的教师或管理员，无权操作'));
        $courseSerObj->addCourseCategory($id,$categoryId);
        $this->ajaxReturn(array('status'=>'success'));
    }

    private function getCourseInvitationService(){
        return createService('Course.CourseInvitationService');
    }

    private function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
    }

    private function getClassService()
    {
        return createService('Group.ClassService');
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }


    private function getLevelService()
    {
        return createService('Vip:Vip.LevelService');
    }

    private function getFileService()
    {
        return createService('Content.FileService');
    }

    private function getWebExtension()
    {
        return $this->container->get('redcloud.twig.web_extension');
    }

    private function getNotificationService()
    {
        return createService('User.NotificationService');
    }

    private function getTagService()
    {
        return createService('Taxonomy.TagService');
    }

    private function getNoteService()
    {
        return createService('Course.NoteService');
    }

    private function getThreadService()
    {
        return createService('Course.ThreadService');
    }

    private function getSettingService()
    {
        return createService('System.SettingService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

    protected function getClassroomService()
    {
        return createService('Classroom:Classroom.ClassroomService');
    }
    
    private function getCourseSignInService()
    {
        return createService('Course.CourseSignInService');
    }
    
    private function getCourseMemberService()
    {
        return createService('Course.CourseMemberService');
    }

    private function getCourseCooperationService(){
        return createService("Course.CourseCooperationService");
    }
}