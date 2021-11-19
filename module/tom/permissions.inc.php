<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/tools.class.php';

$arr = array(
    'createThread' => array(
        'enable' => true,
        'desc' => '发布帖子'
    ),
    'insertImage' => array(
        'enable' => true,
        'desc' => '插入图片'
    ),
    'insertAttachment' => array(
        'enable' => true,
        'desc' => '插入附件'
    )
);

Utils::outPut(0, '接口调用成功', $arr);