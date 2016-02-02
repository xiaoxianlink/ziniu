<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>我的订单</title>
<link href="../Public/css/weixin.css" rel="stylesheet" type="text/css">
<link href="/tpl/simplebootx/Public/css/weixin.css" rel="stylesheet" type="text/css">
<link href="/tpl/simplebootx/Public/css/scrollbar.css" rel="stylesheet" type="text/css">
<script src="/statics/js/jquery.js"></script>
<script src="/statics/js/iscroll.js"></script>
<script type="text/javascript">
var myScroll,
pullDownEl, pullDownOffset,
pullUpEl, pullUpOffset,
generatedCount = 0;

/**
* 下拉刷新 （自定义实现此方法）
* myScroll.refresh();		// 数据加载完成后，调用界面更新方法
*/
function pullDownAction () {
setTimeout(function () {	// <-- Simulate network congestion, remove setTimeout from production!
	var el, li, i;
	el = document.getElementById('thelist');

	for (i=0; i<3; i++) {
		li = document.createElement('li');
		li.innerHtml = 'Generated row ' + (++generatedCount);
		el.insertBefore(li, el.childNodes[0]);
	}
	
	myScroll.refresh();		//数据加载完成后，调用界面更新方法   Remember to refresh when contents are loaded (ie: on ajax completion)
}, 1000);	// <-- Simulate network congestion, remove setTimeout from production!
}

/**
* 滚动翻页 （自定义实现此方法）
* myScroll.refresh();		// 数据加载完成后，调用界面更新方法
*/
function pullUpAction () {
setTimeout(function () {	// <-- Simulate network congestion, remove setTimeout from production!
	var el, li, i;
	el = document.getElementById('thelist');

	var pageIndex = ++generatedCount;
	var user_id = $("#user_id").val();
	$.post('<?php echo U("weixin/order/get_order");?>', { pageIndex: pageIndex, user_id: user_id }, function (data) {
		var data = data[0];
		if (data.length < 5) {
			$("#pullUp").hide();
		}
		for (i=0; i<5; i++) {
			if (data[i] != null) {
				li = document.createElement('li');
				li.innerHTML = data[i];
				el.appendChild(li, el.childNodes[0]);
			}
		}
		myScroll.refresh();		// 数据加载完成后，调用界面更新方法 Remember to refresh when contents are loaded (ie: on ajax completion)
	});
	
	
}, 1000);	// <-- Simulate network congestion, remove setTimeout from production!
}

/**
* 初始化iScroll控件
*/
function loaded() {
pullDownEl = document.getElementById('pullDown');
pullDownOffset = pullDownEl.offsetHeight;
pullUpEl = document.getElementById('pullUp');	
pullUpOffset = pullUpEl.offsetHeight;

myScroll = new iScroll('wrapper', {
	scrollbarClass: 'myScrollbar', /* 重要样式 */
	useTransition: false, /* 此属性不知用意，本人从true改为false */
	topOffset: pullDownOffset,
	onRefresh: function () {
		if (pullDownEl.className.match('loading')) {
			pullDownEl.className = '';
			pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
		} else if (pullUpEl.className.match('loading')) {
			pullUpEl.className = '';
			pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
		}
	},
	onScrollMove: function () {
		if (this.y > 5 && !pullDownEl.className.match('flip')) {
			pullDownEl.className = 'flip';
			pullDownEl.querySelector('.pullDownLabel').innerHTML = '松手开始更新...';
			this.minScrollY = 0;
		} else if (this.y < 5 && pullDownEl.className.match('flip')) {
			pullDownEl.className = '';
			pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
			this.minScrollY = -pullDownOffset;
		} else if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
			pullUpEl.className = 'flip';
			pullUpEl.querySelector('.pullUpLabel').innerHTML = '松手开始更新...';
			this.maxScrollY = this.maxScrollY;
		} else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
			pullUpEl.className = '';
			pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
			this.maxScrollY = pullUpOffset;
		}
	},
	onScrollEnd: function () {
		if (pullDownEl.className.match('flip')) {
			pullDownEl.className = 'loading';
			pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载中...';				
			pullDownAction();	// Execute custom function (ajax call?)
		} else if (pullUpEl.className.match('flip')) {
			pullUpEl.className = 'loading';
			pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载中...';				
			pullUpAction();	// Execute custom function (ajax call?)
		}
	}
});

setTimeout(function () { document.getElementById('wrapper').style.left = '0'; }, 800);
}

//初始化绑定iScroll控件 
document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
document.addEventListener('DOMContentLoaded', loaded, false); 

function scan_info(id,license_number,so_id,user_id,order_id){
	window.location.href="<?php echo u('Scan/scan_info');?>"+"&id="+id+"&license_number="+license_number+"&so_id="+so_id+"&user_id="+user_id+"&state=1&order_id="+order_id;
}
</script>
<style type="text/css" media="all">
body,ul,li {
	padding:0;
	margin:0;
	border:0;
}

body {
	font-size:12px;
	-webkit-user-select:none;
    -webkit-text-size-adjust:none;
	font-family:helvetica;
}

