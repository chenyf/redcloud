<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class CouponController extends BaseController 
{    

    public function indexAction (Request $request)
    {   
        $conditions = $request->query->all();

        $paginator = new Paginator(
            $request,
            $this->getCouponService()->searchCouponsCount($conditions),
            20
        );

        $coupons = $this->getCouponService()->searchCoupons(
            $conditions,
            array('createdTime', 'DESC'),
            $paginator->getOffsetCount(),  
            $paginator->getPerPageCount()
        );
        $batchs = $this->getCouponService()->findBatchsbyIds(ArrayToolkit::column($coupons, 'batchId'));
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($coupons, 'userId'));
        $courses = $this->getCourseService()->findCoursesByIds(ArrayToolkit::column($coupons, 'targetId'));

        return $this->render('Coupon:query', array(
            'coupons' => $coupons,
            'paginator' => $paginator,
            'batchs' => $batchs,
            'users' => $users,
            'courses' =>$courses  
        ));
    }

    private function getCouponService()
    {
        return createService('Coupon.CouponService');
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    private function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
    }

}