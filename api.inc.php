<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT.'./source/plugin/zhaisoul_dzq_api/tools.class.php';


if(empty($_SERVER['HTTP_AUTHORIZATION'])) {
    //Apache获取Token
    $Header = getallheaders();
    $Token = $Header['Authorization'];
} else {
    //Nginx获取Token
    $Token = $_SERVER['HTTP_AUTHORIZATION'];
}

$is_login = false;

if($Token) {
    //获取payload
    $payload = Utils::verifyToken(str_replace('Bearer ', '', $Token));

    if($payload) {
        require_once libfile('function/member');
        $member = getuserbyuid($payload['sub'], 1);
        setloginstatus($member, 300);
        $is_login = true;
    } else if(!in_array($_GET['module'], array('users/username.login', 'forum', 'emoji', 'thread.list', 'thread.stick', 'categories'))) {
        Utils::outPut(-4011, "无效的Token");
    }
}

//以下模块必须要Token才可正常使用
$need_token_module = array(
    'thread.create',
    'post.create',
    'post.update',
    'unreadnotification'
);
$origin_path = explode('/', $_GET['module']);
$origin_module = count($origin_path) != 1 ? explode('.', trim($origin_path[1])) : explode('.', trim($_GET['module']));
$module = $origin_module[0];
$ac = !empty($origin_module[1]) ? $origin_module[1] : 'index';

if(in_array($origin_module, $need_token_module) && !$is_login) {
    Utils::outPut(-3001, "需要登录后使用");
}
if(count($origin_path) == 1) {
    $file = DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/module/' . $module . '/' . $ac . '.inc.php';
} else {
    $file = DISCUZ_ROOT . './source/plugin/zhaisoul_dzq_api/module/'. $origin_path[0] . '/' . $module . '/' . $ac . '.inc.php';
}

if(file_exists($file)){
    try {
        require_once $file;
    } catch (Exception $exception) {
        Utils::outPut(-5002, "后端异常：".$exception);
    }
}else{
    Utils::outPut(-2001, "参数错误！模块不存在");
}