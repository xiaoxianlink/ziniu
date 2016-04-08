<?php

namespace Weixin\Controller;
// use Common\Controller\HomeBaseController;
use Weixin\Controller\IndexController;
use Think\Log;

class ApiController extends IndexController {
	/**
	 * 每日定时查询
	 */
	public function timing_scan() {
		$code = $this->index ();
		if ($code == 102) {
			$code = $this->week_scan ();
			if ($code == 102) {
				$code = $this->month_scan ();
			}
		}
		$msg = array (
				"code" => 101 
		);
		echo json_encode ( $msg );
	}
	/**
	 * 每日定时查询
	 */
	public function index() {
		$timing_model = M ( "Timing_scan" );
		$timing_info = $timing_model->field ( "id,nums" )->where ( "type = 1 and FROM_UNIXTIME(create_time,'%Y-%m-%d') = FROM_UNIXTIME(unix_timestamp(now()),'%Y-%m-%d')" )->find ();
		if (! empty ( $timing_info )) {
			$id = $timing_info ['id'];
			$count = $timing_info ['nums'];
		} else {
			$data = array (
					"type" => 1,
					"create_time" => time (),
					"nums" => 0 
			);
			$timing_model->add ( $data );
			$id = $timing_model->getLastInsID ();
			$count = 0;
		}
		$model = M ();
		$car = $model->table ( "cw_endorsement as e" )->join ( "cw_car as c on c.id=e.car_id" )->field ( "c.*,e.area" )->where ( "e.is_manage <> 2 and c.scan_state = 1" )->group ( "e.area" )->limit ( $count . ',' . timing_count1 )->select ();
		if (empty ( $car )) {
			return 102; // 查完了
			exit ();
		}
		$i = 0;
		foreach ( $car as $k => $v ) {
			$i ++;
			// 查询违章保存信息
			$this->scan_api_day ( $v ['id'], $v ['area'] );
			$data = array (
					"nums" => $count + $i 
			);
			$timing_model->where ( "id='$id'" )->save ( $data );
		}
		return 101;
		exit ();
	}
	/**
	 * 每周查询推送
	 */
	public function week_scan() {
		$timing_model = M ( "Timing_scan" );
		$timing_info = $timing_model->field ( "id,nums" )->where ( "type = 2 and FROM_UNIXTIME(create_time,'%Y-%m-%d') = FROM_UNIXTIME(unix_timestamp(now()),'%Y-%m-%d')" )->find ();
		if (! empty ( $timing_info )) {
			$id = $timing_info ['id'];
			$count = $timing_info ['nums'];
		} else {
			$data = array (
					"type" => 2,
					"create_time" => time (),
					"nums" => 0 
			);
			$timing_model->add ( $data );
			$id = $timing_model->getLastInsID ();
			$count = 0;
		}
		$car_array = array (
				"7,'V','W','X','Y','Z'",
				"1,8",
				"2,9",
				"3,'0'",
				"4,'A','B','C','D','E','F','G'",
				"5,'H','I','J','K','L','M','N'",
				"6,'O','P','Q','R','S','T','U'" 
		);
		$week = date ( "w" );
		$num = $car_array [$week];
		$car_model = M ( "Car" );
		$car = $car_model->where ( "right(license_number,1) in ($num) and scan_state = 1" )->limit ( $count . ',' . timing_count1 )->select ();
		if (empty ( $car )) {
			return 102; // 查完了
			exit ();
		}
		$i = 0;
		foreach ( $car as $k => $v ) {
			$i ++;
			// 查询违章保存信息
			$l_nums = mb_substr ( $v ['license_number'], 0, 2, 'utf-8' );
			$region_model = M ( "Region" );
			$region = $region_model->where ( "nums = '$l_nums'" )->find ();
			if (! empty ( $region )) {
				$jsoninfo = $this->scan_api_one ( $v ['id'], $region ['city'] );
			}
			$model = M ();
			$list = $model->table ( "cw_user_car as uc" )->join ( "cw_user as u on u.id = uc.user_id" )->field ( "u.city" )->where ( "uc.car_id='{$v ['id']}'" )->group ( "u.city" )->select ();
			foreach ( $list as $u ) {
				if ($u ['city'] != '' && $u ['city'] != null && $u ['city'] != $region ['city']) {
					$jsoninfo = $this->scan_api_one ( $v ['id'], $u ['city'] );
				}
			}
			
			/*
			 * // 查询数据库违章信息 $endorsement_model = M ( "Endorsement" ); $where = array ( "car_id" => $v ['id'], "is_manage" => 0 ); $endorsement = $endorsement_model->field ( "count(*) as nums, sum(points) as all_points, sum(money) as all_money" )->where ( $where )->find (); $date = date ( 'Y-m-d' ); if (! empty ( $endorsement )) { if ($endorsement ['nums'] != 0) { $data = array ( 'first' => array ( 'value' => urlencode ( "您好，{$v ['license_number']}近期违章统计信息如下：" ), 'color' => "#000000" ), 'keyword1' => array ( 'value' => urlencode ( "{$v ['license_number']}" ), 'color' => '#000000' ), 'keyword2' => array ( 'value' => urlencode ( "{$endorsement['nums']}" ), 'color' => '#000000' ), 'keyword3' => array ( 'value' => urlencode ( "{$endorsement['all_points']}" ), 'color' => '#000000' ), 'keyword4' => array ( 'value' => urlencode ( "{$endorsement['all_money']}" ), 'color' => '#000000' ), 'keyword5' => array ( 'value' => urlencode ( $date ), 'color' => '#000000' ), 'remark' => array ( 'value' => urlencode ( "" ), 'color' => '#000000' ) ); $user_model = M (); $user = $user_model->table ( "cw_user as u" )->join ( "cw_user_car as uc on uc.user_id = u.id" )->field ( "u.openid" )->where ( "uc.car_id='{$v['id']}' and uc.is_sub = 0" )->select (); foreach ( $user as $p ) { $this->doSend ( $v ['id'], $endorsement, $p ['openid'], MUBAN1, URL3 . "&openid=" . $p ['openid'] . "&carid=" . $v ['id'], $data ); } } }
			 */
			$data = array (
					"nums" => $count + $i 
			);
			$timing_model->where ( "id='$id'" )->save ( $data );
		}
		return 101;
		exit ();
	}
	/**
	 * 每月查询推送
	 */
	public function month_scan() {
		$timing_model = M ( "Timing_scan" );
		$timing_info = $timing_model->field ( "id,nums" )->where ( "type = 3 and FROM_UNIXTIME(create_time,'%Y-%m')=date_format(now(),'%Y-%m')" )->find ();
		if (! empty ( $timing_info )) {
			$id = $timing_info ['id'];
			$count = $timing_info ['nums'];
		} else {
			$data = array (
					"type" => 3,
					"create_time" => time (),
					"nums" => 0 
			);
			$timing_model->add ( $data );
			$id = $timing_model->getLastInsID ();
			$count = 0;
		}
		$car_model = M ( "Car" );
		$car = $car_model->where("scan_state = 1")->limit ( $count . ',' . timing_count2 )->select ();
		if (empty ( $car )) {
			return 102; // 查完了
			exit ();
		}
		$i = 0;
		foreach ( $car as $k => $v ) {
			$i ++;
			// 查询违章保存信息
			$region_model = M ( "Region" );
			$region = $region_model->where ( "`level` = 1" )->group ( "code" )->select ();
			foreach ( $region as $p ) {
				$this->scan_api_one ( $v ['id'], $p ['province'], 2 );
			}
			$data = array (
					"nums" => $count + $i 
			);
			$timing_model->where ( "id='$id'" )->save ( $data );
		}
		return 101;
		exit ();
	}
	// 查询
	function scan_api_one($car_id, $city, $type = 1) {
		$log = new Log ();
		$car_model = M ( "Car" );
		$car = $car_model->where ( "id = $car_id" )->find ();
		$region_model = M ( "Region" );
		if ($type == 1) {
			$region = $region_model->where ( "city = '$city'" )->find ();
		} else {
			$region = $region_model->where ( "province = '$city' and level = 1" )->find ();
		}
		
		// 车首页查询
		$app_id = app_id;
		$app_key = app_key;
		$car = "{hphm={$car['license_number']}&classno={$car['frame_number']}&engineno={$car['engine_number']}&city_id={$region['code']}&car_type=02}";
		$car_info = urlencode ( $car );
		$time = time ();
		$sign = md5 ( $app_id . $car . $time . $app_key );
		$url = "http://www.cheshouye.com/api/weizhang/query_task?car_info=$car_info&sign=$sign&timestamp=$time&app_id=$app_id";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$jsoninfo = json_decode ( $output, true );
		$endorsement_model = M ( "Endorsement" );
		$log_model = M ( "Endorsement_log" );
		// 保存车首页查询信息
		$log->write ( "请求参数：" . $url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$log->write ( "返回参数：" . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$ids = "0";
		$jilu_model = M ( "endorsement_jilu" );
		$jilu_data = array (
				"car_id" => $car_id,
				"city" => $city,
				"money" => 0,
				"points" => 0,
				"all_nums" => 0,
				"add_nums" => 0,
				"edit_nums" => 0,
				"c_time" => time (),
				"port" => 'cheshouye.com',
				"code" => $jsoninfo ['status'],
				"state" => 0 
		);
		$jilu_id = $jilu_model->add ( $jilu_data );
		if ($jsoninfo ['status'] == 2001) {
			foreach ( $jsoninfo ['historys'] as $v ) {
				$jilu_data ['all_nums'] ++;
				$jilu_data ['money'] += $v ['money'];
				$jilu_data ['points'] += $v ['fen'];
				$time = strtotime ( $v ['occur_date'] );
				$endorsement = $endorsement_model->where ( "car_id = '$car_id' and time = '$time'" )->find ();
				if (empty ( $endorsement )) {
					$city = isset ( $v ['city_name'] ) ? $v ['city_name'] : $city;
					$data = array (
							"car_id" => $car_id,
							"area" => $city,
							"query_port" => csyapi,
							"code" => $v ['code'],
							"time" => $time,
							"money" => $v ['money'],
							"points" => $v ['fen'],
							"address" => $v ['occur_area'],
							"content" => $v ['info'],
							"create_time" => time (),
							"manage_time" => time (),
							"query_no" => $jilu_id
							// "certificate_no" => $v ['archive'],
							//"office" => $v ['officer'] 
					);
					$endorsement_model->add ( $data );
					$end_id = $endorsement_model->getLastInsID ();
					$data = array (
							"end_id" => $end_id,
							"state" => 1,
							"c_time" => time (),
							"type" => 0 
					);
					$log_model->add ( $data );
					$jilu_data ['add_nums'] ++;
					$this->send ( $car_id, $end_id );
				}
			}
		} elseif ($jsoninfo ['status'] == 2000) {
		} elseif($jsoninfo ['status'] == 5008){
			$car_scan_data = array (
				"scan_state" => 0,
				"scan_state_desc" => "输入的车辆信息有误，请查证后重新输入",
				"scan_state_time" => time (),
				"scan_stop_query" => $jilu_id
			);
			$car_model->where ( "id='$car_id'" )->save ( $car_scan_data );
		}
		else {
			/*
			 * $jsoninfo = $this->get_endorsement ( $car_id, $city ); if ($jsoninfo ['code'] == '0') { foreach ( $jsoninfo ['data'] ['result'] as $v ) { $time = strtotime ( $v ['violationTime'] ); $endorsement = $endorsement_model->where ( "car_id = '$car_id' and time = '$time' and code = '{$v ['violationCode']}'" )->find (); if (empty ( $endorsement )) { $data = array ( "car_id" => $car_id, "area" => $v ['violationCity'], "query_port" => acfapi, "code" => $v ['violationCode'], "time" => $time, "money" => $v ['violationPrice'], "points" => $v ['violationMark'], "address" => $v ['violationAddress'], "content" => $v ['violationDesc'], "create_time" => time (), "query_no" => $v ['id'], "office" => $v ['officeName'] ); $endorsement_model->add ( $data ); $this->send ( $car_id, $endorsement_model->getLastInsID () ); } } }
			 */
		}
		if ($jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data )) {
			$data = array (
					"last_time" => time () 
			);
			$car_model->where ( "id = '$car_id'" )->save ( $data );
		}
	}
	
	function send_test() {
		$car_id = $_REQUEST['car_id'];
		$end_id = $_REQUEST['end_id'];
		$this->send($car_id, $end_id);
	}
	
	// 推送
	function send($car_id, $end_id) {
		$log = new Log ();
		$log->write ( "send---------------------------$end_id", 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$end_model = M ( "endorsement" );
		$end_info = $end_model->where ( "id='$end_id'" )->find ();
		if (empty ( $end_info )) {
			return false;
		}
		$log->write ( "senddata---------------------------" . json_encode ( $end_info ), 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$car_model = M ( "Car" );
		$car_info = $car_model->where ( "id='$car_id'" )->find ();
		$user_model = M ();
		$user = $user_model->table ( "cw_user as u" )->join ( "cw_user_car as uc on uc.user_id = u.id" )->field ( "u.id, u.openid, u.nickname, u.channel, u.channel_key" )->where ( "uc.car_id='$car_id' and uc.is_sub = 0" )->select ();
		$date = date ( "Y年m月d日 H:i", $end_info ['time'] );
		foreach ( $user as $p ) {
			if($p['channel'] == 0){
				$data = array (
					'first' => array (
							'value' => urlencode ( "{$p['nickname']}，{$car_info ['license_number']}有一条新违章" ),
							'color' => "#000000" 
					),
					'violationTime' => array (
							'value' => urlencode ( $date ),
							'color' => '#000000' 
					),
					'violationAddress' => array (
							'value' => urlencode ( "{$end_info['address']}" ),
							'color' => '#000000' 
					),
					'violationType' => array (
							'value' => urlencode ( "{$end_info['content']}" ),
							'color' => '#000000' 
					),
					'violationFine' => array (
							'value' => urlencode ( "{$end_info['money']}" ),
							'color' => '#000000' 
					),
					'violationPoints' => array (
							'value' => urlencode ( "{$end_info['points']}" ),
							'color' => '#000000' 
					),
					'remark' => array (
							'value' => urlencode ( "" ),
							'color' => '#000000' 
					) 
				);
				$this->doSend ( 0, '', $p ['openid'], MUBAN2, URL3 . "&openid=" . $p ['openid'] . "&carid=" . $car_id . "&end_id=" . $end_id, $data );
			}
			if($p['channel'] == 99){
				$bizapi_id = substr($p['channel_key'], 7);
				$bizapi_model = M('bizapi');
				$bizapi = $bizapi_model->where("id = $bizapi_id")->find();
				if(!empty($bizapi)){
					$target_url = $bizapi['app_domain'];
					$target_url = $bizapi_app_domain;
					if(false === strpos($target_url, 'http://')){
						$target_url = "http://" . $target_url;
					}
					$target_url = $target_url . "/api/weizhang/weizhangtixing";
					$fuwu = $this->find_fuwu($car_id, $end_info['code'], $end_info['money'], $end_info['points'], $end_info['area']);
					$post_data = array (
						'chepai' => $car_info ['license_number'],
						'weizhangtime' => $end_info['time'],
						'weizhangcity' => $end_info['area'],
						'weizhangcode' => $end_info['code'],
						'fajin' => $end_info['money'],
						'fafen' => $end_info['points'],
						'zhangshucode' => $end_info['certificate_code'],
						'weizhangaddress' => $end_info['address'],
						'weizhanginfo' => $end_info['content'],
						'weizhangoffice' => $end_info['office'],
						'ischuli' => 'N',
						'timestamp' => time()
					);
					if(!empty($fuwu)){
						$post_data['ischuli'] = 'Y';
						$post_data['daibanprice'] = $fuwu['so_money'];
						$post_data['daibanlink'] = "http://wxdev.xiaoxianlink.com/index.php?g=weixin&m=scan&a=scan_info&id=".$end_info['id']."&license_number=". urlencode($car_info ['license_number'] ) ."&so_id=".$fuwu['so_id']."&so_type=".$fuwu['so_type']."&user_id=".$p['id'];
					}
					$log->write ( "target_url= " . $target_url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
					$log->write ( serialize ( http_build_query($post_data) ), 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
					$dataRes = $this->request_post($target_url, http_build_query($post_data));
					$log->write ( serialize ( $dataRes ), 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
				}
			}
			$data = array (
					"from_userid" => 0,
					"openid" => $p ['openid'],
					"tar_id" => $end_info ['id'],
					"create_time" => time (),
					"msg_type" => 2,
					"nums" => 1,
					"all_points" => $end_info ['points'],
					"all_money" => $end_info ['money'] 
			);
			$model = M ( "Message" );
			$model->add ( $data );
		}
	}
	
	function find_fuwu($car_id, $code, $money, $points, $area){
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
		
		$so_model = M(''); // 1.a
		$so_sql = "select srv.id as services_id, so.id as so_id, so.money from cw_services as srv, cw_services_city as scity, cw_services_code as scode, cw_services_order as so where srv.id = scity.services_id and srv.id = scode.services_id and srv.id = so.services_id and srv.state = 0 and srv.grade > 4 and ((scity.code = $city_id1 and scity.state = 0) or (scity.code = $city_id2 and scity.state = 0)) and (scode.code = '$code' and scode.state = 0 ) and so.violation = '$code' and (so.code = $city_id1 or so.code = $city_id2) group by srv.id order by money asc ";
		//$log->write ( $so_sql );
		$solist = $so_model->query($so_sql);
		
		$sd_model = M(''); // 1.b
		$sd_sql = "select * from (select dyna.services_id, dyna.id as so_id, ($money + dyna.fee + dyna.point_fee * $points) dyna_fee from cw_services as srv, cw_services_city as scity, cw_services_code as scode, cw_services_dyna as dyna where srv.id = scity.services_id and srv.id = scode.services_id and srv.id = dyna.services_id and srv.state = 0 and srv.grade > 4 and ((scity.code = $city_id1 and scity.state = 0) or (scity.code = $city_id2 and scity.state = 0)) and scode.code = '$code' and scode.state = 0 and (dyna.code = $city_id1 or dyna.code = $city_id2) ORDER BY dyna_fee ASC) as service_dyna group by services_id";
		//$log->write ( $sd_sql );
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
		$fuwu['so_id'] = $so_id;
		$fuwu['so_type'] = $so_type;
		$fuwu['so_money'] = $lowest_price;
		
		return $fuwu;
	}
	
	// 每天定时多次查询违章
	function scan_api_day($car_id, $city) {
		$log = new Log ();
		$car_model = M ( "Car" );
		$car = $car_model->where ( "id = $car_id" )->find ();
		$region_model = M ( "Region" );
		$region = $region_model->where ( "city = '$city'" )->find ();
		// 车首页查询
		$app_id = app_id;
		$app_key = app_key;
		$engineLen = $region ['c_engine_nums'];
		if ($engineLen > 0) {
			$engine_number = substr ( $car ['engine_number'], - $engineLen );
		} else {
			$engine_number = $car ['engine_number'];
		}
		$frameLen = $region ['c_frame_nums'];
		if ($frameLen > 0) {
			$frame_number = substr ( $car ['frame_number'], - $frameLen );
		} else {
			$frame_number = $car ['frame_number'];
		}
		$car = "{hphm={$car['license_number']}&classno={$frame_number}&engineno={$engine_number}&city_id={$region['code']}&car_type=02}";
		$car_info = urlencode ( $car );
		$time = time ();
		$sign = md5 ( $app_id . $car . $time . $app_key );
		$url = "http://www.cheshouye.com/api/weizhang/query_task?car_info=$car_info&sign=$sign&timestamp=$time&app_id=$app_id";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$jsoninfo = json_decode ( $output, true );
		$endorsement_model = M ( "Endorsement" );
		$log_model = M ( "Endorsement_log" );
		// 保存车首页查询信息
		// 保存车首页查询信息
		$log->write ( "请求参数：" . $url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$log->write ( "返回参数：" . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weixin/' . date ( 'y_m_d' ) . '.log' );
		$ids = "0";
		$jilu_model = M ( "endorsement_jilu" );
		$jilu_data = array (
				"car_id" => $car_id,
				"city" => $city,
				"money" => 0,
				"points" => 0,
				"all_nums" => 0,
				"add_nums" => 0,
				"edit_nums" => 0,
				"c_time" => time (),
				"port" => 'cheshouye.com',
				"code" => $jsoninfo ['status'],
				"state" => 0 
		);
		$jilu_id = $jilu_model->add ( $jilu_data );
		if ($jsoninfo ['status'] == 2001) {
			foreach ( $jsoninfo ['historys'] as $v ) {
				$jilu_data ['all_nums'] ++;
				$jilu_data ['money'] += $v ['money'];
				$jilu_data ['points'] += $v ['fen'];
				$time = strtotime ( $v ['occur_date'] );
				$endorsement = $endorsement_model->where ( "car_id = '$car_id' and time = '$time'" )->find ();
				if (empty ( $endorsement )) {
					$city = isset ( $v ['city_name'] ) ? $v ['city_name'] : $city;
					$data = array (
							"car_id" => $car_id,
							"area" => $city,
							"query_port" => csyapi,
							"code" => $v ['code'],
							"time" => $time,
							"money" => $v ['money'],
							"points" => $v ['fen'],
							"address" => $v ['occur_area'],
							"content" => $v ['info'],
							"create_time" => time (),
							"manage_time" => time (),
							"query_no" => $jilu_id,
							// "certificate_no" => $v ['archive'],
							"office" => $v ['officer'] 
					);
					$endorsement_model->add ( $data );
					$jilu_data ['add_nums'] ++;
					$end_id = $endorsement_model->getLastInsID ();
					$data = array (
							"end_id" => $end_id,
							"state" => 1,
							"c_time" => time (),
							"type" => 0 
					);
					$log_model->add ( $data );
					$this->send ( $car_id, $end_id );
				} else {
					$ids .= $ids . "," . $endorsement ['id'];
				}
			}
			$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
			// 如果3次没有查到违章，表示已解决
			/*
			 * $endorsement = $endorsement_model->where ( "id not in ($ids) and car_id = '$car_id' and is_manage <> 2 and area = '$city'" )->select (); $ec_model = M ( "Endorsement_record" ); foreach ( $endorsement as $v ) { $ecinfo = $ec_model->where ( "endor_id = {$v['id']}" )->find (); if (! empty ( $ecinfo )) { $data = array ( "nums" => $ecinfo ['nums'] + 1, "c_time" => time () ); $ec_model->where ( "id = '{$ecinfo['id']}'" )->save ( $data ); if ($ecinfo ['nums'] >= 2) { $data = array ( "manage_time" => time (), "query_no" => $jilu_id, "is_manage" => 2 ); $endorsement_model->where ( "id = '{$v['id']}'" )->save ( $data ); $this->finish_order ( $v ['id'] ); $jilu_data ['edit_nums'] ++; $data = array ( "end_id" => $v['id'], "state" => 2, "c_time" => time (), "type" => 2 ); $log_model->add ( $data ); } } else { $data = array ( "c_time" => time (), "endor_id" => $v ['id'], "nums" => $ecinfo ['nums'] + 1 ); $ec_model->add ( $data ); } }
			 */
		} elseif ($jsoninfo ['status'] == 2000) {
			// 如果3次没有查到违章，表示已解决
			/*
			 * $endorsement = $endorsement_model->where ( "car_id = '$car_id' and is_manage <> 2 and area = '$city'" )->select (); $ec_model = M ( "Endorsement_record" ); foreach ( $endorsement as $v ) { $ecinfo = $ec_model->where ( "endor_id = {$v['id']}" )->find (); if (! empty ( $ecinfo )) { $data = array ( "nums" => $ecinfo ['nums'] + 1, "c_time" => time () ); $ec_model->where ( "id = '{$ecinfo['id']}'" )->save ( $data ); if ($ecinfo ['nums'] >= 2) { $data = array ( "manage_time" => time (), "query_no" => $jilu_id, "is_manage" => 2 ); $endorsement_model->where ( "id = '{$v['id']}'" )->save ( $data ); $this->finish_order ( $v ['id'] ); $jilu_data ['edit_nums'] ++; $data = array ( "end_id" => $v['id'], "state" => 2, "c_time" => time (), "type" => 2 ); $log_model->add ( $data ); } } else { $data = array ( "c_time" => time (), "endor_id" => $v ['id'], "nums" => $ecinfo ['nums'] + 1 ); $ec_model->add ( $data ); } }
			 */
		}
		elseif($jsoninfo ['status'] == 5008){
			$car_scan_data = array (
				"scan_state" => 0,
				"scan_state_desc" => "输入的车辆信息有误，请查证后重新输入",
				"scan_state_time" => time (),
				"scan_stop_query" => $jilu_id
			);
			$car_model->where ( "id='$car_id'" )->save ( $car_scan_data );
		}
		else {
			// 爱车坊接口处理逻辑同上
			$jsoninfo = $this->get_endorsement ( $car_id, $city );
			$jilu_data = array (
					"car_id" => $car_id,
					"city" => $city,
					"money" => 0,
					"points" => 0,
					"all_nums" => 0,
					"add_nums" => 0,
					"edit_nums" => 0,
					"c_time" => time (),
					"port" => "http://120.26.57.239/api/",
					"code" => $jsoninfo ['code'],
					"state" => 0 
			);
			$jilu_id = $jilu_model->add ( $jilu_data );
			if ($jsoninfo ['code'] == '0') {
				$ids = "0";
				foreach ( $jsoninfo ['data'] [0] ['result'] as $v ) {
					$v ['violationPrice'] = isset($v ['violationPrice']) ? $v ['violationPrice'] : 0;
					$v ['violationMark'] = isset($v ['violationMark']) ? $v ['violationMark'] : '-1';
					$v ['violationTime'] = isset($v ['violationTime']) ? $v ['violationTime'] : '-1';
					$v ['violationCode'] = isset($v ['violationCode']) ? $v ['violationCode'] : 0;
					$v ['violationAddress'] = isset($v ['violationAddress']) ? $v ['violationAddress'] : '-1';
					$v ['violationDesc'] = isset($v ['violationDesc']) ? $v ['violationDesc'] : '-1';
					if ($v ['violationPrice'] != 0 && $v ['violationMark'] != '-1' && $v ['violationTime'] != '-1' && $v ['violationCode'] != 0 && $v ['violationAddress'] != '-1' && $v ['violationDesc'] != '-1') {
						$v ['violationPrice'] = $v ['violationPrice'] / 100;
						$jilu_data ['all_nums'] ++;
						$jilu_data ['money'] += $v ['violationPrice'];
						$jilu_data ['points'] += $v ['violationMark'];
						$time = strtotime ( $v ['violationTime'] );
						$endorsement = $endorsement_model->where ( "car_id = '$car_id' and time = '$time'" )->find ();
						if (empty ( $endorsement )) {
							$city = isset ( $v ['violationCity'] ) ? $v ['violationCity'] : $city;
							$data = array (
									"car_id" => $car_id,
									"area" => $city,
									"query_port" => acfapi,
									"code" => $v ['violationCode'],
									"time" => $time,
									"money" => $v ['violationPrice'],
									"points" => $v ['violationMark'],
									"address" => $v ['violationAddress'],
									"content" => $v ['violationDesc'],
									"create_time" => time (),
									"manage_time" => time (),
									"query_no" => $jilu_id,
									// "certificate_no" => $v ['archive'],
									"office" => $v ['officeName']
							);
							$endorsement_model->add ( $data );
							$jilu_data ['add_nums'] ++;
							$data = array (
									"end_id" => $endorsement_model->getLastInsID (),
									"state" => 1,
									"c_time" => time (),
									"type" => 0
							);
							$log_model->add ( $data );
							$this->send ( $car_id, $endorsement_model->getLastInsID () );
						} else {
							$ids .= $ids . "," . $endorsement ['id'];
						}
					}
					// 如果3次没有查到违章，表示已解决
					/*
					 * $endorsement = $endorsement_model->where ( "id not in ($ids) and car_id = '$car_id' and is_manage <> 2 and area = '$city'" )->select (); $ec_model = M ( "Endorsement_record" ); foreach ( $endorsement as $v ) { $ecinfo = $ec_model->where ( "endor_id = {$v['id']}" )->find (); if (! empty ( $ecinfo )) { $data = array ( "nums" => $ecinfo ['nums'] + 1, "c_time" => time () ); $ec_model->where ( "id = '{$ecinfo['id']}'" )->save ( $data ); if ($ecinfo ['nums'] >= 2) { $data = array ( "manage_time" => time (), "query_no" => $jilu_id, "is_manage" => 2 ); $endorsement_model->where ( "id = '{$v['id']}'" )->save ( $data ); $this->finish_order ( $v ['id'] ); $jilu_data ['edit_nums'] ++; $data = array ( "end_id" => $v['id'], "state" => 2, "c_time" => time (), "type" => 2 ); $log_model->add ( $data ); } } else { $data = array ( "c_time" => time (), "endor_id" => $v ['id'], "nums" => $ecinfo ['nums'] + 1 ); $ec_model->add ( $data ); } }
					 */
				}
			}
			elseif($jsoninfo ['code'] == 29 || ($jsoninfo ['code'] >= 31 && $jsoninfo ['code'] <= 34)){
				$car_scan_data = array (
					"scan_state" => 0,
					"scan_state_desc" => $jsoninfo['message'],
					"scan_state_time" => time (),
					"scan_stop_query" => $jilu_id
				);
				$car_model->where ( "id='$car_id'" )->save ( $car_scan_data );
			}
			$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
		}
		$data = array (
				"last_time" => time () 
		);
		$car_model->where ( "id = '$car_id'" )->save ( $data );
	}
	function finish_order($e_id) {
		$order_model = M ( "order" );
		$order_info = $order_model->where ( "endorsement_id='$e_id'" )->find ();
		if (! empty ( $order_info )) {
			$data = array (
					"order_status" => 5,
					"last_time" => time () 
			);
			$order_model->where ( "id='{$order_info['id']}'" )->save ( $data );
			
			$t_order_model = M ( "turn_order" );
			$data = array (
					"state" => 5,
					"l_time" => time () 
			);
			$t_order_info = $t_order_model->where ( "order_id = '{$order_info['id']}' and sod_id = '{$order_info['so_id']}'" )->find ();
			$t_order_model->where ( "order_id = '{$order_info['id']}' and sod_id = '{$order_info['so_id']}'" )->save ( $data );
			if (! empty ( $t_order_info )) {
				$so_model = M ( "services_order" );
				$so_info = $so_model->where ( "id='{$order_info['so_id']}'" )->find ();
				$bank_model = M ( "bank" );
				$bank_info = $bank_model->where ( "bank_id='{$order_info['services_id']}'" )->find ();
				if (! empty ( $bank_info )) {
					$data = array (
							"end_money" => ($bank_info ['end_money'] - $so_info ['money']) > 0 ? ($bank_info ['end_money'] - $so_info ['money']) : 0,
							"user_money" => $bank_info ['user_money'] + $so_info ['money'],
							"income_money" => $bank_info ['income_money'] + $so_info ['money'] 
					);
					$bank_model->where ( "id='{$bank_info['id']}'" )->save ( $data );
				}
				// 记录
				$bank_info = $bank_model->where ( "bank_id='{$order_info['services_id']}'" )->find ();
				$data = array (
						"services_id" => $bank_info ['bank_id'],
						"income_money" => $bank_info ['income_money'],
						"pay_money" => $bank_info ['pay_money'],
						"end_money" => $bank_info ['end_money'],
						"user_money" => $bank_info ['user_money'],
						"money" => $bank_info ['money'],
						"order_id" => $order_info ['id'],
						"c_time" => time () 
				);
				$jl_model = M ( "services_jilu" );
				$jl_model->add ( $data );
			}
			
			// 推送消息
			$model = M ();
			$user = $model->table ( "cw_order as o" )->join ( "cw_user as u on u.id=o.user_id" )->join ( "cw_car as c on c.id=o.car_id" )->field ( "u.openid, o.order_sn, c.license_number" )->where ( "o.id = '{$order_info['id']}'" )->find ();
			if (! empty ( $user )) {
				$data = array (
						'first' => array (
								'value' => urlencode ( first_key ),
								'color' => "#000000" 
						),
						'keyword1' => array (
								'value' => urlencode ( "{$user ['order_sn']}" ),
								'color' => '#000000' 
						),
						'keyword2' => array (
								'value' => urlencode ( "{$user['license_number']}" ),
								'color' => '#000000' 
						),
						'keyword3' => array (
								'value' => urlencode ( status2 ),
								'color' => '#000000' 
						),
						'remark' => array (
								'value' => urlencode ( last_key ),
								'color' => '#000000' 
						) 
				);
				$this->doSend ( 0, '', $user ['openid'], MUBAN3, URL2, $data );
			}
		}
	}
}