<?php

namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class CashAccountServiceModel extends BaseModel {

    public function createAccount($userId) {
        $fields = array('userId' => $userId, 'cash' => 0);
        return $this->getAccountDao()->addAccount($fields);
    }

    public function getAccountByUserId($userId, $lock = false) {
        return $this->getAccountDao()->getAccountByUserId($userId);
    }

    public function getAccountByOrgId($orgId) {
        return $this->getAccountDao()->getAccountByOrgId($orgId);
    }

    public function getAccount($id) {
        return $this->getAccountDao()->getAccount($id);
    }

    public function getChangeByUserId($userId) {
        return $this->getChangeDao()->getChangeByUserId($userId);
    }

    public function addChange($userId) {
        $fields = array(
            'userId' => $userId,
            'amount' => 0,
        );

        return $this->getChangeDao()->addChange($fields);
    }

    public function changeCoin($amount, $coinAmount, $userId) {
        $coinSetting = $this->getSettingService()->get('coin', array());

        try {

            $this->getAccountDao()->getConnection()->beginTransaction();

            $account = $this->getAccountDao()->getAccountByUserId($userId, true);
            if (empty($account)) {
                E("Account #{$userId} is not exist.");
            }

            $inflow = array();
            $inflow = array(
                'userId' => $userId,
                'sn' => $this->makeSn(),
                'type' => 'inflow',
                'amount' => $coinAmount,
                'name' => "兑换" . $coinAmount . $coinSetting['coin_name'],
                'category' => "exchange",
                'orderSn' => 'E' . $this->makeSn(),
                'createdTime' => time(),
            );

            $inflow = $this->getFlowDao()->addFlow($inflow);

            $this->getAccountDao()->waveCashField($account['id'], $coinAmount);

            $change = $this->getChangeDao()->getChangeByUserId($userId, true);

            $this->getChangeDao()->waveCashField($change['id'], $amount);

            $this->getNotificationService()->notify($userId, 'default', "您已成功兑换" . $coinAmount . $coinSetting['coin_name'] . ",前往 <a href='/my/coin'>我的账户</a> 查看");

            $this->getAccountDao()->getConnection()->commit();

            return $inflow;
        } catch (\Exception $e) {
            $this->getAccountDao()->getConnection()->rollback();

            E($e);
        }
    }

    public function searchAccount($conditions, $orderBy, $start, $limit) {
        return $this->getAccountDao()->searchAccount($conditions, $orderBy, $start, $limit);
    }

    public function searchAccountCount($conditions) {
        return $this->getAccountDao()->searchAccountCount($conditions);
    }

    public function waveCashField($id, $value) {
        if (!is_numeric($value)) {
            E('充值金额必须为整数!');
        }
        $coinSetting = $this->getSettingService()->get('coin', array());
        $coinSetting['coin_name'] = isset($coinSetting['coin_name']) ? $coinSetting['coin_name'] : "虚拟币";

        $account = $this->getAccount($id);
        $this->getNotificationService()->notify($account['userId'], 'default', "您已成功充值" . $value . ",前往 <a href='/my/coin'>我的账户</a> 查看");

        return $this->getAccountDao()->waveCashField($id, $value);
    }

    public function waveDownCashField($id, $value) {
        if (!is_numeric($value)) {
            E('充值金额必须为整数!');
        }

        $coinSetting = $this->getSettingService()->get('coin', array());
        $coinSetting['coin_name'] = isset($coinSetting['coin_name']) ? $coinSetting['coin_name'] : "虚拟币";
        $account = $this->getAccountDao()->getAccount($id);
        $this->getNotificationService()->notify($account['userId'], 'default', "您被扣除" . $value . $coinSetting['coin_name'] . ",前往 <a href='/my/coin'>我的账户</a> 查看");

        return $this->getAccountDao()->waveDownCashField($id, $value);
    }

    public function reward($amount, $name, $userId, $type = null) {
        $coinSetting = $this->getSettingService()->get('coin', array());

        try {

            $this->getAccountDao()->getConnection()->beginTransaction();

            $account = $this->getAccountDao()->getAccountByUserId($userId, true);

            if (empty($account)) {
                $this->createAccount($userId);
            }

            $inflow = array();

            if ($type == "cut") {

                $inflow = array(
                    'userId' => $userId,
                    'sn' => $this->makeSn(),
                    'type' => 'outflow',
                    'amount' => $amount,
                    'name' => $name,
                    'category' => "exchange",
                    'orderSn' => 'R' . $this->makeSn(),
                    'createdTime' => time(),
                );

                $inflow = $this->getFlowDao()->addFlow($inflow);

                $this->getAccountDao()->waveDownCashField($account['id'], $amount);
            } else {

                $inflow = array(
                    'userId' => $userId,
                    'sn' => $this->makeSn(),
                    'type' => 'inflow',
                    'amount' => $amount,
                    'name' => $name,
                    'category' => "exchange",
                    'orderSn' => 'R' . $this->makeSn(),
                    'createdTime' => time(),
                );

                $inflow = $this->getFlowDao()->addFlow($inflow);

                $this->getAccountDao()->waveCashField($account['id'], $amount);
            }

            $this->getAccountDao()->getConnection()->commit();

            return $inflow;
        } catch (\Exception $e) {
            $this->getAccountDao()->getConnection()->rollback();

            E($e);
        }
    }

    //郭俊强 更新余额
    public function updCashAccount($amount, $userId, $type) {

        $account = $this->getAccountDao()->getAccountByUserId($userId, true);
        if (empty($account)) {
            $account['id'] = $this->createAccount($userId);
            if ($type == 2) {
                $data['iosCash'] = $amount;
            } else {
                $data['cash'] = $amount;
            }
        } else {
            if ($type == 2) {
                $data['iosCash'] = $amount + $account['iosCash'];
            } else {
                $data['cash'] = $amount + $account['cash'];
            }
        }
        return $this->getAccountDao()->updateAccount($account['id'], $data);
    }

    //合并数据
    public function updCashAccountUser($uid, $appUid) {
        $appUser= $this->getAccountDao()->getAccountByUserId($appUid, true);
        $account = $this->getAccountDao()->getAccountByUserId($uid, true);
        if (empty($account)) {
            $account['id'] = $this->createAccount($userId);
            $data['iosCash'] = $appUser['iosCash'];
        } else {
           $data['iosCash'] = $account['iosCash'] + $appUser['iosCash'];
        }
        $this->getAccountDao()->updateAccount($appUser['id'], array('iosCash'=>0));
        return $this->getAccountDao()->updateAccount($account['id'], $data);
    }

    public function setDesCash($userId, $amount) {
        return $this->getAccountDao()->setDesCash($userId, $amount);
    }
    public function setDesCashbyOrg($orgId, $amount) {
        return $this->getAccountDao()->setDesCash($orgId, $amount);
    }
    public function setIncCash($userId, $amount) {
        return $this->getAccountDao()->setIncCash($userId, $amount);
    }
    public function setDesIosCash($userId, $amount) {
        return $this->getAccountDao()->setDesIosCash($userId, $amount);
    }
    public function setIncIosCash($userId, $amount) {
        return $this->getAccountDao()->setIncIosCash($userId, $amount);
    }
    protected function getSettingService() {
        return $this->createService('System.SettingServiceModel');
    }

    private function makeSn() {
        return date('YmdHis') . rand(10000, 99999);
    }

    protected function getNotificationService() {
        return $this->createService('User.NotificationServiceModel');
    }

    protected function getAccountDao() {
        return $this->createDao('Cash.CashAccountModel');
    }

    protected function getChangeDao() {
        return $this->createDao('Cash.CashChangeModel');
    }

    protected function getFlowDao() {
        return $this->createDao('Cash.CashFlowModel');
    }

}