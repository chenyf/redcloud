<?php
namespace Home\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class SearchController extends BaseController
{
    public function indexAction(Request $request)
    {
        $courses = $paginator = null;

        $currentUser = $this->getCurrentUser();

        if($request->getMethod() == 'POST'){
            $fields = $_POST;
//            $fields = $request->request->all();
            $keywords = trim(remove_xss($fields['q']));

            if (!$keywords) {
                goto response;
            }

            $conditions = array(
                'status' => 'published',
                'title' => $keywords,
                'categoryId' => $fields['categoryIds']
            );

            $paginator = new Paginator(
                $this->get('request'),
                $this->getCourseService()->searchCourseCount($conditions)
                , 12
            );

            $courses = $this->getCourseService()->searchCourses(
                $conditions,
                'latest',
                $paginator->getOffsetCount(),
                $paginator->getPerPageCount()
            );

        }

        $parentId = 0;
        $categories = $this->getCategoryService()->findAllCategoriesByParentId($parentId);
        
        $categoryIds=array();
        for($i=0;$i<count($categories);$i++){
            $id = $categories[$i]['id'];
            $name = $categories[$i]['name'];
            $categoryIds[$id] = $name;
        }

        $keywords = empty($keywords) ? "" : htmlentities($keywords,ENT_QUOTES ,'utf-8');

        response:
        return $this->render('Search:index', array(
            'courses' => $courses,
            'paginator' => $paginator,
            'keywords' => $keywords,
            'categoryIds' => $categoryIds
        ));
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getThreadService()
    {
        return createService('Course.ThreadService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

   protected function getLevelService()
    {
        return createService('Vip:Vip.LevelService');
    }

     protected function getVipService()
    {
        return createService('Vip:Vip.VipService');
    }    

    private function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
    }

}