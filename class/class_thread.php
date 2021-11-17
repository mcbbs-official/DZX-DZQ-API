<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * @param array|null $thread
 * @param $uid
 * @param $forum_info
 */
function outPutThread(?array $thread, $uid, $forum_info)
{
    global $_G;
    require_once libfile('function/discuzcode');

    $post = C::t('forum_post')->fetch_threadpost_by_tid_invisible($thread['tid']);

    $_G['tid'] = $thread['tid'];
    require_once libfile('function/attachment');
    if($post['attachment']) {
        if ((!empty($_G['setting']['guestviewthumb']['flag']) && !$_G['uid']) || $_G['group']['allowgetattach'] || $_G['group']['allowgetimage']) {
            $_G['forum_attachpids'][] = $post['pid'];
            if (preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $post['message'], $matchaids)) {
                $_G['forum_attachtags'][$post['pid']] = $matchaids[1];
            }
        } else {
            $post['message'] = preg_replace("/\[attach\](\d+)\[\/attach\]/i", '', $post['message']);
        }

        $postlist = array($post['pid'] => $post);
        parseattach($_G['forum_attachpids'], $_G['forum_attachtags'], $postlist, null);
        $post = $postlist[$post['pid']];
    }

    $post_count = intval(C::t('forum_post')->count_visiblepost_by_tid($thread['tid'])) - 1;
    $isrecommend = !empty(C::t('forum_memberrecommend')->fetch_by_recommenduid_tid($uid, $thread['tid']));
    $like_list = C::t('#zhaisoul_dzq_api#forum_memberrecommend_API')->fetch_all_user_by_tid($thread['tid']);
    $like_user_list = array();
    $authorinfo = getuserbyuid($thread['authorid']);
    $author_thread_count = C::t('forum_thread')->count_by_authorid($thread['authorid']);
    $author_group = C::t('common_usergroup')->fetch($authorinfo['groupid']);
    foreach ($like_list as $like_user) {
        $like_user_list[] = array(
            'avatar' => avatar($like_user['recommenduid'], 'middle', true),
            'createdAt' => dgmdate($like_user['dateline']),
            'nickname' => getuserbyuid($like_user['recommenduid'])['username'],
            'type' => 0,
            'userId' => $like_user['recommenduid']
        );
    }
    $thread = array(
        'threadId' => $thread['tid'],
        'postId' => $post['pid'],
        'userId' => $thread['authorid'],
        'parentCategoryId' => $thread['fid'],
        'topicId' => $thread['typeid'],
        'categoryName' => $forum_info[$thread['fid']]['name'],
        'parentCategoryName' => $forum_info[$forum_info[$thread['fid']]['fup']]['name'],
        'title' => $thread['subject'],
        'viewCount' => $thread['views'],
        'isApproved' => $thread['displayorder'] >= 0 ? 1 : 0,
        'isStick' => $thread['displayorder'] > 0,
        'isDraft' => $thread['displayorder'] == -3,
        'isSite' => false,
        'isAnonymous' => $post['anonymous'] != '0',
        'isFavorite' => false,
        'price' => 0,
        'payType' => 0,
        'paid' => false,
        'isLike' => $isrecommend,
        'isReward' => false,
        'issueAt' => dgmdate($thread['dateline']),
        'createdAt' => dgmdate($thread['dateline']),
        'updateAt' => dgmdate($thread['dateline']),
        'diffTime' => dgmdate($thread['dateline']),
        'freewords' => 0,
        'userStickStatus' => 0,
        'user' => array(
            'userId' => $authorinfo['uid'],
            'nickname' => $authorinfo['username'],
            'avatar' => avatar($like_user['recommenduid'], 'middle', true),
            'threadCount' => $author_thread_count,
            'followCount' => 0,
            'fansCount' => 0,
            'questionCount' => 0,
            'isRealName' => false,
            'joinedAt' => dgmdate($authorinfo['regdate'])
        ),
        'group' => array(
            'groupIcon' => '',
            'groupId' => $author_group['groupid'],
            'groupName' => $author_group['grouptitle'],
            'isDisplay' => $author_group['allowvisit'] > 0
        ),
        'likeReward' => array(
            'likePayCount' => intval($thread['recommend_add']),
            'postCount' => intval($post_count),
            'shareCount' => 0,
            'users' => $like_user_list
        ),
        'displayTag' => array(
            'isEssence' => $thread['digest'] >= 1,
            'isPrice' => false,
            'isRedPack' => false,
            'isReward' => $thread['special'] == 3,
            'isVote' => $thread['special'] == 1
        ),
        'position' => array(
            'address' => '',
            'latitude' => 0.000000,
            'location' => '',
            'longitude' => 0.00000,
        ),
        'ability' => array(
            'canDelete' => false,
            'canDownloadAttachment' => true,
            'canEdit' => true,
            'canEssence' => false,
            'canFreeViewPost' => true,
            'canReply' => true,
            'canStick' => false,
            'canViewPost' => true,
            'canViewAttachment' => true
        ),
        'content' => array(
            'text' => discuzcode($post['message'], false, false, 0, 1, 1, 1, 0, 0, 0, 0, 1, $post['pid'])
        )
    );
    return $thread;
}
