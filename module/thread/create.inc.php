<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT.'./source/plugin/zhaisoul_dzq_api/tools.class.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method != 'POST') {
    Utils::outPut(-2004, "请求方式错误");
}

$POST = json_decode(file_get_contents('php://input'));

$_GET['mod'] = 'post';
$_GET['action'] = 'newthread';
$_GET['subject'] = $POST->title;
$_GET['fid'] = intval($POST->categoryId);
$_G['forum']['fid'] = $_GET['fid'];
$_GET['save'] = $POST->draft;
$_GET['isanonymous'] = $POST->anonymous;
include_once libfile('function/editor');
$_GET['message'] = html2bbcode($POST->content->text);
$_GET['topicsubmit'] = 'yes';
$_GET['formhash'] = formhash();
$_SERVER['HTTP_REFERER'] = null;

if(empty($_GET['subject']) || empty($_GET['message'])) {
    Utils::outPut(-4001, "参数错误！标题或正文不存在");
}

require_once DISCUZ_ROOT.'./source/function/function_forum.php';

if(IN_MOBILE == 0) {
    $_G['setting']['hookscript']['plugin']['zhaisoul_dzq_api'] = $_G['setting']['hookscript']['plugin']['zhaisoul'];
} else {
    $_G['setting']['hookscriptmobile']['plugin']['zhaisoul_dzq_api'] = $_G['setting']['hookscript']['plugin']['zhaisoul'];
}

require_once DISCUZ_ROOT.'./source/module/forum/forum_post.php';
