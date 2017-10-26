<?php
namespace BackManage\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Think\Exception;

class CategoryController extends BaseController
{
    public function embedAction(Request $request,$group)
    {
        $type = intval($request->query->get('type')) ? : 0;
        if(!in_array($type, array(0,1,2))) $type = 0;
        
        $currentUser = $this->getCurrentUser();
        $categories = $this->getCategoryService()->getCategoryTree($group['id']);
        //$categories = $this->getCategoryService()->findUserCategoryTree($currentUser['id'],$type);
        return $this->render('Category:embed', array(
            'type'  => $type,
            'categories' => $categories
        ));
    }

    public function createAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $category = $this->getCategoryService()->createCategory($request->request->all());
            return $this->renderTbody();
        }

        $category = array(
            'id' => 0,
            'name' => '',
            'code' => '',
            'description'=>'',
            'groupId' => (int) $request->query->get('groupId'),
            'parentId' => (int) $request->query->get('parentId', 0),
            'weight' => 0,
            'icon' => ''
        );

        return $this->render('Category:modal', array(
            'category' => $category
        ));
    }

    public function editAction(Request $request, $id)
    {
        $category = $this->getCategoryService()->getCategory($id);
        if (empty($category)) {
            throw $this->createNotFoundException();
        }
        
        $type = intval($request->query->get('type')) ? : 0;
        if(!in_array($type, array(0,1,2))) $type = 0;
        
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $type = isset($data["type"]) ? intval($data["type"]) : 0;
            unset($data["type"]);
            $category = $this->getCategoryService()->updateCategory($id, $data);
            return $this->renderTbody($type);
        }
        $param  = array("iconFromType"=>$category['iconFromType'],"icon"=>$category['icon']);
        $obj = new \Common\Common\Url();
        $category['iconUrl'] = $obj->getCategoryIconUrl($param);
        return $this->render('Category:modal', array(
            'category' => $category,
            'type' => $type
        ));
    }

    public function deleteAction(Request $request, $id,$deleSelf)
    {
        $category = $this->getCategoryService()->getCategory($id);
        if (empty($category)) {
            throw $this->createNotFoundException();
        }
        
        if($deleSelf != '0') $deleSelf = '1';
        $this->getCategoryService()->deleteCategory($id,$deleSelf);

        return $this->renderTbody();
    }
    
    /**
     * 恢复删除的分类
     * @author fubaosheng 2015-05-14
     */
    public function recoverAction(Request $request, $id){
        $category = $this->getCategoryService()->getCategory($id);
        if (empty($category)) {
            throw $this->createNotFoundException();
        }

        $this->getCategoryService()->recoverCategory($id);
        return $this->renderTbody();
    }

	public function recommendAction($id,$status){
		try{
			$this->getCategoryService()->recommend($id,$status);
			$this->success('操作成功');
		}catch (Exception $e){
			$this->error('操作失败:'.$e->getMessage());
		}

	}
    

    public function checkCodeAction(Request $request)
    {
        $code = $request->query->get('value');
        $exclude = $request->query->get('exclude');

        $avaliable = $this->getCategoryService()->isCategoryCodeAvaliable($code, $exclude);

        if ($avaliable) {
            $response = array('success' => true, 'message' => '');
        } else {
            $response = array('success' => false, 'message' => '编码已被占用，请换一个。');
        }

        return $this->createJsonResponse($response);
    }

    public function uploadFileAction (Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $originalFile = $this->get('request')->files->get('file');
            $file = $this->getUploadFileService()->addFile('category', 0, array('isPublic' => 1), 'local', $originalFile);
            $file['hashId'] = "/dataFolder/".$file['hashId'];
            $resp =  new Response(json_encode($file));
            return $resp->send();
        }
    }

    private function renderTbody($type = 2)
    {
        $currentUser = $this->getCurrentUser();
//        $categories = $this->getCategoryService()->getCategoryTree($groupId);
        $categories = $this->getCategoryService()->findUserCategoryTree($currentUser['id'],$type);
        return $this->render('Category:tbody', array(
            'categories' => $categories,
            'type' => $type
        ));
    }


    private function getCategoryService()
    {
        return createService('Taxonomy.CategoryServiceModel');
    }

    private function getUploadFileService()
    {
        return createService('File.UploadFileServiceModel');
    }

}