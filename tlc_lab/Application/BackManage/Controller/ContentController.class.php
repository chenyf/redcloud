<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Common\Model\Content\Type\ContentTypeFactory;

class ContentController extends BaseController
{

    public function indexAction(Request $request)
    {
        $conditions = array_filter($request->query->all());
        $paginator = new Paginator(
            $request,
            $this->getContentService()->searchContentCount($conditions),
            20
        );
//        dump($paginator);die;
        $contents = $this->getContentService()->searchContents(
            $conditions,
            array('createdTime', 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
//        dump($contents);die;
        $userIds = ArrayToolkit::column($contents, 'userId');
        $users = $this->getUserService()->findUsersByIds($userIds);
        $categoryIds = ArrayToolkit::column($contents, 'categoryId');
        $categories = $this->getCategoryService()->findCategoriesByIds($categoryIds);
//        dump($contents);die;
        return $this->render('Content:index',array(
            'contents' => $contents,
            'users' => $users,
            'categories' => $categories,
            'paginator' => $paginator,
        ));
    }

    public function createAction(Request $request, $type)
    {
        $type = ContentTypeFactory::create($type);
        if ($request->getMethod() == 'POST') {
            $content = $request->request->all();
            $content['type'] = $type->getAlias();

            $file = $request->files->get('picture');
            if(!empty($file)){
                $record = $this->getFileService()->uploadFile('default', $file);
                $content['picture'] = $record['uri'];
            }

            $content = $this->filterEditorField($content);

            $content = $this->getContentService()->createContent($this->convertContent($content));
//            dump($content);die;
            return $this->render('Content:content-tr',array(
                'content' => $content,
                'category' => $this->getCategoryService()->getCategory($content['categoryId']),
                'user' => $this->getCurrentUser(),
            ));
        }

        return $this->render('Content:content-modal',array(
            'type' => $type,
        ));
    }

    public function editAction(Request $request, $id)
    {
        $content = $this->getContentService()->getContent($id);
        $type = ContentTypeFactory::create($content['type']);
        $record = array();
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('picture');
            if(!empty($file)){
                $record = $this->getFileService()->uploadFile('default', $file);
            }
            $content = $request->request->all();
            if(isset($record['uri'])){
                $content['picture'] = $record['uri'];
            }

            $content = $this->filterEditorField($content);

            $content = $this->getContentService()->updateContent($id, $this->convertContent($content));
            return $this->render('Content:content-tr',array(
                'content' => $content,
                'category' => $this->getCategoryService()->getCategory($content['categoryId']),
                'user' => $this->getCurrentUser(),
            ));
        }

        return $this->render('Content:content-modal',array(
            'type' => $type,
            'content' => $content,
        ));

    }

    public function trashAction(Request $request, $id)
    {
        $this->getContentService()->trashContent($id);
        return $this->createJsonResponse(true);
    }

    public function deleteAction(Request $request, $id)
    {
        $this->getContentService()->deleteContent($id);
        return $this->createJsonResponse(true);
    }

    public function publishAction(Request $request, $id)
    {
        $this->getContentService()->publishContent($id);
        return $this->createJsonResponse(true);
    }


    public function aliasCheckAction(Request $request)
    {
        $value = $request->query->get('value');
        $thatValue = $request->query->get('that');
//        dump($thatValue);die;
        if (empty($value)) {
            return $this->createJsonResponse(array('success' => true, 'message' => ''));
        }

        if ($value == $thatValue) {
            return $this->createJsonResponse(array('success' => true, 'message' => ''));
        }

        $avaliable = $this->getContentService()->isAliasAvaliable($value);
        if ($avaliable) {
            return $this->createJsonResponse(array('success' => true, 'message' => ''));
        }

        return $this->createJsonResponse(array('success' => false, 'message' => '该URL路径已存在'));
    }

    private function filterEditorField($content)
    {
        if($content['editor'] == 'richeditor'){
            $content['body'] = $content['richeditor-body'];
        } elseif ($content['editor'] == 'none') {
            $content['body'] = $content['noneeditor-body'];
        }

        unset($content['richeditor-body']);
        unset($content['noneeditor-body']);
        return $content;
    }

    private function convertContent($content)
    {
        if (isset($content['tags'])) {
            $tagNames = array_filter(explode(',', $content['tags']));
            $tags = $this->getTagService()->findTagsByNames($tagNames);
            $content['tagIds'] = ArrayToolkit::column($tags, 'id');
        } else {
            $content['tagIds'] = array();
        }

        $content['publishedTime'] = empty($content['publishedTime']) ? 0 : strtotime($content['publishedTime']);

        $content['promoted'] = empty($content['promoted']) ? 0 : 1;
        $content['sticky'] = empty($content['sticky']) ? 0 : 1;
        $content['featured'] = empty($content['featured']) ? 0 : 1;

        return $content;
    }

    private function getContentService()
    {
        return createService('Content.ContentService');
    }

    private function getTagService()
    {
        return createService('Taxonomy.TagService');
    }

    private function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
    }

    private function getFileService()
    {
        return createService('Content.FileService');
    }

}