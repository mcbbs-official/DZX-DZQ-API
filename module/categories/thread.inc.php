<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';

loadcache('forums');
$forums = $_G['cache']['forums'];
$allow_forums = unserialize($_G['cache']['plugin']['zhaisoul_dzq_api']['forums']);
$user_groupid = $_G['groupid'];

$orderby = array(
    'lastpost' => 1,
);

$forum_list = array();

foreach($forums as $forum) {
    if(!in_array($forum['fid'], $allow_forums)) {
        continue;
    }
    if($forum['type'] != 'group') {
        $forum = array_merge($forum, C::t('forum_forumfield')->fetch($forum['fid']));
        $canView = in_array($user_groupid, explode("\t", $forum['viewperm']));
        if(!empty(trim($forum['viewperm'])) && $canView) {
            continue;
        }
        $postprem = explode("\t", $forum['postperm']);
        $canCreateThread = in_array($user_groupid, $postprem) != false;
        if ($forum['type'] == 'forum') {
            $forum_list[intval($forum['fid'])] = array(
                'categoryId' => intval($forum['fid']),
                'canCreateThread' => $canCreateThread,
                'name' => $forum['name'],
                'parentid' => 0,
                'pid' => intval($forum['fid']),
                'sort' => $orderby[$forum['orderby']],
                'threadCount' => intval($forum['threads']),
                'children' => []
            );
        } else {
            $forum_list[$forum['fup']]['children'][] = array(
                'categoryId' => intval($forum['fid']),
                'pid' => intval($forum['fid']),
                'canCreateThread' => $canCreateThread,
                'name' => $forum['name'],
                'parentid' => intval($forum['fup']),
                'sort' => $orderby[$forum['orderby']],
                'threadCount' => intval($forum['threads']),
            );
        }
    }
}

Utils::outPut(0, '资源请求成功', array_values($forum_list));