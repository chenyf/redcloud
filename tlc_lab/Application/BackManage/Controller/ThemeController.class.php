<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Symfony\Component\Finder\Finder;

class ThemeController extends BaseController
{
    public function indexAction (Request $request)
    {
        $currentTheme = $this->setting('theme', array('uri' => 'default'));
        $themes = $this->getThemes();
        if(ONLY_KEPP_COMMON_THEME !=1){
            foreach($themes as $k => $v){
                $res = createService('Content.BlockServiceModel')->getBlockByCode($v['uri'].":home_top_banner");
                $theme[$k] = $v;
                if($res){
                    $theme[$k]['block'] = 'yes';
                }else{
                    $theme[$k]['block'] = 'no';
                }
            }
        }else{
            foreach($themes as $k => $v){
                if($v['uri'] != 'redcloud-all')
                     continue;
                $res = createService('Content.BlockServiceModel')->getBlockByCode($v['uri'].":home_top_banner");
                $theme[$k] = $v;
                if($res){
                    $theme[$k]['block'] = 'yes';
                }else{
                    $theme[$k]['block'] = 'no';
                }
            }
        }
        return $this->render('Theme:index', array(
            'themes' => $theme,
            'currentTheme' => $currentTheme,
        ));
    }

    public function changeAction(Request $request)
    {
        $themeUri = $request->query->get('uri');
        $theme = $this->getTheme($themeUri);
        if (empty($theme)) {
            return $this->createJsonResponse(false);
        }

        $this->getSettingService()->set('theme', $theme);
        
        return $this->createJsonResponse(true);

    }

    private function getTheme($uri)
    {
        if (empty($uri)) {
            return null;
        }

        $dir = getParameter('kernel.root_dir'). '/Public/themes';

        $metaPath = $dir . '/' . $uri . '/theme.json';

        if (!file_exists($metaPath)) {
            return null;
        }

        $theme = json_decode(file_get_contents($metaPath), true);
        if (empty($theme)) {
            return null;
        }

        $theme['uri'] = $uri;

        return $theme;
    }

    private function getThemes()
    {
        $themes = array();

        $dir = getParameter('kernel.root_dir'). '/Public/themes';
        $finder = new Finder();
        foreach ($finder->directories()->in($dir)->depth('== 0') as $directory) {
            $theme = $this->getTheme($directory->getBasename());
            if ($theme) {
                $themes[] = $theme;
            }

        }

        return $themes;
    }

    protected function getSettingService()
    {
        return createService('System.SettingServiceModel');
    }
}