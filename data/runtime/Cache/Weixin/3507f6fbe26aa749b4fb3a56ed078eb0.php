<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>违章订阅</title>
<link href="../Public/css/weixin.css" rel="stylesheet" type="text/css">
<link href="/tpl/simplebootx/Public/css/weixin.css" rel="stylesheet">
<script src="/statics/js/jquery.js"></script>
<script type="text/javascript">
	function showunsubscribe($k) {
		if ($("#unsubscribe"+$k).is(":hidden")) {
			$("#unsubscribe"+$k).show();
		} else {
			$("#unsubscribe"+$k).hide();
		}
	}
	function cancel(id,uc_id) {
		$.post("<?php echo u('Sub/cancel_car');?>", { id: id, uc_id: uc_id }, function (data) {
			 $("#car"+data.id).hide();
			 $("#unsubscribe"+data.id).hide();
			 $("#car"+data.id).hide();
			 valid();
		});
	}
	function valid(){
		showbtn();
		return true;
	}
	function showbtn() {
        $("#bg").css({
            display: "block", height: $(document).height()
        });
        var $box = $('.box');
        $box.css({
            //设置弹出层距离左边的位置
            left: ($("body").width() - $box.width()) / 2 + "px",
            //设置弹出层距离上面的位置
            top: ($(window).height() - $box.height()) / 2 + $(window).scrollTop() + "px",
            display: "block"
        });
        setTimeout("close()",1200);
    }
	function close() {
        $("#bg,.box").css("display", "none");
    }
</script>
</head>
<body>
	<a class="addcar_btn" href="<?php echo U('sub/add_car',array('id'=>$user_id));?>">添加车辆</a>
	<br />
	<?php if(is_array($carlist)): foreach($carlist as $k=>$v): $unsubscribe = 'unsubscribe' . $v['id'];?>
	<div id="<?php echo 'car' . $v['id'];?>" class="f_div"
		style="margin-top: 10px;">
		<div class="s_div" onclick="showunsubscribe(<?php echo ($v["id"]); ?>)">
			<table border="0" cellpadding="0" cellspacing="0" class="pad_l">
				<tr>
					<td rowspan="3" class="td_ln"><?php echo ($v["license_number"]); ?></td>
					<td class="td dy_td" style="padding-top: 5px; font-size: 12px;">车架号：<?php echo ($v["frame_number"]); ?></td>
				</tr>
				<tr>
					<td class="td dy_td" style="font-size: 12px;">发动机号：<?php echo ($v["engine_number"]); ?></td>
				</tr>
				<tr>
					<td class="td dy_td" style="padding-bottom: 5px; font-size: 12px;">最新扫描：<?php if(!empty($v['last_time'])){echo date('Y.m.d', $v['last_time']);}else{echo date('Y.m.d', time());}?></td>
				</tr>
			</table>
		</div>
		<div class="t_div" id="<?php echo $unsubscribe;?>"
			onclick="cancel(<?php echo ($v["id"]); ?>,<?php echo ($v["uc_id"]); ?>)">
			<span class="car_span">退订</span>
		</div>
	</div><?php endforeach; endif; ?>
	
	<div style="position:fixed;bottom:0px;top:auto;"><?php echo ($versions); ?></div>
<div id="bg"></div>
<div class="box" style="display:none">
    <div class="list" id="list">
       取消订阅成功
    </div>
</div>
<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1256820389).'" width="0" height="0"/>';?>
</body>
</html>