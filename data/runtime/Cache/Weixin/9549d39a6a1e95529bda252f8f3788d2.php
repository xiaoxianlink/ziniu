<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo ($license_number); ?></title>
<link href="../Public/css/weixin.css" rel="stylesheet" type="text/css">
<link href="/tpl/simplebootx/Public/css/weixin.css" rel="stylesheet">
<script src="/statics/js/jquery.js"></script>
</head>
<body>
<br/>
<div align="center" style="font-size: 30px;">支付成功</div><br/>
<div><a href="<?php echo U('order/index');?>" class="addcar_btn" style="border:#66d99f 2px solid;">进入订单中心</a></div><br/>
<div><a href="<?php echo U('scan/index',array('openid'=>$order['openid'],'carid'=>$order['car_id']));?>" class="addcar_btn" style="border:#6dc4dc 2px solid; background-color: #6dc4dc;">处理其他违章</a></div><br/>
<div><a href="#" onclick="WeixinJSBridge.call('closeWindow');" class="addcar_btn" style="border:#f77462 2px solid; background-color: #f77462;">关闭</a></div>
<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1256820389).'" width="0" height="0"/>';?>
</body>
</html>