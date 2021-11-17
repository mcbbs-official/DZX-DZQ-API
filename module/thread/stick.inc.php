<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$plugin = $_G['cache']['plugin']['zhaisoul_dzq_api'];
loadcache('forums');
$forum_info = $_G['cache']['forums'];

$fids = $_GET['categoryIds'];

$threads = C::t('forum_thread')->fetch_all_by_displayorder(array(1, 2, 3));
//Utils::outPut('0', "test", $fids);
$thread_list = array();
foreach($threads as $thread) {
    if(!empty($fids) && !in_array($thread['fid'], $fids)) {
        continue;
    } else if (empty($fids) && $thread['displayorder'] != 3) {
        continue;
    }
    $thread_list[] = array(
        'threadId' => $thread['tid'],
        'categoryId' => $thread['fid'],
        'title' => $thread['subject'],
        'updateAt' => dgmdate($thread['dateline']),
        'canViewPosts' => true
    );
}

include_once DISCUZ_ROOT.'./source/plugin/zhaisoul_dzq_api/tools.class.php';
Utils::outPut('0', "调用接口成功", $thread_list);