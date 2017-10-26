<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Model\Util\CloudClientFactory;
class CloudController extends BaseController
{
    public function billAction(Request $request)
    {

        $factory = new CloudClientFactory();
        $client = $factory->createClient();
      
        $result = $client->getBills($client->getBucket());
     
        if (!empty($result['error'])) {
            return $this->createMessageResponse('error', '获取账单信息失败，云视频参数配置不正确，或网络通讯失败。',  '获取账单信息失败');
        }


        return $this->render('Cloud:bill', array(
            'money' => $result['money'],
            'bills' => $result['bills'],
        ));

    }

    public function rechargeAction(Request $request)
    {
        $loginToken = $this->getAppService()->getLoginToken();
        return $this->redirect('http://open.redcloud.com/token_login?token='.$loginToken["token"].'&goto=order_recharge');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }
}