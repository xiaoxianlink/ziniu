<?php

namespace Order\Controller;

use Common\Controller\HomeBaseController;
use Think\Log;

class ApiController extends HomeBaseController {
	
	// 检查点单接口
	public function examine_order() {
		$log = new Log ();
		$log->write ( "examine_order");
		$log->write ( "examine_order", 'DEBUG', '', '/data/Logs/xiaoxianlink.com/ziniu/Weizhang/' . date ( 'y_m_d' ) . '.log' );
		//echo "test";
		$model = M();
		$roles = $model->field ( "a.id as order_id,a.pay_sn,a.order_sn,a.car_idc.license_number,c.time,c.area,c.code,c.money,c.points,a.last_time,a.pay_money,a.order_status,a.pay_type,d.id as user_id,d.phone,a.services_id,a.money as order_money,a.endorsement_id" )->table ( "cw_order as a" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "a.order_status = 1 or a.order_status = 2 or a.order_status = 3" )->select ();
		foreach ( $roles as $k => $v ) {
			$turn_order_model = M ( "turn_order" );
			$to_list = $turn_order_model->field ( "tos.id,tos.c_time,tos.state,tos.l_time,tos.services_id as s_id,tos.money" )->table ( "cw_turn_order as tos" )->where ( "tos.order_id = '{$v['order_id']}'" )->select ();
			foreach ( $to_list as $c => $p ) {
				if($p["s_id"] != null){
					$s_ids[] = $p["s_id"];
				}
			}
			$order_model = M ( "Order" );
			foreach ( $to_list as $c => $p ) { // 推单状态处理
				if ($p ['state'] == 0) {
					$time = jishi1 - (time () - $p ['l_time']);
					if ($time <= 0) { // 超时
						$fuwu = $this->find_fuwu($v["car_id"], $v["code"], $v["money"],$v["points"], $v["area"], $s_ids);
						if (! empty ( $fuwu )) { // 转单
							$data = array (
									"state" => 2,
									"l_time" => time () 
							);
							$turn_order_model->where ( "id='{$p['id']}'" )->save ( $data );
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
								$services_model->where ( "id='{$fuwu['services_id']}'" )->save ();
							}
							/*
							// 转钱
							$bank_model = M ( "bank" );
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							if (! empty ( $bank_info_older )) {
								$data = array (
										"money" => ($bank_info_older ['money'] - $p['money']) > 0 ? ($bank_info_older ['money'] - $p['money']) : 0,
										"end_money" => ($bank_info_older ['end_money'] - $p['money']) > 0 ? ($bank_info_older ['end_money'] - $p ['money']) : 0
								);
								$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info_older ['bank_id'],
									"income_money" => 0,
									"pay_money" => $p['money'],
									"end_money" => $bank_info_older ['end_money'],
									"user_money" => $bank_info_older ['user_money'],
									"money" => $bank_info_older ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
							
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
						} else { // 取消订单
							$data = array (
									"state" => 6,
									"l_time" => time () 
							);
							$turn_order_model->where ( "id='{$p['id']}'" )->save ( $data );
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
							/*
							//扣钱
							$bank_model = M ( "bank" );
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							if (! empty ( $bank_info_older )) {
								$data = array (
										"money" => ($bank_info_older ['money'] - $p['money']) > 0 ? ($bank_info_older ['money'] - $p['money']) : 0,
										"end_money" => ($bank_info_older ['end_money'] - $p['money']) > 0 ? ($bank_info_older ['end_money'] - $p['money']) : 0
								);
								$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info_older ['bank_id'],
									"income_money" => 0,
									"pay_money" => $p['money'],
									"end_money" => $bank_info_older ['end_money'],
									"user_money" => $bank_info_older ['user_money'],
									"money" => $bank_info_older ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
							*/
						}
					}
				} else if ($p ['state'] == 3) {
					$time = jishi2 - (time () - $p ['l_time']);
					if ($time <= 0) { // 超时
						$fuwu = $this->find_fuwu($v["car_id"], $v["code"], $v["money"],$v["points"], $v["area"], $s_ids);
						if (! empty ( $fuwu )) { // 转单
							$data = array (
									"state" => 2,
									"l_time" => time () 
							);
							$turn_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							$data = array (
									"order_id" => $v ['order_id'],
									"services_id" => $fuwu ['s_id'],
									"sod_id" => $fuwu ['so_id'],
									"so_type" => $fuwu ['so_type'],
									"money" => $fuwu ['money'],
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
							$services_info = $services_model->where ( "id='{$fuwu ['s_id']}'" )->find ();
							if (! empty ( $services_info )) {
								$data = array (
										"all_nums" => $services_info ['all_nums'] + 1 
								);
								$services_model->where ( "id='{$fuwu['s_id']}'" )->save ();
							}
							// 转钱
							$bank_model = M ( "bank" );
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							if (! empty ( $bank_info_older )) {
								$data = array (
										"money" => ($bank_info_older ['money'] - $p['money']) > 0 ? ($bank_info_older ['money'] - $p['money']) : 0,
										"end_money" => ($bank_info_older ['end_money'] - $p['money']) > 0 ? ($bank_info_older ['end_money'] - $p['money']) : 0
								);
								$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info_older ['bank_id'],
									"income_money" => 0,
									"pay_money" => $p['money'],
									"end_money" => $bank_info_older ['end_money'],
									"user_money" => $bank_info_older ['user_money'],
									"money" => $bank_info_older ['money'],
									"order_id" => $v ['order_id'],
									"c_time" => time ()
							);
							$jl_model = M ( "services_jilu" );
							$jl_model->add ( $data );
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
						} else { // 取消订单
							$data = array (
									"state" => 2,
									"l_time" => time () 
							);
							$turn_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							$data = array (
									"order_status" => 8 
							);
							$order_model->where ( "id='{$v['order_id']}'" )->save ( $data );
							$data = array (
									"state" => 6
							);
							$turn_order_model->where ( "id='{$p['id']}'" )->save ( $data );
							// 修改违章状态
							$data = array (
									"is_manage" => 0,
									"manage_time" => time () 
							);
							$endorsement_model = M ( "Endorsement" );
							$endorsement_model->where ( "id={$v['endorsement_id']}" )->save ( $data );
							//扣钱
							$bank_model = M ( "bank" );
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							if (! empty ( $bank_info_older )) {
								$data = array (
										"money" => ($bank_info_older ['money'] - $p['money']) > 0 ? ($bank_info_older ['money'] - $p['money']) : 0,
										"end_money" => ($bank_info_older ['end_money'] - $p['money']) > 0 ? ($bank_info_older ['end_money'] - $p['money']) : 0
								);
								$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
							}
							// 记录
							$bank_info_older = $bank_model->where ( "bank_id='{$v['services_id']}'" )->find ();
							$data = array (
									"services_id" => $bank_info_older ['bank_id'],
									"income_money" => 0,
									"pay_money" => $p['money'],
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
				}
			}
		}
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
		
		$a_class = array("京", "沪", "津", "渝");
		$l_nums = "";
		$l_nums_a = mb_substr ( $car ['license_number'], 0, 1, 'utf-8' );
		if(in_array($l_nums_a, $a_class)){
			$l_nums = $l_nums_a;
		}
		else{
			$l_nums = mb_substr ( $car ['license_number'], 0, 2, 'utf-8' );
		}
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
		$so_sql = "select srv.id as services_id, so.id as so_id, so.money from cw_services as srv, cw_services_city as scity, cw_services_code as scode, cw_services_order as so where $where srv.id = scity.services_id and srv.id = scode.services_id and srv.id = so.services_id and srv.state = 0 and srv.grade > 4 and ((scity.code = $city_id1 and scity.state = 0) or (scity.code = $city_id2 and scity.state = 0)) and ((scode.code = '$code' or scode.code = '$s_code') and scode.state = 0 ) and so.violation = '$code' and (so.code = $city_id1 or so.code = $city_id2) group by srv.id order by money asc ";
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
}




