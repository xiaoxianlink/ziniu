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

define ( 'api_chexingyi', "cx580.com" ); // ����ҳ�˿�
define ( "app_id_chexingyi", "anlian2016" ); // ����ҳappid
define ( "app_key_chexingyi", "8lsb0WZMwJfULtEYzBrRWA==" ); // ����ҳappkey

define ( 'api_cheshouye', "cheshouye.com" ); // ����ҳ�˿�
define ( "app_id_cheshouye", "928" ); // ����ҳappid
define ( "app_key_cheshouye", "edd9312406d6ed867262f0d50a49029c" ); // ����ҳappkey

define ( "api_icar", "http://120.26.57.239/api/" ); // �������˿�
define ( "app_id_icar", "2500000002" ); // ������merCode
define ( "app_key_icar", "9a5eae4723e87befc85459d5b0c585dc" ); // ������merKey

define ( "timing_count", "20" ); // ÿ�ܶ�ʱ��ѯ����

