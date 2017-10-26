<?php

namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Common\Common\ServiceException;

class StatisticController extends BaseController
{

    public function indexAction(Request $request)
    { 
        return $this->render('Statistic:index');
    }

}