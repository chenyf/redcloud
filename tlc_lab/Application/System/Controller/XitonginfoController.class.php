<?php

namespace System\Controller;

use Common\Lib\ArrayToolkit;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Topxia\System;

class XitonginfoController extends \Home\Controller\BaseController
{
    public function indexAction (Request $request)
    {   
        $version = $this->getParam($request, "version", '1');
        $systemVersion = C("version");
        $info = array(
            'version' => $systemVersion['asset'],
            'name' => $this->setting('site.name', ''),
            'mobileApiVersion' => $version,
            'mobileApiUrl' => $request->getSchemeAndHttpHost() . '/mapi_v' . $version,
        );

        return $this->createJson($request, $info);
    }

    protected function getParam($request, $name, $default = null)
    {
        if ($request->getMethod() == "POST") {
            $result = $request->request->get($name);
        } else {
            $result = $request->query->get($name);
        }

        return $result ? $result : $default;
    }

    protected function createJson(Request $request, $data)
    {
        $callback = $request->query->get('callback');
        if ($callback) {
            return $this->createJsonP($request, $callback, $data);
        } else {
            return new JsonResponse($data);
        }
    }

    protected function createJsonP(Request $request, $callback, $data)
    {
        $response = new JsonResponse($data);
        $response->setCallback($callback);
        return $response;
    }
}