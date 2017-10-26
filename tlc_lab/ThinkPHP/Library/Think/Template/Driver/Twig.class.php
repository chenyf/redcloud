<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think\Template\Driver;

/**
 * Twig模板引擎驱动
 */
class Twig {

    /**
     * 渲染模板输出
     * @access public
     * @param string $templateFile 模板文件名
     * @param array $parameters 模板变量
     * @return void
     */
    public function fetch($templateFile, $parameters) {
        /*
          $str = strrev($templateFile);
          for($i = 0; $i<2; $i++){
          $num = strpos($str, '/');
          $str = substr($str,$num+1);
          }
          $fixModPath = strrev($str).'/';
         */
        # by yangjinlong 
        $fixModPath = preg_replace("/([\w|(-\.)]+[\/]{1}[\w|(-\.)]+)([\.]{1}html[\.]twig)$/", "", $templateFile);
        #edit guojunqiang
        $tpl = C("TEMPLATE_TPL");
        $default = C('TEMPLATE_DEFAULT_TPL');
        $moduleViewPath = APP_PATH . ucfirst($Think . MODULE_NAME) . '/' . $tpl;
        if (!file_exists($moduleViewPath)) {
            $moduleViewPath = APP_PATH . ucfirst($Think . MODULE_NAME) . '/View';
        }
        $loader = new \Twig_Loader_Filesystem(array($fixModPath, THEME_PATH, $moduleViewPath));
        $loader->addPath(TEMPLATE_PATH);

        #qzw 2015-11-19  guojunqiang 2016-02-18
        if ($moduleList = C('MODULE_ALLOW_LIST')) {
            foreach ($moduleList as $module) {
                $pathTpl = APP_PATH . "{$module}/{$tpl}";
                if (!file_exists($pathTpl)) {
                    $pathTpl = APP_PATH . "{$module}/{$default}";
                }
                $loader->addPath($pathTpl, $module);
            }
        }
//            $loader->addPath(APP_PATH.'Admin/View', 'Admin');
//            $loader->addPath(APP_PATH.'Center/View', 'Center');
//            $loader->addPath(APP_PATH.'Home/View', 'Home');

        $loader->addPath(THEME_PATH, 'Web');

        $twig = new \Twig_Environment($loader, array(
            "cache" => CACHE_PATH,
            "auto_reload" => true,
                //     'debug' => true,
        ));
        $container = new \Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag();
        $twig->addExtension(new \Common\Twig\Web\WebExtension());
        $twig->addExtension(new \Common\Twig\Web\DataExtension());
        $twig->addExtension(new \Common\Twig\Web\HtmlExtension());
        $twig->addExtension(new \Common\Twig\Web\FormExtension($twig));
        $twig->addExtension(new \Common\Twig\Web\SecurityExtension());
        $twig->addExtension(new \Common\Twig\Web\AssetsExtension());
        //    $twig->addExtension(new \Common\Twig\Web\FormExtension());
        //$twig->addExtension(new \Symfony\Bundle\TwigBundle\Extension\AssetsExtension($container));
        //$twig->addExtension(new \Common\Twig\Web\RoutingExtension(new \Symfony\Component\Routing\Generator\UrlGenerator(new \Symfony\Component\Routing\RouteCollection(),new \Symfony\Component\Routing\RequestContext())));
        $routes = new \Symfony\Component\Routing\RouteCollection();
        $context = new \Symfony\Component\Routing\RequestContext();
        $urlgenerate = new \Symfony\Component\Routing\Generator\UrlGenerator($routes, $context);
        $twig->addExtension(new \Common\Twig\Web\RoutingExtension($urlgenerate));
        $twig->addExtension(new \Symfony\Bridge\Twig\Extension\HttpKernelExtension(""));
        //$twig->addExtension(new \Symfony\Bridge\Twig\Extension\FormExtension($twig));
        //$twig->addExtension(new \Symfony\Bridge\Twig\Extension\SecurityExtension(""));
        $twig->addExtension(new \Twig_Extensions_Extension_I18n());
        //$templateFile = substr($templateFile, strlen(THEME_PATH));
        $templateFile = preg_replace('/.*\/(views|' . $default . '|' . $tpl . ')\//', '', $templateFile);
        echo $twig->render($templateFile, $parameters);
    }

}