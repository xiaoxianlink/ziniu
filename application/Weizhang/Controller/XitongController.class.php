<?php

namespace Weizhang\Controller;

use Common\Controller\AdminbaseController;
use Think\Model;
use Think\Log;

class XitongController extends AdminbaseController {

	function stats_view(){
		$query_port = $_POST ['query_port'];
		$query_start = $_POST ['query_start'];
		$query_end = $_POST ['query_end'];
		if (IS_POST) {
			$_SESSION ['stats'] = '';
			$_SESSION ['stats'] ['query_port'] = $query_port;
			$_SESSION ['stats'] ['query_start'] = $query_start;
			$_SESSION ['stats'] ['query_end'] = $query_end;
		} else {
			$query_port = $_SESSION ['stats'] ['query_port'];
			$query_start = $_SESSION ['stats'] ['query_start'];
			$query_end = $_SESSION ['stats'] ['query_end'];
		}
		$this->assign ( "query_port", $query_port );
		$this->assign ( "query_start", $query_start );
		$this->assign ( "query_end", $query_end );
		
		$where = ' 1 = 1 ';
		if (!empty ( $query_port )) {
			$where = $where . " and port like '%$query_port%'";
		}
		if (!empty ( $query_start )) {
			$start = date('Ymd', strtotime($query_start));
			$where = $where . " and day_time >= $start";
		}
		if (!empty ( $query_end )) {
			$end = date('Ymd', strtotime($query_end));
			$where = $where . " and day_time <= $end";
		}
		
		$model = M();
		$count = $model->table ( "cw_endorsement_stats as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $model->field ( "@rownum:=@rownum+1 AS iid,a.*" )->table ( "(SELECT @rownum:=0) r,cw_endorsement_stats as a" )->where ( $where )->order ( "day_time desc" )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
}