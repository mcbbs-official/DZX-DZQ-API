<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/** 用于获取站点信息用的接口 */

include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';

$admin = getuserbyuid(explode(',', $_G['config']['admincp']['founder'])[0]);

$arr = array(
    'setSite' => array(
        'siteName' => $_G['setting']['bbname'],
        'siteTitle' => str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['forum']),
        'siteKeywords' => $_G['setting']['seokeywords'],
        'siteIntroduction' => $_G['setting']['seodescription'],
        'siteMode' => 'public',
        'openExtFields' => 0,
        'siteClose' => $_G['setting']['bbclosed'],
        'siteManage' => array(),
        'apiFreq' => null,
        'siteCloseMsg' => $_G['setting']['closedreason'],
        'siteFavicon' => '/favicon.ico',
        'siteLogo' => $_G['siteurl'].$_G['style']['boardimg'],
        'siteHeaderLogo' => null,
        'siteBackgroundImage' => null,
        'siteUrl' => $_G['cache']['plugin']['zhaisoul_dzq_api']['dzq_url'],
        'siteStat' => "",
        'siteAuthor' => array(
            'id' => $admin['uid'],
            'nickname' => $admin['username'],
            'username' => $admin['username'],
            'avatar' => avatar($admin['uid'], 'middle', true)
        ),
        'siteInstall' => dgmdate(TIMESTAMP),
        'siteRecord' => $_G['setting']['icp'],
        'siteCover' => "",
        'siteRecordCode' => $_G['setting']['mps'],
        'siteMasterScale' => '0',
        'sitePayGroupClose' => "",
        'siteMinimumAmount' => "",
        'siteOpenSort' => 0,
        'usernameLoginIsdisplay' => true,
        'openApiLog' => '0',
        'openViewCount' => '0',
        'version' => 'dzq_api_v1.1'
    ),
    'setReg' => array(
        'registerClose' => $_G['setting']['regstatus'] == 1 || $_G['setting']['regstatus'] == 3,
        'registerValidate' => true,
        'registerCaptcha' => true,
        'passwordLength' => intval($_G['setting']['pwlength']),
        'passwordStrength' => array(),
        'registerType' => 0,
        'isNeedTransition' => true
    ),
    'passport' => array(
        'offiaccountOpen' => false,
        'miniprogramOpen' => false
    ),
    'paycenter' => array(
        'wxpayClose' => false,
        'wxpayIos' => false,
        'wxpayMchpayClose' => false
    ),
    'setAttach' => array(
        'supportImgExt' => 'png,jpg,gif,jpeg',
        'supportFileExt' => $_G['group']['attachextensions'],
        'supportMaxDownloadNum' => '',
        'supportMaxSize' => intval($_G['group']['maxattachsize']) / 1024,
        'supportMaxUploadAttachmentNum' => 10
    ),
    'qcloud' => array(),
    'setCash' => array(
        'cashRate' => 0,
        'cashMinSum' => 0
    ),
    'other' => array(
        'countThreads' => C::t('#zhaisoul_dzq_api#forum_thread_API')->count_by_displayorder(0),
        'countPosts' => C::t('forum_post')->count_by_invisible(0, 0),
        'countUsers' => C::t('common_member')->count(),
        'canEditUserGroup' => false,
        'canEditUserStatus' => false,
        'canCreateThreadInCategory' => true,
        'canViewThreads' => true,
        'canFreeViewPaidThreads' => true,
        'canCreateDialog' => true,
        'canInviteUserScale' => true,
        'canInsertThreadAttachment' => true,
        'canInsertThreadPaid' => true,
        'canInsertThreadVideo' => false,
        'canInsertThreadImage' => true,
        'canInsertThreadAudio' => false,
        'canInsertThreadGoods' => false,
        'canInsertThreadPosition' => true,
        'canInsertThreadRedPacket' => false,
        'canInsertThreadReward' => false,
        'canInsertThreadAnonymous' => false,
        'canInsertThreadVote' => true,
        'initializedPayPassword' => false,
        'createThreadWithCaptcha' => false,
        'publishNeedBindPhone' => false,
        'publishNeedBindWechat' => false,
        'disabledChat' => false
    ),
    'lbs' => array(
        'lbs' => false,
        'qqLbsKey' => ""
    ),
    'ucenter' => array(
        'ucenter' => false
    ),
    'agreement' => array(
        'privacy' => false,
        'privacyContent' => "",
        'register' => true,
        'registerContent' => $_G['setting']['bbrulestxt']
    )
);

if($_G['uid']) {
    $arr['user'] = array(
        'userId' => $_G['uid'],
        'registerTime' => dgmdate($_G['regdate']),
        'groups' => array(
            0 => array(
                'color' => $_G['group']['color'],
                'days' => 0,
                'default' => false,
                'description' => '',
                'fee' => 0.00,
                'icon' => $_G['group']['icon'],
                'id' => intval($_G['group']['groupid']),
                'isCommission' => false,
                'isDisplay' => false,
                'isPaid' => 0,
                'isSubordinate' => 0,
                'level' => 0,
                'name' => $_G['group']['grouptitle'],
                'notice' => "",
                'pivot' => array(
                    'expirationTime' => null,
                    'groupId' => intval($_G['group']['groupid']),
                    'userId' => intval($_G['uid'])
                ),
                'scale' => '5',
                'type' => ''
            )
        )
    );
}

Utils::outPut(0, '接口调用成功', $arr);