<?php

namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use PHPExcel;
use PHPExcel_Writer_Excel2007;

class GeneralizeController extends BaseController {

    public function indexAction(Request $request) {
        $conditions = $request->query->all();
        $data = array();
        if (!empty($conditions["username"])) {
            $userid = createService("User.UserServiceModel")->getUserId($conditions["username"]);
            $userId = array();
            foreach ($userid as $key => $value) {
                foreach ($value as $val)
                    $userId[] = $val;
            }
             $data['uid'] = array('in', $userId);
        } 

        if (!empty($conditions["phone"])) {
            $phone = createService("User.UserServiceModel")->findUserByVerifiedMobile($conditions["phone"]);
            $data['uid'] = array($phone["id"]);
        }
        if (!empty($userId) && !empty($phone)) {
            $userIdArr = array_intersect($userId, array($phone["id"])) ? : array(); 
            $data['uid'] = array('in',$userIdArr);
         }elseif(!empty($userId) && empty($phone)){
             $userIdArr = $userId;
         }elseif(empty($userId) && !empty($phone)){
             $userIdArr = array($phone["id"]);
             $data['uid'] = array('in',$userIdArr);
         }  

         if (!empty($conditions["level"])){
             $leveluid = createService("Generalize.GeneralizeUserServiceModel")->getLevelUid($conditions["level"]);
             $userId = array();
                foreach ($leveluid as $key => $value) {
                   foreach ($value as $val)
                       $userId[] = $val;
               }

               $data['uid'] = array('in',$userId);
         }

        $paginator = new Paginator(
                $request, $this->GeneralizeUserService()->searchGeneralizeCount($data), 10
        );

        $questions = $this->GeneralizeUserService()->searchGeneralize(
                $data, $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

       $res =  $this->GeneralizeLevelService()->searchGeneralizeLevel();

     $level =   ArrayToolkit::index($res,"levelCode");

        return $this->render('Generalize:index', array(
                    $conditions,
                    'GeneralizeUsers' => $questions,
                    'paginator' => $paginator,
                    "res"=>$res,
                    "levelCode"=>$level,
                    "code" =>$conditions['level']
                        )
        );
        
    }

    public function buylistAction(Request $request, $nickname) {
        if(!empty($nickname)){
           $userid = createService("User.UserServiceModel")->getUserId($nickname);
            $userId = array();
            foreach ($userid as $key => $value) {
                foreach ($value as $val)
                    $userId[] = $val;
            };
            $conditions['uid'] = array('in', $userId);
        }

        if (I('puid')) {
            $conditions['pUid'] = I('puid');
        }
        $paginator = new Paginator(
                $request, createService('Generalize.GeneralizeBuyService')->getBuyCourseCount($conditions), 10
        );
        $questions = createService('Generalize.GeneralizeBuyService')
                ->getBuyCourseData($conditions, $paginator->getOffsetCount(), $paginator->getPerPageCount());

        return $this->render("Generalize:buy_details", array(
                    'buyDetalis' => $questions,
                    'paginator' => $paginator
        ));
    }

    public function reglistAction(Request $request) {
        $conditions = $request->query->all();
        $paginator = new Paginator(
                $request, $this->GeneralizeRegService()->searchGeneralizeRegCount($conditions), 10
        );
        $questions = $this->GeneralizeRegService()->searchGeneralizeReg(
                $conditions, $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

        return $this->render("Generalize:reg_details", array(
                    'regDetalis' => $questions,
                    'paginator' => $paginator
        ));
    }

    public function UserAction(Request $request) {
        $nickname = I('username', '');
        if ($nickname != '') {
            $userid = createService("User.UserServiceModel")->getUserId($nickname);
            $userId = array();
            foreach ($userid as $key => $value) {
                foreach ($value as $val)
                    $userId[] = $val;
            };
            $conditions['pUid'] = array('in', $userId);
        }

        $keywordType = I('keywordType', '');
        if ($keywordType != '') {
            $conditions[$keywordType] = I('keyword', '');
        }

        $conditions['keyword'] = trim($conditions['keyword']);
        $conditions['switch'] = true;   # 不影响其他的查询操作 传入一个开关
        $paginator = new Paginator(
                $request, $this->GeneralizeUserService()->searchGeneralizeUserCount($conditions), 13
        );

        $questions = $this->GeneralizeUserService()->searchGeneralizeUser(
                $conditions, $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

        return $this->render('Generalize:user_details', array(
                    'paginator' => $paginator,
                    'generalizeUser' => $questions,
        ));
    }

    /**
     * 购买用户的列表
     */
    public function buyUserAction(Request $request,$nickname) {
       $userid = createService("User.UserServiceModel")->getUserId($nickname);
       $userId = array();
        foreach ($userid as $key => $value) {
            foreach ($value as $val)
                $userId[] = $val;
        };
        $conditions['uid'] = array('in', $userId);
        $paginator = new Paginator(
                $request, createService('Generalize.GeneralizeBuyService')->getBuyCourseCount($conditions), 13
        );
        $questions = createService('Generalize.GeneralizeBuyService')
                ->getBuyCourseData($conditions,$paginator->getOffsetCount(), $paginator->getPerPageCount());

        return $this->render('Generalize:buyUser', array(
                    'paginator' => $paginator,
                    'generalizeBuy' => $questions,
        ));
    }
    public function updateLevelAction(){
       $id = $_POST['id'];
       $val = $_POST['val'];
      $status =  $this->GeneralizeUserService()->updateLevel($id,$val);
      echo $status;
  
    }

        /**
     *  推广等级管理
     */
    public function LevelAction(Request $request) {
        $conditions = $request->query->all();
        $paginator = new Paginator(
                $request, $this->GeneralizeLevelService()->searchGeneralizeLevelCount($conditions), 13
        );
        $questions = $this->GeneralizeLevelService()->searchGeneralizeLevel(
                $conditions, $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        $this->GeneralizeLevelService()->Levelsync();

        return $this->render('Generalize:generalizeLevel', array(
                    'paginator' => $paginator,
                    'generalizeLevel' => $questions,
        ));
    }

    public function editAction() {
        $id = $_GET['id'] ? : 0;
        $levelId = $_POST['levelId'];
        $status = $_POST['status'];
        $ctime = time();
        $arr = array('levelId' => $levelId, 'status' => $status, 'ctime' => $ctime);
        $res = $this->GeneralizeUserService()->editGeneralize($id, $arr);
        if ($res == true) {
            $this->success("修改数据成功");
        } $this->error("修改数据失败");
    }
    
    public function editStatusAction(){
        $status=I('status');
        $status=  intval($status);
        $id=I('id');
        $bool=$this->GeneralizeUserService()->editStatus($id,$status);
        if($bool){
            $arr=array(
                'data'=>$status,
                'status'=>1
            );
            echo json_encode($arr);
            die;
        }else{
            echo 0;
            die;
        }
    }

    public function downGeneralizeUserAction(Request $request) {

        $get = $_GET ? : array();
        $nickname = I('username', '');
        if ($nickname != '') {
            $userid = createService("User.UserServiceModel")->getUserId($nickname);
            $userId = array();
            foreach ($userid as $key => $value) {
                foreach ($value as $val)
                    $userId[] = $val;
            };
            $conditions['pUid'] = array('in', $userId);
        }

        $keywordType = I('keywordType', '');
        if ($keywordType != '') {
            $conditions[$keywordType] = I('keyword', '');
        }

        $conditions['keyword'] = trim($conditions['keyword']);
        $conditions['switch'] = true;   # 不影响其他的查询操作 传入一个开关
//创建对象
        $excel = new PHPExcel();
//Excel表格式,简单的8列,与查询输出的字段有关系
        $letter = array('A', 'B', 'C', 'D', 'E');
//表头数组
        $tableheader = array('ID', '姓名', '推广用户', 'IP地址', '创建时间');
//填充表头信息

        for ($i = 0; $i < count($tableheader); $i++) {
            $excel->getActiveSheet()->setCellValue($letter[$i] . "1", $tableheader[$i]);
        }
// 数据数
        $data = $this->GeneralizeUserService()->searchDownGeneralizeUser($conditions);
//填充表格信息
        foreach ($data as $key => $value) {
            $i = $key + 2;
            $excel->getActiveSheet()->setCellValue("A" . $i, $value['id']);
            $excel->getActiveSheet()->setCellValue("B" . $i, $value['nickname']);
            $excel->getActiveSheet()->setCellValue("C" . $i, getUserName($value['pUid']));
            $excel->getActiveSheet()->setCellValue("D" . $i, $value['loginIp']);
            $excel->getActiveSheet()->setCellValue("E" . $i, date('Y-m-d H:i:s', $value['ctime']));
        }
//创建Excel输入对象
        $write = new PHPExcel_Writer_Excel2007($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="testdata.xls"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
        exit();
    }

    private function GeneralizeRegService() {
        return createService('Generalize.GeneralizeRegServiceModel');
    }

    private function GeneralizeUserService() {
        return createService('Generalize.GeneralizeUserServiceModel');
    }

    private function GeneralizeLevelService() {
        return createService('Generalize.GeneralizeLevelServiceModel');
    }

}