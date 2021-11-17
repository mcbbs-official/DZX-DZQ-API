<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';
$uid = intval($_GET['userId']) ?? $_G['uid'];

$user = getuserbyuid($uid);
$user = array_merge($user, C::t('common_member_profile')->fetch($uid), C::t('common_member_field_forum')->fetch($uid), C::t('common_member_status')->fetch($uid));

if(empty($user)) {
    Utils::outPut(-4004, '资源不存在');
}

$banInfo = array();
if(in_array($user['groupid'], array(4, 5, 6))) {
    $banInfo = end(C::t('common_member_crime')->fetch_all_by_uid_action($uid, $user['groupid']));
}
$group = C::t('common_usergroup')->fetch($user['groupid']);
$arr = array(
    'avatarUrl' => avatar($uid, 'middle', true),
    'backgroundUrl' => '',
    'banReason' => $banInfo['reason'],
    'canBeAsked' => true,
    'canDelete' => false,
    'canEdit' => true,
    'canEditUsername' => true,
    'canWalletPay' => false,
    'denyStatus' => false,
    'expiredAt' => $user['groupexpiry'],
    'expiredDays' => false,
    'follow' => '',
    'followCount' => 0,
    'fansCount' => 0,
    'group' => array(
        'groupName' => $group['grouptitle'],
        'groupId' => $user['groupid'],
        'expirationTime' => $user['groupexpiry'],
        'isTop' => false,
        'color' => $group['color'],
        'level' => 0,
        'remainTime' => 0,
        'typeTime' => 0,
    ),
    'hasPassword' => true,
    'id' => $uid,
    'identity' => '',
    'isBindWechat' => false,
    'isReal' => false,
    'isRenew' => false,
    'likedCount' => 0,
    'loginAt' => '',
    'mobile' => $_G['uid'] == $uid ? $user['mobile'] : null,
    'nickname' => $user['username'],
    'originalAvatarUrl' => '',
    'originalBackGroundUrl' => '',
    'originalMobile' => null,
    'paid' => false,
    'payTime' => '',
    'questionCount' => 0,
    'realname' => '',
    'registerReason' => '',
    'showGroups' => true,
    'signature' => $user['customstatus'],
    'status' => $user['status'],
    'threadCount' => intval(C::t('forum_thread')->count_by_authorid($uid)),
    'updatedAt' => dgmdate($user['lastpost']),
    'username' => $user['username'],
    'usernameBout' => 0,
    'walletBalance' => 0,
    'walletFreeze' => 0,
    'wxHeadImgUrl' => '',
    'wxNickname' => ''
);

Utils::outPut(0, '资源请求成功', $arr);