<?php
if($_SERVER ['SERVER_NAME'] == "ziniu.xiaoxianlink.com"){
	define ( 'runEnv', "production" );
}
elseif($_SERVER ['SERVER_NAME'] == "zndev.xiaoxianlink.com"){
	define ( 'runEnv', "test" );
}
else{
	define ( 'runEnv', "dev" );
}

define ( 'api_chexingyi', "cx580.com" ); // 车首页端口
define ( "app_id_chexingyi", "xiaoxianwangluo2016" ); // 车首页appid
define ( "app_key_chexingyi", "PtR6oMc3ivxtrCl5s+rbYwYIcJZjm/oPmuVPoUBy/MU=" ); // 车首页appkey

define ( 'api_cheshouye', "cheshouye.com" ); // 车首页端口
define ( "app_id_cheshouye", "928" ); // 车首页appid
define ( "app_key_cheshouye", "edd9312406d6ed867262f0d50a49029c" ); // 车首页appkey

define ( "api_icar", "http://120.26.57.239/api/" ); // 爱车坊端口
define ( "app_id_icar", "2500000002" ); // 爱车坊merCode
define ( "app_key_icar", "9a5eae4723e87befc85459d5b0c585dc" ); // 爱车坊merKey

define ( "timing_count", "20" ); // 每周定时查询数量



//环信参数
define ('client_id', 'YXA64C51wKM6EeWpEYsWb4PIOA');
define ('client_secret', 'YXA6_1pOWamYMMEfwKsm5TCdHnQiw-w');
define ('url', 'https://a1.easemob.com/xiaoxianlink/xiaoxianchewu/');

//短信验证码过期时间
define ( "DUANXIN_TIME", 30 * 60 );
//容联云通信参数
define('AccountSid', 'aaf98f894b353559014b38f04fd601f1');
define('AccountToken', 'c9aa7a85305a40dea2d0f8c835b7a489');
define('AppId', '8a48b55151a4acb50151a61212cd054b');
//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
//生产环境（用户应用上线使用）：app.cloopen.com
define('ServerIP', 'sandboxapp.cloopen.com');
define('ServerPort', '8883');
define('SoftVersion', '2013-12-26');
//短信模板id
define ('tempId', '1');
//secret 调试模式开关，允许接口secret参数传 'debug'
define('SECRET_DEBUG_ENABLE',true);
//secret 有效天数 15天
define('VALID_DAYS',30);
//secret 是否支持一个账户只能登陆一个设备 ture:支持
define('ONE_DEVICE_ENABLE', true);
//secret 将在过期时间之前的1天时更新
define('UPDATE_TIME',1*60*60*24);

//个推参数
define('HOST','http://sdk.open.api.igexin.com/apiex.htm');

define('APPKEY','O0ESNemOMR6zVsVXQWKsK2');
define('appid','aeLGotClWN78uXFkcuCBx6');
define('MASTERSECRET','2Wrqc6W4MC8gT36efVY1B3');
//define('CID','');

//推送消息配置
define(title1, '状态变更');
define(content1_1, '%s开始接单，系统将自动发送订单给你。');
define(content1_2, '%s停止接单，系统将不再发送订单给你。');
define(title2, '有订单未处理');
define(content2, '你有新单(%s)将会在2小时过期，请尽快处理，逾期将会取消，并直接影响接单率。');
define(content2_2, '你有新单(%s)将会在6小时过期，请尽快处理，逾期将会取消，并直接影响接单率。');
define(title3, '有订单未处理');
define(content3_1, '你有正在办理的订单(%s)将会在24小时过期，请尽快处理，逾期将会取消，并直接影响接单率。');
define(content3_2, '你有正在办理的订单(%s)将会在6小时过期，请尽快处理，逾期将会取消，并直接影响接单率。');
define(title4, '有新订单');
define(content4, '你有新订单(%s)，请在24小时内处理，逾期将会取消，并直接影响你的接单率。');
define(title5, '订单取消');
define(content5, '订单(%s)因超过24小时未处理，自动取消。');
define(title6, '订单取消');
define(content6, '订单(%s)因超过72小时未办理成功，自动取消。');
define(title7, '订单结算');
define(content7, '订单(%s)已结算成功。');
define(title8, '规则更新');
define(content8, '帮助与规则信息已更新，请及时查看了解。');
define(title9, '账户余额变动');
define(content9_1, '账户余额于%s存入%u元，当前余额%u元。');
define(content9_2, '账户余额于%s支出%u元，当前余额%u元。');
define(title10, '账户余额变动');
define(content10_1, '账户可提现金额于%s存入%u元，当前可提现金额%u元。');
define(content10_2, '账户可提现金额于%s支出%u元，当前可提现金额%u元。');
define(title11, '提现银行卡变动');
define(content11,'账户于%s成功绑定（尾号：%u）%s银行卡。');
define(title12, '提现银行卡变动');
define(content12,'账户于%s成功解绑（尾号：%u）%s银行卡。');
define(tixian_fee,'0.03'); //提现收取的费率，初始化3%。 APP使用
define(req_time,'60'); //短信发送请求时间限制，60秒内请求短信发送会提示请求过于频繁。APP使用