<?php
namespace My\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;

use Topxia\Service\Quiz\Impl\QuestionSerialize;


class MyQuestionController extends \Home\Controller\BaseController
{
    public function favoriteQuestionAction(Request $request ,$id)
    {
        if ($request->getMethod() == 'POST') {
            $targetType = $request->query->get('targetType');
            $targetId = $request->query->get('targetId');
            $target = $targetType."-".$targetId;

            $user = $this->getCurrentUser();

            $favorite = $this->getQuestionService()->favoriteQuestion($id, $target, $user['id']);
        
            return $this->createJsonResponse(true);
        }
    }

    public function unFavoriteQuestionAction(Request $request ,$id)
    {
        if ($request->getMethod() == 'POST') {
            $targetType = $request->query->get('targetType');
            $targetId = $request->query->get('targetId');
            $target = $targetType."-".$targetId;

            $user = $this->getCurrentUser();

            $this->getQuestionService()->unFavoriteQuestion($id, $target, $user['id']);

            return $this->createJsonResponse(true);
        }
    }

    public function showFavoriteQuestionAction (Request $request)
    {
        $user = $this->getCurrentUser();

        $paginator = new Paginator(
            $request,
            $this->getQuestionService()->findFavoriteQuestionsCountByUserId($user['id']),
            10
        );

        $favoriteQuestions = $this->getQuestionService()->findFavoriteQuestionsByUserId(
            $user['id'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
 
        $questionIds = ArrayToolkit::column($favoriteQuestions, 'questionId');

        $questions = $this->getQuestionService()->findQuestionsByIds($questionIds);

        $myTestpaperIds = array();

        $targets = $this->get('wyzc.target_helper')->getTargets(ArrayToolkit::column($favoriteQuestions, 'target'));

        foreach ($favoriteQuestions as $key => $value) {
            if ($targets[$value['target']]['type'] == 'testpaper'){
                array_push($myTestpaperIds, $targets[$value['target']]['id']);
            }
        }

        $myTestpapers = $this->getTestpaperService()->findTestpapersByIds($myTestpaperIds);
 
        return $this->render('MyQuiz:my-favorite-question', array(
            'favoriteActive' => 'active',
            'user' => $user,
            'favoriteQuestions' => ArrayToolkit::index($favoriteQuestions, 'id'),
            'testpapers' => ArrayToolkit::index($myTestpapers, 'id'),
            'questions' => ArrayToolkit::index($questions, 'id'),
            'targets' => $targets,
            'paginator' => $paginator
        ));
    }

    public function previewAction (Request $request, $id)
    {
        $question = $this->getQuestionService()->getQuestion($id);

        $userId = $this->getCurrentUser()->id;
        
        if (empty($question)) {
            return $this->createMessageResponse('error','题目不存在！');
        }

        $myFavorites = $this->getQuestionService()->findAllFavoriteQuestionsByUserId($userId,0);

        if (!in_array($question['id'], ArrayToolkit::column($myFavorites, 'questionId'))){
            throw $this->createAccessDeniedException('无权预览非本人收藏的题目!');
        }

        $item = array(
            'questionId' => $question['id'],
            'questionType' => $question['type'],
            'question' => $question
        );

        if ($question['type'] == 'material'){
            $questions = $this->getQuestionService()->findQuestionsByParentId($id);

            foreach ($questions as $value) {
                $items[] = array(
                    'questionId' => $value['id'],
                    'questionType' => $value['type'],
                    'question' => $value
                );
            }

            $item['items'] = $items;
        }

        $type = in_array($question['type'], array('single_choice', 'uncertain_choice')) ? 'choice' : $question['type'];
        
        $favoriteInfo = $this->getQuestionService()->findFavoriteQuestionByUserIdAndQesId($userId,$id);
        foreach ($favoriteInfo as $favorite){
            $target = explode('-',$favorite['target']);
            $testId = $target[1];
        }
        $option = array(
            'testId' => $testId //试卷id
        );
        $favoritePaperResult = $this->getTestpaperService()->getTestpaperResultByTestId($testId);
        $myCourseClassTest = $this->getCourseClassTestService()->checkCourseTestShow($option);
        $questionPreview = $myCourseClassTest['show'];

        return $this->render('MyQuiz:question-preview-modal', array(
            'item' => $item,
            'type' => $type,
            'questionPreview' => $questionPreview,
            'favoritePaperResult' => $favoritePaperResult
        ));
    }

    private function getQuestionService ()
    {
        return createService('Question.QuestionService');
    }

    private function getCourseService ()
    {
        return createService('Course.CourseService');
    }

    private function getTestpaperService()
    {
        return createService('Testpaper.TestpaperService');
    }
    
    private function getCourseClassTestService() 
    {
        return createService('Course.CourseClassTestServiceModel');
    }
}