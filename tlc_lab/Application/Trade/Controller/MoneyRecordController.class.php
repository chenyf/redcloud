<?php
namespace Trade\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class MoneyRecordController extends \Home\Controller\BaseController
{

    public function indexAction(Request $request)
    {    
        $user = $this->getCurrentUser();

        $conditions = array(
            'userId' => $user['id'],
            'type' =>'income',
            'status' => 'finished'
        );

        $paginator = new Paginator(
            $request,
            $this->getTradeService()->searchMoneyRecordsCount($conditions),
            15
        );
        $incomeRecords = $this->getTradeService()->searchMoneyRecords(
            $conditions,
            'latest',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return $this->render('MoneyRecord:index', array(
            'incomeRecords' => $incomeRecords,
            'paginator' => $paginator
        ));
    }

    public function payoutAction(Request $request)
    {   
        $user = $this->getCurrentUser();

        $conditions = array(
            'userId' => $user['id'],
            'type' =>'payout',
            'status' => 'finished'
        );

        $paginator = new Paginator(
            $request,
            $this->getTradeService()->searchMoneyRecordsCount($conditions),
            15
        );

        $payoutRecords = $this->getTradeService()->searchMoneyRecords(
            $conditions,
            'latest',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        return $this->render('MoneyRecord:payout', array(
            'payoutRecords' => $payoutRecords,
            'paginator' => $paginator
        ));
    }

    private function getTradeService()
    {
        return createService('Order.TradeService');
    }
}