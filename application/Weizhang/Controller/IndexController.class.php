<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Weizhang\Controller;

use Common\Controller\HomeBaseController;
use Think\Log;

class IndexController extends HomeBaseController {

	public function chexingyi(){
		$url = "http://chaxun.cx580.com:9008/InputsCondition.aspx";
		$output = $this->invokeHttpApi($url);
		$json_info = json_decode($output, true);
		$region_model = M ( "Region" );
		foreach ( $json_info as $province ) {
			//echo "+" . $province['ProvinceName'] . "_" . $province['ProvinceID'] . "<br>";
			foreach ( $province["Cities"] as $city ) {
				$data = array (
					"cxy_frame_nums" => $city['CarCodeLen'],
					"cxy_engine_nums" => $city['CarEngineLen']
					);
				$region_model -> where ( "gb_code_c = {$city['CityID']}" ) ->save($data);
			}
		}
		exit;
	}
	
	public function stats(){
		$today = date("Ymd"); 
		$yesterday = date('Ymd', strtotime('-1 day'));
		
		$stats_model = M("endorsement_stats");
		$last = $stats_model->order("day_time desc")->find();
		if(!empty($last)){
			$last_day = strtotime($last['day_time']);
			if($last_day == $yesterday){
				return;
			}
			$start = strtotime('+1 day', $last_day);
			$end = strtotime('+2 day', $last_day);
			$day = date('Ymd', $start);
		}
		else{
			$start = strtotime($yesterday);
			$end = strtotime($today);
			$day = $yesterday;
		}
		$this->stats_one(api_chexingyi, $day, $start, $end);
		$this->stats_one(api_cheshouye, $day, $start, $end);
		$this->stats_one(api_icar, $day, $start, $end);
	}
	
	function stats_one($port, $day, $start, $end){
		$jilu_model = M("endorsement_jilu");
		$result = $jilu_model->where("port = '$port' and c_time >= $start and c_time < $end ")->count("id");
		$stats_model = M("endorsement_stats");
		$last = $stats_model->where("port = '$port'")->order("day_time desc")->find();
		if(!empty($last)){
			$differ = $day - $last['day_time'];
			$is_same_month = false;
			$is_same_year = true;
			if($differ < 31){
				$is_same_month = true;
			}
			if($differ > 1130 ){
				$is_same_year = false;
			}
			$data = array(
				"port" => $port,
				"day_time" => $day,
				"day_stats" => $result,
				"month_stats" => $is_same_month ? $last['month_stats'] + $result : $result,
				"year_stats" =>  $is_same_year ? $last['year_stats'] + $result : $result,
				"c_time" => time()
			);
		}
		else{
			$data = array(
				"port" => $port,
				"day_time" => $day,
				"day_stats" => $result,
				"month_stats" => $result,
				"year_stats" => $result,
				"c_time" => time()
			);
		}
		$stats_model->add($data);
	}
	
	function stats_add($port){
		$stats_model = M("endorsement_stats");
		$today = date("Ymd"); 
		$last = $stats_model->where("port = $port and day_time = $today")->find();
		if(!empty($last)){
			$id = $last['id'];
			$data = array(
				"day_stats" => $last['day_stats']+1,
				"month_stats" => $last['month_stats'] + 1,
				"year_stats" => $last['year_stats'] + 1
			);
			$stats_model->where("id=$id")->save($data);
		}
		else{
			$yesterday = date('Ymd', strtotime('-1 day'));
			$differ = $today - $yesterday;
			$is_same_month = false;
			$is_same_year = true;
			if(differ < 31){
				$is_same_month = true;
			}
			if(differ > 1130 ){
				$is_same_year = false;
			}
			$last = $stats_model->where("port = $port and day_time = $yesterday")->find();
			$data = array(
				"port" => $r['port'],
				"day_time" => $today,
				"day_stats" => 1,
				"month_stats" => $is_same_month ? $last['month_stats'] + 1 : 1,
				"year_stats" =>  $is_same_year ? $last['year_stats'] + 1 : 1
			);
			$stats_model->add($data);
		}
	}

