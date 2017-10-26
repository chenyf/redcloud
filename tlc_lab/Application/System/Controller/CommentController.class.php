<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\Paginator;
use Common\Lib\FileToolkit;
use Common\Services\CommentService;

class CommentController extends \Home\Controller\BaseController {

    public function commentAction(Request $request) {
        if (C('COMMENT_SYSTEM_G') != 'open')
            return false;
        if ($request->getMethod() == 'POST') {
            $all = $request->request->all();

            $data['cmtType'] = $all['cmtType'] ? : '';
            $data['cmtIdStr'] = $all['cmtIdStr'] ? : '';
            $vcode = $all['vcode'] ? : '';
            $page = $all['page'] ? : 1;

            $arr = CommentService::checkCommentVcode($data['cmtType'], $data['cmtIdStr'], $vcode);
            if (!$arr)
                return false;

            $perPage = 10;
            $loadOver = 1;
            $user = $this->getCurrentUser();
            $cmtstat = $this->getCommentService()->getCmtStatByTypeAndIdStr($data);


            $total = $this->getCommentService()->getAllCmtCnt($cmtstat['id']);

            $maxPage = ceil($total / $perPage) ? : 1;
            $comment = $this->getCommentService()->getAllCommentList($cmtstat['id'], '', $perPage * ($page - 1), $perPage);

            foreach ($comment as $k => $v) {
                $strId = 'cmt' . $v['id'];
                $appraise = createService('System.AppraiseService')->getGoodInfoList($data['cmtType'], $strId);
                $comment[$k]['good'] = $appraise['good'];
                $comment[$k]['goodList'] = $appraise['goodList'];
                $isGood = createService('System.AppraiseService')->isGoodByUid($data['cmtType'], $strId, $user['id']);
                $comment[$k]['isGood'] = $isGood == 1 ? 1 : 0;
                $replyComment = $this->getCommentService()->getReplyByCmtId($v['id']);
                foreach ($replyComment as $key => $value) {
                    $replyId = 'reply' . $value['id'];
                    $r = createService('System.AppraiseService')->getGoodInfoList($data['cmtType'], $replyId);
                    $replyComment[$key]['good'] = $r['good'];
                    $replyComment[$key]['goodList'] = $r['goodList'];
                    $isGood = createService('System.AppraiseService')->isGoodByUid($data['cmtType'], $replyId, $user['id']);
                    $replyComment[$key]['isGood'] = $isGood == 1 ? 1 : 0;
                }
                $comment[$k]['replyComment'] = $replyComment;
            }
            $poset = createService('System.AppraiseService')->getGoodInfoList('poster', 'poster' . $data['cmtIdStr']);

            if ($page >= $maxPage)
                $loadOver = 2;
            $cmtRule = C('COMMENT_ITEM');
            $rule = $cmtRule[$data['cmtType']];

            if ($rule['status'] == 'close')
                return false;


            if (!$user['id']) {
                $anony = $this->getCommentService()->getCmtAnonymous();
                $user = '';
                $user['id'] = 0;
                $user['nickname'] = $anony['nickName'];
            } else {
                $delPower = 0;
                if ($cmtstat['createUid'] && $user['id'] == $cmtstat['createUid'])
                    $delPower = 1;
                if (in_array('ROLE_SUPER_ADMIN', $user['roles']) || in_array('ROLE_GOLD_ADMIN', $user['roles']) || in_array('ROLE_TEACHER', $user['roles']) || in_array('ROLE_ADMIN', $user['roles']))
                    $delPower = 1;
            }

            if (intval($page) > 1) {
                $param = $this->render("Comment:more-modal", array(
                    'user' => $user,
                    'vcode' => $vcode,
                    'cmtIdStr' => $data['cmtIdStr'],
                    'cmtType' => $data['cmtType'],
                    'cmtstat' => $cmtstat,
                    'comment' => $comment,
                    'cmtRule' => $rule,
                    'delPower' => $delPower,
                    'poset' => $poset,
                        ), TRUE);
            } else {
                $param = $this->render("Comment:comment-modal", array(
                    'user' => $user,
                    'vcode' => $vcode,
                    'cmtIdStr' => $data['cmtIdStr'],
                    'cmtType' => $data['cmtType'],
                    'cmtstat' => $cmtstat,
                    'comment' => $comment,
                    'cmtRule' => $rule,
                    'maxPage' => $maxPage,
                    'delPower' => $delPower,
                    'poset' => $poset,
                        ), TRUE);
            }


            return $this->createJsonResponse(array('success' => true, 'message' => $param, 'loadOver' => $loadOver));
        }
    }

