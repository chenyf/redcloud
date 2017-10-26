<?php

namespace Trade\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Endroid\QrCode\QrCode;

Vendor('Alipay.Corefunction');
Vendor('Alipay.Md5function');
Vendor('Alipay.Notify');
Vendor('Alipay.Submit');
Vendor('Wxpay.WxPayApi');

class PayCenterController extends \Home\Controller\BaseController {

    public function showAction(Request $request) {
        $user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            return $this->createMessageResponse('error', '用户未登录，不能支付。','',0,'/My/MyOrder/indexAction');
        }

        $paymentSetting = $this->setting("payment");
        if (!isset($paymentSetting["enabled"]) || $paymentSetting["enabled"] == 0) {
            return $this->createMessageResponse('error', '支付中心未开启。','',0,'/My/MyOrder/indexAction');
        }

        $fields = $request->query->all();
        $order = $this->getOrderService()->getOrderBySn($fields["sn"]);

        if (empty($order)) {
            return $this->createMessageResponse('error', '订单不存在!','',0,'/My/MyOrder/indexAction');
        }

        if ($order["userId"] != $user["id"]) {
            return $this->createMessageResponse('error', '不是您的订单，不能支付','',0,'/My/MyOrder/indexAction');
        }

        if ($order["status"] != "created") {
            // Course/Course/showAction/id/142
            return $this->redirect($this->generateUrl('course_show', array(
                                'id' => $order['targetId'],
                                'center' => (CENTER == 'center') ? 1 : 0
            )));
            //   return $this->createMessageResponse('error', '订单状态被更改，不能支付');
        }
        if (($order["createdTime"] + 48 * 60 * 60) < time()) {
            return $this->createMessageResponse('error', '订单已经过期，不能支付','',0,'/My/MyOrder/indexAction');
        }

        if ($order["amount"] == 0 && $order["coinAmount"] == 0) {
            $payData = array(
                'sn' => $order['sn'],
                'status' => 'success',
                'amount' => $order['amount'],
                'paidTime' => time()
            );
            $this->getPayCenterService()->processOrder($payData);
            return $this->redirect($this->generateUrl('course_show', array('id' => $order['targetId'])));
        } else if ($order["amount"] == 0 && $order["coinAmount"] > 0) {
            $payData = array(
                'sn' => $order['sn'],
                'status' => 'success',
                'amount' => $order['amount'],
                'paidTime' => time()
            );
            list($success, $order) = $this->getPayCenterService()->pay($payData);
            $goto = $success && !empty($router) ? $this->generateUrl('course_show', array('id' => $order["targetId"]), true) : $this->generateUrl('homepage', array(), true);

            return $this->redirect($goto);
        }
        $order['title']=str_replace("购买课程",getWebNameByWebcode(C('WEBSITE_CODE')),$order['title']);
        $alipay = $this->alipayUrl(array('out_trade_no' => $order['sn'], 'total_fee' => $order['amount'], 'subject' => $order['title']));
        $wxData = $this->getPayUrl($order);
        //$wxData = $this->getPayUrlOne($order);
        //$strurl=$this->getPayUrlOne($order);
       // error_log(print_r($strurl,true));
        //$wxData = wxPayOrder(array('out_trade_no' => $order['sn'] . rand(100, 999), 'total_fee' => $order['amount'] * 100, 'body' => $order['title']));
        if ($wxData['code_url']) {
            $wxData['ewmurl'] = urlencode($wxData['code_url']);
        } else {
            $wxData['ewmurl'] = '';
        }
        $account = $this->getCashAccountService()->switchDB('center')->getAccountByUserId($user->id, true);
        if (empty($account)) {
            $this->getCashAccountService()->switchDB('center')->createAccount($user->id);
            $account['cash'] = 0;
        }
        
