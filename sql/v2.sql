
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

ALTER TABLE `cw_region`
ADD COLUMN `gb_code_p`  int(4) NULL AFTER `abbreviation`,
ADD COLUMN `gb_code_c`  int(8) NULL AFTER `gb_code_p`,
ADD COLUMN `cxy_engine_nums`  int(4) NULL AFTER `c_frame_nums`,
ADD COLUMN `cxy_frame_nums`  int(4) NULL AFTER `cxy_engine_nums`;

update `cw_region` set gb_code_p = 11, gb_code_c = 11  where city = '����';
update `cw_region` set gb_code_p = 12, gb_code_c = 12  where city = '���';
update `cw_region` set gb_code_p = 31, gb_code_c = 31  where city = '�Ϻ�';
update `cw_region` set gb_code_p = 50, gb_code_c = 50  where province = '����';
update cw_region set gb_code_p = 13, gb_code_c = 1301, nums = '��A' where city ='ʯ��ׯ';
update cw_region set gb_code_p = 13, gb_code_c = 1302, nums = '��B' where city ='��ɽ';
update cw_region set gb_code_p = 13, gb_code_c = 1303, nums = '��C' where city ='�ػʵ�';
update cw_region set gb_code_p = 13, gb_code_c = 1304, nums = '��D' where city ='����';
update cw_region set gb_code_p = 13, gb_code_c = 1305, nums = '��E' where city ='��̨';
update cw_region set gb_code_p = 13, gb_code_c = 1306, nums = '��F' where city ='����';
update cw_region set gb_code_p = 13, gb_code_c = 1307, nums = '��G' where city ='�żҿ�';
update cw_region set gb_code_p = 13, gb_code_c = 1308, nums = '��H' where city ='�е�';
update cw_region set gb_code_p = 13, gb_code_c = 1309, nums = '��J' where city ='����';
update cw_region set gb_code_p = 13, gb_code_c = 1310, nums = '��R' where city ='�ȷ�';
update cw_region set gb_code_p = 13, gb_code_c = 1311, nums = '��T' where city ='��ˮ';
update cw_region set gb_code_p = 13, gb_code_c = 1312, nums = '��S' where city ='��������';
update cw_region set gb_code_p = 14, gb_code_c = 1401, nums = '��A' where city ='̫ԭ';
update cw_region set gb_code_p = 14, gb_code_c = 1402, nums = '��B' where city ='��ͬ';
update cw_region set gb_code_p = 14, gb_code_c = 1403, nums = '��C' where city ='��Ȫ';
update cw_region set gb_code_p = 14, gb_code_c = 1404, nums = '��D' where city ='����';
update cw_region set gb_code_p = 14, gb_code_c = 1405, nums = '��E' where city ='����';
update cw_region set gb_code_p = 14, gb_code_c = 1406, nums = '��F' where city ='˷��';
update cw_region set gb_code_p = 14, gb_code_c = 1407, nums = '��K' where city ='����';
update cw_region set gb_code_p = 14, gb_code_c = 1408, nums = '��M' where city ='�˳�';
update cw_region set gb_code_p = 14, gb_code_c = 1409, nums = '��H' where city ='����';
update cw_region set gb_code_p = 14, gb_code_c = 1410, nums = '��L' where city ='�ٷ�';
update cw_region set gb_code_p = 14, gb_code_c = 1411, nums = '��J' where city ='����';
update cw_region set gb_code_p = 14, gb_code_c = 1423, nums = '��G' where city ='�㱱';
update cw_region set gb_code_p = 15, gb_code_c = 1501, nums = '��A' where city ='���ͺ���';
update cw_region set gb_code_p = 15, gb_code_c = 1502, nums = '��B' where city ='��ͷ';
update cw_region set gb_code_p = 15, gb_code_c = 1503, nums = '��C' where city ='�ں�';
update cw_region set gb_code_p = 15, gb_code_c = 1504, nums = '��D' where city ='���';
update cw_region set gb_code_p = 15, gb_code_c = 1505, nums = '��G' where city ='ͨ��';
update cw_region set gb_code_p = 15, gb_code_c = 1506, nums = '��K' where city ='������˹';
update cw_region set gb_code_p = 15, gb_code_c = 1507, nums = '��E' where city ='���ױ���';
update cw_region set gb_code_p = 15, gb_code_c = 1508, nums = '��L' where city ='�����׶�';
update cw_region set gb_code_p = 15, gb_code_c = 1509, nums = '��J' where city ='�����첼';
update cw_region set gb_code_p = 15, gb_code_c = 1522, nums = '��F' where city ='�˰�';
update cw_region set gb_code_p = 15, gb_code_c = 1525, nums = '��H' where city ='���ֹ���';
update cw_region set gb_code_p = 15, gb_code_c = 1529, nums = '��M' where city ='������';
update cw_region set gb_code_p = 21, gb_code_c = 2101, nums = '��A' where city ='����';
update cw_region set gb_code_p = 21, gb_code_c = 2102, nums = '��B' where city ='����';
update cw_region set gb_code_p = 21, gb_code_c = 2103, nums = '��C' where city ='��ɽ';
update cw_region set gb_code_p = 21, gb_code_c = 2104, nums = '��D' where city ='��˳';
update cw_region set gb_code_p = 21, gb_code_c = 2105, nums = '��E' where city ='��Ϫ';
update cw_region set gb_code_p = 21, gb_code_c = 2106, nums = '��F' where city ='����';
update cw_region set gb_code_p = 21, gb_code_c = 2107, nums = '��G' where city ='����';
update cw_region set gb_code_p = 21, gb_code_c = 2108, nums = '��H' where city ='Ӫ��';
update cw_region set gb_code_p = 21, gb_code_c = 2109, nums = '��J' where city ='����';
update cw_region set gb_code_p = 21, gb_code_c = 2110, nums = '��K' where city ='����';
update cw_region set gb_code_p = 21, gb_code_c = 2111, nums = '��L' where city ='�̽�';
update cw_region set gb_code_p = 21, gb_code_c = 2112, nums = '��M' where city ='����';
update cw_region set gb_code_p = 21, gb_code_c = 2113, nums = '��N' where city ='����';
update cw_region set gb_code_p = 21, gb_code_c = 2114, nums = '��P' where city ='��«��';
update cw_region set gb_code_p = 22, gb_code_c = 2201, nums = '��A' where city ='����';
update cw_region set gb_code_p = 22, gb_code_c = 2202, nums = '��B' where city ='����';
update cw_region set gb_code_p = 22, gb_code_c = 2203, nums = '��C' where city ='��ƽ';
update cw_region set gb_code_p = 22, gb_code_c = 2204, nums = '��D' where city ='��Դ';
update cw_region set gb_code_p = 22, gb_code_c = 2205, nums = '��E' where city ='ͨ��';
update cw_region set gb_code_p = 22, gb_code_c = 2206, nums = '��F' where city ='��ɽ';
update cw_region set gb_code_p = 22, gb_code_c = 2207, nums = '��J' where city ='��ԭ';
update cw_region set gb_code_p = 22, gb_code_c = 2208, nums = '��G' where city ='�׳�';
update cw_region set gb_code_p = 22, gb_code_c = 2224, nums = '��H' where city ='�ӱ�';
update cw_region set gb_code_p = 23, gb_code_c = 2301, nums = '��A' where city ='������';
update cw_region set gb_code_p = 23, gb_code_c = 2302, nums = '��B' where city ='�������';
update cw_region set gb_code_p = 23, gb_code_c = 2303, nums = '��G' where city ='����';
update cw_region set gb_code_p = 23, gb_code_c = 2304, nums = '��H' where city ='�׸�';
update cw_region set gb_code_p = 23, gb_code_c = 2305, nums = '��J' where city ='˫Ѽɽ';
update cw_region set gb_code_p = 23, gb_code_c = 2306, nums = '��E' where city ='����';
update cw_region set gb_code_p = 23, gb_code_c = 2307, nums = '��F' where city ='����';
update cw_region set gb_code_p = 23, gb_code_c = 2308, nums = '��D' where city ='��ľ˹';
update cw_region set gb_code_p = 23, gb_code_c = 2309, nums = '��K', city ='��̨��' where id = 4373;
update cw_region set gb_code_p = 23, gb_code_c = 2310, nums = '��C' where city ='ĵ����';
update cw_region set gb_code_p = 23, gb_code_c = 2311, nums = '��N' where city ='�ں�';
update cw_region set gb_code_p = 23, gb_code_c = 2312, nums = '��M' where city ='�绯';
update cw_region set gb_code_p = 23, gb_code_c = 2327, nums = '��P' where city ='���˰���';
update cw_region set gb_code_p = 23, gb_code_c = 2328, nums = '��L' where city ='�ɻ�������';
update cw_region set gb_code_p = 23, gb_code_c = 2329, nums = '��R' where city ='ũ��ϵͳ';
update cw_region set gb_code_p = 32, gb_code_c = 3201, nums = '��A' where city ='�Ͼ�';
update cw_region set gb_code_p = 32, gb_code_c = 3202, nums = '��B' where city ='����';
update cw_region set gb_code_p = 32, gb_code_c = 3203, nums = '��C' where city ='����';
update cw_region set gb_code_p = 32, gb_code_c = 3204, nums = '��D' where city ='����';
update cw_region set gb_code_p = 32, gb_code_c = 3205, nums = '��E' where city ='����';
update cw_region set gb_code_p = 32, gb_code_c = 3206, nums = '��F' where city ='��ͨ';
update cw_region set gb_code_p = 32, gb_code_c = 3207, nums = '��G' where city ='���Ƹ�';
update cw_region set gb_code_p = 32, gb_code_c = 3208, nums = '��H' where city ='����';
update cw_region set gb_code_p = 32, gb_code_c = 3209, nums = '��J' where city ='�γ�';
update cw_region set gb_code_p = 32, gb_code_c = 3210, nums = '��K' where city ='����';
update cw_region set gb_code_p = 32, gb_code_c = 3211, nums = '��L' where city ='��';
update cw_region set gb_code_p = 32, gb_code_c = 3212, nums = '��M' where city ='̩��';
update cw_region set gb_code_p = 32, gb_code_c = 3213, nums = '��N' where city ='��Ǩ';
update cw_region set gb_code_p = 33, gb_code_c = 3301, nums = '��A' where city ='����';
update cw_region set gb_code_p = 33, gb_code_c = 3302, nums = '��B' where city ='����';
update cw_region set gb_code_p = 33, gb_code_c = 3303, nums = '��C' where city ='����';
update cw_region set gb_code_p = 33, gb_code_c = 3304, nums = '��F' where city ='����';
update cw_region set gb_code_p = 33, gb_code_c = 3305, nums = '��E' where city ='����';
update cw_region set gb_code_p = 33, gb_code_c = 3306, nums = '��D' where city ='����';
update cw_region set gb_code_p = 33, gb_code_c = 3307, nums = '��G' where city ='��';
update cw_region set gb_code_p = 33, gb_code_c = 3308, nums = '��H' where city ='����';
update cw_region set gb_code_p = 33, gb_code_c = 3309, nums = '��L' where city ='��ɽ';
update cw_region set gb_code_p = 33, gb_code_c = 3310, nums = '��J' where city ='̨��';
update cw_region set gb_code_p = 33, gb_code_c = 3311, nums = '��K' where city ='��ˮ';
update cw_region set gb_code_p = 34, gb_code_c = 3401, nums = '��A' where city ='�Ϸ�';
update cw_region set gb_code_p = 34, gb_code_c = 3402, nums = '��B' where city ='�ߺ�';
update cw_region set gb_code_p = 34, gb_code_c = 3403, nums = '��C' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3404, nums = '��D' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3405, nums = '��E' where city ='��ɽ';
update cw_region set gb_code_p = 34, gb_code_c = 3406, nums = '��F' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3407, nums = '��G' where city ='ͭ��';
update cw_region set gb_code_p = 34, gb_code_c = 3408, nums = '��H' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3410, nums = '��J' where city ='��ɽ';
update cw_region set gb_code_p = 34, gb_code_c = 3411, nums = '��M' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3412, nums = '��K' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3413, nums = '��L' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3415, nums = '��N' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3416, nums = '��S' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3417, nums = '��R' where city ='����';
update cw_region set gb_code_p = 34, gb_code_c = 3418, nums = '��P' where city ='����';
update cw_region set gb_code_p = 35, gb_code_c = 3501, nums = '��A' where city ='����';
update cw_region set gb_code_p = 35, gb_code_c = 3502, nums = '��D' where city ='����';
update cw_region set gb_code_p = 35, gb_code_c = 3503, nums = '��B' where city ='����';
update cw_region set gb_code_p = 35, gb_code_c = 3504, nums = '��G' where city ='����';
update cw_region set gb_code_p = 35, gb_code_c = 3505, nums = '��C' where city ='Ȫ��';
update cw_region set gb_code_p = 35, gb_code_c = 3506, nums = '��E' where city ='����';
update cw_region set gb_code_p = 35, gb_code_c = 3507, nums = '��H' where city ='��ƽ';
update cw_region set gb_code_p = 35, gb_code_c = 3508, nums = '��F' where city ='����';
update cw_region set gb_code_p = 35, gb_code_c = 3509, nums = '��J' where city ='����';
update cw_region set gb_code_p = 35, gb_code_c = 3510, nums = '��K' where province='����' and city ='ʡֱϵͳ';
update cw_region set gb_code_p = 36, gb_code_c = 3601, nums = '��A' where city ='�ϲ�';
update cw_region set gb_code_p = 36, gb_code_c = 3602, nums = '��H' where city ='������';
update cw_region set gb_code_p = 36, gb_code_c = 3603, nums = '��J' where city ='Ƽ��';
update cw_region set gb_code_p = 36, gb_code_c = 3604, nums = '��G' where city ='�Ž�';
update cw_region set gb_code_p = 36, gb_code_c = 3605, nums = '��K' where city ='����';
update cw_region set gb_code_p = 36, gb_code_c = 3606, nums = '��L' where city ='ӥ̶';
update cw_region set gb_code_p = 36, gb_code_c = 3607, nums = '��B' where city ='����';
update cw_region set gb_code_p = 36, gb_code_c = 3608, nums = '��D' where city ='����';
update cw_region set gb_code_p = 36, gb_code_c = 3609, nums = '��C' where city ='�˴�';
update cw_region set gb_code_p = 36, gb_code_c = 3610, nums = '��F' where city ='����';
update cw_region set gb_code_p = 36, gb_code_c = 3611, nums = '��E' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3701, nums = '³A' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3702, nums = '³B' where city ='�ൺ';
update cw_region set gb_code_p = 37, gb_code_c = 3703, nums = '³C' where city ='�Ͳ�';
update cw_region set gb_code_p = 37, gb_code_c = 3704, nums = '³D' where city ='��ׯ';
update cw_region set gb_code_p = 37, gb_code_c = 3705, nums = '³E' where city ='��Ӫ';
update cw_region set gb_code_p = 37, gb_code_c = 3706, nums = '³F' where city ='��̨';
update cw_region set gb_code_p = 37, gb_code_c = 3707, nums = '³G' where city ='Ϋ��';
update cw_region set gb_code_p = 37, gb_code_c = 3708, nums = '³H' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3709, nums = '³J' where city ='̩��';
update cw_region set gb_code_p = 37, gb_code_c = 3710, nums = '³K' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3711, nums = '³L' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3712, nums = '³S' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3713, nums = '³Q' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3714, nums = '³N' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3715, nums = '³P' where city ='�ĳ�';
update cw_region set gb_code_p = 37, gb_code_c = 3716, nums = '³M' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3717, nums = '³R' where city ='����';
update cw_region set gb_code_p = 37, gb_code_c = 3718, nums = '³U' where city ='�ൺ����';
update cw_region set gb_code_p = 37, gb_code_c = 3719, nums = '³V' where city ='Ϋ������';
update cw_region set gb_code_p = 37, gb_code_c = 3720, nums = '³Y' where city ='��̨����';
update cw_region set gb_code_p = 41, gb_code_c = 4101, nums = 'ԥA' where city ='֣��';
update cw_region set gb_code_p = 41, gb_code_c = 4102, nums = 'ԥB' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 4103, nums = 'ԥC' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 4104, nums = 'ԥD' where city ='ƽ��ɽ';
update cw_region set gb_code_p = 41, gb_code_c = 4105, nums = 'ԥE' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 4106, nums = 'ԥF' where city ='�ױ�';
update cw_region set gb_code_p = 41, gb_code_c = 4107, nums = 'ԥG' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 4108, nums = 'ԥH' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 4109, nums = 'ԥJ' where city ='���';
update cw_region set gb_code_p = 41, gb_code_c = 4110, nums = 'ԥK' where city ='���';
update cw_region set gb_code_p = 41, gb_code_c = 4111, nums = 'ԥL' where city ='���';
update cw_region set gb_code_p = 41, gb_code_c = 4112, nums = 'ԥM' where city ='����Ͽ';
update cw_region set gb_code_p = 41, gb_code_c = 4113, nums = 'ԥR' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 4114, nums = 'ԥN' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 4115, nums = 'ԥS' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 4116, nums = 'ԥP' where city ='�ܿ�';
update cw_region set gb_code_p = 41, gb_code_c = 4117, nums = 'ԥQ' where city ='פ���';
update cw_region set gb_code_p = 42, gb_code_c = 4201, nums = '��A' where city ='�人';
update cw_region set gb_code_p = 42, gb_code_c = 4202, nums = '��B' where city ='��ʯ';
update cw_region set gb_code_p = 42, gb_code_c = 4203, nums = '��C' where city ='ʮ��';
update cw_region set gb_code_p = 42, gb_code_c = 4205, nums = '��E' where city ='�˲�';
update cw_region set gb_code_p = 42, gb_code_c = 4206, nums = '��F' where city ='�差';
update cw_region set gb_code_p = 42, gb_code_c = 4207, nums = '��G' where city ='����';
update cw_region set gb_code_p = 42, gb_code_c = 4208, nums = '��H' where city ='����';
update cw_region set gb_code_p = 42, gb_code_c = 4209, nums = '��K' where city ='Т��';
update cw_region set gb_code_p = 42, gb_code_c = 4210, nums = '��D' where city ='����';
update cw_region set gb_code_p = 42, gb_code_c = 4211, nums = '��J' where city ='�Ƹ�';
update cw_region set gb_code_p = 42, gb_code_c = 4212, nums = '��L' where city ='����';
update cw_region set gb_code_p = 42, gb_code_c = 4213, nums = '��S' where city ='����';
update cw_region set gb_code_p = 42, gb_code_c = 4228, nums = '��Q' where city ='��ʩ';
update cw_region set gb_code_p = 43, gb_code_c = 4301, nums = '��A' where city ='��ɳ';
update cw_region set gb_code_p = 43, gb_code_c = 4302, nums = '��B' where city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4303, nums = '��C' where city ='��̶';
update cw_region set gb_code_p = 43, gb_code_c = 4304, nums = '��D' where city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4305, nums = '��E' where city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4306, nums = '��F' where city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4307, nums = '��J' where city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4308, nums = '��G' where city ='�żҽ�';
update cw_region set gb_code_p = 43, gb_code_c = 4309, nums = '��H' where city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4310, nums = '��L' where city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4311, nums = '��M' where province='����' and city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4312, nums = '��N' where city ='����';
update cw_region set gb_code_p = 43, gb_code_c = 4313, nums = '��K' where city ='¦��';
update cw_region set gb_code_p = 43, gb_code_c = 4331, nums = '��U' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4401, nums = '��A' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4402, nums = '��F' where city ='�ع�';
update cw_region set gb_code_p = 44, gb_code_c = 4403, nums = '��B' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4404, nums = '��C' where city ='�麣';
update cw_region set gb_code_p = 44, gb_code_c = 4405, nums = '��D' where city ='��ͷ';
update cw_region set gb_code_p = 44, gb_code_c = 4406, nums = '��E' where city ='��ɽ';
update cw_region set gb_code_p = 44, gb_code_c = 4407, nums = '��J' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4408, nums = '��G' where city ='տ��';
update cw_region set gb_code_p = 44, gb_code_c = 4409, nums = '��K' where city ='ï��';
update cw_region set gb_code_p = 44, gb_code_c = 4412, nums = '��H' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4413, nums = '��L' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4414, nums = '��M' where city ='÷��';
update cw_region set gb_code_p = 44, gb_code_c = 4415, nums = '��N' where city ='��β';
update cw_region set gb_code_p = 44, gb_code_c = 4416, nums = '��P' where city ='��Դ';
update cw_region set gb_code_p = 44, gb_code_c = 4417, nums = '��Q' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4418, nums = '��R' where city ='��Զ';
update cw_region set gb_code_p = 44, gb_code_c = 4419, nums = '��S' where city ='��ݸ';
update cw_region set gb_code_p = 44, gb_code_c = 4420, nums = '��T' where city ='��ɽ';
update cw_region set gb_code_p = 44, gb_code_c = 4451, nums = '��U' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4452, nums = '��V' where city ='����';
update cw_region set gb_code_p = 44, gb_code_c = 4453, nums = '��W' where city ='�Ƹ�';
update cw_region set gb_code_p = 44, gb_code_c = 4454, nums = '��X' where city ='˳��';
update cw_region set gb_code_p = 44, gb_code_c = 4455, nums = '��Y' where city ='�Ϻ�';
update cw_region set gb_code_p = 44, gb_code_c = 4480, nums = '��Z' where city ='�۰�';
update cw_region set gb_code_p = 45, gb_code_c = 4501, nums = '��A' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4502, nums = '��B' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4503, nums = '��C' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4504, nums = '��D' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4505, nums = '��E' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4506, nums = '��P', city ='���Ǹ�' where id=4491;
update cw_region set gb_code_p = 45, gb_code_c = 4507, nums = '��N' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4508, nums = '��R' where city ='���';
update cw_region set gb_code_p = 45, gb_code_c = 4509, nums = '��K' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4510, nums = '��L' where city ='��ɫ';
update cw_region set gb_code_p = 45, gb_code_c = 4511, nums = '��J' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4512, nums = '��M' where city ='�ӳ�';
update cw_region set gb_code_p = 45, gb_code_c = 4513, nums = '��G' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4514, nums = '��F' where city ='����';
update cw_region set gb_code_p = 45, gb_code_c = 4525, nums = '��H' where city ='����';
update cw_region set gb_code_p = 46, gb_code_c = 4601, nums = '��A' where city ='����';
update cw_region set gb_code_p = 46, gb_code_c = 4602, nums = '��B' where city ='����';
update cw_region set gb_code_p = 46, gb_code_c = 4606, nums = '��E' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5101, nums = '��A' where city ='�ɶ�';
update cw_region set gb_code_p = 51, gb_code_c = 5103, nums = '��C' where city ='�Թ�';
update cw_region set gb_code_p = 51, gb_code_c = 5104, nums = '��D' where city ='��֦��';
update cw_region set gb_code_p = 51, gb_code_c = 5105, nums = '��E' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5106, nums = '��F' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5107, nums = '��B' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5108, nums = '��H' where city ='��Ԫ';
update cw_region set gb_code_p = 51, gb_code_c = 5109, nums = '��J' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5110, nums = '��K' where city ='�ڽ�';
update cw_region set gb_code_p = 51, gb_code_c = 5111, nums = '��L' where city ='��ɽ';
update cw_region set gb_code_p = 51, gb_code_c = 5113, nums = '��R' where city ='�ϳ�';
update cw_region set gb_code_p = 51, gb_code_c = 5114, nums = '��Z' where city ='üɽ';
update cw_region set gb_code_p = 51, gb_code_c = 5115, nums = '��Q' where city ='�˱�';
update cw_region set gb_code_p = 51, gb_code_c = 5116, nums = '��X' where city ='�㰲';
update cw_region set gb_code_p = 51, gb_code_c = 5117, nums = '��S' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5118, nums = '��T' where city ='�Ű�';
update cw_region set gb_code_p = 51, gb_code_c = 5119, nums = '��Y' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5120, nums = '��M' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5132, nums = '��U' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5133, nums = '��V' where city ='����';
update cw_region set gb_code_p = 51, gb_code_c = 5134, nums = '��W' where city ='��ɽ';
update cw_region set gb_code_p = 52, gb_code_c = 5201, nums = '��A' where city ='����';
update cw_region set gb_code_p = 52, gb_code_c = 5202, nums = '��B' where city ='����ˮ';
update cw_region set gb_code_p = 52, gb_code_c = 5203, nums = '��C' where city ='����';
update cw_region set gb_code_p = 52, gb_code_c = 5204, nums = '��G' where city ='��˳';
update cw_region set gb_code_p = 52, gb_code_c = 5205, nums = '��F' where city ='�Ͻ�';
update cw_region set gb_code_p = 52, gb_code_c = 5206, nums = '��D' where city ='ͭ��';
update cw_region set gb_code_p = 52, gb_code_c = 5223, nums = '��E' where city ='ǭ����';
update cw_region set gb_code_p = 52, gb_code_c = 5226, nums = '��H' where city ='ǭ����';
update cw_region set gb_code_p = 52, gb_code_c = 5227, nums = '��J' where city ='ǭ��';
update cw_region set gb_code_p = 53, gb_code_c = 5301, nums = '��A' where city ='����';
update cw_region set gb_code_p = 53, gb_code_c = 5303, nums = '��D' where city ='����';
update cw_region set gb_code_p = 53, gb_code_c = 5304, nums = '��F' where city ='��Ϫ';
update cw_region set gb_code_p = 53, gb_code_c = 5305, nums = '��M' where city ='��ɽ';
update cw_region set gb_code_p = 53, gb_code_c = 5306, nums = '��C' where city ='��ͨ';
update cw_region set gb_code_p = 53, gb_code_c = 5307, nums = '��P' where city ='����';
update cw_region set gb_code_p = 53, gb_code_c = 5308, nums = '��J' where city ='�ն�';
update cw_region set gb_code_p = 53, gb_code_c = 5309, nums = '��S' where city ='�ٲ�';
update cw_region set gb_code_p = 53, gb_code_c = 5323, nums = '��E' where city ='����';
update cw_region set gb_code_p = 53, gb_code_c = 5325, nums = '��G' where city ='���';
update cw_region set gb_code_p = 53, gb_code_c = 5326, nums = '��H' where city ='��ɽ';
update cw_region set gb_code_p = 53, gb_code_c = 5328, nums = '��K' where city ='��˫����';
update cw_region set gb_code_p = 53, gb_code_c = 5329, nums = '��L' where city ='����';
update cw_region set gb_code_p = 53, gb_code_c = 5331, nums = '��N' where city ='�º�';
update cw_region set gb_code_p = 53, gb_code_c = 5333, nums = '��Q' where city ='ŭ��';
update cw_region set gb_code_p = 53, gb_code_c = 5334, nums = '��R' where city ='����';
update cw_region set gb_code_p = 54, gb_code_c = 5401, nums = '��A' where city ='����';
update cw_region set gb_code_p = 54, gb_code_c = 5421, nums = '��B' where city ='����';
update cw_region set gb_code_p = 54, gb_code_c = 5422, nums = '��C' where city ='ɽ��';
update cw_region set gb_code_p = 54, gb_code_c = 5423, nums = '��D' where city ='�տ���';
update cw_region set gb_code_p = 54, gb_code_c = 5424, nums = '��E' where city ='����';
update cw_region set gb_code_p = 54, gb_code_c = 5425, nums = '��F' where city ='����';
update cw_region set gb_code_p = 54, gb_code_c = 5426, nums = '��G' where city ='��֥';
update cw_region set gb_code_p = 61, gb_code_c = 6101, nums = '��A' where city ='����';
update cw_region set gb_code_p = 61, gb_code_c = 6102, nums = '��B' where city ='ͭ��';
update cw_region set gb_code_p = 61, gb_code_c = 6103, nums = '��C' where city ='����';
update cw_region set gb_code_p = 61, gb_code_c = 6104, nums = '��D' where city ='����';
update cw_region set gb_code_p = 61, gb_code_c = 6105, nums = '��E' where city ='μ��';
update cw_region set gb_code_p = 61, gb_code_c = 6106, nums = '��J' where city ='�Ӱ�';
update cw_region set gb_code_p = 61, gb_code_c = 6107, nums = '��F' where city ='����';
update cw_region set gb_code_p = 61, gb_code_c = 6108, nums = '��K' where city ='����';
update cw_region set gb_code_p = 61, gb_code_c = 6109, nums = '��G' where city ='����';
update cw_region set gb_code_p = 61, gb_code_c = 6110, nums = '��H' where city ='����';
update cw_region set gb_code_p = 61, gb_code_c = 6125, nums = '��V' where city ='����';
update cw_region set gb_code_p = 62, gb_code_c = 6201, nums = '��A' where city ='����';
update cw_region set gb_code_p = 62, gb_code_c = 6202, nums = '��B' where city ='������';
update cw_region set gb_code_p = 62, gb_code_c = 6203, nums = '��C' where city ='���';
update cw_region set gb_code_p = 62, gb_code_c = 6204, nums = '��D' where city ='����';
update cw_region set gb_code_p = 62, gb_code_c = 6205, nums = '��E' where city ='��ˮ';
update cw_region set gb_code_p = 62, gb_code_c = 6206, nums = '��H' where city ='����';
update cw_region set gb_code_p = 62, gb_code_c = 6207, nums = '��G' where city ='��Ҵ';
update cw_region set gb_code_p = 62, gb_code_c = 6208, nums = '��L' where city ='ƽ��';
update cw_region set gb_code_p = 62, gb_code_c = 6209, nums = '��F' where city ='��Ȫ';
update cw_region set gb_code_p = 62, gb_code_c = 6210, nums = '��M' where city ='����';
update cw_region set gb_code_p = 62, gb_code_c = 6211, nums = '��J' where city ='����';
update cw_region set gb_code_p = 62, gb_code_c = 6212, nums = '��K' where city ='¤��';
update cw_region set gb_code_p = 62, gb_code_c = 6229, nums = '��N' where city ='����';
update cw_region set gb_code_p = 62, gb_code_c = 6230, nums = '��P' where city ='����';
update cw_region set gb_code_p = 63, gb_code_c = 6301, nums = '��A' where city ='����';
update cw_region set gb_code_p = 63, gb_code_c = 6321, nums = '��B' where city ='����';
update cw_region set gb_code_p = 63, gb_code_c = 6322, nums = '��C' where city ='����';
update cw_region set gb_code_p = 63, gb_code_c = 6323, nums = '��D' where city ='����';
update cw_region set gb_code_p = 63, gb_code_c = 6325, nums = '��E' where city ='����';
update cw_region set gb_code_p = 63, gb_code_c = 6326, nums = '��F' where city ='����';
update cw_region set gb_code_p = 63, gb_code_c = 6327, nums = '��G' where city ='����';
update cw_region set gb_code_p = 63, gb_code_c = 6328, nums = '��H' where city ='����';
update cw_region set gb_code_p = 64, gb_code_c = 6401, nums = '��A' where city ='����';
update cw_region set gb_code_p = 64, gb_code_c = 6402, nums = '��B' where city ='ʯ��ɽ';
update cw_region set gb_code_p = 64, gb_code_c = 6403, nums = '��C' where city ='����';
update cw_region set gb_code_p = 64, gb_code_c = 6404, nums = '��D' where city ='��ԭ';
update cw_region set gb_code_p = 64, gb_code_c = 6405, nums = '��E' where city ='����';
update cw_region set gb_code_p = 65, gb_code_c = 6501, nums = '��A' where city ='��³ľ��';
update cw_region set gb_code_p = 65, gb_code_c = 6502, nums = '��J' where city ='��������';
update cw_region set gb_code_p = 65, gb_code_c = 6521, nums = '��K' where city ='��³��';
update cw_region set gb_code_p = 65, gb_code_c = 6522, nums = '��L' where city ='����';
update cw_region set gb_code_p = 65, gb_code_c = 6523, nums = '��B' where city ='����';
update cw_region set gb_code_p = 65, gb_code_c = 6527, nums = '��E' where city ='��������';
update cw_region set gb_code_p = 65, gb_code_c = 6528, nums = '��M' where city ='��������';
update cw_region set gb_code_p = 65, gb_code_c = 6529, nums = '��N' where city ='������';
update cw_region set gb_code_p = 65, gb_code_c = 6530, nums = '��P' where city ='��������';
update cw_region set gb_code_p = 65, gb_code_c = 6531, nums = '��Q' where city ='��ʲ';
update cw_region set gb_code_p = 65, gb_code_c = 6532, nums = '��R' where city ='����';
update cw_region set gb_code_p = 65, gb_code_c = 6540, nums = '��F' where city ='����';
update cw_region set gb_code_p = 65, gb_code_c = 6542, nums = '��G' where city ='����';
update cw_region set gb_code_p = 65, gb_code_c = 6543, nums = '��H' where city ='����̩';
update cw_region set gb_code_p = 34, gb_code_c = 340181, nums = '��Q' where city ='����';
update cw_region set gb_code_p = 41, gb_code_c = 419001, nums = 'ԥU' where city ='��Դ';
update cw_region set gb_code_p = 42, gb_code_c = 429004, nums = '��M' where city ='����';
update cw_region set gb_code_p = 42, gb_code_c = 429005, nums = '��N' where city ='Ǳ��';
update cw_region set gb_code_p = 42, gb_code_c = 429006, nums = '��R' where city ='����';
update cw_region set gb_code_p = 42, gb_code_c = 429021, nums = '��P' where city ='��ũ��';
update cw_region set gb_code_p = 46, gb_code_c = 469001, nums = '��D' where city ='��ָɽ';
update cw_region set gb_code_p = 46, gb_code_c = 469002, nums = '��C' where city ='��';
update cw_region set gb_code_p = 51, gb_code_c = 513401, nums = '��W' where city ='����';
update cw_region set gb_code_p = 65, gb_code_c = 654003, nums = '��D' where city ='����';
update cw_region set gb_code_p = 65, gb_code_c = 659001, nums = '��C' where city ='ʯ����';

