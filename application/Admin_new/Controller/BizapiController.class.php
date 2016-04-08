<?php

namespace Admin_new\Controller;

use Common\Controller\AdminbaseController;

class BizapiController extends AdminbaseController {
	
	public function _initialize() {
		parent::_initialize ();
		$_SESSION ['dingyue'] = '';
		$_SESSION ['jiao'] = '';
		$_SESSION ['xitong'] = '';
	}
	
	function bizapi_list() {
		$query_appid = $_POST ['query_appid'];
		$_order = $_REQUEST ['order'];
		if (IS_POST) {
			$_SESSION ['bizapi'] = '';
			$_SESSION ['bizapi'] ['query_appid'] = $query_appid;
		} else {
			$query_appid = $_SESSION ['bizapi'] ['query_appid'];
		}
		$this->assign ( "query_appid", $query_appid );
		if (empty ( $query_appid )) {
			$where = '';
		} else {
			$where = " app_id like '%$query_appid%'";
		}
		if (empty ( $_order )) {
			$_order = 'desc';
		}
		$order = "a.app_domain $_order";
		$this->assign ( 'order', $_order );
		
		$model = M();
		$count = $model->table ( "cw_bizapi as a" )->where ( $where )->count ();
		$page = $this->page ( $count, 50 );
		$roles = $model->field ( "@rownum:=@rownum+1 AS iid,a.*" )->table ( "(SELECT @rownum:=0) r,cw_bizapi as a" )->where ( $where )->order ( $order )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( "str", $roles );
		$this->assign ( "Page", $page->show ( 'Admin' ) );
		$this->assign ( "pageIndex", $page->firstRow );
		$this->display ();
	}
	
	function bizapi_add_check() {
		$name = $_REQUEST ['bizapi_name'];
		$app_domain = $_REQUEST ['bizapi_app_domain'];
		$app_id = $_REQUEST ['bizapi_app_id'];
		$model = M ( "bizapi" );
		$result = $model->where("name = '$name'")->count();
		if($result > 0){
			$this->ajaxReturn ( array("info"=>"名称重复","status"=>1));
		}
		$result = $model->where("app_domain = '$app_domain'")->count();
		if($result > 0){
			$this->ajaxReturn ( array("info"=>"APP DOMAIN重复","status"=>2));
		}
		$result = $model->where("app_id = '$app_id'")->count();
		if($result > 0){
			$this->ajaxReturn ( array("info"=>"APP ID重复","status"=>3));
		}
		$this->ajaxReturn ( 0 );
	}
	
	function bizapi_add() {
		$name = $_POST ['bizapi_name'];
		$app_domain = $_POST ['bizapi_app_domain'];
		$app_id = $_POST ['bizapi_app_id'];
		$app_key = md5 ( $app_id . $app_domain );
		$creat_time = time();
		$expiration_time = strtotime ( $_POST ['expiration_time'] );
		$data = array (
				'name' => $name,
				'app_domain' => $app_domain,
				'app_id' => $app_id,
				'app_key' => $app_key,
				'create_time' => $creat_time,
				'expiration_time' => $expiration_time,
				'state' => 1 
		);
		$model = M ( "bizapi" );
		$model->add ( $data );
		$this->success ( "操作成功" );
	}
	
	function bizapi_update_check() {
		$id = $_REQUEST ['bizapi_id'];
		$name = $_REQUEST ['bizapi_name'];
		$app_domain = $_REQUEST ['bizapi_app_domain'];
		$app_id = $_REQUEST ['bizapi_app_id'];
		$model = M ( "bizapi" );
		$result = $model->where("id <> $id and name = '$name'")->count();
		if($result > 0){
			$this->ajaxReturn ( array("info"=>"名称重复","status"=>1));
		}
		$result = $model->where("id <> $id and app_domain = '$app_domain'")->count();
		if($result > 0){
			$this->ajaxReturn ( array("info"=>"APP DOMAIN重复","status"=>2));
		}
		$result = $model->where("id <> $id and app_id = '$app_id'")->count();
		if($result > 0){
			$this->ajaxReturn ( array("info"=>"APP ID重复","status"=>3));
		}
		$this->ajaxReturn ( 0 );
	}
	
	function bizapi_edit() {
		$id = $_REQUEST ['id'];
		$model = M ( "bizapi" );
		$result = $model->where("id = '$id'")->find();
		if(!empty($result)){
			$this->assign("api", $result);
			$this->display();
		}
		else{
			$this->error ( "数据不存在" );
		}
	}
	
	function bizapi_update() {
		$id = $_POST ['bizapi_id'];
		$name = $_POST ['bizapi_name'];
		$app_domain = $_POST ['bizapi_app_domain'];
		$app_id = $_POST ['bizapi_app_id'];
		$app_key = md5 ( $app_id . $app_domain );
		$expiration_time = strtotime ( $_POST ['expiration_time'] );
		$state = $_POST ['state'];
		$data = array (
				'name' => $name,
				'app_domain' => $app_domain,
				'app_id' => $app_id,
				'app_key' => $app_key,
				'expiration_time' => $expiration_time,
				'state' => $state 
		);
		$model = M ( "bizapi" );
		$model->where("id = '$id'")->save ( $data );
		$this->success ( "操作成功" );
	}

}