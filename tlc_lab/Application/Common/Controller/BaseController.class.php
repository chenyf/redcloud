<?php

/**
 * 公共Controller
 * @author 赵作武 2015-03-25
 */

namespace Common\Controller;

use Think\Controller;

class BaseController extends Controller {
    protected $pool = array();
    /**
     * 实例化\Common\Model里Model
     * @author ZhaoZuoWu 2015-03-25
     */
    public function createModelService($name) {
        if (empty($this->pool[$name])) {
            $class = $this->getModelClassName($name);
            $this->pool[$name] = new $class();
        }
        return $this->pool[$name];
    }
    /**
     * 
     * @param type $type
     * @param type $name
     * @return type
     */
    private function getModelClassName($name)
    {
        $namespace = substr(__NAMESPACE__, 0, -strlen('Controller')-1);
        list($module, $className) = explode('.', $name);
         $class = '\\'.$namespace . '\\Model\\' . $module. '\\' . $className;
         return $class;
       
    }

}

?>
