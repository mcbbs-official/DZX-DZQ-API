<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';

$tid = intval($_GET['threadId']);
require_once libfile('function/forum');
$thread = get_thread_by_tid($tid);

$arr = array(
    'viewCount' => $thread['views']
);

if(!discuz_process::islocked($_G['uid'].'view_lock_tid:'.$tid, 300)) {
    if (isset($_G['forum_thread']['addviews'])) {
        if ($_G['forum_thread']['addviews'] < 100) {
            C::t('forum_threadaddviews')->update_by_tid($tid);
        } else {
            if (!discuz_process::islocked('update_thread_view')) {
                $row = C::t('forum_threadaddviews')->fetch($tid);
                C::t('forum_threadaddviews')->update($_G['tid'], array('addviews' => 0));
                C::t('forum_thread')->increase($_G['tid'], array('views' => $row['addviews'] + 1), true);
                discuz_process::unlock('update_thread_view');
            }
        }
    }
}

Utils::outPut(0, '资源请求成功', $arr);