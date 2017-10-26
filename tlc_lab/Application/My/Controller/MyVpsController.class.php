<?php
/**
 * Created by PhpStorm.
 * User: sike
 * Date: 2016/8/10
 * Time: 15:16
 */

namespace My\Controller;
use Common\Lib\Connect;
use Common\Lib\Server;
use Org\Util\ArrayToolkit;

class MyVpsController extends \Home\Controller\BaseController
{
	
	private static $connect;
    
    public function _initialize(){
        $user = $this->getCurrentUser();
        if(!$user->isLogin())
            $this->redirect('User/Signin/index');
		
		if(self::$connect == null){
			self::$connect = new Connect();
			self::$connect->init_connect("10.20.6.2","admin","admin","admin");
		}
    }

    public function indexAction(){
		$my_vps = $this->getVpsService()->getMyVps();
        doLog("test for vnc");
        $server = self::$connect->getServer();
        //$res =$server->getVncByInstanceId("9556e6bd-5199-4278-9f04-59c0186ebe15");
        //doLog("vnc url: " . $res); 
		if(empty($my_vps)){
			$serverList = $this->getAvailableServerList();
			return $this->render("MyVps:index",array('vpsList'=>$serverList));
		}else{
			$vnc_url =$server->getVncByInstanceId($my_vps["vpsId"]);
			
			return $this->render("MyVps:index",array('my_vps'=>$my_vps,'vnc_url' => $vnc_url));
		}
        
    }
	
	public function showVpsAction(Request $request){
		$my_vps = $this->getVpsService()->getMyVps();
		return $this->render("MyVps:vps_show_view",array('my_vps'=>$my_vps));
	}
	
	public function showVpsListAction(Request $request){
		$serverList = $this->getAvailableServerList();
		return $this->render("MyVps:vps_select_view",array('vpsList'=>$serverList));
	}

	//用户选择虚拟机
	public function selectVpsAction(Request $request){
		if ($request->getMethod() == 'POST'){
			$vpsId = $_POST['vpsid'];
			$serverList = $this->getAvailableServerList();
			$vpsIdList = ArrayToolkit::column($serverList,'instanceId');
			if(empty($vpsId) || !in_array($vpsId,$vpsIdList)){
				return $this->createJsonResponse(array("error" => 1,"message" => "虚拟机不可用，请重新选择"));
			}else{
				$addResult = $this->getVpsService()->addMyVps($vpsId);
				if(empty($addResult)){
					return $this->createJsonResponse(array("error" => 0,"message" => "选择虚拟机成功！"));
				}else{
					return $this->createJsonResponse(array("error" => 1,"message" => $addResult));
				}
			}
		}
		
		return $this->createJsonResponse(array("error" => 2,"message" => "方法不正确"));
	}
	
	//开机、关机、重启
	public function oprationVpsAction(Request $request){
		if ($request->getMethod() == 'POST'){
			$my_vps = $this->getVpsService()->getMyVps();
			if(empty($my_vps)){
				return $this->createJsonResponse(array("error" => 1,"message" => "您无可用虚拟机"));
			}

			$data = $request->request->all();
			$codeKey = isset($data['key']) ? intval($data['key']) : 0;

			$options = array(
				0 => '',
				1 => '开机',
				2 => '关机',
				3 => '重启',
			);

			if(!in_array($codeKey,array(1,2,3))){
				return $this->createJsonResponse(array("error" => 3,"message" => "操作不正确！"));
			}

			$server = self::$connect->getServer();

			$vpsId = $my_vps['vpsId'];

			if ($codeKey == 1 && $server->startServer($vpsId)){
				return $this->createJsonResponse(array("error" => 0,"message" => "{$options[$codeKey]}成功！"));
			}else if($codeKey == 2 && $server->stopServer($vpsId)){
				return $this->createJsonResponse(array("error" => 0,"message" => "{$options[$codeKey]}成功！"));
			}else if($codeKey == 3 && $server->rebootServer($vpsId)){
				return $this->createJsonResponse(array("error" => 0,"message" => "{$options[$codeKey]}成功！"));
			}

			return $this->createJsonResponse(array("error" => 4,"message" => "{$options[$codeKey]}失败！"));
		}

		return $this->createJsonResponse(array("error" => 2,"message" => "方法不正确"));
	}


	//vps-list-modal
    public function listVpsAction(){
        $serverList = $this->getAvailableServerList();
        return $this->render("MyVps:vps-list-modal",array(
            'vpsList' => $serverList
        ));
    }

	//获取可用虚拟机列表，被选择过的不可用
	public function getAvailableServerList(){
		$serverList = $this->getAllServerList();

		$usedVpsIdList = $this->getVpsService()->selectVpsIdList();

		$serverList = array_filter($serverList,function($s) use($usedVpsIdList){

			if(!isset($s['instanceId']) || empty($s['instanceId']) || in_array($s['instanceId'],$usedVpsIdList)){
				return false;
			}else{
				return true;
			}

		});

		return $serverList;
	}

	//获取所有的虚拟机列表，包括不可用的
	public function getAllServerList(){
		$server = self::$connect->getServer();
		$serverList = $server->getServerList();
		return $this->setFlavorAndImage($serverList);
	}

	//设置镜像名和规格名
	public function setFlavorAndImage(array $serverList){
		$image = self::$connect->getImage();
		$flavor = self::$connect->getFlavor();
		foreach($serverList as $key => $value){
			//*****set the imageId value with imageName ,not imageId show in html
			$serverImageId = $serverList[$key]["imageId"];
			$imageTemp = $image->getImageByImageId($serverImageId);
			$imageName = empty($imageTemp) ? "" : $imageTemp->getImageName();
			$serverList[$key]["imageName"]=$imageName;
			//*****set the flavorId value with flavorName, not flavorId show in html
			$serverFlavorId = $serverList[$key]["flavorId"];
			$flavorTemp = $flavor->getFlavorByFlavorId($serverFlavorId);
			$flavorName = empty($flavorTemp) ? "" : $flavorTemp->getFlavorName();
			$serverList[$key]["flavorName"]=$flavorName;
		}

		return $serverList;
	}

    public function getVpsService(){
        return createService('Course.VpsService');
    }

}
