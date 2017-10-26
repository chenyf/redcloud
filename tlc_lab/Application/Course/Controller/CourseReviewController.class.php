<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;
use Common\Form\ReviewType;

class CourseReviewController extends \Home\Controller\BaseController
{

    public function listAction(Request $request, $id)
    {
        $course = $this->getCourseService()->getCourse($id);

        $previewAs = $request->query->get('previewAs');
        $isModal = $request->query->get('isModal');
        
        $reviewSerObj = $this->getReviewService();
        
        $paginator = new Paginator(
            $this->get('request'),
            $reviewSerObj->getCourseReviewCount($id)
            , 10
        );

        $reviews = $reviewSerObj->findCourseReviews(
            $id,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($reviews, 'userId'));

        return $this->render('CourseReview:list', array(
            'course' => $course,
            'reviews' => $reviews,
            'users' => $users,
            'isModal' => $isModal,
            'paginator' => $paginator
        ));
    }

    public function createAction(Request $request, $id)
    {
        $currentUser = $this->getCurrentUser();
        list($course, $member) = $this->getCourseService()->tryTakeCourse($id);
        $review = $this->getReviewService()->getUserCourseReview($currentUser['id'], $course['id']);
        $form = $this->createForm(new ReviewType(), $review ? : array());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
//            if ($form->isValid()) {
            if (1) {
                $fields = $form->getData();
                $fields['rating'] = $fields['rating'];
                $fields['userId']= $currentUser['id'];
                $fields['courseId']= $id;
                $this->getReviewService()->saveReview($fields);
                return $this->createJsonResponse(true);
            }
        }
//        dump($review);die;
        return $this->render('CourseReview:write-modal', array(
            'form' => $form->createView(),
            'course' => $course,
            'review' => $review
        ));
    }

    public function latestBlockAction($course)
    {
        $reviews = $this->getReviewService()->findCourseReviews($course['id'], 0, 10);
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($reviews, 'userId'));
        return $this->render('CourseReview:latest-block', array(
            'course' => $course,
            'reviews' => $reviews,
            'users' => $users,
        ));

    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    private function getReviewService()
    {
        return createService('Course.ReviewService');
    }

}