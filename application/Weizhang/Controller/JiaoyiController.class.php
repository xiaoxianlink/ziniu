<?php

namespace Weizhang\Controller;

use Common\Controller\AdminbaseController;
use Think\Model;
use Think\Log;

require_once "application/Common/getui/IGt.Push.php";

class JiaoyiController extends AdminbaseController {
	
	function close_endorsements(){
		$order_state = $_POST ['order_state'];
		$query_no = $_POST ['query_no'];
		$license_number = $_POST ['license_number'];
		if (IS_POST) {
			$_SESSION ['close_endorsements'] = '';
			$_SESSION ['close_endorsements'] ['order_state'] = $order_state;
			$_SESSION ['close_endorsements'] ['query_no'] = $query_no;
			$_SESSION ['close_endorsements'] ['license_number'] = $license_number;
		} else {
			$order_state = $_SESSION ['close_endorsements'] ['order_state'];
			$query_no = $_SESSION ['close_endorsements'] ['query_no'];
			$license_number = $_SESSION ['close_endorsements'] ['license_number'];
		}
		$this->assign ( "order_state", $order_state );
		$this->assign ( "query_no", $query_no );
		$this->assign ( "license_number", $license_number );
		$where = "";
		if(!empty($order_state)){
			if($order_state == 1){
				$where .= " and o.id is null ";
			}
			else{
				$where .= " and o.id is not null ";
			}
		}
		if(!empty($query_no)){
			$where .= " and end.close_query_no = $query_no ";
		}
		if(!empty($license_number)){
			$where .= " and car.license_number like '%$license_number%'";
		}
		$model = M ( "" );
		$now = time();
		$count = $model->table ( "cw_endorsement as end" )->join ( "cw_car as car on car.id = end.car_id")->join ( "cw_order as o on o.endorsement_id = end.id", "left" )->join ( "cw_services as srv on srv.id = o.services_id", "left" )->join ( "cw_turn_order as t on t.order_id = o.id and t.sod_id = o.so_id", "left" )->where ( "end.close_confirm = 1 and end.is_manage <> 2 and end.close_time <= $now"  . $where)->count("Distinct end.id");
		$orderSQL = "end.close_time desc";
		$page = $this->page ( $count, 50 );
		/*
		$roles = $model->field ( "@rownum:=@rownum+1 AS iid, car.license_number, end.*, o.order_sn, o.order_status, o.c_time as order_time, o.pay_money, o.pay_type, o.pay_sn, o.services_id, srv.services_sn, srv.phone, t.id as to_id, t.c_time as to_c_time, t.finish_time as to_finish_time, t.state as to_state" )->table ( "cw_endorsement as end" )->join ( "cw_car as car on car.id = end.car_id")->join ( "cw_order as o on o.endorsement_id = end.id", "left" )->join ( "cw_services as srv on srv.id = o.services_id", "left" )->join ( "cw_turn_order as t on t.order_id = o.id and t.sod_id = o.so_id", "left" )->where ( "end.close_confirm = 1 and end.is_manage <> 2 and end.close_time <= $now " . $where)->order("end.close_time")->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		*/
		$roles = $model->field ( "@rownum:=@rownum+1 AS iid, car.license_number, end.*" )->table ( "cw_endorsement as end" )->join ( "cw_car as car on car.id = end.car_id")->join ( "cw_order as o on o.endorsement_id = end.id", "left" )->join ( "cw_services as srv on srv.id = o.services_id", "left" )->join ( "cw_turn_order as t on t.order_id = o.id and t.sod_id = o.so_id", "left" )->where ( "end.close_confirm = 1 and end.is_manage <> 2 and end.close_time <= $now " . $where)->order("end.close_time")->group("end.id")->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$end_ids = array();
		foreach ( $roles as $k => $v ) {
			$end_ids[] = $v["id"];
		}
		if(!empty($end_ids)){
			$subWhere = " o.endorsement_id in (" . implode(",", $end_ids) . ")";
			$order_model = M ();
			$order_list = $order_model->field ( "o.order_sn, o.endorsement_id, o.order_status, o.c_time as order_time, o.pay_money, o.pay_type, o.pay_sn, o.services_id, srv.services_sn, srv.phone, t.id as to_id, t.c_time as to_c_time, t.finish_time as to_finish_time, t.state as to_state ")->table ( "cw_order as o")->join ( "cw_services as srv on srv.id = o.services_id", "left" )->join ( "cw_turn_order as t on t.order_id = o.id and t.sod_id = o.so_id", "left" )->where ($subWhere)->select ();
			foreach ( $roles as $k => $v ) {
				$o_list = array();
				foreach ( $order_list as $c => $p ) { 
					if($p['endorsement_id'] == $v["id"]){
						$o_list [] = $p;
					}
				}
				$roles [$k]['o_list'] = $o_list;
			}
		}
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( 'str', $roles );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ("close_endorsements");
	}
	
