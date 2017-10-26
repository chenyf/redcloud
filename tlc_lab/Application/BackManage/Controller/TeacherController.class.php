<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;

class TeacherController extends BaseController {

    public function indexAction (Request $request)
    {
        $fields = $request->query->all();
        $conditions = array(
            'roles'=>'ROLE_TEACHER',
            'keywordType'=>'',
            'keyword'=>'',
            'switch'=>true
        );
        $conditions = array_merge($conditions, $fields);
        $paginator = new Paginator(
            $this->get('request'),
            $this->getUserService()->searchUserCount($conditions),
            20
        );

        $users = $this->getUserService()->searchUsers(
            $conditions,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        foreach($users as $k => $v){
            $categoryId = $v['teacherCategoryId'];
            $category = createService("Taxonomy.CategoryModel")->getCategory($categoryId);
            $users[$k]['categoryName'] = $category['name'];
        }
//        dump($users);die;
        return $this->render('Teacher:index', array(
            'users' => $users ,
            'paginator' => $paginator
        ));
    }

    public function promoteAction(Request $request, $id)
    {
        $this->getUserService()->promoteUser($id);
        return $this->createJsonResponse(true);
    }

    public function promoteCancelAction(Request $request, $id)
    {
        $this->getUserService()->cancelPromoteUser($id);
        return $this->createJsonResponse(true);
    }


}