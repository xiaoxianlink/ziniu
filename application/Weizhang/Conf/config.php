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
define ( "app_id_chexingyi", "xiaoxianwangluo2016" ); // ����ҳappid
define ( "app_key_chexingyi", "PtR6oMc3ivxtrCl5s+rbYwYIcJZjm/oPmuVPoUBy/MU=" ); // ����ҳappkey

define ( 'api_cheshouye', "cheshouye.com" ); // ����ҳ�˿�
define ( "app_id_cheshouye", "928" ); // ����ҳappid
define ( "app_key_cheshouye", "edd9312406d6ed867262f0d50a49029c" ); // ����ҳappkey

define ( "api_icar", "http://120.26.57.239/api/" ); // �������˿�
define ( "app_id_icar", "2500000002" ); // ������merCode
define ( "app_key_icar", "9a5eae4723e87befc85459d5b0c585dc" ); // ������merKey

define ( "timing_count", "20" ); // ÿ�ܶ�ʱ��ѯ����



//���Ų���
define ('client_id', 'YXA64C51wKM6EeWpEYsWb4PIOA');
define ('client_secret', 'YXA6_1pOWamYMMEfwKsm5TCdHnQiw-w');
define ('url', 'https://a1.easemob.com/xiaoxianlink/xiaoxianchewu/');

//������֤�����ʱ��
define ( "DUANXIN_TIME", 30 * 60 );
//������ͨ�Ų���
define('AccountSid', 'aaf98f894b353559014b38f04fd601f1');
define('AccountToken', 'c9aa7a85305a40dea2d0f8c835b7a489');
define('AppId', '8a48b55151a4acb50151a61212cd054b');
//ɳ�л���������Ӧ�ÿ������ԣ���sandboxapp.cloopen.com
//�����������û�Ӧ������ʹ�ã���app.cloopen.com
define('ServerIP', 'sandboxapp.cloopen.com');
define('ServerPort', '8883');
define('SoftVersion', '2013-12-26');
//����ģ��id
define ('tempId', '1');
//secret ����ģʽ���أ�����ӿ�secret������ 'debug'
define('SECRET_DEBUG_ENABLE',true);
//secret ��Ч���� 15��
define('VALID_DAYS',30);
//secret �Ƿ�֧��һ���˻�ֻ�ܵ�½һ���豸 ture:֧��
define('ONE_DEVICE_ENABLE', true);
//secret ���ڹ���ʱ��֮ǰ��1��ʱ����
define('UPDATE_TIME',1*60*60*24);

//���Ʋ���
define('HOST','http://sdk.open.api.igexin.com/apiex.htm');

define('APPKEY','O0ESNemOMR6zVsVXQWKsK2');
define('appid','aeLGotClWN78uXFkcuCBx6');
define('MASTERSECRET','2Wrqc6W4MC8gT36efVY1B3');
//define('CID','');

//������Ϣ����
define(title1, '״̬���');
define(content1_1, '%s��ʼ�ӵ���ϵͳ���Զ����Ͷ������㡣');
define(content1_2, '%sֹͣ�ӵ���ϵͳ�����ٷ��Ͷ������㡣');
define(title2, '�ж���δ����');
define(content2, '�����µ�(%s)������2Сʱ���ڣ��뾡�촦�����ڽ���ȡ������ֱ��Ӱ��ӵ��ʡ�');
define(content2_2, '�����µ�(%s)������6Сʱ���ڣ��뾡�촦�����ڽ���ȡ������ֱ��Ӱ��ӵ��ʡ�');
define(title3, '�ж���δ����');
define(content3_1, '�������ڰ���Ķ���(%s)������24Сʱ���ڣ��뾡�촦�����ڽ���ȡ������ֱ��Ӱ��ӵ��ʡ�');
define(content3_2, '�������ڰ���Ķ���(%s)������6Сʱ���ڣ��뾡�촦�����ڽ���ȡ������ֱ��Ӱ��ӵ��ʡ�');
define(title4, '���¶���');
define(content4, '�����¶���(%s)������24Сʱ�ڴ������ڽ���ȡ������ֱ��Ӱ����Ľӵ��ʡ�');
define(title5, '����ȡ��');
define(content5, '����(%s)�򳬹�24Сʱδ�����Զ�ȡ����');
define(title6, '����ȡ��');
define(content6, '����(%s)�򳬹�72Сʱδ����ɹ����Զ�ȡ����');
define(title7, '��������');
define(content7, '����(%s)�ѽ���ɹ���');
define(title8, '�������');
define(content8, '�����������Ϣ�Ѹ��£��뼰ʱ�鿴�˽⡣');
define(title9, '�˻����䶯');
define(content9_1, '�˻������%s����%uԪ����ǰ���%uԪ��');
define(content9_2, '�˻������%s֧��%uԪ����ǰ���%uԪ��');
define(title10, '�˻����䶯');
define(content10_1, '�˻������ֽ����%s����%uԪ����ǰ�����ֽ��%uԪ��');
define(content10_2, '�˻������ֽ����%s֧��%uԪ����ǰ�����ֽ��%uԪ��');
define(title11, '�������п��䶯');
define(content11,'�˻���%s�ɹ��󶨣�β�ţ�%u��%s���п���');
define(title12, '�������п��䶯');
define(content12,'�˻���%s�ɹ����β�ţ�%u��%s���п���');
define(tixian_fee,'0.03'); //������ȡ�ķ��ʣ���ʼ��3%�� APPʹ��
define(req_time,'60'); //���ŷ�������ʱ�����ƣ�60����������ŷ��ͻ���ʾ�������Ƶ����APPʹ��