<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$threadId = intval($_GET['threadId']);
require_once libfile('class/thread', 'plugin/zhaisoul_dzq_api');
include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';

loadcache('forums');
$forum_info = $_G['cache']['forums'];

require_once libfile('function/forum');
$thread = get_thread_by_tid($threadId);

if($thread) {
    if($thread['displayorder'] <= -2 && $thread['authorid'] != $_G['uid']) {
        Utils::outPut(-4004, '资源不存在');
    }
    Utils::outPut(0, '资源请求成功', outPutThread($thread, $_G['uid'], $forum_info));
} else {
    Utils::outPut(-4004, '资源不存在');
}