#header {
	position:absolute;
	top:0; left:0;
	width:100%;
	height:45px;
	line-height:45px;
	background-image:-webkit-gradient(linear, 0 0, 0 100%, color-stop(0, #fe96c9), color-stop(0.05, #d51875), color-stop(1, #7b0a2e));
	background-image:-moz-linear-gradient(top, #fe96c9, #d51875 5%, #7b0a2e);
	background-image:-o-linear-gradient(top, #fe96c9, #d51875 5%, #7b0a2e);
	padding:0;
	color:#eee;
	font-size:20px;
	text-align:center;
}

#header a {
	color:#f3f3f3;
	text-decoration:none;
	font-weight:bold;
	text-shadow:0 -1px 0 rgba(0,0,0,0.5);
}

#footer {
	position:absolute;
	bottom:0; left:0;
	width:100%;
	height:48px;
	background-image:-webkit-gradient(linear, 0 0, 0 100%, color-stop(0, #999), color-stop(0.02, #666), color-stop(1, #222));
	background-image:-moz-linear-gradient(top, #999, #666 2%, #222);
	background-image:-o-linear-gradient(top, #999, #666 2%, #222);
	padding:0;
	border-top:1px solid #444;
}

#wrapper {
	position:absolute; z-index:1;
	top:0; bottom:48px; left:0;
	width:100%;
	overflow:auto;
}

#scroller {
}

#scroller ul {
}

#scroller li {
}

#scroller li > a {
	display:block;
}
	/**
 *
 * 下拉样式 Pull down styles
 *
 */
#pullDown, #pullUp {
	background:#ebebeb;
	height:40px;
	line-height:40px;
	padding:5px 10px;
	border-bottom:1px solid #ccc;
	font-weight:bold;
	font-size:14px;
	color:#888;
}
#pullDown .pullDownIcon, #pullUp .pullUpIcon  {
	display:block; float:left;
	width:40px; height:40px;
	background:url(pull-icon@2x.png) 0 0 no-repeat;
	-webkit-background-size:40px 80px; background-size:40px 80px;
	-webkit-transition-property:-webkit-transform;
	-webkit-transition-duration:250ms;	
}
#pullDown .pullDownIcon {
	-webkit-transform:rotate(0deg) translateZ(0);
}
#pullUp .pullUpIcon  {
	-webkit-transform:rotate(-180deg) translateZ(0);
}

#pullDown.flip .pullDownIcon {
	-webkit-transform:rotate(-180deg) translateZ(0);
}

#pullUp.flip .pullUpIcon {
	-webkit-transform:rotate(0deg) translateZ(0);
}

#pullDown.loading .pullDownIcon, #pullUp.loading .pullUpIcon {
	background-position:0 100%;
	-webkit-transform:rotate(0deg) translateZ(0);
	-webkit-transition-duration:0ms;

	-webkit-animation-name:loading;
	-webkit-animation-duration:2s;
	-webkit-animation-iteration-count:infinite;
	-webkit-animation-timing-function:linear;
}

@-webkit-keyframes loading {
	from { -webkit-transform:rotate(0deg) translateZ(0); }
	to { -webkit-transform:rotate(360deg) translateZ(0); }
}
	
</style>
</head>
 <?php if(count($orderlist) == 0): ?><body class="no_order"></body>
 <?php else: ?>
 <body>
 <input type="hidden" id="user_id" value="<?php echo ($user_id); ?>" >
 <div id="wrapper">
	<div id="scroller">
 	<div id="pullDown">
		<!-- <span class="pullDownIcon"></span><span class="pullDownLabel">下拉刷新...</span> -->
	</div>
	<ul id="thelist">
	<?php if(is_array($orderlist)): foreach($orderlist as $k=>$v): ?><li>
	<table border="0" cellpadding="0" cellspacing="0" class="pad_l">
    	<thead><th colspan="2" class="th td"><?php echo ($v["license_number"]); ?></th></thead>
		<tr>
			<td colspan="2" class="td"  style="padding-top:15px;">处理编号：<?php echo ($v["order_sn"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章地区：<?php echo ($v["area"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章代码：<?php echo ($v["code"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章时间：<?php echo date('Y-m-d H:i:s', $v['time']); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">违章地点：<?php echo ($v["address"]); ?></td
		></tr>
		<tr>
			<td colspan="2" class="td">违章内容：<?php echo ($v["content"]); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="td">罚&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;款：<?php echo ($v["money"]); ?></td>
		</tr>
		<tr>			
			<td colspan="2" class="td" style="padding-bottom:15px;">罚&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;分：<?php echo ($v["points"]); ?></td>
		</tr>
        <tr>
        	<td class="l_pay td" style="font-size:20px;padding-top:10px;"><?php echo ($v["o_money"]); ?>元</td>
            <?php if($v['order_status'] == 1){ ?><td rowspan="2" class="r_pay" onclick="scan_info('<?php echo ($v["endorsement_id"]); ?>', '<?php echo ($v["license_number"]); ?>', '<?php echo ($v["so_id"]); ?>', '<?php echo ($user_id); ?>','<?php echo ($v["id"]); ?>')"><?php }else{ ?><td rowspan="2" class="r_pay"><?php } echo $order_status[$v['order_status']]; ?></td>
        </tr>
        <tr>
        	<td class="l_pay td" style="font-size:12px;padding-bottom:10px;"><?php echo date('Y/m/d H:i', $v['last_time']); ?></td>
        </tr>
	</table>
	</li><br/><?php endforeach; endif; ?>
	</ul>
	<div id="pullUp">
		<span class="pullUpIcon"></span><span class="pullUpLabel">上拉加载更多...</span>
	</div>
</div>
</div>
<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1256820389).'" width="0" height="0"/>';?>
</body><?php endif; ?>
</html>