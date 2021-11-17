<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$tid = intval($_GET['threadId']);
$page = $_GET['page'] ? intval($_GET['page']) : 1;
$perPage = 10;
$type = 1;
$pageData = array();

$start = ($page - 1) * $perPage;

$user_count = intval(C::t('#zhaisoul_dzq_api#forum_memberrecommend_API')->count_by_tid($tid));

$pageData = array(
    'allCount' => $user_count,
    'likeCount' => $user_count,
    'rewardCount' => 0,
    'paidCount' => 0,
    'list' => array()
);

$recommends = C::t('#zhaisoul_dzq_api#forum_memberrecommend_API')->fetch_all_user_by_tid($tid, $start, $perPage);
foreach($recommends as $recommend) {
    $user = getuserbyuid($recommend['recommenduid']);
    $pageData['list'][] = array(
        'userId' => intval($recommend['recommenduid']),
        'createdAt' => $user['regdate'],
        'type' => 1,
        'passedAt' => dgmdate($recommend['dateline']),
        'nickname' => $user['username'],
        'avatar' => avatar($recommend['recommenduid'], 'middle', true)
    );
}

$arr = array(
  'pageData' => $pageData,
  'currentPage' => $page,
  'perPage' => $perPage,
  'pageLength' => $perPage,
  'totalCount' => $user_count,
  'totalPage' => intval($user_count / $perPage)
);

include_once DISCUZ_ROOT.'./source/plugin/zhaisoul_dzq_api/tools.class.php';
Utils::outPut('0', "调用接口成功", $arr);