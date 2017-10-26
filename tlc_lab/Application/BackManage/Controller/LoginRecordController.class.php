<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Lib\ConvertIpToolkit;
use Common\Lib\Paginator;

class LoginRecordController extends BaseController
{
    public function indexAction (Request $request)
    {
        $conditions = $request->query->all();
        $userCondotions = array();
        if (!empty($conditions['keywordType'])) {
            $userCondotions['keywordType'] =$conditions["keywordType"];
        }

        if (!empty($conditions['keyword'])) {
            $userCondotions['keyword'] =$conditions["keyword"];
        }
        $userCondotions['switch'] = true;
        if(isset($userCondotions['keywordType']) && isset($userCondotions['keyword'])){
            $users = $this->getUserService()->searchUsers($userCondotions,0,2000);
            $userIds = ArrayToolkit::column($users, 'id');
            if($userIds){
                $conditions['userIds'] = $userIds;
                unset($conditions['nickname']);
            } else {
                $paginator = new Paginator(
                    $this->get('request'),
                    0,
                    20
                );
                return $this->render('LoginRecord:index', array(
                    'logRecords' => array(),
                    'users' => array(),
                    'paginator' => $paginator
                ));
            }
        }

        $conditions['action'] ='login_success';
        $conditions['switch'] =true;
        
        $paginator = new Paginator(
            $this->get('request'),
            $this->getLogService()->searchLogCount($conditions),
            20
        );

        $logRecords = $this->getLogService()->searchLogs(
            $conditions,
            'created',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $logRecords = ConvertIpToolkit::ConvertIps($logRecords);

        $userIds = ArrayToolkit::column($logRecords, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);
        return $this->render('LoginRecord:index', array(
            'logRecords' => $logRecords,
            'users' => $users,
            'paginator' => $paginator,
            'formData' => $conditions
        ));
    }

    public function showUserLoginRecordAction (Request $request, $id)
    {
        $user = $this->getUserService()->getUser($id);

        $paginator = new Paginator(
            $this->get('request'),
            $this->getLogService()->searchLogCount(array('userId' => $user['id'],'switch'=>true)),
            8
        );

        $loginRecords = $this->getLogService()->searchLogs(
            array('userId' => $user['id'],'switch'=>true),
            'created',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $loginRecords = ConvertIpToolkit::ConvertIps($loginRecords);

        return $this->render('LoginRecord:login-record-details',array(
            'user' => $user,
            'loginRecords' => $loginRecords,
            'loginRecordPaginator' => $paginator
        ));
    }

    protected function getLogService()
    {
        return createService('System.LogService');
    }
}