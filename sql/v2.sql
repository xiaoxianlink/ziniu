
-- ----------------------------
-- dynamic pricing
-- ----------------------------
DROP TABLE IF EXISTS `cw_services_dyna`;
CREATE TABLE `cw_services_dyna` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `services_id` varchar(32) NOT NULL COMMENT '服务商id',
  `code` int(11) NOT NULL COMMENT '城市code',
  `point_fee` int(11) NOT NULL COMMENT '违章代码id',
  `fee` int(10) NOT NULL COMMENT '定价',
  `create_time` int(11) DEFAULT NULL COMMENT '定价时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk ROW_FORMAT=DYNAMIC COMMENT='服务商定价表';

ALTER TABLE `cw_order`
ADD COLUMN `so_type`  int(2) NULL DEFAULT 1 AFTER `so_id`;

-- ----------------------------
-- mobile app
-- ----------------------------
ALTER TABLE `cw_services` ADD `nickname` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '昵称' ;

ALTER TABLE `cw_message` CHANGE `msg_type` `msg_type` INT(11) NULL DEFAULT NULL COMMENT '消息类型；1：违章提醒，2：新违章提醒，3：小仙提醒，4：小仙账务';

ALTER TABLE `cw_message` ADD `content` VARCHAR(128) NULL COMMENT '消息内容' ;

ALTER TABLE `cw_message` ADD `is_readed` INT(10) NULL COMMENT '消息状态：0未读，1已读' ;

ALTER TABLE `cw_message` CHANGE `msg_type` `msg_type` INT(11) NULL DEFAULT NULL COMMENT '消息类型；1：违章提醒，2：新违章提醒，3：小仙提醒，4：小仙账务，5：客服小仙';

ALTER TABLE `cw_message` ADD `tixing_type` INT(10) NULL COMMENT '小仙提醒类型，当msg_type=3时有效' AFTER `msg_type`;

ALTER TABLE `cw_message` CHANGE `tixing_type` `tixing_type` INT(10) NULL COMMENT '小仙提醒类型，当msg_type=3时有效。1开始接单状态更新提醒，2系统升级提醒，3新单推送提醒，4新单即将过期提醒，5接单处理即将过期提醒，6接单成功提醒';

ALTER TABLE `cw_message` ADD `zhangwu_type` INT(10) NULL COMMENT '小仙账务类型，当msg_type=4时有效' AFTER `tixing_type`;

ALTER TABLE `cw_message` CHANGE `zhangwu_type` `zhangwu_type` INT(10) NULL COMMENT '小仙账务类型，当type=4时有效。1可提现金额变动提醒，2账户金额变动提醒，3提现申请提醒，4提现到账提醒';

ALTER TABLE `cw_message` ADD `icon` VARCHAR(32) NULL COMMENT '图片' ;

ALTER TABLE `cw_message` CHANGE `tixing_type` `tixing_type` INT(10) NULL COMMENT '小仙提醒类型，当msg_type=3时有效。1开始接单状态更新提醒，2新订单即将过期提醒，3接单处理即将过期提醒，4新订单提醒，5新单超时取消提醒，6接单超时取消提醒，7订单结算提醒，8帮助与规则更新';

ALTER TABLE `cw_message` CHANGE `zhangwu_type` `zhangwu_type` INT(10) NULL COMMENT '小仙账务类型，当ty1账户余额变动提醒，2可提现金额变动提醒，3绑定银行卡/解绑银行卡，4提现操作';

CREATE TABLE `cw_yinhang` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号id 自增长主键' , `bank_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '银行名称' , `bank_img` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '缩略图' , `state` INT(11) NOT NULL COMMENT '是否使用：0使用，1不使用' , `sort` INT(11) NOT NULL COMMENT '排序' , PRIMARY KEY (`id`) );

CREATE TABLE `cw_duanxin` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增长主键' , `phone` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机号码' , `code` VARCHAR(32) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL COMMENT '短信验证码' , `expires_time` DATETIME NOT NULL COMMENT '短信过期时间' , `create_time` DATETIME NOT NULL COMMENT '创建时间' , PRIMARY KEY (`id`) );

CREATE TABLE `cw_secret` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增长主键' , `username` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登录账户' , `secret` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '秘钥' , `clientUUID` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备id 用于区分每台设备' , `user_agent` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User-Agent' , `expires_time` DATETIME NOT NULL COMMENT '秘钥过期时间' , `update_time` DATETIME NOT NULL COMMENT '秘钥更新时间' , PRIMARY KEY (`id`) ) COMMENT = '秘钥记录';

ALTER TABLE `cw_bank` ADD `yh_id` INT(11) NULL COMMENT '银行id' ;

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

insert into cw_menu (parentid, app, model, action, type, status, name, listorder) values (165, 'Admin_new', 'Bizapi',	'bizapi_list', 1, 1, '商用接口管理', 5);

insert into cw_auth_rule (module, type, name, title, status) values ('Admin', 'admin_url', 'admin_new/bizapi/bizapi_list', '商用接口管理', 1);	

ALTER TABLE `cw_region`
ADD COLUMN `gb_code_p`  int(4) NULL AFTER `abbreviation`,
ADD COLUMN `gb_code_c`  int(8) NULL AFTER `gb_code_p`,
ADD COLUMN `cxy_engine_nums`  int(4) NULL AFTER `c_frame_nums`,
ADD COLUMN `cxy_frame_nums`  int(4) NULL AFTER `cxy_engine_nums`;

