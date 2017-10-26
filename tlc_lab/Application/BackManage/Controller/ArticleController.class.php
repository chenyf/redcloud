<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Common\Lib\FileToolkit;
use Imagine\Gd\Imagine;

class ArticleController extends BaseController
{

    public function indexAction(Request $request)
    {
        $conditions = $request->query->all();
        $conditions['keyword']  = '123';
        $categoryId = 0;
        if(!empty($conditions['categoryId'])){
            $conditions['includeChildren'] = true;
            $categoryId = $conditions['categoryId'];
        }
        $paginator = new Paginator(
            $request,
            $this->getArticleService()->searchArticlesCount($conditions),
            20
        );
        $articles = $this->getArticleService()->searchArticles(
            $conditions,
            'normal',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        $categoryIds = ArrayToolkit::column($articles, 'categoryId');
        $categories = $this->getCategoryService()->findCategoriesByIds($categoryIds);
        $categoryTree = $this->getCategoryService()->getCategoryTree();
        return $this->render('Article:index',array(
            'articles' => $articles,
            'categories' => $categories,
            'paginator' => $paginator,
            'categoryTree'  => $categoryTree,
            'categoryId'  => $categoryId
        ));
    }

    public function createAction(Request $request)
    {
        if($request->getMethod() == 'POST'){
            $article = $request->request->all();
            $article['tags'] = array_filter(explode(',', $article['tags']));

            $article = $this->getArticleService()->createArticle($article);

            return $this->redirect($this->generateUrl('admin_article'));
        }
        
        $categoryTree = $this->getCategoryService()->getCategoryTree();
        return $this->render('Article:article-modal',array(
            'categoryTree'  => $categoryTree,
            'category'  => array( 'id' =>0, 'parentId' =>0)
        ));
    }

    public function editAction(Request $request, $id)
    {
        $article = $this->getArticleService()->getArticle($id);

        if (empty($article)) {
//            throw $this->createNotFoundException('文章已删除或者未发布！');
        }
        if(empty($article['tagIds'])){
            $article['tagIds'] = array();
        }

        $tags = $this->getTagService()->findTagsByIds($article['tagIds']);
        $tagNames = ArrayToolkit::column($tags, 'name');

        $categoryId = $article['categoryId'];
        $category = $this->getCategoryService()->getCategory($categoryId);

        $categoryTree = $this->getCategoryService()->getCategoryTree();
        if ($request->getMethod() == 'POST') {
            $formData = $request->request->all();
            $article = $this->getArticleService()->updateArticle($id, $formData);
            return $this->redirect($this->generateUrl('admin_article'));
        }
        return $this->render('Article:article-modal',array(
            'article' => $article,
            'categoryTree'  => $categoryTree,
            'category'  => $category,
            'tagNames' => $tagNames
        ));
    }

    public function setArticlePropertyAction(Request $request,$id,$property)
    {
         $this->getArticleService()->setArticleProperty($id, $property);
         return $this->createJsonResponse(true); 
    }

    public function cancelArticlePropertyAction(Request $request,$id,$property)
    {
         $this->getArticleService()->cancelArticleProperty($id, $property);
         return $this->createJsonResponse(true);
    }

    public function trashAction(Request $request, $id)
    {
        $this->getArticleService()->trashArticle($id);
        return $this->createJsonResponse(true);
    }

    public function thumbRemoveAction(Request $Request,$id)
    {
        $this->getArticleService()->removeArticlethumb($id);
        return $this->createJsonResponse(true);
    }

    public function deleteAction(Request $request)
    {
        $ids = $request->request->get('ids', array());
        $id = $request->query->get('id', null);
        if ($id) {
            array_push($ids, $id);
        }
        $result = $this->getArticleService()->deleteArticlesByIds($ids);
        
        if($result){
            return $this->createJsonResponse(array("status" =>"failed"));
        } else {
            return $this->createJsonResponse(array("status" =>"success")); 
        }
    }

    public function publishAction(Request $request, $id)
    {
        $this->getArticleService()->publishArticle($id);
        return $this->createJsonResponse(true);
    } 

    public function unpublishAction(Request $request, $id)
    {
        $this->getArticleService()->unpublishArticle($id);
        return $this->createJsonResponse(true);
    }

    public function settingAction(Request $request)
    {   
        $articleSetting = $this->getSettingService()->get('article', array());

        $default = array(
            'name' => '资讯频道',
            'pageNums' => 20
        );

        $articleSetting = array_merge($default, $articleSetting);
        if ($request->getMethod() == 'POST') {
            $articleSetting = $request->request->all();
            $this->getSettingService()->set('article', $articleSetting);
            $this->getLogService()->info('article', 'update_settings', "更新资讯频道设置", $articleSetting);
            $this->setFlashMessage('success', '资讯频道设置已保存！');
        };

        return $this->render('Article:setting', array(
            'articleSetting' => $articleSetting
        ));
    }

    public function showUploadAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('picture');
            if (!FileToolkit::isImageFile($file)) {
                return $this->createMessageResponse('error', '上传图片格式错误，请上传jpg, gif, png格式的文件。');
            }

            $filenamePrefix = "article_";
            $hash = substr(md5($filenamePrefix . time()), -8);
            $ext = $file->getClientOriginalExtension();
            $filename = $filenamePrefix . $hash . '.' . $ext;

//            $directory = $this->container->getParameter('redcloud.upload.public_directory') . '/tmp';
            $directory = getParameter('redcloud.upload.public_directory') . '/tmp';
            $file = $file->move($directory, $filename);
            $fileName = str_replace('.', '!', $file->getFilename());
            $articlePicture = $this->getPictureAtributes($fileName);
            return $this->render('Article:article-picture-crop-modal', array(
                'filename' => $fileName,
                'pictureUrl' => $articlePicture['pictureUrl'],
                'naturalSize' => $articlePicture['naturalSize'],
                'scaledSize' => $articlePicture['scaledSize']
            ));
        }
        return $this->render('Article:aticle-picture-modal', array(
            'pictureUrl' => "",
        ));
    }

    public function pictureCropAction(Request $request)
    {

        if($request->getMethod() == 'POST') {
            $options = $request->request->all();
            $filename = $request->query->get('filename');
            $filename = str_replace('!', '.', $filename);
            $filename = str_replace(array('..' , '/', '\\'), '', $filename);
            $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;
//            $pictureFilePath = $this->container->getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;
            $response = $this->getArticleService()->changeIndexPicture(realpath($pictureFilePath), $options);
            die(json_encode($response));
            //return new Response(json_encode($response));
        }
    }

    private function getPictureAtributes($filename)
    {
        $filename = str_replace('!', '.', $filename);
        $filename = str_replace(array('..' , '/', '\\'), '', $filename);
        $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;
//        $pictureFilePath = $this->container->getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;
        try {
            $imagine = new Imagine();
            $image = $imagine->open($pictureFilePath);
        } catch (\Exception $e) {
            @unlink($pictureFilePath);
            return $this->createMessageResponse('error', '该文件为非图片格式文件，请重新上传。');
        }
        $naturalSize = $image->getSize();
        $scaledSize = $naturalSize->widen(270)->heighten(270);
        $pictureUrl = $this->container->getParameter('redcloud.upload.public_url_path') . '/tmp/' . $filename;

        return array(
            'naturalSize' => $naturalSize,
            'scaledSize' => $scaledSize,
            'pictureUrl' => $pictureUrl
        );
    }

    private function getArticleService()
    {
        return createService('Article.ArticleServiceModel');
    }

    private function getTagService()
    {
        return createService('Taxonomy.TagServiceModel');
    }

    private function getCategoryService()
    {
        return createService('Article.CategoryServiceModel');
    }

    private function getFileService()
    {
        return createService('Article.FileServiceModel');
    }

    private function getSettingService()
    {
        return createService('System.SettingServiceModel');
    }

}