    public function addCommentAction(Request $request) {
        if (C('COMMENT_SYSTEM_G') != 'open')
            return false;
        if ($request->getMethod() == 'POST') {
            $all = $request->request->all();

            $user = $this->getCurrentUser();
            $data['comment'] = trim($all['content']) ? trim($all['content']) : '';
            $data['sendUid'] = intval($all['sendUid']) ? intval($all['sendUid']) : 0;
            $data['sendName'] = trim($all['sendName']) ? trim($all['sendName']) : '';
            $vcode = trim($all['vcode']) ? trim($all['vcode']) : '';

            $data['uid'] = $user['id'] ? $user['id'] : 0;

            if (!$vcode)
                return false;
            if ($user['id'] && !$data['sendName'])
                return false;
            list($cmtType, $cmtIdStr, $createUid) = CommentService::jiemi($vcode);
            $data['cmtType'] = $cmtType;
            $data['cmtIdStr'] = $cmtIdStr;
            if ($createUid)
                $data['createUid'] = $createUid;

            if (!$cmtType || !$cmtIdStr)
                return $this->createJsonResponse(array('success' => false, 'message' => '参数错误'));

            $cmtRule = C('COMMENT_ITEM');
            $rule = $cmtRule[$cmtType];
            if ($rule['status'] != 'open')
                return $this->createJsonResponse(array('success' => false, 'message' => '评论未开启'));
            if (!$user['id'] && $rule['forceLogin'] == 1)
                return $this->createJsonResponse(array('success' => false, 'message' => '评论请先登录'));
            if (!$user['id'] && $rule['anonyNoLogin'] == 0)
                return $this->createJsonResponse(array('success' => false, 'message' => '评论请先登录'));
            if ($user['id']) {
                $userDayCnt = $this->getCommentService()->getUserDayCommentCnt($user['id']);
                if (!$user['id'] && $userDayCnt >= $rule['userDayCmtMaxCnt'])
                    return $this->createJsonResponse(array('success' => false, 'message' => '评论次数过多'));
            }
            if (!$data['comment'])
                return $this->createJsonResponse(array('success' => false, 'message' => '请给予评论'));

            $str = str_replace(array("\r\n", "\r", "\n"), "", $data['comment']);
            if (iconv_strlen($str, 'utf-8') > 140)
                return $this->createJsonResponse(array('success' => false, 'message' => '评论字数过多'));


            if ($data['sendUid'] == 0) {
                $anonyDayCnt = $this->getCommentService()->getAnonyDayCommentCnt();
                if (!$user['id'] && $anonyDayCnt >= $rule['noLoginDayMaxCnt'])
                    return $this->createJsonResponse(array('success' => false, 'message' => '评论次数过多'));
                if (!$data['sendName'])
                    return $this->createJsonResponse(array('success' => false, 'message' => '请填写匿名'));
                if (iconv_strlen($data['sendName'], 'utf-8') > 7)
                    return $this->createJsonResponse(array('success' => false, 'message' => '匿名字数不超过七个'));
                $data['nickName'] = $data['sendName'];
                $anony = $this->getCommentService()->addCmtAnonymous($data);
                $user = '';
                $user['id'] = 0;
                $user['nickname'] = $anony['nickName'];
            }

            $r = $this->getCommentService()->addComment($data);

            if (!$r)
                return $this->createJsonResponse(array('success' => false, 'message' => '评论失败'));
            $cmtstat = $this->getCommentService()->getCmtStatByTypeAndIdStr($data);
            if ($user['id']) {
                $delPower = 0;
                if ($cmtstat['createUid'] && $user['id'] == $cmtstat['createUid'])
                    $delPower = 1;
                if (in_array('ROLE_SUPER_ADMIN', $user['roles']) || in_array('ROLE_GOLD_ADMIN', $user['roles']) || in_array('ROLE_TEACHER', $user['roles']) || in_array('ROLE_ADMIN', $user['roles']))
                    $delPower = 1;
            }
            $param = $this->render("Comment:cmt-modal", array(
                'user' => $user,
                'vcode' => $vcode,
                'cmtIdStr' => $cmtIdStr,
                'cmtType' => $cmtType,
                'cmt' => $r,
                'cmtRule' => $rule,
                'vcode' => $vcode,
                'delPower' => $delPower,
                    ), TRUE);
            if (!$cmtstat['cmtCnt'])
                $cmtstat['cmtCnt'] = 0;
            if (!$cmtstat['userCnt'])
                $cmtstat['userCnt'] = 0;
            //评论发送通知
            $this->getCommentService()->pushCommentLog($cmtType ,$cmtIdStr);
            return $this->createJsonResponse(array('success' => true, 'message' => $param, 'anonyName' => $r['sendName'], 'cmtNum' => $cmtstat['cmtCnt'], 'userNum' => $cmtstat['userCnt']));
        }
    }
    

