<?php

namespace Admin_new\Controller;

use Common\Controller\AdminbaseController;
use Think\Model;
use Think\Log;
use Weixin\Controller\IndexController;
use Weizhang\Controller\JiaoyiController;
include_once 'application/Weixin/Conf/config.php';

class XitongController extends AdminbaseController {
	protected $xitong_model;
	public function _initialize() {
		parent::_initialize ();
		$_SESSION ['dingyue'] = '';
		$_SESSION ['fuwu'] = '';
		$_SESSION ['jiao'] = '';
		$this->xitong_model = D ( "Common/users" );
	}
	function city() {
		$province = $_POST ["province_name"];
		$this->assign ( "cityname", $province );
		$area = $_POST ["wei_range"];
		$vcode = $_GET ['vcode'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['province_name1'] = $province;
			$_SESSION ['xitong'] ['wei_range1'] = $area;
		} else {
			$province = $_SESSION ['xitong'] ['province_name1'];
			$area = $_SESSION ['xitong'] ['wei_range1'];
		}
		if (empty ( $province )) {
			$where = "";
		} else {
			$where = " (province like '%$province%' or abbreviation like '%$province%')";
		}
		if (! empty ( $vcode )) {
			$where = " is_dredge=$vcode-1";
			$this->assign ( "vcode", $vcode );
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.sf_id $_order,a.level asc";
		$this->assign ( 'order', $_order );
		$count = $this->xitong_model->table ( "cw_region as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.sf_id,a.level,a.province,a.abbreviation,a.code,a.acode,a.city,a.nums,a.engine_nums,a.frame_nums,a.c_engine_nums,a.c_frame_nums,a.gb_code_c,a.cxy_frame_nums,a.cxy_engine_nums,a.is_dredge " )->table ( "(SELECT @rownum:=0) r,cw_region as a" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$is_dredge = $this->xitong_model->field ( "count(*) as num" )->table ( "cw_region" )->where ( " is_dredge=0" )->select ();
		$no_dredge = $this->xitong_model->field ( "count(*) as nums" )->table ( "cw_region" )->where ( " is_dredge=1" )->select ();
		$all_dregde = $this->xitong_model->field ( "count(*) as numer" )->table ( "cw_region" )->where ( "is_dredge=0 or is_dredge=1" )->select ();
		$this->assign ( "is_num", $is_dredge );
		$this->assign ( "no_num", $no_dredge );
		$this->assign ( "all_dregde", $all_dregde );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function city_add() {
		$cxy_code = $_POST ['cxy_code'];
		$acode = $_POST ['acode'];
		$code = $_POST ['code'];
		$level = 2;
		$province = $_POST ['province'];
		$is_dredge = isset ( $_POST ['is_dredge'] ) ? $_POST ['is_dredge'] : 0;
		$city = $_POST ['city'];
		$abbreviation = $_POST ['abbreviation'];
		$nums = $_POST ['nums'];
		$cxy_engine_nums = $_POST ['cxy_engine_nums'];
		$cxy_frame_nums = $_POST ['cxy_frame_nums'];
		$engine_nums = $_POST ['engine_nums'];
		$frame_nums = $_POST ['frame_nums'];
		$c_engine_nums = $_POST ['c_engine_nums'];
		$c_frame_nums = $_POST ['c_frame_nums'];
		if (! empty ( $province )) {
			$roles = $this->xitong_model->execute ( "insert into cw_region (level,province,city,abbreviation,nums,engine_nums,frame_nums,c_engine_nums,c_frame_nums,cxy_engine_nums,cxy_frame_nums,is_dredge,orders,gb_code_c,code,acode)  values('$level','$province','$city','$abbreviation','$nums','$engine_nums','$frame_nums','$c_engine_nums','$c_frame_nums','$cxy_engine_nums','$cxy_frame_nums',$is_dredge,'50','$cxy_code','$code','$acode')" );
			if ($roles > 0) {
				$this->success ( "添加成功！" );
			} else {
				$this->error ( "添加失败！" );
			}
		} else {
			$this->error ( "添加失败！" );
		}
	}
	function city_update() {
		$id = $_REQUEST ['id'];
		$is_dredge = isset ( $_REQUEST ['is_dredge'] ) ? $_REQUEST ['is_dredge'] : 0;
		if ($is_dredge == 0) {
			if ($this->xitong_model->execute ( "update cw_region set is_dredge=1 where id=$id" ) > 0) {
				$this->ajaxReturn ( 1 );
			}
		} elseif ($is_dredge == 1) {
			if ($this->xitong_model->execute ( "update cw_region  set is_dredge=0 where id=$id" )) {
				$this->ajaxReturn ( 1 );
			}
		}
	}
	
	function daima() {
		$code = $_POST ['wei_code'];
		$range = $_POST ['wei_range'];
		$state = $_POST ['wei_state'];
		$type = $_POST ['wei_type'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['wei_code2'] = $code;
			$_SESSION ['xitong'] ['wei_range2'] = $range;
			$_SESSION ['xitong'] ['wei_state2'] = $state;
			$_SESSION ['xitong'] ['wei_type2'] = $type;
		} else {
			$code = $_SESSION ['xitong'] ['wei_code2'];
			$range = $_SESSION ['xitong'] ['wei_range2'];
			$state = $_SESSION ['xitong'] ['wei_state2'];
			$type = $_SESSION ['xitong'] ['wei_type2'];
		}
		$array = array (
				$code,
				$range,
				$state,
				$type 
		);
		$this->assign ( 'array', $array );
		$where = " 1=1 ";
		if (! empty ( $range )) {
			$where .= "";
		}
		if (! empty ( $state )) {
			$where .= " and points='$state'-1 ";
		}
		if (! empty ( $type )) {
			$where .= " and state='$type'-1 ";
		}
		if (! empty ( $code )) {
			$where .= " and code like '%$code%' ";
		}
		if (empty ( $_order )) {
			$_order = 'asc';
		}
		$order = "a.code $_order";
		$this->assign ( 'order', $_order );
		$count = $this->xitong_model->table ( "cw_violation" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.code,a.money,a.points,a.content,a.explain,a.gist,a.state,a.area" )->table ( "(SELECT @rownum:=0) r,cw_violation as a" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$r_model = M ( "region" );
		foreach ( $roles as $k => $v ) {
			$areas = explode ( ',', $v ['area'] );
			if (in_array ( '0', $areas )) {
				$roles [$k] ['city'] = '全国';
			} else {
				$area_s = '';
				foreach ( $areas as $c ) {
					$area_s .= "'{$c}',";
				}
				$area_s = rtrim ( $area_s, ',' );
				$r_list = $r_model->where ( "id in ($area_s)" )->select ();
				$citys = '';
				foreach ( $r_list as $c ) {
					$citys .= "{$c['city']},";
				}
				$citys = rtrim ( $citys, "," );
				$roles [$k] ['city'] = $citys;
			}
		}
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_a = $this->xitong_model->field ( "count(state) as sums" )->table ( "cw_violation" )->group ( "state" )->select ();
		$state_0 = $roles_a [0] [sums];
		$state_1 = $roles_a [1] [sums];
		$state_num = $state_0 + $state_1;
		$this->assign ( "array_num", array (
				$state_0,
				$state_1,
				$state_num 
		) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	
	function dai_add() {
		$id = $_POST ['id'];
		$code = $_POST ['code'];
		$money = $_POST ['money'];
		$points = $_POST ['points'];
		$content = $_POST ['content'];
		$explain = $_POST ['explain'];
		$gist = $_POST ['gist'];
		$area = $_POST ['area'];
		$state = $_POST ['state'];
		if ($id == '0') {
			$data = array (
					"code" => $code,
					"money" => $money,
					"points" => $points,
					"content" => $content,
					"explain" => $explain,
					"gist" => $gist,
					"area" => $area,
					"state" => strtr ( $state, "，", "," ) 
			);
			$vio_model = M ( "violation" );
			
			if ($vio_model->add ( $data )) {
				$this->success ( "添加成功！" );
			} else {
				$this->error ( "添加失败！" );
			}
		} else {
			$data = array (
					"code" => $code,
					"money" => $money,
					"points" => $points,
					"content" => $content,
					"explain" => $explain,
					"gist" => $gist,
					"area" => str_replace ( "，", ",", $area ),
					"state" => $state 
			);
			$vio_model = M ( "violation" );
			
			if ($vio_model->where ( "id='$id'" )->save ( $data )) {
				$this->success ( "修改成功！" );
			} else {
				$this->error ( "修改失败！" );
			}
		}
	}
	function dai_update() {
		$id = intval ( I ( "get.id" ) );
		$roles = $this->xitong_model->query ( "select * from cw_violation where id=$id" );
		$this->assign ( 'roles', $roles );
		$code = $_POST ['code'];
		$money = $_POST ['money'];
		$points = $_POST ['points'];
		$content = $_POST ['content'];
		$explain = $_POST ['explain'];
		$gist = $_POST ['gist'];
		$state = $_POST ['state'];
		$dai_id = $_POST ['id'];
		$area = str_replace ( "，", ",", $_POST ['area'] );
		if (! empty ( $code )) {
			if ($this->xitong_model->execute ( "update cw_violation set code='$code',money='$money',points=$points,content='$content',gist='$gist',state='$state',`explain`=$explain,`area`=$area where id=$dai_id" ) > 0) {
				$this->success ( "修改成功！", U ( 'Xitong/daima' ) );
			} else {
				$this->error ( "修改失败！" );
			}
		}
		$this->display ();
	}
	function dai_delete() {
		$id = intval ( I ( "get.id" ) );
		if ($this->xitong_model->execute ( "delete from cw_violation where id=$id" ) > 0) {
			$this->success ( "删除成功！" );
		} else {
			$this->error ( "删除失败！" );
		}
	}
	function jilu() {
		$number = $_POST ['number'];
		$city = $_POST ['city'];
		$state = $_POST ['state'];
		$time_start = strtotime ( $_POST ['time_start'] );
		$time_end = strtotime ( $_POST ['time_end'] );
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['number3'] = $number;
			$_SESSION ['xitong'] ['city3'] = $city;
			$_SESSION ['xitong'] ['state3'] = $state;
			$_SESSION ['xitong'] ['time_start3'] = $time_start;
			$_SESSION ['xitong'] ['time_end3'] = $time_end;
		} else {
			$number = $_SESSION ['xitong'] ['number3'];
			$city = $_SESSION ['xitong'] ['city3'];
			$state = $_SESSION ['xitong'] ['state3'];
			$time_start = $_SESSION ['xitong'] ['time_start3'];
			$time_end = $_SESSION ['xitong'] ['time_end3'];
		}
		$this->assign ( "array", array (
				$number,
				$city,
				$state,
				empty ( $time_start ) ? '' : date ( "Y/m/d H:i:s", $time_start ),
				empty ( $time_end ) ? '' : date ( "Y/m/d H:i:s", $time_end ) 
		) );
		$where = "1=1";
		if (! empty ( $number )) {
			$where .= " and  b.license_number like '%$number%' ";
		}
		if (! empty ( $city )) {
			$where .= "";
		}
		if (! empty ( $state )) {
			$where .= " and a.is_manage='$state'-1 ";
		}
		if (! empty ( $time_start )) {
			$where .= " and a.time >$time_start ";
		}
		if (! empty ( $time_end )) {
			$where .= " and a.time <$time_end ";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->xitong_model->table ( "cw_endorsement as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_violation as v on v.code=a.code" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,b.license_number,a.time,a.area,a.code,a.money,a.points,a.query_no,a.certificate_no,a.address,a.content,a.office,a.is_manage,a.id,v.code as v_code,v.money as v_money,v.points as v_points,v.content as v_content" )->table ( "(SELECT @rownum:=0) r,cw_endorsement as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_violation as v on v.code=a.code" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_a = $this->xitong_model->field ( "count(id) as numbers" )->table ( "cw_endorsement" )->select ();
		$this->assign ( 'sums', $roles_a );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function log() {
		$number = $_POST ['log_number'];
		$time_start = strtotime ( $_POST ['time_start'] );
		$time_end = strtotime ( $_POST ['time_end'] );
		$state = $_POST ['log_state'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['log_number4'] = $number;
			$_SESSION ['xitong'] ['time_start4'] = $time_start;
			$_SESSION ['xitong'] ['time_end4'] = $time_end;
			$_SESSION ['xitong'] ['log_state4'] = $state;
		} else {
			$number = $_SESSION ['xitong'] ['log_number4'];
			$time_start = $_SESSION ['xitong'] ['time_start4'];
			$time_end = $_SESSION ['xitong'] ['time_end4'];
			$state = $_SESSION ['xitong'] ['log_state4'];
		}
		$this->assign ( "array", array (
				$number,
				$state,
				empty ( $time_start ) ? '' : date ( "Y-m-d H:i:s", $time_start ),
				empty ( $time_end ) ? '' : date ( "Y-m-d H:i:s", $time_end ) 
		) );
		$where = "1=1";
		if (! empty ( $number )) {
			$where .= " and b.license_number like '%$number%'";
		}
		if ($state != null && $state != '') {
			if ($state == '0') {
				$where .= " and l.state = 1";
			} else {
				$where .= " and l.state = 2";
			}
		}
		if (! empty ( $time_start )) {
			$where .= " and l.c_time >$time_start ";
		}
		if (! empty ( $time_end )) {
			$where .= " and l.c_time <$time_end ";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "l.c_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->xitong_model->table ( "cw_endorsement_log as l" )->join ( "cw_endorsement as a on a.id=l.end_id" )->join ( "cw_car as b on a.car_id=b.id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,b.license_number,a.time,a.area,a.code,a.money,a.points,a.query_no,a.certificate_no,a.address,a.content,a.office,a.is_manage,a.manage_time,a.create_time,a.close_query_no,l.state as l_state,l.c_time as l_c_time,l.type as l_type" )->table ( "(SELECT @rownum:=0) r,cw_endorsement_log as l" )->join ( "cw_endorsement as a on a.id=l.end_id" )->join ( "cw_car as b on a.car_id=b.id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_a = $this->xitong_model->query ( "select count(id) as numbers from cw_endorsement_log " );
		$this->assign ( 'sums', $roles_a );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function select() {
		$port = $_POST ['port'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['port5'] = $port;
		} else {
			$port = $_SESSION ['xitong'] ['port5'];
		}
		$this->assign ( "port", $port );
		if (empty ( $port )) {
			$where = '';
		} else {
			$where = "port like '%$port%'";
		}
		$count = $this->xitong_model->table ( "cw_code" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,a.port,a.code,a.content " )->table ( "(SELECT @rownum:=0) r,cw_code as  a" )->where ( $where )->order ( "a.port asc,a.code asc" )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function role() {
		$this->display ();
	}
	function user() {
		$name = $_POST ['province_name'];
		if (empty ( $name )) {
			$where = "";
		} else {
			$where = " user_login like '%$name%'";
		}
		$this->assign ( "name", $name );
		$count = $this->xitong_model->table ( "cw_users as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,a.id,a.user_login,a.create_time,a.last_login_time,a.user_role " )->table ( "(SELECT @rownum:=0) r,cw_users as a" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->display ();
	}
	function user_add() {
		$username = $_POST ['username'];
		$password_one = md5 ( $_POST ['password_one'] );
		$password_two = md5 ( $_POST ['password_two'] );
		$type = $_POST ['type'];
		if (empty ( $username )) {
			$this->error ( "用户名不能为空" );
		} elseif (empty ( $password_one )) {
			$this->error ( "密码不能为空" );
		}
		$t_u_id = $this->xitong_model->field ( "a.id" )->table ( "cw_users as a" )->where ( "user_login='$username'" )->select ();
		if (! empty ( $t_u_id )) {
			$this->error ( "用户名已注册" );
		} elseif ($password_one !== $password_two) {
			$this->error ( "两次密码不一致" );
		}
		$create_time = date ( 'Y-m-d H:i:s' );
		/*
		 * if( $this->xitong_model->execute("insert into cw_users (user_login,user_pass,user_role,create_time) values('$username','$password_one','$type','$create_time')")>0){ $this->success("添加成功",U('Xitong/user')); }
		 */
		if ($this->xitong_model->add ( array (
				'user_login' => $username,
				'user_pass' => $password_one,
				'user_role' => $type,
				'create_time' => $create_time 
		) ) - count () > 0) {
			$this->success ( "添加成功", U ( 'Xitong/user' ) );
		}
	}
	function user_password() {
		$password_old = $_POST ['password_old'];
		$password_one = $_POST ['password_one'];
		$password_two = $_POST ['password_two'];
		echo $id = intval ( I ( 'get.id' ) );
	}
	function user_delete() {
		$id = intval ( I ( 'get.id' ) );
		if ($this->xitong_model->execute ( "delete from cw_users where id=$id" ) > 0) {
			$this->success ( "删除成功" );
		}
	}
	function user_role() {
	}
	function window() {
		$window_number = $_POST ['window_number'];
		$type = $_POST ['window_type'];
		if (IS_POST) {
			$_SESSION ['xitong'] = '';
			$_SESSION ['xitong'] ['window_number6'] = $window_number;
			$_SESSION ['xitong'] ['window_type6'] = $type;
		} else {
			$window_number = $_SESSION ['xitong'] ['window_number6'];
			$type = $_SESSION ['xitong'] ['window_type6'];
		}
		$this->assign ( "type", $type );
		$this->assign ( "window_number", $window_number );
		$where = "(t.state = 0 or t.state = 3 or t.state = 4)";
		/*
		 * if (empty ( $window_number ) && empty ( $type )) { $where = ""; } elseif (! empty ( $window_number ) && empty ( $type )) { $where = " a.id='$window_number'"; } elseif (empty ( $window_number ) && ! empty ( $type )) { $where = " b.order_status='$type'"; } elseif (! empty ( $window_number ) && ! empty ( $type )) { $where = " a.id='$window_number' and b.order_status='$type'"; }
		 */
		if (! empty ( $window_number )) {
			$where .= " and s.services_sn like '%$window_number%' ";
		}
		if (! empty ( $type )) {
			if ($type == 1) {
				$type = 0;
			}
			$where .= " and t.state='$type' ";
		}
		$count = $this->xitong_model->table ( "cw_turn_order as t" )->join ( "cw_order as b on b.id=t.order_id" )->join ( "cw_car as c on b.car_id=c.id" )->join ( "cw_endorsement as d on b.endorsement_id=d.id" )->join ( "cw_services as s on s.id = b.services_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->xitong_model->field ( "@rownum:=@rownum+1 AS iid,t.id,b.order_sn,s.id as s_id,s.phone,t.c_time,b.last_time,t.state,b.money,b.pay_money,b.pay_sn,c.license_number,c.frame_number,c.engine_number,d.code,d.time,d.area,d.points,d.money,t.l_time,s.services_sn" )->table ( "(SELECT @rownum:=0) r,cw_turn_order as t" )->join ( "cw_order as b on b.id=t.order_id" )->join ( "cw_car as c on b.car_id=c.id" )->join ( "cw_endorsement as d on b.endorsement_id=d.id" )->join ( "cw_services as s on s.id = b.services_id" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		foreach ( $roles as $k => $v ) {
			$roles [$k] ['so_id'] = $v ['order_sn'] . substr ( $v ['c_time'], - 2 ) . $v ['s_id'];
			$time = '--';
			if ($v ['state'] == 0) {
				$time = jishi1 + $v ['l_time'];
			} else if ($v ['state'] == 3) {
				$time = jishi2 + $v ['l_time'];
			}
			$roles [$k] ['tmr'] = $time;
		}
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->display ();
	}
	function shuju() {
		$type = $_POST ['xi_type'];
		$this->assign ( "type", $type );
		$this->display ();
	}
	public function manage() {
		$state = $_REQUEST ['state'];
		$id = $_REQUEST ['id'];
		
		$turn_order_model = M ( "turn_order" );
		$info = $turn_order_model->where ( "id='$id' and state in (0,3,4)" )->find ();
		
		if(empty($info)){
			$this->redirect ( "Xitong/window" );
		}
		
		$latest_info = $turn_order_model->where ( "order_id='{$info['order_id']}'" )->order("id desc")->find ();
		if($latest_info['id'] <> $info['id']){
			$this->redirect ( "Xitong/window" );
		}
		
		$model = M ();
		$v = $model->field ( "a.id as order_id,a.pay_sn,a.order_sn,b.id as car_id,b.license_number,c.time,c.area,c.code,c.money,c.points,a.last_time,a.pay_money,a.order_status,a.pay_type,d.id as user_id,d.phone,a.services_id,a.money as order_money,a.endorsement_id,a.user_id" )->table ( "cw_order as a" )->join ( "cw_car as b on a.car_id=b.id" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "a.id = '{$info['order_id']}'" )->find ();
		
		$to_list = $turn_order_model->field ( "tos.id,tos.c_time,tos.state,tos.l_time,tos.services_id as s_id,tos.money" )->table ( "cw_turn_order as tos" )->where ( "tos.order_id = '{$info['order_id']}'" )->select ();
		$s_ids = array();
		foreach ( $to_list as $c => $p ) {
			if($p["s_id"] != null){
				$s_ids[] = $p["s_id"];
			}
		}
		
		$user_model = M ("User");
		$user = $user_model->where ( "id = '{$v['user_id']}'" )->find ();
		
		$order_model = M ( "Order" );
		switch ($state) {
			case 1 : // 办不了
				$fuwu = $this->find_fuwu($v["car_id"], $v["code"], $v["money"],$v["points"], $v["area"], $s_ids);
				if (! empty ( $fuwu )) {
					$data = array (
							"state" => 1,
							"l_time" => time () 
					);
					$turn_order_model->where ( "id='{$info['id']}'" )->save ( $data );
					$data = array (
							"order_id" => $v ['order_id'],
							"services_id" => $fuwu ['s_id'],
							"sod_id" => $fuwu ['so_id'],
							"so_type" => $fuwu ['so_type'],
							"money" => $fuwu ['so_money'],
							"state" => 0,
							"c_time" => time (),
							"l_time" => time () 
					);
					$turn_order_model->add ( $data );
					$data = array (
							"services_id" => $fuwu ['s_id'],
							"so_id" => $fuwu ['so_id'],
							"so_type" => $fuwu ['so_type']
					);
					$order_model->where ( "id='{$v['order_id']}'" )->save ( $data );
					
					$services_model = M ( "services" );
					$services_info = $services_model->where ( "id='{$fuwu['s_id']}'" )->find ();
					if (! empty ( $services_info )) {
						$data = array (
								"all_nums" => $services_info ['all_nums'] + 1 
						);
						$services_model->where ( "id='{$fuwu['s_id']}'" )->save ();
					}
					
					if($info['state'] == 3 or $info['state'] == 4){
						// 转钱
						$bank_model = M ( "bank" );
						$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
						if (! empty ( $bank_info_older )) {
							$data = array (
									"money" => ($bank_info_older ['money'] - $info['money']) > 0 ? ($bank_info_older ['money'] - $info['money']) : 0,
									"end_money" => ($bank_info_older ['end_money'] - $info['money']) > 0 ? ($bank_info_older ['end_money'] - $info['money']) : 0
							);
							$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
						}
						// 记录
						$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
						$data = array (
								"services_id" => $bank_info_older ['bank_id'],
								"income_money" => 0,
								"pay_money" => $info['money'],
								"end_money" => $bank_info_older ['end_money'],
								"user_money" => $bank_info_older ['user_money'],
								"money" => $bank_info_older ['money'],
								"order_id" => $v ['order_id'],
								"c_time" => time ()
						);
						$jl_model = M ( "services_jilu" );
						$jl_model->add ( $data );
					}
					/*
					$bank_info = $bank_model->where ( "bank_id='{$fuwu['s_id']}'" )->find ();
					if (! empty ( $bank_info )) {
						$data = array (
								"money" => $bank_info ['money'] + $fuwu['so_money'],
								"end_money" => $bank_info ['end_money'] + $fuwu['so_money']
						);
						$bank_model->where ( "id='{$bank_info['id']}'" )->save ( $data );
					}
					// 记录
					$bank_info = $bank_model->where ( "bank_id='{$fuwu['s_id']}'" )->find ();
					$data = array (
							"services_id" => $bank_info ['bank_id'],
							"income_money" => $fuwu['so_money'],
							"pay_money" => 0,
							"end_money" => $bank_info ['end_money'],
							"user_money" => $bank_info ['user_money'],
							"money" => $bank_info ['money'],
							"order_id" => $v ['order_id'],
							"c_time" => time ()
					);
					$jl_model = M ( "services_jilu" );
					$jl_model->add ( $data );
					*/
				} else {
					$data = array (
							"state" => 6,
							"l_time" => time () 
					);
					$turn_order_model->where ( "id='{$info['id']}'" )->save ( $data );
					$data = array (
							"order_status" => 8 
					);
					$order_model->where ( "id='{$v['order_id']}'" )->save ( $data );
					// 修改违章状态
					$data = array (
							"is_manage" => 0,
							"manage_time" => time () 
					);
					$endorsement_model = M ( "Endorsement" );
					$endorsement_model->where ( "id={$v['endorsement_id']}" )->save ( $data );
					
					if($info['state'] == 3 or $info['state'] == 4){
						//扣钱
						$bank_model = M ( "bank" );
						$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
						if (! empty ( $bank_info_older )) {
							$data = array (
									"money" => ($bank_info_older ['money'] - $info['money']) > 0 ? ($bank_info_older ['money'] - $info['money']) : 0,
									"end_money" => ($bank_info_older ['end_money'] - $info['money']) > 0 ? ($bank_info_older ['end_money'] - $info['money']) : 0
							);
							$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
						}
						// 记录
						$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
						$data = array (
								"services_id" => $bank_info_older ['bank_id'],
								"income_money" => 0,
								"pay_money" => $info['money'],
								"end_money" => $bank_info_older ['end_money'],
								"user_money" => $bank_info_older ['user_money'],
								"money" => $bank_info_older ['money'],
								"order_id" => $v ['order_id'],
								"c_time" => time ()
						);
						$jl_model = M ( "services_jilu" );
						$jl_model->add ( $data );
					}
				}
				//$this->postBizApi($order_info, $user, 1);
				break;
			case 3 : // 我来办理
				$data = array (
						"state" => 3,
						'l_time' => time (),
						'do_time' => time () 
				);
				$turn_order_model->where ( "id='{$info['id']}'" )->save ( $data );
				$data = array (
						"last_time" => time (),
						"order_status" => 3 
				);
				$order_model->where ( "id='{$v['order_id']}'" )->save ( $data );
				// 评估
				$services_model = M ( "services" );
				$services_info = $services_model->where ( "id='{$v['services_id']}'" )->find ();
				if (! empty ( $services_info )) {
					$data = array (
							"nums" => $services_info ['nums'] + 1 
					);
					$services_model->where ( "id='{$v['services_id']}'" )->save ( $data );
				}
				$bank_model = M ( "bank" );
				$bank_info = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
				if (! empty ( $bank_info )) {
					$data = array (
							"money" => $bank_info ['money'] + $info['money'],
							"end_money" => $bank_info ['end_money'] + $info['money']
					);
					$bank_model->where ( "id='{$bank_info['id']}'" )->save ( $data );
				}
				else{
					$data = array (
						"bank_id" => $v['services_id'],
						"name" => 0,
						"user_bank" => 0,
						"user_number" => 0,
						"money" => $info ['money'],
						"end_money" => $info ['money'],
						"user_money" => 0,
						"create_time" => time ()
					);
					$bank_model->add ( $data );
				}
				$bank_info = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
				$data = array (
					"services_id" => $v['services_id'],
					"income_money" => $info ['money'],
					"pay_money" => 0,
					"end_money" => $bank_info ['end_money'],
					"user_money" => $bank_info ['user_money'],
					"money" => $bank_info ['money'],
					"order_id" => $info ['order_id'],
					"c_time" => time () 
				);
				$jl_model = M ( "services_jilu" );
				$jl_model->add ( $data );
				// 推送消息
				if ($user["is_att"] == 0 && $user['channel'] == 0) {
					$data = array (
							'first' => array (
									'value' => urlencode ( first_key ),
									'color' => "#000000" 
							),
							'keyword1' => array (
									'value' => urlencode ( "{$v['order_sn']}" ),
									'color' => '#000000' 
							),
							'keyword2' => array (
									'value' => urlencode ( "{$v['license_number']}" ),
									'color' => '#000000' 
							),
							'keyword3' => array (
									'value' => urlencode ( status1 ),
									'color' => '#000000' 
							),
							'remark' => array (
									'value' => urlencode ( last_key ),
									'color' => '#000000' 
							) 
					);
					include_once 'application/Weixin/Conf/config.php';
					$model = new IndexController ();
					$msgurl = "/index.php?g=weixin&m=order&a=index"; 
					if(runEnv == 'production'){
						$weixin_url = "http://weixin.xiaoxianlink.com" . $msgurl;
					}
					elseif(runEnv == 'test'){
						$weixin_url = "http://wxdev.xiaoxianlink.com" . $msgurl;
					}
					else{
						$weixin_url = "http://wx.xiaoxian.com" . $msgurl;
					}
					$model->doSend ( 0, '', $user ['openid'], MUBAN3, $weixin_url, $data );
				}
				$this->postBizApi($order_info, $user, 3);
				break;
			case 4 : // 办理完成
				$data = array (
						"state" => 4,
						'l_time' => time (),
						'finish_time' => time () 
				);
				$turn_order_model->where ( "id='{$info['id']}'" )->save ( $data );
				/*
				$data = array (
						"last_time" => time (),
						"order_status" => 4 
				);
				$order_model->where ( "id='{$order_info['id']}'" )->save ( $data );
				$this->postBizApi($order_info, $user, 4);
				*/
				break;
			default :
				;
				break;
		}
		$this->redirect ( "Xitong/window" );
	}
	
	function postBizApi($order, $user, $state){
		$log = new Log();
		if($user['channel'] == 99){
			$_model = M("");
			$_result = $_model->query("select c.license_number, e.* from cw_endorsement e, cw_car c where e.id = {$order['endorsement_id']} and c.id = {$order['car_id']}");
			$result = $_result[0];
			
			$bizapi_id = substr($user['channel_key'], 7);
			$bizapi_model = M('bizapi');
			$now = time();
			$bizapi = $bizapi_model->where("id = $bizapi_id and state = 1 and expiration_time >= $now ")->find();
			
			if(!empty($bizapi)){
				$target_url = $bizapi['app_domain'];
				if(false === strpos($target_url, 'http://')){
						$target_url = "http://" . $target_url;
					}
				$target_url = $target_url . "/api/weizhang/banlijieguo";
				if($state == 1){
					$state_desc = "未处理";
				}
				if($state == 3){
					$state_desc = "处理中";
				}
				if($state == 4){
					$state_desc = "已处理";
				}
				$post_data = array (
					'chepai' => $result['license_number'],
					'weizhangtime' => $result['time'],
					'weizhangcity' => $result['area'],
					'weizhangcode' => $result['code'],
					'fajin' => $result['money'],
					'fafen' => $result['points'],
					'dingdanhao' => $order['order_sn'],
					'banlizhuangtai' => $state_desc,
					'timestamp' => time()
					);
				$log->write ( "target_url=" . $target_url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Ziniu/' . date ( 'y_m_d' ) . '.log' );
				$log->write ( serialize ( http_build_query($post_data) ), 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Ziniu/' . date ( 'y_m_d' ) . '.log' );
				$dataRes = $this->request_post($target_url, http_build_query($post_data));
				$log->write ( serialize ( $dataRes ), 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Ziniu/' . date ( 'y_m_d' ) . '.log' );
			}
		}
	}
	
	function request_post($url = '', $param = '') {
		if (empty ( $url ) || empty ( $param )) {
			return false;
		}
		$postUrl = $url;
		$curlPost = $param;
		$ch = curl_init (); // 初始化curl
		curl_setopt ( $ch, CURLOPT_URL, $postUrl ); // 抓取指定网页
		curl_setopt ( $ch, CURLOPT_HEADER, 0 ); // 设置header
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 ); // 要求结果为字符串且输出到屏幕上
		curl_setopt ( $ch, CURLOPT_POST, 1 ); // post提交方式
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $curlPost );
		$data = curl_exec ( $ch ); // 运行curl
		curl_close ( $ch );
		return $data;
	}
	
	function find_fuwu($car_id, $code, $money, $points, $area, $exclude_list = null){
		$log = new Log ();
		$fuwu = Array();
		$region_model = M ( "Region" );
		$where = array (
				"city" => $area,
				"level" => 2,
				"is_dredge" => 0 
		);
		$region = $region_model->where ( $where )->order ( 'id' )->find ();
		if (empty ( $region )) {
			$city_id1 = 0;
		}
		else{
			$city_id1 = $region ['id'];
		}
		
		$where = array (
				"id" => $car_id
		);
		$car_model = M ( "Car" );
		$car = $car_model->where ( $where )->find ();
		$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		$region = $region_model->where ( "city = '{$region['city']}'" )->order ( "id" )->find ();
		if (empty ( $region )) {
			$city_id2 = 0;
		} else {
			$city_id2 = $region ['id'];
		}
		
		$violation_model = M("violation");
		$violation = $violation_model->field("money, points")->where("code = '$code'")->find();
		if(empty($violation) || $violation['state'] == 1){
			return $fuwu;
		}
		
		$where = "";
		if(!empty($exclude_list)){
			$where = "srv.id not in (" . implode(",", $exclude_list) . ") and ";
		}
		$s_code = substr($code, 0, 4);
		
		$so_model = M(''); // 1.a
		$so_sql = "select srv.id as services_id, so.id as so_id, so.money from cw_services as srv, cw_services_city as scity, cw_services_code as scode, cw_services_order as so where $where srv.id = scity.services_id and srv.id = scode.services_id and srv.id = so.services_id and srv.state = 0 and srv.grade > 4 and ((scity.code = $city_id1 and scity.state = 0) or (scity.code = $city_id2 and scity.state = 0)) and ((scode.code = '$code' or scode.code = '$s_code') and scode.state = 0 ) and so.violation = '$code' and (so.code = $city_id1 or so.code = $city_id2)group by srv.id order by money asc ";
		$log->write ( $so_sql );
		$solist = $so_model->query($so_sql);
		
		$sd_model = M(''); // 1.b
		$sd_sql = "select * from (select dyna.services_id, dyna.id as so_id, ($money + dyna.fee + dyna.point_fee * $points) dyna_fee from cw_services as srv, cw_services_city as scity, cw_services_code as scode, cw_services_dyna as dyna where   $where srv.id = scity.services_id and srv.id = scode.services_id and srv.id = dyna.services_id and srv.state = 0 and srv.grade > 4 and ((scity.code = $city_id1 and scity.state = 0) or (scity.code = $city_id2 and scity.state = 0)) and (scode.code = '$code' or scode.code = '$s_code') and scode.state = 0 and (dyna.code = $city_id1 or dyna.code = $city_id2) ORDER BY dyna_fee ASC) as service_dyna group by services_id order by dyna_fee asc";
		$log->write ( $sd_sql );
		$sdlist = $sd_model->query($sd_sql);
		
		// we now get the lowest price
		$lowest_price = -1;
		$so_id = -1;
		$so_type = -1;
		if( ! empty($solist)){
			$lowest_price = $solist[0]['money'];
			$so_id = $solist[0]['so_id'];
			$so_type = 1;
		}
		if( ! empty($sdlist)){
			if($lowest_price > -1 ){
				if($lowest_price > $sdlist[0]['dyna_fee']){
					$lowest_price = $sdlist[0]['dyna_fee'];
					$so_id = $sdlist[0]['so_id'];
					$so_type = 2;
				}
			}
			else{
				$lowest_price = $sdlist[0]['dyna_fee'];
				$so_id = $sdlist[0]['so_id'];
				$so_type = 2;
			}
		}
		//$log->write ( "lowest_price=". $lowest_price );
		if($lowest_price == -1){
			return $fuwu;
		}
		
		$where = "";
		$firstCondition = false;
		$services_id_by_money = array ();
		if( ! empty($solist)){
			foreach ( $solist as $p => $c ) {
				if($c['money'] == $lowest_price){
					if ($firstCondition == false) {
						$where .= " services_id = {$c['services_id']}";
						$firstCondition = true;
					} else {
						$where .= " or services_id = {$c['services_id']}";
					}
					$services_id_by_money[] = $c['services_id'];
				}
				else{
					break;
				}
			}
		}
		if( ! empty($sdlist)){
			foreach ( $sdlist as $p => $c ) {
				if($c['dyna_fee'] == $lowest_price){
					if ($firstCondition == false) {
						$where .= " services_id = '{$c['services_id']}'";
						$firstCondition = true;
					} else {
						$where .= " or services_id = '{$c['services_id']}'";
					}
					$services_id_by_money[] = $c['services_id'];
				}
				else{
					break;
				}
			}
		}
		$order_model = M(''); // 2
		$sql = "SELECT COUNT(*) as nums, `services_id` FROM `cw_order` WHERE $where GROUP BY `services_id` ORDER BY nums";
		//$log->write ( $sql);
		$orderlist = $order_model->query ( $sql );
		$services_id_by_ordernum = array ();
		foreach ( $orderlist as $p => $c ) {
			$services_id_by_ordernum [] = $c ['services_id'];
		}
		$services = array_diff ( $services_id_by_money, $services_id_by_ordernum );
		if (! empty ( $services )) {
			foreach ( $services as $r ) {
				$services_id = $r;
				break;
			}
		} else {
			$services_id = $orderlist [0] ['services_id'];
		}
		//$log->write ( "services_id=". $services_id );
		// 3
		$fuwu['s_id'] = $services_id;
		$fuwu['so_id'] = $so_id;
		$fuwu['so_type'] = $so_type;
		$fuwu['so_money'] = $lowest_price;
		
		return $fuwu;
	}
	// 违章已处理
	function e_finish() {
		$e_id = isset ( $_REQUEST ['e_id'] ) ? $_REQUEST ['e_id'] : 0;
		$endorsement_model = M ( "Endorsement" );
		$log_model = M ( "Endorsement_log" );
		if ($e_id != 0) {
			// TODO fixme
			$api = new JiaoyiController();
			$api->close_endorsement ( $e_id );
			$api->finish_order ( $e_id );
		}
		$this->ajaxReturn ( 1 );
	}
	
	/* start 后台添加银行管理和版本管理*/
	//银行管理
	public function yinhang(){
	    $model = M ('yinhang');
	    $order = "sort desc";
	    $count = $model->count ();
	    $page = $this->page ( $count, 50 );
	    $roles = $model->field ( "@rownum:=@rownum+1 AS iid,id,sort,bank_name,bank_img,state " )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
	    $this->assign ( "str", $roles );
	    $this->assign ( "Page", $page->show ( 'Admin' ) );
	    $this->assign ( "pageIndex", $page->firstRow );
	    $this->display ();
	}
	function yh_add(){
	    $this->display();
	}
	function yhadd_post() {
	    $sort = $_POST ['sort'];
	    $bank_name = $_POST ['bank_name'];
	    $state = $_POST ['state'];
	    if($sort == ''){
	        $this->error("排序不能为空！");
	    }
	    if($bank_name == ''){
	        $this->error("银行名称不能为空！");
	    }
	    $config = array (
	        'FILE_UPLOAD_TYPE' => sp_is_sae () ? "Sae" : 'Local', // TODO 其它存储类型暂不考虑
	        'rootPath' => './' . C ( "UPLOADPATH" ),
	        'savePath' => './file/',
	        'maxSize' => 2097152, // 2M
	        'saveName' => array (
	            'uniqid',
	            ''
	        ),
	        'exts' => array (
	            'jpg',
	            'png',
	            'jpeg'
	        ),
	        'autoSub' => false
	    );
	    $upload = new \Think\Upload ( $config ); //
	    $info = $upload->upload ( $_FILES );
	        $data = array (
	            "sort" => $sort,
	            "bank_name" => $bank_name,
	            "bank_img" =>  'file/'.$info ['bank_img'] ['savename'],
	            "state" => strtr ( $state, "，", "," )
	        );
	        $model = M ( "yinhang" );
	        if ($model->add ( $data )) {
	            $this->success ( "添加成功！" );
	        } else {
	            $this->error ( "添加失败！" );
	        }
	}
	/**
	 * 编辑
	 */
	public function yh_edit() {
	    $id = $_REQUEST['id'];
	    $model = M ( "yinhang" );
	    $data = $model->where(array("id" => $id))->find();
	    $this->assign("data", $data);
	    $this->display();
	}
	
	/**
	 * 编辑
	 */
	public function yhedit_post() {
	    $id = $_REQUEST['id'];  
		$sort = $_POST ['sort'];  
		$bank_name = $_POST ['bank_name'];  
		$state = $_POST ['state'];  
		if($sort == ''){  
			$this->error("排序不能为空！"); 
		}  
		if($bank_name == ''){  
			$this->error("银行名称不能为空！");  
		}  
		$config = array (  
			'FILE_UPLOAD_TYPE' => sp_is_sae () ? "Sae" : 'Local', // TODO 其它存储类型暂不考虑  
			'rootPath' => './' . C ( "UPLOADPATH" ),  
			'savePath' => './file/',  
			'maxSize' => 2097152, // 2M  
			'saveName' => array (  
			 'uniqid',  
			 ''  
			),  
			'exts' => array (  
			 'jpg',  
			 'png',  
			 'jpeg'  
			),  
			'autoSub' => false  
		);  
		$upload = new \Think\Upload ( $config ); //  
		$info = $upload->upload ( $_FILES );  
		$data = array (  
			"sort" => $sort,  
			"bank_name" => $bank_name,  
			"bank_img" =>  'file/'.$info ['bank_img'] ['savename'],  
			"state" => strtr ( $state, "，", "," )  
		);  
		$model = M ( "yinhang" );  
		if ($model->where ( "id='$id'" )->save ( $data )) {  
			$this->success("修改成功！", U('xitong/yinhang'));  
		} else {  
			$this->error ( "修改失败！" );  
		}  
	}
	/**
	 * 删除
	 */
	public function yh_delete() {
	    $id = intval(I("get.id"));
	    $model = M ( "yinhang" );
	   
	        $status = $model->delete($id);
	        if ($status!==false) {
	            $this->success("删除成功！", U('xitong/yinhang'));
	        } else {
	            $this->error("删除失败！");
	        }
	    }
	/**
	 * 版本管理
	 */
	public function version(){
	    $model = M ('version');
	    $order = "version desc";
	    $count = $model->count ();
	    $page = $this->page ( $count, 50 );
	    $roles = $model->field ( "@rownum:=@rownum+1 AS iid,id,url,version,extra,is_must,update_time " )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
	    $this->assign ( "str", $roles );
	    $this->assign ( "Page", $page->show ( 'Admin' ) );
	    $this->assign ( "pageIndex", $page->firstRow );
	    $this->display ();
	}
	function version_add(){
	    $this->display();
	}
	function versionadd_post() {
	    $url = $_POST ['url'];
	    $version = $_POST ['version'];
	    $extra = $_POST ['extra'];
	    $is_must = $_POST ['is_must'];
	    $update_time = $_POST['update_time'];
	    if($url == ''){
	        $this->error("下载地址不能为空！");
	    }
	    if($version == ''){
	        $this->error("版本号不能为空！");
	    }
	    $data = array (
	        "url" => $url,
	        "version" => $version,
	        "extra" =>  $extra,
	        "is_must" => $is_must,
	        "update_time"=>time()
	    );
	    $model = M ( "version" );
	    if ($model->add ( $data )) {
	        $this->success ( "添加成功！" );
	    } else {
	        $this->error ( "添加失败！" );
	    }
	}
	/**
	 * 修改版本
	 */
	public function version_edit() {
	    $id = $_REQUEST['id'];
	    $model = M ( "version" );
	    $data = $model->where(array("id" => $id))->find();
	    $this->assign("data", $data);
	    $this->display();
	}
	public function versionedit_post() {
	    $id = $_REQUEST['id'];
	    $url = $_POST ['url'];
	    $extra = $_POST ['extra'];
	    $version = $_POST ['version'];
	    $is_must = $_POST ['is_must'];
	    if($url == ''){
	        $this->error("下载地址不能为空！");
	    }
	    if($version == ''){
	        $this->error("版本号不能为空！");
	    }
	    $data = array (
	        "url" => $url,
	        "version" => $version,
	        "extra" =>  $extra,
	        "is_must" => $is_must,
	        "update_time"=>time()
	    );
	    $model = M ( "version" );
	    if ($model->where ( "id='$id'" )->save ( $data )) {
	        $this->success("修改成功！", U('xitong/version'));
	    } else {
	        $this->error ( "修改失败！" );
	    }
	}
	/**
	 * 删除
	 */
	public function version_delete() {
	    $id = intval(I("get.id"));
	    $model = M ( "version" );
	
	    $status = $model->delete($id);
	    if ($status!==false) {
	        $this->success("删除成功！", U('xitong/version'));
	    } else {
	        $this->error("删除失败！");
	    }
	}
	/* end */
}