update `cw_region` set gb_code_p = 11, gb_code_c = 11  where city = '北京';
update `cw_region` set gb_code_p = 12, gb_code_c = 12  where city = '天津';
update `cw_region` set gb_code_p = 31, gb_code_c = 31  where city = '上海';
update `cw_region` set gb_code_p = 50, gb_code_c = 50  where province = '重庆';
update cw_region set gb_code_p = 13, gb_code_c = 1301, nums = '冀A' where city ='石家庄';
update cw_region set gb_code_p = 13, gb_code_c = 1302, nums = '冀B' where city ='唐山';
update cw_region set gb_code_p = 13, gb_code_c = 1303, nums = '冀C' where city ='秦皇岛';
update cw_region set gb_code_p = 13, gb_code_c = 1304, nums = '冀D' where city ='邯郸';
update cw_region set gb_code_p = 13, gb_code_c = 1305, nums = '冀E' where city ='邢台';
update cw_region set gb_code_p = 13, gb_code_c = 1306, nums = '冀F' where city ='保定';
update cw_region set gb_code_p = 13, gb_code_c = 1307, nums = '冀G' where city ='张家口';
update cw_region set gb_code_p = 13, gb_code_c = 1308, nums = '冀H' where city ='承德';
update cw_region set gb_code_p = 13, gb_code_c = 1309, nums = '冀J' where city ='沧州';
update cw_region set gb_code_p = 13, gb_code_c = 1310, nums = '冀R' where city ='廊坊';
update cw_region set gb_code_p = 13, gb_code_c = 1311, nums = '冀T' where city ='衡水';
update cw_region set gb_code_p = 13, gb_code_c = 1312, nums = '冀S' where city ='沧州行署';
update cw_region set gb_code_p = 14, gb_code_c = 1401, nums = '晋A' where city ='太原';
update cw_region set gb_code_p = 14, gb_code_c = 1402, nums = '晋B' where city ='大同';
update cw_region set gb_code_p = 14, gb_code_c = 1403, nums = '晋C' where city ='阳泉';
update cw_region set gb_code_p = 14, gb_code_c = 1404, nums = '晋D' where city ='长治';
update cw_region set gb_code_p = 14, gb_code_c = 1405, nums = '晋E' where city ='晋城';
update cw_region set gb_code_p = 14, gb_code_c = 1406, nums = '晋F' where city ='朔州';
update cw_region set gb_code_p = 14, gb_code_c = 1407, nums = '晋K' where city ='晋中';
update cw_region set gb_code_p = 14, gb_code_c = 1408, nums = '晋M' where city ='运城';
update cw_region set gb_code_p = 14, gb_code_c = 1409, nums = '晋H' where city ='忻州';
update cw_region set gb_code_p = 14, gb_code_c = 1410, nums = '晋L' where city ='临汾';
update cw_region set gb_code_p = 14, gb_code_c = 1411, nums = '晋J' where city ='吕梁';
update cw_region set gb_code_p = 14, gb_code_c = 1423, nums = '晋G' where city ='雁北';
update cw_region set gb_code_p = 15, gb_code_c = 1501, nums = '蒙A' where city ='呼和浩特';
update cw_region set gb_code_p = 15, gb_code_c = 1502, nums = '蒙B' where city ='包头';
update cw_region set gb_code_p = 15, gb_code_c = 1503, nums = '蒙C' where city ='乌海';
update cw_region set gb_code_p = 15, gb_code_c = 1504, nums = '蒙D' where city ='赤峰';
update cw_region set gb_code_p = 15, gb_code_c = 1505, nums = '蒙G' where city ='通辽';
update cw_region set gb_code_p = 15, gb_code_c = 1506, nums = '蒙K' where city ='鄂尔多斯';
update cw_region set gb_code_p = 15, gb_code_c = 1507, nums = '蒙E' where city ='呼伦贝尔';
update cw_region set gb_code_p = 15, gb_code_c = 1508, nums = '蒙L' where city ='巴彦淖尔';
update cw_region set gb_code_p = 15, gb_code_c = 1509, nums = '蒙J' where city ='乌兰察布';
update cw_region set gb_code_p = 15, gb_code_c = 1522, nums = '蒙F' where city ='兴安';
update cw_region set gb_code_p = 15, gb_code_c = 1525, nums = '蒙H' where city ='锡林郭勒';
update cw_region set gb_code_p = 15, gb_code_c = 1529, nums = '蒙M' where city ='阿拉善';
update cw_region set gb_code_p = 21, gb_code_c = 2101, nums = '辽A' where city ='沈阳';
update cw_region set gb_code_p = 21, gb_code_c = 2102, nums = '辽B' where city ='大连';
update cw_region set gb_code_p = 21, gb_code_c = 2103, nums = '辽C' where city ='鞍山';
update cw_region set gb_code_p = 21, gb_code_c = 2104, nums = '辽D' where city ='抚顺';
update cw_region set gb_code_p = 21, gb_code_c = 2105, nums = '辽E' where city ='本溪';
update cw_region set gb_code_p = 21, gb_code_c = 2106, nums = '辽F' where city ='丹东';
update cw_region set gb_code_p = 21, gb_code_c = 2107, nums = '辽G' where city ='锦州';
update cw_region set gb_code_p = 21, gb_code_c = 2108, nums = '辽H' where city ='营口';
update cw_region set gb_code_p = 21, gb_code_c = 2109, nums = '辽J' where city ='阜新';
update cw_region set gb_code_p = 21, gb_code_c = 2110, nums = '辽K' where city ='辽阳';
update cw_region set gb_code_p = 21, gb_code_c = 2111, nums = '辽L' where city ='盘锦';
update cw_region set gb_code_p = 21, gb_code_c = 2112, nums = '辽M' where city ='铁岭';
update cw_region set gb_code_p = 21, gb_code_c = 2113, nums = '辽N' where city ='朝阳';
update cw_region set gb_code_p = 21, gb_code_c = 2114, nums = '辽P' where city ='葫芦岛';
update cw_region set gb_code_p = 22, gb_code_c = 2201, nums = '吉A' where city ='长春';
update cw_region set gb_code_p = 22, gb_code_c = 2202, nums = '吉B' where city ='吉林';
update cw_region set gb_code_p = 22, gb_code_c = 2203, nums = '吉C' where city ='四平';
update cw_region set gb_code_p = 22, gb_code_c = 2204, nums = '吉D' where city ='辽源';
update cw_region set gb_code_p = 22, gb_code_c = 2205, nums = '吉E' where city ='通化';
update cw_region set gb_code_p = 22, gb_code_c = 2206, nums = '吉F' where city ='白山';
update cw_region set gb_code_p = 22, gb_code_c = 2207, nums = '吉J' where city ='松原';
update cw_region set gb_code_p = 22, gb_code_c = 2208, nums = '吉G' where city ='白城';
update cw_region set gb_code_p = 22, gb_code_c = 2224, nums = '吉H' where city ='延边';
update cw_region set gb_code_p = 23, gb_code_c = 2301, nums = '黑A' where city ='哈尔滨';
update cw_region set gb_code_p = 23, gb_code_c = 2302, nums = '黑B' where city ='齐齐哈尔';
update cw_region set gb_code_p = 23, gb_code_c = 2303, nums = '黑G' where city ='鸡西';
update cw_region set gb_code_p = 23, gb_code_c = 2304, nums = '黑H' where city ='鹤岗';
update cw_region set gb_code_p = 23, gb_code_c = 2305, nums = '黑J' where city ='双鸭山';
update cw_region set gb_code_p = 23, gb_code_c = 2306, nums = '黑E' where city ='大庆';
update cw_region set gb_code_p = 23, gb_code_c = 2307, nums = '黑F' where city ='伊春';
update cw_region set gb_code_p = 23, gb_code_c = 2308, nums = '黑D' where city ='佳木斯';
update cw_region set gb_code_p = 23, gb_code_c = 2309, nums = '黑K', city ='七台河' where id = 4373;
update cw_region set gb_code_p = 23, gb_code_c = 2310, nums = '黑C' where city ='牡丹江';
update cw_region set gb_code_p = 23, gb_code_c = 2311, nums = '黑N' where city ='黑河';
update cw_region set gb_code_p = 23, gb_code_c = 2312, nums = '黑M' where city ='绥化';
update cw_region set gb_code_p = 23, gb_code_c = 2327, nums = '黑P' where city ='大兴安岭';
update cw_region set gb_code_p = 23, gb_code_c = 2328, nums = '黑L' where city ='松花江地区';
update cw_region set gb_code_p = 23, gb_code_c = 2329, nums = '黑R' where city ='农垦系统';
update cw_region set gb_code_p = 32, gb_code_c = 3201, nums = '苏A' where city ='南京';
update cw_region set gb_code_p = 32, gb_code_c = 3202, nums = '苏B' where city ='无锡';
update cw_region set gb_code_p = 32, gb_code_c = 3203, nums = '苏C' where city ='徐州';
update cw_region set gb_code_p = 32, gb_code_c = 3204, nums = '苏D' where city ='常州';
update cw_region set gb_code_p = 32, gb_code_c = 3205, nums = '苏E' where city ='苏州';
update cw_region set gb_code_p = 32, gb_code_c = 3206, nums = '苏F' where city ='南通';
update cw_region set gb_code_p = 32, gb_code_c = 3207, nums = '苏G' where city ='连云港';
update cw_region set gb_code_p = 32, gb_code_c = 3208, nums = '苏H' where city ='淮安';
update cw_region set gb_code_p = 32, gb_code_c = 3209, nums = '苏J' where city ='盐城';
update cw_region set gb_code_p = 32, gb_code_c = 3210, nums = '苏K' where city ='扬州';
update cw_region set gb_code_p = 32, gb_code_c = 3211, nums = '苏L' where city ='镇江';
update cw_region set gb_code_p = 32, gb_code_c = 3212, nums = '苏M' where city ='泰州';
update cw_region set gb_code_p = 32, gb_code_c = 3213, nums = '苏N' where city ='宿迁';
update cw_region set gb_code_p = 33, gb_code_c = 3301, nums = '浙A' where city ='杭州';
update cw_region set gb_code_p = 33, gb_code_c = 3302, nums = '浙B' where city ='宁波';
update cw_region set gb_code_p = 33, gb_code_c = 3303, nums = '浙C' where city ='温州';
update cw_region set gb_code_p = 33, gb_code_c = 3304, nums = '浙F' where city ='嘉兴';
update cw_region set gb_code_p = 33, gb_code_c = 3305, nums = '浙E' where city ='湖州';
update cw_region set gb_code_p = 33, gb_code_c = 3306, nums = '浙D' where city ='绍兴';
update cw_region set gb_code_p = 33, gb_code_c = 3307, nums = '浙G' where city ='金华';
update cw_region set gb_code_p = 33, gb_code_c = 3308, nums = '浙H' where city ='衢州';
update cw_region set gb_code_p = 33, gb_code_c = 3309, nums = '浙L' where city ='舟山';
update cw_region set gb_code_p = 33, gb_code_c = 3310, nums = '浙J' where city ='台州';
update cw_region set gb_code_p = 33, gb_code_c = 3311, nums = '浙K' where city ='丽水';
update cw_region set gb_code_p = 34, gb_code_c = 3401, nums = '皖A' where city ='合肥';
update cw_region set gb_code_p = 34, gb_code_c = 3402, nums = '皖B' where city ='芜湖';
update cw_region set gb_code_p = 34, gb_code_c = 3403, nums = '皖C' where city ='蚌埠';
update cw_region set gb_code_p = 34, gb_code_c = 3404, nums = '皖D' where city ='淮南';
update cw_region set gb_code_p = 34, gb_code_c = 3405, nums = '皖E' where city ='马鞍山';
update cw_region set gb_code_p = 34, gb_code_c = 3406, nums = '皖F' where city ='淮北';
update cw_region set gb_code_p = 34, gb_code_c = 3407, nums = '皖G' where city ='铜陵';
update cw_region set gb_code_p = 34, gb_code_c = 3408, nums = '皖H' where city ='安庆';
update cw_region set gb_code_p = 34, gb_code_c = 3410, nums = '皖J' where city ='黄山';
update cw_region set gb_code_p = 34, gb_code_c = 3411, nums = '皖M' where city ='滁州';
update cw_region set gb_code_p = 34, gb_code_c = 3412, nums = '皖K' where city ='阜阳';
update cw_region set gb_code_p = 34, gb_code_c = 3413, nums = '皖L' where city ='宿州';
update cw_region set gb_code_p = 34, gb_code_c = 3415, nums = '皖N' where city ='六安';
update cw_region set gb_code_p = 34, gb_code_c = 3416, nums = '皖S' where city ='亳州';
update cw_region set gb_code_p = 34, gb_code_c = 3417, nums = '皖R' where city ='池州';
update cw_region set gb_code_p = 34, gb_code_c = 3418, nums = '皖P' where city ='宣城';
update cw_region set gb_code_p = 35, gb_code_c = 3501, nums = '闽A' where city ='福州';
update cw_region set gb_code_p = 35, gb_code_c = 3502, nums = '闽D' where city ='厦门';
update cw_region set gb_code_p = 35, gb_code_c = 3503, nums = '闽B' where city ='莆田';
update cw_region set gb_code_p = 35, gb_code_c = 3504, nums = '闽G' where city ='三明';
update cw_region set gb_code_p = 35, gb_code_c = 3505, nums = '闽C' where city ='泉州';
update cw_region set gb_code_p = 35, gb_code_c = 3506, nums = '闽E' where city ='漳州';
update cw_region set gb_code_p = 35, gb_code_c = 3507, nums = '闽H' where city ='南平';
update cw_region set gb_code_p = 35, gb_code_c = 3508, nums = '闽F' where city ='龙岩';
update cw_region set gb_code_p = 35, gb_code_c = 3509, nums = '闽J' where city ='宁德';
update cw_region set gb_code_p = 35, gb_code_c = 3510, nums = '闽K' where province='福建' and city ='省直系统';
update cw_region set gb_code_p = 36, gb_code_c = 3601, nums = '赣A' where city ='南昌';
update cw_region set gb_code_p = 36, gb_code_c = 3602, nums = '赣H' where city ='景德镇';
update cw_region set gb_code_p = 36, gb_code_c = 3603, nums = '赣J' where city ='萍乡';
update cw_region set gb_code_p = 36, gb_code_c = 3604, nums = '赣G' where city ='九江';
update cw_region set gb_code_p = 36, gb_code_c = 3605, nums = '赣K' where city ='新余';
update cw_region set gb_code_p = 36, gb_code_c = 3606, nums = '赣L' where city ='鹰潭';
update cw_region set gb_code_p = 36, gb_code_c = 3607, nums = '赣B' where city ='赣州';
update cw_region set gb_code_p = 36, gb_code_c = 3608, nums = '赣D' where city ='吉安';
update cw_region set gb_code_p = 36, gb_code_c = 3609, nums = '赣C' where city ='宜春';
update cw_region set gb_code_p = 36, gb_code_c = 3610, nums = '赣F' where city ='抚州';
update cw_region set gb_code_p = 36, gb_code_c = 3611, nums = '赣E' where city ='上饶';
update cw_region set gb_code_p = 37, gb_code_c = 3701, nums = '鲁A' where city ='济南';
update cw_region set gb_code_p = 37, gb_code_c = 3702, nums = '鲁B' where city ='青岛';
update cw_region set gb_code_p = 37, gb_code_c = 3703, nums = '鲁C' where city ='淄博';
update cw_region set gb_code_p = 37, gb_code_c = 3704, nums = '鲁D' where city ='枣庄';
update cw_region set gb_code_p = 37, gb_code_c = 3705, nums = '鲁E' where city ='东营';
update cw_region set gb_code_p = 37, gb_code_c = 3706, nums = '鲁F' where city ='烟台';
update cw_region set gb_code_p = 37, gb_code_c = 3707, nums = '鲁G' where city ='潍坊';
update cw_region set gb_code_p = 37, gb_code_c = 3708, nums = '鲁H' where city ='济宁';
update cw_region set gb_code_p = 37, gb_code_c = 3709, nums = '鲁J' where city ='泰安';
update cw_region set gb_code_p = 37, gb_code_c = 3710, nums = '鲁K' where city ='威海';
update cw_region set gb_code_p = 37, gb_code_c = 3711, nums = '鲁L' where city ='日照';
update cw_region set gb_code_p = 37, gb_code_c = 3712, nums = '鲁S' where city ='莱芜';
update cw_region set gb_code_p = 37, gb_code_c = 3713, nums = '鲁Q' where city ='临沂';
update cw_region set gb_code_p = 37, gb_code_c = 3714, nums = '鲁N' where city ='德州';
update cw_region set gb_code_p = 37, gb_code_c = 3715, nums = '鲁P' where city ='聊城';
update cw_region set gb_code_p = 37, gb_code_c = 3716, nums = '鲁M' where city ='滨州';
update cw_region set gb_code_p = 37, gb_code_c = 3717, nums = '鲁R' where city ='菏泽';
update cw_region set gb_code_p = 37, gb_code_c = 3718, nums = '鲁U' where city ='青岛增补';
update cw_region set gb_code_p = 37, gb_code_c = 3719, nums = '鲁V' where city ='潍坊增补';
update cw_region set gb_code_p = 37, gb_code_c = 3720, nums = '鲁Y' where city ='烟台增补';
update cw_region set gb_code_p = 41, gb_code_c = 4101, nums = '豫A' where city ='郑州';
update cw_region set gb_code_p = 41, gb_code_c = 4102, nums = '豫B' where city ='开封';
update cw_region set gb_code_p = 41, gb_code_c = 4103, nums = '豫C' where city ='洛阳';
update cw_region set gb_code_p = 41, gb_code_c = 4104, nums = '豫D' where city ='平顶山';
update cw_region set gb_code_p = 41, gb_code_c = 4105, nums = '豫E' where city ='安阳';
update cw_region set gb_code_p = 41, gb_code_c = 4106, nums = '豫F' where city ='鹤壁';
update cw_region set gb_code_p = 41, gb_code_c = 4107, nums = '豫G' where city ='新乡';
update cw_region set gb_code_p = 41, gb_code_c = 4108, nums = '豫H' where city ='焦作';
update cw_region set gb_code_p = 41, gb_code_c = 4109, nums = '豫J' where city ='濮阳';
update cw_region set gb_code_p = 41, gb_code_c = 4110, nums = '豫K' where city ='许昌';
update cw_region set gb_code_p = 41, gb_code_c = 4111, nums = '豫L' where city ='漯河';
update cw_region set gb_code_p = 41, gb_code_c = 4112, nums = '豫M' where city ='三门峡';
update cw_region set gb_code_p = 41, gb_code_c = 4113, nums = '豫R' where city ='南阳';
update cw_region set gb_code_p = 41, gb_code_c = 4114, nums = '豫N' where city ='商丘';
update cw_region set gb_code_p = 41, gb_code_c = 4115, nums = '豫S' where city ='信阳';
update cw_region set gb_code_p = 41, gb_code_c = 4116, nums = '豫P' where city ='周口';
update cw_region set gb_code_p = 41, gb_code_c = 4117, nums = '豫Q' where city ='驻马店';
update cw_region set gb_code_p = 42, gb_code_c = 4201, nums = '鄂A' where city ='武汉';
update cw_region set gb_code_p = 42, gb_code_c = 4202, nums = '鄂B' where city ='黄石';
update cw_region set gb_code_p = 42, gb_code_c = 4203, nums = '鄂C' where city ='十堰';
update cw_region set gb_code_p = 42, gb_code_c = 4205, nums = '鄂E' where city ='宜昌';
update cw_region set gb_code_p = 42, gb_code_c = 4206, nums = '鄂F' where city ='襄樊';
update cw_region set gb_code_p = 42, gb_code_c = 4207, nums = '鄂G' where city ='鄂州';
update cw_region set gb_code_p = 42, gb_code_c = 4208, nums = '鄂H' where city ='荆门';
update cw_region set gb_code_p = 42, gb_code_c = 4209, nums = '鄂K' where city ='孝感';
update cw_region set gb_code_p = 42, gb_code_c = 4210, nums = '鄂D' where city ='荆州';
update cw_region set gb_code_p = 42, gb_code_c = 4211, nums = '鄂J' where city ='黄冈';
update cw_region set gb_code_p = 42, gb_code_c = 4212, nums = '鄂L' where city ='咸宁';
update cw_region set gb_code_p = 42, gb_code_c = 4213, nums = '鄂S' where city ='随州';
update cw_region set gb_code_p = 42, gb_code_c = 4228, nums = '鄂Q' where city ='恩施';
update cw_region set gb_code_p = 43, gb_code_c = 4301, nums = '湘A' where city ='长沙';
update cw_region set gb_code_p = 43, gb_code_c = 4302, nums = '湘B' where city ='株洲';
update cw_region set gb_code_p = 43, gb_code_c = 4303, nums = '湘C' where city ='湘潭';
update cw_region set gb_code_p = 43, gb_code_c = 4304, nums = '湘D' where city ='衡阳';
update cw_region set gb_code_p = 43, gb_code_c = 4305, nums = '湘E' where city ='邵阳';
update cw_region set gb_code_p = 43, gb_code_c = 4306, nums = '湘F' where city ='岳阳';
update cw_region set gb_code_p = 43, gb_code_c = 4307, nums = '湘J' where city ='常德';
update cw_region set gb_code_p = 43, gb_code_c = 4308, nums = '湘G' where city ='张家界';
update cw_region set gb_code_p = 43, gb_code_c = 4309, nums = '湘H' where city ='益阳';
update cw_region set gb_code_p = 43, gb_code_c = 4310, nums = '湘L' where city ='郴州';
update cw_region set gb_code_p = 43, gb_code_c = 4311, nums = '湘M' where province='湖南' and city ='永州';
update cw_region set gb_code_p = 43, gb_code_c = 4312, nums = '湘N' where city ='怀化';
update cw_region set gb_code_p = 43, gb_code_c = 4313, nums = '湘K' where city ='娄底';
update cw_region set gb_code_p = 43, gb_code_c = 4331, nums = '湘U' where city ='湘西';
update cw_region set gb_code_p = 44, gb_code_c = 4401, nums = '粤A' where city ='广州';
update cw_region set gb_code_p = 44, gb_code_c = 4402, nums = '粤F' where city ='韶关';
update cw_region set gb_code_p = 44, gb_code_c = 4403, nums = '粤B' where city ='深圳';
update cw_region set gb_code_p = 44, gb_code_c = 4404, nums = '粤C' where city ='珠海';
update cw_region set gb_code_p = 44, gb_code_c = 4405, nums = '粤D' where city ='汕头';
update cw_region set gb_code_p = 44, gb_code_c = 4406, nums = '粤E' where city ='佛山';
update cw_region set gb_code_p = 44, gb_code_c = 4407, nums = '粤J' where city ='江门';
update cw_region set gb_code_p = 44, gb_code_c = 4408, nums = '粤G' where city ='湛江';
update cw_region set gb_code_p = 44, gb_code_c = 4409, nums = '粤K' where city ='茂名';
update cw_region set gb_code_p = 44, gb_code_c = 4412, nums = '粤H' where city ='肇庆';
update cw_region set gb_code_p = 44, gb_code_c = 4413, nums = '粤L' where city ='惠州';
update cw_region set gb_code_p = 44, gb_code_c = 4414, nums = '粤M' where city ='梅州';
update cw_region set gb_code_p = 44, gb_code_c = 4415, nums = '粤N' where city ='汕尾';
update cw_region set gb_code_p = 44, gb_code_c = 4416, nums = '粤P' where city ='河源';
update cw_region set gb_code_p = 44, gb_code_c = 4417, nums = '粤Q' where city ='阳江';
update cw_region set gb_code_p = 44, gb_code_c = 4418, nums = '粤R' where city ='清远';
update cw_region set gb_code_p = 44, gb_code_c = 4419, nums = '粤S' where city ='东莞';
update cw_region set gb_code_p = 44, gb_code_c = 4420, nums = '粤T' where city ='中山';
update cw_region set gb_code_p = 44, gb_code_c = 4451, nums = '粤U' where city ='潮州';
update cw_region set gb_code_p = 44, gb_code_c = 4452, nums = '粤V' where city ='揭阳';
update cw_region set gb_code_p = 44, gb_code_c = 4453, nums = '粤W' where city ='云浮';
update cw_region set gb_code_p = 44, gb_code_c = 4454, nums = '粤X' where city ='顺德';
update cw_region set gb_code_p = 44, gb_code_c = 4455, nums = '粤Y' where city ='南海';
update cw_region set gb_code_p = 44, gb_code_c = 4480, nums = '粤Z' where city ='港澳';
update cw_region set gb_code_p = 45, gb_code_c = 4501, nums = '桂A' where city ='南宁';
update cw_region set gb_code_p = 45, gb_code_c = 4502, nums = '桂B' where city ='柳州';
update cw_region set gb_code_p = 45, gb_code_c = 4503, nums = '桂C' where city ='桂林';
update cw_region set gb_code_p = 45, gb_code_c = 4504, nums = '桂D' where city ='梧州';
update cw_region set gb_code_p = 45, gb_code_c = 4505, nums = '桂E' where city ='北海';
update cw_region set gb_code_p = 45, gb_code_c = 4506, nums = '桂P', city ='防城港' where id=4491;
update cw_region set gb_code_p = 45, gb_code_c = 4507, nums = '桂N' where city ='钦州';
update cw_region set gb_code_p = 45, gb_code_c = 4508, nums = '桂R' where city ='贵港';
update cw_region set gb_code_p = 45, gb_code_c = 4509, nums = '桂K' where city ='玉林';
update cw_region set gb_code_p = 45, gb_code_c = 4510, nums = '桂L' where city ='百色';
update cw_region set gb_code_p = 45, gb_code_c = 4511, nums = '桂J' where city ='贺州';
update cw_region set gb_code_p = 45, gb_code_c = 4512, nums = '桂M' where city ='河池';
update cw_region set gb_code_p = 45, gb_code_c = 4513, nums = '桂G' where city ='来宾';
update cw_region set gb_code_p = 45, gb_code_c = 4514, nums = '桂F' where city ='崇左';
update cw_region set gb_code_p = 45, gb_code_c = 4525, nums = '桂H' where city ='桂林';
update cw_region set gb_code_p = 46, gb_code_c = 4601, nums = '琼A' where city ='海口';
update cw_region set gb_code_p = 46, gb_code_c = 4602, nums = '琼B' where city ='三亚';
update cw_region set gb_code_p = 46, gb_code_c = 4606, nums = '琼E' where city ='洋浦';
update cw_region set gb_code_p = 51, gb_code_c = 5101, nums = '川A' where city ='成都';
update cw_region set gb_code_p = 51, gb_code_c = 5103, nums = '川C' where city ='自贡';
update cw_region set gb_code_p = 51, gb_code_c = 5104, nums = '川D' where city ='攀枝花';
update cw_region set gb_code_p = 51, gb_code_c = 5105, nums = '川E' where city ='泸州';
update cw_region set gb_code_p = 51, gb_code_c = 5106, nums = '川F' where city ='德阳';
update cw_region set gb_code_p = 51, gb_code_c = 5107, nums = '川B' where city ='绵阳';
update cw_region set gb_code_p = 51, gb_code_c = 5108, nums = '川H' where city ='广元';
update cw_region set gb_code_p = 51, gb_code_c = 5109, nums = '川J' where city ='遂宁';
update cw_region set gb_code_p = 51, gb_code_c = 5110, nums = '川K' where city ='内江';
update cw_region set gb_code_p = 51, gb_code_c = 5111, nums = '川L' where city ='乐山';
update cw_region set gb_code_p = 51, gb_code_c = 5113, nums = '川R' where city ='南充';
update cw_region set gb_code_p = 51, gb_code_c = 5114, nums = '川Z' where city ='眉山';
update cw_region set gb_code_p = 51, gb_code_c = 5115, nums = '川Q' where city ='宜宾';
update cw_region set gb_code_p = 51, gb_code_c = 5116, nums = '川X' where city ='广安';
update cw_region set gb_code_p = 51, gb_code_c = 5117, nums = '川S' where city ='达州';
update cw_region set gb_code_p = 51, gb_code_c = 5118, nums = '川T' where city ='雅安';
update cw_region set gb_code_p = 51, gb_code_c = 5119, nums = '川Y' where city ='巴中';
update cw_region set gb_code_p = 51, gb_code_c = 5120, nums = '川M' where city ='资阳';
update cw_region set gb_code_p = 51, gb_code_c = 5132, nums = '川U' where city ='阿坝';
update cw_region set gb_code_p = 51, gb_code_c = 5133, nums = '川V' where city ='甘孜';
update cw_region set gb_code_p = 51, gb_code_c = 5134, nums = '川W' where city ='凉山';
update cw_region set gb_code_p = 52, gb_code_c = 5201, nums = '贵A' where city ='贵阳';
update cw_region set gb_code_p = 52, gb_code_c = 5202, nums = '贵B' where city ='六盘水';
update cw_region set gb_code_p = 52, gb_code_c = 5203, nums = '贵C' where city ='遵义';
update cw_region set gb_code_p = 52, gb_code_c = 5204, nums = '贵G' where city ='安顺';
update cw_region set gb_code_p = 52, gb_code_c = 5205, nums = '贵F' where city ='毕节';
update cw_region set gb_code_p = 52, gb_code_c = 5206, nums = '贵D' where city ='铜仁';
update cw_region set gb_code_p = 52, gb_code_c = 5223, nums = '贵E' where city ='黔西南';
update cw_region set gb_code_p = 52, gb_code_c = 5226, nums = '贵H' where city ='黔东南';
update cw_region set gb_code_p = 52, gb_code_c = 5227, nums = '贵J' where city ='黔南';
update cw_region set gb_code_p = 53, gb_code_c = 5301, nums = '云A' where city ='昆明';
update cw_region set gb_code_p = 53, gb_code_c = 5303, nums = '云D' where city ='曲靖';
update cw_region set gb_code_p = 53, gb_code_c = 5304, nums = '云F' where city ='玉溪';
update cw_region set gb_code_p = 53, gb_code_c = 5305, nums = '云M' where city ='保山';
update cw_region set gb_code_p = 53, gb_code_c = 5306, nums = '云C' where city ='昭通';
update cw_region set gb_code_p = 53, gb_code_c = 5307, nums = '云P' where city ='丽江';
update cw_region set gb_code_p = 53, gb_code_c = 5308, nums = '云J' where city ='普洱';
update cw_region set gb_code_p = 53, gb_code_c = 5309, nums = '云S' where city ='临沧';
update cw_region set gb_code_p = 53, gb_code_c = 5323, nums = '云E' where city ='楚雄';
update cw_region set gb_code_p = 53, gb_code_c = 5325, nums = '云G' where city ='红河';
update cw_region set gb_code_p = 53, gb_code_c = 5326, nums = '云H' where city ='文山';
update cw_region set gb_code_p = 53, gb_code_c = 5328, nums = '云K' where city ='西双版纳';
update cw_region set gb_code_p = 53, gb_code_c = 5329, nums = '云L' where city ='大理';
update cw_region set gb_code_p = 53, gb_code_c = 5331, nums = '云N' where city ='德宏';
update cw_region set gb_code_p = 53, gb_code_c = 5333, nums = '云Q' where city ='怒江';
update cw_region set gb_code_p = 53, gb_code_c = 5334, nums = '云R' where city ='迪庆';
update cw_region set gb_code_p = 54, gb_code_c = 5401, nums = '藏A' where city ='拉萨';
update cw_region set gb_code_p = 54, gb_code_c = 5421, nums = '藏B' where city ='昌都';
update cw_region set gb_code_p = 54, gb_code_c = 5422, nums = '藏C' where city ='山南';
update cw_region set gb_code_p = 54, gb_code_c = 5423, nums = '藏D' where city ='日喀则';
update cw_region set gb_code_p = 54, gb_code_c = 5424, nums = '藏E' where city ='那曲';
update cw_region set gb_code_p = 54, gb_code_c = 5425, nums = '藏F' where city ='阿里';
update cw_region set gb_code_p = 54, gb_code_c = 5426, nums = '藏G' where city ='林芝';
update cw_region set gb_code_p = 61, gb_code_c = 6101, nums = '陕A' where city ='西安';
update cw_region set gb_code_p = 61, gb_code_c = 6102, nums = '陕B' where city ='铜川';
update cw_region set gb_code_p = 61, gb_code_c = 6103, nums = '陕C' where city ='宝鸡';
update cw_region set gb_code_p = 61, gb_code_c = 6104, nums = '陕D' where city ='咸阳';
update cw_region set gb_code_p = 61, gb_code_c = 6105, nums = '陕E' where city ='渭南';
update cw_region set gb_code_p = 61, gb_code_c = 6106, nums = '陕J' where city ='延安';
update cw_region set gb_code_p = 61, gb_code_c = 6107, nums = '陕F' where city ='汉中';
update cw_region set gb_code_p = 61, gb_code_c = 6108, nums = '陕K' where city ='榆林';
update cw_region set gb_code_p = 61, gb_code_c = 6109, nums = '陕G' where city ='安康';
update cw_region set gb_code_p = 61, gb_code_c = 6110, nums = '陕H' where city ='商洛';
update cw_region set gb_code_p = 61, gb_code_c = 6125, nums = '陕V' where city ='杨凌';
update cw_region set gb_code_p = 62, gb_code_c = 6201, nums = '甘A' where city ='兰州';
update cw_region set gb_code_p = 62, gb_code_c = 6202, nums = '甘B' where city ='嘉峪关';
update cw_region set gb_code_p = 62, gb_code_c = 6203, nums = '甘C' where city ='金昌';
update cw_region set gb_code_p = 62, gb_code_c = 6204, nums = '甘D' where city ='白银';
update cw_region set gb_code_p = 62, gb_code_c = 6205, nums = '甘E' where city ='天水';
update cw_region set gb_code_p = 62, gb_code_c = 6206, nums = '甘H' where city ='武威';
update cw_region set gb_code_p = 62, gb_code_c = 6207, nums = '甘G' where city ='张掖';
update cw_region set gb_code_p = 62, gb_code_c = 6208, nums = '甘L' where city ='平凉';
update cw_region set gb_code_p = 62, gb_code_c = 6209, nums = '甘F' where city ='酒泉';
update cw_region set gb_code_p = 62, gb_code_c = 6210, nums = '甘M' where city ='庆阳';
update cw_region set gb_code_p = 62, gb_code_c = 6211, nums = '甘J' where city ='定西';
update cw_region set gb_code_p = 62, gb_code_c = 6212, nums = '甘K' where city ='陇南';
update cw_region set gb_code_p = 62, gb_code_c = 6229, nums = '甘N' where city ='临夏';
update cw_region set gb_code_p = 62, gb_code_c = 6230, nums = '甘P' where city ='甘南';
update cw_region set gb_code_p = 63, gb_code_c = 6301, nums = '青A' where city ='西宁';
update cw_region set gb_code_p = 63, gb_code_c = 6321, nums = '青B' where city ='海东';
update cw_region set gb_code_p = 63, gb_code_c = 6322, nums = '青C' where city ='海北';
update cw_region set gb_code_p = 63, gb_code_c = 6323, nums = '青D' where city ='黄南';
update cw_region set gb_code_p = 63, gb_code_c = 6325, nums = '青E' where city ='海南';
update cw_region set gb_code_p = 63, gb_code_c = 6326, nums = '青F' where city ='果洛';
update cw_region set gb_code_p = 63, gb_code_c = 6327, nums = '青G' where city ='玉树';
update cw_region set gb_code_p = 63, gb_code_c = 6328, nums = '青H' where city ='海西';
update cw_region set gb_code_p = 64, gb_code_c = 6401, nums = '宁A' where city ='银川';
update cw_region set gb_code_p = 64, gb_code_c = 6402, nums = '宁B' where city ='石嘴山';
update cw_region set gb_code_p = 64, gb_code_c = 6403, nums = '宁C' where city ='吴忠';
update cw_region set gb_code_p = 64, gb_code_c = 6404, nums = '宁D' where city ='固原';
update cw_region set gb_code_p = 64, gb_code_c = 6405, nums = '宁E' where city ='中卫';
update cw_region set gb_code_p = 65, gb_code_c = 6501, nums = '新A' where city ='乌鲁木齐';
update cw_region set gb_code_p = 65, gb_code_c = 6502, nums = '新J' where city ='克拉玛依';
update cw_region set gb_code_p = 65, gb_code_c = 6521, nums = '新K' where city ='吐鲁番';
update cw_region set gb_code_p = 65, gb_code_c = 6522, nums = '新L' where city ='哈密';
update cw_region set gb_code_p = 65, gb_code_c = 6523, nums = '新B' where city ='昌吉';
update cw_region set gb_code_p = 65, gb_code_c = 6527, nums = '新E' where city ='博尔塔拉';
update cw_region set gb_code_p = 65, gb_code_c = 6528, nums = '新M' where city ='巴音郭楞';
update cw_region set gb_code_p = 65, gb_code_c = 6529, nums = '新N' where city ='阿克苏';
update cw_region set gb_code_p = 65, gb_code_c = 6530, nums = '新P' where city ='克孜勒苏';
update cw_region set gb_code_p = 65, gb_code_c = 6531, nums = '新Q' where city ='喀什';
update cw_region set gb_code_p = 65, gb_code_c = 6532, nums = '新R' where city ='和田';
update cw_region set gb_code_p = 65, gb_code_c = 6540, nums = '新F' where city ='伊犁';
update cw_region set gb_code_p = 65, gb_code_c = 6542, nums = '新G' where city ='塔城';
update cw_region set gb_code_p = 65, gb_code_c = 6543, nums = '新H' where city ='阿勒泰';
update cw_region set gb_code_p = 34, gb_code_c = 340181, nums = '皖Q' where city ='巢湖';
update cw_region set gb_code_p = 41, gb_code_c = 419001, nums = '豫U' where city ='济源';
update cw_region set gb_code_p = 42, gb_code_c = 429004, nums = '鄂M' where city ='仙桃';
update cw_region set gb_code_p = 42, gb_code_c = 429005, nums = '鄂N' where city ='潜江';
update cw_region set gb_code_p = 42, gb_code_c = 429006, nums = '鄂R' where city ='天门';
update cw_region set gb_code_p = 42, gb_code_c = 429021, nums = '鄂P' where city ='神农架';
update cw_region set gb_code_p = 46, gb_code_c = 469001, nums = '琼D' where city ='五指山';
update cw_region set gb_code_p = 46, gb_code_c = 469002, nums = '琼C' where city ='琼海';
update cw_region set gb_code_p = 51, gb_code_c = 513401, nums = '川W' where city ='西昌';
update cw_region set gb_code_p = 65, gb_code_c = 654003, nums = '新D' where city ='奎屯';
update cw_region set gb_code_p = 65, gb_code_c = 659001, nums = '新C' where city ='石河子';