    public function delCommentAction(Request $request) {
        if (C('COMMENT_SYSTEM_G') != 'open')
            return false;
        if ($request->getMethod() == 'POST') {
            $cmtId = $request->request->get('cmtId');
            $param['cmtType'] = $request->request->get('cmtType');
            $param['cmtIdStr'] = $request->request->get('cmtIdStr');
            $vcode = $request->request->get('vcode');

            $id = intval($cmtId) ? : 0;
            $user = $this->getCurrentUser();

            $arr = CommentService::checkCommentVcode($param['cmtType'], $param['cmtIdStr'], $vcode);
            if (!$arr)
                return false;

            if (!$id || !$user['id'])
                return $this->createJsonResponse(array('success' => false, 'message' => '评论删除失败'));

            $r = $this->getCommentService()->getComment($id);
            $cmt = $this->getCommentService()->delComment($id, $r['cmtStatId']);
            if (!$cmt)
                return $this->createJsonResponse(array('success' => false, 'message' => '评论删除失败'));
            $cmtstat = $this->getCommentService()->getCmtStatByTypeAndIdStr($param);
            if (!$cmtstat['cmtCnt'])
                $cmtstat['cmtCnt'] = 0;
            if (!$cmtstat['userCnt'])
                $cmtstat['userCnt'] = 0;
            return $this->createJsonResponse(array('success' => true, 'message' => '评论删除成功', 'cmtNum' => $cmtstat['cmtCnt'], 'userNum' => $cmtstat['userCnt']));
        }
    }

