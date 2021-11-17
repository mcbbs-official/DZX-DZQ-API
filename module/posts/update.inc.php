<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT.'./source/plugin/zhaisoul_dzq_api/tools.class.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method != 'POST') {
    Utils::outPut(-2004, "请求方式错误");
}

$POST = json_decode(file_get_contents('php://input'), JSON_OBJECT_AS_ARRAY);

$_GET['tid'] = $POST['id'];
$_G['tid'] = $_GET['tid'];
$_GET['action'] = 'recommend';
$_GET['do'] = 'add';
//$_GET['pid'] = $POST['postId'];
$_GET['hash'] = formhash();

require_once DISCUZ_ROOT.'./source/function/function_forum.php';
if(IN_MOBILE == 0) {
    $_G['setting']['hookscript']['plugin']['zhaisoul_dzq_api'] = $_G['setting']['hookscript']['plugin']['zhaisoul'];
} else {
    $_G['setting']['hookscriptmobile']['plugin']['zhaisoul_dzq_api'] = $_G['setting']['hookscript']['plugin']['zhaisoul'];
}
require_once DISCUZ_ROOT.'./source/module/forum/forum_misc.php';