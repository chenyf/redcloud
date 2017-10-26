<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class TagController extends \Home\Controller\BaseController
{
    /**
     * 获取所有标签，以JSONM的方式返回数据
     * 
     * @return JSONM Response
     */
    public function indexAction(Request $request)
    {   
        $tags = $this->getTagService()->findAllTags(0, 100);

        return $this->render('Tag:index', array(
            'tags'=>$tags
        ));
    }

    public function showAction(Request $request,$name)
    {   
        
        $courses = $paginator = null;

        $tag = $this->getTagService()->getTagByName($name);
        $courseSerObj = $this->getCourseService();
        if($tag) {  
            $conditions = array(
                'status' => 'published',
                'tagId' => $tag['id']
            );

            $paginator = new Paginator(
                $this->get('request'),
                $courseSerObj->searchCourseCount($conditions)
                , 10
            );       

            $courses = $courseSerObj->searchCourses(
                $conditions,
                'latest',
                $paginator->getOffsetCount(),
                $paginator->getPerPageCount()
            );
        }

        return $this->render('Tag:show', array(
            'tag'=>$tag,
            'courses'=>$courses,
            'paginator' => $paginator
        ));
    }

    public function allAction()
    {
        $data = array();

        $tags = $this->getTagService()->findAllTags(0, 100);
        foreach ($tags as $tag) {
            $data[] = array('id' => $tag['id'],  'name' => $tag['name'] );
        }
        return $this->createJsonmResponse($data);
    }

    public function matchAction(Request $request)
    {
        $data = array();
        $queryString = $request->query->get('q');
        $callback = $request->query->get('callback');
        $tags = $this->getTagService()->getTagByLikeName($queryString);
        foreach ($tags as $tag) {
            $data[] = array('id' => $tag['id'],  'name' => $tag['name'] );
        }
       
        return  $this->createJsonResponse($data);
    }

    protected function getTagService()
    {
        return createService('Taxonomy.TagServiceModel');
    }

    protected function getCourseService()
    {
        return createService('Course.CourseServiceModel');
    }

}