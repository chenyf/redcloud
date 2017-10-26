<?php

namespace BackManage\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Common\ServiceException;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;


class BlockController extends BaseController
{
    public function indexAction(Request $request)
    {
        $code = isset($_GET['code']) ? $_GET['code'] : "";
        
        $paginator = new Paginator(
            getRequest(),
//            $this->container->get('request'),
            $this->getBlockService()->searchBlockCount(),
            20
        );
        
        $where['status'] = array("eq" ,'1');

        if(ONLY_KEPP_COMMON_THEME ==1){
            $where['_query'] = "code=redcloud-all:home_top_banner&isTheme=no&_logic=or";
        }
        
        $findedBlocks = $this->getBlockService()->searchBlocks($paginator->getOffsetCount(),
        $paginator->getPerPageCount(),$where);
        $latestBlockHistory = $this->getBlockService()->getLatestBlockHistory();
        $latestUpdateUser = $this->getUserService()->getUser($latestBlockHistory['userId']);
        foreach($findedBlocks as $k => $v){
            $arr = explode(":",$v['code']);
            $findedBlocks[$k]['themeCode'] = $arr[0];
        }
        return $this->render('Block:index', array(
            'blocks'=>$findedBlocks,
            'latestUpdateUser'=>$latestUpdateUser,
            'paginator' => $paginator,
            'code' => $code
        ));
    }

    public function previewAction(Request $request, $id)
    {
        $type = $_GET['type'];
        $blockHistory = $this->getBlockService()->getBlockHistory($id);
        if($type=='small'){
            $blockHistory['content'] = $blockHistory['contentsmall'];
        }
        return $this->render('Block:blockhistory-preview', array(
            'blockHistory'=>$blockHistory
        ));
    }

    public function updateAction(Request $request, $block)
    {
        $blockType  = intval($block);
        if (is_numeric(($block))) {
            $block = $this->getBlockService()->getBlock($block);
        } else {
            $block = $this->getBlockService()->getBlockByCode($block);
        }

        #qzw 2015-1-19
        $block['bkContent'] = $block['bkContentsmall'] = array();       

        $regex = "/(<a.*>.*<\/a>)/iU";
        $r = preg_match_all($regex, $block['content'], $matchA);
        if($r) $block['bkContent']= $matchA[0];
        $r = preg_match_all($regex, $block['contentsmall'], $matchB); 
        if($r) $block['bkContentsmall'] = $matchB[0];

        $paginator = new Paginator(
            $this->get('request'),
            $this->getBlockService()->findBlockHistoryCountByBlockId($block['id']),
            5
        );
        $templateData = array();
        $templateItems = array();
        if ($block['mode'] == 'template') {
            $templateItems = $this->getBlockService()->generateBlockTemplateItems($block);
            $templateData = json_decode($block['templateData'],true);
        } 

        $blockHistorys = $this->getBlockService()->findBlockHistorysByBlockId(
            $block['id'], 
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount());
        foreach ($blockHistorys as &$blockHistory) {
            $blockHistory['templateData'] = json_decode($blockHistory['templateData'],true);
        }
        $historyUsers = $this->getUserService()->findUsersByIds(ArrayToolkit::column($blockHistorys, 'userId'));

        if ('POST' == $request->getMethod()) {
            $fields = $request->request->all();
            $templateData = array();
            if ($block['mode'] == 'template') {
                $template = $block['template'];
                
                $template = str_replace(array("(( "," ))","((  ","  )"),array("((","))","((","))"),$template); 
                
                $content = "";
                
                foreach ($fields as $key => $value) {   
                    $content = str_replace('(('.$key.'))', $value, $template);
                    break;
                };
                foreach ($fields as $key => $value) {   
                    $content = str_replace('(('.$key.'))', $value, $content);
                }
                $templateData = $fields;
                $fields = "";
                $fields['content'] = $content;
                $fields['contentsmall'] = $contentsmall;
                $fields['templateData'] = json_encode($templateData);
            }
            if(isset($fields["_csrf_token"])) unset($fields["_csrf_token"]);
            $block = $this->getBlockService()->updateBlock($block['id'], $fields);
            $latestBlockHistory = $this->getBlockService()->getLatestBlockHistory();
            $latestUpdateUser = $this->getUserService()->getUser($latestBlockHistory['userId']);
            $html = $this->renderView('Block:list-tr', array(
                'block' => $block, 'latestUpdateUser'=>$latestUpdateUser
            ),true);
            $this->getLogService()->info('block', 'update', "编辑区管理更新", $fields);
            return $this->createJsonResponse(array('status' => 'ok', 'html' => $html));          
        }
        return $this->render('Block:block-update-modal', array(
            'block' => $block,
            'blockHistorys' => $blockHistorys,
            'historyUsers' => $historyUsers,
            'paginator' => $paginator,
            'templateItems' => $templateItems,
            'blockType'=>$blockType,
            'templateData' => $templateData
        ));
    }