    public function replyCommentAction(Request $request) {
        if (C('COMMENT_SYSTEM_G') != 'open')
            return false;
        if ($request->getMethod() == 'POST') {
            $all = $request->request->all();
            $user = $this->getCurrentUser();
            
            $data['cmtStatId'] = intval($all['statId']) ? : '';
            $data['cmtId'] = intval($all['cmtId']) ? : 0;
            $data['pid'] = intval($all['pid']) ? : 0;
            $data['reply'] = trim($all['content']) ? : '';
            $data['replyUid'] = intval($all['sendUid']) ? : 0;
            $data['replyName'] = trim($all['sendName']) ? : '';
            $data['uid'] = $user['id'] ? $user['id'] : 0;
            
            $cmtType = trim($all['cmtType']) ? : '';
            $replyType = trim($all['replyType']) ? : '';
            $cmtIdStr = trim($all['cmtIdStr']) ? : '';

            $vcode = trim($all['vcode']) ? : '';

            $arr = CommentService::checkCommentVcode($cmtType, $cmtIdStr, $vcode);
            if (!$arr)
                return false;

            $cmtRule = C('COMMENT_ITEM');
            $rule = $cmtRule[$cmtType];
            if ($rule['status'] != 'open')
                return $this->createJsonResponse(array('success' => false, 'message' => '评论未开启'));

            if (!$user['id'] && $rule['forceLogin'] == 1)
                return $this->createJsonResponse(array('success' => false, 'message' => '回复请先登录'));
            if (!$user['id'] && $rule['anonyNoLogin'] == 0)
                return $this->createJsonResponse(array('success' => false, 'message' => '回复请先登录'));

            if (!$data['reply'])
                return $this->createJsonResponse(array('success' => false, 'message' => '请给予评论'));
            if (iconv_strlen($data['reply'], 'utf-8') > 140)
                return $this->createJsonResponse(array('success' => false, 'message' => '回复字数过多'));
            if ($user['id']) {
                $userDayReplyCnt = $this->getCommentService()->getUserDayReplyCnt($user['id']);
                if (!$user['id'] && $userDayReplyCnt >= $rule['userDayCmtMaxCnt'])
                    return $this->createJsonResponse(array('success' => false, 'message' => '回复次数过多'));
            }


            if ($user['id'] && !$data['replyName'])
                return $this->createJsonResponse(array('success' => false, 'message' => '回复失败'));

            if (!$cmtType || !$replyType || !$cmtIdStr || !$data['reply'] || !$data['cmtStatId'] || !$data['cmtId'])
                return $this->createJsonResponse(array('success' => false, 'message' => '回复失败'));

            if ($data['replyUid'] == 0) {
                $anonyDayReplyCnt = $this->getCommentService()->getAnonyDayReplyCnt();
                if (!$user['id'] && $anonyDayReplyCnt >= $rule['noLoginDayMaxCnt'])
                    return $this->createJsonResponse(array('success' => false, 'message' => '回复次数过多'));
                if (!$data['replyName'])
                    return $this->createJsonResponse(array('success' => false, 'message' => '请填写匿名'));
                if (iconv_strlen($data['replyName'], 'utf-8') > 7)
                    return $this->createJsonResponse(array('success' => false, 'message' => '匿名字数不超过七个'));
                $data['nickName'] = $data['replyName'];
                $anony = $this->getCommentService()->addCmtAnonymous($data);
                $user = '';
                $user['id'] = 0;
                $user['nickname'] = $anony['nickName'];
            }

            $r = $this->getCommentService()->addReplyComment($data);
            if ($r['pid'] == 0) {
                $beReply = $this->getCommentService()->getCmtSendName($r['cmtId']);
                $r['beReplyName'] = $beReply['sendName'];
                $r['beReplyUid'] = $beReply['sendUid'];
            } else {
                $beReply = $this->getCommentService()->getReplyUser($r['pid']);
                $r['beReplyName'] = $beReply['replyName'];
                $r['beReplyUid'] = $beReply['replyUid'];
            }
            if (!$r)
                return $this->createJsonResponse(array('success' => false, 'message' => '评论失败'));

            $cmtstat = $this->getCommentService()->getCmtStatByTypeAndIdStr($all);
            if ($user['id']) {
                $delPower = 0;
                if ($cmtstat['createUid'] && $user['id'] == $cmtstat['createUid'])
                    $delPower = 1;
                if (in_array('ROLE_SUPER_ADMIN', $user['roles']) || in_array('ROLE_GOLD_ADMIN', $user['roles']) || in_array('ROLE_TEACHER', $user['roles']) || in_array('ROLE_ADMIN', $user['roles']))
                    $delPower = 1;
            }
            $param = $this->render("Comment:reply-modal", array(
                'user' => $user,
                'cmtIdStr' => $cmtIdStr,
                'cmtType' => $cmtType,
                'reply' => $r,
                'replyType' => $replyType,
                'cmtRule' => $rule,
                'vcode' => $vcode,
                'delPower' => $delPower,
                    ), TRUE);
            if (!$cmtstat['cmtCnt'])
                $cmtstat['cmtCnt'] = 0;
            if (!$cmtstat['userCnt'])
                $cmtstat['userCnt'] = 0;
            //回复发送通知
            $replyLog['replyId'] = $r['id'] ;
            $replyLog['cmtType'] = $cmtType ;
            $replyLog['cmtIdStr'] = $cmtIdStr ;
            $replyLog['cmtStatId'] = $r['cmtStatId'] ;
            $this->getCommentService()->pushReplyLog($replyLog);
            return $this->createJsonResponse(array('success' => true, 'message' => $param, 'anonyName' => $r['replyName'], 'cmtNum' => $cmtstat['cmtCnt'], 'userNum' => $cmtstat['userCnt']));
        }
    }
    
    

