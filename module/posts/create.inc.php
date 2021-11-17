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
$_GET['action'] = 'reply';
include_once libfile('function/editor');
$_GET['message'] = html2bbcode($POST->content);
$_GET['tid'] = intval($POST->id);

$_G['tid'] = $_GET['tid'];
require_once libfile('function/forum');
$thread = get_thread_by_tid($_GET['tid']);

$_GET['fid'] = $thread['fid'];
$_GET['replysubmit'] = 'yes';
$_GET['formhash'] = formhash();
$_SERVER['HTTP_REFERER'] = null;
$_GET['isComment'] = 1;

if(empty($_GET['message'])) {
    Utils::outPut(-4001, "参数错误！正文不存在");
}

require_once DISCUZ_ROOT.'./source/function/function_forum.php';
if(IN_MOBILE == 0) {
    $_G['setting']['hookscript']['plugin']['zhaisoul_dzq_api'] = $_G['setting']['hookscript']['plugin']['zhaisoul'];
} else {
    $_G['setting']['hookscriptmobile']['plugin']['zhaisoul_dzq_api'] = $_G['setting']['hookscript']['plugin']['zhaisoul'];
}

//调用DZX自带的发帖逻辑
require_once DISCUZ_ROOT.'./source/module/forum/forum_post.php';
