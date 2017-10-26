<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 加载css
 * @author 钱志伟 2015-07-26
 */
class LoadCssController extends Controller{
    
    /**
     * 加载css
     */
    public function loadCssAction(){
        $cssFileStr = isset($_GET['cssFileStr']) ? $_GET['cssFileStr'] : '';
        $cssContent = '';
        if($cssFileStr && $cssFileArr = explode(',', $cssFileStr)){
            foreach($cssFileArr as $cssFile){
                $cssFile = ltrim($cssFile, '/');
                $cssFilePath = __ROOT__ . '/Public/'.$cssFile;
                if(pathinfo($cssFilePath, PATHINFO_EXTENSION) !='css' || !file_exists($cssFilePath)){
                    $cssContent .= '/*invalid or noexists css file:'.$cssFile.', please contact admin*/';
                    continue;
                }
                $cssContent .= "/*{$cssFile}*/\r\n";
                $cssContent .= file_get_contents($cssFilePath);
            }
        }

        $site = getSetting('site');
        $themeCfg = isset($site['themeCfg']) ? $site['themeCfg'] : C('DEFAULT_THEME_CFG');
        $cssContent = str_replace(array_keys($themeCfg), array_values($themeCfg), $cssContent);

        header('Content-type: text/css'); 
        echo $cssContent ? $cssContent : '/*no css,please check*/';
    }
}

function cache()
{
    $etag = 'http://longrujun.name';
    if($_SERVER['HTTP_IF_NONE_MATCH'] == $etag)
    {
        header('Etag:'.$etag, true, 304);
        exit;
    }
    else header('Etag:'.$etag);
}