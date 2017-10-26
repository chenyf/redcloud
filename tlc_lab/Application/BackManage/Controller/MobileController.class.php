<?php
/*
 * 手机app管理
 * @package    
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;

class MobileController extends BaseController {

	/**
	 * app版本列表
	 */
	public function versionAction(Request $request) {
                $formData = $request->query->all();
                $isLocalCenter = \Common\Lib\WebCode::isLocalCenterWeb();
                $conditions['siteSelect'] = (isset($formData['siteSelect']) && !empty($formData['siteSelect'])) ? $formData['siteSelect'] : '';
                $conditions['keyword'] = (isset($formData['keyword']) && !empty($formData['keyword'])) ? $formData['keyword'] : '';
                $conditions['keyword_type'] = (isset($formData['keyword_type']) && !empty($formData['keyword_type'])) ? $formData['keyword_type'] : '';
                $conditions['keyword_kind'] = (isset($formData['keyword_kind']) && !empty($formData['keyword_kind'])) ? $formData['keyword_kind'] : 0;
                if($isLocalCenter){
                    $count = $this->getVersionService()->getWebCodeCount($conditions);
                }else{
                    $count = $this->getVersionService()->count();
                }
		$paginator = new Paginator(
			$request,
			$count,
			15
		);
		$versions = $this->getVersionService()->getVersionList(
			$paginator->getOffsetCount(),
			$paginator->getPerPageCount(),
			$conditions,
                        $switch = true
		);
		$this->render('Mobile:version-list', array(
			'versions' => $versions,
			'paginator' => $paginator,
			'formData' => $formData,
		));
	}

	/**
	 * 创建app版本
	 */
	public function versionCreateAction() {
		//客户端类型
		$type = C('MOBILE_TYPE');
		//课程类型
		$course_type = C('COURSE_TYPE');

		if (I('post.')) {
                        $arr = I('post.');
                        $arr['appWebCode'] = isset($arr['siteSelect']) && !empty($arr['siteSelect']) ? $arr['siteSelect'] : "";
                        unset($arr['siteSelect']);
                        unset($arr['schoolName']);
                        $id = $this->getVersionService()->addVersion($arr);
			if ($id) {
                            $info = "管理员添加新的app版本,appWebCode是{$arr['appWebCode']},类型是{$type[$arr['type']]},版本是{$arr['version']},升级地址是{$arr['url']},id是{$id}";
                            $this->getLogService()->info('mobile', 'version_add',$info);
                            $this->redirect($this->generateUrl('admin_mobile_version'));
			} else {
                            $this->error($this->getVersionService()->getError());
			}
		}

		$this->render('Mobile:version-modal', array(
			'types'        => $type,
			'course_types' => $course_type
		));
	}

	/**
	 * 修改App版本
	 */
	public function versionEditAction() {
		//客户端类型
		$type = C('MOBILE_TYPE');
		//课程类型
		$course_type = C('COURSE_TYPE');
		$id          = I('id') ? I('id') : 0;
        $version = $this->getVersionService()->find($id);
		if(I('post.')){
                        $arr = I('post.');
                        $arr['appWebCode'] = isset($arr['siteSelect']) && !empty($arr['siteSelect']) ? $arr['siteSelect'] : "";
                        unset($arr['siteSelect']);
                        unset($arr['schoolName']);
			if ($this->getVersionService()->editVersion($arr)) {
                            $info = "管理员修改了app版本,appWebCode是{$arr['appWebCode']},类型是{$type[$arr['type']]},版本是{$arr['version']},升级地址是{$arr['url']},id是{$arr['id']}";
                            $this->getLogService()->info('mobile', 'version_edit', $info);
                            $this->redirect($this->generateUrl('admin_mobile_version'));
			} else {
                            $this->error($this->getVersionService()->getError());
			}
		}
                $version['url'] = htmlspecialchars_decode($version['url']);
                $version['scanUrl'] = htmlspecialchars_decode($version['scanUrl']);
		$this->render('Mobile:version-modal', array(
			'types'        => $type,
			'course_types' => $course_type,
			'version'      => $version
		));
	}

	/**
	 * 删除App版本
	 */
	public function delVersionAction($id){
		$id = $id ? $id : 0 ;
                
                //客户端类型
		$type = C('MOBILE_TYPE');
        $version= M("mobile_version")->where("id = {$id}")->find();

        $result = $this->getVersionService()->delete($id);

		if($result > 0){
                    $info = "管理员删除了app版本,appWebCode是{$version['appWebCode']},类型是{$type[$version['type']]},版本是{$version['version']},升级地址是{$version['url']},id是{$id}";
                    $this->getLogService()->info('mobile', 'version_delete', $info);
                    return $this->ajaxReturn(array('status' => 'ok'));
		} else {
                    return $this->ajaxReturn(array('status' => 'error'));
		}
	}
        
     /**
	 * app配置信息
         * @author 谈海涛 2015-08-28 
	 */
	public function infoCfgAction(Request $request) {
                $isLocalCenter = \Common\Lib\WebCode::isLocalCenterWeb();
                utf8Header();
                if(!$isLocalCenter){
                    echo '您无权限访问';
                    return false;
                }
                $infoCfgList = $this->getAppInfoCfgService()->getInfoCfgList();
                $info = C('APP_INFO_CFG');
                foreach ($infoCfgList as $k => $v) {
                    $infoCfgList[$k]['info'] = $info[$v['cfgKey']];
                }
                
		$this->render('Mobile:infoCfg', array(
			'infoCfgList' => $infoCfgList,
                        'info'        => $info
		));
	}
        
        /**
	 * 添加app配置信息
         * @author 谈海涛 2015-08-28
	 */
	public function infoCfgCreateAction() {
            $data['cfgKey']   = $_POST['cfgKey']   ? $_POST['cfgKey']   : '';
            $data['cfgValue'] = $_POST['cfgValue'];//有的值为0
            
            $uid = $this->getCurrentUser()->id;
            $data['uid'] = intval($uid);
            $isExistsCfgKey = $this->getAppInfoCfgService()->isExistsCfgKey($data['cfgKey']);
            $userName = getUserName($data['uid']);
            if($isExistsCfgKey){
               $this->getAppInfoCfgService()->editSameCfgKey($data); 
               $this->getLogService()->info('Mobile', 'edit', "{$userName} 编辑了APP配置信息{$data['cfgKey']}");
            }else{
              $this->getAppInfoCfgService()->addInfoCfg($data); 
              $this->getLogService()->info('Mobile', 'add', "{$userName} 添加了APP配置信息{$data['cfgKey']}");
            }
            
            
            $this->redirect($this->generateUrl('admin_mobile_info_cfg'));
	}
        
        /**
	 *  编辑app配置信息
         * @author 谈海涛 2015-08-28
	 */
	public function infoCfgEditAction(Request $request ,$id) {
        echo '您无权限访问';
        return false;
	}

	protected function getVersionService() {
		return createService('Mobile.VersionModel');
	}
        
    protected function getLogService(){
        return createService('System.LogService');
    }
        
    protected function getAppInfoCfgService() {
		return createService('Mobile.AppInfoCfgService');
	}
}