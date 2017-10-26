<?php

namespace System\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Topxia\Common\StringToolkit;
use Topxia\Component\Payment\Payment;
use Topxia\WebBundle\Util\AvatarAlert;
use Symfony\Component\HttpFoundation\Response;
use Home\Controller\BaseController;
use Common\Lib\Paginator;

Vendor('Alipay.Corefunction');
Vendor('Alipay.Md5function');
Vendor('Alipay.Rsafunction');
Vendor('Alipay.Notify');
Vendor('Alipay.Submit');
Vendor('Wxpay.WxPayApi');

class CoinController extends \Home\Controller\BaseController {

    public function indexAction(Request $request) {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            return $this->createMessageResponse('error', '用户未登录，请先登录！');
        }
        $account = $this->getCashAccountService()->getAccountByUserId($user->id, true);
        if (empty($account)) {
            $this->getCashAccountService()->createAccount($user->id);
        }
        return $this->render('Coin:index', array(
                    'account' => $account
        ));
    }

    public function cashBillAction(Request $request) {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            return $this->createMessageResponse('error', '用户未登录，请先登录！');
        }
        $conditions = array(
            'userId' => $user['id'],
        );

//  $conditions['cashType'] = 'RMB';
        $conditions['startTime'] = 0;
        $conditions['endTime'] = time();


        switch ($request->get('lastHowManyMonths')) {
            case 'oneWeek':
                $conditions['startTime'] = $conditions['endTime'] - 7 * 24 * 3600;
                break;
            case 'twoWeeks':
                $conditions['startTime'] = $conditions['endTime'] - 14 * 24 * 3600;
                break;
            case 'oneMonth':
                $conditions['startTime'] = $conditions['endTime'] - 30 * 24 * 3600;
                break;
            case 'twoMonths':
                $conditions['startTime'] = $conditions['endTime'] - 60 * 24 * 3600;
                break;
            case 'threeMonths':
                $conditions['startTime'] = $conditions['endTime'] - 90 * 24 * 3600;
                break;
        }
        $paginator = new Paginator(
                $request, $this->getCashOrdersService()->searchOrdersCount($conditions), 20
        );

        $cashes = $this->getCashOrdersService()->searchOrders(
                $conditions, array('ID', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        $this->getCashOrdersService()->closeOrders();
        return $this->render('Coin:cash_bill', array(
                    'cashes' => $cashes,
                    'paginator' => $paginator
        ));
    }

    public function changeAction(Request $request) {
        $user = $this->getCurrentUser();
        $userId = $user->id;

        $change = $this->getCashAccountService()->getChangeByUserId($userId);

        if (empty($change)) {

            $change = $this->getCashAccountService()->addChange($userId);
        }

        $amount = $this->getOrderService()->analysisAmount(array('userId' => $user->id, 'status' => 'paid'));
        $amount+=$this->getCashOrdersService()->analysisAmount(array('userId' => $user->id, 'status' => 'paid'));

        $changeAmount = $amount - $change['amount'];

        list($canUseAmount, $canChange, $data) = $this->caculate($changeAmount, 0, array());

        if ($request->getMethod() == "POST") {

            if ($canChange > 0)
                $this->getCashAccountService()->changeCoin($changeAmount - $canUseAmount, $canChange, $userId);

            return $this->redirect($this->generateUrl('my_coin'));
        }

        return $this->render('Coin:coin-change-modal', array(
                    'amount' => $amount,
                    'changeAmount' => $changeAmount,
                    'canChange' => $canChange,
                    'canUseAmount' => $canUseAmount,
                    'data' => $data,
        ));
    }

    public function showAction(Request $request) {
        $coinSetting = $this->getSettingService()->get('coin', array());

        if (isset($coinSetting['coin_content'])) {
            $content = $coinSetting['coin_content'];
        } else {
            $content = '';
        }

        return $this->render('Coin:coin-content-show', array(
                    'content' => $content,
                    'coinSetting' => $coinSetting
        ));
    }

    private function caculate($amount, $canChange, $data) {
        $coinSetting = $this->getSettingService()->get('coin', array());

        $coinRanges = $coinSetting['coin_consume_range_and_present'];

        if ($coinRanges == array(array(0, 0)))
            return array($amount, $canChange, $data);

        for ($i = 0; $i < count($coinRanges); $i++) {

            $consume = $coinRanges[$i][0];
            $change = $coinRanges[$i][1];

            foreach ($coinRanges as $key => $range) {

                if ($change == $range[1] && $consume > $range[0]) {

                    $consume = $range[0];
                }
            }

            $ranges[] = array($consume, $change);
        }

        $ranges = ArrayToolkit::index($ranges, 1);

        $send = 0;
        $bottomConsume = 0;
        foreach ($ranges as $key => $range) {

            if ($amount >= $range[0] && $send < $range[1]) {
                $send = $range[1];
            }

            if ($bottomConsume > $range[0] || $bottomConsume == 0) {
                $bottomConsume = $range[0];
            }
        }

        if (isset($ranges[$send]) && $amount >= $ranges[$send][0]) {
            $canUseAmount = $amount - $ranges[$send][0];
            $canChange+=$send;
        } else {
            $canUseAmount = $amount;
            $canChange+=$send;
        }

        if ($send > 0) {
            $data[] = array(
                'send' => "消费满{$ranges[$send][0]}元送{$ranges[$send][1]}",
                'sendAmount' => "{$ranges[$send][1]}",);
        }

        if ($canUseAmount >= $bottomConsume) {

            list($canUseAmount, $canChange, $data) = $this->caculate($canUseAmount, $canChange, $data);
        }

        return array($canUseAmount, $canChange, $data);
    }

    public function payAction(Request $request) {
        $formData = $request->request->all();
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            return $this->createMessageResponse('error', '用户未登录，创建订单失败。');
        }
        $formData['payment'] = "none";
        $formData['userId'] = $user['id'];
        $formData['type'] = 0;
        $order = $this->getCashOrdersService()->addOrder($formData);
        if ($order) {
            return $this->redirect(U('System/Coin/payShow', array('id' => $order['id'])));
        }
    }

    public function payShowAction(Request $request, $id) {

        $order = $this->getCashOrdersService()->getOrder($id);
        if ($order["status"] != "created") {
            return $this->redirect(U('System/Coin/index'));
        }
        if (($order["createdTime"] + 48 * 60 * 60) < time()) {
            return $this->createMessageResponse('error', '订单已经过期，不能支付');
        }
        $alipay = $this->alipayUrl(array('out_trade_no' => $order['sn'], 'total_fee' => $order['amount'], 'subject' => $order['title']));
        $wxData = $this->getPayUrl($order);
        if ($wxData['code_url']) {
            $wxData['ewmurl'] = urlencode($wxData['code_url']);
        } else {
            $wxData['ewmurl'] = '';
        }

        return $this->render('Coin:submit-pay-request', array(
                    'order' => $order,
                    'alipay' => $alipay,
                    'wxData' => $wxData
        ));
    }

