<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_thread_API extends table_forum_thread {
	function get_thread_with_coverimg_by_fid($fid) {
		global $_G;
		$threadlist = dunserialize(memory('get', 'api_news', $this->_pre_cache_key));
		if(!$threadlist) {
			$_order = "displayorder DESC, lastpost DESC";
			$filterarr = array("inforum" => $fid);
			$threadlist = $this->fetch_all_search($filterarr, 0, 0, 10, $_order, '', '');
			foreach($threadlist as &$thread) {
				$tableid = getattachtableid($thread['tid']);
				$threadimg = DB::fetch_all("SELECT * FROM ".DB::table('forum_attachment_'.$tableid)." WHERE tid ='".$thread["tid"]."' and isimage=1 ORDER BY aid ASC");
				if($threadimg) {
					$thread['img'] = $_G['setting']['attachurl'].'forum/'.$threadimg[0]['attachment'];
				}
				$post = C::t('forum_post')->fetch_threadpost_by_tid_invisible($thread['tid']);
				$thread['summary'] = substr($post['message'], 0, 150, 'utf-8');
				$thread['authorAvatar'] = avatar($thread['authorid'], "middle", true);
			}
			memory('set', $this->_pre_cache_key.'api_news', serialize($threadlist), 600);
		}
		return $threadlist;
	}

    function count_by_authorid_displayorder($authorid, $displayorder = null, $dglue = '=', $closed = null, $subject = '', $start = 0, $limit = 0, $replies = null, $fid = null, $rglue = '>=', $tableid = 0) {

        $parameter = array($this->get_table_name($tableid));
        $wherearr = array();
        if(!empty($authorid)) {
            $authorid = dintval($authorid, true);
            $parameter[] = $authorid;
            $wherearr[] = is_array($authorid) && $authorid ? 'authorid IN(%n)' : 'authorid=%d';
        }
        if($fid !== null) {
            $fid = dintval($fid, true);
            $parameter[] = $fid;
            $wherearr[] = is_array($fid) && $fid ? 'fid IN(%n)' : 'fid=%d';
        }
        if(getglobal('setting/followforumid')) {
            $parameter[] = getglobal('setting/followforumid');
            $wherearr[] = 'fid<>%d';
        }
        if($displayorder !== null) {
            $parameter[] = $displayorder;
            $dglue = helper_util::check_glue($dglue);
            $wherearr[] = "displayorder{$dglue}%d";
        }
        if($closed !== null) {
            $parameter[] = $closed;
            $wherearr[] = "closed=%d";
        }
        if($replies !== null) {
            $parameter[] = $replies;
            $rglue = helper_util::check_glue($rglue);
            $wherearr[] = "replies{$rglue}%d";
        }
        if(!empty($subject)) {
            $parameter[] = '%'.$subject.'%';
            $wherearr[] = "subject LIKE %s";
        }
        $wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
        return DB::result_first("SELECT COUNT(*) FROM %t $wheresql ORDER BY dateline DESC ".DB::limit($start, $limit), $parameter, $this->_pk);
    }

    function count_by_fid_displayorder_page($fids, $displayorder = 0, $glue = '>=', $order = 'lastpost', $sort = 'DESC') {
        $parameter = array($this->get_table_name());
        $wherearr = array();
        $fids = dintval($fids, true);
        if(!empty($fids)) {
            $parameter[] = $fids;
            $wherearr[] = is_array($fids) && $fids ? 'fid IN(%n)' : 'fid=%d';
        }
        if($displayorder !== null) {
            $parameter[] = $displayorder;
            $dglue = helper_util::check_glue($glue);
            $wherearr[] = "displayorder{$dglue}%d";
        }
        $ordersql = !empty($order) ? ' ORDER BY '.DB::order($order, $sort) : '';
        $wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
        return DB::result_first("SELECT COUNT(*) FROM %t $wheresql $ordersql ", $parameter, $this->_pk);
    }
}