        return $this->render('PayCenter:show', array(
                    'order' => $order,
                    'payments' => $this->getEnabledPayments(),
                    'alipay' => $alipay,
                    'wxData' => $wxData,
                    'account' => $account
        ));
    }

    public function getOrderStatusAction(Request $request, $sn) {
        $order = $this->getOrderService()->getOrderBySn($sn);
        if ($order["status"] == "paid") {
            return $this->createJsonResponse(true);
        } else {
            return $this->createJsonResponse(false);
        }
    }

    /*     * ******************支付宝支付start*************************************** */

    public function alipayUrl($param = array()) {
        $alipay_config = C('alipay_config');
        if ($_POST['sign_type'] == 'RSA') {
            $alipay_config['sign_type'] = 'RSA';
        }
        $options = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type" => 1, //支付类型
            "notify_url" => C('CALLBACK_URL'). U('Trade/PayCenter/payApiCallback'), //需http://格式的完整路径，不能加?id=123这类自定义参数 服务器异步通知页面路径
            "return_url" => '', //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/ 页面跳转同步通知页面路径
            "out_trade_no" => '', //商户订单号
            "subject" => '我赢职场商品', //订单名称
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

    //支付宝退款
    public function notifyAction() {
        $alipay_config = C('alipay_config');
        $parameter = array(
            "service" => "refund_fastpay_by_platform_pwd",
            "partner" => trim($alipay_config['partner']),
            "notify_url" =>C('CALLBACK_URL') . U('Trade/PayCenter/payApiCallback'),
            "seller_email" => trim($alipay_config['seller_email']),
            "refund_date" => date("Y-m-d H:i:s"),
            "batch_no" => date('Ymd') . '0001',
            "batch_num" => "1",
            "detail_data" => "2015102021001004890065470514^0.01^测试退款",
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        );
        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        echo $alipaySubmit->buildRequestForm($parameter, "get", "确认");
    }

    //回调 
    public function payApiCallbackAction(Request $request) {
        $alipay_config = C('alipay_config');
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
                $webcode = substr($out_trade_no, 20);
                //$out_trade_no = substr($out_trade_no, 0, 20);
                $this->getOrderService()->switchDB($webcode)->payOrder(array(
                    'sn' => $out_trade_no,
                    'status' => 'success',
                    'tradeNo' => $trade_no,
                    //'amount' => $total_fee,
                    'payment' => 'alipay',
                    'webCode' => $webcode,
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

    /*     * ******************支付宝支付end*************************************** */
    /*     * ******************微信支付start*************************************** */

    //微信扫码支付 模式一
    public function getPayUrlOne($order) {
        $biz = new \WxPayBizPayUrl();
        $biz->SetProduct_id($order['sn']);
        $wxpay = new \WxPayApi();
        $values = $wxpay->bizpayurl($biz);
        $wxData['code_url'] = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
        return $wxData;
    }

    /**
     * 
     * 参数数组转换为url参数
     * @param array $urlObj
     */
    private function ToUrlParams($urlObj) {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    //微信扫码支付 模式二
    public function getPayUrl($order, $openid = '') {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['title']);
        $input->SetOut_trade_no($order['sn'] . rand(100, 999));
        $input->SetTotal_fee($order['amount'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url(C('CALLBACK_URL') . U('Trade/PayCenter/payWxCallback'));
        $input->SetTrade_type("NATIVE");
        if ($openid != '') {
            $input->SetOpenid($openId);
        }
        $input->SetProduct_id($order['sn']);

        $wxpay = new \WxPayApi();
        $data = $wxpay->unifiedOrder($input);
        return $data;
    }

    //
    public function orderCallbackAction() {
        $strXml = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($strXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $openid = strval($postObj->openid);
        $product_id = strval($postObj->product_id);
        $webcode = substr($product_id, 20);
        $order = $this->getOrderService()->switchDB($webcode)->getOrderBySn($product_id);
        $data = $this->getPayUrl($order, $openid);
        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml.="<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml.="<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml.="</xml>";
        echo $xml;
    }

    public function getPayWxImgAction(Request $request, $url) {
        $qrCode = new QrCode();
        $qrCode->setText(urldecode($url));
        $qrCode->setSize(150);
        $qrCode->setPadding(10);
        $img = $qrCode->get('png');
        $headers = array('Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="image.png"');
        $rsp = new Response($img, 200, $headers);
        $rsp->send();
    }

    //微信退款
    public function wxRefundAction() {
        $input = new \WxPayRefund();
        $input->SetTransaction_id('1008920357201510161220076165');
        $input->SetTotal_fee(1);
        $input->SetRefund_fee(1);
        $input->SetOut_refund_no(date("YmdHis"));
        $input->SetOp_user_id('1227841102');
        $wxpay = new \WxPayApi();
        $data = $wxpay->refund($input);
        exit();
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
                $str = substr($out_trade_no, -3);
                $out_trade_no = str_replace($str, '', $out_trade_no);
                $webcode = substr($out_trade_no, 20);
                $this->getOrderService()->switchDB($webcode)->payOrder(array(
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

    /*     * ******************微信支付end*************************************** */

    //余额支付回调
    public function cashCallbackAction(Request $request){
        $user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            return $this->createMessageResponse('error', '用户未登录，不能支付。');
        }
        $fields = $request->query->all();
        $order = $this->getOrderService()->getOrderBySn($fields["sn"]);
        if (empty($order)) {
            return $this->createMessageResponse('error', '订单不存在!');
        }
        if ($order["userId"] != $user["id"]) {
            return $this->createMessageResponse('error', '不是您的订单，不能支付');
        }
        if ($order["status"] != "created") {
            return $this->redirect($this->generateUrl('course_show', array(
                                'id' => $order['targetId'],
                                'center' => (CENTER == 'center') ? 1 : 0
            )));

        }
        $account = $this->getCashAccountService()->switchDB('center')->getAccountByUserId($user->id, true);
        if (empty($account)) {
            $this->getCashAccountService()->switchDB('center')->createAccount($user->id);
            $account['cash'] = 0;
        }
        if($account['cash'] >= $order['amount']){
            $webcode = substr($order['sn'], 20);
            $this->getOrderService()->payOrder(array(
                'sn' => $order['sn'],
                'status' => 'success',
                'tradeNo' => '0',
                //'amount' => $total_fee,
                'payment' => 'coin',
                'webCode' => $webcode,
                'paidTime' => time()
            ));
             $this->getCashAccountService()->switchDB('center')->setDesCash($user->id,$order['amount']);
             return $this->redirect($this->generateUrl('course_show', array(
                                'id' => $order['targetId'],
                                'center' => (CENTER == 'center') ? 1 : 0
            )));
        }else{
            return $this->createMessageResponse('error', '余额不足!');
        }
        
        
    }
    
    
    //end回调
    public function blankHelpAction(Request $request) {
        return $this->render('PayCenter:bank-help');
    }

    public function payAction(Request $request) {
        $fields = $request->request->all();
        $user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            return $this->createMessageResponse('error', '用户未登录，支付失败。');
        }

        if (!array_key_exists("orderId", $fields)) {
            return $this->createMessageResponse('error', '缺少订单，支付失败');
        }

        $order = $this->getOrderService()->getOrder($fields["orderId"]);

        if ($user["id"] != $order["userId"]) {
            return $this->createMessageResponse('error', '不是您创建的订单，支付失败');
        }

        if ($order['status'] == 'paid') {
            $processor = OrderProcessorFactory::create($order["targetType"]);
            $router = $processor->getRouter();

            return $this->redirect($this->generateUrl($router, array('id' => $order['targetId'])));
        } else {

            $payRequestParams = array(
                'returnUrl' => $this->generateUrl('pay_return', array('name' => $order['payment']), true),
                'notifyUrl' => $this->generateUrl('pay_notify', array('name' => $order['payment']), true),
                'showUrl' => $this->generateUrl('pay_success_show', array('id' => $order['id']), true),
            );

            return $this->forward('Home:PayCenter:submitPayRequest', array(
                        'order' => $order,
                        'requestParams' => $payRequestParams,
            ));
        }
    }

    public function payReturnAction(Request $request, $name, $successCallback = null) {
        $this->getLogService()->info('order', 'pay_result', "{$name}页面跳转支付通知", $request->query->all());
        $response = $this->createPaymentResponse($name, $request->query->all());

        $payData = $response->getPayData();

        if ($payData['status'] == "waitBuyerConfirmGoods") {
            return $this->forward("Home:PayCenter:resultNotice");
        }

        list($success, $order) = $this->getPayCenterService()->pay($payData);

        if (!$success) {
            return $this->redirect("pay_error");
        }

        $processor = OrderProcessorFactory::create($order["targetType"]);
        $router = $processor->getRouter();

        $goto = !empty($router) ? $this->generateUrl($router, array('id' => $order["targetId"]), true) : $this->generateUrl('homepage', array(), true);

        return $this->redirect($goto);
    }

    public function payErrorAction(Request $request) {
        return $this->createMessageResponse('error', '由于余额不足，支付失败，订单已被取消。');
    }

    public function payNotifyAction(Request $request, $name) {
        $this->getLogService()->info('order', 'pay_result', "{$name}服务器端支付通知", $request->request->all());
        $response = $this->createPaymentResponse($name, $request->request->all());

        $payData = $response->getPayData();
        if ($payData['status'] == "waitBuyerConfirmGoods") {
            return new Response('success');
        }

        if ($payData['status'] == "success") {
            list($success, $order) = $this->getPayCenterService()->pay($payData);
            $processor = OrderProcessorFactory::create($order["targetType"]);

            if ($success) {
                return new Response('success');
            }
        }

        return new Response('failture');
    }

    public function showTargetAction(Request $request) {
        $orderId = $request->query->get("id");
        $order = $this->getOrderService()->getOrder($orderId);

        $processor = OrderProcessorFactory::create($order["targetType"]);
        $router = $processor->getRouter();
        return $this->redirect($this->generateUrl($router, array('id' => $order['targetId'])));
    }

    public function payPasswordCheckAction(Request $request) {
        $password = $request->query->get('value');

        $user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            $response = array('success' => false, 'message' => '用户未登录');
        }

        $isRight = $this->getAuthService()->checkPayPassword($user["id"], $password);
        if (!$isRight) {
            $response = array('success' => false, 'message' => '支付密码不正确');
        } else {
            $response = array('success' => true, 'message' => '支付密码正确');
        }

        return $this->createJsonResponse($response);
    }

    public function submitPayRequestAction(Request $request, $order, $requestParams) {
        $paymentRequest = $this->createPaymentRequest($order, $requestParams);

        return $this->render('PayCenter:submit-pay-request', array(
                    'form' => $paymentRequest->form(),
                    'order' => $order,
        ));
    }

    public function resultNoticeAction(Request $request) {
        return $this->render('PayCenter:resultNotice');
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

    private function createPaymentResponse($name, $params) {
        $options = $this->getPaymentOptions($name);
        $response = Payment::createResponse($name, $options);

        return $response->setParams($params);
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

    protected function getAuthService() {
        return createService('User.AuthService');
    }

    protected function getOrderService() {
        return createService('Order.OrderService');
    }

    protected function getPayCenterService() {
        return createService('PayCenter.PayCenterService');
    }
    protected function getCashAccountService() {
        return createService('Cash.CashAccountService');
    }

}