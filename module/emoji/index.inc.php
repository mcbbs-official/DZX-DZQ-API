<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/** 用于获取表情用的接口 */


include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';

$emoji_list = memory('get', 'emoji_api');

if(!is_array($emoji_list) || empty($emoji_list)) {
    foreach (C::t('forum_imagetype')->fetch_all_by_type('smiley', 1) as $type) {
        foreach (C::t('common_smiley')->fetch_all_by_type_code_typeid('smiley', $type['typeid']) as $smiley) {
            $emoji_list[] = array(
                'id' => $smiley['id'],
                'category' => $smiley['typeid'],
                'code' => $smiley['code'],
                'createdAt' => dgmdate(TIMESTAMP, DATE_ISO8601),
                'order' => $type['displayorder'],
                'updatedAt' => dgmdate(TIMESTAMP, DATE_ISO8601),
                'url' => $_G['siteurl'].'static/image/smiley/' . $type['directory'] . '/' . $smiley['url']
            );
        }
    }
    //表情设置3600秒（1个小时）的缓存时间
    memory('set', 'dzq_emoji_api', $emoji_list, 3600);
}


Utils::outPut(0, '资源请求成功', $emoji_list);