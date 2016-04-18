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
define ( "app_id_chexingyi", "anlian2016" ); // 车首页appid
define ( "app_key_chexingyi", "8lsb0WZMwJfULtEYzBrRWA==" ); // 车首页appkey

define ( 'api_cheshouye', "cheshouye.com" ); // 车首页端口
define ( "app_id_cheshouye", "928" ); // 车首页appid
define ( "app_key_cheshouye", "edd9312406d6ed867262f0d50a49029c" ); // 车首页appkey

define ( "api_icar", "http://120.26.57.239/api/" ); // 爱车坊端口
define ( "app_id_icar", "2500000002" ); // 爱车坊merCode
define ( "app_key_icar", "9a5eae4723e87befc85459d5b0c585dc" ); // 爱车坊merKey

define ( "timing_count", "20" ); // 每周定时查询数量

