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
		$url = "http://test.cx580.com:9000/InputsCondition.aspx";
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

	public function week() {
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
			$result = $this->scan_api($v);
			$data = array (
					"nums" => $count + $i 
			);
			$timing_model->where ( "id='$id'" )->save ( $data );
		}
		return 1;
		exit ();
	}

	public function index() {
		$car_id = $_REQUEST['car_id'];
		
		$car_model = M ( "Car" );
		$car = $car_model->where ( "id = $car_id" )->find ();
		
		$result = $this->scan_api($car);
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
	
	function scan_api($car){
		$result = -1;
		
		if(runEnv == 'production'){
			echo "chexingyi";
			$result = $this->scan_api_chexingyi($car);
		}
		else{
			echo "cheshouye";
			$result = $this->scan_api_cheshouye($car);
		}
		
		if($result < 0){
			echo "icar";
			$result = $this->scan_api_icar($car);
		}
		return $result;
	}
	
	function scan_api_chexingyi($car){
		$result = 0;
		$l_nums = mb_substr ( $car ['license_number'], 0, 1, 'utf-8' );
		if($l_nums == '桂'){
			$l_nums = '桂A';
		}
		else{
			$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		}
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		if (! empty ( $region )) {
			$result = $this->scan_api_chexingyi_one($car, $region);
		}
		
		$a_class = array("粤", "冀", "陕", "吉", "京", "沪", "苏", "鄂","琼", "渝", "甘", "黑", "宁", "青", "贵", "新", "浙", "皖", "湘");
		$result_s = 0;
		$model = M ();
		$list = $model->table ( "cw_user_car as uc" )->join ( "cw_user as u on u.id = uc.user_id" )->field ( "u.city" )->where ( "uc.car_id='{$car['id']}'" )->group ( "u.city" )->select ();
		foreach ( $list as $u ) {
			if ($u ['city'] != '' && $u ['city'] != null && $u ['city'] != $region ['city']) {
				$region = $region_model->where ( "city = '{$u['city']}'" )->find ();
				if(! empty ( $region )){
					if(!in_array($region['abbreviation'], $a_class ) || !in_array($l_nums, $a_class )){
						$r = $this->scan_api_chexingyi_one ( $car, $region, true);
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
	
	function scan_api_chexingyi_one($car, $region, $special = false){
		$app_id = app_id_chexingyi;
        $app_key = app_key_chexingyi;
		$carnumber = urlencode($car['license_number']);
		$url = "http://test.cx580.com:9000/queryindex.aspx?userid=$app_id&userpwd=$app_key&carnumber=$carnumber";
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
		
		$jilu_model = M ( "endorsement_jilu" );
		if(!empty($output)){
			$jsoninfo = json_decode($output, true);
			$car_model = M ( "Car" );
			$endorsement_model = M ( "Endorsement" );
			$log_model = M ( "Endorsement_log" );
			$jilu_data = array (
					"car_id" => $car['id'],
					"city" => $region['city'],
					"money" => 0,
					"points" => 0,
					"all_nums" => 0,
					"add_nums" => 0,
					"edit_nums" => 0,
					"c_time" => time (),
					"port" => api_chexingyi,
					"code" => $jsoninfo ['ErrorCode'],
					"state" => 1 
			);
			$errorCodes = array (-6, -61, -62, -63);
			$jilu_id = $jilu_model->add ( $jilu_data );
			if ($jsoninfo ['ErrorCode'] == 0) {
				if (isset($jsoninfo['Records']) && $jsoninfo['Records']) {
					foreach ( $jsoninfo ['Records'] as $v ) {
						$jilu_data ['all_nums'] ++;
						$jilu_data ['money'] += $v ['count'];
						$jilu_data ['points'] += $v['Degree'];
						$time = strtotime ( $v ['Time'] );
						$endorsement = $endorsement_model->where ( "car_id = '{$car['id']}' and time = '$time'" )->find ();
						if (empty ( $endorsement )) {
							$city = isset ( $v ['location_name'] ) ? mb_substr($v ['location_name'], -4) : $region['city'];
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
							$jilu_data ['add_nums'] ++;
							$data = array (
									"end_id" => $endorsement_model->getLastInsID (),
									"state" => 1,
									"c_time" => time (),
									"type" => 0 
							);
							$log_model->add ( $data );
						}
					}
					$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
				}
				return 1;
			} elseif(in_array($jsoninfo ['ErrorCode'], $errorCodes)){
				$car_scan_data = array (
					"scan_state" => 0,
					"scan_state_desc" => "输入的车辆信息有误，请查证后重新输入",
					"scan_state_time" => time (),
					"scan_stop_query" => $jilu_id
				);
				$car_model->where ( "id={$car['id']}" )->save ( $car_scan_data );
				return 1;
			}else{
				return -1;
			}
		}else{
			$jilu_data = array (
				"car_id" => $car['id'],
				"city" => $region['city'],
				"money" => 0,
				"points" => 0,
				"all_nums" => 0,
				"add_nums" => 0,
				"edit_nums" => 0,
				"c_time" => time (),
				"port" => api_chexingyi,
				"code" => -9999,
				"state" => 1 
			);
			$jilu_id = $jilu_model->add ( $jilu_data );
			return -1;
		}
		return -1;
	}
	
	function scan_api_cheshouye( $car ) {
		$result = 0;
		$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		if (! empty ( $region )) {
			$result = $this->scan_api_cheshouye_one ( $car, $region );
		}
		$result_s = 0;
		$model = M ();
		$list = $model->table ( "cw_user_car as uc" )->join ( "cw_user as u on u.id = uc.user_id" )->field ( "u.city" )->where ( "uc.car_id='{$car['id']}'" )->group ( "u.city" )->select ();
		foreach ( $list as $u ) {
			if ($u ['city'] != '' && $u ['city'] != null && $u ['city'] != $region ['city']) {
				$region = $region_model->where ( "city = '{$u['city']}'" )->find ();
				if(! empty ( $region )){
					$r = $this->scan_api_cheshouye_one ( $car, $region );
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
	function scan_api_cheshouye_one($car, $region) {
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
		
		$jilu_model = M ( "endorsement_jilu" );
		if(!empty($output)){
			$jsoninfo = json_decode ( $output, true );
			$endorsement_model = M ( "Endorsement" );
			$log_model = M ( "Endorsement_log" );
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
				"state" => 1 
			);
			$jilu_id = $jilu_model->add ( $jilu_data );
			if ($jsoninfo ['status'] == 2001) {
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
						$jilu_data ['add_nums'] ++;
						$data = array (
								"end_id" => $endorsement_model->getLastInsID (),
								"state" => 1,
								"c_time" => time (),
								"type" => 0 
						);
						$log_model->add ( $data );
					}
				}
				$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
				return 1;
			} elseif ($jsoninfo ['status'] == 2000) {
				return 1;
			} elseif($jsoninfo ['status'] == 5008){
				$car_scan_data = array (
					"scan_state" => 0,
					"scan_state_desc" => "输入的车辆信息有误，请查证后重新输入",
					"scan_state_time" => time (),
					"scan_stop_query" => $jilu_id
				);
				$car_model->where ( "id={$car['id']}" )->save ( $car_scan_data );
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
				"code" => -9999,
				"state" => 1 
			);
			$jilu_id = $jilu_model->add ( $jilu_data );
			return -1;
		}
		return -1;
	}
	
	function scan_api_icar($car) {
		$result = 0;
		$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		$region_model = M ( "Region" );
		$region = $region_model->where ( "nums = '$l_nums'" )->find ();
		if (! empty ( $region )) {
			$result = $this->scan_api_icar_one ( $car, $region );
		}
		$result_s = 0;
		$model = M ();
		$list = $model->table ( "cw_user_car as uc" )->join ( "cw_user as u on u.id = uc.user_id" )->field ( "u.city" )->where ( "uc.car_id='{$car['id']}'" )->group ( "u.city" )->select ();
		foreach ( $list as $u ) {
			if ($u ['city'] != '' && $u ['city'] != null && $u ['city'] != $region ['city']) {
				$region = $region_model->where ( "city = '{$u['city']}'" )->find ();
				if(! empty ( $region )){
					$r = $this->scan_api_icar_one ( $car, $region );
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
	function scan_api_icar_one($car, $region) {
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
			
			$jilu_model = M ( "endorsement_jilu" );
			if(!empty($output)){
				$jsoninfo = json_decode ( $output, true );
				$endorsement_model = M ( "Endorsement" );
				$log_model = M ( "Endorsement_log" );
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
					"state" => 1
				);
				$jilu_id = $jilu_model->add ( $jilu_data );
				if ($jsoninfo ['code'] == 0) {
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
								$jilu_data ['add_nums'] ++;
								$data = array (
										"end_id" => $endorsement_model->getLastInsID (),
										"state" => 1,
										"c_time" => time (),
										"type" => 0
								);
								$log_model->add ( $data );
							}
						}
					}
					$jilu_model->where ( "id='$jilu_id'" )->save ( $jilu_data );
				}
				elseif($jsoninfo ['code'] == 29 || ($jsoninfo ['code'] >= 31 && $jsoninfo ['code'] <= 34)){
					$car_scan_data = array (
						"scan_state" => 0,
						"scan_state_desc" => $jsoninfo['message'],
						"scan_state_time" => time (),
						"scan_stop_query" => $jilu_id
					);
					$car_model->where ( "id={$car['id']}" )->save ( $car_scan_data );
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
					"code" => -9999,
					"state" => 1 
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
		$token = $jsoninfo ['data'] [0] ['accessToken'];
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
	
}
