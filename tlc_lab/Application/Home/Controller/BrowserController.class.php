<?php
namespace Home\Controller;

class BrowserController extends BaseController
{
    public function upgradeAction()
    {
        return $this->render('Browser:upgrade');
    }
}