	public function week() {
		$prefer = $_REQUEST['prefer'];
		$timing_model = M ( "timing_scan" );
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
		$car = $car_model->where ( "right(license_number,1) in ($num) and scan_state = 1" )->limit ( $count . ',' . timing_count )->select ();
		if (empty ( $car )) {
			return 0; // 查完了
			exit ();
		}
		$i = 0;
		foreach ( $car as $k => $v ) {
			$i++;
			$result = $this->scan_api($v, 0, $prefer);
			$data = array (
					"nums" => $count + $i 
			);
			$timing_model->where ( "id='$id'" )->save ( $data );
		}
		return 1;
		exit ();
	}
	
	function close_endorsement_commit(){
		$now = time();
		$now_168 = $now - 168 * 3600; 
		$end_model = M ( "Endorsement" );
		$ends = $end_model->field("e.*")->table("cw_endorsement as e ")->join("cw_order as o on e.id = o.endorsement_id", "left")->where ("e.is_manage = 0 and e.close_confirm = 1 and e.close_time <= $now_168 and o.id is null")->limit(100)->select();
		foreach ( $ends as $k => $v ) {
			$end_id = $v["id"];
			$data = array(
				"is_manage" => 2,
				"manage_time" => $now
			);
			$end_model->where("id=$end_id")->save($data);
			
			$log_model = M ( "Endorsement_log" );
			$data = array (
					"end_id" => $end_id,
					"state" => 2,
					"c_time" => $now,
					"type" => 2 
			);
			$log_model->add ( $data );
			$this->push_confirm($end_id);
		}
	}
	
