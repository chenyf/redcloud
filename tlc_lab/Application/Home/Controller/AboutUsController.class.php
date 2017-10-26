<?php

    namespace Home\Controller;

    use Common\Lib\ArrayToolkit;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
    use Topxia\System;
    use Common\Lib\Paginator;
     /**
    * 关于我们的调用
    * @author @czq 2016-3-9
    */
    class AboutUSController extends BaseController
    {
        public function aboutAction ($id)
        {

            $article = createService('System.AboutUs')->findIndex($id);
            $list = createService('System.AboutUs')->findAllOther();
            $site = createService('System.SettingServiceModel')->get('site', array());
            
           if (empty($article)) {
               throw $this->createNotFoundException('文章已删除或者未发布！');
           }

            return $this->render('Default:about-us', array(
                    "article" => $article,
                    "list"    => $list,
                    "site"    => $site
                ));
        }
    }
