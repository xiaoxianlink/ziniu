<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/statics/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/statics/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/statics/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/statics/simpleboot/font-awesome/4.2.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		.length_3{width: 180px;}
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
		.tr_hover {background: #c0e5ff !important;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/statics/simpleboot/font-awesome/4.2.0/css/font-awesome-ie7.min.css">
	<![endif]-->
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "/",
    JS_ROOT: "statics/js/",
    TOKEN: ""
};
</script>
<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/statics/js/jquery.js"></script>
    <script src="/statics/js/wind.js"></script>
    <script src="/statics/simpleboot/bootstrap/js/bootstrap.min.js"></script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>

<meta charset="UTF-8">
<style type="text/css">
#mask {
	position: fixed;
}

.mask {
	left: 0;
	top:;
	right: 0;
	bottom: 0;
	width: 100%;
	height: 100%;
	background: #000;
	filter: alpha(opacity = 30);
	opacity: .3;
	z-index: 1;
}

#city_add {
	position: absolute;
	width: 750px;;
	height: 400px;
	margin-top: 110px;
	background: #EDF2F4;
	border-radius: 5px;
	z-index: 2;
}
</style>
<script type="text/javascript">
	$(function() {
		$('#city_add').hide();
		$('#city_link').click(
				function() {
					$('body').append('<div class="mask" id="mask"></div>');
					$('#city_add').css('left',
							Math.ceil(($('body').width()) / 5) + 'px');
					$('#city_add').css('top',
							Math.round($(this).position().top) + 'px');
					$('#city_add').show();
				})
		$('.closebtn').click(function() {
			$('#city_add').hide();
			$('#mask').remove();
		})
	})
</script>
<script type="text/javascript">
$(document).ready(function(){
	$('tr').click(function(){
		$('tr').siblings().removeClass("tr_hover");
		$(this).addClass("tr_hover");
	});
});
</script>
<body class="J_scroll_fixed">
	<div class="wrap J_check_wrap">
		<div id="cheliang_submit" class="top_div">
			<form action="<?php echo U('Jiaoyi/youhui');?> " method="post">
				<div class="query_div">
					<input type="button" id="city_link" class="query_btn" value="添加优惠券"
						style="width: 100px;" />
				</div>
				<input type="submit" id="cheliang_submit_input" class="query_btn"
					value="查询" style="margin-right: 20px;" />
				<div class="query_div">
					发放渠道 <select name="you_state" class="query_txt">
						<option value=""></option>
						<option value="1"<?php if($state==1){echo "selected='selected'";}?>>微信</option>
						<option value="2"<?php if($state==2){echo "selected='selected'";}?>>支付宝</option>
					</select>
				</div>
				<div class="query_div">
					是否使用 <select name="you_use" class="query_txt">
						<option value=""></option>
						<option value="2" <?php if($use==2){echo "selected='selected'";}?>>是</option>
						<option value="1" <?php if($use==1){echo "selected='selected'";}?>>否</option>
				  	</select>
	</div>
	<div  class="query_div" > 
		优惠券名称 <input type="text"  class="query_txt"  name="you_name" value="<?php echo ($name); ?>"/> 
	</div>
	</form>
</div>
<table class="table table-hover table-bordered table-list" id="menus-table">
	<tr>
		<th>#</th>
		<th>优惠券编码</th>
		<th>优惠券名称</th>
		<th>优惠券类型</th>
		<th>优惠券金额</th>
		<th>优惠券有效开始时间</th>
		<th>优惠券有效结束时间</th>
		<th>是否有效</th>
		<th>发放渠道</th>
		<th>发放用户</th>
		<th>是否使用</th>
		<th>使用时间</th>
	</tr>
	<?php $i = 1;?>
	<?php $i = $pageIndex + $i;?>
	<?php if(is_array($str)): foreach($str as $key=>$vo): ?><tr>
			<td><?php echo ($i++); ?></td>
			<td><?php echo ($vo["card"]); ?></td>
			<td><?php echo ($vo["name"]); ?></td>
			<td><?php echo ($vo["condition"]); ?></td>
			<td><?php echo ($vo["money"]); ?></td>
			<td><?php if($vo['start_time'] != ''): echo (date('Y-m-d H:i:s',$vo["start_time"])); endif; ?></td>
			<td><?php if($vo['expiration_time'] != ''): echo (date('Y-m-d H:i:s',$vo["expiration_time"])); endif; ?></td>
			<td><?php if($vo['expiration_time']-$vo['start_time'] > 0): ?>是<?php else: ?>否<?php endif; ?></td>
			<td><?php if($vo['state'] == 2): ?>支付宝<?php else: ?>微信<?php endif; ?></td>
			<td><?php echo ($vo["username"]); ?></td>
			<td><?php if($vo['is_used'] == '0'): ?>否<?php else: ?>是<?php endif; ?></td>
			<td><?php if($vo['use_time'] != ''): echo (date('Y-m-d H:i:s',$vo["use_time"])); endif; ?></td>
		</tr><?php endforeach; endif; ?>
</table>
<div id="city_add">
	<div class="city_top">
				<span class="city_span">添加优惠券</span><a href="#"
					class="city_close closebtn">X</a>
			</div>
			<div class="common-form">
				<form method="post" class="form-horizontal J_ajaxForm"
					action="<?php echo U('Jiaoyi/youhui_add');?>">
					<div class="window_div">
						<span class="window_span">优惠券名称:</span> <span class="window_span"><input type="radio" class="input"
							name="username" value="新用户优惠券" checked="checked">新用户优惠券</span> <span class="window_span"><input
							type="radio" class="input" name="username" value="积分优惠券">积分优惠券</span>
					</div>
					<div class="window_div">
						<span class="window_span">优惠券类型:</span><span class="window_span"><select><option value="">满减</option></select></span>
					</div>
					<div class="window_div">
						<span class="window_span">满减金额:</span><span class="window_span"><input type="text" class="input"
							name="condition" value=""></span> <span class="window_span">减去金额:</span><span class="window_span"><input
							type="text" class="input" name="money" value=""></span>
					</div>
					<div class="window_div">
						<span class="window_span">发放渠道:</span><span class="window_span" style="width: 145px;"><input type="radio" class="input"
							name="state" value="1" checked="checked"> 微信 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
							type="radio" class="input" name="state" value="2"
							checked="checked"> 支付宝</span> <span class="window_span">发放用户:</span><span class="window_span"><input
							type="text" class="input" name="user_id" value="">（请输入用户id，以,分隔）</span>
					</div>
					<div class="window_div">
						<span class="window_span">有效开始时间:</span><span class="window_span"><input type="text"
							class="input length_3 J_datetime" name="start_time" value=""></span>
						<span class="window_span">有效结束时间:</span><span class="window_span"><input type="text"
							class="input length_3 J_datetime" name="expiration_time" value=""></span>
					</div>
					<div class="window_div">
						<input type="submit" style="background: #66d99f;"
							class="query_btn" value="确定" /> <input type="button"
							style="background: #ffa600;" class="query_btn closebtn"
							value="取消" />
					</div>
				</form>
			</div>
		</div>
		<div class="pagination"><?php echo ($Page); ?></div>
	</div>
	<script type="text/javascript" src="/statics/js/common.js"></script>
	<script type="text/javascript"
		src="/statics/js/content_addtop.js"></script>
</body>
</html>