	function close_endorsement_prepare($car_id, $query_no, $exclude_list = null){
		$endorsement_model = M ( "Endorsement" );
		$where = "car_id = $car_id and is_manage <> 2 and close_confirm = 0";
		if(!empty($exclude_list)){
			$where .= " and id not in (" . implode(",", $exclude_list) . ")";
		}
		$now = time();
		$sql = " update __TABLE__ set close_confirm = 1, close_query_no = $query_no, close_time = $now where " . $where ;
		$log = new Log ();
		$log->write ( "cheyixing: " . $sql, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weizhang/' . date ( 'y_m_d' ) . '.log' );
		$endorsement = $endorsement_model->execute ( $sql );
	}

	public function index() {
		$car_id = $_REQUEST['car_id'];
		$prefer = $_REQUEST['prefer'];
		
		$car_model = M ( "Car" );
		$car = $car_model->where ( "id = $car_id" )->find ();
		
		$result = $this->scan_api($car, 1, $prefer);
		if($result > 0){
			$data = array (
					"last_time" => time () 
			);
			$car_model->where ( "id = '$car_id'" )->save ( $data );
			return $result;
		}
		return 0;
		exit;
	}
	
	function scan_api($car, $scan_type = 1, $prefer = null){
		$result = -1;
		
		if(runEnv == 'production' || $prefer == 'chexingyi'){
			echo "chexingyi";
			$result = $this->scan_api_chexingyi($car, $scan_type);
		}
		else{
			echo "cheshouye";
			$result = $this->scan_api_cheshouye($car, $scan_type);
		}
		/*
		echo "chexingyi";
		$result = $this->scan_api_chexingyi($car, $scan_type);
		*/
		if($result < 0){
			echo "icar";
			$result = $this->scan_api_icar($car, $scan_type);
		}
		return $result;
	}
	
	function scan_api_chexingyi($car, $scan_type = 1){
		$result = 0;
		$l_nums_a = mb_substr ( $car ['license_number'], 0, 1, 'utf-8' );
		$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		if($l_nums_a == '桂'){
			$l_nums = '桂A';
		}
		
		$a_class = array("粤", "冀", "陕", "吉", "京", "沪", "苏", "鄂","琼", "渝", "甘", "黑", "宁", "青", "贵", "新", "浙", "皖", "湘");
		
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		
		if(! empty ( $region )){
			if(in_array($l_nums_a, $a_class )){
				$result = $this->scan_api_chexingyi_one($car, $region, false, $scan_type);
			}
			else{
				$result = $this->scan_api_chexingyi_one($car, $region, true, $scan_type);
			}
		}
		
		$result_s = 0;
		$model = M ();
		$list = $model->table ( "cw_user_car as uc" )->join ( "cw_user as u on u.id = uc.user_id" )->field ( "u.city" )->where ( "uc.car_id='{$car['id']}'" )->group ( "u.city" )->select ();
		foreach ( $list as $u ) {
			if ($u ['city'] != '' && $u ['city'] != null && $u ['city'] != $region ['city']) {
				$region_s = $region_model->where ( "city = '{$u['city']}'" )->find ();
				if(! empty ( $region_s )){
					if(!in_array($region_s['abbreviation'], $a_class ) || !in_array($l_nums_a, $a_class )){
						$r = $this->scan_api_chexingyi_one ( $car, $region_s, true, $scan_type);
						if($r < 0 ){
							$result_s = $r;
						}
					}
				}
			}
		}
		if($result < 1 && $result_s < 1){
			return -1;
		}
		return 1;
	}
	
	function scan_api_chexingyi_one($car, $region, $special = false, $scan_type = 1){
		$app_id = app_id_chexingyi;
        $app_key = urlencode(app_key_chexingyi);
		$carnumber = urlencode($car['license_number']);
		$url = "http://chaxun.cx580.com:9008/queryindex.aspx?userid=$app_id&userpwd=$app_key&carnumber=$carnumber";
		$engineLen = $region ['cxy_engine_nums'];
		if ($engineLen > 0) {
			$cardrivenumber = substr ( $car ['engine_number'], - $engineLen );
			$url = $url . "&cardrivenumber=$cardrivenumber";
		} 
		$frameLen = $region ['cxy_frame_nums'];
		if ($frameLen > 0) {
			$carcode = substr ( $car ['frame_number'], - $frameLen );
			$url = $url . "&carcode=$carcode";
		}
		if($special){
			$provinceid = $region['gb_code_p'];
			$cityid = $region['gb_code_c'];
			$url = $url . "&provinceid=$provinceid&cityid=$cityid";
		}
		$output = $this->invokeHttpApi($url);
		$log = new Log ();
		$log->write ( "cheyixing: " . $url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weizhang/' . date ( 'y_m_d' ) . '.log' );
		$log->write ( "cheyixing: " . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weizhang/' . date ( 'y_m_d' ) . '.log' );
		
		$has_error = 0;
		if(empty($output)){
			$has_error = -9999;
		}
		$jsoninfo = json_decode($output, true);
		$json_error = json_last_error();
		if($json_error != JSON_ERROR_NONE){
			$has_error = -9998;
		}
		$jilu_model = M ( "endorsement_jilu" );
		if( $has_error == 0){
			$jilu_data = array (
					"car_id" => $car['id'],
					"money" => 0,
					"points" => 0,
					"all_nums" => 0,
					"add_nums" => 0,
					"edit_nums" => 0,
					"c_time" => time (),
					"port" => api_chexingyi,
					"code" => $jsoninfo ['ErrorCode'],
					"state" => $scan_type 
			);
			if($special){
				$jilu_data["city"] = $region['city'];
			}
			$jilu_id = $jilu_model->add ( $jilu_data );
			
			$errorCodes = array (-6, -61, -62, -63);
			if ($jsoninfo ['ErrorCode'] == 0) {
				if (isset($jsoninfo['Records']) && $jsoninfo['Records']) {
					$endorsement_model = M ( "Endorsement" );
					$log_model = M ( "Endorsement_log" );
					$end_list = array();
					foreach ( $jsoninfo ['Records'] as $v ) {
						$jilu_data ['all_nums'] ++;
						$jilu_data ['money'] += $v ['count'];
						$jilu_data ['points'] += $v['Degree'];
						$time = strtotime ( $v ['Time'] );
						$endorsement = $endorsement_model->where ( "car_id = '{$car['id']}' and time = '$time'" )->find ();
						if (empty ( $endorsement )) {
							$city = $region['city'];
							if(isset ( $v ['Locationid'] )){
								$region_model = M("region");
								$v_region = $region_model->where("gb_code_c = {$v ['Locationid']}")->find();
								if(!empty($v_region) && !empty($v_region["city"])){
									$city = $v_region["city"];
								}
							}
							$code = '0000';
							if( isset ( $v ['Code'] ) &&  $v['Code'] != NULL &&  $v['Code'] != "null" ){
								$code = $v ['Code'];
							}
							$data = array (
									"car_id" => $car['id'],
									"area" => $city,
									"query_port" => api_chexingyi,
									"code" => $code,
									"time" => $time,
									"money" => $v ['count'],
									"points" => $v ['Degree'],
									"address" => $v ['Location'],
									"content" => $v ['Reason'],
									"create_time" => time (),
									"manage_time" => time (),
									"query_no" => $jilu_id,
									"office" => $v ['department'] 
							);
							$endorsement_model->add ( $data );
							$end_id = $endorsement_model->getLastInsID ();
							if($v['status'] == 0){
								$end_list[] = $end_id;
							}
							$jilu_data ['add_nums'] ++;
							$data = array (
									"end_id" => $end_id,
									"state" => 1,
									"c_time" => time (),
									"type" => 0 
							);
							$log_model->add ( $data );
							if($scan_type == 0){
								$this->push($car['id'], $end_id);
							}
						}
						else{
							if($v['status'] == 0){
								$end_list[] = $endorsement["id"];
								if($endorsement["close_confirm"] == 1){
									$data = array(
										"close_confirm" => 0, 
										"close_query_no" => $jilu_id, 
										"close_time" => time() 
									);
									$endorsement_model->where("id = '{$endorsement['id']}'")->save($data);
								}
							}
						}
					}
					$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
					$this->close_endorsement_prepare($car['id'], $jilu_id, $end_list);
				}
				else{
					$this->close_endorsement_prepare($car['id'], $jilu_id);
				}
				return 1;
			} elseif(in_array($jsoninfo ['ErrorCode'], $errorCodes)){
				$car_scan_data = array (
					"scan_state" => 0,
					"scan_state_desc" => "输入的车辆信息有误，请查证后重新输入",
					"scan_state_time" => time (),
					"scan_stop_query" => $jilu_id
				);
				$car_model = M ( "Car" );
				$car_model->where ( "id={$car['id']}" )->save ( $car_scan_data );
				return 1;
			}else{
				return -1;
			}
		}else{
			$jilu_data = array (
				"car_id" => $car['id'],
				"money" => 0,
				"points" => 0,
				"all_nums" => 0,
				"add_nums" => 0,
				"edit_nums" => 0,
				"c_time" => time (),
				"port" => api_chexingyi,
				"code" => $has_error,
				"state" => $scan_type 
			);
			if($special){
				$jilu_data["city"] = $region['city'];
			}
			$jilu_id = $jilu_model->add ( $jilu_data );
			return -1;
		}
		return -1;
	}
	
	function scan_api_cheshouye( $car, $scan_type = 1 ) {
		$result = 0;
		$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		if (! empty ( $region )) {
			$result = $this->scan_api_cheshouye_one ( $car, $region, $scan_type );
		}
		$result_s = 0;
		$model = M ();
		$list = $model->table ( "cw_user_car as uc" )->join ( "cw_user as u on u.id = uc.user_id" )->field ( "u.city" )->where ( "uc.car_id='{$car['id']}'" )->group ( "u.city" )->select ();
		foreach ( $list as $u ) {
			if ($u ['city'] != '' && $u ['city'] != null && $u ['city'] != $region ['city']) {
				$region = $region_model->where ( "city = '{$u['city']}'" )->find ();
				if(! empty ( $region )){
					$r = $this->scan_api_cheshouye_one ( $car, $region, $scan_type );
					if($r < 0 ){
						$result_s = $r;
					}
				}
			}
		}
		if($result < 1 && $result_s < 1){
			return -1;
		}
		return 1;
	}
	
	// 车首页接口
	function scan_api_cheshouye_one($car, $region, $scan_type = 1) {
		$app_id = app_id_cheshouye;
		$app_key = app_key_cheshouye;
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
		$car_data = "{hphm={$car['license_number']}&classno={$frame_number}&engineno={$engine_number}&city_id={$region['code']}&car_type=02}";
		$car_info = urlencode ( $car_data );
		$time = time ();
		$sign = md5 ( $app_id . $car_data . $time . $app_key );
		$url = "http://www.cheshouye.com/api/weizhang/query_task?car_info=$car_info&sign=$sign&timestamp=$time&app_id=$app_id";
		
		$output = $this->invokeHttpApi ( $url );
		$log = new Log ();
		$log->write ( "cheshouye: " . $url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weizhang/' . date ( 'y_m_d' ) . '.log' );
		$log->write ( "cheshouye: " . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weizhang/' . date ( 'y_m_d' ) . '.log' );
		
		$has_error = 0;
		if(empty($output)){
			$has_error = -9999;
		}
		$jsoninfo = json_decode($output, true);
		$json_error = json_last_error();
		if($json_error != JSON_ERROR_NONE){
			$has_error = -9998;
		}
		$jilu_model = M ( "endorsement_jilu" );
		if( $has_error == 0){
			$jilu_data = array (
				"car_id" => $car['id'],
				"city" => $region['city'],
				"money" => 0,
				"points" => 0,
				"all_nums" => 0,
				"add_nums" => 0,
				"edit_nums" => 0,
				"c_time" => time (),
				"port" => api_cheshouye,
				"code" => $jsoninfo ['status'],
				"state" => $scan_type 
			);
			$jilu_id = $jilu_model->add ( $jilu_data );
			if ($jsoninfo ['status'] == 2001) {
				$endorsement_model = M ( "Endorsement" );
				$log_model = M ( "Endorsement_log" );
				$end_list = array();
				foreach ( $jsoninfo ['historys'] as $v ) {
					$jilu_data ['all_nums'] ++;
					$jilu_data ['money'] += $v ['money'];
					$jilu_data ['points'] += $v ['fen'];
					$time = strtotime ( $v ['occur_date'] );
					$endorsement = $endorsement_model->where ( "car_id = '${car['id']}' and time = '$time'" )->find ();
					if (empty ( $endorsement )) {
						$city = isset ( $v ['city_name'] ) ? $v ['city_name'] : $region['city'];
						$code = '0000';
						if( isset ( $v ['code'] ) &&  $v['code'] != NULL &&  $v['code'] != "null" ){
							$code = $v ['code'];
						}
						$data = array (
								"car_id" => $car['id'],
								"area" => $city,
								"query_port" => api_cheshouye,
								"code" => $v ['code'],
								"time" => $time,
								"money" => $v ['money'],
								"points" => $v ['fen'],
								"address" => $v ['occur_area'],
								"content" => $v ['info'],
								"create_time" => time (),
								"manage_time" => time (),
								"query_no" => $jilu_id
						);
						$endorsement_model->add ( $data );
						$end_id = $endorsement_model->getLastInsID ();
						$end_list[] = $end_id;
						$jilu_data ['add_nums'] ++;
						$data = array (
								"end_id" => $end_id,
								"state" => 1,
								"c_time" => time (),
								"type" => 0 
						);
						$log_model->add ( $data );
						if($scan_type == 0){
							$this->push($car['id'], $end_id);
						}
					}
					else{
						$end_list[] = $endorsement["id"];
					}
				}
				$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
				//$this->close_endorsement_prepare($car['id'], $jilu_id, $end_list);
				return 1;
			} elseif ($jsoninfo ['status'] == 2000) {
				//$this->close_endorsement_prepare($car['id'], $jilu_id);
				return 1;
			} elseif($jsoninfo ['status'] == 5008){
				/*
				$car_scan_data = array (
					"scan_state" => 0,
					"scan_state_desc" => "输入的车辆信息有误，请查证后重新输入",
					"scan_state_time" => time (),
					"scan_stop_query" => $jilu_id
				);
				$car_model = M ( "Car" );
				$car_model->where ( "id={$car['id']}" )->save ( $car_scan_data );
				*/
				return 1;
			}else{
				return -1;
			}
		} else{
			$jilu_data = array (
				"car_id" => $car['id'],
				"city" => $region['city'],
				"money" => 0,
				"points" => 0,
				"all_nums" => 0,
				"add_nums" => 0,
				"edit_nums" => 0,
				"c_time" => time (),
				"port" => api_cheshouye,
				"code" => $has_error,
				"state" => $scan_type
			);
			$jilu_id = $jilu_model->add ( $jilu_data );
			return -1;
		}
		return -1;
	}
	
	function scan_api_icar($car, $scan_type = 1) {
		$result = 0;
		$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		if (! empty ( $region )) {
			$result = $this->scan_api_icar_one ( $car, $region, $scan_type);
		}
		$result_s = 0;
		$model = M ();
		$list = $model->table ( "cw_user_car as uc" )->join ( "cw_user as u on u.id = uc.user_id" )->field ( "u.city" )->where ( "uc.car_id='{$car['id']}'" )->group ( "u.city" )->select ();
		foreach ( $list as $u ) {
			if ($u ['city'] != '' && $u ['city'] != null && $u ['city'] != $region ['city']) {
				$region = $region_model->where ( "city = '{$u['city']}'" )->find ();
				if(! empty ( $region )){
					$r = $this->scan_api_icar_one ( $car, $region, $scan_type);
					if($r < 0 ){
						$result_s = $r;
					}
				}
			}
		}
		if($result < 1 && $result_s < 1){
			return -1;
		}
		return 1;
	}
	
	// 爱车坊接口
	function scan_api_icar_one($car, $region, $scan_type = 1) {
		$token = $this->get_icar_token ();
		if ($token != '' && $token != null) {
			$license_nums = $car ['license_number'];
			$provinceCode = urlencode ( mb_substr ( $license_nums, 0, 1, 'utf-8' ) );
			$carNumber = mb_substr ( $license_nums, 1, strlen ( $license_nums ), 'utf-8' );
			$engineLen = $region ['engine_nums'];
			if ($engineLen > 0) {
				$engine_number = substr ( $car ['engine_number'], - $engineLen );
			} else {
				$engine_number = $car ['engine_number'];
			}
			$frameLen = $region ['frame_nums'];
			if ($frameLen > 0) {
				$frame_number = substr ( $car ['frame_number'], - $frameLen );
			} else {
				$frame_number = $car ['frame_number'];
			}
			$url = "http://120.26.57.239/api/queryCarViolateInfo?provinceCode=$provinceCode&carNumber=$carNumber&vioCityCode={$region['acode']}&carType=0&carFrame={$frame_number}&carEngine={$engine_number}";
			$log = new Log ();
			$log->write ( $url, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weizhang/' . date ( 'y_m_d' ) . '.log' );
			$header = array (
					"token: $token" 
				);
			$output = $this->invokeHttpApi ( $url , null, $header);
			$log = new Log ();
			$log->write ( "aichefang:" . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weizhang/' . date ( 'y_m_d' ) . '.log' );
			
			$has_error = 0;
			if(empty($output)){
				$has_error = -9999;
			}
			$jsoninfo = json_decode($output, true);
			$json_error = json_last_error();
			if($json_error != JSON_ERROR_NONE){
				$has_error = -9998;
			}
			$jilu_model = M ( "endorsement_jilu" );
			if( $has_error == 0){
				$jilu_data = array (
					"car_id" => $car['id'],
					"city" => $region['city'],
					"money" => 0,
					"points" => 0,
					"all_nums" => 0,
					"add_nums" => 0,
					"edit_nums" => 0,
					"c_time" => time (),
					"port" => api_icar,
					"code" => $jsoninfo ['code'],
					"state" => $scan_type
				);
				$jilu_id = $jilu_model->add ( $jilu_data );
				if ($jsoninfo ['code'] == 0) {
					$endorsement_model = M ( "Endorsement" );
					$log_model = M ( "Endorsement_log" );
					$end_list = array();
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
							$endorsement = $endorsement_model->where ( "car_id = '{$car['id']}' and time = '$time'" )->find ();
							if (empty ( $endorsement )) {
								$city = isset ( $v ['violationCity'] ) ? $v ['violationCity'] : $region['city'];
								$data = array (
										"car_id" => $car['id'],
										"area" => $city,
										"query_port" => api_icar,
										"code" => $v ['violationCode'],
										"time" => $time,
										"money" => $v ['violationPrice'],
										"points" => $v ['violationMark'],
										"address" => $v ['violationAddress'],
										"content" => $v ['violationDesc'],
										"create_time" => time (),
										"manage_time" => time (),
										"query_no" => $jilu_id,
										"office" => $v ['officeName']
								);
								$endorsement_model->add ( $data );
								$end_id = $endorsement_model->getLastInsID ();
								$end_list[] = $end_id;
								$jilu_data ['add_nums'] ++;
								$data = array (
										"end_id" => $end_id,
										"state" => 1,
										"c_time" => time (),
										"type" => 0 
								);
								$log_model->add ( $data );
								if($scan_type == 0){
									$this->push($car['id'], $end_id);
								}
							}
							else{
								$end_list[] = $endorsement['id'];
							}
						}
					}
					$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
					//$this->close_endorsement_prepare($car['id'], $jilu_id, $end_list);
				}
				elseif($jsoninfo ['code'] == 29 || ($jsoninfo ['code'] >= 31 && $jsoninfo ['code'] <= 34)){
					/*
					$car_scan_data = array (
						"scan_state" => 0,
						"scan_state_desc" => $jsoninfo['message'],
						"scan_state_time" => time (),
						"scan_stop_query" => $jilu_id
					);
					$car_model = M ( "Car" );
					$car_model->where ( "id={$car['id']}" )->save ( $car_scan_data );
					*/
				}
				else{
					return -1;
				}
				return 1;
			} else {
				$jilu_data = array (
					"car_id" => $car['id'],
					"city" => $region['city'],
					"money" => 0,
					"points" => 0,
					"all_nums" => 0,
					"add_nums" => 0,
					"edit_nums" => 0,
					"c_time" => time (),
					"port" => api_icar,
					"code" => $has_error,
					"state" => $scan_type 
					);
				$jilu_id = $jilu_model->add ( $jilu_data );
				return -1;
			}
		}
		return -1;
	}
	// 获取爱车坊token
	function get_icar_token() {
		$url = "http://120.26.57.239/api/getAccessToken?merKey=" . app_key_icar . "&merCode=" . app_id_icar;
		$output = $this->invokeHttpApi ( $url );
		$log = new Log ();
		$log->write ( "get_token:" . $output, 'DEBUG', '', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . '/Logs/Weizhang/' . date ( 'y_m_d' ) . '.log' );
		$jsoninfo = json_decode ( $output, true );
		$json_error = json_last_error();
		$token = null;
		if($json_error == JSON_ERROR_NONE){
			$token = $jsoninfo ['data'] [0] ['accessToken'];
		}
		return $token;
	}
	
	function invokeHttpApi($url, $params = null, $header = null, $post = false){
		if (!empty($params) && $post != true) {
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= is_string($params) ? $params : http_build_query($params);
        }
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		if($post){
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
		}
		else{
			curl_setopt ( $ch, CURLOPT_HTTPGET, true );
		}
		if(!empty($header)){
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		}
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 45);
		curl_setopt ( $ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		return $output;
	}
	
	function push($car_id, $end_id){
		$param = array (
			"car_id" => $car_id,
			"end_id" => $end_id
		);
			
		$weixin_url = "";
		if(runEnv == 'production'){
			$weixin_url = "http://weixin.xiaoxianlink.com";
		}
		elseif(runEnv == 'test'){
			$weixin_url = "http://wxdev.xiaoxianlink.com";
		}
		else{
			$weixin_url = "http://wx.xiaoxian.com";
		}
		$url = $weixin_url . "/index.php?g=weixin&m=api&a=push_weizhang";
		$this->invokeHttpApi($url, $param, null, true);
		
		$bizapi_url = "";
		if(runEnv == 'production'){
			$bizapi_url = "http://bizapi.xiaoxianlink.com";
		}
		elseif(runEnv == 'test'){
			$bizapi_url = "http://testapi.xiaoxianlink.com";
		}
		else{
			$bizapi_url = "http://ba.xiaoxian.com";
		}
		$url = $bizapi_url . "/index.php?g=bizapi&m=api&a=push_weizhang";
		$this->invokeHttpApi($url, $param, null, true);
	}
	
	function push_confirm($end_id){
		$param = array (
			"end_id" => $end_id
		);
			
		$weixin_url = "";
		if(runEnv == 'production'){
			$weixin_url = "http://weixin.xiaoxianlink.com";
		}
		elseif(runEnv == 'test'){
			$weixin_url = "http://wxdev.xiaoxianlink.com";
		}
		else{
			$weixin_url = "http://wx.xiaoxian.com";
		}
		$url = $weixin_url . "/index.php?g=weixin&m=api&a=push_confirm";
		$this->invokeHttpApi($url, $param, null, true);
	}
}
