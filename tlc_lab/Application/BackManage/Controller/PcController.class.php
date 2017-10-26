<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;

class PcController extends BaseController {

	/**
	 * 直播客户端版本列表
	 */
	public function versionAction(Request $request) {
                $formData = $request->query->all();
                $isLocalCenter = \Common\Lib\WebCode::isLocalCenterWeb();
                $conditions['siteSelect'] = (isset($formData['siteSelect']) && !empty($formData['siteSelect'])) ? $formData['siteSelect'] : '';
                $conditions['keyword'] = (isset($formData['keyword']) && !empty($formData['keyword'])) ? $formData['keyword'] : '';
                $conditions['keyword_type'] = (isset($formData['keyword_type']) && !empty($formData['keyword_type'])) ? $formData['keyword_type'] : '';
                $conditions['keyword_kind'] = (isset($formData['keyword_kind']) && !empty($formData['keyword_kind'])) ? $formData['keyword_kind'] : 0;
                if($isLocalCenter){
                    $count = $this->getPcVersionService()->getWebCodeCount($conditions);
                }else{
                    $count = $this->getPcVersionService()->count();
                }
		$paginator = new Paginator(
			$request,
			$count,
			15
		);
		$versions = $this->getPcVersionService()->getVersionList(
			$paginator->getOffsetCount(),
			$paginator->getPerPageCount(),
			$conditions,
                        $switch = true
		);
		$this->render('Pc:version-list', array(
			'versions' => $versions,
			'paginator' => $paginator,
			'formData' => $formData,
		));
	}

	/**
	 * 创建客户端版本
	 */
	public function versionCreateAction() {
		//客户端类型
		$type = C('PC_CLIENT_TYPE');

		if (I('post.')) {
                        $arr = I('post.');
                        $arr['appWebCode'] = isset($arr['siteSelect']) && !empty($arr['siteSelect']) ? $arr['siteSelect'] : "";
                        unset($arr['siteSelect']);
                        unset($arr['schoolName']);
                        $id = $this->getPcVersionService()->addVersion($arr);
			if ($id) {
                            $info = "管理员添加新的客户端版本,appWebCode是{$arr['appWebCode']},类型是{$type[$arr['type']]},版本是{$arr['version']},升级地址是{$arr['url']},id是{$id}";
                            $this->getLogService()->info('Pc', 'version_add',$info);
                            $this->redirect($this->generateUrl('admin_pc_version'));
			} else {
                            $this->error($this->getPcVersionService()->getError());
			}
		}

		$this->render('Pc:version-modal', array(
			'types'        => $type,
		));
	}

	/**
	 * 修改客户端版本
	 */
	public function versionEditAction() {
		//客户端类型
		$type = C('PC_CLIENT_TYPE');
		$id          = I('id') ? I('id') : 0;
		$version = $this->getPcVersionService()->find($id);
		if(I('post.')){
                        $arr = I('post.');
                        $arr['appWebCode'] = isset($arr['siteSelect']) && !empty($arr['siteSelect']) ? $arr['siteSelect'] : "";
                        unset($arr['siteSelect']);
                        unset($arr['schoolName']);
			if ($this->getPcVersionService()->editVersion($arr)) {
				$info = "管理员修改了app版本,appWebCode是{$arr['appWebCode']},类型是{$type[$arr['type']]},版本是{$arr['version']},升级地址是{$arr['url']},id是{$arr['id']}";
				$this->getLogService()->info('Pc', 'version_edit', $info);
				$this->redirect($this->generateUrl('admin_pc_version'));
			} else {
				$this->error($this->getPcVersionService()->getError());
			}
		}
		$version['url'] = htmlspecialchars_decode($version['url']);
		$version['scanUrl'] = htmlspecialchars_decode($version['scanUrl']);
		$this->render('Pc:version-modal', array(
			'types'        => $type,
			'course_types' => $course_type,
			'version'      => $version
		));
	}

	/**
	 * 删除客户端版本
	 */
	public function delVersionAction($id){
		$id = $id ? $id : 0 ;
                
                //客户端类型
		$type = C('PC_CLIENT_TYPE');
		$version= $this->getPcVersionService()->getVersionFind($id);
		$result = $this->getPcVersionService()->delete($id);

		if($result > 0){
                    $info = "管理员删除了客户端版本,appWebCode是{$version['appWebCode']},类型是{$type[$version['type']]},版本是{$version['version']},升级地址是{$version['url']},id是{$id}";
                    $this->getLogService()->info('Pc', 'version_delete', $info);
                    return $this->ajaxReturn(array('status' => 'ok'));
		} else {
                    return $this->ajaxReturn(array('status' => 'error'));
		}
	}
        
    

	protected function getPcVersionService() {
		return createService('Pc.PcVersion');
	}
        
        protected function getLogService(){
                return createService('System.LogService');
        }
        
}