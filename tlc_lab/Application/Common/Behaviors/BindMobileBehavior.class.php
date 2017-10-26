<?php

namespace Common\Behaviors;

use Think\Behavior;
use Think\Hook;

class BindMobileBehavior extends Behavior {

    public function run(&$param) {
       $isBind = $this->BindMobile()->isBind($param['uid']);
       if ($isBind['bindStatus'] != 1) {
            $this->BindMobile()->updateReg($param['uid']);
            $arr = $this->getUidByCode()->ThirdId($param['uid']);
            $res = $this->getUidByCode()->ThirdId($arr['pUid']);
             $data['bindMobileNum'] = $res['bindMobileNum'] + 1;
            $this->getUidByCode()->upRegNum($res['generalizeCode'], $data);
        }
        
      
      
        
    }

    private function getUidByCode() {
        return createService('Generalize.GeneralizeUserService');
    }

    private function BindMobile() {
        return createService('Generalize.GeneralizeRegService');
    }

}