    public function delReplyCommentAction(Request $request) {
        if (C('COMMENT_SYSTEM_G') != 'open')
            return false;
        if ($request->getMethod() == 'POST') {
            $rid = $request->request->get('rid');
            $vcode = $request->request->get('vcode');

            $id = intval($rid) ? : 0;
            $user = $this->getCurrentUser();

            list($cmtType, $cmtIdStr, $createUid) = CommentService::jiemi($vcode);
            $data['cmtType'] = $cmtType;
            $data['cmtIdStr'] = $cmtIdStr;
            if ($createUid)
                $data['createUid'] = $createUid;

            if (!$cmtType || !$cmtIdStr)
                return $this->createJsonResponse(array('success' => false, 'message' => '参数错误'));
            if (!$id || !$user['id'])
                return $this->createJsonResponse(array('success' => false, 'message' => '回复删除失败'));

            $reply = $this->getCommentService()->getReplyComment($id);
            //获得回复此回复的回复id数组
            $pdata = $this->getCommentService()->getIdByReplyPid($id);
            $replyArr = $this->getIdArr($pdata);
            foreach ($replyArr as $k => $v) {
                $idArr[] = $v['id'];
            }
            $idArr = array_unique($idArr);

            $r = $this->getCommentService()->delReplyComment($reply);

            if (!$r)
                return $this->createJsonResponse(array('success' => false, 'message' => '回复删除失败'));
            $cmtstat = $this->getCommentService()->getCmtStatByTypeAndIdStr($data);
            if (!$cmtstat['cmtCnt'])
                $cmtstat['cmtCnt'] = 0;
            if (!$cmtstat['userCnt'])
                $cmtstat['userCnt'] = 0;
            return $this->createJsonResponse(array('success' => true, 'message' => '回复删除成功', 'idArr' => $idArr, 'cmtNum' => $cmtstat['cmtCnt'], 'userNum' => $cmtstat['userCnt']));
        }
    }

    public function getIdArr($data) {
        $arr = $data;
        foreach ($data as $k => $v) {
            $param = $this->getCommentService()->getIdByReplyPid($v['id']);
            if (!empty($param)) {
                $arr = array_merge($arr, $param);
                $parr = $this->getIdArr($param);
                if (!empty($parr))
                    $arr = array_merge($arr, $parr);
            }
        }
        return $arr;
    }

    public function exitAnonymousAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $anony = $request->request->get('anony');
            if ($anony == '1')
                $r = $this->getCommentService()->delCmtAnonymous();

            if (!$r)
                return $this->createJsonResponse(array('success' => false, 'message' => '退出失败'));
            return $this->createJsonResponse(array('success' => true));
        }
    }

    private function getCommentService() {
        return createService('Comment.CommentService');
    }
    

}