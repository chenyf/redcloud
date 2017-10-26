<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;

class JournalController extends BaseController {

    public function indexAction(Request $request)
    {
        $fields = $request->query->all();
        $conditions = array(
            'startDateTime'=>'',
            'endDateTime'=>'',
            'nickname'=>'',
            'level'=>''
        );

        if(!empty($fields)){
            $conditions =$fields;
        }

        $paginator = new Paginator(
            $this->get('request'),
            $this->getLogService()->searchLogCount($conditions),
            30
        );

        $logs = $this->getLogService()->searchLogs(
            $conditions, 
            'created', 
            $paginator->getOffsetCount(), 
            $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($logs, 'userId'));

        return $this->render('System:logs', array(
            'logs' => $logs,
            'paginator' => $paginator,
            'users' => $users
        ));

    }

    protected function getLogService()
    {
        return createService('System.LogService');        
    }

}