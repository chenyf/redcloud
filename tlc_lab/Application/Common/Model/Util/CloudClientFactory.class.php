<?php

namespace Common\Model\Util;


class CloudClientFactory
{

    public function createClient()
    {
        $parameter = getParameter('cloud_client');

        $arguments = empty($parameter['arguments']) ? array() : $parameter['arguments'];

        $class = __NAMESPACE__ . '\\PolyvCloudClient';
       
        $client = new $class($arguments);

        return $client;
    }
}