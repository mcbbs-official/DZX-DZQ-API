<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';

$POST = json_decode(file_get_contents('php://input'), JSON_OBJECT_AS_ARRAY);
$_GET['mod'] = 'logging';
$_GET['action'] = !empty($_GET['action']) ? $_GET['action'] : 'login';
$_GET['loginsubmit'] = 'yes';
$_GET['username'] = $POST['username'];
$_GET['password'] = $POST['password'];

require_once libfile('function/member');
require_once libfile('class/member');

clearcookies();

if(IN_MOBILE == 0) {
    $_G['setting']['hookscript']['plugin']['zhaisoul_dzq_api'] = $_G['setting']['hookscript']['plugin']['zhaisoul'];
} else {
    $_G['setting']['hookscriptmobile']['plugin']['zhaisoul_dzq_api'] = $_G['setting']['hookscript']['plugin']['zhaisoul'];
}

require_once DISCUZ_ROOT.'./source/module/member/member_logging.php';