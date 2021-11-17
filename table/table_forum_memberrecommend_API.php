<?php

class table_forum_memberrecommend_API extends table_forum_memberrecommend {
    function fetch_all_user_by_tid($tid, $start = 0, $limit = 10) {
        return DB::fetch_all('SELECT * FROM %t WHERE tid=%d'.DB::limit($start, $limit), array($this->_table, $tid));
    }

    function count_by_tid($tid) {
        return DB::fetch_first('SELECT COUNT(*) FROM %t WHERE tid=%d', array($this->_table, $tid));
    }
}