<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT.'./source/plugin/zhaisoul_dzq_api/tools.class.php';

$page = intval($_GET['page']) ?? 1;
$perPage = $_GET['perPage'] ? intval($_GET['perPage']) : 10;
$scope = in_array(intval($_GET['scope']), array(0, 1, 2, 3)) ? intval($_GET['scope']) : 0;
$threadId = intval($_GET['filter']['thread']);

$start = ($page - 1) * $perPage;

require_once libfile('function/forum');
$posts = C::t('forum_post')->fetch_all_by_tid(0, $threadId, true, '', $start, $perPage, 0, 0);
$post_list = array();
$post_count = intval(C::t('forum_post')->count_visiblepost_by_tid($threadId));

require_once libfile('function/discuzcode');
foreach($posts as $post) {
    $post_list[] = array(
        'canDelete' => false,
        'canHide' => false,
        'canLike' => false,
        'commentPostId' => 0,
        'commentUserId' => 0,
        'content' => discuzcode($post['message']),
        'createdAt' => dgmdate($post['dateline']),
        'id' => intval($post['pid']),
        'images' => [],
        'isApproved' => $post['invisible'] == 0 ? 1 : 0,
        'isComment' => false,
        'isDeleted' => $post['invisible'] == -1,
        'isFirst' => $post['first'] == '1',
        'isLiked' => false,
        'likeCount' => 0,
        'likeState' => null,
        'likedAt' => '',
        'redPacketAmount' => 0,
        'replyCount' => 0,
        'replyPostId' => 0,
        'replyUserId' => 0,
        'rewards' => 0,
        'summaryText' => discuzcode($post['message']),
        'threadId' => $threadId,
        'user' => array(
            'avatar' => avatar($post['authorid'], 'middle', true),
            'id' => $post['authorid'],
            'isReal' => false,
            'nickname' => $post['author'],
            'username' => $post['author']
        ),
        'userId' => intval($post['authorid']),
        'lastThreeComments' => []
    );
}

$arr = array(
    'pageData' => $post_list,
    'currentPage' => $page,
    'perPage' => $perPage,
    'pageLength' => intval($post_count / $perPage),
    'totalCount' => $post_count - 1,
    'totalPage' => intval($post_count / $perPage)
);



Utils::outPut('0', "调用接口成功", $arr);