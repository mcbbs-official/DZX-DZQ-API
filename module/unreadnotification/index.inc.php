<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';



$arr = array(
    'dialogNotifications' => intval($_G['member']['newpm']),
    'unreadNotifications' => intval($_G['member']['newprompt']),
    'typeUnreadNotifications' => array()
);

Utils::outPut(0, '接口调用成功', $arr);