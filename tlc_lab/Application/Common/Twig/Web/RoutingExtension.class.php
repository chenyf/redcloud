<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Twig\Web;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provides integration of the Routing component with Twig.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RoutingExtension extends \Twig_Extension {

    private $generator;

    public function __construct(UrlGeneratorInterface $generator) {
        $this->generator = $generator;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('url', array($this, 'getUrl'), array('is_safe_callback' => array($this, 'isUrlGenerationSafe'))),
            new \Twig_SimpleFunction('path', array($this, 'getPath'), array('is_safe_callback' => array($this, 'isUrlGenerationSafe'))),
        );
    }

    /**
     * 解析yml格式的文件写入到config
     * @author ZhaoZuoWu 2015-03-31
     */
    public function parseYml($name, $parameters = array(), $relative = false) {
        $file = "./Application/Common/Conf/routing_1.yml";
        $info = yaml_parse_file($file);
        static $array = array();
        static $list = array();
        foreach ($info as $k => $v) {
            $url = $k;
            $defaults = $v["defaults"];
            $mapArr = explode(":", $defaults["_controller"]);
            if (strpos($mapArr[0], "Admin") !== false) {
                $mapArr[0] == "Admin";
            } else {
                $mapArr[0] = "Home";
            }
            $map = "";
            foreach ($mapArr as $key => $value) {

                $map.="/" . $value;
            }
            $list[$url] = $map;
        }
        $string_start = "<?php\n return array('URL_ROUTE_RULES'=>";
        $string_process = var_export($list, TRUE);
        $string_end = ");\n?>";
        $string = $string_start . $string_process . $string_end; //开始写入文件 
        $res = file_put_contents('./Application/Common/Conf/config.route_admin.php', $string);
        var_dump($res);
    }

    public function getPath($name, $parameters = array(), $relative = false) {
        $routeHomeArr = C("route_home");
        //var_dump($routeHomeArr["course_show"],$name);exit();
        $routeAdminArr = C("route_admin");
        $map = "";
        if (isset($routeHomeArr[$name])) {
            $map = $routeHomeArr[$name];
        } else if (isset($routeAdminArr[$name])) {
            $map = $routeAdminArr[$name];
        }
        return U($map, $parameters);
    }

    public function getUrl($name, $parameters = array(), $schemeRelative = false, $absolute = false) {
        $routeHomeArr = C("route_home");
        //var_dump($routeHomeArr["course_show"],$name);exit();
        $routeAdminArr = C("route_admin");
        $map = "";
        if (isset($routeHomeArr[$name])) {
            $map = $routeHomeArr[$name];
        } else if (isset($routeAdminArr[$name])) {
            $map = $routeAdminArr[$name];
        }
	    if(!$absolute){
		    return U($map, $parameters);
	    }
	    return $_SERVER['HTTP_HOST'].U($map, $parameters);
	    // return $this->generator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Determines at compile time whether the generated URL will be safe and thus
     * saving the unneeded automatic escaping for performance reasons.
     *
     * The URL generation process percent encodes non-alphanumeric characters. So there is no risk
     * that malicious/invalid characters are part of the URL. The only character within an URL that
     * must be escaped in html is the ampersand ("&") which separates query params. So we cannot mark
     * the URL generation as always safe, but only when we are sure there won't be multiple query
     * params. This is the case when there are none or only one constant parameter given.
     * E.g. we know beforehand this will be safe:
     * - path('route')
     * - path('route', {'param': 'value'})
     * But the following may not:
     * - path('route', var)
     * - path('route', {'param': ['val1', 'val2'] }) // a sub-array
     * - path('route', {'param1': 'value1', 'param2': 'value2'})
     * If param1 and param2 reference placeholder in the route, it would still be safe. But we don't know.
     *
     * @param \Twig_Node $argsNode The arguments of the path/url function
     *
     * @return array An array with the contexts the URL is safe
     */
    public function isUrlGenerationSafe(\Twig_Node $argsNode) {
        // support named arguments
        $paramsNode = $argsNode->hasNode('parameters') ? $argsNode->getNode('parameters') : (
                $argsNode->hasNode(1) ? $argsNode->getNode(1) : null
                );

        if (null === $paramsNode || $paramsNode instanceof \Twig_Node_Expression_Array && count($paramsNode) <= 2 &&
                (!$paramsNode->hasNode(1) || $paramsNode->getNode(1) instanceof \Twig_Node_Expression_Constant)
        ) {
            return array('html');
        }

        return array();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'routing';
    }

}
