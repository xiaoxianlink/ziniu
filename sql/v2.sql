
-- ----------------------------
-- dynamic pricing
-- ----------------------------
DROP TABLE IF EXISTS `cw_services_dyna`;
CREATE TABLE `cw_services_dyna` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `services_id` varchar(32) NOT NULL COMMENT '������id',
  `code` int(11) NOT NULL COMMENT '����code',
  `point_fee` int(11) NOT NULL COMMENT 'Υ�´���id',
  `fee` int(10) NOT NULL COMMENT '����',
  `create_time` int(11) DEFAULT NULL COMMENT '����ʱ��',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk ROW_FORMAT=DYNAMIC COMMENT='�����̶��۱�';

ALTER TABLE `cw_order`
ADD COLUMN `so_type`  int(2) NULL DEFAULT 1 AFTER `so_id`;

-- ----------------------------
-- mobile app
-- ----------------------------
ALTER TABLE `cw_services` ADD `nickname` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '�ǳ�' ;

ALTER TABLE `cw_message` CHANGE `msg_type` `msg_type` INT(11) NULL DEFAULT NULL COMMENT '��Ϣ���ͣ�1��Υ�����ѣ�2����Υ�����ѣ�3��С�����ѣ�4��С������';

ALTER TABLE `cw_message` ADD `content` VARCHAR(128) NULL COMMENT '��Ϣ����' ;

ALTER TABLE `cw_message` ADD `is_readed` INT(10) NULL COMMENT '��Ϣ״̬��0δ����1�Ѷ�' ;

ALTER TABLE `cw_message` CHANGE `msg_type` `msg_type` INT(11) NULL DEFAULT NULL COMMENT '��Ϣ���ͣ�1��Υ�����ѣ�2����Υ�����ѣ�3��С�����ѣ�4��С������5���ͷ�С��';

ALTER TABLE `cw_message` ADD `tixing_type` INT(10) NULL COMMENT 'С���������ͣ���msg_type=3ʱ��Ч' AFTER `msg_type`;

ALTER TABLE `cw_message` CHANGE `tixing_type` `tixing_type` INT(10) NULL COMMENT 'С���������ͣ���msg_type=3ʱ��Ч��1��ʼ�ӵ�״̬�������ѣ�2ϵͳ�������ѣ�3�µ��������ѣ�4�µ������������ѣ�5�ӵ��������������ѣ�6�ӵ��ɹ�����';

ALTER TABLE `cw_message` ADD `zhangwu_type` INT(10) NULL COMMENT 'С���������ͣ���msg_type=4ʱ��Ч' AFTER `tixing_type`;

ALTER TABLE `cw_message` CHANGE `zhangwu_type` `zhangwu_type` INT(10) NULL COMMENT 'С���������ͣ���type=4ʱ��Ч��1�����ֽ��䶯���ѣ�2�˻����䶯���ѣ�3�����������ѣ�4���ֵ�������';

ALTER TABLE `cw_message` ADD `icon` VARCHAR(32) NULL COMMENT 'ͼƬ' ;

ALTER TABLE `cw_message` CHANGE `tixing_type` `tixing_type` INT(10) NULL COMMENT 'С���������ͣ���msg_type=3ʱ��Ч��1��ʼ�ӵ�״̬�������ѣ�2�¶��������������ѣ�3�ӵ��������������ѣ�4�¶������ѣ�5�µ���ʱȡ�����ѣ�6�ӵ���ʱȡ�����ѣ�7�����������ѣ�8������������';

ALTER TABLE `cw_message` CHANGE `zhangwu_type` `zhangwu_type` INT(10) NULL COMMENT 'С���������ͣ���ty1�˻����䶯���ѣ�2�����ֽ��䶯���ѣ�3�����п�/������п���4���ֲ���';

CREATE TABLE `cw_yinhang` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '���id ����������' , `bank_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '��������' , `bank_img` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '����ͼ' , `state` INT(11) NOT NULL COMMENT '�Ƿ�ʹ�ã�0ʹ�ã�1��ʹ��' , `sort` INT(11) NOT NULL COMMENT '����' , PRIMARY KEY (`id`) );

CREATE TABLE `cw_duanxin` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '����������' , `phone` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '�ֻ�����' , `code` VARCHAR(32) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL COMMENT '������֤��' , `expires_time` DATETIME NOT NULL COMMENT '���Ź���ʱ��' , `create_time` DATETIME NOT NULL COMMENT '����ʱ��' , PRIMARY KEY (`id`) );

CREATE TABLE `cw_secret` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '����������' , `username` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '��¼�˻�' , `secret` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '��Կ' , `clientUUID` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '�豸id ��������ÿ̨�豸' , `user_agent` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User-Agent' , `expires_time` DATETIME NOT NULL COMMENT '��Կ����ʱ��' , `update_time` DATETIME NOT NULL COMMENT '��Կ����ʱ��' , PRIMARY KEY (`id`) ) COMMENT = '��Կ��¼';

ALTER TABLE `cw_bank` ADD `yh_id` INT(11) NULL COMMENT '����id' ;

-- ----------------------------
-- scan by state of car
-- ----------------------------
ALTER TABLE `cw_car`
ADD COLUMN `scan_state`  int(2) NULL DEFAULT 1 AFTER `unsubscribe_time`,
ADD COLUMN `scan_state_desc`  varchar(256) NULL AFTER `scan_state`,
ADD COLUMN `scan_state_time`  int(11) NULL AFTER `scan_state_desc`,
ADD COLUMN `scan_stop_query`  int(11) NULL AFTER `scan_state_time`;

-- ----------------------------
-- bizapi
-- ----------------------------
CREATE TABLE `cw_bizapi` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`app_domain`  varchar(256) NOT NULL ,
`app_id`  varchar(256) NOT NULL ,
`app_key`  varchar(256) NOT NULL ,
`create_time`  int(11) NOT NULL ,
`expiration_time`  int(11) NOT NULL ,
`state`  int(2) NOT NULL DEFAULT 1 ,
`last_time`  int(11) NULL ,
PRIMARY KEY (`id`)
)
;
ALTER TABLE `cw_car`
ADD COLUMN `channel_key`  varchar(128) NULL AFTER `channel`;

ALTER TABLE `cw_user`
ADD COLUMN `channel_key`  varchar(128) NULL AFTER `channel`;

insert into cw_menu (parentid, app, model, action, type, status, name, listorder) values (165, 'Admin_new', 'Bizapi',	'bizapi_list', 1, 1, '���ýӿڹ���', 5);

insert into cw_auth_rule (module, type, name, title, status) values ('Admin', 'admin_url', 'admin_new/bizapi/bizapi_list', '���ýӿڹ���', 1);	


