<?php

namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;
use Think\Upload;

class CmsController extends BaseController {

    public function indexAction(Request $request) {
//      获取所有传递过来的参数  $data
        $data = $request->query->all();
        $conditions = array(
            'startDateTime' => isset($data['startDateTime']) ? strtotime($data['startDateTime']) : 0,
            'endDateTime' => isset($data['endDateTime']) ? strtotime($data['endDateTime']) : 0,
            'title' => I('module', ''),
            'agstatus' => I('agstatus', 0),
            'type' => ''
        );
        
        
        if (empty($data)) {
            $data = array();
        } else {
            if (isset($data['type']) && $data['type'])
                $data['type'] = $data['type'] - 1;
        }
        $conditions = array_merge($conditions, $data);
        $paginator = new Paginator(
                $this->get('request'), $this->getCmsListService()->searchCmsListCount($conditions), 8
        );
        $applys = $this->getCmsListService()->searchCmsList(
                $conditions, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

        return $this->render("Cms:index", array(
                    $conditions,
                    'res' => $applys,
                    'paginator' => $paginator
        ));
    }

    public function modularAction(Request $request) {
        $id = $_GET['cmsId'];
        header("content-type:text/html;charset=utf8");
        $arr = $this->Cms_ModularServer()->getModularData($id);

        $cmsIdArr = implode(ArrayToolkit::column($arr, 'id'), ",");
        
        $v = $this->Cms_ModularServer()->getConfig($cmsIdArr);
   //    echo "<pre>";
       //  print_r($v);die;
        $status = $this->getCmsListService()->getCms($id);

        $res = $this->Cms_ModularServer()->getFindModular($id);
       
        return $this->render('Cms:modular', array("cmsId" => $id, "list" => $arr, "find" => $res, "data" => $v, "status" => $status['status'],"title"=>$status['title']));

   
//        if(!isset($_COOKIE['counter'])) $_COOKIE['counter'] = 0;
//        $b =  $_COOKIE['counter']+1;
//        setCookie("counter",$c,time()+3600*24);
    
       


    }
   public function showAction(Request $request)
   {
       $id = $_GET['id'] ? : 0;
       if($id){
           $result= $this->getCmsListService()->getCms($id);
       }else{
           $result = array();
       }
       return $this->render('Cms:create_list',array('id'=>$id,'CmsList'=>$result));
   }
    public function totalAction(Request $request)
   {
      $id = $_GET['id'] ? : 0;
      if($_POST){
          $statisticalCode = $_POST['statisticalCode'];
          $updTime = time();
          $arr = array('statisticalCode'=>$statisticalCode,'updTime'=>$updTime);
         if(mb_strlen($statisticalCode,'UTF8') > 500 ) { $this->error('咨询代码内容超过了最大的500字数'); }
         $result = $this->getCmsListService()->getCodeCms($arr,$id);
       if($result ==true ){
           $this->success('统计代码编写成功');
       }else{
//           $this->error('统计代码编辑失败');
            $this->error('您没有进行任何统计代码的编写');
       }
      }
      $res = $this->getCmsListService()->getCmsList($id);
      return $this->render('Cms:total_code',array('id'=>$id,'row'=>$res));
   }
    public function seeAction(Request $request)
   {
      $id = $_GET['id'] ? : 0;
      if($_POST){
        $consultCode = $_POST['consultCode'];
        $updTime = time();
        $arr = array('consultCode'=>$consultCode,'updTime'=>$updTime);
        if(mb_strlen($consultCode,'UTF8') > 500 ) { $this->error('统计代码内容超过了最大的500字数'); }
        $result = $this->getCmsListService()->consultCms($arr,$id);
        if($result ==true ){
            $this->success('咨询代码编写成功');
        }else{
//            $this->error('咨询代码编辑失败');
             $this->error('您没有进行任何咨询代码的编写');
        }
      }
      $res = $this->getCmsListService()->getCmsListcode($id);
      return $this->render('Cms:see_code',array('id'=>$id,'row'=>$res));
   }

   public function createAction(Request $request){
       $currentUser = $this->getCurrentUser();
       $uid =  $currentUser->id;
       $title = $_POST['title'];
       $keywords = $_POST['keywords'];
       $description = $_POST['description'];
       if($title == null){ $this->error('请输入文件的标题，再继续！'); }
       if(mb_strlen($title,'UTF8') < 2 ) { $this->error('你的标题过短，重新输入！至少2位'); }
       if($keywords == null) { $this->error('关键字不能为空'); }
       if(mb_strlen($keywords,'UTF8') < 5 ) { $this->error('你的关键字过短，重新输入！至少5位'); }
       if(mb_strlen($description,'UTF8') > 500 ) { $this->error('你的描述内容超过了最大的500字数'); }
       $time = time();
       $arr = array(
           'title'=>$title,
           'keywords'=>$keywords,
           'description'=>$description,
           'createdTime'=>$time,
           'userId'=>$uid
       );
       $res = $this->getCmsListService()->getData($arr);
       if($res == true){
           $this->success('添加数据成功',U('Cms/modular',"cmsId=$res"));
       }else{
           $this->error('添加失败');
       }
   }
   public function editAction(Request $request){
       $data = $request->query->all();
       $currentUser = $this->getCurrentUser();
       $uid =  $currentUser->id;
       $id = $_GET['id'];
       $title = $_POST['title'];
       $keywords = $_POST['keywords'];
       $description = $_POST['description'];
       $updTime = time();
       $arr = array('title'=>$title,'keywords'=>$keywords,'description'=>$description,'updTime'=>$updTime,'updUid'=>$uid);
       $result = $this->getCmsListService()->getUpdataCms($id,$arr);
       if($result == true){
           $this->success('修改成功');
       }else{
           $this->error('修改失败');
       }
   }
   public function delAction(){
       $id = $_GET['id'];
       $id  = $id ? : 0;
       $res = $this->getCmsListService()->getdelCms($id);
       if($res == true){
           $this->success('删除成功');
       }$this->error('删除失败');   
   }
   public function statusAction(Request $request)
   {
       $id = $_GET['id']; 
       $status = $this->getCmsListService()->getStatusCms($id);
       if($status == true){
           $this->success('发布成功');
       }$this->error('已经发布，无需重复');
   }
   public function statusUpdateAction()
   {
       $id = $_GET['id']; 
       $status = $this->getCmsListService()->getStatusUpdateCms($id);
       if($status == true){
           $this->success('取消发布修改成功');
       }$this->error('取消发布修改失败');
   }
   public function agstatusAction(Request $request)
   {
       $id = $_GET['id']; 
       $status = $this->getCmsListService()->getagStatusCms($id);
       if($status == true){
           $this->success('恢复落地页，成功！');
       }$this->error('恢复落地页失败');
   }
   public function browseAction(){
      $id = $_GET['id'] ? :0;
      $res = $_SERVER["SERVER_NAME"];
      return $this->render('Cms:browse',array('id'=>$id,'test'=>$res));
   }
    public function insertModularAction() {
         $id = $_POST['cmsId'];/*
          $S = $this->Cms_ModularServer()->getFindShuffling($id);
         * 
        
        
          $I = $this->Cms_ModularServer()->getFindImg($id);
          $N = $this->Cms_ModularServer()->getFindNav($id); */
        $res = $this->Cms_ModularServer()->getFindModular($id);
          if($_POST['cmsType']==$res['cmsType']){
          echo 1;die;
          }
        
        $data = array("cmsId" => $_POST['cmsId'],
            "cmsType" => $_POST['cmsType'],
            "title" => $_POST['title']
        );
        $this->Cms_ModularServer()->insertModular($data);
    }

    public function createModularAction() {
        $id = $_GET['id']; //

        $cmsId = $_GET['cmsId']; //总id

        $res = $this->Cms_ModularServer()->getSingleData($id);

        $arr = $this->Cms_ModularServer()->getTitle($cmsId);

        $list = $this->Cms_ModularServer()->getList($id); //查询图片模块
    

        $this->render("Cms:create-Modilar", array("order" => $res, "list" => $arr, "cmsId" => $cmsId, "listTitle" => $list));
    }
    
    
    public function eaditModularAction(){
          $id = $_GET['id']; //

        $cmsId = $_GET['cmsId']; //总id

        $res = $this->Cms_ModularServer()->getSingleData($id);

        $arr = $this->Cms_ModularServer()->getTitle($cmsId);
        
        $list = $this->Cms_ModularServer()->getList($id); //查询图片模块

        $this->render("Cms:eaditModular", array("order" => $res, "list" => $arr, "cmsId" => $cmsId, "listTitle" => $list));
    }

    public function updateLandingAction(){
        $cmsModId=$_POST['cmsModId'];
        $cmsId=$_POST['cmsId'];
        if($_POST['coursename']){
            $key = array_keys($_POST['VideoUrl']);;
              for($i = 0; $i < count($_POST['VideoUrl']); $i++) {
                 $data['sort']=$key[$i];
                  $data['picPath']=$_POST['VideoUrl'][$i];
                  $data['videoPath']=$_POST['videoPath'][$i];
                  $id=$_POST['id'][$i];
                  $data['polyvVid']=$_POST['polyvVid'][$i];
                  $data['title']=$_POST['videotitle'][$i];
                  $data['createdTime'] = time();
                  $this->Cms_ModularServer()->updateImgSort($data,$id);
              }
              
                 $arr['CourseName'] = $_POST['coursename'];
                $arr['PreferentialPrice'] = $_POST['Preferentialprice'];
                $arr['ButtonDescribe'] = $_POST['ButtonDescribe'];
                $arr['price'] = $_POST['price'];
                $arr['buyUrl'] = $_POST['buyurl'];
              
               
        }elseif ($_POST['picPath']) {
            $key = array_keys($_POST['picPath']);
            for ($i = 0; $i < count($_POST['picPath']); $i++) {
                 $id=$_POST['id'][$i];
                $data['picPath'] = $_POST['picPath'][$i];
                $data['url'] = $_POST['url'][$i];
                $data['color'] = $_POST['color'][$i];
                $data['createdTime'] = time();
                 $data['sort']=$key[$i];
                $this->Cms_ModularServer()->updateImgSort($data, $id);
            }
            
            
       
    }
                $arr['createdTime'] = time();
                $arr['title'] = $_POST['nav'];
                 $nav['title'] = $_POST['nav'];
               $this->Cms_ModularServer()->updateNavSort($nav, $cmsModId);
          $this->Cms_ModularServer()->updateCmsModular($arr, $cmsModId);
    return $this->redirect("BackManage/Cms/modular", array("cmsId" => $cmsId));
    
            }
    
    
    public function ajaxDeleteNavAction() {
        $ancharId = $_POST['ancharId'];
        if ($_GET['id']) {
            $id = $_GET['id']; //删除图片
            $arr = $this->Cms_ModularServer()->selpicPath($id);


            if (is_file("." . $arr["picPath"])) {
                if (unlink("." . $arr["picPath"])) {
                    
                }
            }
            $this->Cms_ModularServer()->delImg($id);
        }

        $this->Cms_ModularServer()->delModularById($ancharId);
    }
    
    
    

    public function addAttributeAction() {
     
        $cmsModId = $_POST['cmsModId'];
        $cmsId = $_POST['cmsId'];
        $nav = $_POST['nav'];
        $rootPath = "./Data/private_photo/Upload/" . C('WEBSITE_CODE') . "/videoPic";
        if (!file_exists($rootPath))
            @mkdir($rootPath, 0777, true);
        if ($_POST['shuffling']) {//轮播
            for ($i = 0; $i < count($_POST['picPath']); $i++) {
                $data[$i]['picPath'] = $_POST['picPath'][$i];
                $data[$i]['url'] = $_POST['url'][$i];
                $data[$i]['createdTime'] = time();
                $data[$i]['cmsModId'] = $cmsModId;
                $data[$i]['color'] = $_POST['color'][$i];
            }
            $arr['createdTime'] = time();

           // $arr['height'] = $_POST['height'];
            $arr['direction'] = 1;
        }else if($_POST['img']){
             for ($i = 0; $i < count($_POST['picPath']); $i++) {
                $data[$i]['picPath'] = $_POST['picPath'][$i];
                $data[$i]['url'] = $_POST['url'][$i];
                $data[$i]['createdTime'] = time();
                $data[$i]['cmsModId'] = $cmsModId;
                $data[$i]['color'] = $_POST['color'][$i];
            }
            $arr['createdTime'] = time();

            // $arr['height'] = $_POST['height'];
            $arr['direction'] = 2;
        } elseif ($_POST['anchorId']) {//导航
            //echo "<pre>";
            // print_r($_POST);die;
           
            $state = $this->Cms_ModularServer()->deleteModularConfig($cmsModId);
            $sort = array_keys($_POST['anchorId']);
            for ($i = 0; $i < count($_POST['anchorId']); $i++) {
                $data[$i]['anchorId'] = $_POST['anchorId'][$i];
                $data[$i]['title'] = $_POST['title'][$i];
                $data[$i]['createdTime'] = time();
                $data[$i]['sort'] = $sort[$i];
                $data[$i]['cmsModId'] = $cmsModId;
            }

            $arr['createdTime'] = time();
        } else {
            for ($i = 0; $i < count($_POST['videoPath']); $i++) {
           
                $data[$i]['videoPath'] = $_POST['videoPath'][$i];
                $data[$i]['title'] = $_POST['videotitle'][$i];
                $data[$i]['polyvVid'] = $_POST['polyvVid'][$i];
                $data[$i]['mediaSource'] = "polyv";
                $data[$i]['picPath'] = $_POST['imgPath'][$i];
                $data[$i]['createdTime'] = time();
                $data[$i]['cmsModId'] = $cmsModId;

               
            }

            $arr['CourseName'] = $_POST['coursename'];
            $arr['PreferentialPrice'] = $_POST['Preferentialprice'];
            $arr['ButtonDescribe'] = $_POST['ButtonDescribe'];
            $arr['price'] = $_POST['price'];
            $arr['buyUrl'] = $_POST['buyurl'];
            $arr['createdTime'] = time();
        }

        $upfind['title'] = $nav;
        $arr['title'] = $nav;

        $this->Cms_ModularServer()->addAttribute($data);
        $this->Cms_ModularServer()->updateCmsModular($arr, $cmsModId);
        $this->Cms_ModularServer()->updateNavSort($upfind, $cmsModId);
        return $this->redirect("BackManage/Cms/modular", array("cmsId" => $cmsId));
    }

    public function orderAction() {
        $value = $_POST['sort'];
        $key = array_keys($value);
        for ($i = 0; $i < count($value); $i++) {
            $id = $value[$i];
            $data['sort'] = $key[$i];
            $this->Cms_ModularServer()->updateSort($data, $id);
            $this->Cms_ModularServer()->updateNavSort($data, $id);
        }
    }

    public function ajaxPhotoAction() {
        $typeArr = array("jpg", "png", "gif"); //允许上传文件格式
        $path = "/Data/private_photo/Upload/" . C('WEBSITE_CODE') . "/videoPic";
        if (!file_exists($path))
            @mkdir($path, 0777, true);


        if (isset($_POST)) {
            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $name_tmp = $_FILES['file']['tmp_name'];
            if (empty($name)) {
                echo json_encode(array("error" => "您还未选择图片"));
                exit;
            }
            $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型

            if (!in_array($type, $typeArr)) {
                echo json_encode(array("error" => "清上传jpg,png或gif类型的图片！"));
                exit;
            }
            if ($size > (5 * 1024 * 1024)) {
                echo json_encode(array("error" => "图片大小已超过5兆！"));
                exit;
            }

            $pic_name = time() . rand(10000, 99999) . "." . $type; //图片名称
            $pic_url = "." . $path . "/" . $pic_name; //上传后图片路径+名称
            $pic = $path . "/" . $pic_name; //上传后图片路径+名称

            if (move_uploaded_file($name_tmp, $pic_url)) { //临时文件转移到目标文件夹
                echo json_encode(array("error" => "0", "pic" => $pic, "name" => $pic_name));
            } else {
                echo json_encode(array("error" => "上传有误，清检查服务器配置！"));
            }
        }
    }

    public function deleteAction() {
        $id = $_GET['id'];

        $cmsId = $_GET['cmsId'];
        $arr = $this->Cms_ModularServer()->getPicPath($id);

        for ($i = 0; $i < count($arr); $i++) {

            if (is_file("." . $arr[$i]["picPath"])) {
                if (unlink("." . $arr[$i]["picPath"])) {
                    
                }
            }
        }
        $this->Cms_ModularServer()->deleteModular($id);
        $this->Cms_ModularServer()->delModularById($id);
        $this->Cms_ModularServer()->deleteModularConfig($id);
        echo $_GET['cmsType'];
    }

    protected function Cms_ModularServer() {
        return createService('Cms.CmsModularServer');
    }
    public function helpAction(){
        
        $this->render('Cms:help');
    }
    private function getCmsListService() {
        return createService('Cms.CmsListServiceModel');
    }

}
