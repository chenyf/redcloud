<?php
namespace Common\Common;

use Symfony\Component\EventDispatcher\GenericEvent;
use Common\Common\ServiceKernel;

class ServiceEvent extends GenericEvent
{

    public function getKernel()
    {
        return ServiceKernel::instance();
    }

    public function createService($name)
    {
        return $this->getKernel()->createService($name);
    }

    public function getCurrentUser()
    {
        return $this->getKernel()->getCurrentUser();
    }

}