//===================================
//微信扫码支付 模式二
    public function getPayUrl($order, $openid = '') {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("支付");
        $input->SetOut_trade_no($order['sn'] . rand(100, 999));
        $input->SetTotal_fee($order['amount'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url(C('CALLBACK_URL') . U('System/Coin/payWxCallback'));
        $input->SetTrade_type("NATIVE");
        if ($openid != '') {
            $input->SetOpenid($openId);
        }
        $input->SetProduct_id($order['sn']);

        $wxpay = new \WxPayApi();
        $data = $wxpay->unifiedOrder($input);
        return $data;
    }

//微信支付回调
    public function payWxCallbackAction() {
        $strXml = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($strXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $return_code = strval($postObj->return_code);
        $result_code = strval($postObj->result_code);
        if ($return_code == 'SUCCESS') {
            if ($result_code == 'SUCCESS') {
                $out_trade_no = strval($postObj->out_trade_no);
                $transaction_id = strval($postObj->transaction_id);
                $out_trade_no = substr($out_trade_no, 0, 20);
                $this->getCashOrdersService()->payOrder(array(
                    'sn' => $out_trade_no,
                    'status' => 'success',
                    'tradeNo' => $transaction_id,
                    //'amount' => $total_fee,
                    'payment' => 'wxpay',
                    'webCode' => $webcode,
                    'paidTime' => time()
                ));
                exit('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>');
            } else {
                exit('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FALSE]]></return_msg></xml>');
            }
        } else {
            exit('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FALSE]]></return_msg></xml>');
        }
    }

    public function alipayUrl($param = array()) {
        $alipay_config = C('alipay_config');
        $options = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type" => 1, //支付类型
            "notify_url" => C('CALLBACK_URL'). U('System/Coin/payApiCallback'), //需http://格式的完整路径，不能加?id=123这类自定义参数 服务器异步通知页面路径
            "return_url" => '', //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/ 页面跳转同步通知页面路径
            "out_trade_no" => '', //商户订单号
            "subject" => '瑞德云课', //订单名称
            "total_fee" => 0, //付款金额
            "body" => '', //订单描述
            "show_url" => '', //商品展示地址
            "anti_phishing_key" => '', //防钓鱼时间戳 若要使用请调用类文件submit中的query_timestamp函数
            "exter_invoke_ip" => get_client_ip(), //客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        );
        $options = array_merge($options, $param);
        extract($options);
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        return $alipaySubmit->buildRequestForm($options, "get", "确认");
    }

//回调 
    public function payApiCallbackAction(Request $request) {
        $alipay_config = C('alipay_config');
        if ($_POST['sign_type'] == 'RSA') {
            $alipay_config['sign_type'] = 'RSA';
        }
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //商户订单号    
            $out_trade_no = isset($_POST['out_trade_no']) ? trim($_POST['out_trade_no']) : '';
            //支付宝交易号
            $trade_no = isset($_POST['trade_no']) ? trim($_POST['trade_no']) : '';
            //$total_fee = isset($_POST['total_fee']) ? t($_POST['total_fee']) : '0';
            //交易状态
            $trade_status = isset($_POST['trade_status']) ? trim($_POST['trade_status']) : '';
            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
//$out_trade_no = substr($out_trade_no, 0, 20);
                $this->getCashOrdersService()->payOrder(array(
                    'sn' => $out_trade_no,
                    'status' => 'success',
                    'tradeNo' => $trade_no,
                    //'amount' => $total_fee,
                    'payment' => 'alipay',
                    'paidTime' => time()
                ));
            }
//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";  //请不要修改或删除
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
//验证失败
            echo "fail";

//调试用，写文本函数记录程序运行情况是否正常
//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

//===================================
//我的结算
    public function getTeacherCashAction(Request $request) {
        $user = $this->getCurrentUser();
        if (!$user->isTeacher()) {
            return $this->createMessageResponse('error', '您不是老师，不能查看此页面！');
        }
        $conditions = $request->query->all();
        $conditions['userId'] = $user['id'];
        $cashId = $this->getCashTeacherService()->searchCashTeacherId($conditions);
        $map['status'] = 1;
        $map['type'] = $conditions['type'];
        if ($cashId) {
            $map['id'] = array('not in', $cashId);
        }
        $orders = $this->getSchoolOrdersService()->searchSchoolOrders(
                $map, 'latest', 0, 1000
        );

        $paginator = new Paginator(
                $request, $this->getCashTeacherService()->searchCashTeacherCount($conditions), 20
        );

        $cashes = $this->getCashTeacherService()->searchCashTeacher(
                $conditions, 'latest', $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        return $this->render('Coin:cash-order', array(
                    'request' => $request,
                    'orders' => $orders,
                    'cashes' => $cashes
//    'paginator' => $paginator,
        ));
    }

    public function applyAction(Request $request, $id) {
        $user = $this->getCurrentUser();
        if (!$user->isTeacher()) {
            return $this->createMessageResponse('error', '您不是老师，不能查看此页面！');
        }

        $info = $this->getSchoolOrdersService()->getSchoolOrdersInfo($id);

        $db = $info['type'] == 1 ? 'center' : C('WEBSITE_CODE');

        $courseId = $this->getCourseService()->getUserCourseId($user['id']);
        $conditions['a.cashId'] = $id;
        $conditions['b.targetId'] = array('in', $courseId);
        if ($_POST) {
            try {
                $restult = $this->getOrderCashService()->doTeacherOrder($conditions, $info['type']);
                $this->success('操作成功');
            } catch (\RuntimeException $e) {
                $this->error('操作失败:' . $e->getMessage());
            }
        }
        $orders = $this->getOrderCashService()->getTeacherOrder($conditions);
        $this->render('Coin:status', array(
            'orders' => $orders,
            'info' => $info
        ));
    }

    public function applyInfoAction(Request $request, $id) {
        $user = $this->getCurrentUser();
        if (!$user->isTeacher()) {
            return $this->createMessageResponse('error', '您不是老师，不能查看此页面！');
        }
        $info = $this->getSchoolOrdersService()->getSchoolOrdersInfo($id);
        $cashinfo = $this->getCashTeacherService()->getCashCashIdInfo($id);
        $db = $info['type'] == 1 ? 'center' : C('WEBSITE_CODE');

        $courseId = $this->getCourseService()->getUserCourseId($user['id']);
        $conditions['a.cashId'] = $id;
        $conditions['b.targetId'] = array('in', $courseId);
        $orders = $this->getOrderCashService()->getTeacherOrder($conditions);
        $this->render('Coin:apply_info', array(
            'orders' => $orders,
            'info' => $info,
            'cashinfo' => $cashinfo
        ));
    }

    public function cashInfoAction(Request $request, $id) {
        $user = $this->getCurrentUser();
        if (!$user->isTeacher()) {
            return $this->createMessageResponse('error', '您不是老师，不能查看此页面！');
        }
        $cashinfo = $this->getCashTeacherService()->getCashTeacherInfo($id);

        $info = $this->getSchoolOrdersService()->getSchoolOrdersInfo($cashinfo['cashId']);

        $db = $info['type'] == 1 ? 'center' : C('WEBSITE_CODE');

        $courseId = $this->getCourseService()->getUserCourseId($user['id']);
        $conditions['a.cashId'] = $cashinfo['cashId'];
        $conditions['b.targetId'] = array('in', $courseId);
        $orders = $this->getOrderCashService()->getTeacherOrder($conditions);
        $this->render('Coin:cash_info', array(
            'orders' => $orders,
            'info' => $cashinfo,
            'type' => $info['type']
        ));
    }

    public function getOrderStatusAction(Request $request, $id) {
        $order = $this->getCashOrdersService()->getOrder($id);
        if ($order["status"] == "paid") {
            return $this->createJsonResponse(true);
        } else {
            return $this->createJsonResponse(false);
        }
    }

    public function cancelAction(Request $request, $id) {
        $this->getCashOrdersService()->cancelOrder($id);
        return $this->createJsonResponse(true);
    }

    public function submitPayRequestAction(Request $request, $order, $requestParams) {
        $paymentRequest = $this->createPaymentRequest($order, $requestParams);

        return $this->render('Coin:submit-pay-request', array(
                    'form' => $paymentRequest->form(),
                    'order' => $order,
        ));
    }

    private function createPaymentRequest($order, $requestParams) {
        $options = $this->getPaymentOptions($order['payment']);
        $request = Payment::createRequest($order['payment'], $options);

        $requestParams = array_merge($requestParams, array(
            'orderSn' => $order['sn'],
            'title' => $order['title'],
            'summary' => '',
            'amount' => $order['amount'],
        ));
        return $request->setParams($requestParams);
    }

    public function payReturnAction(Request $request, $name) {
        $this->getLogService()->info('order', 'pay_result', "{$name}页面跳转支付通知", $request->query->all());
        $response = $this->createPaymentResponse($name, $request->query->all());

        $payData = $response->getPayData();

        if ($payData['status'] == "waitBuyerConfirmGoods") {
            return $this->forward("Home:Coin:resultNotice");
        }

        list($success, $order) = $this->getCashOrdersService()->payOrder($payData);

        if ($order['status'] == 'paid' and $success) {
            $successUrl = $this->generateUrl('my_coin', array(), true);
        }

        $goto = empty($successUrl) ? $this->generateUrl('homepage', array(), true) : $successUrl;
        return $this->redirect($goto);
    }

    public function resultNoticeAction(Request $request) {
        return $this->render('Coin:retrun-notice');
    }

    public function payNotifyAction(Request $request, $name) {
        $this->getLogService()->info('order', 'pay_result', "{$name}服务器端支付通知", $request->request->all());
        $response = $this->createPaymentResponse($name, $request->request->all());

        $payData = $response->getPayData();
        try {
            list($success, $order) = $this->getCashOrdersService()->payOrder($payData);

            return new Response('success');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function createPaymentResponse($name, $params) {
        $options = $this->getPaymentOptions($name);
        $response = Payment::createResponse($name, $options);

        return $response->setParams($params);
    }

    private function getPaymentOptions($payment) {
        $settings = $this->setting('payment');

        if (empty($settings)) {
            throw new \RuntimeException('支付参数尚未配置，请先配置。');
        }

        if (empty($settings['enabled'])) {
            throw new \RuntimeException("支付模块未开启，请先开启。");
        }

        if (empty($settings[$payment . '_enabled'])) {
            throw new \RuntimeException("支付模块({$payment})未开启，请先开启。");
        }

        if (empty($settings["{$payment}_key"]) or empty($settings["{$payment}_secret"])) {
            throw new \RuntimeException("支付模块({$payment})参数未设置，请先设置。");
        }

        $options = array(
            'key' => $settings["{$payment}_key"],
            'secret' => $settings["{$payment}_secret"],
            'type' => $settings["{$payment}_type"]
        );

        return $options;
    }

    private function getEnabledPayments() {
        $enableds = array();

        $setting = $this->setting('payment', array());

        if (empty($setting['enabled'])) {
            return $enableds;
        }

        $payNames = array('alipay');
        foreach ($payNames as $payName) {
            if (!empty($setting[$payName . '_enabled'])) {
                $enableds[$payName] = array(
                    'type' => empty($setting[$payName . '_type']) ? '' : $setting[$payName . '_type'],
                );
            }
        }

        return $enableds;
    }

    protected function getCashService() {

        return createService('Cash.CashService');
    }

    protected function getOrderCashService() {
        return createService('Order.OrderCashService');
    }

    protected function getCashTeacherService() {
        return createService('Cash.CashTeacherService');
    }

    protected function getCashAccountService() {
        return createService('Cash.CashAccountService');
    }

    protected function getCashOrdersService() {

        return createService('Cash.CashOrdersService');
    }

    protected function getOrderService() {
        return createService('Order.OrderService');
    }

    protected function getSettingService() {

        return createService('System.SettingService');
    }

    protected function getSchoolOrdersService() {
        return createService('Center.SchoolOrdersServiceModel');
    }

    protected function getCourseService() {
        return createService('Course.CourseServiceModel');
    }

    protected function getAppService() {
        return createService('CloudPlatform.AppService');
    }

}
