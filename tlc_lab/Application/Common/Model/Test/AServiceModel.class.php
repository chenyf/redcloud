<?php
namespace Common\Model\Test;
use \Common\Model\Common\BaseModel;

class AServiceModel extends BaseModel
{
    
    public function out(){
        $this->getB1Dao()->out();
        $this->getA1Dao()->out();
        $this->getA1Dao()->out2();
        $this->getB1Dao()->out2();
        $this->getBService()->out();
    }
    
    protected function getA1Dao ()
    {
     
        return $this->createDao('Test.A1');
    }
    
    protected function getB1Dao ()
    {
     
        return $this->createDao('Test.B1');
    }
    
    protected function getAService()
    {
        return $this->createService('Test.AService');
    }
    
    protected function getBService()
    {
        return $this->createService('Test.BService');
    }


}