delete from cw_region where province='海南' and city='海南';
delete from cw_region where province='陕西' and city='省直系统';
update cw_region set gb_code_p = 36, gb_code_c = 3601, nums = '赣A' where city ='南昌';
update cw_region set gb_code_p = 50, gb_code_c = 50, nums = '渝C' where province='重庆' and city ='永州';
update cw_region set gb_code_p = 50, gb_code_c = 50, city = '重庆' where province='重庆' and city ='直属车辆';
update cw_region set gb_code_p = 36, gb_code_c = 3612, nums = '赣M' where province='江西' and city ='省直系统';
     
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '天津', '天津', '津', '津', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '辽宁', '辽宁全省', '辽', '辽', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '吉林', '吉林全省', '吉', '吉', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '河南', '河南全省', '豫', '豫', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '陕西', '陕西全省', '陕', '陕', 0, 50);
insert into cw_region (level, province, city, abbreviation, nums, is_dredge, orders) values (2, '广西', '广西全省', '桂', '桂', 0, 50);

insert into cw_code (port, code, content) values ('cx580.com', '0', '成功');
insert into cw_code (port, code, content) values ('cx580.com', '-1', '缺少必要的参数或找不到车牌前缀所匹配的城市');
insert into cw_code (port, code, content) values ('cx580.com', '-3', '本系统暂不提供该城市违章查询请求');
insert into cw_code (port, code, content) values ('cx580.com', '-5', '服务器错误（超时，数据获取异常等）');
insert into cw_code (port, code, content) values ('cx580.com', '-10', '未被授权访问该服务或用户名密码不正确');
insert into cw_code (port, code, content) values ('cx580.com', '-20', '未和错误');
insert into cw_code (port, code, content) values ('cx580.com', '-40', '未被授权查询此车牌信息');
insert into cw_code (port, code, content) values ('cx580.com', '-41', '输入参数不符合数据源要求');
insert into cw_code (port, code, content) values ('cx580.com', '-42', '数据源暂不可用');
insert into cw_code (port, code, content) values ('cx580.com', '-43', '当日查询数已达到授权数标准，无法继续查询');
insert into cw_code (port, code, content) values ('cx580.com', '-44', '已达到查询上限');
insert into cw_code (port, code, content) values ('cx580.com', '-45', '确认数据完整性，是否被篡改');
insert into cw_code (port, code, content) values ('cx580.com', '-6', '错误：您输入信息有误');
insert into cw_code (port, code, content) values ('cx580.com', '-61', '输入车牌号有误');
insert into cw_code (port, code, content) values ('cx580.com', '-62', '输入车架号有误');
insert into cw_code (port, code, content) values ('cx580.com', '-63', '输入发动机号有误');
insert into cw_code (port, code, content) values ('cx580.com', '-66', '不支持的车辆类型');
insert into cw_code (port, code, content) values ('cx580.com', '-67', '该省份（城市）不支持异地车牌');


insert into cw_code (port, code, content) values ('cx580.com', '-9999', '数据源查询超时');
insert into cw_code (port, code, content) values ('cheshouye.com', '-9999', '数据源查询超时');
insert into cw_code (port, code, content) values ('http://120.26.57.239/api/', '-9999', '数据源查询超时');

insert into cw_code (port, code, content) values ('cx580.com', '-9998', '数据源查询返回异常');
insert into cw_code (port, code, content) values ('cheshouye.com', '-9998', '数据源查询返回异常');
insert into cw_code (port, code, content) values ('http://120.26.57.239/api/', '-9998', '数据源查询返回异常');