    public function editAction(Request $request, $block)
    {
        $block = $this->getBlockService()->getBlock($block);

        if ('POST' == $request->getMethod()) {

            $fields = $request->request->all();
            $block = $this->getBlockService()->updateBlock($block['id'], $fields);
            $user = $this->getCurrentUser();
            $html = $this->renderView('Block:list-tr', array(
                'block' => $block, 'latestUpdateUser'=>$user
            ));
            return $this->createJsonResponse(array('status' => 'ok', 'html' => $html));
        }

        return $this->render('Block:block-modal', array(
            'editBlock' => $block
        ));
    }
    
    public function addBlockAction(){
        $fields['title'] = isset($_POST['title'])?$_POST['title']:"";
        $fields['code'] = isset($_POST['code'])?$_POST['code'].":home_top_banner":"";
        $fields['isTheme'] = "yes";
        $block = $this->getBlockService()->createBlock($fields);
        if($block){
            echo "success";
        }else{
            echo "error";
        }
    }

    public function createAction(Request $request)
    {
        
        if ('POST' == $request->getMethod()) {
            $block = $this->getBlockService()->createBlock($request->request->all());
            $user = $this->getCurrentUser();
            $html = $this->renderView('Block:list-tr', array('block' => $block,'latestUpdateUser'=>$user));
            $this->getLogService()->info('block', 'create', "编辑区添加");
            return $this->createJsonResponse(array('status' => 'ok', 'html' => $html));
        }

        $editBlock = array(
            'id' => 0,
            'title' => '',
            'code' => '',
            'mode' => 'html',
            'template' => ''
        );

        return $this->render('Block:block-modal', array(
            'editBlock' => $editBlock
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        try {
            $this->getBlockService()->deleteBlock($id);
            $this->getLogService()->info('block', 'delete', "编辑区删除");
            return $this->createJsonResponse(array('status' => 'ok'));
        } catch (ServiceException $e) {
            return $this->createJsonResponse(array('status' => 'error'));
        }
    }

    public function checkBlockCodeForCreateAction(Request $request)
    {
        $code = $request->query->get('value');
        $blockByCode = $this->getBlockService()->getBlockByCode($code);
        if (empty($blockByCode)) {
            return $this->createJsonResponse(array('success' => true, 'message' => '此编码可以使用'));
        }
        return $this->createJsonResponse(array('success' => false, 'message' => '此编码已存在,不允许使用'));
    }

    public function checkBlockCodeForEditAction(Request $request, $id)
    {
        $code = $request->query->get('value');
        $blockByCode = $this->getBlockService()->getBlockByCode($code);
        if(empty($blockByCode)){
            return $this->createJsonResponse(array('success' => true, 'message' => 'ok'));
        } elseif ($id == $blockByCode['id']){
            return $this->createJsonResponse(array('success' => true, 'message' => 'ok'));
        } elseif ($id != $blockByCode['id']){
            return $this->createJsonResponse(array('success' => false, 'message' => '不允许设置为已存在的其他编码值'));
        }
    }

    protected function getBlockService()
    {
        return createService('Content.BlockService');
    }

}