<?php

namespace Admin_new\Controller;

use Common\Controller\AdminbaseController;
use Weixin\Controller\IndexController;
use Think\Log;

include_once 'application/Weixin/Conf/config.php';
class JiaoyiController extends AdminbaseController {
	protected $jiaoyi_model;
	public function _initialize() {
		parent::_initialize ();
		$_SESSION ['dingyue'] = '';
		$_SESSION ['fuwu'] = '';
		$_SESSION ['xitong'] = '';
		$this->jiaoyi_model = D ( "Common/coupon" );
	}
	function dingdan() {
		$type = $_POST ['che_type'];
		$time_start = strtotime ( $_POST ['che_time_start'] );
		$time_end = strtotime ( $_POST ['che_time_end'] );
		$number = $_POST ['che_number'];
		$water = $_POST ['che_water'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['che_type1'] = $type;
			$_SESSION ['jiao'] ['che_time_start1'] = $time_start;
			$_SESSION ['jiao'] ['che_time_end1'] = $time_end;
			$_SESSION ['jiao'] ['che_number1'] = $number;
			$_SESSION ['jiao'] ['che_water1'] = $water;
		} else {
			$type = $_SESSION ['jiao'] ['che_type1'];
			$time_start = $_SESSION ['jiao'] ['che_time_start1'];
			$time_end = $_SESSION ['jiao'] ['che_time_end1'];
			$number = $_SESSION ['jiao'] ['che_number1'];
			$water = $_SESSION ['jiao'] ['che_water1'];
		}
		$this->assign ( "time_start", (empty ( $time_start ) ? '' : date ( "Y-m-d H:i:s", $time_start )) );
		$this->assign ( "time_end", (empty ( $time_end ) ? '' : date ( "Y-m-d H:i:s", $time_end )) );
		$this->assign ( "number", $number );
		$this->assign ( "type", $type );
		$this->assign ( "water", $water );
		$where = "1=1";
		if (! empty ( $type )) {
			$where .= " and order_status=$type";
		} else {
			$where .= " and (order_status != 5 and order_status != 7 and order_status != 8)";
		}
		if (! empty ( $number )) {
			$where .= " and license_number like '%$number%'";
		}
		if (! empty ( $time_start ) && ! empty ( $time_end )) {
			$where .= " and (a.last_time between $time_start and $time_end)";
		}
		if (! empty ( $water )) {
			$where .= " and a.pay_sn like '%$water%'";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.last_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->jiaoyi_model->table ( "cw_order as a" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "a.id as order_id,a.pay_sn,a.order_sn,c.license_number,c.time,c.area,c.code,c.money,c.points,a.last_time,a.pay_money,a.order_status,a.pay_type" )->table ( "cw_order as a" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		//$this->examine_order (); // 查询 订单是否超时
		foreach ( $roles as $k => $v ) {
			$turn_order_model = M ();
			$to_list = $turn_order_model->field ( "tos.id,tos.c_time,tos.state,tos.l_time,tos.money,s.id as s_id,s.phone,s.services_sn" )->table ( "cw_turn_order as tos left join cw_services as s on s.id = tos.services_id")->where ( "tos.order_id = '{$v['order_id']}'" )->select ();
			foreach ( $to_list as $c => $p ) { // 推单状态处理
				$to_list [$c] ['so_id'] = $v ['order_sn'] . substr ( $p ['c_time'], - 2 ) . $p ['s_id'];
				$time = '--';
				if ($p ['state'] == 0) {
					$time = jishi1 + $p ['l_time'];
				} else if ($p ['state'] == 3) {
					$time = jishi2 + $p ['l_time'];
				}
				$to_list [$c] ['tmr'] = $time;
			}
			$roles [$k] ['to_list'] = $to_list;
		}
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function time_his($time) {
		$hour = $time / 3600;
		return 1;
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
	
	function fuwu() {
		$name = $_POST ['fu_name'];
		$number = $_POST ['fu_number'];
		$phone = $_POST ['fu_phone'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['fu_name2'] = $name;
			$_SESSION ['jiao'] ['fu_number2'] = $number;
			$_SESSION ['jiao'] ['fu_phone2'] = $phone;
		} else {
			$name = $_SESSION ['jiao'] ['fu_name2'];
			$number = $_SESSION ['jiao'] ['fu_number2'];
			$phone = $_SESSION ['jiao'] ['fu_phone2'];
		}
		$this->assign ( "array_post", array (
				$name,
				$number,
				$phone 
		) );
		$where = "1=1";
		if (! empty ( $name )) {
			$where .= " and b.name like '%$name%'";
		}
		if (! empty ( $phone )) {
			$where .= " and a.phone like '%$phone%'";
		}
		if (! empty ( $number )) {
			$where .= " and a.services_sn like '%$number%'";
		}
	   ///
		unset($_SESSION['fu_time_start']);
		unset($_SESSION['fu_time_end']);
		unset($_SESSION['type']);
		$state = $_GET ['state'];
		if (! empty ( $state )) {
			$where = " a.state='$state'-1";
			$this->assign ( "vstate", $state );
		}
		$count = $this->jiaoyi_model->table ( "cw_services as a" )->join ( "cw_bank as b on a.id=b.bank_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "a.id,a.phone,a.status,b.name,b.user_bank,b.user_number,b.user_money,b.end_money,b.money,a.state,b.bank_id,a.services_sn,b.type" )->table ( "cw_services as a" )->join ( "cw_bank as b on a.id=b.bank_id" )->where ( $where )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( 'str', $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_state = $this->jiaoyi_model->field ( "count(a.state) as sum" )->table ( "cw_services as a" )->join ( "cw_bank as b on a.id=b.bank_id" )->group ( "state" )->select ();
		$num_0 = $roles_state [0] [sum];
		$num_1 = $roles_state [1] [sum];
		$num_2 = $num_0 + $num_1;
		$this->assign ( "state", array (
				$num_0,
				$num_1,
				$num_2 
		) );
		$roles_money = $this->jiaoyi_model->field ( "sum( b.money) as money,sum(b.user_money) as user_money,sum(end_money) as end_money" )->table ( "cw_services as a" )->join ( "cw_bank as b on a.id=b.bank_id" )->select ();
		$this->assign ( "money", $roles_money );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function fuwu_message(){
	    $user_id = $_REQUEST ['id'];
	    //判断user_id是否为空
	    if(!empty($user_id)){
	       $_SESSION['jiaoyi_id']=$user_id;
	    }else{
	       $user_id=$_SESSION['jiaoyi_id'];
	    }
	    $services_sn=$_REQUEST['services_sn'];
	    $where = "sjl.services_id = '$user_id'";
	    //查询条件
	    $time_start = strtotime ( $_REQUEST ['fu_time_start'] );
	    $time_end = strtotime ( $_REQUEST ['fu_time_end'] );
	    $type = $_REQUEST ['type'];
	    $state = $_REQUEST ['state'];
	    //判断是否是出表单处提交的数据，如果是就重置session值
	    if($_REQUEST['leixing']==1){
	        $_SESSION['fu_time_start']=$time_start;
	        $_SESSION['fu_time_end']=$time_end;
	        $_SESSION['type']=$type;
	    }
	    if (! empty ( $_SESSION['type'] )) {
	        $type=$_SESSION['type'];
	         $where .= " and o.pay_type = ".$_SESSION['type'];
	    }
	     if (! empty ( $_SESSION['fu_time_start'] )) {
	        $fu_time_start=date("Y/m/d H:i:s",$_SESSION['fu_time_start']);
	        $where .= "  and sjl.c_time > ".$_SESSION['fu_time_start'];
	    }
	    if (! empty ( $_SESSION['fu_time_end'] )) {
	        $fu_time_end=date("Y/m/d H:i:s",$_SESSION['fu_time_end']);
	        $where .= "  and sjl.c_time < ".$_SESSION['fu_time_end'] ;
	    } 
	    //echo "--user_id= ".$user_id." --条件-- ".$where." -----表单-- ".$_REQUEST['leixing']." ---页数-- ".$_REQUEST['p'];
	    $this->assign("select",array($type,$fu_time_start,$fu_time_end));
	    $model=M();
	    $count=$model->table ( "cw_services_jilu as sjl" )->join ( "cw_order as o on o.id=sjl.order_id" )->where ( $where )->count ();
	    $page = $this->page ( $count, 35 );
	    $date = $model->field ( "sjl.*,o.order_sn" )->table ( "cw_services_jilu as sjl" )->join ( "cw_order as o on o.id=sjl.order_id" )->where ( $where )->order("sjl.id desc")->limit ( $page->firstRow . ',' . $page->listRows )->select ();
	    $this->assign("str",$date);
	    $info = $model->field ( "s.services_sn,s.phone,b.money,b.user_money" )->table ( "cw_services as s" )->join ( "cw_bank as b on b.bank_id = s.id" )->where ( "s.services_sn = '{$services_sn}'" )->find ();
	    $money=array($services_sn,$info ['phone'],$info ['user_money'],$info['money'],$user_id);
	    $this->assign("money",$money);
	    $this->assign ( "Page", $page->show ( 'Admin' ) );
	    $this->display ();
	}
	function fuwu_div() {
		$user_id = $_REQUEST ['id'];
		$time_long = $_REQUEST ['time'];
		$services_sn = $_REQUEST ['services_sn'];
		$where = "sjl.services_id = '$user_id'";
		$str = '';
		$date = $this->jiaoyi_model->field ( "sjl.*,o.order_sn" )->table ( "cw_services_jilu as sjl" )->join ( "cw_order as o on o.id=sjl.order_id" )->where ( $where )->order ( "sjl.c_time desc limit 20" )->select ();
		foreach ( $date as $k => $v ) {
			$str .= "<tr>
                            <td>" . date ( "Y/m/d H:i:s", $v ['c_time'] ) . "</td>
                            <td>$v[income_money]</td>
                            <td>$v[pay_money]</td>
                            <td>$v[end_money]</td>
                            <td>$v[user_money]</td>
                            <td>$v[money]</td>
                            <td>$v[order_sn]</td>
                        </tr>";
		}
		$info = $this->jiaoyi_model->field ( "s.services_sn,s.phone,b.money,b.user_money" )->table ( "cw_services as s" )->join ( "cw_bank as b on b.bank_id = s.id" )->where ( "s.services_sn = '{$services_sn}'" )->find ();
		$astr = '<div class="count_txt" >供应商编号 &nbsp;&nbsp;' . $services_sn . ' </div><div class="count_txt">手机号&nbsp;&nbsp;' . $info ['phone'] . ' </div><div class="count_txt">可提现金额&nbsp;&nbsp; ' . $info ['user_money'] . ' </div><div class="count_txt"> 账户余额&nbsp;&nbsp;' . $info['money'] . '</div>';
		$data = array (
				0 => $str,
				1 => $astr,
				2 => $user_id 
		);
		$this->ajaxReturn ( $data );
	}
	function fuwu_div2() {
		$user_id = $_REQUEST ['id'];
		$time_long = $_REQUEST ['time'];
		$time_start = strtotime ( $_REQUEST ['time_start'] );
		$time_end = strtotime ( $_REQUEST ['time_end'] );
		$type = $_REQUEST ['type'];
		$state = $_REQUEST ['state'];
		$where = "sjl.services_id = '$user_id'";
		if (! empty ( $type )) {
			$where .= " and o.pay_type = $type";
		}
		if (! empty ( $time_start )) {
			$where .= "  and sjl.c_time>$time_start ";
		}
		if (! empty ( $time_end )) {
			$where .= "  and sjl.c_time<$time_end ";
		}
		switch ($state) {
			case 1 :
				$where .= " and to_days(FROM_UNIXTIME( sjl.c_time, '%Y%m%d' )) = to_days(now())";
				break;
			case 2 :
				$where .= " and to_days(now()) - to_days( FROM_UNIXTIME(sjl.c_time, '%Y%m%d') ) <= 1";
				break;
			case 3 :
				$where .= " and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(FROM_UNIXTIME( sjl.c_time, '%Y%m%d' ))";
				break;
			default :
				;
				break;
		}
		$str = '<tr><th>账户变动时间</th><th>收入金额</th><th>支出金额</th><th>未结算金额</th><th>可提现金额</th><th>账户余额</th><th>订单号</th><tr/>';
		$date = $this->jiaoyi_model->field ( "sjl.*,o.order_sn" )->table ( "cw_services_jilu as sjl" )->join ( "cw_order as o on o.id=sjl.order_id" )->where ( $where )->order ( "sjl.c_time desc limit 20" )->select ();
		foreach ( $date as $k => $v ) {
			$str .= "<tr>
                            <td>" . date ( "Y/m/d H:i:s", $v ['c_time'] ) . "</td>
                            <td>$v[income_money]</td>
                            <td>$v[pay_money]</td>
                            <td>$v[end_money]</td>
                            <td>$v[user_money]</td>
                            <td>$v[money]</td>
                            <td>$v[order_sn]</td>
                        </tr>";
		}
		$astr = '<div class="count_txt" >供应商编号 &nbsp;&nbsp;' . $date [0] [id] . ' </div><div class="count_txt">手机号&nbsp;&nbsp;' . $date [0] [phone] . ' </div><div class="count_txt">可提现金额&nbsp;&nbsp; ' . $date [0] [user_money] . ' </div><div class="count_txt"> 账户余额&nbsp;&nbsp;' . $date [0] [balance_money] . '</div>';
		$strs=" ";
		$data = array (
				0 => $str,
				1 => $astr,
				2 => $user_id ,
		        3=> $strs
		);
		$this->ajaxReturn ( $data );
	}
	function fuwu_update() {
		$id = $_GET ['id'];
		$state = $_GET ['state'];
		if ($state == 0) {
			$time = strtotime ( date ( "Y-m-d H:i:s" ) );
			if ($this->jiaoyi_model->execute ( "update cw_services set state=1,time=$time where id=$id" ) > 0) {
				$this->success ( "封存成功！" );
			} else {
				$this->error ( "封存失败！" );
			}
		} elseif ($state == 1) {
			if ($this->jiaoyi_model->execute ( "update cw_services set state=0  where id=$id" ) > 0) {
				$this->success ( "启封成功！" );
			} else {
				$this->error ( "启封失败！" );
			}
		}
	}
	function history() {
		$code = $_POST ['fu_code'];
		$time_start = strtotime ( $_POST ['fu_time_start'] );
		$time_end = strtotime ( $_POST ['fu_time_end'] );
		$number = $_POST ['fu_number'];
		$numbers = $_POST ['fu_numbers'];
		$order_status = $_GET ['order_status'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['fu_code4'] = $code;
			$_SESSION ['jiao'] ['fu_time_start4'] = $time_start;
			$_SESSION ['jiao'] ['fu_time_end4'] = $time_end;
			$_SESSION ['jiao'] ['fu_number4'] = $number;
			$_SESSION ['jiao'] ['fu_numbers4'] = $numbers;
		} else {
			$code = $_SESSION ['jiao'] ['fu_code4'];
			$time_start = $_SESSION ['jiao'] ['fu_time_start4'];
			$time_end = $_SESSION ['jiao'] ['fu_time_end4'];
			$number = $_SESSION ['jiao'] ['fu_number4'];
			$numbers = $_SESSION ['jiao'] ['fu_numbers4'];
		}
		$where = "(order_status = 5 or order_status = 7 or order_status = 8)";
		if (! empty ( $order_status )) {
			$where = " order_status=$order_status";
		}
		if (! empty ( $code )) {
			$where .= " and c.license_number like '%$code%'";
		}
		if (! empty ( $time_start ) && ! empty ( $time_end )) {
			$where .= " and (a.last_time between $time_start and $time_end)";
		}
		if (! empty ( $number )) {
			$where .= " and d.services_sn like '%$number%' ";
		}
		if (! empty ( $numbers )) {
			$where .= " and order_sn like '%$numbers%'";
		}
		$this->assign ( "array_post", array (
				$code,
				$time_start = empty ( $time_start ) ? '' : date ( "Y-m-d H:i:s", $time_start ),
				$time_end = empty ( $time_end ) ? '' : date ( "Y-m-d H:i:s", $time_end ),
				$number,
				$numbers 
		) );
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.last_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->jiaoyi_model->table ( "cw_order as a" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "a.id as order_id,a.pay_sn,a.order_sn,c.license_number,c.time,c.area,c.code,c.money,c.points,a.last_time,a.pay_money,a.money as end_money,a.order_status,a.pay_type,d.id as user_id,d.phone,d.services_sn,a.so_id,a.c_time as o_time" )->table ( "cw_order as a" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		foreach ( $roles as $k => $v ) {
			$true_order_model = M ();
			$to_list = $true_order_model->field ( "tos.id,so.id as so_id,s.id as s_id,s.phone,tos.c_time,tos.state,tos.l_time,s.services_sn" )->table ( "cw_turn_order as tos" )->join ( "cw_services_order as so on so.id=tos.sod_id", 'left' )->join ( "cw_services as s on s.id=so.services_id", 'left' )->where ( "tos.order_id = '{$v['order_id']}' and tos.sod_id = '{$v['so_id']}'" )->find ();
			$roles [$k] ['so_id'] = $v ['order_sn'] . substr ( $to_list ['c_time'], - 2 ) . $v ['user_id'];
		}
		$this->assign ( 'str', $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$roles_finish = $this->jiaoyi_model->field ( "count(order_status) as counts" )->table ( "cw_order as a" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "order_status=5" )->select ();
		$roles_undo = $this->jiaoyi_model->field ( "count(order_status) as counts" )->table ( "cw_order as a" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "order_status=7" )->select ();
		$roles_counts = $this->jiaoyi_model->field ( "count(order_status) as counts" )->table ( "cw_order as a" )->join ( "cw_endorsement as c on c.id=a.endorsement_id" )->join ( "cw_services as d on a.services_id=d.id" )->where ( "order_status = 5 or order_status = 7" )->select ();
		$this->assign ( "order_status", array (
				$roles_counts [0] [counts],
				$roles_finish [0] [counts],
				$roles_undo [0] [counts] 
		) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function tixian() {
		$time_start = strtotime ( $_POST ['fu_time_start'] );
		$time_end = strtotime ( $_POST ['fu_time_end'] );
		$type = $_POST ['fu_type'];
		$state = $_GET ['state'];
		$long_time = $_GET ['time'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['fu_time_start3'] = $time_start;
			$_SESSION ['jiao'] ['fu_time_end3'] = $time_end;
			$_SESSION ['jiao'] ['fu_type3'] = $type;
		} else {
			$time_start = $_SESSION ['jiao'] ['fu_time_start3'];
			$time_end = $_SESSION ['jiao'] ['fu_time_end3'];
			$type = $_SESSION ['jiao'] ['fu_type3'];
		}
		$this->assign ( "type", $type );
		$this->assign ( "time_start", $time_start );
		$this->assign ( "time_end", $time_end );
		$where = "1=1";
		if (! empty ( $time_start ) && ! empty ( $time_end )) {
			$where .= " and (c.please_time between '$time_start' and '$time_end')";
		}
		if (! empty ( $type )) {
			$where .= " and c.bank_state='$type'";
		}
		if (! empty ( $state )) {
			$where = " bank_state='$state'";
		}
		switch ($long_time) {
			case 1 :
				$where .= " and to_days(FROM_UNIXTIME(now())) - to_days( FROM_UNIXTIME( c.please_time, '%Y%m%d' ) ) <= 1";
				break;
			case 2 :
				$where .= " and to_days(FROM_UNIXTIME( c.please_time, '%Y%m%d' )) = to_days(now())";
				break;
			
			case 8 :
				$where .= " and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(FROM_UNIXTIME( c.please_time, '%Y%m%d' ))";
				break;
			default :
				;
				break;
		}
		$this->assign ( "time", $long_time );
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "c.please_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->jiaoyi_model->table ( "cw_expend as c" )->join ( "cw_bank as b on c.expend_id=b.id" )->join ( "cw_services as a on a.id=b.bank_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "c.id as ex_id,a.id,c.please_money,c.please_time,c.operate,c.dispose_user,c.dispose_time,a.phone,c.type,c.bank_state,b.name,c.user_bank,b.user_number,b.user_money,b.end_money,b.money,a.services_sn,c.expend_sn,c.card_number,c.tixian_name" )->table ( "cw_expend as c" )->join ( "cw_bank as b on c.expend_id=b.id" )->join ( "cw_services as a on a.id=b.bank_id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		/* start*/
		foreach($roles as $k=>$r){
		    $roles[$k]['expend_sn'] = $r['id'] . $r['ex_id'] . time();
		}
		/* end*/
		$this->assign ( 'str', $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		/*start */
		$roles_state1 = $this->jiaoyi_model->field ( "count(bank_state) as num" )->table ( "cw_expend as c" )->join ( "cw_bank as b on c.expend_id=b.id" )->join ( "cw_services as a on a.id=b.bank_id" )->where ( "c.bank_state = 1" )->find ();
		$roles_state2 = $this->jiaoyi_model->field ( "count(bank_state) as num" )->table ( "cw_expend as c" )->join ( "cw_bank as b on c.expend_id=b.id" )->join ( "cw_services as a on a.id=b.bank_id" )->where ( "c.bank_state = 2" )->find ();
		$state_1 = $roles_state1 [num];
		$state_2 = $roles_state2 [num];
		/*end */
		$state_3 = $state_1 + $state_2;
		$this->assign ( "array_state", array (
				$state_1,
				$state_2,
				$state_3 
		) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function youhui() {
		$name = $_POST ['you_name'];
		$valid = $_POST ['you_valid'];
		$use = $_POST ['you_use'];
		$state = $_POST ['you_state'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['you_name5'] = $name;
			$_SESSION ['jiao'] ['you_valid5'] = $valid;
			$_SESSION ['jiao'] ['you_use5'] = $use;
			$_SESSION ['jiao'] ['you_state5'] = $state;
		} else {
			$name = $_SESSION ['jiao'] ['you_name5'];
			$valid = $_SESSION ['jiao'] ['you_valid5'];
			$use = $_SESSION ['jiao'] ['you_use5'];
			$state = $_SESSION ['jiao'] ['you_state5'];
		}
		$this->assign ( "name", $name );
		$this->assign ( "valid", $valid );
		$this->assign ( "use", $use );
		$this->assign ( "state", $state );
		$where = "1=1";
		if (! empty ( $name )) {
			$where .= " and a.name like '%$name%'";
		}
		if (! empty ( $use )) {
			$where .= " and is_used=$use-1";
		}
		if (! empty ( $state )) {
			$where .= " and state =$state";
		}
		$count = $this->jiaoyi_model->table ( "cw_user_coupon as b" )->join ( "cw_coupon as a on b.coupon_id=a.id" )->join ( "cw_user as c on b.user_id=c.id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "a.state,a.id,a.name,a.condition,a.money,a.start_time,a.expiration_time,c.username,b.is_used,b.use_time,b.card" )->table ( "cw_user_coupon as b" )->join ( "cw_coupon as a on b.coupon_id=a.id" )->join ( "cw_user as c on b.user_id=c.id" )->where ( $where )->order ( "b.id desc" )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	function dispose() {
		$transfer_sn = isset ( $_REQUEST ['transfer_sn'] ) ? $_REQUEST ['transfer_sn'] : '';
		$pay_type = isset ( $_REQUEST ['pay_type'] ) ? $_REQUEST ['pay_type'] : '';
		$remark = isset ( $_REQUEST ['remark'] ) ? $_REQUEST ['remark'] : '';
		$id = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : '';
		$model = M ( "expend" );
		$data = array (
				"transfer_sn" => $transfer_sn,
				"pay_type" => $pay_type,
				"remark" => $remark,
				"bank_state" => 2 ,
		         /*start */
		        "dispose_user" => $_SESSION['name'],
		        "create_time"=>time(),
		        "dispose_time"=>time()
		         /*end */
		);
	    $model->where ( "id='$id'" )->save ( $data );
		
		$this->success ();
	}
	function youhui_add() {
		$username = $_POST ['username'];
		$start_time = strtotime ( $_POST ['start_time'] );
		$expiration_time = strtotime ( $_POST ['expiration_time'] );
		$condition = $_POST ['condition'];
		$money = $_POST ['money'];
		$user = $_POST ['user_id'];
		$state = $_POST ['state'];
		$time = time ();
		$date = array (
				'name' => $username,
				'condition' => $condition,
				'money' => $money,
				'start_time' => $start_time,
				'expiration_time' => $expiration_time,
				'create_time' => $time,
				'state' => $state 
		);
		$model = M ( "coupon" );
		$model->add ( $date );
		$lastid = $model->getLastInsID ();
		if (! empty ( $money )) {
			$user_id = explode ( ',', $user );
			foreach ( $user_id as $k => $v ) {
				$model = M ( "user_coupon" );
				$date = array (
						'user_id' => $v,
						'coupon_id' => $lastid,
						'create_time' => $time,
						"card" => $v . time (),
						"is_used" => 0 
				);
				$roles = $model->add ( $date );
			}
			if ($roles > 0) {
				$this->success ( "添加成功" );
			}
		}
	}
	function zhuandan() {
		$_order = $_REQUEST ['order'];
		$td_number = $_REQUEST ['td_number'];
		$fu_number = $_REQUEST ['fu_number'];
		$order_sn = $_REQUEST ['order_sn'];
		if (IS_POST) {
			$_SESSION ['jiao'] = '';
			$_SESSION ['jiao'] ['td_number6'] = $td_number;
			$_SESSION ['jiao'] ['fu_number6'] = $fu_number;
			$_SESSION ['jiao'] ['order_sn6'] = $order_sn;
		} else {
			$td_number = $_SESSION ['jiao'] ['td_number6'];
			$fu_number = $_SESSION ['jiao'] ['fu_number6'];
			$order_sn = $_SESSION ['jiao'] ['order_sn6'];
		}
		$this->assign ( "td_number", $td_number );
		$this->assign ( "fu_number", $fu_number );
		$this->assign ( "order_sn", $order_sn );
		$where = "(tod.state = 0 or tod.state = 3 or tod.state = 4 or tod.state = 6) and o.order_status != 7";
		if (! empty ( $td_number )) {
			$where .= " and concat(o.order_sn,right(convert(tod.c_time,char(11)),2),o.services_id) like '%$td_number%'";
		}
		if (! empty ( $fu_number )) {
			$where .= " and s.services_sn like '%$fu_number%'";
		}
		if (! empty ( $order_sn )) {
			$where .= " and o.order_sn like '%$order_sn%'";
		}
		if (empty ( $_order )) {
			$_order = 'asc';
		}
		$order = "tod.c_time $_order";
		$this->assign ( 'order', $_order );
		$count = $this->jiaoyi_model->table ( "cw_order as o" )->join ( "cw_turn_order as tod on o.id = tod.order_id" )->join ( "cw_services as s on s.id = o.services_id" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $this->jiaoyi_model->field ( "o.order_sn,o.services_id,s.phone,tod.c_time,tod.order_id,tod.id,s.services_sn,tod.sod_id,o.order_status,e.license_number,e.time as e_time,e.area as e_area,e.code as e_code,e.money as e_money,e.points as e_points,o.pay_money,tod.l_time,tod.state" )->table ( "cw_order as o" )->join ( "cw_endorsement as e on e.id = o.endorsement_id" )->join ( "cw_turn_order as tod on o.id = tod.order_id" )->join ( "cw_services as s on s.id = o.services_id" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		foreach ( $roles as $k => $v ) {
			$roles [$k] ['sod_id'] = $v ['order_sn'] . substr ( $v ['c_time'], - 2 ) . $v ['services_id'];
			$tod_model = M ( "turn_order" );
			$roles [$k] ['tod_count'] = $tod_model->where ( "order_id = '{$v['order_id']}' and sod_id <> '{$v['sod_id']}'" )->count ();
			$older_tod = $this->jiaoyi_model->field ( "o.order_sn,tod.c_time,tod.order_id,tod.state,tod.services_id,s.phone,s.services_sn" )->table ( "cw_order as o" )->join ( "cw_turn_order as tod on o.id = tod.order_id" )->join ( "cw_services as s on s.id = tod.services_id" )->where ( "tod.order_id = '{$v['order_id']}' and tod.sod_id <> '{$v['sod_id']}'" )->order ( "tod.id desc" )->find ();
			if (! empty ( $older_tod )) {
				$roles [$k] ['older_sod_id'] = $v ['order_sn'] . substr ( $older_tod ['c_time'], - 2 ) . $older_tod ['services_id'];
				$roles [$k] ['older_s_id'] = $older_tod ['services_id'];
				$roles [$k] ['older_phone'] = $older_tod ['phone'];
				$roles [$k] ['older_s_sn'] = $older_tod ['services_sn'];
				$array = array (
						1 => '手动转单',
						2 => '超时转单' 
				);
				$roles [$k] ['older_state'] = $array [$older_tod ['state']];
			}
			$time = '--';
			if ($v ['state'] == 0) {
				$time = jishi1 + $v ['l_time'];
			} else if ($v ['state'] == 3) {
				$time = jishi2 + $v ['l_time'];
			}
			$roles [$k] ['tmr'] = $time;
		}
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$this->display ();
	}
	function refund() {
		$order_sn = $_REQUEST ['order_sn'];
		$model = M ( "order" );
		$info = $model->where ( "order_sn = '$order_sn'" )->find ();
		$data = array (
				"out_trade_no" => $order_sn,
				"total_fee" => intval($info["pay_money"]*100),
				"refund_fee" => intval($info["pay_money"]*100) 
		);
		$url = "http://" . $_SERVER ['SERVER_NAME'] . "/Wxpay/example/refund.php";
		$post_params = $data;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_params );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		$data = array (
				"order_status" => 7 
		);
		$model->where ( "order_sn = '$order_sn'" )->save ( $data );
		
		// 服务商相关操作
		$turn_order_model = M ( "turn_order" );
		$turn_order_info = $turn_order_model->where ( "sod_id='{$info['so_id']}' and order_id = '{$info['id']}'" )->find();
		$data = array (
				"state" => 1 
		);
		$turn_order_model->where ( "sod_id='{$info['so_id']}' and order_id = '{$info['id']}'" )->save ( $data );
		$bank_model = M ( "bank" );
		$bank_info_older = $bank_model->where ( "bank_id='{$info['services_id']}'" )->find ();
		if (! empty ( $bank_info_older )) {
			$data = array (
					"money" => ($bank_info_older ['money'] - $turn_order_info ['money']) > 0 ? ($bank_info_older ['money'] - $turn_order_info ['money']) : 0,
					"end_money" => ($bank_info_older ['end_money'] - $turn_order_info ['money']) > 0 ? ($bank_info_older ['end_money'] - $turn_order_info ['money']) : 0,
			);
			$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
			// 记录
			$bank_info_older = $bank_model->where ( "bank_id='{$info['services_id']}'" )->find ();
			$data = array (
					"services_id" => $bank_info_older ['bank_id'],
					"income_money" => 0,
					"pay_money" => $turn_order_info ['money'],
					"end_money" => $bank_info_older ['end_money'],
					"user_money" => $bank_info_older ['user_money'],
					"money" => $bank_info_older ['money'],
					"order_id" => $info ['id'],
					"c_time" => time ()
			);
			$jl_model = M ( "services_jilu" );
			$jl_model->add ( $data );
		}
		
		// 修改违章状态
		$data = array (
				"is_manage" => 0,
				"manage_time" => time () 
		);
		$endorsement_model = M ( "Endorsement" );
		$endorsement_model->where ( "id={$info['endorsement_id']}" )->save ( $data );
		// 推送消息
		$model = M ();
		$user = $model->table ( "cw_order as o" )->join ( "cw_user as u on u.id=o.user_id" )->join ( "cw_car as c on c.id=o.car_id" )->field ( "u.openid, o.order_sn, c.license_number" )->where ( "o.id = '{$info['id']}'" )->find ();
		if (! empty ( $user )) {
			$model = new IndexController ();
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
							'value' => urlencode ( status3 ),
							'color' => '#000000' 
					),
					'remark' => array (
							'value' => urlencode ( last_key ),
							'color' => '#000000' 
					) 
			);
			include_once 'application/Weixin/Conf/config.php';
			$model->doSend ( 0, '', $user ['openid'], MUBAN3, URL2, $data );
		}
		$this->ajaxReturn ( 1 );
		return true;
	}
}