delete from cw_region where province='����' and city='����';
delete from cw_region where province='����' and city='ʡֱϵͳ';
update cw_region set gb_code_p = 36, gb_code_c = 3601, nums = '��A' where city ='�ϲ�';
update cw_region set gb_code_p = 50, gb_code_c = 50, nums = '��C' where province='����' and city ='����';
update cw_region set gb_code_p = 50, gb_code_c = 50, city = '����' where province='����' and city ='ֱ������';
update cw_region set gb_code_p = 36, gb_code_c = 3612, nums = '��M' where province='����' and city ='ʡֱϵͳ';
     
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '���', '���', '��', '��', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '����', '����ȫʡ', '��', '��', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '����', '����ȫʡ', '��', '��', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '����', '����ȫʡ', 'ԥ', 'ԥ', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '����', '����ȫʡ', '��', '��', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '����', '����ȫʡ', '��', '��', 0, 50);

insert into cw_code (port, code, content) values ('cx580.com', '0', '�ɹ�');
insert into cw_code (port, code, content) values ('cx580.com', '-1', 'ȱ�ٱ�Ҫ�Ĳ������Ҳ�������ǰ׺��ƥ��ĳ���');
insert into cw_code (port, code, content) values ('cx580.com', '-3', '��ϵͳ�ݲ��ṩ�ó���Υ�²�ѯ����');
insert into cw_code (port, code, content) values ('cx580.com', '-5', '���������󣨳�ʱ�����ݻ�ȡ�쳣�ȣ�');
insert into cw_code (port, code, content) values ('cx580.com', '-10', 'δ����Ȩ���ʸ÷�����û������벻��ȷ');
insert into cw_code (port, code, content) values ('cx580.com', '-20', 'δ�ʹ���');
insert into cw_code (port, code, content) values ('cx580.com', '-40', 'δ����Ȩ��ѯ�˳�����Ϣ');
insert into cw_code (port, code, content) values ('cx580.com', '-41', '�����������������ԴҪ��');
insert into cw_code (port, code, content) values ('cx580.com', '-42', '����Դ�ݲ�����');
insert into cw_code (port, code, content) values ('cx580.com', '-43', '���ղ�ѯ���Ѵﵽ��Ȩ����׼���޷�������ѯ');
insert into cw_code (port, code, content) values ('cx580.com', '-44', '�Ѵﵽ��ѯ����');
insert into cw_code (port, code, content) values ('cx580.com', '-45', 'ȷ�����������ԣ��Ƿ񱻴۸�');
insert into cw_code (port, code, content) values ('cx580.com', '-6', '������������Ϣ����');
insert into cw_code (port, code, content) values ('cx580.com', '-61', '���복�ƺ�����');
insert into cw_code (port, code, content) values ('cx580.com', '-62', '���복�ܺ�����');
insert into cw_code (port, code, content) values ('cx580.com', '-63', '���뷢����������');
insert into cw_code (port, code, content) values ('cx580.com', '-66', '��֧�ֵĳ�������');
insert into cw_code (port, code, content) values ('cx580.com', '-67', '��ʡ�ݣ����У���֧����س���');


insert into cw_code (port, code, content) values ('cx580.com', '-9999', '����Դ��ѯ��ʱ');
insert into cw_code (port, code, content) values ('cheshouye.com', '-9999', '����Դ��ѯ��ʱ');
insert into cw_code (port, code, content) values ('http://120.26.57.239/api/', '-9999', '����Դ��ѯ��ʱ');

insert into cw_code (port, code, content) values ('cx580.com', '-9998', '����Դ��ѯ�����쳣');
insert into cw_code (port, code, content) values ('cheshouye.com', '-9998', '����Դ��ѯ�����쳣');
insert into cw_code (port, code, content) values ('http://120.26.57.239/api/', '-9998', '����Դ��ѯ�����쳣');

