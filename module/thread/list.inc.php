<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT.'./source/plugin/zhaisoul_dzq_api/tools.class.php';

$plugin = $_G['cache']['plugin']['zhaisoul_dzq_api'];
loadcache('forums');
$forum_info = $_G['cache']['forums'];

$allow_forums = unserialize($_G['cache']['plugin']['zhaisoul_dzq_api']['forums']);

$page = intval($_GET['page']) ?? 1;
$perPage = $_GET['perPage'] ? intval($_GET['perPage']) : 10;
$scope = in_array(intval($_GET['scope']), array(0, 1, 2, 3)) ? intval($_GET['scope']) : 0;
$essence = intval($_GET['filter']['essence']) ?? 1;
$types = $_GET['filter']['types'];
$search = trim($_GET['filter']['search']);
$sort = in_array(intval($_GET['filter']['sort']), array(1, 2, 3, 4)) ? intval($_GET['filter']['sort']) : 1;
$attention = in_array(intval($_GET['filter']['attention']), array(1, 2)) ? intval($_GET['filter']['attention']) : 0;
$complex = in_array(intval($_GET['filter']['complex']), array(0, 1, 2, 3, 4, 5)) ? intval($_GET['filter']['complex']) : 0;
$toUserId = $_GET['filter']['toUserId'] == '0' ? $_G['uid'] : intval($_GET['filter']['toUserId']);
$categoryids = $_GET['filter']['categoryids'];

if($categoryids[0] && !in_array($categoryids[0], $allow_forums)) {
    Utils::outPut(-7076, "调用接口成功");
}


if ($scope == 3) {
    $page = 1;
    $perPage = 10;
}

if(!$categoryids) {
    $categoryids = dunserialize($plugin['forums']);
}

$start = ($page - 1) * $perPage;

$order = array(
    0 => 'dateline',
    1 => 'dateline',
    2 => 'lastpost',
);

if($toUserId) {
    $count_thread = C::t('#zhaisoul_dzq_api#forum_thread_API')->count_by_authorid_displayorder($toUserId, null, '=', null, '');
    $threads = C::t('#zhaisoul_dzq_api#forum_thread_API')->fetch_all_by_authorid_displayorder($toUserId, null, '=', null, '', $start, $perPage);
} else {
    $count_thread = C::t('#zhaisoul_dzq_api#forum_thread_API')->count_by_fid_displayorder_page($categoryids, 0, '>=', $order[$sort]);
    $threads = C::t('#zhaisoul_dzq_api#forum_thread_API')->fetch_all_by_fid_displayorder($categoryids, 0, null, null, $start, $perPage, $order[$sort]);
}
$thread_list = memory('get', 'dzq_thread_list_fid:'.serialize($categoryids));
require_once libfile('class/thread', 'plugin/zhaisoul_dzq_api');

if(!$thread_list || !is_array($thread_list)) {
    foreach ($threads as $thread) {
        if ($thread['displayorder'] < 0 && $thread['authorid'] != $_G['uid']) {
            continue;
        }

        $thread_list[] = outPutThread($thread, $_G['uid'], $forum_info);
    }

    if($thread_list) {
        memory('set', 'dzq_thread_list_fid:'.serialize($categoryids), $thread_list);
    }
}

$arr = [
    'currentPage' => $page,
    'pageLength' => count($threads),
    'perPage' => $perPage,
    'pageData' => $thread_list,
    'totalCount' => intval($count_thread),
    'totalPage' => intval(intval($count_thread) / $perPage)
];

Utils::outPut(0, "调用接口成功", $arr);