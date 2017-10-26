<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;

class SignCoordinateController extends BaseController
{

    public function _initialize(){
        if(!C('if_open_sign')){
            return $this->_404("未开启签到功能");
        }
    }

    public function helloAction(Request $request){
        $coordinate = $this->getSignCoordinateService()->findAllSignCoordinateConditions([]);
        print_r($coordinate);

        echo C('if_open_sign');
    }

    //地点坐标列表首页
    public function indexAction(Request $request)
    {
//        $conditions = [];
//        $coordinateObj = $this->getSignCoordinateService();
//        $paginator = new Paginator(
//            $this->get('request'),
//            $coordinateObj->searchSignCoordinateCount($conditions)
//            , 20
//        );
//
//        $coordinates = $coordinateObj->searchSignCoordinate(
//            $conditions,
//            $paginator->getOffsetCount(),
//            $paginator->getPerPageCount()
//        );

        return $this->render('Sign:coordinate_manage',array(
            'coordinates'   =>  [],
            'paginator'    =>  null
        ));

    }

    //添加地点坐标
    public function addAction(Request $request){
        if ($request->getMethod() == 'POST'){
            $data  = $request->request->all();
            $data['name'] = isset($data['name']) ? trim($data['name']) : "";
            $error = $this->getSignCoordinateService()->validFields($data);

            if(!is_null($error)){
                return $this->createJsonResponse(array('status'=>false,'message'=>$error));
            }

            if(!empty($this->getSignCoordinateService()->findSignCoordinateByName($data['name']))){
                return $this->createJsonResponse(array('status'=>false,'message'=>"已存在该名称的坐标地点！"));
            }

            $this->getSignCoordinateService()->addSignCoordinate($data);
            return $this->createJsonResponse(array('status'=>true,'message'=>'添加成功！'));
        }

        return $this->render('Sign:coordinate-edit-modal',array('create' => 1));
    }

    //编辑地点坐标
    public function editAction(Request $request,$id){
        $coordinate = $this->getSignCoordinateService()->findSignCoordinateById($id);
        if ($request->getMethod() == 'POST'){
            $data  = $request->request->all();
            $error = $this->getSignCoordinateService()->validFields($data);

            if(!is_null($error)){
                return $this->createJsonResponse(array('status'=>false,'message'=>$error));
            }

            $this->getSignCoordinateService()->updateSignCoordinate($data['id'],$data);
            return $this->createJsonResponse(array('status'=>true,'message'=>'更新成功！'));
        }

        return $this->render('Sign:coordinate-edit-modal',array('id'   =>  $id,'coordinate'    =>  $coordinate,'create' => 0));
    }

    //删除地点坐标
    public function deleteAction(Request $request,$id){
        if ($request->getMethod() == 'POST') {
            $id = $_POST['cid'];
            if(empty($id)){
                return $this->createJsonResponse(array('status'=>false,'message'=>'id不能为空'));
            }

            if($this->getSignCoordinateService()->deleteSignCoordinate($id)){
                return $this->createJsonResponse(array('status'=>true,'message'=>'删除地点坐标成功！'));
            }else{
                return $this->createJsonResponse(array('status'=>false,'message'=>'删除地点坐标失败！'));
            }
        }
        return $this->render('Sign:coordinate-remove-modal',array('id'   =>  $id));
    }

    private function getSignCoordinateService(){
        return createService('Sign.SignCoordinateService');
    }

    private function getSignService(){
        return createService('Sign.SignService');
    }

}