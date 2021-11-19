<?php

class plugin_zhaisoul_dzq_api {
    function common() {
        //api::common();
    }

    //魔法钩子
    function zhaisoul_dzq_api_message($param) {
        global $_G;
        include_once DISCUZ_ROOT.'./source/plugin/zhaisoul_dzq_api/tools.class.php';

        loadcache('forums');
        $forum_info = $_G['cache']['forums'];
        $plugin = $_G['cache']['plugin']['zhaisoul_dzq_api'];

        if($param['param'][0] == 'forum_nonexistence') {
            Utils::outPut(-7076, "参数错误！分类不存在");
        } else if($param['param'][0] == 'thread_nonexistence') {
            Utils::outPut(-4004, "参数错误！主题不存在");
        } else if ($param['param'][0] == 'post_newthread_succeed') {
            $thread = get_thread_by_tid($param['param'][2]['tid']);
            require_once libfile('class/thread', 'plugin/zhaisoul_dzq_api');

            $categoryids = dunserialize($plugin['forums']);
            //发布主题成功后，清理掉主页列表缓存
            memory('rm', 'dzq_thread_list_fid:'.serialize($categoryids));
            //清理掉所在版块的版块列表缓存
            memory('rm', 'dzq_thread_list_fid:'.serialize($thread['fid']));

            Utils::outPut(0, '主题发布成功', outPutThread($thread, $_G['uid'], $forum_info));
        } else if ($param['param'][0] == 'post_reply_succeed') {
            $post = get_post_by_pid($param['param'][2]['pid']);
            $post_arr = array(
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
                'threadId' => $param['param'][2]['tid'],
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

            Utils::outPut(0, '主题发布成功', $post_arr);
        } else if($param['param'][0] == 'recommend_succeed' || $param['param'][0] == 'recommend_duplicate' || $param['param'][0] == 'recommend_daycount_succeed' || $param['param'][0] == 'recommend_outoftimes') {
            $user_count = intval(C::t('#zhaisoul_dzq_api#forum_memberrecommend_API')->count_by_tid($_GET['tid']));
            $post_count = intval(C::t('forum_post')->count_visiblepost_by_tid($_GET['tid']));
            $isLike = true;
            if($param['param'][0] == 'recommend_outoftimes' || $param['param'][0] == 'recommend_duplicate') {
                $isLike = false;
            }

            $arr = array(
                'canLike' => $isLike,
                'canFavorite' => true,
                'content' => '',
                'isApproved' => 1,
                'isFavorite' => false,
                'isFirst' => true,
                'isLiked' => $isLike,
                'likeCount' => $user_count,
                'likePayCount' => $user_count,
                'redPacketAmount' => 0,
                'replyCount' => $post_count - 1,
                'rewards' => 0,
                'threadId' => intval($_GET['tid'])
            );

            Utils::outPut(0, '接口请求成功', $arr);
        } else if(in_array($param['param'][0], array('login_question_empty', 'login_invalid', 'login_question_invalid', 'login_password_invalid'))) {
            Utils::outPut(-7040, '登录失败: '.$param['param'][0]);
        } else if(in_array($param['param'][0], array('login_succeed', 'location_login_succeed_mobile', 'location_login_succeed', 'login_succeed_inactive_member', 'login_succeed_password_change'))) {
            $payload = array(
                'aud' => '',
                'jti' => md5(uniqid('JWT').time()),
                'iat' => TIMESTAMP,
                'nbf' => TIMESTAMP,
                'exp' => TIMESTAMP + 2592000,
                'sub' => $_G['uid'],
                'scopes' => array(
                    0 => null
                )
            );
            $token = Utils::getToken($payload);

            $arr = array(
                'tokenType' => 'Bearer',
                'expiresIn' => 2592000,
                'accessToken' => $token,
                'refreshToken' => '',
                'isMissNickname' => 0,
                'avatarUrl' => avatar($_G['uid'], 'middle', true),
                'userStatus' => 0,
                'userId' => $_G['uid'],
                'uid' => $_G['uid']
            );

            Utils::outPut(0, '登录成功', $arr);
        } else {
            Utils::outPut(-5003, '接口错误: '.$param['param'][0]);
        }
    }
}

class plugin_zhaisoul_dzq_api_forum extends plugin_zhaisoul_dzq_api {

}

class plugin_zhaisoul_dzq_api_member extends plugin_zhaisoul_dzq_api {

}

class plugin_zhaisoul_dzq_api_plugin extends plugin_zhaisoul_dzq_api_forum {

}

class mobileplugin_zhaisoul_dzq_api extends plugin_zhaisoul_dzq_api {
    public function zhaisoul_dzq_api_message($param) {
        parent::zhaisoul_dzq_api_message($param); // TODO: Change the autogenerated stub
    }
}

class mobileplugin_zhaisoul_dzq_api_forum extends mobileplugin_zhaisoul_dzq_api {

}

class mobileplugin_zhaisoul_dzq_api_member extends mobileplugin_zhaisoul_dzq_api {

}

class mobileplugin_zhaisoul_dzq_api_plugin extends mobileplugin_zhaisoul_dzq_api_forum {

}