	function close_endorsement_cancel(){
		$end_id= $_REQUEST["end_id"];
		$model = M ( "endorsement" );
		$data = array(
				"close_confirm" => 0,
				"close_query_no" => null,
				"close_time" => null
			);
		$model->where("id=$end_id")->save($data);
		$this->redirect("Jiaoyi/close_endorsements");
	}
	
	function close_endorsement_confirm(){
		$end_id= $_REQUEST["end_id"];
		
		$end_model = M ( "endorsement" );
		$end = $end_model->where("id=$end_id")->find();
		if(!empty($end)){
			if($end['is_manage'] <> 2 && $end['close_confirm'] == 1){
				$this->close_endorsement($end_id);
				$this->finish_order($end_id);
				$this->push_confirm($end_id);
			}
		}
		
		$this->redirect("Jiaoyi/close_endorsements");
	}
	
	function close_endorsement($end_id){
		$now = time();
		$end_model = M ( "endorsement" );
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
	}
	
	function finish_order($end_id){
		$now = time();
		$order_model = M ("order");
		$where = "endorsement_id='$end_id' and order_status in (1,2,3)";
		$order_infos = $order_model->where ( $where )->select ();
		if (! empty ( $order_infos )) {
		    foreach ($order_infos as $order_info){
				$data = array (
					"last_time" => $now 
				);
				if($order_info['order_status'] == 1){
					$data['order_status'] = 8;
				}
				if($order_info['order_status'] == 2){
					$data['order_status'] = 8;
				}
				if($order_info['order_status'] == 3){
					$data['order_status'] = 5;
				}
				$order_model->where ( "id='{$order_info['id']}'" )->save ( $data );
				
				$t_order_model = M ( "turn_order" );
				$data = array (
							"state" => 6,
							"l_time" => $now 
						);
				$t_order_model->where ( "order_id = '{$order_info['id']}' and state in (1,2,3)" )->save ($data);
				$data = array (
							"state" => 5,
							"l_time" => $now
						);
				$t_order_model->where ( "order_id = '{$order_info['id']}' and state = 4 " )->save ($data);
				$t_order_info = $t_order_model->where ( "order_id = '{$order_info['id']}'" )->order("id desc")->find();
				
				if ($order_info['order_status'] == 2 || $order_info['order_status'] == 3) {
					$bank_model = M ( "bank" );
					$bank_info_older = $bank_model->where ( "bank_id='{$order_info['services_id']}'" )->find ();
					if (! empty ( $bank_info_older )) {
						$data = array (
								"end_money" => ($bank_info_older ['end_money'] - $t_order_info['money']) > 0 ? ($bank_info_older ['end_money'] - $t_order_info['money']) : 0
						);
						if($order_info['order_status'] == 3) {
							$data["user_money"] = $bank_info_older ['user_money'] + $t_order_info['money'];
						}
						$bank_model->where ( "id='{$bank_info_older['id']}'" )->save ( $data );
						
						/* start 增加可提现金额变动提醒推送*/
						if($order_info['order_status'] == 3){
							//可提现金额变动提醒
							$model_services = M ("services");
							$services = $model_services->where ("id = '{$order_info['services_id']}'")->find();
							$time = date("Y.m.d H:i:s");
							$money = $t_order_info ['money'];
							$user_money = $bank_info_older ['user_money'];
							$content = sprintf(content10_1, $time,$money,$user_money);
							$title = title10;
							$tz_content = sprintf(content10_1, $time,$money,$user_money);
							$this->pushMessageToSingle($content, $title,$tz_content,$services['phone']);
							$this->add_message($services['id'], 4, '', 2, $content);
						}
						/* end */
					
						$bank_info = $bank_model->where ( "bank_id='{$order_info['services_id']}'" )->find ();
						$data = array (
								"services_id" => $bank_info ['bank_id'],
								"income_money" => 0,
								"pay_money" => 0,
								"end_money" => $bank_info ['end_money'],
								"user_money" => $bank_info ['user_money'],
								"money" => $bank_info ['money'],
								"order_id" => $order['id'],
								"c_time" => time () 
						);
						if($order_info['order_status'] == 2){
							$data["pay_money"] = $t_order_info['money'];
						}
						
						$jl_model = M ( "services_jilu" );
						$jl_model->add ( $data );
						
					}
				}
				$this->push_order($order_info['id']);
			}
		}
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
	
	function push_order($end_id){
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
		$url = $weixin_url . "/index.php?g=weixin&m=api&a=push_order";
		$this->invokeHttpApi($url, $param, null, true);
		
		$bizapi_url = "";
		if(runEnv == 'production'){
			$bizapi_url = "http://bizapi.xiaoxianlink.com";
		}
		elseif(runEnv == 'test'){
			$bizapi_url = "http://testapi.xiaoxianlink.com";
		}
		else{
			$v = "http://ba.xiaoxian.com";
		}
		$url = $weixin_url . "/index.php?g=bizapi&m=api&a=push_order";
		$this->invokeHttpApi($url, $param, null, true);
		
	}
	
	/* start 增加推送方法*/
	
	//单推接口案例（个推）
	function pushMessageToSingle($content,$title,$tz_content,$alias){
	
	    $igt = new \IGeTui(NULL,APPKEY,MASTERSECRET,false);
	
	    $template = $this->IGtNotificationTemplateDemo($content,$title,$tz_content);
	
	    //个推信息体
	    $message = new \IGtSingleMessage();
	
	    $message->set_isOffline(true);//是否离线
	    $message->set_offlineExpireTime(3600*12*1000);//离线时间
	    $message->set_data($template);//设置推送消息类型
	    //接收方
	    $target = new \IGtTarget();
	    $target->set_appId(appid);
	    $target->set_alias($alias);
	
	    try {
	        $rep = $igt->pushMessageToSingle($message, $target);
	
	    }catch(\RequestException $e){
	        $requstId =e.getRequestId();
	        $rep = $igt->pushMessageToSingle($message, $target,$requstId);
	    }
	
	}
	//群推接口案例
	function pushMessageToApp($content, $title,$tz_content){
	    $igt = new \IGeTui(HOST,APPKEY,MASTERSECRET);
	    $template = $this->IGtNotificationTemplateDemo($content,$title,$tz_content);
	    //个推信息体
	    //基于应用消息体
	    $message = new \IGtAppMessage();
	    $message->set_isOffline(true);
	    $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
	    $message->set_data($template);
	
	    $appIdList=array(appid);
	    $phoneTypeList=array('ANDROID');
	
	    $message->set_appIdList($appIdList);
	
	    $igt->pushMessageToApp($message);
	
	}
	
	function IGtNotificationTemplateDemo($content,$title,$tz_content){
	    $template =  new \IGtNotificationTemplate();
	    $template->set_appId(appid);//应用appid
	    $template->set_appkey(APPKEY);//应用appkey
	    $template->set_transmissionType(1);//透传消息类型
	    $template->set_transmissionContent($content);//透传内容
	    $template->set_title($title);//通知栏标题
	    $template->set_text($tz_content);//通知栏内容
	    $template->set_logo("");//通知栏logo
	    $template->set_isRing(true);//是否响铃
	    $template->set_isVibrate(true);//是否震动
	    $template->set_isClearable(true);//通知栏是否可清除
	    return $template;
	}
	/**
	 * 插入消息表
	 */
	public function add_message($services_id, $msg_type, $tixing_type, $zhangwu_type, $content){
	    $model_msg = M ("message");
	    $data = array(
	        'from_userid'=>0,
	        'openid'=>$services_id,
	        'msg_type'=>$msg_type,
	        'tixing_type'=>$tixing_type,
	        'zhangwu_type'=>$zhangwu_type,
	        'create_time'=>time(),
	        'content'=>$content,
	        'is_readed'=>0
	    );
	    $model_msg->add($data);
	}
	/* end */

}